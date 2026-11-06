<?php

namespace App\Http\Controllers;

use App\Services\TransApiClient;
use Illuminate\Http\Request;

class WmsController extends Controller
{
    /**
     * Terima handoff token SSO dari portal Trans, tukar jadi API token WMS,
     * lalu simpan sesi. Satu-satunya request ke Trans saat login.
     */
    public function sso(Request $request)
    {
        $ssoToken = (string) $request->query('sso_token', '');

        if ($ssoToken === '') {
            return $this->error('Token SSO tidak ditemukan. Silakan masuk lewat portal Trans.');
        }

        $result = (new TransApiClient)->exchangeSso($ssoToken);

        if (!$result['ok']) {
            return $this->error($result['message']);
        }

        $data = $result['data'];
        $bootstrap = $data['bootstrap'] ?? [];
        $user = $bootstrap['user'] ?? [];

        if (empty($data['token']) || empty($user['id'])) {
            return $this->error('Respons login WMS tidak lengkap.');
        }

        $request->session()->regenerate();
        $request->session()->put('wms.token', $data['token']);
        $request->session()->put('wms.user', $user);
        $request->session()->put('wms.bootstrap', $bootstrap);
        $request->session()->put('wms.login_at', time());

        return redirect()->route('dashboard');
    }

    public function dashboard(Request $request)
    {
        $user = session('wms.user');
        $token = session('wms.token');
        // Data ringkasan sama untuk semua user WMS, jadi cache-nya dipakai
        // bersama. TTL 60 detik: cukup segar, tapi tidak menembak Trans
        // setiap halaman dibuka.
        $cacheKey = 'wms.dashboard.summary';

        // Tombol "Muat ulang" melewati cache; refresh biasa tetap hemat request.
        if ($request->boolean('fresh')) {
            \Illuminate\Support\Facades\Cache::forget($cacheKey);
        }

        // Pengingat + statistik kinerja diambil sekali dan disimpan bersama.
        $data = \Illuminate\Support\Facades\Cache::remember(
            $cacheKey,
            60,
            function () use ($token) {
                return [
                    'summary'     => (new TransApiClient)->dashboard($token),
                    'performance' => (new TransApiClient)->performance($token),
                ];
            }
        );

        return view('dashboard', [
            'user'    => $user,
            'menus'   => config('wms.menus', []),
            'summary' => $data['summary']['ok'] ? ($data['summary']['data'] ?? []) : null,
            'summaryError' => $data['summary']['ok'] ? null : ($data['summary']['message'] ?? 'Pengingat belum bisa dimuat.'),
            'performance' => $data['performance']['ok'] ? ($data['performance']['data'] ?? null) : null,
        ]);
    }

    public function page($menu)
    {
        $menuConfig = config('wms.menus.' . $menu);

        if (!$menuConfig) {
            abort(404);
        }

        return view('page', [
            'user' => session('wms.user'),
            'menu' => $menuConfig,
        ]);
    }

    public function logout(Request $request)
    {
        $request->session()->forget(['wms.token', 'wms.user', 'wms.bootstrap', 'wms.login_at']);
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->away(config('wms.trans_login_url'));
    }

    protected function error($message)
    {
        return response()->view('error', ['message' => $message], 403);
    }
}
