<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Relation extends MY_Controller {

	function __construct() {
		parent::__construct();
		$this->load->model(array(
			'Relation_model',
		));
	}

	public function index() {

	}

}
