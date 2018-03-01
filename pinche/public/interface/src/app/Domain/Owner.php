<?php
namespace App\Domain;

use App\Model\Publish as ModelPublish;
use App\Model\Passenger as ModelPassenger;
use App\Model\Choose as ModelChoose;
class Owner {
    public function publish($uid,$departure,$destination,$address1,$address2,$distance,$peoplenum,$starttime,$remark,$price,$lng1,$lat1,$lng2,$lat2,$iscity) {
        $model = new ModelPublish();
        $result = $model->publish($uid,$departure,$destination,$address1,$address2,$distance,$peoplenum,$starttime,$remark,$price,$lng1,$lat1,$lng2,$lat2,$iscity);
        return $result;
    }

    public function mylists($uid,$page){
        $model= new ModelPublish();
        $result=$model->mylists($uid,$page);
        return $result;
    }

    public function mydetail($uid,$pcid){
        // $model= new ModelPassenger();
        // $result=$model->mydetail($uid,$pid);
        // return $result;
        $models = new ModelPublish();
        $info = $models->checkpublish($uid,$pcid);
        $model = new ModelChoose();
        $list = $model->mydetail($uid,$pcid);
        $data['info']=$info;
        $data['list']=$list;
        return $data;
    }

    public function agree($uid,$id){
        // $model= new ModelPassenger();
        $model = new ModelChoose();
        $result=$model->agree($uid,$id);
        return $result;
    }

    public function reject($uid,$id){
        // $model= new ModelPassenger();
        $model = new ModelChoose();
        $result=$model->reject($uid,$id);
        return $result;
    }

    public function confirmserver($uid,$id){
        // $model= new ModelPassenger();
        $model = new ModelChoose();
        $result=$model->confirmserver($uid,$id);
        return $result;
    }

    public function serverfinish($uid,$id){
        // $model= new ModelPassenger();
        $model = new ModelChoose();
        $result=$model->serverfinish($uid,$id);
        return $result;
    }

    public function canceloffer($uid,$pcid){
        $model= new ModelPublish();
        $result=$model->canceloffer($uid,$pcid);
        return $result;
    }

    public function cancelorder($uid,$pcid,$reason,$remark){
        $model= new ModelChoose();
        $result=$model->cancelorderss($uid,$pcid,$reason,$remark);
        return $result;
    }
	
	public function complain($uid,$ppid,$reason){
		return 1;
	}

    public function editpublish($uid,$pcid){
        $model = new ModelPublish();
        $result = $model->editpublish($uid,$pcid);
        return $result;
    }
}
