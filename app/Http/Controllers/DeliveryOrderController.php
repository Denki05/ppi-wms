<?php

namespace App\Http\Controllers;

use App\Services\TransApiClient;
use Illuminate\Http\Request;

class DeliveryOrderController extends Controller
{
    /**
     * Checker Transaksi (DO): tab List SPK/Cetak SJ/Update Resi/History Resi.
     */
    public function index(Request $request)
    {
        $tab = strtoupper((string) $request->query('tab', 'SPK'));
        if (!in_array($tab, ['SPK', 'CETAK', 'RESI', 'HISTORY'], true)) {
            $tab = 'SPK';
        }

        $month = trim((string) $request->query('month', ''));
        if (!preg_match('/^\d{4}-(0[1-9]|1[0-2])$/', $month)) {
            $month = '';
        }
        if ($tab === 'HISTORY' && $month === '') {
            $month = now('Asia/Jakarta')->format('Y-m');
        }

        $query = array_filter([
            'tab'     => $tab,
            'search'  => $request->query('search'),
            'printed' => $request->query('printed'),
            'month'   => $tab === 'HISTORY' ? $month : null,
            'page'    => $request->query('page'),
        ], function ($value) {
            return $value !== null && $value !== '';
        });

        $result = (new TransApiClient)->deliveryOrders(session('wms.token'), $query);

        return view('delivery_order.index', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Daftar DO belum bisa dimuat.'),
            'filters'      => [
                'tab'     => $tab,
                'search'  => (string) $request->query('search', ''),
                'printed' => (string) $request->query('printed', ''),
                'month'   => $month,
            ],
        ]);
    }

    /**
     * Detail DO: checklist kerjakan, surat jalan, update resi, history.
     */
    public function show(Request $request, $id)
    {
        $result = (new TransApiClient)->deliveryOrderDetail(session('wms.token'), $id);

        $back = $this->backQuery($request);

        return view('delivery_order.show', [
            'user'         => session('wms.user'),
            'id'           => (int) $id,
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Detail DO belum bisa dimuat.'),
            'backUrl'      => route('delivery_order', $back),
            'backQuery'    => http_build_query($back),
        ]);
    }

    /**
     * Print SPK: PDF diproksikan dari Trans (butuh bearer token).
     */
    public function printSpk(Request $request, $id)
    {
        $result = (new TransApiClient)->printManifest(session('wms.token'), $id);

        if (!$result['ok']) {
            return redirect()
                ->route('delivery_order.show', ['id' => (int) $id, 'from' => $request->query('from')])
                ->with('error', $result['message'] ?? 'Print SPK gagal.');
        }

        return response($result['body'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SPK-' . (int) $id . '.pdf"',
        ]);
    }

    /**
     * Cetak SJ: PDF surat jalan diproksikan dari Trans (Crystal print DO).
     */
    public function printSj(Request $request, $id)
    {
        $result = (new TransApiClient)->printSuratJalan(session('wms.token'), $id);

        if (!$result['ok']) {
            return redirect()
                ->route('delivery_order.show', ['id' => (int) $id, 'from' => $request->query('from')])
                ->with('error', $result['message'] ?? 'Cetak SJ gagal.');
        }

        return response($result['body'], 200, [
            'Content-Type'        => 'application/pdf',
            'Content-Disposition' => 'inline; filename="SJ-' . (int) $id . '.pdf"',
        ]);
    }

    public function packed(Request $request, $id)
    {
        $result = (new TransApiClient)->packDeliveryOrder(session('wms.token'), $id);

        return $this->backToDetail($request, $id, $result, 'DO berhasil diubah ke Siap Kirim.');
    }

    public function sending(Request $request, $id)
    {
        $result = (new TransApiClient)->sendDeliveryOrder(session('wms.token'), $id);

        return $this->backToDetail($request, $id, $result, 'DO berhasil diubah ke Delivering.');
    }

    public function sent(Request $request, $id)
    {
        $request->validate([
            'image'          => 'nullable|image|max:4096',
            'image2'         => 'nullable|image|max:4096',
            'other_cost_idr' => 'nullable|numeric',
            'other_cost_note' => 'nullable|string|max:255',
        ]);

        $result = (new TransApiClient)->deliverOrder(
            session('wms.token'),
            $id,
            [
                'other_cost_idr'  => (float) $request->input('other_cost_idr', 0),
                'other_cost_note' => (string) $request->input('other_cost_note', ''),
            ],
            $request->file('image'),
            $request->file('image2')
        );

        return $this->backToDetail($request, $id, $result, 'DO berhasil update resi.');
    }

    private function backToDetail(Request $request, $id, array $result, $successMessage)
    {
        return redirect()
            ->route('delivery_order.show', ['id' => (int) $id, 'from' => $request->input('from')])
            ->with($result['ok'] ? 'ok' : 'error', $result['ok']
                ? ($result['message'] ?: $successMessage)
                : ($result['message'] ?? $successMessage));
    }

    private function backQuery(Request $request)
    {
        parse_str((string) $request->query('from', ''), $back);
        $back = array_intersect_key($back, array_flip(['tab', 'search', 'printed', 'month', 'page']));

        return array_filter($back, function ($value) {
            return $value !== null && $value !== '';
        });
    }
}
