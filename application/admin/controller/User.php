<?php

namespace app\admin\controller;
use app\admin\model\User as UserModel;
use app\admin\model\Article as ArticleModel;
use app\admin\model\Diary as DiaryModel;
use app\admin\model\Img as ImgModel;
use app\admin\model\Qimg as QimgModel;
use app\admin\model\Cate as CateModel;
use app\admin\model\Message as MesModel;
use app\admin\model\Comment as CommentModel;
use app\admin\model\Reply as ReplyModel;
use think\Controller;
use think\facade\Request;
use think\facade\Session;
class User extends Controller
{
    //登录功能
    public function addLogin()
    {
        $data = [
            'username'  => Request::post('username', '', 'trim' ),
            'password'  => Request::post('password', '', 'trim')
            ];//要验证的数据
        $rule = [
            'username|用户名'    => 'require|chsAlphaNum',
            'password|密码'      => 'require|alphaNum|length:6,20',
        ];//自定义的验证规则
        $res = $this->validate($data, $rule);//开始验证
        if ($res === true) {
            $user = UserModel::where('username', $data['username'])->find();//查询
            if ($user) {
                if ($user['password'] == md5($data['password'])) {
                    Session::set('id', $user['id']);
                    Session::set('username', $user['username']);
                    Session::set('mobile', $user['mobile']);
                    Session::set('email', $user['email']);
                    Session::set('user_img', $user['user_img']);
                    $this->success('登录成功', 'Index/index');
                } else {
                    $this->error('密码错误，登录失败!');
                }
            } else {
                $this->error('没有该用户!');
            }
        } else {
            $this->error($res);
        }
    }

    //注销功能
    public function logout()
    {
        if (Session::get('id')) {
            Session::clear();
            $this->error('注销成功!', 'index/Index/index');
        } else {
            $this->error('你还没有登录，请登录!');
        }
    }

