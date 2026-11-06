<?php

namespace App\Http\Controllers;

use App\Services\TransApiClient;
use Illuminate\Http\Request;

class StockController extends Controller
{
    /**
     * Inventory - Stock: satu request ke Trans per pemuatan/filter.
     */
    public function index(Request $request)
    {
        $search    = trim((string) $request->query('search', ''));
        $brand     = trim((string) $request->query('brand', ''));
        $packaging = trim((string) $request->query('packaging', ''));

        // Daftar stok baru dimuat setelah ada filter; tanpa filter hanya
        // pilihan filter yang diambil (init) agar tidak query seluruh stok.
        $applied = $search !== '' || $brand !== '' || $packaging !== '' || $request->has('warehouse_id');

        $query = $applied
            ? array_filter([
                'warehouse_id' => $request->query('warehouse_id'),
                'brand'        => $request->query('brand'),
                'packaging'    => $request->query('packaging'),
                'search'       => $request->query('search'),
                'page'         => $request->query('page'),
            ], function ($value) {
                return $value !== null && $value !== '';
            })
            : ['init' => 1];

        $result = (new TransApiClient)->stock(session('wms.token'), $query);

        return view('stock.index', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Data stock belum bisa dimuat.'),
            'applied'      => $applied,
            'filters'      => [
                'warehouse_id' => $request->query('warehouse_id'),
                'brand'        => $brand,
                'packaging'    => $packaging,
                'search'       => $search,
            ],
        ]);
    }

    /**
     * Kartu stock satu produk per bulan + cetak PDF.
     */
    public function detail(Request $request, $warehouseId, $encoded)
    {
        $month = (string) $request->query('month', '');

        $result = (new TransApiClient)->stockDetail(
            session('wms.token'),
            $warehouseId,
            $encoded,
            $month !== '' ? $month : null
        );

        return view('stock.detail', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Kartu stock belum bisa dimuat.'),
            'warehouseId'  => (int) $warehouseId,
            'encoded'      => $encoded,
            'month'        => $month,
            'embed'        => $request->boolean('embed'),
            'backUrl'      => route('stock', $this->backQuery($request)),
        ]);
    }

    public function pdf(Request $request, $warehouseId, $encoded)
    {
        $month = (string) $request->query('month', '');

        $result = (new TransApiClient)->stockPdf(
            session('wms.token'),
            $warehouseId,
            $encoded,
            $month !== '' ? $month : null
        );

        if (!$result['ok']) {
            return redirect()
                ->route('stock.detail', [
                    'warehouseId' => (int) $warehouseId,
                    'encoded'     => $encoded,
                    'month'       => $month,
                ])
                ->with('error', $result['message'] ?? 'Kartu stock gagal dicetak.');
        }

        return response($result['body'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="kartu-stock.pdf"',
        ]);
    }

    /**
     * Kembali ke daftar dengan filter terakhir, bukan halaman default.
     */
    private function backQuery(Request $request)
    {
        parse_str((string) $request->query('from', ''), $back);
        $back = array_intersect_key($back, array_flip(['warehouse_id', 'brand', 'packaging', 'search', 'page']));

        return array_filter($back, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
