<?php
class Customers_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }




  public function get_credit($code)
  {
    $rs = $this->db->where('customer_code', $code)->get('customer_credit');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->balance;
    }

    return 0.00;
  }



  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('customers', $ds);
    }

    return FALSE;
  }


  //--- add new credit
  public function add_credit(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('customer_credit', $ds);
    }

    return FALSE;
  }

  //--- change credit line
  public function update_credit($code, $amount)
  {
    return $this->db->set('amount', $amount)->where('customer_code', $code)->update('customer_credit');
  }


  public function update_balance($code)
  {
    $qr = "UPDATE customer_credit SET balance = amount - used WHERE customer_code = '{$code}'";
    return $this->db->query($qr);
  }


  public function update_used($code, $used)
  {
    return $this->db->set('used', $used)->where('customer_code', $code)->update('customer_credit');
  }



  public function get_credit_balance($code)
  {
    $rs = $this->db->where('customer_code', $code)->get('customer_credit');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->balance;
    }

    return 0;
  }


  public function get_credit_amount($code)
  {
    $rs = $this->db->select('amount')->where('customer_code', $code)->get('customer_credit');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->amount;
    }

    return FALSE;
  }


  public function has_credit($code)
  {
    $rs = $this->db->where('customer_code', $code)->get('customer_credit');
    if($rs->num_rows() === 1)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('customers', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('customers');
  }


  public function count_rows($code = '', $name = '', $group = '', $kind = '', $type = '', $class = '', $area = '')
  {
    $this->db->select('code');

    if($code != '')
    {
      $this->db->like('code', $code);
    }

    if($name != '')
    {
      $this->db->like('name', $name);
    }


    if($group != '')
    {
      $this->db->where('group_code', $group);
    }


    if($kind != '')
    {
      $this->db->where('kind_code', $kind);
    }

    if($type != '')
    {
      $this->db->where('type_code', $type);
    }

    if($class != '')
    {
      $this->db->where('class_code', $class);
    }

    if($area != '')
    {
      $this->db->where('area_code', $area);
    }


    $rs = $this->db->get('customers');

    return $rs->num_rows();
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  public function get_name($code)
  {
    $rs = $this->db->select('name')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->name;
    }

    return NULL;
  }



  public function get_data($code = '', $name = '', $group = '', $kind = '', $type = '', $class = '', $area = '', $perpage = '', $offset = '')
  {
    if($code != '')
    {
      $this->db->like('code', $code);
    }

    if($name != '')
    {
      $this->db->like('name', $name);
    }


    if($group != '')
    {
      $this->db->where('group_code', $group);
    }


    if($kind != '')
    {
      $this->db->where('kind_code', $kind);
    }

    if($type != '')
    {
      $this->db->where('type_code', $type);
    }

    if($class != '')
    {
      $this->db->where('class_code', $class);
    }

    if($area != '')
    {
      $this->db->where('area_code', $area);
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('customers');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('customers');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = '')
  {
    if($old_name != '')
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->where('name', $name)->get('customers');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function get_sale_code($code)
  {
    $rs = $this->db->select('sale_code')->where('code', $code)->get('customers');
    if($rs->num_rows() === 1)
    {
      return $rs->row()->sale_code;
    }

    return NULL;
  }


  public function search($txt)
  {
    $qr = "SELECT code FROM customers WHERE code LIKE '%".$txt."%' OR name LIKE '%".$txt."%'";
    $rs = $this->db->query($qr);
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }
    else
    {
      return array();
    }

  }



  public function getSlp()
  {
    $rs = $this->db
    ->where('Active', 1)
    ->get('saleman');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }

}
?>
