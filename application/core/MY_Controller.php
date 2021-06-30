<?php 
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
    public $rest_setting_global = [];
    function __construct()
    {
        parent::__construct();

        if($this->session->userdata('loggedIn')){
            $departement = $this->__getDepartement();
            $this->load->model('master/m_master');
            $this->load->model('m_menu');
            $this->load->model('m_menu2');
            $this->load->model('m_menu3lpmi');
            $this->load->model('m_menuwebdivisi');
            // define config Virtual Account
            if (!defined('VA_client_id')) {
                $getCFGVA = $this->m_master->showData_array('db_va.cfg_bank');
                define('VA_client_id',$getCFGVA[0]['client_id']);
                define('VA_secret_key',$getCFGVA[0]['secret_key']);
                define('VA_url',$getCFGVA[0]['url']);
            }

            $this->rest_setting_global = $this->m_master->showData_array('db_it.rest_setting');

        } else {
            redirect(base_url());
        }

        $this->load->library('JWT');
        $this->load->library('google');
    }

    public function checkMaintenanceMode(){


        // Cek mode
        $dataMode = $this->db->get_where('db_it.m_config',array(
            'ID' => 3
        ))->result_array();

        if($dataMode[0]['MaintenanceMode']=='1'){
            $data['include'] = $this->load->view('template/include','',true);
            $this->load->view('template/maintenance',$data);
        }


    }

    public function decodeToken($token,$key=''){
        $key = ($key == '') ? "UAP)(*" : $key ;
        $decode = $this->jwt->decode($token,$key);
        return $decode;
    }
}

// deklarasi nama function yang akan diimplementasikan ke sub class dibawah nya
abstract class MyAbstract  extends MY_Controller{
   abstract protected function template($content);
   abstract protected function blank_temp($content);
   abstract protected function menu_header();
   abstract protected function menu_navigation();
   abstract protected function crumbs();
   abstract protected function __getDepartement();
   abstract protected function __setDepartement($dpt);
}

abstract class Globalclass extends MyAbstract{

    public function __construct()
    {
        parent::__construct();
    }

    public function template($content,$ClassContainerTemplate = '')
    {

        $data['include'] = $this->load->view('template/include','',true);

        $depertment = $this->__getDepartement();
        if($depertment!=null && $depertment!=''){
            $data['header'] = $this->menu_header();
            $data['navigation'] = $this->menu_navigation();
            $data['crumbs'] = $this->crumbs();
            if (!empty($ClassContainerTemplate)) {
                 $data['ClassContainer'] = $ClassContainerTemplate;
            }
            $data['content'] = $content;
            $this->load->view('template/template',$data);
        } else {
            $this->load->view('template/userfalse',$data);
        }

    }

    public function page404(){
        $data['include'] = $this->load->view('template/include','',true);
        $this->load->view('template/404page',$data);
    }

    public function blank_temp($content){
        $data['include'] = $this->load->view('template/include','',true);
        $data['content'] = $content;


        $this->load->view('template/blank',$data);
    }

    protected function menu_header(){

        $nav_departement['departement'] = $this->__getDepartement();
        $data['page_departement'] = $this->load->view('template/navigation_departement',$nav_departement,true);

        $exp_name = explode(" ",$this->session->userdata('Name'));
        $data['name']= (count($exp_name)>0) ? $exp_name[0] : $this->session->userdata('Name');

        $MainPosition = $this->session->userdata('PositionMain');
        $data['rule_service'] = $this->m_master->__getService($MainPosition['IDDivision']);
        
        $page = $this->load->view('template/header',$data,true);
        return $page;
    }

