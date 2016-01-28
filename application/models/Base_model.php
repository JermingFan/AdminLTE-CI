<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Base_model extends CI_Model {

  public $table = NULL; // Table name
  public $pk_name = NULL; // Table primary key column name

  public $limit = NULL; // Query Limit
  public $offset = NULL; // Query Offset

  protected $_list_fields = array(); // Local store of list_fields results


  /**
   * Constructor
   */
  public function __construct() {
    parent::__construct();
  }

  /**
   * Get the model table name
   */
  public function get_table() {
    return $this->table;
  }

  /**
   * Sets the current table
   * @param string  table name
   */
  public function set_table($table) {
    $this->table = $table;
  }

  /**
   * Returns the first PK field nam found for the given table
   */
  public function get_pk_name($table = NULL) {
    if (!is_null($table)) {
      $fields = $this->db->field_data($table);
      foreach ($fields as $field) {
        if ($field->primary_key) {
          return $field->name;
          break;
        }
      }
    } else {
      return $this->pk_name;
    }
    return FALSE;
  }

  /**
   * Sets the current table pk
   * @param string  table pk name
   */
  public function set_pk_name($pk_name) {
    $this->pk_name = $pk_name;
  }

  /** 
   * Get one element
   *
   * @param   string  where array
   * @param   string  Optional. Lang code
   * @return  array   array of media
   */
  public function get($where) {
    $data = array();

    if (is_array($where)) {
      foreach ($where as $key => $value) {
        $this->db->where($this->table .'.'. $key, $value);
      }
    } else {
      $this->db->where($this->table .'.'. $this->pk_name, $where);
    }

    // ID
    if ($this->pk_name != NULL) {
      $this->db->select($this->table .'.'. $this->pk_name .' as id', FALSE);
    }

    $query = $this->db->get($this->table);

    if ($query->num_rows() > 0) {
      $data = $query->row_array();
      $query->free_result();
    }

    return $data;
  }

  /**
   * Insert a row
   *
   * @access  public
   * @param   array An associative array of data
   * @return  the last inserted id
   *
   */
  public function insert($data = NULL, $table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    $data = $this->clean_data($data, $table);
    $this->db->insert($table, $data);

    return $this->db->insert_id();
  }

  public function insert_ignore($data = NULL, $table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    $data = $this->clean_data($data, $table);

    $insert_query = $this->db->insert_string($table, $data);
    $insert_query = str_replace('INSERT INTO', 'INSERT IGNORE INTO', $insert_query);
    $inserted = $this->db->query($insert_query);

    return $inserted;
  }

  /**
   * Insert row(s)
   */
  public function insert_batch($data = NULL, $table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    $array = array();
    foreach ($data as $val) {
      $array[] = $this->clean_data($val, $table);
    }
    $rows = $this->db->insert_batch($table, $array);

    return $rows;
  }

  /**
   * Update a row
   *
   * @access  public
   *
   * @param   Mixed   Where condition. If single value, PK of the table
   * @param   array   An associative array of data
   * @param   String  Table name. If not set, current models table
   *
   * @return  int     Number of updated rows
   *
   */
  public function update($where = NULL, $data = NULL, $table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    $data = $this->clean_data($data, $table);

    if (is_array($where)) {
      $this->db->where($where);
    } else {
      $pk_name = $this->get_pk_name($table);
      $this->db->where($pk_name, $where);
    }

    $this->db->update($table, $data);

    return (int)$this->db->affected_rows();
  }

  /**
   * Delete row(s)
   *
   * @param null $where   Where condition. If single value, PK of the table
   * @param null $table
   *
   * @return int        Affected rows
   */
  public function delete($where = NULL, $table = NULL, $delete = FALSE) {
    $table = (!is_null($table)) ? $table : $this->table;

    if (is_array($where)) {
      $this->db->where($where);
    } else {
      $pk_name = $this->get_pk_name($table);
      $this->db->where($pk_name, $where);
    }

    if ($delete == TRUE) {
      $this->db->delete($table);
    } else {
      $this->db->update($table, array('delete' => 1));
    }

    return (int)$this->db->affected_rows();
  }

  /**
   * Save one element
   *
   * @param   array Standard data table
   *
   * @return  int   Saved element ID
   */
  public function save($data) {
    /*
     * Base data save
     */
    $data = $this->clean_data($data);

    // Insert
    if (!isset($data[$this->pk_name]) || $data[$this->pk_name] == '' ) {
      // Remove the ID so the generated SQL will be clean (no empty String insert in the table PK field)
      unset($data[$this->pk_name]);

      $this->db->insert($this->table, $data);
      $id = $this->db->insert_id();
    } else {
      // Update or Insert
      $where = array($this->pk_name => $data[$this->pk_name]);

      // Update
      if( $this->exists($where, $this->table)) {
        $this->db->where($this->pk_name, $data[$this->pk_name]);
        $this->db->update($this->table, $data);
      }
      // Insert
      else {
        $this->db->insert($this->table, $data);
      }

      $id = $data[$this->pk_name];
    }
    return $id;
  }

  /**
   * Get all the records
   *
   * @param null $table
   *
   * @return mixed
   */
  public function get_all($table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    $query = $this->db->get($table);
    
    return $query->result_array();
  }

  /**
   * Get one row
   *
   * @access  public
   * @param   int   The result id
   * @return  object  A row object
   *
   */
  public function get_row($id = NULL) {
    $this->db->where($this->pk_name, $id);
    $query = $this->db->get($this->table);

    return $query->row();
  }

  /**
   * Get one row_array
   *
   * @param null $where
   * @param null $table
   *
   * @return mixed
   */
  public function get_row_array($where = NULL, $table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    if (is_array($where)) {
      // Perform conditions from the $where array
      foreach(array('limit', 'offset', 'order_by', 'like') as $key) {
        if (isset($where[$key])) {
          call_user_func(array($this->db, $key), $where[$key]);
          unset($where[$key]);
        }
      }
      if (isset($where['where_in'])) {
        foreach($where['where_in'] as $key => $value) {
          if (!empty($value)) {
            $this->db->where_in($key, $value);
          }
        }
        unset($where['where_in']);
      }

      $this->db->where($where);
    }

    $query = $this->db->get($table);

    return $query->row_array();
  }
  
  /**
   * Get array of records
   *
   * @access  public
   * @param   array   An associative array
   * @param   string  table name. Optional.
   * @return  array   Array of records
   *
   */
  public function get_list($where = NULL, $table = NULL) {
    $data = array();

    $table = (!is_null($table)) ? $table : $this->table;

    // Perform conditions from the $where array
    foreach(array('limit', 'offset', 'order_by', 'like') as $key) {
      if (isset($where[$key])) {
        call_user_func(array($this->db, $key), $where[$key]);
        unset($where[$key]);
      }
    }

    if (isset($where['where_in'])) {
      foreach($where['where_in'] as $key => $value) {
        $this->db->where_in($key, $value);
      }
      unset($where['where_in']);
    }


    if (!empty($where)) {
      foreach($where as $cond => $value) {
        if (is_string($cond)) {
          $this->db->where($cond, $value);
        } else {
          $this->db->where($value);
        }
      }
    }

    // Add ID
    if ($table == $this->table && $this->pk_name != NULL) {
      $this->db->select($table .'.'. $this->pk_name .' as id', FALSE);
    }

    $this->db->select($table .'.*', FALSE);

    $query = $this->db->get($table);

    if ($query->num_rows() > 0) {
      $data = $query->result_array();
    }

    $query->free_result();

    return $data;
  }


  public function simple_search($term, $field, $limit) {
    $data = array();

    $this->db->like($this->table .'.'. $field, $term);
    $this->db->limit($limit);
    $query = $this->db->get($this->table);

    if ($query->num_rows() > 0) {
      $data = $query->result_array();
    }

    return $data;
  }

  /**
   * Checks if one table is empty, based on given conditions
   *
   * @param null $where
   * @param null $table
   *
   * @return bool
   */
  public function is_empty($where = NULL, $table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    $query = $this->db->get_where($table, $where);

    if ($query->num_rows() > 0) {
      return FALSE;
    }

    return TRUE;
  }

  /**
   * Check if a record exists in a table
   *
   * @param array conditions
   * @param string  table name
   *
   * @access  public
   * @return  boolean
   *
   */
  public function exists($where = NULL, $table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    $query = $this->db->get_where($table, $where);

    if ($query->num_rows() > 0) {
      return TRUE;
    }

    return FALSE;
  }
  
  /**
   * Removes from the data array the index which are not in the table
   *
   * @param      $data    The data array to clean
   * @param bool $table   Reference table. $this->table if not set.
   *
   * @return array
   */
  public function clean_data($data, $table = NULL) {
    $cleaned_data = array();

    if (!empty($data)) {
      $table = (!is_null($table)) ? $table : $this->table;

      $fields = $this->db->list_fields($table);
      $fields = array_fill_keys($fields, '');

      $cleaned_data = array_intersect_key($data, $fields);
    }
    foreach($cleaned_data as $key => $row) {
      if (is_array($row)) {
        unset($cleaned_data[$key]);
      }
    }
    return $cleaned_data;
  }

  /**
   * Check for a table field
   *
   * @param String    Table name
   * @param null $table
   *
   * @return  Boolean True if the field is found
   *
   */
  public function has_field($field, $table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    $fields = $this->db->list_fields($table);

    if (in_array($field, $fields)) return TRUE;

    return FALSE;
  }

  /**
   * List fields from one table of the current DB group
   * and stores the result locally.
   *
   * @param string
   * @return  Array List of table fields
   *
   */
  public function list_fields($table = NULL) {
    $table = (!is_null($table)) ? $table : $this->table;

    if (isset($this->_list_fields[$table])) {
      return $this->_list_fields[$table];
    }

    $this->_list_fields[$table] = $this->db->list_fields($table);

    return $this->_list_fields[$table];
  }

  public function last_query() {
    return $this->db->last_query();
  }

  public function table_exists($table) {
    return $this->db->table_exists($table);
  }

//  public function sql_query($sql)
//  {
//    return $this->db->query($sql);
//
//  }
}
