<?php
namespace app\sytechadmin\controller;
use think\Controller;
use think\Db;
use think\db\Query;
use think\Request;
use app\sytechadmin\controller\Sytechadminbase;
use app\sytechadmin\model\Rule as RuleModel;
use app\sytechadmin\service\RuleService;

class Rule extends Sytechadminbase{
    //权限列表
    public function rule_list(){
        $rule_model=new RuleModel();
        $list=$rule_model->getRule();
        $this->assign('list', $list);
        return $this->fetch();
    }

    //添加权限
    public function rule_add(){
        if(request()->isPost() || request()->isAjax()){
            $ruleservice=new RuleService();
            return $ruleservice->rule_verify(0);
        }
        $rule_model=new RuleModel();
        $parentlist=$rule_model->getParentList();
        $this->assign('parentlist',$parentlist);
        return $this->fetch();
    }

    //修改权限
    public function rule_edit(){
        if(request()->isPost() || request()->isAjax()){
            $ruleid=input('post.ruleid','0','intval');
            $ruleservice=new RuleService();
            return $ruleservice->rule_verify($ruleid);
        }
        $ruleid=input('ruleid','0','intval');
        if($ruleid<=0){
            return jsondata('400','请选择要编辑的权限');
        }
        $map[]=['id','=',$ruleid];
        $info=DB::name('rule')->where($map)->find();
        if(empty($info)){
            return jsondata('400','选择编辑的权限信息不存在');
        }
        $this->assign('info',$info);
        $rule_model=new RuleModel();
        $parentlist=$rule_model->getParentList();
        $this->assign('parentlist',$parentlist);
        return $this->fetch();
    }

    //删除权限
    public function rule_del(){
        set_time_limit(0);
        if(request()->isPost() || request()->isAjax()){
            $ruleid=input('post.ruleid','','trim');
            if($ruleid==''){
                return jsondata('400','请选择要删除的权限');
            }
            $ruleid=explode(',',trim($ruleid,','));
            if(empty($ruleid)){
                return jsondata('400','请选择要删除的权限');
            }
            $ruleservice=new RuleService();
            return $ruleservice->rule_delete($ruleid);
        }
        return jsondata('400','网络错误');
    }

}
