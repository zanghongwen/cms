<?php

function trimScript($str) {
	if(is_array($str)){
		foreach ($str as $key => $val){
			$str[$key] = trimScript($val);
		}
 	}else{
 		$str = preg_replace ( '/\<([\/]?)script([^\>]*?)\>/si', '&lt;\\1script\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)iframe([^\>]*?)\>/si', '&lt;\\1iframe\\2&gt;', $str );
		$str = preg_replace ( '/\<([\/]?)frame([^\>]*?)\>/si', '&lt;\\1frame\\2&gt;', $str );
		$str = str_replace ( 'javascript:', 'javascript：', $str );
 	}
	return $str;
}

function removeXss($string) { 
    $string = preg_replace('/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S', '', $string);

    $parm1 = ['javascript', 'vbscript', 'expression', 'applet', 'meta', 'xml', 'blink', 'link', 'script', 'embed', 'object', 'iframe', 'frame', 'frameset', 'ilayer', 'layer', 'bgsound', 'title', 'base'];

    $parm2 = ['onabort', 'onactivate', 'onafterprint', 'onafterupdate', 'onbeforeactivate', 'onbeforecopy', 'onbeforecut', 'onbeforedeactivate', 'onbeforeeditfocus', 'onbeforepaste', 'onbeforeprint', 'onbeforeunload', 'onbeforeupdate', 'onblur', 'onbounce', 'oncellchange', 'onchange', 'onclick', 'oncontextmenu', 'oncontrolselect', 'oncopy', 'oncut', 'ondataavailable', 'ondatasetchanged', 'ondatasetcomplete', 'ondblclick', 'ondeactivate', 'ondrag', 'ondragend', 'ondragenter', 'ondragleave', 'ondragover', 'ondragstart', 'ondrop', 'onerror', 'onerrorupdate', 'onfilterchange', 'onfinish', 'onfocus', 'onfocusin', 'onfocusout', 'onhelp', 'onkeydown', 'onkeypress', 'onkeyup', 'onlayoutcomplete', 'onload', 'onlosecapture', 'onmousedown', 'onmouseenter', 'onmouseleave', 'onmousemove', 'onmouseout', 'onmouseover', 'onmouseup', 'onmousewheel', 'onmove', 'onmoveend', 'onmovestart', 'onpaste', 'onpropertychange', 'onreadystatechange', 'onreset', 'onresize', 'onresizeend', 'onresizestart', 'onrowenter', 'onrowexit', 'onrowsdelete', 'onrowsinserted', 'onscroll', 'onselect', 'onselectionchange', 'onselectstart', 'onstart', 'onstop', 'onsubmit', 'onunload'];

    $parm = array_merge($parm1, $parm2); 

	for ($i = 0; $i < sizeof($parm); $i++) { 
		$pattern = '/'; 
		for ($j = 0; $j < strlen($parm[$i]); $j++) { 
			if ($j > 0) { 
				$pattern .= '('; 
				$pattern .= '(&#[x|X]0([9][a][b]);?)?'; 
				$pattern .= '|(&#0([9][10][13]);?)?'; 
				$pattern .= ')?'; 
			}
			$pattern .= $parm[$i][$j]; 
		}
		$pattern .= '/i';
		$string = preg_replace($pattern, ' ', $string); 
	}
	return $string;
}

function fiveAddslashes($string)
{
    if (!is_array($string)) {
        return addslashes($string);
    }
    foreach ($string as $key => $val) {
        $string[$key] = fiveAddslashes($val);
    }
    return $string;
}

function fiveStripslashes($string)
{
    if (!is_array($string)) {
        return stripslashes($string);
    }
    foreach ($string as $key => $val) {
        $string[$key] = fiveStripslashes($val);
    }
    return $string;
}

