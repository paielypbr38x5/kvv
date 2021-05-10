<?php
namespace Home\Controller;
use Think\Controller\RestController;

/**
 * Created by IntelliJ IDEA.
 * User: Neo
 * Date: 2017/3/23
 * Time: 10:32
 */
class IndexController extends RestController
{

    public function index(){
        $this->display();
    }
}