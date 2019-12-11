<?php
class Movement_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('stock_movement', $ds);
    }

    return FALSE;
  }



  public function move_in($reference, $product_code, $zone_code, $move_in, $date_add)
  {
    $id = $this->get_id($reference, $product_code, $zone_code, 'move_in');
    if($id !== FALSE)
    {
      return $this->db->set("move_in", "qty + {$move_in}", FALSE)->where('id', $id)->update('stock_movement');
    }
    else
    {
      $arr = array(
        'reference' => $reference,
        'zone_code' => $zone_code,
        'product_code' => $product_code,
        'move_in' => $move_in,
        'date_add' => $date_add
      );

      return $this->db->insert('stock_movement', $arr);
    }

    return FALSE;
  }



  private function get_id($reference, $product_code, $zone_code, $move_type = 'move_in')
  {
    $this->db
    ->select('id')
    ->where('reference', $reference)
    ->where('product_code', $product_code)
    ->where('zone_code', $zone_code);

    if($move_type = 'move_in')
    {
      $this->db->where('move_out', 0)->where('move_in !=', 0);
    }
    else if($move_type = 'move_out')
    {
      $this->db->where('move_in', 0)->where('move_out !=', 0);
    }

    $rs = $this->db->get('stock_movement');

    if($rs->num_rows() === 1)
    {
      return $rs->row()->id;
    }

    return FALSE;
  }



  public function drop_movement($code)
  {
    return $this->db->where('reference', $code)->delete('stock_movement');
  }

} //--- end class

?>