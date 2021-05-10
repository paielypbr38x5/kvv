<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK IT ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006-2014 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------
namespace Think;
/**
 * ThinkPHP 应用程序类 执行应用过程管理
 */
class SMS
{

    /**
     *
     * CREATE TABLE IF NOT EXISTS `sms` (
     * `id` int(11) NOT NULL AUTO_INCREMENT,
     * `phone` varchar(11) COLLATE utf8_bin NOT NULL,
     * `message` varchar(140) COLLATE utf8_bin NOT NULL,
     * `who` varchar(100) COLLATE utf8_bin NOT NULL,
     * `ip` varchar(16) COLLATE utf8_bin NOT NULL,
     * `time` int(11) NOT NULL,
     * PRIMARY KEY (`id`)
     * ) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_bin AUTO_INCREMENT=1 ;
     **/

    const PHONE_ERROR = -1000;
    private $code;
    protected $config = array(
        'accessId' => 'CDJS000466',   // 验证码加密密钥
        'accessKey' => 'xiaowuguiduanxin@',
        'tag' => '【Kvv科技】',
        'allowCount' => 5,              //允许每天发送次数
        'allowTime' => 60,             //允许发送间隔时间
        'allow' => false,
    );


    /**
     * 架构方法 设置参数
     * @access public
     * @param  array $config 配置参数
     */
    public function __construct($config = array())
    {
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 使用 $this->name 获取配置
     * @access public
     * @param  string $name 配置名称
     * @return multitype    配置值
     */
    public function __get($name)
    {
        return $this->config[$name];
    }

    /**
     * 设置验证码配置
     * @access public
     * @param  string $name 配置名称
     * @param  string $value 配置值
     * @return void
     */
    public function __set($name, $value)
    {
        if (isset($this->config[$name])) {
            $this->config[$name] = $value;
        }
    }

    /**
     * 检查配置
     * @access public
     * @param  string $name 配置名称
     * @return multitype
     */
    public function __isset($name)
    {
        return isset($this->config[$name]);
    }

    public function send($phone, $message, $who)
    {
        if ($this->isPhone($phone)) {
            session('phone', $phone);
        } else {
            $this->code = self::PHONE_ERROR;
        }

        $Sms = M('Sms');
        $ip = get_client_ip();
        $prevtime = $Sms->where("phone='%s'", $phone)->order('time desc')->getField('time');
        $today_count['phone'] = $Sms->where("phone='%s' and time>%d", $phone, time() - 86400)->count();
        $today_count['ip'] = $Sms->where("ip='%s' and time>%d", $ip, time() - 86400)->count();


        if ($this->allow === false) {
//    限制每天最多发送允许的短信次数
            if ($today_count['phone'] > $this->allowCount || $today_count['ip'] > $this->allowCount) {
                $this->code = -1001;
                return false;
            }

//    限制两次短信的间隔时间
            if ($prevtime && $prevtime + $this->allowTime > time()) {
                $this->code = -1002;
                return false;
            }
        }
        header("Content-type: text/html; charset=utf-8");
        date_default_timezone_set('PRC'); //设置默认时区为北京时间
        //短信接口用户名 $uid

        $msg = rawurlencode(mb_convert_encoding($message . $this->tag, "gb2312", "utf-8"));


        $gateway = "http://sdk2.028lk.com:9880/sdk2/BatchSend2.aspx?CorpID={$this->accessId}&Pwd={$this->accessKey}&Mobile={$phone}&Content={$msg}&Cell=&SendTime=";


        $this->code = file_get_contents($gateway);

        if ($this->code > 0) {
            $Sms->add(Array(
                'phone' => $phone,
                'message' => $message,
                'who' => $who,
                'ip' => $ip,
                'time' => time()
            ));

            $gateway = "http://sdk2.028lk.com:9880/sdk2/SelSum.aspx?CorpID={$this->accessId}&Pwd={$this->accessKey}";
            $result = file_get_contents($gateway);
            if ($result == 50 || $result == 150 || $result == 300 || $result == 500) {
                $this->send(C('WEB_PHONE'), "警告，短信余额不足" . $result . "条，请注意！！！", __METHOD__, 0, 0);
            }

            return true;

        } else {
            return false;
        }

    }

    public function getCode()
    {
        return $this->code;
    }

    public function getResult()
    {
        /**
         *      大于0的数字    提交成功
         *      –1    账号未注册
         *      –2    其他错误
         *      –3    帐号或密码错误
         *      –5    余额不足，请充值
         *      –6    定时发送时间不是有效的时间格式
         *      -7    提交信息末尾未签名，请添加中文的企业签名【 】
         *      –8    发送内容需在1到300字之间
         *      -9    发送号码为空
         *      -10    定时时间不能小于系统当前时间
         *      -100    IP黑名单
         *      -102    账号黑名单
         *      -103    IP未导白
         *      -1000   手机号码错误
         *      -1001   短信已超过系统限制
         *      -1002   发送频繁
         **/
        if ($this->code > 0) {
            $result = '发送成功';
        } else {
            switch ($this->code) {
                case -1:
                    $result = '账号未注册';
                    break;
                case -2:
                    $result = '其他错误';
                    break;
                case -3:
                    $result = '帐号或密码错误';
                    break;
                case -5:
                    $result = '余额不足，请充值';
                    break;
                case -6:
                    $result = '定时发送时间不是有效的时间格式';
                    break;
                case -7:
                    $result = '提交信息末尾未签名，请添加中文的企业签名【 】';
                    break;
                case -8:
                    $result = '发送内容需在1到300字之间';
                    break;
                case -9:
                    $result = '发送号码为空';
                    break;
                case -1000:
                    $result = '手机号码错误';
                    break;
                case -1001:
                    $result = '本手机号或本IP今日发送的短信已超过系统限制，请明天再来';
                    break;
                case -1002:
                    $result = '短信发送过于频繁，请稍后再试';
                    break;
                case false:
                    $result = '发送超时';
                    break;
                default:
                    $result = '未知错误';
                    break;
            }
        }
        return $result;
    }


    private function isPhone($phone)
    {
        return preg_match('/^((00|\+)?(86(?:-| )))?((\d{11})|(\d{3}[- ]{1}\d{4}[- ]{1}\d{4})|((\d{2,4}[- ]){1}(\d{7,8}|(\d{3,4}[- ]{1}\d{4}))([- ]{1}\d{1,4})?))$/', $phone) ? true : false;
    }


}
