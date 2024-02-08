<?php

ini_set('display_errors', 1);
ini_set('error_reporting', E_ALL);


define('VG_ACCESS', true);

header('Content-Type:text/html;charset=utf-8');
session_start();

require_once 'config.php';
require_once 'core/base/settings/internal_settings.php';


use core\base\controller\JScontroller;
use core\base\controller\RouteController;
use core\base\exceptions\RouteException;

// test

$json = file_get_contents('php://input');
$crud = json_decode($json, true);
if ($crud !== null) {
    JScontroller::instance()->run($crud);
} else {
    try {
        RouteController::instance()->route();
    } catch (RouteException | DbExceptionException $e) {
        exit($e->getMessage());
    }
}

