<?php
class Po_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  //--- get document data
  public function get($code)
  {
    $rs = $this->db->where('code', $code)->get('po');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }


  //--- get po detail in document
  public function get_details($code)
  {
    $rs = $this->db->where('po_code', $code)->get('po_detail');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_detail($po_code, $product_code)
  {
    $rs = $this->db->where('po_code', $po_code)->where('product_code', $product_code)->get('po_detail');
    if($rs->num_rows() === 1)
    {
      return $rs->row();
    }

    return FALSE;
  }



  //--- add new document
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('po', $ds);
    }

    return FALSE;
  }



  public function update($code, array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('po', $ds);
    }

    return FALSE;
  }

  public function add_detail(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('po_detail', $ds);
    }

    return FALSE;
  }


  public function update_detail($id, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('id', $id)->update('po_detail', $ds);
    }

    return FALSE;
  }



  public function delete_detail($id)
  {
    return $this->db->where('id', $id)->delete('po_detail');
  }


  public function delete_all_details($code)
  {
    return $this->db->where('po_code', $code)->delete('po_detail');
  }



  public function delete_po($code)
  {
    return $this->db->where('code', $code)->delete('po');
  }


  public function change_status($code, $status)
  {
    return $this->db->set('status', $status)->where('code', $code)->update('po');
  }


  public function close_po($code)
  {
    $this->db->trans_start();
    $this->db->set('status', 3)->where('code', $code)->update('po');
    $this->db->set('valid', 1)->where('po_code', $code)->update('po_detail');
    $this->db->trans_complete();

    return $this->db->trans_status();
  }


  public function un_close_po($code, $status)
  {
    $this->db->trans_start();
    $this->db->set('status', $status)->where('code', $code)->update('po');
    $this->db->set('valid', 0)->where('po_code', $code)->update('po_detail');
    $this->db->trans_complete();
    return $this->db->trans_status();
  }


  public function get_list(array $ds = array(), $perpage = NULL, $offset = NULL)
  {
    if(!empty($ds))
    {
      $this->db->select('po.*, vender.name');
      $this->db->from('po')->join('vender', 'po.vender_code = vender.code','left');

      if(!empty($ds['code']))
      {
        $this->db->like('po.code', $ds['code']);
      }

      if(!empty($ds['vender']))
      {
        $this->db->group_start();
        $this->db->like('vender.code', $ds['vender'])->or_like('vender.name', $ds['vender']);
        $this->db->group_end();
      }

      if($ds['status'] !== 'all')
      {
        //-- 0 = not save, 1= saved (open) , 2 = partail received, 3 = closed, 4 = cancled
        if($ds['status'] == 1)
        {
          $this->db->where_in('po.status', array('1', '2'));
        }
        else
        {
          $this->db->where('po.status', $ds['status']);
        }
      }

      if($ds['from_date'] != '' && $ds['to_date'] != '')
      {
        $this->db->where('date_add >=', from_date($ds['from_date']));
        $this->db->where('date_add <=', to_date($ds['to_date']));
      }

      $this->db->order_by('po.code', 'DESC');

      if(!empty($perpage))
      {
        $offset = $offset === NULL ? 0 : $offset;
        $this->db->limit($perpage, $offset);
      }

      $rs = $this->db->get();

      return $rs->result();
    }

    return FALSE;
  }



  public function count_rows(array $ds = array())
  {
    if(!empty($ds))
    {
      $this->db->from('po')->join('vender', 'po.vender_code = vender.code','left');

      if(!empty($ds['code']))
      {
        $this->db->like('po.code', $ds['code']);
      }

      if(!empty($ds['vender']))
      {
        $this->db->group_start();
        $this->db->like('vender.code', $ds['vender'])->or_like('vender.name', $ds['vender']);
        $this->db->group_end();
      }

      if($ds['status'] !== 'all')
      {
        $this->db->where('po.status', $ds['status']); //-- 0 not save, 1= saved (open) , 2 = closed, 3 = cancled
      }

      if($ds['from_date'] != '' && $ds['to_date'] != '')
      {
        $this->db->where('date_add >=', from_date($ds['from_date']));
        $this->db->where('date_add <=', to_date($ds['to_date']));
      }

      return $this->db->count_all_results();
    }

    return 0;
  }


  public function is_exists_detail($po_code, $product_code)
  {
    $rs = $this->db->select('id')->where('po_code', $po_code)->where('product_code', $product_code)->get('po_detail');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }






  public function get_sum_amount($code)
  {
    $rs = $this->db
    ->select_sum('total_amount')
    ->where('po_code', $code)
    ->get('po_detail');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->total_amount;
    }

    return 0;
  }


  public function get_sum_received($code)
  {
    $rs = $this->db->select_sum('received', 'received')->where('po_code', $code)->get('po_detail');
    if($rs->num_rows() === 1)
    {
      $qty = is_null($rs->row()->received) ? 0 : $rs->row()->received;
      return $qty;
    }

    return FALSE;
  }


  public function get_max_code($code)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $code, 'after')
    ->order_by('code', 'DESC')
    ->get('po');

    return $rs->row()->code;
  }



} //---- end class
 ?>
