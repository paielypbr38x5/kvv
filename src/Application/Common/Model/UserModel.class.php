<?php

/**
 * Created by IntelliJ IDEA.
 * User: Neo
 * Date: 2016/11/8
 * Time: 9:34
 */
namespace Common\Model;

use Think\Model;

class UserModel extends Model
{

    protected  $_validate = array(
        array('game_user', '/^[0-9]*$/', '游戏ID格式不正确',self::MUST_VALIDATE), //
        array('gid', '/^[0-9]*$/', '游戏名称不正确',self::MUST_VALIDATE), //
    );

    protected $_auto = array(
//             array(完成字段1,完成规则,[完成条件,附加规则]),

        array('mask','maskFactory',self::MODEL_INSERT,'callback'),
        array('status',1)
    );

    public function maskFactory(){
        do{
            $mask=strtoupper(rand_str(6,'all'));
        }
        while ($this->where("mask='%s'",$mask)->find());
        return $mask;
    }



}