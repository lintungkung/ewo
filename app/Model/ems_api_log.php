<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;

class ems_api_log extends Model
{
	public $timestamps = false;
	protected $primaryKey = 'Id';
	protected $connection = 'WMDB';
	protected $table = 'dbo.ems_api_log';
}
