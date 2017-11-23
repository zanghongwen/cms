<?php
namespace app\api\controller;

use think\Controller;
class Upload extends Controller
{
    public $webconfig;

    protected function _initialize()
    {
        $this->webconfig = db('system')->find(1);
    }
    public function upimg()
    {
        $file = request()->file('file');
        $info = $file->move('./uploads/');
        if (!empty($info)) {
            $filepath = 'uploads/' . pathConvert($info->getSaveName());
            $this->check($filepath);
            $data['url'] = __ROOT__ . '/uploads/' . pathConvert($info->getSaveName());
            $data['status'] = 1;
        } else {
            $data['status'] = 0;
        }
        return json($data);
    }
    public function ueditor()
    {
        $config = ["savePath" => "uploads/", "maxSize" => 1024, "allowFiles" => [".gif", ".png", ".jpg", ".jpeg", ".bmp"]];
        $up = new \Uploader("upfile", $config);
        $info = $up->getFileInfo();
        $this->check($info['url']);
        echo json_encode($info);
    }
    private function check($url)
    {
        if ($this->webconfig['isthumb']) {
            $image = \Image::open('./' . $url);
            $image->thumb($this->webconfig['width'], $this->webconfig['height'])->save('./' . $url);
        }
        if ($this->webconfig['iswater']) {
            $image = \Image::open('./' . $url);
            if ($this->webconfig['pwater'] == 0) {
                $this->webconfig['pwater'] = rand(1, 9);
            }
            $image->water('./public/admin/water/water.png', $this->webconfig['pwater'])->save('./' . $url);
        }
    }
}