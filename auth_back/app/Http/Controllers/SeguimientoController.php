<?php

namespace App\Http\Controllers;

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
        //
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
        //
        $request->validate([
            'asunto' => 'required|string|max:255',
            'oficinas_destino' => 'required|exists:oficinas,id',
            'tipo_documento' => 'required|string|max:50',
            'numero_folios' => 'required|integer|min:1',
            'file_archivo' => 'nullable|file|mimes:pdf|max:2048', // Max 2MB
            'numero_documento' => 'required|integer',
            'oficinas_origen' => 'required|exists:oficinas,id',
        ]);

        $usuario = auth()->user();

        $documento = new Documento();
        $documento->numero_documento = $request->numero_documento;
        $documento->dni = $usuario->dni; // Assuming the user has a 'dni' attribute
        $documento->nombre = $usuario->name; // Assuming the user has a 'name' attribute
        $documento->tipo_documento = $request->tipo_documento;
        $documento->fecha_emision = now();
        $documento->numero_folios = $request->numero_folios;
        if ($request->hasFile('file_archivo')) {
            // TODO: SUBIR A AWS S3

            $documento->file_archivo = $request->file('file_archivo')->store('documentos', 'public');
        }
        $documento->save();


        $seguimiento = new Seguimiento();

        // Armar codigo CU -- CU[OFICINA][AUTO_INCREMENT]
        $lastCU = Seguimiento::where('oficina_origen', $request->oficinas_origen)
            ->orderBy('id', 'desc')
            ->first();

        $oficinaOrigen = $request->oficinas_origen;

        if ($lastCU) {
            $CU = substr($lastCU->CU, -5, 4);
            $nextNumber = (int) $CU + 1;
            $seguimiento->CU = 'CU' . str_pad($oficinaOrigen, 3, '0', STR_PAD_LEFT) . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
        } else {
            $seguimiento->CU = 'CU' . str_pad($oficinaOrigen, 3, '0', STR_PAD_LEFT) . '0001';
        }

        $seguimiento->asunto = $request->asunto;
        $seguimiento->oficina_origen = $request->oficinas_origen;
        $seguimiento->oficina_destino = $request->oficinas_destino;
        $seguimiento->documento_id = $documento->id;
        $seguimiento->estado = 'PENDIENTE';
        $seguimiento->save();

        return response()->json([
            'message' => 'Seguimiento creado exitosamente',
            'seguimiento' => $seguimiento,
        ], 201);
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
