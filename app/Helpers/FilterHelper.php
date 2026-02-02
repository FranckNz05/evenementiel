<?php

namespace App\Helpers;

class FilterHelper
{
    public static function toggleFilter($key, $value)
    {
        $query = request()->query();
        
        if (isset($query[$key]) && $query[$key] == $value) {
            unset($query[$key]);
        } else {
            $query[$key] = $value;
        }
        
        return url()->current() . '?' . http_build_query($query);
    }
} 