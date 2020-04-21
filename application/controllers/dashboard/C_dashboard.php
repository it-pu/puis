<?php
defined('BASEPATH') OR exit('No direct script access allowed');

include_once APPPATH.'vendor/autoload.php';
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;

class C_dashboard extends Globalclass {

    function __construct()
    {
        parent::__construct();
        $this->load->model(array('master/m_master','General_model','General_model','global-informations/Globalinformation_model','hr/m_hr'));
        $this->load->helper("General_helper");
    }
    public function temp($content)
    {
        parent::template($content);
        $this->load->model(array('master/m_master','General_model','General_model','global-informations/Globalinformation_model'));
    }

    public function maintenance(){
        parent::checkMaintenanceMode();
    }

    public function index()
    {
        // $G_dt = $this->m_master->showData_array('db_prodi.cfg_sub_menu');
        // for ($i=0; $i < count($G_dt); $i++) { 
        //     $dataSave = [
        //         'cfg_group_user' => 1,
        //         'ID_cfg_sub_menu' => $G_dt[$i]['ID'],
        //         'read' => 1,
        //         'write' => 1,
        //         'update' => 1,
        //         'delete' => 1,
        //     ];

        //     $this->db->insert('db_prodi.cfg_rule_g_user',$dataSave);
        // }
        // die();

        // $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
        // $G_it = $this->m_master->getEmployeeByDepartment(12);
        // for ($i=0; $i < count($G_prodi); $i++) { 
        //     $ProdiID = $G_prodi[$i]['ID'];
        //     for ($k=0; $k < count($G_it); $k++) { 
        //         $NIPIT = $G_it[$k]['NIP'];
        //         $dataSave = [
        //             'NIP' => $NIPIT,
        //             'G_user' => 1,
        //             'ProdiID' => $ProdiID,
        //         ];

        //         $this->db->insert('db_prodi.previleges_guser',$dataSave);
        //     }
        // }
        // die();
        // $rs = $this->m_master->DeleteFileToNas("http://localhost",'admission/45.png');
        // print_r($rs);
        // die();

        $data['department'] = parent::__getDepartement();
        $dpt = $this->session->userdata('IDdepartementNavigation');

        if (file_exists(APPPATH.'views/page/'.$data['department'].'/dashboard.php')) {
            switch ($dpt) {
                case 10: // admission
                case 18: // BA
                    $set_ta = $this->m_master->showData_array('db_admission.set_ta');
                    $data['set_ta'] = $set_ta[0]['Ta'];
                    $this->m_menu->set_model('admission_sess','auth_admission_sess','menu_admission_sess','menu_admission_grouping','db_admission');
                    $content = $this->load->view('page/'.$data['department'].'/dashboard',$data,true);
                    $this->temp($content);
                break;
                case 4: // Purchasing
                    $this->m_menu2->set_model('purchasing_sess','auth_purchasing_sess','menu_purchasing_sess','menu_purchasing_grouping','db_purchasing');

                    $content = $this->load->view('page/'.$data['department'].'/dashboard',$data,true);
                    $this->temp($content);
                break;
                case 6: // Academic
                    $this->m_menu2->set_model('academic_sess','auth_academic_sess','menu_academic_sess','menu_academic_grouping','db_academic');

                    $content = $this->load->view('page/'.$data['department'].'/dashboard',$data,true);
                    $this->temp($content);
                break;
                case 12: // IT
                    $this->m_menu2->set_model('it_sess','auth_it_sess','menu_it_sess','menu_it_grouping','db_it');
                    $content = $this->load->view('page/'.$data['department'].'/dashboard',$data,true);
                    $this->temp($content);
                break;
                case 15: // Admin Prodi
                    // check session admin prodi
                    $this->load->model('prodi/m_prodi');
                    if ($this->session->userdata('prodi_get')) {
                        // check multiple akses
                        if (count($this->session->userdata('prodi_get')) > 1) {
                            if (empty($_POST)) {
                                $content = $this->load->view('global/switch_prodi',$data,true);
                                $this->temp($content);
                            }
                            else
                            {
                                $prodi_active_id =  $this->input->post('Prodi');
                                $get = $this->m_master->caribasedprimary('db_academic.program_study','ID',$prodi_active_id);
                                $this->session->set_userdata('prodi_active',$get[0]['Name']);
                                $this->session->set_userdata('prodi_active_id',$get[0]['ID']);

                                $this->session->unset_userdata(array('prodi_sess', 'auth_prodi_sess', 'menu_prodi_sess', 'menu_prodi_grouping'));
                                $this->m_menu2->set_model('prodi_sess','auth_prodi_sess','menu_prodi_sess','menu_prodi_grouping','db_prodi');
                                
                                $data['NameProdi'] = $get[0]['Name'];
                                $data['NameProdi'] = strtolower($data['NameProdi'] );
                                $data['NameProdi']  = str_replace(" ", "-", $data['NameProdi'] );
                                // print_r($data['department']);die();
                                if (file_exists(APPPATH.'views/page/'.$data['department'].'/'.$data['NameProdi'].'/dashboard.php')) {
                                    $content = $this->load->view('page/'.$data['department'].'/'.$data['NameProdi'].'/dashboard',$data,true);
                                }
                                else
                                {
                                    $content = $this->load->view('dashboard/dashboard',$data,true);
                                }
                                $this->temp($content);
                            }
                        }
                        else
                        {
                            $this->m_menu2->set_model('prodi_sess','auth_prodi_sess','menu_prodi_sess','menu_prodi_grouping','db_prodi');
                            $data['NameProdi'] = $this->session->userdata('prodi_active');
                            if (file_exists(APPPATH.'views/page/'.$data['department'].'/'.$data['NameProdi'].'/dashboard.php')) {
                                $content = $this->load->view('page/'.$data['department'].'/'.$data['NameProdi'].'/dashboard',$data,true);
                            }
                            else
                            {
                                $content = $this->load->view('dashboard/dashboard',$data,true);
                            }
                            $this->temp($content);
                        }

                    }
                    else
                    {
                        $this->m_prodi->auth(); // get session
                        $this->m_menu2->set_model('prodi_sess','auth_prodi_sess','menu_prodi_sess','menu_prodi_grouping','db_prodi');
                        // check multiple akses
                        if (count($this->session->userdata('prodi_get')) > 1) {
                            $content = $this->load->view('global/switch_prodi',$data,true);
                            $this->temp($content);
                        }
                        else
                        {
                            $data['NameProdi']  = $this->session->userdata('prodi_active');
                            $this->m_menu2->set_model('prodi_sess','auth_prodi_sess','menu_prodi_sess','menu_prodi_grouping','db_prodi');
                            if (file_exists(APPPATH.'views/page/'.$data['department'].'/'.$data['NameProdi'].'/dashboard.php')) {
                                $content = $this->load->view('page/'.$data['department'].'/'.$data['NameProdi'].'/dashboard',$data,true);
                            }
                            else
                            {
                                $content = $this->load->view('dashboard/dashboard',$data,true);
                            }

                            $this->temp($content);
                        }
                    }
                break;
                case 34: // Admin Fakultas
                    // check session admin Fakultas
                    $this->load->model('faculty/m_faculty');
                    if ($this->session->userdata('faculty_get')) {
                        // check multiple akses
                        if (count($this->session->userdata('faculty_get')) > 1) {
                            if (empty($_POST)) {
                                $content = $this->load->view('global/switch_faculty',$data,true);
                                $this->temp($content);
                            }
                            else
                            {
                                $faculty_active_id =  $this->input->post('faculty');
                                $get = $this->m_master->caribasedprimary('db_academic.faculty','ID',$faculty_active_id);
                                $this->session->set_userdata('faculty_active',$get[0]['Name']);
                                $this->session->set_userdata('faculty_active_id',$get[0]['ID']);
                                $data['Namefaculty'] = $get[0]['Name'];
                                $data['Namefaculty'] = strtolower($data['Namefaculty'] );
                                $data['Namefaculty']  = str_replace(" ", "-", $data['Namefaculty'] );
                                // print_r($data['department']);die();
                                if (file_exists(APPPATH.'views/page/'.$data['department'].'/'.$data['Namefaculty'].'/dashboard.php')) {
                                    $content = $this->load->view('page/'.$data['department'].'/'.$data['Namefaculty'].'/dashboard',$data,true);
                                }
                                else
                                {
                                    $content = $this->load->view('page/'.$data['department'].'/dashboard',$data,true);
                                }
                                $this->temp($content);
                            }
                        }
                        else
                        {
                            $data['Namefaculty'] = $this->session->userdata('faculty_active');
                            if (file_exists(APPPATH.'views/page/'.$data['department'].'/'.$data['Namefaculty'].'/dashboard.php')) {
                                $content = $this->load->view('page/'.$data['department'].'/'.$data['Namefaculty'].'/dashboard',$data,true);
                            }
                            else
                            {
                                $content = $this->load->view('page/'.$data['department'].'/dashboard',$data,true);
                            }
                            $this->temp($content);
                        }

                    }
                    else
                    {
                        $this->m_faculty->auth(); // get session
                        // check multiple akses
                        if (count($this->session->userdata('faculty_get')) > 1) {
                            $content = $this->load->view('global/switch_faculty',$data,true);
                            $this->temp($content);
                        }
                        else
                        {
                            $data['Namefaculty']  = $this->session->userdata('faculty_active');
                            if (file_exists(APPPATH.'views/page/'.$data['department'].'/'.$data['Namefaculty'].'/dashboard.php')) {
                                $content = $this->load->view('page/'.$data['department'].'/'.$data['Namefaculty'].'/dashboard',$data,true);
                            }
                            else
                            {
                                $content = $this->load->view('page/'.$data['department'].'/dashboard',$data,true);
                            }

                            $this->temp($content);
                        }
                    }
                break;

                default:
                    $getSemester = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
                    $data['getSemester'] = $getSemester;
                    $content = $this->load->view('page/'.$data['department'].'/dashboard',$data,true);
                    $this->temp($content);
                    break;
            }

        }
        else
        {
            $content = $this->load->view('dashboard/dashboard',$data,true);
            $this->temp($content);
        }

    }

