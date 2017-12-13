<?php
namespace app\admin\controller;
use app\admin\model\Guanggao;
use app\admin\model\Plan;
use think\Db;
use think\Request;

class PlanController extends AdminAuth
{
    /**
     * [index 获取用户数据列表]
     * @return [type] [description]
     */
    public function index()
    {
        $data = array(
            'module_name' => '广告计划',
            'module_url'  => SITE_URL.'/admin/plan/',
            'module_slug' => 'plan',
            'upload_path' => UPLOAD_PATH,
            'upload_url'  => '/public/uploads/',
        );


        $list =  Plan::where('status','>','0')->order('plan_id', 'ASC')->paginate(5);
        $this->assign('data',$data);
        $this->assign('list',$list);
        return $this->fetch();

    }

    /**
     * [create 新增广告计划页面]
     * @return \think\response\View
     */
    public function create()
    {
        $data = array(
            'module_name' => '广告计划',
            'module_url'  => SITE_URL.'/admin/plan/',
            'module_slug' => 'plan',
            'upload_path' => PUBLIC_PATH,
            'upload_url'  => '/public/uploads/',
        );
        $this->assign('data',$data);
        return view();
    }

    /**
     * create 新增广告计划
     */
    public function add()
    {
        $model_url = SITE_URL.'/admin/plan/';
        $request = Request::instance();
        $file = request()->file('p_lianjie');
        if($file){
            $path = 'public'  . DS . 'static/plan';
            $p_lianjiename = $file->getinfo("name");
            $file->rule('uniqid')->move(ROOT_PATH . $path,$p_lianjiename); //保存文件
        }
        $plan = new Plan();
        $addData =$request->param();
        unset($addData['aa']);
        if(isset($p_lianjiename) && !empty($p_lianjiename)){
            $addData['p_lianjie'] = $p_lianjiename;
        }
        $result = $plan->add($addData);

        if($result==false){
            $this->error('广告计划添加失败，请稍后重试',$model_url);
        }else{
            $filename=  Db::table('plan')->max('plan_id');
            $planname = $addData['name'];
            $js_data = " ";
            file_put_contents("public/static/planjs/$filename.js",$js_data);
            $this->success('广告计划添加成功',$model_url);


        }

    }

    /**
     * 广告计划修改
     */
    public function update()
    {
        $module_url = SITE_URL.'/admin/plan/';
        $request = Request::instance();
        if($request->isGet()){
            $planId = Request::instance()->get('plan_id');
            $p_lianjie = $_GET['p_lianjie'];
            $data = Plan::getPlanById($planId);
            $data->module_url = $module_url;
            $this->assign('data',$data);
            $this->assign('p_lianjie',$p_lianjie);
            return view();
        }else if($request->isPost()){
            $file = request()->file('p_lianjie');
            if($file){
                $path = 'public'  . DS . 'static/plan';
                $p_lianjiename = $file->getinfo("name");
                $file->rule('uniqid')->move(ROOT_PATH . $path,$p_lianjiename); //保存文件
            }
            $plan = new Plan();
            $updateData =$request->param();
            unset($updateData['aa']);
            if(isset($p_lianjiename) && !empty($p_lianjiename)){
                $updateData['p_lianjie'] = $p_lianjiename;
            }
            $result = Plan::update($updateData, Request::instance()->get('plan_id'));
            if($result) {
                $plan_id = $_POST['plan_id'];
                Guanggao::corejs($plan_id);

                $this->success('广告计划修改成功', $module_url);
            }else {
                $this->error('请求失败', $module_url);
            }
        }
    }

    /**
     * 广告计划删除
     */
    public function del(){
        $plan_id=$_GET['plan_id'];
         Db::transaction(function(){
            Db::table('plan')->where('plan_id',$_GET['plan_id'])->delete();
            Db::table('guanggao')->where('plan_id',$_GET['plan_id'])->delete();
        });
       //删除对应的js文件
        unlink("public/static/planjs/$plan_id.js");
         $this->success('删除成功','Plan/index');
    }
}


/*Db::transaction(function(){
   Db::table('think_user')->find(1);
   Db::table('think_user')->delete(1);
});*/
