<?php
namespace app\admin\model;

use think\Model;
use think\Db;

class Record extends Model
{
    protected $table = 'record';


     static  function get_Data(){
        $data = array(
            'module_name' => '管理员',
            'module_url'  => SITE_URL.'/admin/administrator/',
            'module_slug' => 'administrator',
            'upload_path' => UPLOAD_PATH,
            'upload_url'  => '/public/uploads/',
        );
        $data['edit_fields'] = array(
            'username' => array('type' => 'text', 'label'     => '用户名'),
            'nickname' => array('type' => 'text', 'label'     => '用户昵称'),
            'password' => array('type' => 'password', 'label' => '密码','notes'=>'更新管理员资料时默认不填则不修改'),
            'salt'     => array('type' => 'text', 'label'     => '加密盐'),
            'mobile'   => array('type' => 'text', 'label'     => '手机号'),
            'avatar'   => array('type' => 'file','label'     => '头像'),
            'status'   => array('type' => 'radio', 'label' => '状态','default'=> array(-1 => '删除', 0 => '禁用', 1 => '正常', 2 => '待审核')),
        );
       return $data ;
    }

   /*
    * 展示统计数据
    * */
    static function Statistics($start_time,$end_time,$g_id){
        $result_show = Db::table('record')
            ->where("time >= $start_time AND time <=$end_time")
            ->where('type = 1')
            ->where("g_id = $g_id")
            ->count("num");
        $recData_show = Db::table('record')
            ->alias('r')
            ->join('plan p','p.plan_id=r.plan_id')
            ->join('guanggao g','g.g_id=r.g_id')
            ->field('g.g_name,p.name as planname,r.g_id,r.plan_id,r.userip,r.num,r.type,r.time')
            ->where("r.time >= $start_time AND r.time <= $end_time")
            ->where('r.type = 1')
            ->where("r.g_id = $g_id")
            ->find();
        $recData_show['num'] = $result_show;
        //展示总次数
        $result_clik = Db::table('record')
            ->where("time > $start_time AND time < $end_time")
            ->where('type = 2')
            ->where("g_id = $g_id")
            ->count("num");
        $list[0] = $recData_show;
        if ($result_clik) {
            $recData_clik = Db::table('record')
                ->alias('r')
                ->join('plan p','p.plan_id=r.plan_id')
                ->join('guanggao g','g.g_id=r.g_id')
                ->field('g.g_name,p.name as planname,r.g_id,r.plan_id,r.userip,r.num,r.type,r.time')
                ->where("r.time >= $start_time AND r.time <= $end_time")
                ->where('r.type = 2')
                ->where("r.g_id = $g_id")
                ->find();
            $recData_clik['num'] = $result_clik;
            $list[1] = $recData_clik;
        }
         return   $list;

    }
    /*
     *获取计划统计数据
     * */
   static function getPlanistics(){
       $plan = Db::table('record')
           ->alias('r')
           ->join('plan p','p.plan_id=r.plan_id')
           ->field('sum(r.num) as showNum,p.plan_id,p.name as p_name')
           ->where('r.type = 1')
           ->group('p.plan_id')
           ->order('r.time desc')
           ->select();

       $returnData =$plan;
       foreach ($plan as $k => $v) {
           $returnData[$k] = $v;
           $data = DB::table('record')
               ->field('count(num)as cliclNum')
               ->where('type=2')
               ->where('plan_id','=',$v['plan_id'])
               ->find();
           $returnData[$k]['cliclNum'] =$data['cliclNum'];
           }
           return $returnData;
    }
    /*
     * 获取广告数据
     */
  static function getAvder(){
      $g_data = Db::table('record')
          ->alias('r')
          ->join('guanggao g','g.g_id=r.g_id')
          ->where('r.type = 1')
          ->field('sum(r.num) as showNum,g.g_id,g.g_name')
          ->group('r.g_id')
          ->order('r.g_id desc')
          ->paginate(5);
      $returnData = array();
      foreach ($g_data as $k => $v) {
          $returnData[$k] = $v;
          $data = DB::table('record')
              ->field('count(num) as cliclNum')
              ->where('type=2')
              ->where('g_id','=',$v['g_id'])
              ->find();
//          var_dump($data);exit;
          $returnData[$k]['cliclNum'] = $data['cliclNum'];
         }
         return $returnData;
     }

}