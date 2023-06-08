<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MS0301 extends Model
{
	public $timestamps = false;
	protected $connection = 'COSSDB';
	protected $table = 'cossdb.dbo.MS0301';
}
