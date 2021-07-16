<?php
namespace app\sytechadmin\model;
use think\Model;
class Rule extends Model{
    public function getRule($map=[]){
        $tree=[];
        $info=$this->where($map)->order('id', 'asc')->select()->toArray();
        foreach ($info as $k=>$item) {
            if($item['parentid']==0) {
                $tree[$item['id']]=$item;
                unset($info[$k]);
            }
        }
        foreach($tree as $key=>$item2){
            foreach($info as $k=>$item){
                if ($item2['id']==$item['parentid']){
                    $tree[$key]['son'][$item['id']]=$item;
                    unset($info[$k]);
                }
            }
        }
        foreach($tree as &$item){
            if(!isset($item['son'])){continue;}
            foreach($item['son'] as $k2=>$item2) {
                foreach($info as $key=>$val) {
                    if($item2['id'] ==$val['parentid']){
                        $item['son'][$k2]['son'][$val['id']]=$val;
                        unset($info[$key]);
                    }
                }
            }
        }
        return $tree;
    }

    //获取父级权限
    public function getParentList(){
        $map=array();
        $map[]=['parentid','=',0];
        $map[]=['rule_isshow','=',1];
        $parentlist=$this->where($map)->order('rule_sort desc')->select()->toArray();
        return $parentlist;
    }

    /**
     * 获取导航菜单
     * @return [type] [description]
     */
    public function getMenu()
    {

        $permission_list = session('permission_list');

        $menu_tree = $this->field('id,rule_desc')->where('parent_id', 0)->order('sort', 'asc')->select()->toArray();

        foreach ($menu_tree as $key => $item) {
            $temp = $this->field('id,rule_desc,rule_url')->where('parent_id', $item['id'])->order('sort', 'asc')->select()->toArray();
            foreach ($temp as $k => $item2) {
                if (!in_array($item2['id'], $permission_list)) {
                    unset($temp[$k]);
                }
            }

            if (!empty($temp)) {
                $menu_tree[$key]['son'] = $temp;
            } else {
                unset($menu_tree[$key]);
            }
        }

        return $menu_tree;
    }

    /**
     * 通过url规则获取规则id
     * @param  string $url 规则url
     * @return [type]      [description]
     */
    public function getRuleIdByUrl($url)
    {
        return $this->field('id')->where('rule_url', $url)->value('id');
    }

}
