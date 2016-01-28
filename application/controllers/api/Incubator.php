<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incubator extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Incubator_model',
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
      $info = $this->Incubator_model->get_sort();
      foreach ($info as $key => $val) {
        $branch_array = array();
        if (isset($val[Incubator_model::BRANCHES]) && !empty($val[Incubator_model::BRANCHES])) {
          $branches = str_replace(' ', '', $val[Incubator_model::BRANCHES]);
          $branch_array = array_values(explode(',', $branches));
        }
        $info[$key][Incubator_model::BRANCHES] = $branch_array;
      }
      $this->send_response($info);
    }

}