<?php
namespace App\Model;

use PhalApi\Model\NotORMModel as NotORM;
class Cardauth extends NotORM {

    protected function getTableName($id) {
        return 'tzy_cardauth';
    }

    public function cardauth($uid,$realname,$idcard,$picture1,$picture2) {
        //判断是否在申请了，已申请和同意申请，不能再次提交，拒绝可以再次提交
        $info=$this->getORM()->where('uid',$uid)->select('id,state,picture1,picture2')->fetchOne();
        $uploadFolder = sprintf('%s/public/uploads/', API_ROOT);
        if(!empty($info)){
            if($info['state']==1){
                $code=1;
            }else if($info['state']==2){
                $code=2;
            }else{
                //之前的删除，再重新添加新的数据
                //$re=$this->getORM()->where('id',$info['id'])->delete();
                //删除图片
                //unlink($uploadFolder.$info['picture1']);
                //unlink($uploadFolder.$info['picture2']);
                //unlink()
                $data=array(
                    'realname'=>$realname,
                    'idcard'=>$idcard,
                    'picture1'=>$picture1,
                    'picture2'=>$picture2,
                    'state'=>'1',
                    'create_time'=>date('Y-m-d H:i:s',time())
                );    
                $res = $this->getORM()->where('uid',$uid)->update($data);
                if($res){
                    $code=0;
                }else{
                    $code=4;
                }
            }
        }else{
            $data=array(
                    'uid'=>$uid,
                    'realname'=>$realname,
                    'idcard'=>$idcard,
                    'picture1'=>$picture1,
                    'picture2'=>$picture2,
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
