<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;
use Think\Auth;

class AuthController extends AdminBaseController {
    static protected $Auth;

    protected function _initialize(){
        parent::_initialize(); // TODO: Change the autogenerated stub
        self::$Auth = new Auth();
        if (session('Admin')) {
            $rule_name = MODULE_NAME . '/' . CONTROLLER_NAME . ',' . MODULE_NAME . '/' . CONTROLLER_NAME . '/' . ACTION_NAME;

//            dump($rule_name);
            $result = self::$Auth->check($rule_name, session('Admin.id'));
            if (!$result) {
                if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest') {
                    // ajax 请求的处理方式
                    show(0, '您没有权限操作');

                } else {
                    // 正常请求的处理方式
                    exit('您没有权限访问');
                };

            }

        } else {
            $this->error('登录信息已过期，请重新登录', U('/Login'));
        }
    }

}
