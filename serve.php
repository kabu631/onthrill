<?php

/*
|--------------------------------------------------------------------------
| Fast local development server
|--------------------------------------------------------------------------
| Same as `php artisan serve`, but with PHP's OPcache switched on for this
| server only (your global php.ini is not touched). Without OPcache, PHP
| recompiles the whole framework on every page view (~0.5 s per request).
|
|   php serve.php                 serves http://127.0.0.1:8001
|                                 (8001, so it can run beside Online Poojan on 8000)
|   php serve.php 127.0.0.1 8080  for another host/port
|   composer serve                is the same as the first line
|
| On a real server, enable OPcache in php.ini instead (see README).
*/

$host = $argv[1] ?? '127.0.0.1';
$port = $argv[2] ?? '8001';

$options = [
    'opcache.enable=1',
    'opcache.enable_cli=1',              // the built-in server runs under the CLI
    'opcache.memory_consumption=192',
    'opcache.interned_strings_buffer=16',
    'opcache.max_accelerated_files=20000',
    'opcache.validate_timestamps=1',     // pick up code changes immediately while developing
    'opcache.revalidate_freq=0',
    'realpath_cache_size=4096K',
    'realpath_cache_ttl=600',
];

$command = [PHP_BINARY];
if (! extension_loaded('Zend OPcache')) {
    array_push($command, '-d', 'zend_extension=opcache');
}
foreach ($options as $option) {
    array_push($command, '-d', $option);
}
array_push($command, '-S', "{$host}:{$port}", __DIR__.'/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php');

// The server writes a log line for every request. Sending it to a file (instead of the
// console) means a busy or paused terminal can never make requests wait.
$log = __DIR__."/storage/logs/dev-server-{$port}.log";
@unlink($log);

echo "OnThrill running at http://{$host}:{$port}  (OPcache on; press Ctrl+C to stop)\n";
echo "Request log: storage/logs/dev-server-{$port}.log\n";

// The router script serves files from the current directory, so run it from public/.
$process = proc_open($command, [STDIN, ['file', $log, 'a'], ['file', $log, 'a']], $pipes, __DIR__.'/public');
exit(is_resource($process) ? proc_close($process) : 1);
