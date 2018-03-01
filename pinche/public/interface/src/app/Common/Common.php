<?php
namespace App\Common;
class Object{
	public $result;
}
class Common{

  /*
   * $lat1, $lon1: 第一个点的经纬度
   * $lat2, $lon2: 第二个点的经纬度
   * $radius: 可选，默认为地球的半径
   */
  function calculate($lng1, $lat1, $lng2, $lat2, $num,$type,$iscity) {
    $num = 1;
  	$EARTH_RADIUS = 6370.996; // 地球半径系数
    $PI = 3.1415926;

    $radLat1 = $lat1 * $PI / 180.0;
    $radLat2 = $lat2 * $PI / 180.0;

    $radLng1 = $lng1 * $PI / 180.0;
    $radLng2 = $lng2 * $PI /180.0;

    $a = $radLat1 - $radLat2;
    $b = $radLng1 - $radLng2;

    $distance = 2 * asin(sqrt(pow(sin($a/2),2) + cos($radLat1) * cos($radLat2) * pow(sin($b/2),2)));
    $distance = $distance * $EARTH_RADIUS * 1000;

    $distance = $distance / 1000;

    $distances = round($distance, 2);
    $price=0.0;
    if($distance < 30){//行程不超过30公里
      if($type == 1){//拼车
        
          $price=0.96*$distances*$num;
        
      }else{//不拼车
          $price=1.12*$distances*$num;
      }
    }else if($distance < 100 && $distance >= 30){
      if($type == 1){//拼车
          $price=0.44*$distances*$num;
      }else{//不拼车
          $price=1.2*$distances*$num;
      }
    }else if($distance < 300 && $distance >= 100){
      if($type == 1){//拼车
          $price=0.4*$distances*$num;
      }else{//不拼车
          $price=1.04*$distances*$num;
      }
    }else{
      if($type == 1){//拼车
          $price=0.32*$distances*$num;
      }else{//不拼车
          $price=0.96*$distances*$num;
      }
    }

    $data['distance']=sprintf('%.1f', $distances);
    $data['price']=sprintf('%.1f', $price);
    return $data;
  }

  
  //调用百度地图api
  public function calculate1($lng1, $lat1, $lng2, $lat2){
  	//http://api.map.baidu.com/direction/v2/driving?origin=40.01116,116.339303&destination=39.936404,116.452562&ak=您的ak
	/* $url = "http://api.map.baidu.com/direction/v2/driving";
	 $data=array(
	 	'origin'=>$lng1.','.$lat1,
	 	'destination'=>$lng2.','.$lat2,
	 	'ak'=>'wHKCXeieSkK4ZR1w9LGsuAt4K2bbKnme'
	 );
	 $distance = $this->curl_post($url,$data);
	 var_dump($distance);exit;
	 */
	 $distance=file_get_contents("http://api.map.baidu.com/direction/v2/driving?origin=$lat1,$lng1&destination=$lat2,$lng2&ak=wHKCXeieSkK4ZR1w9LGsuAt4K2bbKnme");
	 $arr = json_decode($distance);
   $distance = $arr->result->routes[0]->distance;

	 
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

    function getdistance($lat1, $lng1, $lat2, $lng2){   
      $earthRadius = 6367000; //approximate radius of earth in meters   
      $lat1 = ($lat1 * pi() ) / 180;   
      $lng1 = ($lng1 * pi() ) / 180;   
      $lat2 = ($lat2 * pi() ) / 180;   
      $lng2 = ($lng2 * pi() ) / 180;   
      $calcLongitude = $lng2 - $lng1;   
      $calcLatitude = $lat2 - $lat1;   
      $stepOne = pow(sin($calcLatitude / 2), 2) + cos($lat1) * cos($lat2) * pow(sin($calcLongitude / 2), 2);   
      $stepTwo = 2 * asin(min(1, sqrt($stepOne)));   
      $calculatedDistance = $earthRadius * $stepTwo;   
      return round($calculatedDistance)/1000;   
    }   
	
	function calculateprice($distance,$num,$type,$iscity){
    $num = 1;
		$distances = round($distance, 2);
		if($distance < 30){//行程不超过30公里
      if($type == 1){//拼车
        
          $price=0.96*$distances*$num;
        
      }else{//不拼车
          $price=1.12*$distances*$num;
      }
    }else if($distance < 100 && $distance >= 30){
      if($type == 1){//拼车
          $price=0.44*$distances*$num;
      }else{//不拼车
          $price=1.2*$distances*$num;
      }
    }else if($distance < 300 && $distance >= 100){
      if($type == 1){//拼车
          $price=0.4*$distances*$num;
      }else{//不拼车
          $price=1.04*$distances*$num;
      }
    }else{
      if($type == 1){//拼车
          $price=0.32*$distances*$num;
      }else{//不拼车
          $price=0.96*$distances*$num;
      }
    }
		
		$data['distance']=sprintf('%.1f', $distances);
    $data['price']=sprintf('%.1f', $price);
		return $data;
	}
}
