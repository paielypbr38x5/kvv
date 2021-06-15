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
            $DataTable->field=['game_user','sell','sell_count','game','recycle','recycle_count','date'];
            $DataTable->filter = ['game_user','Game.name','mask'];

            $DataTable->map['mask'] = I('param.mask');
            $DataTable->map['status'] = array('eq', 1);

            if (I('param.start_time')) {
                $DataTable->map['date'] = array('egt', I('param.start_time'));
            }
            if (I('param.end_time')) {
                $DataTable->map['date'] = array('elt', I('param.end_time'));
            }
            if (I('param.start_time') && I('param.end_time')){
                $DataTable->map['date'] = array(array('egt', I('param.start_time')),array('elt', I('param.end_time')));
                $DataTable->ext=D('DataView')->where($DataTable->map)->field('sum(sell)/sum(sell_count)/16500 as sell_exp,sum(recycle)/sum(recycle_count)/16500 as recycle_exp,sum(recycle)/sum(sell) as kvv_exp')->find();
                if ($DataTable->ext['sell_exp']<60){
                    $DataTable->ext['sell_info']='状态较为稳定，但要注意对客户的维护。';
                }else if ($DataTable->ext['sell_exp']<65){
                    $DataTable->ext['sell_info']='比较稳定，潜力很大，也要注意进行客户的增加。';
                }else{
                    $DataTable->ext['sell_info']='大客户较多，大客户不够稳定，在尽量稳定大客户的同时多发展小客户。';
                }
                if ($DataTable->ext['recycle_exp']){
                    $DataTable->ext['recycle_info']='正常';
                }
                if ($DataTable->ext['kvv_exp']<0.1){
                    $DataTable->ext['kvv_info']='回收过少，记得提醒客户下分，养成对客户下分的习惯。';
                }else if ($DataTable->ext['kvv_exp']<0.15){
                    $DataTable->ext['kvv_info']='处于较为稳定阶段，最近请多寻找新客户。';
                }else if($DataTable->ext['kvv_exp']<0.2){
                    $DataTable->ext['kvv_info']='回收数量正常。';
                }else{
                    $DataTable->ext['kvv_info']='回收数量过多。';
                }

            }
            $DataTable->lists();

            foreach ($DataTable->data as $i=>$item){
                $DataTable->data[$i]['sell_exp']=$item['sell']/16500/$item['sell_count'];
                $DataTable->data[$i]['sell_money']=$item['sell']/16500;
                $DataTable->data[$i]['recycle_exp']=$item['recycle']/18000/$item['recycle_count'];
                $DataTable->data[$i]['recycle_money']=$item['recycle']/18000;
                $DataTable->data[$i]['kvv_exp']=$item['recycle']/$item['sell'];

                if ($DataTable->data[$i]['sell_exp']<60){
                    $DataTable->data[$i]['sell_info']='状态较为稳定，但要注意对客户的维护。';
                }else if ($DataTable->data[$i]['sell_exp']<65){
                    $DataTable->data[$i]['sell_info']='比较稳定，潜力很大，也要注意进行客户的增加。';
                }else{
                    $DataTable->data[$i]['sell_info']='大客户较多，大客户不够稳定，在尽量稳定大客户的同时多发展小客户。';
                }
                if ($DataTable->data[$i]['recycle_exp']){
                    $DataTable->data[$i]['recycle_info']='正常';
                }
                if ($DataTable->data[$i]['kvv_exp']<0.1){
                    $DataTable->data[$i]['kvv_info']='回收过少，记得提醒客户下分，养成对客户下分的习惯。';
                }else if ($DataTable->data[$i]['kvv_exp']<0.15){
                    $DataTable->data[$i]['kvv_info']='处于较为稳定阶段，最近请多寻找新客户。';
                }else if($DataTable->data[$i]['kvv_exp']<0.2){
                    $DataTable->data[$i]['kvv_info']='回收数量正常。';
                }else{
                    $DataTable->data[$i]['kvv_info']='回收数量过多。';
                }
            }
            if (I('param.export')==true) {
                $this->export($DataTable->data);
            } else {
                $DataTable->returnJson();
            }
        }
        if ($_SERVER['HTTP_REFERER']!=='http://192.168.0.9/admin.php/Data.html' && !APP_DEBUG){
            exit('您没有权限访问');
        }
        $this->display();
    }

    private function export($Data)
    {
        Vendor('PHPExcel.PHPExcel');
        $objPHPExcel = new \PHPExcel();
        $Sheet = $objPHPExcel->getActiveSheet();

        $Sheet
            ->setCellValue('A1', '游戏ID')
            ->setCellValue('B1', '游戏')
            ->setCellValue('C1', '日期')
            ->setCellValue('D1', '销售总额')
            ->setCellValue('E1', '销售笔数')
            ->setCellValue('F1', '销售指数')
            ->setCellValue('G1', '回收总额')
            ->setCellValue('H1', '回收笔数')
            ->setCellValue('I1', '回收指数')
            ->setCellValue('J1', 'KVV指数');
        if ($Data) {
            $i = 2;
            foreach ($Data as $item) {
                $Sheet
                    ->setCellValueExplicit('A' . $i, $item['game_user'])
                    ->setCellValueExplicit('B' . $i, $item['game'])
                    ->setCellValueExplicit('C' . $i, $item['date'])
                    ->setCellValueExplicit('D' . $i, $item['sell'])
                    ->setCellValueExplicit('E' . $i, $item['sell_count'])
                    ->setCellValueExplicit('F' . $i, $item['sell_exp'])
                    ->setCellValueExplicit('G' . $i, $item['recycle'])
                    ->setCellValueExplicit('H' . $i, $item['recycle_count'])
                    ->setCellValueExplicit('I' . $i, $item['recycle_exp'])
                    ->setCellValueExplicit('J' . $i, $item['kvv_exp']);
                $i++;
            }

            $Sheet->getColumnDimension('A')->setAutoSize(true);
            $Sheet->getColumnDimension('B')->setAutoSize(true);
            $Sheet->getColumnDimension('C')->setAutoSize(true);
            $Sheet->getColumnDimension('D')->setAutoSize(true);
            $Sheet->getColumnDimension('E')->setAutoSize(true);
            $Sheet->getColumnDimension('F')->setAutoSize(true);
            $Sheet->getColumnDimension('G')->setAutoSize(true);
            $Sheet->getColumnDimension('H')->setAutoSize(true);
            $Sheet->getColumnDimension('I')->setAutoSize(true);
            $Sheet->getColumnDimension('J')->setAutoSize(true);

            header('Content-Type: application/vnd.ms-excel');
            header('Content-Disposition: attachment;filename="交易记录.xls"');
            header('Cache-Control: max-age=0');
            $objWriter = \PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel5');
            $objWriter->save('php://output');
            exit;
        } else {
            exit('导出失败');
        }

    }

}