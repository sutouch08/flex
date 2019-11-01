<?php
class Employee_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('employee');

    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }

}
?>
