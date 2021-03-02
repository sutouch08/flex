<?php
class Shop_model extends CI_Model
{
  public function __construct()
  {
    parent::__construct();
  }

  //--- add new zone (use with sync only)
  public function add(array $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->insert('shop', $ds);
    }

    return FALSE;
  }


  //--- update zone with sync only
  public function update($code, $ds = array())
  {
    if(!empty($ds))
    {
      return $this->db->where('code', $code)->update('shop', $ds);
    }

    return FALSE;
  }



	public function get($code = NULL)
	{
		if(! is_null($code))
		{
			$rs = $this->db
			->select('shop.*, customers.name AS customer_name, zone.name AS zone_name')
			->from('shop')
			->join('customers', 'shop.customer_code = customers.code', 'left')
			->join('zone', 'shop.zone_code = zone.code', 'left')
			->where('shop.code', $code)
			->get();

			if($rs->num_rows() === 1)
			{
				return $rs->row();
			}
		}

		return NULL;
	}



  //---- delete zone  must use only mistake on sap and delete zone in SAP already
  public function delete($code)
  {
    return $this->db->where('code', $code)->delete('shop');
  }


	//---- checl transection
	public function has_transection($code)
	{
		//---- order


		return FALSE;
	}



  //--- check zone exists or not
  public function is_exists($code)
  {
    if($this->db->where('code', $code)->count_all_results('shop') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  //--- check zone exists by id
  public function is_exists_id($id)
  {
    if($this->db->where('id', $id)->count_all_results('shop') > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


  public function is_exists_code($code, $old_code = NULL)
  {
    $this->db->where('code', $code);

    if(! is_null($old_code))
    {
      $this->db->where('code !=', $old_code);
    }

    $rs = $this->db->get('shop');
    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }



  public function is_exists_name($name, $old_name = NULL)
  {
    $this->db->where('name', $name);

    if(! is_null($old_name))
    {
      $this->db->where('name !=', $old_name);
    }

    $rs = $this->db->get('shop');

    if($rs->num_rows() > 0)
    {
      return TRUE;
    }

    return FALSE;
  }


	public function is_exists_zone($zone_code, $shop_code = NULL)
	{
		$this->db->where('zone_code', $zone_code);

		if(! is_null($shop_code))
		{
			$this->db->where('code !=', $shop_code);
		}

		$rs = $this->db->get('shop');

		if($rs->num_rows() > 0)
		{
			return TRUE;
		}

		return FALSE;
	}


  public function count_rows(array $ds = array())
  {
  	if(! empty($ds))
		{
			$this->db->from('shop')->join('zone', 'shop.zone_code = zone.code', 'left');

			if(!empty($ds['code']))
			{
				$this->db->like('shop.code', $ds['code']);
			}

			if(!empty($ds['name']))
			{
				$this->db->like('shop.name', $ds['name']);
			}

			if(!empty($ds['zone']))
			{
				$this->db
				->group_start()
				->like('zone.code', $ds['zone'])
				->or_like('zone.name', $ds['zone'])
				->group_end();
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('shop.active', $ds['status']);
			}

			return $this->db->count_all_results();
		}

		return 0;
  }



  public function get_list(array $ds = array(), $perpage = 20, $offset = 0)
  {
		if(! empty($ds))
		{
			$this->db
			->select('shop.*, zone.name AS zone_name')
			->from('shop')
			->join('zone', 'shop.zone_code = zone.code', 'left');

			if(!empty($ds['code']))
			{
				$this->db->like('shop.code', $ds['code']);
			}

			if(!empty($ds['name']))
			{
				$this->db->like('shop.name', $ds['name']);
			}

			if(!empty($ds['zone']))
			{
				$this->db
				->group_start()
				->like('zone.code', $ds['zone'])
				->or_like('zone.name', $ds['zone'])
				->group_end();
			}

			if($ds['status'] !== 'all')
			{
				$this->db->where('shop.active', $ds['status']);
			}

			$this->db->limit($perpage, $offset);

			$rs = $this->db->get();

			if($rs->num_rows() > 0)
			{
				return $rs->result();
			}
		}

		return NULL;

  }



	public function get_all()
	{
		$rs = $this->db->get('shop');
		if($rs->num_rows() > 0)
		{
			return $rs->result();
		}

		return NULL;
	}


} //--- end class

 ?>
