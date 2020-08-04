<?php

namespace app\index\controller;

use app\admin\model\Comment;
use app\admin\model\Article as ArticleModel;
use app\admin\model\Diary as DiaryModel;
use app\admin\model\Message as MesModel;
use app\admin\model\Comment as CommentModel;
use app\admin\model\Img as ImgModel;
use app\admin\model\Reply as ReplyModel;
use app\admin\model\Cate as CateModel;
use think\facade\Request;
use think\Controller;
use think\Db;
use think\facade\Session;
class Index extends Controller
{
    //渲染登录页面
    public function login()
    {
        return $this->fetch();
    }

    //渲染前台主页
    public function index()
    {
        Session::set('page_id',1);
        return $this->fetch();
    }

    //渲染文章页面
    public function article()
    {
        Session::set('page_id',2);
        $where = [];
        $userId = Request::param('cid', '', 'intval');
        $keywords = Request::param('keywords', '', 'trim');
        if ($userId) {
            $where[] = ['cid', '=', $userId];
        }
        if (!empty($keywords)) {
            $where[] = ['article_title|article_content', 'like', $keywords];
        }
        $articleList = ArticleModel::where($where)
            ->field('aid,article_title,cid,uid,article_content,create_time')
            ->order('create_time','DESC')
            ->paginate(6);
        $articlesList = ArticleModel::field('aid,article_title')
            ->order('create_time','DESC')
            ->limit(11)
            ->select();
        $commentsList = CommentModel::field('aid,own_name')
            ->order('create_time','DESC')
            ->limit(11)
            ->select();
        $cateList = CateModel::where('is_article', 1)
            ->field('cid,cate_name')
            ->select();
        $this->view->assign('cateList',$cateList);
        $this->view->assign('articleList',$articleList);
        $this->view->assign('commentsList',$commentsList);
        $this->view->assign('articlesList',$articlesList);
        return $this->view->fetch('article');
    }

    //渲染日迹页面
    public function diary()
    {
        Session::set('page_id',3);
        $diaryList = DiaryModel::field('did,diary_content,fabulous,browse,create_time,update_time,uid')
            ->order('create_time','DESC')
            ->paginate('6');
        $commentList = CommentModel::where('aid', 0)
            ->field('cid,did,own_name,own_email,own_content,create_time')
            ->select();
        $replyList = ReplyModel::field('rid,cid,rusername,remail,rcontent,create_time')
            ->where('mid', 0)
            ->select();
        $countList = DiaryModel::alias('a')
            ->join('blog_comment b','a.did = b.did')
            ->field('b.did,b.cid')
            ->order('b.did', 'ASC')
            ->select();
        $num = CommentModel::alias('a')
            ->join('blog_reply b', 'a.cid = b.cid')
            ->group('b.cid')
            ->field('b.cid,b.rid,COUNT(rid) as count_rid')
            ->select();
        foreach ($countList as $key=>$value) {
            $countList[$key]['count_cid'] = 1;
            foreach ($num as $k=>$v) {
                if ($countList[$key]['cid'] == $num[$k]['cid']) {
                    $countList[$key]['count_cid'] += $num[$k]['count_rid'];
                }
            }
        }
        for ($i = 0; $i < count($countList)-1; $i++) {
            if ($countList[$i+1]['did'] == $countList[$i]['did']) {
                $countList[$i+1]['count_cid'] += $countList[$i]['count_cid'];
                $countList[$i]['count_cid'] = 'u';
            }
        }
        foreach ($countList as $key=>$value) {
            if ($countList[$key]['count_cid'] === 'u') {
                unset($countList[$key]);
            }
        }
        $this->view->assign('commentList', $commentList);
        $this->view->assign('diaryList', $diaryList);
        $this->view->assign('replyList', $replyList);
        $this->view->assign('countList', $countList);
        return $this->view->fetch('diary');//渲染管理员列表的模板
    }

    //渲染相册页面
    public function album()
    {
        Session::set('page_id',4);
        $userId = Request::param('cid', '', 'intval');
        $where = [];
        if ($userId) {
            $where[] = ['cid', '=', $userId];
        }
        $albumList = ImgModel::where($where)
            ->field('cid,imgs,uid,create_time')
            ->select();
        foreach ($albumList as $key => $value) {
            $albumList[$key]['imgs'] = explode(',', $value['imgs']);
        }
        $cateList = CateModel::where('is_article', 2)
            ->field('cid,cate_name')
            ->select();
        $this->view->assign('cateList',$cateList);
        $this->view->assign('albumList',$albumList);
        return $this->view->fetch('album');
    }

    //渲染留言页面
    public function message()
    {
        Session::set('page_id',5);
        $messageList = MesModel::field('mid,mes_name,mes_email,mes_img,mes_text,create_time')
            ->order('create_time', 'DESC')
            ->select();
        $replyList = ReplyModel::field('mid,rid,cid,rusername,remail,rcontent,create_time')
            ->where('cid', 0)
            ->order('create_time', 'DESC')
            ->select();
        $this->view->assign('messageList',$messageList);
        $this->view->assign('replyList',$replyList);
        return $this->view->fetch('message');
    }

    //渲染文章详情页面
    public function details()
    {
        Session::set('page_id',2);
        $userId = Request::param('aid', '', 'intval');
        $where[] = ['aid', '=', $userId];
        Db::table('blog_article')->where($where)->setInc('browse');
        $detailsList = ArticleModel::where($where)
            ->field('aid,article_title,cid,fabulous,browse,article_content,create_time')
            ->find();
        $articlesList = ArticleModel::field('aid,article_title')
            ->order('create_time', 'DESC')
            ->limit(11)
            ->select();
        $commentList = CommentModel::where($where)
            ->field('cid,own_name,own_content,create_time')
            ->select();
        $replyList = ReplyModel::field('rid,cid,rusername,remail,rcontent,create_time')
            ->where('mid', 0)
            ->select();
        $commentsList = CommentModel::field('aid,own_name')
            ->order('create_time','DESC')
            ->limit(11)
            ->select();
        $this->view->assign('detailsList',$detailsList);
        $this->view->assign('articlesList',$articlesList);
        $this->view->assign('commentList',$commentList);
        $this->view->assign('commentsList',$commentsList);
        $this->view->assign('replyList',$replyList);
        return $this->view->fetch('details');
    }
}