    protected function menu_navigation(){
        $nav = $this->__getDepartement();
        $data['departement'] = $nav;

        $IDdepartementNavigation = $this->session->userdata('IDdepartementNavigation');
        // Cek apakah mempunyai menu share atau tidak
        $dataMenu = $this->db->query('SELECT sm.* FROM db_it.sm_menu sm 
                                            LEFT JOIN db_it.sm_user smu 
                                            ON (smu.IDSM = sm.ID)
                                            WHERE 
                                            smu.IDDivision = "'.$IDdepartementNavigation.'" ')
                                ->result_array();

        if(count($dataMenu)>0){
            for ($i=0;$i<count($dataMenu);$i++){
                $d = $dataMenu[$i];
                // cek level pertama
                $dataMenu[$i]['DataLevel_1'] = $this->db->get_where('db_it.sm_menu_details',array(
                    'IDSM' => $d['ID']
                ))->result_array();


            }
        }

//        print_r($dataMenu);
//        exit();

        $data['dataMenuShare'] = $dataMenu;
        $data['resetpassmenu'] = $this->db->where('Status', '0')->from('db_it.reset_password')->count_all_results();
        $page = $this->load->view('page/'.$data['departement'].'/menu_navigation',$data,true);
        return $page;
    }

    protected function crumbs(){
        $data['crumbs_departement'] = $this->session->userdata('departementNavigation');
        $data['segment'] = $this->uri->segment_array();
        $page = $this->load->view('template/crumbs',$data,true);
        return $page;
    }


    //==== Get Set ===
    public function __getDepartement(){
        $nav = $this->session->userdata('departementNavigation');
        return $nav;
    }

    public function __setDepartement($dpt)
    {
        $this->session->set_userdata('departementNavigation', ''.$dpt);
    }

    public function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    public function config_pagination_default_ajax($total_rows = 999,$per_page = 10,$uri_segment = 6)
    {
        $config = array();
        $config["base_url"] = "#";
        $config["total_rows"] =  $total_rows;
        $config["per_page"] = $per_page;
        $config["uri_segment"] = $uri_segment;
        $config["use_page_numbers"] = TRUE;
        $config["full_tag_open"] = '<ul class="pagination">';
        $config["full_tag_close"] = '</ul>';
        $config["first_tag_open"] = '<li>';
        $config["first_tag_close"] = '</li>';
        $config["last_tag_open"] = '<li>';
        $config["last_tag_close"] = '</li>';
        $config['next_link'] = '&gt;';
        $config["next_tag_open"] = '<li>';
        $config["next_tag_close"] = '</li>';
        $config["prev_link"] = "&lt;";
        $config["prev_tag_open"] = "<li>";
        $config["prev_tag_close"] = "</li>";
        $config["cur_tag_open"] = "<li class='active'><a href='#'>";
        $config["cur_tag_close"] = "</a></li>";
        $config["num_tag_open"] = "<li>";
        $config["num_tag_close"] = "</li>";
        $config["num_links"] = 4;

        return $config;
    }

    public function registrationListData(){
        $this->load->model('admission/m_admission');
        $requestData= $_REQUEST;
        $reqTahun = $this->input->post('tahun');
        $FormulirType = $this->input->post('FormulirType');
        $StatusPayment = $this->input->post('StatusPayment');
        // print_r($requestData);
        // die();
        $No = $requestData['start'] + 1;
        $totalData = $this->m_admission->getCountAllDataPersonal_Candidate($requestData,$reqTahun,$FormulirType,$StatusPayment);
        $AddWhere = '';
        $AddWhere2 = '';
        if ($FormulirType != '%') {
           $AddWhere .= ' and a.StatusReg = '.$FormulirType.' ';
        }
        $sql = 'select ccc.* from (
                select a.ID as RegisterID,a.Name,a.SchoolID,a.Phone,b.SchoolName,a.Email,a.RegisterAT,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, e.ID as ID_register_formulir,e.UploadFoto,
                xq.DiscountType,
                if(f.Rangking > 0 ,f.Rangking,"-") as Rangking,
                if(
                    (select count(*) as total from db_finance.payment_pre where `Status` = 0 and ID_register_formulir = e.ID limit 1) = 0 ,
                        if((select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID limit 1)
                             > 0 ,"Lunas","-"
                          )
                        ,
                        "Belum Lunas"
                  ) as chklunas,
                (select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID ) as Cicilan
                ,xx.Name as NameSales,
                if(a.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,(select No_Ref from db_admission.formulir_number_online_m where FormulirCode = c.FormulirCode limit 1)  ) as No_Ref,a.StatusReg,
                if(
                  (select count(*) as total from db_finance.payment_pre where `Status` = 1 and ID_register_formulir = e.ID ) > 0,"Intake","Not Intake"
                ) as CekIntake,
                if(
                  (select count(*) as total from db_finance.register_refund where ID_register_formulir = e.ID ) > 0,"Refund",""
                ) as CekRefund,
                ra.Pay_Cond,ra.PaymentShow,ra.PaymentShowTextMSG
                from db_admission.register as a
                LEFT join db_admission.school as b
                on a.SchoolID = b.ID
                LEFT JOIN db_admission.register_verification as z
                on a.ID = z.RegisterID
                LEFT JOIN db_admission.register_verified as c
                on z.ID = c.RegVerificationID
                LEFT JOIN db_admission.register_formulir as e
                on c.ID = e.ID_register_verified
                LEFT join db_academic.program_study as d
                on e.ID_program_study = d.ID
                LEFT join db_admission.register_rangking as f
                on e.ID = f.ID_register_formulir
                left join db_admission.sale_formulir_offline as xz
                  on c.FormulirCode = xz.FormulirCodeOffline
                LEFT JOIN db_employees.employees as xx
                on xz.PIC = xx.NIP
                LEFT JOIN db_finance.register_admisi as xy
                on e.ID = xy.ID_register_formulir
                LEFT JOIN db_admission.register_dsn_type_m as xq
                on xq.ID = xy.TypeBeasiswa
                LEFT JOIN db_finance.register_admisi as ra on ra.ID_register_formulir = e.ID
                where a.SetTa = "'.$reqTahun.'" '.$AddWhere.'
              ) ccc
            ';
        if ($StatusPayment != '%') {
          if ($StatusPayment == '-100') {
            
            $AddWhere2 .= ' and (FormulirCode = "" or FormulirCode is NULL ) ';
          }
          elseif ($StatusPayment == '100') {
             $AddWhere2 .= ' and FormulirCode != "" and FormulirCode is not NULL  ';
          }
          elseif ($StatusPayment == 'Refund') {
             $AddWhere2 .= ' and CekRefund = "Refund"  ';
          }
          elseif($StatusPayment == 'Intake')
          {
              // $AddWhere2 .= ' and CekIntake = "Intake"  ';
              $AddWhere2 .= ' and CekIntake = "Intake" and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
                join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
                join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
                join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
                join db_admission.register as reg on reg.ID = rve.RegisterID
                where reg.SetTa = "'.$reqTahun.'"

              ) ';
          }
          else{
            $AddWhere2 .= ' and chklunas = "'.$StatusPayment.'" ';
          }
        }    
        $sql.= ' where ( Name LIKE "'.$requestData['search']['value'].'%" or NamePrody LIKE "%'.$requestData['search']['value'].'%"
                or FormulirCode LIKE "'.$requestData['search']['value'].'%" or SchoolName LIKE "%'.$requestData['search']['value'].'%"
                #or chklunas LIKE "'.$requestData['search']['value'].'%" 
                or DiscountType LIKE "'.$requestData['search']['value'].'%"
                or NameSales LIKE "'.$requestData['search']['value'].'%"
                or No_Ref LIKE "'.$requestData['search']['value'].'%" )
                '.$AddWhere2.'
                ';
        $sql.= ' ORDER BY chklunas ASC, RegisterID DESC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            // online or offline
            $stFormulirAct = '<i class="fa fa-circle" style="color:#bdc80e;"></i>'; // online
            if ($row['StatusReg'] == 1) {
              $stFormulirAct = '<i class="fa fa-circle" style="color:#db4273;"></i>'; // offline
            }
            
            $Code = ($row['No_Ref'] != "") ? $row['FormulirCode'].' / '.$row['No_Ref'] : $row['FormulirCode'];
            $nestedData[] = $No;

            $nestedData[] = $this->m_master->setBintang_HTML($row['Pay_Cond']).'<br/>'.$row['Name'].'<br>'.$row['Email'].'<br>'.$row['Phone'].'<br>'.$row['SchoolName'].'<br/>'.$stFormulirAct;
            $nestedData[] = $row['NamePrody'].'<br>'.$Code.'<br>'.$row['VA_number'];
            $nestedData[] = $row['NameSales'];
            $nestedData[] = $row['Rangking'];
            $nestedData[] = $row['DiscountType'];
            $nestedData[] = '<button class="btn btn-inverse btn-notification btn-show" id-register-formulir = "'.$row['ID_register_formulir'].'" email = "'.$row['Email'].'" Nama = "'.$row['Name'].'">Show</button>';
            // get tagihan
            $getTagihan = $this->m_admission->getPaymentType_Cost_created($row['ID_register_formulir']);
            $tagihan = '';
            for ($j=0; $j < count($getTagihan); $j++) {
                $tagihan .= $getTagihan[$j]['Abbreviation'].' : '.'Rp '.number_format($getTagihan[$j]['Pay_tuition_fee'],2,',','.').'<br>';
            }

            $nestedData[] = $tagihan;
            $cicilan = '';
            if ($row['Cicilan'] == 0) {
              $cicilan = '-';
            }
            elseif ($row['Cicilan'] == 1) {
               $cicilan = '1x Pembayaran'.'<br><button class = "btn btn-primary btn-payment" id-register-formulir = "'.$row['ID_register_formulir'].'" Nama = "'.$row['Name'].'">Detail</button>';
             }
             elseif ($row['Cicilan'] > 1) {
               $cicilan = $row['Cicilan'].'x Pembayaran'.'<br><button class = "btn btn-primary btn-payment" id-register-formulir = "'.$row['ID_register_formulir'].'" Nama = "'.$row['Name'].'">Detail</button>';
             }

             // check refund or not
               if (!empty($row['CekRefund'])) {
                 $cicilan .= '<br/><br/><label style = "color:red;">Refund</label>';
               }

            $nestedData[] = $cicilan;
            $nestedData[] = $row['chklunas'];
            $nestedData[] = $row['RegisterAT'];

            $actLogin = '<a href="javascript:void(0)" class ="btnLoginPortalRegister" data-xx="'.$row['Email'].'" data-xx2="'.$row['FormulirCode'].'">Login portal</a>';
            $actDetailResendEmail = '<a href="javascript:void(0)" class = "btnDetaiLResendEmail" RegisterID = "'.$row['RegisterID'].'"> Detail Resend Email</a>';
            $actResendEmail = '';
            $actSetTahun = '';
            if ( ($row['FormulirCode'] == '' || $row['FormulirCode'] == null || empty($row['FormulirCode'])) && $row['StatusReg'] == 0 ) {
              // show email to resend in html
              $DataEmailSend = $this->m_admission->DataEmailSend($row['RegisterID']);
              $actResendEmail = '<a href="javascript:void(0)" class = "btnResendEmail" RegisterID = "'.$row['RegisterID'].'" DataEmailSend = "'.$DataEmailSend.'">Resend Email</a>';
              $actSetTahun = '<a href="javascript:void(0)" class = "btnSetTahun" RegisterID = "'.$row['RegisterID'].'" data = "'.$row['Name'].' || '.$row['Email'].'" >Set Tahun</a>';

            }

            $actionCol = '<div class="btn-group">
                            <button type="button" class="btn btn-sm btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            <i class="fa fa-edit"></i> <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                              <li>'.$actLogin.'</li>
                              <li role="separator" class="divider"></li>
                              <li>'.$actDetailResendEmail.'</li>
                              <li role="separator" class="divider"></li>
                              <li>'.$actResendEmail.'</li>
                              <li role="separator" class="divider"></li>
                              <li>'.$actSetTahun.'</li>
                            </ul>
                          </div>


            '; 

             $nestedData[] = $actionCol;
             $nestedData['dataToken'] = $this->jwt->encode($row,"UAP)(*");

            $data[] = $nestedData;
            $No++;
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

}

