<?php
/**
 * Created by PhpStorm.
 * User: alex14v
 * Date: 22.02.18
 * Time: 18:59
 */

namespace App\Helpers;


class StrHelper
{
    /**
     *  Get word plural form
     *
     * @param $n
     * @param $form1
     * @param $form2
     * @param $form3
     * @return mixed
     */
    public static function pluralForm($n, $form1, $form2, $form3)
    {
        $n = abs($n) % 100;
        $n1 = $n % 10;

        if ($n > 10 && $n < 20) {
            return $form3;
        }

        if ($n1 > 1 && $n1 < 5) {
            return $form2;
        }

        if ($n1 == 1) {
            return $form1;
        }

        return $form3;
    }

}