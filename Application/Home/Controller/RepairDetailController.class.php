<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2015/11/24
 * Time: 14:22
 */

namespace Home\Controller;


use Common\Controller\BaseController;


class RepairDetailController extends BaseController
{
    public function index(){
        if(empty($_GET['id'])) $this->error('没有找到该维修记录!');
        //获取维修记录id
        $id = intval($_GET['id']);
        if(!$detail = D('RepairRecord')->getRepairRecord($id)){
            $this->error(D('RepairRecord')->getError());
        }
        $this->assign('detail',$detail);
        $this->setTitle("维修详情");
        $this->setHeader("维修详情");
        $this->display();
    }
}