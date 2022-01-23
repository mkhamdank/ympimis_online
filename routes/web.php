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

if (version_compare(PHP_VERSION, '7.2.0', '>=')) {
	error_reporting(E_ALL ^ E_NOTICE ^ E_WARNING);
}

Route::get('index/employee/service', 'EmployeeController@indexEmployeeService')->name('emp_service');

Route::get('minkd', 'TrialController@minkd');

Route::get('testmail', 'TrialController@testmail');
Route::get('testmail2', 'AccountingController@coba');
Route::get('testprint', 'TrialController@testPrint');
Route::get('tesurgent', 'MaintenanceController@indexSPKUrgent');

Route::get('fetch_trial2', 'StockTakingController@printSummary');
Route::get('test', 'TrialController@testTgl');

Route::get('trial_load', 'TrialController@trialload');
Route::get('trial_loc', 'TrialController@trialLoc');
Route::get('trial_loc2/{lat}/{long}', 'TrialController@getLocation');

Route::get('index/whatsapp_api', 'ChatBotController@index');
Route::get('whatsapp_api', 'TrialController@indexWhatsappApi');
Route::get('kirimTelegram/{pesan}', 'TrialController@kirimTelegram');

Route::get('index_push_pull_trial', 'TrialController@index_push_pull_trial');
Route::post('push_pull_trial', 'TrialController@push_pull_trial');
Route::get('fetch_push_pull_trial', 'TrialController@fetch_push_pull_trial');

Route::get('/index/emergency_response', 'TrialController@tes2');
Route::get('/index/trials', 'TrialController@tes');
Route::get('/index/unification_uniform', 'VoteController@indexUnificationUniform');
Route::get('fetch/employee/data', 'TrialController@fetch_data');
Route::get('happybirthday', function ()
{
	return view('trials.birthday');
});
Route::get('tesseract', 'TrialController@testTesseract');

Route::get('xml_parser', 'TrialController@xmlParser');
Route::post('xml_parser_upload', 'TrialController@xmlParserUpload');

Route::get('trialmail', 'TrialController@trialmail');

Route::get('/trial', function () {
	return view('trial');
});

Route::get('/trialPrint', function () {
	return view('maintenance/apar/print');
});
Route::get('/index/apar/print', function () {
	return view('maintenance/apar/aparPrint');
});

Route::get('/qr', function () {
	return view('maintenance/apar/aparQr');
});

Route::get('/fetch/trial2', 'PlcController@fetchTemperature');
Route::get('print/trial', 'TrialController@stocktaking');
Route::get('trial_machine', 'TrialController@fetch_machine');

Route::get('/machinery_monitoring', function () {
	return view('plant_maintenance.machinery_monitoring', array(
		'title' => 'Machinery Monitoring',
		'title_jp' => ''
	));
});

Route::get('/information_board', function () {
	return view('information_board')->with('title', 'INFORMATION BOARD')->with('title_jp', '情報板');
});

Auth::routes();

Route::get('/', function () {
	if (Auth::check()) {
		if (Auth::user()->role_code == 'emp-srv') {
			// return redirect()->action('EmployeeController@indexEmployeeService', ['id' => 1]);
			return \redirect()->route('emp_service', ['id' => 1, 'tahun' => date('Y')]);
			// return redirect()->route('index/employee/service/{ctg}', ['ctg' => 'home']);
		} else {
			return view('home');
		}
	} else {
		return view('auth.login');
	}
});

Route::get('/forgot/password', function () {
    return view('auth.passwords.email')->with('success');
})->middleware('guest')->name('password.request');

Route::post('request/reset/password', 'PasswordController@requestResetPassword');
Route::get('reset/password/{id}', 'PasswordController@resetPassword');
Route::post('reset/password/confirm', 'PasswordController@resetPasswordConfirm');

Route::get('register', 'PasswordController@register');
Route::post('register/confirm', 'PasswordController@confirmRegister');

