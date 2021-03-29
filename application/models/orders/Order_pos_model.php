<?php
class Order_pos_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



	public function add(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->insert('order_pos', $ds);
		}

		return FALSE;
	}

	public function add_detail(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->insert('order_pos_detail', $ds);
		}

		return FALSE;
	}



  public function count_hold_bills($pos_id)
  {
    return $this->db->where('pos_id', $pos_id)->where('status', 2)->count_all_results('order_pos');
  }


  public function get_hold_bills($pos_id)
  {
    $rs = $this->db->where('pos_id', $pos_id)->where('status', 2)->get('order_pos');
    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

	public function get_max_code($pre)
  {
    $rs = $this->db
    ->select_max('code')
    ->like('code', $pre, 'after')
    ->order_by('code', 'DESC')
    ->get('order_pos');

		if($rs->num_rows() == 1)
		{
			return $rs->row()->code;
		}

		return  NULL;
  }

} //---- end model
?>
