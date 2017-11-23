<?php
namespace app\admin\controller;

use think\Controller;
class Login extends Controller
{
    public function index()
    {
        return $this->fetch('login');
    }
    public function check()
    {
        if (request()->isPost()) {
            $username = input('username');
            $password = input('password');
            $code = input('code');
            $checkData = ['username' => $username, 'password' => $password];
//            $validate = validate('Login');
//            if (!$validate->check($checkData)) {
//                $this->error($validate->getError());
//            }
//            $captcha = new \Captcha();
//            if (!$captcha->check($code)) {
//                $this->error('验证码错误');
//            }
            $r = db('admin')->where('username', $username)->find();
            if (!$r) {
                $this->error('用户名不存在');
            }
            if ($r['password'] != fivePassword($password, $r['encrypt'])) {
                $this->error('密码错误');
            }
            session('fadminid', $r['id']);
            session('fadminusername', $username);
            session('fadminlasttime', $r['lasttime']);
            session('fadminlastip', $r['lastip']);
            session('fadminlogintime', request()->time());
            db('admin')->where('username', $username)->update(['lastip' => request()->ip(), 'lasttime' => request()->time()]);
            $this->success('登录成功', 'Index/index');
        }
    }
}