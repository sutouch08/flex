<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_invoice extends PS_Controller
{
	public $menu_code = 'SOODIV';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = 'ใบส่งสินค้า/ใบกำกับภาษี';
  public $filter;

	public function __construct()
	{
		parent::__construct();
		$this->home = base_url().'orders/order_invoice';
		$this->load->model('orders/order_invoice_model');
		$this->load->model('masters/customers_model');
		$this->load->helper('vat');
		$this->title = getConfig('USE_VAT') == 1 ? 'ใบส่งสินค้า/ใบกำกับภาษี' : 'ใบส่งสินค้า';
	}


	public function index()
	{
		$filter = array(
			'code' => get_filter('code', 'invoice_code', ''),
			'order_code' => get_filter('order_code', 'invoice_reference', ''),
			'customer' => get_filter('customer', 'invoice_customer', ''),
			'from_date' => get_filter('from_date', 'invoice_from_date', ''),
			'to_date' => get_filter('to_date', 'invoice_to_date', ''),
			'status' => get_filter('status', 'invoice_status', 'all')
		);

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->order_invoice_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->order_invoice_model->get_list($filter, $perpage, $this->uri->segment($segment));
		$filter['order'] = $orders;

		$this->pagination->initialize($init);

    $this->load->view('order_invoice/order_invoice_list', $filter);
	}


	public function add_new()
	{
		$this->load->view('order_invoice/order_invoice_add');
	}


	public function add()
	{
		$sc = TRUE;
		$customer_code = $this->input->post('customer_code');
		$customer = $this->customers_model->get($customer_code);
		$doc_date = db_date($this->input->post('doc_date'));

		if(!empty($customer))
		{
			$code = $this->get_new_code($doc_date);
			$arr = array(
				'code' => $code,
				'doc_date' => $doc_date,
				'customer_code' => $customer->code,
				'customer_name' => $customer->name,
				'tax_id' => get_null($customer->Tax_Id),
				'branch_code' => get_null(trim($this->input->post('branch_code'))),
				'branch_name' => get_null(trim($this->input->post('branch_name'))),
				'address' => get_null(trim($this->input->post('address'))),
				'phone' => get_null(trim($this->input->post('phone'))),
				'remark' => get_null(trim($this->input->post('remark'))),
				'uname' => $this->_user->uname
			);

			if(! $this->order_invoice_model->add($arr))
			{
				$sc = FALSE;
				$this->error = "เพิ่มเอกสารไม่สำเร็จ";
			}

		}
		else
		{
			$sc = FALSE;
			$this->error = "รหัสลูกค้าไม่ถูกต้อง {$customer_code}";
		}

		if($sc === TRUE)
		{
			$ds = array(
				'status' => 'success',
				'code' => $code
			);
		}
		else
		{
			$ds = array(
				'status' => 'error',
				'message' => $this->error
			);
		}

		echo json_encode($ds);
	}


	public function edit($code)
	{
		$order = $this->order_invoice_model->get($code);

		if(!empty($order))
		{
			$details = $this->order_invoice_model->get_details($code);
			$reference = $this->order_invoice_model->get_all_reference($code);

			$ds = array(
				'order' => $order,
				'details' => $details,
				'reference' => $reference,
				'use_vat' => getConfig('USE_VAT') == 1 ? TRUE : FALSE
			);

			$this->load->view('order_invoice/order_invoice_edit', $ds);
		}
		else
		{
			$this->load->view('page_error');
		}
	}



	public function update()
	{
		$sc = TRUE;
		$code = $this->input->post('code');
		$customer_code = $this->input->post('customer_code');

		if(!empty($code))
		{
			$customer = $this->customers_model->get($customer_code);
			if(!empty($customer))
			{
				$arr = array(
					'doc_date' => db_date($this->input->post('doc_date')),
					'vat_type' => trim($this->input->post('vat_type')),
					'customer_code' => $customer->code,
					'customer_name' => $customer->name,
					'tax_id' => $customer->Tax_Id,
					'branch_code' => trim($this->input->post('branch_code')),
					'branch_name' => trim($this->input->post('branch_name')),
					'address' => trim($this->input->post('address')),
					'phone' => get_null(trim($this->input->post('phone'))),
					'remark' => get_null(trim($this->input->post('remark'))),
					'upd_user' => $this->_user->uname
				);

				if(!$this->order_invoice_model->update($code, $arr))
				{
					$sc = FALSE;
					$this->error = "Update failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid customer code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		$this->response($sc);
	}



	public function view_detail($code)
	{
		$order = $this->order_invoice_model->get($code);

		if(!empty($order))
		{
			$details = $this->order_invoice_model->get_details($code);
			$reference = $this->order_invoice_model->get_all_reference($code);

			$ds = array(
				'order' => $order,
				'details' => $details,
				'reference' => $reference,
				'use_vat' => getConfig('USE_VAT') == 1 ? TRUE : FALSE
			);

			$this->load->view('order_invoice/order_invoice_view_detail', $ds);
		}
		else
		{
			$this->load->view('page_error');
		}
	}


	public function print_invoice($code)
	{
		$order = $this->order_invoice_model->get($code);
		if(!empty($order))
		{
			$this->load->library('printer');

			$details = $this->order_invoice_model->get_details($code);
			$sale = $this->customers_model->get_saleman($order->customer_code);
			$ds = array(
				'title' => 'ใบกำกับภาษี',
				'order' => $order,
				'details' => $details,
				'saleman' => $sale
			);

			$this->load->view('print/print_invoice', $ds);
		}
	}



	public function save()
	{
		$sc = TRUE;
		$code = trim($this->input->post('code'));
		if(!empty($code))
		{
			$order = $this->order_invoice_model->get($code);
			if(!empty($order))
			{
				$reference = $this->order_invoice_model->get_all_reference($code);
				$order_code = "";

				if(!empty($reference))
				{
					$i = 1;
					foreach($reference as $rs)
					{
						$order_code .= $i === 1 ? $rs->order_code : ", {$rs->order_code}";
						$i++;
					}
				}

				$ds = $this->order_invoice_model->get_total_amount_and_vat_amount($code);


				$arr = array(
					'reference' => get_null($order_code),
					'total_amount' => (!empty($ds) ? $ds->total_amount : 0.00),
					'vat_amount' => (!empty($ds) ? $ds->total_vat_amount : 0.00),
					'status' => 1
				);

				if(! $this->order_invoice_model->update($code, $arr))
				{
					$sc = FALSE;
					$this->error = "Update Failed";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Invalid code : {$code}";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter : code";
		}

		$this->response($sc);
	}




	public function add_to_order()
	{
		$this->load->model('orders/orders_model');

		$sc = TRUE;

		$code = trim($this->input->post('code'));
		$list = $this->input->post('order_list');

		$order = $this->order_invoice_model->get($code);
		if(!empty($order))
		{
			if(!empty($list))
			{
				foreach($list as $order_code)
				{
					$details = $this->order_invoice_model->get_billed_details($order_code);
					if(!empty($details))
					{
						foreach($details as $rs)
						{
							//--- use id from order_sold to check duplicate item
							$is_exists = $this->order_invoice_model->is_exists_detail($rs->reference, $rs->product_code);

							if(! $is_exists)
							{
								$discount_label = $rs->discount_label;
								//--- recal discount
								if($rs->avgBillDiscAmount > 0 && $rs->discount_amount > 0)
								{
									$price = $rs->price * $rs->qty;
									$discount = $price == 0 ? 0 : ($rs->discount_amount/$price) * 100;
									$discount_label = $discount === 0 ? 0 : round($discount,2).'%';
								}


								$arr = array(
									'invoice_code' => $code,
									'order_code' => $rs->reference,
									'product_code' => $rs->product_code,
									'product_name' => $rs->product_name,
									'qty' => $rs->qty,
									'price' => $rs->price,
									'unit_code' => $rs->unit_code,
									'unit_name' => $rs->unit_name,
									'vat_code' => $rs->vat_code,
									'vat_rate' => $rs->vat_rate,
									'discount_label' => $discount_label,
									'discount_amount' => $rs->discount_amount,
									'amount' => $rs->total_amount,
									'vat_amount' => $rs->vat_amount
								);

								$this->order_invoice_model->add_detail($arr);
							}
						}
					}

					$ds = array(
						'invoice_code' => $code
					);

					$this->orders_model->update($order_code, $ds);
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : order list";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "เลขที่เอกสารไม่ถูกต้อง";
		}

		$this->response($sc);
	}



	public function remove_reference_detail()
	{
		$this->load->model('orders/orders_model');

		$sc = TRUE;

		$code = trim($this->input->post('code'));
		$reference = trim($this->input->post('reference'));

		if(!empty($code) && !empty($reference))
		{
			if(! $this->order_invoice_model->remove_reference_detail($code, $reference))
			{
				$sc = FALSE;
				$this->error = "ลบรายการไม่สำเร็จ";
			}
			else
			{
				$arr = array(
					'invoice_code' => NULL
				);

				$this->orders_model->update($reference, $arr);
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing required parameter";
		}

		$this->response($sc);
	}




	public function get_order_list()
	{
		$customer_code = trim($this->input->get('customer_code'));
		$orders = $this->order_invoice_model->get_non_invoice_list_by_customer($customer_code);

		$ds = array();
		if(!empty($orders))
		{
			foreach($orders as $rs)
			{
				$arr = array(
					'orderCode' => $rs->code,
					'amount' => number($rs->total_amount, 2)
				);

				array_push($ds, $arr);
			}
		}
		else
		{
			$arr = array(
				"nodata" => "no data"
			);

			array_push($ds, $arr);
		}

		echo json_encode($ds);
	}





	public function get_new_code($date = NULL)
  {
    $date = empty($date) ? date('Y-m-d') : $date;
    $Y = date('y', strtotime($date));
    $M = date('m', strtotime($date));
    $prefix = getConfig('PREFIX_INVOICE');
    $run_digit = getConfig('RUN_DIGIT_INVOICE');
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_invoice_model->get_max_code($pre);
    if(! is_null($code))
    {
      $run_no = mb_substr($code, ($run_digit*-1), NULL, 'UTF-8') + 1;
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', $run_no);
    }
    else
    {
      $new_code = $prefix . '-' . $Y . $M . sprintf('%0'.$run_digit.'d', '001');
    }

    return $new_code;
  }


	public function clear_filter()
	{
		$filter = array(
			'invoice_code',
			'invoice_reference',
			'invoice_customer',
			'invoice_from_date',
			'invoice_to_date',
			'invoice_status'
		);

		clear_filter($filter);

		echo 'done';
	}


} //--- end class

?>
