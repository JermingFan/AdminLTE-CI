<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partner extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Partner_model',
            'Partnertag_model',
        ));
    }

    function index_get()
    {
        $operation = $this->get_segment(1);
        $args = $this->get();

        switch($operation) {
          case '':
          default:
              $this->_index($args);
            break;
        }
    }

    function index_post()
    {
        $operation = $this->get_segment(1);
        $args = $this->post();
        switch($operation) {
          case '':
          default:
              $this->_index($args);
            break;
        }
    }
    
    function _index($args)
    {
        $partners = $this->Partner_model->get_list_join_tag();
        $ret = array();

        $partner = array();
        foreach ($partners as $data) {
          $partner[$data[Partner_model::TYPE]][] = array(
            'id' => intval($data[Partner_model::ID]),
            'type' => intval($data[Partner_model::TYPE]),
            'name' => (string)$data[Partner_model::NAME],
            'img' => (string)$data[Partner_model::IMG],
            'url' => (string)$data[Partner_model::URL],
            'sort' => intval($data[Partner_model::SORT]),
            'created' => intval($data[Partner_model::CREATED])
          );

          $ret[$data[Partner_model::TYPE]] = array(
            'tagid' => intval($data['tagid']),
            'tagname' => (string)$data['tagname'],
            //'partner' => array_values($partner[$data[Partner_model::TYPE]])
          );
        }
        foreach ($partner as $key => $val) {
          $ret[$key]['partner'] = array_values($val);
        }
        $ret = array_values($ret);
        $this->send_response($ret);
    }
}