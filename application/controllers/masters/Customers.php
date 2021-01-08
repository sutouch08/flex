<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Customers extends PS_Controller
{
  public $menu_code = 'DBCUST';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'CUSTOMER';
	public $title = 'เพิ่ม/แก้ไข รายชื่อลูกค้า';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/customers';
    $this->load->model('masters/customers_model');
    $this->load->model('masters/customer_group_model');
    $this->load->model('masters/customer_kind_model');
    $this->load->model('masters/customer_type_model');
    $this->load->model('masters/customer_class_model');
    $this->load->model('masters/customer_area_model');
    $this->load->helper('customer');
  }


  public function index()
  {
		$code = get_filter('code', 'code', '');
		$name = get_filter('name', 'name', '');
    $group = get_filter('group', 'group', '');
    $kind = get_filter('kind', 'kind', '');
    $type = get_filter('type', 'type', '');
    $class = get_filter('class', 'class', '');
    $area = get_filter('area', 'area', '');

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment = 4; //-- url segment
		$rows = $this->customers_model->count_rows($code, $name, $group, $kind, $type, $class, $area);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	= pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$customers = $this->customers_model->get_data($code, $name, $group, $kind, $type, $class, $area, $perpage, $this->uri->segment($segment));
    if(!empty($customers))
    {
      foreach($customers as $rs)
      {
        $rs->group  = $this->customer_group_model->get_name($rs->group_code);
        $rs->kind   = $this->customer_kind_model->get_name($rs->kind_code);
        $rs->type   = $this->customer_type_model->get_name($rs->type_code);
        $rs->class  = $this->customer_class_model->get_name($rs->class_code);
        //$rs->area   = $this->customer_area_model->get_name($rs->area_code);
      }
    }

    $data = array(
      'code' => $code,
      'name' => $name,
      'group' => $group,
      'kind' => $kind,
      'type' => $type,
      'class' => $class,
      'area' => $area,
			'data' => $customers
    );

		$this->pagination->initialize($init);
    $this->load->view('masters/customers/customers_view', $data);
  }




  public function add_new()
  {
    $data['code'] = $this->session->flashdata('code');
    $data['name'] = $this->session->flashdata('name');
    $data['Tax_Id'] = $this->session->flashdata('Tax_Id');
    $data['group'] = $this->session->flashdata('group');
    $data['kind'] = $this->session->flashdata('kind');
    $data['type'] = $this->session->flashdata('type');
    $data['class'] = $this->session->flashdata('class');
    $data['area'] = $this->session->flashdata('area');
    $data['sale'] = $this->session->flashdata('sale');
    $data['credit'] = $this->session->flashdata('credit');
    $data['credit_term'] = $this->session->flashdata('credit_term');
    $data['note'] = $this->session->flashdata('note');

    $this->load->view('masters/customers/customers_add_view', $data);
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $sc = TRUE;
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $credit = $this->input->post('CreditLine');
      $credit_term = $this->input->post('credit_term');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'Tax_Id' => $this->input->post('Tax_id'),
        'group_code' => $this->input->post('group'),
        'kind_code' => $this->input->post('kind'),
        'type_code' => $this->input->post('type'),
        'class_code' => $this->input->post('class'),
        'area_code' => $this->input->post('area'),
        'sale_code' => $this->input->post('sale'),
        'credit_term' => empty($credit_term) ? 0 : $credit_term,
        'amount' => empty($credit) ? 0 : $credit,
        'note' => get_null($this->input->post('note'))
      );

      if($this->customers_model->is_exists($code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' มีในระบบแล้ว");
      }

      if($this->customers_model->is_exists_name($name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' มีในระบบแล้ว");
      }

      if($sc === TRUE)
      {
        if($this->customers_model->add($ds))
        {
          $this->customers_model->update_balance($code);
          set_message('เพิ่มข้อมูลเรียบร้อยแล้ว');
        }
        else
        {
          $sc = FALSE;
          set_error('เพิ่มข้อมูลไม่สำเร็จ');
        }
      }


      if($sc === FALSE)
      {
        $this->session->set_flashdata('code', $code);
        $this->session->set_flashdata('name', $name);
        $this->session->set_flashdata('Tax_Id', $this->input->post('Tax_Id'));
        $this->session->set_flashdata('group', $this->input->post('group'));
        $this->session->set_flashdata('kind', $this->input->post('kind'));
        $this->session->set_flashdata('type', $this->input->post('type'));
        $this->session->set_flashdata('class', $this->input->post('class'));
        $this->session->set_flashdata('area', $this->input->post('area'));
        $this->session->set_flashdata('sale', $this->input->post('sale'));
        $this->session->set_flashdata('CreditLine', $this->input->post('CreditLine'));
        $this->session->set_flashdata('credit_term', $this->input->post('credit_term'));
      }
    }
    else
    {
      set_error('ไม่พบข้อมูล');
    }

    redirect($this->home.'/add_new');
  }



  public function edit($code, $tab='infoTab')
  {
    $this->load->model('address/customer_address_model');
    $this->load->model('address/address_model');
    $rs = $this->customers_model->get($code);
    $bill_to = $this->customer_address_model->get_customer_bill_to_address($code);
    $ship_to = $this->address_model->get_shipping_address($code);

    $data['ds'] = $rs;
    $data['tab'] = $tab;
    $data['disabled'] = ''; //--- ไม่ต้องปิดการแก้ไข
    $data['bill'] = $bill_to;
    $data['addr'] = $ship_to;

    $this->load->view('masters/customers/customers_edit_view', $data);
  }



  public function view_detail($code, $tab='infoTab')
  {
    $this->load->model('address/customer_address_model');
    $this->load->model('address/address_model');
    $rs = $this->customers_model->get($code);
    $bill_to = $this->customer_address_model->get_customer_bill_to_address($code);
    $ship_to = $this->address_model->get_shipping_address($code);

    $data['ds'] = $rs;
    $data['tab'] = $tab;
    $data['disabled'] = 'disabled';
    $data['bill'] = $bill_to;
    $data['addr'] = $ship_to;

    $this->load->view('masters/customers/customers_detail_view', $data);
  }


  public function add_bill_to($code)
  {
    if($this->input->post('address'))
    {
      $this->load->model('address/customer_address_model');
      $branch_code = $this->input->post('branch_code');
      $branch_name = $this->input->post('branch_name');
      $country = $this->input->post('country');
      $ds = array(
        'customer_code' => $code,
        'branch_code' => empty($branch_code) ? '000' : $branch_code,
        'branch_name' => empty($branch_name) ? 'สำนักงานใหญ่' : $branch_name,
        'address' => $this->input->post('address'),
        'sub_district' => $this->input->post('sub_district'),
        'district' => $this->input->post('district'),
        'province' => $this->input->post('province'),
        'postcode' => $this->input->post('postcode'),
        'country' => empty($country) ? 'TH' : $country,
        'phone' => $this->input->post('phone')
      );

      $rs = $this->customer_address_model->add_bill_to($ds);
      if($rs === TRUE)
      {
        set_message("เพิ่มที่อยู่เปิดบิลเรียบร้อยแล้ว");
      }
      else
      {
        set_error("เพิ่มที่อยู่ไม่สำเร็จ");
      }
    }
    else
    {
      set_error("ที่อยู่ต้องไม่ว่างเปล่า");
    }

    redirect($this->home.'/edit/'.$code.'/billTab');
  }



  public function update_bill_to($code)
  {
    if($this->input->post('address'))
    {
      $this->load->model('address/customer_address_model');
      $branch_code = $this->input->post('branch_code');
      $branch_name = $this->input->post('branch_name');
      $country = $this->input->post('country');
      $ds = array(
        'branch_code' => empty($branch_code) ? '000' : $branch_code,
        'branch_name' => empty($branch_name) ? 'สำนักงานใหญ่' : $branch_name,
        'address' => $this->input->post('address'),
        'sub_district' => $this->input->post('sub_district'),
        'district' => $this->input->post('district'),
        'province' => $this->input->post('province'),
        'postcode' => $this->input->post('postcode'),
        'country' => empty($country) ? 'TH' : $country,
        'phone' => $this->input->post('phone')
      );

      $rs = $this->customer_address_model->update_bill_to($code, $ds);
      if($rs === TRUE)
      {
        set_message("ปรับปรุงที่อยู่เปิดบิลเรียบร้อยแล้ว");
      }
      else
      {
        set_error("ปรับปรุงที่อยู่ไม่สำเร็จ");
      }
    }
    else
    {
      set_error("ที่อยู่ต้องไม่ว่างเปล่า");
    }

    redirect($this->home.'/edit/'.$code.'/billTab');
  }



  public function update()
  {
    $sc = TRUE;

    if($this->input->post('code'))
    {
      $old_code = $this->input->post('customers_code');
      $old_name = $this->input->post('customers_name');
      $code = $this->input->post('code');
      $name = $this->input->post('name');
      $credit = $this->input->post('CreditLine');
      $credit_term = $this->input->post('credit_term');

      $ds = array(
        'code' => $code,
        'name' => $name,
        'Tax_Id' => $this->input->post('Tax_Id'),
        'group_code' => $this->input->post('group'),
        'kind_code' => $this->input->post('kind'),
        'type_code' => $this->input->post('type'),
        'class_code' => $this->input->post('class'),
        'area_code' => $this->input->post('area'),
        'sale_code' => $this->input->post('sale'),
        'amount' => $credit,
        'credit_term' => $credit_term,
        'note' => get_null($this->input->post('note'))
      );

      if($sc === TRUE && $this->customers_model->is_exists($code, $old_code) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$code."' มีอยู่ในระบบแล้ว โปรดใช้รหัสอื่น");
      }

      if($sc === TRUE && $this->customers_model->is_exists_name($name, $old_name) === TRUE)
      {
        $sc = FALSE;
        set_error("'".$name."' มีอยู่ในระบบแล้ว โปรดใช้ชื่ออื่น");
      }

      if($sc === TRUE)
      {
        if($this->customers_model->update($old_code, $ds) === TRUE)
        {
          $this->customers_model->update_balance($code);
          set_message('ปรับปรุงข้อมูลเรียบร้อยแล้ว');
        }
        else
        {
          $sc = FALSE;
          set_error('ปรับปรุงข้อมูลไม่สำเร็จ');
        }
      }

    }
    else
    {
      $sc = FALSE;
      set_error('ไม่พบข้อมูล');
    }

    if($sc === FALSE)
    {
      $code = $this->input->post('customers_code');
    }

    redirect($this->home.'/edit/'.$code);
  }



  public function delete($code)
  {
    if($code != '')
    {
      if($this->customers_model->delete($code))
      {
        set_message('ลบข้อมูลเรียบร้อยแล้ว');
      }
      else
      {
        set_error('ลบข้อมูลไม่สำเร็จ');
      }
    }
    else
    {
      set_error('ไม่พบข้อมูล');
    }

    redirect($this->home);
  }




  public function do_export($code)
  {
    $this->load->model('masters/slp_model');
    $cs = $this->customers_model->get($code);
    if(!empty($cs))
    {
      $ds = array(
        'CardCode' => $cs->code,
        'CardName' => $cs->name,
        'CardType' => $cs->CardType,
        'GroupCode' => $cs->GroupCode,
        'CmpPrivate' => $cs->cmpPrivate,
        'SlpCode' => $cs->sale_code,
        //'SlpName' => $this->slp_model->get_name($cs->sale_code),
        'Currency' => getConfig('CURRENCY'),
        'GroupNum' => $cs->GroupNum,
        'VatStatus' => 'Y',
        'LicTradNum' => $cs->Tax_Id,
        'DebPayAcct' => $cs->DebPayAcct,
        'U_BPBACKLIST' => 'N',
        'F_E_Commerce' => 'A',
        'F_E_CommerceDate' => $cs->date_upd
      );

      if($this->customers_model->sap_customer_exists($cs->code))
      {
        $ds['F_E_Commerce'] = 'U';

        return $this->customers_model->update_sap_customer($cs->code, $ds);
      }
      else
      {
        return $this->customers_model->add_sap_customer($ds);
      }

    }

    return FALSE;
  }



  public function export_customer($code)
  {
    $rs = $this->do_export($code);
    if($rs === TRUE)
    {
      echo 'success';
    }
    else
    {
      echo 'Export fail';
    }
  }




  public function syncData()
  {
    $ds = $this->customers_model->get_update_data();
    if(!empty($ds))
    {
      foreach($ds as $rs)
      {
        $arr = array(
          'code' => $rs->code,
          'name' => $rs->name,
          'Tax_Id' => $rs->Tax_Id,
          'DebPayAcct' => $rs->DebPayAcct,
          'CardType' => $rs->CardType,
          'GroupCode' => $rs->GroupCode,
          'cmpPrivate' => $rs->CmpPrivate,
          'GroupNum' => $rs->GroupNum,
          'sale_code' => $rs->sale_code,
          'CreditLine' => $rs->CreditLine
        );

        if($this->customers_model->is_exists($rs->code) === TRUE)
        {
          $this->customers_model->update($rs->code, $arr);
        }
        else
        {
          $this->customers_model->add($arr);
        }
      }
    }

    set_message('Sync completed');
  }



	public function download_template($token)
	{
		//--- load excel library
		$this->load->library('excel');

		$this->excel->setActiveSheetIndex(0);
		$this->excel->getActiveSheet()->setTitle('Items Master Template');

		//--- set report title header
		$this->excel->getActiveSheet()->setCellValue('A1', 'Customer Code');
		$this->excel->getActiveSheet()->setCellValue('B1', 'Customer Name');
		$this->excel->getActiveSheet()->setCellValue('C1', 'Tax Id');
		$this->excel->getActiveSheet()->setCellValue('D1', 'Customer Group');
		$this->excel->getActiveSheet()->setCellValue('E1', 'Customer Kind');
		$this->excel->getActiveSheet()->setCellValue('F1', 'Customer Type');
		$this->excel->getActiveSheet()->setCellValue('G1', 'Customer Grade');
		$this->excel->getActiveSheet()->setCellValue('H1', 'Sales Area');
		$this->excel->getActiveSheet()->setCellValue('I1', 'Sale Code');
		$this->excel->getActiveSheet()->setCellValue('J1', 'Credit Term');
		$this->excel->getActiveSheet()->setCellValue('K1', 'Credit Amount');
		$this->excel->getActiveSheet()->setCellValue('L1', 'Notes');



		setToken($token);

		$file_name = "Customers_master_template.xlsx";
		header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
		header('Content-Disposition: attachment;filename="'.$file_name.'"');
		$writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
		$writer->save('php://output');
	}



	public function import_customers()
	{
		$sc = TRUE;
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
  	$path = $this->config->item('upload_path');
    $file	= 'uploadFile';
		$config = array(   // initial config for upload class
			"allowed_types" => "xlsx",
			"upload_path" => $path,
			"file_name"	=> "import_customers",
			"max_size" => 5120,
			"overwrite" => TRUE
			);

		$this->load->library("upload", $config);

		if(! $this->upload->do_upload($file))
		{
			$sc = FALSE;
			$this->error = $this->upload->display_errors();
		}
		else
		{
			$this->load->library('excel');

			$info = $this->upload->data();
			/// read file
			$excel = PHPExcel_IOFactory::load($info['full_path']);
			//get only the Cell Collection
			$collection	= $excel->getActiveSheet()->toArray(NULL, TRUE, TRUE, TRUE);

			$i = 1;
			$count = count($collection);
			$limit = intval(getConfig('IMPORT_ROWS_LIMIT'))+1;

			if($count <= $limit)
			{
				foreach($collection as $rs)
				{
					if($i == 1)
					{
						$i++;

						$headCol = array(
							'A' => 'Customer Code',
							'B' => 'Customer Name',
							'C' => 'Tax Id',
							'D' => 'Customer Group',
							'E' => 'Customer Kind',
							'F' => 'Customer Type',
							'G' => 'Customer Grade',
							'H' => 'Sales Area',
							'I' => 'Sale Code',
							'J' => 'Credit Term',
							'K' => 'Credit Amount',
							'L' => 'Notes'
						);

						foreach($headCol as $col => $field)
						{
							if($rs[$col] !== $field)
							{
								$sc = FALSE;
								$this->error = 'Column '.$col.' Should be '.$field;
								break;
							}
						}

						if($sc === FALSE)
						{
							break;
						}

					}
					else if(!empty($rs['A']))
					{
						if($sc === FALSE)
						{
							break;
						}

						$code_pattern = '/[^a-zA-Z0-9_-]/';

						$code = preg_replace($code_pattern, '', trim($rs['A']));
						$name = get_null(trim($rs['B']));
						$taxId = get_null(trim($rs['C']));
						$group = get_null(trim($rs['D']));
						$kind = get_null(trim($rs['E']));
						$type = get_null(trim($rs['F']));
						$class = get_null(trim($rs['G']));
						$area = get_null(trim($rs['H']));
						$sale = get_null(trim($rs['I']));
						$term = get_null(trim($rs['J']));
						$amount = get_null(trim($rs['K']));
						$notes = get_null(trim($rs['L']));

						if(!empty($group) && ! $this->customer_group_model->is_exists($group))
						{
							$this->addGroup($group);
						}

						if(!empty($kind) && !$this->customer_kind_model->is_exists($kind))
						{
							$this->addKind($kind);
						}

						if(!empty($type) && !$this->customer_type_model->is_exists($type))
						{
							$this->addType($type);
						}

						if(!empty($class) && !$this->customer_class_model->is_exists($class))
						{
							$this->addClass($class);
						}

						if(!empty($area) && !$this->customer_area_model->is_exists($area))
						{
							$this->addArea($area);
						}

						$arr = array(
							'code' => $code,
							'name' => $name,
							'Tax_Id' => $taxId,
							'group_code' => $group,
							'kind_code' => $kind,
							'type_code' => $type,
							'class_code' => $class,
							'area_code' => $area,
							'sale_code' => $sale,
							'credit_term' => $term,
							'amount' => $amount
						);

						if($this->customers_model->is_exists($code))
						{
							$is_done = $this->customers_model->update($code, $arr);
						}
						else
						{
							$is_done = $this->customers_model->add($arr);
						}

					}
				} //-- end foreach
			}
			else
			{
				$sc = FALSE;
				$this->error = "จำนวนนำเข้าสูงสุดได้ไม่เกิน {$limit} บรรทัด";
			} //-- end if count limit
		}

		echo $sc === TRUE ? 'success' : $this->error;
	}


	private function addGroup($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		$this->customer_group_model->add($arr);
	}

	private function addKind($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		$this->customer_kind_model->add($arr);
	}

	private function addType($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		$this->customer_type_model->add($arr);
	}

	private function addClass($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		$this->customer_class_model->add($arr);
	}


	private function addArea($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		$this->customer_area_model->add($arr);
	}

  public function clear_filter()
	{
    $filter = array( 'code', 'name','group','kind','type', 'class','area');
    clear_filter($filter);
	}
}

?>
