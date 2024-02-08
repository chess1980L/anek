<?php

namespace api\messages;

use api\controller\ApiBaseController;
class RequestError
{
    public static function displayErrorMessage($errorCode)
    {
        $messages = include 'exception_messages.php';

        if (is_numeric($errorCode)) {
            if (isset($messages[$errorCode])) {
                $message = $messages[$errorCode];
               ApiBaseController::encodeAndEcho( $message);
            }
        } else {
            ApiBaseController::encodeAndEcho($errorCode);
        }
    }
}