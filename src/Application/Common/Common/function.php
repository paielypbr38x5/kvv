<?php
/**
 * Created by IntelliJ IDEA.
 * User: Neo
 * Date: 2017/2/18
 * Time: 10:32
 *
 */
/**
 * @param $status
 * @param $info
 * @param array $data
 */
function show($status, $info, $data = array())
{
    header('Content-type: application/json');

    $result = array(
        'status' => $status,
        'info' => $info,
        'data' => $data,
    );
    exit(json_encode($result));
}

function xmd5($string)
{
    if (empty($string))
        return false;
    return md5($string . C('SALT'));
}

//验证图形验证码
function check_img_verify($code, $id = '')
{
    if (APP_DEBUG) {
        if ($code == '123456')
            return true;
    }
    $verify = new \Think\Verify();
    return $verify->check($code, $id);
}//验证图形验证码
function check_phone_verify($code, $id = '')
{
    if ($code && $code == session('Verify' . $id . '.Verify')) {
        return true;
    } else {
        return false;
    }
}
function check_self_phone($phone, $id='')
{
    if ($phone && $phone == session('Verify' . $id . '.Phone')) {
        return true;
    } else {
        return false;
    }
}

function send_sms($phone, $message, $who)
{
    $Sms = new \Think\SMS();
    $Sms->phone = $phone;
    $Sms->send($phone, $message, $who);
    $code = $Sms->getCode();
    if ($code > 0) {
        show(1, '短信发送成功，请注意查看');
    } else {
        show(0, $Sms->getResult());
    }
}

function rand_str($length = 6, $type = 'number', $convert = 0)
{
    $config = array(
        'number' => '1234567890',
        'letter' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ',
        'string' => 'abcdefghjkmnpqrstuvwxyzABCDEFGHJKMNPQRSTUVWXYZ23456789',
        'all' => 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890'
    );

    if (!isset($config[$type])) $type = 'string';
    $string = $config[$type];

    $code = '';
    $strlen = strlen($string) - 1;
    for ($i = 0; $i < $length; $i++) {
        $code .= $string{mt_rand(0, $strlen)};
    }
    if (!empty($convert)) {
        $code = ($convert > 0) ? strtoupper($code) : strtolower($code);
    }
    return $code;
}
