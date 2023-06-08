<?php

namespace App\Http\Controllers;

use Validator;
use Exception;
use Illuminate\Support\Facades\Log;

class MyException extends Exception
{
    // 重定義構造器使 message 變為必須被指定的屬性
    public function __construct($message, $code = 0) {
        // 自定義的代碼

        // 確保所有變量都被正確賦值
        parent::__construct($message, $code);
    }

    // 自定義字符串輸出的樣式
    public function __toString() {
        return __CLASS__ . ": [{$this->code}]: {$this->message}\n";
    }

    public function customFunction() {
        echo "A Custom function for this type of exception\n";
    }
}
