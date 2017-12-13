<?php
/**
 * Created by PhpStorm.
 * User: wbb
 * Date: 2017/11/4
 * Time: 19:13
 */
namespace app\admin\controller;

use app\model\Data;
use app\model\Info;
use app\model\Vm;
use think\Db;
use think\Session;

class TestController extends AdminAuth{
    protected $VM = [
        'VMWARE','VBOX','QEMU','VIRTUAL','HYPER','XEN','VIRTIO','RED HAT','82371AB/EB PCI BUS MASTER IDE'
    ];
    protected $Mac = [
        '00-50-56',
        '00-1C-14',
        '00-0C-29',
        '00-05-69'
    ];

    public function index(){
        $data = [
            'module_url'  => SITE_URL.'/admin/test/',
            'module_slug' => 'data',
        ];

        $this->assign('data',$data);
        return view();
    }

    public function add(){
        $param = input('post.');

        if(!$param){
            return $this->json('no post param',0);
        }
        $today = str_replace('-','',$param['time']);
        /*当天pv数量+1*/
        $this->pvCount($today);

        $this->checkTable($today);
        /* get mac count*/
        $macSum = $param['macInfo'];
        $channel = rand(500,520);


        $data = [];


        /*判断根据mac值判断$data的isNew*/
        $data['type'] = 1;

        $isNew = 1;
        $one = Db::name('mac')->where('name',$macSum)->where('date',$today)->find();
        if($one){
            $isNew = 0;

            /*在isNew=0基础上判端type=0,则直接返回success*/
            if($data['type'] == 0){
                $alive = Db::name($today.'_info')->where('mac',$macSum)->where('type',0)->find();
                if($alive){
                    return $this->json('live already,success');
                }
            }
        }
        $data['isNew'] = $isNew;


        Db::startTrans();
        try{
            $this->checkAndSave('mac',$macSum,'macId',$data,$today);
            $this->checkAndSave('app',trim($param['APPName']),'appId',$data,$today);
            $this->checkAndSave('channel',trim($channel),'channelId',$data,$today);
            $this->checkAndSave('version',trim($param['Version']),'versionId',$data,$today);


            $data['isVm'] = rand(0,1);
            $data['b360'] = rand(0,1);
            $data['bQQ'] = rand(0,1);
            $data['isNetBar'] = rand(0,1);


            /*save table date,info*/
            $id = Data::saveTestNew($data,$today);
            if($id){
                $name = $today.'t'.$data['type'];
                $this->insertCount($name,$today);

                if($data['type'] != 0){
                    $name = $today.'t'.$data['type'].'n'.$isNew;
                    $this->insertCount($name,$today);
                }

                $arr1['name'] = $param['VMStr'];
                $arr1['date'] = $today;
                $arr1['dataId'] = $id;
                $res1 = Db::name('vm')->insert($arr1);

                $arr2['name'] = $param['macName'];
                $arr2['mac'] = $param['macInfo'];
                $arr2['ip'] = $param['macIp'];
                $arr2['type'] = $data['type'];
                $arr2['dataId'] =$id;
                $res2 = Db::name($today.'_info')->insert($arr2);
                if($res1 && $res2){
                    /*总表summary插入数据*/
                    $data['date'] = $today;
                    $data['time'] = date('Y-m-d H:i:s',time());
                    $data['dataId'] = $id;
                    Db::name('summary')->insert($data);

                    Db::commit();
                    return $this->json('post success');
                }
            }

        } catch (\Exception $e){
            Db::rollback();
            return $this->json('post error',0);
        }
    }

    private function checkTable($time){
        $table1 = $time.'_data';
        $table2 = $time.'_info';
        $exist1 = Db::query('show tables like "'.$table1.'"');
        if(!$exist1){
            $sql = <<<sql
           CREATE TABLE `$table1` (
              `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
              `appId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'appname id',
              `channelId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'channel id',
              `macId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'mac id',
              `versionId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT 'version id',
              `isVm` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.是 0.否',
              `isNetBar` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.是 0.否',
              `b360` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.是 0.否',
              `bQQ` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.是 0.否',
              `isNew` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.是 0.否',
              `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.install 2.uninstall 3.live',
              PRIMARY KEY (`id`)
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
sql;
            dump(Db::execute($sql));
        }
        $exist2 = Db::query('show tables like "'.$table2.'"');
        if(!$exist2){
            $sql = <<<sql
          CREATE TABLE `$table2` (
          `id` int(11) unsigned NOT NULL AUTO_INCREMENT,
          `dataId` int(11) unsigned NOT NULL DEFAULT '0' COMMENT '对应日期data表id',
          `name` varchar(255) DEFAULT '' COMMENT 'mac name',
          `mac` varchar(255) DEFAULT '' COMMENT 'mac',
          `type` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '1.install 2.uninstall 3.live',
          `ip` varchar(255) DEFAULT '' COMMENT 'ip',
          PRIMARY KEY (`id`)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8;
sql;
            dump(Db::execute($sql));
        }
    }

    private function checkAndSave($table,$value,$saveField,&$data,$today){
        $one = Db::name($table)->where('name',$value)->find();
        $isNew =1;
        if($one){
            $isNew = 0;
        }

        $Ymd = getYmd($today);
        $insert['name'] = $value;
        $insert['date'] = $today;
        $insert['isNew'] = $isNew;
        $insert['year'] = $Ymd['year'];
        $insert['month'] = $Ymd['month'];
        $insert['day'] = $Ymd['day'];
        $data[$saveField] = Db::name($table)->insertGetId($insert);


        /*count增加统计数据*/
        if($table == 'version' || $table == 'channel' ){
            $header = $today.$table.$value;
            $this->insertCount($header,$today);

            if($table == 'channel'){
                $name = $header.'t'.$data['type'];
                $this->insertCount($name,$today);

                /*日活不需要分isNew*/
                if($data['type'] != 0){
                    $name = $header.'t'.$data['type'].'n'.$data['isNew'];
                    $this->insertCount($name,$today);
                }
            }
        }
        return $data;
    }


    /*most import function*/
    private function insertCount($name,$today){
        $one = Db::name('count')->where('name',$name)->where('date',$today)->find();
        if(!$one){
            $new['name'] = $name;
            $new['date'] = $today;
            Db::name('count')->insert($new);
        }
        else{
            Db::name('count')->where('name',$name)->where('date',$today)->setInc('num');
        }
    }


    private function pvCount($today){
        $name = $today.'pv';
        $one = Db::name('count')->where('name',$name)->where('date',$today)->find();
        if(!$one){
            $data['name'] =$name;
            $data['date'] = $today;
            Db::name('count')->insert($data);
        }
        else{
            Db::name('count')->where('name',$name)->where('date',$today)->setInc('num');
        }
    }
}