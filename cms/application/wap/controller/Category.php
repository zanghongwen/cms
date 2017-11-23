<?php
namespace app\wap\controller;

use think\Controller;
class Category extends Common
{
    public function lists()
    {
        $catid = intval(input('catid'));
        if (!$catid) {
            $this->error('参数错误');
        }
        $this->assign('catid', $catid);
        /*栏目详情start*/
        $cateInfo = db('category')->where('catid', $catid)->find();
        if (!$cateInfo) {
            $this->error('栏目不存在');
        }
        $this->assign('category', fiveHtmlentitydecode($cateInfo));
        /*栏目详情end*/
        /*文章列表start*/
        $list = db('article')->alias('a')->join('__CATEGORY__ c', 'c.catid= a.catid')->field('a.*,c.catname')->where('a.catid', 'in', catidStr($catid))->where('a.status', 1)->order('a.id desc')->paginate($cateInfo['pn'], false, ['query' => ['catid' => $catid]]);
        $pages = $list->render();
        $this->assign('list', $list);
        $this->assign('pages', $pages);
        /*文章列表end*/
        /*seo start*/
        $seo = seo($cateInfo['catname'] . '-' . $this->seo['title'], $cateInfo['keywords'], $cateInfo['description']);
        $this->assign('seo', $seo);
        /*seo end*/
        /*模板start*/
        if ($cateInfo['ispart'] == 1) {
            return $this->fetch($this->template . $cateInfo['category']);
        } else {
            return $this->fetch($this->template . $cateInfo['list']);
        }
        /*模板end*/
    }
    public function more()
    {
        $catid = intval(input('catid'));
        if (!$catid) {
            $this->error('参数错误');
        }
        $this->assign('catid', $catid);
        /*栏目详情start*/
        $cateInfo = db('category')->where('catid', $catid)->find();
        if (!$cateInfo) {
            $this->error('栏目不存在');
        }
        $this->assign('category', fiveHtmlentitydecode($cateInfo));
        /*栏目详情end*/
        $article = db('article');
        $total = $article->where('catid', 'in', catidStr($catid))->count();
        $this->assign('total', $total);
        $list = $article->where('catid', 'in', catidStr($catid))->limit(5)->order('id desc')->select();
        foreach($list as $k=>$v){
			$list[$k]['inputtime']=date('Y-m-d H:i:s',$v['inputtime']);
			}
		$this->assign('list', $list);
        /*seo start*/
        $seo = seo($cateInfo['catname'] . '-' . $this->seo['title'], $cateInfo['keywords'], $cateInfo['description']);
        $this->assign('seo', $seo);
        /*seo end*/
        return $this->fetch($this->template . 'more.html');
    }
    
    public function ajax()
    {
        $article = db('article');
        $start = input('start');
        $list = $article->limit($start, 5)->order('id desc')->select();
		foreach($list as $k=>$v){
			$list[$k]['inputtime']=date('Y-m-d H:i:s',$v['inputtime']);
			}
        return json(['result' => $list, 'status' => 1, 'msg' => '获取成功！']);
    }
}