<?php
class Stock_balance_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }


  public function get_current_stock_balance(array $ds = array())
  {
    $this->db
    ->select('products.barcode, products.code, products.name, products.cost')
    ->select_sum('stock.qty')
    ->from('products')
		->join('stock', 'products.code = stock.product_code', 'left')
    ->join('product_size', 'product_size.code = products.size_code', 'left');

    //--- if specify warehouse
    if(empty($ds['allWhouse']))
    {
      $this->db->join('zone', 'stock.zone_code = zone.code', 'left');
      $this->db->where_in('zone.warehouse_code', $ds['warehouse']);
    }

    //--- if specify product
    if(empty($ds['allProduct']))
    {
      $this->db->where('products.style_code >=', $ds['pdFrom']);
      $this->db->where('products.style_code <=', $ds['pdTo']);
    }

		if(empty($ds['allResult']))
		{
			$this->db->where('stock.qty IS NOT NULL', NULL, FALSE);
		}

    $this->db->group_by('products.code');
    $this->db->order_by('products.style_code', 'ASC');
    $this->db->order_by('products.color_code', 'ASC');
    $this->db->order_by('product_size.position', 'ASC');

    $rs = $this->db->get();

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }


  public function get_stock_balance_prev_date(array $ds = array())
  {
    $date = to_date($ds['date']);

    $qr  = "SELECT pd.barcode, pd.code, pd.name, pd.cost, ";
		$qr .= "(SELECT SUM(s.move_in) - SUM(s.move_out) ";
		$qr .= "FROM stock_movement AS s ";
		$qr .= "WHERE s.date_add >= '{$date}' AND s.product_code = pd.code) AS qty ";
    $qr .= "FROM products AS pd ";
    $qr .= "LEFT JOIN stock_movement AS s ON pd.code = s.product_code ";
    $qr .= "LEFT JOIN product_size AS ps ON pd.size_code = ps.code ";

    if(empty($ds['allWhouse']))
    {
      $qr .= "JOIN zone AS z ON s.zone_code = z.code ";
    }

    $qr .= "WHERE pd.code IS NOT NULL ";

    if(empty($ds['allProduct']))
    {
      $qr .= "AND pd.style_code >= '{$ds['pdFrom']}' ";
      $qr .= "AND pd.style_code <= '{$ds['pdTo']}' ";
    }


    if(empty($ds['allWhouse']))
    {
      $wh_list = "";
      $i = 1;
      foreach($ds['warehouse'] as $wh)
      {
        $wh_list .= $i === 1 ? "'{$wh}'" : ", '{$wh}'";
        $i++;
      }

      $qr .= "AND z.warehouse_code IN({$wh_list}) ";
    }

    $qr .= "GROUP BY pd.code ";
    $qr .= "ORDER BY pd.style_code ASC, ";
    $qr .= "pd.color_code ASC, ";
    $qr .= "ps.position ASC";


    $rs = $this->db->query($qr);

    if($rs->num_rows() > 0)
    {
      return $rs->result();
    }

    return FALSE;
  }
}

 ?>
