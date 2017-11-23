<?php
function templateList($style)
{
    $list = glob(ROOT_PATH . 'template' . DIRECTORY_SEPARATOR . $style . DIRECTORY_SEPARATOR . '*', GLOB_ONLYDIR);
    $arr = [];
    foreach ($list as $key => $v) {
        $dirname = basename($v);
        if (file_exists($v . DIRECTORY_SEPARATOR . 'config.php')) {
            $arr[$key] = (include $v . DIRECTORY_SEPARATOR . 'config.php');
        } else {
            $arr[$key]['name'] = $dirname;
        }
        $arr[$key]['dirname'] = $dirname;
    }
    return $arr;
}