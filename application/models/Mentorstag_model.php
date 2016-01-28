<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Mentorstag_model extends Base_model {

    const TABLE = 'mentorstag';
    const ID = 'id';
    const NAME = 'name';
    const SORT = 'sort';
    const CREATED = 'created';
    //const DELETE = 'delete';

    function __construct() {
        parent::__construct();
        $this->table = self::TABLE;
        $this->pk_name = self::ID;
    }

}