<?php

use think\Db;

//根据用户的主键id，查询用户名称
if (!function_exists('getUserName')) {
    function getUserName($id)
    {
        return Db::table('blog_users')->where('id', $id)->value('username');
    }
}

//根据用户的主键id，查询用户的头像
if (!function_exists('getUserImg')) {
    function getUserImg($id)
    {
        return Db::table('blog_users')->where('id', $id)->value('user_img');
    }
}

//根据日迹的主键did，查询日迹的图片
if (!function_exists('getDiaryImg')) {
    function getDiaryImg($did)
    {
        return Db::table('blog_dimg')->where('did', $did)->field('diary_img')->select();
    }
}

//根据类型的主键cid，查询类型名称
if (!function_exists('getCate')) {
    function getCate($id)
    {
        return Db::table('blog_cate')->where('cid', $id)->value('cate_name');
    }
}

//根据文章的主键aid，查询文章作者
if (!function_exists('getArticleName')) {
    function getArticleName($id)
    {
        return Db::table('blog_article')->where('aid', $id)->value('uid');
    }
}

//根据日迹的主键aid，查询日迹作者
if (!function_exists('getDiaryName')) {
    function getDiaryName($id)
    {
        return Db::table('blog_diary')->where('did', $id)->value('uid');
    }
}

//根据文章的主键aid，查询作者头像
if (!function_exists('getArticleUserImg')) {
    function getArticleUserImg($id)
    {
        $uid =  Db::table('blog_article')->where('aid', $id)->value('uid');
        return Db::table('blog_users')->where('id', $uid)->value('user_img');
    }
}
