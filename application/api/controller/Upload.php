<?php
namespace app\api\controller;
use think\Controller;
use think\Config;
use think\Loader;
use think\Db;
use think\Session;
use think\db\Query;
use app\api\controller\Indexbase;

class Upload extends Indexbase{
    //上传文件
    public function file_upload(){
        $type=input("param.type");
        $img = request()->file('file');
        $info = $img->move(ROOT_PATH . 'public' . DS . 'upload'. DS . $type. DS . date('Y') . DS . date('m-d'),md5(microtime(true)));
        if($info){
            $imgPath = "/upload/$type/" . date('Y') . '/' . date('m-d') . '/' . $info->getSaveName();
            return json(["code"=> 1, "msg" => "上传成功", "url" => $_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].$imgPath]);
        }else{
            return json(["code"=> 0, "msg" => $img->getError(), "url" => '']);
        }
    }

    //上传文件 $file 文件域名称;$size 大小单位M $water是否打水印
    public function file_upload_param($type,$file,$size='2',$water=0){
        $img = request()->file($file);
        if(empty($img)){
            return ["code"=> 0, "msg" =>'请选择上传文件', "url" => ''];
        }
        $imginfo=$img->getInfo();
        if($imginfo['size']/1024/1024>$size){
            return ["code"=> 0, "msg" =>'上传文件过大,请重新上传', "url" => ''];
        }
        $info = $img->move(ROOT_PATH . 'public' . DS . 'upload'. DS . $type. DS . date('Y') . DS . date('m-d'),md5(microtime(true)));
        if($info){
            $imgPath = "/upload/$type/" . date('Y') . '/' . date('m-d') . '/' . $info->getSaveName();
            if($water==1){
                $filename='.'.$imgPath;
                $waterimage=\think\Image::open($filename);
                $waterimage->water('./static/waterimg/water.png',\think\Image::WATER_CENTER,30)->save($filename);
            }
            return ["code"=> 1, "msg" => "上传成功", "url" => $imgPath];
        }else{
            return ["code"=> 0, "msg" => $img->getError(), "url" => ''];
        }
    }

    //多文件
    public function mutifile_upload_param($type,$file,$size='2'){
        $img = request()->file($file);
        if(empty($img)){
            return ["code"=> 0, "msg" =>'请选择上传文件', "url" => ''];
        }
        $urls=[];
        foreach($img as $v){
            $imginfo=$v->getInfo();
            if($imginfo['size']/1024/1024>$size){
                return ["code"=> 0, "msg" =>'上传文件过大,请重新上传', "url" => ''];
            }
            $info = $v->move(ROOT_PATH . 'public' . DS . 'upload'. DS . $type. DS . date('Y') . DS . date('m-d'),md5(microtime(true)));
            if($info){
                $imgPath = "/upload/$type/" . date('Y') . '/' . date('m-d') . '/' . $info->getSaveName();
                $urls[]=$imgPath;

            }
        }
        if(empty($urls)){
            return ["code"=> 0, "msg" => $v->getError(), "url" => ''];
        }else{
            return ["code"=> 1, "msg" => "上传成功", "url" => $urls];
        }
    }

}
