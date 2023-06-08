<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class MI0130 extends Model
{
	public $timestamps = false;
	protected $primaryKey = 'Id';
	protected $connection = 'WMDB';
	protected $table = 'COSSERP.dbo.MI0130';
}
