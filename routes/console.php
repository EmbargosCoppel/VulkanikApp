<?php

use Illuminate\Support\Facades\Schedule;
use Illuminate\Support\Facades\Storage;

/*
|--------------------------------------------------------------------------
| Console Routes
|--------------------------------------------------------------------------
|
| This file is where you may define all of your Console Commands.
|
*/

// Backup automático diario de la base de datos
Schedule::call(function () {
    $disk = config('taller.backup.disk', 'local');
    $path = config('taller.backup.path', 'backups');
    $filename = 'backup-' . now()->format('Y-m-d-H-i-s') . '.sql';

    $dbConnection = config('database.default');
    $dbDatabase = config("database.connections.{$dbConnection}.database");

    if ($dbConnection === 'sqlite') {
        $command = "cp " . database_path('database.sqlite') . " " . storage_path("app/{$path}/{$filename}");
        exec($command);
    } elseif ($dbConnection === 'mysql') {
        $host = config("database.connections.{$dbConnection}.host");
        $port = config("database.connections.{$dbConnection}.port", 3306);
        $username = config("database.connections.{$dbConnection}.username");
        $password = config("database.connections.{$dbConnection}.password");

        $command = "mysqldump -h {$host} -P {$port} -u {$username} -p{$password} {$dbDatabase} > " . storage_path("app/{$path}/{$filename}");
        exec($command);
    }

    // Limpiar backups antiguos (mantener últimos 7 días)
    $files = Storage::disk($disk)->files($path);
    foreach ($files as $file) {
        if (Storage::disk($disk)->lastModified($file) < now()->subDays(7)->timestamp) {
            Storage::disk($disk)->delete($file);
        }
    }
})->dailyAt('2:00');
