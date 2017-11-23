<?php
namespace app\admin\validate;

use think\Validate;
class Login extends Validate
{
    protected $rule = ['username' => 'require|isUsername', 
	                   'password' => 'require|isPassword', 
					   'code' => 'require|isCode'
					  ];
					  
    protected $message = ['username.require' => '用户名必须填写',
	                      'password.require' => '密码必须填写', 
						  'code.require' => '验证码必须填写'
						 ];
						 
    protected function isUsername($value, $rule, $data)
    {
        if (!preg_match('/^[a-zA-Z0-9_-]{5,18}$/', $value)) {
            return '用户名格式错误';
        }
        return true;
    }
    protected function isPassword($value, $rule, $data)
    {
        if (!preg_match('/^[a-zA-Z0-9_-]{5,18}$/', $value)) {
            return '密码格式错误';
        }
        return true;
    }
    protected function isCode($value, $rule, $data)
    {
        if (!preg_match('/^[a-zA-Z0-9]{4}$/', $value)) {
            return '验证码格式错误';
        }
        return true;
    }
}