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
			$this->title = "POS";
			$pos = $this->pos_model->get_pos($id);
			if(!empty($pos))
			{
				$this->title = $pos->name;
				$pos->customer_list = $this->pos_model->get_customer_shop_list($pos->shop_id);
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
