<?php
namespace App\Model;

use App\Model\Publish as ModelPublish;
use App\Model\Notice as ModelNotice;
use App\Model\Choose as ModelChoose;
use App\Common\Common as Commons;
use PhalApi\Model\NotORMModel as NotORM;
class Passenger extends NotORM {
    const PAGESIZE=15;
    protected function getTableName($id) {
        return 'tzy_passengerpub';
    }

    public function mydetail($uid,$pid){
        //return $this->getORM()->where('pid',$pid)->select('id,departure,destination,address1,address2,num,remark,price')->fetchAll();
        $sql="SELECT
                a.id ppid,
                departure,
                destination,
                address1,
                address2,
                num,
                remark,
                price,
                nick,
                mobile,
                avatar,
                sex,
                credit
            FROM
                pc_tzy_passengerpub a
            LEFT JOIN pc_tzy_user b ON a.uid = b.id
            WHERE
                a.pid = :pid";
            $rows = $this->getORM()->queryAll($sql, array(':pid'=>$pid));
            return $rows;
    }

    public function publish($uid,$departure,$destination,$address1,$address2,$num,$remark,$lng1,$lat1,$lng2,$lat2,$iscity,$starttime,$endtime){
        $models = new Commons();
		$distance=$models->getdistance($lat1, $lng1, $lat2, $lng2);
		$data=array(
            'uid'=>$uid,
            'departure'=>$departure,
            'destination'=>$destination,
            'address1'=>$address1,
            'address2'=>$address2,
            'num'=>$num,
            'remark'=>$remark,
            'starttime'=>$starttime,
            'endtime'=>$endtime,
            'lng1'=>$lng1,
            'lat1'=>$lat1,
            'lng2'=>$lng2,
            'lat2'=>$lat2,
			'distance'=>$distance,
			'iscity'=>$iscity,
            'create_time'=>date('Y-m-d H:i:s',time())
        );
        $model = new ModelNotice();
        $str= date('Y-m-d H:i:s').' 用户id（乘客）:'.$uid.'发布了从：'.$departure.'到'.$destination.' 的需求';
        $model->writelog($str);
        return $this->getORM()->insert($data);
    }

    public function checkmyslists($uid,$pid){
        return $this->getORM()->where('id = ? and state < 8',$pid)->select('distance,num,remark,departure,destination,starttime,endtime,address1,address2')->fetchOne();
    }

    public function agree($uid,$pid){
        $data=array(
            'state'=>2
        );
        return $this->getORM()->where('id',$pid)->update($data);
    }
    
    public function reject($uid,$pid){
        $data=array(
            'state'=>3
        );
        //司机座位数量还原，
        $info=$this->getORM()->where('id',$pid)->select('pid,num')->fetchOne();
        $model = new ModelPublish();
        $model->resotrenum($info['pid'],$info['num']);
        return $this->getORM()->where('id',$pid)->update($data);
    }

    public function confirmserver($uid,$pid){
        $data=array(
            'state'=>4
        );
        return $this->getORM()->where('id',$pid)->update($data);
    }

    public function serverfinish($uid,$pid){
        $data=array(
            'state'=>5
        );
        return $this->getORM()->where('id',$pid)->update($data);
    }

    public function choose($uid,$pid,$cpid,$num,$price){
        //判断是否已经报过价了
        $sql = "select * from pc_tzy_choose where ppid = ? and pcid = ? ";
        $res = $this->getORM()->queryAll($sql,array($pid,$cpid));
        if(empty($res)){
            //判断座位是否够
            $info=$this->getORM()->where('id',$pid)->fetchOne();
            if($info['num'] > $num){
                $code = 4;
                return $code;
            }else{
                $sql = "select uid from pc_tzy_carpublish where id =".$cpid;
                $infos = $this->getORM()->queryAll($sql);
                $data['puid']=$uid;
                $data['cuid']=$infos[0]['uid'];
                $data['pcid']=$cpid;
                $data['ppid']=$pid;
                $data['state'] = '1';
                $data['price']=$price;
                $data['create_time']=date('Y-m-d H:i:s',time());
                $models=new ModelChoose();
                $re = $models->insertorder($data);
                //推送给车主：有用户来选择
                $datas=array(
                    'uid'=>$infos[0]['uid'],
                    'title'=>'新乘客加入！',
                    'notice'=>'您发布的行程有新的乘客加入，快去看看吧！',
                    'type'=>'1',
                    'create_time'=>date('Y-m-d H:i:s',time()),
                    'pcid'=>$cpid
                );
                $models = new ModelNotice();
                $res = $models->addPassengerNotice($datas);

                if($re){
                    //减少司机座位数量；
                    if($info['state'] ==0){
                        $this->getORM()->where('id',$pid)->update(array('state'=>'1'));
                    }
                    $model = new ModelPublish();
                    $model->reducenum($cpid,$info['num']);
                    $code = 1;
                }else{
                    $code = 2;
                }
            }
        }else{
            $code = 3 ;
        }
        
        return $code;
    }

