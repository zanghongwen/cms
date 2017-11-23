<?php 
// name space　スペース
namespace app\admin\controller;

// import think php controller
use think\controller;

// import think php DB
use think\Db;

/**
 * Create Order class
 * 作者: penny
 * 日付: 2029384
 */
class Order extends Common{
	
	/**
	 * index function
	 * ORDER　ページ
	 * 
	 */
	public function index() {
		// get name from order index page
		$name = input('name');
		if($name) {
			// set name to map
			$map['name'] = ['like', '%'.strip_tags(trim($name)).'%'];
		}
		
		// get code from order index page
		$code = input('code');
		if($code) {
			$map['code'] = trim($code);
		}
		
		// get date from order index page
		$date = input('date');
		if($date) {
			var_dump(123123);
			$map['date'] = trim($date);
		}
		
		// set map to 1 if map is null
        if(!isset($map)){
            $map = 1;
        }
        
        // search order from DB, order by ID asc, paginate by five.
		$list = db('order')->where($map)->order('id asc')->paginate(5);
		
		// render page object
        $page = $list->render();
        
        // ページを利用する
		$this->assign('orderList', $list);
        $this->assign('page', $page);
        $this->assign('name',$name);
        $this->assign('code',$code);
        $this->assign('date',$date);
		return $this->fetch();
	}
	
	public function add() {
		if(request()->isPost()) {
			$data = input('post.');
			$data = $this->dataCheck($data);
			$userId = session('fadminid');
			$data['create_id'] = $userId;
			$id = db('order')->insertGetId($data);
			if(!$id) {
				return FALSE;
			}
			if (isset($data['dosubmit'])) {
				$this->success('添加成功', 'Order/index');
			}else {
				$this->success('添加成功');
			}
		}else {
			return $this->fetch();
			
		}
	}
	
	public function delete() {
		$data = input('param.');
		if (!isset($data['id'])|| empty($data['id'])) {
			$this->error('参数错误');
		}
		if (is_array($data['id'])) {
			foreach ($data['id'] as $v){
				db('order')->delete($v);	
			}
		}else {
			$id = intval($data['id']);
			if (!$id) {
				$this->error('非法参数');
			}
			db('order')->delete($id);
		}
		$this->success('删除成功');
	}
	
	
    /**
     * 点击修改按钮
     */
	public function toEdit() {
		  $id = intval($_GET['id']);
          if (!$id) {
              $this->error('非法参数');
          }
          $result = db('order')->find($id);
          if (!$result) {
              $this->error('订单不存在');
          }
          $this->assign('result', $result);
          return $this->fetch('edit');
	}
	

	/**
	 * save edit data
	 */
	public function saveEdit() {
        $data = input('post.');
        $data = $this->dataCheck($data);
        db('order')->update($data);
        $this->success('修改成功', 'Order/index');
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