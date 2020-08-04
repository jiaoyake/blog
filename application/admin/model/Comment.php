<?php
/**
 * Created by PhpStorm.
 * User: 皮卡皮卡皮卡丘
 * Date: 2019/4/2
 * Time: 10:41
 */

namespace app\admin\model;
use think\Model;


class Comment extends Model
{
    protected $pk = 'cid';
    protected $table = 'blog_comment';
}