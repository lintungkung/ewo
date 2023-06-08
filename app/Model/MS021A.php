<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MS021A extends Model
{
	public $timestamps = false;
	protected $primaryKey = 'Id';
	protected $connection = 'COSSDB';
	protected $table = 'COSSDB.dbo.MS021A';
}
