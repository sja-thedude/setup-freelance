<?php

/**
 * START QUICKFIX: rewrite urls and follow userflows
 * ---
 * All routes should use without www to prevent the application from being logged off.
 **/
if(isset($_SERVER['SERVER_NAME'])) {
    $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';

    switch($_SERVER['SERVER_NAME']) {
        case 'www.itsready.be':
            header('Location: https://itsready.be' . $requestUri);
            exit;
        break;

        case 'admin.itsready.be':
            header('Location: https://itsready.be/admin');
            exit;
        break;

        case 'manager.itsready.be':
            header('Location: https://itsready.be/manager');
            exit;
        break;
    }
}
/* END QUICKFIX: rewrite urls and follow userflows */

/* START QUICKFIX: redirect itsready.be or itsready.be/nl to b2b.itsready.be */
if(isset($_SERVER['SERVER_NAME']) && $_SERVER['SERVER_NAME'] == 'itsready.be') {
    $requestUri = isset($_SERVER['REQUEST_URI']) ? $_SERVER['REQUEST_URI'] : '/';
    if(in_array($requestUri, [
        '/',
        '/nl'
    ])) {
        header('Location: https://b2b.itsready.be/');
        // include(__DIR__ . '/index-itsready.html');
        exit;
    }
}
/* END QUICKFIX: redirect itsready.be to b2b.itsready.be */



/**
 * Laravel - A PHP Framework For Web Artisans
 *
 * @package  Laravel
 * @author   Taylor Otwell <taylorotwell@gmail.com>
 */

/*
|--------------------------------------------------------------------------
| Register The Auto Loader
|--------------------------------------------------------------------------
|
| Composer provides a convenient, automatically generated class loader for
| our application. We just need to utilize it! We'll simply require it
| into the script here so that we don't have to worry about manual
| loading any of our classes later on. It feels nice to relax.
|
*/

require __DIR__.'/../bootstrap/autoload.php';

/*
|--------------------------------------------------------------------------
| Turn On The Lights
|--------------------------------------------------------------------------
|
| We need to illuminate PHP development, so let us turn on the lights.
| This bootstraps the framework and gets it ready for use, then it
| will load up this application so that we can run it and send
| the responses back to the browser and delight our users.
|
*/

$app = require_once __DIR__.'/../bootstrap/app.php';

/*
|--------------------------------------------------------------------------
| Run The Application
|--------------------------------------------------------------------------
|
| Once we have the application, we can handle the incoming request
| through the kernel, and send the associated response back to
| the client's browser allowing them to enjoy the creative
| and wonderful application we have prepared for them.
|
*/

$kernel = $app->make(Illuminate\Contracts\Http\Kernel::class);

$response = $kernel->handle(
    $request = Illuminate\Http\Request::capture()
);

$response->send();

$kernel->terminate($request, $response);
