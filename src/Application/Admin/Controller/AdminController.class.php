<?php
namespace Admin\Controller;

use Lib\DataTable;

class AdminController extends AuthController
{
//    管理员列表
    public function adminList()
    {
        if (I('param.draw')) {
            $DataTable = new DataTable(D('Admin'));

            $DataTable->filter = ['username', 'phone'];

            $DataTable->map['status'] = array('egt', 0);
            $DataTable->lists();

            foreach ($DataTable->data as $i => $item) {
                $DataTable->data[$i]['group'] = implode(' | ', array_column(self::$Auth->getGroups($item['id']), 'title'));
            }

            $DataTable->returnJson();
        }
        $this->display('admin-list');
    }

    //    管理员编辑
    public function adminEdit($id='')
    {   
        if (IS_POST){

            $Admin = D("Admin"); // 实例化User对象
            if (!$Admin->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                show(0, $Admin->getError());
            } else {
                if ($Admin->id) {
                    //        编辑保存
                    $Admin->save();
                    $Admin->addGroup($id, I('post.groups'));
                } else {
                    //        增加保存
                    $Admin->addGroup($Admin->add(), I('post.groups'));
                }
                show(1, '保存成功');
            }

        }
        $GroupList = M(C('AUTH_CONFIG.AUTH_GROUP'),'')->where('status=%d', 1)->select();
        $Admin = D('Admin')->find($id);

        $ActiveGroupList = self::$Auth->getGroups($id);
        foreach ($GroupList as $key => $value) {
            foreach ($ActiveGroupList as $Active) {
                if ($value['id'] == $Active['group_id']) {
                    $GroupList[$key]['checked'] = 'checked';
                }
            }
        }
        

//        dump($GroupList);
        $this->assign('GroupList', $GroupList);
        $this->assign('Admin', $Admin);

        $this->display('admin-edit');
    }

//    管理员状态
    public function adminStatus($id, $status)
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
            if (M('Admin')->save($data)) {
                show(1, $action . '成功');
            } else {
                show(0, $action . '失败');
            }
        }
        $succeed = 0;
        foreach ($id as $i => $item) {
            $data['id'] = $item;
            if (M('Admin')->save($data) != false) {
                $succeed++;
            }
        }
        show(1, '共选择 ' . sizeof($id) . ' 条记录，成功' . $action . ' ' . $succeed . ' 条');
    }

//    管理员状态
    public function adminRemove($id)
    {
        if (is_numeric($id)) {
            if (M('Admin')->delete($id)) {
                show(1, '删除成功');
            } else {
                show(0, '删除失败');
            }
        }
        if (is_array($id)) {
            $succeed = 0;
            foreach ($id as $i => $item) {
                $data['id'] = $item;
                if (M('Admin')->save($data) != false) {
                    $succeed++;
                }
            }
            show(1, '共选择 ' . sizeof($id) . ' 条记录，成功删除' . $succeed . ' 条');
        }
    }


//    权限组列表
    public function groupList()
    {
        if (I('param.draw')) {
            $DataTable = new DataTable(M(C('AUTH_CONFIG.AUTH_GROUP'), ''));

            $DataTable->filter = ['username', 'phone'];

            $DataTable->map['status'] = array('egt', 0);
            $DataTable->lists();

            foreach ($DataTable->data as $i => $item) {
                $map['id'] = array('in', $item['rules']);
                $DataTable->data[$i]['rules'] = implode('、', M(C('AUTH_CONFIG.AUTH_RULE'))->where($map)->getField('title', true));
            }

            $DataTable->returnJson();
        }
        $this->display('group-list');
    }