class Academic_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
//        print_r('ok');exit;
        $this->m_menu2->set_model('academic_sess','auth_academic_sess','menu_academic_sess','menu_academic_grouping','db_academic');
    }
}

class HR_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
    }
}

abstract class Student_Life extends Globalclass{
    public function __construct()
    {
        parent::__construct();
    }
}

abstract class Lpmi_Controler extends Globalclass{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('lpmi/m_lpmi');
        $this->m_menu3lpmi->set_model('lpmi_sess','auth_lpmi_sess','menu_lpmi_sess','menu_lpmi_grouping','db_lpmi');
    }
}

abstract class Lppm_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
        // $this->load->model('lpmi/m_lpmi');
        $this->m_menu3lpmi->set_model('lpmi_sess','auth_lpmi_sess','menu_lpmi_sess','menu_lpmi_grouping','db_lpmi');
    }

    public function temp($content)
    {
        $this->template($content);
    }

    // overide function
    public function template($content,$ClassContainerTemplate = '')
    {

        $data['include'] = $this->load->view('template/include','',true);

        $data['header'] = $this->menu_header();
        $data['navigation'] = $this->menu_navigation();
        $data['crumbs'] = $this->crumbs();

        $data['content'] = $content;
        $this->load->view('template/template',$data);

    }

    // overide function
    public function  menu_navigation(){
        $data['departement'] = $this->__getDepartement();
        $page = $this->load->view('page/lppm/menu_navigation','',true);
        return $page;
    } 
} 

