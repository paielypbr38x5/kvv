<?php
/**
 * Created by IntelliJ IDEA.
 * User: Neo
 * Date: 2017/2/23
 * Time: 12:39
 */

namespace Lib;

class DataTable
{
    public $Model;
    public $filter;
    public $field=true;
    public $map;
    public $ext; //扩展数据
    public $data;

    private $draw;
    private $recordsTotal;
    private $recordsFiltered;
    private $debug;
    
    public function __construct($Model)
    {
        $this->Model=$Model;
    }

    public function lists(){
        $this->draw=I('param.draw');
        $order_column=I('param.order')[0]['column'];
        $order_dir=I('param.order')[0]['dir'];
        $order=is_numeric($order_column)?'`'.I('param.columns')[$order_column]['data'].'` '.$order_dir:I('param.columns')[0]['data'];
        $start=I('param.start');
        $length=I('param.length');
        $search=I('param.search')['value'];

        if ($start<0 || $length <10){
            $start=0;
            $length=10;
        }

//        $map['uid']=session('User.UserID');
        $this->recordsTotal = $this->Model->where($this->map)->count();

        if(strlen($search) > 0){
            $this->filter=implode('|',$this->filter);
            $this->map[$this->filter]=array('like', '%' . $search . '%');
            $this->recordsFiltered = $this->Model->where($this->map)->count();
        }else{
            $this->recordsFiltered=$this->recordsTotal;
        }

        if (is_array($this->field)){
            $this->field=implode(',',$this->field);
        }
        $this->data=$this->Model->where($this->map)->order($order)->limit("{$start},{$length}")->field($this->field)->select();

        if (APP_DEBUG){
            $this->debug=array(
                'order'=>$order,
                'search'=>$search,
                'limit'=>"$start,$length",
                'order_default'=>I('param.columns')[0]['data'],
                'sql'=>$this->Model->getLastSql(),
            );
        }else{
            $this->debug=false;
        }
    }

    public function returnJson(){
        $result=array(
            'status'=>1,
            'draw'=>$this->draw,
            'recordsTotal'=>$this->recordsTotal,
            'recordsFiltered'=>$this->recordsFiltered,
            'info'=>'获取成功',
            'data'=>$this->data,
            'ext'=>$this->ext,
            'debug'=>$this->debug
        );
        header('Content-type: application/json');
        exit(json_encode($result));
    }
    
}