    public function myactivities(){
        $content = $this->load->view('dashboard/activities/myactivities','',true);
        $this->temp($content);
    }
    public function ticket(){
        $content = $this->load->view('dashboard/ticket','',true);
        $this->temp($content);
    }

    public function change_departement(){
        $dpt = $this->input->post('departement');
        $IDDivision = $this->input->post('IDDivision');
        $this->session->set_userdata('IDdepartementNavigation', ''.$IDDivision);
        parent::__setDepartement($dpt);
    }

    /*public function profile($username=''){
        $data['']=123;
        $content = $this->load->view('dashboard/profile','',true);
        $this->temp($content);
    }*/

    public function load_data_registration_upload()
    {
        $content = $this->load->view('page/load_data_registration_upload',$this->data,true);
        echo $content;
    }

    public function readNotificationDivision()
    {
        $this->load->model('master/m_master');
        $this->m_master->readNotificationDivision();
        echo json_encode(1);
    }

    public function testadi()
    {
        /*for ($i=0; $i <= 100 ; $i= $i + 5) {
            $dataSave = array(
                'discount' => $i,
            );
            $this->db->insert('db_finance.discount', $dataSave);
        }

        echo 'test';*/


        $client = new Client(new Version1X('//localhost:3000'));

        $client->initialize();
        // send message to connected clients
        $client->emit('update_notifikasi', ['update_notifikasi' => '1']);
        $client->close();
    }

    public function page404(){
        parent::page404();
//        $data['']=123;
//        $content = $this->load->view('template/404page','',true);
//        $this->temp($content);
    }

    public function finance_dashboard()
    {
        // echo __FUNCTION__;
        $data['department'] = parent::__getDepartement();
        // print_r(APPPATH.'views/page/'.$data['department'].'/dashboard.php');die();
        if (file_exists(APPPATH.'views/page/'.$data['department'].'/dashboard.php')) {
            $content = $this->load->view('dashboard/finance_dashboard',$data,true);
            $this->temp($content);
        }
        else
        {
            $this->index();
        }

    }

    // public function summary_payment()
    // {
    //     $arr_json = array();
    //     $arrDB = array();
    //     $sqlDB = 'show databases like "%ta_2%"';
    //     $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
    //     $SemesterYear = $SemesterID[0]['Year'];
    //     $queryDB=$this->db->query($sqlDB, array())->result_array();
    //     foreach ($queryDB as $key) {
    //       foreach ($key as $keyB ) {
    //         $YearDB = explode('_', $keyB);
    //         $YearDB = $YearDB[1];
    //         if ($SemesterYear >= $YearDB) {
    //             $arrDB[] = $keyB;
    //         }
    //       }

    //     }

    //     rsort($arrDB);
    //     $Year = 'ta_'.date('Y');
    //     $Semester = $SemesterID[0]['ID'];
    //     $Semester = ' and SemesterID = '.$Semester;
    //     $unk = 1;

    //     // get paid off
    //     $Paid_Off = array();
    //     $Unpaid_Off = array();
    //     $unsetPaid = array();
    //     for ($i=0; $i < count($arrDB); $i++) {
    //         // if ($arrDB[$i] != $Year) {

    //             $a_Paid_Off = 0;
    //             $a_Unpaid_Off = 0;
    //             $a_unsetPaid = 0;
    //                 // get Data Mahasiswa
    //                 $sql = 'select a.NPM,a.Name,b.NameEng from '.$arrDB[$i].'.students as a join db_academic.program_study as b on a.ProdiID = b.ID where a.StatusStudentID in (3,2,8) ';
    //                 $query=$this->db->query($sql, array())->result_array();
    //                 for ($u=0; $u < count($query); $u++) {

    //                   // cek BPP
    //                   $sqlBPP = 'select * from db_finance.payment where PTID = 2 and NPM = ? '.$Semester; //  limit 1
    //                   $queryBPP=$this->db->query($sqlBPP, array($query[$u]['NPM']))->result_array();
    //                   $arrBPP = array(
    //                     'BPP' => '0',
    //                     'PayBPP' => '0',
    //                     'SisaBPP' => '0',
    //                     'DetailPaymentBPP' => '',
    //                   );
    //                     if (count($queryBPP) > 0) {
    //                         for ($t=0; $t < count($queryBPP); $t++) {
    //                           // cek payment students
    //                           $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryBPP[$t]['ID']);
    //                           $PayBPP = 0;
    //                           $SisaBPP = 0;
    //                           for ($r=0; $r < count($Q_invStudent); $r++) {
    //                             if ($Q_invStudent[$r]['Status'] == 1) { // lunas
    //                               $PayBPP = $PayBPP + $Q_invStudent[$r]['Invoice'];
    //                             }
    //                             else
    //                             {
    //                               $SisaBPP = $SisaBPP + $Q_invStudent[$r]['Invoice'];
    //                             }
    //                           }

    //                           $arrBPP = array(
    //                             'BPP' => (int)$queryBPP[$t]['Invoice'],
    //                             'PayBPP' => (int)$PayBPP,
    //                             'SisaBPP' => (int)$SisaBPP,
    //                             'DetailPaymentBPP' => $Q_invStudent,
    //                           );

    //                         }
    //                     }

    //                   // cek Credit
    //                   $sqlCr = 'select * from db_finance.payment where PTID = 3 and NPM = ? '.$Semester; // limit 1
    //                   $queryCr=$this->db->query($sqlCr, array($query[$u]['NPM']))->result_array();
    //                   $arrCr = array(
    //                     'Cr' => '0',
    //                     'PayCr' => '0',
    //                     'SisaCr' => '0',
    //                     'DetailPaymentCr' => '',
    //                   );
    //                     if (count($queryCr) > 0) {
    //                         for ($t=0; $t < count($queryCr); $t++) {
    //                           // cek payment students
    //                           $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryCr[$t]['ID']);
    //                           $PayCr = 0;
    //                           $SisaCr = 0;
    //                           for ($r=0; $r < count($Q_invStudent); $r++) {
    //                             if ($Q_invStudent[$r]['Status'] == 1) { // lunas
    //                               $PayCr = $PayCr + $Q_invStudent[$r]['Invoice'];
    //                             }
    //                             else
    //                             {
    //                               $SisaCr = $SisaCr + $Q_invStudent[$r]['Invoice'];
    //                             }
    //                           }

    //                           $arrCr = array(
    //                             'Cr' => (int)$queryCr[$t]['Invoice'],
    //                             'PayCr' => (int)$PayCr,
    //                             'SisaCr' => (int)$SisaCr,
    //                             'DetailPaymentCr' => $Q_invStudent,
    //                           );

    //                         }
    //                     }


