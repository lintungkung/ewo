<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class wm_orderlist extends Model
{
	public $timestamps = false;
	protected $primaryKey = 'Id';
	protected $connection = 'WMDB';
	protected $table = 'dbo.wm_orderlist';

}
