<?php
namespace app\admin\model;

use think\Model;

class Plan extends Model{

// 设置完整的数据表（包含前缀）
    protected $table = 'plan';

    public $rule = [
        'name'        =>'require|max:25',
        'status'         =>'in:1,2',
        'limit'         =>'require|number',
        'type'        =>'require',
        'start_time'        =>'require|date',
        'end_time'    =>'require|date',
    ];

    public $message = [
        'name.require'     => '计划名称不能为空',
        'status'        => '展示状态参数错误',
        'type'            => '广告展示类型参数错误',
        'begin_time'        => '开始时间格式错误',
        'end_time'        => '结束时间格式错误'
    ];
    // 关闭自动写入时间戳
    //protected $autoWriteTimestamp = false;

    //默认时间格式
    protected $dateFormat = 'Y-m-d H:i:s';

    protected $type       = [
        // 设置时间戳类型（整型）
        'start_time'     => 'timestamp',
        'end_time'     => 'timestamp',
        'update_time' => 'timestamp',
        'add_time' => 'timestamp',
    ];

    //自动完成
    protected $insert = [
        'create_time',
        'update_time',
    ];

    protected $update = ['update_time'];

    // 属性修改器
    protected function setCreateTimeAttr($value, $data)
    {
        return time();
    }

    /**
     * 添加广告计划
     * @param $data
     * @return bool
     */
    public function add($data)
    {
        if(!$this->validate($this->rule,$this->message)->save($data))
        {
            return $this->error;
        }else{
            return true;
        }
    }

    /**
     * 广告计划修改
     * @param $data
     * @return bool
     */
    public function doUpdate($data)
    {
        if(!$this->validate($this->rule,$this->message)->save($data))
        {
            return $this->error;
        }else{
            return true;
        }
    }

    /**
     * [根据广告计划Id查找广告计划]
     * @param $planId
     * @return static
     */
    static function getPlanById($planId)
    {
        return  Plan::get($planId);
    }


}