<?php

namespace api\model;

class ApiModel
{
    /**
     * Отправляет POST-запрос на указанный URL с JSON-данными.
     *
     * @param string $url URL-адрес запроса
     * @param mixed $jsonData Данные для отправки в формате JSON
     *
     * @return string Результат запроса
     */

    public static function sendRequest($url, $jsonData)
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $jsonData);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
        $result = curl_exec($curl);
        if ($result === false) {
            echo 'Error: ' . curl_error($curl);
        } else {
            header('Content-Type: application/json');
            echo json_encode($result);
        }
        curl_close($curl);
        return $result;
    }

    /**
     * Выполняет действие с указанными параметрами.
     *
     * @param string $action Действие
     * @param string $ctg Категория
     * @param int $quantity Количество
     * @param string $login Логин
     */

    public static function actionModel($action, $ctg, $quantity, $login)
    {
        $data = array(
            'api' => 'api',
            'action' => $action,
            'ctg' => $ctg,
            'quantity' => $quantity,
            'login' => $login
        );
        // Кодирование массива в JSON
        $jsonData = json_encode($data);
        // Установка параметров для запроса cURL
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php';
        // Вызов метода sendRequest
        self::sendRequest($url, $jsonData);
    }

    /**
     * Выполняет процесс логина с указанным логином.
     *
     * @param string $login Логин
     */


    public static function loginModel($login)
    {
        // Ваш код для метода loginModel
        $data = array(
            'api' => 'api',
            'requestLogin' => $login,
        );
        // Кодирование массива в JSON
        $jsonData = json_encode($data);
        // Установка параметров для запроса cURL
        $url = 'http://' . $_SERVER['HTTP_HOST'] . '/index.php';
        // Вызов метода sendRequest
        self::sendRequest($url, $jsonData);
    }
}
