<?php

/**
 * Created by IntelliJ IDEA.
 * User: Neo
 * Date: 2016/11/8
 * Time: 9:34
 */
namespace Admin\Model;

use Think\Model;

class AdminModel extends Model
{
    protected $connection =  'mysql://root:root@localhost:3306/ssly_admin';

    protected  $_validate = array(

        array('username', '/^[a-zA-Z]{1}([a-zA-Z0-9_]){3,15}$/', '用户名必须为以英文字母开头，4到20位的数字与字母的组合'), //
        array('password', '/^(?=.*?[a-zA-Z])(?=.*?[0-9])[a-zA-Z0-9]{6,20}$/', '用户密码必须为6到20位的数字与字母的组合',0,'regex',self::MODEL_INSERT), // 自定义函数验证密码格式
        array('email', 'email', '邮箱格式不正确！'), //
        array('qq','/^[1-9]\d{4,10}$/','QQ号填写不正确！'), //
        array('phone', '/^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$/', '这不是一个手机号'), // 自定义函数验证手机号格式
        array('username', '', '帐号名称已经存在！', 0, 'unique'), // 新增或编辑时必须判断账号是否唯一


    );

    protected $_auto = array(
        array('status', '1'),  // 新增的时候把status字段设置为1 启用
        array('password', 'xmd5', 3, 'function'), // 对password字段在新增和编辑的时候使md5函数处理
        array('create_time', 'time', 1, 'function'), // 对signup_time字段在新增的时候写入当前时间戳
    );

    public function addGroup($uid, $groups = array())
    {
        M(C('AUTH_CONFIG.AUTH_GROUP_ACCESS'),'')->where('uid=%d', $uid)->delete();
        $dataList = array();
        foreach ($groups as $gid) {
            $dataList[] = array(
                'uid' => $uid,
                'group_id' => $gid
            );
        }
        M(C('AUTH_CONFIG.AUTH_GROUP_ACCESS'),'')->addAll($dataList);
    }

}