<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

include_once('install.php');

Route::middleware(['IsInstalled'])->group(function () {

    Route::get('/', function () {
        return redirect('/login');
    });

    Auth::routes();

    Route::get('/business/register', 'BusinessController@getRegister')->name('business.getRegister');
    Route::post('/business/register', 'BusinessController@postRegister')->name('business.postRegister');
    Route::post('/business/register/check-username', 'BusinessController@postCheckUsername')->name('business.postCheckUsername');
    Route::post('/business/register/check-initial', 'BusinessController@postCheckInitial')->name('business.postCheckInitial');
});

Route::get('/link', function () {
    $target = __DIR__ . '/../storage/app/public';
    $shortcut = __DIR__ . '/../public/storage';

    try {
        symlink($target, $shortcut);
        return response()->json("Symlink created successfully.");
    } catch (\Exception $e) {
        return response()->json("Failed to create symlink: " . $e->getMessage());
    }
});

Route::prefix('master')->group(function () {
    Route::get('/', 'Master\MasterController@index')->name('master.index');
});

//Routes for authenticated users only
Route::middleware(['IsInstalled', 'auth', 'SetSessionData', 'language', 'timezone'])->group(function () {

    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::post('/home/get-purchase-details', 'HomeController@getPurchaseDetails');
    Route::post('/home/get-sell-details', 'HomeController@getSellDetails');
    Route::get('/home/get-produk-populer', 'HomeController@getProductPopuler');
    Route::get('/home/get-purchase-supplier', 'HomeController@getPurchaseSupplier');
    Route::get('/home/product-stock-alert', 'HomeController@getProductStockAlert');
    Route::get('/home/purchase-payment-dues', 'HomeController@getPurchasePaymentDues');
    Route::get('/home/sales-payment-dues', 'HomeController@getSalesPaymentDues');

    Route::get('/business/settings', 'BusinessController@getBusinessSettings')->name('business.getBusinessSettings');
    Route::post('/business/update', 'BusinessController@postBusinessSettings')->name('business.postBusinessSettings');
    Route::get('/user/profile', 'UserController@getProfile')->name('user.getProfile');
    Route::post('/user/update', 'UserController@updateProfile')->name('user.updateProfile');
    Route::post('/user/update-password', 'UserController@updatePassword')->name('user.updatePassword');

    Route::resource('brands', 'BrandController');
    Route::resource('simpan-barang', 'SimpanBarangController');

    // Route::resource('payment-account', 'PaymentAccountController');

    Route::resource('tax-rates', 'TaxRateController');

    Route::resource('units', 'UnitController');

    Route::get('/contacts/import', 'ContactController@getImportContacts')->name('contacts.import');
    Route::post('/contacts/import', 'ContactController@postImportContacts');
    Route::post('/contacts/check-contact-id', 'ContactController@checkContactId');
    Route::get('/contacts/customers', 'ContactController@getCustomers');
    Route::get('/contacts/datacus/{id}', 'ContactController@getDataCus');
    Route::resource('contacts', 'ContactController');

    Route::resource('categories', 'CategoryController');

    Route::resource('variation-templates', 'VariationTemplateController');

    Route::get('/products/view-product-group-price/{id}', 'ProductController@viewGroupPrice');
    Route::get('/products/add-selling-prices/{id}', 'ProductController@addSellingPrices');
    Route::get('/products/add-price-quantity/{id}', 'ProductController@addPriceQuantity');
    Route::post('/products/save-selling-prices', 'ProductController@saveSellingPrices');
    Route::post('/products/save-price-quantities', 'ProductController@savePriceQuantity');
    Route::post('/products/mass-delete', 'ProductController@massDestroy');
    Route::get('/products/view/{id}', 'ProductController@view');
    Route::get('/products/list', 'ProductController@getProducts');
    Route::get('/products/list-no-variation', 'ProductController@getProductsWithoutVariations');

    Route::post('/products/get_sub_categories', 'ProductController@getSubCategories');
    Route::post('/products/product_form_part', 'ProductController@getProductVariationFormPart');
    Route::post('/products/get_product_variation_row', 'ProductController@getProductVariationRow');
    Route::post('/products/get_variation_template', 'ProductController@getVariationTemplate');
    Route::get('/products/get_variation_value_row', 'ProductController@getVariationValueRow');
    Route::post('/products/check_product_sku', 'ProductController@checkProductSku');
    Route::get('/products/quick_add', 'ProductController@quickAdd');
    Route::post('/products/save_quick_product', 'ProductController@saveQuickProduct');

    Route::resource('products', 'ProductController');

    Route::get('/purchases/get_products', 'PurchaseController@getProducts');
    Route::get('/purchases/get_suppliers', 'PurchaseController@getSuppliers');
    Route::post('/purchases/get_purchase_entry_row', 'PurchaseController@getPurchaseEntryRow');
    Route::post('/purchases/check_ref_number', 'PurchaseController@checkRefNumber');
    Route::get('/purchases/print/{id}', 'PurchaseController@printInvoice');
    Route::resource('purchases', 'PurchaseController');

    Route::get('/sells/duplicate/{id}', 'SellController@duplicateSell');
    Route::get('/sells/drafts', 'SellController@getDrafts');
    Route::get('/sells/quotations', 'SellController@getQuotations');
    Route::get('/sells/draft-dt', 'SellController@getDraftDatables');
    Route::get('sells/get-customer-group-id', 'SellController@getCustomerGroupId');
    Route::resource('sells', 'SellController');

    Route::get('/sells/pos/get_product_row/{variation_id}/{location_id}', 'SellPosController@getProductRow');
    Route::post('/sells/pos/get_payment_row', 'SellPosController@getPaymentRow');
    Route::get('/sells/pos/get-recent-transactions', 'SellPosController@getRecentTransactions');
    Route::get('/sells/{transaction_id}/print', 'SellPosController@printInvoice')->name('sell.printInvoice');
    Route::get('/sells/{transaction_id}/printNota', 'SellPosController@printNota')->name('sell.printNota');
    Route::get('/sells/pos/get-product-suggestion', 'SellPosController@getProductSuggestion');
    Route::resource('pos', 'SellPosController');
    Route::post('/pos/get_amount_pos', 'SellPosController@getAmountPos');

    // pre-order
    Route::resource('preorder', 'PreOrderController');
    Route::get('/preorder/{transaction_id}/print', 'PreOrderController@printPreOrder')->name('preorder.printPreOrder');
    Route::get('/preorder/get_product_row/{variation_id}/{location_id}', 'PreOrderController@getProductRow');
    Route::post('/preorder/save_pre_order', 'PreOrderController@storePayPreOrder');
    Route::post('/preorder/cancel_pre_order/{id}', 'PreOrderController@cancelPreOrder');


    Route::resource('roles', 'RoleController');
    Route::resource('permisi', 'PermisiController');


    Route::resource('users', 'ManageUserController');

    Route::resource('group-taxes', 'GroupTaxController');

    Route::get('/barcodes/set_default/{id}', 'BarcodeController@setDefault');
    Route::resource('barcodes', 'BarcodeController');

    //Invoice schemes..
    Route::get('/invoice-schemes/set_default/{id}', 'InvoiceSchemeController@setDefault');
    Route::resource('invoice-schemes', 'InvoiceSchemeController');

    //Print Labels
    Route::get('/labels/show', 'LabelsController@show');
    Route::get('/labels/add-product-row', 'LabelsController@addProductRow');
    Route::post('/labels/preview', 'LabelsController@preview');

    //Laporan
    Route::get('/laporan', 'LaporanController@index')->name('laporan');
    Route::get('/tutup-saldo', 'LaporanController@tutup')->name('tutup-saldo');

    Route::get('/laporan/inventaris', 'LaporanController@inventaris');
    Route::get('/laporan/biaya_dibayar_dimuka', 'LaporanController@inventaris');
    Route::get('/laporan/aktiva_tetap', 'LaporanController@inventaris');

    Route::get('/laporan/neraca', 'LaporanController@neraca')->name('neraca');
    Route::get('/laporan/saldo_neraca', 'LaporanController@saldo_neraca')->name('saldo_neraca');
    Route::get('/laporan/laba_rugi', 'LaporanController@laba_rugi')->name('laba_rugi');
    Route::get('/laporan/buku_besar', 'LaporanController@buku_besar')->name('buku_besar');
    Route::get('/laporan/cover', 'LaporanController@cover')->name('cover');

    //tutup buku
    Route::get('/tutup_buku', 'TutupBukuController@index')->name('tutup_buku');
    Route::post('/do_tutup_buku', 'TutupBukuController@doTutupBuku')->name('do_tutup_buku');

    //Reports...
    Route::get('/reports/service-staff-report', 'ReportController@getServiceStaffReport');
    Route::get('/reports/table-report', 'ReportController@getTableReport');
    Route::get('/reports/profit-loss', 'ReportController@getProfitLoss');
    Route::get('/reports/get-opening-stock', 'ReportController@getOpeningStock');
    Route::get('/reports/purchase-sell', 'ReportController@getPurchaseSell');
    Route::get('/reports/customer-supplier', 'ReportController@getCustomerSuppliers');
    Route::get('/reports/stock-report', 'ReportController@getStockReport');
    Route::get('/reports/stock-details', 'ReportController@getStockDetails');
    Route::get('/reports/tax-report', 'ReportController@getTaxReport');
    Route::get('/reports/trending-products', 'ReportController@getTrendingProducts');
    Route::get('/reports/expense-report', 'ReportController@getExpenseReport');
    Route::get('/reports/stock-adjustment-report', 'ReportController@getStockAdjustmentReport');

    Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', 'StockAdjustmentController@removeExpiredStock');
    Route::post('/stock-adjustments/get_product_row', 'StockAdjustmentController@getProductRow');
    Route::resource('stock-adjustments', 'StockAdjustmentController');

    //new so
    Route::get('/penyesuaian/daftar-stock-opname', 'StockAdjustmentController@getDaftarSO');
    Route::get('/penyesuaian/print-daftar-so', 'StockAdjustmentController@printDaftarSO');
    Route::get('/penyesuaian/stock-adjustments', 'StockAdjustmentController@getStockAdjustment');
    Route::get('/stock/{id}/print', 'StockAdjustmentController@print');

    Route::get('/reports/register-report', 'ReportController@getRegisterReport');
    Route::get('/reports/sales-representative-report', 'ReportController@getSalesRepresentativeReport');
    Route::get('/reports/sales-representative-total-expense', 'ReportController@getSalesRepresentativeTotalExpense');
    Route::get('/reports/sales-representative-total-sell', 'ReportController@getSalesRepresentativeTotalSell');
    Route::get('/reports/sales-representative-total-commission', 'ReportController@getSalesRepresentativeTotalCommission');
    Route::get('/reports/stock-expiry', 'ReportController@getStockExpiryReport');
    Route::get('/reports/stock-expiry-edit-modal/{purchase_line_id}', 'ReportController@getStockExpiryReportEditModal');
    Route::post('/reports/stock-expiry-update', 'ReportController@updateStockExpiryReport')->name('updateStockExpiryReport');
    Route::get('/reports/customer-group', 'ReportController@getCustomerGroup');
    Route::get('/reports/product-purchase-report', 'ReportController@getproductPurchaseReport');
    Route::get('/reports/product-sell-report', 'ReportController@getproductSellReport');
    Route::get('/reports/lot-report', 'ReportController@getLotReport');
    Route::get('/reports/purchase-payment-report', 'ReportController@purchasePaymentReport');
    Route::get('/reports/sell-payment-report', 'ReportController@sellPaymentReport');
    Route::get('/reports/laporan-sell-periode', 'ReportController@getSellReportPeriode');
    Route::get('/reports/print-sell-periode', 'ReportController@printSellPeriode');

    //Business Location Settings...
    Route::prefix('business-location/{location_id}')->name('location.')->group(function () {
        Route::get('settings', 'LocationSettingsController@index')->name('settings');
        Route::post('settings', 'LocationSettingsController@updateSettings')->name('settings_update');
    });

    //Business Locations...
    Route::post('business-location/check-location-id', 'BusinessLocationController@checkLocationId');
    Route::resource('business-location', 'BusinessLocationController');

    //Invoice layouts..
    Route::resource('invoice-layouts', 'InvoiceLayoutController');

    //Expense Categories...
    Route::resource('expense-categories', 'ExpenseCategoryController');

    //Expenses...
    Route::resource('expenses', 'ExpenseController');

    //Transaction payments...
    Route::get('/payments/show-child-payments/{payment_id}', 'TransactionPaymentController@showChildPayments');
    Route::get('/payments/view-payment/{payment_id}', 'TransactionPaymentController@viewPayment');
    Route::get('/payments/add_payment/{transaction_id}', 'TransactionPaymentController@addPayment');
    Route::get('/payments/add_pay_order/{transaction_id}', 'TransactionPaymentController@addPayOrder');
    Route::get('/payments/pay-contact-due/{contact_id}', 'TransactionPaymentController@getPayContactDue');
    Route::post('/payments/pay-contact-due', 'TransactionPaymentController@postPayContactDue');
    Route::get('/payments/show-pay-order/{transaction_id}', 'TransactionPaymentController@showPayOrder');
    Route::resource('payments', 'TransactionPaymentController');

    //Printers...
    Route::resource('printers', 'PrinterController');

    Route::get('/stock-adjustments/remove-expired-stock/{purchase_line_id}', 'StockAdjustmentController@removeExpiredStock');
    Route::post('/stock-adjustments/get_product_row', 'StockAdjustmentController@getProductRow');
    Route::resource('stock-adjustments', 'StockAdjustmentController');
    //  Route::get('/stock/stock-adjustments', 'StockAdjustmentController@getStockAdjustment');
    //  Route::get('/penyesuaian/stock-adjustments', 'StockAdjustmentController@getStockAdjustment');

    Route::get('/cash-register/register-details', 'CashRegisterController@getRegisterDetails');
    Route::get('/cash-register/close-register', 'CashRegisterController@getCloseRegister');
    Route::post('/cash-register/close-register', 'CashRegisterController@postCloseRegister');
    Route::resource('cash-register', 'CashRegisterController');

    //Import products
    Route::get('/import-products', 'ImportProductsController@index');
    Route::post('/import-products/store', 'ImportProductsController@store');

    //Sales Commission Agent
    Route::resource('sales-commission-agents', 'SalesCommissionAgentController');

    //Stock Transfer
    Route::get('stock-transfers/print/{id}', 'StockTransferController@printInvoice');
    Route::resource('stock-transfers', 'StockTransferController');

    Route::get('/opening-stock/add/{product_id}', 'OpeningStockController@add');
    Route::post('/opening-stock/save', 'OpeningStockController@save');

    //Customer Groups
    Route::resource('customer-group', 'CustomerGroupController');

    //Import opening stock
    Route::get('/import-opening-stock', 'ImportOpeningStockController@index');
    Route::post('/import-opening-stock/store', 'ImportOpeningStockController@store');

    //Sell return
    Route::resource('sell-return', 'SellReturnController');
    Route::get('sell-return/get-product-row', 'SellReturnController@getProductRow');
    Route::get('/sell-return/print/{id}', 'SellReturnController@printInvoice');
    Route::get('/sell-return/add/{id}', 'SellReturnController@add');
    Route::get('/sell-return/addRetur/{id}', 'SellReturnController@addReturAsli');

    //Backup
    Route::get('backup/download/{file_name}', 'BackUpController@download');
    Route::get('backup/delete/{file_name}', 'BackUpController@delete');
    Route::resource('backup', 'BackUpController', ['only' => [
        'index', 'create', 'store'
    ]]);

    //tutup buku
    Route::get('/tutup_buku', 'TutupBukuController@index')->name('tutup_buku');
    Route::post('/do_tutup_buku', 'TutupBukuController@doTutupBuku')->name('do_tutup_buku');

    Route::resource('jurnal', 'JurnalController');

    Route::resource('selling-price-group', 'SellingPriceGroupController');
    Route::resource('price-quantity-group', 'PriceQuantityGroupController');
    Route::resource('variation-quantity', 'VariationGroupQuantityController');

    Route::resource('notification-templates', 'NotificationTemplateController')->only(['index', 'store']);
    Route::get('notification/get-template/{transaction_id}/{template_for}', 'NotificationController@getTemplate');
    Route::post('notification/send', 'NotificationController@send');

    Route::get('/purchase-return/add/{id}', 'PurchaseReturnController@add');
    Route::resource('/purchase-return', 'PurchaseReturnController');

    //Restaurant module
    Route::group(['prefix' => 'modules'], function () {

        Route::resource('tables', 'Restaurant\TableController');
        Route::resource('modifiers', 'Restaurant\ModifierSetsController');

        //Map modifier to products
        Route::get('/product-modifiers/{id}/edit', 'Restaurant\ProductModifierSetController@edit');
        Route::post('/product-modifiers/{id}/update', 'Restaurant\ProductModifierSetController@update');
        Route::get('/product-modifiers/product-row/{product_id}', 'Restaurant\ProductModifierSetController@product_row');

        Route::get('/add-selected-modifiers', 'Restaurant\ProductModifierSetController@add_selected_modifiers');

        Route::get('/kitchen', 'Restaurant\KitchenController@index');
        Route::get('/kitchen/mark-as-cooked/{id}', 'Restaurant\KitchenController@markAsCooked');
        Route::post('/refresh-orders-list', 'Restaurant\KitchenController@refreshOrdersList');

        Route::get('/orders', 'Restaurant\OrderController@index');
        Route::get('/orders/mark-as-served/{id}', 'Restaurant\OrderController@markAsServed');
        Route::get('/data/get-pos-details', 'Restaurant\DataController@getPosDetails');
    });

    Route::get('bookings/get-todays-bookings', 'Restaurant\BookingController@getTodaysBookings');
    Route::resource('bookings', 'Restaurant\BookingController');
    Route::resource('jurnal', 'JurnalController');
    Route::get('get_rekening/{id}/{jenis_trx}', 'JurnalController@getRekening')->name('jurnal.get_rekening');
    Route::get('get_pasangan/{id}', 'JurnalController@getPasangan')->name('jurnal.get_pasangan');
    Route::get('get_hutang/{id}', 'JurnalController@getHutang')->name('jurnal.get_hutang');
    Route::resource('pembayaran_hutang', 'PembayaranHutangController');
    Route::get('hapus_pembayaran_hutang/{id}/{payment_id}', 'PembayaranHutangController@hapusPembayaranHutang')->name('hapus_pembayaran_hutang');
    Route::resource('pembayaran_piutang', 'PembayaranPiutangController');
    Route::get('hapus_pembayaran_piutang/{id}/{payment_id}', 'PembayaranPiutangController@hapusPembayaranPiutang')->name('hapus_pembayaran_piutang');
});
