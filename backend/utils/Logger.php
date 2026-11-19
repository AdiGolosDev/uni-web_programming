<?php

class Logger
{
    public static function info($message)  { self::write('info.log', 'INFO', $message); }
    public static function error($message) { self::write('error.log', 'ERROR', $message); }

    private static function write($file, $level, $message)
    {
        $dir = __DIR__ . '/../logs';
        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }
        $message = str_replace(["\r", "\n"], ' ', (string) $message);
        // a broken log must never break a request, hence the @
        @file_put_contents("$dir/$file", '[' . date('Y-m-d H:i:s') . "] $level $message\n", FILE_APPEND | LOCK_EX);
    }
}