abstract class Library extends Globalclass{
    public function __construct()
    {
        parent::__construct();
    }
}

abstract class Admission_Controler extends Globalclass{
    public $GlobalVariableAdi = array('url_registration' => 'http://demo.web.podomorouniversity.ac.id/registeronline/');
    public $GlobalData = array('NameMenu' => '');

    public function __construct()
    {
        parent::__construct();
        $this->m_menu->set_model('admission_sess','auth_admission_sess','menu_admission_sess','menu_admission_grouping','db_admission');

        $this->GetNameMenu();
    }

    public $path_upload_regOnline = path_register_online.'document/';

    private function GetNameMenu()
    {
        $currentURL = current_url();
        $Slug = str_replace(serverRoot.'/', '', $currentURL);
        $get = $this->m_master->caribasedprimary('db_admission.cfg_sub_menu','Slug',$Slug);
        if (count($get) > 0) {
            if ($get[0]['SubMenu2'] == 'Empty') {
                $this->GlobalData['NameMenu'] = $get[0]['SubMenu1'];
            }
            else
            {
                $this->GlobalData['NameMenu'] = $get[0]['SubMenu1'].'-'.$get[0]['SubMenu2'];
            }
            
        }
    }

    public function temp($content,$ClassContainer = '')
    {
        $this->template($content,$ClassContainer);
    }