    //修改个人信息
    public function personInformation()
    {
        $id = Session::get('id');//获取当前登陆者的id
        if (request()->isPost()) {
            $data = input('post.');//获取修改信息时表单中的数据
            $data['id'] = $id;
        }
        $add = new UserModel();
        // 获取表单上传文件
        $file = request()->file('user_img');
        if ($file != NULL) {
            // 移动到uploads目录下
            $info = $file->move( '../public/uploads');
            if ($info) {
                // 成功上传后 获取上传信息
                $user_img=$info->getSaveName();
                $data['user_img']=$user_img;
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        $rule = [
            'username|昵称'       => 'require|unique:blog_users',
            'email|邮箱'          => 'require|email|unique:blog_users',
            'password|密码'       => 'require|alphaNum|length:6,20',
            'mobile|手机号'        => 'require|length:11',
        ];
        $res = $this->validate($data, $rule);
        if ($res === true) {
            $data['password']       = md5($data['password']);
            $data['update_time']    = date('Y-m-d H:i:s', time());
            if ($add->update($data)) {
                $this->success('修改信息成功!',url('Index/person'));
            } else {
                $this->error('修改信息失败!');
            }
            return $this->fetch();
        } else {
            $this->error($res);
        }
    }

    //添加管理员
    public function add(){
        $data = [
            'username'    =>   Request::post('username', '', 'trim'),
            'password'    =>   Request::post('password', '', 'trim'),
            'mobile'      =>   Request::post('mobile', '', 'trim'),
            'email'       =>   Request::post('email', '', 'trim'),
        ];
        $data['create_time'] = date('Y-m-d H:i:s', time());
        // 获取表单上传文件
        $file = request()->file('user_img');
        if ($file != NULL) {
            // 移动到uploads目录下
            $info = $file->move( '../public/uploads');
            if ($info) {
                // 成功上传后 获取上传信息
                $user_img=$info->getSaveName();
                $data['user_img']=$user_img;
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        $rule = [
            'username|昵称'        =>     'require|unique:blog_users',
            'password|密码'        =>     'require|alphaNum|length:6,20',
            'mobile|手机号'         =>     'require|length:11|unique:blog_users',
            'email|邮箱'           =>     'require|email|unique:blog_users',
        ];
        $res = $this->validate($data,$rule);
        if ($res === true) {
            $data['password'] = md5($data['password']);
            if (UserModel::create($data)) {
                $this->success('添加管理员成功!',url('Index/personmessage'));
            } else {
                $this->error('添加管理员失败!');
            }
        } else {
            $this->error($res);
        }
    }

    //编辑管理员
    public function edit(){
        $data = [
            'id'              =>      Request::param('id', '', 'intval'),
            'username'        =>      Request::post('username', '', 'trim'),
            'password'        =>      Request::post('password', '', 'trim'),
            'mobile'          =>      Request::post('mobile', '', 'trim'),
            'email'           =>      Request::post('email', '', 'trim'),
        ];
        $rule = [
            'username|用户名'      =>      'require|unique:blog_users',
            'email|邮箱'           =>     'require|email|unique:blog_users',
            'password|密码'        =>     'require|alphaNum|length:6,20',
            'mobile|手机号'        =>      'require|length:11',
        ];
        $res = $this->validate($data,$rule);
        $data['update_time'] = date('Y-m-d H:i:s', time());
        // 获取表单上传文件
        $file = request()->file('user_img');
        if ($file != NULL) {
            // 移动到uploads目录下
            $info = $file->move( '../public/uploads');
            if ($info) {
                // 成功上传后 获取上传信息
                $user_img=$info->getSaveName();
                $data['user_img']=$user_img;
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        if ($res === true) {
            $data['password'] = md5($data['password']);
            if (UserModel::update($data)) {
                $this->error('修改管理员成功!',url('Index/personmessage'));
            } else {
                $this->error('修改管理员失败!');
            }
        }else {
            $this->error($res);
        }
    }

    //删除管理员
    public function del(){
        $userId = Request::param('id');
        if (UserModel::destroy($userId)) {
            $this->success('删除管理员成功!',url('Index/personmessage'));
        } else {
            $this->error('删除管理员失败!');
        }
    }

    //发布文章
    public function articleadd(){
        $data = [
                'article_title'    =>    Request::post('article_title', '','trim'),
                'cid'              =>    Request::post('cid', '', 'intval'),
                'article_content'  =>    Request::post('article_content', '', 'trim')
        ];
        $rule = [
            'article_title|文章标题'     =>     'require|length:1,20',
            'cid|文章所属类型'            =>      'require',
            'article_content|文章内容'   =>     'require'
        ];
        $res = $this->validate($data,$rule);
        if ($res === true) {
            $data['create_time'] = date('Y-m-d H:i:s', time());
            $data['uid'] = Session::get('id');
            if (ArticleModel::create($data)) {
                $this->success('发表内容成功!',url('Index/articlemessage'));
            } else {
                $this->error('发表内容失败!');
            }
        } else {
            $this->error($res);
        }
    }

    //编辑文章
    public function articleedit(){
        $data = [
            'aid'              =>    Request::post('aid', '', 'intval'),
            'article_title'    =>    Request::post('article_title', '','trim'),
            'cid'              =>    Request::post('cid', '', 'intval'),
            'article_content'  =>    Request::post('article_content', '', 'trim'),
        ];
        $rule = [
            'article_title|文章标题'     =>      'require|length:1,20',
            'cid|文章所属类型'            =>      'require',
            'article_content|文章内容'   =>      'require'
        ];
        $res = $this->validate($data,$rule);
        if ($res === true) {
            $data['update_time'] = date('Y-m-d H:i:s', time());
            if (ArticleModel::where('aid', $data['aid'])->update($data)) {
                $this->error('编辑文章成功！',url('Index/articlemessage'));
            } else {
                $this->error('编辑文章失败！');
            }
        }else {
            $this->error($res);
        }
    }

    //删除文章
    public function articledel(){
        $userId = Request::param('aid', '', 'intval');
        if (ArticleModel::destroy($userId)) {
            $this->success('删除文章成功!',url('Index/articlemessage'));
        } else {
            $this->error('删除文章失败!');
        }
    }

    //发布日迹
    public function diaryadd(){
        $data['diary_content'] = Request::post('diary_content', '', 'trim');
        $rule = [
            'diary_content|日迹内容'  =>  'require'
        ];
        $res = $this->validate($data,$rule);
        if ($res === true) {
            $data['create_time'] = date('Y-m-d H:i:s', time());
            $data['uid'] = Session::get('id');
            if (DiaryModel::create($data)) {
                    $this->success('发表日迹成功', url('Index/diarymessage'));
                } else {
                    $this->error('发表日迹失败');
                }
        } else {
            $this->error($res);
        }
    }

    //编辑日迹
    public function diaryedit(){
        $data = [
            'did'             =>   Request::post('did', '', 'intval'),
            'diary_content'   =>   Request::post('diary_content', '', 'trim')
        ];
        $rule= [
            'diary_content|日迹内容'    =>  'require'
        ];
        $res = $this->validate($data,$rule);
        if ($res === true) {
            $data['update_time'] = date('Y-m-d H:i:s', time());
            $data['uid'] = Session::get('id');
            if (DiaryModel::update($data)) {
                $this->error('编辑日迹成功！',url('Index/diarymessage'));
            } else {
                $this->error('编辑日迹失败！');
            }
        }else {
            $this->error($res);
        }
    }

    //删除日迹
    public function diarydel(){
        $userId = Request::param('did', '', 'intval');
        if (DiaryModel::destroy($userId)) {
            $this->success('删除日迹成功!',url('Index/diarymessage'));
        } else {
            $this->error('删除日迹失败!');
        }
    }

    //上传图片
    public function photosadd(){
        $up_imgs = [];
        $codes = [];
        $hidden_img[] = input('post.hidden_imgs/a');
        $data = [
            'cid'          =>     Request::post('cid', '', 'intval')
        ];
        // 获取表单上传文件
        $files = request()->file('imgs');
        if ($hidden_img[0] != '') {
            foreach ($files as $key => $value) {
                $imgmes = $files[$key]->getInfo();
                $up_imgs[] = $imgmes['name'];
            }
            foreach ($up_imgs as $key => $value) {
                foreach ($hidden_img[0] as $k => $v) {
                    if ($value == $v) {
                        foreach ($files as $ke => $val) {
                            $imgmes = $files[$ke]->getInfo();
                            if ($v == $imgmes['name']) {
                                unset($files[$ke]);
                            }
                        }
                    }
                }
            }
        }
        foreach ($files as $file) {
            // 移动到uploads目录下
            $info = $file->move( '../public/uploads');
            if ($info) {
                // 成功上传后 获取上传信息
                $codes[] = $info->getSaveName();
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        $rule = [
            'cid|照片所属类型类型'  =>  'require'
        ];
        $res = $this->validate($data,$rule);
        if ($res === true) {
            $data['create_time'] = date('Y-m-d H:i:s', time());
            $data['uid'] = Session::get('id');
            $data['imgs'] = implode(",", $codes);
            if (ImgModel::create($data)) {
                $this->success('上传图片成功!',url('Index/photosmessage'));
            } else {
                $this->error('上传图片失败!');
            }
        } else {
            $this->error($res);
        }
    }

    //编辑图片
    public function photoedit(){
        $up_imgs = [];
        $codes = [];
        $hidden_img[] = input('post.hidden_imgs/a');
        $data = [
            'iid'         =>      Request::post('iid', '', 'intval')
        ];
        // 获取表单上传文件
        $files = request()->file('imgs');
        if ($hidden_img[0] != '') {
            foreach ($files as $key => $value) {
                $imgmes = $files[$key]->getInfo();
                $up_imgs[] = $imgmes['name'];
            }
            foreach ($up_imgs as $key => $value) {
                foreach ($hidden_img[0] as $k => $v) {
                    if ($value == $v) {
                        foreach ($files as $ke => $val) {
                            $imgmes = $files[$ke]->getInfo();
                            if ($v == $imgmes['name']) {
                                unset($files[$ke]);
                            }
                        }
                    }
                }
            }
        }
        foreach ($files as $file) {
            // 移动到uploads目录下
            $info = $file->move( '../public/uploads');
            if ($info) {
                // 成功上传后 获取上传信息
                $codes[] = $info->getSaveName();
            } else {
                // 上传失败获取错误信息
                echo $file->getError();
            }
        }
        $rule= [
            'cid|照片所属类型类型'=>'require'
        ];
        $res = $this->validate($data,$rule);
        if ($res === true) {
            $data['update_time'] = date('Y-m-d H:i:s', time());
            $data['uid'] = Session::get('id');
            $data['imgs'] = implode(",", $codes);
            if (ImgModel::update($data)) {
                $this->error('编辑图片成功！',url('Index/photosmessage'));
            } else {
                $this->error('编辑图片失败！');
            }
        }else {
            $this->error($res);
        }
    }


    //删除图片
    public function photodel(){
        $userId = Request::param('iid', '', 'intval');
        if (ImgModel::destroy($userId)) {
            $this->success('删除图片成功!',url('Index/photosmessage'));
        } else {
            $this->error('删除图片失败!');
        }
    }

    //添加分类
    public function cateadd(){
        $data = [
            'is_article'    =>   Request::post('is_article', 0, 'intval'),
            'cate_name'     =>   Request::post('cate_name', '', 'trim')
        ];
        $rule = [
            'is_article|分类所属'    =>       'require|number',
            'cate_name|分类名称'     =>       'require'
        ];
        $res = $this->validate($data, $rule);
        if ($res === true) {
            $data['create_time'] = date('Y-m-d', time());
            if (CateModel::create($data)) {
                $this->success('添加分类成功', 'admin/Index/catemessage');
            } else {
                $this->error('添加分类失败');
            }
        } else {
            $this->error($res);
        }
    }

    //编辑分类
    public function cateedit(){
        $data = [
            'cid'           =>      Request::post('cid', '', 'intval'),
            'is_article'    =>      Request::post('is_article', '', 'intval'),
            'cate_name'     =>      Request::post('cate_name', '', 'trim')
        ];
        $rule = [
            'cid|分类ID'            =>      'require|number',
            'is_article|分类所属'    =>      'require|number',
            'cate_name|分类名称'     =>      'require'
        ];
        $res = $this->validate($data, $rule);
        if ($res === true) {
            if (CateModel::update($data)) {
                $this->success('编辑分类成功','admin/Index/catemessage');
            } else {
                $this->error('编辑分类失败');
            }
        } else {
            $this->error($res);
        }
    }

    //删除分类
    public function catedel(){
        $cid = Request::param('cid', '', 'intval');
        if (CateModel::destroy($cid)) {
            $this->success('删除分类成功', 'admin/Index/catemessage');
        } else {
            $this->error('删除分类失败');
        }
    }

    //上传轮播图
    public function imgadd(){
        $data = Request::param();
        // 获取表单上传文件
        $file = request()->file('qimg');
        // 移动到uploads目录下
        $info = $file->move( '../public/uploads');
        if($info){
            // 成功上传后 获取上传信息
            $qimg=$info->getSaveName();
            $data['qimg']=$qimg;
        }else{
            // 上传失败获取错误信息
            echo $file->getError();
        }
        $rule = [
            'qtitle|轮播图标题'=>'require|length:1,20',
            'cid|照片所属类型类型'=>'require',
            'qdesc|轮播图描述'=>'require|min:1'
        ];
        $res = $this->validate($data,$rule);
        if ($res == true) {
            $data['create_time'] = date('Y-m-d H:i:s', time());
            $data['uid'] = Session::get('id');
            if(QimgModel::create($data)){
                $this->success('上传轮播图成功!',url('Index/imgmessage'));
            }else{
                $this->error('上传轮播图失败!');
            }
        }else{
            $this->error($res);
        }
    }

//删除轮播图
    public function imgdel(){
        $userId = Request::param('qid');
        if(QimgModel::destroy($userId)){
            $this->success('删除图片成功!',url('Index/imgmessage'));
        }else{
            $this->error('删除图片失败!');
        }
    }

    //发布留言
    public function addmessage(){
        $data = [
            'mes_name'    =>   Request::post('mes_name', '', 'trim'),
            'mes_email'   =>   Request::post('mes_email', '', 'trim'),
            'mes_text'    =>   Request::post('mes_text', '', 'trim')
        ];
        $rule = [
            'mes_name|留言者昵称' => 'require',
            'mes_email|邮箱'     => 'require|email',
            'mes_text|留言内容'   => 'require',
        ];
        $res = $this->validate($data,$rule);
        if ($res === true) {
            $data['create_time'] = date('Y-m-d H:i:s', time());
            if (MesModel::create($data)) {
                $this->success('留言成功!',url('index/Index/message'));
            } else {
                $this->error('留言失败!');
            }
        } else {
            $this->error($res);
        }
    }

    //删除留言
    public function messagedel(){
        $messageId = Request::param('mid', '', 'intval');
        if (MesModel::destroy($messageId)) {
            $this->success('删除日迹成功!',url('Index/message'));
        } else {
            $this->error('删除日迹失败!');
        }
    }

    //发表评论
    public function addcomment(){
        $data = [
            'own_name'     =>    Request::get('own_name', '', 'trim'),
            'own_email'    =>    Request::get('own_email', '', 'trim'),
            'own_content'  =>    Request::get('own_content', '', 'trim'),
            'aid'          =>    Request::get('aid', '', 'intval'),
            'did'          =>    Request::get('did', '', 'intval'),
        ];
        $rule = [
            'own_name|评论者昵称'    =>     'require',
            'own_email|邮箱'        =>     'require|email',
            'own_content|评论内容'   =>     'require',
        ];
        $res = $this->validate($data,$rule);
        if ($res === true) {
            $data['create_time'] = date('Y-m-d H:i:s', time());
            if (CommentModel::create($data)) {
                $this->success('评论成功');
            } else {
                $this->error('评论失败');
            }
        } else {
            $this->error($res);
        }
    }

    //删除评论
    public function commentdel(){
        $commentId = Request::param('cid', '', 'intval');
        if (CommentModel::destroy($commentId)) {
            $this->success('删除评论成功!',url('Index/articlemessage'));
        } else {
            $this->error('删除评论失败!');
        }
    }

    //对评论回复
    public function addreply() {
        $data = [
            'cid'          =>     Request::post('cid', 0, 'intval'),
            'mid'          =>     Request::post('mid', 0, 'intval'),
            'rusername'    =>     Request::post('rusername', '', 'trim'),
            'remail'       =>     Request::post('remail', '', 'trim'),
            'rcontent'     =>     Request::post('rcontent', '', 'trim'),
        ];
        $rule = [
            'rusername|回复者昵称'     =>     'require',
            'remail|回复者邮箱'        =>     'require|email',
            'rcontent|回复内容'        =>     'require',
        ];
        $res = $this->validate($data, $rule);
        if ($res === true) {
            $data['create_time'] = date('Y-m-d H:i:s', time());
            if (ReplyModel::create($data)) {
                $this->success('回复成功');
            } else {
                $this->error('回复失败');
            }
        } else {
            $this->error($res);
        }
    }

    //删除回复
    public function replydel()
    {
        $replyId = Request::param('rid', '', 'intval');
        if (ReplyModel::destroy($replyId)) {
            $this->success('删除回复成功!');
        } else {
            $this->error('删除回复失败!');
        }
    }

    //markdown语法中上传图片功能
    public function uploadimg()
    {
        if (request()->isPost()) {
            $file = request()->file('editormd-image-file');                   
                $info = $file->move( '../public/uploads');
                if ($info) {
                    $value=config('uploadFiles').'/uploads/'.$info->getSaveName();
                    return json(['url'=>$value,'success'=>1,'message'=>'图片上传成功!']);
                } else {        
                    echo $file->getError();
                }            
        } else {
            $this->error('非法请求');
        }
    }

}