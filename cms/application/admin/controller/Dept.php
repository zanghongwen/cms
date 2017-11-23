<?php 
namespace app\admin\controller;

use think\controller;
use think\Db;
class Dept extends Common{
    public function index(){
        // search dept table and order by id desc, paginated by 10.
        $list = db('dept')->where(1)->order('id desc')->paginate(10);
        
        // generate page object.
        $page = $list->render();
        $this->assign('deptList', $list);
        
       return $this->fetch();   
    }
 
    public function add(){
        if(request()->isPost()){
            $data = input('post.');
            $data = $this->dataCheck($data);
            $userId = session('fadminid');
            $data['create_id'] = $userId;
            $id = db('Dept')->insertGetId($data);
            if(!$id){
                return FALSE;
            }
            if(isset($data['dosubmit'])){
                $this->success('添加成功','Dept/index');
            }else {
                $this->success('添加成功');
            }
        }else 
        return $this->fetch();
    }
    public function delete() {
        $data = input('param.');
        if (!isset($data['id']) ||empty($data['id'])) {
            $this->error('参数错误');
        }
        if (is_array($data['id'])) {
            foreach ($data['id']as $v){
                db('dept')->delete($v);
            }
        } else {
            $id = intval($data['id']);
            if (!$id) {
                $this->error('参数错误');
            }
            db('dept')->delete($id);
        }
        $this->success('删除成功');
        
    }
    
    public function edit(){
        if (request()->isPost()){
            $data = input('post.');
            $data = $this->dataCheck($data);
            db('dept')->update($data);
            $this->success('修改成功','Dept/index');
        }
        else{
            $id = intval($_GET['id']);
            if(!$id){
                $this->error('非法参数');
            }
            $result = db('dept')->find($id);
            if(!$result){
                $this->error('部门不存在');
            }
            $this->assign('result',$result);
            return $this->fetch();
        }
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