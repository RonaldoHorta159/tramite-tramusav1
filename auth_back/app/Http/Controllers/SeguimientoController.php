<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use App\Models\Seguimiento;
use Illuminate\Http\Request;
use App\Models\Documento;


class SeguimientoController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Obtenemos al usuario autenticado
        $user = auth()->user();

        // Buscamos los documentos que pertenecen al DNI del usuario
        $documentos_ids = Documento::where('dni', $user->dni)->pluck('id');

        // Obtenemos los seguimientos asociados a esos documentos
        // Usamos with() para cargar las relaciones y evitar consultas N+1 (más eficiente)
        $seguimientos = Seguimiento::whereIn('documentos_id', $documentos_ids)
            ->with(['documento', 'oficinaOrigen', 'oficinaDestino'])
            ->orderBy('created_at', 'desc') // Ordenamos por más reciente
            ->get();

        // Devolvemos la lista en formato JSON
        return response()->json($seguimientos);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // 1. Validación
        $validatedData = $request->validate([
            'asunto' => 'required|string|max:255',
            'oficinas_destino' => 'required|exists:oficinas,id',
            'tipo_documento' => 'required|string|max:50',
            'numero_documento' => 'required|string|max:50',
            'numero_folios' => 'required|integer|min:1',
            'pdf' => 'nullable|file|mimes:pdf|max:2048',
        ]);

        try {
            // 2. Transacción
            $seguimiento = DB::transaction(function () use ($validatedData, $request) {
                $usuario = auth()->user();

                if (!$usuario->oficina_id) {
                    throw new \Exception('El usuario no tiene una oficina de origen asignada.');
                }

                $pdfPath = null;
                if ($request->hasFile('pdf')) {
                    $path = $request->file('pdf')->store('documentos', 'public');
                    $pdfPath = Storage::url($path);
                }

                // 3. Creación del Documento (usando los campos correctos)
                $documento = Documento::create([
                    'numero_documento' => $validatedData['numero_documento'],
                    'tipo_documento' => $validatedData['tipo_documento'],
                    'numero_folios' => $validatedData['numero_folios'],
                    'pdf_url' => $pdfPath,
                ]);

                // 4. Creación del Seguimiento (usando los nombres de columna estándar de Laravel)
                $nuevoSeguimiento = Seguimiento::create([
                    'documento_id' => $documento->id,
                    'asunto' => $validatedData['asunto'],
                    'estado' => 'PENDIENTE',
                    'origen_id' => $usuario->oficina_id,
                    'destino_id' => $validatedData['oficinas_destino'],
                ]);

                // 5. Generación y guardado del Código Único (CU)
                $cu_number = str_pad($nuevoSeguimiento->id, 7, '0', STR_PAD_LEFT);
                $cu_code = "CU-" . $nuevoSeguimiento->origen_id . "-" . $cu_number;
                $nuevoSeguimiento->update(['CU' => $cu_code]);

                return $nuevoSeguimiento;
            });

            // 6. Respuesta de éxito
            return response()->json(['message' => 'Trámite registrado con éxito.', 'data' => $seguimiento], 201);

        } catch (\Exception $e) {
            // 7. Respuesta de error detallada
            return response()->json([
                'message' => 'Error interno al registrar el trámite.',
                'error' => $e->getMessage() // Esto nos dará una pista si vuelve a fallar
            ], 500);
        }
    }


    /**
     * Muestra los trámites pendientes de ser recibidos por la oficina del usuario.
     */
    public function porRecibir()
    {
        // 1. Obtenemos al usuario y su oficina
        $usuario = auth()->user();
        $oficinaUsuarioId = $usuario->oficina_id;

        // 2. Validamos si el usuario tiene una oficina asignada
        if (!$oficinaUsuarioId) {
            // Si no tiene oficina, no puede recibir nada. Devolvemos una lista vacía.
            return response()->json([]);
        }

        // 3. Buscamos los seguimientos dirigidos a su oficina y que están "Enviados"
        $tramitesPorRecibir = Seguimiento::where('oficinas_destino', $oficinaUsuarioId)
            ->where('estado', 'Enviado') // Asumimos que 'Enviado' es el estado pendiente
            ->with(['documento', 'oficinaOrigen', 'oficinaDestino']) // Cargamos relaciones para info completa
            ->orderBy('created_at', 'asc') // Mostramos los más antiguos primero
            ->get();

        // 4. Devolvemos la lista en formato JSON
        return response()->json($tramitesPorRecibir);
    }

    /**
     * Cambia el estado de un trámite a "Recibido".
     *
     * @param  \App\Models\Seguimiento  $seguimiento
     * @return \Illuminate\Http\JsonResponse
     */
    public function recibir(Seguimiento $seguimiento)
    {
        // 1. (Seguridad) Validar que el usuario actual pertenece a la oficina de destino del trámite.
        $usuario = auth()->user();
        if ($usuario->oficina_id !== $seguimiento->oficinas_destino) {
            return response()->json(['message' => 'No autorizado para recibir este trámite.'], 403);
        }

        // 2. (Lógica) Actualizar el estado del trámite
        $seguimiento->estado = 'Recibido';
        $seguimiento->save();

        // 3. (Respuesta) Devolver un mensaje de éxito
        return response()->json([
            'message' => 'Trámite ' . $seguimiento->CU . ' recibido con éxito.',
            'data' => $seguimiento
        ]);
    }

    /**
     * Deriva un trámite a una nueva oficina.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Seguimiento  $seguimiento // El trámite original que se está derivando
     * @return \Illuminate\Http\JsonResponse
     */
    public function derivar(Request $request, Seguimiento $seguimiento)
    {
        // 1. Validar los datos de entrada
        $validatedData = $request->validate([
            'oficinas_destino' => 'required|exists:oficinas,id',
            'proveido' => 'required|string|max:1000',
        ]);

        $usuario = auth()->user();

        // 2. (Seguridad) Asegurarse de que el usuario que deriva es el destinatario actual del trámite
        if ($usuario->oficina_id !== $seguimiento->oficinas_destino) {
            return response()->json(['message' => 'No tiene permiso para derivar este trámite.'], 403);
        }

        try {
            // 3. Usamos una transacción para asegurar la integridad de los datos
            $nuevoSeguimiento = DB::transaction(function () use ($validatedData, $seguimiento, $usuario) {
                // --- Paso A: Actualizar el trámite antiguo ---
                $seguimiento->estado = 'Derivado';
                $seguimiento->save();

                // --- Paso B: Crear el nuevo registro de seguimiento ---
                $derivacion = new Seguimiento();
                $derivacion->documentos_id = $seguimiento->documentos_id; // Mismo documento
                $derivacion->asunto = $seguimiento->asunto; // Mismo asunto
                $derivacion->fecha_seguimiento = now();
                $derivacion->estado = 'Enviado'; // El nuevo estado es "Enviado" para la oficina de destino
                $derivacion->oficinas_origen = $usuario->oficina_id; // El origen es la oficina del usuario actual
                $derivacion->oficinas_destino = $validatedData['oficinas_destino'];
                $derivacion->proveido = $validatedData['proveido']; // El "Pase a:"
                $derivacion->save(); // Guardamos para obtener el ID

                // --- Paso C: Generar el nuevo Código Único (CU) ---
                $cu_number = str_pad($derivacion->id, 7, '0', STR_PAD_LEFT);
                $cu_code = "CU-" . $derivacion->oficinas_origen . "-" . $cu_number;
                $derivacion->CU = $cu_code;
                $derivacion->save();

                return $derivacion;
            });

            return response()->json([
                'message' => 'Trámite derivado con éxito al CU: ' . $nuevoSeguimiento->CU,
                'data' => $nuevoSeguimiento
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error al derivar el trámite.',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    /**
     * Anula un trámite, cambiando su estado a "Anulado".
     * Solo para administradores.
     *
     * @param  \App\Models\Seguimiento  $seguimiento
     * @return \Illuminate\Http\JsonResponse
     */
    public function anular(Seguimiento $seguimiento)
    {
        // 1. (SEGURIDAD) Validar que el usuario sea administrador.
        //    Asumimos que en tu tabla 'users' tienes una columna 'rol' y el valor es 'admin'.
        if (auth()->user()->rol !== 'admin') {
            return response()->json(['message' => 'Acción no autorizada.'], 403); // 403 Forbidden
        }

        // 2. (LÓGICA) Validar que el trámite no esté ya anulado para evitar acciones redundantes.
        if ($seguimiento->estado === 'Anulado') {
            return response()->json(['message' => 'Este trámite ya ha sido anulado.'], 409); // 409 Conflict
        }

        // 3. Actualizar el estado del trámite
        $seguimiento->estado = 'Anulado';
        $seguimiento->save();

        // 4. Devolver un mensaje de éxito
        return response()->json([
            'message' => 'Trámite ' . $seguimiento->CU . ' ha sido anulado con éxito.',
            'data' => $seguimiento
        ]);
    }

    /**
     * Añade una observación (proveído) a un trámite y cambia su estado a "Observado".
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Seguimiento  $seguimiento
     * @return \Illuminate\Http\JsonResponse
     */
    public function observar(Request $request, Seguimiento $seguimiento)
    {
        // 1. Validar que el campo 'proveido' venga en la petición
        $validatedData = $request->validate([
            'proveido' => 'required|string|max:1000',
        ]);

        // 2. (Seguridad) Validar que el usuario pertenece a la oficina de destino del trámite
        $usuario = auth()->user();
        if ($usuario->oficina_id !== $seguimiento->oficinas_destino) {
            return response()->json(['message' => 'No autorizado para observar este trámite.'], 403);
        }

        // 3. Actualizar el estado y el proveído
        $seguimiento->estado = 'Observado';
        $seguimiento->proveido = $validatedData['proveido'];
        $seguimiento->save();

        // 4. Devolver un mensaje de éxito
        return response()->json([
            'message' => 'Trámite ' . $seguimiento->CU . ' observado con éxito.',
            'data' => $seguimiento
        ]);
    }


    /**
     * Cambia el estado de un trámite a "Rechazado".
     *
     * @param  \App\Models\Seguimiento  $seguimiento
     * @return \Illuminate\Http\JsonResponse
     */
    public function rechazar(Seguimiento $seguimiento)
    {
        // 1. (Seguridad) Validar que el usuario pertenece a la oficina de destino del trámite
        $usuario = auth()->user();
        if ($usuario->oficina_id !== $seguimiento->oficinas_destino) {
            return response()->json(['message' => 'No autorizado para rechazar este trámite.'], 403);
        }

        // 2. Actualizar el estado del trámite
        $seguimiento->estado = 'Rechazado';
        $seguimiento->save();

        // 3. Devolver un mensaje de éxito
        return response()->json([
            'message' => 'Trámite ' . $seguimiento->CU . ' ha sido rechazado.',
            'data' => $seguimiento
        ]);
    }

    /**
     * Recibe múltiples trámites en lote.
     */
    public function recibirLote(Request $request)
    {
        // Validamos que recibimos un array de IDs
        $validated = $request->validate([
            'seguimiento_ids' => 'required|array',
            'seguimiento_ids.*' => 'exists:seguimientos,id', // Valida que cada ID exista
        ]);

        $userOficinaId = auth()->user()->oficina_id;

        // Usamos una transacción para asegurar la integridad de los datos
        DB::transaction(function () use ($validated, $userOficinaId) {
            foreach ($validated['seguimiento_ids'] as $seguimientoId) {
                $seguimiento = Seguimiento::findOrFail($seguimientoId);

                // Verificación de seguridad: el trámite debe estar destinado a la oficina del usuario
                if ($seguimiento->destino_id !== $userOficinaId || $seguimiento->estado !== 'PENDIENTE') {
                    // Si un trámite no cumple, abortamos toda la transacción
                    abort(403, "No autorizado para recibir el trámite ID: " . $seguimientoId);
                }

                $seguimiento->update(['estado' => 'RECIBIDO']);
            }
        });

        return response()->json(['message' => 'Trámites recibidos correctamente.'], 200);
    }
}
