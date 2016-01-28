<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incubatortag extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Incubatortag_model',
        ));
    }
    function index_post()
    {
        $tag = $this->Incubatortag_model->get_all();
        $this->send_response($tag);
    }
    function index_get()
    {
        $tag = $this->Incubatortag_model->get_all();
        $this->send_response($tag);
    }
}
