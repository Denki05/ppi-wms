<?php

namespace App\Http\Controllers;

use App\Services\TransApiClient;
use Illuminate\Http\Request;

class MutasiController extends Controller
{
    /**
     * Checker Mutasi: jenis showroom/gudang dan tab aktif/belum/selesai.
     */
    public function index(Request $request)
    {
        $type = strtolower((string) $request->query('type', 'showroom'));
        if (!in_array($type, ['showroom', 'gudang'], true)) {
            $type = 'showroom';
        }

        $tab = strtoupper((string) $request->query('tab', 'AKTIF'));
        if (!in_array($tab, ['AKTIF', 'BELUM', 'SELESAI'], true)) {
            $tab = 'AKTIF';
        }

        $query = array_filter([
            'type'   => $type,
            'tab'    => $tab,
            'search' => $request->query('search'),
            'page'   => $request->query('page'),
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        $result = (new TransApiClient)->mutasi(session('wms.token'), $query);

        return view('mutasi.index', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Daftar mutasi belum bisa dimuat.'),
            'filters'      => [
                'type'   => $type,
                'tab'    => $tab,
                'search' => (string) $request->query('search', ''),
            ],
        ]);
    }

    /**
     * Detail mutasi: checklist -> print SJ -> update barang.
     */
    public function show(Request $request, $type, $id)
    {
        $type = $this->normalizeType($type);

        $result = (new TransApiClient)->mutasiDetail(session('wms.token'), $type, $id);

        return view('mutasi.show', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Detail mutasi belum bisa dimuat.'),
            'type'         => $type,
            'mutasiId'     => (int) $id,
            'backUrl'      => route('mutasi', $this->backQuery($request)),
        ]);
    }

    public function checklist(Request $request, $type, $id)
    {
        $type = $this->normalizeType($type);

        $request->validate([
            'items'   => 'required|array|min:1',
            'items.*' => 'integer',
        ]);

        $result = (new TransApiClient)->mutasiChecklist(
            session('wms.token'),
            $type,
            $id,
            $request->input('items', [])
        );

        return $this->backToDetail($request, $type, $id, $result, 'Checklist mutasi gagal disimpan.');
    }

    public function cancel(Request $request, $type, $id)
    {
        $type = $this->normalizeType($type);

        $result = (new TransApiClient)->mutasiCancel(session('wms.token'), $type, $id);

        return $this->backToDetail($request, $type, $id, $result, 'Pembatalan checklist gagal.');
    }

    public function next(Request $request, $type, $id)
    {
        $type = $this->normalizeType($type);

        $result = (new TransApiClient)->mutasiNext(session('wms.token'), $type, $id);

        return $this->backToDetail($request, $type, $id, $result, 'Tidak bisa lanjut ke update barang.');
    }

    public function updateBarang(Request $request, $type, $id)
    {
        $type = $this->normalizeType($type);

        $request->validate([
            'status_barang' => 'required|in:1,2',
            'image'         => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $result = (new TransApiClient)->mutasiUpdateBarang(
            session('wms.token'),
            $type,
            $id,
            $request->input('status_barang'),
            $request->file('image')
        );

        return $this->backToDetail($request, $type, $id, $result, 'Update status barang gagal.');
    }

    public function pdf(Request $request, $type, $id)
    {
        $type = $this->normalizeType($type);

        $result = (new TransApiClient)->mutasiPdf(session('wms.token'), $type, $id);

        if (!$result['ok']) {
            return $this->backToDetail($request, $type, $id, $result, 'Surat jalan mutasi gagal dibuat.');
        }

        return response($result['body'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="mutasi-' . (int) $id . '.pdf"',
        ]);
    }

    public function image(Request $request, $type, $id)
    {
        $type = $this->normalizeType($type);

        $result = (new TransApiClient)->mutasiImage(session('wms.token'), $type, $id);

        if (!$result['ok']) {
            return $this->backToDetail($request, $type, $id, $result, 'Bukti foto gagal dimuat.');
        }

        return response($result['body'], 200, [
            'Content-Type'        => $result['content_type'],
            'Content-Disposition' => 'inline',
        ]);
    }

    private function backToDetail(Request $request, $type, $id, array $result, $fallback)
    {
        $query = ['type' => $type, 'id' => (int) $id];
        $back  = $this->backQuery($request);

        if (!empty($back)) {
            $query['from'] = http_build_query($back);
        }

        $route = route('mutasi.show', $query);

        if ($result['ok']) {
            return redirect($route)->with('ok', $result['message'] ?: 'Berhasil.');
        }

        return redirect($route)->with('error', $result['message'] ?? $fallback);
    }

    private function normalizeType($type)
    {
        return $type === 'gudang' ? 'gudang' : 'showroom';
    }

    /**
     * Kembali ke daftar dengan tab/filter terakhir.
     */
    private function backQuery(Request $request)
    {
        parse_str((string) $request->query('from', ''), $back);
        $back = array_intersect_key($back, array_flip(['type', 'tab', 'search', 'page']));

        return array_filter($back, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
