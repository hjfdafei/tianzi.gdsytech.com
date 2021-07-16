<?php
namespace app\sytechadmin\behavior;
use app\sytechadmin\model\AdminActionLog;
use think\Request;
class Log{
    public function run(Request $request, $params){
        if (!session('admininfo.id')) {
            return true;
        }
        $no_log=config('app.notlog_controller'); //不需要记录日志的控制器，填在这里。
        if(in_array($request->controller(), $no_log)) {
            return true;
        }
        //记录操作日志
        $action_log = new AdminActionLog();
        $action_log->method     = $request->method(); //请求类型
        $action_log->params     = serialize(['get' => $request->get(), 'post' => $request->post()]);
        $action_log->url        = $request->pathinfo();
        $action_log->ip         = $request->ip();
        $action_log->admin_id   = session('admininfo.id');
        $action_log->create_time = date('Y-m-d H:i:s');
        $action_log->save();
    }
}
