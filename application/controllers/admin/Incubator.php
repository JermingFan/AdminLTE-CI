<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incubator extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Incubator_model',
            'Incubatortag_model',
        ));
    }

    public function index() {

    }

    public function li() {
        $data = $this->Incubator_model->get_list(array(
            'limit' => '',
            'offset' => 0,
            'order_by' => Incubator_model::CREATED .' DESC',
            //Course_model::DELETE => 0,
        ));

        $ret = array(
            'data' =>array(
                'type' => Incubator_model::TABLE,
                'items' => $data,
            )
        );

        $this->_display('admin/list_incubator.html', $ret);
    }

    public function litag() {
        $data = $this->Incubatortag_model->get_list(array(
            'limit' => '',
            'offset' => 0,
            'order_by' => Incubatortag_model::ID .' DESC',
            //Course_model::DELETE => 0,
        ));

        $ret = array(
            'data' =>array(
                'type' => Incubator_model::TABLE,
                'items' => $data,
            )
        );

        $this->_display('admin/list_incubatortag.html', $ret);
    }

    public function add() {
        $args = $this->input->post();
        if (!empty($args)) {
            //$uid = $this->session->userdata('uid');
            $file = $_FILES['img'];
            $this->load->library('picuploader');
            $img = $this->picuploader->upload($file['tmp_name'], '/unicorn/incubator/'.$file['name']);
            if (!empty($img))
            {
                $insert = array(
                    Incubator_model::IMG => $img,
                    Incubator_model::TYPE => trim($args['type']),
                    Incubator_model::BRANCHES => trim($args['branches']),
                    Incubator_model::NAME => trim($args['name']),
                    Incubator_model::URL => trim($args['url']),
                    Incubator_model::SORT => $args['sort'],
                    Incubator_model::CREATED => time(),
                );
                if (!empty($args['sort']))
                {
                    $sql = "UPDATE `incubator` SET `sort` = `sort` + 1 WHERE `sort` >= " . $args['sort'];
                    $this->db->query($sql);
                }
                $this->Incubator_model->insert($insert);
                if (empty($args['sort']))
                {
                    $id = $this->db->insert_id();
                    $this->Incubator_model->update(array(Incubator_model::ID => $id), array(Incubator_model::SORT => $id));
                }
                redirect('admin/incubator/li');
            }
            else
            {
                header("refresh:1;url=/admin/incubator/li");
                echo '图片上传失败，1秒后返回列表';
                die;
            }
        }
        $option = $this->Incubatortag_model->get_all();
        $ret = array(
            'data' =>array(
                'options' => $option,
            )
        );
        $this->_display('admin/add_incubator.html', $ret);
    }

    public function addtag() {
        $args = $this->input->post();
        if (!empty($args)) {
            //var_dump($_FILES);die;
            //$uid = $this->session->userdata('uid');

            $insert = array(
                Incubatortag_model::NAME => trim($args['type']),
                Incubatortag_model::SORT => $args['sort'],
            );
            if (!empty($args['sort']))
            {
                $sql = "UPDATE `incubatortag` SET `sort` = `sort` + 1 WHERE `sort` >= " . $args['sort'];
                $this->db->query($sql);
            }
            $this->Incubatortag_model->insert($insert);
            if (empty($args['sort']))
            {
                $id = $this->db->insert_id();
                $this->Incubatortag_model->update(array(Incubatortag_model::ID => $id), array(Incubatortag_model::SORT => $id));
            }
            redirect('admin/incubator/litag');

        }
        $this->_display('admin/add_incubatortag.html');
    }

    public function edit() {
        $id = $this->input->get('id');
        if ($id) {
            $data = $this->Incubator_model->get_row_array(array(Incubator_model::ID => $id));
            $option = $this->Incubatortag_model->get_all();
            $ret = array(
                'data' =>array(
                    'options' => $option,
                    'items' => $data,
                )
            );
            $this->_display('admin/edit_incubator.html', $ret);
        } else {

            $args = $this->input->post();
            //$uid = $this->session->userdata('uid');
            $file = $_FILES['img'];
            $this->load->library('picuploader');
            $img = $this->picuploader->upload($file['tmp_name'], '/unicorn/incubator/'.$file['name']);
            if (!empty($img)) {
                $update = array(
                    Incubator_model::IMG => $img,
                    Incubator_model::TYPE => trim($args['type']),
                    Incubator_model::BRANCHES => trim($args['branches']),
                    Incubator_model::NAME => trim($args['name']),
                    Incubator_model::URL => trim($args['url']),
                    Incubator_model::SORT => $args['sort'],
                );

                if ($args['oldsort'] > $args['sort'])
                {
                    $sql = "UPDATE `incubator` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'] . " AND `sort` < " . $args['oldsort'];
                    $this->db->query($sql);
                }
                if ($args['oldsort'] < $args['sort'])
                {
                    $sql = "UPDATE `incubator` SET `sort` = `sort` - 1 WHERE `sort` <= ".$args['sort'] . " AND `sort` > " . $args['oldsort'];
                    $this->db->query($sql);
                }

                $result = $this->Incubator_model->update(array(Incubator_model::ID => $args['id']), $update);
                redirect('admin/incubator/li');
            }
            else
            {
                $update = array(
                    Incubator_model::TYPE => trim($args['type']),
                    Incubator_model::BRANCHES => trim($args['branches']),
                    Incubator_model::NAME => trim($args['name']),
                    Incubator_model::URL => trim($args['url']),
                    Incubator_model::SORT => trim($args['sort']),
                );

                if ($args['oldsort'] > $args['sort'])
                {
                    $sql = "UPDATE `incubator` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'] . " AND `sort` < " . $args['oldsort'];
                    $this->db->query($sql);
                }
                if ($args['oldsort'] < $args['sort'])
                {
                    $sql = "UPDATE `incubator` SET `sort` = `sort` - 1 WHERE `sort` <= ".$args['sort'] . " AND `sort` > " . $args['oldsort'];
                    $this->db->query($sql);
                }

                $result = $this->Incubator_model->update(array(Incubator_model::ID => $args['id']), $update);
                redirect('admin/incubator/li');
            }
        }
    }

    public function edittag() {
        $id = $this->input->get('id');
        if ($id) {
            $data = $this->Incubatortag_model->get_row_array(array(Incubatortag_model::ID => $id));
            $ret = array(
                'data' =>array(
                    'items' => $data,
                )
            );
            $this->_display('admin/edit_incubatortag.html', $ret);
        } else {
            $args = $this->input->post();
            //$uid = $this->session->userdata('uid');

            $update = array(
                Incubatortag_model::NAME => trim($args['type']),
                Incubatortag_model::SORT => $args['sort'],
            );

            if ($args['oldsort'] > $args['sort'])
            {
                $sql = "UPDATE `incubatortag` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'] . " AND `sort` < " . $args['oldsort'];
                $this->db->query($sql);
            }
            if ($args['oldsort'] < $args['sort'])
            {
                $sql = "UPDATE `incubatortag` SET `sort` = `sort` - 1 WHERE `sort` <= ".$args['sort'] . " AND `sort` > " . $args['oldsort'];
                $this->db->query($sql);
            }

            $result = $this->Incubatortag_model->update(array(Incubatortag_model::ID => $args['id']), $update);
            redirect('admin/incubator/litag');
        }
    }

}
