<?php
defined('BASEPATH') OR exit('No direct script access allowed');
class User_pwd extends PS_Controller
{
  public $title = 'เปลี่ยนรหัสผ่าน';
	public $menu_code = 'change password';
	public $menu_group_code = 'SC';
	public $pm;
  public $error;
  //public $isViewer = 0;

	public function __construct()
	{
		parent::__construct();
		_check_login();
		$this->pm = new stdClass();
		$this->pm->can_view = 1;
    $this->load->model('users/user_model');
    $this->home = base_url().'user_pwd';
	}


	public function index()
	{
    $code = get_cookie('uname');
    if(!empty($code))
    {
      $user = $this->user_model->get($code);
      if(!empty($user))
      {
        $ds['data'] = $user;
        $this->load->view('users/change_pwd', $ds);
      }
      else
      {
        //--- ถ้าไม่มีข้อมูล ให้ไป login ใหม่
        redirect(base_url().'users/authentication');
      }
    }
    else
    {
      //--- ถ้าไม่มีข้อมูล ให้ไป login ใหม่
  		redirect(base_url().'users/authentication');
    }

	}


  public function change($code)
	{
    if(!empty($code))
    {
      $user = $this->user_model->get($code);
      if(!empty($user))
      {
        $ds['data'] = $user;
        $this->load->view('users/change_pwd', $ds);
      }
      else
      {
        //--- ถ้าไม่มีข้อมูล ให้ไป login ใหม่
        redirect(base_url().'users/authentication');
      }
    }
    else
    {
      //--- ถ้าไม่มีข้อมูล ให้ไป login ใหม่
  		redirect(base_url().'users/authentication');
    }
	}


  public function change_password()
	{
		if($this->input->post('user_id'))
		{
			$id = $this->input->post('user_id');
			$pwd = password_hash($this->input->post('pwd'), PASSWORD_DEFAULT);
			$rs = $this->user_model->change_password($id, $pwd);

			if($rs === TRUE)
			{
				$this->session->set_flashdata('success', 'Password changed');
			}
			else
			{
				$this->session->set_flashdata('error', 'Change password not successfull, please try again');
			}
		}

		redirect($this->home);
	}




  public function change_skey()
  {
    $sc = TRUE;
    $uid = trim($this->input->post('uid'));
    $user = $this->user_model->get_user_by_uid($uid);
    if(!empty($user))
    {
      $skey = trim($this->input->post('skey'));
      $skey = md5($skey);
      $is_exists = $this->user_model->is_skey_exists($skey, $uid);
      if($is_exists)
      {
        $sc = FALSE;
        $this->error = "ไม่สามารถใช้รหัสนี้ได้กรุณากำหนดรหัสอื่น";
      }
      else
      {
        $arr = array('skey' => $skey);
        if(! $this->user_model->update_user($user->id, $arr))
        {
          $sc = FALSE;
          $this->error = "เปลี่ยนรหัสลับไม่สำเร็จ";
        }
      }
    }
    else
    {
      $sc = FALSE;
      $this->error = "ไม่พบ user หรือ user ไม่ถูกต้อง";
    }

    echo $sc === TRUE ? 'success' : $this->error;

  }

}
 ?>
