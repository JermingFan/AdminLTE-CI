<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partnertag extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Partnertag_model',
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
        $tags = $this->Partnertag_model->get_list(array('order_by' => Partnertag_model::SORT));

        $ret = array();
        foreach ($tags as $data) {
            $ret[] = array(
                'id' => intval($data[Partnertag_model::ID]),
                'name' => (string)$data[Partnertag_model::NAME],
                'created' => intval($data[Partnertag_model::CREATED]),
                'sort' => intval($data[Partnertag_model::SORT])
            );
        }

        $this->send_response($ret);
    }
}
