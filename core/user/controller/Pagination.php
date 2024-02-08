<?php


namespace core\user\controller;


class Pagination extends IndexController
{
    public static function pagination($currentElement, $countPage)
    {
        $elements = [];

        if ($countPage < 2) {
            return ''; // Если $countPage равен 1, возвращаем пустую строку
        }

        $countPage = $countPage - 1;

        $elements['Previous'] = $currentElement - 1;
        $elements['first'] = 1;
        $elements['PreviousCurrentElement'] = $currentElement - 1;
        $elements['currentElement'] = $currentElement;
        $elements['NextCurrentElement'] = $currentElement + 1;
        $elements['Last'] = $countPage;
        $elements['next'] = $currentElement + 1;

        if ($currentElement === 0) {
            unset($elements["Previous"]);
            unset($elements["PreviousCurrentElement"]);
            unset($elements["currentElement"]);
            unset($elements["NextCurrentElement"]);
        }
        if ($currentElement === $countPage) {
            unset($elements["next"]);
            unset($elements["NextCurrentElement"]);
            unset($elements["Last"]);
        }
        if ($currentElement + 1 === $countPage) {
            unset($elements["Last"]);
        }
        if ($currentElement === 1) {
            unset($elements["Previous"]);
            unset($elements["first"]);
            unset($elements["PreviousCurrentElement"]);
        }
        if ($currentElement - 1 === 1) {
            unset($elements["PreviousCurrentElement"]);
        }
        return self::generatePaginationHtml($elements);
    }


    public static function generatePaginationHtml($elements)
    {
        $html = '<nav aria-label="..."><ul class="pagination">';

        foreach ($elements as $key => $value) {
            if ($key === 'Previous') {
                $html .= '<li class="page-item"><a class="page-link" href="' . $value . '">' . $key . '</a></li>';
            } elseif ($key === 'first' || $key === 'Last') {
                $html .= '<li class="page-item"><a class="page-link" href="' . $value . '">' . $value . '</a></li>';
            } elseif ($key === 'currentElement') {
                $html .= '<li class="page-item active" aria-current="page"><a class="page-link" href="#">' . $value . '</a></li>';
            } elseif ($key === 'next') {
                $html .= '<li class="page-item"><a class="page-link" href="' . $value . '">' . $key . '</a></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . $value . '">' . ($key === 'PreviousCurrentElement' || $key === 'NextCurrentElement' ? $value : $key) . '</a></li>';
            }
        }

        $html .= '</ul></nav>';

        return $html;
    }


}