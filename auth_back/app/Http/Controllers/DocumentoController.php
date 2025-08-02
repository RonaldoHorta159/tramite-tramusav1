<?php

namespace App\Http\Controllers;

use App\Models\Documento;
use App\Models\Seguimiento;
use Illuminate\Http\Request;

class DocumentoController extends Controller
{

    public function historial(Documento $documento)
    {
        // Buscamos todos los seguimientos asociados a este ID de documento
        // y los ordenamos por fecha para mostrar la secuencia correcta.
        $historial = Seguimiento::where('documentos_id', $documento->id)->with('oficinaOrigen', 'oficinaDestino')->orderBy('created_at', 'asc')->get();

        return response()->json($historial);
    }
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
    }

    /**
     * Display the specified resource.
     */
    public function show(Documento $documento)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Documento $documento)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Documento $documento)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Documento $documento)
    {
        //
    }
}
