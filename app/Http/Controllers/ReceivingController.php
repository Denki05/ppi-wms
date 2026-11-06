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

    /**
     * Simpan satu log QC penerimaan (hanya saat status QC).
     */
    public function storeQc(Request $request, $id, $detailId)
    {
        $validator = \Validator::make($request->all(), [
            'qty_qc'      => 'required|numeric|min:0.1',
            'status_qc'   => 'required|in:OK,NOT OK,Not OK',
            'is_sellable' => 'nullable|boolean',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('receiving.show', ['id' => (int) $id])
                ->with('error', implode(' ', $validator->errors()->all()));
        }

        $result = (new TransApiClient)->storeReceivingQc(session('wms.token'), $id, $detailId, [
            'qty_qc'      => (float) $request->input('qty_qc'),
            'status_qc'   => strtoupper($request->input('status_qc')),
            'is_sellable' => $request->boolean('is_sellable') ? 1 : 0,
        ]);

        return $this->backToDetail($id, $result, 'Simpan QC gagal.');
    }

    public function approveQc(Request $request, $id, $detailId, $logId)
    {
        $result = (new TransApiClient)->approveReceivingQc(
            session('wms.token'),
            $id,
            $detailId,
            $logId,
            $this->qcLogFields($request)
        );

        return $this->backToDetail($id, $result, 'Approve QC gagal.');
    }

    public function deleteQc(Request $request, $id, $detailId, $logId)
    {
        $result = (new TransApiClient)->deleteReceivingQc(
            session('wms.token'),
            $id,
            $detailId,
            $logId,
            $this->qcLogFields($request)
        );

        return $this->backToDetail($id, $result, 'Hapus log QC gagal.');
    }

    /**
     * Baris log QC lama di Trans bisa ber-id 0, jadi atribut baris ikut dikirim.
     */
    private function qcLogFields(Request $request)
    {
        return array_filter([
            'qty_qc'      => $request->input('qty_qc'),
            'status_qc'   => $request->input('status_qc'),
            'is_sellable' => $request->input('is_sellable'),
            'is_approved' => $request->input('is_approved'),
        ], function ($value) {
            return $value !== null && $value !== '';
        });
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
