<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
class Tag extends Common
{
    public function index()
    {
        $this->check();
        $list = db('tag')->order('tagid desc')->paginate(10);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }
    private function check()
    {
        db('tag')->where(['tag'=>''])->delete();
       
    }
    public function delete()
    {
        $data = input('param.');
        if (!isset($data['tagid']) || empty($data['tagid'])) {
            $this->error('参数错误');
        }
        if (is_array($data['tagid'])) {
            foreach ($data['tagid'] as $v) {
                db('tag')->delete($v);
                db('tag_data')->where(['tagid' => $v])->delete();
            }
        } else {
            $tagid = intval($data['tagid']);
            if (!$tagid) {
                $this->error('非法参数');
            }
            db('tag')->delete($tagid);
            db('tag_data')->where(['tagid' => $tagid])->delete();
        }
        $this->success('删除成功');
    }
	
	public function deleteAll()
    {
        Db::execute('truncate ' . $this->prefix . 'tag');
		Db::execute('truncate ' . $this->prefix . 'tag_data');
        $this->success('删除成功');
    }
}