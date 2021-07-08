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
		$this->load->helper('payment_method');
		$this->load->helper('channels');
		$this->load->helper('sender');
  }

  public function index()
  {
    $filter = array(
      'code' => get_filter('code', 'code', ''),
      'customer' => get_filter('customer', 'customer', ''),
			'payment' => get_filter('payment', 'payment', 'all'),
			'channels' => get_filter('channels', 'channels', 'all'),
			'sender' => get_filter('sender', 'sender', 'all'),
      'from_date' => get_filter('from_date', 'from_date', ''),
      'to_date' => get_filter('to_date', 'to_date', ''),
			'print_status' => get_filter('print_status', 'print_status', '0')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 5; //-- url segment
		$rows     = $this->delivery_slip_model->count_rows($filter,8);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$orders   = $this->delivery_slip_model->get_list($filter, $perpage, $this->uri->segment($segment),8);

    $filter['orders'] = $orders;

		$this->pagination->initialize($init);
    $this->load->view('report/inventory/delivery_slip_list', $filter);
  }


	public function clear_filter()
	{
		$filter = array('code', 'customer', 'payment', 'channels', 'sender', 'from_date', 'to_date', 'print_status');

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


	public function exportKerryTemplate()
  {
		$list = $this->input->post('code');
		$token = $this->input->post('token');
		//--- load excel library
		$this->load->library('excel');

		$this->excel->setActiveSheetIndex(0);
		$sheetName = "Kerry ".date('Ymd');
		$this->excel->getActiveSheet()->setTitle($sheetName);

		$borderStyle = array(
		  'borders' => array(
		    'allborders' => array(
		      'style' => PHPExcel_Style_Border::BORDER_THIN
		    )
		  )
		);

		$colorGreyStyle = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'ADACAE')
			)
		);

		$colorGreenStyle = array(
			'fill' => array(
				'type' => PHPExcel_Style_Fill::FILL_SOLID,
				'color' => array('rgb'=>'88D588')
			)
		);

		$this->excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('E')->setWidth(60);
		$this->excel->getActiveSheet()->getColumnDimension('F')->setWidth(60);
		$this->excel->getActiveSheet()->getColumnDimension('G')->setWidth(10);
		$this->excel->getActiveSheet()->getColumnDimension('H')->setWidth(15);
		$this->excel->getActiveSheet()->getColumnDimension('I')->setWidth(25);
		$this->excel->getActiveSheet()->getColumnDimension('J')->setWidth(20);
		$this->excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);

		//--- set report title header
		$this->excel->getActiveSheet()->setCellValue('A2', "KERRY EXPRESS (ส่งไว ส่งชัวร์ ทั่วไทย)");

		//--- set Table header
		$this->excel->getActiveSheet()->setCellValue('A4', 'No');
		$this->excel->getActiveSheet()->setCellValue('B4', 'Recipient Name');
		$this->excel->getActiveSheet()->setCellValue('C4', 'Mobile No.');
		$this->excel->getActiveSheet()->setCellValue('D4', 'Email');
		$this->excel->getActiveSheet()->setCellValue('E4', 'Address #1');
		$this->excel->getActiveSheet()->setCellValue('F4', 'Address #2');
		$this->excel->getActiveSheet()->setCellValue('G4', 'Zip Code');
		$this->excel->getActiveSheet()->setCellValue('H4', 'COD Amt (Baht)');
		$this->excel->getActiveSheet()->setCellValue('I4', 'Remark');
		$this->excel->getActiveSheet()->setCellValue('J4', 'Ref #1');
		$this->excel->getActiveSheet()->setCellValue('K4', 'Ref #2');

		//--- set Table header
		$this->excel->getActiveSheet()->setCellValue('A5', '1');
		$this->excel->getActiveSheet()->setCellValue('B5', 'คุณตัวอย่าง ข้อมูล');
		$this->excel->getActiveSheet()->setCellValueExplicit('C5', '099999999',PHPExcel_Cell_DataType::TYPE_STRING);
		$this->excel->getActiveSheet()->setCellValue('D5', 'me@sample.com');
		$this->excel->getActiveSheet()->setCellValue('E5', '999/9 หมู่บ้านพัฒนา');
		$this->excel->getActiveSheet()->setCellValue('F5', 'แขวงยานนาวา เขตสาทร กรุงเทพมหานคร');
		$this->excel->getActiveSheet()->setCellValue('G5', '10120');
		$this->excel->getActiveSheet()->setCellValue('H5', '500');
		$this->excel->getActiveSheet()->setCellValue('I5', 'พวงกุญแจ');
		$this->excel->getActiveSheet()->setCellValue('J5', 'ORDER0001');
		$this->excel->getActiveSheet()->setCellValue('K5', 'PO0017');

		$this->excel->getActiveSheet()->getStyle('A4:K4')->applyFromArray($colorGreyStyle);
		$this->excel->getActiveSheet()->getStyle('A4:K5')->applyFromArray($borderStyle);


		//--- set Table header
		$this->excel->getActiveSheet()->setCellValue('A8', 'No');
		$this->excel->getActiveSheet()->setCellValue('B8', 'Recipient Name');
		$this->excel->getActiveSheet()->setCellValue('C8', 'Mobile No.');
		$this->excel->getActiveSheet()->setCellValue('D8', 'Email');
		$this->excel->getActiveSheet()->setCellValue('E8', 'Address #1');
		$this->excel->getActiveSheet()->setCellValue('F8', 'Address #2');
		$this->excel->getActiveSheet()->setCellValue('G8', 'Zip Code');
		$this->excel->getActiveSheet()->setCellValue('H8', 'COD Amt (Baht)');
		$this->excel->getActiveSheet()->setCellValue('I8', 'Remark');
		$this->excel->getActiveSheet()->setCellValue('J8', 'Ref #1');
		$this->excel->getActiveSheet()->setCellValue('K8', 'Ref #2');

		$this->excel->getActiveSheet()->getStyle('A8:K8')->applyFromArray($colorGreenStyle);

    if(!empty($list) && is_array($list))
		{
			$this->load->model('masters/sender_model');
			$this->load->model('masters/payment_methods_model');
			$this->load->model('address/address_model');

			$row = 9;
			$no = 1;
			foreach($list as $code)
			{
				$order = $this->orders_model->get_with_payment_role($code);

				if(!empty($order))
				{
					$adr = $this->get_address($order->address_id, $order->customer_ref, $order->customer_code);
					$box = $this->count_box($code);

					if(empty($adr))
					{
						$adr = new stdClass;
						$adr->name = "";
						$adr->address = "";
						$adr->sub_district = "";
						$adr->district = "";
						$adr->province = "";
						$adr->postcode = "";
						$adr->email = "";
						$adr->phone = "";
					}

					$box = $this->count_box($code);

					if($box > 1)
					{
						$cbox = 1;
						while($cbox <= $box)
						{
							$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
			        $this->excel->getActiveSheet()->setCellValue('B'.$row, ($adr->name." ".$cbox."/".$box));
							$this->excel->getActiveSheet()->setCellValueExplicit('C'.$row, $adr->phone,PHPExcel_Cell_DataType::TYPE_STRING);
			        $this->excel->getActiveSheet()->setCellValue('D'.$row, $adr->email);
			        $this->excel->getActiveSheet()->setCellValue('E'.$row, $adr->address." ".$adr->sub_district);
			        $this->excel->getActiveSheet()->setCellValue('F'.$row, $adr->district." ".$adr->province);
			        $this->excel->getActiveSheet()->setCellValue('G'.$row, $adr->postcode);
							if($order->payment_role == 4)
							{
								$this->excel->getActiveSheet()->setCellValue('H'.$row, $order->balance);
							}
							$this->excel->getActiveSheet()->setCellValue('J'.$row, $order->code);
							$this->excel->getActiveSheet()->setCellValue('K'.$row, $order->reference);

							$cbox++;
			        $no++;
			        $row++;
						}
					}
					else
					{
						$this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
						$this->excel->getActiveSheet()->setCellValue('B'.$row, $adr->name);
						$this->excel->getActiveSheet()->setCellValueExplicit('C'.$row, $adr->phone,PHPExcel_Cell_DataType::TYPE_STRING);
						$this->excel->getActiveSheet()->setCellValue('D'.$row, $adr->email);
						$this->excel->getActiveSheet()->setCellValue('E'.$row, $adr->address." ".$adr->sub_district);
						$this->excel->getActiveSheet()->setCellValue('F'.$row, $adr->district." ".$adr->province);
						$this->excel->getActiveSheet()->setCellValue('G'.$row, $adr->postcode);
						if($order->payment_role == 4)
						{
							$this->excel->getActiveSheet()->setCellValue('H'.$row, $order->balance);
						}
						$this->excel->getActiveSheet()->setCellValue('J'.$row, $order->code);
						$this->excel->getActiveSheet()->setCellValue('K'.$row, $order->reference);

						$no++;
						$row++;
					}
				}
			}

			$this->excel->getActiveSheet()->getStyle('A8:K'.$row)->applyFromArray($borderStyle);

		}

		setToken($token);
		$file_name = "Kerry_template_".date('Ymd').".xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');
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
