<?php
namespace app\api\controller;

use think\Controller;
class Code extends Controller
{
    public function index($id = '')
    {
        $captcha = new \Captcha(config('captcha'));
        return $captcha->entry($id);
    }
}