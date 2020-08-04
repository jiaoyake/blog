<?php
namespace app\admin\model;
use think\Model;

class User extends Model
{
    protected  $pk   = 'id';//默认主键
    protected $table = 'blog_users';
}
