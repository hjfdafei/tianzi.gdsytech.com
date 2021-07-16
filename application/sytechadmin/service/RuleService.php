<?php
namespace app\sytechadmin\service;
use think\facade\Request;
use think\Db;
use think\db\Query;
use app\sytechadmin\model\Role;

class RuleService extends Base{
    //权限添加/修改
    public function rule_verify($ruleid=0){
        $parentid=input('post.parentid','0','intval');
        $rule_title=input('post.rule_title','','trim');
        $rule_module=input('post.rule_module','sytechadmin','trim');
        $rule_controller=input('post.rule_controller','','trim');
        $rule_action=input('post.rule_action','','trim');
        $rule_class=input('post.rule_class','','trim');
        $rule_sort=input('post.rule_sort','0','intval');
        $rule_ismenu=input('post.rule_ismenu','2','intval');
        $ruleid=intval($ruleid);
        if($rule_title==''){
            return jsondata('400','请输入权限名称');
        }
        if($rule_module==''){
            return jsondata('400','请输入模块名称');
        }
        if($rule_controller==''){
            return jsondata('400','请输入控制器名称');
        }
        if($rule_action==''){
            return jsondata('400','请输入方法名称');
        }
        if($parentid>0){
            $parentinfo=DB::name('rule')->where([['id','=',$parentid],['rule_isshow','=',1]])->find();
            if(empty($parentinfo)){
                return jsondata('400','选择的上级权限信息不存在');
            }
        }
        if($rule_controller!='#' && $rule_action!='#'){
            $hasrule=DB::name('rule')->where([['rule_controller','=',$rule_controller],['rule_action','=',$rule_action]])->find();
            if(!empty($hasrule)){
                if($hasrule['id']!=$ruleid){
                    return jsondata('400','权限方法已存在');
                }
            }
        }
        $hasrule2=DB::name('rule')->where([['rule_title','=',$rule_title]])->find();
        if(!empty($hasrule2)){
            if($hasrule2['id']!=$ruleid){
                return jsondata('400','权限名称已存在');
            }
        }
        $ruleinfo=array();
        if($ruleid>0){
            $ruleinfo=DB::name('rule')->where([['id','=',$ruleid]])->find();
        }
        $data['parentid']=$parentid;
        $data['rule_title']=$rule_title;
        $data['rule_module']=$rule_module;
        $data['rule_controller']=$rule_controller;
        $data['rule_action']=$rule_action;
        $data['rule_class']=$rule_class;
        $data['rule_sort']=$rule_sort;
        $data['rule_ismenu']=$rule_ismenu;
        if(empty($ruleinfo)){
            $data['create_time']=date('Y-m-d H:i:s');
            $res=DB::name('rule')->insert($data);
        }else{
            $data['update_time']=date('Y-m-d H:i:s');
            if($ruleid==$parentid){
                return jsondata('400','父级不能选择本身噢');
            }
            $res=DB::name('rule')->where([['id','=',$ruleinfo['id']]])->update($data);
        }
        if($res){
            return jsondata('200','数据保存成功');
        }else{
            return jsondata('400','数据保存失败,请重试');
        }
    }

    //权限删除
    public function rule_delete($ruleid){
        $delruleid=array();
        foreach($ruleid as $v){
            $ruleinfo=DB::name('rule')->where([['id','=',intval($v)]])->find();
            if(!empty($ruleinfo)){
                $delruleid[]=$ruleinfo['id'];
            }
        }
        if(empty($delruleid)){
            return jsondata('400','请选择要删除的权限');
        }
        Db::startTrans();
        $map=array();
        $num=0;
        foreach($delruleid as $v){
            $map['id']=intval($v);
            $res=DB::name('rule')->where($map)->delete();
            if($res){
                $num++;
            }
        }
        if($num>0){
            Db::commit();
            return jsondata('200','删除权限成功');
        }else{
            Db::rollback();
            return jsondata('400','删除权限失败,请重试');
        }
    }
}
