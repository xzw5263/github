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

class OtherController extends AdminBaseController
{
    /**
     * 后台首页
     */
    public function canceldutyrule()
    {
		//$data=$request->input('name');
		//$type=$this->request->ispost();
		if(!empty($_POST['post'])){
			$data['content']=$_POST['post'];
			$re=DB::name('tzy_canceldutyrule')->where('id=1')->update($data);
			if($re){
				$this->success('编辑成功！');
			}else{
				$this->error('编辑失败');
			}
		}else{
			$info = DB::name('tzy_canceldutyrule')->where('id=1')->find();
			$this->assign('info',$info);
			return $this->fetch();
		}
    }
	
	public function version(){
		$list = DB::name('tzy_version')->order('create_time desc')->select();
		$this->assign('list',$list);
		return $this->fetch();
	}
	
	public function addversion(){
		if(!empty($_POST)){
			if($_POST['type']==1){
				$data=array(
					'title'=>$_POST['title'],
					'version_name'=>$_POST['version_name'],
					'version_size'=>$_POST['version_size'],
					'version_content'=>$_POST['version_content'],
					'version_code'=>$_POST['version_code'],
					'type'=>$_POST['type'],
					'version_link'=>'http://car.trackline.cn/download/carandshi.apk',
					'create_time'=>date('Y-m-d H:i:s',time())
				);
				}else{
					$data=array(
					'title'=>$_POST['title'],
					'version_name'=>$_POST['version_name'],
					'version_size'=>$_POST['version_size'],
					'version_content'=>$_POST['version_content'],
					'version_code'=>$_POST['version_code'],
					'type'=>$_POST['type'],
					'version_link'=>'http://car.trackline.cn/download/carandshi.apk',
					'create_time'=>date('Y-m-d H:i:s',time())
				);
			}
			$re=DB::name('tzy_version')->insert($data);
			if($re){
				$this->success('添加成功！','Other/version');
			}else{
				$this->error('添加失败！','Other/version');
			}
			
		}else{
			return $this->fetch();
		}
		
	}
}
