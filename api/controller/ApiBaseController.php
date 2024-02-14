<?php

namespace api\controller;

use api\model\ApiModel;
use api\messages\RequestError;
use core\base\settings\Settings;
use api\messages;

/**
 * Class ApiBaseController
 * @package api\controller
 */
class ApiBaseController
{
    private $listControllers;

    public function __construct()
    {
        $this->listControllers = include('settings/listControllers.php');
        $this->handleRequest();
    }

    /**
     * Обработка запроса.
     */

    private function handleRequest()
    {
        $url = $_SERVER['REQUEST_URI'];
        // Удаление "/api/" из URL
        $url = str_replace('/api/', '', $url);

        if ($url == '') {
            $this->templates();
            return;
        }

        $parameters = explode("/", $url);

        if ($parameters[0] == 'login') {
            $login = $parameters[1];
            $this->loginController($login);
            return;
        }

        if (in_array($parameters[0], $this->listControllers)) {
            $action = $parameters[0];

            if (isset($parameters[1]) && is_numeric($parameters[1])) {
                $quantity = $parameters[1];
                $ctg = '';
                $login = end($parameters);
            } elseif (isset($parameters[1]) && !empty($parameters[1])) { //и если второй параметр $parameters[1] не является пустым
                //  но не содержит числовое значение (is_numeric($parameters[2]) возвращает false),
                if (is_numeric($parameters[2])) {
                    $quantity = $parameters[2];
                } elseif (!empty($parameters[2])) {
                    RequestError::displayErrorMessage(0);

                    return;
                } else {
                    $quantity = '';
                    $login = end($parameters);
                }
                $ctg = $parameters[1];
                $ctg = urldecode($ctg);

                $ctg = $this->compareCategory($ctg);

                $login = end($parameters);
            } else {
                $quantity = '';
                $ctg = '';
                $login = end($parameters);
            }

            if (count($parameters) > 4) {
                RequestError::displayErrorMessage(1);
            } else {
                $this->actionController($action, $ctg, $quantity, $login);
            }
        } else {
            RequestError::displayErrorMessage(2);
        }
    }

    /**
     * Сравнивает категорию с массивом маршрутов и возвращает соответствующее значение.
     *
     * @param string $ctg Категория для сравнения
     * @return string Найденное значение из массива маршрутов
     */

    private function compareCategory($ctg)
    {
        $found = false;
        $resultArray = Settings::get('routes');
        foreach ($resultArray as $key => $value) {
            if ($key == $value) {
                // Вычисляем расстояние Левенштейна между $value и $ctg
                $distance = $this->levenshtein_utf8($value, $ctg);

                // Если расстояние Левенштейна меньше или равно количеству допустимых опечаток
                if ($distance <= maxTypos) {
                    return $value;
                }
            } elseif ($key == $ctg) {
                return $value;
            } else {
                $found = false;
            }
        }

        if (!$found) {
            RequestError::displayErrorMessage(3);
        }
    }

    /**
     * Вычисляет расстояние Левенштейна между двумя строками UTF-8.
     *
     * @param string $s1 Первая строка для сравнения
     * @param string $s2 Вторая строка для сравнения
     * @return int Расстояние Левенштейна между двумя строками
     */

    private function levenshtein_utf8($s1, $s2)
    {
        $s1 = preg_split('//u', $s1, -1, PREG_SPLIT_NO_EMPTY);
        $s2 = preg_split('//u', $s2, -1, PREG_SPLIT_NO_EMPTY);
        $m = count($s1);
        $n = count($s2);
        $matrix = array();

        for ($i = 0; $i <= $m; $i++) {
            $matrix[$i][0] = $i;
        }

        for ($j = 0; $j <= $n; $j++) {
            $matrix[0][$j] = $j;
        }

        for ($i = 1; $i <= $m; $i++) {
            for ($j = 1; $j <= $n; $j++) {
                $cost = ($s1[$i - 1] === $s2[$j - 1]) ? 0 : 1;
                $matrix[$i][$j] = min(
                    $matrix[$i - 1][$j] + 1,
                    $matrix[$i][$j - 1] + 1,
                    $matrix[$i - 1][$j - 1] + $cost
                );
            }
        }

        return $matrix[$m][$n];
    }

    /**
     * Обрабатывает контроллер логина.
     *
     * @param string $login Логин для обработки
     */

    private function loginController($login)
    {
        ApiModel::loginModel($login);
    }

    /**
     * Обрабатывает контроллер действия.
     *
     * @param string $action Действие для обработки
     * @param string $ctg Категория для обработки
     * @param int|string $quantity Количество для обработки
     * @param string $login Логин для обработки
     */

    private function actionController($action, $ctg, $quantity, $login)
    {
        if ($quantity == '') {
            $quantity = constant('DEFAULT' . strtoupper($action));
        }

        ApiModel::actionModel($action, $ctg, $quantity, $login);
    }

    public static function defaultController()
    {
        exit();
    }

    /**
     * Выводит шаблон страницы.
     */

    private function templates()
    {
        require_once 'view/index.php';
    }

    /**
     * Кодирует данные в формат JSON и выводит их.
     *
     * @param mixed $data Данные для кодирования и вывода
     */

    public static function encodeAndEcho($data)
    {
        if (is_array($data)) {
            if (isset($data[0]) && is_array($data[0])) {
                $newData = array();
                foreach ($data as $item) {
                    $newData[] = $item['joke'];
                }
                $data = implode(PHP_EOL . PHP_EOL, $newData);
            } else {
                $data = implode(PHP_EOL . PHP_EOL, $data);
            }
        }

        $jsonString = json_encode($data, JSON_UNESCAPED_UNICODE);
        echo $jsonString;
    }
}
