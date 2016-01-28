<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partner extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Partner_model',
            'Partnertag_model',
        ));
    }

    public function index() {

    }

    public function li() {
        $data = $this->Partner_model->get_list(array(
            'limit' => '',
            'offset' => 0,
            'order_by' => Partner_model::CREATED .' DESC',
            //Course_model::DELETE => 0,
        ));

        $ret = array(
            'data' =>array(
                'type' => Partner_model::TABLE,
                'items' => $data,
            )
        );

        $this->_display('admin/list_partner.html', $ret);
    }

    public function litag() {
        $data = $this->Partnertag_model->get_list(array(
            'limit' => '',
            'offset' => 0,
            'order_by' => Partnertag_model::ID .' DESC',
            //Course_model::DELETE => 0,
        ));

        $ret = array(
            'data' =>array(
                'type' => Partner_model::TABLE,
                'items' => $data,
            )
        );

        $this->_display('admin/list_partnertag.html', $ret);
    }

    public function add() {
        $args = $this->input->post();
        if (!empty($args)) {
            //$uid = $this->session->userdata('uid');
            $file = $_FILES['img'];
            $this->load->library('picuploader');
            $img = $this->picuploader->upload($file['tmp_name'], '/unicorn/partner/'.$file['name']);
            if (!empty($img))
            {
                $insert = array(
                    Partner_model::IMG => $img,
                    Partner_model::TYPE => trim($args['type']),
                    Partner_model::NAME => trim($args['name']),
                    Partner_model::URL => trim($args['url']),
                    Partner_model::SORT => $args['sort'],
                    Partner_model::CREATED => time(),
                );
                if (!empty($args['sort']))
                {
                    $sql = "UPDATE `partner` SET `sort` = `sort` + 1 WHERE `sort` >= " . $args['sort'];
                    $this->db->query($sql);
                }
                $this->Partner_model->insert($insert);
                if (empty($args['sort']))
                {
                    $id = $this->db->insert_id();
                    $this->Partner_model->update(array(Partner_model::ID => $id), array(Partner_model::SORT => $id));
                }
                redirect('admin/partner/li');
            }
            else
            {
                header("refresh:1;url=/admin/partner/li");
                echo '图片上传失败，1秒后返回列表';
                die;
            }
        }
        $option = $this->Partnertag_model->get_all();
        $ret = array(
            'data' =>array(
                'options' => $option,
            )
        );
        $this->_display('admin/add_partner.html', $ret);
    }

    public function addtag() {
        $args = $this->input->post();
        if (!empty($args)) {
            //var_dump($_FILES);die;
            //$uid = $this->session->userdata('uid');

            $insert = array(
                Partnertag_model::NAME => trim($args['type']),
                Partnertag_model::SORT => $args['sort'],
            );
            if (!empty($args['sort']))
            {
                $sql = "UPDATE `partnertag` SET `sort` = `sort` + 1 WHERE `sort` >= " . $args['sort'];
                $this->db->query($sql);
            }
            $this->Partnertag_model->insert($insert);
            if (empty($args['sort']))
            {
                $id = $this->db->insert_id();
                $this->Partnertag_model->update(array(Partnertag_model::ID => $id), array(Partnertag_model::SORT => $id));
            }
            redirect('admin/partner/litag');

        }
        $this->_display('admin/add_partnertag.html');
    }

    public function edit() {
        $id = $this->input->get('id');
        if ($id) {
            $data = $this->Partner_model->get_row_array(array(Partner_model::ID => $id));
            $option = $this->Partnertag_model->get_all();
            $ret = array(
                'data' =>array(
                    'options' => $option,
                    'items' => $data,
                )
            );
            $this->_display('admin/edit_partner.html', $ret);
        } else {

            $args = $this->input->post();
            //$uid = $this->session->userdata('uid');
            $file = $_FILES['img'];
            $this->load->library('picuploader');
            $img = $this->picuploader->upload($file['tmp_name'], '/unicorn/partner/'.$file['name']);
            if (!empty($img)) {
                $update = array(
                    Partner_model::IMG => $img,
                    Partner_model::TYPE => trim($args['type']),
                    Partner_model::NAME => trim($args['name']),
                    Partner_model::URL => trim($args['url']),
                    Partner_model::SORT => $args['sort'],
                );

                if ($args['oldsort'] > $args['sort'])
                {
                    $sql = "UPDATE `partner` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'] . " AND `sort` < " . $args['oldsort'];
                    $this->db->query($sql);
                }
                if ($args['oldsort'] < $args['sort'])
                {
                    $sql = "UPDATE `partner` SET `sort` = `sort` - 1 WHERE `sort` <= ".$args['sort'] . " AND `sort` > " . $args['oldsort'];
                    $this->db->query($sql);
                }

                $result = $this->Partner_model->update(array(Partner_model::ID => $args['id']), $update);
                redirect('admin/partner/li');
            }
            else
            {
                $update = array(
                    Partner_model::TYPE => trim($args['type']),
                    Partner_model::NAME => trim($args['name']),
                    Partner_model::URL => trim($args['url']),
                    Partner_model::SORT => $args['sort'],
                );

                if ($args['oldsort'] > $args['sort'])
                {
                    $sql = "UPDATE `partner` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'] . " AND `sort` < " . $args['oldsort'];
                    $this->db->query($sql);
                }
                if ($args['oldsort'] < $args['sort'])
                {
                    $sql = "UPDATE `partner` SET `sort` = `sort` - 1 WHERE `sort` <= ".$args['sort'] . " AND `sort` > " . $args['oldsort'];
                    $this->db->query($sql);
                }

                $result = $this->Partner_model->update(array(Partner_model::ID => $args['id']), $update);
                redirect('admin/partner/li');
            }
        }
    }

    public function edittag() {
        $id = $this->input->get('id');
        if ($id) {
            $data = $this->Partnertag_model->get_row_array(array(Partnertag_model::ID => $id));
            $ret = array(
                'data' =>array(
                    'items' => $data,
                )
            );
            $this->_display('admin/edit_partnertag.html', $ret);
        } else {
            $args = $this->input->post();
            //$uid = $this->session->userdata('uid');

            $update = array(
                Partnertag_model::NAME => trim($args['type']),
                Partnertag_model::SORT => $args['sort'],
            );

            if ($args['oldsort'] > $args['sort'])
            {
                $sql = "UPDATE `partnertag` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'] . " AND `sort` < " . $args['oldsort'];
                $this->db->query($sql);
            }
            if ($args['oldsort'] < $args['sort'])
            {
                $sql = "UPDATE `partnertag` SET `sort` = `sort` - 1 WHERE `sort` <= ".$args['sort'] . " AND `sort` > " . $args['oldsort'];
                $this->db->query($sql);
            }

            $result = $this->Partnertag_model->update(array(Partnertag_model::ID => $args['id']), $update);
            redirect('admin/partner/litag');
        }
    }
}
