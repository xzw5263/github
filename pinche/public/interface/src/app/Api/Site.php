<?php
namespace App\Api;

use App\Domain\Index as DomainIndex;
use PhalApi\Api;

/**
 * 公共接口服务类
 *
 * @author: dogstar <chanzonghuang@gmail.com> 2014-10-04
 */

class Site extends Api {

	public function getRules() {
        return array(
            'canceldutyrule' => array(
            ),
			'uploadfile'=>array(
				'picture'=>array('name'=>'picture','type'=>'file','desc'=>'上传图片')
			),
            'editavatar'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'avatar'=>array('name'=>'avatar','type'=>'string','require'=>true,'desc'=>'上传头像')
             ),
            'authentication'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'licencepic'=>array('name'=>'licencepic','type'=>'string','desc'=>'驾照'),
                'driving'=>array('name'=>'driving','type'=>'string','desc'=>'行驶证'),
                'cartype'=>array('name'=>'cartype','type'=>'string','desc'=>'车型'),
                'licence'=>array('name'=>'licence','type'=>'string','desc'=>'车牌号'),
                'color'=>array('name'=>'color','type'=>'string','desc'=>'车颜色')
             ),
             'idcardauth'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'realname'=>array('name'=>'realname','type'=>'string','require'=>true,'desc'=>'真实姓名'),
                'idcard'=>array('name'=>'idcard','type'=>'string','max'=>'18','require'=>true,'desc'=>'身份证号'),
                'picture1'=>array('name'=>'picture1','type'=>'string','require'=>true,'desc'=>'身份证 正面'),
                'picture2'=>array('name'=>'picture2','type'=>'string','require'=>true,'desc'=>'身份证 反面'),
             ),
             'getversion'=>array(
                'uid'=>array('name'=>'uid','type'=>'int','require'=>true,'desc'=>'用户id'),
                'type'=>array('name'=>'type','type'=>'int','default'=>'1','desc'=>'1、安卓，2、苹果')
             )
        );
	}
	
	/**
	 * 取消责任规则接口
     * @desc 取消责任规则说明文档
	 * @return string content 内容
	 */
	public function canceldutyrule() {
        $rs=array(
            'code'=>1,
            'msg'=>'',
            'info'=>array(),
        );
        $domail=new DomainIndex();
        $result=$domail->canceldutyrule();
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='获取成功！';
            $rs['info']= $result;
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='获取失败~';
            $rs['info']= array();
            return $rs;
        }
	}
	
	/**
	 * 单独的上传图片返回路径
     * @desc 取消责任规则说明文档
	 * @return string content 内容
	 */
	public function uploadfile(){
		$rs=array(
            'code'=>1,
            'msg'=>'',
            'info'=>array(),
        );
		
		if(!empty($this->picture)){
			//var_dump($this->picture);
            $tmpName = $this->picture['tmp_name'];
            $name = md5($this->picture['name'] . $_SERVER['REQUEST_TIME']);
            $ext = strrchr($this->picture['name'], '.');
            if(empty($ext)){
                $ext='.jpg';
            }
            $uploadFolder = sprintf('%s/public/uploads/avatar/', API_ROOT);
            if (!is_dir($uploadFolder)) {
                mkdir($uploadFolder, 0777);
            }
            $imgPath = $uploadFolder .  $name . $ext;
            if (move_uploaded_file($tmpName, $imgPath)) {
                //$licencepic = sprintf('http://%s/phalapi/public/uploads/auth/%s%s', $_SERVER['SERVER_NAME'], $name, $ext);
                $rs['info'][]='avatar/'.$name.$ext;
            }
        }
		
		return $rs;
	}

    /**
     * 修改头像接口(安卓)
     * @desc 用户修改个人头像
     * @return string state 1、成功 0、失败
     */
    public function editavatar(){
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );

        $domain = new DomainIndex();
        $result = $domain->editavatar($this->uid,$this->avatar);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='上传成功！';
            $rs['info']= array();
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='上传失败~';
            $rs['info']= array();
            return $rs;
        }
    }

    /**
     * 车主 认证接口（安卓）
     * @desc 认证接口，车主认证时使用
     * @return string state 1、成功 0、失败
     */
    public function authentication(){
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );

        $domain = new DomainIndex();
        $result = $domain->authentication($this->uid,$this->licencepic,$this->driving,$this->cartype,$this->licence,$this->color);
        if ($result==0) {
            $rs['code']=1;
            $rs['msg']='认证申请成功，请耐心等待！';
            $rs['info']= $result;
            return $rs;
        }else if($result==1){
            $rs['code']=0;
            $rs['msg']='您正在申请认证，请勿重复提交';
            $rs['info']= array();
            return $rs;
        }else if($result==2){
            $rs['code']=0;
            $rs['msg']='您的认证申请通过！';
            $rs['info']= array();
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='认证申请提交失败~';
            $rs['info']= array();
            return $rs;
        }
    }

    /**
     * 实名认证接口（安卓）
     * @desc 认证接口，乘客认证时使用
     * @return string state 1、成功 0、失败
     */
    public function idcardauth(){
        $rs = array(
            'code' => 1,
            'msg' => '',
            'info' => array()
        );

        $domain = new DomainIndex();
        $result = $domain->idcardauth($this->uid,$this->realname,$this->idcard,$this->picture1,$this->picture2);
        if ($result==0) {
            $rs['code']=1;
            $rs['msg']='认证申请成功，请耐心等待！';
            $rs['info']= $result;
            return $rs;
        }else if($result==1){
            $rs['code']=0;
            $rs['msg']='您正在申请认证，请勿重复提交';
            $rs['info']= array();
            return $rs;
        }else if($result==2){
            $rs['code']=0;
            $rs['msg']='您的认证申请通过！';
            $rs['info']= array();
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='认证申请提交失败~';
            $rs['info']= array();
            return $rs;
        }
    }

    /**
     * 获取版本号接口
     * @desc 手机获取最新版本号
     * @return string content 内容
     */
    public function getversion() {
        $rs=array(
            'code'=>1,
            'msg'=>'',
            'info'=>array(),
        );
        $domail=new DomainIndex();
        $result=$domail->getversion($this->uid,$this->type);
        if (!empty($result)) {
            $rs['code']=1;
            $rs['msg']='获取成功！';
            $rs['info']= $result;
            return $rs;
        }else{
            $rs['code']=0;
            $rs['msg']='获取失败~';
            $rs['info']= array();
            return $rs;
        }
    }
}
