<?php
header('Content-Type: application/json');
defined('BASEPATH') OR exit('No direct script access allowed');

class C_react_mobile extends CI_Controller {
    public $data = [];
    private $keyToken = 's3Cr3T-G4N';
    function __construct(){
        parent::__construct();
        $this->load->model('master/m_master');
        $this->load->library('JWT');
        try {
          $G_setting = $this->m_master->showData_array('db_ticketing.rest_setting');
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

    private function __addHttpOrhttps($arr){
      $rs = [];
      for ($i=0; $i < count($arr); $i++) { 
        $rs[] = 'http://'.$arr[$i];
        $rs[] = 'https://'.$arr[$i];
      }
      return $rs;
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        $data_arr = json_decode(json_encode($data_arr),true);
        return $data_arr;
    }

    private function AsIT($dataEMP){
      $IT_DivisionID = 12;
      $bool = false;
      $arr_key = ['DivisionID','DivisionID_other1','DivisionID_other2','DivisionID_other3'];
      if (count($dataEMP) > 0 ) {
        for ($i=0; $i < count($dataEMP); $i++) { 
          $s = $dataEMP[$i];
          foreach ($s as $key => $value) {
            if (in_array($key, $arr_key) && $value == $IT_DivisionID ) {
              $bool = true;
              break;
            }
          }

          if ($bool) {
            break;
          }
        }
      }

      return $bool;
    }

    public function LoginMobile(){
      $rs = ['status' => -1,'msg' => '','callback' => []];
      $dataToken = $this->getInputToken();
      $data = $dataToken['data'];
      $NIP = $data['NIP'];
      $Password = $this->m_master->genratePassword($NIP, $data['Password']);
      // $dataEMP = $this->db->query(
      //   'select * from db_employees.employees where NIP = "'.$NIP.'" and Password = "'.$Password.'" 
      //   and Status = "1"
      //    '
      // )->result_array();

      $checkEMP =  $this->db->query('
          select count(*) as total from 
          db_employees.employees where NIP = "'.$NIP.'" and Password = "'.$Password.'" 
        and Status = "1"
        ')->row()->total;

      $dataEMP = $this->m_master->SearchEmployeesByNIP($NIP);

      $RuleUser = $this->db->query('SELECT * FROM db_employees.rule_users WHERE NIP LIKE "'.$NIP.'"')->result_array();
      if ($checkEMP > 0 && count($RuleUser) > 0 ) {
         $this->load->model('ticketing/m_general');
        // read rule user
         $DeptList = [];

         $AsIT = $this->AsIT($dataEMP);

         $As = function($NIP,$DepartmenID) {
          $Total = $this->db->query(
            'select count(*) as total from db_ticketing.admin_register where NIP = "'.$NIP.'"
             and DepartmentID = "'.$DepartmenID.'"
            ' 
          )->result_array();$Total = $Total[0]['total'];
          $adm  = ($Total > 0) ? true : false;
          $auth = $this->m_general->auth($DepartmenID,$NIP);
          return ($adm || $auth) ? 'Admin' : 'Non Admin';
         };

         for ($i=0; $i <count($RuleUser) ; $i++) { 
           switch ($RuleUser[$i]['IDDivision']) {
             case '15': // Prodi
             case 15:
               if ($AsIT) {
                 $getPrody = $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
                 for ($z=0; $z < count($getPrody); $z++) { 
                   $DepartmenID = 'AC.'.$getPrody[$z]['ID'];
                   $DeptList[] = [
                    'DepartmenID' => $DepartmenID,
                    'DepartmentName' => 'Prodi '.$getPrody[$z]['Name'],
                    'As' => 'Admin'
                   ];
                 }
                 
               }
               else
               {
                  $Auth_prodi = $this->m_master->caribasedprimary('db_prodi.auth_prodi','NIP',$NIP);
                  if (count($Auth_prodi) > 0) {
                    $Auth_prodi =   $ProdiAuth[0]['ProdiAuth'];
                    $Auth_prodi =   json_decode($Auth_prodi,true);
                    for ($j=0; $j < count($Auth_prodi); $j++) { 
                      $ProdiID =  $Auth_prodi[$i];
                      $d = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                      $DepartmenID = 'AC.'.$ProdiID;
                      $DeptList[] = [
                       'DepartmenID' => $DepartmenID,
                       'DepartmentName' => 'Prodi '.$d[0]['Name'],
                       'As' =>   $As($NIP,$DepartmenID)
                      ];
                    }
                  }
               }
               
               break;
             
             case '34':
             case 34:
              if ($AsIT) {
                $getFaculty = $this->m_master->showData_array('db_academic.faculty');
                for ($z=0; $z < count($getFaculty); $z++) { 
                    $DepartmenID = 'FT.'.$getFaculty[$z]['ID'];
                    $DeptList[] = [
                     'DepartmenID' => $DepartmenID,
                     'DepartmentName' => 'Fakultas '.$getFaculty[$z]['Name'],
                     'As' => 'Admin'
                    ];
                }
              }
              else
              {
                  $a_ID = $this->m_master->caribasedprimary('db_academic.faculty','AdminID',$NIP);
                  $k_ID = $this->m_master->caribasedprimary('db_academic.faculty','NIP',$NIP);
                  if (count($a_ID) > 0) {
                      $DepartmenID = 'FT.'.$a_ID[0]['ID'];
                      $DeptList[] = [
                       'DepartmenID' => $DepartmenID,
                       'DepartmentName' => 'Fakultas '.$a_ID[0]['Name'],
                       'As' => $As($NIP,$DepartmenID)
                      ];
                  }
                  elseif (count($k_ID) > 0) {
                      $DepartmenID = 'FT.'.$k_ID[0]['ID'];
                      $DeptList[] = [
                       'DepartmenID' => $DepartmenID,
                       'DepartmentName' => 'Fakultas '.$k_ID[0]['Name'],
                       'As' => $As($NIP,$DepartmenID)
                      ];
                  }
              }
              
              break;  

             default:
               if ($AsIT) {
                  $DeptDiv = $this->db->query(
                     'select * from db_employees.division where ID = '.$RuleUser[$i]['IDDivision'].'  '
                  )->result_array();
                  $DepartmenID = 'NA.'.$DeptDiv[0]['ID'];
                  $DeptList[] = [
                   'DepartmenID' => $DepartmenID,
                   'DepartmentName' => $DeptDiv[0]['Division'],
                   'As' => 'Admin'
                  ];
                  
               }
               else
               {
                $DeptDiv = $this->db->query(
                   'select * from db_employees.division where ID = '.$RuleUser[$i]['IDDivision'].'  '
                )->result_array();
                $DepartmenID = 'NA.'.$DeptDiv[0]['ID'];
                $DeptList[] = [
                 'DepartmenID' => $DepartmenID,
                 'DepartmentName' => $DeptDiv[0]['Division'],
                 'As' => $As($NIP,$DepartmenID)
                ];
               }
              
               break;
           }
         }

         $rs['status'] = 1;
         $rs['callback'] = [
          'data' => $dataEMP,
          'deptList' => $DeptList
         ];
         // set expired for auto login
         $timestamp = date('Y-m-d H:i:s');
         $start_date = date($timestamp);
         $expires = strtotime('+30 days', strtotime($timestamp));
         $rs['callback']['expiresIn'] = $expires;
       }

      echo json_encode($rs);


    }

}