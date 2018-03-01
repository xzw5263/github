<?php
namespace App\Api;

use App\Domain\Passenger as DomainPassenger;
use PhalApi\Api;

/**
 * 乘客 接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Passenger extends Api {

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
                'starttime'=>array('name'=>'starttime','type'=>'string','require'=>true,'desc'=>'出发时间'),
                'endtime'=>array('name'=>'endtime','type'=>'string','require'=>true,'desc'=>'结束时间'),
            	'lng1'=>array('name'=>'lng1','type'=>'string','require'=>true,'desc'=>'初始经度'),
                'lat1'=>array('name'=>'lat1','type'=>'string','require'=>true,'desc'=>'初始纬度'),
                'lng2'=>array('name'=>'lng2','type'=>'string','require'=>true,'desc'=>'结束经度'),
                'lat2'=>array('name'=>'lat2','type'=>'string','require'=>true,'desc'=>'结束纬度'),
            	'num'=>array('name'=>'num','type'=>'int','max'=>8,'require'=>true,'desc'=>'人数'),
            	'remark'=>array('name'=>'remark','type'=>'string','desc'=>'备注'),
                'iscity'=>array('name'=>'iscity','type'=>'int','default'=>'1','desc'=>'1、城际，2、市内')
            ),
            'recommend'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'ppid'=>array('name'=>'ppid','type'=>'int','require'=>true,'desc'=>'用户发布id'),
            	'departure'=>array('name' => 'departure','type'=>'string','require'=>true,'desc'=>'出发地城市'),
            	'destination'=>array('name'=>'destination','type'=>'string','require'=>true,'desc'=>'目的地城市'),
                'starttime'=>array('name'=>'starttime','type'=>'string','desc'=>'出发时间'),
				'endtime'=>array('name'=>'endtime','type'=>'string','desc'=>'出发时间'),
				'page'=>array('name'=>'page','type'=>'int','default'=>1,'desc'=>'页码')
            ),
            'mylists'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'ppid'=>array('name'=>'ppid','type'=>'int','require'=>true,'desc'=>'用户发布id')
            ),
            'choose'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
            	'ppid'=>array('name'=>'ppid','type'=>'int','require'=>true,'desc'=>'用户发布id'),
            	'pcid'=>array('name'=>'pcid','type'=>'int','require'=>true,'desc'=>'车主发布id'),
				'price'=>array('name'=>'price','type'=>'string','desc'=>'我的报价')
            ),
            'cancelorder'=>array(
            	'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'ppid'=>array('name'=>'ppid','type'=>'int','require'=>true,'desc'=>'用户发布id'),
                'reason'=>array('name'=>'reason','type'=>'string','require'=>true,'desc'=>'取消行程原因…'),
            	'remark'=>array('name'=>'remark','type'=>'string','desc'=>'备注')
            ),
            'cancelorders'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'id'=>array('name'=>'id','type'=>'int','require'=>true,'desc'=>'订单id')
            ),
            'canceloffer'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'ppid'=>array('name'=>'ppid','type'=>'int','require'=>true,'desc'=>'用户发布id'),
            ),
            'mypublish'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
				'page'=>array('name'=>'page','type'=>'int','default'=>1,'desc'=>'页码')
            ),
            'deleteorder'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'id'=>array('name'=>'id','type'=>'int','require'=>true,'desc'=>'订单id')
            ),
			'confirmserver'=>array(
				'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'id'=>array('name'=>'id','type'=>'int','require'=>true,'desc'=>'订单id')
			),
			'finishserver'=>array(
				'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'id'=>array('name'=>'id','type'=>'int','require'=>true,'desc'=>'订单id')
			),
			'suggest'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','desc'=>'用户id'),
            	'ppid'=>array('name'=>'ppid','type'=>'int','require'=>true,'desc'=>'用户发布id')
            ),
			'complain'=>array(
				'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
				'pcid'=>array('name'=>'pcid','type'=>'int','require'=>true,'desc'=>'车主发布id'),
				'reason'=>array('name'=>'reason','type'=>'string','desc'=>'投诉原因')
			),
            'editpublish'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','desc'=>'用户id'),
                'ppid'=>array('name'=>'ppid','type'=>'int','require'=>true,'desc'=>'用户发布id')
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
	 * 乘客 我的发布
	 * @desc 乘客 发布需求
	 * @return 返回成功或者失败
	 * @return 
	 */
	public function publish(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
    	$domain = new DomainPassenger();
        $result = $domain->publish($this->uid,$this->departure,$this->destination,$this->address1,$this->address2,$this->num,$this->remark,$this->lng1,$this->lat1,$this->lng2,$this->lat2,$this->iscity,$this->starttime,$this->endtime);
        if (!empty($result)) {
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

	/**
	 * 乘客 选择车主 接口
     * @desc 乘客 发布行程后 进入到的列表界面（推荐车主）
	 * @return int pcid  车主发布id
	 * @return string nick 昵称
	 * @return int sex 性别
	 * @return double credit 信用
	 * @return string licence 车牌号
	 * @return string cartype 车类型
	 * @return string avatar 头像
	 * @return string depature 出发城市
	 * @return string destination 目的城市
	 * @return string address1 出发城市地址
	 * @return string address2 目的城市地址
	 * @return string starttime 出发时间
	 * @return string peoplenum 座位数量
	 * @return string realnum 剩余数量
	 * @return string price 价格
     * @return string distance 车主与乘客的距离
     * @return string ordernums 车主接单数
	 * @return string page 页码 （1为还有下一页，0为没有下一页）
	 */
	public function recommend() {
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
    	$domain = new DomainPassenger();
        $result = $domain->recommend($this->uid,$this->ppid,$this->departure,$this->destination,$this->starttime,$this->endtime,$this->page);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='获取成功！';
            $rs['info']= $result;
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='暂时没有匹配到同行程车辆信息！';
            $rs['info']= array();
            return $rs;
        }
	}

    /**
     * 乘客 我的行程
     * @desc 乘客 查看我选择的司机
     * @return int id  订单id
     * @return string nick 昵称
     * @return int sex 性别
     * @return double credit 信用
     * @return string licence 车牌号
     * @return string cartype 车类型
     * @return string avatar 头像
     * @return string depature 出发城市
     * @return string destination 目的城市
     * @return string address1 出发城市地址
     * @return string address2 目的城市地址
     * @return string starttime 出发时间
     * @return string peoplenum 座位数量
     * @return string realnum 剩余数量
     * @return string price 价格
     * @return string state 返回状态（6、乘客确认上车，7、乘客确认到达，8、取消订单）
     */
    public function mylists() {
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
        $domain = new DomainPassenger();
        $result = $domain->mylists($this->uid,$this->ppid);
        if (!empty($result['lists'])) {
            $rs['code']=1;
            $rs['msg']='获取成功！';
            $rs['info'][]= $result;
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='暂时没有该行程车辆信息！';
            $rs['info']= $result;
            return $rs;
        }
    }

	/**
	 * 乘客 选他
	 * @desc 乘客 选他
	 * @return 返回成功或者失败
	 */
	public function choose(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
    	$domain = new DomainPassenger();
        $result = $domain->choose($this->uid,$this->ppid,$this->pcid,$this->price);
        if ($result == 1) {
            $rs['code']=1;
            $rs['msg']='申请成功，请等待司机回应~';
            $rs['info']= array();
            return $rs;
        }else if($result == 4){
            $rs['code']=0;
            $rs['msg']='很抱歉，座位数量不足！';
            $rs['info']= array();
            return $rs;
        }else if($result == 3){
        	$rs['code']=0;
            $rs['msg']='您已经报过价了';
            $rs['info']= array();
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='申请失败，请重新选择';
            $rs['info']= array();
            return $rs;
        }
	}

	/**
	 * 乘客 取消订单(需要填写取消原因)
	 * @desc 乘客 取消订单
	 * @return 返回成功或者失败
	 * @return 
	 */
	public function cancelorder(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
    	$domain = new DomainPassenger();
        $result = $domain->cancelorder($this->uid,$this->ppid,$this->reason,$this->remark);
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
     * 乘客 取消报价(不需要填写取消原因)
     * @desc 乘客 取消报价
     * @return 返回成功或者失败
     * @return 
     */
    public function cancelorders(){
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
        $domain = new DomainPassenger();
        $result = $domain->cancelorders($this->uid,$this->id);
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
     * 乘客 取消行程
     * @desc 乘客 取消行程
     * @return 返回成功或者失败
     * @return 
     */
    public function canceloffer(){
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
        $domain = new DomainPassenger();
        $result = $domain->canceloffer($this->uid,$this->ppid);
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
     * 乘客 删除订单
     * @desc 乘客 交易完成，乘客可以选删除订单
     * @return 返回成功或者失败
     * @return 
     */
    public function deleteorder(){
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
        $domain = new DomainPassenger();
        $result = $domain->deleteorder($this->uid,$this->id);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='删除成功！';
            $rs['info']= array();
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='删除失败';
            $rs['info']= array();
            return $rs;
        }
    }

    /**
     * 乘客 我的发布列表
     * @desc 首页展示的我的列表
     * @return string status （0：尚无车主、1：待选择、2：进行中）
     * @return int ppid 乘客发布id
     * @return string departure 出发地
     * @return string destination 目的地
     * @return string address1 出发地具体地址
     * @return string address2 目的地具体地址
     * @return string starttime 出发时间
     * @return string endtime 截止时间
     */
    public function mypublish() {
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
        $domain = new DomainPassenger();
        $result = $domain->mypublish($this->uid,$this->page);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='获取成功！';
            $rs['info']= $result;
            return $rs;
        }else{
            $rs['code']=1;
            $rs['msg']='获取失败';
            $rs['info']= array();
            return $rs;
        }
    }
	
	/**
     * 乘客 确认服务
     * @desc 乘客 确认上车接口
     * @return 返回成功或者失败
     * @return 
     */
    public function confirmserver(){
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
        $domain = new DomainPassenger();
        $result = $domain->confirmserver($this->uid,$this->id);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='确认成功！';
            $rs['info']= array();
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='确认失败';
            $rs['info']= array();
            return $rs;
        }
    }
	
	/**
     * 乘客 服务完成
     * @desc 乘客 服务完成接口
     * @return 返回成功或者失败
     * @return 
     */
    public function finishserver(){
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
        $domain = new DomainPassenger();
        $result = $domain->finishserver($this->uid,$this->id);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='删除成功！';
            $rs['info']= array();
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='删除失败';
            $rs['info']= array();
            return $rs;
        }
    }
	
	/**
	 * 乘客 建议价格 接口
	 * @desc 乘客 建议价格
	 * @return 返回内容
	 * @return string distance 距离
	 * @return string price 建议价格
	 */
	public function suggest(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
    	$domain = new DomainPassenger();
        $result = $domain->suggest($this->ppid);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='获取信息成功';
            $rs['info'][]= $result;
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='获取失败';
            $rs['info']= array(0);
            return $rs;
        }
	}
	
	/**
	 * 乘客 投诉接口
	 * @desc 乘客 投诉
	 * @return 返回投诉成功/失败
	 */
	public function complain(){
		$rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
		$domain = new DomainPassenger();
		$result = $domain->complain($this->uid,$this->pcid,$this->reason);
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

    /**
     * 乘客 编辑接口
     * @desc 乘客 重新发布
     * @return int ppid 发布行程id
     * @return string departure 出发地
     * @return string address1 出发地地址
     * @return string destination 目的地
     * @return string address2 目的地地址
     * @return string starttime 出发时间
     * @return string num 剩余车位数
     * @return string price 价格
     * @return string remark 备注
     */
    public function editpublish(){
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );
        
        $domain = new DomainPassenger();
        $result = $domain->editpublish($this->uid,$this->ppid);
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
