<?php
declare(strict_types=1);

require __DIR__.'/../vendor/autoload.php';

use App\Kernel;

print_r("Annotations\n\n");


$kernel = new Kernel();
$kernel->boot();
$container = $kernel->getContainer();


print("<pre>".print_r($container->getServices(), true)."</pre><br><br>");

print("<pre>" . print_r($container->getService('App\\Controller\\IndexController')->index(), true) . "</pre>");
print("<pre>" . print_r($container->getService('App\\Controller\\PostController')->index(), true) . "</pre>");
