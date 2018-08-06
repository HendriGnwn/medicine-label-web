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
    return redirect('login');
    return view('welcome');
})->name('/');

Auth::routes();

Route::group(['middleware' => ['auth', 'revalidate']], function () {
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/ajax-home-data', 'AjaxController@getHomeData')->name('ajax.home-data');
    Route::get('/ajax-home-report-label', 'AjaxController@getHomeReportLabel')->name('ajax.home-report-label');
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
    
    Route::group(['prefix' => 'report'], function() {
        
        Route::group(['prefix' => 'period'], function() {
            Route::get('/', ['as' => 'report.period', 'uses' => 'ReportController@period']);
            Route::get('/list', ['as' => 'report.period.list', 'uses' => 'ReportController@periodList']);
            Route::get('/list-detail', ['as' => 'report.period.list-detail', 'uses' => 'ReportController@periodListDetail']);
            Route::get('/export-to-excel', ['as' => 'report.period.export-to-excel', 'uses' => 'ReportController@periodExportToExcel']);
        });
        
        Route::group(['prefix' => 'transaction-type'], function() {
            Route::get('/', ['as' => 'report.transaction-type', 'uses' => 'ReportController@transactionType']);
            Route::get('/list', ['as' => 'report.transaction-type.list', 'uses' => 'ReportController@transactionTypeList']);
            Route::get('/export-to-excel', ['as' => 'report.transaction-type.export-to-excel', 'uses' => 'ReportController@transactionTypeExportToExcel']);
        });
        
        Route::get('/daily', ['as' => 'report.daily', 'uses' => 'ReportController@daily']);
        Route::get('/list', ['as' => 'report.list', 'uses' => 'ReportController@showList']);
        Route::get('/export-to-excel', ['as' => 'report.export-to-excel', 'uses' => 'ReportController@exportToExcel']);
    });
    
    Route::get('/how-to-use/data', ['as' => 'how-to-use.data', 'uses' => 'HowToUseController@listIndex']);
	Route::resource('/how-to-use', 'HowToUseController');
});

Route::get('coba2', function() {
   return ('{
			"labels": ["January", "February", "March", "April", "May", "June", "July"],
			"datasets": [{
				"label": "Dataset 1",
				"backgroundColor": "rgb(255, 99, 132)",
				"yAxisID": "y-axis-1",
				"data": [
					10,
					10,
					10,
					10,
					10,
					10,
					10
				]
			}, {
				"label": "Dataset 2",
				"backgroundColor": "rgb(255, 159, 64)",
				"yAxisID": "y-axis-2",
				"data": [
					10,
					10,
					10,
					10,
					10,
					10,
					10
				]
			}]
		}');
});

Route::get('coba1', function() {
    return json_decode('[
  {
    "date": 1493922600000,
    "units": 320
  },
  {
    "date": 1494009000000,
    "units": 552
  },
  {
    "date": 1494095400000,
    "units": 342
  },
  {
    "date": 1494181800000,
    "units": 431
  },
  {
    "date": 1494268200000,
    "units": 251
  },
  {
    "date": 1494354600000,
    "units": 445
  }
]');
});
