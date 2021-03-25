<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Order_pos extends PS_Controller
{
  public $menu_code = 'SOPOS';
	public $menu_group_code = 'SO';
  public $menu_sub_group_code = 'ORDER';
	public $title = '';
	public $channels_code;

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

		$this->channels_code = getConfig('POS_CHANNELS');
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
        $pos->channels = $this->channels_code;
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

  public function get_product_by_barcode()
  {
    $this->load->model('masters/products_model');
		$this->load->model('orders/discount_model');

    $barcode = trim($this->input->get('barcode'));
		$customer_code = trim($this->input->get('customer_code'));

    if(! is_null($barcode))
    {
      $item = $this->products_model->get_product_by_barcode($barcode);
			if(!empty($item))
			{
				$vat_rate = $this->products_model->get_vat_rate($item->code);

				$arr = array(
					'id' => md5($item->code),
					'code' => $item->code,
					'name' => $item->name,
					'price' => $item->price,
					'sell_price' => $item->price,
					'qty' => 1,
					'discount' => 0,
					'discount_amount' => 0,
					'total' => $item->price,
					'tax_rate' => $vat_rate,
					'tax_amount' => $item->price * ($vat_rate * 0.01)
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
			echo "Invalid barcode";
		}
  }


}

//--- End class
?>
