<?php
namespace app\index\model;

use think\Model;
use think\Db;
class IndexModel extends Model
{
    static function insert_Table($type)
    {   header('Access-Control-Allow-Origin:*');
        header('Access-Control-Allow-Methods:POST');
        header('Access-Control-Allow-Headers:x-requested-with,content-type');
        $g_id =  $_POST['g_id'];

        $ip = $_SERVER['REMOTE_ADDR'];
        $time = time();
        $plan_id = Db::table('guanggao')
            ->where("g_id =$g_id")
            ->value('plan_id');
        Db::table('record')
            ->insert(
                ['userip'=>$ip,
                'plan_id'=>$plan_id,
                'g_id'=>$g_id,
                'num'=>1,
                'time'=>$time,
                'type' =>$type
                ]);

    }
/*
查询record满足where条件的表数据
static function getR_info($ip,$g_id)
{
$reData = Db::table('record')
->where('userip',$ip)
->where('g_id',$g_id)
->order('id','desc')
->find();
return $reData;
}*/

/*
recod表插入数据
static function setRecord($ip,$g_id,$time,$num,$plan_id)
{
Db::table('record')
->insert(
[
'userip'=>$ip,
'plan_id'=>$plan_id,
'g_id'=>$g_id,
'num'=>$num+1,
'time'=>$time
]);
}*/

}