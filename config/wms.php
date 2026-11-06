<?php

return [

    // Allowlist user yang boleh masuk WMS (tanpa profil akses baru di Trans).
    'user_ids' => array_values(array_filter(array_map('trim',
        explode(',', env('WMS_USER_IDS', '1,29,33'))
    ))),

    // User pengambil alih operasional (badge Overtake).
    'overtake_user_ids' => array_values(array_filter(array_map('trim',
        explode(',', env('WMS_OVERTAKE_USER_IDS', '33'))
    ))),

    // Trans tetap sumber data; WMS hanya klien API.
    'trans_api_base' => rtrim(env('TRANS_API_BASE_URL', 'http://localhost/ppi-dist/public'), '/'),
    'trans_portal_url' => env('TRANS_PORTAL_URL', 'http://localhost/ppi-dist/public/portal/modules'),
    'trans_url' => env('TRANS_URL', 'http://localhost/ppi-dist/public'),
    'trans_login_url' => env('TRANS_LOGIN_URL', 'http://localhost/ppi-dist/public/auth/superuser'),

    'http_timeout' => (int) env('WMS_HTTP_TIMEOUT', 15),

    /*
    |--------------------------------------------------------------------
    | Menu WMS — pengelompokan visual, tetap mengikuti alur Trans.
    | phase = fase pengerjaan; halaman menampilkan status kesiapan.
    */
    'menus' => [
        'do' => [
            'label'       => 'Checker Transaksi (DO)',
            'short'       => 'Checker Transaksi (DO)',
            'mark'        => 'DO',
            'description' => 'Checklist DO, cetak SPK, sending, dan update resi.',
            'phase'       => 6,
        ],
        'mutasi' => [
            'label'       => 'Checker Mutasi',
            'short'       => 'Checker Mutasi',
            'mark'        => 'MT',
            'description' => 'Checklist mutasi, cetak surat jalan, dan status barang.',
            'phase'       => 5,
        ],
        'receiving' => [
            'label'       => 'Receiving',
            'short'       => 'Receiving',
            'mark'        => 'RC',
            'description' => 'Draft penerimaan, QC, dan ACC penerimaan.',
            'phase'       => 3,
        ],
        'receiving_qc' => [
            'label'       => 'Receiving - Transaksi',
            'short'       => 'Receiving - Transaksi',
            'mark'        => 'QC',
            'description' => 'Input QC dan approval log QC penerimaan.',
            'phase'       => 3,
        ],
        'komplain' => [
            'label'       => 'Receiving - Komplain',
            'short'       => 'Receiving - Komplain',
            'mark'        => 'KM',
            'description' => 'Penerimaan komplain dan ACC ke gudang QC.',
            'phase'       => 4,
        ],
        'stock' => [
            'label'       => 'Inventory - Stock',
            'short'       => 'Inventory - Stock',
            'mark'        => 'ST',
            'description' => 'Lihat stock dan mutasi per gudang.',
            'phase'       => 2,
        ],
    ],

    /*
    |--------------------------------------------------------------------
    | Pengelompokan visual menu header. Urutan tampil mengikuti array ini;
    | caption tiap menu tetap memakai 'label'/'short' dari Trans.
    */
    'menu_groups' => [
        ['label' => 'Checker', 'codes' => ['do', 'mutasi']],
        ['label' => 'Receiving', 'codes' => ['receiving', 'receiving_qc', 'komplain']],
        ['label' => 'Inventory', 'codes' => ['stock']],
    ],

    /*
    |--------------------------------------------------------------------
    | Pengingat dashboard — dikelompokkan per modul.
    | label memakai caption yang sama dengan tab/status di Trans.
    */
    'reminders' => [
        [
            'menu'  => 'do',
            'label' => 'Checker Transaksi (DO)',
            'items' => [
                ['key' => 'do.belum_print', 'label' => 'List SPK', 'hint' => 'DO belum dicetak'],
                ['key' => 'do.siap_kirim', 'label' => 'Cetak SJ', 'hint' => 'DO siap kirim'],
                ['key' => 'do.belum_resi', 'label' => 'Update Resi', 'hint' => 'DO dalam pengiriman'],
            ],
        ],
        [
            'menu'  => 'mutasi',
            'label' => 'Checker Mutasi',
            'items' => [
                ['key' => 'mutasi.perlu_cek', 'label' => 'Aktif', 'hint' => 'Mutasi sudah dipublish'],
                ['key' => 'mutasi.belum_diambil', 'label' => 'Belum Diambil', 'hint' => 'Barang belum diambil'],
            ],
        ],
        [
            'menu'  => 'receiving',
            'label' => 'Receiving',
            'items' => [
                ['key' => 'receiving.qc', 'label' => 'QC', 'hint' => 'Proses QC penerimaan'],
                ['key' => 'receiving.menunggu_acc', 'label' => 'READY', 'hint' => 'Menunggu ACC'],
            ],
        ],
        [
            'menu'  => 'komplain',
            'label' => 'Receiving - Komplain',
            'items' => [
                ['key' => 'komplain.menunggu_acc', 'label' => 'ACTIVE', 'hint' => 'Menunggu ACC'],
            ],
        ],
        [
            'menu'  => 'stock',
            'label' => 'Inventory - Stock',
            'items' => [
                ['key' => 'stock.minus', 'label' => 'Stok minus', 'hint' => 'Qty di bawah nol'],
            ],
        ],
    ],

];
