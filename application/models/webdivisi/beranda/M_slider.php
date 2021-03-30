<?php 
class M_slider extends CI_Model{

    public function __construct()
    {
        parent::__construct();
        // Load database prodi
        $this->db2 = $this->load->database('webdivisi', TRUE);
    }

    // Fungsi untuk validasi form tambah dan ubah
      public function validation($mode){
        $this->load->library('form_validation'); // Load library form_validation untuk proses validasinya
        // Tambahkan if apakah $mode save atau update
        // Karena ketika update, NIS tidak harus divalidasi
        // Jadi NIS di validasi hanya ketika menambah data siswa saja
        if($mode == "save")
          $this->form_validation->set_rules('input_images', 'Images', 'required');
        $this->form_validation->set_rules('input_title', 'TitleImages', 'required');
        $this->form_validation->set_rules('input_status', 'status', 'required');
        
        if($this->form_validation->run()) // Jika validasi benar
          return true; // Maka kembalikan hasilnya dengan TRUE
        else // Jika ada data yang tidak sesuai validasi
          return false; // Maka kembalikan hasilnya dengan FALSE
      }


      // Fungsi untuk melakukan simpan data ke tabel siswa
      public function save(){
        $data = array(
          "images" => $this->input->post('input_images'),
          "title" => $this->input->post('input_title'),
          "status" => $this->input->post('input_status'),
          "user" =>  $this->session->userdata('Name'),
          "prodi" => $this->session->userdata('prodi_active'),
          "date" => date('m-d-Y')
        );
        $this->db2->insert('slider', $data); // Untuk mengeksekusi perintah insert data
    }


    function getAllSlider()
    {
        return $this->db2->get('slider')->result();
    }


    function slider_list(){
        // $hasil=$this->db2->query("SELECT * FROM slider");
        $array = array(
        'judul' => 'judul',
        'gambar' => 'gambar',
        'date'  => 'date',
        'user' => 'user'
        );
         return array();
        // return $hasil->result();
    }


    function simpan_images($koprodiid,$user,$prodi,$date,$judul,$image,$status)
    {
        $hasil=$this->db2->query("INSERT INTO slider VALUES('','$koprodiid','$judul','$image','$date','$user','$prodi','$status')");
    }


    function get_slider_by_id($idslider){
        $hsl=$this->db2->query("SELECT * FROM slider WHERE ID_Slider='$idslider'");
        if($hsl->num_rows()>0){
            foreach ($hsl->result() as $data) {
                $hasil=array(
                    'judul' => $data->judul,
                    'gambar' => $data->gambar,
                    );
            }
        }
        return $hasil;
    }


}
?>