function fiveHtmlspecialchars($string)
{
    $encoding = 'utf-8';
    if (!is_array($string)) {
        return htmlspecialchars($string, ENT_QUOTES, $encoding);
    }
    foreach ($string as $key => $val) {
        $string[$key] = fiveHtmlspecialchars($val);
    }
    return $string;
}
function fiveHtmlentitydecode($string)
{
    $encoding = 'utf-8';
    if (!is_array($string)) {
        return html_entity_decode($string, ENT_QUOTES, $encoding);
    }
    foreach ($string as $key => $val) {
        $string[$key] = fiveHtmlentitydecode($val);
    }
    return $string;
}
function fiveHtmlentities($string)
{
    $encoding = 'utf-8';
    return htmlentities($string, ENT_QUOTES, $encoding);
}
function safeReplace($string)
{
    $string = str_replace('%20', '', $string);
    $string = str_replace('%27', '', $string);
    $string = str_replace('%2527', '', $string);
    $string = str_replace('*', '', $string);
    $string = str_replace('"', '&quot;', $string);
    $string = str_replace("'", '', $string);
    $string = str_replace('"', '', $string);
    $string = str_replace(';', '', $string);
    $string = str_replace('<', '&lt;', $string);
    $string = str_replace('>', '&gt;', $string);
    $string = str_replace("{", '', $string);
    $string = str_replace('}', '', $string);
    $string = str_replace('\\', '', $string);
    return $string;
}

function delDir($dirpath){
    $dh=opendir($dirpath);
    while (($file=readdir($dh))!==false) {
        if($file!="." && $file!="..") {
            $fullpath=$dirpath."/".$file;
            if(!is_dir($fullpath)) {
                unlink($fullpath);
            } else {
                delDir($fullpath);
                @rmdir($fullpath);
            }
        }
    }    
    closedir($dh);
    $isEmpty = true;
    $dh=opendir($dirpath);
    while (($file=readdir($dh))!== false) {
        if($file!="." && $file!="..") {
            $isEmpty = false;
            break;
        }
    }
    return $isEmpty;
}

function fiveCut($string, $length, $dot = '...')
{
    $strlen = strlen($string);
    if ($strlen <= $length) {
        return $string;
    }
    $string = str_replace([' ', '&nbsp;', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'], ['∵', ' ', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'], $string);
    $strcut = '';
    $length = intval($length - strlen($dot) - $length / 3);
    $n = $tn = $noc = 0;
    while ($n < strlen($string)) {
        $t = ord($string[$n]);
        if ($t == 9 || $t == 10 || 32 <= $t && $t <= 126) {
            $tn = 1;
            $n++;
            $noc++;
        } elseif (194 <= $t && $t <= 223) {
            $tn = 2;
            $n += 2;
            $noc += 2;
        } elseif (224 <= $t && $t <= 239) {
            $tn = 3;
            $n += 3;
            $noc += 2;
        } elseif (240 <= $t && $t <= 247) {
            $tn = 4;
            $n += 4;
            $noc += 2;
        } elseif (248 <= $t && $t <= 251) {
            $tn = 5;
            $n += 5;
            $noc += 2;
        } elseif ($t == 252 || $t == 253) {
            $tn = 6;
            $n += 6;
            $noc += 2;
        } else {
            $n++;
        }
        if ($noc >= $length) {
            break;
        }
    }
    if ($noc > $length) {
        $n -= $tn;
    }
    $strcut = substr($string, 0, $n);
    $strcut = str_replace(['∵', '&', '"', "'", '“', '”', '—', '<', '>', '·', '…'], [' ', '&amp;', '&quot;', '&#039;', '&ldquo;', '&rdquo;', '&mdash;', '&lt;', '&gt;', '&middot;', '&hellip;'], $strcut);
    return $strcut . $dot;
}
function dirPath($path)
{
    $path = str_replace('\\', '/', $path);
    if (substr($path, -1) != '/') {
        $path = $path . '/';
    }
    return $path;
}
function dirCreate($path, $mode = 0777)
{
    if (is_dir($path)) {
        return TRUE;
    }
    $ftp_enable = 0;
    $path = dirPath($path);
    $temp = explode('/', $path);
    $cur_dir = '';
    $max = count($temp) - 1;
    for ($i = 0; $i < $max; $i++) {
        $cur_dir .= $temp[$i] . '/';
        if (@is_dir($cur_dir)) {
            continue;
        }
        @mkdir($cur_dir, 0777, true);
        @chmod($cur_dir, 0777);
    }
    return is_dir($path);
}
//获取某分类的直接子分类
function getSons($categorys, $catid = 0)
{
    $sons = [];
    foreach ($categorys as $item) {
        if ($item['pid'] == $catid) {
            $sons[] = $item;
        }
    }
    return $sons;
}
//获取某个分类的所有子分类
function getSubs($categorys, $catid = 0, $level = 1)
{
    $subs = [];
    foreach ($categorys as $item) {
        if ($item['pid'] == $catid) {
            $item['level'] = $level;
            $subs[] = $item;
            $subs = array_merge($subs, getSubs($categorys, $item['catid'], $level + 1));
        }
    }
    return $subs;
}
//获取某个分类的所有父分类
function getParents($categorys, $catid)
{
    $tree = [];
    foreach ($categorys as $item) {
        if ($item['catid'] == $catid) {
            if ($item['pid'] > 0) {
                $tree = array_merge($tree, getParents($categorys, $item['pid']));
            }
            $tree[] = $item;
            break;
        }
    }
    return $tree;
}


