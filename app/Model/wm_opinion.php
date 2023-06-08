<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class wm_opinion extends Model
{
	public $timestamps = false;
	protected $primaryKey = 'Id';
	protected $connection = 'WMDB';
	protected $table = 'dbo.wm_opinion';
    protected $fillable = [
        'Id',
        'userCode',
        'userName',
        'companyNo',
        'subsid',
        'queryType',
        'queryDesc',
        'answer',
        'file',
        'status',
    ];
}
