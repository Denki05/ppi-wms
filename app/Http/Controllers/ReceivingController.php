<?php

namespace App\Http\Controllers;

use App\Services\TransApiClient;
use Illuminate\Http\Request;

class ReceivingController extends Controller
{
    /**
     * Receiving (type inbound): satu request ke Trans per pemuatan/filter.
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

        $result = (new TransApiClient)->receiving(session('wms.token'), $query);

        return view('receiving.index', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Daftar penerimaan belum bisa dimuat.'),
            'filters'      => [
                'status' => strtoupper((string) $request->query('status', 'ACTIVE')),
                'search' => (string) $request->query('search', ''),
                'month'  => $month,
            ],
        ]);
    }

    /**
     * Daftar penerimaan hanya untuk tahun berjalan; default bulan berjalan.
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
     * Detail penerimaan + hak ACC.
     */
    public function show(Request $request, $id)
    {
        $result = (new TransApiClient)->receivingDetail(session('wms.token'), $id);

        // Kembali ke daftar dengan filter terakhir, bukan halaman default.
        parse_str((string) $request->query('from', ''), $back);
        $back = array_intersect_key($back, array_flip(['status', 'search', 'month', 'page']));
        $back = array_filter($back, function ($value) {
            return $value !== null && $value !== '';
        });

        return view('receiving.show', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Detail penerimaan belum bisa dimuat.'),
            'backUrl'      => route('receiving', $back),
        ]);
    }

    /**
     * ACC penerimaan; stok & PO diproses di Trans.
     */
    public function acc(Request $request, $id)
    {
        $result = (new TransApiClient)->accReceiving(session('wms.token'), $id);

        if ($result['ok']) {
            return redirect()
                ->route('receiving.show', ['id' => (int) $id])
                ->with('ok', $result['message'] ?: 'Receiving berhasil di-ACC.');
        }

        return redirect()
            ->route('receiving.show', ['id' => (int) $id])
            ->with('error', $result['message'] ?? 'ACC penerimaan gagal.');
    }

    public function publish(Request $request, $id)
    {
        $result = (new TransApiClient)->publishReceiving(session('wms.token'), $id);

        return $this->backToDetail($id, $result, 'Publish penerimaan gagal.');
    }

    public function cancel(Request $request, $id)
    {
        $result = (new TransApiClient)->cancelReceiving(session('wms.token'), $id);

        return $this->backToDetail($id, $result, 'Pembatalan penerimaan gagal.');
    }

    public function rollback(Request $request, $id)
    {
        $result = (new TransApiClient)->rollbackReceiving(session('wms.token'), $id);

        return $this->backToDetail($id, $result, 'Rollback penerimaan gagal.');
    }

    private function backToDetail($id, array $result, $fallback)
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

        if ($result['ok']) {
            return redirect()
                ->route('receiving.show', $params)
                ->with('ok', $result['message'] ?: 'Aksi berhasil diproses.');
        }

        return redirect()
            ->route('receiving.show', $params)
            ->with('error', $result['message'] ?? $fallback);
    }
}
