<?php
/**
 * Logs helper
 *
 * Created by PhpStorm.
 * User: opiy
 * Date: 03.02.2018
 * Time: 14:31
 */

namespace App\Helpers;

use Illuminate\Support\Facades\Log;

class l
{
    /**
     * Exception
     *
     * @param object $class
     * @param \Exception $exception
     * @param string $message
     */
    public static function exc($class, \Exception $exception, string $message = '')
    {
        Log::error(class_basename($class) . ': ' . $message . ' ' . $exception->getMessage() . "\n" . $exception->getFile() . ' ' . $exception->getLine());
    }


    /**
     * Error
     *
     * @param object $class
     * @param string $message
     */
    public static function error($class, string $message)
    {
        Log::error(class_basename($class) . ': ' . $message);
    }

    /**
     * Debug
     *
     * @param string $message
     * @param mixed $var
     * @param object|null $class
     */
    public static function debug(string $message = '', $var, $class = null)
    {
        if ($class) {
            Log::debug(class_basename($class) . ': ' . $message . print_r($var, true));
        } else {
            Log::debug($message . print_r($var, true));
        }
    }

}