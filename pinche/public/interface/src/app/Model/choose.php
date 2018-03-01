<?php
namespace App\Model;

use App\Model\Publish as ModelPublish;
use App\Model\Passenger as ModelPassenger;
use App\Model\Notice as ModelNotice;
use App\Common\Common as Commons;
use PhalApi\Model\NotORMModel as NotORM;
class Choose extends NotORM {

    protected function getTableName($id) {
        return 'tzy_choose';
    }

    public function insertorder($data){
        return $this->getORM()->insert($data);
    }

    public function myslists($uid,$ppid){
        $sql1 = "select lng1,lat1 from pc_tzy_passengerpub where id = ?";
        $info = $this->getORM()->queryAll($sql1,array($ppid));
        $lng1=$info[0]['lng1'];
        $lat1=$info[0]['lat1'];
    	$sql="SELECT
				a.id,a.pcid,a.ppid,a.state,b.uid,b.lng1,b.lat1,avatar,nick,sex,credit,licence,cartype,mobile,color,departure,destination,address1,address2,starttime,peoplenum,realnum,remark,a.price,a.create_time
			FROM
				pc_tzy_choose a
			LEFT JOIN pc_tzy_carpublish b ON a.pcid = b.id
			LEFT JOIN pc_tzy_user c ON c.id = b.uid
			LEFT JOIN pc_tzy_authentication d on d.uid = c.id
			where a.ppid= ?
            ORDER BY create_time desc";
			$param=array($ppid);
			$list = $this->getORM()->queryAll($sql,$param);
            $arr=array();
            foreach($list as $val){
                $numss=$this->getORM()->select('count(*) count')->where('puid = ? and state = 7',$val['uid'])->fetchOne();
                $common = new Commons();
                $val['distance']=$common->getdistance($lat1,$lng1,$val['lat1'],$val['lng1']);
                unset($val['lng1']);
                unset($val['lat1']);
                unset($val['uid']);
                $val['ordernums']=$numss['count'];
                $arr[]=$val;
            }
            return $arr;
    }

    public function mydetail($uid,$pcid){
        $sql1 = "select lng1,lat1 from pc_tzy_carpublish where id = ?";
        $info = $this->getORM()->queryAll($sql1,array($pcid));
        $lng1=$info[0]['lng1'];
        $lat1=$info[0]['lat1'];
    	$sql="SELECT
				a.id,a.pcid,a.ppid,nick,sex,mobile,credit,avatar,licence,cartype,departure,destination,address1,address2,starttime,num,remark,a.price,a.state,b.uid,b.lng1,b.lat1,a.create_time
			FROM
				pc_tzy_choose a
			LEFT JOIN pc_tzy_passengerpub b ON a.ppid = b.id
			LEFT JOIN pc_tzy_user c on c.id = b.uid
            LEFT JOIN pc_tzy_authentication d on d.uid = c.id
			WHERE
				a.pcid = ?
            ORDER BY create_time desc";
		$param=array($pcid);
		$list = $this->getORM()->queryAll($sql,$param);
        $arr=array();
        foreach ($list as $key => $val) {
            $numss=$this->getORM()->select('count(*) count')->where('puid = ? and state = 7',$val['uid'])->fetchOne();
            $common = new Commons();
            $val['distance']=$common->getdistance($lat1,$lng1,$val['lat1'],$val['lng1']);
            unset($val['lng1']);
            unset($val['lat1']);
            unset($val['uid']);
            $val['ordernums']=$numss['count'];
            $arr[]=$val;
        }
        return $arr;
    }

    public function agree($uid,$id){
        $re = $this->getORM()->where('id',$id)->update(array('state'=>2));
        if($re){
            $info = $this->getORM()->where('id',$id)->select('ppid,puid')->fetchOne();
            //推送给乘客，已经同意了请求
            $data=array(
                'uid'=>$info['puid'],
                'title'=>'车主已同意',
                'notice'=>'您选择的车主已同意了您的请求，记得按时到达哦！',
                'type'=>'2',
                'create_time'=>date('Y-m-d H:i:s',time()),
                'ppid'=>$info['ppid']
            );
            $model = new ModelNotice();
            $res = $model->addPassengerNotice($data);
            //删除乘客选择的其他的车主（还要把选择的其他车主车位归还）
            $list = $this->getORM()->where('ppid = ? and id != ?',$info['ppid'],$id)->fetchAll();
            if(!empty($list)){
                $sql = "select num from pc_tzy_passengerpub where id =?";
                $infos = $this->getORM()->queryAll($sql,array($info['ppid']));
                $num = $infos[0]['num'];
                foreach($list as $val){
                    $sqls = "update pc_tzy_carpublish set realnum = realnum + ? where id = ?";
                    $red = $this->getORM()->queryAll($sqls,array($num,$val['pcid']));
                    $this->getORM()->where('id',$val['id'])->delete();
                }
            }
            
        }
        return $re;
    }

