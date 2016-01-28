<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mentors_model extends Base_model {

  const TABLE = 'mentors';
  const ID = 'id';
  const TYPE = 'type';
  const IMG = 'img';
  const NAME = 'name';
  const SMALL = 'small';
  const DESC = 'desc';
  const TAGS = 'tags';
  const SORT = 'sort';
  const RELATIONTAG = 'relationtag';
  const CREATED = 'created';
  //const DELETE = 'delete';

  function __construct() {
    parent::__construct();
    $this->table = self::TABLE;
    $this->pk_name = self::ID;
  }

  public function get_sort($type = 0, $start = 0, $limit = 8) {
    $this->db->from(self::TABLE);
    if (!empty($type)) {
      $this->db->where(self::TYPE, $type);
    }
    $this->db->order_by(self::SORT, 'ASC');
    $this->db->order_by(self::ID, 'ASC');
    if (!empty($start) || !empty($limit)) {
      $this->db->limit($limit, $start);
    }
    $query = $this->db->get();
    return $query->result_array();
  }
}
