<?php

namespace lobtao\rpc;

class BaseRpc
{
    public $ERR_MSG_METHOD_FORMAT_ERROR = 'request method format error';
    public $ERR_MSG_PARAMS_FORMAT_ERROR = 'request parameter format error';
    public $ERR_MSG_CLASS_NOT_FOUND = '`%s` does not exist';
    public $ERR_MSG_FUNCTION_NOT_FOUND_IN_CLASS = 'medthod `{0}` does not exist in class `{1}`';

    protected $namespace;
    protected $func;
    protected $args;

    /**
     * @param $namespace
     * @param $func
     * @param $args
     * @param null $authVerify
     * @param null $outMakeInstance
     * @return mixed
     * @throws RpcException
     */
    public function handle($namespace, $func, $args, $authVerify = null, $outMakeInstance = null)
    {
        $this->namespace = $namespace;
        $this->func = $func;
        $this->args = $args;

        // 微信小程序特别设置；浏览器提交过来自动转换
        if (gettype($this->args) == 'string') {
            $this->args = json_decode($this->args, true);
        }

        // 检查参数格式
        if (empty($this->args)) {
            $this->args = [[]];
        } else {
            if (gettype($this->args) != 'array' || !isset($this->args[0])) {
                throw new RpcException($this->ERR_MSG_PARAMS_FORMAT_ERROR);
            }
        }

        // 权限等验证过滤处理
        if (isset($authVerify)) {
            call_user_func_array($authVerify, [$this->func, $this->args]);
        }

        return $this->invokeFunc($this->func, $this->args, $outMakeInstance);
    }

    /**
     * 以‘_’来分割ajax传递过来的类名和方法名，调用该方法，并返回值
     *
     * @param $func
     * @param $args
     * @param null $outMakeInstance
     * @return mixed
     * @throws RpcException
     */
    protected function invokeFunc($func, $args, $outMakeInstance = null)
    {
        $params = explode('_', $func, 2);
        if (count($params) != 2) throw new RpcException($this->ERR_MSG_METHOD_FORMAT_ERROR);

        $serName = ucfirst($params[0]);
        $className = $this->namespace . $serName . 'Service';
        $funcName = $params[1];
        if (!class_exists($className)) throw new RpcException(sprintf($this->ERR_MSG_CLASS_NOT_FOUND, $className));

        if (isset($outMakeInstance)) {
            $object = $outMakeInstance($className);
        } else {
            $object = new $className();
        }

        if (!method_exists($object, $funcName)) {
            $msg = str_replace('{0}', $funcName, $this->ERR_MSG_FUNCTION_NOT_FOUND_IN_CLASS);
            $msg = str_replace('{1}', $className, $msg);
            throw new RpcException($msg);
        }
        return call_user_func_array([$object, $funcName], $args);
    }

}
