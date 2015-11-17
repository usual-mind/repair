<?php
return array(
	//'配置项'=>'配置值'
    //数据库配置信息
    'DB_TYPE'   => 'mysqli', // 数据库类型
    'DB_HOST'   => 'localhost', // 服务器地址
    'DB_NAME'   => 'repair', // 数据库名
    'DB_USER'   => 'root', // 用户名
    'DB_PWD'    => '', // 密码
    'DB_PORT'   => 3306, // 端口
    'DB_PREFIX' => '', // 数据库表前缀
    'DB_CHARSET'=> 'utf8', // 字符集
    'SECURE_CODE'=> 'e8net',//加密字串
    'COOKIE_PREFIX'=>'E8_',//cookie前缀

    //游客可以访问或禁止访问的一些操作列表
    'ACCESS'    =>  include ('access.php'),
    //notifyNode配置
    'NOTIFY'    => include ('notify.php'),
    'SHOW_PAGE_TRACE' =>true,
    //缩略图相关配置，根据横宽比生成缩略图，所以只要配置宽度
    'thumbnail' => array(
        //小图
        'smThumbnail' => array(
            'widht' => 200,
            'suffix' => '_sm'
        ),
        //中图
        'mdThumbnail' => array(
            'widht' => 300,
            'suffix' => '_md'
        ),
        //大图
        'lgThumbnail' => array(
            'widht' => 400,
            'suffix' => '_lg'
        )
    )
);