    // overide function
    // public function template($content)
    // {

    //     $data['include'] = $this->load->view('template/include','',true);

    //     $data['header'] = $this->menu_header();
    //     $data['navigation'] = $this->menu_navigation();
    //     $data['crumbs'] = $this->crumbs();

    //     $data['content'] = $content;
    //     $this->load->view('template/template',$data);

    // }

    // // overide function
    // public function  menu_navigation(){
    //     $data['departement'] = $this->__getDepartement();
    //     $page = $this->load->view('page/'.$data['departement'].'/menu_navigation','',true);
    //     return $page;
    // }

    public function auth_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
    }

}


abstract class Finnance_Controler extends Globalclass{
    public $GlobalVariableAdi = array('url_registration' => 'http://demo.web.podomorouniversity.ac.id/registeronline/');

    public function __construct()
    {
        parent::__construct();
        // save session using VA or NOT
        $this->get_PolicySYS();
    }

    public function get_PolicySYS()
    {
        if (!$this->session->userdata('finance_auth_Policy_SYS')) {
            $get = $this->m_master->showData_array('db_finance.cfg_policy_sys');
            $this->session->set_userdata('finance_auth_Policy_SYS',$get[0]['VA_active']);
        }
    }


}


abstract class Vreservation_Controler extends Globalclass{

    public $data = array();

    public function __construct()
    {
        parent::__construct();
        $this->load->model('vreservation/m_reservation');
        if (!$this->session->userdata('auth_vreservation_sess')) {
            $this->getAuthVreservation();
        }
        // read policy
        $dayPolicy = $this->session->userdata('V_BookingDay');
        $dateDay = date('Y-m-d');
        $dateDay = date('Y-m-d', strtotime($dateDay . ' +'.$dayPolicy.' day'));
        $this->data['dateDay'] = $dateDay;
        $this->data['dayPolicy'] = $dayPolicy;
    }

    public $pathView = 'page/vreservation/';

    public function temp($content)
    {
        $this->template($content);
    }


    // overide function
    public function template($content,$ClassContainerTemplate = '')
    {

        $data['include'] = $this->load->view('template/include','',true);

        $data['header'] = $this->menu_header();
        $data['navigation'] = $this->menu_navigation();
        $data['crumbs'] = $this->crumbs();

        $data['content'] = $content;
        $this->load->view('template/template',$data);

    }

    // overide function
    public function  menu_navigation(){
        $data['departement'] = $this->__getDepartement();
        $page = $this->load->view('page/vreservation/menu_navigation','',true);
        return $page;
    }

    private function getAuthVreservation()
    {
        $data = array();
        $getDataMenu = $this->m_master->getMenuGroupUser($this->session->userdata('NIP'),'db_reservation');
        $data_sess = array();
        if (count($getDataMenu) > 0) {
            $this->session->set_userdata('auth_vreservation_sess',1);
            $this->session->set_userdata('menu_vreservation_sess',$getDataMenu);
            $this->session->set_userdata('menu_vreservation_grouping',$this->groupBYMenu_sess());
            // save session group user
            $this->m_reservation->save_sess_policy_grouping();
        }
    }

    public function groupBYMenu_sess()
    {
        $DataDB = $this->session->userdata('menu_vreservation_sess');
        $arr = array();
        for ($i=0; $i < count($DataDB); $i++) {
            $submenu1 = $this->m_menu->getSubmenu1BaseMenu_grouping($DataDB[$i]['ID_menu'],'db_reservation');
            $arr2 = array();
            for ($k=0; $k < count($submenu1); $k++) { 
                $submenu2 = $this->m_menu->getSubmenu2BaseSubmenu1_grouping($submenu1[$k]['SubMenu1'],'db_reservation',$DataDB[$i]['ID_menu']);
                $arr2[] = array(
                    'SubMenu1' => $submenu1[$k]['SubMenu1'],
                    'Submenu' => $submenu2,
                );
            }

            $arr[] =array(
                'Menu' => $DataDB[$i]['Menu'],
                'Icon' => $DataDB[$i]['Icon'],
                'Submenu' => $arr2

            );
            
        }
        return $arr;
    }

