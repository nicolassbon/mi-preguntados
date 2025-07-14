<?php

class Router
{
    private String $defaultController;
    private String $defaultMethod;
    private Configuration $configuration;

    public function __construct($defaultController, $defaultMethod, $configuration)
    {
        $this->defaultController = $defaultController;
        $this->defaultMethod = $defaultMethod;
        $this->configuration = $configuration;
    }

    public function go($controllerName, $methodName): void
    {
        $controller = $this->getControllerFrom($controllerName);
        $this->executeMethodFromController($controller, $methodName);
    }

    private function getControllerFrom($controllerName)
    {
        $controllerName = 'get' . ucfirst($controllerName) . 'Controller';
        $validController = method_exists($this->configuration, $controllerName) ? $controllerName : $this->defaultController;
        return call_user_func(array($this->configuration, $validController));
    }

    private function executeMethodFromController($controller, $method): void
    {
        $validMethod = method_exists($controller, $method) ? $method : $this->defaultMethod;
        $controller->$validMethod();
    }
}
