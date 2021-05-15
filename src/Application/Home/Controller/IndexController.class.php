<?php
namespace Home\Controller;
use Lib\DataTable;
use Think\Controller\RestController;

/**
 * Created by IntelliJ IDEA.
 * User: Neo
 * Date: 2017/3/23
 * Time: 10:32
 */
class IndexController extends RestController
{

    public function index(){
        if (I('param.draw')) {
            $DataTable = new DataTable(D('DataView'));

            $DataTable->filter = ['game_user','Game.name','mask'];

            $DataTable->map['mask'] = I('param.mask');
            if (I('param.start_time')) {
                $DataTable->map['date'] = array('egt', I('param.start_time'));
            }
            if (I('param.end_time')) {
                $DataTable->map['date'] = array('elt', I('param.end_time'));
            }
            if (I('param.start_time') && I('param.end_time')){
                $DataTable->map['date'] = array(array('egt', I('param.start_time')),array('elt', I('param.end_time')));
            }
            $DataTable->map['status'] = array('eq', 1);
            $DataTable->lists();


            $DataTable->returnJson();
        }

        $this->display();
    }
    
}