    public function auth_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
    }

    public function checkAuth_user()
    {
        $base_url = base_url();
        $currentURL = current_url();
        $getURL = str_replace($base_url,"",$currentURL);
        $chk = $this->m_reservation->chkAuthDB_Base_URL_vreservation($getURL);

        if (!$this->input->is_ajax_request()) {
            if (count($chk) == 0) {
                show_404($log_error = TRUE); 
            }
            else
            {
                if ($chk[0]['read'] == 0) {
                   show_404($log_error = TRUE); 
                }
            }
            
        }
        
    }

}


abstract class Budgeting_Controler extends Globalclass{

    public $data = array();


    public function __construct()
    {
        parent::__construct();
        $this->load->model('budgeting/m_budgeting');
        $this->load->model('budgeting/m_global');
        $this->load->model('budgeting/m_pr_po');
        $this->load->model('budgeting/m_spb');

        $this->session->unset_userdata('auth_budgeting_sess');
        $this->session->unset_userdata('menu_budgeting_sess');
        $this->session->unset_userdata('menu_budgeting_grouping');
        
        $PositionMain = $this->session->userdata('PositionMain');
        $DivisionPage = $PositionMain['Division'];
        $IDPosition = $PositionMain['IDPosition'];
        $G_departementNavigation = $this->m_master->caribasedprimary('db_employees.division','ID',$this->session->userdata('IDdepartementNavigation'));
        //print_r($this->session->userdata('IDdepartementNavigation'));die();
        $departementNavigation = $G_departementNavigation[0]['Division'];
        $this->data['department'] = ($PositionMain['IDDivision'] == 12 || $IDPosition <= 6)? $departementNavigation : $DivisionPage;
        $this->data['department'] = strtolower($this->data['department']);
        $this->data['department'] = str_replace(" ", "-", $this->data['department']);
        $this->data['IDdepartment'] = $PositionMain['IDDivision'];
        // set session division 
        $this->session->set_userdata('IDDepartement',$PositionMain['IDDivision']);

        // adding menu department
        // $MenuDepartement= ($this->data['IDdepartment'] == 12) ? 'NA.'.$this->session->userdata('IDdepartementNavigation'):'NA.'.$this->data['IDdepartment']; 
        $MenuDepartement= 'NA.'.$this->session->userdata('IDdepartementNavigation'); 

        if ($this->session->userdata('IDdepartementNavigation') == 15 || $this->session->userdata('IDdepartementNavigation') == 14) {
            $MenuDepartement= 'AC.'.$this->session->userdata('prodi_active_id');
        }

        if ($MenuDepartement == 'NA.34') {
            $MenuDepartement = 'FT.'.$this->session->userdata('faculty_active_id');
        }

        $this->getAuthSession($MenuDepartement);
    }

    public function temp($content)
    {
        $this->template($content);
    }


    // overide function
    public function template($content,$ClassContainerTemplate = '')
    {

        $data['include'] = $this->load->view('template/include','',true);

        $data['header'] = $this->menu_header();
        $data['navigation'] = $this->menu_navigation();
        $data['crumbs'] = $this->crumbs();

        $data['content'] = $content;
        $this->load->view('template/template',$data);

    }

    // overide function
    public function  menu_navigation(){
        $data['departement'] = $this->__getDepartement();
        $page = $this->load->view('global/budgeting/menu_navigation','',true);
        return $page;
    }

    public function auth_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
    }

    public function getAuthSession($MenuDepartement)
    {
        $data = array();
        if ($MenuDepartement == 'NA.15' || $MenuDepartement == 'NA.14') {
            $MenuDepartement = 'AC.'.$this->session->userdata('prodi_active_id');
        }

        if ($MenuDepartement == 'NA.34') {
            $MenuDepartement = 'FT.'.$this->session->userdata('faculty_active_id');
        }

        if ($MenuDepartement == 'NA.36') { // other division
            $PositionMain = $this->session->userdata('PositionMain');
            $MenuDepartement = 'NA.'.$PositionMain['IDDivision'];
        }

        $getDataMenu = $this->m_budgeting->getMenuGroupUser($this->session->userdata('NIP'),$MenuDepartement);
        $this->session->set_userdata('IDDepartementPUBudget',$MenuDepartement);
        // print_r($MenuDepartement);die();
        $data_sess = array();
        if (count($getDataMenu) > 0) {
            $this->session->set_userdata('auth_budgeting_sess',1);
            $this->session->set_userdata('menu_budgeting_sess',$getDataMenu);
            $this->session->set_userdata('menu_budgeting_grouping',$this->groupBYMenu_sess());
            // $this->session->set_userdata('role_user_budgeting',$this->m_budgeting->role_user_budgeting());
        }
    }

    public function groupBYMenu_sess()
    {
        $DataDB = $this->session->userdata('menu_budgeting_sess');
        $arr = array();
        for ($i=0; $i < count($DataDB); $i++) {
            $submenu1 = $this->m_budgeting->getSubmenu1BaseMenu_grouping($DataDB[$i]['ID_menu'],'db_budgeting');
            // print_r($submenu1);
            $arr2 = array();
            for ($k=0; $k < count($submenu1); $k++) { 
                $submenu2 = $this->m_budgeting->getSubmenu2BaseSubmenu1_grouping($submenu1[$k]['SubMenu1'],'db_budgeting',$DataDB[$i]['ID_menu']);
                $arr2[] = array(
                    'SubMenu1' => $submenu1[$k]['SubMenu1'],
                    'Submenu' => $submenu2,
                );
            }

            $arr[] =array(
                'Menu' => $DataDB[$i]['Menu'],
                'Icon' => $DataDB[$i]['Icon'],
                'Submenu' => $arr2

            );
            
        }
        // print_r($arr);die();
        return $arr;
    }

}

