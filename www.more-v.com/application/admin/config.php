<?php
return [

    'menu' => [
        //用户管理
        '管理员' => [
            '管理员列表' => 'admin/admin_list',
            '权限码列表' => 'admin/rule_list',
            '角色列表' => 'admin/group_list',
//            '系统邮件' =>'admin/system_email_list',

        ],

        'CRM'=>[
            '供应商列表'=>'seller/seller_list',
            '品牌列表'=>'brand/brand_list',
            '商品列表'=>'goods/goods_list',
            '分类管理'=>'goods/category_list',
            '枚举列表'=>'goods/enumerate_list',
            '贸管客户列表'=>'seller/client_list',
            '报名管理'=>'seller/baoming_list'
        ],
        '样品管理'=>[

            '库存列表'=>'sample/storage_list',
            '入库记录'=>'sample/warehousing_record',
            '出库记录'=>'sample/storage_out_list'

        ],
        '邮件管理'=>[
            '写信'=>'email/write_email',
            '收件箱'=>'email/inbox_list',
            '已发送'=>'email/email_sent'


        ],

        '会员管理' => [
            '会员列表' => 'user/user_list',
            '会员素材' => 'user/material_list',
//            '权限码列表' => 'admin/rule_list',
//            '角色列表' => 'admin/group_list'
        ],
        "信息管理" => [
            '信息列表' => 'message/message_list'


        ],
        '活动管理'=>[
            '活动列表'=>'activitys/activity_list'

        ],
        '新闻管理'=>[

            '新闻列表'=>'news/news_list'
        ],
        '需求管理'=>[
            '需求列表'=>'sell/sell_list'
        ]


    ],



];

