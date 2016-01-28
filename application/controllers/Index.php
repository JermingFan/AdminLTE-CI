<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Index extends MY_Controller {

  function __construct() {
    parent::__construct();
    $this->load->model(array(
      'Course_model',
      'Incubator_model',
      'Incubatortag_model',
      'Mentors_model',
      'Mentorstag_model',
      'Partner_model',
      'Partnertag_model'
    ));
  }

  public function index() {
    $limit = 8;
    $course = self::course($limit);
    $incubator = self::incubator($limit);
    $incubatortag = self::incubatortag();
    $mentors = self::mentors($limit);
    $mentorstag = self::mentorstag();
    $partner = self::partner();

    $ret = array(
      'course' => $course,
      'incubator' => $incubator,
      'incubatortag' => $incubatortag,
      'mentors' => $mentors,
      'mentorstag' => $mentorstag,
      'partner' => $partner,
    );

    $this->_display('unicorn/index.html', $ret);
  }

  public function dev()
  {
    $limit = 8;
    $course = self::course($limit);
    $incubator = self::incubator($limit);
    $incubatortag = self::incubatortag();
    $mentors = self::mentors($limit);
    $mentorstag = self::mentorstag();
    $partner = self::partner($limit);

    $ret = array(
      'course' => $course,
      'incubator' => $incubator,
      'incubatortag' => $incubatortag,
      'mentors' => $mentors,
      'mentorstag' => $mentorstag,
      'partner' => $partner,
    );

    $this->_display('unicorn/index-dev.html', $ret);
  }

  public function course($limit)
  {
    $sql = "select c.*, group_concat(m.id order by m.sort desc) as mid,  group_concat(m.`name` order by m.sort desc) as mname
            from course c
            left join relation r on r.cid = c.id
            left join mentors m on m.id = r.mid
            group by c.id
            order by c.id
            limit ". $limit;

    $res = $this->db->query($sql);
    $data = $res->result_array();
    $info = array();
    foreach($data as $d)
    {
        $mid = (!empty($d['mid'])) ? explode(',',$d['mid'] ) : array();
        $mname = (!empty($d['mname'])) ? explode(',',$d['mname'] ) : array();
        $mentors = array();
        for($i = 0;$i<count($mid);$i++)
        {
            $mentors[] = array(
                'id' => intval($mid[$i]),
                'name' => $mname[$i],
            );
        }

        $info[] = array(
            'id' => intval($d['id']),
            'img' => $d['img'],
            'name' => $d['name'],
            'desc' => $d['desc'],
            'mentors' => $mentors
        );
    }

    return $info;
  }

  public function incubator($limit)
  {
    //$this->db->limit($limit);
    $incubator = $this->Incubator_model->get_sort();

    $data = array();
    foreach ($incubator as $item) {
      //$branch_array = array();
      if (isset($item[Incubator_model::BRANCHES]) && !empty($item[Incubator_model::BRANCHES])) {
        $item[Incubator_model::BRANCHES] = str_replace(' ', '', $item[Incubator_model::BRANCHES]);
        $item[Incubator_model::BRANCHES] = str_replace(',', '、', $item[Incubator_model::BRANCHES]);
        //$branch_array = array_values(explode(',', $item[Incubator_model::BRANCHES]));
      }
      $data[$item[Incubator_model::TYPE]][] = array(
        'name' => (string)$item[Incubator_model::NAME],
        'img' => (string)$item[Incubator_model::IMG],
        'branches' => (string)$item[Incubator_model::BRANCHES],
        //'branches' => $branch_array,
      );
    }

    // get incubator city list
    $incubatortag = $this->Incubatortag_model->get_all();
    $citys = array();
    foreach ($incubatortag as $item) {
      $citys[$item[Incubatortag_model::ID]] = $item[Incubatortag_model::NAME];
    }

    // packaging data
    $ret = array();
    foreach ($data as $key => $val) {
      if (!isset($citys[$key])) {
        continue;
      }
      $ret[] = array(
        'name' => (string)$citys[$key],
        'tags' => $val,
      );
    }

    return $ret;
  }

  public function incubatortag()
  {
    $incubatortag = $this->Incubatortag_model->get_all();
    return $incubatortag;
  }

  public function mentors($limit)
  {
    $mentors = $this->Mentors_model->get_sort(0, 0, $limit);
    return $mentors;
  }

  public function mentorstag()
  {
    $mentorstag = $this->Mentorstag_model->get_all();
    return $mentorstag;
  }

  public function partner()
  {
    $partners = $this->Partner_model->get_list_join_tag();
    $ret = array();

    $partner = array();
    foreach ($partners as $data) {
      $partner[$data[Partner_model::TYPE]][] = array(
        'id' => intval($data[Partner_model::ID]),
        'type' => intval($data[Partner_model::TYPE]),
        'name' => (string)$data[Partner_model::NAME],
        'img' => (string)$data[Partner_model::IMG],
        'url' => (string)$data[Partner_model::URL],
        'sort' => intval($data[Partner_model::SORT]),
        'created' => intval($data[Partner_model::CREATED])
      );

      $ret[$data[Partner_model::TYPE]] = array(
        'tagid' => intval($data['tagid']),
        'tagname' => (string)$data['tagname'],
        //'partner' => array_values($partner[$data[Partner_model::TYPE]])
      );
    }
    foreach ($partner as $key => $val) {
      $ret[$key]['partner'] = array_values($val);
    }
    $ret = array_values($ret);
    return $ret;
  }
}
