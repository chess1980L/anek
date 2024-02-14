<?php


namespace core\user\controller;

use core\base\model\BaseModel;
use core\base\controller\BaseController;

class anekdotController extends BaseController
{

    protected function inputData()
    {
        //templates/default/anekdot.php

        $tags = BaseModel::processCrud([
            'action' => 'r',
            'tags' => '**'
        ]);

        $data = $this->parameters['joke'];
        $data['tags'] = $tags['tags'];

        $title = $data['jokes'][0]['joke'];

        $title = mb_substr($title, 0, 200, 'UTF-8');
        $data['title'] = $title;
        $content = $this->render('', compact('data'));

        return compact('content',);


    }
}