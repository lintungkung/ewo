<?php

namespace App\Api\Controllers;

use App\Http\Controllers\Controller;
use App\Model\wm_opinion;
use Illuminate\Http\File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use Exception;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class apiOpinion
{

    /*
     *  新增英雄帖
     *
    */


    static public function getResult($request)
    {
        $func = data_get($request,'func');

        switch ($func) {
            case 'add':
                $ret = self::add($request);
                break;
            case 'getList':
                $ret = self::getList($request);
                break;
            default:
                $ret = 'apiOpinion;'.$func;
                break;
        }
        return $ret;
    }

    // add
    static public function add($request)
    {
        $code = '0000';
        $data = 'OK';
        $v_id = data_get($request,'id');
        $v_userCode = data_get($request,'userCode');
        $v_userName = data_get($request,'userName');
        $v_companyNo = data_get($request,'companyNo');
        $v_subsid = data_get($request,'subsid');
        $v_queryType = data_get($request,'queryType');
        $v_queryDesc = data_get($request,'queryDesc');
        $v_answer = data_get($request,'answer');

        try {
            $v_fileName = '';

            $validator = Validator::make($request->all()
                ,[
//                        'file' => 'required|mimes:png,jpg,jpeg,csv,txt,pdf|max:2048'
                ]
                ,[
//                        'file.required' => '檔案格式不符',
                ]
            );

            if ($validator->fails()) {
                $error = $validator->errors()->all();
                throw new Exception($error[0],'0510');
            }

            if($request->file('file')) {
                $file = $request->file('file');
                $filename = $v_userCode.'_'.time().'_'.$file->getClientOriginalName();
                $v_fileName = config('order.EWO_URL')."/ewo_opinion/$filename";
                $file->move(public_path('ewo_opinion'),$filename);
            }

            //回到傳送資料來的頁面
            $dataAry = array(
                'userCode' => $v_userCode,
                'userName' => $v_userName,
                'companyNo' => $v_companyNo,
                'subsid' => $v_subsid,
                'queryType' => $v_queryType,
                'queryDesc' => $v_queryDesc,
                'answer' => $v_answer,
                'file' => $v_fileName,
                'status' => 'add',
            );
            if(empty($v_id)) {
                $p_model = wm_opinion::insertGetId($dataAry);
            } else {
                $p_model = wm_opinion::where(['Id'=>$v_id])->update($dataAry);
            }

        } catch (Exception $e) {
            $code = $e->getCode();
            $code = empty($code)? '0500' : substr("000$code",-4);
            $data = $e->getMessage();
        }

        $ret = array(
            'code' => $code,
            'data' => $data,
            'date' => date('Y-m-d H:i:s'),
        );

        return $ret;
    }

    // getList
    static public function getList($request)
    {
        $v_userCode = data_get($request,'userCode');
        $query = wm_opinion::query()->where(['userCode'=>$v_userCode,'status'=>'add'])->get()->toArray();

        $ret = array(
            'code' => '0000',
            'data' => $query,
            'date' => date('Y-m-d H:i:s'),
        );

        return $ret;
    }

}


