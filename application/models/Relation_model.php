<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Relation_model extends Base_model {

    const TABLE = 'relation';
    const ID = 'id';
    const MID = 'mid';
    const CID = 'cid';
    const TAGS = 'tags';
    //const DELETE = 'delete';

    function __construct() {
        parent::__construct();
        $this->table = self::TABLE;
        $this->pk_name = self::ID;
    }

}
