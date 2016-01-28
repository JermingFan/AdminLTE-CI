<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Course_model',
        ));
    }

    public function index() {

    }

    public function li() {
        $data = $this->Course_model->get_list(array(
            'limit' => '',
            'offset' => 0,
            'order_by' => Course_model::CREATED .' DESC',
            //Course_model::DELETE => 0,
        ));

        $ret = array(
            'data' =>array(
                'type' => Course_model::TABLE,
                'items' => $data,
            )
        );

        $this->_display('admin/list_course.html', $ret);
    }

    public function add() {
        $args = $this->input->post();
        if (!empty($args)) {
            //var_dump($args);die;
            //$uid = $this->session->userdata('uid');

            $file = $_FILES['img'];
            $this->load->library('picuploader');
            $img = $this->picuploader->upload($file['tmp_name'], '/unicorn/course/'.$file['name']);
            if (!empty($img)) {
                $insert = array(
                    Course_model::IMG => $img,
                    Course_model::NAME => trim($args['name']),
                    Course_model::DESC => trim($args['desc']),
                    Course_model::CREATED => time(),);
                //if (isset($args['status'])) {
                //  $insert[Course_model::STATUS] = intval($args['status']);
                //}
                $this->Course_model->insert($insert);
                redirect('admin/course/li');
            }
            else
            {
                header("refresh:1;url=/admin/course/li");
                echo '图片上传失败，1秒后返回列表';
                die;
            }
        }
        $this->_display('admin/add_course.html');
    }

    public function edit() {
        $id = $this->input->get('id');
        if ($id) {
            $data = $this->Course_model->get_row_array(array(Course_model::ID => $id));
            $this->_display('admin/edit_course.html', array('data' => $data));
        } else {

            $args = $this->input->post();
            //$uid = $this->session->userdata('uid');
            $file = $_FILES['img'];
            $this->load->library('picuploader');
            $img = $this->picuploader->upload($file['tmp_name'], '/unicorn/course/'.$file['name']);
            if (!empty($img)) {
                $update = array(
                    Course_model::IMG => $img,
                    Course_model::NAME => trim($args['name']),
                    Course_model::DESC => trim($args['desc']),
                    //Course_model::CHANGED => time(),
                );
                //if (isset($args['status'])) {
                //  $update[Course_model::STATUS] = intval($args['status']);
                //}
                $result = $this->Course_model->update(array(Course_model::ID => $args['id']), $update);
                redirect('admin/course/li');
            }
            else
            {
                $update = array(
                    Course_model::NAME => trim($args['name']),
                    Course_model::DESC => trim($args['desc']),
                    //Course_model::CHANGED => time(),
                );
                //if (isset($args['status'])) {
                //  $update[Course_model::STATUS] = intval($args['status']);
                //}
                $result = $this->Course_model->update(array(Course_model::ID => $args['id']), $update);
                redirect('admin/course/li');
            }
        }
    }

}