    //                     // cek SPP
    //                     $sqlSPP = 'select * from db_finance.payment where PTID = 1 and NPM = ? '.$Semester; //  limit 1
    //                     $querySPP=$this->db->query($sqlSPP, array($query[$u]['NPM']))->result_array();
    //                     $arrSPP = array(
    //                       'SPP' => '0',
    //                       'PaySPP' => '0',
    //                       'SisaSPP' => '0',
    //                       'DetailPaymentSPP' => '',
    //                     );
    //                       if (count($querySPP) > 0) {
    //                           for ($t=0; $t < count($querySPP); $t++) {
    //                             // cek payment students
    //                             $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$querySPP[$t]['ID']);
    //                             $PaySPP = 0;
    //                             $SisaSPP = 0;
    //                             for ($r=0; $r < count($Q_invStudent); $r++) {
    //                               if ($Q_invStudent[$r]['Status'] == 1) { // lunas
    //                                 $PaySPP = $PaySPP + $Q_invStudent[$r]['Invoice'];
    //                               }
    //                               else
    //                               {
    //                                 $SisaSPP = $SisaSPP + $Q_invStudent[$r]['Invoice'];
    //                               }
    //                             }

    //                             $arrSPP = array(
    //                               'SPP' => (int)$querySPP[$t]['Invoice'],
    //                               'PaySPP' => (int)$PaySPP,
    //                               'SisaSPP' => (int)$SisaSPP,
    //                               'DetailPaymentSPP' => $Q_invStudent,
    //                             );

    //                           }
    //                       }

    //                       // cek lain-lain
    //                       $sqlAn = 'select * from db_finance.payment where PTID = 4 and NPM = ? '.$Semester; //  limit 1
    //                       $queryAn=$this->db->query($sqlAn, array($query[$u]['NPM']))->result_array();
    //                       $arrAn = array(
    //                         'An' => '0',
    //                         'PayAn' => '0',
    //                         'SisaAn' => '0',
    //                         'DetailPaymentAn' => '',
    //                       );
    //                         if (count($queryAn) > 0) {
    //                             for ($t=0; $t < count($queryAn); $t++) {
    //                               // cek payment students
    //                               $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryAn[$t]['ID']);
    //                               $PayAn = 0;
    //                               $SisaAn = 0;
    //                               for ($r=0; $r < count($Q_invStudent); $r++) {
    //                                 if ($Q_invStudent[$r]['Status'] == 1) { // lunas
    //                                   $PayAn = $PayAn + $Q_invStudent[$r]['Invoice'];
    //                                 }
    //                                 else
    //                                 {
    //                                   $SisaAn = $SisaAn + $Q_invStudent[$r]['Invoice'];
    //                                 }
    //                               }

    //                               $arrAn = array(
    //                                 'An' => (int)$queryAn[$t]['Invoice'],
    //                                 'PayAn' => (int)$PayAn,
    //                                 'SisaAn' => (int)$SisaAn,
    //                                 'DetailPaymentAn' => $Q_invStudent,
    //                               );

    //                             }
    //                         }

    //                     if ($arrBPP['DetailPaymentBPP'] == '' || $arrCr['DetailPaymentCr'] == '') { // unset paid
    //                       $a_unsetPaid = $a_unsetPaid + 1;

    //                     }
    //                     else
    //                     {
    //                         if ($arrBPP['DetailPaymentBPP'] != '' && $arrCr['DetailPaymentCr'] != '' &&  $arrBPP['SisaBPP'] == 0 && $arrCr['SisaCr'] == 0 &&  $arrSPP['SisaSPP'] == 0 && $arrAn['SisaAn'] == 0) { // lunas
    //                           $a_Paid_Off = $a_Paid_Off + 1;

    //                         }
    //                         elseif ( $arrBPP['DetailPaymentBPP'] != '' || $arrCr['DetailPaymentCr'] != '' ||  $arrBPP['SisaBPP'] > 0 || $arrCr['SisaCr'] > 0 ||  $arrSPP['SisaSPP'] > 0 || $arrAn['SisaAn'] > 0) { // belum lunas
    //                           $a_Unpaid_Off = $a_Unpaid_Off + 1;

    //                         }

    //                     }

    //                 } // loop per mhs

    //             $strUnk = $unk.'.6818181818181817';
    //             $YearDB = explode('_', $arrDB[$i]);
    //             $YearDB = $YearDB[1];

    //             $Paid_Off[] = array($YearDB,$a_Paid_Off);
    //             $Unpaid_Off[] = array($YearDB,$a_Unpaid_Off);
    //             $unsetPaid[] = array($YearDB,$a_unsetPaid);
    //             $unk++;

    //         // }
    //     }

    //     $arr_json = array('Paid_Off'=> $Paid_Off,'Unpaid_Off' => $Unpaid_Off,'unsetPaid' => $unsetPaid);
    //     echo json_encode($arr_json);
    // }

