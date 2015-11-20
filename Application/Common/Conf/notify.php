<?php
/**Notify节点配置
 * Created by PhpStorm.
 * User: TAOYU
 * Date: 2015/11/17
 * Time: 16:02
 */
return array(
    //'Node名称'=>'格式化类容'
    'NOTIFY_RECEIVED_EVALUATION_TITLE' => '【{site}】 您收到一个新的评价',
    'NOTIFY_RECEIVED_EVALUATION_CONTENT'
        => '{name}评价了您。快去<a href="{detailurl}" target="_blank">看看</a>吧。',
    'NOTIFY_NEW_REPAIR_RECORD_TITLE'=>'【{site}】又来一台电脑需要维修',
    'NOTIFY_NEW_REPAIR_RECORD_CONTENT'=>'以下电脑正等待您的维修：{computer};<a target="_blank" href="{detailurl}">点击查看</a>',
    'DIGG_MESSAGE_TITLE'=>'{user} 赞了我',
    'DIGG_MESSAGE_CONTENT'=>'{content} <a href="{sourceurl}&digg=1" target="_blank">去看看>></a>',

);
