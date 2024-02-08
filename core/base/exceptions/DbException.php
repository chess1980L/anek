<?php

namespace core\base\exceptions;

use core\base\controller\BaseMethods;

class DbException extends \Exception
{
    use BaseMethods;

    protected $messages = [];

    public function __construct($message = "", $code = 0)
    {
        $this->messages = include 'messages.php';

        if (empty($message) && isset($this->messages[$code])) {
            $message = $this->messages[$code];
        }

        $error = $message . "\r\n" . 'file ' . $this->getFile() . "\r\n" . 'In line ' . $this->getLine() . "\r\n";

        parent::__construct($error, $code);

        $this->writeLog($error, 'db-log.txt');
    }
}