function fiveRandom($length, $chars = '0123456789')
{
    $hash = '';
    $max = strlen($chars) - 1;
    for ($i = 0; $i < $length; $i++) {
        $hash .= $chars[mt_rand(0, $max)];
    }
    return $hash;
}
function fiveRandomStr($lenth = 6)
{
    return fiveRandom($lenth, '123456789abcdefghijklmnpqrstuvwxyzABCDEFGHIJKLMNPQRSTUVWXYZ');
}
function fivePassword($password, $encrypt = '')
{
    $pwd = [];
    $pwd['encrypt'] = $encrypt ? $encrypt : fiveRandomStr();
    $pwd['password'] = md5(md5(trim($password)) . $pwd['encrypt']);
    return $encrypt ? $pwd['password'] : $pwd;
}
function pathConvert($path)
{
    $path = str_replace('./', '/', $path);
    $path = str_replace('\\', '/', $path);
    return str_replace('//', '/', $path);
}

function keywordToArray($str)
{
    $result = [];
    $array = [];
    $str = str_replace('，', ',', $str);
    $str = str_replace("n", ',', $str);
    $str = str_replace("rn", ',', $str);
    $str = str_replace(' ', ',', $str);
    $array = explode(',', $str);
    foreach ($array as $key => $value) {
        if ('' != ($value = trim($value))) {
            $result[] = $value;
        }
    }
    return $result;
}
function fiveRandomColor()
{
    mt_srand((double) microtime() * 1000000);
    $c = '';
    while (strlen($c) < 6) {
        $c .= sprintf("%02X", mt_rand(0, 255));
    }
    return $c;
}

function string2array($data)
{
    $data = trim($data);
    if ($data == '') {
        return [];
    }
    if (strpos($data, 'array') === 0) {
        @eval("\$array = {$data};");
    } else {
        if (strpos($data, '{\\') === 0) {
            $data = stripslashes($data);
        }
        $array = json_decode($data, true);
    }
    return $array;
}
function array2string($data, $isformdata = 1)
{
    if ($data == '' || empty($data)) {
        return '';
    }
    if ($isformdata) {
        $data = fiveStripslashes($data);
    }
    if (version_compare(PHP_VERSION, '5.3.0', '<')) {
        return addslashes(json_encode($data));
    } else {
        return addslashes(json_encode($data, JSON_FORCE_OBJECT));
    }
}

