<?php
namespace app\admin\controller;
use app\admin\model\Record;
use app\admin\controller\AdminAuth;
use ReCaptcha\RequestMethod\Post;
use think\paginator\driver\Bootstrap;
use think\Db;
use think\Validate;
use think\Image;
use think\Request;
use lib\Page;

class RecordController extends AdminAuth
 {
    /*
     * 展示统计
     */
    public function index()
    {
        $data = array(
            'module_name' => '广告',
            'module_url' => SITE_URL . '/admin/record/',
            'module_slug' => 'record',
            'upload_path' => UPLOAD_PATH,
            'upload_url' => '/public/uploads/',
        );
        $recData = Db::table('record')
            ->alias('r')
            ->join('plan p','p.plan_id=r.plan_id')
            ->join('guanggao g','g.g_id=r.g_id')
            ->field('g.g_name,p.name as planname,r.g_id,r.plan_id,r.userip,r.num,r.type,r.time')
            ->order('r.time desc')
            ->paginate(10);
        $this->assign('data', $data);
        $this->assign('page', $recData->render());
        $this->assign('list', $recData);
        return view();
    }
    /*
     * 展示统计搜索
     */
    public function searchRecord()
    {
        $data = array(
            'module_name' => '广告',
            'module_url' => SITE_URL . '/admin/record/',
            'module_slug' => 'record',
            'upload_path' => UPLOAD_PATH,
            'upload_url' => '/public/uploads/',
        );
        $g_id = Request::instance()->get('g_id');
        //起始时间
        $start_time = strtotime(Request::instance()->get('start_time'));
        //结束时间
        $end_time = strtotime(Request::instance()->get('end_time'));
        if (!empty($g_id)) {
            $g_Data = Db::table('guanggao')->where('g_id', $g_id)->find();
            if (!empty($g_Data)) {
                $list = Record::Statistics($start_time,$end_time,$g_id) ;
                $this->assign('data', $data);
                $this->assign('list', $list);
                $this->assign('page', '');
                return view('index');
            } else {
                return $this->error('广告ID不存在');
            }
        }
    }
    /*
     * 计划统计
     */
    public function planStat(){
          $list = Record::getPlanistics();
          $data = Record::get_Data();
          $this->assign('list',$list);
          $this->assign('data',$data);
          return view("Record/plan");
    }
    /*
     * 计划统计搜索
     */
    public function searchPlan(){
        $plan_id = Request::instance()->get('p_id');
        $start_time = strtotime(Request::instance()->get('start_time')); //起始时间
        $end_time = strtotime(Request::instance()->get('end_time'));  //结束时间
        $plan = Db::table('record')
            ->alias('r')
            ->join('plan p','p.plan_id=r.plan_id')
            ->where('r.type = 1')
            ->where("r.plan_id =$plan_id")
            ->where("r.time > $start_time AND r.time < $end_time")
            ->field('sum(r.num) as showNum,p.plan_id,p.name as p_name')
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
            $data = Record::get_Data();
            $this->assign('list',$returnData);
            $this->assign('data',$data);
            return view("Record/plan");
         }
    /*
     * 广告统计
     */
    public function adverStat()
    {
        $list = Record::getAvder();
        $data = Record::get_Data();
        if (empty($list)) {
            return $this->error('没有广告数据');
        } else {
            //数组分页
            $curpage = input('page') ? input('page') : 1;//当前第x页，有效值为：1,2,3,4,5...
            $listRow = 2;//每页2行记录
            $showdata = array_chunk($list[$curpage - 1], count($list[0]), true);

            $p = Bootstrap::make($showdata, $listRow, $curpage, count($list), false, [
                'var_page' => 'page',
                'path' => url('/admin/record/adverstat'),//这里根据需要修改url
                'query' => [],
                'fragment' => '',
            ]);
            $p->appends($_GET);

            $this->assign('data', $data);
            $this->assign('list', $list);
            $this->assign('page', $p->render());
            return view("Record/avder");
        }
    }
    /*
     * 广告统计搜索
     */
    public function searchAdver(){
         $start_time = strtotime(Request::instance()->get('start_time')); //起始时间
         $end_time = strtotime(Request::instance()->get('end_time'));  //结束时间
         $g_data = Db::table('record')
             ->alias('r')
             ->join('guanggao g','g.g_id=r.g_id')
             ->where('r.type = 1')
             ->where("r.time >= $start_time AND r.time <= $end_time")
             ->field('sum(r.num) as showNum,g.g_id,g.g_name')
             ->group('r.g_id')
             ->order('r.g_id desc')
             ->paginate(5);

         $returnData =array();
         foreach ($g_data as $k => $v) {
             $returnData[$k] = $v;
             $data = DB::table('record')
                 ->where("time >= $start_time AND time <= $end_time")
                 ->where('type = 2')
                 ->where("g_id", "=", $v['g_id'])
                 ->field('count(num)as cliclNum')
                 ->find();
                 $returnData[$k]['cliclNum'] =$data['cliclNum'];
             }
                if(empty($returnData)){
                  return $this->error('该时间段内没有广告数据');
                 }else{
                //数组分页
                $curpage = input('page') ? input('page') : 1;//当前第x页，有效值为：1,2,3,4,5...
                $listRow = 2;//每页2行记录
                $showdata = array_chunk($returnData[$curpage-1], count($returnData[0]),true);

                $p = Bootstrap::make($showdata, $listRow, $curpage, count($returnData), false, [
                    'var_page' => 'page',
                    'path'     => url('/admin/record/searchAdver'),//这里根据需要修改url
                    'query'    => [],
                    'fragment' => '',
                ]);
                $p->appends($_GET);

                $data = Record::get_Data();
                $this->assign('list',$returnData);
                $this->assign('data',$data);
                $this->assign('page',$p->render());
                return view("Record/avder");
                }
            }










}