<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_slider extends Prodi_Controler {
    public $data = array();

    function __construct()
    {
        parent::__construct();
        // load slider_model
        $this->load->model('webdivisi/beranda/m_slider');
        $this->load->helper(array('form', 'url'));
    }


    public function temp($content)
    {
        parent::template($content);
    }


    public function slider()
    {
        // Database prodi

        $data['tampil'] = $this->m_slider->getAllSlider();
    	$data['department'] = parent::__getDepartement();
    	$content = $this->load->view('page/'.$data['department'].'/beranda/v_slider',$data,true);
    	parent::template($content);
    }

    public function simpan(){
        if($this->m_slider->validation()){ // Jika validasi sukses atau hasil validasi adalah true

              $this->m_slider->save(); // Panggil fungsi save() yang ada di m_slider.php
              // Load ulang view.php agar data yang baru bisa muncul di tabel pada view.php
              $data['department'] = parent::__getDepartement();
              $html = $this->load->view('page/'.$data['department'].'/beranda/v_slider', array('model'=>$this->m_slider->view()), true);
              parent::template($html);

              $callback = array(
                'status'=>'sukses',
                'pesan'=>'Data berhasil disimpan',
                'html'=>$html
              );
              
        }else{
          $callback = array(
            'status'=>'gagal',
            'pesan'=>validation_errors()
          );
        }
        echo json_encode($callback);
      }


    function data_slider(){
        $data['tampil']=$this->m_slider->slider_list();
        echo json_encode($data);
    }

    function UploadImages(){
        $config['upload_path']="./assets/template/img/slider"; //path folder file upload
        $config['allowed_types']='gif|jpg|png'; //type file yang boleh di upload
        $config['encrypt_name'] = TRUE; //enkripsi file name upload
        $this->load->library('upload',$config); //call library upload 
         if ( ! $this->upload->do_upload('gambar'))
                {
                        $error = array('error' => $this->upload->display_errors());
        }else{
                        $data = array('upload_data' => $this->upload->data()); //ambil file name yang diupload
                        $judul= $this->input->post('judul'); //get judul image
                        $image= $data['upload_data']['file_name']; //set file name ke variable image
                        $koprodiid= $this->session->userdata('NIP');
                        $date= date('Y-m-d H:i:s');
                        $status= $this->input->post('status');
                        $user= $this->session->userdata('Name');
                        $prodi= $this->session->userdata('prodi_active');
                        $data=$this->m_slider->simpan_images($koprodiid,$user,$prodi,$date,$judul,$image,$status);//kirim value ke model m_upload
                        echo json_encode($data);
                 }            
        }

    function get_slider(){
            $idslider=$this->input->get('id');
            $data=$this->m_slider->get_slider_by_id($idslider);
            echo json_encode($data);
        }

        
}

