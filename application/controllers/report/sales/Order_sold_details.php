<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class Order_sold_details extends PS_Controller
{
  public $menu_code = 'RSDEPA';
	public $menu_group_code = 'RE';
  public $menu_sub_group_code = 'RESALE';
	public $title = 'รายงานวิเคราะห์ขายแบบละเอีด';
  public $filter;
  public function __construct()
  {
    parent::__construct();
    $this->home = base_url().'report/sales/order_sold_details';
    $this->load->model('report/sales/sales_report_model');
  }

  public function index()
  {
    $this->load->view('report/sales/order_sold_details');
  }



  public function do_export()
  {
    $role = $this->input->post('role');

    $fromDate = $this->input->post('fromDate');
    $toDate = $this->input->post('toDate');

    //---  Report title
    $report_title = 'รายงานวิเคราะห์ขายแบบละเอียด';
    $date_title = 'วันที่ : '.thai_date($fromDate, FALSE, '/').' - '.thai_date($toDate, FALSE, '/');
    $role_title = 'ประเภท : '.($role == 'all' ? 'ขาย, ฝากขาย' : ($role == 'S' ? 'เฉพาะขาย' : 'เฉพาะฝากขาย');


    //--- load excel library
    $this->load->library('excel');

    $this->excel->setActiveSheetIndex(0);
    $this->excel->getActiveSheet()->setTitle('Sales By Customer Show Items');

    //--- set report title header
    $this->excel->getActiveSheet()->setCellValue('A1', $report_title);
    $this->excel->getActiveSheet()->setCellValue('A2', $date_title);
    $this->excel->getActiveSheet()->setCellValue('A3', $role_title);

		$row = 4;
		//--------- Report Table header
		$excel->getActiveSheet()->setCellValue("A{$row}", 'date');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'reference');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'role');
		$excel->getActiveSheet()->setCellValue("C{$row}", 'payment');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'channels');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'model');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'item code');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'item name');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'color');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'size');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'item group');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'item category');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'item kind');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'item type');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'brand');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'year');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'cost (ex)');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'cost (inc)');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'price (ex)');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'price (inc)');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'qty');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'discount item');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'bill discount');
		$excel->getActiveSheet()->setCellValue("B{$row}", 'role');




    $row = 5;

    $ds = array(
      'allCustomer' => is_true($allCustomer),
      'cusFrom' => $cusFrom,
      'cusTo' => $cusTo,
      'fromDate' => from_date($fromDate),
      'toDate' => to_date($toDate)
    );

    $result = $this->sales_report_model->get_order_sold_by_date_upd($ds);

    if(!empty($result))
    {
      $no = 1;
      foreach($result as $rs)
      {
        $this->excel->getActiveSheet()->setCellValue('A'.$row, $no);
        $this->excel->getActiveSheet()->setCellValue('B'.$row, thai_date($rs->date_upd, FALSE, '/'));
        $this->excel->getActiveSheet()->setCellValue('C'.$row, $rs->customer_name);
        $this->excel->getActiveSheet()->setCellValue('D'.$row, $rs->reference);
        $this->excel->getActiveSheet()->setCellValue('E'.$row, $rs->product_code);
        $this->excel->getActiveSheet()->setCellValue('F'.$row, number($rs->price, 2));
        $this->excel->getActiveSheet()->setCellValue('G'.$row, $rs->discount_label);
        $this->excel->getActiveSheet()->setCellValue('H'.$row, number($rs->qty));
        $this->excel->getActiveSheet()->setCellValue('I'.$row, number($rs->total_amount));
        $no++;
        $row++;
      }

      $res = $row -1;

      $this->excel->getActiveSheet()->setCellValue('A'.$row, 'รวม');
      $this->excel->getActiveSheet()->mergeCells('A'.$row.':G'.$row);
      $this->excel->getActiveSheet()->setCellValue('H'.$row, '=SUM(H5:H'.$res.')');
      $this->excel->getActiveSheet()->setCellValue('I'.$row, '=SUM(I5:I'.$res.')');

      $this->excel->getActiveSheet()->getStyle('A'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('F5:F'.$row)->getNumberFormat()->setFormatCode('#,##0');
      $this->excel->getActiveSheet()->getStyle('G5:G'.$row)->getAlignment()->setHorizontal('center');
      $this->excel->getActiveSheet()->getStyle('G5:G'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
      $this->excel->getActiveSheet()->getStyle('H5:I'.$row)->getAlignment()->setHorizontal('right');
      $this->excel->getActiveSheet()->getStyle('H5:H'.$row)->getNumberFormat()->setFormatCode('0');
      $this->excel->getActiveSheet()->getStyle('I5:I'.$row)->getNumberFormat()->setFormatCode('#,##0.00');
    }


    $file_name = "Report Sales by customer show items.xlsx";
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'); /// form excel 2007 XLSX
    header('Content-Disposition: attachment;filename="'.$file_name.'"');
    $writer = PHPExcel_IOFactory::createWriter($this->excel, 'Excel2007');
    $writer->save('php://output');

  }


} //--- end class








 ?>
