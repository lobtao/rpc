<?php

use lobtao\rpc\BaseRpc;
use lobtao\rpc\RpcException;

require_once __DIR__ . '/../vendor/autoload.php';

$rpc = new BaseRpc();


/**
 * 类和方法以_分割
 * 可访问方法    test_func1, test_func2
 * 不存在方法    test_func3
 * 不存在类      test1_func
 * 禁止访问方法   test_func
 */
$func = 'test1_func'; // 测试访问方法
// 数据必须以数组包裹
$args = [
    [
        'name' => "I'm test data",
    ],
];

// 该部分可以放在控制器方法内, 以提供通用调用服务
try {
    $data = $rpc->handle('lobtao\\example\\service\\', $func, $args, function ($f, $p)  {
        // 白名单函数
        $while_funcs = [
            'test_func1',
            'test_func2',
        ];

        if (in_array($f, $while_funcs)) {
            return true;
        }

        if ($f == 'test_func') {
            throw new RpcException('禁止访问函数');
        }
    });
    var_dump($data);
} catch (Exception $ex) {
    if ($ex instanceof RpcException) {
        // 记录到日志等
        echo $ex->getMessage() . PHP_EOL;
    }
}