Route::get('404', function() {
	return view('404');
});

Route::get('terms', 'PasswordController@terms');
Route::get('policy', 'PasswordController@policy');

Route::group(['nav' => 'A1', 'middleware' => 'permission'], function(){
	Route::get('index/batch_setting', 'BatchSettingController@index');
	Route::get('create/batch_setting', 'BatchSettingController@create');
	Route::post('create/batch_setting','BatchSettingController@store');
	Route::get('destroy/batch_setting/{id}', 'BatchSettingController@destroy');
	Route::get('edit/batch_setting/{id}', 'BatchSettingController@edit');
	Route::post('edit/batch_setting/{id}', 'BatchSettingController@update');
	Route::get('show/batch_setting/{id}', 'BatchSettingController@show');
});

Route::group(['nav' => 'A6', 'middleware' => 'permission'], function(){
	Route::get('index/user', 'UserController@index');
	Route::get('create/user', 'UserController@create');
	Route::post('create/user','UserController@store');
	Route::get('destroy/user/{id}', 'UserController@destroy');
	Route::get('edit/user/{id}', 'UserController@edit');
	Route::post('edit/user/{id}', 'UserController@update');
	Route::get('show/user/{id}', 'UserController@show');
});

Route::group(['nav' => 'A11', 'middleware' => 'permission'], function(){
	Route::get('index/rio', 'RioController@indexrio');
});

Route::group(['nav' => 'A7', 'middleware' => 'permission'], function(){
	Route::get('index/daily_report', 'DailyReportController@index');
	Route::post('create/daily_report', 'DailyReportController@create');
	Route::post('update/daily_report', 'DailyReportController@update');
	Route::post('delete/daily_report', 'DailyReportController@delete');
	Route::get('fetch/daily_report', 'DailyReportController@fetchDailyReport');
	Route::get('download/daily_report', 'DailyReportController@downloadDailyReport');
	Route::get('fetch/daily_report_detail', 'DailyReportController@fetchDailyReportDetail');
	Route::get('edit/daily_report', 'DailyReportController@edit');
});

Route::get('setting/user', 'UserController@index_setting');
Route::post('setting/user', 'UserController@setting');
Route::post('register', 'RegisterController@register')->name('register');

Route::group(['nav' => 'A3', 'middleware' => 'permission'], function(){
	Route::get('index/navigation', 'NavigationController@index');
	Route::get('create/navigation', 'NavigationController@create');
	Route::post('create/navigation','NavigationController@store');
	Route::get('destroy/navigation/{id}', 'NavigationController@destroy');
	Route::get('edit/navigation/{id}', 'NavigationController@edit');
	Route::post('edit/navigation/{id}', 'NavigationController@update');
	Route::get('show/navigation/{id}', 'NavigationController@show');
});

Route::group(['nav' => 'A4', 'middleware' => 'permission'], function(){
	Route::get('index/role', 'RoleController@index');
	Route::get('create/role', 'RoleController@create');
	Route::post('create/role','RoleController@store');
	Route::get('destroy/role/{id}', 'RoleController@destroy');
	Route::get('edit/role/{id}', 'RoleController@edit');
	Route::post('edit/role/{id}', 'RoleController@update');
	Route::get('show/role/{id}', 'RoleController@show');
});

