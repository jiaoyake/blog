<?php
namespace app\admin\model;

use think\Model;

class Diary extends Model
{
    protected  $pk = 'did';//默认主键
    protected  $table = 'blog_diary';

    public function Dimg()
    {
        return $this->belongsTo('Dimg', 'did', 'did')->bind([
            'diary_img' => 'diary_img',
        ]);
    }
}
