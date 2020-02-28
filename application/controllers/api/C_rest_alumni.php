<?php
header('Content-Type: application/json');
defined('BASEPATH') OR exit('No direct script access allowed');

class C_rest_alumni extends CI_Controller {
    public $data = [];
    private $keyToken = 's3Cr3T-G4N';
    private $callback = ['status' => 0,'msg' => '','callback' => array() ];
    function __construct(){
        parent::__construct();
        $this->load->model('master/m_master');
        $this->load->model('alumni/m_alumni');
        $this->load->library('JWT');
        try {
          $G_setting = $this->m_master->showData_array('db_alumni.rest_setting');
          if (!$this->auth($G_setting)) {
            echo '{"status":"999","message":"Not Authenfication"}'; 
            die();
          }
          else
          {
            header('Access-Control-Allow-Origin: *');
          }
        } catch (Exception $e) {
          echo json_encode($e);
          die();
        }
    }

    private function auth($G_setting){
      $Bool = false;
      try {
        $dataToken = $this->getInputToken();
        $getallheaders = getallheaders();
        foreach ($getallheaders as $name  => $value) {
          if ($name == 'Hjwtkey' && $value == $G_setting[0]['Hjwtkey']) {
            // cek api get
            if(isset($_GET['apikey']) && $_GET['apikey'] == $G_setting[0]['Apikey'] && array_key_exists("auth",$dataToken) &&  $dataToken['auth'] == $this->keyToken ) {
                $Bool = true;
                break;
            } 
          }
        }

        return $Bool;
      } catch (Exception $e) {
         echo json_encode($e);
         die();
      }

      return false;
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        $data_arr = json_decode(json_encode($data_arr),true);
        return $data_arr;
    }

    public function registration(){
      $input = $this->getInputToken();
      $action = $input['action'];
      switch ($action) {
        case 'registration':
          $data = $input['data'];
          $DtRegistrationByNPM = $this->m_master->caribasedprimary('db_alumni.registration','NPM',$data['NPM']);
          if (count($DtRegistrationByNPM) > 0 ) {
             if ($data['ChooseAlumni'] == 0) {
               // delete data di registration
               $actTable = 'delete';
               $this->callback = $this->m_alumni->registration($actTable,$data);
             }
             else
             {
              $this->callback['status'] = 1;
             }
          }
          else
          {
            if ($data['ChooseAlumni'] == 1) {
              $actTable = 'create';
              $this->callback = $this->m_alumni->registration($actTable,$data);
            }
            else
            {
              $this->callback['status'] = 1;
            }
            
          }

          echo json_encode($this->callback);

          break;
        default:
          # code...
          break;
      }
    }

    public function authAlumniAPISession(){
      $input = $this->getInputToken();
      $action = $input['action'];
      switch ($action) {
        case 'authLogin':
          $data = $input['data'];
          $this->callback =  $this->m_alumni->authLogin($data);
          echo json_encode($this->callback);
          break;
        
        default:
          # code...
          break;
      }
    }

    public function change_photo(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->change_photo($input);
      echo json_encode($this->callback);
    }

    public function save_biodata(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->save_biodata($input);
      echo json_encode($this->callback);
    }

    public function upd_tbl_ta(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->upd_tbl_ta($input);
      echo json_encode($this->callback);
    }

    public function load_data_education(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->load_data_education($input);
      echo json_encode($this->callback);
    }

    public function submit_education(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->submit_education($input);
      echo json_encode($this->callback);
    }

    public function load_data_skills(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->load_data_skills($input);
      echo json_encode($this->callback);
    }

    public function submit_skills(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->submit_skills($input);
      echo json_encode($this->callback);
    }

    public function load_data_forum_server_side(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->load_data_forum_server_side($input);
      echo json_encode($this->callback);
    }

    public function submit_forum_alumni(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->submit_forum_alumni($input);
      echo json_encode($this->callback);
    }

    public function get_detail_topic(){
      $input = $this->getInputToken();
      $this->callback = $this->m_alumni->get_detail_topic($input);
      echo json_encode($this->callback);
    }

}