abstract class Purchasing_Controler extends Globalclass{

    public $data = array();


    public function __construct()
    {
        parent::__construct();

        // add session department budgeting
        $PositionMain = $this->session->userdata('PositionMain');
        $this->session->set_userdata('IDDepartement',$PositionMain['IDDivision']);
        $this->data['IDdepartment'] = $PositionMain['IDDivision'];
        // adding menu department
        $IDDepartementPUBudget= ($PositionMain['IDDivision']== 12) ? 'NA.'.$this->session->userdata('IDdepartementNavigation'):'NA.'.$PositionMain['IDDivision']; 
        $this->session->set_userdata('IDDepartementPUBudget',$IDDepartementPUBudget);
        $this->m_menu2->set_model('purchasing_sess','auth_purchasing_sess','menu_purchasing_sess','menu_purchasing_grouping','db_purchasing');
    }

    public function temp($content)
    {
        $this->template($content);
    }


    // overide function
    // public function template($content)
    // {

    //     $data['include'] = $this->load->view('template/include','',true);

    //     $data['header'] = $this->menu_header();
    //     $data['navigation'] = $this->menu_navigation();
    //     $data['crumbs'] = $this->crumbs();

    //     $data['content'] = $content;
    //     $this->load->view('template/template',$data);

    // }

    // // overide function
    // public function  menu_navigation(){
    //     $data['departement'] = $this->__getDepartement();
    //     $page = $this->load->view('page/'.$data['departement'].'/menu_navigation','',true);
    //     return $page;
    // }

    public function auth_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
    }

}

class Transaksi_Controler extends Purchasing_Controler{
    public function __construct()
    {
        parent::__construct();
    }

    public function page_po($page)
    {
      $page['department'] = parent::__getDepartement();
      $content = $this->load->view('page/'.$page['department'].'/transaksi/po/page',$page,true);
      $this->temp($content);
    }
}


abstract class It_Controler extends Globalclass{
    public $data = array();

    public function __construct()
    {
        parent::__construct();
        $this->m_menu2->set_model('it_sess','auth_it_sess','menu_it_sess','menu_it_grouping','db_it');
    }

    // public function temp($content)
    // {
    //     $this->template($content);
    // }

    public function temp($content,$ClassContainer = '')
    {
        $this->template($content,$ClassContainer);
    }

    public function auth_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
    }


    // overide function
    // public function template($content)
    // {

    //     $data['include'] = $this->load->view('template/include','',true);

    //     $data['header'] = $this->menu_header();
    //     $data['navigation'] = $this->menu_navigation();
    //     $data['crumbs'] = $this->crumbs();

    //     $data['content'] = $content;
    //     $this->load->view('template/template',$data);

    // }

    // // overide function
    // public function  menu_navigation(){
    //     $data['departement'] = $this->__getDepartement();
    //     $page = $this->load->view('page/'.$data['departement'].'/menu_navigation','',true);
    //     return $page;
    // }

}


abstract class Prodi_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('prodi/m_prodi');
        if (!$this->session->userdata('prodi_get')) {
          $this->m_prodi->auth();  
        }
        $this->m_menu2->set_model('prodi_sess','auth_prodi_sess','menu_prodi_sess','menu_prodi_grouping','db_prodi');
    }

}

abstract class Webdivisi_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('webdivisi/m_webdivisi');
        if (!$this->session->userdata('webdivisi_get')) {
          $this->m_webdivisi->auth();  
        }
        $this->m_menuwebdivisi->set_model('webdivisi_sess','auth_webdivisi_sess','menu_webdivisi_sess','menu_webdivisi_grouping','db_webdivisi');
    }

}

