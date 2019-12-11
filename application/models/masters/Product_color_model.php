<?php
class Product_color_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return  $this->db->insert('product_color', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->where('code', $code);
      return $this->db->update('product_color', $ds);
    }

    return FALSE;
  }


  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('product_color');
  }


  public function count_rows($code = '', $name = '', $status = 2)
  {
    $this->db->select('code');

    if($status != 2)
    {
      $this->db->where('active', $status);
    }

    if($code != '')
    {
      $this->db->like('code', $code);
    }

    if($name != '')
    {
      $this->db->like('name', $name);
    }

    $rs = $this->db->get('product_color');

    return $rs->num_rows();
  }




  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('product_color');
    return $rs->row();
  }



  public function get_name($code)
  {
    if($code === NULL OR $code === '')
    {
      return $code;
    }

    $rs = $this->db->select('name')->where('code', $code)->get('product_color');
    return $rs->row()->name;
  }




  public function get_data($code = '', $name = '', $status = 1, $perpage = '', $offset = '')
  {
    if($status != 2)
    {
      $this->db->where('active', $status);
    }

    if($code != '')
    {
      $this->db->like('code', $code);
    }

    if($name != '')
    {
      $this->db->like('name', $name);
    }

    if($perpage != '')
    {
      $offset = $offset === NULL ? 0 : $offset;
      $this->db->limit($perpage, $offset);
    }

    $rs = $this->db->get('product_color');

    return $rs->result();
  }




  public function is_exists($code, $old_code = '')
  {
    if($old_code != '')
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->where('code', $code)->get('product_color');

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

    $rs = $this->db->where('name', $name)->get('product_color');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function set_active($code, $active)
  {
    return $this->db->set('active', $active)->where('code', $code)->update('product_color');
  }

  public function count_members($code)
  {
    $this->db->select('active')->where('color_code', $code);
    $rs = $this->db->get('products');
    return $rs->num_rows();
  }


}
?>