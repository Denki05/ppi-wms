<?php

namespace App\Services;

use GuzzleHttp\Client;

class TransApiClient
{
    protected function client()
    {
        return new Client([
            'timeout'         => (int) config('wms.http_timeout', 15),
            'connect_timeout' => 5,
        ]);
    }

    /**
     * Tukar handoff token SSO menjadi API token WMS.
     * Satu-satunya panggilan ke Trans saat login.
     */
    public function exchangeSso($ssoToken)
    {
        try {
            $response = $this->client()->post(
                config('wms.trans_api_base') . '/api/wms/auth/exchange',
                [
                    'form_params' => ['sso_token' => $ssoToken],
                    'headers'     => ['Accept' => 'application/json'],
                    'http_errors' => false,
                ]
            );
        } catch (\Throwable $e) {
            return [
                'ok'      => false,
                'message' => 'Tidak dapat menghubungi Trans. Coba lagi atau masuk lewat portal.',
            ];
        }

        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200 || ($body['status'] ?? '') !== 'success') {
            return [
                'ok'      => false,
                'message' => $body['message'] ?? 'Login WMS ditolak oleh Trans.',
            ];
        }

        return [
            'ok'   => true,
            'data' => $body['data'] ?? [],
        ];
    }

    /**
     * Ringkasan pekerjaan gudang (count) untuk dashboard.
     */
    public function dashboard($token)
    {
        return $this->getJson($token, '/api/wms/dashboard', [], 'Pengingat belum bisa dimuat.');
    }

    /**
     * Daftar stock per gudang (read-only) untuk menu Inventory - Stock.
     */
    public function stock($token, array $query = [])
    {
        return $this->getJson($token, '/api/wms/stock', $query, 'Data stock belum bisa dimuat.');
    }

    public function stockDetail($token, $warehouseId, $encodedPack, $month = null)
    {
        $query = $month ? ['month' => $month] : [];

        return $this->getJson(
            $token,
            '/api/wms/stock/' . (int) $warehouseId . '/detail/' . rawurlencode((string) $encodedPack),
            $query,
            'Kartu stock belum bisa dimuat.'
        );
    }

    public function stockPdf($token, $warehouseId, $encodedPack, $month = null)
    {
        $path = '/api/wms/stock/' . (int) $warehouseId . '/detail/' . rawurlencode((string) $encodedPack) . '/pdf';

        if ($month) {
            $path .= '?month=' . rawurlencode($month);
        }

        return $this->getPdf($token, $path, 'Kartu stock gagal dicetak.');
    }

    public function receiving($token, array $query = [])
    {
        return $this->getJson($token, '/api/wms/receiving', $query, 'Daftar penerimaan belum bisa dimuat.');
    }

    public function receivingDetail($token, $id)
    {
        return $this->getJson($token, '/api/wms/receiving/' . (int) $id, [], 'Detail penerimaan belum bisa dimuat.');
    }

    public function accReceiving($token, $id)
    {
        return $this->postJson($token, '/api/wms/receiving/' . (int) $id . '/acc', [], 'ACC penerimaan gagal.');
    }

    public function publishReceiving($token, $id)
    {
        return $this->postJson($token, '/api/wms/receiving/' . (int) $id . '/publish', [], 'Publish penerimaan gagal.');
    }

    public function cancelReceiving($token, $id)
    {
        return $this->postJson($token, '/api/wms/receiving/' . (int) $id . '/cancel', [], 'Pembatalan penerimaan gagal.');
    }

    public function rollbackReceiving($token, $id)
    {
        return $this->postJson($token, '/api/wms/receiving/' . (int) $id . '/rollback', [], 'Rollback penerimaan gagal.');
    }

    public function storeReceivingQc($token, $id, $detailId, array $fields)
    {
        return $this->postJson(
            $token,
            '/api/wms/receiving/' . (int) $id . '/details/' . (int) $detailId . '/qc',
            $fields,
            'Simpan QC gagal.'
        );
    }

    public function approveReceivingQc($token, $id, $detailId, $logId, array $fields = [])
    {
        return $this->postJson(
            $token,
            '/api/wms/receiving/' . (int) $id . '/details/' . (int) $detailId . '/qc/' . (int) $logId . '/approve',
            $fields,
            'Approve QC gagal.'
        );
    }

    public function deleteReceivingQc($token, $id, $detailId, $logId, array $fields = [])
    {
        return $this->postJson(
            $token,
            '/api/wms/receiving/' . (int) $id . '/details/' . (int) $detailId . '/qc/' . (int) $logId . '/delete',
            $fields,
            'Hapus log QC gagal.'
        );
    }

    public function receivingTransaksi($token, array $query = [])
    {
        return $this->getJson($token, '/api/wms/receiving-transaksi', $query, 'Daftar Receiving - Transaksi belum bisa dimuat.');
    }

    public function receivingTransaksiDetail($token, $id)
    {
        return $this->getJson($token, '/api/wms/receiving-transaksi/' . (int) $id, [], 'Detail Receiving - Transaksi belum bisa dimuat.');
    }

    public function publishReceivingTransaksi($token, $id)
    {
        return $this->postJson($token, '/api/wms/receiving-transaksi/' . (int) $id . '/publish', [], 'Publish Receiving - Transaksi gagal.');
    }

    public function accReceivingTransaksi($token, $id)
    {
        return $this->postJson($token, '/api/wms/receiving-transaksi/' . (int) $id . '/acc', [], 'ACC Receiving - Transaksi gagal.');
    }

    public function addReceivingTransaksiDetail($token, $id, array $fields)
    {
        return $this->postJson($token, '/api/wms/receiving-transaksi/' . (int) $id . '/details', $fields, 'Tambah detail gagal.');
    }

    public function deleteReceivingTransaksiDetail($token, $id, $detailId)
    {
        return $this->postJson($token, '/api/wms/receiving-transaksi/' . (int) $id . '/details/' . (int) $detailId . '/delete', [], 'Hapus detail gagal.');
    }

    public function storeReceivingTransaksiQc($token, $id, $detailId, array $fields)
    {
        return $this->postJson($token, '/api/wms/receiving-transaksi/' . (int) $id . '/details/' . (int) $detailId . '/qc', $fields, 'Simpan QC gagal.');
    }

    public function deleteReceivingTransaksiQc($token, $id, $detailId, $logId, $qty)
    {
        return $this->postJson(
            $token,
            '/api/wms/receiving-transaksi/' . (int) $id . '/details/' . (int) $detailId . '/qc/' . (int) $logId . '/delete',
            ['qty_qc' => (float) $qty],
            'Hapus log QC gagal.'
        );
    }

    public function receivingTransaksiPdf($token, $id)
    {
        return $this->getPdf($token, '/api/wms/receiving-transaksi/' . (int) $id . '/pdf', 'Surat jalan gagal dibuat.');
    }

    public function komplain($token, array $query = [])
    {
        return $this->getJson($token, '/api/wms/komplain', $query, 'Daftar komplain belum bisa dimuat.');
    }

    public function komplainDetail($token, $id)
    {
        return $this->getJson($token, '/api/wms/komplain/' . (int) $id, [], 'Detail komplain belum bisa dimuat.');
    }

    public function accKomplain($token, $id)
    {
        return $this->postJson($token, '/api/wms/komplain/' . (int) $id . '/acc', [], 'ACC komplain gagal.');
    }

    public function komplainPdf($token, $id)
    {
        return $this->getPdf($token, '/api/wms/komplain/' . (int) $id . '/pdf', 'Surat jalan komplain gagal dibuat.');
    }

    public function mutasi($token, array $query = [])
    {
        return $this->getJson($token, '/api/wms/mutasi', $query, 'Daftar mutasi belum bisa dimuat.');
    }

    public function mutasiDetail($token, $type, $id)
    {
        return $this->getJson(
            $token,
            '/api/wms/mutasi/' . $type . '/' . (int) $id,
            [],
            'Detail mutasi belum bisa dimuat.'
        );
    }

    public function mutasiChecklist($token, $type, $id, array $items)
    {
        return $this->postJson(
            $token,
            '/api/wms/mutasi/' . $type . '/' . (int) $id . '/checklist',
            ['items' => array_values($items)],
            'Checklist mutasi gagal disimpan.'
        );
    }

    public function mutasiCancel($token, $type, $id)
    {
        return $this->postJson(
            $token,
            '/api/wms/mutasi/' . $type . '/' . (int) $id . '/cancel',
            [],
            'Pembatalan checklist gagal.'
        );
    }

    public function mutasiNext($token, $type, $id)
    {
        return $this->postJson(
            $token,
            '/api/wms/mutasi/' . $type . '/' . (int) $id . '/next',
            [],
            'Tidak bisa lanjut ke update barang.'
        );
    }

    /**
     * Update status barang (multipart bila ada foto bukti).
     */
    public function mutasiUpdateBarang($token, $type, $id, $statusBarang, $image = null)
    {
        $multipart = [
            ['name' => 'status_barang', 'contents' => (string) $statusBarang],
        ];

        if ($image && $image->isValid()) {
            $multipart[] = [
                'name'     => 'image',
                'contents' => fopen($image->getRealPath(), 'r'),
                'filename' => $image->getClientOriginalName(),
            ];
        }

        try {
            $response = $this->client()->post(
                config('wms.trans_api_base') . '/api/wms/mutasi/' . $type . '/' . (int) $id . '/barang',
                [
                    'multipart' => $multipart,
                    'headers'   => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'http_errors' => false,
                ]
            );
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Update barang gagal. Trans tidak merespons.'];
        }

        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200 || ($body['status'] ?? '') !== 'success') {
            return ['ok' => false, 'message' => $body['message'] ?? 'Update barang gagal.'];
        }

        return ['ok' => true, 'message' => $body['message'] ?? null];
    }

    public function mutasiPdf($token, $type, $id)
    {
        return $this->getPdf(
            $token,
            '/api/wms/mutasi/' . $type . '/' . (int) $id . '/pdf',
            'Surat jalan mutasi gagal dibuat.'
        );
    }

    public function mutasiImage($token, $type, $id)
    {
        return $this->getBinary(
            $token,
            '/api/wms/mutasi/' . $type . '/' . (int) $id . '/image',
            'Bukti foto gagal dimuat.'
        );
    }

    /**
     * Notifikasi DO milik user WMS yang login (dipoll untuk lonceng).
     */
    public function notifications($token, array $query = [])
    {
        return $this->getJson($token, '/api/wms/notifications', $query, 'Notifikasi belum bisa dimuat.');
    }

    public function readNotification($token, $id)
    {
        return $this->postJson(
            $token,
            '/api/wms/notifications/' . rawurlencode((string) $id) . '/read',
            [],
            'Gagal menandai notifikasi.'
        );
    }

    public function readAllNotifications($token)
    {
        return $this->postJson($token, '/api/wms/notifications/read-all', [], 'Gagal menandai semua notifikasi.');
    }

    public function deliveryOrders($token, array $query = [])
    {
        return $this->getJson($token, '/api/wms/delivery-orders', $query, 'Daftar DO belum bisa dimuat.');
    }

    /**
     * Kinerja logistik (kecepatan DO) untuk kartu dashboard.
     */
    public function performance($token)
    {
        return $this->getJson($token, '/api/wms/performance', [], 'Statistik kinerja belum bisa dimuat.');
    }

    public function deliveryOrderDetail($token, $id)
    {
        return $this->getJson($token, '/api/wms/delivery-orders/' . (int) $id, [], 'Detail DO belum bisa dimuat.');
    }

    /**
     * Ambil PDF Print SPK (di Trans sekaligus menaikkan print_count).
     */
    public function printManifest($token, $id)
    {
        try {
            $response = $this->client()->get(
                config('wms.trans_api_base') . '/api/wms/delivery-orders/' . (int) $id . '/print-spk',
                [
                    'headers' => [
                        'Accept'        => 'application/pdf, application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'http_errors' => false,
                ]
            );
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Print SPK gagal. Trans tidak merespons.'];
        }

        $body = (string) $response->getBody();

        if ($response->getStatusCode() !== 200) {
            $decoded = json_decode($body, true);
            return ['ok' => false, 'message' => $decoded['message'] ?? 'Print SPK gagal diproses.'];
        }

        if (stripos($response->getHeaderLine('Content-Type'), 'application/pdf') === false) {
            $decoded = json_decode($body, true);
            return ['ok' => false, 'message' => $decoded['message'] ?? 'Print SPK tidak menghasilkan PDF.'];
        }

        return ['ok' => true, 'body' => $body];
    }

    /**
     * Ambil PDF surat jalan (Crystal print DO di Trans).
     */
    public function printSuratJalan($token, $id)
    {
        return $this->getPdf($token, '/api/wms/delivery-orders/' . (int) $id . '/print-sj', 'Cetak SJ gagal.');
    }

    public function packDeliveryOrder($token, $id)
    {
        return $this->postJson($token, '/api/wms/delivery-orders/' . (int) $id . '/packed', [], 'Kerjakan DO gagal.');
    }

    public function sendDeliveryOrder($token, $id)
    {
        return $this->postJson($token, '/api/wms/delivery-orders/' . (int) $id . '/sending', [], 'Surat jalan gagal diproses.');
    }

    /**
     * Update resi + upload bukti kirim (multipart).
     */
    public function deliverOrder($token, $id, array $fields = [], $image = null, $image2 = null)
    {
        $multipart = [];

        foreach ($fields as $name => $value) {
            $multipart[] = ['name' => $name, 'contents' => (string) $value];
        }

        foreach (['image' => $image, 'image2' => $image2] as $name => $file) {
            if (!$file || !$file->isValid()) {
                continue;
            }

            $multipart[] = [
                'name'     => $name,
                'contents' => fopen($file->getRealPath(), 'r'),
                'filename' => $file->getClientOriginalName(),
            ];
        }

        try {
            $response = $this->client()->post(
                config('wms.trans_api_base') . '/api/wms/delivery-orders/' . (int) $id . '/sent',
                [
                    'multipart' => $multipart,
                    'headers'   => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'http_errors' => false,
                ]
            );
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => 'Update resi gagal. Trans tidak merespons.'];
        }

        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() !== 200 || ($body['status'] ?? '') !== 'success') {
            return ['ok' => false, 'message' => $body['message'] ?? 'Update resi gagal.'];
        }

        return [
            'ok'      => true,
            'data'    => $body['data'] ?? [],
            'message' => $body['message'] ?? null,
        ];
    }

    protected function getPdf($token, $path, $fallbackMessage)
    {
        try {
            // Export Crystal (COM) di Trans bisa lambat; beri waktu lebih longgar.
            $client = new Client([
                'timeout'         => max((int) config('wms.http_timeout', 15), 60),
                'connect_timeout' => 5,
            ]);

            $response = $client->get(
                config('wms.trans_api_base') . $path,
                [
                    'headers' => [
                        'Accept'        => 'application/pdf, application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'http_errors' => false,
                ]
            );
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $fallbackMessage . ' Trans tidak merespons.'];
        }

        $body = (string) $response->getBody();

        if ($response->getStatusCode() !== 200) {
            $decoded = json_decode($body, true);
            return ['ok' => false, 'message' => $decoded['message'] ?? $fallbackMessage . ' (HTTP ' . $response->getStatusCode() . ' dari Trans).'];
        }

        if (stripos($response->getHeaderLine('Content-Type'), 'application/pdf') === false) {
            $decoded = json_decode($body, true);
            return ['ok' => false, 'message' => $decoded['message'] ?? $fallbackMessage . ' Trans tidak mengirim PDF.'];
        }

        return ['ok' => true, 'body' => $body];
    }

    protected function getBinary($token, $path, $fallbackMessage)
    {
        try {
            $response = $this->client()->get(
                config('wms.trans_api_base') . $path,
                [
                    'headers' => [
                        'Accept'        => 'image/*, application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'http_errors' => false,
                ]
            );
        } catch (\Throwable $e) {
            return ['ok' => false, 'message' => $fallbackMessage . ' Trans tidak merespons.'];
        }

        $body = (string) $response->getBody();
        $contentType = $response->getHeaderLine('Content-Type');

        if ($response->getStatusCode() !== 200 || stripos($contentType, 'image/') === false) {
            $decoded = json_decode($body, true);

            return ['ok' => false, 'message' => $decoded['message'] ?? $fallbackMessage];
        }

        return [
            'ok'           => true,
            'body'         => $body,
            'content_type' => $contentType,
        ];
    }

    protected function getJson($token, $path, array $query = [], $fallbackMessage)
    {
        try {
            $response = $this->client()->get(
                config('wms.trans_api_base') . $path,
                [
                    'query'   => $query,
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'http_errors' => false,
                ]
            );
        } catch (\Throwable $e) {
            return [
                'ok'      => false,
                'message' => $fallbackMessage . ' Trans tidak merespons.',
            ];
        }

        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 429) {
            return [
                'ok'      => false,
                'message' => 'Trans sedang membatasi permintaan (429). Tunggu sebentar lalu coba lagi.',
            ];
        }

        if ($response->getStatusCode() !== 200 || ($body['status'] ?? '') !== 'success') {
            return [
                'ok'      => false,
                'message' => $body['message'] ?? $fallbackMessage,
            ];
        }

        return [
            'ok'   => true,
            'data' => $body['data'] ?? [],
        ];
    }

    protected function postJson($token, $path, array $payload = [], $fallbackMessage)
    {
        try {
            $response = $this->client()->post(
                config('wms.trans_api_base') . $path,
                [
                    'json'    => $payload,
                    'headers' => [
                        'Accept'        => 'application/json',
                        'Authorization' => 'Bearer ' . $token,
                    ],
                    'http_errors' => false,
                ]
            );
        } catch (\Throwable $e) {
            return [
                'ok'      => false,
                'message' => $fallbackMessage . ' Trans tidak merespons.',
            ];
        }

        $body = json_decode((string) $response->getBody(), true);

        if ($response->getStatusCode() === 429) {
            return [
                'ok'      => false,
                'message' => 'Trans sedang membatasi permintaan (429). Tunggu sebentar lalu coba lagi.',
            ];
        }

        if ($response->getStatusCode() !== 200 || ($body['status'] ?? '') !== 'success') {
            return [
                'ok'      => false,
                'message' => $body['message'] ?? $fallbackMessage,
            ];
        }

        return [
            'ok'      => true,
            'data'    => $body['data'] ?? [],
            'message' => $body['message'] ?? null,
        ];
    }
}
