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
    
    Route::get('/manually/data', ['as' => 'manually.data', 'uses' => 'ManuallyController@listIndex']);
    Route::get('/manually/print-preview/{id}', ['as' => 'manually.print-preview', 'uses' => 'ManuallyController@printPreview']);
	Route::resource('/manually', 'ManuallyController');
    
	Route::get('/setting', ['as' => 'setting.index', 'uses' => 'SettingController@index']);
	Route::patch('/setting', ['as' => 'setting.index', 'uses' => 'SettingController@update']);
    
    Route::get('doctor/find', ['as' => 'doctor.find', 'uses' => 'AjaxController@findDoctor']);
    Route::get('doctor/find-and-unit', ['as' => 'doctor.find-and-unit', 'uses' => 'AjaxController@findDoctorAndUnit']);
    Route::get('medicine/find', ['as' => 'medicine.find', 'uses' => 'AjaxController@findMedicine']);
    Route::get('patient/find', ['as' => 'patient.find', 'uses' => 'AjaxController@findPatient']);
    Route::get('medicine/how-to-use', ['as' => 'medicine.how-to-use', 'uses' => 'AjaxController@findMedicineHowToUse']);
    
    Route::get('/user/edit-profile', ['as' => 'user.edit-profile', 'uses' => 'UserController@editProfile']);
    Route::get('/user/data', ['as' => 'user.data', 'uses' => 'UserController@listIndex']);
    Route::resource('/user', 'UserController');
    
    Route::get('/transaction-medicine/doctor-data', ['as' => 'transaction-medicine.doctor-data', 'uses' => 'TransactionMedicineController@listDoctorData']);
    Route::get('/transaction-medicine/doctor', ['as' => 'transaction-medicine.doctor', 'uses' => 'TransactionMedicineController@doctor']);
    
    Route::get('/transaction-medicine/pharmacist-data', ['as' => 'transaction-medicine.pharmacist-data', 'uses' => 'TransactionMedicineController@listPharmacistData']);
    Route::get('/transaction-medicine/pharmacist', ['as' => 'transaction-medicine.pharmacist', 'uses' => 'TransactionMedicineController@pharmacist']);
    
    Route::get('/transaction-add-medicine/list-index', ['as' => 'transaction-add-medicine.list-index', 'uses' => 'TransactionAddMedicineController@listIndex']);
    Route::get('/transaction-add-medicine/index', ['as' => 'transaction-add-medicine.index', 'uses' => 'TransactionAddMedicineController@index']);
    Route::get('/transaction-add-medicine/{id}/edit', ['as' => 'transaction-add-medicine.edit', 'uses' => 'TransactionAddMedicineController@edit']);
    Route::patch('/transaction-add-medicine/{id}', ['as' => 'transaction-add-medicine.update', 'uses' => 'TransactionAddMedicineController@update']);
    Route::get('/transaction-add-medicine/{id}/print', ['as' => 'transaction-add-medicine.print', 'uses' => 'TransactionAddMedicineController@printPreview']);
    Route::post('/transaction-add-medicine/{id}/post-print', ['as' => 'transaction-add-medicine.post-print', 'uses' => 'TransactionAddMedicineController@postPrint']);
    
    Route::get('/report/list-index', ['as' => 'report.list-index', 'uses' => 'ReportController@listIndex']);
    Route::get('/report/index', ['as' => 'report.index', 'uses' => 'ReportController@index']);
});
