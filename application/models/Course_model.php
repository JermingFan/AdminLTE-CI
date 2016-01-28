<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Course_model extends Base_model {

    const TABLE = 'course';
    const ID = 'id';
    const NAME = 'name';
    const IMG = 'img';
    const DESC = 'desc';
    const TAGS = 'tags';
    const CREATED = 'created';
    //const DELETE = 'delete';

    function __construct() {
        parent::__construct();
        $this->table = self::TABLE;
        $this->pk_name = self::ID;
    }

}
