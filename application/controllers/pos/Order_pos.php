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
		$this->load->model('masters/shop_model');
		$this->load->model('masters/pos_model');
  }


  public function index()
  {
		$shop = $this->shop_model->get_all();

		if(!empty($shop))
		{
			foreach($shop as $sh)
			{
				if($sh->active)
				{
					$sh->pos = $this->pos_model->get_shop_pos($sh->id);
				}
			}
		}

		$this->load->view('pos/shop_list', $shop);
  }



}

//--- End class
?>
