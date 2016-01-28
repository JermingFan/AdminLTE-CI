<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Course_model',
            'Relation_model',
            'Mentors_model',
        ));
    }

    function index_get() {
      $operation = $this->get_segment(1);
      $args = $this->get();

      switch($operation) {
        case '':
        default:
            $this->_index($args);
          break;
      }
    }

    function index_post() {
      $operation = $this->get_segment(1);
      $args = $this->post();
      switch($operation) {
        case '':
        default:
            $this->_index($args);
          break;
      }
    }
    
    function _index($args) {
      if (!isset($args['start']) || empty($args['start']))
      {
        $args['start'] = 0;
      }

      if (!isset($args['limit']) || $args['limit'] == '')
      {
        $args['limit'] = 8;
      }

//        $info = $this->Course_model->get_all();

        $sql = "select c.*, group_concat(m.id order by m.sort desc) as mid,  group_concat(m.`name` order by m.sort desc) as mname
                from course c
                left join relation r on r.cid = c.id
                left join mentors m on m.id = r.mid
                group by c.id
                order by c.id";
        if (empty($args['start']) || !empty($args['limit']))
        {
            $sql .= ' limit '. $args['start'] .','. $args['limit'];
        }

        $res = $this->db->query($sql);
        $data = $res->result_array();
        $info = array();
        foreach($data as $d)
        {
            $mid = (!empty($d['mid'])) ? explode(',',$d['mid'] ) : array();
            $mname = (!empty($d['mname'])) ? explode(',',$d['mname'] ) : array();
            $mentors = array();
            for($i = 0;$i<count($mid);$i++)
            {
                $mentors[] = array(
                    'id' => intval($mid[$i]),
                    'name' => $mname[$i],
                );
            }

            $info[] = array(
                'id' => intval($d['id']),
                'img' => $d['img'],
                'name' => $d['name'],
                'desc' => $d['desc'],
                'mentors' => $mentors
            );
        }

        $this->send_response($info);
    }
}