function autoSaveImage($body)
    {
        $body = fiveStripslashes($body);
        if (!preg_match_all('/<img.*?src="(.*?)".*?>/is', $body, $img_array)) {
            return $body;
        }
        $img_array = array_unique($img_array[1]);
        set_time_limit(0);
        $imgPath = 'uploads/' . date("Ymd");
        $milliSecond = date("YmdHis");
        dirCreate($imgPath);
        foreach ($img_array as $key => $value) {
            if (preg_match("#" . "http://" . $_SERVER["HTTP_HOST"] . "#i", $value)) {
                continue;
            }
            if (!preg_match("#^http:\\/\\/#i", $value)) {
                continue;
            }
            $value = trim($value);
            $imgAttr = get_headers($value, true);
            switch ($imgAttr['Content-Type']) {
                case 'image/png':
                    $ext = 'png';
                    break;
                case 'image/jpeg':
                    $ext = 'jpg';
                    break;
                case 'image/gif':
                    $ext = 'gif';
                    break;
                default:
                    $ext = 'jpg';
            }
            $get_file = @file_get_contents($value);
            $filename = mt_rand(100000, 999999) . $milliSecond . $key . '.' . $ext;
            $rndFileName = $imgPath . '/' . $filename;
            if ($get_file) {
                $fp = @fopen($rndFileName, "w");
                @fwrite($fp, $get_file);
                @fclose($fp);
                $webconfig = db('system')->find(1);
                if ($webconfig['isthumb']) {
                    $image = \Image::open('./' . $rndFileName);
                    $image->thumb($webconfig['width'], $webconfig['height'])->save('./' . $rndFileName);
                }
                if ($webconfig['iswater']) {
                    $image = \Image::open('./' . $rndFileName);
                    if ($webconfig['pwater'] == 0) {
                        $webconfig['pwater'] = rand(1, 9);
                    }
                    $image->water('./public/admin/water/water.png', $webconfig['pwater'])->save('./' . $rndFileName);
                }
            }
            $body = str_replace($value, __ROOT__ . '/' . $rndFileName, $body);
        }
        return $body;
    }
function formImage($field,$value=''){
    $str='<input type="file" id="'.$field.'">';
    if($value){
        $str .='<img src="'.$value.'" style="max-width:300px; max-height:100px" />';
        }
    $str .="<script type=\"text/javascript\">
            $(\"#".$field."\").uploadify({
                queueSizeLimit : 1,
                height          : 30,
                swf             : '__PUBLIC__/uploadify/uploadify.swf',
                fileObjName     : 'file',
                buttonText      : '上传图片',
                uploader        : '__ROOT__/api.php/upload/upimg.html',
                width           : 120,
                removeTimeout     : 1,
                fileTypeExts      : '*.jpg; *.png; *.gif;',
                fileSizeLimit   :2048,
                onUploadSuccess : uploadPicture,
                onFallback : function() {
                    alert('未检测到兼容版本的Flash.');
                }
            });
            function uploadPicture(file, data){
                var data = \$.parseJSON(data);
                if(data.status){            
                            var html = '<span>'+ '<img style=\"max-width:300px; max-height:100px;\" src=\"'+data.url+'\">' ;
                            html += '<a href=\"javascript:void(0)\" onclick=\"delete_attachment(this);\">&nbsp;&nbsp;删除</a>';
                            html += '<input type=\"hidden\" name=".$field." value=\"'+data.url+'\" /></span>';
                            \$('#".$field."').after(html);
                } else {
                    alert('上传出错，请稍后再试');
                    return false;
                }
            }
            </script>";
    
    
    return $str;
    }
function formImages($field,$value=''){
    
    $str='<input type="file" id="'.$field.'">';
    if($value){
        $value=string2array($value);
        foreach($value as $v){
        $str .='<span><img style="max-width:300px; max-height:100px;" src='.$v.' />' ;
        $str .='<a href="javascript:void(0)" onclick="delete_attachment(this);">&nbsp;删除&nbsp;</a>';
        $str .='<input type="hidden" name='.$field.'[] value='.$v.' /></span>';
        }
        }
    $str .="<script type=\"text/javascript\">
            $(\"#".$field."\").uploadify({
                queueSizeLimit : 20,
                height          : 30,
                swf             : '__PUBLIC__/uploadify/uploadify.swf',
                fileObjName     : 'file',
                buttonText      : '上传图片',
                uploader        : '__ROOT__/api.php/upload/upimg.html',
                width           : 120,
                removeTimeout     : 1,
                fileTypeExts      : '*.jpg; *.png; *.gif;',
                fileSizeLimit   :2048,
                onUploadSuccess : uploadPicture,
                onFallback : function() {
                    alert('未检测到兼容版本的Flash.');
                }
            });
            function uploadPicture(file, data){
                var data = \$.parseJSON(data);
                if(data.status){            
                            var html = '<span>'+ '<img style=\"max-width:300px; max-height:100px;\" src=\"'+data.url+'\" />' ;
                            html += '<a href=\"javascript:void(0)\" onclick=\"delete_attachment(this);\">&nbsp;删除&nbsp;</a>';
                            html += '<input type=\"hidden\" name=".$field."[] value=\"'+data.url+'\" /></span>';
                            \$('#".$field."').after(html);
                } else {
                    alert('上传出错，请稍后再试');
                    return false;
                }
            }
            </script>";
    
    
    return $str;
    }
