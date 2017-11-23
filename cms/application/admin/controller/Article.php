<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
class Article extends Common
{
    public function index()
    {
        $q = input('q');
        $catid = input('catid');
        if ($q) {
            $map['title'] = ['like', '%' . strip_tags(trim($q)) . '%'];
        }
        if ($catid) {
            $map['catid'] = intval($catid);
        }
        if (!isset($map)) {
            $map = 1;
        }
        $articleList = db('article')->where($map)->order('listorder desc,id desc')->paginate(10, false, ['query' => ['q' => $q, 'catid' => $catid]]);
        $page = $articleList->render();
        $this->assign('q', $q);
        $this->assign('catid', $catid);
        $this->assign('articleList', $articleList);
        $this->assign('cateList', model('category')->getList());
        $this->assign('page', $page);
        return $this->fetch();
    }
    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $this->addContent($data);
            if (isset($data['dosubmit'])) {
                $this->success('添加成功', 'Article/index');
            } else {
                $this->success('添加成功');
            }
        }
        $contentForm = new \ContentForm();
        $forminfos = $contentForm->get();
        $this->assign('forminfos', $forminfos);
        $catid = input('catid');
        $this->assign('list', model('category')->getList());
        $this->assign('catid', intval($catid));
        return $this->fetch();
    }
    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $this->editContent($data);
            $this->success('修改成功', 'Article/index');
        }
        $id = intval($_GET['id']);
        if (!$id) {
            $this->error('非法参数');
        }
        $data = db('article')->alias('a')->join('__ARTICLE_DATA__ d ', 'a.id= d.id')->where('a.id', $id)->find();
        if (!$data) {
            $this->error('文章不存在');
        }
        $this->assign('data', $data);
        $contentForm = new \ContentForm();
        $forminfos = $contentForm->get($data);
        $this->assign('forminfos', $forminfos);
        $this->assign('list', model('category')->getList());
        return $this->fetch();
    }
    public function delete()
    {
        $data = input('param.');
        if (!isset($data['id']) || empty($data['id'])) {
            $this->error('参数错误');
        }
        if (is_array($data['id'])) {
            foreach ($data['id'] as $v) {
                db('article')->delete($v);
                db('article_data')->delete($v);
                db('tag_data')->where('contentid', $v)->delete();
            }
        } else {
            $id = intval($data['id']);
            if (!$id) {
                $this->error('非法参数');
            }
            db('article')->delete($id);
            db('article_data')->delete($id);
            db('tag_data')->where('contentid', $id)->delete();
        }
        $this->success('删除成功');
    }
    public function deleteAll()
    {
        Db::execute('truncate ' . $this->prefix . 'article');
        Db::execute('truncate ' . $this->prefix . 'article_data');
        Db::execute('truncate ' . $this->prefix . 'tag');
        Db::execute('truncate ' . $this->prefix . 'tag_data');
        $this->success('删除成功');
    }
    public function listorder()
    {
        $data = input('post.');
        if (!$data) {
            $this->error('参数错误');
        }
        foreach ($data['listorder'] as $k => $v) {
            $k = intval($k);
            db('article')->update(['id' => $k, 'listorder' => $v]);
        }
        $this->success('更新成功');
    }
    public function status()
    {
        $data = input('post.');
        if (!isset($data['id']) || empty($data['id'])) {
            $this->error('参数错误');
        }
        foreach ($data['id'] as $v) {
            $v = intval($v);
            $status = db('article')->where('id', $v)->value('status');
            $status = $status ? 0 : 1;
            db('article')->update(['id' => $v, 'status' => $status]);
        }
        $this->success('更新成功');
    }
    private function toTag($contentid, $data)
    {
        $data = preg_split('/[ ,]+/', $data);
        if (is_array($data) && !empty($data)) {
            foreach ($data as $v) {
                $v = safeReplace(addslashes($v));
                $v = str_replace(['//', '#', '.'], ' ', $v);
                $r = db('tag')->where('tag', $v)->find();
                if (!$r) {
                    $tagid = db('tag')->insertGetId(['tag' => $v, 'count' => 1]);
                } else {
                    db('tag')->where('tagid', $r['tagid'])->setInc('count', 1);
                    $tagid = $r['tagid'];
                }
                if (!db('tag_data')->where(['tagid' => $tagid, 'contentid' => $contentid])->find()) {
                    db('tag_data')->insert(['tagid' => $tagid, 'contentid' => $contentid]);
                }
            }
        }
    }
    private function dataCheck($data)
    {
        if (!intval($data['catid'])) {
            $this->error('栏目必须选择');
        }
        if (trim($data['title']) == '') {
            $this->error('标题不能为空');
        }
        
        if ($data['description'] == '' && isset($data['content'])) {
            $content = stripslashes($data['content']);
            $data['description'] = fiveCut(str_replace(["'", "\r\n", "\t", '[page]', '[/page]', '&ldquo;', '&rdquo;', '&nbsp;'], '', strip_tags($content)), 300);
            $data['description'] = addslashes($data['description']);
        }
        if (!isset($data['thumb']) && isset($data['content'])) {
            $content = stripslashes($data['content']);
            if (preg_match_all("/(src)=([\"|']?)([^ \"'>]+\\.(gif|jpg|jpeg|bmp|png))\\2/i", $content, $matches)) {
                $data['thumb'] = $matches[3][0];
            }
        }
        if (isset($data['description'])) {
            $data['description'] = str_replace(['/', '\\', '#', '.', "'"], ' ', $data['description']);
        }
        if (isset($data['keywords'])) {
            $data['keywords'] = str_replace(['/', '\\', '#', '.', "'"], ' ', $data['keywords']);
        }
        return $data;
    }
    private function addContent($data)
    {
        $data = $this->dataCheck($data);
        $contentInput = new \ContentInput();
        $inputinfo = $contentInput->get($data);
        $dataA = $inputinfo['a'];
        $dataA['catid'] = $data['catid'];
        $dataA['status'] = 1;
        $dataB = isset($inputinfo['b']) ? $inputinfo['b'] : [];
        $id = db('article')->insertGetId($dataA);
        if (!$id) {
            return FALSE;
        }
        $dataB['id'] = $id;
        db('article_data')->insert($dataB);
        if ($dataA['keywords']) {
            $this->toTag($id, $dataA['keywords']);
        }
    }
    private function editContent($data)
    {
        $data = $this->dataCheck($data);
        $contentInput = new \ContentInput();
        $inputinfo = $contentInput->get($data);
        $dataA = $inputinfo['a'];
        $dataA['catid'] = $data['catid'];
        $dataA['status'] = 1;
        $dataB = isset($inputinfo['b']) ? $inputinfo['b'] : [];
        $dataA['id'] = $dataB['id'] = $data['id'];
        db('article')->update($dataA);
        db('article_data')->update($dataB);
        if ($dataA['keywords']) {
            $this->toTag($data['id'], $dataA['keywords']);
        }
    }
}