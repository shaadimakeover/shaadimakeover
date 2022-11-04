<?php

namespace App\Helpers;

use Hashids\Hashids;

class HasherServiceHelper
{
    public static function encode(...$args)
    {
        return app(Hashids::class)->encode(...$args);
    }
    public static function decode($enc)
    {
        if (is_int($enc)) {
            return $enc;
        }
        $arr=app(Hashids::class)->decode($enc);
        if(count($arr)>0)
            return app(Hashids::class)->decode($enc)[0];
        else 
            abort(404);
    }
}
