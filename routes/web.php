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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::group(['middleware' => 'auth'], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/ajax-home-data', 'AjaxController@getHomeData')->name('ajax.home-data');
    Route::get('trigger-drop-all-big-data', 'HomeController@triggerDropAllOnBigData');
    
    Route::get('/home/count-patient', 'HomeController@countPatient')->name('home.count-patient');
    Route::get('/home/count-patient-data', 'HomeController@countPatientData')->name('home.count-patient-data');
    
    Route::get('/manually/data', ['as' => 'manually.data', 'uses' => 'ManuallyController@listIndex']);
    Route::get('/manually/print-preview/{id}', ['as' => 'manually.print-preview', 'uses' => 'ManuallyController@printPreview']);
	Route::resource('/manually', 'ManuallyController');
    
	Route::get('/setting', ['as' => 'setting.index', 'uses' => 'SettingController@index']);
	Route::patch('/setting', ['as' => 'setting.index', 'uses' => 'SettingController@update']);
    
    Route::get('doctor/find', ['as' => 'doctor.find', 'uses' => 'AjaxController@findDoctor']);
    Route::get('doctor/find-and-unit', ['as' => 'doctor.find-and-unit', 'uses' => 'AjaxController@findDoctorAndUnit']);
    
    Route::get('medicine/find', ['as' => 'medicine.find', 'uses' => 'AjaxController@findMedicine']);
    Route::get('medicine/how-to-use', ['as' => 'medicine.how-to-use', 'uses' => 'AjaxController@findMedicineHowToUse']);
    
    Route::get('patient/find', ['as' => 'patient.find', 'uses' => 'AjaxController@findPatient']);
    Route::get('patient/find-registered', ['as' => 'patient.find-registered', 'uses' => 'AjaxController@findPatientRegistered']);
    Route::post('patient/get-result-find-registered', ['as' => 'patient.get-result-find-registered', 'uses' => 'AjaxController@getResultPatientRegistered']);
    
    Route::get('/user/edit-profile', ['as' => 'user.edit-profile', 'uses' => 'UserController@editProfile']);
    Route::get('/user/data', ['as' => 'user.data', 'uses' => 'UserController@listIndex']);
    Route::resource('/user', 'UserController');
    
    Route::get('/transaction-medicine/doctor-data', ['as' => 'transaction-medicine.doctor-data', 'uses' => 'TransactionMedicineController@listDoctorData']);
    Route::get('/transaction-medicine/doctor', ['as' => 'transaction-medicine.doctor', 'uses' => 'TransactionMedicineController@doctor']);
    
    Route::get('/transaction-medicine/pharmacist-data', ['as' => 'transaction-medicine.pharmacist-data', 'uses' => 'TransactionMedicineController@listPharmacistData']);
    Route::get('/transaction-medicine/pharmacist', ['as' => 'transaction-medicine.pharmacist', 'uses' => 'TransactionMedicineController@pharmacist']);
    
    Route::get('/transaction-add-medicine/list-index', ['as' => 'transaction-add-medicine.list-index', 'uses' => 'TransactionAddMedicineController@listIndex']);
    Route::get('/transaction-add-medicine/index', ['as' => 'transaction-add-medicine.index', 'uses' => 'TransactionAddMedicineController@index']);
    Route::get('/transaction-add-medicine/{id}/{receiptNumber}/edit', ['as' => 'transaction-add-medicine.edit', 'uses' => 'TransactionAddMedicineController@edit']);
    Route::patch('/transaction-add-medicine/{id}/{receiptNumber}', ['as' => 'transaction-add-medicine.update', 'uses' => 'TransactionAddMedicineController@update']);
    Route::get('/transaction-add-medicine/{id}/{receiptNumber}/print', ['as' => 'transaction-add-medicine.print', 'uses' => 'TransactionAddMedicineController@printPreview']);
    Route::post('/transaction-add-medicine/{id}/{receiptNumber}/post-print', ['as' => 'transaction-add-medicine.post-print', 'uses' => 'TransactionAddMedicineController@postPrint']);
    
    Route::get('/report/index', ['as' => 'report.index', 'uses' => 'ReportController@index']);
    Route::get('/report/list', ['as' => 'report.list', 'uses' => 'ReportController@showList']);
    Route::get('/report/export-to-excel', ['as' => 'report.export-to-excel', 'uses' => 'ReportController@exportToExcel']);
    
    Route::get('/how-to-use/data', ['as' => 'how-to-use.data', 'uses' => 'HowToUseController@listIndex']);
	Route::resource('/how-to-use', 'HowToUseController');
});
