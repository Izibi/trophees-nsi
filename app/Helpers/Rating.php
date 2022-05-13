<?php
namespace App\Helpers;

class Rating {

    public static function rangeOptions($max) {
        $res = [
            '' => ''
        ];
        for($i=0; $i<=$max; $i++) {
            $res[''.$i] = $i.' / '.$max;
        }
        return $res;
    }
}
