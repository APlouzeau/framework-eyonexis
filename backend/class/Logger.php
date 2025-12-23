<?php

class Logger
{
    public static function debug(string $text, $data = null): void
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);

        if (is_array($data)) {
            error_log($text);
            foreach ($data as $key => $value) {
                error_log($key . ' => ' . print_r($value, true));
            }
        } else {
            error_log($text . print_r($data, true));
        }

        foreach ($trace as $frame) {
            if (basename($frame['file']) !== 'index.php') {
                error_log('Appel depuis : ' . $frame['file']);
                error_log('Ligne ' . $frame['line']);
                break;
            }
        }
    }

    public static function logs(string $title, array $messages, string $file): void
    {
        $logFile = APP_PATH . '/logs/' . $file . '.log';
        $logDir = dirname($logFile);

        if (!is_dir($logDir)) {
            @mkdir($logDir, 0755, true);
        }

        $timestamp = date('Y-m-d H:i:s');

        $logContent = "[{$timestamp}] {$title}\n";
        foreach ($messages as $message) {
            $logContent .= "  - {$message}\n";
        }
        $logContent .= "\n";

        @file_put_contents($logFile, $logContent, FILE_APPEND);
    }
}
