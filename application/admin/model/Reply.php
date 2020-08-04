<?php
/**
 * Created by PhpStorm.
 * User: JYK
 * Date: 2019/8/8
 * Time: 16:26
 */

namespace app\admin\model;

use think\Model;


class Reply extends Model
{
    protected  $pk = 'rid';
    protected  $table = 'blog_reply';
}