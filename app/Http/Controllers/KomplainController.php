<?php

namespace App\Http\Controllers;

use App\Services\TransApiClient;
use Illuminate\Http\Request;

class KomplainController extends Controller
{
    /**
     * Receiving - Komplain: satu request per pemuatan/filter.
     */
    public function index(Request $request)
    {
        $month = $this->monthFilter($request);

        $query = array_filter([
            'status' => $request->query('status'),
            'search' => $request->query('search'),
            'month'  => $month,
            'page'   => $request->query('page'),
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        $result = (new TransApiClient)->komplain(session('wms.token'), $query);

        return view('komplain.index', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Daftar komplain belum bisa dimuat.'),
            'filters'      => [
                'status' => strtoupper((string) $request->query('status', 'ACTIVE')),
                'search' => (string) $request->query('search', ''),
                'month'  => $month,
            ],
        ]);
    }

    /**
     * Daftar komplain hanya untuk tahun berjalan; default bulan berjalan.
     */
    private function monthFilter(Request $request)
    {
        $month = trim((string) $request->query('month', ''));

        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $month = '';
        }

        $current = now('Asia/Jakarta')->format('Y-m');

        return ($month === '' || substr($month, 0, 4) !== substr($current, 0, 4))
            ? $current
            : $month;
    }

    /**
     * Detail komplain + hak ACC.
     */
    public function show(Request $request, $id)
    {
        $result = (new TransApiClient)->komplainDetail(session('wms.token'), $id);

        // Kembali ke daftar dengan filter terakhir, bukan halaman default.
        parse_str((string) $request->query('from', ''), $back);
        $back = array_intersect_key($back, array_flip(['status', 'search', 'month', 'page']));
        $back = array_filter($back, function ($value) {
            return $value !== null && $value !== '';
        });

        return view('komplain.show', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Detail komplain belum bisa dimuat.'),
            'backUrl'      => route('komplain', $back),
        ]);
    }

    /**
     * ACC komplain; stok Araya -> gudang QC diproses di Trans.
     */
    public function acc(Request $request, $id)
    {
        $result = (new TransApiClient)->accKomplain(session('wms.token'), $id);

        if ($result['ok']) {
            return redirect()
                ->route('komplain.show', $this->detailParams($id))
                ->with('ok', $result['message'] ?: 'Komplain berhasil di-ACC.');
        }

        return redirect()
            ->route('komplain.show', $this->detailParams($id))
            ->with('error', $result['message'] ?? 'ACC komplain gagal.');
    }

    public function pdf(Request $request, $id)
    {
        $result = (new TransApiClient)->komplainPdf(session('wms.token'), $id);

        if (!$result['ok']) {
            return redirect()
                ->route('komplain.show', $this->detailParams($id))
                ->with('error', $result['message'] ?? 'Surat jalan komplain gagal dibuat.');
        }

        return response($result['body'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="komplain-' . (int) $id . '.pdf"',
        ]);
    }

    private function detailParams($id)
    {
        $params = ['id' => (int) $id];

        parse_str((string) request()->input('from', ''), $from);
        $from = array_intersect_key($from, array_flip(['status', 'search', 'month', 'page']));
        $from = array_filter($from, function ($value) {
            return $value !== null && $value !== '';
        });

        if (!empty($from)) {
            $params['from'] = http_build_query($from);
        }

        return $params;
    }
}
