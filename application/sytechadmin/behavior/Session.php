<?php
namespace app\sytechadmin\behavior;
use think\facade\Session as SessionService;

class Session{
    public function run(){
        SessionService::init([
            'prefix'     => 'sytechadmin',
            'type'       => '',
            'name'       => 'sy',
            'auto_start' => true,
        ]);
    }
}
