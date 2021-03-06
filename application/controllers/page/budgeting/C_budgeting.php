<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_budgeting extends Budgeting_Controler {
    public $Msg = array(
            'Duplicate' => 'The data duplicate, Please check',
            'NotAction' => 'The data has been used for transaction, Cannot be action',
            'Error' => 'Error connection',
    );

    public function __construct()
    {
        parent::__construct();
    }

    public function index()
    {
        // --- edited -- //
        $this->data['GetPeriod'] = $this->m_budgeting->GetPeriod();
        // if (file_exists(APPPATH.'views/page/'.$data['department'].'/dashboard.php')) {
        if (file_exists(APPPATH.'views/page/budgeting/'.$this->data['department'].'/dashboard.php')) {
            $content = $this->load->view('page/budgeting/'.$this->data['department'].'/dashboard',$this->data,true);
        }
        else
        {
            $content = $this->load->view('page/budgeting/dashboard',$this->data,true);
        }
        
        $this->temp($content);
    }

    public function configfinance_budgeting($Request = null)
    {
        $this->authFin();
        $arr_menuConfig = array('CodePrefix',
                                'TimePeriod',
                                'MasterPost',
                                'SetPostDepartement',
                                'MasterUserRole',
                                'UserRole',
                                null
                            );
        if (in_array($Request, $arr_menuConfig))
          {
            $this->data['request'] = $Request;
            $content = $this->load->view('page/budgeting/'.$this->data['department'].'/configfinance',$this->data,true);
            $this->temp($content);
          }
        else
          {
            show_404($log_error = TRUE);
          }
    }

    public function pageLoadTimePeriod()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/pageLoadTimePeriod',$this->data,true);
        echo json_encode($arr_result);
    }

    public function modal_pageLoadTimePeriod()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        $this->data['id'] = $input['CDID'];
        if ($input['Action'] == 'edit') {
            $sql = 'select * from db_budgeting.cfg_dateperiod where Year = ? and Active = 1';
            $query=$this->db->query($sql, array($this->data['id']))->result_array();
            $this->data['getData'] = $query;
        }
        echo $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/modalform_timeperiod',$this->data,true);
    }

    public function modal_pageLoadTimePeriod_save()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $Msg = '';
        switch ($input['Action']) {
            case 'add':
                // $dateStart = cal_days_in_month(CAL_GREGORIAN, $input['MonthStart'], $input['Year']); 
                $dateStart = '01'; 
                $dateEnd= cal_days_in_month(CAL_GREGORIAN, $input['MonthEnd'], $input['Year']);
                $Year = $input['Year'];
                $StartPeriod = $Year.'-'.$input['MonthStart'].'-'.$dateStart;
                $EndPeriod = ($Year + 1).'-'.$input['MonthEnd'].'-'.$dateEnd;
                $sql = 'select * from db_budgeting.cfg_dateperiod where Year = ? and Active = 1';
                $query=$this->db->query($sql, array($Year))->result_array();
                if (count($query) > 0) {
                    $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                    $dataSave = array(
                        'Year' => $Year,
                        'StartPeriod' => $StartPeriod,
                        'EndPeriod' => $EndPeriod,
                        'Activated' => 1
                    );
                    $this->db->insert('db_budgeting.cfg_dateperiod', $dataSave);

                    $sql = 'update db_budgeting.cfg_dateperiod set Activated = 0 where Year != ? ';
                    $query=$this->db->query($sql, array($Year));
                }

                break;
            case 'edit':
                // $dateStart = cal_days_in_month(CAL_GREGORIAN, $input['MonthStart'], $input['Year']); 
                $dateStart = '01';
                $dateEnd= cal_days_in_month(CAL_GREGORIAN, $input['MonthEnd'], $input['Year']);
                $Year = $input['Year'];
                $StartPeriod = $Year.'-'.$input['MonthStart'].'-'.$dateStart;
                $EndPeriod = ($Year + 1).'-'.$input['MonthEnd'].'-'.$dateEnd;
                $sql = 'select * from db_budgeting.cfg_dateperiod where Year = ? and Active = 1';
                $query=$this->db->query($sql, array($Year))->result_array();

                $Status = $query[0]['Status']; // check can be delete
                if ($Status == 1) {
                    try {
                        $dataSave = array(
                            'Year' => $Year,
                            'StartPeriod' => $StartPeriod,
                            'EndPeriod' => $EndPeriod
                        );
                        $this->db->where('Year', $Year);
                        $this->db->where('Active', 1);
                        $this->db->update('db_budgeting.cfg_dateperiod', $dataSave);
                    } catch (Exception $e) {
                         $Msg = $this->Msg['Duplicate'];
                    }
                }
                else
                {
                    $Msg = $this->Msg['NotAction'];
                }
                break;
            case 'delete':
                $Year = $input['CDID'];
                $sql = 'select * from db_budgeting.cfg_dateperiod where Year = ? and Active = 1';
                $query=$this->db->query($sql, array($Year))->result_array();
                $Status = $query[0]['Status']; // check can be delete
                   if ($Status == 1) {
                       // $dataSave = array(
                       //     'Year' => $Year,
                       //     'StartPeriod' => $query[0]['StartPeriod'],
                       //     'EndPeriod' => $query[0]['EndPeriod'],
                       //     'Active' => 0
                       // );
                       // $this->db->where('Year', $Year);
                       // $this->db->where('Active', 1);
                       // $this->db->update('db_budgeting.cfg_dateperiod', $dataSave);
                       $this->db->where(array('Year' => $Year));
                       $this->db->delete('db_budgeting.cfg_dateperiod');
                   }
                   else
                   {
                       $Msg = $this->Msg['NotAction'];
                   }
                break;
            case 'activated':
                $Year = $input['CDID'];
                $sql = 'select * from db_budgeting.cfg_dateperiod where Year = ? and Active = 1';
                $query=$this->db->query($sql, array($Year))->result_array();
                $Status = $query[0]['Status']; // check can be delete
                   if ($Status == 1) {
                        // check activated
                        $Activated = ($query[0]['Activated'] == 0) ? 1 : 0;

                       $dataSave = array(
                           'Activated' => $Activated,
                       );
                       $this->db->where('Year', $Year);
                       $this->db->where('Active', 1);
                       $this->db->update('db_budgeting.cfg_dateperiod', $dataSave);

                        $sql = 'update db_budgeting.cfg_dateperiod set Activated = 0 where Year != ? ';
                        $query=$this->db->query($sql, array($Year));
                   }
                   else
                   {
                       $Msg = $this->Msg['NotAction'];
                   }
                break;    
            default:
                # code...
                break;
        }

        echo json_encode($Msg);
    }

    public function LoadTable_db_budgeting_cari($table,$field,$fieldValue,$Active = null)
    {
        $this->auth_ajax();
        $query = array();
        if ($Active == null) {
            $sql = 'select * from db_budgeting.'.$table.' where '.$field.' = ?';
            $query=$this->db->query($sql, array($fieldValue))->result_array();
        }
        else
        {
            $sql = 'select * from db_budgeting.'.$table.' where '.$field.' = ? and Active = ?';
            $query=$this->db->query($sql, array($fieldValue,$Active))->result_array();
        }

        echo json_encode($query);
    }

    public function LoadTable_db_budgeting_all($table,$Active = null)
    {
        $this->auth_ajax();
        $query = array();
        if ($Active == null) {
            $sql = 'select * from db_budgeting.'.$table;
            $query=$this->db->query($sql, array())->result_array();
        }
        else
        {
            $sql = 'select * from db_budgeting.'.$table.' where Active = ?';
            $query=$this->db->query($sql, array($Active))->result_array();
        }

        echo json_encode($query);
    }

    public function loadCodePrefix()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $this->data['loadData'] = $this->m_master->showData_array('db_budgeting.cfg_codeprefix');
        $this->data['loadData'] = json_encode($this->data['loadData']);
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/pageloadCodePrefix',$this->data,true);
        echo json_encode($arr_result);
    }

    public function pageloadMasterPost()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/pageloadMasterPost',$this->data,true);
        echo json_encode($arr_result);
    }

    public function save_codeprefix()
    {
        $this->auth_ajax();
        $input =  $this->getInputToken();
        if(array_key_exists("CodePost",$input))
        {
            $dataSave = array(
                'CodePost' => $input['CodePost'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodePost",$input))
        {
            $dataSave = array(
                'LengthCodePost' => $input['LengthCodePost'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("CodePostRealisasi",$input))
        {
            $dataSave = array(
                'CodePostRealisasi' => $input['CodePostRealisasi'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodePostRealisasi",$input))
        {
            $dataSave = array(
                'LengthCodePostRealisasi' => $input['LengthCodePostRealisasi'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("CodePostBudget",$input))
        {
            $dataSave = array(
                'CodePostBudget' => $input['CodePostBudget'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("YearCodePostBudget",$input))
        {
            $dataSave = array(
                'YearCodePostBudget' => $input['YearCodePostBudget'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodePostBudget",$input))
        {
            $dataSave = array(
                'LengthCodePostBudget' => $input['LengthCodePostBudget'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("CodeCatalog",$input))
        {
            $dataSave = array(
                'CodeCatalog' => $input['CodeCatalog'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodeCatalog",$input))
        {
            $dataSave = array(
                'LengthCodeCatalog' => $input['LengthCodeCatalog'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("CodeSupplier",$input))
        {
            $dataSave = array(
                'CodeSupplier' => $input['CodeSupplier'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }

        if(array_key_exists("LengthCodeSupplier",$input))
        {
            $dataSave = array(
                'LengthCodeSupplier' => $input['LengthCodeSupplier'],
            );
            $this->db->update('db_budgeting.cfg_codeprefix', $dataSave);
        }
    }

    public function get_cfg_postrealisasi()
    {
        $this->auth_ajax();
        $getData = $this->m_budgeting->getData_cfg_postrealisasi(1);
        echo json_encode($getData);
    }

    public function get_cfg_head_account()
    {
        $this->auth_ajax();
        $getData = $this->m_budgeting->get_cfg_head_account(1);
        echo json_encode($getData);
    }

    public function modal_pageloadMasterPost()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        // print_r($this->data);
        $this->data['id'] = $input['CDID'];
        if ($input['Action'] == 'edit') {
            $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
            $query=$this->db->query($sql, array($this->data['id']))->result_array();
            $this->data['getData'] = $query;
        }
        echo $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/modalform_masterpost',$this->data,true);

    }

    public function modal_pageloadMasterPost_save()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $Msg = '';
        switch ($input['Action']) {
            case 'add':
                $NeedPrefix = $input['NeedPrefix'];
                $CodePost = $input['CodePost'];
                if ($NeedPrefix == 1) { // get the code
                    $CfgCode = $this->m_master->showData_array('db_budgeting.cfg_codeprefix');
                    $CodePostPrefix = $CfgCode[0]['CodePost'];
                    $LengthCode = $CfgCode[0]['LengthCodePost'];
                    $tbl = 'db_budgeting.cfg_post';
                    $fieldCode = 'CodePost';
                    $CodePost = $this->m_budgeting->getTheCode($tbl,$fieldCode,$CodePostPrefix,$LengthCode);
                }


                $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePost))->result_array();
                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                   $dataSave = array(
                       'CodePost' => $CodePost,
                       'PostName' => trim(ucwords($input['PostName'])),
                       'CreatedBy' => $this->session->userdata('NIP'),
                       'CreatedAt' => date('Y-m-d'),
                   );
                   $this->db->insert('db_budgeting.cfg_post', $dataSave);
                }
                break;
            case 'edit':
                $CodePost = $input['CodePost'];
                $query = array();
                if ($CodePost != $input['CDID']) {
                    $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
                    $query=$this->db->query($sql, array($CodePost))->result_array();
                }

                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                    $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
                    $query=$this->db->query($sql, array($input['CDID']))->result_array();
                    $Status = $query[0]['Status'];
                    if ($Status == 1) {
                        try {
                           $dataSave = array(
                               'CodePost' => $CodePost,
                               'PostName' => trim(ucwords($input['PostName'])),
                           );
                           $this->db->where('CodePost', $input['CDID']);
                           $this->db->where('Active', 1);
                           $this->db->update('db_budgeting.cfg_post', $dataSave);
                        } catch (Exception $e) {
                             $Msg = $this->Msg['Duplicate'];
                        }   
                    }
                    else
                    {
                        // check data already exist in cfg_head_account,
                        $G = $this->m_master->caribasedprimary('db_budgeting.cfg_head_account','CodePost',$CodePost);
                        if (count($G) > 0) {
                            $Msg = $this->Msg['NotAction'];
                        }
                        else
                        {
                            $dataSave = array(
                                'CodePost' => $CodePost,
                                'PostName' => trim(ucwords($input['PostName'])),
                            );
                            $this->db->where('CodePost', $input['CDID']);
                            $this->db->where('Active', 1);
                            $this->db->update('db_budgeting.cfg_post', $dataSave);
                        }
                    }
                }
                break;
            case 'delete':
                $CodePost = $input['CDID'];
                $sql = 'select * from db_budgeting.cfg_post where CodePost = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePost))->result_array();
                $Status = $query[0]['Status']; // check can be delete
                   if ($Status == 1) {
                       // $dataSave = array(
                       //     'Active' => 0
                       // );
                       // $this->db->where('CodePost', $CodePost);
                       // $this->db->where('Active', 1);
                       // $this->db->update('db_budgeting.cfg_post', $dataSave);
                        $this ->db-> where('CodePost', $CodePost);
                        $this ->db-> delete('db_budgeting.cfg_post');
                   }
                   else
                   {
                       // check data already exist in cfg_head_account,
                       $G = $this->m_master->caribasedprimary('db_budgeting.cfg_head_account','CodePost',$CodePost);
                       if (count($G) > 0) {
                           $Msg = $this->Msg['NotAction'];
                       }
                       else
                       {
                        // $dataSave = array(
                        //     'Active' => 0
                        // );
                        // $this->db->where('CodePost', $CodePost);
                        // $this->db->where('Active', 1);
                        // $this->db->update('db_budgeting.cfg_post', $dataSave);
                        $this ->db-> where('CodePost', $CodePost);
                        $this ->db-> delete('db_budgeting.cfg_post');
                       }
                       
                   }
                break;
            default:
                # code...
                break;
        }

        echo json_encode($Msg);
    }

    public function modal_postrealisasi()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        $this->data['id'] = $input['CDID'];
        if (array_key_exists('Departement', $input)) {
            $this->data['Departement'] = $input['Departement'];
        }
        if ($input['Action'] == 'edit') {
            $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
            $query=$this->db->query($sql, array($this->data['id']))->result_array();
            $this->data['getData'] = $query;
        }
        echo $this->load->view('page/budgeting/'.'finance'.'/configuration/modal_postrealisasi',$this->data,true);
    }

    public function save_postrealisasi()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $Msg = '';

        switch ($input['Action']) {
            case 'add':
                $NeedPrefix = $input['NeedPrefix'];
                $CodePostRealisasi = $input['CodePostRealisasi'];
                if ($NeedPrefix == 1) { // get the code
                    // Get Division from Head Account
                    $G = $this->m_master->caribasedprimary('db_budgeting.cfg_head_account','CodeHeadAccount',$input['HeadAccount']);
                    $Departement = $G[0]['Departement'];
                    $CfgCode = $this->m_master->showData_array('db_budgeting.cfg_codeprefix');
                    $CodePostPrefix = $CfgCode[0]['CodePostRealisasi'];
                    $LengthCode = $CfgCode[0]['LengthCodePostRealisasi'];
                    $tbl = 'db_budgeting.cfg_postrealisasi';
                    $fieldCode = 'CodePostRealisasi';
                    $CodePostRealisasi = $this->m_budgeting->getTheCodeByDiv($tbl,$fieldCode,$CodePostPrefix,$LengthCode,$Departement);
                }


                $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePostRealisasi))->result_array();
                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                   $dataSave = array(
                       'CodePostRealisasi' => $CodePostRealisasi,
                       'CodeHeadAccount' => $input['HeadAccount'],
                       'RealisasiPostName' => trim(ucwords($input['RealisasiPostName'])),
                       'UnitDiv' => $input['UnitDiv'],
                       'Desc' => $input['Desc'],
                       'CreatedBy' => $this->session->userdata('NIP'),
                       'CreatedAt' => date('Y-m-d'),
                   );
                   $this->db->insert('db_budgeting.cfg_postrealisasi', $dataSave);

                   $tbl = 'db_budgeting.cfg_head_account';
                   $fieldCode = 'CodeHeadAccount';
                   $ValueCode = $input['HeadAccount'];
                   $this->m_budgeting->makeCanBeDelete($tbl,$fieldCode,$ValueCode);
                }
                break;
            case 'edit':
                $CodePostRealisasi = $input['CodePostRealisasi'];
                $query = array();
                if ($CodePostRealisasi != $input['CDID']) {
                    $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
                    $query=$this->db->query($sql, array($CodePostRealisasi))->result_array();
                }

                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                    $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
                    $query=$this->db->query($sql, array($input['CDID']))->result_array();
                    $Status = $query[0]['Status'];
                    if ($Status == 1) {
                        try {
                           $dataSave = array(
                               'CodePostRealisasi' => $CodePostRealisasi,
                               'RealisasiPostName' => trim(ucwords($input['RealisasiPostName'])),
                               'CodeHeadAccount' => $input['HeadAccount'],
                               'UnitDiv' => $input['UnitDiv'],
                               'Desc' => $input['Desc'],
                           );
                           $this->db->where('CodePostRealisasi', $input['CDID']);
                           $this->db->where('Active', 1);
                           $this->db->update('db_budgeting.cfg_postrealisasi', $dataSave);
                        } catch (Exception $e) {
                             $Msg = $this->Msg['Duplicate'];
                        }   
                    }
                    else
                    {
                        // cek data exist di  creator_budget
                           $G = $this->m_master->caribasedprimary('db_budgeting.creator_budget','CodePostRealisasi',$CodePostRealisasi);
                           if (count($G) > 0) {
                               $Msg = $this->Msg['NotAction'];
                           }
                           else
                           {
                             $dataSave = array(
                                 'CodePostRealisasi' => $CodePostRealisasi,
                                 'RealisasiPostName' => trim(ucwords($input['RealisasiPostName'])),
                                 'CodeHeadAccount' => $input['HeadAccount'],
                                 'UnitDiv' => $input['UnitDiv'],
                             );
                             $this->db->where('CodePostRealisasi', $input['CDID']);
                             $this->db->where('Active', 1);
                             $this->db->update('db_budgeting.cfg_postrealisasi', $dataSave);
                           }
                    }
                }
                break;
            case 'delete':
                $CodePostRealisasi = $input['CDID'];
                $sql = 'select * from db_budgeting.cfg_postrealisasi where CodePostRealisasi = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePostRealisasi))->result_array();
                $Status = $query[0]['Status']; // check can be delete
                   if ($Status == 1) {
                       // $dataSave = array(
                       //     'Active' => 0
                       // );
                       // $this->db->where('CodePostRealisasi', $CodePostRealisasi);
                       // $this->db->where('Active', 1);
                       // $this->db->update('db_budgeting.cfg_postrealisasi', $dataSave);
                        $this ->db-> where('CodePostRealisasi', $CodePostRealisasi);
                        $this ->db-> delete('db_budgeting.cfg_postrealisasi');

                        // delete data di creator_budget
                        $this ->db-> where('CodePostRealisasi', $CodePostRealisasi);
                        $this ->db-> delete('db_budgeting.creator_budget');
                   }
                   else
                   {
                       // cek data exist di  creator_budget
                          $G = $this->m_master->caribasedprimary('db_budgeting.creator_budget','CodePostRealisasi',$CodePostRealisasi);
                          if (count($G) > 0) {
                              $Msg = $this->Msg['NotAction'];
                          }
                          else
                          {
                            // $dataSave = array(
                            //     'Active' => 0
                            // );
                            // $this->db->where('CodePostRealisasi', $CodePostRealisasi);
                            // $this->db->where('Active', 1);
                            // $this->db->update('db_budgeting.cfg_postrealisasi', $dataSave);
                            $this ->db-> where('CodePostRealisasi', $CodePostRealisasi);
                            $this ->db-> delete('db_budgeting.cfg_postrealisasi');

                            // delete data di creator_budget
                            $this ->db-> where('CodePostRealisasi', $CodePostRealisasi);
                            $this ->db-> delete('db_budgeting.creator_budget');
                          }

                       
                   }
                break;
            default:
                # code...
                break;
        }

        echo json_encode($Msg);

    }

    public function modal_headaccount()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        $this->data['id'] = $input['CDID'];
        if ($input['Action'] == 'edit') {
            $sql = 'select * from db_budgeting.cfg_head_account where CodeHeadAccount = ? and Active = 1';
            $query=$this->db->query($sql, array($this->data['id']))->result_array();
            $this->data['getData'] = $query;
        }
        echo $this->load->view('page/budgeting/'.'finance'.'/configuration/modal_headaccount',$this->data,true);
    }

    public function save_headaccount()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $Msg = '';

        switch ($input['Action']) {
            case 'add':
                $NeedPrefix = $input['NeedPrefix'];
                $CodeHeadAccount = $input['CodeHeadAccount'];
                if ($NeedPrefix == 1) { // get the code
                    $CfgCode = $this->m_master->showData_array('db_budgeting.cfg_codeprefix');
                    $CodePostPrefix = $CfgCode[0]['HeadAccount'];
                    $LengthCode = $CfgCode[0]['LengthHeadAccount'];
                    $tbl = 'db_budgeting.cfg_head_account';
                    $fieldCode = 'CodeHeadAccount';
                    $CodeHeadAccount = $this->m_budgeting->getTheCodeByDiv($tbl,$fieldCode,$CodePostPrefix,$LengthCode,$input['Departement']);
                }

                $sql = 'select * from db_budgeting.cfg_head_account where CodeHeadAccount = ? and Active = 1';
                $query=$this->db->query($sql, array($CodeHeadAccount))->result_array();
                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                   $dataSave = array(
                       'CodeHeadAccount' => $CodeHeadAccount,
                       'CodePost' => $input['PostItem'],
                       'Name' => trim(ucwords($input['HeadAccountName'])),
                       'Departement' => $input['Departement'],
                       'CreatedBy' => $this->session->userdata('NIP'),
                       'CreatedAt' => date('Y-m-d'),
                   );
                   $this->db->insert('db_budgeting.cfg_head_account', $dataSave);

                   $tbl = 'db_budgeting.cfg_post';
                   $fieldCode = 'CodePost';
                   $ValueCode = $input['PostItem'];
                   $this->m_budgeting->makeCanBeDelete($tbl,$fieldCode,$ValueCode);

                   // insert data to cfg_set_post with year activated now and fill budget is zero
                   $YearActivated = $this->m_master->caribasedprimary('db_budgeting.cfg_dateperiod','Activated',1);
                   $Year = $YearActivated[0]['Year'];
                   $sql = 'select count(*) as total from db_budgeting.cfg_set_post where Year = ? and CodeHeadAccount = ? ';
                   $query=$this->db->query($sql, array($Year,$CodeHeadAccount))->result_array();
                   if ($query[0]['total'] == 0) {
                        // get the code 
                        $tbl = 'db_budgeting.cfg_set_post';
                        $fieldCode = 'CodePostBudget';
                        $CfgCode = $this->m_master->showData_array('db_budgeting.cfg_codeprefix');
                        $CodePostPrefix = $CfgCode[0]['CodePostBudget'];
                        $LengthCode = $CfgCode[0]['LengthCodePostBudget'];
                        $CodePostBudget = $this->m_budgeting->getTheCode($tbl,$fieldCode,$CodePostPrefix,$LengthCode,$Year);

                       $dataSave = array(
                           'CodePostBudget' => $CodePostBudget,
                           'CodeHeadAccount' => $CodeHeadAccount,
                           'Year' => $Year,
                           'Budget' => 0,
                           'CreatedBy' => $this->session->userdata('NIP'),
                           'CreatedAt' => date('Y-m-d'),
                       );
                       $this->db->insert('db_budgeting.cfg_set_post', $dataSave);

                       $dataSave = array(
                           'CodePostBudget' => $CodePostBudget,
                           'Time' => date('Y-m-d H:i:s'),
                           'ActionBy' => $this->session->userdata('NIP'),
                           'Detail' => json_encode(array('action' => 'Created')),
                       );
                       $this->db->insert('db_budgeting.log_cfg_set_post', $dataSave);
                   }
                   
                }
                break;
            case 'edit':
                $CodeHeadAccount = $input['CodeHeadAccount'];
                $query = array();
                if ($CodeHeadAccount != $input['CDID']) {
                    $sql = 'select * from db_budgeting.cfg_head_account where CodeHeadAccount = ? and Active = 1';
                    $query=$this->db->query($sql, array($CodeHeadAccount))->result_array();
                }

                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                    $sql = 'select * from db_budgeting.cfg_head_account where CodeHeadAccount = ? and Active = 1';
                    $query=$this->db->query($sql, array($input['CDID']))->result_array();
                    $Status = $query[0]['Status'];
                    if ($Status == 1) {
                        try {
                           $dataSave = array(
                               'CodeHeadAccount' => $CodeHeadAccount,
                               'Name' => trim(ucwords($input['HeadAccountName'])),
                               'CodePost' => $input['PostItem'],
                               'Departement' => $input['Departement'],
                           );
                           $this->db->where('CodeHeadAccount', $input['CDID']);
                           $this->db->where('Active', 1);
                           $this->db->update('db_budgeting.cfg_head_account', $dataSave);
                        } catch (Exception $e) {
                             $Msg = $this->Msg['Duplicate'];
                        }   
                    }
                    else
                    {
                        // check data in cfg_set_post,cfg_postrealisasi,
                        $b = true;
                        $arr_tbl = array('db_budgeting.cfg_set_post','db_budgeting.cfg_postrealisasi');
                        for ($i=0; $i < count($arr_tbl); $i++) { 
                            $sql = 'select * from '.$arr_tbl[$i].' where CodeHeadAccount = ? and Active = 1';
                            $query=$this->db->query($sql, array($CodeHeadAccount))->result_array(); 
                            $G = $query;
                            if (count($G) > 0) {
                                $Msg = $this->Msg['NotAction'];
                                $b = false;
                                break;
                            }
                        }
                        
                        if ($b) {
                           $dataSave = array(
                               'CodeHeadAccount' => $CodeHeadAccount,
                               'Name' => trim(ucwords($input['HeadAccountName'])),
                               'CodePost' => $input['PostItem'],
                               'Departement' => $input['Departement'],
                           );
                           $this->db->where('CodeHeadAccount', $input['CDID']);
                           $this->db->where('Active', 1);
                           $this->db->update('db_budgeting.cfg_head_account', $dataSave); 
                        }

                    }
                }
                break;
            case 'delete':
                $CodeHeadAccount = $input['CDID'];
                $sql = 'select * from db_budgeting.cfg_head_account where CodeHeadAccount = ? and Active = 1';
                $query=$this->db->query($sql, array($CodeHeadAccount))->result_array();
                $Status = $query[0]['Status']; // check can be delete
                   if ($Status == 1) {
                       // $dataSave = array(
                       //     'Active' => 0
                       // );
                       // $this->db->where('CodeHeadAccount', $CodeHeadAccount);
                       // $this->db->where('Active', 1);
                       // $this->db->update('db_budgeting.cfg_head_account', $dataSave);
                        $this ->db-> where('CodeHeadAccount', $CodeHeadAccount);
                        $this ->db-> delete('db_budgeting.cfg_head_account');
                   }
                   else
                   {
                       // check data in cfg_set_post,cfg_postrealisasi,
                       $b = true;
                       $arr_tbl = array('db_budgeting.cfg_set_post','db_budgeting.cfg_postrealisasi');
                       for ($i=0; $i < count($arr_tbl); $i++) {
                            $sql = 'select * from '.$arr_tbl[$i].' where CodeHeadAccount = ? and Active = 1';
                            $query=$this->db->query($sql, array($CodeHeadAccount))->result_array(); 
                           $G = $query;
                           if (count($G) > 0) {
                               $Msg = $this->Msg['NotAction'];
                               $b = false;
                               break;
                           }
                       }

                       if ($b) {
                          // $dataSave = array(
                          //     'Active' => 0
                          // );
                          // $this->db->where('CodeHeadAccount', $CodeHeadAccount);
                          // $this->db->where('Active', 1);
                          // $this->db->update('db_budgeting.cfg_head_account', $dataSave);

                          $this ->db-> where('CodeHeadAccount', $CodeHeadAccount);
                          $this ->db-> delete('db_budgeting.cfg_head_account'); 
                       }
                       
                   }
                break;
            default:
                # code...
                break;
        }

        echo json_encode($Msg);

    }

    public function LoadSetPostDepartement()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/pageSetPostDepartement',$this->data,true);
        echo json_encode($arr_result);
    }

    public function LoadInputsetPostDepartement()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/setpostdepartement/pageInputsetPostDepartement',$this->data,true);
        echo json_encode($arr_result);
    }

    public function ExportPostDepartement()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/setpostdepartement/pageExportPostDepartement',$this->data,true);
        echo json_encode($arr_result);
    }

    public function getPostDepartement()
    {
         $this->auth_ajax();
         $input = $this->getInputToken();
         $getData = $this->m_budgeting->getPostDepartement($input['Year'],$input['Departement']);
         echo json_encode($getData);
    }

    public function getDomPostDepartement()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $getDataForDom = $this->m_budgeting->getPostDepartementForDom($input['Year'],$input['Departement']);
        echo json_encode($getDataForDom);
    }

    public function save_setpostdepartement()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $Msg = '';

        switch ($input['Action']) {
            case 'add':
            // check data telah diapprove atau belum // data pass : Year & Departement
                $Q_get = $this->m_master->caribasedprimary('db_budgeting.cfg_head_account','CodeHeadAccount',$input['CodeHeadAccount']);
                $Departement = $Q_get[0]['Departement'];
                $Q_get = $this->m_budgeting->get_creator_budget_approval($input['Year'],$Departement);
                if (count($Q_get) > 0) {
                    $Msg = $this->Msg['NotAction'];
                    break;
                }

                $tbl = 'db_budgeting.cfg_set_post';
                $fieldCode = 'CodePostBudget';
                $CfgCode = $this->m_master->showData_array('db_budgeting.cfg_codeprefix');
                $CodePostPrefix = $CfgCode[0]['CodePostBudget'];
                $LengthCode = $CfgCode[0]['LengthCodePostBudget'];
                $CodePostBudget = $this->m_budgeting->getTheCode($tbl,$fieldCode,$CodePostPrefix,$LengthCode,$input['Year']);

                $sql = 'select * from db_budgeting.cfg_set_post where CodeHeadAccount = ? and Active = 1 and Year = ?';
                $query=$this->db->query($sql, array($input['CodeHeadAccount'],$input['Year']))->result_array();
                if (count($query) > 0) {
                   $Msg = $this->Msg['Duplicate'];
                }
                else
                {
                   $dataSave = array(
                       'CodePostBudget' => $CodePostBudget,
                       'CodeHeadAccount' => $input['CodeHeadAccount'],
                       'Year' => $input['Year'],
                       'Budget' => $input['Budget'],
                       'CreatedBy' => $this->session->userdata('NIP'),
                       'CreatedAt' => date('Y-m-d'),
                   );
                   $this->db->insert('db_budgeting.cfg_set_post', $dataSave);

                   $dataSave = array(
                       'CodePostBudget' => $CodePostBudget,
                       'Time' => date('Y-m-d H:i:s'),
                       'ActionBy' => $this->session->userdata('NIP'),
                       'Detail' => json_encode(array('action' => 'Created')),
                   );
                   $this->db->insert('db_budgeting.log_cfg_set_post', $dataSave);

                   // $tbl = 'db_budgeting.cfg_postrealisasi';
                   // $fieldCode = 'CodePostRealisasi';
                   // $ValueCode = $input['CodeSubPost'];
                   // $this->m_budgeting->makeCanBeDelete($tbl,$fieldCode,$ValueCode);
                }
                break;
            case 'edit':
                $CodePostBudget = $input['CodePostBudget'];
                $sql = 'select * from db_budgeting.cfg_set_post where CodePostBudget = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePostBudget))->result_array();
                $Status = $query[0]['Status'];
                if ($Status == 1) {
                    try {
                       $get = $this->m_master->caribasedprimary('db_budgeting.cfg_set_post','CodePostBudget',$CodePostBudget); 
                       $time = date('Y-m-d H:i:s');
                       $dataSave = array(
                           'Budget' => $input['Budget'],
                           'LastUpdateBy' => $this->session->userdata('NIP'),
                           'LastUpdateAt' => $time,
                       );
                       $this->db->where('CodePostBudget', $CodePostBudget);
                       $this->db->update('db_budgeting.cfg_set_post', $dataSave);

                       $arr_detail = array(
                            'Before' => array(
                                   'Budget' => $get[0]['Budget'],
                            ),
                            'After' => array(
                                'Budget' => $input['Budget'],
                            ),    
                       );
                       $dataSave = array(
                           'CodePostBudget' => $CodePostBudget,
                           'Time' => $time,
                           'ActionBy' => $this->session->userdata('NIP'),
                           'Detail' => json_encode(array('action' => 'Edited','Detail' => $arr_detail)),
                       );
                       $this->db->insert('db_budgeting.log_cfg_set_post', $dataSave);
                    } catch (Exception $e) {
                         $Msg = $this->Msg['Error'];
                    }   
                }
                else
                {
                    $Msg = $this->Msg['NotAction'];
                }
                break;
            case 'delete':
                $CodePostBudget = $input['CodePostBudget'];
                $sql = 'select * from db_budgeting.cfg_set_post where CodePostBudget = ? and Active = 1';
                $query=$this->db->query($sql, array($CodePostBudget))->result_array();
                $Status = $query[0]['Status']; // check can be delete
                   if ($Status == 1) {
                       // $dataSave = array(
                       //     'Active' => 0
                       // );
                       // $this->db->where('CodePostBudget', $CodePostBudget);
                       // $this->db->where('Active', 1);
                       // $this->db->update('db_budgeting.cfg_set_post', $dataSave);
                        $this ->db-> where('CodePostBudget', $CodePostBudget);
                        $this ->db-> delete('db_budgeting.cfg_set_post');

                       $dataSave = array(
                           'CodePostBudget' => $CodePostBudget,
                           'Time' => date('Y-m-d H:i:s'),
                           'ActionBy' => $this->session->userdata('NIP'),
                           'Detail' => json_encode(array('action' => 'Delete')),
                       );
                       $this->db->insert('db_budgeting.log_cfg_set_post', $dataSave);
                   }
                   else
                   {
                       $Msg = $this->Msg['NotAction'];
                   }
                break;
            default:
                # code...
                break;
        }

        echo json_encode($Msg);
    }

    public function getBudgetLastYearByCode()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $LastYear = $input['Year'] - 1;
        $sql = 'select Budget from db_budgeting.cfg_set_post where CodeHeadAccount = ? and Year = ? and Active = 1 limit 1';
        $query=$this->db->query($sql, array($input['CodeHeadAccount'],$LastYear))->result_array();
        echo json_encode($query);
    }

    public function LogPostDepartement()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/setpostdepartement/pageLogPostDepartement',$this->data,true);
        echo json_encode($arr_result);
    }

    public function DataLogPostDepartement()
    {
        $requestData= $_REQUEST;
        $sqltotalData = 'select count(*) as total from db_budgeting.log_cfg_set_post';
        $querytotalData = $this->db->query($sqltotalData)->result_array();
        $totalData = $querytotalData[0]['total'];

        $sql = 'select a.*,b.PostName,b.CodePost,c.CodeHeadAccount,c.Name as NameHeadAccount,c.Departement,d.Year,e.Name as NameAction,e.NIP from db_budgeting.log_cfg_set_post as a
                join db_budgeting.cfg_set_post as d on a.CodePostBudget = d.CodePostBudget
                join db_budgeting.cfg_head_account as c on d.CodeHeadAccount =  c.CodeHeadAccount
                join db_budgeting.cfg_post as b on c.CodePost = b.CodePost
                join db_employees.employees as e on a.ActionBy = e.NIP   
               ';

        $sql.= ' where e.NIP LIKE "'.$requestData['search']['value'].'%" or e.Name LIKE "'.$requestData['search']['value'].'%" or a.CodePostBudget LIKE "'.$requestData['search']['value'].'%" or b.PostName LIKE "'.$requestData['search']['value'].'%" or c.Name LIKE "'.$requestData['search']['value'].'%"
                ';
        $sql.= ' ORDER BY a.Time Desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $Departement = $row['Departement'];
            $exp = explode('.', $Departement);
            if ($exp[0] == 'NA') { // Non Academic
                $tget = $this->m_master->caribasedprimary('db_employees.division','ID',$exp[1]);
                $Departement = $tget[0]['Description'].' ('.$tget[0]['Division'].')';
            }
            elseif ($exp[0] == 'AC') {
                $tget = $this->m_master->caribasedprimary('db_academic.program_study','ID',$exp[1]);
                $Departement = $tget[0]['NameEng'];
            }

            $DayDateTime = '';
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $row['Time']);
            $DayDateTime = $datetime->format('D').','.$row['Time'];

            // get Detail 
            $JsonArr = json_decode($row['Detail']);
            $JsonAction = $JsonArr->action;
            $str = $JsonAction.'<br>';
            if (array_key_exists("Detail",$JsonArr)) {
                $JsonDetail = $JsonArr->Detail;
                $Count1 = count($JsonDetail);
                $No1 = 1;
                foreach ($JsonDetail as $key => $value) {
                    $str .= $key.' : ';
                    $Count2 = count($value);
                    $No2 = 1;
                    foreach ($value as $ac => $valuee) {
                        if ($No2 != $Count1) {
                            $str .= $ac.' = '.'Rp. '.number_format($valuee,0,",",".").',-'.' ; '; 
                        }
                        else
                        {
                            $str .= $ac.' = '.'Rp. '.number_format($valuee,0,",",".").',-'.'<br>';
                        }
                        $No2++;
                    }

                    $No1++;
                }
            }
            

            $nestedData[] = $row['CodePostBudget'];
            $nestedData[] = $row['CodeHeadAccount'].'<br>'.$row['PostName'].'-'.$row['NameHeadAccount'].'<br>'.$Departement;
            $nestedData[] = $row['NIP'].'<br>'.$row['NameAction'];
            $nestedData[] = $DayDateTime;
            $nestedData[] = $str;
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

    public function LoadSetUserRole()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/pageLoadSetUserRole',$this->data,true);
        echo json_encode($arr_result);
    }

    public function LoadMasterUserRoleDepartement()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        // pass check data existing
        $this->data['dt'] = $this->m_master->showData_array('db_budgeting.cfg_set_userrole');
        $this->data['cfg_m_userrole'] = $this->m_master->showData_array('db_budgeting.cfg_m_userrole');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/setuserrole/LoadMasterUserRoleDepartement',$this->data,true);
        echo json_encode($arr_result);
    }

    public function AutoCompletePostDepartement()
    {
        $this->auth_ajax();
        $input = $this->getInputToken();
        $data['response'] = 'true'; //mengatur response
        $data['message'] = array(); //membuat array
        $getData = $this->m_budgeting->getPostDepartementAutoComplete($input['PostDepartement']);
        for ($i=0; $i < count($getData); $i++) {
        // for ($i=0; $i < 2; $i++) {
            $data['message'][] = array(
                'label' => $getData[$i]['CodePostRealisasi'].' | '.$getData[$i]['PostName'].'-'.$getData[$i]['RealisasiPostName'].' | '.$getData[$i]['NameDepartement'],
                'value' => $getData[$i]['CodePostRealisasi']
            );
        }
        echo json_encode($data);
    }

    public function save_cfg_set_userrole()
    {
        $this->auth_ajax();
        $Input = $this->getInputToken();
        // check data insert or update
        $sql = 'select * from db_budgeting.cfg_set_userrole where CodePostRealisasi = ? and ID_m_userrole = ?';
        $query=$this->db->query($sql, array($Input['CodePostRealisasi'],$Input['id_m_userrole']))->result_array();
        if (count($query) > 0) {
            // update
            $dataSave = array(
                $Input['field'] => $Input['Input'],
            );
            $this->db->where('CodePostRealisasi', $Input['CodePostRealisasi']);
            $this->db->where('ID_m_userrole', $Input['id_m_userrole']);
            $this->db->update('db_budgeting.cfg_set_userrole', $dataSave);
        }
        else
        {
            // insert
            $dataSave = array(
                $Input['field'] => $Input['Input'],
                'CodePostRealisasi' => $Input['CodePostRealisasi'],
                'ID_m_userrole' => $Input['id_m_userrole']
            );
            $this->db->insert('db_budgeting.cfg_set_userrole', $dataSave);
        }

    }

    public function LoadSetUserApprovalDepartement()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $this->data['cfg_m_type_approval'] = $this->m_master->showData_array('db_budgeting.cfg_m_type_approval');
        // $this->data['employees'] = $this->m_master->showData_array('db_budgeting.cfg_m_type_approval');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/configuration/setuserrole/LoadSetUserApprovalDepartement',$this->data,true);
        echo json_encode($arr_result);
    }

    public function get_cfg_set_roleuser_budgeting($Departement)
    {
        $this->auth_ajax();
        $getData = $this->m_budgeting->get_cfg_set_roleuser_budgeting($Departement);
        echo json_encode($getData);
    }

    public function save_cfg_set_roleuser_budgeting()
    {
        $this->auth_ajax();
        $msg = array('status' => 0,'msg' => '');
        $Input = $this->getInputToken();
        $Action = $Input['Action'];
        switch ($Action) {
            case "":
                $dt = $Input['dt'];
                $dt = (array) json_decode(json_encode($dt),true);
                for ($i=0; $i < count($dt); $i++) { 
                    $FormInsert = $dt[$i]['FormInsert'];
                    // check NIM already exist in employees
                    $NIP = $FormInsert['NIP'];
                    $G = $this->m_master->caribasedprimary('db_employees.employees','NIP',$NIP);
                    if (count($G) == 0) {
                        $msg['msg'] = 'NIP : '.$NIP.' is not already exist';   
                        break;
                    }
                    $Method = $dt[$i]['Method'];
                    $subAction = $Method['Action'];
                    if ($subAction == 'add') {
                        $this->db->insert('db_budgeting.cfg_approval_budget',$FormInsert);
                    }
                    else
                    {
                        $ID = $Method['ID'];
                        $this->db->where('ID', $ID);
                        $this->db->update('db_budgeting.cfg_approval_budget', $FormInsert);
                    }
                }
                if ($msg['msg'] == '') {
                   $msg['status'] = 1;
                }
                break;
            case "delete":
                $ID = $Input['ID_set_roleuser'];
                $this->m_master->delete_id_table_all_db($ID,'db_budgeting.cfg_approval_budget');
                $msg['status'] = 1;
                break;
            default:
                # code...
                break;
        }
        

        echo json_encode($msg);
    }

    /*Note ***
    *    Budgeting Entry for All
    *Alhadi Rahman 02 Oktober 2018
    */

    public function entry_budgeting($Request = null)
    {
        $arr = array('EntryPostItemBudgeting',
                    'EntryBudget',
                    'Approval',
                    'ListBudgetDepartement',
                    'budget_revisi',
                    'report_anggaran_per_years',
                    null
                );
                if (in_array($Request, $arr))
                  {
                    $this->data['request'] = $Request;
                    $content = $this->load->view('global/budgeting/entry_budgeting',$this->data,true);
                    $this->temp($content);
                  }
                else
                  {
                    show_404($log_error = TRUE);
                  }
       
    }

    public function EntryBudget($Year = null)
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $get = $this->m_master->caribasedprimary('db_budgeting.cfg_dateperiod','Activated',1);
        $this->data['arr_Year'] = $this->m_master->showData_array('db_budgeting.cfg_dateperiod');
        $Departement = $this->session->userdata('IDDepartementPUBudget');
        // filtering auth department cfg_approval_budget
        $arr_department_pu = $this->m_budgeting->Budget_department_auth($Departement);
        $this->data['arr_department_pu'] = $arr_department_pu;
        $arr_result['html'] = $this->load->view('global/budgeting/form_entry_budgeting',$this->data,true);
        echo json_encode($arr_result);
    }

    public function EntryBudget_Approval()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('global/budgeting/form_approval_budgeting',$this->data,true);
        echo json_encode($arr_result);
    }

    public function getLoadApprovalBudget($Year = null)
    {
        $Input = $this->getInputToken();
        $Departement = $Input['Departement'];
        $arr_result = array('html' => '','jsonPass' => '');
        $this->data['arr_Year'] = $this->m_master->showData_array('db_budgeting.cfg_dateperiod');
        $get = $this->m_master->caribasedprimary('db_budgeting.cfg_dateperiod','Activated',1);
        $arr_bulan = $this->m_master->getShowIntervalBulan($get[0]['StartPeriod'],$get[0]['EndPeriod']);
        $Year = ($Year == null ) ? $get[0]['Year'] : $Year;
        $get = $this->m_budgeting->getPostDepartementForDomApproval($Year,$Departement);
        $this->data['fin'] = 0;
        $DepartementSess = $this->session->userdata('IDDepartementPUBudget');
        if ($DepartementSess == 'NA.9') {
            $this->data['fin'] = 1;
        }
        $this->data['Year'] = $Year;
        $this->data['Departement'] = $Departement;
        $this->data['arr_PostBudget'] = $get['data'];
        $this->data['arr_bulan'] = $arr_bulan;
        $arr_result['html'] = $this->load->view('global/budgeting/form_entry_budgeting',$this->data,true);
        echo json_encode($arr_result);
    }

    public function EntryPostItemBudgeting()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('global/budgeting/entry_post_item_budgeting',$this->data,true);
        echo json_encode($arr_result);
    } 

    public function getCreatorBudget()
    {
        $this->auth_ajax();
        $Input = $this->getInputToken();
        $Year = $Input['Year'];
        $Departement = $Input['Departement'];
        $get = $this->m_master->caribasedprimary('db_budgeting.cfg_dateperiod','Year',$Year);
        $arr_result = array('creator_budget_approval' => array(),'creator_budget' => array());
        $arr_bulan = $this->m_master->getShowIntervalBulan($get[0]['StartPeriod'],$get[0]['EndPeriod']);
        $arr_result['arr_bulan'] = $arr_bulan;
        $get = $this->m_budgeting->get_creator_budget_approval($Year,$Departement,'');
        if (count($get) > 0) {
            // get Creator Budget
            $ID_creator_budget_approval= $get[0]['ID'];
            $get2 = $this->m_budgeting->get_creator_budget($ID_creator_budget_approval);
            $arr_result['creator_budget_approval'] = $get;
            $arr_result['creator_budget'] = $get2;
        }
        
        $get = $this->m_budgeting->getPostDepartementForDomApproval($Year,$Departement);
        $arr_result['PostBudget'] = $get;
        $arr_result['Approval'] = $this->m_budgeting->get_cfg_set_roleuser_budgeting($Departement); 

        // adding department while the user have auth of custom approval was added by admin
            $arr_result['Add_department_IFCustom_approval'] = $this->m_budgeting->Add_department_IFCustom_approval($Year);
            $arr_result['m_type_user'] = $this->m_master->showData_array('db_budgeting.cfg_m_type_approval');

        echo json_encode($arr_result);
    }

    public function update_approval_budgeting()
    {
        $this->auth_ajax();
        $Input = $this->getInputToken();
        $rs = array('msg' => '');
        switch ($Input['action']) {
            case 'add':
                // validation urutan Approver
                    $id_creator_budget_approval = $Input['id_creator_budget_approval'];
                    $G_data = $this->m_master->caribasedprimary('db_budgeting.creator_budget_approval','ID',$id_creator_budget_approval);
                    $JsonStatus = $G_data[0]['JsonStatus'];
                    $JsonStatus = (array)json_decode($JsonStatus,true);
                    $Approver = $Input['NIP'];
                    $indexjson = $Input['indexjson'];
                    $Visible = $Input['Visible'];
                    $NameTypeDesc = $Input['NameTypeDesc'];
                    $indexjsonAdd = count($JsonStatus); // hitung index array
                    if ($indexjson == $indexjsonAdd ) { // validation urutan Approver
                        $JsonStatus[] = array(
                            'NIP' => $Approver,
                            'Status' => 0,
                            'ApproveAt' => date('Y-m-d H:i:s'),
                            'Representedby' => '',
                            'Visible' => $Visible,
                            'NameTypeDesc' => $NameTypeDesc,
                         );

                        $JsonStatusSave = json_encode($JsonStatus);
                        $dataSave = array(
                            'JsonStatus' => $JsonStatusSave,
                        );    
                        $this->db->where('ID',$id_creator_budget_approval);
                        $this->db->update('db_budgeting.creator_budget_approval',$dataSave);
                        
                        $rs['data']=  $JsonStatusSave;
                            // save to log
                                $this->m_budgeting->log_budget($id_creator_budget_approval,'Custom Approval',$By = $this->session->userdata('NIP')); 
                    }
                    else
                    {
                        $rs['msg'] = 'Please fill Approver '.(count($JsonStatus)+1);
                    }
                break;
            case 'edit':
                    if ($Input['indexjson'] == 0) {
                        $id_creator_budget_approval = $Input['id_creator_budget_approval'];
                        $G_data = $this->m_master->caribasedprimary('db_budgeting.creator_budget_approval','ID',$id_creator_budget_approval);
                        $JsonStatus = $G_data[0]['JsonStatus'];
                        $JsonStatus = (array)json_decode($JsonStatus,true);
                        $Approver = $Input['NIP'];
                        $indexjson = $Input['indexjson'];
                        $Visible = $Input['Visible'];
                        $NameTypeDesc = $JsonStatus[$indexjson]['NameTypeDesc'];
                        $ApproveAt = $JsonStatus[$indexjson]['ApproveAt'];

                        $JsonStatus[$indexjson] = array(
                            'NIP' => $Approver,
                            'Status' => 1,
                            'ApproveAt' => $ApproveAt,
                            'Representedby' => '',
                            'Visible' => $Visible,
                            'NameTypeDesc' => $NameTypeDesc,
                        );
                        $JsonStatusSave = json_encode($JsonStatus);
                        $dataSave = array(
                            'JsonStatus' => $JsonStatusSave,
                        );    
                        $this->db->where('ID',$id_creator_budget_approval);
                        $this->db->update('db_budgeting.creator_budget_approval',$dataSave);
                            
                        $rs['data']= $JsonStatusSave;
                        // save to log
                            $this->m_budgeting->log_budget($id_creator_budget_approval,'Custom Approval',$By = $this->session->userdata('NIP')); 
                    }
                    else
                    {
                        // $id_creator_budget_approval = $Input['id_creator_budget_approval'];
                        // $G_data = $this->m_master->caribasedprimary('db_budgeting.creator_budget_approval','ID',$id_creator_budget_approval);
                        // if ($G_data[0]['Status'] == 0 || $G_data[0]['Status'] == 3) {
                        //     $JsonStatus = $G_data[0]['JsonStatus'];
                        //     $JsonStatus = (array)json_decode($JsonStatus,true);
                        //     $Approver = $Input['NIP'];
                        //     $indexjson = $Input['indexjson'];
                        //     $Visible = $Input['Visible'];
                        //     $NameTypeDesc = $Input['NameTypeDesc'];

                        //     $ApproveAt = $JsonStatus[$indexjson]['ApproveAt'];

                        //     $JsonStatus[$indexjson] = array(
                        //         'NIP' => $Approver,
                        //         'Status' => 0,
                        //         'ApproveAt' =>  $ApproveAt,
                        //         'Representedby' => '',
                        //         'Visible' => $Visible,
                        //         'NameTypeDesc' => $NameTypeDesc,
                        //     );
                        //     $JsonStatusSave = json_encode($JsonStatus);
                        //     $dataSave = array(
                        //         'JsonStatus' => $JsonStatusSave,
                        //     );    
                        //     $this->db->where('ID',$id_creator_budget_approval);
                        //     $this->db->update('db_budgeting.creator_budget_approval',$dataSave);
                            
                        //     $rs['data']= $JsonStatusSave;
                        //     // save to log
                        //         $this->m_budgeting->log_budget($id_creator_budget_approval,'Custom Approval',$By = $this->session->userdata('NIP')); 
                        // }
                        // else
                        // {
                        //     $id_creator_budget_approval = $Input['id_creator_budget_approval'];
                        //     $G_data = $this->m_master->caribasedprimary('db_budgeting.creator_budget_approval','ID',$id_creator_budget_approval);
                        //     $JsonStatus = $G_data[0]['JsonStatus'];
                        //     $JsonStatus = (array)json_decode($JsonStatus,true);
                        //     $Approver = $Input['NIP'];
                        //     $indexjson = $Input['indexjson'];
                        //     $Visible = $Input['Visible'];
                        //     $NameTypeDesc = $JsonStatus[$indexjson]['NameTypeDesc'];
                        //     $Status = $JsonStatus[$indexjson]['Status'];
                        //     $ApproveAt = $JsonStatus[$indexjson]['ApproveAt'];

                        //     $JsonStatus[$indexjson] = array(
                        //         'NIP' => $Approver,
                        //         'Status' => $Status,
                        //         'ApproveAt' => $ApproveAt,
                        //         'Representedby' => '',
                        //         'Visible' => $Visible,
                        //         'NameTypeDesc' => $NameTypeDesc,
                        //     );
                        //     $JsonStatusSave = json_encode($JsonStatus);
                        //     $dataSave = array(
                        //         'JsonStatus' => $JsonStatusSave,
                        //     );    
                        //     $this->db->where('ID',$id_creator_budget_approval);
                        //     $this->db->update('db_budgeting.creator_budget_approval',$dataSave);
                                
                        //     $rs['data']= $JsonStatusSave;
                        //     // save to log
                        //         $this->m_budgeting->log_budget($id_creator_budget_approval,'Custom Approval',$By = $this->session->userdata('NIP')); 
                        // }

                        $id_creator_budget_approval = $Input['id_creator_budget_approval'];
                        $G_data = $this->m_master->caribasedprimary('db_budgeting.creator_budget_approval','ID',$id_creator_budget_approval);
                        $JsonStatus = $G_data[0]['JsonStatus'];
                        $JsonStatus = (array)json_decode($JsonStatus,true);
                        $Approver = $Input['NIP'];
                        $indexjson = $Input['indexjson'];
                        $Visible = $Input['Visible'];
                        // $NameTypeDesc = $JsonStatus[$indexjson]['NameTypeDesc'];
                        $NameTypeDesc = $Input['NameTypeDesc'];
                        $Status = $JsonStatus[$indexjson]['Status'];
                        $ApproveAt = $JsonStatus[$indexjson]['ApproveAt'];

                        $JsonStatus[$indexjson] = array(
                            'NIP' => $Approver,
                            'Status' => $Status,
                            'ApproveAt' => $ApproveAt,
                            'Representedby' => '',
                            'Visible' => $Visible,
                            'NameTypeDesc' => $NameTypeDesc,
                        );
                        $JsonStatusSave = json_encode($JsonStatus);
                        $dataSave = array(
                            'JsonStatus' => $JsonStatusSave,
                        );    
                        $this->db->where('ID',$id_creator_budget_approval);
                        $this->db->update('db_budgeting.creator_budget_approval',$dataSave);
                            
                        $rs['data']= $JsonStatusSave;
                        // save to log
                            $this->m_budgeting->log_budget($id_creator_budget_approval,'Custom Approval',$By = $this->session->userdata('NIP')); 
                        
                    }
                    
                break;    
            case 'delete':
                    $id_creator_budget_approval = $Input['id_creator_budget_approval'];
                    $indexjson = $Input['indexjson'];
                    $G_data = $this->m_master->caribasedprimary('db_budgeting.creator_budget_approval','ID',$id_creator_budget_approval);
                    $JsonStatus = $G_data[0]['JsonStatus'];
                    $JsonStatus = (array)json_decode($JsonStatus,true);
                    // Data json yang boleh dihapus adalah yang terakhir
                    $KeyJsonStatus = count($JsonStatus) - 1;
                    if ($indexjson == $KeyJsonStatus) {
                        $t = array();
                        for ($i=0; $i < count($JsonStatus) - 1; $i++) { // add 0 until last key - 1
                            $t[] = $JsonStatus[$i];
                        }

                        $JsonStatus = $t;
                        $JsonStatusSave = json_encode($JsonStatus);
                        $dataSave = array(
                            'JsonStatus' => $JsonStatusSave,
                        );
                        $this->db->where('ID',$id_creator_budget_approval);
                        $this->db->update('db_budgeting.creator_budget_approval',$dataSave);
                        $rs['msg'] = '';
                        $rs['data']= $JsonStatusSave;
                        // save to log
                        $this->m_budgeting->log_budget($id_creator_budget_approval,'Custom Approval',$By = $this->session->userdata('NIP')); 
                    }
                    else
                    {
                        $rs['msg'] = 'Please delete last Approval first';
                    }

                break;
            default:
                # code...
                break;
        }

        echo json_encode($rs);
    }

    public function saveCreatorbudget()
    {
        $this->auth_ajax();
        $msg = array('Status' => 0,'msg'=>'error');
        $Input = $this->getInputToken();
        $creator_budget = $Input['creator_budget'];
         $creator_budget_approval = $Input['creator_budget_approval'];
        // save to creator_budget
        switch ($Input['action']) {
            case 'add':
                // get rule approval
                    $Approval = $this->m_budgeting->get_approval_budgeting($creator_budget_approval->Departement);
                    $JsonStatus = array();
                    for ($i=0; $i < count($Approval); $i++) { 
                        $Status = ($i==0) ? 1 : 0;
                        $NIP = $Approval[$i]['NIP'];
                        $Visible = $Approval[$i]['Visible'];
                        $NameTypeDesc = $Approval[$i]['NameTypeDesc'];
                        $ApproveAt = '';
                        $Representedby = '';
                        $JsonStatus[] = array(
                            'NIP' => $NIP,
                            'Status' => $Status,
                            'ApproveAt' => $ApproveAt,
                            'Representedby' => $Representedby,
                            'Visible' => $Visible,
                            'NameTypeDesc' => $NameTypeDesc,
                        );

                    }

                $dataSave = array(
                    'Departement' => $creator_budget_approval->Departement,
                    'Year' => $creator_budget_approval->Year,
                    'Note' => $creator_budget_approval->Note,
                    'Status' => $creator_budget_approval->Status,
                    'JsonStatus' => json_encode($JsonStatus),
                );
                $this->db->insert('db_budgeting.creator_budget_approval', $dataSave);
                $ID_creator_budget_approval = $this->db->insert_id();

                for ($i=0; $i < count($creator_budget); $i++) { 
                    $CodePostRealisasi = $creator_budget[$i]->CodePostRealisasi;
                    $UnitCost = $creator_budget[$i]->UnitCost;
                    $Freq = $creator_budget[$i]->Freq;
                    $DetailMonth = $creator_budget[$i]->DetailMonth;
                    $DetailMonth = json_encode($DetailMonth);
                    $SubTotal = $creator_budget[$i]->SubTotal;

                    $dataSave = array(
                        'CodePostRealisasi' => $CodePostRealisasi,
                        'UnitCost' => $UnitCost,
                        'Freq' => $Freq,
                        'DetailMonth' => $DetailMonth,
                        'SubTotal' => $SubTotal,
                        'CreatedBy' => $this->session->userdata('NIP'),
                        'CreatedAt' => date('Y-m-d H:i:s'),
                        'ID_creator_budget_approval' => $ID_creator_budget_approval
                    );
                    $this->db->insert('db_budgeting.creator_budget', $dataSave);

                }

                // save date period
                    $update = array('Status' => 0);
                    $this->db->where('Year', $creator_budget_approval->Year);
                    $this->db->update('db_budgeting.cfg_dateperiod', $update);
                
                // save to log
                    $this->m_budgeting->log_budget($ID_creator_budget_approval,'Create',$By = $this->session->userdata('NIP'));

                // send to approval jika status = 1
                if ($creator_budget_approval->Status == 1) {
                    // Send Notif
                        $IDdiv = $creator_budget_approval->Departement;
                        $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                        // $NameDepartement = $G_div[0]['NameDepartement'];
                        $Code = $G_div[0]['Code'];
                        $data = array(
                            'auth' => 's3Cr3T-G4N',
                            'Logging' => array(
                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Set Budget of '.$Code,
                                            'Description' => 'Budget '.$Code.' has been already set by '.$this->session->userdata('Name'),
                                            'URLDirect' => 'budgeting_entry',
                                            'CreatedBy' => $this->session->userdata('NIP'),
                                          ),
                            'To' => array(
                                      'NIP' => array($JsonStatus[1]['NIP']),
                                    ),
                            'Email' => 'No', 
                        );

                        // send email is holding or warek keatas
                             $this->m_master->send_email_budgeting_holding($JsonStatus[1]['NIP'],$IDdiv,$data['Logging']['URLDirect'],$data['Logging']['Description']);

                        $url = url_pas.'rest2/__send_notif_browser';
                        $token = $this->jwt->encode($data,"UAP)(*");
                        $this->m_master->apiservertoserver($url,$token);
                }        

                $msg = array('Status' => 1,'msg'=>$ID_creator_budget_approval );
                break;
            case 'edit':
                $ID = $Input['ID'];
                // del first
                $this ->db-> where('ID_creator_budget_approval', $ID);
                $this ->db-> delete('db_budgeting.creator_budget');
                // create again
                for ($i=0; $i < count($creator_budget); $i++) { 
                    $CodePostRealisasi = $creator_budget[$i]->CodePostRealisasi;
                    $UnitCost = $creator_budget[$i]->UnitCost;
                    $Freq = $creator_budget[$i]->Freq;
                    $DetailMonth = $creator_budget[$i]->DetailMonth;
                    $DetailMonth = json_encode($DetailMonth);
                    $SubTotal = $creator_budget[$i]->SubTotal;

                    // $dataSave = array(
                    //     'UnitCost' => $UnitCost,
                    //     'Freq' => $Freq,
                    //     'DetailMonth' => $DetailMonth,
                    //     'SubTotal' => $SubTotal,
                    //     'LastUpdateBy' => $this->session->userdata('NIP'),
                    //     'LastUpdateAt' => date('Y-m-d H:i:s'),
                    // );
                    // $this->db->where('CodePostRealisasi', $CodePostRealisasi);
                    // $this->db->update('db_budgeting.creator_budget', $dataSave);

                    $dataSave = array(
                        'CodePostRealisasi' => $CodePostRealisasi,
                        'UnitCost' => $UnitCost,
                        'Freq' => $Freq,
                        'DetailMonth' => $DetailMonth,
                        'SubTotal' => $SubTotal,
                        'CreatedBy' => $this->session->userdata('NIP'),
                        'CreatedAt' => date('Y-m-d H:i:s'),
                        'ID_creator_budget_approval' => $ID
                    );
                    $this->db->insert('db_budgeting.creator_budget', $dataSave);

                }

                $creator_budget_approval = $Input['creator_budget_approval'];
                // get Json Status to set All Status to 0
                    $G_data = $this->m_master->caribasedprimary('db_budgeting.creator_budget_approval','ID',$ID);
                    // $JsonStatus =(array) json_decode($G_data[0]['JsonStatus'],true);
                    // for ($i=1; $i < count($JsonStatus); $i++) { 
                    //     $JsonStatus[$i]['Status'] = 0;
                    // }

                    // after edit get all JsonStatus
                     $Approval = $this->m_budgeting->get_approval_budgeting($creator_budget_approval->Departement);
                     $JsonStatus = array();
                     for ($i=0; $i < count($Approval); $i++) { 
                         $Status = ($i==0) ? 1 : 0;
                         $NIP = $Approval[$i]['NIP'];
                         $Visible = $Approval[$i]['Visible'];
                         $NameTypeDesc = $Approval[$i]['NameTypeDesc'];
                         $ApproveAt = '';
                         $Representedby = '';
                         $JsonStatus[] = array(
                             'NIP' => $NIP,
                             'Status' => $Status,
                             'ApproveAt' => $ApproveAt,
                             'Representedby' => $Representedby,
                             'Visible' => $Visible,
                             'NameTypeDesc' => $NameTypeDesc,
                         );

                     }

                $dataSave = array(
                    'Note' => $creator_budget_approval->Note,
                    'Status' => $creator_budget_approval->Status,
                    'JsonStatus' => json_encode($JsonStatus),
                );
                $this->db->where('ID', $ID);
                $this->db->update('db_budgeting.creator_budget_approval', $dataSave);

                $st = ($creator_budget_approval->Status == 0 || $creator_budget_approval->Status == '0') ? 'Edited' : 'Issued / Submit';
                // save to log
                    $this->m_budgeting->log_budget($ID,$st,$By = $this->session->userdata('NIP')); 

                $msg = array('Status' => 1,'msg'=>$ID );

                // Send Notif
                    // send revisi or not
                    $RevisiOrNotNotif = $this->m_master->__RevisiOrNotNotif($ID,'db_budgeting.log_budget','ID_creator_budget_approval');

                    $IDdiv = $G_data[0]['Departement'];
                    $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                    // $NameDepartement = $G_div[0]['NameDepartement'];
                    $Code = $G_div[0]['Code'];
                    $data = array(
                        'auth' => 's3Cr3T-G4N',
                        'Logging' => array(
                                        'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> '.$RevisiOrNotNotif.' Set Budget of '.$Code,
                                        'Description' => $RevisiOrNotNotif.'Budget '.$Code.' has been already set by '.$this->session->userdata('Name'),
                                        'URLDirect' => 'budgeting_entry',
                                        'CreatedBy' => $this->session->userdata('NIP'),
                                      ),
                        'To' => array(
                                  'NIP' => array($JsonStatus[1]['NIP']),
                                ),
                        'Email' => 'No', 
                    );

                    // send email is holding or warek keatas
                         $this->m_master->send_email_budgeting_holding($JsonStatus[1]['NIP'],$IDdiv,$data['Logging']['URLDirect'],$data['Logging']['Description']);

                    $url = url_pas.'rest2/__send_notif_browser';
                    $token = $this->jwt->encode($data,"UAP)(*");
                    $this->m_master->apiservertoserver($url,$token);                    
                break;
            default:
                # code...
                break;
        }
        echo json_encode($msg);
    }

    public function Upload_File_Creatorbudget()
    {
        $input = $this->getInputToken();
        // upload file
        $filename = $input['attachName'].'.'.$input['extension'];
        $config['upload_path']   = './uploads/budgeting';
        $config['overwrite'] = TRUE; 
        $config['allowed_types'] = '*'; 
        $config['file_name'] = $filename;
        //$config['max_size']      = 100; 
        //$config['max_width']     = 300; 
        //$config['max_height']    = 300;  
        $this->load->library('upload', $config);
           
        if ( ! $this->upload->do_upload('fileData')) {
           // return $error = $this->upload->display_errors(); 
           echo json_encode(array('msg' => 'The file did not upload successfully','status' => 0));
           //$this->load->view('upload_form', $error); 
        }
           
        else { 
          // return $data =  $this->upload->data(); 
            // update data to save file
                $dataSave['FileUpload'] = $filename;
                $ID_creator_budget_approval = $input['id_creator_budget_approval'];
                $this->db->where('ID', $ID_creator_budget_approval);
                $this->db->update('db_budgeting.creator_budget_approval', $dataSave);

          echo json_encode(array('msg' => 'The file has been successfully uploaded','status' => 1,'filename' => $filename));
        }
    }

    public function Upload_File_Creatorbudget_all()
    {
        $input = $this->getInputToken();
        // upload file
        $filename = $input['attachName'].'.'.$input['extension'];
        $config['upload_path']   = './uploads/budgeting';
        $config['overwrite'] = TRUE; 
        $config['allowed_types'] = '*'; 
        $config['file_name'] = $filename;
        //$config['max_size']      = 100; 
        //$config['max_width']     = 300; 
        //$config['max_height']    = 300;  
        $this->load->library('upload', $config);
           
        if ( ! $this->upload->do_upload('fileData')) {
           // return $error = $this->upload->display_errors(); 
           echo json_encode(array('msg' => 'The file did not upload successfully','status' => 0));
           //$this->load->view('upload_form', $error); 
        }
           
        else { 
          // return $data =  $this->upload->data(); 
            // update data to save file
                $dataSave['BudgetApproveUpload'] = $filename;
                $year = $input['year'];
                $this->db->where('Year', $year);
                $this->db->update('db_budgeting.cfg_dateperiod', $dataSave);

          echo json_encode(array('msg' => 'The file has been successfully uploaded','status' => 1,'filename' => $filename));
        }
    }    

    public function ListBudgetDepartement()
    {
        $this->auth_ajax();
        $this->authFin();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/budget/ListBudgetDepartement',$this->data,true);
        echo json_encode($arr_result);
    }

    public function authFin()
    {
        $DepartementSess = $this->session->userdata('IDDepartementPUBudget');
        if ($DepartementSess != 'NA.9') {
            exit('No direct script access allowed');
        }
        
    }

    public function getListBudgetingDepartement()
    {
        $this->auth_ajax();
        $rs = array('dt' => array(),'dt_Year' => array());
        $Input = $this->getInputToken();
        $Year = $Input['Year'];
        $dt = $this->m_budgeting->getListBudgetingDepartement($Year);
        $dt_Year = $this->m_master->caribasedprimary('db_budgeting.cfg_dateperiod','Year',$Year);
        $rs['dt'] = $dt;
        $rs['dt_Year'] = $dt_Year;
        echo json_encode($rs);
    }

    /*
        End Budgeting
        27 March 2019
        Alhadi Rahman
    */

    public function BudgetLeft()
    {
        $this->auth_ajax();
        // $Departement = $this->session->userdata('IDDepartementPUBudget');
        // switch ($Departement) {
        //     case 'NA.9':
        //         $this->BudgetRemainingFinance();
        //         break;
            
        //     default:
        //         $this->BudgetRemainingPerDiv();
        //         break;
        // }
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('global/budgeting/BudgetRemaining',$this->data,true);
        echo json_encode($arr_result);
    }

    public function getListBudgetingRemaining()
    {
        $this->auth_ajax();
        $Input = $this->getInputToken();
        $Year = $Input['Year'];
        $get = $this->m_budgeting->getListBudgetingRemaining($Year);
        echo json_encode($get);
    }

    public function detail_budgeting_remaining()
    {
        $this->auth_ajax();
        // header('Content-Type: application/json');
        $arr_result = array('data' =>'');
        $Input = $this->getInputToken();
        $Year = $Input['Year'];
        $Departement = $Input['Departement'];
        $getData = $this->m_budgeting->get_budget_remaining($Year,$Departement);
        $arr_result = array('data' =>$getData);
        echo json_encode($arr_result);
    }

    public function detail_budgeting_remaining_All()
    {
        $this->auth_ajax();
        $arr_result = array('data' =>'');
        $Input = $this->getInputToken();
        $Year = $Input['Year'];
        $getData = $this->m_budgeting->get_budget_remaining_all($Year);
        $arr_result = array('data' =>$getData);
        echo json_encode($arr_result);
    }

    // PR Start

    public function PostBudgetThisMonth_Department()
    {
        $this->auth_ajax();
        $Input = $this->getInputToken();
        $Month = date('Y-m');
        $Departement = $Input['Departement'];
        $PostBudget = $Input['PostBudget'];
        $get = $this->m_budgeting->PostBudgetThisMonth_Department($Departement,$PostBudget,$Month);
        echo json_encode($get);
    }

    public function getPostBudgetDepartement()
    {
        $this->auth_ajax();
        $Input = $this->getInputToken();
        $Departement = $Input['Departement'];
        $Year = $Input['Year'];
        $get = $this->m_budgeting->getPostBudgetDepartement($Departement,$Year);
        echo json_encode($get);
    }

    

   

    public function update_approver()
    {
        $this->auth_ajax();
        $Input = $this->getInputToken();
        $rs = array('msg' => '');
        switch ($Input['action']) {
            case 'add':
                // validation urutan Approver
                    $PRCode = $Input['PRCode'];
                    $G_data = $this->m_master->caribasedprimary('db_budgeting.pr_create','PRCode',$PRCode);
                    $JsonStatus = $G_data[0]['JsonStatus'];
                    $JsonStatus = (array)json_decode($JsonStatus,true);
                    $Approver = $Input['Approver'];
                    $indexjson = $Input['indexjson'];
                    $indexjsonAdd = count($JsonStatus); // hitung index array
                    if ($indexjson == $indexjsonAdd ) { // validation urutan Approver
                        $JsonStatus[] = array(
                             'ApprovedBy' => $Approver,
                             'Status' => 0,
                             'ApproveAt' => '',
                             'Representedby' => '',
                         );

                        $JsonStatusSave = json_encode($JsonStatus);
                        $dataSave = array(
                            'JsonStatus' => $JsonStatusSave,
                        );    
                        $this->db->where('PRCode',$PRCode);
                        $this->db->update('db_budgeting.pr_create',$dataSave);
                        // get Name Approver for callback
                            for ($i=0; $i < count($JsonStatus); $i++) { 
                                $Name = $this->m_master->caribasedprimary('db_employees.employees','NIP',$JsonStatus[$i]['ApprovedBy']);
                                $Name = $Name[0]['Name'];
                                $JsonStatus[$i]['NameApprovedBy'] = $Name; 
                            }
                            
                            $rs['data']= $JsonStatus;
                            // insert to pr_circulation_sheet
                                $this->m_budgeting->pr_circulation_sheet($PRCode,'Custom Approval');
                    }
                    else
                    {
                        $rs['msg'] = 'Please fill Approver '.(count($JsonStatus)+1);
                    }
                break;
            case 'edit':
                    $PRCode = $Input['PRCode'];
                    $G_data = $this->m_master->caribasedprimary('db_budgeting.pr_create','PRCode',$PRCode);
                    $JsonStatus = $G_data[0]['JsonStatus'];
                    $JsonStatus = (array)json_decode($JsonStatus,true);
                    $Approver = $Input['Approver'];
                    $indexjson = $Input['indexjson'];
                    $JsonStatus[$indexjson] = array(
                        'ApprovedBy' => $Approver,
                        'Status' => 0,
                        'ApproveAt' => '',
                        'Representedby' => '',
                    );
                    $JsonStatusSave = json_encode($JsonStatus);
                    $dataSave = array(
                        'JsonStatus' => $JsonStatusSave,
                    );    
                    $this->db->where('PRCode',$PRCode);
                    $this->db->update('db_budgeting.pr_create',$dataSave);
                    for ($i=0; $i < count($JsonStatus); $i++) { 
                        $Name = $this->m_master->caribasedprimary('db_employees.employees','NIP',$JsonStatus[$i]['ApprovedBy']);
                        $Name = $Name[0]['Name'];
                        $JsonStatus[$i]['NameApprovedBy'] = $Name; 
                    }
                    
                    $rs['data']= $JsonStatus;
                    // insert to pr_circulation_sheet
                        $this->m_budgeting->pr_circulation_sheet($PRCode,'Custom Approval');
                break;    
            case 'delete':
                    $PRCode = $Input['PRCode'];
                    $indexjson = $Input['indexjson'];
                    $G_data = $this->m_master->caribasedprimary('db_budgeting.pr_create','PRCode',$PRCode);
                    $JsonStatus = $G_data[0]['JsonStatus'];
                    $JsonStatus = (array)json_decode($JsonStatus,true);
                    // Data json yang boleh dihapus adalah yang terakhir
                    $KeyJsonStatus = count($JsonStatus) - 1;
                    if ($indexjson == $KeyJsonStatus) {
                        $t = array();
                        for ($i=0; $i < count($JsonStatus) - 1; $i++) { // add 0 until last key - 1
                            $Name = $this->m_master->caribasedprimary('db_employees.employees','NIP',$JsonStatus[$i]['ApprovedBy']);
                            $Name = $Name[0]['Name'];
                            $JsonStatus[$i]['NameApprovedBy'] = $Name;
                            $t[] = $JsonStatus[$i];
                        }

                        $JsonStatus = $t;
                        $JsonStatusSave = json_encode($JsonStatus);
                        $dataSave = array(
                            'JsonStatus' => $JsonStatusSave,
                        );
                        $this->db->where('PRCode',$PRCode);
                        $this->db->update('db_budgeting.pr_create',$dataSave);
                        $rs['msg'] = '';
                        $rs['data']= $JsonStatus;
                    }
                    else
                    {
                        $rs['msg'] = 'Please delete last Approver first';
                    }

                break;
            default:
                # code...
                break;
        }

        echo json_encode($rs);
    }

    public function cancel_budget_department()
    {
        $this->auth_ajax();
        $Input = $this->getInputToken();
        $rs = array('msg' => '');
        // cek data existing in PR
            $id_creator_budget_approval = $Input['id_creator_budget_approval'];
            $G = $this->m_master->caribasedprimary('db_budgeting.creator_budget_approval','ID',$id_creator_budget_approval);
            $Departement = $G[0]['Departement'];
            $Year = $G[0]['Year'];
            $sql = 'select count(*) as total from db_budgeting.pr_create where Departement = ? and Year = ?';
            $query=$this->db->query($sql, array($Departement,$Year))->result_array();
             if ($query[0]['total'] > 0) {
                 $rs['msg'] =  $this->Msg['NotAction'];
             }
             else
             {
                // delete creator_budget_approval,creator_budget,log_budget,budget_left
                $this->db->where('ID',$id_creator_budget_approval);
                $this->db->delete('db_budgeting.creator_budget_approval');

                // delete budget_left
                $G_ = $this->m_master->caribasedprimary('db_budgeting.creator_budget','ID_creator_budget_approval',$id_creator_budget_approval);

                for ($i=0; $i < count($G_); $i++) { 
                    $ID_creator_budget = $G_[$i]['ID'];
                    $this->db->where('ID_creator_budget',$ID_creator_budget);
                    $this->db->delete('db_budgeting.budget_left');
                }
                // die();
                $this->db->where('ID_creator_budget_approval',$id_creator_budget_approval);
                $this->db->delete('db_budgeting.creator_budget');

                $this->db->where('ID_creator_budget_approval',$id_creator_budget_approval);
                $this->db->delete('db_budgeting.log_budget');

                $rs['msg'] =  'Success';
             }

        echo json_encode($rs);     
    }

    public function report_anggaran_per_years()
    {
        $this->auth_ajax();
        $this->authFin();
        $arr_result = array('html' => '','jsonPass' => '');
        $this->data['arr_Year'] = $this->m_master->showData_array('db_budgeting.cfg_dateperiod');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/budget/report_anggaran_per_years',$this->data,true);
        echo json_encode($arr_result);
    }

    public function budgeting_real_detail($token)
    {
        try {
            $key = "UAP)(*";
            $token = $this->jwt->decode($token,$key);
            $ID_budget_left = $token;
            $Departement = $this->session->userdata('IDDepartementPUBudget');
            $G_data = $this->m_budgeting->FindBudgetLeft_Department($ID_budget_left,$Departement);
            if (count($G_data) > 0) {
                $G_budget_left_payment = $this->m_budgeting->get_budget_left_group_by_month($ID_budget_left);
                $this->data['ID_budget_left'] = $ID_budget_left;
                $this->data['G_budget_left_payment'] = $G_budget_left_payment;
                $this->data['G_data'] = $G_data;
                $content = $this->load->view('global/budgeting/detail_budget_left_real',$this->data,true);
                $this->temp($content);   
            }
            else
            {
                show_404($log_error = TRUE);
            }
        } catch (Exception $e) {
            show_404($log_error = TRUE);
        }
    }

    public function budgeting_onprocess_detail($token)
    {
        try {
            $key = "UAP)(*";
            $token = $this->jwt->decode($token,$key);
            $ID_budget_left = $token;
            $Departement = $this->session->userdata('IDDepartementPUBudget');
            $G_data = $this->m_budgeting->FindBudgetLeft_Department($ID_budget_left,$Departement);
            if (count($G_data) > 0) {
                $G_budget_left_payment = $this->m_budgeting->get_budget_left_onprocess_group_by_month($ID_budget_left);
                $this->data['ID_budget_left'] = $ID_budget_left;
                $this->data['G_budget_left_payment'] = $G_budget_left_payment;
                $this->data['G_data'] = $G_data;
                $content = $this->load->view('global/budgeting/detail_budget_left_onprocess',$this->data,true);
                $this->temp($content);   
            }
            else
            {
                show_404($log_error = TRUE);
            }
        } catch (Exception $e) {
            show_404($log_error = TRUE);
        }
    }

    public function budget_revisi()
    {
        $this->auth_ajax();
        $this->authFin();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/budget/budget_revisi',$this->data,true);
        echo json_encode($arr_result);
    }

    public function budget_revisi_Revisi()
    {
        $this->auth_ajax();
        $this->authFin();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/budget/budget_revisi/revisi',$this->data,true);
        echo json_encode($arr_result);
    }

    public function budget_revisi_Revisi_save()
    {
        $Input = $this->getInputToken();
        // add or less budget
        switch ($Input['Type']) {
            case 'Add':
                $ID_budget_left = $Input['ID_budget_left'];
                $G_ = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
                $Value = $G_[0]['Value'];
                $Invoice = $Input['Invoice'];
                $Value = $Value + $Invoice;
                $this->db->where('ID',$ID_budget_left);
                $this->db->update('db_budgeting.budget_left',array('Value' => $Value));
                break;
            case 'Less':
                $ID_budget_left = $Input['ID_budget_left'];
                $G_ = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
                $Value = $G_[0]['Value'];
                $Invoice = $Input['Invoice'];
                $Value = $Value - $Invoice;
                $this->db->where('ID',$ID_budget_left);
                $this->db->update('db_budgeting.budget_left',array('Value' => $Value));
                break;
            default:
                # code...
                break;
        }

        $Input['CreateBy'] = $this->session->userdata('NIP');
        $Input['CreateAt'] = date('Y-m-d H:i:s');
        $this->db->insert('db_budgeting.budget_adjustment',$Input);

    }

    public function budget_revisi_Revisi_load()
    {
        $sql = 'select a.*,b.Name from db_budgeting.budget_adjustment as a 
                join db_employees.employees as b on a.CreateBy = b.NIP
                where Type != "Mutasi"  and a.ID not in (select ID_budget_adjustment_a from db_budgeting.budget_mutasi)
                and a.ID not in (select ID_budget_adjustment_b from db_budgeting.budget_mutasi)
                order by a.ID desc limit 500
            ';
        $query=$this->db->query($sql, array())->result_array();
        for ($i=0; $i < count($query); $i++) { 
            $ID_budget_left = $query[$i]['ID_budget_left'];
            $query[$i]['DetailPostBudget'] = $this->m_pr_po->Get_DataBudgeting_by_ID_budget_left($ID_budget_left);
        }

        echo json_encode($query);

    }

    public function budget_revisi_Mutasi()
    {
        $this->auth_ajax();
        $this->authFin();
        $arr_result = array('html' => '','jsonPass' => '');
        $arr_result['html'] = $this->load->view('page/budgeting/'.$this->data['department'].'/budget/budget_revisi/Mutasi',$this->data,true);
        echo json_encode($arr_result);
    }

    public function budget_revisi_Mutasi_save()
    {
        $Input = $this->getInputToken();
        $data1 = $Input['data1'];
        $data1 = json_decode(json_encode($data1),true);
        $data2 = $Input['data2'];
        $data2 = json_decode(json_encode($data2),true);

        // kurangi data 1 dengan invoice pada budget left
        $ID_budget_left = $data1['ID_budget_left'];
        $G_ = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
        $Value = $G_[0]['Value'];
        $Invoice = $data1['Invoice'];
        $Value = $Value - $Invoice;
        $this->db->where('ID',$ID_budget_left);
        $this->db->update('db_budgeting.budget_left',array('Value' => $Value));

        $data1['CreateBy'] = $this->session->userdata('NIP');
        $data1['CreateAt'] = date('Y-m-d H:i:s');
        $this->db->insert('db_budgeting.budget_adjustment',$data1);
        $ID_budget_adjustment_a = $this->db->insert_id(); 

        // tambahkan data 2 dengan invoice pada budget left
        $ID_budget_left = $data2['ID_budget_left'];
        $G_ = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
        $Value = $G_[0]['Value'];
        $Invoice = $data2['Invoice'];
        $Value = $Value + $Invoice;
        $this->db->where('ID',$ID_budget_left);
        $this->db->update('db_budgeting.budget_left',array('Value' => $Value));

        $data2['CreateBy'] = $this->session->userdata('NIP');
        $data2['CreateAt'] = date('Y-m-d H:i:s');
        $this->db->insert('db_budgeting.budget_adjustment',$data2);
        $ID_budget_adjustment_b = $this->db->insert_id(); 

        $dataSave = array(
            'ID_budget_adjustment_a' => $ID_budget_adjustment_a,
            'ID_budget_adjustment_b' => $ID_budget_adjustment_b,
        );

        $this->db->insert('db_budgeting.budget_mutasi',$dataSave);
    }

    public function budget_revisi_Mutasi_load()
    {
        $sql = 'select a.*,b.ID_budget_left as ID_budget_left_a,b.CreateAt,b.CreateBy,b.Invoice,b.Reason,
                c.ID_budget_left as ID_budget_left_b,d.Name 
                from db_budgeting.budget_mutasi as a
                join db_budgeting.budget_adjustment as b on a.ID_budget_adjustment_a = b.ID
                join db_budgeting.budget_adjustment as c on a.ID_budget_adjustment_b = c.ID
                join db_employees.employees as d on b.CreateBy = d.NIP
                order by a.ID desc limit 500
            ';
        $query=$this->db->query($sql, array())->result_array();
        for ($i=0; $i < count($query); $i++) { 
            $ID_budget_left = $query[$i]['ID_budget_left_a'];
            $query[$i]['DetailPostBudget_a'] = $this->m_pr_po->Get_DataBudgeting_by_ID_budget_left($ID_budget_left);

            $ID_budget_left = $query[$i]['ID_budget_left_b'];
            $query[$i]['DetailPostBudget_b'] = $this->m_pr_po->Get_DataBudgeting_by_ID_budget_left($ID_budget_left);
        }

        echo json_encode($query);
    } 

}
