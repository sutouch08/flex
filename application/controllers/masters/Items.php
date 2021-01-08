<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Items extends PS_Controller
{
  public $menu_code = 'DBITEM';
	public $menu_group_code = 'DB';
  public $menu_sub_group_code = 'PRODUCT';
	public $title = 'เพิ่ม/แก้ไข รายการสินค้า';
  public $error = '';

  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'masters/items';
    //--- load model
    $this->load->model('masters/products_model');
    $this->load->model('masters/product_group_model');
    $this->load->model('masters/product_sub_group_model');
    $this->load->model('masters/product_kind_model');
    $this->load->model('masters/product_type_model');
    $this->load->model('masters/product_style_model');
    $this->load->model('masters/product_brand_model');
    $this->load->model('masters/product_category_model');
    $this->load->model('masters/product_color_model');
    $this->load->model('masters/product_size_model');
    $this->load->model('masters/product_tab_model');
    $this->load->model('masters/product_image_model');

    //---- load helper
    $this->load->helper('product_tab');
    $this->load->helper('product_brand');
    $this->load->helper('product_tab');
    $this->load->helper('product_kind');
    $this->load->helper('product_type');
    $this->load->helper('product_group');
    $this->load->helper('product_category');
    $this->load->helper('product_sub_group');
    $this->load->helper('product_images');
    $this->load->helper('unit');

  }


  public function index()
  {
    $filter = array(
      'code'      => get_filter('code', 'item_code', ''),
      'name'      => get_filter('name', 'item_name', ''),
      'barcode'   => get_filter('barcode', 'item_barcode', ''),
      'color'     => get_filter('color', 'color' ,''),
      'size'      => get_filter('size', 'size', ''),
      'group'     => get_filter('group', 'group', ''),
      'sub_group' => get_filter('sub_group', 'sub_group', ''),
      'category'  => get_filter('category', 'category', ''),
      'kind'      => get_filter('kind', 'kind', ''),
      'type'      => get_filter('type', 'type', ''),
      'brand'     => get_filter('brand', 'brand', ''),
      'year'      => get_filter('year', 'year', '')
    );

		//--- แสดงผลกี่รายการต่อหน้า
		$perpage = get_rows();
		//--- หาก user กำหนดการแสดงผลมามากเกินไป จำกัดไว้แค่ 300
		if($perpage > 300)
		{
			$perpage = 20;
		}

		$segment  = 4; //-- url segment
		$rows     = $this->products_model->count_rows($filter);
		//--- ส่งตัวแปรเข้าไป 4 ตัว base_url ,  total_row , perpage = 20, segment = 3
		$init	    = pagination_config($this->home.'/index/', $rows, $perpage, $segment);
		$products = $this->products_model->get_list($filter, $perpage, $this->uri->segment($segment));
    $ds       = array();
    if(!empty($products))
    {
      foreach($products as $rs)
      {
        $rs->group   = $this->product_group_model->get_name($rs->group_code);
        $rs->kind    = $this->product_kind_model->get_name($rs->kind_code);
        $rs->type    = $this->product_type_model->get_name($rs->type_code);
        $rs->category  = $this->product_category_model->get_name($rs->category_code);
        $rs->brand   = $this->product_brand_model->get_name($rs->brand_code);
      }
    }

    $filter['data'] = $products;

		$this->pagination->initialize($init);
    $this->load->view('masters/product_items/items_list', $filter);
  }


  public function import_items()
  {
    $sc = TRUE;
    $file = isset( $_FILES['uploadFile'] ) ? $_FILES['uploadFile'] : FALSE;
  	$path = $this->config->item('upload_path').'items/';
    $file	= 'uploadFile';
		$config = array(   // initial config for upload class
			"allowed_types" => "xlsx",
			"upload_path" => $path,
			"file_name"	=> "import_items",
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
        $this->load->library('api');

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
                'A' => 'Code',
                'B' => 'Name',
                'C' => 'Barcode',
                'D' => 'Model',
                'E' => 'Color',
                'F' => 'Size',
                'G' => 'Group',
                'H' => 'SubGroup',
                'I' => 'Category',
                'J' => 'Kind',
                'K' => 'Type',
                'L' => 'Brand',
                'M' => 'Year',
                'N' => 'Cost',
                'O' => 'Price',
                'P' => 'Unit',
                'Q' => 'CountStock',
                'R' => 'IsAPI'
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
              $rs['D'] = str_replace(array("\n", "\r"), '', $rs['D']); //--- เอาตัวขึ้นบรรทัดใหม่ออก

              $style = preg_replace($code_pattern, '', get_null(trim($rs['D'])));
              $color_code = get_null(trim($rs['E']));
              $size_code = get_null(trim($rs['F']));
              $group_code = get_null(trim($rs['G']));
              $sub_group_code = get_null(trim($rs['H']));
              $category_code = get_null(trim($rs['I']));
              $kind_code = get_null(trim($rs['J']));
              $type_code = get_null(trim($rs['K']));
              $brand_code = get_null(trim($rs['L']));
              $year = empty($rs['M']) ? '0000' : trim($rs['M']);

              if(!empty($color_code) && ! $this->product_color_model->is_exists($color_code))
              {
                $this->addColor($color_code);
              }
              else if(!empty($size_code) && ! $this->product_size_model->is_exists($size_code))
              {
                $this->addSize($size_code);
              }
              else if(!empty($group_code) && ! $this->product_group_model->is_exists($group_code))
              {
                $this->addGroup($group_code);
              }
              else if(!empty($sub_group_code) && ! $this->product_sub_group_model->is_exists($sub_group_code))
              {
                $this->addSubGroup($sub_group_code);
              }
              else if(!empty($category_code) && ! $this->product_category_model->is_exists($category_code))
              {
                $this->addCategory($category_code);
              }
              else if(!empty($kind_code) && ! $this->product_kind_model->is_exists($kind_code))
              {
                $this->addKind($kind_code);
              }
              else if(!empty($type_code) && ! $this->product_type_model->is_exists($type_code))
              {
                $this->addType($type_code);
              }
              else if(!empty($brand_code) && ! $this->product_brand_model->is_exists($brand_code))
              {
                $this->addBrand($brand_code);
              }

              if(!empty($style))
              {
                if(! $this->product_style_model->is_exists($style) )
                {
                  $ds = array(
                    'code' => $style,
                    'name' => $style,
                    'group_code' => $group_code,
                    'sub_group_code' => $sub_group_code,
                    'category_code' => $category_code,
                    'kind_code' => $kind_code,
                    'type_code' => $type_code,
                    'brand_code' => $brand_code,
                    'year' => $year,
                    'cost' => round(trim($rs['N']), 2),
                    'price' => round(trim($rs['O']), 2),
                    'unit_code' => trim($rs['P']),
                    'count_stock' => trim($rs['Q']) === 'N' ? 0:1,
                    'is_api' => trim($rs['R']) === 'N' ? 0 : 1,
                    'update_user' => get_cookie('uname')
                  );

									$this->product_style_model->add($ds);
                }
              }

              $rs['A'] = str_replace(array("\n", "\r"), '', $rs['A']); //--- เอาตัวขึ้นบรรทัดใหม่ออก
              $code = preg_replace($code_pattern, '', trim($rs['A']));
              $arr = array(
                'code' => $code,
                'name' => trim($rs['B']),
                'barcode' => get_null(trim($rs['C'])),
                'style_code' => get_null(trim($rs['D'])),
                'color_code' => get_null(trim($rs['E'])),
                'size_code' => get_null(trim($rs['F'])),
                'group_code' => get_null(trim($rs['G'])),
                'sub_group_code' => get_null(trim($rs['H'])),
                'category_code' => get_null(trim($rs['I'])),
                'kind_code' => get_null(trim($rs['J'])),
                'type_code' => get_null(trim($rs['K'])),
                'brand_code' => get_null(trim($rs['L'])),
                'year' => trim($rs['M']),
                'cost' => round(trim($rs['N']), 2),
                'price' => round(trim($rs['O']), 2),
                'unit_code' => empty(trim($rs['P'])) ? 'PCS' : trim($rs['P']),
                'count_stock' => trim($rs['Q']) === 'N' ? 0:1,
                'is_api' => trim($rs['R']) === 'N' ? 0 : 1,
                'update_user' => get_cookie('uname')
              );

              if($this->products_model->is_exists($code))
              {
                $is_done = $this->products_model->update($code, $arr);
              }
              else
              {
                $is_done = $this->products_model->add($arr);
              }

            }
          } //-- end foreach
        }
        else
        {
          $sc = FALSE;
          $this->error = "จำนวนนำเข้าสูงสุดได้ไม่เกิน {$limit} บรรทัด";
        } //-- end if count limit

      } //--- end if else

    echo $sc === TRUE ? 'success' : $this->error;
  }



	public function addBrand($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		return $this->product_brand_model->add($arr);
	}


	public function addColor($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		return $this->product_color_model->add($arr);
	}


	public function addSize($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		return $this->product_size_model->add($arr);
	}


	public function addCategory($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		return $this->product_category_model->add($arr);
	}


	public function addKind($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		return $this->product_kind_model->add($arr);
	}


	public function addType($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		return $this->product_type_model->add($arr);
	}

	public function addGroup($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		return $this->product_group_model->add($arr);
	}

	public function addSubGroup($code)
	{
		$arr = array(
			'code' => $code,
			'name' => $code
		);

		return $this->product_sub_group_model->add($arr);
	}


  public function add_new()
  {
    $this->load->view('masters/product_items/items_add_view');
  }


  public function add()
  {
    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      if($this->products_model->is_exists($code))
      {
        set_error($code.' '.'already_exists');
      }
      else
      {
        $count = $this->input->post('count_stock');
        $sell = $this->input->post('can_sell');
        $api = $this->input->post('is_api');
        $active = $this->input->post('active');
        $user = get_cookie('uname');

        $arr = array(
          'code' => trim($this->input->post('code')),
          'name' => trim($this->input->post('name')),
          'barcode' => get_null(trim($this->input->post('barcode'))),
          'style_code' => trim($this->input->post('style')),
          'color_code' => get_null($this->input->post('color')),
          'size_code' => get_null($this->input->post('size')),
          'group_code' => get_null($this->input->post('group_code')),
          'sub_group_code' => get_null($this->input->post('sub_group_code')),
          'category_code' => get_null($this->input->post('category_code')),
          'kind_code' => get_null($this->input->post('kind_code')),
          'type_code' => get_null($this->input->post('type_code')),
          'brand_code' => get_null($this->input->post('brand_code')),
          'year' => $this->input->post('year'),
          'cost' => round($this->input->post('cost'), 2),
          'price' => round($this->input->post('price'), 2),
          'unit_code' => $this->input->post('unit_code'),
          'count_stock' => is_null($count) ? 0 : 1,
          'can_sell' => is_null($sell) ? 0 : 1,
          'active' => is_null($active) ? 0 : 1,
          'is_api' => is_null($api) ? 0 : 1,
          'update_user' => $user
        );

        if($this->products_model->add($arr))
        {
          set_message('insert success');
        }
        else
        {
          set_error('insert fail');
        }
      }
    }
    else
    {
      set_error('no data found');
    }

    redirect($this->home.'/add_new');
  }


  public function add_duplicate()
  {
    if($this->input->post('code'))
    {
      $code = $this->input->post('code');
      if($this->products_model->is_exists($code))
      {
        set_error($code.' already_exists');
      }
      else
      {
        $count = $this->input->post('count_stock');
        $sell = $this->input->post('can_sell');
        $api = $this->input->post('is_api');
        $active = $this->input->post('active');
        $user = get_cookie('uname');

        $arr = array(
          'code' => trim($this->input->post('code')),
          'name' => trim($this->input->post('name')),
          'barcode' => get_null(trim($this->input->post('barcode'))),
          'style_code' => trim($this->input->post('style')),
          'color_code' => get_null($this->input->post('color')),
          'size_code' => get_null($this->input->post('size')),
          'group_code' => get_null($this->input->post('group_code')),
          'sub_group_code' => get_null($this->input->post('sub_group_code')),
          'category_code' => get_null($this->input->post('category_code')),
          'kind_code' => get_null($this->input->post('kind_code')),
          'type_code' => get_null($this->input->post('type_code')),
          'brand_code' => get_null($this->input->post('brand_code')),
          'year' => $this->input->post('year'),
          'cost' => round($this->input->post('cost'), 2),
          'price' => round($this->input->post('price'), 2),
          'unit_code' => $this->input->post('unit_code'),
          'count_stock' => is_null($count) ? 0 : 1,
          'can_sell' => is_null($sell) ? 0 : 1,
          'active' => is_null($active) ? 0 : 1,
          'is_api' => is_null($api) ? 0 : 1,
          'update_user' => $user
        );

        if($this->products_model->add($arr))
        {
          set_message('insert success');
        }
        else
        {
          set_error('insert failed');
        }
      }
    }
    else
    {
      set_error('no data found');
    }

    redirect($this->home);
  }




  public function edit($code)
  {
    $item = $this->products_model->get($code);
    if(!empty($item))
    {
      $this->load->view('masters/product_items/items_edit_view', $item);
    }
    else
    {
      set_error('ไม่พบข้อมูล');
      redirect($this->home);
    }
  }



  public function duplicate($code)
  {
    $item = $this->products_model->get($code);
    if(!empty($item))
    {
      $this->load->view('masters/product_items/items_duplicate_view', $item);
    }
    else
    {
      set_error('ไม่พบข้อมูล');
      redirect($this->home);
    }
  }


  public function update($code)
  {
    $count = $this->input->post('count_stock');
    $sell = $this->input->post('can_sell');
    $api = $this->input->post('is_api');
    $active = $this->input->post('active');
    $user = get_cookie('uname');

    $arr = array(
      'name' => trim($this->input->post('name')),
      'barcode' => get_null(trim($this->input->post('barcode'))),
      'style_code' => trim($this->input->post('style')),
      'color_code' => get_null($this->input->post('color')),
      'size_code' => get_null($this->input->post('size')),
      'group_code' => get_null($this->input->post('group_code')),
      'sub_group_code' => get_null($this->input->post('sub_group_code')),
      'category_code' => get_null($this->input->post('category_code')),
      'kind_code' => get_null($this->input->post('kind_code')),
      'type_code' => get_null($this->input->post('type_code')),
      'brand_code' => get_null($this->input->post('brand_code')),
      'year' => $this->input->post('year'),
      'cost' => round($this->input->post('cost'), 2),
      'price' => round($this->input->post('price'), 2),
      'unit_code' => $this->input->post('unit_code'),
      'count_stock' => is_null($count) ? 0 : 1,
      'can_sell' => is_null($sell) ? 0 : 1,
      'active' => is_null($active) ? 0 : 1,
      'is_api' => is_null($api) ? 0 : 1,
      'update_user' => $user
    );

    if($this->products_model->update($code, $arr))
    {
      set_message('Update success');
      redirect($this->home.'/edit/'.$code);
    }
    else
    {
      set_error('Update failed');
      redirect($this->home.'/edit/'.$code);
    }
  }



  public function is_exists_code($code, $old_code = '')
  {
    if($this->products_model->is_exists($code, $old_code))
    {
      echo 'รหัสซ้ำ';
    }
    else
    {
      echo 'ok';
    }
  }



  public function toggle_can_sell($code)
  {
    $status = $this->products_model->get_status('can_sell', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('can_sell', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }


  public function toggle_active($code)
  {
    $status = $this->products_model->get_status('active', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('active', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }



  public function toggle_api($code)
  {
    $status = $this->products_model->get_status('is_api', $code);
    $status = $status == 1 ? 0 : 1;

    if($this->products_model->set_status('is_api', $code, $status))
    {
      echo $status;
    }
    else
    {
      echo 'fail';
    }
  }


  public function delete_item($item)
  {
    $sc = TRUE;

    if($item != '')
    {
      if(! $this->products_model->has_transection($item))
      {
        if(! $this->products_model->delete_item($item))
        {
          $sc = FALSE;
          $message = "ลบรายการไม่สำเร็จ";
        }
      }
      else
      {
        $sc = FALSE;
        $message = "ไม่สามารถลบ {$item} ได้ เนื่องจากสินค้ามี Transcetion เกิดขึ้นแล้ว";
      }
    }
    else
    {
      $sc = FALSE;
      $message = 'ไม่พบข้อมูล';
    }

    echo $sc === TRUE ? 'success' : $message;
  }



    public function download_template($token)
    {
      //--- load excel library
      $this->load->library('excel');

      $this->excel->setActiveSheetIndex(0);
      $this->excel->getActiveSheet()->setTitle('Items Master Template');

      //--- set report title header
      $this->excel->getActiveSheet()->setCellValue('A1', 'Code');
      $this->excel->getActiveSheet()->setCellValue('B1', 'Name');
      $this->excel->getActiveSheet()->setCellValue('C1', 'Barcode');
      $this->excel->getActiveSheet()->setCellValue('D1', 'Model');
      $this->excel->getActiveSheet()->setCellValue('E1', 'Color');
      $this->excel->getActiveSheet()->setCellValue('F1', 'Size');
      $this->excel->getActiveSheet()->setCellValue('G1', 'Group');
      $this->excel->getActiveSheet()->setCellValue('H1', 'SubGroup');
      $this->excel->getActiveSheet()->setCellValue('I1', 'Category');
      $this->excel->getActiveSheet()->setCellValue('J1', 'Kind');
      $this->excel->getActiveSheet()->setCellValue('K1', 'Type');
      $this->excel->getActiveSheet()->setCellValue('L1', 'Brand');
      $this->excel->getActiveSheet()->setCellValue('M1', 'Year');
      $this->excel->getActiveSheet()->setCellValue('N1', 'Cost');
      $this->excel->getActiveSheet()->setCellValue('O1', 'Price');
      $this->excel->getActiveSheet()->setCellValue('P1', 'Unit');
      $this->excel->getActiveSheet()->setCellValue('Q1', 'CountStock');
      $this->excel->getActiveSheet()->setCellValue('R1', 'IsAPI');


      setToken($token);

      $file_name = "Items_master_template.xlsx";
      header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
      header('Content-Disposition: attachment;filename="'.$file_name.'"');
      $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
      $writer->save('php://output');
    }

  public function clear_filter()
	{
    $filter = array('item_code','item_name','item_barcode','color', 'size','group','sub_group','category','kind','type','brand','year');
    clear_filter($filter);
	}
}

?>