    public function summary_payment()
    {
        $arr_json = array();
        $url = url_pas.'rest/__rekapmhspayment';
        $data = array(
                'auth' => 's3Cr3T-G4N',
            );
        $Input = $this->jwt->encode($data,"UAP)(*");
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
                    "token=".$Input);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $get = curl_exec($ch);
        $get =json_decode($get, True);
        $get =(array)$get;
        curl_close ($ch);
        $arr_json = array('Paid_Off'=> json_decode($get[0]['Paid_Off']),'Unpaid_Off' => json_decode($get[0]['Unpaid_Off']),'unsetPaid' => json_decode($get[0]['unsetPaid']));
        echo json_encode($arr_json);
    }

    public function dashboard_getoutstanding_today()
    {
        $requestData= $_REQUEST;
        // print_r($requestData);
        $sql = 'select count(*) as total
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID join db_finance.payment_students as e
                on a.ID = e.ID_payment and e.Status = 0 and DATE_FORMAT(e.Deadline,"%Y-%m-%d") <= curdate() group by a.ID';
        $query=$this->db->query($sql, array())->result_array();
        $totalData = count($query);

        $sql = 'select a.*, b.Year,b.EmailPU,b.Pay_Cond,c.Name as NameSemester, d.Description
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID join db_finance.payment_students as e
                on a.ID = e.ID_payment
            ';
        $Year = date('Y');
        $sql.= ' where ( e.Status = 0 and DATE_FORMAT(e.Deadline,"%Y-%m-%d") <= curdate() ) and  (a.NPM like "'.$requestData['search']['value'].'%" or d.Description like "'.$requestData['search']['value'].'%" or c.Name like "'.$requestData['search']['value'].'%" ) and b.Year !='.$Year.' group by a.ID';
        $sql.= ' ORDER BY a.NPM ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();

        $data = array();
        $this->load->model('master/m_master');
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];
            $Year = $row['Year'];
            $getData = $this->m_master->caribasedprimary('ta_'.$Year.'.students','NPM',$row['NPM']);
            $nestedData[] = $row['NPM'].'<br>'.$getData[0]['Name'];
            $nestedData[] = $row['Description'];
            $nestedData[] = $row['NameSemester'];
            $nestedData[] = '<button class="btn btn-default edit" NPM = "'.$row['NPM'].'" semester = "'.$row['SemesterID'].'" PTID = "'.$row['PTID'].'" PaymentID = "'.$row['ID'].'"><i class="fa fa-pencil-square-o" aria-hidden="true"></i>
 Edit</button>';
            $data[] = $nestedData;
        }

        // print_r($data);

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function summary_payment_admission()
    {
        $arr_json = array();
        $arrDB = array();
        $sqlDB = 'show databases like "%ta_2%"';
        $queryDB=$this->db->query($sqlDB, array())->result_array();
        foreach ($queryDB as $key) {
          foreach ($key as $keyB ) {
            $Year = explode('_', $keyB);
            $Year = $Year[1];
            $arrDB[] = $Year;
          }

        }

        rsort($arrDB);
        $taDb = $this->m_master->showData_array('db_admission.set_ta');
        $taDb = $taDb[0]['Ta'];
         if(!in_array($taDb, $arrDB))
           {
              $arrDB[] = $taDb;
           }
        // get paid off
        $Paid_Off = array();
        $Unpaid_Off = array();
        $Unset_Paid = array();
        for ($i=0; $i < count($arrDB); $i++) {
            // lunas
            $sqlPaid_Off = 'select count(*) as total from (
                    select a.ID as ID_register_formulir,
                    if((select count(*) as total from db_finance.payment_pre where `Status` = 0 and ID_register_formulir = a.ID limit 1) = 0 ,"Lunas","Belum Lunas") as StatusPayment
                    from db_admission.register_formulir as a
                    left JOIN db_admission.register_verified as b
                    ON a.ID_register_verified = b.ID
                    left JOIN db_admission.register_verification as c
                    ON b.RegVerificationID = c.ID
                    left JOIN db_admission.register as d
                    ON c.RegisterID = d.ID
                    left JOIN db_admission.country as e
                    ON a.NationalityID = e.ctr_code
                    left JOIN db_employees.religion as f
                    ON a.ReligionID = f.IDReligion
                    left JOIN db_admission.school_type as l
                    ON l.sct_code = a.ID_school_type
                    left JOIN db_admission.register_major_school as m
                    ON m.ID = a.ID_register_major_school
                    left JOIN db_admission.school as n
                    ON n.ID = d.SchoolID
                    left join db_academic.program_study as o
                    on o.ID = a.ID_program_study
                    left join db_finance.register_admisi as p
                    on a.ID = p.ID_register_formulir
                    where p.Status = "Approved"  and d.SetTa = "'.$arrDB[$i].'" group by a.ID

                    ) SubQuery where StatusPayment = "Lunas";
                ';
            $queryPaid_Off = $this->db->query($sqlPaid_Off)->result_array();

            $sqlUnpaid_Off = 'select count(*) as total from (
                    select a.ID as ID_register_formulir,
                    if((select count(*) as total from db_finance.payment_pre where `Status` = 0 and ID_register_formulir = a.ID limit 1) = 0 ,"Lunas","Belum Lunas") as StatusPayment
                    from db_admission.register_formulir as a
                    left JOIN db_admission.register_verified as b
                    ON a.ID_register_verified = b.ID
                    left JOIN db_admission.register_verification as c
                    ON b.RegVerificationID = c.ID
                    left JOIN db_admission.register as d
                    ON c.RegisterID = d.ID
                    left JOIN db_admission.country as e
                    ON a.NationalityID = e.ctr_code
                    left JOIN db_employees.religion as f
                    ON a.ReligionID = f.IDReligion
                    left JOIN db_admission.school_type as l
                    ON l.sct_code = a.ID_school_type
                    left JOIN db_admission.register_major_school as m
                    ON m.ID = a.ID_register_major_school
                    left JOIN db_admission.school as n
                    ON n.ID = d.SchoolID
                    left join db_academic.program_study as o
                    on o.ID = a.ID_program_study
                    left join db_finance.register_admisi as p
                    on a.ID = p.ID_register_formulir
                    where p.Status = "Approved"  and d.SetTa = "'.$arrDB[$i].'" group by a.ID

                    ) SubQuery where StatusPayment = "Belum Lunas";
                ';
            $queryUnpaid_Off = $this->db->query($sqlUnpaid_Off)->result_array();

            $sqlUnset_Paid = 'select count(*) as total from (
                    select a.ID as ID_register_formulir
                    from db_admission.register_formulir as a
                    left JOIN db_admission.register_verified as b
                    ON a.ID_register_verified = b.ID
                    left JOIN db_admission.register_verification as c
                    ON b.RegVerificationID = c.ID
                    left JOIN db_admission.register as d
                    ON c.RegisterID = d.ID
                    left JOIN db_admission.country as e
                    ON a.NationalityID = e.ctr_code
                    left JOIN db_employees.religion as f
                    ON a.ReligionID = f.IDReligion
                    left JOIN db_admission.school_type as l
                    ON l.sct_code = a.ID_school_type
                    left JOIN db_admission.register_major_school as m
                    ON m.ID = a.ID_register_major_school
                    left JOIN db_admission.school as n
                    ON n.ID = d.SchoolID
                    left join db_academic.program_study as o
                    on o.ID = a.ID_program_study
                    left join db_finance.register_admisi as p
                    on a.ID = p.ID_register_formulir
                    where (p.Status = "Created"  or  a.ID not in (select ID_register_formulir from db_finance.register_admisi)  ) and d.SetTa = "'.$arrDB[$i].'" group by a.ID

                    ) SubQuery;
                ';
            $queryUnset_Paid = $this->db->query($sqlUnset_Paid)->result_array();

             $Paid_Off[] = array($arrDB[$i],$queryPaid_Off[0]['total']);
             $Unpaid_Off[] = array($arrDB[$i],$queryUnpaid_Off[0]['total']);
             $Unset_Paid[] = array($arrDB[$i],$queryUnset_Paid[0]['total']);
        }

        $arr_json = array('Paid_Off'=> $Paid_Off,'Unpaid_Off' => $Unpaid_Off,'Unset_Paid' => $Unset_Paid);
        echo json_encode($arr_json);
    }

    public function summary_payment_formulir()
    {
        $arr_json = array();
        $arrDB = array();
        $sqlDB = 'show databases like "%ta_2%"';
        $queryDB=$this->db->query($sqlDB, array())->result_array();
        foreach ($queryDB as $key) {
          foreach ($key as $keyB ) {
            $Year = explode('_', $keyB);
            $Year = $Year[1];
            $arrDB[] = $Year;
          }

        }

        rsort($arrDB);
        $taDb = $this->m_master->showData_array('db_admission.set_ta');
        $taDb = $taDb[0]['Ta'];
         if(!in_array($taDb,$arrDB))
           {
              $arrDB[] = $taDb;
           }

        $Paid_Off = array();
        $Return_Formulir = array();
        for ($i=0; $i < count($arrDB); $i++) {
            // lunas
            $sql = 'select count(*) as total from(
                select FormulirCode from db_admission.formulir_number_online_m where Status = 1 and Years = "'.$arrDB[$i].'"
                union
                select FormulirCode from db_admission.formulir_number_offline_m where StatusJual = 1 and Years = "'.$arrDB[$i].'"
            ) subquery';

            $query=$this->db->query($sql, array())->result_array();

            $sqlReturn_Formulir = 'select count(*) as total from(
                        select a.ID as ID_register_formulir
                        from db_admission.register_formulir as a
                        left JOIN db_admission.register_verified as b
                        ON a.ID_register_verified = b.ID
                        left JOIN db_admission.register_verification as c
                        ON b.RegVerificationID = c.ID
                        left JOIN db_admission.register as d
                        ON c.RegisterID = d.ID
                        where d.SetTa = "'.$arrDB[$i].'"
                    ) subquery';

            $queryReturn_Formulir=$this->db->query($sqlReturn_Formulir, array())->result_array();

            $Paid_Off[] = array($arrDB[$i],$query[0]['total']);
            $Return_Formulir[] = array($arrDB[$i],$queryReturn_Formulir[0]['total']);

        }
        $arr_json = array('Paid_Off'=> $Paid_Off,'Return_Formulir' => $Return_Formulir);
        echo json_encode($arr_json);


    }

    public function SummaryFormulirPerSales()
    {
        $arr_result = array();
        // get all grouping from sales
            $set_ta = $this->m_master->showData_array('db_admission.set_ta');
            $Ta = $set_ta[0]['Ta'];
            $sql = 'select a.PIC,if(b.Name IS NULL or b.Name = "","Unknown",b.Name) as Name,count(*) as total from db_admission.sale_formulir_offline as a
                    left join db_employees.employees as b on a.PIC = b.NIP
                    left join db_admission.formulir_number_offline_m as c
                    on a.FormulirCodeOffline = c.FormulirCode
                    where c.Years = ?
                    group by a.PIC
                    ';
            $query=$this->db->query($sql, array($Ta))->result_array();
            for ($i=0; $i < count($query); $i++) {
                $Name = explode(" ", trim($query[$i]['Name']));
                $Name = $Name[0];
                $arr_result[] = array($i,$query[$i]['total'],$Name);
            }
        $arr_json = array('arr_result'=> $arr_result);
        echo json_encode($arr_json);

    }

    public function SummaryBox()
    {
        $set_ta = $this->m_master->showData_array('db_admission.set_ta');
        $Ta = $set_ta[0]['Ta'];
        // valueFormulir
            $sql = 'select sum(Price_Form) as total from
                    (
                        select a.Price_Form from db_admission.sale_formulir_offline as a
                            left join db_admission.formulir_number_offline_m as c
                            on a.FormulirCodeOffline = c.FormulirCode
                            where c.Years = ?
                    )aa
                    ';
            $query=$this->db->query($sql, array($Ta))->result_array();

        // value tuition fee
            $sqlTuitionFee = 'select sum(Invoice) as total from(
                        select e.Invoice
                        from db_admission.register_formulir as a
                        left JOIN db_admission.register_verified as b
                        ON a.ID_register_verified = b.ID
                        left JOIN db_admission.register_verification as c
                        ON b.RegVerificationID = c.ID
                        left JOIN db_admission.register as d
                        ON c.RegisterID = d.ID
                        left join db_finance.payment_pre as e
                        on a.ID = e.ID_register_formulir
                        where d.SetTa = ? and e.Status = 1
                    ) subquery';

            $queryTuitionFee=$this->db->query($sqlTuitionFee, array($Ta))->result_array();

        $arr_json = array('Formulir'=> ($query[0]['total'] == null || $query[0]['total'] == "") ? 0 : $query[0]['total'],'tuition_fee' => ($queryTuitionFee[0]['total'] == null || $queryTuitionFee[0]['total'] == "") ? 0 : $queryTuitionFee[0]['total']);
        echo json_encode($arr_json);
    }

    public function Help()
    {
        $post = $_POST;
        if (count($post) > 0) {
            $data['selected'] = $post['Division'];
            $data['G_data'] = $this->m_master->UserQNA($data['selected']);
            echo $this->load->view('global/help/content_change',$data,true);
        }
        else
        {
            $data['G_division'] = $this->m_master->caribasedprimary('db_employees.division','StatusDiv',1);
            $data['selected'] = 6;
             $data['G_data'] = $this->m_master->UserQNA($data['selected']);
            $content = $this->load->view('global/help/help',$data,true);
            $this->temp($content);
        }

    }

    public function upload_help(){

        $fileName = $this->input->get('fileName');
        $old = $this->input->get('old');
        $ID = $this->input->get('id');

        $config['upload_path']          = './uploads/help/';
        $config['allowed_types']        = '*';
        $config['max_size']             = 8000; // 8 mb
        $config['file_name']            = $fileName;

        if($old!=''  && is_file('./uploads/help/'.$old)){
            unlink('./uploads/help/'.$old);
        }

        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('userfile')){
            $error = array('error' => $this->upload->display_errors());
            return print_r(json_encode($error));
        }
        else {

            // Update DB
            $this->db->where('ID', $ID);
            $this->db->update('db_employees.user_qna',array(
                'File' => $fileName
            ));

            $success = array('success' => $this->upload->data());
            $success['success']['formGrade'] = 0;

            return print_r(json_encode($success));
        }

    }
    public function kb()
    {
        $post = $_POST;
        if (count($post) > 0) {
            $data['selected'] = $post['Division'];
            $data['G_data'] = $this->m_master->userKB($data['selected']);
            echo $this->load->view('global/kb/content_change',$data,true);
        }
        else
        {
            $data['G_division'] = $this->m_master->caribasedprimary('db_employees.division','StatusDiv',1);
            $data['selected'] = 6;
            $data['G_data'] = $this->m_master->userKB($data['selected']);
            $content = $this->load->view('global/kb/kb',$data,true);
            $this->temp($content);
        }

    }

    public function upload_kb(){

        $fileName = $this->input->get('fileName');
        $old = $this->input->get('old');
        $ID = $this->input->get('id');

        $config['upload_path']          = './uploads/kb/';
        $config['allowed_types']        = '*';
        $config['max_size']             = 8000; // 8 mb
        $config['file_name']            = $fileName;

        if($old!=''  && is_file('./uploads/kb/'.$old)){
            unlink('./uploads/kb/'.$old);
        }

        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('userfile')){
            $error = array('error' => $this->upload->display_errors());
            return print_r(json_encode($error));
        }
        else {

            // Update DB
            $this->db->where('ID', $ID);
            $this->db->update('db_employees.knowledge_base',array(
                'File' => $fileName
            ));

            $success = array('success' => $this->upload->data());
            $success['success']['formGrade'] = 0;

            return print_r(json_encode($success));
        }

    }

    public function ShowLoggingNotification()
    {
        $content = $this->load->view('dashboard/LoggingNotification','',true);
        $this->temp($content);
    }



    /*ADDED BY FEBRI @ MARCH 2020*/
    public function tab_menu_new_emp($page,$NIP){
        
        if(!empty($_GET['resubmit']) && !empty($_GET['data'])){
            if($_GET['resubmit'] == 'yes'){
                $key = "UAP)(*";
                $data_arr = (array) $this->jwt->decode($_GET['data'],$key);
                $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$data_arr['NIP'],"isApproved"=>2))->row();
                if(!empty($isExist)){
                    $data['param'] = "?resubmit=".$_GET['resubmit']."&data=".$_GET['data'];
                    $data['Logs'] = $isExist->Logs;
                }
            }
        }
        
        $param[] = array("field"=>"em.NIP","data"=>" = ".$NIP." ","filter"=>"AND",);    
        $data['employee'] = $this->Globalinformation_model->fetchEmployee(false,$param)->row();
        $getHistoricalJoin = $this->General_model->fetchData("db_employees.employees_joindate",array("NIP"=>$NIP),"ID","ASC")->row();
        $data['employee']->HistoricalJoin = (!empty($getHistoricalJoin) ? $getHistoricalJoin : null);
        $data['NIP'] = $data['employee']->NIP;
        $data['page'] = $page;
        $content = $this->load->view('dashboard/profile/tab_menu_new_emp',$data,true);
        $this->temp($content);       
    }

    public function profile(){
        $myNIP = $this->session->userdata('NIP');
        $myName = $this->session->userdata('Name');
        $param[] = array("field"=>"em.NIP","data"=>" = ".$myNIP." ","filter"=>"AND",);    
        $data['employee'] = $this->Globalinformation_model->fetchEmployee(false,$param)->row();
        $data['NIP'] = $myNIP;
        $page = $this->load->view('dashboard/profile/personal-data',$data,true);
        $this->tab_menu_new_emp($page,$myNIP);
    }


    public function additionalInfo($NIP){
        $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$NIP))->row();
        if(!empty($isExist)){
            $data['NIP'] = $NIP;
            $data['detail'] = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$NIP))->row();
            
            $page = $this->load->view('dashboard/profile/additional-information',$data,true);
            $this->tab_menu_new_emp($page,$NIP);
        }else{show_404();}
    }


    public function family($NIP){
        $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$NIP))->row();
        if(!empty($isExist)){
            $data['NIP'] = $NIP;
            $data['familytree'] = $this->General_model->fetchData("db_employees.master_family_relations",array("IsActive"=>1))->result();
            $data['educationLevel'] = $this->General_model->fetchData("db_employees.level_education",array())->result();
            $data['myfamily'] = $this->General_model->fetchData("db_employees.employees_family_member",array("NIP"=>$NIP))->result();
            $page = $this->load->view('dashboard/profile/family',$data,true);
            $this->tab_menu_new_emp($page,$NIP);
        }else{show_404();}
    }


    public function educations($NIP){
        $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$NIP))->row();
        if(!empty($isExist)){
            $data['NIP'] = $NIP;
            $data['educationLevel'] = $this->General_model->fetchData("db_employees.level_education",array())->result();
            $data['industry'] = $this->General_model->fetchData("db_employees.master_industry_type",array("IsActive"=>1))->result();
            $page = $this->load->view('dashboard/profile/education',$data,true);
            $this->tab_menu_new_emp($page,$NIP);
        }else{show_404();}
    }


    public function training($NIP){
        $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$NIP))->row();
        if(!empty($isExist)){
            $data['NIP'] = $NIP;
            $page = $this->load->view('dashboard/profile/training',$data,true);
            $this->tab_menu_new_emp($page,$NIP);
        }else{show_404();}
    }


    public function workExperience($NIP){
        $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$NIP))->row();
        if(!empty($isExist)){
            $data['NIP'] = $NIP;
            $data['educationLevel'] = $this->General_model->fetchData("db_employees.level_education",array())->result();
            $data['industry'] = $this->General_model->fetchData("db_employees.master_industry_type",array("IsActive"=>1))->result();
            $page = $this->load->view('dashboard/profile/experience',$data,true);
            $this->tab_menu_new_emp($page,$NIP);
        }else{show_404();}
    }


    public function careerLevel($NIP){
        $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$NIP))->row();
        if(!empty($isExist)){
            $data['NIP'] = $NIP;
            $data['status'] = $this->General_model->fetchData("db_employees.master_status",array("IsActive"=>1))->result();
            $data['level'] = $this->General_model->fetchData("db_employees.master_level",array("IsActive"=>1))->result();
            //$data['division'] = $this->General_model->fetchData("db_employees.sto_temp",array("isMainSTO"=>1, "typeNode"=>1,"isActive"=>1))->result();
            $data['division'] = $this->General_model->fetchData("db_employees.division",array())->result();
            $data['position'] = $this->General_model->fetchData("db_employees.position",array())->result();
            $data['employees_status'] = $this->General_model->fetchData("db_employees.employees_status","Type != 'lec' and IDStatus != '-2'")->result();
            $data['detail'] = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$NIP))->row();
            //$data['currComp'] = $this->General_model->fetchData("db_employees.master_company",array("ID"=>1))->row();
            $page = $this->load->view('dashboard/profile/career-level',$data,true);
            $this->tab_menu_new_emp($page,$NIP);
        }else{show_404();}
    }


    public function departmentMember($NIP){
        $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$NIP))->row();
        if(!empty($isExist)){
            $data['NIP'] = $NIP;            
            $data['detail'] = $isExist;            
            $page = $this->load->view('dashboard/profile/department-member',$data,true);
            $this->tab_menu_new_emp($page,$NIP);
        }else{show_404();}
    }


    public function saveChanges($NIP){
        $myNIP = $this->session->userdata('NIP');
        $myName = $this->session->userdata('Name');
        $data = $this->input->post();
        if($data){
            $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$data['NIP']))->row();
            $uri = "profile";$err_msg=""; $action = $data['action']; unset($data['action']);
            if(!empty($isExist)){                
                if(!empty($_FILES['userfile']['name'])){                            
                    $ispic = false;
                    $file_name = $_FILES['userfile']['name'];
                    $file_size =$_FILES['userfile']['size'];
                    $file_tmp =$_FILES['userfile']['tmp_name'];
                    $file_type=$_FILES['userfile']['type'];
                    if($file_type == "image/jpeg" || $file_type == "image/png"){
                        $ispic = true;
                    }else {
                        $ispic = false;
                        $err_msg     .= "Extention image '".$file_name."' doesn't allowed.";
                    }
                    if($file_size > 2000000){ //2Mb
                        $ispic = false;
                        $err_msg     .= "Size of image '".$file_name."'s too large from 2Mb.";
                    }else { $ispic = true; }

                    $newFilename = $data['NIP']."-REQ-".date("d.m.Y").".jpg";

                    if($_SERVER['SERVER_NAME']=='pcam.podomorouniversity.ac.id'){
                        $pathInPieces = explode('/', $_SERVER['DOCUMENT_ROOT']);
                        $t = count($pathInPieces) - 1;
                        $newPath = "";
                        for ($i=0; $i < $t; $i++) { 
                            $newPath .= $pathInPieces[$i]."/";
                        }
                        $folderPCAM = $newPath.'pcam/';
                    }else{
                        $folderPCAM = $_SERVER['DOCUMENT_ROOT'].'/puis/';
                    }
                    $folderPCAM = $folderPCAM.'uploads/employees';
                    if(!file_exists($folderPCAM)){
                        mkdir($folderPCAM,0777);
                        $error403 = "<html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>";
                        file_put_contents($folderPCAM."/index.html", $error403);
                    }
                    if($ispic){
                        $moveimage = move_uploaded_file($file_tmp,$folderPCAM."//".$newFilename);
                        if(!$moveimage){
                            $err_msg .= "<b>Failed insert file.</b><br>";
                        }else{
                            $data['Photo'] = $newFilename;
                        }
                    }
                }

                $Logs = [];
                //if(!empty($isExist->Logs)){
                $Logs = json_decode($isExist->Logs);
                //var_dump($Logs);

                $Logs->UpdatedBy = $myNIP."/".preg_replace('/\s+/', '-', $myName);
                if(!empty($data['Photo'])){
                    $Logs->Photo = (!empty($data['Photo'])? $data['Photo'] : $Logs->Photo);                    
                }
                if(!empty($data['bankName'])){
                    $myBank = array();
                    for ($b=0; $b < count($data['bankName']); $b++) { 
                        $myBank[] = array("ID"=>$data['bankID'][$b],"NIP"=>$data['NIP'],"bank"=>$data['bankName'][$b],"accountName"=>$data['bankAccName'][$b],"accountNumber"=>$data['bankAccNum'][$b]);
                    }
                    $Logs->MyBank = $myBank;
                }
                if(!empty($data['familyrelation'])){
                    $myFamily = array();
                    for ($f=0; $f < count($data['familyrelation']); $f++) { 
                        $myFamily[] = array("ID"=>$data['familyID'][$f],"NIP"=>$data['NIP'],"name"=>$data['familyname'][$f],"relationID"=>$data['familyrelation'][$f],"gender"=>$data['familygender'][$f],"birthdate"=>$data['familybirthdate'][$f],"placeBirth"=>$data['familyplaceBirth'][$f],"lastEduID"=>$data['familylastEdu'][$f]);
                    }
                    $Logs->MyFamily = $myFamily;
                }                      
                if(!empty($data['eduLevel'])){
                    $myEdu = array();
                    for ($e=0; $e < count($data['eduLevel']); $e++) { 
                        $myEdu[] = array("ID"=>$data['eduID'][$e],"NIP"=>$data['NIP'],"levelEduID"=>$data['eduLevel'][$e],"instituteName"=>$data['eduInstitute'][$e],"location"=>$data['eduCC'][$e],"major"=>$data['eduMajor'][$e],"graduation"=>$data['eduGraduation'][$e],"gpa"=>$data['eduGPA'][$e]);
                    }
                    $Logs->MyEducation = $myEdu;
                }
                if(!empty($data['nonEduInstitute'])){
                    $nonEduInstitute = array();
                    for ($n=0; $n < count($data['nonEduInstitute']); $n++) { 
                        $nonEduInstitute[] = array("ID"=>$data['nonEduID'][$n],"NIP"=>$data['NIP'],"subject"=>$data['nonEduSubject'][$n],"instituteName"=>$data['nonEduInstitute'][$n],"start_event"=>$data['nonEduStart'][$n],"end_event"=>$data['nonEduEnd'][$n],"location"=>$data['nonEduCC'][$n]);
                    }
                    $Logs->MyEducationNonFormal = $nonEduInstitute;
                }
                if(!empty($data['trainingTitle'])){
                    $certificates = array();
                    for ($k=0; $k < count($data['trainingTitle']); $k++) {                             
                        if(!empty($_FILES['certificate']['name'][$k])){                            
                            $ispic = false;
                            $file_name = $_FILES['certificate']['name'][$k];
                            $file_size =$_FILES['certificate']['size'][$k];
                            $file_tmp =$_FILES['certificate']['tmp_name'][$k];
                            $file_type=$_FILES['certificate']['type'][$k];
                            if($file_type == "image/jpeg" || $file_type == "image/png"){
                                $ispic = true;
                            }else {
                                $ispic = false;
                                $err_msg     .= "Extention image '".$file_name."' doesn't allowed.";
                            }
                            if($file_size > 2000000){ //2Mb
                                $ispic = false;
                                $err_msg     .= "Size of image '".$file_name."'s too large from 2Mb.";
                            }else { $ispic = true; }

                            $trainingTitleFilename = preg_replace('/\s+/', "_", $data['trainingTitle'][$k]);
                            $newFilename = $data['NIP']."-TRAINING-".$trainingTitleFilename."-".date('ymd').".jpg";

                            if($_SERVER['SERVER_NAME']=='pcam.podomorouniversity.ac.id'){
                                $pathInPieces = explode('/', $_SERVER['DOCUMENT_ROOT']);
                                $t = count($pathInPieces) - 1;
                                $newPath = "";
                                for ($i=0; $i < $t; $i++) { 
                                    $newPath .= $pathInPieces[$i]."/";
                                }
                                $folderPCAM = $newPath.'pcam/uploads/profile/training';
                            }else{
                                $folderPCAM = $_SERVER['DOCUMENT_ROOT'].'/puis/uploads/profile/training';
                            }

                            if(!file_exists($folderPCAM)){
                                mkdir($folderPCAM,0777);
                                $error403 = "<html><head><title>403 Forbidden</title></head><body><p>Directory access is forbidden.</p></body></html>";
                                file_put_contents($folderPCAM."/index.html", $error403);
                            }
                            if($ispic){
                                $moveimage = move_uploaded_file($file_tmp,$folderPCAM."//".$newFilename);
                                if(!$moveimage){
                                    $err_msg .= "<b>Failed insert file.</b><br>";
                                }else{
                                    $certificateName = $newFilename;
                                }
                            }
                        }else{
                            $certificateName = null;
                        }
                        $certificates[] = array("ID"=>$data['trainingID'][$k],"NIP"=>$data['NIP'],"name"=>$data['trainingTitle'][$k],"organizer"=>$data['trainingorganizer'][$k],"start_event"=>$data['trainingStart'][$k].' '.$data['trainingStartTime'][$k].':00',"end_event"=>$data['trainingEnd'][$k].' '.$data['trainingEndTime'][$k].':00',"location"=>$data['trainingLocation'][$k],"category"=>$data['trainingCategory'][$k],"costCompany"=>$data['trainingCostCompany'][$k],"costEmployee"=>$data['trainingCostEmployee'][$k],"certificate"=>$certificateName);                                
                    }
                    $Logs->MyEducationTraining = $certificates;
                }

                if(!empty($data['comName'])){
                    $comName = array();
                    for ($c=0; $c < count($data['comName']); $c++) { 
                        $comName[] = array("ID"=>$data['comID'][$c],"NIP"=>$data['NIP'],"company"=>$data['comName'][$c],"industryID"=>$data['comIndustry'][$c],"start_join"=>$data['comStartJoin'][$c],"end_join"=>$data['comEndJoin'][$c],"jobTitle"=>$data['comJobTitle'][$c],"reason"=>$data['comReason'][$c]);
                    }
                    $Logs->MyExperience = $comName;
                }

                $example = array('An example','Another example','One Example','Last example');
                $searchword = 'last';
                $matches = array();
                foreach($example as $k=>$v) {
                    if(preg_match("/\b$searchword\b/i", $v)) {
                        $matches[$k] = $v;
                    }
                }

                $execpt = array("bank","edu","family","nonEdu","training","com");
                foreach ($data as $key => $value) {
                    foreach ($execpt as $v) {
                        if(preg_match_all("/$v/", $key)) {
                            unset($data[$key]);
                        }
                    }                    
                }
                foreach ($data as $key => $value) {
                    $Logs->$key = $value;                    
                }
                
                $dataPost = json_encode($Logs);
                $update = $this->General_model->updateData("db_employees.employees",array("isApproved"=>3,"Logs"=>$dataPost,"UpdatedBy"=>$myNIP."/".preg_replace('/\s+/', '-', $myName)),array("NIP"=>$data['NIP']));
                $message = (($update) ? "Successfully":"Failed")." updated.".(!empty($err_msg) ? "Failed upload photo. ".$err_msg : '');
            }else{
                $message = "Employee doesn't founded.";
            }

            if($action != 'profile'){
                $uri = 'profile/'.$action.'/'.$data['NIP'];
            }else{
                $uri = $action.'/'.$data['NIP'];
            }

            $this->session->set_flashdata("message",$message);
            redirect(site_url($uri));            
        }
    }


    public function requestDetail($NIP){
        $data = $this->input->post();
        if($data){
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($data['token'],$key);    
            $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$data_arr['NIP']))->row();                        
            $subdata = array();
            if(!empty($isExist)){
                if(!empty($isExist->Logs)){
                    $Logs = json_decode($isExist->Logs);
                    
                    if(!empty($Logs->ReligionID)){
                        $Logs->Religion = $this->General_model->fetchData("db_employees.religion",array("IDReligion"=>$Logs->ReligionID))->row();                        
                    }
                    if(!empty($Logs->MaritalStatus)){
                        $Logs->MaritalStatus = $this->General_model->fetchData("db_employees.master_marital_status",array("ID"=>$Logs->MaritalStatus))->row();
                    }
                    if(!empty($Logs->CountryID)){
                        $Logs->Country = $this->General_model->fetchData("db_admission.country",array("ctr_code"=>$Logs->CountryID))->row();                        
                    }
                    if(!empty($Logs->ProvinceID)){
                        $Logs->Province = $this->General_model->fetchData("db_admission.province",array("ProvinceID"=>$Logs->ProvinceID))->row();
                        if(!empty($Logs->RegionID)){
                            $Logs->Region = $this->General_model->fetchData("db_admission.region",array("RegionID"=>$Logs->RegionID))->row();
                        }
                        if(!empty($Logs->DistrictID)){
                            $Logs->District = $this->General_model->fetchData("db_admission.district",array("DistrictID"=>$Logs->DistrictID))->row();
                        }
                    }
                    $subdata['detail'] = $Logs;
                    if(!empty($isExist->NoteApproved)){
                        $subdata['Note'] = $isExist->NoteApproved;
                    }
                    $subdata['isApproved'] = $isExist->isApproved;
                }
            }
            $this->load->view("dashboard/profile/request-detail",$subdata);
        }else{show_404();}
    }


    public function submitRequest($NIP){
        $json = array();
        $data = $this->input->post();
        if($data){
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($data['token'],$key);
            $isExist = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$data_arr['NIP']))->row();
            if(!empty($isExist)){
                $update = $this->General_model->updateData("db_employees.employees",array("isApproved"=>1),array("NIP"=>$data_arr['NIP']));
                $message = (($update) ? "Successfully":"Failed")." saved and your request will be forward to HR.";
                if($update){
                    //SEND NOTIFICATION
                    // Send Notif for next approval
                    $getHRStaff = $this->General_model->fetchData("db_employees.employees","PositionMain like '13.%' ")->result();
                    $dataNIP = array();
                    if(!empty($getHRStaff)){
                        foreach ($getHRStaff as $v) {
                            $dataNIP[] = $v->NIP;
                        }
                    }
                    $data = array(
                        'auth' => 's3Cr3T-G4N',
                        'Logging' => array(
                                        'Title' => '<i class="fa fa-question-circle margin-right" style="color:blue;"></i>  Approval Lecturer Profile',
                                        'Description' => 'Request approval Lecturer Profile',
                                        'URLDirect' => 'human-resources/employees',
                                        'CreatedBy' => $isExist->NIP,
                                        'CreatedName' => $isExist->Name,
                                      ),
                        'To' => array(
                                  'NIP' => $dataNIP,
                                ),
                        'Email' => 'No', 
                    );

                    $url = base_url().'rest2/__send_notif_browser';
                    $token = $this->jwt->encode($data,"UAP)(*");
                    $resultNotif = $this->m_master->apiservertoserver($url,$token);
                    //END SEND NOTIFICATION
                }
            }else{$message = "Data employee not founded.";}
            $json = array("message"=>$message);

        }

        echo json_encode($json);
    }


    public function myTeamActivities(){
        $data['statusstd'] = $this->General_model->fetchData("db_employees.employees_status","IDStatus = 1 or IDStatus = 2","IDStatus","asc")->result();
        $data['division'] = $this->General_model->fetchData("db_employees.division",array())->result();
        $data['position'] = $this->General_model->fetchData("db_employees.position",array())->result();
        $data['religion'] = $this->General_model->fetchData("db_employees.religion",array())->result();
        $data['level_education'] = $this->General_model->fetchData("db_employees.level_education",array())->result();
        $this->load->view('dashboard/activities/myactivitiesteam',$data);
    }


    public function fetchActivitiesEmp(){
        $reqdata = $this->input->post();
        if($reqdata){
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($reqdata['token'],$key);
            $param = array();$orderBy=" em.ID DESC ";

            $myNIP = $this->session->userdata('NIP');
            $myName = $this->session->userdata('Name');
            $isEmployee = $this->General_model->fetchData("db_employees.employees",array("NIP"=>$myNIP))->row(); 

            if(!empty($isEmployee)){
                $explodeMain = explode(".", $isEmployee->PositionMain);
                $param[] = array("field"=>"em.PositionMain","data"=>" like '".$explodeMain[0].".%' ","filter"=>"AND",);
            }

            if(!empty($reqdata['search']['value']) ) {
                $search = $reqdata['search']['value'];

                $param[] = array("field"=>"(em.NIP","data"=>" like '%".$search."%' ","filter"=>"AND",);
                $param[] = array("field"=>"em.Name","data"=>" like '%".$search."%' ","filter"=>"OR",);
                $param[] = array("field"=>"em.NIDN","data"=>" like '%".$search."%' )","filter"=>"OR",);
            }
            if(!empty($data_arr['Filter'])){
                $parse = parse_str($data_arr['Filter'],$output);

                //check data emp if lecturers
                if(!empty($output['isLecturer'])){
                    $divLect = '14';
                    $param[] = array("field"=>"(em.PositionMain","data"=>" like'".$divLect.".%' ","filter"=>"AND",);
                    $param[] = array("field"=>"em.PositionOther1","data"=>" like'".$divLect.".%' ","filter"=>"OR",);
                    $param[] = array("field"=>"em.PositionOther2","data"=>" like'".$divLect.".%' ","filter"=>"OR",);
                    $param[] = array("field"=>"em.PositionOther3","data"=>" like'".$divLect.".%' )","filter"=>"OR",);
                    if( !empty($output['position'])){
                        $param[] = array("field"=>"em.PositionMain","data"=>" = '".$divLect.".".$output['position']."' ","filter"=>"AND",);
                    }
                    if(!empty($output['status'])){
                        $sn = 1;
                        $dataArrStatus = array();
                        $param[] = array("field"=>"(","data"=>null,"filter"=>"AND");
                        if(count($output['status']) == 1){
                            $param[] = array("field"=>"em.`StatusLecturerID`","data"=>" ='".$output['status'][0]."' ","filter"=> "" );
                        }else{
                            foreach ($output['status'] as $s) {
                                $param[] = array("field"=>"em.`StatusLecturerID`","data"=>" ='".$s."' ".((($sn < count($output['status'])) ? ' OR ':'')) ,"filter"=> null );
                                $sn++;
                            }
                        }
                        $param[] = array("field"=>")","data"=>null,"filter"=>null);
                    }
                    if(!empty($output['study_program'])){
                        $sn = 1;
                        $dataArrStatus = array();
                        $param[] = array("field"=>"(","data"=>null,"filter"=>"AND");
                        if(count($output['study_program']) == 1){
                            $param[] = array("field"=>"em.ProdiID","data"=>" ='".$output['study_program'][0]."' ","filter"=> "" );
                        }else{
                            foreach ($output['study_program'] as $s) {
                                $param[] = array("field"=>"em.ProdiID","data"=>" ='".$s."' ".((($sn < count($output['study_program'])) ? ' OR ':'')) ,"filter"=> null );
                                $sn++;
                            }
                        }
                        $param[] = array("field"=>")","data"=>null,"filter"=>null);
                    }
                }
                //check data for employee
                else{
                    if(!empty($output['division'])){
                        $param[] = array("field"=>"em.PositionMain","data"=>" like '".$output['division'].".%' ","filter"=>"AND",);
                    }
                    if( !empty($output['division']) && !empty($output['position'])){
                        $param[] = array("field"=>"em.PositionMain","data"=>" = '".$output['division'].".".$output['position']."' ","filter"=>"AND",);
                    }

                    if(!empty($output['status'])){
                        $sn = 1;
                        $dataArrStatus = array();
                        $param[] = array("field"=>"(","data"=>null,"filter"=>"AND");
                        if(count($output['status']) == 1){
                            $param[] = array("field"=>"em.`StatusEmployeeID`","data"=>" ='".$output['status'][0]."' ","filter"=> "" );
                        }else{
                            foreach ($output['status'] as $s) {
                                $param[] = array("field"=>"em.`StatusEmployeeID`","data"=>" ='".$s."' ".((($sn < count($output['status'])) ? ' OR ':'')) ,"filter"=> null );
                                $sn++;
                            }
                        }
                        $param[] = array("field"=>")","data"=>null,"filter"=>null);
                    }
                }

                if(!empty($output['staff'])){
                    $param[] = array("field"=>"(em.NIP","data"=>" like '%".$output['staff']."%' ","filter"=>"AND",);
                    $param[] = array("field"=>"ps.NameEng","data"=>" like '%".$output['staff']."%' ","filter"=>"OR",);
                    $param[] = array("field"=>"em.Name","data"=>" like '%".$output['staff']."%' )","filter"=>"OR",);
                }
                if(!empty($output['religion'])){
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field"=>"(","data"=>null,"filter"=>"AND");
                    if(count($output['religion']) == 1){
                        $param[] = array("field"=>"em.ReligionID","data"=>" ='".$output['religion'][0]."' ","filter"=> "" );
                    }else{
                        foreach ($output['religion'] as $s) {
                            $param[] = array("field"=>"em.ReligionID","data"=>" ='".$s."' ".((($sn < count($output['religion'])) ? ' OR ':'')) ,"filter"=> null );
                            $sn++;
                        }
                    }
                    $param[] = array("field"=>")","data"=>null,"filter"=>null);
                }
                if(!empty($output['gender'])){
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field"=>"(","data"=>null,"filter"=>"AND");
                    if(count($output['gender']) == 1){
                        $param[] = array("field"=>"em.Gender","data"=>" ='".$output['gender'][0]."' ","filter"=> "" );
                    }else{
                        foreach ($output['gender'] as $s) {
                            $param[] = array("field"=>"em.Gender","data"=>" ='".$s."' ".((($sn < count($output['gender'])) ? ' OR ':'')) ,"filter"=> null );
                            $sn++;
                        }
                    }
                    $param[] = array("field"=>")","data"=>null,"filter"=>null);
                }
                if(!empty($output['level_education'])){
                    $sn = 1;
                    $dataArrStatus = array();
                    $param[] = array("field"=>"(","data"=>null,"filter"=>"AND");
                    if(count($output['level_education']) == 1){
                        $param[] = array("field"=>"em.LevelEducationID","data"=>" ='".$output['level_education'][0]."' ","filter"=> "" );
                    }else{
                        foreach ($output['level_education'] as $s) {
                            $param[] = array("field"=>"em.LevelEducationID","data"=>" ='".$s."' ".((($sn < count($output['level_education'])) ? ' OR ':'')) ,"filter"=> null );
                            $sn++;
                        }
                    }
                    $param[] = array("field"=>")","data"=>null,"filter"=>null);
                }

                if(!empty($output['attendance_start'])){
                    $param[] = array("field"=>"lem.AccessedOn","operate"=>" = ","data"=>"'".date("Y-m-d",strtotime($output['attendance_start']))."' ","filter"=>"AND",);
                }else{
                    $param[] = array("field"=>"lem.AccessedOn","operate"=>"=","data"=>"'".date("Y-m-d")."' ","filter"=>"AND",);
                }

                if(!empty($output['sorted'])){
                    $orderBy = $output['sorted'];
                }
            }
            $totalData = $this->m_hr->fetchEmployee(true,$param)->row();
            $TotalData = (!empty($totalData) ? $totalData->Total : 0);
            if(!empty($reqdata['start']) && !empty($reqdata['length'])){
                $result = $this->m_hr->fetchEmployee(false,$param,$reqdata['start'],$reqdata['length'],$orderBy)->result();
            }else{
                $result = $this->m_hr->fetchEmployee(false,$param)->result();
            }

            $json_data = array(
                "draw"            => intval( (!empty($reqdata['draw']) ? $reqdata['draw'] : null) ),
                "recordsTotal"    => intval($TotalData),
                "recordsFiltered" => intval($TotalData),
                "data"            => (!empty($result) ? $result : 0)
            );

        }else{$json_data=null;}
        $response = $json_data;
        echo json_encode($response);
    }


    public function detailActivitiesEmp(){
        $data = $this->input->post();
        if($data){
            $key = "UAP)(*";
            $data_arr = (array) $this->jwt->decode($data['token'],$key);
            $param[] = array("field"=>"em.NIP","data"=>" = ".$data_arr['NIP'],"filter"=>"AND",);
            $isExist = $this->m_hr->fetchEmployee(false,$param)->row();
            if(!empty($isExist)){
                $data['attendance'] = $this->General_model->fetchData("db_employees.log_employees","NIP = ".$data_arr['NIP']." and DATE(AccessedOn) = DATE('".$data_arr['DATE']."')","AccessedOn","asc")->result();
                $data['employee'] = $isExist;
                $data['TotalActivity'] = $this->General_model->fetchData("db_employees.log_employees","NIP = ".$data_arr['NIP']." and DATE(AccessedOn) = DATE('".$data_arr['DATE']."')","AccessedOn","asc",null,"AccessedOn")->result();
                $this->load->view('dashboard/activities/detailActivities',$data);
            }else{echo "<h1>Employee not founded</h1>";}
        }else{show_404();}
    }


    /*END ADDED BY FEBRI @ MARCH 2020*/

}
