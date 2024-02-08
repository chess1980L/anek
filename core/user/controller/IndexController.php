<?php

namespace core\user\controller;

use core\base\controller\BaseController;
use core\base\model\BaseModel;


class IndexController extends BaseController
{

    protected function inputData()
    {
        if ($this->parameters[0][0] != '') {

            if (!is_numeric($this->parameters[0][0])) {

                $section = $this->parameters[0];

                $data = BaseModel::processCrud([
                        'action' => 'r',
                        $section]
                );

            } else {  // это число

                $offset = $this->parameters[0][0];
                $data = BaseModel::processCrud([
                        'action' => 'r',
                        'offset' => $offset]
                );
            }

        } else {
            $data = BaseModel::processCrud([
                'action' => 'r']);

        }

        $src = $this->checkCookie();

        $data['src'] = $src;

        if (isset($_COOKIE['username'])) {
            $data['username'] = $_COOKIE['username'];
        }

        $pagination = $this->countElements();

        $data['pagination'] = $pagination;

        $content = $this->render('', compact('data'));

        return compact('content', );

    }

    public function checkCookie()
    {
        if (!isset($_COOKIE['username'])) {
            return "userJS.js";
        } else {
            if (empty($_COOKIE['username'])) {
                return "userJS.js";
            } else {
                $username = $_COOKIE['username'];
                return BaseModel::checkModelCookie($username);
            }
        }
    }

    public function countElements()
    {
        // Формируем $currentElement и $countPage
        $currentElement = isset($this->parameters['page']) ? (int)$this->parameters['page'] : 0;
        $countPage = $this->parameters['countPage'];
        // Вызываем статическую функцию pagination() и передаем ей $currentElement и $countPage в качестве параметров
        return Pagination::pagination($currentElement, $countPage);
    }
}

