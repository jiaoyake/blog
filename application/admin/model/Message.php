<?php
/**
 * Created by PhpStorm.
 * User: 皮卡皮卡皮卡丘
 * Date: 2019/4/1
 * Time: 17:17
 */

namespace app\admin\model;
use think\Model;


class message extends Model
{
    protected  $pk = 'mid';//默认主键
    protected $table = 'blog_message';
}