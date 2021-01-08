<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Delivery_slip extends PS_Controller
{
  public $menu_code = 'REORDL';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'REINVT';
	public $title = 'รายงานการจัดส่ง';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/inventory/delivery_slip';
		$this->load->model('report/inventory/delivery_slip_model');
		$this->load->model('inventory/invoice_model');
    $this->load->model('orders/orders_model');
    $this->load->model('masters/customers_model');
    $this->load->model('inventory/delivery_order_model');
    $this->load->helper('order');
  }

  public function index()
  {
    $filter = array(
      'code'          => get_filter('code', 'code', ''),
      'customer'      => get_filter('customer', 'customer', ''),
      'from_date'     => get_filter('from_date', 'from_date', ''),
      'to_date'       => get_filter('to_date', 'to_date', ''),
			'print_status'  => get_filter('print_status', 'print_status', '0')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->delivery_slip_model->count_rows($filter, 8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->delivery_slip_model->get_list($filter, $perpage, $this->uri->segment($segment), 8);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('report/inventory/delivery_slip_list', $filter);
  }


	public function clear_filter()
	{
		$filter = array('code', 'customer', 'from_date', 'to_date', 'print_status');

		clear_filter($filter);

		echo 'done';
	}

  public function get_report()
  {
		$this->load->library('printer');
		$select_code = $this->input->post('code');

		$data = array();
    if(!empty($select_code))
		{
			$this->load->model('masters/sender_model');
			$this->load->model('masters/payment_methods_model');
			foreach($select_code as $code)
			{
				$order = $this->orders_model->get($code);
				if(!empty($order))
				{
					$adr = $this->get_address($order->address_id, $order->customer_ref, $order->customer_code);

					$box = $this->count_box($code);

					$arr = array(
						'code' => $code,
						'adr' => $adr,
						'notes' => $order->remark,
						'box' => $box,
						'amount' => $order->total_amount,
						'sender' => $this->sender_model->get_name($order->sender_id),
						'payment' => $this->payment_methods_model->get_name($order->payment_code)
					);

					array_push($data, $arr);
				}
			}

			$this->delivery_slip_model->update_status($select_code);
		}

		$ds = array(
			'data' => $data
		);

		$this->load->view('report/inventory/delivery_slip_report', $ds);
  }



	public function get_address($address_id, $customer_ref, $customer_code)
	{
		$this->load->model('address/address_model');
		$address = NULL;

		if(empty($address_id))
		{
			$adr_code = empty($customer_ref) ? $customer_code : $customer_ref;
			$address = $this->address_model->get_default_address($adr_code);
		}
		else
		{
			$address = $this->address_model->get_shipping_detail($address_id);
		}

		return $address;
	}



	private function count_box($code)
	{
		return $this->db->where('order_code', $code)->count_all_results('qc_box');
	}


	public function do_export()
  {
		//--- load excel library
		$this->load->library('excel');

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Delivery_slip');

		//--- set report title header
		$this->excel->getActiveSheet()->setCellValue('A1', "ใบรายงานจัดเตรียมสินค้าเพื่อขนส่ง");
		$this->excel->getActiveSheet()->mergeCells('A1:G1');

		//--- set Table header
		$this->excel->getActiveSheet()->setCellValue('A2', 'ลำดับ');
		$this->excel->getActiveSheet()->setCellValue('B2', 'ชื่อและที่อยู่');
		$this->excel->getActiveSheet()->setCellValue('C2', 'หมายเหตุ');
		$this->excel->getActiveSheet()->setCellValue('D2', 'จำนวนลัง');
		$this->excel->getActiveSheet()->setCellValue('E2', 'ยอดเงิน');
		$this->excel->getActiveSheet()->setCellValue('F2', 'การจัดส่ง');
		$this->excel->getActiveSheet()->setCellValue('G2', 'การชำระเงิน');

		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);


		$list = $this->input->post('code');
		$select_code = explode(',', $list);
		$data = array();
    if(!empty($select_code))
		{
			$this->load->model('masters/sender_model');
			$this->load->model('masters/payment_methods_model');

			$row = 3;
			$no = 1;
			foreach($select_code as $code)
			{
				$order = $this->orders_model->get($code);

				if(!empty($order))
				{
					$adr = $this->get_address($order->address_id, $order->customer_ref, $order->customer_code);
					$box = $this->count_box($code);

					$adress = "";
					if(!empty($adr))
					{
						$adress .= "ชื่อ : {$adr->name} \r";
						$adress .= "ที่อยู่ : {$adr->address} {$adr->sub_district} {$adr->district} {$adr->province} {$adr->postcode} \r";
						$adress .= "โทร. {$adr->phone} \r";
					}

					$adress .= "เลขที่บิล : {$code}";

					$box = $this->count_box($code);
					$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
	        $this->excel->getActiveSheet()->setCellValue('B'.$row, $adress);
					$this->excel->getActiveSheet()->getStyle('B'.$row)->getAlignment()->setWrapText(TRUE);
	        $this->excel->getActiveSheet()->setCellValue('C'.$row, $order->remark);
	        $this->excel->getActiveSheet()->setCellValue('D'.$row, $box);
	        $this->excel->getActiveSheet()->setCellValue('E'.$row, $order->total_amount);
	        $this->excel->getActiveSheet()->setCellValue('F'.$row, $this->sender_model->get_name($order->sender_id));
	        $this->excel->getActiveSheet()->setCellValue('G'.$row, $this->payment_methods_model->get_name($order->payment_code));
	        $no++;
	        $row++;
				}
			}

		}

		$file_name = "Delivery_slip.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
  }


} //--- end class








 ?>
