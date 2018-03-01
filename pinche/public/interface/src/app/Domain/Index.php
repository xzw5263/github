<?php
namespace App\Domain;

use App\Model\User as ModelUser;
use App\Model\Auth as ModelAuth;
use App\Model\Notice as ModelNotice;
use App\Model\Cardauth as ModelCardauth;
use App\Common\Common as Commons;
use PhalApi\Cache\RedisCache as Redis;
class Index {
    public function Login($mobile,$captcha) {
        $model = new ModelUser();
        $total = $model->login($mobile,$captcha);
        return $total;
    }

    public function getcity(){
        $model = new ModelUser();
        return $model->getcity();
    }

    public function logins($mobile,$password) {
        $model = new ModelUser();
        $total = $model->logins($mobile,$password);
        return $total;
    }

    public function register($mobile,$password,$captcha){
        $model = new ModelUser();
        $total = $model->register($mobile,$password,$captcha);
        return $total;
    }

     private function curl_post($url, $post_arr, $referer = '')
    {
        $post_str = '';
        foreach ($post_arr as $k => $v) {
            $post_str .= $k . '=' . $v . '&';
        }
        $post_str = substr($post_str, 0, - 1);
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url); // 要访问的地址 即要登录的地址页面
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false); // 对认证证书来源的检查
        curl_setopt($curl, CURLOPT_POST, 1); // 发送一个常规的Post请求
        curl_setopt($curl, CURLOPT_POSTFIELDS, $post_str); // Post提交的数据包
        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, 0); // 使用自动跳转
        curl_setopt($curl, CURLOPT_REFERER, $referer); // 设置Referer
        // curl_setopt ( $curl, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 5.1; rv:9.0.1) Gecko/20100101 Firefox/9.0.1" ); // 模拟用户使用的浏览器
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1); // 获取的信息以文件流的形式返回
        curl_setopt($curl, CURLOPT_HEADER, false); // 获取header信息
        curl_setopt($curl, CURLOPT_TIMEOUT, 30); // 只需要设置一个秒的数量就可以
        $result = curl_exec($curl);
        return $result;
    }
    
    public function getVerifyCode($moblie,$type)
    {
        $rand = mt_rand(100000, 999999);
        if($type==1){
            $content = '【欢迎拼搭】您的注册码为：' . $rand.'，请妥善保存，请勿泄露。'; // 短信内容
        }else if($type == 2){
            $content = '【欢迎拼搭】您正在修改手机号，验证码为：' . $rand.'，请妥善保存，请勿泄露。'; // 短信内容
        }else{
            $content = '【欢迎拼搭】您正在找回密码，验证码为：' . $rand.'，请妥善保存，请勿泄露。'; // 短信内容
        }
        
        $postData = array(
            "accountname" => "sdkwhmaifb",
            "accountpwd" => "244456",
            "mobilecodes" => $moblie,
            "msgcontent" => $content
        );
        $result['rand'] = $rand;

        //$di->cache->set($mobile,$rand);
        $result['re'] = $this->curl_post("http://csdk.zzwhxx.com:8002/submitsms.aspx", $postData);
		session_start();
        if($type==1){
            $_SESSION["mobile"]=$rand;
        }else if($type == 2){
            $_SESSION["change"]=$rand;
        }else{
            $_SESSION['find']=$rand;
        }
		
        return $result;
    }

    public function authentication($uid,$licencepic,$driving,$cartype,$licence,$color){
        $model = new ModelAuth();
        $result = $model->authentication($uid,$licencepic,$driving,$cartype,$licence,$color);
        return $result;
    }

    public function suggest($lng1,$lat1,$lng2,$lat2,$num,$type,$iscity){
        $model=new Commons();
        //$result= $model->calculate1($lng1,$lat1,$lng2,$lat2);
        $result= $model->calculate($lng1,$lat1,$lng2,$lat2,$num,$type,$iscity);
        //$distance = sprintf("%2.f", $result * 0.612);
        return $result;
    }

    public function idcardauth($uid,$realname,$idcard,$picture1,$picture2){
        $model = new ModelCardauth();
        return $model->cardauth($uid,$realname,$idcard,$picture1,$picture2);
    }

    public function notice($uid,$type,$page){
        $model = new ModelNotice();
        return $model->getnotice($uid,$type,$page);
    }
	
	public function readnotice($id){
        $model = new ModelNotice();
        return $model->readnotice($id);
    }

    public function editavatar($uid,$avatar){
        $model = new ModelUser();
        return $model->editavatar($uid,$avatar);
    }

    public function editnick($uid,$nick){
        $model = new ModelUser();
        return $model->editnick($uid,$nick);
    }

    public function editsex($uid,$sex){
        $model = new ModelUser();
        return $model->editsex($uid,$sex);
    }

    public function editpass($uid,$oldpass,$newpass){
        $model = new ModelUser();
        return $model->editpass($uid,$oldpass,$newpass);
    }

    public function changemobile($uid,$mobile,$captcha){
        $model = new ModelUser();
        return $model->changemobile($uid,$mobile,$captcha);
    }

    public function findpass($mobile,$password,$captcha){
        $model = new ModelUser();
        return $model->findpass($mobile,$password,$captcha);
    }

    public function getcarbrand(){
        $model = new ModelNotice();
        return $model->getcarbrand();
    }

    public function getcartype($id){
        $model = new ModelNotice();
        return $model->getcartype($id);
    }

    public function getuserinfo($uid){
        $model = new ModelUser();
        return $model->getuserinfo($uid);
    }

    public function changetype($uid){
        $model = new ModelUser();
        return $model->changetype($uid);
    }

    public function canceldutyrule(){
        $model = new ModelNotice();
        return $model->canceldutyrule();
    }

    public function deletenotice($id){
        $model = new ModelNotice();
        return $model->deletenotice($id);
    }

    public function getversion($uid,$type){
        $model = new ModelNotice();
        return $model->getversion($type);
    }
}
