<?php

namespace core\base\controller;

use core\base\model\ApiModel;
use core\base\model\BaseModel;
use api\controller\ApiBaseController;

class JScontroller
{

    use Singleton;

    public function run($crud)
    {
        if (isset($crud['validation'])) {
            $return = BaseModel::checkModelCookie($crud['validation']['username'], $crud['validation']['password']);
            echo json_encode($return);
        }
        elseif (isset($crud['api'])){

            if (isset($crud['action'])){
            $action = $crud['action'];
            $ctg = $crud['ctg'];
            $quantity= $crud['quantity'];
            $login = $crud['login'];
            $apiData = ApiModel::apiSwitchModel($action, $ctg, $quantity, $login);

            ApiBaseController::encodeAndEcho($apiData);

            }
            elseif(isset($crud['requestLogin'])){
                $login = $crud['requestLogin'];
                $apiData = ApiModel::loginModel($login);
                ApiBaseController::encodeAndEcho($apiData);


            }
        }
        else {
            if (isset($crud['currentUrl'])) {
                $currentUrl = urldecode($crud['currentUrl']);
            } else {
                $data = BaseModel::processCrud($crud);
                $response = json_encode($data);
                echo $response;
            }
        }
    }

}
