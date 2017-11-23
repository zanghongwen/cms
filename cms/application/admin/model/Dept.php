<?php
namespace app\admin\model;

use think\Model;
class Dept extends Model
{
    public function getList($pid=0)
    {
        $list = $this->order('id desc')->select();
        return $list;
    }
}