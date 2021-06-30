
<?php
defined('BASEPATH') OR exit('No direct script access allowed');
/**
 * Created by PhpStorm.
 * User: Nandang
 * Date: 12/20/2017
 * Time: 1:41 PM
 */
include_once APPPATH.'vendor/autoload.php';
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;

class C_login extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->library('JWT');
        $this->load->library('google');
        $this->load->model('m_auth');
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model('master/m_master');
        // define config Virtual Account
        if (!defined('VA_client_id')) {
            $getCFGVA = $this->m_master->showData_array('db_va.cfg_bank');
            define('VA_client_id',$getCFGVA[0]['client_id']);
            define('VA_secret_key',$getCFGVA[0]['secret_key']);
            define('VA_url',$getCFGVA[0]['url']);
        }

    }

    public function page404r(){
        $data['include'] = $this->load->view('template/include','',true);
        $this->load->view('template/404page',$data);
    }

    public function temp($content){

        $data['include'] = $this->load->view('template/include','',true);
        $data['content'] = $content;


        $this->load->view('template/blank',$data);
    }

    public function index()
    {
        redirect(url_sign_out);
//        $data['loginURL'] = $this->google->loginURL();
//        $content = $this->load->view('auth/login',$data,true);
//        $this->temp($content);
    }

    // LOGIN FROM PORAL
    public function portal4SignIn(){
        $token = $this->input->post('token');
        $key = "s3Cr3T-G4N";
        $data_arr = (array) $this->jwt->decode($token,$key);

        $dateNow = date("Y-m-d");

        if($dateNow==$data_arr['dueDate']){
            $dataEmp = $this->db->select('ID,NIP')->get_where('db_employees.employees',
                array('NIP'=>$data_arr['Username'],'Password'=>$data_arr['Token']),1)->result_array();

            if(count($dataEmp)>0){

                // Set Last Login
                $dateTimeNow = date("Y-m-d H:i:s");
                $this->db->set('LastLogin', $dateTimeNow);
                $this->db->where('ID', $dataEmp[0]['ID']);
                $this->db->update('db_employees.employees');

                $this->setSession($dataEmp[0]['ID'],$dataEmp[0]['NIP']);
                if($data_arr['TypeUser']=='i'){
                    redirect(base_url('invigilator'));
                } else {
                    redirect(base_url('dashboard'));
                }

            } else {
//            echo 'gagal login son';
                redirect(url_sign_out);
            }
        } else {
            redirect(url_sign_out);
        }


    }

    public function authGoogle(){
        if(isset($_GET['code'])){

            try{
                //authenticate user
                $this->google->getAuthenticate();

                //get user info from google
                $gpInfo = $this->google->getUserInfo();

                //preparing data for database insertion
                $userData['oauth_provider'] = 'google';
                $userData['oauth_uid'] 		= $gpInfo['id'];
                $userData['first_name'] 	= $gpInfo['given_name'];
                $userData['last_name'] 		= $gpInfo['family_name'];
                $userData['email'] 			= $gpInfo['email'];
                $userData['gender'] 		= !empty($gpInfo['gender'])?$gpInfo['gender']:'';
                $userData['locale'] 		= !empty($gpInfo['locale'])?$gpInfo['locale']:'';
                $userData['profile_url'] 	= !empty($gpInfo['link'])?$gpInfo['link']:'';
                $userData['picture_url'] 	= !empty($gpInfo['picture'])?$gpInfo['picture']:'';


                // Cek Userdata
                $dataUser = $this->m_auth->__getUserByEmailPU($userData['email'] );

                if(count($dataUser)>0) {
                    $this->setSession($dataUser[0]['ID'],$dataUser[0]['NIP']);
                    redirect(base_url('dashboard'));
                } else {
                    redirect(base_url());
                }

            } catch (Exception $err){
                redirect(base_url());
            }


        }
    }

    public function authUserPassword(){
        $token = $this->input->post('token');
        $key = "L06M31N";
        $data_arr = (array) $this->jwt->decode($token,$key);

        if(count($data_arr)>0){

            $NIP = $data_arr['nip'];
            $Password = $this->genratePassword($NIP,$data_arr['password']);

            $dataUser = $this->m_auth->__getauthUserPassword($NIP,$Password);

            if(count($dataUser)>0){
                $this->setSession($dataUser[0]['ID'],$dataUser[0]['NIP']);
                return print_r(1);
            } else {
                return print_r(0);
            }
        } else {
            return print_r(0);
        }
    }

    public function resetPasswordUser(){
        $this->load->model('m_sendemail');

        $token = $this->input->get('token');
        $flag = $this->input->get('f');
        $daten = $this->input->get('d');

        if($flag=='st'){
            $dataUser = $this->db->query('SELECT s.NPM AS Username, s.EmailPU FROM db_academic.auth_students s 
                      WHERE s.Password = "'.$token.'"  LIMIT 1')->result_array();
        } else {
            $dataUser = $this->db->query('SELECT em.NIP AS Username, em.EmailPU FROM db_employees.employees em 
                      WHERE em.Password = "'.$token.'" LIMIT 1')->result_array();
        }

        $res=0;
        if(count($dataUser)>0){

            $dataT = array(
                'Username' => $dataUser[0]['Username'],
                'TokenUser' => $token,
                'Flag' => $flag
            );
            $token2 = $this->jwt->encode($dataT,'chPass');

            $to = $dataUser[0]['EmailPU'];
            $subject = 'Reset Password - Podomoro University '.$daten;
            $text = 'Dear <strong style="color: blue;">'.$to.'</strong>,
                <p style="color: #673AB7;">To reset your password on <strong>Portal Podomoro University</strong>, please Click the link below, 
                and your account will be activated instantly</p>
                <table width="178" cellspacing="0" cellpadding="12" border="0">
                    <tbody>
                    <tr>
                        <td bgcolor="#ff9000" align="center">
                            <a href="'.url_sign_out.'resetPasswordpage?token='.$token2.'&f='.$flag.'" style="font:bold 16px/1 Helvetica,Arial,sans-serif;color:#ffffff;text-decoration:none;background-color:#ff9000" target="_blank" >Reset password &#187;</a>
                        </td>
                    </tr>
                    </tbody>
                </table>';
            $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);

            $res=1;
        }

        return print_r($res);

    }

    public function callback()
    {
        include_once APPPATH.'third_party/bni/BniEnc.php';
        // FROM BNI
        $client_id = VA_client_id;
        $secret_key = VA_secret_key;

        // URL utk simulasi pembayaran: http://dev.bni-ecollection.com/


        $data = file_get_contents('php://input');

        $data_json = json_decode($data, true);

        if (!$data_json) {
            // handling orang iseng
            echo '{"status":"999","message":"jangan iseng :D"}';
        }
        else {
            if ($data_json['client_id'] === $client_id) {
                $data_asli = BniEnc::decrypt(
                    $data_json['data'],
                    $client_id,
                    $secret_key
                );

                if (!$data_asli) {
                    // handling jika waktu server salah/tdk sesuai atau secret key salah
                    echo '{"status":"999","message":"waktu server tidak sesuai NTP atau secret key salah."}';
                }
                else {
                    // content berhasil
                    // $this->load->model('master/m_master');
                    $this->load->model('finance/m_finance');
                    $this->load->model('m_sendemail');
                    $BNIdbLog = $this->m_master->caribasedprimary('db_va.va_log','trx_id',$data_asli['trx_id']);
                    $routes_table =$BNIdbLog[0]['routes_table'];
                    switch ($routes_table) {
                        case 'db_admission.register':
                            if ($BNIdbLog[0]['Status'] == 0) {
                                $getData = $this->m_master->caribasedprimary('db_admission.register','BilingID',$data_asli['trx_id']);
                                $Email = $getData[0]['Email'];
                                $RegisterID = $getData[0]['ID'];
                                $this->m_master->saveDataToVerification_offline($RegisterID);
                                $getData = $this->m_master->caribasedprimary('db_admission.register_verification','RegisterID',$RegisterID);
                                $RegVerificationID = $getData[0]['ID'];
                                $FormulirCode = $this->m_finance->getFormulirCode('online');
                                // save data to register_verified
                                $this->m_master->saveDataRegisterVerified($RegVerificationID,$FormulirCode);

                                $text = 'Dear Candidate,<br><br>
                                              Your payment has been received,<br>
                                              Please click link below to login your portal <br>
                                              '.url_registration."login/".'
                                          ';
                                $to = $Email;
                                $subject = "Podomoro University Link Formulir Registration";
                                $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                                $this->m_master->update_va_log($data_asli);
                            }
                            echo '{"status":"000"}';
                            exit;
                            break;
                        case 'db_finance.payment_pre':
                            $getData = $this->m_master->caribasedprimary('db_admission.register','VA_number',$BNIdbLog[0]['virtual_account']);
                            $Email = $getData[0]['Email'];
                            $RegisterID = $getData[0]['ID'];
                            $Name = $getData[0]['Name'];

                            $getData2 = $this->m_master->caribasedprimary('db_finance.payment_pre','BilingID',$data_asli['trx_id']);
                            $ID_register_formulir = $getData2[0]['ID_register_formulir'];

                            if ($getData2[0]['Status'] == 0) {
                                //update data telah dibayar
                                $this->m_finance->update_payment_pre($data_asli['trx_id']);

                                // proses cicilan atau generate va kembali
                                $p = $this->m_finance->proses_cicilan($ID_register_formulir,$getData);

                                $text = 'Dear '.$Name.',<br><br>
                                                   Your payment has been received,<br>
                                                   as much as Rp '.number_format($getData2[0]['Invoice'],2,',','.').'
                                                   <br>'.$p.'
                                               ';
                                $to = $Email;
                                $subject = "Podomoro University Payment thank you";
                                $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                                $this->m_master->update_va_log($data_asli);
                            }
                            echo '{"status":"000"}';
                            exit;
                            break;
                        case 'db_finance.payment_students':

                            // Get Status sudah bayar atau belum karena BNi hit lebih dari satu kali
                            $getData = $this->m_master->caribasedprimary('db_finance.payment_students','BilingID',$data_asli['trx_id']);
                            if ($getData[0]['Status'] == 0) {
                                // get Informasi Mahasiswa
                                $GetPayment = $this->m_master->caribasedprimary('db_finance.payment','ID',$getData[0]['ID_payment']);
                                $NPM = $GetPayment[0]['NPM'];
                                $data = $this->m_master->PaymentgetMahasiswaByNPM($NPM);
                                $PTIDDesc = $data['PTIDDesc'];
                                $SemesterName = $data['SemesterName'];
                                $Nama = $data['Nama'];
                                $EmailPU = $data['EmailPU'];
                                $ProdiEng = $data['ProdiEng'];

                                // Buat Update dan View Node JS untuk notifikasi
                                $this->m_finance->update_payment_MHS($data_asli['trx_id'],$getData[0]['ID_payment']);

                                if (ENVIRONMENT == 'production') {
                                    // Send Email
                                    $text = 'Dear '.$Nama.',<br><br>
                                                  Your payment has been received,<br><br>
                                                  Payment Type : '.$PTIDDesc.'<br>
                                                  Prodi : '.$ProdiEng.'<br>
                                                  SemesterName : '.$SemesterName.'<br><br>
                                                  as much as Rp '.number_format($getData[0]['Invoice'],2,',','.').'
                                                  <br>
                                              ';
                                    $to = $EmailPU;
                                    $subject = "Podomoro University Payment thank you";
                                    $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                                }

                                $this->m_master->update_va_log($data_asli);
                                $this->m_master->saveNotification($data);

                                // send notifikasi
                                $client = new Client(new Version1X('//10.1.10.230:3000'));

                                $client->initialize();
                                // send message to connected clients
                                $client->emit('update_notifikasi', ['update_notifikasi' => '1']);
                                $client->close();

                            }

                            // find data apakah memiliki beberapa cicilan
                            $ID_payment = $getData[0]['ID_payment'];
                            $getData3 = $this->m_finance->findDatapayment_studentsBaseID_payment($ID_payment);
                            if (count($getData3) > 0) {
                                // create va selanjutnya
                                $GetPayment = $this->m_master->caribasedprimary('db_finance.payment','ID',$getData[0]['ID_payment']);
                                $NPM = $GetPayment[0]['NPM'];
                                $data = $this->m_master->PaymentgetMahasiswaByNPM($NPM);
                                $Nama = $data['Nama'];
                                $EmailPU = $data['EmailPU'];
                                $payment = $getData3[0]['Invoice'];

                                $deli = strpos($payment, '.');
                                $payment = substr($payment, 0,$deli);

                                $DeadLinePayment = $getData3[0]['Deadline'];
                                $VA_number = $this->m_finance->getVANumberMHS($NPM);
                                $create_va = $this->m_finance->create_va_Payment($payment,$DeadLinePayment, $Nama, $EmailPU,$VA_number,'Cicilan',$tableRoutes = 'db_finance.payment_students');

                                if ($create_va['status']) {
                                    // After create va update payment students
                                    $ab = $this->m_finance->updatePaymentStudentsFromCicilan($create_va['msg']['trx_id'],$getData3[0]['ID']);
                                    if (ENVIRONMENT == 'production') {
                                        // Send Email
                                        $msg = 'Please continue to pay the next installment with VA Number : '.$VA_number. ' <br> as much as Rp '.number_format($getData3[0]['Invoice'],2,',','.');
                                        $text = 'Dear '.$Nama.',<br><br>
                                                  '.$msg.'    
                                                  ';
                                        $to = $EmailPU;
                                        $subject = "Podomoro University Notification";
                                        $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                                    }
                                }
                            }
                            echo '{"status":"000"}';
                            exit;
                            break;
                        default:
                            # code...
                            break;
                    }

                }
            }
        }
    }

    private function genratePassword($Username,$Password){

        $plan_password = $Username.''.$Password;
        $pas = md5($plan_password);
        $pass = sha1('jksdhf832746aiH{}{()&(*&(*'.$pas.'HdfevgyDDw{}{}{;;*766&*&*');

        return $pass;
    }

    public function logMeOut(){

        $this->session->sess_destroy();
        return 1;
    }

    // ========= Session Employees ========
    private function setSession($ID,$NIP){

        $dataSession = $this->m_auth->__getUserAuth($ID,$NIP);
        $timePerCredits = $this->m_auth->__getTimePerCredits();

        $ruleUser = $this->m_auth->__getRuleUser($NIP);

        // Super Divisi -- Lihat ID Di table db_employees.division
        // 1 (Yayasan), 2 (Rectore) , 12 (IT)
        $superDivision = [1,2,12];

        $setSession = array(
            'ID'  => $dataSession[0]['ID'],
            'NIP'  => $dataSession[0]['NIP'],
            'Name'  => $dataSession[0]['Name'],
            'FullNameTitle'  => $dataSession[0]['TitleAhead'].' '.$dataSession[0]['Name'].' '.$dataSession[0]['TitleBehind'],
            'Email'  => $dataSession[0]['Email'],
            'EmailPU'  => $dataSession[0]['EmailPU'],
            'Address'  => $dataSession[0]['Address'],
            'Photo'  => $dataSession[0]['Photo'],
            'Password'  => $dataSession[0]['Password'],
            'PositionMain'  => array(
                'IDDivision' => $dataSession[0]['IDDivision'],
                'Division' => $dataSession[0]['Division'],
                'IDPosition' => $dataSession[0]['IDPosition'],
                'Position' => $dataSession[0]['Position']
            ),
            'PositionOther1' => array(
                'IDDivisionOther1' => $dataSession[0]['IDDivisionOther1'],
                'DivisionOther1' => $dataSession[0]['DivisionOther1'],
                'IDPositionOther1' => $dataSession[0]['IDPositionOther1'],
                'PositionOther1' => $dataSession[0]['PositionOther1']
            ),
            'PositionOther2' => array(
                'IDDivisionOther2' => $dataSession[0]['IDDivisionOther2'],
                'DivisionOther2' => $dataSession[0]['DivisionOther2'],
                'IDPositionOther2' => $dataSession[0]['IDPositionOther2'],
                'PositionOther2' => $dataSession[0]['PositionOther2']
            ),
            'PositionOther3' => array(
                'IDDivisionOther3' => $dataSession[0]['IDDivisionOther3'],
                'DivisionOther3' => $dataSession[0]['DivisionOther3'],
                'IDPositionOther3' => $dataSession[0]['IDPositionOther3'],
                'PositionOther3' => $dataSession[0]['PositionOther3']
            ),
            'timePerCredits' => $timePerCredits['time'],
            'ruleUser' => $ruleUser,
            'menuDepartement' => (count($ruleUser)>1) ? false : true ,
            'departementNavigation' => $dataSession[0]['MenuNavigation'],
            'IDdepartementNavigation' => $dataSession[0]['IDMenuNavigation'],
            'LoginAt' => date("Y-m-d H:i:s"),
            'loggedIn' => true
        );

        $this->session->set_userdata($setSession);

        // adding for budgeting
        $PositionMain = $this->session->userdata('PositionMain');
        $Position = $PositionMain['IDPosition'];
        switch ($Position) {
            case 5: // Dekan
                $this->load->model('faculty/m_faculty');
                $this->session->set_userdata('IDdepartementNavigation','34');
                $this->session->set_userdata('departementNavigation','admin-fakultas');
                $this->m_faculty->auth(); // get session
                break;
            case 6: // KAPRODI
                $this->load->model('prodi/m_prodi');
                $this->session->set_userdata('IDdepartementNavigation','15');
                $this->session->set_userdata('departementNavigation','admin-prodi');
                $this->m_prodi->auth(); // get session
                break;
            case 7: // DOSEN
                // $this->session->set_userdata('IDdepartementNavigation','36');
                $this->session->set_userdata('IDdepartementNavigation','15');
                // $this->session->set_userdata('departementNavigation','other-division');
                $this->session->set_userdata('departementNavigation','admin-prodi');
                break;
            default:
                break;
        }
    }


    public function ApiServerToServer()
    {
        $data = file_get_contents('php://input');

        $data_json = json_decode($data,true);

        if (!$data_json) {
            // handling orang iseng
            echo '{"status":"999","message":"jangan iseng :D"}';
        }
        else {
            $getData = $data_json['data'];
            $token = $getData;
            $key = "UAP)(*";
            $getData = (array) $this->jwt->decode($token,$key);
            $echo = json_encode($getData);
            // $data_json = json_encode($data_json,true);
            // echo '{"status":"FCKGW","data" : '.$echo.'}';
            if ($data_json['client_id'] == '10.1.30.102') {
                if ($getData['adi'] == 'adi') {
                    // runscript
                    $this->load->model('m_sendemail');
                    $text = 'Dear Test API server to server
                        ';
                    $to = 'it@podomorouniversity.ac.id';
                    $subject = "Podomoro University Test API server to server";
                    $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                    echo '{"status":"FCKGW","data" : '.$echo.'}';
                }
                else
                {
                    echo '{"status":"Key is wrong"}';
                }

            }
            else
            {
                echo '{"status":"Your are not register"}';
            }

        }
    }

    // public function loginToVenue()
    // {
    //     try {
    //         $token = $this->input->post('token');
    //         $key = "s3Cr3T-G4N";
    //         $input = (array) $this->jwt->decode($token,$key);
    //         if ($input['url'] != url_pas.'loginToVenue') {
    //             echo '{"status":"Key is wrong"}';
    //         }
    //         else
    //         {
    //             $dataUser = $this->m_master->caribasedprimary('db_employees.employees','NIP',$input['NIP']);
    //             if (count($dataUser) > 0) {
    //                 $this->setSession($dataUser[0]['ID'],$dataUser[0]['NIP']);
    //                 redirect(base_url().'vreservation');
    //             }
    //         }

    //     }
    //         //catch exception
    //     catch(Exception $e) {
    //         // handling orang iseng
    //         echo '{"status":"999","message":"jangan iseng :D"}';
    //     }
    // }

    // public function loginToAdminProdi()
    // {
    //     try {
    //         $token = $this->input->post('token');
    //         $key = "s3Cr3T-G4N";
    //         $input = (array) $this->jwt->decode($token,$key);
    //         if ($input['url'] != url_pas.'loginToAdminProdi') {
    //             echo '{"status":"Key is wrong"}';
    //         }
    //         else
    //         {
    //             $dataUser = $this->m_master->caribasedprimary('db_employees.employees','NIP',$input['NIP']);
    //             if (count($dataUser) > 0) {
    //                 $this->setSession($dataUser[0]['ID'],$dataUser[0]['NIP']);
    //                 $this->load->model('prodi/m_prodi');
    //                 $this->session->set_userdata('IDdepartementNavigation','15');
    //                 $this->session->set_userdata('departementNavigation','admin-prodi');
    //                 $this->m_prodi->auth(); // get session
    //                 redirect(base_url().'dashboard');
    //             }
    //         }

    //     }
    //         //catch exception
    //     catch(Exception $e) {
    //         // handling orang iseng
    //         echo '{"status":"999","message":"jangan iseng :D"}';
    //     }
    // }

    // public function loginToAdminFaculty()
    // {
    //     try {
    //         $token = $this->input->post('token');
    //         $key = "s3Cr3T-G4N";
    //         $input = (array) $this->jwt->decode($token,$key);
    //         if ($input['url'] != url_pas.'loginToAdminFaculty') {
    //             echo '{"status":"Key is wrong"}';
    //         }
    //         else
    //         {
    //             $dataUser = $this->m_master->caribasedprimary('db_employees.employees','NIP',$input['NIP']);
    //             if (count($dataUser) > 0) {
    //                 $this->setSession($dataUser[0]['ID'],$dataUser[0]['NIP']);
    //                 $this->load->model('faculty/m_faculty');
    //                 $this->session->set_userdata('IDdepartementNavigation','34');
    //                 $this->session->set_userdata('departementNavigation','admin-fakultas');
    //                 $this->m_faculty->auth(); // get session
    //                 redirect(base_url().'dashboard');
    //             }
    //         }

    //     }
    //         //catch exception
    //     catch(Exception $e) {
    //         // handling orang iseng
    //         echo '{"status":"999","message":"jangan iseng :D"}';
    //     }
    // }


    public function portal()
    {

        try {
            $token = $this->input->post('token');
            $key = "s3Cr3T-G4N";
            $input = (array) $this->jwt->decode($token,$key);
            if ($input['url'] != url_pas.'portal') {
                echo '{"status":"Key is wrong"}';
            }
            else
            {

//                $data['include'] = $this->load->view('template/include','',true);
//                $this->load->view('template/loginFromPortalLecturer',$data);

                $dataUser = $this->m_master->caribasedprimary('db_employees.employees','NIP',$input['NIP']);
                if (count($dataUser) > 0) {
                    $this->setSession($dataUser[0]['ID'],$dataUser[0]['NIP']);
                    // cek User (KaProdi,Dekan & Lecturer)
                    $PositionMain = $this->session->userdata('PositionMain');
                    $Position = $PositionMain['IDPosition'];

                    // $URLRedirect = ($input['URLRedirect']!='') ? $input['URLRedirect'] : base_url().'dashboard';
                    $URLRedirect = (array_key_exists('URLRedirect', $input)) ? $input['URLRedirect'] : base_url().'dashboard';
                    $ProdiID = NULL;
                    if (array_key_exists('ProdiID', $ProdiID)) {
                        $ProdiID = $input['ProdiID'];
                    }
                    
                    switch ($Position) {
                        case 5: // Dekan
                            $this->load->model('faculty/m_faculty');
                            $this->session->set_userdata('IDdepartementNavigation','34');
                            $this->session->set_userdata('departementNavigation','admin-fakultas');
                            $this->m_faculty->auth(); // get session
                            break;
                        case 6: // KAPRODI
                            $this->load->model('prodi/m_prodi');
                            $this->session->set_userdata('IDdepartementNavigation','15');
                            $this->session->set_userdata('departementNavigation','admin-prodi');
                            // print($ProdiID);die();
                            $this->m_prodi->auth($ProdiID); // get session
                            break;
                        case 7: // DOSEN
                            // $this->session->set_userdata('IDdepartementNavigation','36');
                            $this->session->set_userdata('IDdepartementNavigation','15');
                            // $this->session->set_userdata('departementNavigation','other-division');
                            $this->session->set_userdata('departementNavigation','admin-prodi');
                            break;
                        default:
                            break;
                    }

                    redirect($URLRedirect);


                }
            }

        }
            //catch exception
        catch(Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function login_to_budget($TokenNIP,$TokenDep,$TokenURL)
    {
        error_reporting(0);
        try {
            $this->load->model('budgeting/m_budgeting');
            $key = "UAP)(*";
            $NIP = $this->jwt->decode($TokenNIP,$key);
            $Dep = $this->jwt->decode($TokenDep,$key);
            $UrlRedirect = $this->jwt->decode($TokenURL,$key);
            $dataUser = $this->m_master->caribasedprimary('db_employees.employees','NIP',$NIP);
            $G_div = $this->m_budgeting->SearchDepartementBudgeting($Dep);
            // cek Department existing or not
            if (count($dataUser) > 0 && count($G_div) > 0) {
                $this->setSession($dataUser[0]['ID'],$dataUser[0]['NIP']);
                // cek User (KaProdi,Dekan & Lecturer)
                $PositionMain = $this->session->userdata('PositionMain');
                $Position = $PositionMain['IDPosition'];
                $this->session->set_userdata('lecturer_PUISLogin',true);
                switch ($Position) {
                    case 5: // Dekan
                        $this->load->model('faculty/m_faculty');
                        $this->session->set_userdata('IDdepartementNavigation','34');
                        $this->session->set_userdata('departementNavigation','admin-fakultas');
                        $this->m_faculty->auth(); // get session
                        redirect(base_url().$UrlRedirect);
                        break;
                    case 6: // KAPRODI
                        $this->load->model('prodi/m_prodi');
                        $this->session->set_userdata('IDdepartementNavigation','15');
                        $this->session->set_userdata('departementNavigation','admin-prodi');
                        $this->m_prodi->auth(); // get session
                        redirect(base_url().$UrlRedirect);
                        break;
                    case 7: // DOSEN
                        $this->session->set_userdata('IDdepartementNavigation','36');
                        $this->session->set_userdata('departementNavigation','other-division');
                        redirect(base_url().$UrlRedirect);
                        break;
                    default:
                        redirect(base_url().$UrlRedirect);
                        break;
                }

            }
            else
            {
                redirect(url_sign_out);
            }
        }
            //catch exception
        catch(Exception $e) {
            redirect(url_sign_out);
        }
    }



}
