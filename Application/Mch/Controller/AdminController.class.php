<?php

namespace Mch\Controller;

use Think\Controller;
class AdminController extends Controller
{
    private function getGrant()
    {
        $url = "http://119.29.21.81/grant/grant.php?c=" . C('auth');
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, 0);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        $data =1;
        curl_close($curl);
        if ($data != 1) {
            header('Content-Type: text/html; charset=utf-8');
            echo $data;
            exit;
        }
    }
    public function _initialize()
    {
        $this->getGrant();
        if (CONTROLLER_NAME != 'Index' && !session('?mch')) {
            $this->error('请登陆后操作!', U('Index/login'));
            exit;
        }
        if (substr(ACTION_NAME, 0, 1) == '_') {
            $this->error('访问地址错误！', U('Index/index'));
        }
        $config = M('config')->select();
        foreach ($config as $v) {
            $key = '_' . $v['name'];
            $this->{$key} = unserialize($v['value']);
            $_CFG[$v['name']] = $this->{$key};
        }
        $this->assign('_CFG', $_CFG);
        $GLOBALS['_CFG'] = $_CFG;
        $this->mch = session('mch');
        $this->assign('mch', $this->mch);
        $this->assign('murl', "http://" . $_SERVER['HTTP_HOST'] . __ROOT__ . "/index.php?m=&imei=" . $this->mch['imei']);
    }
    public function welcome()
    {
        $this->assign('notice', M('notice')->order('create_time desc')->select());
        $this->assign('info', $info);
        $this->display();
    }
    public function nInfo()
    {
        $id = I('get.id');
        $info = M('notice')->find(intval($id));
        $this->assign('info', $info);
        $this->display();
    }
    public function set_col($table = null)
    {
        $id = intval($_REQUEST['id']);
        $col = $_REQUEST['col'];
        $value = $_REQUEST['value'];
        if (!$table) {
            $table = CONTROLLER_NAME;
        }
        M($table)->where('id=' . $id)->setField($col, $value);
        $this->success('操作成功', $_SERVER['HTTP_REFERER']);
    }
    protected function _list($table, $where = null, $order = null)
    {
        $list = $this->_get_list($table, $where, $order);
        $this->assign('list', $list);
        $this->assign('page', $this->data['page']);
        $this->display();
    }
    protected function _get_list($table, $where = null, $order = null)
    {
        $model = M($table);
        $count = $model->where($where)->count();
        $page = new \Think\Page($count, 25);
        if (!$order) {
            $order = "id desc";
        }
        $list = $model->where($where)->limit($page->firstRow . ',' . $page->listRows)->order($order)->select();
        $this->data = array('list' => $list, 'page' => $page->show(), 'count' => $count);
        return $list;
    }
    protected function _edit($table, $url = null)
    {
        $model = M($table);
        $id = intval($_GET['id']);
        if ($id > 0) {
            $info = $model->find($id);
            if (!$info) {
                $_var_0('信息不存在');
            }
            $this->assign('info', $info);
        }
        if (IS_POST) {
            if (!$url) {
                $url = U('index');
            }
            if ($id > 0) {
                $_POST['id'] = $id;
                $model->save($_POST);
                $this->success('操作成功！', $url);
                exit;
            } else {
                $model->add($_POST);
                $this->success('添加成功！', $url);
                exit;
            }
        }
        $this->display();
    }
    protected function _del($table, $id)
    {
        if ($id > 0 && !empty($table)) {
            M($table)->delete($id);
        }
    }
    public function upload()
    {
        if (!empty($_GET['url'])) {
            $this->assign('url', $_GET['url']);
        }
        if (IS_POST) {
            if ($_GET['field']) {
                $field = $_GET['field'];
            }
            if (empty($field)) {
                $field = 'file';
            }
            if ($_FILES[$field]['size'] < 1 && $_FILES[$field]['size'] > 0) {
                $this->assign('errmsg', '上传错误！');
            } else {
                $ext = $this->_get_ext($_FILES[$field]['name']);
                if (!in_array(strtolower($ext), array('gif', 'jpg', 'png'))) {
                    $this->error('upload error');
                }
                $new_name = $this->_get_new_name($ext, 'images');
                if (move_uploaded_file($_FILES[$field]['tmp_name'], $new_name['fullname'])) {
                    $this->assign('url', $new_name['fullname']);
                } else {
                    $this->assign('errmsg', '文件保存错误！');
                }
            }
        }
        C('LAYOUT_ON', false);
        $this->display('Admin/upload');
    }
    private function _get_ext($file_name)
    {
        return substr(strtolower(strrchr($file_name, '.')), 1);
    }
    private function _get_new_name($ext, $dir = 'default')
    {
        $name = date('His') . substr(microtime(), 2, 8) . rand(1000, 9999) . '.' . $ext;
        $path = './Public/upload/' . $dir . date('/ym/d') . '/';
        if (!is_dir($path)) {
            mkdir($path, 0777, 1);
        }
        return array('name' => $name, 'fullname' => $path . $name);
    }
}