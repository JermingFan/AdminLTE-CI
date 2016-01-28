<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Incubator_model extends Base_model {

  const TABLE = 'incubator';
  const ID = 'id';
  const TYPE = 'type';
  const BRANCHES = 'branches';
  const NAME = 'name';
  const IMG = 'img';
  const URL = 'url';
  const SORT = 'sort';
  const CREATED = 'created';
  //const DELETE = 'delete';

  function __construct() {
    parent::__construct();
    $this->table = self::TABLE;
    $this->pk_name = self::ID;
  }

  public function get_sort() {
    $this->db->order_by(Incubator_model::TYPE, 'ASC');
    $this->db->order_by(Incubator_model::SORT, 'ASC');
    $query = $this->db->get(self::TABLE);
    return $query->result_array();
  }
}
