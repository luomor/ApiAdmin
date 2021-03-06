<?php
/**
 *
 * @since   2018-02-11
 * @author  zhaoxiang <zhaoxiang051405@gmail.com>
 */

namespace app\admin\controller;


use app\model\AdminAppGroup;
use app\util\ReturnCode;
use app\util\Tools;

class AppGroup extends Base {
    /**
     * 获取应用组列表
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function index() {
        $limit = $this->request->get('size', config('apiAdmin.ADMIN_LIST_DEFAULT'));
        $start = $limit * ($this->request->get('page', 1) - 1);
        $keywords = $this->request->get('keywords', '');
        $type = $this->request->get('type', '');
        $status = $this->request->get('status', '');

        $where = [];
        if ($status === '1' || $status === '0') {
            $where['status'] = $status;
        }
        if ($type) {
            switch ($type) {
                case 1:
                    $where['hash'] = $keywords;
                    break;
                case 2:
                    $where['name'] = ['like', "%{$keywords}%"];
                    break;
            }
        }

        $listInfo = (new AdminAppGroup())->where($where)->limit($start, $limit)->select();
        $count = (new AdminAppGroup())->where($where)->count();
        $listInfo = Tools::buildArrFromObj($listInfo);

        return $this->buildSuccess([
            'list'     => $listInfo,
            'count'    => $count
        ]);
    }

    /**
     * 获取全部有效的应用组
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getAll() {
        $listInfo = (new AdminAppGroup())->where(['status' => 1])->select();

        return $this->buildSuccess([
            'list'     => $listInfo
        ]);
    }

    /**
     * 应用组状态编辑
     * @return array
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     */
    public function changeStatus() {
        $id = $this->request->get('id');
        $status = $this->request->get('status');
        $res = AdminAppGroup::update([
            'status' => $status
        ], [
            'id' => $id
        ]);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 添加应用组
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function add() {
        $postData = $this->request->post();
        $res = AdminAppGroup::create($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 应用组编辑
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function edit() {
        $postData = $this->request->post();
        $res = AdminAppGroup::update($postData);
        if ($res === false) {
            return $this->buildFailed(ReturnCode::DB_SAVE_ERROR, '操作失败');
        } else {
            return $this->buildSuccess([]);
        }
    }

    /**
     * 应用组删除
     * @author zhaoxiang <zhaoxiang051405@gmail.com>
     * @return array
     */
    public function del() {
        $hash = $this->request->get('hash');
        if (!$hash) {
            return $this->buildFailed(ReturnCode::EMPTY_PARAMS, '缺少必要参数');
        }
        AdminAppGroup::destroy(['hash' => $hash]);

        return $this->buildSuccess([]);
    }
}
