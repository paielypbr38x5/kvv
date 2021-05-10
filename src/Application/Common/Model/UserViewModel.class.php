<?php

/**
 * Created by IntelliJ IDEA.
 * User: Neo
 * Date: 2016/11/8
 * Time: 9:34
 */
namespace Common\Model;

use Think\Model;
use Think\Model\ViewModel;

class UserViewModel extends ViewModel
{
    public $viewFields = array(
        'User'=>array('id','game_user','mask','status','time'),
        'Game'=>array('name'=>'game', '_on'=>'User.gid=Game.id'),
    );

}