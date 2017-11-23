<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
class Guestbook extends Common
{
    public function index()
    {
        $list = db('guestbook')->order('id desc')->paginate(10);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function edit()
    {
        $id = intval($_GET['id']);
        if (!$id) {
            $this->error('非法参数');
        }
        $result = db('guestbook')->find($id);
        if (!$result) {
            $this->error('留言不存在');
        }
        $this->assign('result', $result);
        return $this->fetch();
    }
    public function update()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data['replytime'] = request()->time();
            $data['status'] = 1;
            db('guestbook')->update($data);
            $this->success('回复成功', 'guestbook/index');
        }
    }
    public function delete()
    {
        $data = input('param.');
        if (!isset($data['id']) || empty($data['id'])) {
            $this->error('参数错误');
        }
        if (is_array($data['id'])) {
            foreach ($data['id'] as $v) {
                db('guestbook')->delete($v);
            }
        } else {
            $id = intval($data['id']);
            if (!$id) {
                $this->error('非法参数');
            }
            db('guestbook')->delete($id);
        }
        $this->success('删除成功');
    }
    public function deleteAll()
    {
        Db::execute('truncate ' . $this->prefix . 'guestbook');
        $this->success('删除成功');
    }
}