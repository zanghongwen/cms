<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Config;
class Field extends Common
{
    public $fields, $forbidFields, $forbidDelete, $forbidEdit, $tablepre;
    public function __construct()
    {
        parent::__construct();
        $this->fields = ['text' => '单行文本', 'textarea' => '多行文本', 'editor' => '编辑器', 'image' => '单图片', 'images' => '多图片', 'datetime' => '日期和时间', 'number' => '数字', 'radio' => ' 单选按钮', 'checkbox' => '复选框', 'select' => '下拉框'];
        $this->forbidFields = ['title', 'updatetime', 'hits', 'inputtime', 'listorder', 'status'];
        $this->forbidDelete = ['title', 'thumb', 'description', 'keywords', 'updatetime', 'inputtime', 'listorder', 'status', 'hits'];
        $this->forbidEdit = ['title', 'thumb', 'keywords', 'updatetime', 'inputtime', 'listorder', 'status', 'hits'];
        $dbconfig = Config::get('database');
        $this->tablepre = $dbconfig['prefix'];
    }
    public function index()
    {
        $list = db('field')->order('listorder asc , id asc')->select();
        $this->assign('list', $list);
        $this->assign('forbidFields', $this->forbidFields);
        $this->assign('forbidDelete', $this->forbidDelete);
        return $this->fetch();
    }
    public function add()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->checkData($data);
            if ($data['issystem'] == 1) {
                $tablename = $this->tablepre . 'article';
            } else {
                $tablename = $this->tablepre . 'article_data';
            }
            $field = $data['field'];
            $this->checkField($field, '', $tablename);
            $defaultvalue = isset($data['defaultvalue']) ? $data['defaultvalue'] : '';
            $fieldType = $this->fieldType($data['formtype']);
            if (isset($data['length'])) {
                $data['length'] = intval($data['length']);
                if ($data['length'] < 1 || $data['length'] > 255) {
                    $this->error('长度范围1~255');
                }
                $length = $data['length'];
            } else {
                $length = $data['length'] = 0;
            }
            $this->addSql($tablename, $field, $fieldType, $length, $defaultvalue);
            $id = db('field')->insertGetId($data);
            if ($id > 0) {
                $this->success('添加成功', url('Field/index'));
            } else {
                $this->error('添加失败');
            }
        }
        $this->assign('allField', $this->fields);
        return $this->fetch();
    }
    public function edit()
    {
        if (request()->isPost()) {
            $data = input('post.');
            $data = $this->checkData($data);
            if ($data['issystem'] == 1) {
                $tablename = $this->tablepre . 'article';
            } else {
                $tablename = $this->tablepre . 'article_data';
            }
            $field = $data['field'];
            $formtype = $data['formtype'];
            $defaultvalue = isset($data['defaultvalue']) ? $data['defaultvalue'] : '';
            $fieldType = $this->fieldType($data['formtype']);
            $oldfield = $data['oldfield'];
            $this->checkField($field, $oldfield, $tablename);
            if (isset($data['length'])) {
                $data['length'] = intval($data['length']);
                if ($data['length'] < 1 || $data['length'] > 255) {
                    $this->error('长度范围1~255');
                }
                $length = $data['length'];
            } else {
                $length = $data['length'] = 0;
            }
            $this->editSql($tablename, $oldfield, $field, $fieldType, $length, $defaultvalue);
            db('field')->update($data);
            $this->success('修改成功', url('Field/index'));
        }
        $id = intval(input('id'));
        if (!$id) {
            $this->error('参数错误');
        }
        $r = db('field')->find($id);
        $type = $this->fields[$r['formtype']];
        $this->assign('type', $type);
        $this->assign('formtype', $r['formtype']);
        $this->assign('id', $id);
        $this->assign('forbidEdit', $this->forbidEdit);
        $this->assign('detail', $r);
        return $this->fetch();
    }
    public function delete()
    {
        $id = intval(input('id'));
        if (!$id) {
            $this->error('参数错误');
        }
        $r = db('field')->where('id', $id)->find();
        if ($r['issystem'] == 1) {
            $tablename = $this->tablepre . 'article';
        } else {
            $tablename = $this->tablepre . 'article_data';
        }
        $field = $r['field'];
        db('field')->delete($id);
        Db::execute("ALTER TABLE `{$tablename}` DROP `{$field}`;");
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
            db('field')->where('id', $k)->update(['listorder' => $v]);
        }
        $this->success('更新成功');
    }
    public function disabled()
    {
        $id = intval(input('id'));
        if (!$id) {
            $this->error('参数错误！');
        }
        $disabled = input('disabled') ? 0 : 1;
        db('field')->where('id', $id)->update(['disabled' => $disabled]);
        $this->success('操作成功！');
    }
    private function checkField($field, $oldfield, $tablename)
    {
        $field = strtolower($field);
        $oldfield = strtolower($oldfield);
        if ($field != $oldfield) {
            $variable = Db::query("SHOW COLUMNS FROM `{$tablename}`");
            $fields = [];
            foreach ($variable as $key => $r) {
                $fields[$r['Field']] = $r['Type'];
            }
            if (array_key_exists($field, $fields)) {
                $this->error('字段已存在');
            }
        }
    }
    private function checkData($data)
    {
        if (!$data['formtype']) {
            $this->error('请选择字段类型');
        }
        if (!$data['field']) {
            $this->error('字段名不能为空');
        }
        if (!preg_match("/^[a-zA-Z]{1}([a-zA-Z0-9]|[_]){0,19}[a-zA-Z0-9]{1}\$/", $data['field'])) {
            $this->error('字段名格式错误');
        }
        if (!$data['name']) {
            $this->error('字段别名不能为空');
        }
        if ($data['formtype'] == 'number') {
            $data['defaultvalue'] = intval($data['defaultvalue']);
        }
        return $data;
    }
    public function defaultValue()
    {
        $formtype = input('formtype');
        $html = '';
        switch ($formtype) {
            case 'text':
                $html .= '<input class="common-text" size="60" name="defaultvalue" type="text">字段长度：<input class="common-text" size="10" value="100" name="length" type="text">范围：1~255';
                break;
            case 'textarea':
                $html .= '<textarea name="defaultvalue"  class="common-textarea" style="height:40px; width:80%"></textarea>';
                break;
            case 'radio':
                $html .= '<textarea name="defaultvalue"  class="common-textarea" style="height:80px; width:40%"></textarea>每行一个值';
                break;
            case 'checkbox':
                $html .= '<textarea name="defaultvalue"  class="common-textarea" style="height:80px; width:40%"></textarea>每行一个值';
                break;
            case 'select':
                $html .= '<textarea name="defaultvalue"  class="common-textarea" style="height:80px; width:40%"></textarea>每行一个值';
                break;
            case 'number':
                $html .= '<input class="common-text" size="30" name="defaultvalue" value="0" type="text">';
                break;
            default:
                # code...
                break;
        }
        echo $html;
    }
    public function defaultTips()
    {
        $formtype = input('formtype');
        $html = '';
        switch ($formtype) {
            case 'text':
                $html .= '默认值';
                break;
            case 'textarea':
                $html .= '默认值';
                break;
            case 'radio':
                $html .= '单选列表';
                break;
            case 'checkbox':
                $html .= '复选列表';
                break;
            case 'select':
                $html .= '下拉列表';
                break;
            case 'number':
                $html .= '默认值';
                break;
            default:
                # code...
                break;
        }
        echo $html;
    }
    private function addSql($tablename, $field, $fieldType, $length, $defaultvalue)
    {
        switch ($fieldType) {
            case 'varchar':
                $sql = "ALTER TABLE `{$tablename}` ADD `{$field}` VARCHAR({$length}) DEFAULT '{$defaultvalue}'";
                Db::execute($sql);
                break;
            case 'number':
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `{$tablename}` ADD `{$field}` INT(10) UNSIGNED DEFAULT '{$defaultvalue}'";
                Db::execute($sql);
                break;
            case 'int':
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `{$tablename}` ADD `{$field}` INT(10) UNSIGNED DEFAULT '{$defaultvalue}'";
                Db::execute($sql);
                break;
            case 'smallint':
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `{$tablename}` ADD `{$field}` SMALLINT(5) UNSIGNED DEFAULT '{$defaultvalue}'";
                Db::execute($sql);
                break;
            case 'text':
                Db::execute("ALTER TABLE `{$tablename}` ADD `{$field}` TEXT");
                break;
        }
    }
    private function fieldType($formtype)
    {
        switch ($formtype) {
            case 'datetime':
                $fieldType = 'int';
                break;
            case 'editor':
                $fieldType = 'text';
                break;
            case 'image':
                $fieldType = 'varchar';
                break;
            case 'images':
                $fieldType = 'text';
                break;
            case 'number':
                $fieldType = 'number';
                break;
            case 'text':
                $fieldType = 'varchar';
                break;
            case 'textarea':
                $fieldType = 'text';
                break;
            case 'radio':
                $fieldType = 'smallint';
                break;
            case 'checkbox':
                $fieldType = 'text';
                break;
            case 'select':
                $fieldType = 'smallint';
                break;
        }
        return $fieldType;
    }
    private function editSql($tablename, $oldfield, $field, $fieldType, $length, $defaultvalue)
    {
        switch ($fieldType) {
            case 'varchar':
                $sql = "ALTER TABLE `{$tablename}` CHANGE `{$oldfield}` `{$field}` VARCHAR({$length}) DEFAULT '{$defaultvalue}'";
                Db::execute($sql);
                break;
            case 'number':
                $defaultvalue = intval($defaultvalue);
                $sql = "ALTER TABLE `{$tablename}` CHANGE `{$oldfield}` `{$field}` INT(10) UNSIGNED DEFAULT '{$defaultvalue}'";
                Db::execute($sql);
                break;
            case 'int':
                $defaultvalue = intval($defaultvalue);
                Db::execute("ALTER TABLE `{$tablename}` CHANGE `{$oldfield}` `{$field}` INT(10) UNSIGNED  DEFAULT '{$defaultvalue}'");
                break;
            case 'smallint':
                $defaultvalue = intval($defaultvalue);
                Db::execute("ALTER TABLE `{$tablename}` CHANGE `{$oldfield}` `{$field}` SMALLINT(5) UNSIGNED  DEFAULT '{$defaultvalue}'");
                break;
            case 'text':
                Db::execute("ALTER TABLE `{$tablename}` CHANGE `{$oldfield}` `{$field}` TEXT");
                break;
        }
    }
}