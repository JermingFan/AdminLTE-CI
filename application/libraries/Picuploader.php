<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PicUploader {

  protected $CI; // Reference to CodeIgniter instance

  private $bucket;
  private $form_api_secret;
  private $username;
  private $domain;
  
  function __construct() {
    $this->CI =& get_instance();

    $this->CI->config->load('upyun');
    $this->bucket = config_item('bucket');
    $this->form_api_secret = config_item('form_api_secret');
    $this->username = config_item('username');
    $this->password = config_item('password');
    $this->domain = config_item('domain');
  }

  function upload($file, $path) {
    if (!empty($file) && !empty($path)) {
      require_once('upyun.class.php');
      $upyun = new UpYun($this->bucket, $this->username, $this->password);

      try {
        $fh = fopen($file, 'rb');
        $return = $upyun->writeFile($path, $fh, TRUE);
        fclose($fh);
        return $this->domain . $path;
      } catch (Exception $e) {
        //echo $e->getCode();
        //echo $e->getMessage();
      }
    }
    return FALSE;
  }
}

?>