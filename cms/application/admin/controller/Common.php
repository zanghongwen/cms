<?php
namespace app\admin\controller;

use think\Controller;
use think\Config;
class Common extends Controller
{
    public $prefix;
    protected function _initialize()
    {
        if (!session('fadminid') || !session('fadminusername') || request()->time() - session('fadminlogintime') > 2 * 60 * 60) {
            $this->error('请重新登录','Login/index');
        }
        $config = Config::get('database');
        $this->prefix = $config['prefix'];
    }
    public function logout()
    {
        session('fadminusername', null);
        session('fadminid', null);
        session('fadminlasttime', null);
        session('fadminlogintime', null);
        session('fadminlastip', null);
        $this->success('退出成功', 'Login/index');
    }
    public function cache()
    {
        $path = RUNTIME_PATH;
        delDir($path);
        $this->success('清除缓存成功');
    }
}