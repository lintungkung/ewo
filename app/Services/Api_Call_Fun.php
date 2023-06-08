<?php
namespace App\Services;

use Cache;
use \Log;

class Api_Call_Fun
{
    const  STANDARD_MESSAGE_FUNCTION = array("status"=>4, "message"=>"Please call valid function via POST method", "data"=>null);
    const  STANDARD_MESSAGE = array("status"=>9, "message"=>"Please assign parameters if exist via POST method", "data"=>null);
    
    static function Call_Class_Func($p_class_name, $p_params, $p_cache = 0)
    {
        //if(isset($p_params['method']) && $p_params['method']!='POST')
        //    return self::STANDARD_MESSAGE;
        Log::info("[Api_Call_Fun ".$p_class_name."]: ".json_encode($p_params));
        $p_class_name = __NAMESPACE__."\\".$p_class_name;
        if(!class_exists($p_class_name))  
            return self::STANDARD_MESSAGE_FUNCTION;
        $p_cache_key = md5($p_class_name.json_encode($p_params));
        if($p_cache)
        {
            if (Cache::store('redis')->has($p_cache_key))
            {
                $p_cache_value = Cache::store('redis')->get($p_cache_key);
                Cache::store('redis')->put($p_cache_key, $p_cache_value, 60);
                //$p_cache_value['source'] = 'Redis';
                return $p_cache_value;
            }
        }
        $p_cache_value = $p_class_name::getResult($p_params);
        Cache::store('redis')->put($p_cache_key, $p_cache_value, 60);
        //$p_cache_value['source'] = 'Original';
        Log::info("[Api_Call_Fun-reply ".$p_class_name."]: ".json_encode($p_cache_value));
        return $p_cache_value;
    }
    
    public static function index()
    {
        return 'Api_Call_Fun API V1.0';
    }
    
}
