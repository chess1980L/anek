<?php


namespace core\user\controller;


use core\base\controller\BaseController;

class telegaController extends BaseController
{

    protected function inputdata()
    {

        $a = 7;

        $content = $this->render('', compact('a'));

        return compact('content',);
    }


}