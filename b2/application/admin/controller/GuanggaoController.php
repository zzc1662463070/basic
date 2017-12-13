<?php
namespace app\admin\controller;
use app\admin\model\Guanggao as Guang;
use app\admin\model\Plan;
use app\admin\controller\AdminAuth;
use app\admin\model\GuangGao;
use ReCaptcha\RequestMethod\Post;
use think\Db;
use think\Validate;
use think\Image;
use think\Request;


class GuanggaoController extends AdminAuth

{
    public function index()
    {
        $data = array(
            'module_name' => '广告',
            'module_url'  => SITE_URL.'/admin/guanggao/',
            'module_slug' => 'guanggao',
            'upload_path' => UPLOAD_PATH,
            'upload_url'  => '/public/uploads/',
        );
        //$list = Db::table("guanggao")->select();
        $list =  Guang::where('g_id','>','0')->order('g_id', 'ASC')->paginate(5);
//        var_dump($list);exit;

        $this->assign('data',$data);
        $this->assign('list',$list);
        $abc=Db::table('guanggao')->count();


        return view();
    }
    public function create()
    {
    $id=$_GET['id'];

        $data = array(
            'module_name' => '管理员',
            'module_url'  => SITE_URL.'/admin/guanggao/',
            'module_slug' => 'guanggao',
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

        //默认值设置
        $item['status'] = '正常';
        $item['salt'] = rand(100,999);
        $this->assign('item',$item);
        $this->assign('data',$data);
        $this->assign('id',$id);
        $plantype = Db::table("plan")->where('plan_id',$id)->value('type');
        $this->assign('type',$plantype);
        return view();
    }
    public function add(){
        $name=$_POST['name'];
        $type=$_POST['type'];
        $info=$_POST['info'];
        $lianjie_A=$_POST['lianjie_A'];
        $lianjie_I=$_POST['lianjie_I'];
        $plan_id = $_POST['plan_id'];
        $file1 = request()->file('lianjie_A');
        $file2 = request()->file('lianjie_I');
        if ($file1&&$file2) {
            $path = 'public'  . DS . 'static/img';
            $lianjiename1 = $file1->getinfo("name");
            $file1->rule('uniqid')->move(ROOT_PATH . $path,$lianjiename1); //保存文件
            $lianjiename2 = $file2->getinfo("name");
            $file2->rule('uniqid')->move(ROOT_PATH . $path,$lianjiename2); //保存文件
            if ($_POST['type'] == '1') {
                Db::table('guanggao')->insert(['g_name' => $name, 'g_info' => $info, 'g_type' => $type, 'plan_id' => $plan_id, 'g_lianjie_A' => $lianjiename1, 'g_lianjie_I' => $lianjiename2]);
                $num = Db::table('plan')->field('num')->where('plan_id', $plan_id)->find();
                Db::table('plan')->where('plan_id', $plan_id)->update(['num' => $num['num'] + 1]);
               Guang::corejs($plan_id);
                return $this->success('添加成功', 'Guanggao/index');
            } else {
                if (!empty(input("file.pic"))) {
                    $file_img = input("file.pic");
                    $imgname = $file_img->getinfo("name");
                    $path_1 = 'public' . DS . 'static/img';
                    $file_img->move(ROOT_PATH . $path_1, $imgname); //保存文件
                    Db::table('guanggao')->insert(['g_name' => $name, 'g_info' => $info, 'g_type' => $type, 'picname' => $imgname, 'plan_id' => $plan_id, 'g_lianjie_A' => $lianjiename1, 'g_lianjie_I' => $lianjiename2]);
                    $num = Db::table('plan')->field('num')->where('plan_id', $plan_id)->find();
                    Db::table('plan')->where('plan_id', $plan_id)->update(['num' => $num['num'] + 1]);
                    Guang::corejs($plan_id);
                    return $this->success('添加成功', 'Guanggao/index');
                } else {

                    return $this->error('添加失败');
                }
            }
        }else if(!$file2&&!$file1) {
            if ($_POST['type'] == '1') {
                Db::table('guanggao')->insert(['g_name' => $name, 'g_info' => $info, 'g_type' => $type, 'plan_id' => $plan_id, 'g_lianjie_A' => $lianjie_A,'g_lianjie_I' => $lianjie_I]);
                $num = Db::table('plan')->field('num')->where('plan_id', $plan_id)->find();
                Db::table('plan')->where('plan_id', $plan_id)->update(['num' => $num['num'] + 1]);
                 Guang::corejs($plan_id);
                return $this->success('添加成功', 'Guanggao/index');
            } else {
                if (!empty(input("file.pic"))) {
                    $file_img = input("file.pic");
                    $imgname = $file_img->getinfo("name");
                    $path_1 = 'public' . DS . 'static/img';
                    $file_img->move(ROOT_PATH . $path_1, $imgname); //保存文件
                    Db::table('guanggao')->insert(['g_name' => $name, 'g_info' => $info, 'g_type' => $type, 'picname' => $imgname, 'plan_id' => $plan_id,  'g_lianjie_A' => $lianjie_A,'g_lianjie_I' => $lianjie_I]);
                    $num = Db::table('plan')->field('num')->where('plan_id', $plan_id)->find();
                    Db::table('plan')->where('plan_id', $plan_id)->update(['num' => $num['num'] + 1]);
                    Guang::corejs($plan_id);
                    return $this->success('添加成功', 'Guanggao/index');
                } else {
                    return $this->error('添加失败');
                }
            }
        }else if($file1&&!$file2){

            $path = 'public'  . DS . 'static/img';
            $lianjiename1 = $file1->getinfo("name");
            $file1->rule('uniqid')->move(ROOT_PATH . $path,$lianjiename1); //保存文件
            if ($_POST['type'] == '1') {
                Db::table('guanggao')->insert(['g_name' => $name, 'g_info' => $info, 'g_type' => $type, 'plan_id' => $plan_id, 'g_lianjie_A' => $lianjiename1, 'g_lianjie_I' => $lianjie_I]);
                $num = Db::table('plan')->field('num')->where('plan_id', $plan_id)->find();
                Db::table('plan')->where('plan_id', $plan_id)->update(['num' => $num['num'] + 1]);
                Guang::corejs($plan_id);
                return $this->success('添加成功', 'Guanggao/index');
            } else {
                if (!empty(input("file.pic"))) {
                    $file_img = input("file.pic");
                    $imgname = $file_img->getinfo("name");
                    $path_1 = 'public' . DS . 'static/img';
                    $file_img->move(ROOT_PATH . $path_1, $imgname); //保存文件
                    Db::table('guanggao')->insert(['g_name' => $name, 'g_info' => $info, 'g_type' => $type, 'picname' => $imgname, 'plan_id' => $plan_id, 'g_lianjie_A' => $lianjiename1, 'g_lianjie_I' => $lianjie_I]);
                    $num = Db::table('plan')->field('num')->where('plan_id', $plan_id)->find();
                    Db::table('plan')->where('plan_id', $plan_id)->update(['num' => $num['num'] + 1]);
                    Guang::corejs($plan_id);
                    return $this->success('添加成功', 'Guanggao/index');
                } else {
                    return $this->error('添加失败');
                }
            }
        }else if(!$file1&&$file2){
            $path = 'public'  . DS . 'static/img';
            $lianjiename2 = $file2->getinfo("name");
            $file2->rule('uniqid')->move(ROOT_PATH . $path,$lianjiename2); //保存文件
            if ($_POST['type'] == '1') {
                Db::table('guanggao')->insert(['g_name' => $name, 'g_info' => $info, 'g_type' => $type, 'plan_id' => $plan_id, 'g_lianjie_A' => $lianjie_A, 'g_lianjie_I' => $lianjiename2]);
                $num = Db::table('plan')->field('num')->where('plan_id', $plan_id)->find();
                Db::table('plan')->where('plan_id', $plan_id)->update(['num' => $num['num'] + 1]);
                Guang::corejs($plan_id);
                return $this->success('添加成功', 'Guanggao/index');
            } else {
                if (!empty(input("file.pic"))) {
                    $file_img = input("file.pic");
                    $imgname = $file_img->getinfo("name");
                    $path_1 = 'public' . DS . 'static/img';
                    $file_img->move(ROOT_PATH . $path_1, $imgname); //保存文件
                    Db::table('guanggao')->insert(['g_name' => $name, 'g_info' => $info, 'g_type' => $type, 'picname' => $imgname, 'plan_id' => $plan_id, 'g_lianjie_A' => $lianjie_A, 'g_lianjie_I' => $lianjiename2]);
                    $num = Db::table('plan')->field('num')->where('plan_id', $plan_id)->find();
                    Db::table('plan')->where('plan_id', $plan_id)->update(['num' => $num['num'] + 1]);
                    Guang::corejs($plan_id);
                    return $this->success('添加成功', 'Guanggao/index');
                } else {
                    return $this->error('添加失败');
                }
            }
        }
    }
    public function del(){
        $g_id=$_GET['g_id'];
        $plan_id=$_GET['plan_id'];
        Db::table('guanggao')->where('g_id',$g_id)->delete();
        $num=Db::table('plan')->field('num')->where('plan_id',$plan_id)->find();
        Db::table('plan')->where('plan_id',$plan_id)->update(['num'=>$num['num']-1]);
        Guang::corejs($plan_id);
        return $this->success('删除成功','Guanggao/index');
    }
    public function edit(){
        if(Request::instance()->isPost()){
            $name=$_POST['name'];
            $type=$_POST['type'];
            $info=$_POST['info'];
            $status=$_POST['status'];
            $plan_id = $_POST['plan_id'];
            $file1 = request()->file('lianjie_A');
            $file2 = request()->file('lianjie_I');
            if ($file1&&$file2) {
                $path = 'public' . DS . 'static/img';
                $lianjiename1 = $file1->getinfo("name");
                $lianjiename2 = $file2->getinfo("name");
                $file1->rule('uniqid')->move(ROOT_PATH . $path, $lianjiename1); //保存文件
                $file2->rule('uniqid')->move(ROOT_PATH . $path, $lianjiename2); //保存文件
                if($_POST['type']=='1'){
                    Db::table('guanggao')->where('g_id',$_GET['g_id'])->update(['g_name'=>$name,'g_info'=>$info,'g_type'=>$type,'plan_id'=>$plan_id,'status'=>$status,'g_lianjie_A'=>$lianjiename1,'g_lianjie_I'=>$lianjiename2]);
                    Guang::corejs($plan_id);
                    return $this->success('修改成功','Guanggao/index');
                } else {
                    if(!empty(input("file.pic"))){
                        $file_img = input("file.pic");
                        $imgname= $file_img->getinfo("name");
                        $path_1 = 'public' . DS . 'img' ;
                        $file_img->move(ROOT_PATH . $path_1,$imgname ); //保存文件
                        Db::table('guanggao')->where('g_id',$_GET['g_id'])->update(['g_name'=>$name,'g_info'=>$info,'g_type'=>$type,'picname'=>$imgname,'plan_id'=>$plan_id,'status'=>$status,'g_lianjie_A'=>$lianjiename1,'g_lianjie_I'=>$lianjiename2]);
                        Guang::corejs($plan_id);
                        return $this->success('修改成功','Guanggao/index');
                    }else{
                        return $this->error('修改失败');
                    }
                }
            }else if(!$file2&&!$file1){

                $lianjie_A=$_POST['lianjie_A'];
                $lianjie_I=$_POST['lianjie_I'];
                if($_POST['type']=='1'){
                    Db::table('guanggao')->where('g_id',$_GET['g_id'])->update(['g_name'=>$name,'g_info'=>$info,'g_type'=>$type,'plan_id'=>$plan_id,'status'=>$status,'g_lianjie_A' => $lianjie_A,'g_lianjie_I' => $lianjie_I
                    ]);
                    Guang::corejs($plan_id);
                    return $this->success('修改成功','Guanggao/index');
                } else {
                    if(!empty(input("file.pic"))){
                        $file_img = input("file.pic");
                        $imgname= $file_img->getinfo("name");
                        $path_1 = 'public' . DS . 'img' ;
                        $file_img->move(ROOT_PATH . $path_1,$imgname ); //保存文件
                        Db::table('guanggao')->where('g_id',$_GET['g_id'])->update(['g_name'=>$name,'g_info'=>$info,'g_type'=>$type,'picname'=>$imgname,'plan_id'=>$plan_id,'status'=>$status,'g_lianjie_A' => $lianjie_A,'g_lianjie_I' => $lianjie_I]);
                        Guang::corejs($plan_id);
                        return $this->success('修改成功','Guanggao/index');
                    }else{
                        return $this->error('修改失败');
                    }
                }
            }else if($file2&&!$file1){
                $path = 'public' . DS . 'static/img';
                $file2->rule('uniqid')->move(ROOT_PATH . $path, $lianjiename2); //保存文件
                $lianjie_A=$_POST['lianjie_A'];

                if($_POST['type']=='1'){
                    Db::table('guanggao')->where('g_id',$_GET['g_id'])->update(['g_name'=>$name,'g_info'=>$info,'g_type'=>$type,'plan_id'=>$plan_id,'status'=>$status,'g_lianjie_A' => $lianjie_A,'g_lianjie_I' => $lianjiename2
                    ]);
                    Guang::corejs($plan_id);
                    return $this->success('修改成功','Guanggao/index');
                } else {
                    if(!empty(input("file.pic"))){
                        $file_img = input("file.pic");
                        $imgname= $file_img->getinfo("name");
                        $path_1 = 'public' . DS . 'img' ;
                        $file_img->move(ROOT_PATH . $path_1,$imgname ); //保存文件
                        Db::table('guanggao')
                            ->where('g_id',$_GET['g_id'])
                            ->update([
                                'g_name'=>$name,
                                'g_info'=>$info,
                                'g_type'=>$type,
                                'picname'=>$imgname,
                                'plan_id'=>$plan_id,
                                'status'=>$status,
                                'g_lianjie_A' => $lianjie_A,
                                'g_lianjie_I' => $lianjiename2
                            ]);
                        Guang::corejs($plan_id);
                        return $this->success('修改成功','Guanggao/index');
                    }else{
                        return $this->error('修改失败');
                    }
                }
            }else if(!$file2&&$file1){
                $path = 'public' . DS . 'static/img';
                $lianjiename1 = $file1->getinfo("name");
//            print_r($lianjiename);
//            exit;
                $file1->rule('uniqid')->move(ROOT_PATH . $path, $lianjiename1); //保存文件
                $lianjie_I=$_POST['lianjie_I'];

                if($_POST['type']=='1'){
                    Db::table('guanggao')->where('g_id',$_GET['g_id'])->update(['g_name'=>$name,'g_info'=>$info,'g_type'=>$type,'plan_id'=>$plan_id,'status'=>$status,'g_lianjie_A' => $lianjiename1,'g_lianjie_I' => $lianjie_I
                    ]);
                    Guang::corejs($plan_id);
                    return $this->success('修改成功','Guanggao/index');
                } else {
                    if(!empty(input("file.pic"))){
                        $file_img = input("file.pic");
                        $imgname= $file_img->getinfo("name");
                        $path_1 = 'public' . DS . 'img' ;
                        $file_img->move(ROOT_PATH . $path_1,$imgname ); //保存文件
                        Db::table('guanggao')->where('g_id',$_GET['g_id'])->update(['g_name'=>$name,'g_info'=>$info,'g_type'=>$type,'picname'=>$imgname,'plan_id'=>$plan_id,'status'=>$status,'g_lianjie_A' => $lianjiename1,'g_lianjie_I' => $lianjie_I]);
                        Guang::corejs($plan_id);
                        return $this->success('修改成功','Guanggao/index');
                    }else{
                        return $this->error('修改失败');
                    }
                }
            }
       }else {
            $g_id=$_GET['g_id'];
            $g_info=$_GET['g_info'];
            $g_name=$_GET['g_name'];
            $plan_id=$_GET['plan_id'];
            $g_lianjie_A=$_GET['lianjie_A'];
            $g_lianjie_I=$_GET['lianjie_I'];
            $type=Db::table("guanggao")->where('g_id',$g_id)->value('g_type');
             $data = array(
                'module_name' => '管理员',
                'module_url' => SITE_URL . '/admin/guanggao/',
                'module_slug' => 'guanggao',
                'upload_path' => UPLOAD_PATH,
                'upload_url' => '/public/uploads/',
            );
            $data['edit_fields'] = array(
                'username' => array('type' => 'text', 'label' => '用户名'),
                'nickname' => array('type' => 'text', 'label' => '用户昵称'),
                'password' => array('type' => 'password', 'label' => '密码', 'notes' => '更新管理员资料时默认不填则不修改'),
                'salt' => array('type'

                => 'text', 'label' => '加密盐'),
                'mobile' => array('type' => 'text', 'label' => '手机号'),
                'avatar' => array('type' => 'file', 'label' => '头像'),
                'status' => array('type' => 'radio', 'label' => '状态', 'default' => array(-1 => '删除', 0 => '禁用', 1 => '正常', 2 => '待审核')),
            );
            $item['status'] = '正常';
            $item['salt'] = rand(100, 999);
            $this->assign('item', $item);
            $this->assign('data', $data);
            $this->assign('plan_id',$plan_id);
            $this->assign('g_id',$g_id);
            $this->assign('g_info',$g_info);
            $this->assign('g_lianjie_A',$g_lianjie_A);
            $this->assign('g_lianjie_I',$g_lianjie_I);
            $this->assign('g_name',$g_name);
            $this->assign('type',$type);
            return view('guanggao/update');

        }
    }

}