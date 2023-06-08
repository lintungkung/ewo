<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class wm_dstb_remotes extends Model
{
	protected $connection = 'WMDB';
	protected $table = 'dbo.wm_dstb_remotes';

    protected $fillable = [
        'CompanyNo',
        'CustID',
        'SubsID',
        'AssignSheet',
        'SingleSN',
        'remoteQrCode',
        'remoteVendor',
        'userCode',
        'userName',
        'created_at',
        'updated_at'
    ];

}
