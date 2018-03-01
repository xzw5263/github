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

class DistanceController extends AdminBaseController
{
	const PAGESIZE = 15;
    /**
	 * 车主发布行程
	 */
	public function index(){
		$list = DB::name('tzy_carpublish')->alias('a')
		->join('tzy_user b','a.uid = b.id','left')
		->join('tzy_authentication c','a.uid = c.uid','left')
		->field('a.id,b.nick,a.departure,a.destination,a.create_time,c.licence,c.cartype')
		->order('a.create_time desc')
		->paginate(self::PAGESIZE);
		$page = $list->render();
		$this->assign("page", $page);
		$this->assign('list',$list);
		return $this->fetch();
	} 
	
	public function cardetail(){
		$id = $this->request->param('id',0,'intval');
		$list = DB::name('tzy_choose')->alias('a')
		->join('tzy_carpublish b','a.pcid = b.id','left')
		->join('tzy_passengerpub d','a.ppid = d.id','left')
		->join('tzy_user c','b.uid = c.id')
		->where('a.pcid',$id)
		->field('a.id,b.departure,b.destination,c.nick,c.avatar,d.num,d.remark,a.price,a.state,b.starttime')->select();
		$this->assign('list',$list);
		return $this->fetch();
	}
	
}
