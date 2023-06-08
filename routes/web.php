<?php

use Illuminate\Support\Facades\Route;

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
	return view('p');
});

Route::get('download', function () {
	return view('download');
});

// 九宮格
//Route::prefix('ewo/func')->group(function ($router){
////    Route::resource('/','funcListController')->names('wm.wmFuncList');
//
//    $router->resource('/','funcListController')->names('funcList');
//    $router->resource('/{p1}','funcListController')->names('funcList');
//
//    // Survey 新版問卷
////    $router->namespace('\App\Admin\survey\Controllers')->prefix('survey')->group(function ($router) {
////        $router->resource('pageEdit', 'survey_pageEditController')->names('survey.pageEdit');
////        $router->resource('chapterEdit', 'survey_chapterEditController')->names('survey.chapterEdit');
////        $router->resource('chapterPageOrderEdit', 'survey_chapterMapPagesOrderController')->names('survey.chapterPageOrderEdit');
////        $router->match(array('GET', 'POST'), 'feedback/{so}/{custid}/{chapter}', 'survey_feedbackController@index');
////    });
//});


// EWO Electronic Work Order 電子工單
Route::prefix('ewo')->group(function () {

	Route::get('phpinfo', function () {
		echo phpinfo();
		return;
	});


    // 九宮格
	Route::any('func','funcListController')->name('ewoFunc');
	Route::any('func/{p1?}','funcListController');

	Route::get('login', 'Ewo_LoginController@index');
	Route::post('login', 'Ewo_LoginController@login');

	Route::get('order_list/{cns?}', 'Ewo_OrderController@index')->name('ewoOrderList');
	Route::post('order_list/search/{cns}', 'Ewo_OrderController@search');

	Route::get('order_info/{worksheet}/{cns}', 'Ewo_OrderInfoController@index');
	Route::post('order_info/uploadimg', 'Ewo_OrderInfoController@uploadImage');
	Route::post('order_info/delate', 'Ewo_EventController@delateApi');
	Route::post('event', 'Ewo_EventController@eventApi');
	Route::post('checkData', 'Ewo_EventController@checkData');

	Route::post('hardConsAdd', 'Ewo_EventController@hardConsAdd');

    //Route::get('front','TestImgController@index');
	//Route::post('frontUpload', 'TestImgController@upload');

	Route::get('pdf', function () {
		unset($p_data);
		$p_data['header'] = '';
		return view('ewo.order_pdf', compact('p_data'));
	});
	Route::get('8code', function () {
		return view('ewo.order_8code');
	});
	Route::get('sentmail', function () {
		return view('ewo.sendMailPDF');
	});

	Route::get('t_sentmail', 'Ewo_EventController@t_sentmail');
	Route::post('upload/img', 'uploadController@index');

    Route::get('t_pdf/{type}', 'ewoPDFController@createPDF');

    // 勞工安全[上工前上傳勞保圖片]
    Route::get('laborSafety', 'ewoLaborSafetyController@index');

    // 圖片、PDF下載，網址轉碼
    Route::get('download/{p1}', function($p1){

        $p1 = substr($p1,5,strlen($p1)-10);
        $p1 = str_replace('_','/',$p1);
        $path = base64_decode($p1);
        $path = public_path($path);
        if(file_exists($path) === false) {
            return '查無檔案';
        }

        return Response::download($path);
    });

    // PDFv3 header
    Route::get('pdfV3Header/{so}', function($so){
        return view('pdf.table_v3_header',compact('so'));
    });

    //此route group 為 shou 本地開發用測試Controller 勿動
    Route::group(['domain' => 'dev.ewo.com.tw'],function(){
        Route::get('shou_test/order_list_for_shou', '\App\Http\Controllers\Tests\ShouController@orderListForShou');
    });



});

//倉管連動
Route::prefix('consumables')->group(function () {
	Route::get('', 'ConsumablesController@index')->name('consumables.index');
	Route::post('login', 'ConsumablesController@login')->name('consumables.login');;
	Route::get('logout', 'ConsumablesController@logout')->name('consumables.logout');

	Route::middleware(['consumables'])->group(function () {
		Route::get('menu', 'ConsumablesController@menu')->name('consumables.menu');
		Route::get('receive', 'ConsumablesReceiveController@receive')->name('consumables.receive');
		Route::get('list', 'ConsumablesListController@list')->name('consumables.list');
		Route::get('receivelist', 'ConsumablesReceiveListController@index')->name('consumables.recyldevice');
	});
});
