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
        // 1. Validación de los datos
        $validatedData = $request->validate([
            'asunto' => 'required|string|max:255',
            'oficinas_destino' => 'required|exists:oficinas,id',
            'tipo_documento' => 'required|string|max:50',
            'numero_folios' => 'required|integer|min:1',
            'file_archivo' => 'nullable|file|mimes:pdf|max:2048', // PDF de máx 2MB
        ]);

        try {
            // 2. Usamos una transacción para garantizar la integridad de los datos
            $seguimiento = DB::transaction(function () use ($validatedData, $request) {
                $usuario = auth()->user();

                if (!$usuario->oficina_id) {
                    throw new \Exception('El usuario no tiene una oficina de origen asignada.');
                }

                // --- Creación del Documento ---
                $documento = new Documento();
                $documento->numero_documento = 0; // Se puede autogenerar o quitar si no es necesario
                $documento->dni = $usuario->dni;
                $documento->nombre = $usuario->name;
                $documento->tipo_documento = $validatedData['tipo_documento'];
                $documento->fecha_emision = now();
                $documento->numero_folios = $validatedData['numero_folios'];
                $documento->pdf_url = null;

                if ($request->hasFile('file_archivo')) {
                    // Guarda el archivo y obtén la URL pública
                    $path = $request->file('file_archivo')->store('documentos', 'public');
                    $documento->pdf_url = \Illuminate\Support\Facades\Storage::url($path);
                }
                $documento->save();

                // --- Creación del Seguimiento (sin el CU aún) ---
                $nuevoSeguimiento = new Seguimiento();
                $nuevoSeguimiento->documentos_id = $documento->id;
                $nuevoSeguimiento->asunto = $validatedData['asunto'];
                $nuevoSeguimiento->fecha_seguimiento = now();
                $nuevoSeguimiento->estado = 'Enviado';
                $nuevoSeguimiento->oficinas_origen = $usuario->oficina_id;
                $nuevoSeguimiento->oficinas_destino = $validatedData['oficinas_destino'];
                $nuevoSeguimiento->save(); // Guardamos para obtener el ID

                // --- Generación y guardado del Código Único (CU) ---
                $cu_number = str_pad($nuevoSeguimiento->id, 7, '0', STR_PAD_LEFT);
                $cu_code = "CU-" . $nuevoSeguimiento->oficinas_origen . "-" . $cu_number;
                $nuevoSeguimiento->CU = $cu_code;
                $nuevoSeguimiento->save(); // Actualizamos con el CU

                return $nuevoSeguimiento;
            });

            // 3. Devolvemos una respuesta de éxito con el trámite creado
            return response()->json([
                'message' => 'Trámite registrado con éxito.',
                'data' => $seguimiento->load(['documento', 'oficinaOrigen', 'oficinaDestino'])
            ], 201);

        } catch (\Exception $e) {
            // Si algo falla, la transacción se revierte y devolvemos un error
            return response()->json([
                'message' => 'Error al registrar el trámite.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Seguimiento $seguimiento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Seguimiento $seguimiento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Seguimiento $seguimiento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Seguimiento $seguimiento)
    {
        //
    }
}
