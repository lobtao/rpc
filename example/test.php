<?php

use lobtao\rpc\BaseRpc;
use lobtao\rpc\RpcException;

require_once __DIR__ . '/../vendor/autoload.php';

$rpc = new BaseRpc();

$while_funcs = [
    'test_func1',
    'test_func2',
    'test_func4',
    'test1_func',
];
// 类和方法以_分割
// $func = 'test_func1';
$func = 'test_func5'; // 不能访问方法
// 数据必须以数组包裹
$args = [
    [
        'name' => "I'm test data",
    ],
];

// 该部分可以放在控制器方法内, 以提供通用调用服务
try {
    $data = $rpc->handle('lobtao\\example\\service\\', $func, $args, function ($f, $p) use ($while_funcs) {
        if (in_array($f, $while_funcs)) {
            return true;
        }

        if ($f == 'test_test') {
            throw new RpcException('授权验证失败, 该方法不在白名单内');
        }
    });
    var_dump($data);
} catch (Exception $ex) {
    if ($ex instanceof RpcException) {
        // 记录到日志等
        echo $ex->getMessage() . PHP_EOL;
    }
}
