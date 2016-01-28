<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mentors extends MY_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Mentors_model',
            'Mentorstag_model',
            'Course_model',
            'Relation_model',
        ));
    }

    public function index() {

    }

    public function li() {
        $data = $this->Mentors_model->get_list(array(
            'limit' => '',
            'offset' => 0,
            'order_by' => Mentors_model::CREATED .' DESC',
        ));

        $ret = array(
            'data' =>array(
                'type' => Mentors_model::TABLE,
                'items' => $data,
            )
        );

        $this->_display('admin/list_mentors.html', $ret);
    }

    public function litag() {
        $data = $this->Mentorstag_model->get_list(array(
            'limit' => '',
            'offset' => 0,
            'order_by' => Mentorstag_model::ID .' DESC',
        ));

        $ret = array(
            'data' =>array(
                'type' => Mentors_model::TABLE,
                'items' => $data,
            )
        );

        $this->_display('admin/list_mentorstag.html', $ret);
    }

    public function add() {
        $args = $this->input->post();

        if (!empty($args)) {
            $file = $_FILES['img'];
            $this->load->library('picuploader');
            $img = $this->picuploader->upload($file['tmp_name'], '/unicorn/mentors/'.$file['name']);
            if (!empty($img))
            {
                $insert = array(
                    Mentors_model::IMG => $img,
                    Mentors_model::TYPE => trim($args['type']),
                    Mentors_model::NAME => trim($args['name']),
                    Mentors_model::SMALL => trim($args['small']),
                    Mentors_model::DESC => trim($args['desc']),
                    Mentors_model::SORT => $args['sort'],
                    Mentors_model::CREATED => time(),
                );
                if (!empty($args['sort']))
                {
                    $sql = "UPDATE `mentors` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'];
                    $this->db->query($sql);
                }
                $this->Mentors_model->insert($insert);
                if (empty($args['sort']))
                {
                    $id = $this->db->insert_id();
                    $this->Mentors_model->update(array(Mentors_model::ID => $id), array(Mentors_model::SORT => $id));
                }
                $data = array();
                $name = array();

                $this->db->order_by('created', 'DESC');
                $id = $this->Mentors_model->get(array('name' => $args['name']));
                if (isset($args['tags']) && !empty($args['tags']))
                {
                    foreach($args['tags'] as $tag)
                    {
                        $data[]  = array(
                            'mid' => $id['id'],
                            'cid' => $tag,
                        );
                        $name[] = $this->Course_model->get_list(array('id' => $tag));
                    }
                }

                $nnn = '';
                foreach($name as $n){
                    foreach($n as $nn)
                    {
                        $nnn .= $nn['name'].'|';
                    }
                }
                $this->Mentors_model->update(array(Mentors_model::ID => $id['id']), array(Mentors_model::RELATIONTAG => $nnn));
                if (!empty($data)) {
                    $this->Relation_model->insert_batch($data);
                }



                redirect('admin/mentors/li');
            }
            else
            {
                header("refresh:1;url=/admin/mentors/li");
                echo '图片上传失败，1秒后返回列表';
                die;
            }
        }
        $option = $this->Mentorstag_model->get_all();
        $course = $this->Course_model->get_all();
        $ret = array(
            'data' =>array(
                'options' => $option,
                'course' => $course,
            )
        );
        $this->_display('admin/add_mentors.html', $ret);
    }

    public function addtag() {
        $args = $this->input->post();
        if (!empty($args)) {

            $insert = array(
                Mentorstag_model::NAME => trim($args['type']),
                Mentorstag_model::SORT => $args['sort'],
            );
            if (!empty($args['sort']))
            {
                $sql = "UPDATE `mentorstag` SET `sort` = `sort` + 1 WHERE `sort` >= " . $args['sort'];
                $this->db->query($sql);
            }
            $this->Mentorstag_model->insert($insert);
            if (empty($args['sort']))
            {
                $id = $this->db->insert_id();
                $this->Mentorstag_model->update(array(Mentorstag_model::ID => $id), array(Mentorstag_model::SORT => $id));
            }
            redirect('admin/mentors/litag');

        }
        $this->_display('admin/add_mentorstag.html');
    }

    public function edit() {
        $id = $this->input->get('id');
        if ($id) {
            $data = $this->Mentors_model->get_row_array(array(Mentors_model::ID => $id));
            $option = $this->Mentorstag_model->get_all();
            $course = $this->Course_model->get_all();
            $relation = $this->Relation_model->get_list(array(Relation_model::MID => $id));
            $cids = array();
            foreach ($relation as $item) {
                $cids[] = $item['cid'];
            }
            $cid = !empty($cids) ? array_values($cids) : NULL;

            $ret = array(
                'data' =>array(
                    'options' => $option,
                    'items' => $data,
                    'course' => $course,
                    'relation' => $relation,
                    'cid' => $cid,
                )
            );
            $this->_display('admin/edit_mentors.html', $ret);
        } else {

            $args = $this->input->post();
            $file = $_FILES['img'];
            $this->load->library('picuploader');
            $img = $this->picuploader->upload($file['tmp_name'], '/unicorn/mentors/'.$file['name']);

            if (!empty($img))
            {
                $update = array(
                    Mentors_model::IMG => $img,
                    Mentors_model::TYPE => trim($args['type']),
                    Mentors_model::NAME => trim($args['name']),
                    Mentors_model::SMALL => trim($args['small']),
                    Mentors_model::DESC => trim($args['desc']),
                    Mentors_model::SORT => $args['sort'],
                    Mentors_model::CREATED => time(),
                );

                if ($args['oldsort'] > $args['sort'])
                {
                    $sql = "UPDATE `mentors` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'] . " AND `sort` < " . $args['oldsort'];
                    $this->db->query($sql);
                }
                if ($args['oldsort'] < $args['sort'])
                {
                    $sql = "UPDATE `mentors` SET `sort` = `sort` - 1 WHERE `sort` <= ".$args['sort'] . " AND `sort` > " . $args['oldsort'];
                    $this->db->query($sql);
                }

                $result = $this->Mentors_model->update(array(Mentors_model::ID => $args['id']), $update);

                $this->Relation_model->delete(array('mid' => $args['id']),Relation_model::TABLE, TRUE);

                foreach($args['tags'] as $tag)
                {
                    $ids = array(
                        'mid' => $args['id'],
                        'cid' => $tag,
                    );
                    $data[] = $ids;
                    $name[] = $this->Course_model->get_list(array('id' => $tag));
                }

                $nnn = '';
                foreach($name as $n){
                    foreach($n as $nn)
                    {
                        $nnn .= $nn['name'].'|';
                    }
                }
                $this->Mentors_model->update(array('id' => $args['id']), array('relationtag' => $nnn));

                if (!empty($data)) {
                    $this->Relation_model->insert_batch($data);
                }
                redirect('admin/mentors/li');
            }
            else
            {
                $update = array(
                    Mentors_model::TYPE => trim($args['type']),
                    Mentors_model::NAME => trim($args['name']),
                    Mentors_model::SMALL => trim($args['small']),
                    Mentors_model::DESC => trim($args['desc']),
                    Mentors_model::SORT => trim($args['sort']),
                    Mentors_model::CREATED => time(),
                );

                if ($args['oldsort'] > $args['sort'])
                {
                    $sql = "UPDATE `mentors` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'] . " AND `sort` < " . $args['oldsort'];
                    $this->db->query($sql);
                }
                if ($args['oldsort'] < $args['sort'])
                {
                    $sql = "UPDATE `mentors` SET `sort` = `sort` - 1 WHERE `sort` <= ".$args['sort'] . " AND `sort` > " . $args['oldsort'];
                    $this->db->query($sql);
                }

                $result = $this->Mentors_model->update(array(Mentors_model::ID => $args['id']), $update);

                $this->Relation_model->delete(array('mid' => $args['id']),Relation_model::TABLE, TRUE);

                foreach($args['tags'] as $tag)
                {
                    $ids = array(
                        'mid' => $args['id'],
                        'cid' => $tag,
                    );
                    $data[] = $ids;
                    $name[] = $this->Course_model->get_list(array('id' => $tag));
                }

                $nnn = '|';
                foreach($name as $n){
                    foreach($n as $nn)
                    {
                        $nnn .= $nn['name'].'|';
                    }
                }
                $this->Mentors_model->update(array('id' => $args['id']), array('relationtag' => $nnn));

                if (!empty($data)) {
                    $this->Relation_model->insert_batch($data);
                }

                redirect('admin/mentors/li');
            }
        }
    }

    public function edittag() {
        $id = $this->input->get('id');
        if ($id) {
            $data = $this->Mentorstag_model->get_row_array(array(Mentorstag_model::ID => $id));
            $ret = array(
                'data' =>array(
                    'items' => $data,
                )
            );
            $this->_display('admin/edit_mentorstag.html', $ret);
        } else {
            $args = $this->input->post();

            $update = array(
                Mentorstag_model::NAME => trim($args['type']),
                Mentorstag_model::SORT => $args['sort'],
            );

            if ($args['oldsort'] > $args['sort'])
            {
                $sql = "UPDATE `mentorstag` SET `sort` = `sort` + 1 WHERE `sort` >= ".$args['sort'] . " AND `sort` < " . $args['oldsort'];
                $this->db->query($sql);
            }
            if ($args['oldsort'] < $args['sort'])
            {
                $sql = "UPDATE `mentorstag` SET `sort` = `sort` - 1 WHERE `sort` <= ".$args['sort'] . " AND `sort` > " . $args['oldsort'];
                $this->db->query($sql);
            }

            $result = $this->Mentorstag_model->update(array(Mentorstag_model::ID => $args['id']), $update);
            redirect('admin/mentors/litag');
        }
    }

}
