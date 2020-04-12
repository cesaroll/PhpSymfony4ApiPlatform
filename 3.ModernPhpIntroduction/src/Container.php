<?php

namespace App;

class Container {
    private $services = [];
    private $aliases = [];

    public function addService(
        string $name,
        \Closure $closure,
        ?string $alias = null
    ): void {
        $this->services[$name] = $closure;

        if ($alias) {
            $this->addAlias($alias, $name);
        }
    }

    public function addAlias(string $alias, string $service): void {
        $this->aliases[$alias] = $service;
    }

    public function hasService(string $name): bool {
        return isset($this->services[$name]);
    }

    public function hasAlias(string $name): bool {
        return isset($this->aliases[$name]);
    }

    public function getService(string $name) {

        if(!$this->hasService($name)) {
            return null;
        }

        if($this->services[$name] instanceof \Closure) {
            $this->services[$name] = $this->services[$name]();
        }

        return $this->services[$name];
    }

    public function getAlias(string $name) {
        return $this->getService($this->aliases[$name]);
    }

    public function getServices(): array {
        return [
            'services' => array_keys($this->services),
            'aliases' => $this->aliases
        ];
    }

    public function loadServices(string $namespace): void {
        $baseDir = __DIR__ . '/';

        $actualDirectory = str_replace('\\', '/', $namespace);

        $actualDirectory = $baseDir . substr($actualDirectory, strpos($actualDirectory, '/')+1);

        //print("<pre>".print_r($actualDirectory, true)."</pre>");

        $files = array_filter(scandir($actualDirectory), function ($file) {
            return $file !== '.' && $file !== '..';
        });

        //print("<pre>".print_r($files, true)."</pre><br>");

        foreach ($files as $file) {
            $class = new \ReflectionClass(
                $namespace . '\\' . basename($file, '.php')
            );
            $serviceName = $class->getName();
            //print("<pre>Service: ".print_r($serviceName, true)."</pre><br>");

            $constructor = $class->getConstructor();
            $arguments = $constructor->getParameters();

            // parameters to inject into service constructor
            $serviceParameters = [];

            foreach ($arguments as $argument) {
                $type = (string)$argument->getType();
                //print("<pre>Argument: ".print_r($type, true)."</pre><br>");

                if ($this->hasService($type) || $this->hasAlias($type) ) {
                    $serviceParameters[] = $this->getService($type) ?? $this->getAlias($type);
                } else {
                    $serviceParameters[] = function() use ($type) {
                        return $this->getService($type) ?? $this->getAlias($type);
                    };
                }

            }

            $this->addService($serviceName, function () use ($serviceName, $serviceParameters) {
                foreach ($serviceParameters as $serviceParameter) {
                    if ($serviceParameter instanceof \Closure) {
                        $serviceParameter = $serviceParameter();
                    }
                }

                return new $serviceName(...$serviceParameters);
            });

        }
    }

}
