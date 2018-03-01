<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;
class Notice extends NotORM {
    const PAGESIZE=15;
    protected function getTableName($id) {
        return 'tzy_notice';
    }

    public function getnotice($uid,$type,$page){
        $start = ($page - 1)*self::PAGESIZE;
        $list = $this->getORM()->where('uid = ? and type = ?',$uid,$type)->limit($start,self::PAGESIZE)->fetchAll();
		$num = count($list);
		if($num > 0){
			if($num == self::PAGESIZE){
				$list[0]['page']=1;
			}else{
				$list[0]['page']=0;
			}
		}
		return $list;
    }
	
	public function readnotice($id){
		return $this->getORM()->where('id',$id)->update(array('code'=>'1'));
	}

    public function getcarbrand(){
    	$sql="select id,brand_name,picture,letter_index from pc_tzy_carbrand where pid = 'aaa'";
    	return $this->getORM()->queryAll($sql);
    }

    public function getcartype($id){
    	$sql="select id,brand_name,picture,letter_index from pc_tzy_carbrand where pid = ?";
    	$param=array($id);
    	return $this->getORM()->queryAll($sql,$param);
    }

    public function canceldutyrule(){
        $sql = "select content from pc_tzy_canceldutyrule where id = 1";
        return $this->getORM()->queryAll($sql);
    }

    public function deletenotice($id){
        return $this->getORM()->where('id',$id)->delete();
    }

    public function addPassengerNotice($data){
        //记得加推送
        
        return $this->getORM()->insert($data);
    }

    public function writelog($str){
        $month = date('ym');
        error_log($str,3,'./log/'.$month.'.log');
    }

    public function getversion($type){
        $sql = "select * from pc_tzy_version where type = ? order by create_time desc limit 0,1";
        return $this->getORM()->queryAll($sql,array($type));
    }
}
