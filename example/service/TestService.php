<?php


namespace lobtao\example\service;


class TestService
{
    function func1($params) {
        $params['new_data'] = "I'm func1 data";
        return $params;
    }

    function func2($params) {
        $params['new_data'] = "I'm func2 data";
        return $params;
    }

    function func3($params) {
        $params['new_data'] = "I'm func2 data";
        return $params;
    }
}
