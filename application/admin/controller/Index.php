<?php

namespace app\admin\controller;

use app\admin\model\Comment;
use app\admin\controller\Common;//导入公共控制器
use app\admin\model\User as UserModel;
use app\admin\model\Article as ArticleModel;
use app\admin\model\Diary as DiaryModel;
use app\admin\model\Img as ImgModel;
use app\admin\model\Message as MesModel;
use app\admin\model\Comment as CommentModel;
use app\admin\model\Qimg as QimgModel;
use app\admin\model\Reply as ReplyModel;
use app\admin\model\Cate as CateModel;
use think\facade\Request;
use think\facade\Session;

class Index extends Common
{
    //渲染后台主页
    public function index()
    {
        return $this->fetch();
    }

    //渲染发布文章页面
    public function articleadd()
    {
        $cateList = CateModel::where(['is_article' => '1'])
            ->field('cid,cate_name,create_time')
            ->select();
        $this->view->assign('cateList', $cateList);
        return $this->view->fetch('articleadd');
    }


    //渲染文章评论页面
    public function articlecomment()
    {
        $res = Request::get('own_name', '', 'trim');
        $aid = Request::param('aid', '', 'intval');
        if (!empty($res)) {
            $where[] = ['own_name', 'like', '%' . $res . '%'];
        }
        $where[] = ['aid', 'eq', $aid];
        $commentsList = CommentModel::where($where)
            ->field('aid,cid,own_name,own_content,create_time')
            ->order('create_time', 'DESC')
            ->paginate(5);
        $replyList = CommentModel::alias('a')
            ->join('blog_reply b', 'a.cid = b.cid')
            ->field('b.rid,b.cid,b.rusername,b.rcontent,b.create_time')
            ->order('create_time', 'DESC')
            ->select();
        $this->view->assign('aid', $aid);
        $this->view->assign('commentsList', $commentsList);
        $this->view->assign('replyList', $replyList);
        return $this->fetch();
    }

    //渲染编辑文章页面
    public function articleedit()
    {
        $userId = Request::param('aid', '', 'intval');
        $bookList = ArticleModel::where('aid', $userId)
            ->field('aid,article_title,cid,uid,article_content,update_time')
            ->find();
        $cateList = CateModel::where(['is_article' => '1'])
            ->field('cid,cate_name,create_time')
            ->select();
        $this->view->assign('cateList', $cateList);
        $this->assign('bookList', $bookList);
        return $this->fetch();
    }

    //渲染文章主页面
    public function articlemessage()
    {
        $res = [
            'username'   =>   Request::post('username', '', 'trim'),
            'cid'        =>   Request::post('cate', '', 'intval')
        ];
        if ($res['username']) {
            $user[]      =   ['username', 'like', '%' . $res['username'] . '%'];
            $uid         =   UserModel::where($user)->value('id');
            $where[]     =   ['uid', 'eq', $uid];
        }
        if ($res['cid']) {
            $where[]     =   ['cid', 'eq', $res['cid']];
        }
        $where[] = ['aid', 'neq', 0];
        $bookList = ArticleModel::where($where)
            ->field('aid,cid,uid,article_title,fabulous,article_content,create_time')
            ->order('create_time', 'desc')
            ->paginate(5);
		$cateList = CateModel::where(['is_article' => '1'])
			->field('cid,cate_name,create_time')
			->select();
        $this->view->assign('bookList', $bookList);
		$this->view->assign('cateList', $cateList);
        return $this->view->fetch('articlemessage');
    }

    //渲染发布日迹页面
    public function diaryadd()
    {
        return $this->fetch();
    }

    //渲染日迹评论页面
    public function diarycomment()
    {
        $res = Request::get('own_name', '', 'trim');
        $did = Request::param('did', '', 'intval');
        if (!empty($res)) {
            $where[] = ['own_name', 'like', '%' . $res . '%'];
        }
        $where[] = ['did', 'eq', $did];
        $commentList = CommentModel::where($where)
            ->field('did,aid,cid,own_name,own_content,create_time')
            ->order('create_time', 'DESC')
            ->paginate(5);
        $replyList = CommentModel::alias('a')
            ->join('blog_reply b', 'a.cid = b.cid')
            ->field('b.rid,b.cid,b.rusername,b.rcontent,b.create_time')
            ->order('create_time', 'DESC')
            ->select();
        $this->view->assign('did', $did);
        $this->view->assign('commentList', $commentList);
        $this->view->assign('replyList', $replyList);
        return $this->fetch();
    }

    //渲染编辑日迹页面
    public function diaryedit()
    {
        $userId = Request::param('did', '', 'intval');
        $diaryList = DiaryModel::where('did', $userId)->field('did,diary_img,diary_content')->find();
        $diaryList['show_img'] = explode(',', $diaryList['diary_img']);
        $this->assign('diaryList', $diaryList);
        return $this->fetch();
    }

