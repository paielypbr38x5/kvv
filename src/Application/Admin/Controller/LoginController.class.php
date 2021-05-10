<?php
namespace Admin\Controller;
use Common\Controller\AdminBaseController;

class LoginController extends AdminBaseController {
    public function index(){
        $this->display();
    }

    public function login(){
        if (check_img_verify(I("post.verifyImg"))){
            $username=I("post.username");
            $password=xmd5(I("post.password"));
            $User=M('Admin')->where("username='%s' and password='%s'",$username,$password)->find();
            if ($User){
                if ($User['status']==0){
                    show(0,'该帐号已被禁用');
                }else{
                    session('Admin',$User);
                    session('Admin.login_time',time());
                    show(1,'登陆成功',$User);
                }
            }else{
                show(0,'用户名或密码不正确');
            }
        }else{
            show(0,'请填写正确的验证码！！！');
        }
    }

    //生成图形验证码
    public function verifyImg()
    {
        ob_clean();
        $Verify = new \Think\Verify();
        $Verify->entry();
    }

    public function logout(){
        session('admin',null);
        $this->success('退出成功', U('/Login'));
    }
}