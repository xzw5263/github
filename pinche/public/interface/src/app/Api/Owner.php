<?php
namespace App\Api;

use App\Domain\Owner as DomainOwner;
use App\Domain\Index as DomainIndex;
use App\Common\CheckAuth as CheckAuth;
use PhalApi\Api;

/**
 * 车主 接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Owner extends Api {

	public function getRules() {
        return array(
            'index' => array(
                'username' 	=> array('name' => 'username', 'default' => 'PhalApi'),
            ),
            'publish'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
            	'departure' =>array('name' => 'departure','type'=>'string','require'=>true,'desc'=>'出发地城市'),
            	'destination'=>array('name'=>'destination','type'=>'string','require'=>true,'desc'=>'目的地城市'),
            	'address1'=>array('name'=>'address1','type'=>'string','require'=>true,'desc'=>'出发地地址'),
            	'address2'=>array('name'=>'address2','type'=>'string','require'=>true,'desc'=>'目的地地址'),
            	'distance'=>array('name'=>'distance','type'=>'string','require'=>true,'desc'=>'距离'),
            	'lng1'=>array('name'=>'lng1','type'=>'string','require'=>true,'desc'=>'初始经度'),
            	'lat1'=>array('name'=>'lat1','type'=>'string','require'=>true,'desc'=>'初始纬度'),
            	'lng2'=>array('name'=>'lng2','type'=>'string','require'=>true,'desc'=>'结束经度'),
            	'lat2'=>array('name'=>'lat2','type'=>'string','require'=>true,'desc'=>'结束纬度'),
            	'peoplenum'=>array('name'=>'peoplenum','type'=>'int','max'=>8,'require'=>true,'desc'=>'可载人数'),
            	'starttime'=>array('name'=>'starttime','type'=>'string','require'=>true,'desc'=>'出发时间'),
            	'remark'=>array('name'=>'remark','type'=>'string','desc'=>'备注'),
            	'price'=>array('name'=>'price','type'=>'float','require'=>false,'desc'=>'拼车价格'),
            	'iscity'=>array('name'=>'iscity','type'=>'int','default'=>'1','desc'=>'1、城际，2、市内')
            ),
            'mylists'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
				'page'=>array('name'=>'page','type'=>'int','default'=>1,'desc'=>'页码')
            ),
            'mydetail'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
            	'pcid'=>array('name'=>'pcid','type'=>'int','require'=>true,'desc'=>'车主发布id')
            ),
            'agree'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
            	'id'=>array('name'=>'id','type'=>'int','require'=>true,'desc'=>'订单id')
            ),
            'reject'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'id'=>array('name'=>'id','type'=>'int','require'=>true,'desc'=>'订单id')
            ),
            'confirmserver'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
            	'id'=>array('name'=>'id','type'=>'int','require'=>true,'desc'=>'订单id')
            ),
            'serverfinish'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
            	'id'=>array('name'=>'id','type'=>'int','require'=>true,'desc'=>'订单id')
            ),
            'suggest'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','desc'=>'用户id'),
            	'lng1'=>array('name'=>'lng1','type'=>'float','require'=>true,'desc'=>'出发地经度'),
            	'lat1'=>array('name'=>'lat1','type'=>'float','require'=>true,'desc'=>'出发地纬度'),
            	'lng2'=>array('name'=>'lng2','type'=>'float','require'=>true,'desc'=>'目的地经度'),
            	'lat2'=>array('name'=>'lat2','type'=>'float','require'=>true,'desc'=>'目的地纬度'),
            	'num'=>array('name'=>'num','type'=>'int','require'=>true,'desc'=>'乘车人数'),
            	'type'=>array('name'=>'type','type'=>'int','require'=>true,'default'=>'1','desc'=>'1、拼车，2、不拼车'),
            	'iscity'=>array('name'=>'iscity','type'=>'int','require'=>true,'default'=>'1','desc'=>'1、城际，2、市内')
            ),
            'canceloffer'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'pcid'=>array('name'=>'pcid','type'=>'int','require'=>true,'desc'=>'车主发布id')
            ),
            'cancelorder'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'pcid'=>array('name'=>'pcid','type'=>'int','require'=>true,'desc'=>'车主发布id'),
                'reason'=>array('name'=>'reason','type'=>'string','require'=>true,'desc'=>'取消行程原因…'),
            	'remark'=>array('name'=>'remark','type'=>'string','desc'=>'备注')
            ),
			'complain'=>array(
				'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
				'ppid'=>array('name'=>'ppid','type'=>'int','require'=>true,'desc'=>'乘客发布id'),
				'reason'=>array('name'=>'reason','type'=>'string','desc'=>'投诉原因')
			),
			'editpublish'=>array(
				'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'pcid'=>array('name'=>'pcid','type'=>'int','require'=>true,'desc'=>'车主发布id')
			)

        );
	}
	
	/**
	 * 测试接口
     * @desc 测试接口是否正常返回内容
	 * @return string title 标题
	 * @return string content 内容
	 * @return string version 版本，格式：X.X.X
	 * @return int time 当前时间戳
	 */
	public function index() {
        return array(
            'title' => 'Hello ' . $this->username,
            'version' => PHALAPI_VERSION,
            'time' => $_SERVER['REQUEST_TIME'],
        );
	}

	/**
	 * 车主 发布行程
	 * @desc 车主 发布行程接口
	 * @return string state 1、成功，0、失败
	 */
	public function publish(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->publish($this->uid,$this->departure,$this->destination,$this->address1,$this->address2,$this->distance,$this->peoplenum,$this->starttime,$this->remark,$this->price,$this->lng1,$this->lat1,$this->lng2,$this->lat2,$this->iscity);
	        if (!empty($result)) {
	            unset($result['re']);
	            $rs['code']=1;
	            $rs['msg']='发布成功！';
	            $rs['info'][]= $result;
	            return $rs;
	        }else{
	            $rs['code']=0;
	            $rs['msg']='发布失败！';
	            $rs['info']= array();
	            return $rs;
	        }
        }
        
	}

	
	/**
	 *车主发布行程接口
	 * @desc 车主发布行程列表接口
	 * @return int pcid 发布行程id
	 * @return string status （0：未接单、1：待选择、2：进行中）
	 * @return string departure 出发地
	 * @return string address1 出发地地址
	 * @return string destination 目的地
	 * @return string address2 目的地地址
	 * @return string starttime 出发时间
	 * @return string realnum 剩余车位数
	 * @return string price 价格
	 * @return string remark 备注
	 */
	public function mylists(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1 || $state==3){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->mylists($this->uid,$this->page);
	        if (!empty($result)) {
	            $rs['code']=1;
	            $rs['msg']='获取成功！';
	            $rs['info']= $result;
	            return $rs;
	        }else{
	            $rs['code']=1;
	            $rs['msg']='暂无数据~';
	            $rs['info']= array();
	            return $rs;
	        }
        }
        
	}

	/**
	 * 车主发布行程详细接口
	 * @desc 车主发布行程详细接口
	 * @return int id 订单id
	 * @return string state 返回状态（1、乘客申请、2、车主已同意，3、车主已拒绝，4、车主确认服务，5、车主确认服务完成）
	 * @return string departure 出发地
	 * @return string address1 出发地地址
	 * @return string destination 目的地
	 * @return string address2 目的地地址
	 * @return string starttime 出发时间
	 * @return string num 剩余车位数
	 * @return int ordernums 乘客一共使用了多少单
	 * @return string distances 乘客与车主的距离
	 */
	public function mydetail(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1 || $state==3){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->mydetail($this->uid,$this->pcid);
	        if (!empty($result['list'])) {
	            $rs['code']=1;
	            $rs['msg']='获取成功！';
	            $rs['info'][]= $result;
	            return $rs;
	        }else{
	            $rs['code']=0;
	            $rs['msg']='暂无报价';
	            $rs['info'][]= $result;
	            return $rs;
	        }
        }
        
	}

	/**
	 * 车主 立即接单 接口
	 * @desc 车主 立即接单
	 * @return 返回状态  （接单成功:1/失败:0）
	 * @return 
	 */
	public function agree(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->agree($this->uid,$this->id);
	        if (!empty($result)) {
	            $rs['code']=1;
	            $rs['msg']='接单成功！';
	            $rs['info']= array();
	            return $rs;
	        }else{
	            $rs['code']=0;
	            $rs['msg']='接单失败！';
	            $rs['info']= array();
	            return $rs;
	        }
        }
        
	}

	/**
	 * 车主 拒绝接单 接口
	 * @desc 车主 拒绝接单
	 * @return 返回状态  （拒绝成功:1/失败:0）
	 * @return 
	 */
	public function reject(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->reject($this->uid,$this->id);
	        if (!empty($result)) {
	            $rs['code']=1;
	            $rs['msg']='您已拒绝！';
	            $rs['info']= array();
	            return $rs;
	        }else{
	            $rs['code']=0;
	            $rs['msg']='拒绝失败！';
	            $rs['info']= array();
	            return $rs;
	        }
        }
        
	}

	/**
	 * 车主 确认服务 接口
	 * @desc 车主 确认服务
	 * @return 返回状态  （确认服务成功:1/失败:0）
	 * @return 
	 */
	public function confirmserver(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->confirmserver($this->uid,$this->id);
	        if ($result==1) {
	            $rs['code']=1;
	            $rs['msg']='确认成功！';
	            $rs['info']= array();
	            return $rs;
	        }else if($result ==2){
	            $rs['code']=0;
	            $rs['msg']='您必须在当天才能确认服务';
	            $rs['info']= array();
	            return $rs;
	        }else{
	            $rs['code']=0;
	            $rs['msg']='确认失败！';
	            $rs['info']= array();
	            return $rs;
	        }
        }
        
	}

	/**
	 * 车主 服务完成 接口
	 * @desc 车主 服务完成
	 * @return 返回状态  （成功:1/失败:0）
	 * @return 
	 */
	public function serverfinish(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->serverfinish($this->uid,$this->id);
	        if (!empty($result)) {
	            $rs['code']=1;
	            $rs['msg']='服务完成！';
	            $rs['info']= array();
	            return $rs;
	        }else{
	            $rs['code']=0;
	            $rs['msg']='未知错误！';
	            $rs['info']= array();
	            return $rs;
	        }
        }
        
	}

	/**
	 * 车主 建议价格 接口
	 * @desc 车主 建议价格
	 * @return 返回内容
	 * @return string price 建议价格
	 */
	public function suggest(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
    	$domain = new DomainIndex();
        $result = $domain->suggest($this->lng1,$this->lat1,$this->lng2,$this->lat2,$this->num,$this->type,$this->iscity);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='获取信息成功';
            $rs['info'][]= $result;
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='获取失败';
            $rs['info']= array();
            return $rs;
        }
	}

	/**
	 * 车主 取消行程接口
	 * @desc 车主 取消行程（在还没有人选择的时候，可以取消行程）
	 * @return 返回内容
	 * @return string price 建议价格
	 */
	public function canceloffer(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->canceloffer($this->uid,$this->pcid);
	        if (!empty($result)) {
	            $rs['code']=1;
	            $rs['msg']='您已取消！';
	            $rs['info']= array();
	            return $rs;
	        }else{
	            $rs['code']=0;
	            $rs['msg']='取消失败！';
	            $rs['info']= array();
	            return $rs;
	        }
        }
	}

	/**
	 * 车主 取消订单(需要填写取消原因)
	 * @desc 车主 取消订单
	 * @return 返回成功或者失败
	 * @return 
	 */
	public function cancelorder(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
    	$domain = new DomainPublish();
        $result = $domain->cancelorder($this->uid,$this->id,$this->pcid,$this->reason,$this->remark);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='取消成功！';
            $rs['info']= array();
            return $rs;
        }else{
        	$rs['code']=0;
            $rs['msg']='取消失败';
            $rs['info']= array();
            return $rs;
        }
	}
	
	/**
	 * 车主 投诉接口
	 * @desc 车主 投诉
	 * @return 返回投诉成功/失败
	 */
	public function complain(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->complain($this->uid,$this->ppid,$this->reason);
	        if (!empty($result)) {
	            $rs['code']=1;
	            $rs['msg']='投诉成功！';
	            $rs['info']= array();
	            return $rs;
	        }else{
	            $rs['code']=0;
	            $rs['msg']='投诉失败！';
	            $rs['info']= array();
	            return $rs;
	        }
        }
	}

	/**
	 * 车主 重新编辑接口
	 * @desc 车主 重新编辑
	 * @return int pcid 发布行程id
	 * @return string departure 出发地
	 * @return string address1 出发地地址
	 * @return string destination 目的地
	 * @return string address2 目的地地址
	 * @return string starttime 出发时间
	 * @return string peoplenum 车位数
	 * @return string price 价格
	 * @return string remark 备注
	 */
	public function editpublish(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        $model=new CheckAuth();
        $state=$model->check($this->uid);
        if($state==1){
        	//未认证
        	$rs['code']=0;
            $rs['msg']='您需要进行车主认证！';
            $rs['info']= array();
            return $rs;
        }else{
        	$domain = new DomainOwner();
	        $result = $domain->editpublish($this->uid,$this->pcid);
	        if (!empty($result)) {
	            $rs['code']=1;
	            $rs['msg']='获取成功！';
	            $rs['info']= $result;
	            return $rs;
	        }else{
	            $rs['code']=0;
	            $rs['msg']='获取失败！';
	            $rs['info']= array();
	            return $rs;
	        }
        }
	}
}
