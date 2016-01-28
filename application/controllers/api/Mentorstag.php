<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mentorstag extends API_Controller {

    function __construct() {
        parent::__construct();
        $this->load->model(array(
            'Mentorstag_model',
        ));
    }
    function index_post()
    {
        $tag = $this->Mentorstag_model->get_all();
        $this->send_response($tag);
    }
}
