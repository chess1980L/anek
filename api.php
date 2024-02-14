<?php


$json = file_get_contents('php://input');
$crud = json_decode($json, true);