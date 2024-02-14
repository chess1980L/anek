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

    private $routes = [];

    public function __construct()
    {
        $crudRoutes = BaseModel::processCrud(
            [
                'action' => 'r',
                'tags' => '*'
            ]
        );

        $lowercaseCrudRoutes = array_map(
            function ($route) {
                return mb_strtolower($route, 'UTF-8');
            },
            $crudRoutes
        );

        $shortKeyRoutes = [];
        $longKeyRoutes = [];
        $categoryRoutes = [];

        foreach ($lowercaseCrudRoutes as $value) {
            $categoryRoutes[$value] = $value;

            $words = explode(" ", $value);
            if (count($words) >= 2) {
                $key1 = '';
                $key2 = '';
                foreach ($words as $word) {
                    $key1 .= mb_substr($word, 0, 1, 'UTF-8');
                    if (mb_strlen($word, 'UTF-8') > 1) {
                        $key2 .= mb_substr($word, 0, 1, 'UTF-8');
                    }
                }
                if (!empty($key1)) {
                    $shortKeyRoutes[$key1] = $value;
                }
                if (!empty($key2)) {
                    $longKeyRoutes[$key2] = $value;
                }
            }
        }

        $resultRoutes = $shortKeyRoutes + $longKeyRoutes + $categoryRoutes;
        $this->routes = $resultRoutes;
    }

    static public function get($property)
    {
        return self::instance()->$property;
    }
}
