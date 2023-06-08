<?php

namespace App\Services;

use DB;
use Exception;
use PDOException;
use Illuminate\Database\Eloquent\Collection;

class COSSClass_Mcrypt {
    private const START_KEY = 569;
    private const MULT_KEY = 586;
    private const ADD_KEY = 285;

    /* 震江密碼編碼 */
    public static function CossEncodePWD($pwd)
    {
        if (empty($pwd)) return '';
        $ls_k = date('s');
        $ls_k = $ls_k%10;
        $tmp = strtoupper($pwd);
        $xlen = strlen($tmp);
        $result = str_pad(' ', $xlen);
        $j = 0;
        for($i=1; $i<$xlen; $i+=2) {
            $result[$j++] = chr(ord($tmp[$i])-$ls_k+$i+1);
        }
        for($i=0; $i<$xlen; $i+=2) {
            $result[$j++] = chr(ord($tmp[$i])+$ls_k-$i-1);
        }
        return $ls_k.$result;
    }

    /* 震江密碼解碼 */
    public static function CossDecodePWD($pwd)
    {
        if (empty($pwd)) return '';
        $tmp = $pwd;
        $ls_k = $tmp[0];
        $tmp = substr($tmp, 1, strlen($pwd)-1);
        $xlen = strlen($tmp);
        $dxlen = (int)(strlen($tmp)/2);
        $result = str_pad(' ', $xlen);
        for($i=1; $i<=$dxlen; $i++) {
          $result[$i*2-1] = chr(ord($tmp[$i-1])+$ls_k-$i*2);
        }
        $j = 1;
        for($i=$dxlen+1; $i<=$xlen; $i++) {
          $result[$j-1] = chr(ord($tmp[$i-1])-$ls_k+$j);
          $j = $j+2;
        }
        return $result;
    }

}
