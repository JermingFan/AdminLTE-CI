<?php
defined('BASEPATH') OR exit('No direct script access allowed');

require_once(APPPATH .'libraries/REST_Controller.php');

/**
 * MY_Controller Class
 *
 * Extends CodeIgniter Controller
 * Basic Model loads and settings set.
 *
 */
class MY_Controller extends CI_Controller {

    public function __construct() {
        parent::__construct();

        $this->load->database();

        if (!$this->db->db_select()) {
            $error =& load_class('Exceptions', 'core');
            echo $error->show_error('Database Error', 'Unable to connect to the specified database : '. $this->db->database, 'error_db');
            exit;
        }

        // Models
        $this->load->model(array('base_model'), '', TRUE);

        // Libraries
        $this->load->library('twig');
    }

    protected function xhr_output($data) {
        if (is_array($data) OR is_object($data)) {
            $data = json_encode($data);
        }
        echo($data);
        die();
    }

    protected function _display($template, $data = array(), $return = FALSE) {
        $data['session'] = $this->session->userdata();
        return $this->twig->parse($template, $data, $return);
    }
}
// End MY_Controller

class API_Controller extends REST_Controller {

    private $error = NULL;
    private $fail = NULL;
    private $success = NULL;
    static $error_code = array(
        'success' => 0,
        'fail' => 1000,
    );

    public function __construct() {
        parent::__construct();
    }

    static function error_code($code) {
        return isset($error_code[$code]) ? $error_code[$code] : 1000;
    }

    /**
     * @param string $message
     * @param int    $code
     */
    protected function set_error($message = '', $code = 500) {
        $this->error = array(
            'status' => 'error',
            'code' => $code,
            'message' => $message
        );
    }

    /**
     * @param array   $data
     * @param int     $code
     */
    protected function set_fail($message = '', $code = 1000) {
        $this->fail = array(
            'status' => 'fail',
            'code' => $code,
            'message' => $message
        );
    }

    /**
     * @param array   $data
     * @param int     $code
     */
    protected function set_success($data = array(), $code = 0) {
        $result = array(
            'status' => 'success',
            'code' => $code,
        );
        if (is_array($data)) {
            $result['data'] = $data;
        } else {
            $result['message'] = $data;
        }
        $this->success = $result;
    }

    /**
     * @param array   $data
     * @param int     $code
     */
    protected function send_response($data = array(), $extra = array(), $code = 0) {
        if (!is_null($this->error)) {
            //log_message('error', 'API ERROR : ' . $this->uri->uri_string());
            $code = $this->error['code'];
            $result = $this->error;
        } elseif (!is_null($this->fail)) {
            $code = $this->fail['code'];
            $result = $this->fail;
        } else {
            if (is_null($this->success)) {
                $this->set_success($data, $code);
            }
            $result = $this->success;
        }
        if (!empty($extra)) {
            $result = array_merge($result, $extra);
        }

        $this->response($result, $code);
    }

    /**
     * Returns the key name of the asked GET or POST var position.
     *
     * @param int    $seg
     * @param string $type
     *
     * @return null
     */
    protected function get_segment($seg = 1, $type = 'get') {
        if ($type == 'get') {
            $vars = $this->get();
        } else {
            $vars = $this->post();
        }
        $vars_keys = array_keys($vars);
        if (isset($vars_keys[$seg - 1])) {
            return $vars_keys[$seg - 1];
        }
        return NULL;
    }
}
