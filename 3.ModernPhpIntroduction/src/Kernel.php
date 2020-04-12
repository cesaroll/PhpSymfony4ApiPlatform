<?php

namespace App;

use App\Annotations\Route;
use App\Format\JSON;
use App\Format\XML;
use App\Format\YAML;
use App\Format\FormatInterface;
use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\AnnotationRegistry;

class Kernel {

  private $container;

  public function __construct()
  {
    $this->container = new Container();
  }

  public function getContainer(): Container
  {
    return $this->container;
  }

  public function boot()
  {
    $this->bootContainer($this->container);
  }

  private function bootContainer(Container $container)
  {
    $container->addService('format.json', function() use($container){
        return new JSON();
    });

    $container->addService('format.xml', function() use($container){
        return new XML();
    });

    $container->addService('format', function() use ($container){
        return $container->getService('format.json');
    }, FormatInterface::class);

    $container->loadServices('App\\Service');

    AnnotationRegistry::registerLoader('class_exists');
    $reader = new AnnotationReader();

    $routes = [];

    $container->loadServices(
      'App\\Controller',
      function (string $serviceName, \ReflectionClass $class) use ($reader, $routes) {
        $route = $reader->getClassAnnotation($class, Route::class);

        //print("<pre>Annotation: ".print_r($route, true)."</pre><br>");

        if (!$route) {
          return;
        }

        $baseRoute = $route->route;

        foreach($class->getMethods() as $method) {
          $route = $reader->getMethodAnnotation($method, Route::class);

          if (!$route) {
            continue;
          }

          //print("<pre>Method Annotation: ".print_r($route, true)."</pre><br>");

          $routes[str_replace('//', '/', $baseRoute . $route->route)] = [
            'service' => $serviceName,
            'method' => $method->getName()
          ];

        }

        print("<pre>Routes: ".print_r($routes, true)."</pre><br>");

      }
    );

  }

}
