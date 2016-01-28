<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Partner_model extends Base_model {

    const TABLE = 'partner';
    const ID = 'id';
    const TYPE = 'type';
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
        $this->db->order_by(Partner_model::SORT, 'ASC');
        $this->db->order_by(Partner_model::ID, 'ASC');
        $query = $this->db->get(self::TABLE);
        return $query->result_array();
    }

    public function get_list_join_tag($type = 0) {
        $this->db->select(self::TABLE .'.'. self::ID);
        $this->db->select(self::TABLE .'.'. self::TYPE);
        $this->db->select(self::TABLE .'.'. self::NAME);
        $this->db->select(self::TABLE .'.'. self::IMG);
        $this->db->select(self::TABLE .'.'. self::URL);
        $this->db->select(self::TABLE .'.'. self::SORT);
        $this->db->select(self::TABLE .'.'. self::CREATED);
        $this->db->select(Partnertag_model::TABLE .'.'. Partnertag_model::ID .' AS tagid');
        $this->db->select(Partnertag_model::TABLE .'.'. Partnertag_model::NAME .' AS tagname');
        $this->db->from(self::TABLE);
        $this->db->join(Partnertag_model::TABLE, Partnertag_model::TABLE .'.'. Partnertag_model::ID .'='. self::TABLE .'.'. self::TYPE, 'LEFT');
        $this->db->order_by(Partnertag_model::TABLE .'.'. Partnertag_model::SORT, 'ASC');
        $this->db->order_by(self::TABLE .'.'. self::SORT, 'ASC');
        $this->db->order_by(self::TABLE .'.'. self::ID, 'ASC');
        $query = $this->db->get();
        return $query->result_array();
    }

}
