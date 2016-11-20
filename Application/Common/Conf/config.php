<?php

/**
 * 系统配文件
 * 所有系统级别的配置
 */
return array(
    // 加载扩展配置文件
    'LOAD_EXT_CONFIG' => 'db',

    /* 调试配置 */
    'SHOW_PAGE_TRACE' => false,
    'PAGESIZE' => 500, //每页显示数量
     
    /* 模块相关配置 */
    'DEFAULT_MODULE' => 'Admin',
    'MODULE_DENY_LIST' =>  array('Common','Runtime'),	// 禁止访问的模块列表
    'MODULE_ALLOW_LIST' => array('Admin', 'Home'),
    
    /* 数据缓存设置 */
    'DATA_CACHE_PREFIX' => 'yuanlu', // 缓存前缀
    'DATA_CACHE_TYPE' => 'File', // 数据缓存类型:Memcache\File\CacheMemcached
    'DATA_CACHE_TIME'=>300,// 数据缓存有效期 0表示永久缓存

   
    /* SESSION 和 COOKIE 配置 */
    'SESSION_AUTO_START' => true, // 是否自动开启Session
//    'SESSION_OPTIONS' => array('domain' => '.grandcloud.cn'),
    'COOKIE_DOMAIN' => '.grandcloud.cn', // Cookie有效域名
    'COOKIE_PATH' => '/', // Cookie路径
    'COOKIE_PRE' => 'gszIG_', //Cookie 前缀，同一域名下安装多套系统时，请修改Cookie前缀
    'COOKIE_TTL' => 24*60*60*14, //Cookie 生命周期，0 表示随浏览器进程
    'AUTH_KEY' => 'As9C9vMs9NksljutUuJM72mdEdijd', //密钥

    /* 运行URL */
    'MY_WEBSITE' => 'http://channels.grandcloud.cn',
    
    /* 密码干扰字符 **********************************************/
    'PASSWORDINTER' => 'yuanlu',
);

