<?php
namespace app\admin\model;
use think\Model;

class Cate extends Model
{
    protected  $pk = 'cid';//默认主键
    protected $table = 'blog_cate';
}