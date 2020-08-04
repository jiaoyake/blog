<?php
namespace app\admin\model;
use think\Model;

class Article extends Model
{
    protected  $pk = 'aid';//默认主键
    protected $table = 'blog_article';
}