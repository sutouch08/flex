<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class stock_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }



  public function get_style_sell_stock($style_code)
  {
    $this->db
    ->select_sum('qty', 'qty')
    ->from('stock')
    ->join('products', 'stock.product_code = products.code', 'left')
    ->join('zone', 'stock.zone_code = zone.code', 'left')
    ->join('warehouse', 'zone.warehouse_code = warehouse.code', 'left')
    ->where('warehouse.sell', 1)
    ->where('products.style_code', $style_code);
    $rs = $this->db->get();
    if($rs->num_rows() === 1)
    {
      return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
    }

    return 0;
  }




  public function get_stock_zone($zone_code, $pd_code)
  {
    $rs = $this->db
    ->select('qty')
    ->where('product_code', $pd_code)
    ->where('zone_code', $zone_code)
    ->get('stock');

    if($rs->num_rows() == 1)
    {
      return $rs->row()->qty === NULL ? 0 : $rs->row()->qty;
    }

    return 0;
  }


  //---- ยอดรวมสินค้าในคลังที่สั่งได้ ยอดในโซน
  public function get_sell_stock($item)
  {
    $rs = $this->db
    ->select_sum('qty', 'qty')
    ->from('stock')
    ->join('zone', 'zone.code = stock.zone_code', 'left')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
    ->where('stock.product_code', $item)
    ->where('warehouse.sell', 1)
    ->get();

    return $rs->row()->qty === NULL ? 0 : $this->row()->qty;
  }


  //--- ยอดรวมสินค้าทั้งหมดทุกคลัง (รวมฝากขาย)
  public function get_stock($item)
  {
    $rs = $this->db->select_sum('qty', 'qty')->where('product_code', $item)->get('stock');
    return $rs->row()->qty === NULL ? 0 : $this->row()->qty;
  }


  //---- ยอดสินค้าคงเหลือในแต่ละโซน
  public function get_stock_in_zone($item)
  {
    $rs = $this->db
    ->select('zone_code AS code, qty AS qty')
    ->from('stock')
    ->join('zone', 'zone.code = stock.zone_code', 'left')
    ->join('warehouse', 'warehouse.code = zone.warehouse_code', 'left')
    ->where('warehouse.sell', 1)
    ->where('product_code', $item)
    ->get();

    $result = array();

    if($rs->num_rows() > 0)
    {
      foreach($rs->result() as $stock)
      {
        $ds = new stdClass();
        $ds->code = $stock->code;
        $ds->name = $stock->code;
        $ds->qty  = $stock->qty;
        $result[] = $ds;
      }
    }

    return $result;
  }


  //---- สินค้าทั้งหมดที่อยู่ในโซน (ใช้โอนสินค้าระหว่างคลัง)
  public function get_all_stock_in_zone($zone_code)
  {
    $rs = $this->db
    ->select('product_code, qty')
    ->where('zone_code', $zone_code)
    ->get('stock');

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return NULL;
  }

}//--- end class
