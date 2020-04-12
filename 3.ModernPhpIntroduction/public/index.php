<?php
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use App\Controller\IndexController;
use App\Format\JSON;
use App\Format\XML;
use App\Format\YAML;
use App\Container;
use App\Format\FormatInterface;

use App\Service\Serializer;

print_r("Simple Service Container<br><br>");

$data = [
    "name" => "John",
    "surname" => "Doe"
];

$container = new Container();

$container->addService('format.json', function() use($container){
    return new JSON();
});

$container->addService('format.xml', function() use($container){
    return new XML();
});

$container->addService('format', function() use ($container){
    return $container->getService('format.json');
}, FormatInterface::class);

// $container->addService('serializer', function() use ($container){
//     return new Serializer($container->getService('format'));
// });

// $container->addService('controller.index', function() use ($container){
//     return new IndexController($container->getService('serializer'));
// });

$container->loadServices('App\\Service');
$container->loadServices('App\\Controller');

print("<pre>".print_r($container->getServices(), true)."</pre><br><br>");

print("<pre>" . print_r($container->getService('App\\Controller\\IndexController')->index(), true) . "</pre>");
print("<pre>" . print_r($container->getService('App\\Controller\\PostController')->index(), true) . "</pre>");
