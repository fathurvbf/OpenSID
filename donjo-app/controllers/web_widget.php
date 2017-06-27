<?php if(!defined('BASEPATH')) exit('No direct script access allowed');
class web_widget extends CI_Controller{

	function __construct(){
		parent::__construct();
		session_start();

		// Jika offline_mode dalam level yang menyembunyikan website,
		// tidak perlu menampilkan halaman website
		if ($this->setting->offline_mode >= 2) {
			redirect('hom_desa');
			exit;
		}

		$this->load->model('user_model');
		$grup	= $this->user_model->sesi_grup($_SESSION['sesi']);
		if($grup!=1 AND $grup!=2 AND $grup!=3 AND $grup!=4) redirect('siteman');
		$this->load->model('header_model');
		$this->load->model('web_widget_model');
		$this->modul_ini = 13;
	}

	function clear(){
		unset($_SESSION['cari']);
		unset($_SESSION['filter']);
		redirect('web');
	}

	function pager(){
		if(isset($_POST['per_page']))
			$_SESSION['per_page']=$_POST['per_page'];
		redirect("web_widget");
	}

	function index($p=1,$o=0){
		$data['p']      = $p;
		$data['o']      = $o;

		if(isset($_SESSION['cari']))
			$data['cari'] = $_SESSION['cari'];
		else $data['cari'] = '';

		if(isset($_SESSION['filter']))
			$data['filter'] = $_SESSION['filter'];
		else $data['filter'] = '';

		if(isset($_POST['per_page']))
			$_SESSION['per_page']=$_POST['per_page'];
		$data['per_page'] = $_SESSION['per_page'];

		$data['paging']  = $this->web_widget_model->paging($p,$o);
		$data['main']    = $this->web_widget_model->list_data($o, $data['paging']->offset, $data['paging']->per_page);
		$data['keyword'] = $this->web_widget_model->autocomplete();

		$header = $this->header_model->get_data();
		$nav['act']=7;

		$this->load->view('header', $header);
		$this->load->view('web/nav',$nav);
		$this->load->view('web/artikel/widget',$data);
		$this->load->view('footer');
	}

	function form($cat=1,$p=1,$o=0,$id=''){

		$data['p'] = $p;
		$data['o'] = $o;
		$data['cat'] = $cat;

		if($id){
			$data['artikel']        = $this->web_artikel_model->get_artikel($id);
			$data['form_action'] = site_url("web/update/$cat/$id/$p/$o");
		}
		else{
			$data['artikel']        = null;
			$data['form_action'] = site_url("web/insert/$cat");
		}

		$data['kategori'] = $this->web_artikel_model->get_kategori($cat);

		$header = $this->header_model->get_data();

		if($cat == 1003) $nav['act'] = 7;
		else $nav['act'] = 0;

		$this->load->view('header', $header);
		//$this->load->view('web/spacer');
		$this->load->view('web/nav',$nav);
		if($cat != 1003)
			$this->load->view('web/artikel/form',$data);
		else
			$this->load->view('web/artikel/widget-form',$data);

		$this->load->view('footer');
	}

	function search(){
		$cari = $this->input->post('cari');
		if($cari!='')
			$_SESSION['cari']=$cari;
		else unset($_SESSION['cari']);
		redirect("web_widget");
	}

	function filter(){
		$filter = $this->input->post('filter');
		if($filter!=0)
			$_SESSION['filter']=$filter;
		else unset($_SESSION['filter']);
		redirect("web_widget");
	}

	function insert($cat=1){
		$this->web_artikel_model->insert($cat);
		if ($cat == 1003) redirect("web/widget");
		else redirect("web/index/$cat");
	}

	function update($cat=0,$id='',$p=1,$o=0){
		$this->web_artikel_model->update($cat, $id);
		if ($cat == 1003) redirect("web/widget");
		else redirect("web/index/$cat/$p/$o");
	}

	function delete($cat=1,$p=1,$o=0,$id=''){
		$this->web_artikel_model->delete($id);
		if ($cat == 1003) redirect("web/widget");
		else redirect("web/index/$cat/$p/$o");
	}

	function hapus($cat=1,$p=1,$o=0){
		$this->web_artikel_model->hapus($cat);
		if ($cat == 1003) redirect("web/widget");
		else redirect("web/index/1/$p/$o");
	}

	function delete_all($cat=1,$p=1,$o=0){
		$this->web_artikel_model->delete_all();
		if ($cat == 1003) redirect("web/widget");
		else redirect("web/index/$p/$o");
	}

	function urut($id=0, $arah=0){
		$this->web_widget_model->urut($id,$arah);
		redirect("web_widget");
	}

	function lock($id=0){
		$this->web_widget_model->lock($id,1);
		redirect("web_widget");
	}

	function unlock($id=0){
		$this->web_widget_model->lock($id,2);
		redirect("web_widget");
	}

}
