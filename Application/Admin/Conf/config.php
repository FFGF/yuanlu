<?php
//mob配置
return array(
    
    /* 密码干扰字符 **********************************************/
    'PASSWORDINTER' => 'yuanlu',
    'PASSWORDADMIN' => 'admin@cloud',
    /* URL配置 **********************************************/
    'URL_CASE_INSENSITIVE' => true, //默认false 表示URL区分大小写 true则表示不区分大小写
    'URL_MODEL' => 2, //URL模式
    'VAR_URL_PARAMS' => '', // PATHINFO URL参数变量
    'URL_PATHINFO_DEPR' => '-', //PATHINFO URL分割符
    
    /* SESSION 和 COOKIE 配置 **********************************************/
    'SESSION_PREFIX' => 'channels_s_', //session前缀
    'COOKIE_PREFIX' => 'channels_c_', // Cookie前缀 避免冲突

    /* 模板相关配置 **********************************************/
    'TMPL_PARSE_STRING' => array(
        '__IMG__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Images',
        '__CSS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Css',
        '__JS__' => __ROOT__ . '/Public/' . MODULE_NAME . '/Js',
    ),
    
    /*邮箱配置**********************************************/
    'MAIL_HOST' =>'mail.grandcloud.cn',//smtp服务器的名称
    'MAIL_SMTPAUTH' =>TRUE, //启用smtp认证
    'MAIL_USERNAME' =>'support@grandcloud.cn',//你的邮箱名
    'MAIL_FROM' =>'support@grandcloud.cn',//发件人地址
    'MAIL_FROMNAME'=>'盛大云',//发件人姓名
    'MAIL_PASSWORD' =>'gckf@13975',//邮箱密码
    'MAIL_CHARSET' =>'utf-8',//设置邮件编码
    'MAIL_ISHTML' =>TRUE, // 是否HTML格式邮件
);

