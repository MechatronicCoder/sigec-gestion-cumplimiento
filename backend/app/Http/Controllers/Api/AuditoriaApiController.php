<?php

namespace App\Http\Controllers\Api;

use App\Exports\AuditoriaExport;
use App\Http\Controllers\Controller;
use App\Models\Auditoria;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class AuditoriaApiController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = $this->filtrar(
            Auditoria::with('usuario:id,name,email,rol')->orderByDesc('created_at'),
            $request
        );

        $perPage = (int) $request->input('per_page', 30);

        return response()->json(
            $query->paginate($perPage)->withQueryString()
        );
    }

    public function filtros(): JsonResponse
    {
        return response()->json([
            'usuarios' => User::select('id', 'name')->orderBy('name')->get(),
            'modelos' => Auditoria::select('modelo')->distinct()->pluck('modelo'),
        ]);
    }

    public function exportarExcel(Request $request): BinaryFileResponse
    {
        $nombreArchivo = 'sigec-auditoria-' . now()->format('Y-m-d_H-i-s') . '.xlsx';

        return Excel::download(
            new AuditoriaExport($this->filtrosDesdeRequest($request)),
            $nombreArchivo
        );
    }

    public function exportarPdf(Request $request)
    {
        $registros = $this->filtrar(
            Auditoria::with('usuario:id,name')->orderByDesc('created_at'),
            $request
        )->get();

        $pdf = Pdf::loadView('auditoria.export-pdf', compact('registros'))
            ->setPaper('a4', 'landscape');

        $nombreArchivo = 'sigec-auditoria-' . now()->format('Y-m-d_H-i-s') . '.pdf';

        return $pdf->download($nombreArchivo);
    }

    /**
     * Aplica los filtros de query params (modelo, user_id, desde, hasta) a una consulta existente.
     */
    private function filtrar($query, Request $request)
    {
        if ($request->filled('modelo')) {
            $query->where('modelo', $request->string('modelo'));
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->integer('user_id'));
        }

        if ($request->filled('desde')) {
            $query->whereDate('created_at', '>=', $request->date('desde'));
        }

        if ($request->filled('hasta')) {
            $query->whereDate('created_at', '<=', $request->date('hasta'));
        }

        return $query;
    }

    private function filtrosDesdeRequest(Request $request): array
    {
        return [
            'modelo' => $request->input('modelo'),
            'user_id' => $request->input('user_id'),
            'desde' => $request->input('desde'),
            'hasta' => $request->input('hasta'),
        ];
    }
}
