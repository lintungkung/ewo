<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MSCNS09 extends Model
{
	public $timestamps = false;
	protected $connection = 'WMDB';
	protected $table = 'cossdb.dbo.MSCNS09';
}
