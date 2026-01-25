<?php

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Http\Request;

// 1. Record the start time for performance profiling
define('LARAVEL_START', microtime(true));

// 2. Check for Maintenance Mode
// If you ran 'php artisan down', this loads a simple 'Coming Soon' page
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// 3. Register the Composer Autoloader
// This allows Laravel to find all your classes (Controllers, Models, etc.)
if (!file_exists(__DIR__.'/../vendor/autoload.php')) {
    die('The vendor folder is missing. Please run "composer install".');
}
require __DIR__.'/../vendor/autoload.php';

// 4. Bootstrap the Application
// This loads the 'service container' which is the heart of Laravel
$app = require_once __DIR__.'/../bootstrap/app.php';

// 5. Run the Application
// We make the 'Kernel', capture the user's request, and send back a response
$kernel = $app->make(Kernel::class);

$response = $kernel->handle(
    $request = Request::capture()
)->send();

// 6. Shutdown
// Perform any final tasks before the script ends
$kernel->terminate($request, $response);