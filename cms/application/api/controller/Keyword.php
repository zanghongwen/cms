<?php
namespace app\api\controller;

use think\Controller;
class Keyword extends Controller
{
    public function get()
    {
        $number = input('number');
        $data = input('data');
        return $this->getData($data, $number);
    }
    private function getData($data, $number = 3)
    {
        $data = trim(strip_tags($data));
        if (empty($data)) {
            return '';
        }
        $http = new \Http();
        $data = iconv('utf-8', 'gbk', $data);
        $http->post('http://tool.phpcms.cn/api/get_keywords.php', array('siteurl' => __ROOT__, 'charset' => 'utf-8', 'data' => $data, 'number' => $number));
        if ($http->is_ok()) {
            return iconv('gbk', 'utf-8', $http->get_data());
        }
        return '';
    }
}