abstract class Ga_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('m_sendemail');
        $this->load->model('vreservation/m_reservation');
    }

    public function auth_ajax()
    {
        if (!$this->input->is_ajax_request()) {
            exit('No direct script access allowed');
        }
    }

}

abstract class Cooperation_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
    }

    public function temp($content)
    {
        parent::template($content);
    }
}

abstract class Ticket_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('ticketing/m_general');
    }

    public function temp($content)
    {
        $this->template($content);
    }

    public function menu_ticket($page){
        $data['Authen'] = $this->m_master->showData_array('db_ticketing.rest_setting');
        $data['DepartmentID'] = $this->m_general->getDepartmentNow();
        $data['DepartmentAbbr'] = $this->m_general->DepartmentAbbr($data['DepartmentID']);
        $data['ArrSelectOptionDepartment'] = $this->m_general->getAuthDepartment();
        $data['authTicketDashboard'] = (count($this->m_master->caribasedprimary('db_ticketing.auth_dashboard','NIP',$this->session->userdata('NIP')))> 0 ) ? '' : 'hide'; 
        $data['page'] = $page;
        $content = $this->load->view('dashboard/ticketing/menu_ticketing',$data,true);
        $this->template($content);
    }

    // overide function
    public function template($content,$ClassContainer = '')
    {

        $data['include'] = $this->load->view('template/include','',true);
        $data['header'] = $this->menu_header();
        $data['navigation'] = $this->menu_navigation();
        $data['crumbs'] = $this->crumbs();
        $data['ClassContainer'] = 'sidebar-closed';
        $data['content'] = $content;
        $this->load->view('template/template',$data);

    }


}

abstract class ServiceDocumentGenerator_Controler extends Globalclass{ // for services
    public $data = array();
    
    public function __construct()
    {
        parent::__construct();
        $this->load->model('document-generator/m_doc');
        $this->__setDepartment();
        // get auth Department
        $this->__authDepartment();
    }

    public function __authDepartment(){
        $this->load->model('document-generator/m_auth');
        $this->data['auth'] = $this->m_auth->__authDepartment();
    }

    public function __setDepartment(){
        $this->load->model('ticketing/m_general');
        if (!$this->session->userdata('DepartmentIDDocument')) {
            $DepartmentID= $this->m_general->getDepartmentNow();
            $this->__setDepartmentSession($DepartmentID);
        }
    }

    public function __setDepartmentSession($DepartmentID){
        $this->session->set_userdata('DepartmentIDDocument',$DepartmentID);
    }

    public function temp($content)
    {
        $this->template($content);
    }

    // overide function
    public function template($content,$ClassContainer = '')
    {
        $data['include'] = $this->load->view('template/include','',true);
        $data['header'] = $this->menu_header();
        $data['navigation'] = $this->menu_navigation();
        $data['crumbs'] = $this->crumbs();
        $data['ClassContainer'] = 'sidebar-closed';
        $data['content'] = $content;
        $this->load->view('template/template',$data);
    }

    public function menu_document($page){
        $this->data['page'] = $page;
        $content = $this->load->view('global/request-document-generator/menu_document',$this->data,true);
        $this->temp($content);
    }

}

abstract class Research_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
        $this->load->model('research/m_research');
    }

    // overide function
    public function template($content,$ClassContainer = '')
    {
        $data['include'] = $this->load->view('template/include','',true);
        $data['header'] = $this->menu_header();
        $data['navigation'] = $this->menu_navigation();
        $data['crumbs'] = $this->crumbs();
        $data['ClassContainer'] = 'sidebar-closed';
        $data['content'] = $content;
        $this->load->view('template/template',$data);
    }

    public function temp($content)
    {
        $this->template($content);
    }

    public function menu_portal_eksternal($page){
        $data['page'] = $page;
        $data['department'] = parent::__getDepartement();
        $data['rest_setting_global'] = $this->rest_setting_global;
        $content = $this->load->view('page/research/portal_eksternal/menu_portal_eksternal',$data,true);
        $this->temp($content);
    }
}

abstract class Abdimas_Controler extends Globalclass{

    public function __construct()
    {
        parent::__construct();
    }

    // overide function
    public function template($content,$ClassContainer = '')
    {
        $data['include'] = $this->load->view('template/include','',true);
        $data['header'] = $this->menu_header();
        $data['navigation'] = $this->menu_navigation();
        $data['crumbs'] = $this->crumbs();
        $data['ClassContainer'] = 'sidebar-closed';
        $data['content'] = $content;
        $this->load->view('template/template',$data);
    }

    public function temp($content)
    {
        $this->template($content);
    }
}