<?php
/**
 * Created by PhpStorm.
 * User: БигБосс
 * Date: 09.07.2022
 * Time: 17:01
 */

namespace core\base\settings;


use core\base\controller\Singleton;
use core\base\model\BaseModel;

class Settings
{

    use Singleton;

    private $routes = [
        'user' => [
            'path' => 'core/user/controller/',
            'hrUrl' => true,
            'routes' => []
        ],

        'default' => [
            'controller' => 'IndexController',
            'inputMethod' => 'inputData',
        ]
    ];
    public function __construct()
    {
        $crudRoutes = BaseModel::processCrud([
            'action' => 'r', 'tags' => '*'
        ]);

        $lowercaseCrudRoutes = array_map(function ($route) {
            return mb_strtolower($route, 'UTF-8');
        }, $crudRoutes);

        $this->routes['user']['routes'] = $lowercaseCrudRoutes;
    }

    static public function getId($id){

        $allId= BaseModel::processCrud([
            'action' => 'r',
            'jokes' =>['id'=>$id],
        ]);

        return  $allId;
    }

    static public function countPage($tag = null)
    {
        if ($tag !== null) {
            $count = BaseModel::processCrud([
                'action' => 'r',
                'jokes' => '*',
                'tag' => $tag
            ]);
        } else {
            $count = BaseModel::processCrud([
                'action' => 'r',
                'jokes' => '*'
            ]);
        }

        $countPage = ceil($count / LIMIT);
        return (int)$countPage;
    }

    static public function get($property)
    {
        return self::instance()->$property;

    }

}


