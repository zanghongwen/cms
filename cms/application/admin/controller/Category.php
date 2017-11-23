<?php
namespace app\admin\controller;

use think\Controller;
class Category extends Common
{
    public function index()
    {
        $list = model('category')->getList();
        foreach ($list as $k => $v) {
            $list[$k]['number'] = db('article')->where('catid', $v['catid'])->count();
        }
        $this->assign('list', $list);
        return $this->fetch();
    }
    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->dataCheck($data);
            db('category')->insert($data);
            $this->success('添加成功', 'Category/index');
        }
        $catid = input('catid');
        $this->assign('list', model('category')->getList());
        $this->assign('catid', intval($catid));
        return $this->fetch();
    }
    public function batch()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->dataCheck($data);
            if (strpos($data['catname'], "\n") === false) {
                $data['catname'] = fiveCut($data['catname'], 32);
                db('category')->insert($data);
                $this->success('添加成功', 'Category/index');
            } else {
                $arr = explode("\n", $data['catname']);
                foreach ($arr as $key => $val) {
                    $val = trim($val);
                    if (!$val) {
                        continue;
                    }
                    $data['catname'] = fiveCut($val, 32);
                    db('category')->insert($data);
                }
                $this->success('添加成功', 'Category/index');
            }
        }
        $this->assign('list', model('category')->getList());
        return $this->fetch();
    }
    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->dataCheck($data);
            db('category')->update($data);
            $this->success('修改成功', 'Category/index');
        }
        $catid = input('catid');
        if (!$catid) {
            $this->error('参数错误');
        }
        $detail = db('category')->find($catid);
        $this->assign('list', model('category')->getList());
        $this->assign('detail', $detail);
        return $this->fetch();
    }
    public function listorder()
    {
        if (request()->isPost()) {
            $data = input('post.');
            foreach ($data['listorder'] as $key => $val) {
                db('category')->update(['catid' => $key, 'listorder' => intval($val)]);
            }
            $this->success('排序成功');
        }
    }
    public function delete()
    {
        $catid = input('catid');
        if (!$catid) {
            $this->error('参数错误');
        }
        $catids = explode(',', catidStr($catid));
        db('category')->delete($catids);
        $articleList = db('article')->where('catid', 'in', $catids)->field('id')->select();
        foreach ($articleList as $v) {
            db('article')->delete($v['id']);
            db('article_data')->delete($v['id']);
        }
        $this->success('删除成功');
    }
    private function dataCheck($data)
    {
        $checkData = ['catname' => $data['catname'], 'category' => $data['category'], 'list' => $data['list'], 'show' => $data['show']];
        $validate = validate('Category');
        if (!$validate->check($checkData)) {
            $this->error($validate->getError());
        }
        if (isset($data['content'])) {
            $data['content'] = autoSaveImage($data['content']);
        }
        $data['pn'] = intval($data['pn']) ? intval($data['pn']) : 20;
        return $data;
    }
}