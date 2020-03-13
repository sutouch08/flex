<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Quotation_model extends CI_Model
{

  public function __construct()
  {
    parent::__construct();
  }

  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('order_quotation');
    if(!empty($rs))
    {
      return $rs->row();
    }

    return FALSE;
  }

  

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_quotation', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('order_quotation', $ds);
    }

    return FALSE;
  }


} //--- End class


 ?>