    //渲染日迹主页
    public function diarymessage()
    {
        $res = Request::post('username', '', 'trim');
        if (!empty($res)) {
            $user[] = ['username', 'like', '%' . $res['username'] . '%'];
            $uid = UserModel::where($user)->value('id');
            $where[] = ['uid', 'eq', $uid];
        }
        $where[] = ['did', 'neq', 0];
        $diaryList = DiaryModel::where($where)
            ->field('uid,fabulous,browse,diary_img,create_time,did,diary_content')
            ->order('create_time', 'DESC')
            ->paginate(5);
        foreach ($diaryList as $key => $value) {
            $diaryList[$key]['diary_img'] = explode(",", $value['diary_img']);
        }
        $this->view->assign('diaryList', $diaryList);
        return $this->view->fetch();
    }

    //渲染添加轮播图页面
    public function imgadd()
    {
        return $this->fetch();
    }

    //渲染编辑轮播图页面
    public function imgedit()
    {
        $userId = Request::param('qid', '', 'intval');
        $qimgList = QimgModel::where('qid', $userId)->field('qid,qtitle,qdesc,qid,create_time,qimg')->find();
        $this->assign('qimgList', $qimgList);
        return $this->fetch();
    }

    //渲染轮播图主页
    public function imgmessage()
    {
        $qimgList = QimgModel::field('qid,qtitle,qimg,qdesc,create_time,qstatic')->paginate(6);
        $this->view->assign('qimgList', $qimgList);
        return $this->view->fetch('imgmessage');//渲染管理员列表的模板
    }

    //渲染留言主页
    public function message()
    {
        $res = Request::post('mes_name', '', 'trim');
        if (!empty($res)) {
            $where[] = ['mes_name', 'like', '%' . $res['mes_name'] . '%'];
        }
        $where[] = ['mid', 'neq', 0];
        $messageList = MesModel::where($where)
            ->field('mes_name,mes_email,create_time,mid,mes_text')
            ->order('create_time', 'DESC')
            ->paginate(5);
        $replyList = ReplyModel::where('mid', 'neq', '0')->field('rid,mid,rusername,remail,rcontent,create_time')->select();
        $this->view->assign('messageList', $messageList);
        $this->view->assign('replyList', $replyList);
        return $this->fetch();
    }

    //渲染修改个人信息页面
    public function person()
    {
        $userId = Session::get('id');
        $userList = UserModel::where('id', $userId)
                 ->field('username,password,mobile,email,user_img,create_time')
                 ->find();
        $this->assign('userList', $userList);
        return $this->fetch();
    }

    //渲染添加管理员页面
    public function personadd()
    {
        return $this->fetch();
    }

    //渲染编辑管理员页面
    public function personedit()
    {
        $userId = Request::param('id', '', 'intval');
        $userList = UserModel::where('id', $userId)
                 ->field('id,username,password,mobile,email,user_img,create_time')
                 ->find();
        $this->assign('userList', $userList);
        return $this->fetch();
    }

    //渲染管理员列表
    public function personmessage()
    {
        $userList = UserModel::field('id,username,mobile,create_time')->paginate(6);
        $this->view->assign('userList', $userList);
        return $this->view->fetch();//渲染管理员列表的模板
    }


    //渲染上传图片页面
    public function photosadd()
    {
    	$cateList = CateModel::where(['is_article' => '2'])
    		->field('cid,cate_name')
    		->select();
    	$this->view->assign('cateList', $cateList);
        return $this->fetch();
    }

    //渲染图片编辑页面
    public function photosedit()
    {
        $userId = Request::param('iid', '', 'intval');
        $photoList = ImgModel::where('iid', $userId)->field('iid,imgs,cid')->find();
        $photoList['show_img'] = explode(',', $photoList['imgs']);
        $cateList = CateModel::where(['is_article' => '2'])
    		->field('cid,cate_name')
    		->select();
    	$this->view->assign('cateList', $cateList);
        $this->assign('photoList', $photoList);
        return $this->fetch();
    }

    //渲染图片信息页面
    public function photosmessage()
    {
        $res = Request::post('username', '', 'trim');
        if (!empty($res)) {
            $user[] = ['username', 'like', '%' . $res['username'] . '%'];
            $uid = UserModel::where($user)->value('id');
            $where[] = ['uid', 'eq', $uid];
        }
        $where[] = ['iid', 'neq', 0];
        $photoList = ImgModel::where($where)
            ->field('iid,uid,cid,imgs,create_time')
            ->order('create_time', 'DESC')
            ->paginate(5);
        foreach ($photoList as $key => $value) {
            $photoList[$key]['imgs'] = explode(",", $value['imgs']);
        }
        $this->view->assign('photoList', $photoList);
        return $this->view->fetch();
    }

    //渲染分类主页
    public function catemessage()
    {
        $cateList = CateModel::field('cid,cate_name,is_article,create_time')
            ->order('cid', 'DESC')
            ->paginate(5);
        $this->view->assign('cateList', $cateList);
        return $this->view->fetch();
    }

    //渲染添加分类
    public function cateadd()
    {
        return $this->view->fetch();
    }

    //渲染编辑分类
    public function cateedit()
    {
        $cid = Request::param('cid', '', 'intval');
        $cateList = CateModel::where('cid', $cid)
            ->field('cid,is_article,cate_name')
            ->find();
        $this->view->assign('cateList', $cateList);
        return $this->view->fetch();
    }

}
