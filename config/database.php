<?php

use Illuminate\Support\Str;

return [

    /*
    |--------------------------------------------------------------------------
    | Default Database Connection Name
    |--------------------------------------------------------------------------
    |
    | Here you may specify which of the database connections below you wish
    | to use as your default connection for all database work. Of course
    | you may use many connections at once using the Database library.
    |
    */

    'default' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Database Connections
    |--------------------------------------------------------------------------
    |
    | Here are each of the database connections setup for your application.
    | Of course, examples of configuring each database platform that is
    | supported by Laravel is shown below to make development simple.
    |
    |
    | All database work in Laravel is done through the PHP PDO facilities
    | so make sure you have the driver for your particular database of
    | choice installed on your machine before you begin development.
    |
    */

    'connections' => [

        'sqlite' => [
            'driver' => 'sqlite',
            'url' => env('DATABASE_URL'),
            'database' => env('DB_DATABASE', database_path('database.sqlite')),
            'prefix' => '',
            'foreign_key_constraints' => env('DB_FOREIGN_KEYS', true),
        ],

        'mysql' => [
            'driver' => 'mysql',
            'url' => env('DATABASE_URL'),
            'host' => env('DB_HOST', '127.0.0.1'),
            'port' => env('DB_PORT', '3306'),
            'database' => env('DB_DATABASE', 'forge'),
            'username' => env('DB_USERNAME', 'forge'),
            'password' => env('DB_PASSWORD', ''),
            'unix_socket' => env('DB_SOCKET', ''),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
            'prefix_indexes' => true,
            'strict' => true,
            'engine' => null,
            'options' => extension_loaded('pdo_mysql') ? array_filter([
                PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            ]) : [],
        ],

         'COSSDB' => [
             'driver' => env('DB_CONNECTION_coss', 'sqlsrv'),
             'host' => env('DB_HOST_coss', 'COSSDBServer'),
             'port' => env('DB_PORT_coss', '1433'),
             'database' => env('DB_DATABASE_coss', 'COSSDB'),
             'username' => env('DB_USERNAME_coss', ''),
             'password' => env('DB_PASSWORD_coss', ''),
             'charset' => 'utf8',
             'prefix' => '',
             'prefix_indexes' => true,
         ],

        'CNSDB' => [
            'driver' => env('DB_CONNECTION_CNSDB', 'sqlsrv'),
            'host' => env('DB_HOST_CNSDB', 'COSSDBServer'),
            'port' => env('DB_PORT_CNSDB', '1433'),
            'database' => env('DB_DATABASE_CNSDB', 'CNSDB'),
            'username' => env('DB_USERNAME_CNSDB', ''),
            'password' => env('DB_PASSWORD_CNSDB', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        // 'COSSTRAIN' => [
        //     'driver' => env('DB_CONNECTION_COSSTRAIN', 'sqlsrv'),
        //     'host' => env('DB_HOST_COSSTRAIN', 'COSSDBServer'),
        //     'port' => env('DB_PORT_COSSTRAIN', '1433'),
        //     'database' => env('DB_DATABASE_COSSTRAIN', 'COSSTRAIN'),
        //     'username' => env('DB_USERNAME_COSSTRAIN', ''),
        //     'password' => env('DB_PASSWORD_COSSTRAIN', ''),
        //     'charset' => 'utf8',
        //     'prefix' => '',
        //     'prefix_indexes' => true,
        // ],

        'R1DB' => [
             'driver' => env('DB_CONNECTION_R1DB', 'sqlsrv'),
             'host' => env('DB_HOST_R1DB', '172.17.86.157'),
             'port' => env('DB_PORT_R1DB', '1433'),
             'database' => env('DB_DATABASE_R1DB', 'R1DB'),
             'username' => env('DB_USERNAME_R1DB', ''),
             'password' => env('DB_PASSWORD_R1DB', ''),
             'charset' => 'utf8',
             'prefix' => '',
             'prefix_indexes' => true,
         ],

        'WMDB' => [
             'driver' => env('DB_CONNECTION_WMDB', 'sqlsrv'),
             'host' => env('DB_HOST_WMDB', '172.17.86.157'),
             'port' => env('DB_PORT_WMDB', '1433'),
             'database' => env('DB_DATABASE_WMDB', 'WMDB_APP'),
             'username' => env('DB_USERNAME_WMDB', ''),
             'password' => env('DB_PASSWORD_WMDB', ''),
             'charset' => 'utf8',
             'prefix' => '',
             'prefix_indexes' => true,
         ],
        'WMDB_IO' => [
             'driver' => env('DB_CONNECTION_WMDB_IO', 'sqlsrv'),
             'host' => env('DB_HOST_WMDB_IO', '172.17.86.151'),
             'port' => env('DB_PORT_WMDB_IO', '1433'),
             'database' => env('DB_DATABASE_WMDB_IO', 'WMDB_APP'),
             'username' => env('DB_USERNAME_WMDB_IO', ''),
             'password' => env('DB_PASSWORD_WMDB_IO', ''),
             'charset' => 'utf8',
             'prefix' => '',
             'prefix_indexes' => true,
         ],

        'WMDB_COSSDB' => [
             'driver' => env('DB_CONNECTION_WMDB_COSSDB', 'sqlsrv'),
             'host' => env('DB_HOST_WMDB_COSSDB', '172.17.86.154'),
             'port' => env('DB_PORT_WMDB_COSSDB', '1433'),
             'database' => env('DB_DATABASE_WMDB_COSSDB', 'WMDB_APP'),
             'username' => env('DB_USERNAME_WMDB_COSSDB', ''),
             'password' => env('DB_PASSWORD_WMDB_COSSDB', ''),
             'charset' => 'utf8',
             'prefix' => '',
             'prefix_indexes' => true,
         ],

         'COSSERPDB' => [
            'driver' => env('DB_CONNECTION_ERP', 'sqlsrv'),
            'host' => env('DB_HOST_ERP', 'COSSERPDBServer'),
            'port' => env('DB_PORT_ERP', '1433'),
            'database' => env('DB_DATABASE_ERP', 'COSSERP'),
            'username' => env('DB_USERNAME_ERP', ''),
            'password' => env('DB_PASSWORD_ERP', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'COSSDBNAME' => [
            'driver' => env('DB_CONNECTION_coss', 'sqlsrv'),
            'host' => env('DB_HOST_coss', 'COSSDBServer'),
            'port' => env('DB_PORT_coss', '1433'),
            'database' => env('DB_DATABASE_coss', ''),
            'username' => env('DB_USERNAME_coss', ''),
            'password' => env('DB_PASSWORD_coss', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'COSSDBNAME_IO' => [
            'driver' => env('DB_CONNECTION_coss_IO', 'sqlsrv'),
            'host' => env('DB_HOST_coss_IO', 'COSSDBServer'),
            'port' => env('DB_PORT_coss_IO', '1433'),
            'database' => env('DB_DATABASE_coss_IO', ''),
            'username' => env('DB_USERNAME_coss_IO', ''),
            'password' => env('DB_PASSWORD_coss_IO', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

        'WMDBNAME_IO' => [
            'driver' => env('DB_CONNECTION_WM_IO', 'sqlsrv'),
            'host' => env('DB_HOST_WM_IO', 'COSSDBServer'),
            'port' => env('DB_PORT_WM_IO', '1433'),
            'database' => env('DB_DATABASE_WM_IO', ''),
            'username' => env('DB_USERNAME_WM_IO', ''),
            'password' => env('DB_PASSWORD_WM_IO', ''),
            'charset' => 'utf8',
            'prefix' => '',
            'prefix_indexes' => true,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Migration Repository Table
    |--------------------------------------------------------------------------
    |
    | This table keeps track of all the migrations that have already run for
    | your application. Using this information, we can determine which of
    | the migrations on disk haven't actually been run in the database.
    |
    */

    'migrations' => 'migrations',

    /*
    |--------------------------------------------------------------------------
    | Redis Databases
    |--------------------------------------------------------------------------
    |
    | Redis is an open source, fast, and advanced key-value store that also
    | provides a richer body of commands than a typical key-value system
    | such as APC or Memcached. Laravel makes it easy to dig right in.
    |
    */

    'redis' => [

        'client' => env('REDIS_CLIENT', 'phpredis'),

        'options' => [
            'cluster' => env('REDIS_CLUSTER', 'redis'),
            'prefix' => env('REDIS_PREFIX', Str::slug(env('APP_NAME', 'laravel'), '_').'_database_'),
        ],

        'default' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_DB', '0'),
        ],

        'cache' => [
            'url' => env('REDIS_URL'),
            'host' => env('REDIS_HOST', '127.0.0.1'),
            'password' => env('REDIS_PASSWORD', null),
            'port' => env('REDIS_PORT', '6379'),
            'database' => env('REDIS_CACHE_DB', '1'),
        ],

    ],

];
