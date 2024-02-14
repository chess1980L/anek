<?php
/**
 * Created by PhpStorm.
 * User: БигБосс
 * Date: 09.07.2022
 * Time: 16:39
 */

namespace core\base\controller;

use core\base\exceptions\RouteException;
use core\base\settings\Settings;

class RouteController extends BaseController
{

    use Singleton;

    protected $routes;

    private function __construct()

    {
        $adress_str = $_SERVER['REQUEST_URI'];
        $path = substr($_SERVER['PHP_SELF'], 0, strpos($_SERVER['PHP_SELF'], 'index.php'));
        if ($path === PATH) {
            $this->routes = Settings::get('routes'); // получение маршрутов из Settings
            if (!$this->routes) {
                throw new RouteException('Отсутствуют маршруты в базовых настройках', 1);
            } // проверка пришли ли маршруты
            $url = preg_split('/(\/)|(\?.*)/', $adress_str, 0, PREG_SPLIT_NO_EMPTY);
            $url = explode('/', substr($adress_str, strlen(PATH))); // масив url
            $url[0] = urldecode($url[0]);
            if (is_numeric($url[0])) {
                $count = Settings::countPage();
            } elseif (isset($url[0]) && is_string($url[0]) && !empty($url[0])) {
                // Ваш код для обработки $url[0], если оно является строкой
                $count = Settings::countPage($url[0]);
            } else {
                $count = Settings::countPage();
            }
            if (isset($url[1]) && ($url[1] === "")) {
                unset($url[1]);
            }
            if (($path === $adress_str)
                || ((in_array($url[0], $this->routes['user']['routes']) && !isset($url[1]))
                    || (is_numeric($url[0]) && $url[0] > 0 && $url[0] < $count)
                    || (in_array($url[0],
                            $this->routes['user']['routes']) && is_numeric($url[1]) && $url[1] > 0 && $url[1] < $count))) {

                $hrUrl = $this->routes['user']['hrUrl'];
                $this->controller = $this->routes['user']['path'];
                $route = 'user';
                $this->parameters[] = $url;
                $this->parameters['countPage'] = $count;
                if (isset($url[0]) && !is_numeric($url[0]) && $url[0] != "") {
                    $this->parameters['tag'] = $url[0];

                    if (isset($url[0]) && isset($url[1]) && is_numeric($url[1])) {
                        $this->parameters['page'] = $url[1];
                    }
                }
                if (isset($url[0]) && is_numeric($url[0])) {
                    $this->parameters['page'] = $url[0];
                }
                $this->createRoute($route, $url);

            } elseif (preg_match('/\/anekdot-id-(\d+)\//', $adress_str,
                    $matches) && ($joke = Settings::getID($matches[1])) !== true) {
                // адрес соответствует условию
                $this->parameters['joke'] = $joke;
                $this->controller = 'core\user\controller\anekdotController';
                $this->inputMethod = !empty($route[1]) ? $route[1] : $this->routes['default']['inputMethod'];
            } elseif ($adress_str == '/telega/') {
                $this->controller = 'core\user\controller\telegaController';
                $this->inputMethod = !empty($route[1]) ? $route[1] : $this->routes['default']['inputMethod'];
            } else {
                $this->redirect($path . TEMPLATE . "404.php", 301);
                exit();
            }
        } else {
            throw new RouteException('Не корректная директория сайта', 1);
        }
    }


    private function createRoute()
    {
        $this->controller .= $this->routes['default']['controller'];
        $this->inputMethod = !empty($route[1]) ? $route[1] : $this->routes['default']['inputMethod'];
    }
}