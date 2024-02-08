<?php
defined('VG_ACCESS') or die('Access denied');

const TEMPLATE = 'templates/default/';

const COOKIE_VERSION = '1.0.0';
const CRYOT_KEY = '7890';
const COOKIE_TIME = 60;
const BLOCK_TIME = 3;

const LIMIT = 20;
const QTY = 8;
const QTY_LINKS = 3;

use core\base\exceptions\RouteException;

function autoloadMainClasses($class_name)
{
    $class_path = str_replace('\\', DIRECTORY_SEPARATOR, $class_name);
    $file_path = $_SERVER['DOCUMENT_ROOT'] . DIRECTORY_SEPARATOR . $class_path . ".php";

    if (file_exists($file_path)) {
        include_once $file_path;
    } else {
        throw new RouteException('Не верное имя файла для подключения -' . $class_name);
    }
}
spl_autoload_register('autoloadMainClasses');