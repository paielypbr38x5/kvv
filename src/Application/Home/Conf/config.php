<?php
return array(
    
    //扩展配置文件
    'LOAD_EXT_CONFIG'=>'db,router',
    //
    'SALT'=>'中文加密',

    'WEB_PHONE'=>'18679824508',

    'ALLOW_ORIGIN'=>array(
        'http://localhost:9000',
        'http://192.168.1.123:9000',
    ),

    'AUTOLOAD_NAMESPACE' => array(
        'Lib'     => APP_PATH.'Lib',
    )

);