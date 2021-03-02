<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Shop extends PS_Controller
{
  public $menu_code = 'DBPOSS';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'POS';
	public $title = 'เพิ่ม/แก้ไข จุดขาย';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/shop';
    $this->load->model('masters/shop_model');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'shop_code', ''),
			'name' => get_filter('name', 'shop_name', ''),
			'zone' => get_filter('zone', 'shop_zone', ''),
			'status' => get_filter('status', 'shop_status', 'all')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->shop_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$list = $this->shop_model->get_list($filter, $perpage, $this->uri->segment($segment));

    $filter['list'] = $list;

		$this->pagination->initialize($init);
    $this->load->view('masters/shop/shop_list', $filter);
  }



	public function add_new()
	{
		$this->load->view('masters/shop/shop_add');

	}


	public function add()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			if($this->input->post('code'))
			{
				if($this->input->post('name') && $this->input->post('zone_code'))
				{
					if($this->shop_model->is_exists_code(trim($this->input->post('code'))))
					{
						$sc = FALSE;
						$this->error = "รหัสซ้ำ กรุณากำหนดรหัสจุดขายใหม่";
					}

					if($sc === TRUE && $this->shop_model->is_exists_name(trim($this->input->post('name'))))
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อจุดขายใหม่";
					}

					if($sc === TRUE && $this->shop_model->is_exists_zone(trim($this->input->post('zone_code'))))
					{
						$sc = FALSE;
						$this->error = "โซนซ้ำ โซนนี้ถูกใช้งานแล้ว กรุณากำหนดโซนอื่น";
					}

					if($sc === TRUE)
					{
						$arr = array(
							'code' => trim($this->input->post('code')),
							'name' => trim($this->input->post('name')),
							'zone_code' => trim($this->input->post('zone_code')),
							'customer_code' => trim($this->input->post('customer_code')),
							'active' => $this->input->post('active')
						);

						if(! $this->shop_model->add($arr))
						{
							$sc = FALSE;
							$error = $this->db->error();
							$this->error = $error['message'];
						}
					}

				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing Parameter";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}




	public function edit($code)
	{
		$shop = $this->shop_model->get($code);

		if(!empty($shop))
		{
			$this->load->view('masters/shop/shop_edit', $shop);
		}
		else
		{
			$this->page_error();
		}
	}


	public function update()
	{
		$sc = TRUE;

		if($this->pm->can_add)
		{
			if($this->input->post('code'))
			{
				$code = trim($this->input->post('code'));
				$old_name = trim($this->input->post('old_name'));

				if($this->input->post('name') && $this->input->post('zone_code'))
				{

					if($sc === TRUE && $this->shop_model->is_exists_name(trim($this->input->post('name')), $old_name))
					{
						$sc = FALSE;
						$this->error = "ชื่อซ้ำ กรุณากำหนดชื่อจุดขายใหม่";
					}

					if($sc === TRUE && $this->shop_model->is_exists_zone(trim($this->input->post('zone_code')), $code))
					{
						$sc = FALSE;
						$this->error = "โซนซ้ำ โซนนี้ถูกใช้งานแล้ว กรุณากำหนดโซนอื่น";
					}

					if($sc === TRUE)
					{
						$arr = array(
							'name' => trim($this->input->post('name')),
							'zone_code' => trim($this->input->post('zone_code')),
							'customer_code' => trim($this->input->post('customer_code')),
							'active' => $this->input->post('active')
						);

						if(! $this->shop_model->update($code, $arr))
						{
							$sc = FALSE;
							$error = $this->db->error();
							$this->error = "Update Failed : ".$error['message'];
						}
					}

				}
				else
				{
					$sc = FALSE;
					$this->error = "Missing Parameter";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing required parameter : code";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Permission";
		}

		$this->response($sc);
	}



	public function delete()
	{
		$sc = TRUE;

		$code = $this->input->post('code');
		if(! is_null($code))
		{
			if($this->pm->can_delete)
			{
				//---- check transection
				$transection = $this->shop_model->has_transection($code);

				if(! $transection)
				{
					if( ! $this->shop_model->delete($code))
					{
						$sc = FALSE;
						$error = $this->db->error();
						$this->error = "Delete Failed : ".$error['message'];
					}
				}
				else
				{
					$sc = FALSE;
					$this->error = "Delete Failed : Transection exists";
				}
			}
			else
			{
				$sc = FALSE;
				$this->error = "Missing Permission";
			}
		}
		else
		{
			$sc = FALSE;
			$this->error = "Missing Parameter : code";
		}


		$this->response($sc);
	}



	public function get_zone_code_and_name()
	{
		$txt = trim($this->input->get('term'));
		$ds = array();
		if(! is_null($txt))
		{
			if($txt !== '*')
			{
				$this->db->group_start();
				$this->db->like('code', $txt);
				$this->db->or_like('name', $txt);
				$this->db->group_end();
			}

			$rs = $this->db->limit(20)->get('zone');

			if($rs->num_rows() > 0)
			{
				foreach($rs->result() as $zone)
				{
					$ds[] = $zone->code.' | '.$zone->name;
				}
			}
			else
			{
				$ds[] = 'not found';
			}

		}

		echo json_encode($ds);

	}




	public function get_customer_code_and_name()
	{
		$txt = trim($this->input->get('term'));
		$ds = array();
		if(! is_null($txt))
		{
			if($txt !== '*')
			{
				$this->db->group_start();
				$this->db->like('code', $txt);
				$this->db->or_like('name', $txt);
				$this->db->group_end();
			}

			$rs = $this->db->limit(20)->get('customers');

			if($rs->num_rows() > 0)
			{
				foreach($rs->result() as $customer)
				{
					$ds[] = $customer->code.' | '.$customer->name;
				}
			}
			else
			{
				$ds[] = 'not found';
			}

		}

		echo json_encode($ds);

	}



  public function clear_filter()
  {
    $filter = array(
			'shop_code',
			'shop_name',
			'shop_zone',
			'shop_customer',
			'shop_status'
		);


    clear_filter($filter);
  }

} //--- end class

 ?>
