<?php

namespace core\base\controller;


use core\base\exceptions\RouteException;
use core\base\settings\Settings;

abstract class BaseController
{

    use \core\base\controller\BaseMethods;

    protected $page;
    protected $errors;
    protected $controller;
    protected $inputMethod;
    protected $parameters;
    protected $countPage;
    protected $joke;

    public function route()
    {

        $controller = str_replace('/', '\\', $this->controller);

        try {

            $object = new \ReflectionMethod($controller, 'request');

            $args = [
                'joke' => $this->joke,
                'parameters' => $this->parameters,
                'countPage' => $this->countPage,
                'inputMethod' => $this->inputMethod,
            ];

            $object->invoke(new $controller, $args);
        } catch (\ReflectionException $e) {

            throw new RouteException($e->getMessage());
        }
    }

    public function request($args)
    {
        $this->parameters = $args['parameters'];
        $inputData = $args['inputMethod'];

        $data = $this->$inputData();
         if ($data) {
            $this->page = $data;
        };
        if ($this->errors) {
            $this->writeLog($this->errors);
        }
        $this->getPage();
    }

    protected function getPage()
    {
        if (is_array($this->page)) {
            foreach ($this->page as $block) echo $block;
        } else {
            echo $this->page;
        }
        exit();
    }

    protected function render($path = '', $parameters = [])
    {

        extract($parameters);

        if (!$path) {

            $class = new \ReflectionClass($this);

            $space = str_replace('\\', '/', $class->getNamespaceName() . '\\');
            $routes = Settings::get('routes');

            if ($space === $routes['user']['path']) $template = TEMPLATE;

            $path = $template . explode('controller', strtolower($class->getShortName()))[0];

        }

        ob_start();

        if (!@include $path . '.php') throw new RouteException('Отсутствует шаблон - ' . $path);

        return ob_get_clean();
    }
}