//    权限组编辑
    public function groupEdit($id = '')
    {
        $Group = M(C('AUTH_CONFIG.AUTH_GROUP')); // 实例化User对象
        $GroupData = $Group->find($id);
        if (IS_POST) {
            $rules = array(
                array('title', '', '组名称已经存在！', 1, 'unique', 1), // 在新增的时候验证name字段是否唯一
            );

            if (!$Group->validate($rules)->create()) {
                // 如果创建失败 表示验证没有通过 输出错误提示信息
                show(0, $Group->getError());
            } else {
                $Group->rules = implode(',', $Group->rules);
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
        $AllRule = M(C('AUTH_CONFIG.AUTH_RULE'), '')->select();
        $Rules = array();
        foreach ($AllRule as $item) {
            if (preg_match('/^' . MODULE_NAME . '*\/([a-z0-9]*)$/i', $item['name'], $match)) {
                $Rules[$match[1]] = $item;
            }
            if (preg_match('/^' . MODULE_NAME . '*\/([a-z0-9]*)\/([a-z0-9]*)$/i', $item['name'], $match)) {
                $Rules[$match[1]]['children'][$match[2]] = $item;
            }

        }


        $this->assign('Rules', $Rules);//一级规则
        $this->assign('GroupData', $GroupData);//一级规则

        $this->display('group-edit');
    }

    //    权限组状态
    public
    function groupStatus($id, $status)
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
            if (M(C('AUTH_CONFIG.AUTH_GROUP'))->save($data)) {
                show(1, $action . '成功');
            } else {
                show(0, $action . '失败');
            }
        }
        if (is_array($id)) {
            $succeed = 0;
            foreach ($id as $i => $item) {
                $data['id'] = $item;
                if (M(C('AUTH_CONFIG.AUTH_GROUP'), '')->save($data) != false) {
                    $succeed++;
                }
            }
            show(1, '共选择 ' . sizeof($id) . ' 条记录，成功' . $action . ' ' . $succeed . ' 条');
        }
    }

    //    权限组删除
    public
    function groupRemove($id)
    {
        if (is_numeric($id)) {
            if (M('Admin')->delete($id)) {
                show(1, '删除成功');
            } else {
                show(0, '删除失败');
            }
        }
        if (is_array($id)) {
            $succeed = 0;
            foreach ($id as $i => $item) {
                $data['id'] = $item;
                if (M('Admin')->save($data) != false) {
                    $succeed++;
                }
            }
            show(1, '共选择 ' . sizeof($id) . ' 条记录，成功删除' . $succeed . ' 条');
        }
    }


//    权限规则列表
    public
    function ruleList()
    {

        if (I('param.draw')) {
            $DataTable = new DataTable(M(C('AUTH_CONFIG.AUTH_RULE'), ''));

            $DataTable->filter = ['title', 'name'];

            $DataTable->map['status'] = array('egt', 0);
            $DataTable->lists();

            foreach ($DataTable->data as $i => $item) {
                $DataTable->data[$i]['group'] = implode(' | ', array_column(self::$Auth->getGroups($item['id']), 'title'));
            }

            $DataTable->returnJson();
        }

        $this->display('rule-list');
    }

//    权限规则编辑
    public
    function ruleEdit($id = '')
    {
        if (IS_POST){
            $rules = array(
                array('title', '', '规则名称已经存在！', 1, 'unique'), // 在新增的时候验证name字段是否唯一
                array('name', '', '规则已经存在！', 1, 'unique'), // 在新增的时候验证name字段是否唯一
                array('name', '/^[a-z0-9\/_]*/i', '规则不合法！', 1,'',1), // 在新增的时候验证name字段是否唯一
            );

            $Group = M(C('AUTH_CONFIG.AUTH_RULE'),''); // 实例化User对象
            if (!$Group->validate($rules)->create()) {
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
        $Rule = M(C('AUTH_CONFIG.AUTH_RULE'), ''); // 实例化User对象
        $RuleData = $Rule->find($id);

        $this->assign('RuleData', $RuleData);
        $this->display('rule-edit');
    }

    public
    function ruleStatus($id, $status)
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
            if (M(C('AUTH_CONFIG.AUTH_RULE'))->save($data)) {
                show(1, $action . '成功');
            } else {
                show(0, $action . '失败');
            }
        }
        if (is_array($id)) {
            $succeed = 0;
            foreach ($id as $i => $item) {
                $data['id'] = $item;
                if (M(C('AUTH_CONFIG.AUTH_RULE'))->save($data) != false) {
                    $succeed++;
                }
            }
            show(1, '共选择 ' . sizeof($id) . ' 条记录，成功' . $action . ' ' . $succeed . ' 条');
        }
    }

//    扫描权限规则
    public
    function ruleScan()
    {

    }
}