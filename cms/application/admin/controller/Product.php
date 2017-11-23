<?php namespace app\admin\controller;
// 命名空间 为了区分系统中有重名情况

use think\controller;use think\Db;

class Product extends Common {

	// 跳转到首页
	public function index(){
        $name = input('name');
        if($name){
            $map['name'] = ['like', '%' . strip_tags(trim($name)) . '%'];
        }

        $model = input('model');
        if($model) {
            $map['model'] = trim($model);
        }
        $date = input('date');
        if($date) {
            $map['date'] = trim($date);
        }
        $made = input('made');
        if($made != "") {
            $map['made'] = trim($made);
        }

        if(!isset($map)){
            $map = 1;
        }
    	$list = db('product')->where($map)->order('id desc')->paginate(10);
        $page = $list->render();
        $this->assign('name', $name);
        $this->assign('model', $model);
        $this->assign('date', $date);
        $this->assign('made', $made);
        $this->assign('list', $list);
        $this->assign('page', $page);
		return $this->fetch();
	}

	public function add(){
		 if (request()->isPost()) {
            $data = input('post.');
            $data = $this->dataCheck($data);
            $userId = session('fadminid');
            $data['create_user'] = $userId;          
            $id = db('product')->insertGetId($data);
            if (!$id) {
                return FALSE;
            }
            if (isset($data['dosubmit'])) {
                $this->success('添加成功', 'Product/index');
            } else {
                $this->success('添加成功');
            }
        } else {
        		// get dept data
        		//向页面中传值
        		
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
                db('product')->delete($v);
            }
        } else {
            $id = intval($data['id']);
            if (!$id) {
                $this->error('非法参数');
            }
            db('product')->delete($id);
        }
        $this->success('删除成功');

    }

	public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->dataCheck($data);
            db('product')->update($data);
            $this->success('修改成功', 'Product/index');
        } else {
            $id = intval($_GET['id']);
            if (!$id) {
                $this->error('非法参数');
            }
            $result = db('product')->find($id);
            if (!$result) {
                $this->error('员工不存在');
            }
            $this->assign('result', $result);
            return $this->fetch();
        }
    }

	public function deleteAll()
    {
        Db::execute('truncate ' . $this->prefix . 'employee');
        $this->success('删除成功');
    }

	private function dataCheck($data)
    {
        $checkData = ['name' => $data['name']];
        // $validate = validate('Employee');
        // if (!$validate->check($checkData)) {
        //     $this->error($validate->getError());
        // }
        $data['name'] = safeReplace($data['name']);
        return $data;
    }
}

?>