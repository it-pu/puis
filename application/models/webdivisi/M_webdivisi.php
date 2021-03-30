<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_webdivisi extends CI_Model {
    public $data = array();

    function __construct()
    {
        $this->load->model('master/m_master');
        parent::__construct();
    }

    public function __process_auth_prodi($action,$NIP,$ProdiID){
        $G_dt = $this->m_master->caribasedprimary('db_webdivisi.auth_prodi','NIP',$NIP);
        switch ($action) {
            case 'add':
                if (count($G_dt) == 0) {
                   $arr_ProdiID = [$ProdiID];
                   $ProdiID = json_encode($arr_ProdiID);
                   $dataSave = [
                       'NIP' => $NIP,
                       'ProdiAuth' => $ProdiID,
                   ];

                   $this->db->insert('db_webdivisi.auth_prodi',$dataSave);
                }
                else
                {
                    $arr_ProdiID = json_decode($G_dt[0]['ProdiAuth'],true);
                    // check exist
                    $Bool = true;
                    for ($i=0; $i < count($arr_ProdiID); $i++) { 
                       if ($arr_ProdiID[$i] == $ProdiID) {
                          $Bool = false;
                          break;
                       }
                    }

                    if ($Bool) {
                       $arr_ProdiID[] = $ProdiID;
                       $dataSave = [
                        'NIP' => $NIP,
                        'ProdiAuth' => json_encode($arr_ProdiID),
                       ];

                       $this->db->where('ID',$G_dt[0]['ID']);
                       $this->db->update('db_webdivisi.auth_prodi',$dataSave);
                    }
                }

                // insert di rule  user
                $IDDivision = $this->session->userdata('IDdepartementNavigation');
                $G_rule_user = $this->db->query('select * from db_employees.rule_users where NIP = "'.$NIP.'" and IDDivision = '.$IDDivision.' ',array())->result_array();
                if (count($G_rule_user) == 0) {
                    $dataSave = [
                        'NIP' => $NIP,
                        'IDDivision' => $IDDivision,
                        'privilege' => 1,
                    ];

                    $this->db->insert('db_employees.rule_users',$dataSave);
                }

                
                break;
            case 'edit':
                if (count($G_dt) == 0) {
                   $arr_ProdiID = [$ProdiID];
                   $ProdiID = json_encode($arr_ProdiID);
                   $dataSave = [
                       'NIP' => $NIP,
                       'ProdiAuth' => $ProdiID,
                   ];

                   $this->db->insert('db_webdivisi.auth_prodi',$dataSave);
                }
                else
                {
                    $arr_ProdiID = json_decode($G_dt[0]['ProdiAuth'],true);
                    // check exist
                    $Bool = true;
                    for ($i=0; $i < count($arr_ProdiID); $i++) { 
                       if ($arr_ProdiID[$i] == $ProdiID) {
                          $Bool = false;
                          break;
                       }
                    }

                    if ($Bool) {
                       $arr_ProdiID[] = $ProdiID;
                       $dataSave = [
                        'NIP' => $NIP,
                        'ProdiAuth' => json_encode($arr_ProdiID),
                       ];

                       $this->db->where('ID',$G_dt[0]['ID']);
                       $this->db->update('db_webdivisi.auth_prodi',$dataSave);
                    }
                }
                break;
            case 'delete':
                if (count($G_dt) > 0 ) {
                    $arr_ProdiID = json_decode($G_dt[0]['ProdiAuth'],true);
                    $arr_rs = [];
                    for ($i=0; $i < count($arr_ProdiID); $i++) { 
                        if ($arr_ProdiID[$i] != $ProdiID) {
                            $arr_rs[] = $arr_ProdiID[$i];
                        }
                    }

                    if (count($arr_rs) > 0) {
                       $arr_ProdiID = json_encode($arr_rs);
                       $dataSave = [
                        'NIP' => $NIP,
                        'ProdiAuth' => $arr_ProdiID,
                       ];

                       $this->db->where('NIP',$NIP);
                       $this->db->update('db_webdivisi.auth_prodi',$dataSave);
                    }
                    else
                    {
                        $this->db->where('NIP',$NIP);
                        $this->db->delete('db_webdivisi.auth_prodi');

                        $IDDivision = $this->session->userdata('IDdepartementNavigation');
                        $this->db->where('NIP',$NIP);
                        $this->db->where('IDDivision',$IDDivision);
                        $this->db->delete('db_employees.rule_users');
                    }

                }
                 
                break;
            default:
                # code...
                break;
        }
    }

    private function __join_prodi_auth($NIP,$GetProdi = []){
        $G_data = $this->m_master->caribasedprimary('db_webdivisi.auth_prodi','NIP',$NIP);
        if (count($G_data) > 0) {
            $ProdiAuth = $G_data[0]['ProdiAuth']; // NIP is UNIQUE
            $arr_ProdiAuth = json_decode($ProdiAuth,true);
            for ($i=0; $i < count($arr_ProdiAuth); $i++) {
                $ProdiID =  $arr_ProdiAuth[$i];
                // cek ProdiID exist
                $Bool = true;
                for ($j=0; $j <count($GetProdi) ; $j++) { 
                    if ($GetProdi[$j]['ID'] == $ProdiID) {
                        $Bool = false;
                        break;
                    }
                }
                if ($Bool) {
                    $d = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                    // print_r($ProdiID);die();
                    $GetProdi[] = $d[0];
                }
            }
        }
        return $GetProdi;
    }

    public function auth($ProdiID = NULL)
    {
        $PositionMain = $this->session->userdata('PositionMain');
        $DivisionID = (int)$PositionMain['IDDivision'];
        $IDPosition = (int)$PositionMain['IDPosition'];

        // Position Other 1
        $PositionOther1 = $this->session->userdata('PositionOther1');
        $DivisionIDOther1 = (int)$PositionOther1['IDDivisionOther1'];
        $IDPositionOther1 = (int)$PositionOther1['IDPositionOther1'];

        // Position Other 2
        $PositionOther2 = $this->session->userdata('PositionOther2');
        $DivisionIDOther2 = (int)$PositionOther2['IDDivisionOther2'];
        $IDPositionOther2 = (int)$PositionOther2['IDPositionOther2'];


        // Position Other 3
        $PositionOther3 = $this->session->userdata('PositionOther3');
        $DivisionIDOther3 = (int)$PositionOther3['IDDivisionOther3'];
        $IDPositionOther3 = (int)$PositionOther3['IDPositionOther3'];

        // get prodi
        $GetProdi = (  ($DivisionID == 12  || $DivisionIDOther1 == 12 || $DivisionIDOther2 == 12 || $DivisionIDOther3 == 12  ) || ( ($IDPosition < 5 && $IDPosition >0 ) || ($IDPositionOther1 < 5 && $IDPositionOther1 > 0 ) || ($IDPositionOther2 < 5 && $IDPositionOther2 > 0 ) || ( $IDPositionOther3 < 5 && $IDPositionOther3 > 0 ) )  ) ?  $this->m_master->caribasedprimary('db_academic.program_study','Status',1):array();
     
        // check Prodi
        $NIP = $this->session->userdata('NIP');
        $a_ID = $this->m_master->caribasedprimary('db_academic.program_study','AdminID',$NIP);
        $k_ID = $this->m_master->caribasedprimary('db_academic.program_study','KaprodiID',$NIP);

        if (count($a_ID) > 0) {
            $GetProdi = $a_ID;
        }
        elseif ($IDPosition == 5 || $IDPositionOther1 == 5 || $IDPositionOther2 == 5 || $IDPositionOther3 == 5 ) { // for dekan
            $Fculty = $this->m_master->caribasedprimary('db_academic.faculty','NIP',$this->session->userdata('NIP') );
            if (count($Fculty ) > 0) {
                // cek prody faculty
                $ID = $Fculty[0]['ID'];
                $GetProdi = $this->m_master->caribasedprimary('db_academic.program_study','FacultyID',$ID);
            }
            else
            {
                redirect(base_url().'page404');die();
            }
        }
        elseif (count($k_ID) > 0) {
            $GetProdi = $k_ID;
        }
        else
        {
            // if ($DivisionID != 12 && $IDPosition > 6) {
            //    redirect(base_url().'page404');die();
            // }
            if ( ($DivisionID == 14 || $DivisionIDOther1 == 14 || $DivisionIDOther2 == 14 || $DivisionIDOther3 == 14 ) &&  ($IDPosition == 7  || $IDPositionOther1 == 7 || $IDPositionOther2 == 7 || $IDPositionOther3 == 7 )  ) {
                $G_emp = $this->m_master->caribasedprimary('db_employees.employees','NIP',$NIP);
                $ProdiID = $G_emp[0]['ProdiID'];
                $GetProdi = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
            }
            else
            {
                if (  ($DivisionID == 12  || $DivisionIDOther1 == 12 || $DivisionIDOther2 == 12 || $DivisionIDOther3 == 12  ) || ( ($IDPosition < 5 && $IDPosition >0 ) || ($IDPositionOther1 < 5 && $IDPositionOther1 > 0 ) || ($IDPositionOther2 < 5 && $IDPositionOther2 > 0 ) || ( $IDPositionOther3 < 5 && $IDPositionOther3 > 0 ) )  ) {
                   $GetProdi= $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
                }
                else
                {
                    $GetProdi=[]; 
                }
               
            }
            
        }

        $GetProdi = $this->__join_prodi_auth($NIP,$GetProdi);

        if (count($GetProdi) > 0) {
            $this->session->set_userdata('prodi_get',$GetProdi);
            if ($ProdiID == NULL || $ProdiID == '') {
                // if (count($GetProdi) == 1) {
                //     $a = $this->session->userdata('prodi_get');
                //     $prodi_active = $a[0]['Name'];
                //     $prodi_active = strtolower($prodi_active);
                //     $prodi_active = str_replace(" ", "-", $prodi_active);
                //     $this->session->set_userdata('prodi_active',$prodi_active);
                //     $this->session->set_userdata('prodi_active_id',$a[0]['ID']);
                // }
                $a = $this->session->userdata('prodi_get');
                $prodi_active = $a[0]['Name'];
                $prodi_active = strtolower($prodi_active);
                $prodi_active = str_replace(" ", "-", $prodi_active);
                $this->session->set_userdata('prodi_active',$prodi_active);
                $this->session->set_userdata('prodi_active_id',$a[0]['ID']);
            }
            else
            {
                $GetProdi_selected = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                $a = $this->session->userdata('prodi_get');
                $prodi_active = $GetProdi_selected[0]['Name'];
                $prodi_active = strtolower($prodi_active);
                $prodi_active = str_replace(" ", "-", $prodi_active);
                $this->session->set_userdata('prodi_active',$prodi_active);
                $this->session->set_userdata('prodi_active_id',$GetProdi_selected[0]['ID']);

            }
            
        }
        else
        {
            redirect(base_url().'page404');die();
        }
    }



    /*ADDED BY FEBRI @ MAY 2020 # STOCK GOOD */
    public function fetchMyPurchaseOrder(){
        
    }
    /*END ADDED BY FEBRI @ MAY 2020 # STOCK GOOD */
}