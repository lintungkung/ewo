<?php

namespace App\Console;

use Illuminate\Console\Command;
use Carbon\Carbon;

class BaseCommand extends Command
{
    public function line($string, $style = null, $verbosity = null)
    {
        $string = sprintf('[%s] ', Carbon::now()->toDateTimeString()) . $string;

        parent::line($string, $style, $verbosity);
    }
}

