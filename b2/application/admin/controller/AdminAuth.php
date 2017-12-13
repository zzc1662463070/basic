<?php
/*
 * @thinkphp5.0  后台auth认证   php5.3以上
 * @Created on 2016/07/25
 * @Author  Kevin   858785716@qq.com
 * @如果需要公共控制器，就不要继承AdminAuth，直接继承Controller
 */
namespace app\admin\controller;
use app\admin\model\Administrator as AdministratorModel;
use app\admin\model\Surlog;
use app\admin\model\Survey;
use think\Controller;
use think\Db;
use think\Model;
use think\Request;
use think\Session;

//权限认证
class AdminAuth extends Controller {
	protected function _initialize(){
		$request = request();
		//session存在时，不需要验证的权限
		$not_check = array('admin/login','admin/login_action','admin/lostpassword','admin/logout','admin/lost_password');
		//当前操作的请求 模块名/方法名
		if(in_array($request->module().'/'.$request->action(), $not_check) || $request->module() != 'admin'){
			return true;
		}
        //session不存在时，不允许直接访问
        if(!session('uid')){
            //未登陆跳转
            $this->error('还没有登录，正在跳转到登录页',$request->root(true).'/admin/login');
        }
	}
}
