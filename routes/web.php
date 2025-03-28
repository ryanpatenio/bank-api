<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// In bank-api routes/web.php
Route::get('/check-connections', function() {
    return [
        'env_db_name' => env('DB_DATABASE'),
        'default_connection_db' => DB::connection()->getDatabaseName(),
        'wallets_model_connection' => (new App\Models\Wallets)->getConnection()->getDatabaseName(),
        'wallets_table_exists' => Schema::connection('mysql')->hasTable('wallets'),
        'bankapi_wallets_count' => DB::table('wallets')->count(),
        'paybuddy_wallets_count' => DB::connection('mysql')->table('wallets')->count(),
    ];
});
