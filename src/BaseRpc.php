<?php

namespace lobtao\rpc;

class BaseRpc
{
    protected $namespace;
    protected $func;
    protected $args;

    /**
     * @param $namespace
     * @param $func
     * @param $args
     * @param null $authVerify
     *
     * @return mixed
     * @throws RpcException
     */
    public function handle($namespace, $func, $args, $authVerify = null) {
        $this->namespace = $namespace;
        $this->func      = $func;
        $this->args      = $args;

        // 微信小程序特别设置；浏览器提交过来自动转换
        if (gettype($this->args) == 'string') {
            $this->args = json_decode($this->args, true);
        }

        // 权限等验证过滤处理
        if (isset($authVerify)) {
            call_user_func_array($authVerify, [$this->func, $this->args]);
        }

        return $this->invokeFunc($this->func, $this->args);
    }

    /**
     * 以‘-’来分割ajax传递过来的类名和方法名，调用该方法，并返回值
     *
     * @param $func
     * @param $args
     *
     * @return mixed
     * @throws RpcException
     */
    protected function invokeFunc($func, $args)
    {
        $params = explode('_', $func, 2);
        if (count($params) != 2) throw new RpcException('请求参数错误');

        $serName = ucfirst($params[0]);
        $className = $this->namespace . $serName . 'Service';
        $funcName = $params[1];
        if (!class_exists($className)) throw new RpcException('类' . $className . '不存在！');

        $object = new $className();
        if (!method_exists($object, $funcName)) throw new RpcException($serName . '中不存在' . $funcName . '方法');
        return call_user_func_array([$object, $funcName], $args);
    }

}
