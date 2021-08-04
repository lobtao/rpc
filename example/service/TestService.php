<?php


namespace lobtao\example\service;


class TestService
{
    function func($params) {
        $params['new_data'] = "I'm func data";
        return $params;
    }

    function func1($params) {
        $params['new_data'] = "I'm func1 data";
        return $params;
    }

    function func2($params) {
        $params['new_data'] = "I'm func2 data";
        return $params;
    }

}
