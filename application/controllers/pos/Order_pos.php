<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_pos extends PS_Controller
{
  public $menu_code = 'SOPOS';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = '';

  public function __construct()
  {
    parent::__construct();
		$this->home = base_url().'pos/order_pos';
		$this->load->model('masters/shop_model');
		$this->load->model('masters/pos_model');
		$this->load->model('orders/order_pos_model');
		$this->load->model('orders/discount_model');
    $this->load->helper('bank');
		$this->load->helper('discount');

  }


  public function index()
  {
		$this->title = "เลือกจุดขาย";

		$pos = $this->pos_model->get_active_pos_list();

		$ds = array(
			'list' => $pos
		);

		$this->load->view('pos/pos_list', $ds);
  }


	public function main($pos_id)
	{
		//---
		$hold_bills = $this->order_pos_model->count_hold_bills($pos_id); //---- hold by pos id

		$ds = array(
			'pos_id' => $pos_id,
			'hold_bills' => $hold_bills
		);

		$this->load->view('pos/pos_main', $ds);
	}



	public function add($id)
	{
		if($this->pm->can_add)
		{
			$this->load->model('masters/payment_methods_model');
			$this->load->helper('payment_method');
			$this->title = "POS";
			$pos = $this->pos_model->get_pos($id);

			if(!empty($pos))
			{
				$order = $this->order_pos_model->get_not_save_order($id);
				if(!empty($order))
				{

				}
				else
				{
					$code = $this->get_new_code($pos->prefix);
					$payment_code = getConfig('POS_DEFAULT_PAYMENT');
					$channels_code = getConfig('POS_CHANNELS');
					$payment = $this->payment_methods_model->get($payment_code);

					$arr = array(
						'code' => $code,
						'customer_code' => $pos->customer_code,
						'customer_name' => $pos->customer_name,
						'channels_code' => $channels_code,
						'payment_code' => $payment_code,
						'payment_role' => empty($payment) ? 2 : $payment->role,
						'shop_id' => $pos->shop_id,
						'pos_code' => $pos->pos_code,
						'date_add' => date('Y-m-d H:i:s'),
						'uname' => $this->_user->uname
					);

					if($this->order_pos_model->add($arr))
					{
						$this->title = $pos->name;
						$pos->order_code = $code;
						$pos->customer_list = $this->pos_model->get_customer_shop_list($pos->shop_id);
		        $pos->channels_code = $channels_code;
						$pos->payment_code = $payment_code;
						$this->load->view('pos/pos', $pos);
					}
					else
					{
						$this->page_error();
					}
				}
			}
			else
			{
				$this->page_error();
			}
		}
		else
		{
			$this->deny_page();
		}
	}



	public function pos($id)
	{
		if($this->pm->can_add)
		{
			$this->load->helper('payment_method');
			$this->title = "POS";
			$pos = $this->pos_model->get_pos($id);
			if(!empty($pos))
			{
				$this->title = $pos->name;
				$pos->customer_list = $this->pos_model->get_customer_shop_list($pos->shop_id);
        $pos->channels_code = $this->channels_code;
				$this->load->view('pos/pos', $pos);
			}
			else
			{
				$this->page_error();
			}
		}
		else
		{
			$this->deny_page();
		}
	}


	public function get_product_by_code()
	{
		$this->load->model('masters/products_model');
		$this->load->model('orders/discount_model');

		$code = trim($this->input->get('code'));
		$customer_code = trim($this->input->get('customer_code'));
		$payment_code = $this->input->get('payment_code');

		if(! is_null($code))
    {
      //$item = $this->products_model->get($code);
			$item = $this->products_model->get_product_by_barcode($code);

			if(!empty($item))
			{
				//--- default value if dont have any discount
				// $sc = array(
				// 	'discount1' => 0, //--- ส่วนลดเป็นจำนวนเงิน (ยอดต่อหน่วย)
				// 	'unit1' => 'percent', //--- หน่วยของส่วนลด ('percent', 'amount')
				// 	'discLabel1' => 0, //--- ข้อความที่ใช้แสดงส่วนลด เช่น 30%, 30
				// 	'discount2' => 0,
				// 	'unit2' => 'percent',
				// 	'discLabel2' => 0,
				// 	'discount3' => 0,
				// 	'unit3' => 'percent',
				// 	'discLabel3' => 0,
				// 	'amount' => 0, //--- เอายอดส่วนลดที่ได้ มา คูณ ด้วย จำนวนสั่ง เป้นส่วนลดทั้งหมด
				// 	'id_rule' => NULL
				// ); //-- end array

				$qty = 1;
				$discount = $this->discount_model->get_item_discount($item->code, $customer_code, $qty, $payment_code, $this->channels_code, date('Y-m-d'));
				$vat_rate = $this->products_model->get_vat_rate($item->code);
				$item_disc_amount = empty($discount['amount']) ? 0 : round($discount['amount'] / $qty, 2);
				$sell_price = $item->price - $item_disc_amount;
				$total_amount = round($sell_price * $qty, 2);
				$arr = array(
					'id' => md5($item->code),
					'code' => $item->code,
					'name' => $item->name,
					'price' => $item->price,
					'sell_price' => $sell_price,
					'qty' => $qty,
					'unit_code' => $item->unit_code,
					'discount' => discountLabel($discount['discLabel1'], $discount['discLabel2'], $discount['discLabel3']),
					'discount_amount' => $discount['amount'],
					'total' => $total_amount,
					'tax_rate' => $vat_rate,
					'tax_amount' => $total_amount * ($vat_rate * 0.01),
					'item_type' => $item->count_stock ? 'I' : 'S' //--- I = item (count stock), S = Service (non count stock)
				);

				echo json_encode($arr);
			}
			else
			{
				echo "No item found";
			}
    }
		else
		{
			echo "Invalid Item Code";
		}
	}

/*
	public function add()
	{
		$sc = TRUE;
		$data = json_decode(file_get_contents("php://input"));

		//--- payment role 1 = Credit, 2 = Cash, 3 = Bank transfer, 4 = COD , 5 = Credit Card
		if(!empty($data))
		{
			$this->load->model('inventory/movement_model');
			$this->load->model('stock/stock_model');

			$date = date('Y-m-d');
			$code = $this->get_new_code($data->prefix, $date);

			$is_paid = 0;

			if($data->payment_role == 2 OR $data->payment_role == 3 OR $data->payment_role == 5)
			{
				if($data->amount <= $data->received)
				{
					$is_paid = 1;
				}
			}

			$acc_no = NULL;
			if($data->payment_role == 3)
			{
				$this->load->model('masters/bank_model');
				$acc = $this->bank_model->get($data->acc_no);
				if(!empty($acc))
				{
					$acc_no = $acc->acc_no;
				}
				else
				{
					$sc = FALSE;
					$this->error = "Invalid Bank Account";
				}
			}

			$arr = array(
				'code' => $code,
				'customer_code' => $data->customer_code,
				'customer_name' => $data->customer_name,
				'channels_code' => $data->channels_code,
				'payment_code' => $data->payment_code,
				'payment_role' => $data->payment_role,
				'shop_id' => $data->shop_id,
				'pos_code' => $data->pos_code,
				'amount' => $data->amount,
				'received' => $data->received,
				'changed' => $data->changed,
				'status' => 1,
				'is_paid' => $is_paid,
				'acc_no' => $acc_no,
				'date_add' => date('Y-m-d H:i:s'),
				'uname' => $this->_user->uname
			);

			if($sc === TRUE)
			{
				$this->db->trans_begin();
				if($this->order_pos_model->add($arr))
				{
					if(!empty($data->details))
					{
						foreach($data->details as $rs)
						{
							$arr = array(
								'item_type' => $rs->item_type,
								'order_code' => $code,
								'product_code' => $rs->code,
								'product_name' => $rs->name,
								'unit_code' => $rs->unit_code,
								'qty' => $rs->qty,
								'std_price' => $rs->std_price,
								'price' => $rs->price,
								'discount_label' => $rs->discount_label,
								'discount_amount' => $rs->discount_amount,
								'final_price' => $rs->sell_price,
								'total_amount' => $rs->total_amount,
								'vat_rate' => $rs->vat_rate,
								'vat_amount' => $rs->vat_amount,
								'is_count' => $rs->item_type == 'I' ? 1 : 0,
								'zone_code' => $rs->zone_code,
								'status' => 1
							);

							if(!$this->order_pos_model->add_detail($arr))
							{
								$sc = FALSE;
								$error = $this->db->error();
								$this->error = "Insert Item Failed : ".$error;
								break;
							}
							else
							{
								//--- update stock
								if(! $this->stock_model->update_stock_zone($rs->zone_code, $rs->code, ($rs->qty * -1)))
								{
									$sc = FALSE;
									$error =  $this->db->error();
									$this->error = "Update Stock Failed : {$rs->zone_code} => {$rs->code} : ".$error['message'];
								}

								//--- update movement
								if($sc === TRUE)
								{
									$movement = array(
										'reference' => $code,
										'warehouse_code' => $data->warehouse_code,
										'zone_code' => $rs->zone_code,
										'product_code' => $rs->code,
										'move_in' => 0,
										'move_out' => $rs->qty,
										'date_add' => now()
									);

									if(! $this->movement_model->add($movement))
									{
										$sc = FALSE;
										$error =  $this->db->error();
										$this->error = "Update Movement Failed : {$rs->zone_code} => {$rs->code} : ".$error['message'];
									}
								}
							}
						}
					}
					else
					{
						$sc = FALSE;
						$this->error = "No items found";
					}
				}
				else
				{
					$sc = FALSE;
					$error = $this->db->error();
					$this->error = "Create Order Failed : " . $error['message'];
				}


				if($sc === TRUE)
				{
					$this->db->trans_commit();
				}
				else
				{
					$this->db->trans_rollback();
				}
			}

		} //--- data

		if($sc === TRUE)
		{
			$arr = array(
				'status' => 'success',
				'order_code' => $code
			);

			echo json_encode($arr);
		}
		else
		{
			echo $this->error;
		}

	} //--- add

*/

	public function get_new_code($prefix, $date = NULL)
	{
		$date = empty($date) ? date('Y-m-d') : ($date < '2020-01-01' ? date('Y-m-d') : $date);
		$Y = date('Y', strtotime($date));
    $M = date('m', strtotime($date));
    $run_digit = 5;
    $pre = $prefix .'-'.$Y.$M;
    $code = $this->order_pos_model->get_max_code($pre);

    if(! empty($code))
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



}

//--- End class
?>
