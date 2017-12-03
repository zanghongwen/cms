<?php namespace app\admin\controller;
//明名空间为了区分系统中有重名情况

use think\controller;use think\Db;

class Employee3 extends Common {

	//跳转到首页
	public function index() {
		$name = input('name');
		if($name) {
			$map['name'] = ['like','%' . strip_tags(trim($name)) . '%'];
		}
		
		$number = input('number');
		if($number) {
			$map['number'] = trim($number);
		}
		$age = input('age');
		if($age) {
			$map['age'] = trim($age);
		}
		$gender = input('gender');
		if($gender != "") {
			$map['gender'] = trim($gender);
		}
		$map['deleted'] = '0';
		
		$list = db('employee')->where($map)->order('id desc')->paginate(10);
		$page = $list->render();
		$this->assign('name', $name);
		$this->assign('number', $number);
		$this->assign('gender', $gender);
		$this->assign('ageemply',$age);
		$this->assign('list',$list);
		$this->assign('page',$page);
		return $this->fetch();
	}
	
	public function add() {
		if (request()->isPost()) {
			$data = input('post.');
			$data = $this->dataCheck($data);
			$userId = session('fadminid');
			$data['create_user'] = $userId; 
			$id = db('employee')->insertGetId($data);
			if (!$id) {
				return FALSE;
			}
			if (isset($data['dosubmit'])) {
				$this->success('添加成功','Employee3/index');
			} else {
				$this->success('添加成功');
			}
		} else {
			$this->assign('deptlist',model('dept')->getList());
			return $this->fetch();
		}
	}
	public function delete() {
		$data = input('param.');
		if (!isset($data['id']) || empty($data['id'])) {
			$this->error('参数错误');
		}
		if (is_array($data['id'])) {
			foreach ($data['id'] as $v){
				$data['id'] = $v;
				$data['deleted'] = '1';
				db('employee')->update($data);
			}
		
			
			
			
		} else {
            $id = intval($data['id']);
            if (!$id) {
                $this->error('非法参数');
            }
            $data['id'] = $id;
            $data['deleted'] = '1';
            db('employee')->update($data);

        }
        $this->success('删除成功');
        
	}
	
	public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->dataCheck($data);
            db('employee')->update($data);
            $this->success('修改成功', 'Employee/index');
        } else {
            $id = intval($_GET['id']);
            if (!$id) {
                $this->error('非法参数');
            }
            $result = db('employee')->find($id);
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