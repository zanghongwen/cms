<?php
namespace app\admin\validate;

use think\Validate;
class Category extends Validate
{
    protected $rule = ['catname'  => 'require|max:30', 
	                   'category' => 'require|isHtml', 
					   'list'     => 'require|isHtml',
					   'show'     => 'require|isHtml'
					  ];
					  
    protected $message = ['catname.require' => '栏目名称必须填写',
	                      'category.require' => '频道模板必须填写', 
						  'list.require' => '列表模板必须填写',
						  'show.require'=>'文章模板必须填写'
						 ];
						 
    protected function isHtml($value, $rule, $data)
    {
        if (!preg_match('/^(.+).html$/', $value)) {
            return '模板格式错误';
        }
        return true;
    }
   
}