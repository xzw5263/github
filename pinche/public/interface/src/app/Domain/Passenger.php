<?php
namespace App\Domain;

use App\Model\Passenger as ModelPassenger;
use App\Model\Publish as ModelPublish;
use App\Model\Choose as ModelChoose;
class Passenger {
    public function publish($uid,$departure,$destination,$address1,$address2,$num,$remark,$lng1,$lat1,$lng2,$lat2,$iscity,$starttime,$endtime) {
        $model = new ModelPassenger();
        $result = $model->publish($uid,$departure,$destination,$address1,$address2,$num,$remark,$lng1,$lat1,$lng2,$lat2,$iscity,$starttime,$endtime);
        return $result;
    }

    public function recommend($uid,$ppid,$departure,$destination,$starttime,$endtime,$page){
        $model= new ModelPublish();
        $result=$model->passengerlists($uid,$ppid,$departure,$destination,$starttime,$endtime,$page);
        return $result;
    }

    public function mydetail($uid,$pid){
        $model= new ModelPassenger();
        $result=$model->mydetail($uid,$pid);
        return $result;
    }

    public function mylists($uid,$ppid){
        $model = new ModelChoose();
        $lists = $model->myslists($uid,$ppid);
        
        $models= new ModelPassenger();
        $info=$models->checkmyslists($uid,$ppid);
        $infos=$this->suggest($ppid);
        $info['price']=$infos['price'];
        $data['info']=$info;

		if(!empty($lists)){
			$infoss=$model->checkstatus($ppid);
			if($infoss['state'] == 1){
				$data['info']['state']=1;
			}else{
				$data['info']['state']=2;
			}
		}else{
			$data['info']['state']=0;
		}

        $data['lists']=$lists;
        return $data;
    }

    public function choose($uid,$ppid,$pcid,$price){
        //先判断司机的空位；
        $model= new ModelPublish();
        $num = $model->checknum($pcid);
        if($num < 1){
            $code = 0;
            return $code;
        }
        $models= new ModelPassenger();
        $result=$models->choose($uid,$ppid,$pcid,$num,$price);
        return $result;
    }

    public function cancelorder($uid,$ppid,$reason,$remark){
        // $models= new ModelPassenger();
        // $result=$models->cancelorder($uid,$id,$reason,$remark);
        $model = new ModelChoose();
        $result = $model->cancelorder($uid,$ppid,$reason,$remark);
        return $result;
    }

    public function cancelorders($uid,$id){
        $model = new ModelChoose();
        $result=$model->cancelorders($uid,$id);
        return $result;
    }

    public function canceloffer($uid,$ppid){
        $models= new ModelPassenger();
        $result=$models->canceloffer($uid,$ppid);
        return $result;
    }

    public function mypublish($uid,$page){
        $models= new ModelPassenger();
        $result=$models->mypublish($uid,$page);
        return $result;
    }

    public function deleteorder($uid,$id){
        $models= new ModelChoose();
        $result=$models->deleteorder($uid,$id);
        return $result;
    }
	
	public function confirmserver($uid,$id){
        $models= new ModelChoose();
        $result=$models->confirmserverss($uid,$id);
        return $result;
    }
	
	public function finishserver($uid,$id){
        $models= new ModelChoose();
        $result=$models->finishserver($uid,$id);
        return $result;
    }
	
	public function suggest($ppid){
		$model = new ModelPassenger();
		return $model->suggest($ppid);
	}
	
	public function complain($uid,$pcid,$reason){
		return 1;
	}

    public function editpublish($uid,$ppid){
        $model = new ModelPassenger();
        return $model->editpublish($uid,$ppid);
    }
}
