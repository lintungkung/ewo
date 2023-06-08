<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class wm_testResultLog extends Model
{
	public $timestamps = false;
	protected $primaryKey = 'Id';
	protected $connection = 'WMDB';
	protected $table = 'dbo.wm_testResultLog';
    protected $fillable = [
        'companyNo',
        'workSheet',
        'custId',
        'subsId',
        'userCode',
        'userName',
        'type',
        'info',
        'result',
        'singleSn',
        'source',
        'datalist',
        'created_at',
    ];
}
