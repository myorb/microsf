<?php
//use App\Kernel;
//use Doctrine\Common\Annotations\AnnotationRegistry;
//use Symfony\Component\HttpFoundation\Request;
//$loader = require __DIR__.'/../vendor/autoload.php';
//
//AnnotationRegistry::registerLoader(array($loader, 'loadClass'));
//
//$kernel = new Kernel('dev', true);
//$request = Request::createFromGlobals();
//$response = $kernel->handle($request);
//$response->send();
//$kernel->terminate($request, $response);


require_once __DIR__.'/../vendor/autoload.php';

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

$request = Request::createFromGlobals();

$response = new Response('Goodbye!');
$response->send();