    public function reject($uid,$id){
        $info=$this->getORM()->where('id',$id)->fetchOne();
        $ppid=$info['ppid'];
        $pcid=$info['pcid'];
        $data=array(
            'state'=>3
        );
        //司机座位数量还原，
        $models=new ModelPassenger();
        //$info=$this->getORM()->where('id',$pid)->select('pid,num')->fetchOne();
        $num = $models->getcarnum($ppid);
        
        $re = $this->getORM()->where('id = ?',$id)->update($data);
        if($re){
            //推送给乘客，已经拒绝了请求
            $data=array(
                'uid'=>$info['puid'],
                'title'=>'车主已拒绝',
                'notice'=>'您选择的车主已拒绝了您的请求，请选择其他车主！',
                'type'=>'2',
                'create_time'=>date('Y-m-d H:i:s',time()),
                'ppid'=>$ppid
            );
            $models = new ModelNotice();
            $res = $models->addPassengerNotice($data);
        	$model = new ModelPublish();
        	$model->resotrenum($pcid,$num);
        }
        return $re;
    }

    public function confirmserver($uid,$id){
        //判断是否当天操作
        $info = $this->getORM()->where('id',$id)->fetchOne();
        $model = new ModelPublish();
        $res = $model->checkdates($info['pcid']);
        if($res){
            $re = $this->getORM()->where('id = ?',$id)->update(array('state'=>4));
            if($re){
                $code = 1;
            }else{
                $code = 0;
            }
        }else{
            $code =2;
        }
        
        return $code;
    }

    public function serverfinish($uid,$id){
        return $this->getORM()->where('id = ?',$id)->update(array('state'=>5));
    }

    public function cancelorder($uid,$ppid,$reason,$remark){
        //新增一条数据（取消订单原因）;
        $sql="insert into pc_tzy_cancelorder(`uid`,`create_time`,`remark`,`reason`) VALUE(?,?,?,?)";
        $time=date('Y-m-d H:i:s',time());
        $param=array($uid,$time,$remark,$reason);
        $this->getORM()->queryAll($sql,$param);

        // $sqls = "update pc_tzy_passengerpub set state = '8' where id = ?";
        // $this->getORM()->queryAll($sqls,array($ppid));
        //return $this->getORM()->where('id',$id)->delete();
        return $this->getORM()->where('ppid',$ppid)->update(array('state'=>8));
    }

    public function cancelorderss($uid,$pcid,$reason,$remark){
        //新增一条数据（取消订单原因）;
        $sql="insert into pc_tzy_cancelorder(`uid`,`create_time`,`remark`,`reason`) VALUE(?,?,?,?)";
        $time=date('Y-m-d H:i:s',time());
        $param=array($uid,$time,$remark,$reason);
        $this->getORM()->queryAll($sql,$param);

        // $sqls = "delete from pc_tzy_carpublish where id = ?";
        // $this->getORM()->queryAll($sqls,array($pcid));
        //return $this->getORM()->where('id',$id)->delete();
        return $this->getORM()->where('pcid',$pcid)->update(array('state'=>8));
    }

    public function cancelorders($uid,$id){
        return $this->getORM()->where('id',$id)->delete();
    }

    public function deleteorder($uid,$id){
        return $this->getORM()->where('id = ?',$id)->delete();
    }
	
	public function confirmserverss($uid,$id){
		return $this->getORM()->where('id',$id)->update(array('state'=>6));
	}
	
	public function finishserver($uid,$id){
		return $this->getORM()->where('id',$id)->update(array('state'=>7));
	}
	
	public function checkstatus($ppid){
		return $this->getORM()->where('ppid',$ppid)->select('state')->fetchOne();
	}
}
