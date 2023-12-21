<?php

if (!function_exists('temp')) {
    function temp($value)
    {
        \App\Helpers\DBHelper::temp($value);
    }
}
