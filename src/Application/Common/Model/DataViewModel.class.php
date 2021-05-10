<?php

/**
 * Created by IntelliJ IDEA.
 * User: Neo
 * Date: 2016/11/8
 * Time: 9:34
 */
namespace Common\Model;

use Think\Model\ViewModel;

class DataViewModel extends ViewModel
{
    public $viewFields = array(
        'Data'=>array('id','uid','date','sell','sell_count','recycle','recycle_count'),
        'User'=>array('id'=>'uid','game_user','mask','status','time', '_on'=>'User.id=Data.uid'),
        'Game'=>array('name'=>'game', '_on'=>'User.gid=Game.id'),
    );


}