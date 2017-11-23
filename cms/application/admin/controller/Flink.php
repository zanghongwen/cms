<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
class Flink extends Common
{
    public function index()
    {
        $list = db('flink')->order('listorder desc,id desc')->paginate(10);
        $page = $list->render();
        $this->assign('list', $list);
        $this->assign('page', $page);
        return $this->fetch();
    }

    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->dataCheck($data);
            $id = db('flink')->insertGetId($data);
            if (!$id) {
                return FALSE;
            }
            if (isset($data['dosubmit'])) {
                $this->success('添加成功', 'Flink/index');
            } else {
                $this->success('添加成功');
            }
        } else {
            return $this->fetch();
        }
    }

    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->dataCheck($data);
            db('flink')->update($data);
            $this->success('修改成功', 'Flink/index');
        } else {
            $id = intval($_GET['id']);
            if (!$id) {
                $this->error('非法参数');
            }
            $result = db('flink')->find($id);
            if (!$result) {
                $this->error('友情链接不存在');
            }
            $this->assign('result', $result);
            return $this->fetch();
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
                db('flink')->delete($v);
            }
        } else {
            $id = intval($data['id']);
            if (!$id) {
                $this->error('非法参数');
            }
            db('flink')->delete($id);
        }
        $this->success('删除成功');
    }

    public function deleteAll()
    {
        Db::execute('truncate ' . $this->prefix . 'flink');
        $this->success('删除成功');
    }
    
    public function listorder()
    {
        $data = input('post.');
        if (!$data) {
            $this->error('参数错误');
        }
        foreach ($data['listorder'] as $k => $v) {
            db('flink')->update(['id' => $k, 'listorder' => $v]);
        }
        $this->success('更新成功');
    }
    private function dataCheck($data)
    {
        $checkData = ['title' => $data['title']];
        $validate = validate('Flink');
        if (!$validate->check($checkData)) {
            $this->error($validate->getError());
        }
        $data['title'] = safeReplace($data['title']);
        return $data;
    }
}