<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ClienteController extends Controller
{
    public function buscarPorNit(Request $request): JsonResponse
    {
        $nit = $request->query('nit', '');

        $cliente = Cliente::where('nit_cliente', $nit)
            ->where('activo', true)
            ->first();

        return response()->json([
            'encontrado' => (bool) $cliente,
            'cliente' => $cliente,
        ]);
    }
}