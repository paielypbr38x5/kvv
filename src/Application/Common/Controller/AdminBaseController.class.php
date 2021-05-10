<?php
namespace Common\Controller;
use Think\Controller;
class AdminBaseController extends Controller {
    protected static $User;
    protected function _initialize()
    {
        $origin = isset($_SERVER['HTTP_ORIGIN'])? $_SERVER['HTTP_ORIGIN'] : '';
        if(in_array($origin, C('ALLOW_ORIGIN'))){
            header('Access-Control-Allow-Origin:'.$origin);
            header('Access-Control-Allow-Credentials:true');
        }
//        self::$User=D('Admin');
        
        
        if(APP_DEBUG){
//            session('User.UserID',1);
//            session('phone','123456');
//            session('admin.id','1');
        }
    }
}