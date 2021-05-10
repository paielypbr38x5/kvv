<?php
namespace Admin\Controller;

use Common\Controller\AdminBaseController;
use Lib\DataTable;

class DataController extends AdminBaseController
{
    public function userList()
    {
        if (I('param.draw')) {
            $DataTable = new DataTable(D('UserView'));

            $DataTable->filter = ['game_user'];
//            $DataTable->map['status'] = array('egt', 0);
            $DataTable->lists();


            $DataTable->returnJson();
        }
        $this->display('user-list');
    }

    public function user()
    {
        $User=D('UserView');
        $map['game_user']=array('like', '%' . I('param.query') . '%');
        $data=$User->where($map)->field('id,game_user as label,game')->limit(I('param.limit'))->select();

//        echo $User->getLastSql();
        exit(json_encode($data));
    }

    public
    function userEdit($id = '')
    {
        if (IS_POST) {
            $Group = D('User'); // 实例化User对象
            if (!$Group->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                show(0, $Group->getError());
            } else {
                if ($id) {
                    //        编辑保存
                    $Group->save();
                } else {
                    //        增加保存
                    $Group->add();
                }
                show(1, '保存成功');
            }
        }
        $User = M('User'); // 实例化User对象
        $UserData = $User->find($id);

        $GameData = D('Game')->select();

        $this->assign('UserData', $UserData);
        $this->assign('GameData', $GameData);
        $this->display('user-edit');
    }

    public
    function userStatus($id, $status)
    {
        switch ($status) {
            case 'del':
                $data['status'] = -1;
                $action = '删除';
                break;
            case 'start':
                $action = '启用';
                $data['status'] = 1;
                break;
            case 'stop':
                $action = '停用';
                $data['status'] = 0;
                break;
            default:
                $action = '';
                show(0, '无效操作');
                break;
        }
        if (is_numeric($id)) {
            $data['id'] = $id;
            if (M('User')->save($data)) {
                show(1, $action . '成功');
            } else {
                show(0, $action . '失败');
            }
        }
        if (is_array($id)) {
            $succeed = 0;
            foreach ($id as $i => $item) {
                $data['id'] = $item;
                if (M('User')->save($data) != false) {
                    $succeed++;
                }
            }
            show(1, '共选择 ' . sizeof($id) . ' 条记录，成功' . $action . ' ' . $succeed . ' 条');
        }
    }

    public
    function gameEdit($id = '')
    {
        if (IS_POST) {
            $Group = D('Game'); // 实例化User对象
            if (!$Group->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                show(0, $Group->getError());
            } else {
                if ($id) {
                    //        编辑保存
                    $Group->save();
                } else {
                    //        增加保存
                    $Group->add();
                }
                show(1, '保存成功');
            }
        }
        $Game = M('Game'); // 实例化User对象
        $GameData = $Game->find($id);


        $this->assign('GameData', $GameData);
        $this->display('game-edit');
    }

    public
    function gameStatus($id, $status)
    {
        switch ($status) {
            case 'del':
                $data['status'] = -1;
                $action = '删除';
                break;
            case 'start':
                $action = '启用';
                $data['status'] = 1;
                break;
            case 'stop':
                $action = '停用';
                $data['status'] = 0;
                break;
            default:
                $action = '';
                show(0, '无效操作');
                break;
        }
        if (is_numeric($id)) {
            $data['id'] = $id;
            if (M('Game')->save($data)) {
                show(1, $action . '成功');
            } else {
                show(0, $action . '失败');
            }
        }
        if (is_array($id)) {
            $succeed = 0;
            foreach ($id as $i => $item) {
                $data['id'] = $item;
                if (M('Game')->save($data) != false) {
                    $succeed++;
                }
            }
            show(1, '共选择 ' . sizeof($id) . ' 条记录，成功' . $action . ' ' . $succeed . ' 条');
        }
    }


    public function gameList()
    {
        if (I('param.draw')) {
            $DataTable = new DataTable(D('Game'));

            $DataTable->filter = ['name'];

//            $DataTable->map['status'] = array('egt', 0);
            $DataTable->lists();


            $DataTable->returnJson();
        }
        $this->display('game-list');
    }

    public function dataList()
    {
        if (I('param.draw')) {
            $DataTable = new DataTable(D('DataView'));

            $DataTable->filter = ['game_user','Game.name','mask'];

//            $DataTable->map['status'] = array('egt', 0);
            $DataTable->lists();


            $DataTable->returnJson();
        }

        $data = D('DataView')->select();
        dump($data);

        $this->display('data-list');
    }

    function dataEdit($id = '')
    {
        if (IS_POST) {
            $Group = D('Data'); // 实例化User对象
            if (!$Group->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                show(0, $Group->getError());
            } else {
                if ($id) {
                    //        编辑保存
                    $Group->save();
                } else {
                    //        增加保存
                    $Group->add();
                }
                show(1, '保存成功');
            }
        }
        $Data = D('DataView')->find($id);
        $UserData = D('UserView')->select();

        $GameData = D('Game')->select();

        $this->assign('UserData', $UserData);
        $this->assign('GameData', $GameData);


        $this->assign('Data', $Data);
        $this->display('data-edit');
    }

}