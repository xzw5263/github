<?php
namespace App\Model;

use App\Common\Common as Commons;
use App\Model\Notice as ModelNotice;
use PhalApi\Model\NotORMModel as NotORM;
class Publish extends NotORM {
	const PAGESIZE=15;
    protected function getTableName($id) {
        return 'tzy_carpublish';
    }

    public function publish($uid,$departure,$destination,$address1,$address2,$distance,$peoplenum,$starttime,$remark,$price,$lng1,$lat1,$lng2,$lat2,$iscity){
            $data=array(
                'uid'=>$uid,
                'departure'=>$departure,
                'destination'=>$destination,
                'address1'=>$address1,
                'address2'=>$address2,
                'distance'=>$distance,
                'peoplenum'=>$peoplenum,
                'starttime'=>$starttime,
                'remark'=>$remark,
                'price'=>$price,
                'realnum'=>$peoplenum,
                'create_time'=>date('Y-m-d H:i:s',time()),
                'lng1'=>$lng1,
                'lat1'=>$lat1,
                'lng2'=>$lng2,
                'lat2'=>$lat2
            );
            $model = new ModelNotice();
            $str= date('Y-m-d H:i:s').' 用户id（车主）:'.$uid.'发布了从：'.$departure.'到'.$destination.' 的需求';
            $model->writelog($str);
            return $this->getORM()->insert($data);
    }

    public function mylists($uid,$page){
        //$list=$this->getORM()->where('uid',$uid)->select('id pcid,departure,destination,address2,address1,starttime,realnum')->fetchAll();
        $start = ($page-1)*self::PAGESIZE;
        $sql = "SELECT
                    a.id pcid,
                    departure,
                    destination,
                    address1,
                    address2,
                    starttime,
                    a.price,
                    remark,
                    b.state,
                    count(b.id) nums,
                    a.realnum,
                    a.create_time
            FROM
                    pc_tzy_carpublish a
            LEFT JOIN pc_tzy_choose b ON a.id = b.pcid and b.state < 7
            WHERE
                    a.uid = ?
            GROUP BY
                    a.id 
            ORDER BY create_time desc
            LIMIT $start,".self::PAGESIZE;
        $param = array($uid);
        $list = $this->getORM()->queryAll($sql,$param);
        foreach ($list as $key => $value) {
            # code...
            if($value['nums'] > 0){
                if($value['state'] == 1){
                    $list[$key]['status']=1;
                }else{
                    $list[$key]['status']=2;
                }
            }else{
                $list[$key]['status']=0;
            }
            //unset $list[$key]['state'];
        }
        return $list;
    }

    public function passengerlists($uid,$ppid,$departure,$destination,$starttime,$endtime,$page){
		$start = ($page-1)*self::PAGESIZE;
        $sql1 = "select lng1,lat1,num from pc_tzy_passengerpub where id =?";
        $info = $this->getORM()->queryAll($sql1,array($ppid));
        $sql="SELECT
                a.id pcid,
                a.uid,
                departure,
                destination,
                address1,
                address2,
                peoplenum,
                starttime,
                price,
                licence,
                cartype,
                realnum,
                nick,
                sex,
                mobile,
                avatar,
                lng1,
                lat1
            FROM
                pc_tzy_carpublish a
            LEFT JOIN pc_tzy_user b ON a.uid = b.id
            LEFT JOIN pc_tzy_authentication c on c.uid = b.id and c.is_default=1
            WHERE
                a.departure = ?
            AND a.destination = ?
            AND a.realnum >= ?
            AND a.uid != ?
            ORDER BY a.create_time desc 
			LIMIT $start,".self::PAGESIZE;
        $list=$this->getORM()->queryAll($sql,array($departure,$destination,$info[0]['num'],$uid));

        
        $lng1 = $info[0]['lng1'];
        $lat1 = $info[0]['lat1'];
        $num = count($list);
        $arr= array();

		if(!empty($list)){
            //加入距离与单数            
			foreach($list as $val){
                if($num < self::PAGESIZE){
                    $val['page'] = 0;
                }else{
                    $val['page'] = 1;
                }
                $val['ppid']=$ppid;

                $lng2=$val['lng1'];
                $lat2=$val['lat1'];

                $common = new Commons();
                $val['distance']=$common->getdistance($lat1,$lng1,$lat2,$lng2);
                $sql2 ="select count(*) count from pc_tzy_choose where cuid =? and state =7";
                $numss = $this->getORM()->queryAll($sql2,array($val['uid']));
                $val['ordernums']=$numss[0]['count'];
                unset($val['lat1']);
                unset($val['lng1']);
                unset($val['uid']);
                $arr[]=$val;
            }
		}
        return $arr;  
    }

    public function checknum($pid){
        $num = $this->getORM()->where('id',$pid)->select('realnum')->fetchOne();
        return $num['realnum'];
    }

    public function reducenum($pid,$num){
        //$info = $this->getORM()->where('id',$pid)->select('realnum')->fetchOne();
        //$data['realnum']=$info['realnum']-$num;
        //return $this->getORM()->where('id',$pid)->update($data);
        return $this->getORM()->where('id', $pid)->update(array('realnum' => new \NotORM_Literal("realnum - ".$num)));
    }

    public function resotrenum($pid,$num){
        return $this->getORM()->where('id', $pid)->update(array('realnum' => new \NotORM_Literal("realnum + ".$num)));
    }

    public function checkpublish($uid,$pcid){
        return $this->getORM()->where('id',$pcid)->select('distance,peoplenum,price,starttime,departure,destination,address1,address2,remark')->fetchOne();
    }

    public function canceloffer($uid,$pcid){
        return $this->getORM()->where('id',$pcid)->delete();
    }

    public function checkdates($pcid){
        $info = $this->getORM()->where('id',$pcid)->fetchOne();
        $day = date('m月d日',time());
        $days = mb_substr($info['starttime'],0,6);
        if($day == $days){
            return true;
        }else{
            return false;
        }
    }

    public function editpublish($uid,$pcid){
        return $this->getORM()->where('id',$pcid)->select('id pcid,departure,destination,address1,address2,price,starttime,remark,peoplenum')->fetchOne();
    }
}
