<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;
class Auth extends NotORM {

    protected function getTableName($id) {
        return 'tzy_authentication';
    }

    public function getListItems($state, $page, $perpage) {
        return $this->getORM()
            ->select('*')
            ->where('state', $state)
            ->order('post_date DESC')
            ->limit(($page - 1) * $perpage, $perpage)
            ->fetchAll();
    }

    public function authentication($uid,$licencepic,$driving,$cartype,$licence,$color) {
        //判断是否在申请了，已申请和同意申请，不能再次提交，拒绝可以再次提交
        $info=$this->getORM()->where('uid',$uid)->select('id,state,drivinglicence,licencepicture')->fetchOne();
        $uploadFolder = sprintf('%s/public/uploads/', API_ROOT);
        if(!empty($info)){
            if($info['state']==1){
               // unlink($uploadFolder.$licencepic);
               // unlink($uploadFolder.$driving);
                $code=1;
            }else if($info['state']==2){
               // unlink($uploadFolder.$licencepic);
               // unlink($uploadFolder.$driving);
                $code=2;
            }else{
                //之前的删除，再重新添加新的数据
                //$re=$this->getORM()->where('id',$info['id'])->delete();
                //删除图片
               // unlink($uploadFolder.$info['drivinglicence']);
               // unlink($uploadFolder.$info['licencepicture']);
                //unlink()
                $data=array(
                    'licencepicture'=>$licencepic,
                    'drivinglicence'=>$driving,
                    'cartype'=>$cartype,
                    'licence'=>$licence,
                    'color'=>$color,
                    'create_time'=>date('Y-m-d H:i:s',time())
                );    
                $res = $this->getORM()->where('id',$info['id'])->update($data);
                if($res){
                    $sql = "update pc_tzy_user set type = '2' where id =?";
                    $param = array($uid);
                    $this->getORM()->queryAll($sql);
                    $code=0;
                }else{
                    $code=4;
                }
            }
        }else{
            $data=array(
                'uid'=>$uid,
                'licencepicture'=>$licencepic,
                'drivinglicence'=>$driving,
                'cartype'=>$cartype,
                'licence'=>$licence,
                'color'=>$color,
                'create_time'=>date('Y-m-d H:i:s',time())
            );    
            $res = $this->getORM()->insert($data);
            if($res){
                $code=0;
            }else{
                $code=4;
            }
        }
        return $code;
    }
}
