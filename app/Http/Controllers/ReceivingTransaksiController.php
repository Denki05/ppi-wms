<?php

namespace App\Http\Controllers;

use App\Services\TransApiClient;
use Illuminate\Http\Request;

class ReceivingTransaksiController extends Controller
{
    /**
     * Receiving - Transaksi (type return/QC): satu request per pemuatan/filter.
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

        $result = (new TransApiClient)->receivingTransaksi(session('wms.token'), $query);

        return view('receiving_transaksi.index', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Daftar Receiving - Transaksi belum bisa dimuat.'),
            'filters'      => [
                'status' => strtoupper((string) $request->query('status', 'ACTIVE')),
                'search' => (string) $request->query('search', ''),
                'month'  => $month,
            ],
        ]);
    }

    /**
     * Daftar penerimaan transaksi hanya untuk tahun berjalan; default bulan berjalan.
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

    public function show(Request $request, $id)
    {
        $result = (new TransApiClient)->receivingTransaksiDetail(session('wms.token'), $id);

        parse_str((string) $request->query('from', ''), $back);
        $back = array_intersect_key($back, array_flip(['status', 'search', 'month', 'page']));
        $back = array_filter($back, function ($value) {
            return $value !== null && $value !== '';
        });

        return view('receiving_transaksi.show', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Detail Receiving - Transaksi belum bisa dimuat.'),
            'backUrl'      => route('receiving_transaksi', $back),
        ]);
    }

    public function publish(Request $request, $id)
    {
        $result = (new TransApiClient)->publishReceivingTransaksi(session('wms.token'), $id);

        return $this->backToDetail($id, $result, 'Publish gagal diproses.');
    }

    public function acc(Request $request, $id)
    {
        $result = (new TransApiClient)->accReceivingTransaksi(session('wms.token'), $id);

        return $this->backToDetail($id, $result, 'ACC gagal diproses.');
    }

    public function addDetail(Request $request, $id)
    {
        $validator = \Validator::make($request->all(), [
            'product_pack_id' => 'required',
            'quantity'        => 'required|numeric|min:0.01',
            'no_batch'        => 'nullable|string|max:50',
            'note'            => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('receiving_transaksi.show', ['id' => (int) $id])
                ->with('error', implode(' ', $validator->errors()->all()));
        }

        $result = (new TransApiClient)->addReceivingTransaksiDetail(session('wms.token'), $id, [
            'product_pack_id' => (string) $request->input('product_pack_id'),
            'quantity'        => $request->input('quantity'),
            'no_batch'        => (string) $request->input('no_batch', ''),
            'note'            => (string) $request->input('note', ''),
        ]);

        return $this->backToDetail($id, $result, 'Tambah detail gagal.');
    }

    public function deleteDetail(Request $request, $id, $detailId)
    {
        $result = (new TransApiClient)->deleteReceivingTransaksiDetail(session('wms.token'), $id, $detailId);

        return $this->backToDetail($id, $result, 'Hapus detail gagal.');
    }

    public function storeQc(Request $request, $id, $detailId)
    {
        $validator = \Validator::make($request->all(), [
            'qty_qc'    => 'required|numeric|min:0.1',
            'status_qc' => 'required|in:OK,NOT OK,Not OK',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('receiving_transaksi.show', ['id' => (int) $id])
                ->with('error', implode(' ', $validator->errors()->all()));
        }

        $result = (new TransApiClient)->storeReceivingTransaksiQc(session('wms.token'), $id, $detailId, [
            'qty_qc'    => (float) $request->input('qty_qc'),
            'status_qc' => strtoupper($request->input('status_qc')),
        ]);

        return $this->backToDetail($id, $result, 'Simpan QC gagal.');
    }

    public function deleteQc(Request $request, $id, $detailId, $logId)
    {
        $validator = \Validator::make($request->all(), [
            'qty_qc' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return redirect()
                ->route('receiving_transaksi.show', ['id' => (int) $id])
                ->with('error', implode(' ', $validator->errors()->all()));
        }

        $result = (new TransApiClient)->deleteReceivingTransaksiQc(
            session('wms.token'),
            $id,
            $detailId,
            $logId,
            $request->input('qty_qc')
        );

        return $this->backToDetail($id, $result, 'Hapus log QC gagal.');
    }

    public function pdf(Request $request, $id)
    {
        $result = (new TransApiClient)->receivingTransaksiPdf(session('wms.token'), $id);

        if (!$result['ok']) {
            return redirect()
                ->route('receiving_transaksi.show', ['id' => (int) $id])
                ->with('error', $result['message'] ?? 'Surat jalan gagal dibuat.');
        }

        return response($result['body'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="surat-jalan-' . (int) $id . '.pdf"',
        ]);
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
                ->route('receiving_transaksi.show', $params)
                ->with('ok', $result['message'] ?: 'Aksi berhasil diproses.');
        }

        return redirect()
            ->route('receiving_transaksi.show', $params)
            ->with('error', $result['message'] ?? $fallback);
    }
}
