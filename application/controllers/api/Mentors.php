<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mentors extends API_Controller {

    function __construct() {
      parent::__construct();
      $this->load->model(array(
        'Mentors_model',
        'Mentorstag_model',
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
      if (!isset($args['type']) || $args['type'] == '') {
        $args['type'] = 0;
      }

      if (!isset($args['start']) || empty($args['start'])) {
        $args['start'] = 0;
      }

      if (!isset($args['limit']) || $args['limit'] == '') {
        $args['limit'] = 8;
      }

      // get mentors tags list
      $mentors_tag = $this->Mentorstag_model->get_all();
      $types = array();
      foreach ($mentors_tag as $data) {
        $types[$data[Mentorstag_model::ID]] = $data[Mentorstag_model::NAME];
      }

      // get mentors list
      $info = $this->Mentors_model->get_sort($args['type'], $args['start'], $args['limit']);
      $ret = array();
      foreach ($info as $data) {
        if (isset($types[$data[Mentors_model::TYPE]])) {
          $data[Mentors_model::TAGS] = $types[$data[Mentors_model::TYPE]];
        }
        $ret[] = array(
          'id' => intval($data[Mentors_model::ID]),
          'type' => intval($data[Mentors_model::TYPE]),
          'img' => (string)$data[Mentors_model::IMG],
          'name' => (string)$data[Mentors_model::NAME],
          'small' => (string)$data[Mentors_model::SMALL],
          'desc' => (string)$data[Mentors_model::DESC],
          'tags' => (string)$data[Mentors_model::TAGS],
          'sort' => intval($data[Mentors_model::SORT]),
          'created' => intval($data[Mentors_model::CREATED])
        );
      }

      $this->send_response($ret);
    }
}