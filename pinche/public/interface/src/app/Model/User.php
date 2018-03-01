<?php
namespace App\Model;

use App\Model\Notice as ModelNotice;
use PhalApi\Model\NotORMModel as NotORM;
class User extends NotORM {

    protected function getTableName($id) {
        return 'tzy_user';
    }

    public function getListItems($state, $page, $perpage) {
        return $this->getORM()
            ->select('*')
            ->where('state', $state)
            ->order('post_date DESC')
            ->limit(($page - 1) * $perpage, $perpage)
            ->fetchAll();
    }

    public function register($mobile,$password,$captcha){
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
        $nick='';
        for($i=0;$i<8;$i++){
            $nick.=substr(str_shuffle($chars),0,1);
        }
        $res=array();
        $re=$this->getORM()->where('mobile',$mobile)->fetchOne();
        if(!$re){
            session_start();
            //$rand=$_SESSION['mobile'];
            //if($rand != $captcha){
                $data['mobile']=$mobile;
                $data['password']=md5(md5($password));
                $data['create_time']=date('Y:m:d H:i:s',time());
                $data['token']=md5(time());
                $data['state']='1';
                $data['nick']=$nick;
                $data['avatar']='avatar/avatarstzy123.jpg';
                $res = $this->getORM()->insert($data);
                if($res){
                    unset($res['password']);
                    $res['code']=1;
                }else{
                    $res['code']=0;
                }
           // }else{
            //    $res['code']=3;
            //}
            
        }else{
            $res['code'] = 2;
        }
        return $res;
    }

    public function login($mobile,$capthca) {
        //$total = $this->getORM()  ->where('state', $state) ->count('id');
        $re=$this->getORM()->where('mobile',$mobile)->fetchOne();
        if($re){
            session_start();
            $rand=$_SESSION['mobile'];
            if($capthca != $rand){
                $data['code']=1;
                $data['info']=array();
            }else{

                $data['code']=0;
                $data['info']=$re;
            }
        }else{
            //未注册
            $data['code']=2;
            $data['info']=array();
        } 
        return $data;       
    }

    public function logins($mobile,$pass) {
        //$total = $this->getORM()  ->where('state', $state) ->count('id');
        $info=$this->getORM()->where('mobile',$mobile)->select('id,password,nick,mobile,state')->fetchOne();
        if($info){
            $password=md5(md5($pass));
            if($password != $info['password']){
                $data['code']=1;
                $data['info']=array();
            }else{
                unset($info['password']);

                $model = new ModelNotice();
                $str= date('Y-m-d H:i:s').' 用户'.$mobile.'登录系统';
                $model->writelog($str);

                $data['code']=0;
                $data['info']=$info;
            }
        }else{
            //未注册
            $data['code']=2;
            $data['info']=array();
        } 
        return $data;       
    }

    public function checkAuth($uid){
        $info=$this->getORM()->where('id',$uid)->select('state')->fetchOne();
        return $info['state'];
    }

    public function editavatar($uid,$avatar){
        $data['avatar']=$avatar;
        return $this->getORM()->where('id',$uid)->update($data);
    }

    public function editnick($uid,$nick){
        $data['nick']=$nick;
        return $this->getORM()->where('id',$uid)->update($data);
    }

    public function editsex($uid,$sex){
        $data['sex']=$sex;
        return $this->getORM()->where('id',$uid)->update($data);
    }

    public function editpass($uid,$oldpass,$newpass){
        $old=md5(md5($oldpass));
        $new=md5(md5($newpass));
        $info=$this->getORM()->where('id',$uid)->select('password')->fetchOne();
        if($info['password'] != $old){
            return $code = 2;
        }else{
            return $this->getORM()->where('id',$uid)->update(array('password'=>$new));
        }
    }

    public function changemobile($uid,$mobile,$capthca){
        session_start();
        $rand=$_SESSION['change'];
        if($rand != $capthca){
            return $code=2;
        }else{
            $re= $this->getORM()->where('mobile',$mobile)->fetchOne();
            if($re){
                return $code = 3;
            }else{
                $data['mobile']=$mobile;
                return $this->getORM()->where('id',$uid)->update($data);
            }
        }
    }

    public function findpass($mobile,$password,$captcha){
        session_start();
        $rand=$_SESSION['find'];
        if($rand == $captcha){
            $data['password']=md5(md5($password));
            return $this->getORM()->where('mobile',$mobile)->update($data);
        }else{
            return $code=2;
        }
    }

    public function getcity(){
        $sql="select cityID,city,f_py from pc_tzy_city";
        return $this->getORM()->queryAll($sql);
    }

    public function getuserinfo($uid){
        $info = $this->getORM()->where('id',$uid)->fetchOne();
        unset($info['password']);
        $sql = "select * from pc_tzy_authentication where uid = ?";
        $sql1 = "select * from pc_tzy_cardauth where uid = ?";
        $param = array($uid);
        $info1=$this->getORM()->queryAll($sql,$param);
        $info2=$this->getORM()->queryAll($sql1,$param);
		$info['avatar']="http://pinche.trackline.cn/interface/public/uploads/".$info['avatar'];
		if(!empty($info2)){
			$info2[0]['picture1']="http://pinche.trackline.cn/interface/public/uploads/".$info2[0]['picture1'];
			$info2[0]['picture2']="http://pinche.trackline.cn/interface/public/uploads/".$info2[0]['picture2'];	
		}
		if(!empty($info1)){
			$info1[0]['drivinglicence']="http://pinche.trackline.cn/interface/public/uploads/".$info1[0]['drivinglicence'];
			$info1[0]['licencepicture']="http://pinche.trackline.cn/interface/public/uploads/".$info1[0]['licencepicture'];
		}
        $data['info']=$info;
		
        $data['cardauth']=$info1;
        $data['authentication']=$info2;
        return $data;
    }

    public function changetype($uid){
        $info= $this->getORM()->where('id',$uid)->select('type')->fetchOne();
        if($info['type'] == 1){
            $this->getORM()->where('id',$uid)->update(array('type'=>'2'));
            $code = 2;
        }else{
            $this->getORM()->where('id',$uid)->update(array('type'=>'1'));
            $code = 1;
        }
        return $code;
    }
}