Route::group(['nav' => 'S0', 'middleware' => 'permission'], function(){
	//ALL
	Route::get('index/outgoing/{vendor}', 'OutgoingController@index');
	Route::get('index/incoming/{vendor}/report', 'OutgoingController@indexReportIncoming');
	Route::get('fetch/incoming/{vendor}/report', 'OutgoingController@fetchReportIncoming');

	//ARISA
	Route::get('index/outgoing/arisa/input', 'OutgoingController@indexInputArisa');
	Route::get('fetch/outgoing/arisa/point_check', 'OutgoingController@fetchPointCheck');
	Route::get('fetch/kensa/arisa/serial_number', 'OutgoingController@fetchKensaSerialNumber');
	Route::post('index/outgoing/arisa/confirm', 'OutgoingController@confirmInputArisa');
	Route::get('index/kensa/arisa', 'OutgoingController@indexKensaArisa');
	Route::get('fetch/inspection_level', 'OutgoingController@fetchInspectionLevel');
	Route::get('fetch/kensa/serial_number/{vendor}', 'OutgoingController@kensaSerialNumber');
	Route::get('fetch/final/serial_number/{vendor}', 'OutgoingController@finalSerialNumber');
	Route::post('index/kensa/arisa/confirm', 'OutgoingController@confirmKensaArisa');

	Route::get('index/kensa/arisa/report', 'OutgoingController@indexReportKensaArisa');
	Route::get('fetch/kensa/arisa/report', 'OutgoingController@fetchReportKensaArisa');

	Route::get('index/outgoing/arisa/report', 'OutgoingController@indexReportQcArisa');
	Route::get('fetch/outgoing/arisa/report', 'OutgoingController@fetchReportQcArisa');
	Route::get('input/outgoing/arisa/so_number', 'OutgoingController@inputSONumberArisa');

	//KBI

	Route::get('index/serial_number/kbi', 'OutgoingController@indexUploadSerialNumberKbi');
	Route::get('fetch/serial_number/kbi', 'OutgoingController@fetchSerialNumberKbi');
	Route::post('upload/serial_number/kbi', 'OutgoingController@uploadSerialNumberKbi');
	Route::get('download/serial_number/kbi', 'OutgoingController@downloadSerialNumberKbi');

	Route::get('index/kensa/kbi', 'OutgoingController@indexKensaKbi');
	Route::get('scan/kensa/kbi', 'OutgoingController@scanKensaKbi');
	Route::get('fetch/kensa/serial_number/{vendor}', 'OutgoingController@kensaSerialNumber');
	Route::post('index/kensa/kbi/confirm', 'OutgoingController@confirmKensaKbi');

	Route::get('index/kensa/kbi/report', 'OutgoingController@indexReportKensaKbi');
	Route::get('fetch/kensa/kbi/report', 'OutgoingController@fetchReportKensaKbi');

	//TRUE

	Route::get('index/outgoing/true/input', 'OutgoingController@indexInputTrue');
	Route::post('index/outgoing/true/confirm', 'OutgoingController@confirmInputTrue');
	Route::get('fetch/outgoing/true/material', 'OutgoingController@fetchMaterialTrue');

	Route::get('index/kensa/true/report', 'OutgoingController@indexReportKensaTrue');
	Route::get('fetch/kensa/true/report', 'OutgoingController@fetchReportKensaTrue');

	Route::get('index/serial_number/true', 'OutgoingController@indexUploadSerialNumberTrue');
	Route::get('fetch/serial_number/true', 'OutgoingController@fetchSerialNumberTrue');
	Route::post('upload/serial_number/true', 'OutgoingController@uploadSerialNumberTrue');
	Route::get('download/serial_number/true', 'OutgoingController@downloadSerialNumberTrue');
	Route::get('update/serial_number/true', 'OutgoingController@updateSerialNumberTrue');
	Route::get('delete/serial_number/true', 'OutgoingController@deleteSerialNumberTrue');
});

Route::get('index/outgoing/ng_rate/{vendor}', 'OutgoingController@indexNgRate');
Route::get('fetch/outgoing/ng_rate/{vendor}', 'OutgoingController@fetchNgRate');
Route::get('fetch/outgoing/ng_rate/detail/{vendor}', 'OutgoingController@fetchNgRateDetail');

Route::get('index/outgoing/pareto/{vendor}', 'OutgoingController@indexPareto');
Route::get('fetch/outgoing/pareto/{vendor}', 'OutgoingController@fetchPareto');
Route::get('fetch/outgoing/pareto/detail/{vendor}', 'OutgoingController@fetchParetoDetail');

