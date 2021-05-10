<?php

/**
 * Created by IntelliJ IDEA.
 * User: Neo
 * Date: 2016/11/8
 * Time: 9:34
 */
namespace Common\Model;

use Think\Model;

class DataModel extends Model
{

    protected  $_validate = array(

        array('uid', 'checkUID', '游戏ID不存在',self::MUST_VALIDATE,'callback'), //
        array('date', '/^\d{4}(\-|\/|.)\d{1,2}\1\d{1,2}$/', '日期格式不正确',self::MUST_VALIDATE), // 自定义函数验证密码格式
        array('sell', '/^[0-9]*$/', '出售数量格式不正确！',self::MUST_VALIDATE), //
        array('sell_count','/^[0-9]*$/','出售数量格式不正确！',self::MUST_VALIDATE), //
        array('recycle', '/^[0-9]*$/', '出售数量格式不正确',self::MUST_VALIDATE), // 自定义函数验证手机号格式
        array('recycle_count', '/^[0-9]*$/', '出售数量格式不正确！',self::MUST_VALIDATE), // 新增或编辑时必须判断账号是否唯一

    );

    public function checkUID($uid){
        if (M('User')->find($uid)){
            return true;
        }else{
            return false;
        }
    }
}