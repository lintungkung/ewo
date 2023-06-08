<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::post('ewo/AuthorSTB', 'ApiController@AuthorSTB');
Route::get('ewo/AuthorSTB', 'ApiController@AuthorSTB');



Route::post('authorstb', 'ApiController@AuthorSTB'); //開通
Route::post('installedfinished', 'ApiController@InstalledFinished'); //裝機工單完工
Route::post('maintainfinished', 'ApiController@MaintainFinished'); //維修工單完工
Route::post('removefinished', 'ApiController@RemoveFinished'); //拆機工單完工
Route::post('chargeback', 'ApiController@Chargeback'); //退單

Route::post('changedevice', 'ApiController@ChangeDevice'); //更換設備序號

Route::post('servicesecondreason', 'ApiController@ServiceReasonSecond'); //更換設備序號

Route::post('creditcard', 'ApiController@CreditCard'); //信用卡刷卡

Route::post('cmqualityforkg', 'ApiController@InternetQuality'); //即時網路品質參數

Route::get('createpdf/{source}/{version}/{id}', 'pdfController@index'); //產生PDF

Route::post('costmodify', 'ApiController@changMaintainCost'); //維修，修改維修金額

Route::post('chargeproduct', 'ApiController@chargeProduct'); //維修，加購產品

Route::post('restcm', 'ApiController@RestCM'); //RestCM 重置CM

//Route::post('restartcm', 'ApiController@ReStartCM'); //ReStartCM 重開CM 2022-10-14停用

Route::post('cmwifirestart', 'ApiController@CMWifiRestart'); // CM-Wifi重開

Route::post('cmmacinfo', 'ApiController@CMMACInfo'); // CM MAC 連線資訊

Route::get('updatepdfinfo/{version}/{id}', 'pdfController@UpdatePdfInfo'); //產生PDF

Route::post('changepwd', 'ApiController@ChangePWD'); //修改密碼

Route::post('rebootdstb', 'ApiController@RebootDSTB'); // 重置 DSTB

Route::post('sentmailpdf', 'ApiController@SentMailPDF'); // 寄送mail[PDF]

Route::post('getpushmsg', 'ApiController@GetPushMsg'); // 取得push msg

Route::post('getfault', 'ApiController@getFault'); // 區故查詢

//Route::post('getaddsign', 'ApiController@getAddSign'); // 取得[補]簽名

Route::post('getstatistics', 'ApiController@GetStatistics'); // 取得push msg

Route::post('checkauth20', 'ApiController@checkAuth20'); // authcheck

Route::post('proceede015', 'ApiController@proceedE015'); // proceedE015 頻道重新授權

Route::ANY('imgupload/{p1}', 'ApiController@imgUpload'); // r1上傳圖片

Route::POST('sumReceivableAMT', 'ApiController@sumReceivableAMT'); // 計算應收金額

Route::POST('consumables/{p1}', 'ConsumablesAPIController@index'); // 倉管連動

Route::POST('EMS/{p1}', 'emsAPIController@index'); // EMS API

Route::POST('EWO/{p1}', 'ewoAPIController'); // EWO API

Route::POST('EWOFUNC', 'ewoFuncAPIController'); // EWO Func API


////倉管連動
//Route::prefix('consumables')->group(function () {
//    Route::POST('search','ConsumablesAPIController@search');
//    Route::POST('allot','ConsumablesAPIController@egallot');
//
//});
