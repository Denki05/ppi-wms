<?php

namespace App\Http\Controllers;

use App\Services\TransApiClient;
use Illuminate\Http\Request;

/**
 * Notifikasi Trans (DO) yang diteruskan ke WMS.
 * Semua data dibaca dari API Trans memakai token sesi WMS.
 */
class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $filter = $request->query('filter') === 'unread' ? 'unread' : 'all';
        $page = max(1, (int) $request->query('page', 1));

        $result = (new TransApiClient)->notifications(session('wms.token'), array_filter([
            'filter' => $filter === 'unread' ? 'unread' : null,
            'page'   => $page,
            'limit'  => 10,
        ], function ($value) {
            return $value !== null;
        }));

        return view('notifications.index', [
            'user'         => session('wms.user'),
            'data'         => $result['ok'] ? ($result['data'] ?? []) : null,
            'errorMessage' => $result['ok'] ? null : ($result['message'] ?? 'Notifikasi belum bisa dimuat.'),
            'filter'       => $filter,
        ]);
    }

    /**
     * Data ringkas untuk lonceng (dipoll tiap 60 detik).
     */
    public function feed()
    {
        $result = (new TransApiClient)->notifications(session('wms.token'), ['limit' => 6]);

        if (!$result['ok']) {
            return response()->json([
                'ok'      => false,
                'message' => $result['message'] ?? 'Notifikasi belum bisa dimuat.',
            ]);
        }

        return response()->json([
            'ok'   => true,
            'data' => $result['data'] ?? [],
        ]);
    }

    /**
     * Klik notifikasi: tandai dibaca lalu buka DO terkait.
     */
    public function read(Request $request, $id)
    {
        $result = (new TransApiClient)->readNotification(session('wms.token'), $id);

        if (!$result['ok']) {
            return redirect()->route('notifications')->with('error', $result['message'] ?? 'Gagal menandai notifikasi.');
        }

        $doId = (int) $request->query('do');

        if ($doId > 0) {
            return redirect()->route('delivery_order.show', ['id' => $doId]);
        }

        return redirect()->route('notifications');
    }

    public function readAll()
    {
        $result = (new TransApiClient)->readAllNotifications(session('wms.token'));

        if (!$result['ok']) {
            return back()->with('error', $result['message'] ?? 'Gagal menandai semua notifikasi.');
        }

        return back()->with('ok', 'Semua notifikasi ditandai sudah dibaca.');
    }
}
