<?php
// 命名空间 为了区分系统中有重名情况
namespace app\admin\controller;

use think\controller;use think\Db;
class Employee2 extends Common {

	/**
	 * index page
	 * Get employee date from database to display in index.html
	 * auther: steven
	 * date: 20194930
	 * version: 1.0
	 */
	public function index() {
		// get name from index page
		$name = input('name');
		if($name != '') {
			$map['name'] = ['like', '%' . strip_tags(trim($name)) . '%'];
		}
		
		// get gender from index page
        $number = input('number');
        if($number) {
            $map['number'] = trim($number);
        }
        
        // get age from index age 
        $age = input('age');
        if($age) {
            $map['age'] = trim($age);
        }
        
        // get gender from index age
        $gender = input('gender');
        if($gender != "") {
            $map['gender'] = trim($gender);
        }
        
		// if no saerch condition, set where condition to 1
        if(!isset($map)){
            $map = 1;
        }
		// search employee table and order by id desc, paginated by 10.
		$list = db('employee')->where($map)->order('id desc')->paginate(10);
		
		// generate page object.
		$page = $list->render();
		
		// transfer data to index page
		$this->assign('name', $name);
        $this->assign('number', $number);
        $this->assign('gender', $gender);
        $this->assign('age', $age);
		$this->assign('employeeList', $list);
		$this->assign('employeePage', $page);
		
		// return
		return $this->fetch();
	}

	/**
	 * add function 
	 */
	public function add() {
		//如果是点击提交
		if (request()->isPost()) {
			$data = input('post.');
			$data = $this->dataCheck($data);
			$userId = session('fadminid');
			$data['create_user'] = $userId;
			$id = db('employee')->insertGetId($data);
			if(!$id) {
				return FALSE;
			}
			if(isset($data['dosubmit'])) {
				$this->success('added', 'Employee2/index');
			}else {
				$this->success('added');
			}
		}else {
			//点击增加员工
			return $this->fetch();
		}
	}

	public function delete() {
		$data = input('param.');
		if (!isset($data['id']) ||empty($data['id'])) {
			$this->error('参数错误');
		}
		if (is_array($data['id'])) {
			foreach ($data['id']as $v){
				db('employee')->delete($v);
			}
		} else {
			$id = intval($data['id']);
			if (!$id) {
				$this->error('参数错误');
			}
			db('employee')->delete($id);
		}
		$this->success('删除成功');
   
	}
	
	public function edit(){
	    if (request()->isPost()){
	        $data = input('post.');
	        $data = $this->dataCheck($data);
	        db('employee')->update($data);
	        $this->success('修改成功','employee/index');
	    }
	    else{
	        $id = intval($_GET['id']);
	        if(!$id){
	            $this->error('非法参数');
	      }
	      $result = db('employee')->find($id);
	      if(!$result){
	          $this->error('员工不存在');
	      }
	      $this->assign('result',$result);
	      return $this->fetch();
	    }
	}

	/**
	 * 
	 * datacheck
	 */
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
}?>