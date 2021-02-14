<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PS_Controller extends CI_Controller
{
  public $pm;
  public $home;
  // public $ms;
  // public $mc;
  // public $cn;
  public $close_system;
  // public $notibars;
  // public $WC;
  // public $WT;
  // public $WS;
  // public $WU;
  // public $WQ;
  // public $WV;
  // public $RR;
  // public $WL;
  public $isViewer;
	public $_SuperAdmin = FALSE;
	public $_user;
	public $error;

  public function __construct()
  {
    parent::__construct();


    //--- check is user has logged in ?
    _check_login();

    $this->close_system   = getConfig('CLOSE_SYSTEM'); //--- ปิดระบบทั้งหมดหรือไม่

    if($this->close_system == 1)
    {
      redirect('setting/maintenance');
    }


    $uid = get_cookie('uid');
		$this->_user = $this->user_model->get_user_by_uid($uid);

    $this->isViewer = $this->_user->is_viewer == 1 ? TRUE : FALSE;
		$this->_SuperAdmin = $this->_user->id_profile == -987654321 ? TRUE : FALSE;

    //$this->notibars = getConfig('NOTI_BAR');

    //--- get permission for user
    $this->pm = get_permission($this->menu_code, $uid, get_cookie('id_profile'));

    $language = getConfig('LANGUAGE');
    $display_lang = get_cookie('display_lang');
    $this->language = empty($display_lang) ? 'thai' : $display_lang;
    $this->lang->load($this->language, $this->language);

    // if($this->notibars == 1 && $this->isViewer === FALSE)
    // {
    //   $this->WC = get_permission('SOCCSO', $uid);
  	// 	$this->WT = get_permission('SOCCTR', $uid);
  	// 	$this->WS = get_permission('SOODSP', $uid);
  	// 	$this->WU = get_permission('ICSUPP', $uid);
  	// 	$this->WQ = get_permission('ICTRFM', $uid);
    //   $this->WV = get_permission('ICTRFS', $uid);
    //   $this->RR = get_permission('ICRQRC', $uid);
    //   $this->WL = get_permission('ICLEND', $uid);
    // }
  }


	public function response($sc = TRUE)
	{
		echo $sc === TRUE ? 'success' : $this->error;
	}


  public function deny_page()
  {
    return $this->load->view('deny_page');
  }


  public function error_page()
  {
    return $this->load->view('page_error');
  }

	public function page_error()
  {
    return $this->load->view('page_error');
  }
}

?>