function formEditor($field, $value='')
    {
       
        $str = '<script type="text/plain" id="' . $field . '"  style="width:805px;height:200px;">' . fiveHtmlentitydecode($value) .'</script>';
        $str .= "<script type=\"text/javascript\">\r\n";
        $str .= "var um = UM.getEditor(\"{$field}\",{";
        $str .= 'UEDITOR_HOME_URL: "__PUBLIC__/ueditor/",';
        $str .= 'imageUrl: "__ROOT__/api.php/upload/ueditor",';
        $str .= 'imagePath: "__ROOT__/",';
        $str .= "textarea: '{$field}' });";
        $str .= '</script>';
        return $str;
    }
  
function seo($title = '', $keywords = '', $description = '')
{
    if (!empty($title)) {
        $title = strip_tags($title);
    }
    if (!empty($description)) {
        $description = strip_tags($description);
    }
    if (!empty($keywords)) {
        $description = strip_tags($keywords);
    }
    $seo['keywords'] = $keywords;
    $seo['description'] = $description;
    $seo['title'] = $title;
    foreach ($seo as $k => $v) {
        $seo[$k] = str_replace(["\n", "\r"], '', $v);
    }
    return $seo;
}
function catidStr($catid)
{
    $list = db('category')->field('catid,pid')->select();
    $list = getSubs($list, $catid);
    $str = '';
    foreach ($list as $k1 => $v1) {
        $str .= $v1['catid'] . ',';
    }
    $str = substr($str, 0, -1);
    if ($str == '') {
        $str = $catid;
    } else {
        $str = $catid . ',' . $str;
    }
    return $str;
}
function getKeywords($keywords)
{
    $keywords = keywordToArray($keywords);
    $str = '';
    foreach ($keywords as $v) {
        $str .= '<a  href="' . url('Tag/index', ['tag' => urlencode($v)]) . '" >' . $v . '</a>&nbsp;';
    }
    return $str;
}
function getCatpos($catid, $symbol = ' > ')
{
    $list = db('category')->field('catid,pid,catname')->select();
    $list = getParents($list, $catid);
    $str = '';
    foreach ($list as $v) {
        $str .= '<a href=' . url('Category/lists', ['catid' => $v['catid']]) . '>' . $v['catname'] . '</a>' . $symbol;
    }
    $str = '<a href=' . __ROOT__ . '>首页</a>' . $symbol . $str;
    return $str;
}
/*栏目列表*/
function cateList($pid = 0, $num = 5)
{
    $list = db('category')->where('ishidden', 0)->where('pid', $pid)->limit($num)->select();
    return $list;
}
/*子栏目列表*/
function subCateList($catid, $num = 5)
{
    $list = db('category')->where('pid', $catid)->limit($num)->select();
    return $list;
}
/*指定栏目*/
function getCatname($catid)
{
    return db('category')->where('catid', $catid)->value('catname');
}

function getRadio($field,$value){
    if(!$value)return '';
    $r=db('field')->where('field',$field)->find();
    $defaultvalue=$r['defaultvalue'];
    $defaultvalue = explode("\n", $defaultvalue);
    if(!$value)$value=0;
    return $defaultvalue[$value];
}
function getSelect($field,$value){
    if(!$value)return '';
    $r=db('field')->where('field',$field)->find();
    $defaultvalue=$r['defaultvalue'];
    $defaultvalue = explode("\n", $defaultvalue);
    if(!$value)$value=0;
    return $defaultvalue[$value];
}
function getCheckbox($field,$value){
    if(!$value)return '';
    $value = strpos($value, ',') ? explode(',', $value) : array($value);
    $r=db('field')->where('field',$field)->find();
    $defaultvalue=$r['defaultvalue'];
    $defaultvalue = explode("\n", $defaultvalue);
    $str='';
    foreach ($defaultvalue as $k => $v) {
        if($value && in_array($k, $value)){
            $str .=$v;
           }
           }
    return $str;
}