Route::get('index/outgoing/lot_status/{vendor}', 'OutgoingController@indexLotStatus');
Route::get('fetch/outgoing/lot_status/{vendor}', 'OutgoingController@fetchLotStatus');
Route::get('fetch/outgoing/lot_status/detail/{vendor}', 'OutgoingController@fetchLotStatusDetail');

Route::get('index/incoming/pareto/{vendor}', 'OutgoingController@indexIncomingPareto');
Route::get('fetch/incoming/pareto/{vendor}', 'OutgoingController@fetchIncomingPareto');
Route::get('fetch/incoming/pareto/detail/{vendor}', 'OutgoingController@fetchIncomingParetoDetail');

Route::get('index/incoming/ng_rate/{vendor}', 'OutgoingController@indexIncomingNgRate');
Route::get('fetch/incoming/ng_rate/{vendor}', 'OutgoingController@fetchIncomingNgRate');
Route::get('fetch/incoming/ng_rate/detail/{vendor}', 'OutgoingController@fetchIncomingNgRateDetail');

Route::get('index/invoice', 'AccountingController@indexInvoice');
Route::get('fetch/invoice', 'AccountingController@fetchInvoice');
Route::get('report/invoice/{id}', 'AccountingController@reportInvoice');

Route::group(['nav' => 'S1', 'middleware' => 'permission'], function(){
	Route::get('index/upload_invoice', 'AccountingController@uploadInvoice');
	Route::post('post/upload_invoice', 'AccountingController@uploadInvoicePost');
	Route::get('fetch/monitoring/invoice', 'AccountingController@fetchInvoiceMonitoring');
});

Route::get('index/payment_request', 'AccountingController@indexPaymentRequest');
Route::get('fetch/payment_request', 'AccountingController@fetchPaymentRequest');
Route::get('fetch/payment_request/list', 'AccountingController@fetchPaymentRequestList');
Route::get('fetch/payment_request/detail', 'AccountingController@fetchPaymentRequestDetail');
Route::get('report/payment_request/{id}', 'AccountingController@reportPaymentRequest');
Route::get('email/payment_request', 'AccountingController@emailPaymentRequest');

//Approval Payment Request
Route::get('payment_request/approvemanager/{id}', 'AccountingController@paymentapprovalmanager');
Route::get('payment_request/approvegm/{id}', 'AccountingController@paymentapprovalgm');
Route::get('payment_request/receiveacc/{id}', 'AccountingController@paymentreceiveacc');
Route::get('payment_request/reject/{id}', 'AccountingController@paymentreject');


Route::group(['nav' => 'S2', 'middleware' => 'permission'], function(){
	Route::get('index/purchasing', 'AccountingController@indexPurchasing');
	Route::get('edit/invoice/{id}', 'AccountingController@editInvoice');
	Route::post('create/payment_request', 'AccountingController@createPaymentRequest');
	Route::get('detail/payment_request', 'AccountingController@fetchPaymentRequestDetailAll');
	Route::post('edit/payment_request', 'AccountingController@editPaymentRequest');
	Route::post('delete/payment_request', 'AccountingController@deletePaymentRequest');
	Route::post('checked/invoice', 'AccountingController@checkInvoice');
});

Route::get('get_supplier', 'AccountingController@getSupplier');
Route::post('update/invoice', 'AccountingController@editInvoicePost');

Route::group(['nav' => 'S3', 'middleware' => 'permission'], function(){
	Route::get('index/accounting', 'AccountingController@indexAccounting');
});


Route::group(['nav' => 'S4', 'middleware' => 'permission'], function(){
	Route::get('index/warehouse', 'AccountingController@indexWarehouse');
});

Route::get('/home', ['middleware' => 'permission', 'nav' => 'Dashboard', 'uses' => 'HomeController@index'])->name('home');

