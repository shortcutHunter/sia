<?php

// Error reporting for production
// error_reporting(0);
// ini_set('display_errors', '0');
// ini_set('display_startup_errors', '0');

// Error reporting for development
error_reporting(-1);
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');

// Timezone
date_default_timezone_set('Asia/Jakarta');

// Settings
$settings = [];

// Path settings
$settings['root'] = dirname(__DIR__);
$settings['temp'] = $settings['root'] . '/tmp';
$settings['public'] = $settings['root'] . '/public';
$settings['template_path'] = $settings['root'] . '/templates';
$settings['uploaded'] = $settings['public'] . '/uploaded/';

$settings['row_per_page'] = 10;

// Error Handling Middleware settings
$settings['error'] = [

    // Should be set to false in production
    'display_error_details' => true,

    // Parameter is passed to the default ErrorHandler
    // View in rendered output by enabling the "displayErrorDetails" setting.
    // For the console and unit tests we also disable it
    'log_errors' => true,

    // Display error details in error log
    'log_error_details' => true,
];


$settings['twig'] = [

    // Template paths
    'paths' => [
        $settings['root'] . '/templates'
    ],

    // Twig environment options
    'options' => [
        // Should be set to true in production
        'cache_enabled' => false,
        'cache_path' => $settings['temp'] . '/twig',
    ],

];


$settings['db'] = [
    'host'      => '127.0.0.1',
    // 'database'  => 'sia_new',
    'database'  => 'sia',

    // MYSQL
    'driver'    => 'mysql',
    'username'  => 'root',
    'password'  => '',
    'port'      => '3306',

    // PSQL
    // 'driver'    => 'pgsql',
    // 'username'  => 'postgres',
    // 'password'  => '123',
    // 'port'      => '5432',

    // 'host'    => 'ec2-3-215-76-208.compute-1.amazonaws.com',
    // 'driver'    => 'pgsql',
    // 'database'  => 'd1fd3ibledec77',
    // 'username'  => 'vteujytclkqvzq',
    // 'password'  => '341e10b675b902d0cb5a88f4401dc5c130f38fd1c74d7a20bb7bbd9fa001d87c',
    // 'port'      => '5432',
    
    'charset'   => 'utf8',
    'collation' => 'utf8_unicode_ci',
    'prefix'    => '',
];

return $settings;
