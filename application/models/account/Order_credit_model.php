<?php
class Order_credit_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function get($order_code)
  {
    $rs = $this->db->where('order_code', $order_code)->get('order_credit');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  //---- ตั้งหนี้
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('order_credit', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('order_code', $code)->update('order_credit', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('order_code', $code)->delete('order_credit');
  }


  public function is_exists($code)
  {
    $rs = $this->db->where('order_code', $code)->get('order_credit');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function recal_balance($code)
  {
    $this->db->set('balance', 'amount - paid', FALSE)->where('order_code', $code)->update('order_credit');
    $this->db->set('valid', 1)->where('order_code', $code)->where('balance <=', 0, FALSE)->update('order_credit');
  }


} //--- end class

 ?>
