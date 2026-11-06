<?php

use App\Http\Controllers\DeliveryOrderController;
use App\Http\Controllers\KomplainController;
use App\Http\Controllers\MutasiController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\ReceivingController;
use App\Http\Controllers\ReceivingTransaksiController;
use App\Http\Controllers\StockController;
use App\Http\Controllers\WmsController;

// Handoff dari portal Trans: tukar SSO token lalu masuk dashboard.
Route::get('/module-sso', [WmsController::class, 'sso'])->name('sso');

Route::group(['middleware' => ['wms.session']], function () {
    Route::get('/', [WmsController::class, 'dashboard'])->name('dashboard');
    Route::get('/m/notifications', [NotificationController::class, 'index'])->name('notifications');
    Route::get('/m/notifications/feed', [NotificationController::class, 'feed'])->name('notifications.feed');
    Route::get('/m/notifications/{id}/read', [NotificationController::class, 'read'])->where('id', '[0-9a-fA-F-]{36}')->name('notifications.read');
    Route::post('/m/notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read_all');
    Route::get('/m/do', [DeliveryOrderController::class, 'index'])->name('delivery_order');
    Route::get('/m/do/{id}', [DeliveryOrderController::class, 'show'])->where('id', '[0-9]+')->name('delivery_order.show');
    Route::get('/m/do/{id}/print-spk', [DeliveryOrderController::class, 'printSpk'])->where('id', '[0-9]+')->name('delivery_order.print_spk');
    Route::get('/m/do/{id}/print-sj', [DeliveryOrderController::class, 'printSj'])->where('id', '[0-9]+')->name('delivery_order.print_sj');
    Route::post('/m/do/{id}/packed', [DeliveryOrderController::class, 'packed'])->where('id', '[0-9]+')->name('delivery_order.packed');
    Route::post('/m/do/{id}/sending', [DeliveryOrderController::class, 'sending'])->where('id', '[0-9]+')->name('delivery_order.sending');
    Route::post('/m/do/{id}/sent', [DeliveryOrderController::class, 'sent'])->where('id', '[0-9]+')->name('delivery_order.sent');
    Route::get('/m/mutasi', [MutasiController::class, 'index'])->name('mutasi');
    Route::get('/m/mutasi/{type}/{id}', [MutasiController::class, 'show'])->where('type', 'showroom|gudang')->where('id', '[0-9]+')->name('mutasi.show');
    Route::get('/m/mutasi/{type}/{id}/pdf', [MutasiController::class, 'pdf'])->where('type', 'showroom|gudang')->where('id', '[0-9]+')->name('mutasi.pdf');
    Route::get('/m/mutasi/{type}/{id}/bukti', [MutasiController::class, 'image'])->where('type', 'showroom|gudang')->where('id', '[0-9]+')->name('mutasi.image');
    Route::post('/m/mutasi/{type}/{id}/checklist', [MutasiController::class, 'checklist'])->where('type', 'showroom|gudang')->where('id', '[0-9]+')->name('mutasi.checklist');
    Route::post('/m/mutasi/{type}/{id}/cancel', [MutasiController::class, 'cancel'])->where('type', 'showroom|gudang')->where('id', '[0-9]+')->name('mutasi.cancel');
    Route::post('/m/mutasi/{type}/{id}/next', [MutasiController::class, 'next'])->where('type', 'showroom|gudang')->where('id', '[0-9]+')->name('mutasi.next');
    Route::post('/m/mutasi/{type}/{id}/barang', [MutasiController::class, 'updateBarang'])->where('type', 'showroom|gudang')->where('id', '[0-9]+')->name('mutasi.barang');
    Route::get('/m/receiving', [ReceivingController::class, 'index'])->name('receiving');
    Route::get('/m/receiving/{id}', [ReceivingController::class, 'show'])->where('id', '[0-9]+')->name('receiving.show');
    Route::post('/m/receiving/{id}/acc', [ReceivingController::class, 'acc'])->where('id', '[0-9]+')->name('receiving.acc');
    Route::post('/m/receiving/{id}/publish', [ReceivingController::class, 'publish'])->where('id', '[0-9]+')->name('receiving.publish');
    Route::post('/m/receiving/{id}/cancel', [ReceivingController::class, 'cancel'])->where('id', '[0-9]+')->name('receiving.cancel');
    Route::post('/m/receiving/{id}/rollback', [ReceivingController::class, 'rollback'])->where('id', '[0-9]+')->name('receiving.rollback');
    Route::post('/m/receiving/{id}/details/{detailId}/qc', [ReceivingController::class, 'storeQc'])->where('id', '[0-9]+')->where('detailId', '[0-9]+')->name('receiving.qc_store');
    Route::post('/m/receiving/{id}/details/{detailId}/qc/{logId}/approve', [ReceivingController::class, 'approveQc'])->where('id', '[0-9]+')->where('detailId', '[0-9]+')->where('logId', '[0-9]+')->name('receiving.qc_approve');
    Route::post('/m/receiving/{id}/details/{detailId}/qc/{logId}/delete', [ReceivingController::class, 'deleteQc'])->where('id', '[0-9]+')->where('detailId', '[0-9]+')->where('logId', '[0-9]+')->name('receiving.qc_delete');
    Route::get('/m/receiving-transaksi', [ReceivingTransaksiController::class, 'index'])->name('receiving_transaksi');
    Route::get('/m/receiving-transaksi/{id}', [ReceivingTransaksiController::class, 'show'])->where('id', '[0-9]+')->name('receiving_transaksi.show');
    Route::get('/m/receiving-transaksi/{id}/pdf', [ReceivingTransaksiController::class, 'pdf'])->where('id', '[0-9]+')->name('receiving_transaksi.pdf');
    Route::post('/m/receiving-transaksi/{id}/publish', [ReceivingTransaksiController::class, 'publish'])->where('id', '[0-9]+')->name('receiving_transaksi.publish');
    Route::post('/m/receiving-transaksi/{id}/acc', [ReceivingTransaksiController::class, 'acc'])->where('id', '[0-9]+')->name('receiving_transaksi.acc');
    Route::post('/m/receiving-transaksi/{id}/details', [ReceivingTransaksiController::class, 'addDetail'])->where('id', '[0-9]+')->name('receiving_transaksi.detail_add');
    Route::post('/m/receiving-transaksi/{id}/details/{detailId}/delete', [ReceivingTransaksiController::class, 'deleteDetail'])->where('id', '[0-9]+')->where('detailId', '[0-9]+')->name('receiving_transaksi.detail_delete');
    Route::post('/m/receiving-transaksi/{id}/details/{detailId}/qc', [ReceivingTransaksiController::class, 'storeQc'])->where('id', '[0-9]+')->where('detailId', '[0-9]+')->name('receiving_transaksi.qc_store');
    Route::post('/m/receiving-transaksi/{id}/details/{detailId}/qc/{logId}/delete', [ReceivingTransaksiController::class, 'deleteQc'])->where('id', '[0-9]+')->where('detailId', '[0-9]+')->where('logId', '[0-9]+')->name('receiving_transaksi.qc_delete');
    Route::get('/m/komplain', [KomplainController::class, 'index'])->name('komplain');
    Route::get('/m/komplain/{id}', [KomplainController::class, 'show'])->where('id', '[0-9]+')->name('komplain.show');
    Route::post('/m/komplain/{id}/acc', [KomplainController::class, 'acc'])->where('id', '[0-9]+')->name('komplain.acc');
    Route::get('/m/komplain/{id}/pdf', [KomplainController::class, 'pdf'])->where('id', '[0-9]+')->name('komplain.pdf');
    Route::get('/m/stock', [StockController::class, 'index'])->name('stock');
    Route::get('/m/stock/{warehouseId}/{encoded}', [StockController::class, 'detail'])->where('warehouseId', '[0-9]+')->name('stock.detail');
    Route::get('/m/stock/{warehouseId}/{encoded}/pdf', [StockController::class, 'pdf'])->where('warehouseId', '[0-9]+')->name('stock.pdf');
    Route::get('/m/{menu}', [WmsController::class, 'page'])->name('page');
    Route::get('/logout', [WmsController::class, 'logout'])->name('logout');
});
