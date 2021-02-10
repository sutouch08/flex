<?php
class Order_invoice_model extends CI_Model
{
	public function __construct()
	{
		parent::__construct();
	}

	public function get($code)
	{
		$rs = $this->db->where('code', $code)->get('order_invoice');

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}


	public function get_details($code)
	{
		$rs = $this->db->where('invoice_code', $code)->get('order_invoice_detail');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function add(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->insert('order_invoice', $ds);
		}

		return FALSE;
	}


	public function add_detail(array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->insert('order_invoice_detail', $ds);
		}

		return FALSE;
	}


	public function update($code, array $ds = array())
	{
		if(!empty($ds))
		{
			return $this->db->where('code', $code)->update('order_invoice', $ds);
		}

		return FALSE;
	}


	public function update_details_status($code, $status)
	{
		//--- 0 = pending 1 = saved 2 = cancled
		return $this->db->set('status', $status)->where('invoice_code', $code)->update('order_invoice_detail');
	}



	public function get_total_amount_and_vat_amount($code)
	{
		$rs = $this->db
		->select_sum('amount', 'total_amount')
		->select_sum('vat_amount', 'total_vat_amount')
		->where('invoice_code', $code)
		->group_by('invoice_code')
		->get('order_invoice_detail');

		if($rs->num_rows() === 1)
		{
			return $rs->row();
		}

		return NULL;
	}

	public function get_non_invoice_list_by_customer($customer_code)
	{
		$rs = $this->db
		->select('code, total_amount')
		->where('role', 'S')
		->where('state', 8)
		->where('customer_code', $customer_code)
		->where('invoice_code IS NULL', NULL, FALSE)
		->order_by('code', 'DESC')
		->limit(50)
		->get('orders');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_billed_details($code)
	{
		$rs = $this->db
		->select_sum('qty')
		->select_sum('discount_amount')
		->select_sum('avgBillDiscAmount')
		->select_sum('total_amount')
		->select_sum('vat_amount')
		->select('reference, product_code, product_name, price, unit_code, unit_name')
		->select('vat_code, vat_rate, discount_label')
		->where('reference', $code)
		->group_by('product_code')
		->get('order_sold');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


	public function get_all_reference($code)
	{
		$rs = $this->db
		->distinct()
		->select('order_code')
		->where('invoice_code', $code)
		->group_by('order_code')
		->get('order_invoice_detail');

		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}



	public function remove_reference_detail($invoice_code, $order_code)
	{
		return $this->db->where('invoice_code', $invoice_code)->where('order_code', $order_code)->delete('order_invoice_detail');
	}


	public function is_exists_detail($order_code, $product_code)
	{
		$rs = $this->db
		->where('order_code', $order_code)
		->where('product_code', $product_code)
		->where('status !=', 2)
		->count_all_results('order_invoice_detail');

		if($rs > 0)
		{
			return TRUE;
		}

		return FALSE;
	}


	public function count_rows(array $ds = array())
	{
		if(!empty($ds['code']))
		{
			$this->db->like('code', $ds['code']);
		}

		if(!empty($ds['order_code']))
		{
			$this->db->like('reference', $ds['order_code']);
		}

		if(!empty($ds['customer']))
		{
			$this->db
			->group_start()
			->like('customer_code', $ds['customer'])
			->or_like('customer_name', $ds['customer'])
			->group_end();
		}

		if($ds['status'] != 'all')
		{
			$this->db->where('status', $ds['status']);
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->db
			->where('doc_date >=', from_date($ds['from_date']))
			->where('doc_date <=', to_date($ds['to_date']));
		}

		return $this->db->count_all_results('order_invoice');
	}


	public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
	{
		if(!empty($ds['code']))
		{
			$this->db->like('code', $ds['code']);
		}

		if(!empty($ds['order_code']))
		{
			$this->db->like('reference', $ds['order_code']);
		}

		if(!empty($ds['customer']))
		{
			$this->db
			->group_start()
			->like('customer_code', $ds['customer'])
			->or_like('customer_name', $ds['customer'])
			->group_end();
		}

		if($ds['status'] != 'all')
		{
			$this->db->where('status', $ds['status']);
		}

		if(!empty($ds['from_date']) && !empty($ds['to_date']))
		{
			$this->db
			->where('doc_date >=', from_date($ds['from_date']))
			->where('doc_date <=', to_date($ds['to_date']));
		}

		$this->db->order_by('code', 'DESC')->limit($perpage, $offset);

		$rs = $this->db->get('order_invoice');

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
    ->get('order_invoice');

    return $rs->row()->code;
  }

} //--- end class
 ?>
