<?php
namespace App\Common;

use App\Model\User as ModelUser;
class CheckAuth{

	public function check($uid){
		$model= new ModelUser();
		$info=$model->checkAuth($uid);
		return $info;
	}

}