    // public function cancelorder($uid,$pid,$reasoon,$remark){
    //     $data=array(
    //         'pid'=>0,
    //         'state'=>'0'
    //     );
    //     //新增一条数据（取消订单原因）;
    //     $sql="insert into pc_tzy_cancelorder(`uid`,`create_time`,`remark`,`reason`) VALUE(?,?,?,?)";
    //     $time=date('Y-m-d H:i:s',time());
    //     $param=array($uid,$time,$remark,$reason);
    //     $this->getORM()->queryAll($sql,$param);
    //     return $this->getORM()->where('id',$pid)->update($data);
    // }

    public function cancelorders($uid,$pid){
        $data=array(
            'pid'=>0,
            'state'=>'0'
        );
        return $this->getORM()->where('id',$pid)->update($data);
    }

    public function canceloffer($uid,$ppid){
        return $this->getORM()->where('id',$ppid)->delete();
    }

    public function getcarnum($pid){
        $num=$this->getORM()->where('id',$pid)->select('num')->fetchOne();
        return $num['num'];
    }

    public function mypublish($uid,$page){
        $start = ($page-1)*self::PAGESIZE;
        //return $this->getORM()->where('uid',$uid)->select('departure,destination,address1,address2,starttime,endtime')->fetchOne();
        $sql = "SELECT
                    a.id ppid,
                    departure,
                    destination,
                    address1,
                    address2,
                    starttime,
                    endtime,
                    a.state statss,
                    b.state,
                    count(b.id) nums,
                    price,
                    num,
                    remark,
                    a.create_time
                FROM
                    pc_tzy_passengerpub a
                LEFT JOIN pc_tzy_choose b ON a.id = b.ppid and b.state < 7
                WHERE
                    a.uid = ?
                GROUP BY
                    a.id
                ORDER BY create_time desc
                LIMIT $start,".self::PAGESIZE;
        $param = array($uid);
        $list = $this->getORM()->queryAll($sql,$param);
        // foreach ($list as $key => $value) {
        //     # code...
        //     if($value['nums'] > 0){
        //         if($value['state'] == 1){
        //             $list[$key]['status']=1;
        //         }else{
        //             $list[$key]['status']=2;
        //         }
        //     }else{
        //         $list[$key]['status']=0;
        //     }
        //     unset $list[$key]['state'];
        // }
        $arr=array();
        $count = count($list);
        foreach($list as $val){
            if($val['nums'] > 0){
                if($val['statss']==1){
                    $val['status']=1;
                }else{
                    $val['status']=2;
                }
            }else{
                $val['status']=0;
            }
            if($count == self::PAGESIZE){
                $val['page']=1;
            }else{
                $val['page']=0;
            }
            unset($val['state']);
            unset($val['statss']);
            $arr[]=$val;
        }
        return $arr;
    }
	
	public function suggest($ppid){
		$info = $this->getORM()->where('id',$ppid)->select('lng1,lat1,lng2,lat2,distance,num,type,iscity')->fetchOne();
		if(empty($info['distance'])){
			$models = new Commons();
			$distance=$models->getdistance($info['lat1'], $info['lng1'], $info['lat2'], $info['lng2']);
		}else{
			$distance=$info['distance'];
		}
		$model = new Commons();

		$infos=$model->calculateprice($distance,$info['num'],$info['type'],$info['iscity']);
		return $infos;
	}

    public function editpublish($uid,$ppid){
        return $this->getORM()->where('id',$ppid)->select('id ppid,departure,destination,address1,address2,starttime,endtime,price,num,remark')->fetchOne();
    }
}
