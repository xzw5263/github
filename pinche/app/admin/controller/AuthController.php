<?php
// +----------------------------------------------------------------------
// | ThinkCMF [ WE CAN DO IT MORE SIMPLE ]
// +----------------------------------------------------------------------
// | Copyright (c) 2013-2017 http://www.thinkcmf.com All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: 小夏 < 449134904@qq.com>
// +----------------------------------------------------------------------
namespace app\admin\controller;

use cmf\controller\AdminBaseController;
use think\Db;
use app\admin\model\AdminMenuModel;

class AuthController extends AdminBaseController
{
	const PAGESIZE = 15;
    /**
	 * 认证方法
	 */
	public function idcard(){
		if(!empty($_GET)){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
		$start = ($page - 1)*self::PAGESIZE;
		$list = DB::name('tzy_cardauth')->where("state = '1'")->order('create_time desc')->paginate(self::PAGESIZE);
		// 获取分页显示
        $page = $list->render();
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function agreelist(){
		if(!empty($_GET)){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
		$start = ($page - 1)*self::PAGESIZE;
		$list = DB::name('tzy_cardauth')->where("state = '2'")->order('create_time desc')->paginate(self::PAGESIZE);
		// 获取分页显示
        $page = $list->render();
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function rejectlist(){
		if(!empty($_GET)){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
		$start = ($page - 1)*self::PAGESIZE;
		$list = DB::name('tzy_cardauth')->where("state = '3'")->order('create_time desc')->paginate(self::PAGESIZE);
		// 获取分页显示
        $page = $list->render();
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function idcarddetail(){
		$id = $this->request->param('id',0,'intval');
		$info = DB::name('tzy_cardauth')->alias('a')->join('tzy_user b','a.uid = b.id','left')->where('a.id',$id)->field('nick,a.*')->find();
		$this->assign('info',$info);
		return $this->fetch();
	}
	
	public function idcarddetails(){
		$id = $this->request->param('id',0,'intval');
		$info = DB::name('tzy_cardauth')->alias('a')->join('tzy_user b','a.uid = b.id','left')->where('a.id',$id)->field('nick,a.*')->find();
		$this->assign('info',$info);
		return $this->fetch();
	}
	
	public function cardagree(){
		$id = $this->request->param('id', 0, 'intval');
		$data=array('state'=>'2');
		$re = DB::name('tzy_cardauth')->where('id',$id)->update($data);
		if($re){
			$this->success('更新成功！',url('auth/idcard'));
		}else{
			$this->error('更新失败!',url('auth/idcard'));
		}
	}
	
	public function cardreject(){
		$id = $this->request->param('id',0,'intval');
		$data=array('state'=>'3');
		$re = DB::name('tzy_cardauth')->where('id',$id)->update($data);
		if($re){
			$this->success('更新成功！',url('auth/idcard'));
		}else{
			$this->error('更新失败!',url('auth/idcard'));
		}
	}
	
	public function deletecard(){
		$id = $this->request->param('id',0,'intval');
		$re = DB::name('tzy_cardauth')->where('id',$id)->delete();
		if($re){
			$this->success('更新成功！',url('auth/idcard'));
		}else{
			$this->error('更新失败!',url('auth/idcard'));
		}
	}
	
	public function licenceauth(){
		if(!empty($_GET)){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
		$start = ($page - 1)*self::PAGESIZE;
		$list = DB::name('tzy_authentication')->where("state = '1'")->order('create_time desc')->paginate(self::PAGESIZE);
		// 获取分页显示
        $page = $list->render();
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function licencedetail(){
		$id = $this->request->param('id',0,'intval');
		$info = DB::name('tzy_authentication')->alias('a')->join('tzy_user b','a.uid = b.id','left')->where('a.id',$id)->field('nick,a.*')->find();
		$this->assign('info',$info);
		return $this->fetch();
	}
	
	public function licencedetails(){
		$id = $this->request->param('id',0,'intval');
		$info = DB::name('tzy_authentication')->alias('a')->join('tzy_user b','a.uid = b.id','left')->where('a.id',$id)->field('nick,a.*')->find();
		$this->assign('info',$info);
		return $this->fetch();
	}
	
	public function licenceagree(){
		$id = $this->request->param('id', 0, 'intval');
		$data=array('state'=>'2');
		$re = DB::name('tzy_authentication')->where('id',$id)->update($data);
		if($re){

			$info = DB::name('tzy_authentication')->where('id',$id)->field('uid')->find();

			$uid = $info['uid'];
			DB::name('tzy_user')->where('id',$uid)->update($data);

			$this->success('更新成功！',url('auth/idcard'));
		}else{
			$this->error('更新失败!',url('auth/idcard'));
		}
	}
	
	public function licencereject(){
		$id = $this->request->param('id',0,'intval');
		$data=array('state'=>'3');
		$re = DB::name('tzy_authentication')->where('id',$id)->update($data);
		if($re){
			$this->success('更新成功！',url('auth/idcard'));
		}else{
			$this->error('更新失败!',url('auth/idcard'));
		}
	}
	
	public function lagreelist(){
		if(!empty($_GET)){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
		$start = ($page - 1)*self::PAGESIZE;
		$list = DB::name('tzy_authentication')->where("state = '2'")->order('create_time desc')->paginate(self::PAGESIZE);
		// 获取分页显示
        $page = $list->render();
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function lrejectlist(){
		if(!empty($_GET)){
			$page = $_GET['page'];
		}else{
			$page = 1;
		}
		$start = ($page - 1)*self::PAGESIZE;
		$list = DB::name('tzy_authentication')->where("state = '3'")->order('create_time desc')->paginate(self::PAGESIZE);
		// 获取分页显示
        $page = $list->render();
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function licencedelete(){
		$id = $this->request->param('id',0,'intval');
		$re = DB::name('tzy_authentication')->where('id',$id)->delete();
		if($re){
			$this->success('更新成功！',url('auth/idcard'));
		}else{
			$this->error('更新失败!',url('auth/idcard'));
		}
	}
}
