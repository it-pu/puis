<?php
defined('BASEPATH') OR exit('No direct script access allowed');
include_once APPPATH.'vendor/autoload.php';
use ElephantIO\Client;
use ElephantIO\Engine\SocketIO\Version1X;

class C_api2 extends CI_Controller {

    var $DummyEmail = 'nandang.mulyadi@podomorouniversity.ac.id';

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('master/m_master');
        $this->load->model('hr/m_hr');
        $this->load->model('vreservation/m_reservation');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('notification/m_log');
        $this->load->library('JWT');
        $this->load->library('google');

    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    private function getInputToken2($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    public function is_url_exist($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if($code == 200){
            $status = true;
        }else{
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    private function getInputTokenGet($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    private function sendMailRest($data){
        $url = ($_SERVER['SERVER_NAME']=='localhost')
            ? 'http://pcam.podomorouniversity.ac.id/rest/__sendEmail'
            : base_url('rest/__sendEmail');
        $Input = $this->jwt->encode($data,"UAP)(*");

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_POST, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS,
            "token=".$Input);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $pr = curl_exec($ch);
        curl_close ($ch);
    }


    public function crudScheduleExchage(){
        $data_arr = $this->getInputToken();

        if (count($data_arr) > 0) {

            if($data_arr['action']=='approveExchange'){

                $dataSceduleExchange = $this->db->query('SELECT exc.*,em.Name AS Kaprodi, em2.Name AS Adum FROM db_academic.schedule_exchange exc
                                                                    LEFT JOIN db_employees.employees em ON (em.NIP = exc.Updated1By)
                                                                    LEFT JOIN db_employees.employees em2 ON (em2.NIP = exc.Updated2By)
                                                                    WHERE exc.ID = "'.$data_arr['EXID'].'" LIMIT 1 ')
                                        ->result_array();

                // Cek apakah sudah update atau belum jika sudah maka
                // 1. tidak perlu update lagi
                // 2. tidak perlu kirim email ke adum

                if(count($dataSceduleExchange)>0){

                    $d = $dataSceduleExchange[0];
                    if($d['Status']=='0' || $d['Status']==0){

                        $key = 's3Cr3T-G4N';
                        $token = $dataSceduleExchange[0]['Token'];
                        $dataToken = (array) $this->jwt->decode($token,$key);

                        // Get Name Kaprodi
                        $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                            array('NIP' => $data_arr['ApprovedBy']))->result_array();

                        $bodyEmail = '<div>
                                            Dear <span style="color: #333;">General Affair</span>,
                                            <br/>
                                            Perihal : <b>Permohonan Ruangan Untuk Kuliah Pengganti</b>
                        
                                            <br/>
                                            <br/>
                                            <div style="font-size: 14px;">
                                                <table  width="100%" cellspacing="0" cellpadding="1" border="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="width: 20%;">Dosen</td>
                                                        <td style="width: 2%;">:</td>
                                                        <td style="width: 40%;">'.$dataToken['Lecturer'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Program Studi</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Prodi'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" style="color: #673AB7;">Mengajukan permohonan untuk kuliah pengganti</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mata Kuliah</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Course'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Group Kelas</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['ClassGroup'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sesi (Pertemuan ke)</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Meeting'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jadwal Semula</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['ScheduleExist'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Diganti Pada</td>
                                                        <td>:</td>
                                                        <td style="color: green;font-weight: bolder;">'.$dataToken['ScheduleExchange'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alasan</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Reason'].'</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <br/>
                                                <p>
                                                    Demikian permohonan ini kami ajukan, mohon dapat diproses sesuai dengan ketentuan yang berlaku. Terima kasih
                                                </p>
                                                <br/>
                                                <br/>
                                                <table  width="100%" cellspacing="5" cellpadding="1" border="0">
                                                    <tr>
                                                        <td style="width: 100%;" align="center">
                                                            Approved By
                                                            <br/>
                                                            <h3 style="color: #009688;margin-top: 7px;">'.$dataKaprodi[0]['Name'].'
                                                                <br/>
                                                            <small>'.$data_arr['ApprovedBy'].'</small>
                                                            </h3>
                                                        </td>
                        
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>';

                        $emailGa = $this->db->select('Email')->get_where('db_employees.division',array('ID' => 8))->result_array();

                        $mailTo = (count($emailGa)>0)
                            ? $emailGa[0]['Email']
                            : $this->DummyEmail;


                        $data = array(
                            'to' => $mailTo,
                            'subject' => 'Kaprodi : Permohonan Ruangan Kelas Pengganti',
                            'text' => $bodyEmail,
                            'auth' => 's3Cr3T-G4N'
                        );

                        $this->sendMailRest($data);

                        // Update Status
                        $dataUpdate = array(
                            'Updated1At' => $data_arr['ApprovedAt'],
                            'Updated1By' => $data_arr['ApprovedBy'],
                            'Status' => '1',
                        );
                        $this->db->where('ID', $data_arr['EXID']);
                        $this->db->update('db_academic.schedule_exchange',$dataUpdate);


                        //============= Logging ==========
                        // Insert Logging
                        $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                        $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                            ? $url
                            : url_server_ws.'/images/icon/lecturer.png';

                        $Log_dataInsert = array(
                            'Icon' => $img_profile,
                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Schedule Exchange Approved',
                            'Description' => 'Kaprodi : Schedule Exchange Approved',
                            'URLDirect' => 'ga_schedule_exchange',
                            'URLDirectLecturer' => 'attendance/schedule-exchange',
                            'CreatedBy' => $data_arr['ApprovedBy'],
                            'CreatedName' => $dataKaprodi[0]['Name'],
                            'CreatedAt' => $data_arr['ApprovedAt'],
                        );

                        $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                        $insert_id_logging = $this->db->insert_id();

                        // insert ke user
                        $Log_arr_ins = array(
                            'IDLogging' => $insert_id_logging,
                            'UserID' => $d['NIP']
                        );
                        $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                        // Get Member Adum
                        $dataUserAdum = $this->db->select('NIP')->get_where('db_employees.rule_users',
                            array('IDDivision' => 8))->result_array();

                        if(count($dataUserAdum)>0){
                            foreach ($dataUserAdum as $item){
                                $Log_arr_ins = array(
                                    'IDLogging' => $insert_id_logging,
                                    'UserID' => $item['NIP']
                                );
                                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);
                            }
                        }



                        $result = array(
                            'Status' => '1',
                            'Message' => 'Approved',
                            'By' => $dataKaprodi[0]['Name']
                        );

                    }
                    else if($d['Status']=='1' || $d['Status']==1 || $d['Status']=='2' || $d['Status']==2) {
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Approved',
                            'By' => $d['Kaprodi']
                        );
                    }
                    else if($d['Status']=='-1' || $d['Status']==-1){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Rejected',
                            'Comment' => $d['Comment'],
                            'By' => $d['Kaprodi']
                        );
                    }
                    else if($d['Status']=='-2' || $d['Status']==-2){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Rejected',
                            'Comment' => $d['Comment'],
                            'By' => $d['Adum']
                        );
                    }



                } else {
                    $result = array(
                      'Message' => 'Data Not Yet'
                    );
                }

                return print_r(json_encode($result));


            }
            else if($data_arr['action']=='rejectedExchange'){

                $dataSceduleExchange = $this->db->query('SELECT exc.*,em.Name AS Lecturer, em.EmailPU FROM db_academic.schedule_exchange exc
                                                                    LEFT JOIN db_employees.employees em ON (em.NIP = exc.NIP)
                                                                    WHERE exc.ID = "'.$data_arr['EXID'].'" LIMIT 1 ')
                    ->result_array();

                if(count($dataSceduleExchange)>0){

                    $key = 's3Cr3T-G4N';
                    $token = $dataSceduleExchange[0]['Token'];
                    $dataToken = (array) $this->jwt->decode($token,$key);

                    // Get Name Kaprodi
                    $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                        array('NIP' => $data_arr['ApprovedBy']))->result_array();

                    $bodyEmail = '<div>
                                            Dear <span style="color: #333;">'.$dataSceduleExchange[0]['Lecturer'].'</span>,
                                            <br/>
                                            Perihal : <b>Permohonan Ruangan Untuk Kuliah Pengganti</b>
                        
                                            <br/>
                                            <br/>
                                            
                                            <div style="background: lightyellow;color: red;border: 1px solid red; text-align: center;padding: 7px;margin-bottom: 10px;">
                                                <h2 style="margin-top: 7px;margin-bottom: 0px;">Permohonan Ditolak</h2>
                                                <p style="color: blue;margin-top: 3px;">'.$data_arr['Comment'].'</p>
                                            </div>
                                            
                                            <div style="text-align: center;">
                                                <p>--- Detail permohonan ---</p>
                                            </div>
                                            
                                            <div style="font-size: 14px;">
                                                <table  width="100%" cellspacing="0" cellpadding="1" border="0">
                                                    <tbody>
                                                    <tr>
                                                        <td style="width: 20%;">Dosen</td>
                                                        <td style="width: 2%;">:</td>
                                                        <td style="width: 40%;">'.$dataToken['Lecturer'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Program Studi</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Prodi'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td colspan="3" style="color: #673AB7;">Mengajukan permohonan untuk kuliah pengganti</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Mata Kuliah</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Course'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Group Kelas</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['ClassGroup'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Sesi (Pertemuan ke)</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Meeting'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Jadwal Semula</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['ScheduleExist'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Diganti Pada</td>
                                                        <td>:</td>
                                                        <td style="color: green;font-weight: bolder;">'.$dataToken['ScheduleExchange'].'</td>
                                                    </tr>
                                                    <tr>
                                                        <td>Alasan</td>
                                                        <td>:</td>
                                                        <td>'.$dataToken['Reason'].'</td>
                                                    </tr>
                                                    </tbody>
                                                </table>
                                                <br/>
                                                <p>
                                                    Demikian permohonan ini kami ajukan, mohon dapat diproses sesuai dengan ketentuan yang berlaku. Terima kasih
                                                </p>
                                                <br/>
                                                <br/>
                                                <table  width="100%" cellspacing="5" cellpadding="1" border="0">
                                                    <tr>
                                                        <td style="width: 100%;" align="center">
                                                            Rejected By
                                                            <br/>
                                                            <h3 style="color: #009688;margin-top: 7px;">'.$dataKaprodi[0]['Name'].'
                                                                <br/>
                                                            <small>'.$data_arr['ApprovedBy'].'</small>
                                                            </h3>
                                                        </td>
                        
                                                    </tr>
                                                </table>
                                            </div>
                                        </div>';

                    $mailTo = (count($dataSceduleExchange)>0)
                        ? $dataSceduleExchange[0]['EmailPU']
                        : $this->DummyEmail;

                    $data = array(
                        'to' => $mailTo,
                        'subject' => 'Kaprodi : Permohonan Ruangan Kelas Pengganti',
                        'text' => $bodyEmail,
                        'auth' => 's3Cr3T-G4N'
                    );

                    $this->sendMailRest($data);


                    $dataUpdate =  array(
                        'Updated1At' => $data_arr['ApprovedAt'],
                        'Updated1By' => $data_arr['ApprovedBy'],
                        'Comment' => $data_arr['Comment'],
                        'Status' => '-1',
                    );
                    $this->db->where('ID', $data_arr['EXID']);
                    $this->db->update('db_academic.schedule_exchange',$dataUpdate);


                    //============= Logging ==========
                    // Insert Logging

                    $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                    $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                        ? $url
                        : url_server_ws.'/images/icon/lecturer.png';

                    $Log_dataInsert = array(
                        'Icon' => $img_profile,
                        'Title' => '<i class="fa fa-times-circle margin-right" style="color:darkred;"></i> Schedule Exchange Rejected',
                        'Description' => 'Kaprodi : Schedule Exchange Rejected',
                        'URLDirect' => 'ga_schedule_exchange',
                        'URLDirectLecturer' => 'attendance/schedule-exchange',
                        'CreatedBy' => $data_arr['ApprovedBy'],
                        'CreatedName' => $dataKaprodi[0]['Name'],
                        'CreatedAt' => $data_arr['ApprovedAt'],
                    );


                    $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                    $insert_id_logging = $this->db->insert_id();

                    // insert ke user
                    $Log_arr_ins = array(
                        'IDLogging' => $insert_id_logging,
                        'UserID' => $dataSceduleExchange[0]['NIP']
                    );
                    $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                }



                return print_r(1);

            }
            else if($data_arr['action']=='readStatusExchange'){
                // Cek apakah sudah di approve atau belum
                $dataSceduleExchange = $this->db->query('SELECT exc.*,em.Name AS Kaprodi, em2.Name AS Adum FROM db_academic.schedule_exchange exc
                                                                    LEFT JOIN db_employees.employees em ON (em.NIP = exc.Updated1By)
                                                                    LEFT JOIN db_employees.employees em2 ON (em2.NIP = exc.Updated2By)
                                                                    WHERE exc.ID = "'.$data_arr['EXID'].'" LIMIT 1 ')
                    ->result_array();

                if(count($dataSceduleExchange)>0){
                    $d = $dataSceduleExchange[0];
                    if($d['Status']=='0' || $d['Status']==0){

                        $result = array(
                            'Status' => $d['Status']
                        );

                    }
                    else if($d['Status']=='1' || $d['Status']==1 || $d['Status']=='2' || $d['Status']==2){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Approved',
                            'By' => $d['Kaprodi']
                        );
                    }
                    else if($d['Status']=='-1' || $d['Status']==-1){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Rejected',
                            'Comment' => $d['Comment'],
                            'By' => $d['Kaprodi']
                        );
                    }
                    else if($d['Status']=='-2' || $d['Status']==-2){
                        $result = array(
                            'Status' => $d['Status'],
                            'Message' => 'Rejected',
                            'Comment' => $d['Comment'],
                            'By' => $d['Adum']
                        );
                    }
                }
                else {
                    $result = array(
                        'Message' => 'Data Not Yet'
                    );
                }

                return print_r(json_encode($result));
            }


        }

    }

    public function crudModifyAttendance(){
        $data_arr = $this->getInputToken();
        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'checkStatusModifyAttd') {

                $IDAM = $data_arr['IDAM'];

                $data = $this->db->query('SELECT am.Status, em.Name AS Lecturer, am.Reason_Reject AS Reason FROM db_academic.attendance_modify am 
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = am.Updated1By)
                                                    WHERE am.ID = "'.$IDAM.'" ')->result_array();


                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='rejectedModifyAttd'){

                $dataRequsted = $this->db->query('SELECT am.RequestBy, em.EmailPU, em.Name AS Lecturer, em.Gender, am.DataEmail, am.TokenURL FROM db_academic.attendance_modify am 
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = am.RequestBy)
                                                            WHERE am.ID = "'.$data_arr['IDAM'].'" ')->result_array();


                $dataUpdate = array(
                    'Reason_Reject' => $data_arr['Reason'],
                    'Updated1By' => $data_arr['Updated1By'],
                    'Updated1At' => $data_arr['Updated1At'],
                    'Status' => '-1'
                );
                $this->db->where('ID', $data_arr['IDAM']);
                $this->db->update('db_academic.attendance_modify',$dataUpdate);


                $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                    array('NIP' => $data_arr['Updated1By']))->result_array();

                if(count($dataKaprodi)>0){

                    $DataEmail = $this->getInputToken2($dataRequsted[0]['DataEmail']);

                    //============= Logging ==========
                    // Insert Logging
                    $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                    $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                        ? $url
                        : url_server_ws.'/images/icon/lecturer.png';

                    $Log_dataInsert = array(
                        'Icon' => $img_profile,
                        'Title' => '<i class="fa fa-times-circle margin-right" style="color:darkred;"></i> Modify Attendance Rejected',
                        'Description' => $DataEmail['Code'].' - '.$DataEmail['CourseEng'].' | Group : '.$DataEmail['Group'].' | Session : '.$DataEmail['Session'],
                        'URLDirectLecturer' => 'attendance/modify-attendance/'.$dataRequsted[0]['TokenURL'],
                        'CreatedBy' => $data_arr['Updated1By'],
                        'CreatedName' => $dataKaprodi[0]['Name'],
                        'CreatedAt' => $data_arr['Updated1At'],
                    );

                    $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                    $insert_id_logging = $this->db->insert_id();

                    // insert ke user
                    $Log_arr_ins = array(
                        'IDLogging' => $insert_id_logging,
                        'UserID' => $dataRequsted[0]['RequestBy']
                    );
                    $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                    // Send Email
                    $mailTo = (count($dataRequsted)>0)
                        ? $dataRequsted[0]['EmailPU']
                        : $this->DummyEmail;




                    $greating = ($dataRequsted[0]['Gender']=='L')  ? 'Bapak' : 'Ibu';
                    $bodyEmail = '<div>
                    Dear <span style="color: #333;">'.$greating.' '.ucwords($dataRequsted[0]['Lecturer']).'</span>,
                    <br/>
                    Perihal : <b>Perubahan Data Absensi Mahasiswa</b>

                    <br/>
                    <br/>

                    <div style="background: lightyellow;color: red;border: 1px solid red; text-align: center;padding: 7px;margin-bottom: 10px;">
                        <h2 style="margin-top: 7px;margin-bottom: 0px;">Permohonan Ditolak</h2>
                        <p style="color: blue;margin-top: 3px;">
                            '.$data_arr['Reason'].'
                        </p>
                    </div>

                    <div style="text-align: center;">
                        <p>--- Detail permohonan ---</p>
                    </div>

                    <div style="font-size: 14px;">
                        <table  width="100%" cellspacing="0" cellpadding="1" border="0">
                            <tbody>
                            <tr>
                                <td style="width: 20%;">Dosen</td>
                                <td style="width: 2%;">:</td>
                                <td style="width: 40%;">'.ucwords($dataRequsted[0]['Lecturer']).'</td>
                            </tr>
                            <tr>
                                <td>Kode</td>
                                <td>:</td>
                                <td>'.$DataEmail['Code'].'</td>
                            </tr>
                            <tr>
                                <td>Mata Kuliah</td>
                                <td>:</td>
                                <td>'.$DataEmail['CourseEng'].'</td>
                            </tr>
                            <tr>
                                <td>Group Kelas</td>
                                <td>:</td>
                                <td>'.$DataEmail['Group'].'</td>
                            </tr>
                            <tr>
                                <td>Sesi (Pertemuan ke)</td>
                                <td>:</td>
                                <td>'.$DataEmail['Session'].'</td>
                            </tr>
                            <tr>
                                <td>Alasan</td>
                                <td>:</td>
                                <td>'.$DataEmail['Reason'].'</td>
                            </tr>


                            <tr>
                                <td colspan="3" style="color: #673AB7;">Mengajukan permohonan untuk perubahan daftar hadir mahasiswa</td>
                            </tr>

                            <tr>
                                <td>Sebelumnya</td>
                                <td>:</td>
                                <td>'.$DataEmail['Before'].'</td>
                            </tr>
                            <tr>
                                <td>Menjadi</td>
                                <td>:</td>
                                <td>'.$DataEmail['After'].'</td>
                            </tr>

                            </tbody>
                        </table>
                        <br/>
                        <p>
                            Demikian permohonan ini saya ajukan, mohon dapat diproses sesuai dengan ketentuan yang berlaku. Terima kasih
                        </p>
                        <br/>
                        <br/>

                        <table  width="100%" cellspacing="5" cellpadding="1" border="0">
                            <tr>
                                <td style="width: 100%;" align="center">
                                    Rejected By
                                    <br/>
                                    <h3 style="color: #009688;margin-top: 7px;">'.$dataKaprodi[0]['Name'].'
                                        <br/>
                                        <small>'.$data_arr['Updated1By'].'</small>
                                    </h3>
                                </td>

                            </tr>
                        </table>

                    </div>
                </div>';
                    $data = array(
                        'to' => $mailTo,
                        'subject' => 'Kaprodi : Modify Attendance Rejected',
                        'text' => $bodyEmail,
                        'auth' => 's3Cr3T-G4N'
                    );

                    $this->sendMailRest($data);
                }


                return print_r(1);
            }
            else if($data_arr['action']=='approvedModifyAttd'){
                $dataRequsted = $this->db->query('SELECT am.RequestBy, em.EmailPU, em.Name AS Lecturer, em.Gender, am.DataEmail, am.TokenURL FROM db_academic.attendance_modify am 
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = am.RequestBy)
                                                            WHERE am.ID = "'.$data_arr['IDAM'].'" ')->result_array();


                $dataUpdate = array(
                    'Updated1By' => $data_arr['Updated1By'],
                    'Updated1At' => $data_arr['Updated1At'],
                    'Status' => '1'
                );

                $this->db->where('ID', $data_arr['IDAM']);
                $this->db->update('db_academic.attendance_modify',$dataUpdate);
                $this->db->reset_query();

                // Get Detail Modify Atttendance
                $dataStd = $this->db->get_where('db_academic.attendance_modify_details'
                    ,array('IDAM' => $data_arr['IDAM']))->result_array();

                if(count($dataStd)>0){
                    foreach ($dataStd AS $item){

                        $dataUpdate = array(
                            'M'.$item['Sesi'] => ''.$item['Meet'],
                            'D'.$item['Sesi'] => $item['Reason']
                        );
                        $this->db->where('ID', $item['IDAS']);
                        $this->db->update('db_academic.attendance_students',$dataUpdate);
                    }
                }


                $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                    array('NIP' => $data_arr['Updated1By']))->result_array();

                if(count($dataKaprodi)>0){

                    $DataEmail = $this->getInputToken2($dataRequsted[0]['DataEmail']);

                    //============= Logging ==========
                    // Insert Logging
                    $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                    $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                        ? $url
                        : url_server_ws.'/images/icon/lecturer.png';

                    $Log_dataInsert = array(
                        'Icon' => $img_profile,
                        'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> Modify Attendance Approved',
                        'Description' => $DataEmail['Code'].' - '.$DataEmail['CourseEng'].' | Group : '.$DataEmail['Group'].' | Session : '.$DataEmail['Session'],
                        'URLDirectLecturer' => 'attendance/modify-attendance/'.$dataRequsted[0]['TokenURL'],
                        'CreatedBy' => $data_arr['Updated1By'],
                        'CreatedName' => $dataKaprodi[0]['Name'],
                        'CreatedAt' => $data_arr['Updated1At'],
                    );

                    $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                    $insert_id_logging = $this->db->insert_id();

                    // insert ke user
                    $Log_arr_ins = array(
                        'IDLogging' => $insert_id_logging,
                        'UserID' => $dataRequsted[0]['RequestBy']
                    );
                    $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                    // Send Email
                    $mailTo = (count($dataRequsted)>0)
                        ? $dataRequsted[0]['EmailPU']
                        : $this->DummyEmail;


                    $greating = ($dataRequsted[0]['Gender']=='L')  ? 'Bapak' : 'Ibu';
                    $bodyEmail = '<div>
                    Dear <span style="color: #333;">'.$greating.' '.ucwords($dataRequsted[0]['Lecturer']).'</span>,
                    <br/>
                    Perihal : <b>Perubahan Data Absensi Mahasiswa</b>

                    <br/>
                    <br/>

                    <div style="background: lightyellow;border: 1px solid green;color: green;text-align: center;padding: 7px;margin-bottom: 10px;">
                        <h2 style="margin-top: 7px;margin-bottom: 10px;">Permohonan diterima</h2>
                    </div>

                    <div style="text-align: center;">
                        <p>--- Detail permohonan ---</p>
                    </div>

                    <div style="font-size: 14px;">
                        <table  width="100%" cellspacing="0" cellpadding="1" border="0">
                            <tbody>
                            <tr>
                                <td style="width: 20%;">Dosen</td>
                                <td style="width: 2%;">:</td>
                                <td style="width: 40%;">'.ucwords($dataRequsted[0]['Lecturer']).'</td>
                            </tr>
                            <tr>
                                <td>Kode</td>
                                <td>:</td>
                                <td>'.$DataEmail['Code'].'</td>
                            </tr>
                            <tr>
                                <td>Mata Kuliah</td>
                                <td>:</td>
                                <td>'.$DataEmail['CourseEng'].'</td>
                            </tr>
                            <tr>
                                <td>Group Kelas</td>
                                <td>:</td>
                                <td>'.$DataEmail['Group'].'</td>
                            </tr>
                            <tr>
                                <td>Sesi (Pertemuan ke)</td>
                                <td>:</td>
                                <td>'.$DataEmail['Session'].'</td>
                            </tr>
                            <tr>
                                <td>Alasan</td>
                                <td>:</td>
                                <td>'.$DataEmail['Reason'].'</td>
                            </tr>


                            <tr>
                                <td colspan="3" style="color: #673AB7;">Mengajukan permohonan untuk perubahan daftar hadir mahasiswa</td>
                            </tr>

                            <tr>
                                <td>Sebelumnya</td>
                                <td>:</td>
                                <td>'.$DataEmail['Before'].'</td>
                            </tr>
                            <tr>
                                <td>Menjadi</td>
                                <td>:</td>
                                <td>'.$DataEmail['After'].'</td>
                            </tr>

                            </tbody>
                        </table>
                        <br/>
                        <p>
                            Demikian permohonan ini saya ajukan, mohon dapat diproses sesuai dengan ketentuan yang berlaku. Terima kasih
                        </p>
                        <br/>
                        <br/>

                        <table  width="100%" cellspacing="5" cellpadding="1" border="0">
                            <tr>
                                <td style="width: 100%;" align="center">
                                    Approved By
                                    <br/>
                                    <h3 style="color: #009688;margin-top: 7px;">'.$dataKaprodi[0]['Name'].'
                                        <br/>
                                        <small>'.$data_arr['Updated1By'].'</small>
                                    </h3>
                                </td>

                            </tr>
                        </table>

                    </div>
                </div>';
                    $data = array(
                        'to' => $mailTo,
                        'subject' => 'Kaprodi : Modify Attendance Approved',
                        'text' => $bodyEmail,
                        'auth' => 's3Cr3T-G4N'
                    );

                    $this->sendMailRest($data);
                }


                return print_r(1);
            }
            else if($data_arr['action']=='approveAllModifyAttd'){
                $ProdiID = $data_arr['ProdiID'];
                $SemesterID = $data_arr['SemesterID'];

                $dataAttd = $this->db->query('SELECT am.* 
                                                            FROM db_academic.attendance_modify_prodi amp 
                                                            LEFT JOIN db_academic.attendance_modify am ON (am.ID = amp.IDAM)
                                                            LEFT JOIN db_academic.attendance attd ON (attd.ID = am.ID_Attd)
                                                            WHERE amp.ProdiID = "'.$ProdiID.'" AND attd.SemesterID = "'.$SemesterID.'"
                                                            AND am.Status = "0" ')->result_array();

                if(count($dataAttd)>0){
                    foreach ($dataAttd AS $item){

                        // Update Attendance Student
                        $dataStd = $this->db->get_where('db_academic.attendance_modify_details',array('IDAM' => $item['ID']))->result_array();
                        if(count($dataStd)>0){
                            foreach ($dataStd AS $itemStd){
                                $dataUpdate = array(
                                    'M'.$itemStd['Sesi'] => ''.$itemStd['Meet'],
                                    'D'.$itemStd['Sesi'] => $itemStd['Reason']
                                );
                                $this->db->where('ID', $itemStd['IDAS']);
                                $this->db->update('db_academic.attendance_students',$dataUpdate);
                                $this->db->reset_query();
                            }
                        }

                        // Update Status
                        $this->db->where('ID', $item['ID']);
                        $this->db->update('db_academic.attendance_modify',array(
                            'Status' => '1',
                            'Updated1By' => $data_arr['Updated1By'],
                            'Updated1At' => $data_arr['Updated1At']
                        ));
                        $this->db->reset_query();

                        $dataKaprodi = $this->db->select('Name,Photo')->get_where('db_employees.employees',
                            array('NIP' => $data_arr['Updated1By']))->result_array();

                        if(count($dataKaprodi)>0){
                            $DataEmail = $this->getInputToken2($item['DataEmail']);

                            //============= Logging ==========
                            // Insert Logging
                            $url = base_url('uploads/employees/'.$dataKaprodi[0]['Photo']);
                            $img_profile = ($this->is_url_exist($url) && $dataKaprodi[0]['Photo']!='')
                                ? $url
                                : url_server_ws.'/images/icon/lecturer.png';

                            $Log_dataInsert = array(
                                'Icon' => $img_profile,
                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> Modify Attendance Approved',
                                'Description' => $DataEmail['Code'].' - '.$DataEmail['CourseEng'].' | Group : '.$DataEmail['Group'].' | Session : '.$DataEmail['Session'],
                                'URLDirectLecturer' => 'attendance/modify-attendance/'.$item['TokenURL'],
                                'CreatedBy' => $data_arr['Updated1By'],
                                'CreatedName' => $dataKaprodi[0]['Name'],
                                'CreatedAt' => $data_arr['Updated1At']
                            );
                            $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                            $insert_id_logging = $this->db->insert_id();

                            // insert ke user
                            $Log_arr_ins = array(
                                'IDLogging' => $insert_id_logging,
                                'UserID' => $item['RequestBy']
                            );
                            $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                        }

                    }
                }

                return print_r(1);

            }

        }
    }


    public function getMonitoringAttendance(){

        $requestData= $_REQUEST;

        $data_arr = $this->getInputToken();

        $ProgramsCampusID = $data_arr['ProgramsCampusID'];
        $SemesterID = $data_arr['SemesterID'];
        $ProdiID = $data_arr['ProdiID'];
        $DayID = $data_arr['DayID'];

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( s.ClassGroup LIKE "%'.$search.'%" OR mk.MKCode LIKE "%'.$search.'%"
                           OR mk.NameEng LIKE "%'.$search.'%" OR d.NameEng LIKE "%'.$search.'%"
                           )';
        }

        $w_Prodi = ($ProdiID!='' && $ProdiID!=null) ? ' AND sdc.ProdiID = "'.$ProdiID.'" ' : '';
        $w_Day = ($DayID!='' && $DayID!=null) ? ' AND sd.DayID = "'.$DayID.'" ' : '';



        $queryDefault = 'SELECT s.ID AS ScheduleID, attd.ID AS ID_Attd,sd.ID AS SDID,s.Coordinator , s.ClassGroup, s.TeamTeaching,   
                                        mk.MKCode, mk.NameEng AS CourseEng,
                                        mk.Name AS Course, d.NameEng AS DayEng, cl.Room,
                                        cd.TotalSKS AS Credit, sd.StartSessions, sd.EndSessions, em.Name AS Lecturer
                                        FROM db_academic.schedule_details sd 
                                        LEFT JOIN db_academic.schedule s ON (sd.ScheduleID = s.ID)  
                                        LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)            
                                        LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)                   
                                        LEFT JOIN db_academic.schedule_details_course sdc ON (s.ID = sdc.ScheduleID)
                                        LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                        LEFT JOIN db_academic.attendance attd ON (attd.SemesterID = s.SemesterID AND attd.SDID = sd.ID)
                                        WHERE ( s.ProgramsCampusID = "'.$ProgramsCampusID.'" AND 
                                        s.SemesterID = "'.$SemesterID.'" '.$w_Prodi.' '.$w_Day.' ) '.$dataSearch.'
                                        GROUP BY sd.ID
                                        ORDER BY d.ID, sd.StartSessions, s.ID ASC ';


        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';
        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'];
        $data = array();

        $tempG = '';
        for($i=0;$i<count($query);$i++) {
            $nestedData = array();
            $row = $query[$i];


            if($row['ClassGroup']!=$tempG){
                $no++;
            }
            $tempG = $row['ClassGroup'];


            // ==== Attendance Lecturer ====

            $arrLec = [$row['Coordinator']];
            $arrLecName = [$row['Lecturer']];
            if($row['TeamTeaching']==1 || $row['TeamTeaching']=='1'){
                $dataLec = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule_team_teaching stt 
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                        WHERE stt.ScheduleID = "'.$row['ScheduleID'].'"')->result_array();
                if(count($dataLec)>0){
                    foreach ($dataLec AS $item){
                        array_push($arrLec,$item['NIP']);
                        array_push($arrLecName,$item['Name']);
                    }
                }
            }

            $showLec = '';
            $dataLec = [];
            // Load Data Teacher
            if(count($arrLec)>0){
                for ($t=0;$t<count($arrLec);$t++){
                    // Get Attendance

                    $dataAttd = $this->db->query('SELECT COUNT(*) AS P FROM db_academic.attendance_lecturers attd_l
                                                              LEFT JOIN db_academic.attendance attd ON (attd.ID = attd_l.ID_Attd)
                                                              WHERE attd.ID = "'.$row['ID_Attd'].'"
                                                              AND attd_l.NIP = "'.$arrLec[$t].'" ')->result_array();

                    $br = ($t!=0) ? '<br/>' : '';
                    $showLec = $showLec.''.$br.''.$arrLecName[$t].' - <span style="color: orangered;">'.$dataAttd[0]['P'].'</span>';

                    $arr = array(
                        'NIP' => $arrLec[$t],
                        'Name' => $arrLecName[$t]
                    );
                    array_push($dataLec,$arr);
                }
            }

            // ==== Attendance Student ====
            $dataStd = $this->db->query('SELECT * FROM attendance_students attd_s 
                                                    LEFT JOIN db_academic.attendance attd ON (attd.ID = attd_s.ID_Attd)
                                                    WHERE 
                                                    attd.ID = "'.$row['ID_Attd'].'"
                                                    ')->result_array();

            $arrP = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];
            $arrA = [0,0,0,0,0,0,0,0,0,0,0,0,0,0,0,0];

            if(count($dataStd)>0){
                foreach ($dataStd as $items){
                    $noArr = 0;
                    for($l=1;$l<=14;$l++){
                        if($items['M'.$l]==1 || $items['M'.$l]=='1'){
                            $arrP[$noArr] = $arrP[$noArr]+1;
                        }
                        else if($items['M'.$l]==2 || $items['M'.$l]=='2'){
                            $arrA[$noArr] = $arrA[$noArr]+1;
                        }

                        $noArr ++;
                    }

                    // UTS
                    if($items['UTS']==1 || $items['UTS']=='1'){
                        $arrP[14] = $arrP[14] + 1;
                    } else if($items['UTS']==1 || $items['UTS']=='1'){
                        $arrA[14] = $arrA[14] + 1;
                    }

                    // UAS
                    if($items['UAS']==1 || $items['UAS']=='1'){
                        $arrP[15] = $arrP[15] + 1;
                    } else if($items['UAS']==1 || $items['UAS']=='1'){
                        $arrA[15] = $arrA[15] + 1;
                    }

                }
            }

            $nestedData[] = '<div  style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div  style="text-align:left;"><a href="'.base_url('academic/attendance/details-attendace/'.$row['ScheduleID']).'"><b>'.$row['MKCode'].' - '.$row['CourseEng'].'</b></a><br/>'.$row['Course'].'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['ClassGroup'].'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.$row['Credit'].'</div>';
            $nestedData[] = '<div  style="text-align:center;">'.count($dataStd).'</div>';
            $nestedData[] = '<div  style="text-align:left;">'.$showLec.'<textarea class="hide" id="dateLec'.$row['ID_Attd'].'">'.json_encode($dataLec).'</textarea></div>';
            $nestedData[] = '<div  style="text-align:right;"><b>'.$row['DayEng'].'</b><br/>'.
                substr($row['StartSessions'],0,5).' - '.substr($row['EndSessions'],0,5).' | '.$row['Room'].'</div>';

            $meetTemp = 1;
            for($l=0;$l<count($arrP);$l++){
                $totalStd = $arrP[$l]+$arrA[$l];
                if($totalStd==0){



                    $sw = '<div  style="text-align:center;">-</div>';

                    if($meetTemp<=14){
                        // Cek attd lecturer
                        $dataMeetLec = $this->db->query('SELECT * FROM db_academic.attendance_lecturers 
                                                                WHERE ID_Attd = "'.$row['ID_Attd'].'" 
                                                                AND Meet = "'.$meetTemp.'" ')->result_array();

                        if(count($dataMeetLec)<=0){
                            // Cek apakah sesi sudah 0 / null
                            $dataDSes = $this->db->select('Meet'.$meetTemp)
                                ->get_where('db_academic.attendance',array('ID' => $row['ID_Attd']))->result_array();
                            if($dataDSes[0]['Meet'.$meetTemp]=='1' || $dataDSes[0]['Meet'.$meetTemp]==1){
                                $sw = '<div  style="text-align:center;"><i class="fa fa-exclamation-circle fa-2x" style="color: #ff9800;"></i></div>';
                            }
                        }

                    }



                    $nestedData[] = $sw;

                } else {
                    $nestedData[] = '<div  style="text-align:center;"><span class="label label-success labelAttd">'.$arrP[$l].'</span><br/><span class="label label-danger labelAttd">'.$arrA[$l].'</span></div>';
                }
                $meetTemp+=1;
            }


            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval( count($queryDefaultRow) ),
            "data"            => $data
        );

        echo json_encode($json_data);


    }

    public function crudAttendance2(){
        $data_arr = $this->getInputToken();
        if (count($data_arr) > 0) {
            if($data_arr['action']=='loadScheduleDetails'){
                $ScheduleID = $data_arr['ScheduleID'];

                // Get Course
                $dataCourse = $this->db->query('SELECT mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup, em.NIP, em.Name, s.TeamTeaching FROM db_academic.schedule s 
                                                          LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                          LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                          LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                          WHERE s.ID = "'.$ScheduleID.'" GROUP BY s.ID LIMIT 1')->result_array();

                // Get Lecturer
                $dataLecturer = [array(
                    'NIP' => $dataCourse[0]['NIP'],
                    'Name' => $dataCourse[0]['Name']
                )];

                if($dataCourse[0]['TeamTeaching']==1 || $dataCourse[0]['TeamTeaching']=='1'){
                    $dataT = $this->db->query('SELECT em.NIP,em.Name FROM db_academic.schedule_team_teaching stt 
                                                              LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                              WHERE stt.ScheduleID = "'.$ScheduleID.'" ORDER BY em.NIP ')->result_array();

                    if(count($dataT)>0){
                        foreach ($dataT as $item){
                            $ar = array(
                                'NIP' => $item['NIP'],
                                'Name' => $item['Name']
                            );
                            array_push($dataLecturer,$ar);
                        }
                    }
                }


                $dataSchedule = $this->db->query('SELECT attd.ID AS ID_Attd ,sd.ID AS SDID, sd.StartSessions, sd.EndSessions, cl.Room, d.NameEng AS DayEng FROM db_academic.schedule_details sd 
                                                    LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                    LEFT JOIN db_academic.attendance attd ON (attd.ScheduleID = sd.ScheduleID AND attd.SDID = sd.ID)
                                                    WHERE sd.ScheduleID = "'.$ScheduleID.'" 
                                                    ORDER BY sd.DayID, sd.StartSessions ASC ')->result_array();


                $result = array(
                    'Course' => $dataCourse,
                    'Lecturer' => $dataLecturer,
                    'Schedule' => $dataSchedule
                );

                return print_r(json_encode($result));
            }
            else if($data_arr['action']=='readDetailAttendance'){
                $ScheduleID = $data_arr['ScheduleID'];
                $SDID = $data_arr['SDID'];

                $dataAttd = $this->db->query('SELECT * FROM db_academic.attendance attd 
                                        WHERE attd.ScheduleID = "'.$ScheduleID.'" AND attd.SDID = "'.$SDID.'" LIMIT 1')->result_array();

                $result = [];
                if(count($dataAttd)>0){

                    $ID_Attd = $dataAttd[0]['ID'];

                    for ($i=1;$i<=14;$i++){
                        $bap = $this->db->query('SELECT b.*, auts.Name AS Student, em.Name AS Lecturer
                                                  FROM db_academic.attendance_bap b
                                                  LEFT JOIN db_academic.auth_students auts ON (auts.NPM = b.StudentSignBy)
                                                  LEFT JOIN db_employees.employees em ON (em.NIP = b.NIP)
                                                  WHERE b.ID_Attd = "'.$ID_Attd.'"
                                                   AND b.Sesi = "'.$i.'" ')->result_array();

                        // Get Present
                        $p = ' AND atts.M'.$i.' = "1" ';
                        $present = $this->db->query('SELECT * FROM db_academic.attendance_students atts 
                                                      WHERE atts.ID_Attd = "'.$ID_Attd.'" '.$p)->result_array();

                        // Get Absent
                        $a = ' AND atts.M'.$i.' = "2" ';
                        $absent = $this->db->query('SELECT * FROM db_academic.attendance_students atts 
                                                      WHERE atts.ID_Attd = "'.$ID_Attd.'" '.$a)->result_array();

                        // Get Time Attendance
                        $dataLect = $this->db->query('SELECT al.*, em.Name AS Lecturer FROM db_academic.attendance_lecturers al
                                                      LEFT JOIN db_employees.employees em ON (em.NIP = al.NIP)
                                                      WHERE al.ID_Attd = "'.$ID_Attd.'" AND al.Meet = "'.$i.'" ')->result_array();

                        $countPresent = (count($present)==0 && count($absent)==0) ? '-' : count($present);
                        $countAbsent = (count($present)==0 && count($absent)==0) ? '-' : count($absent);


                        // Cek apakah ada kelas pengganti
                        $dataExc = $this->db->query('SELECT exc.*,cl.Room, cl.Seat, cl.SeatForExam FROM db_academic.schedule_exchange exc 
                                                                LEFT JOIN db_academic.classroom cl ON (cl.ID = exc.ClassroomID)
                                                                WHERE exc.ID_Attd = "'.$ID_Attd.'" AND Meeting = "'.$i.'" LIMIT 1')->result_array();


                        $arrRes = array(
                            'StatusSesi' => $dataAttd[0]['Meet'.$i],
                            'Present' => $countPresent,
                            'Absent' => $countAbsent,
                            'Lecturer' => $dataLect,
                            'BAP' => $bap,
                            'Exchange' => $dataExc
                        );
                        array_push($result,$arrRes);
                    }

                }

                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='updateAttendanceLecturer'){

                $ID_Attd = $data_arr['ID_Attd'];
                $Meet = $data_arr['Meet'];
                $NIP = $data_arr['NIP'];

                // Cek apakah data sudah ada
                // 1. Jika ada maka data akan terupdate
                // 2. Jika tidak ada maka insert

                $dataAttd = $this->db->select('ID')->limit(1)->get_where('db_academic.attendance_lecturers',array(
                    'ID_Attd' => $ID_Attd,
                    'Meet' => $Meet,
                    'NIP' => $NIP
                ))->result_array();

                $dataUpdate = array(
                    'Meet' => $Meet,
                    'Date' => $data_arr['Date'],
                    'In' => $data_arr['In'],
                    'Out' => $data_arr['Out']
                );

                if(count($dataAttd)>0){

                    // Update
                    $this->db->where('ID', $dataAttd[0]['ID']);
                    $this->db->update('db_academic.attendance_lecturers',$dataUpdate);


                } else {
                    $dataUpdate['ID_Attd'] = $ID_Attd;
                    $dataUpdate['NIP'] = $NIP;
                    // Insert
                    $this->db->insert('db_academic.attendance_lecturers', $dataUpdate);
                }


                // Update Attendance
                $dataUpAt = array(
                    'Meet'.$Meet => '1',
                    'Date'.$Meet => $data_arr['Date']
                );
                $this->db->where('ID', $ID_Attd);
                $this->db->update('db_academic.attendance',$dataUpAt);

                return print_r(1);

            }
            else if($data_arr['action']=='deleteAttendanceLecturer'){
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_academic.attendance_lecturers');
                return print_r(1);
            }
            else if($data_arr['action']=='readAttdStudent'){

                $ID_Attd = $data_arr['ID_Attd'];
                $Meet = $data_arr['Meet'];

                //cek apakah status aatd sudah 1 atau blm
                $dataAttd = $this->db->query('SELECT Meet'.$Meet.' AS StatusMeet FROM db_academic.attendance attd 
                                                                    WHERE attd.ID = "'.$ID_Attd.'" LIMIT 1')->result_array();

                // Get Attendance Setting
                $dataSeting = $this->db->query('SELECT attdSet.* FROM db_academic.attendance_setting attdSet 
                                                            LEFT JOIN db_academic.semester s ON (s.ID = attdSet.SemesterID)
                                                            WHERE s.Status = "1" ')->result_array();

                $dataStd = $this->db->query('SELECT attds.ID, ats.Name, ats.NPM, attds.D'.$Meet.' AS D, attds.M'.$Meet.' AS M FROM db_academic.attendance_students attds 
                                                        LEFT JOIN db_academic.auth_students ats ON (ats.NPM = attds.NPM)
                                                        WHERE attds.ID_Attd = "'.$ID_Attd.'" ORDER BY ats.NPM ASC')->result_array();

                $result = array(
                    'Attendance' => $dataAttd,
                    'Setting' => $dataSeting,
                    'Students' => $dataStd
                );

                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='UpdateStudentAttd'){
                $Meet = $data_arr['Meet'];

                $attdStudent = (array) $data_arr['attdStudent'];

                if(count($attdStudent)>0){
                    foreach ($attdStudent AS $item){
                        $Update = array(
                            'M'.$Meet => $item->M,
                            'D'.$Meet => $item->D
                        );
                        $this->db->where('ID', $item->ID);
                        $this->db->update('db_academic.attendance_students',$Update);
                    }
                }

                return print_r(1);


            }
            else if($data_arr['action']=='loadBAP'){

                $Sesi = $data_arr['Sesi'];
                $ID_Attd = $data_arr['ID_Attd'];

                // Cek apakah ada BAP
                $dataBAP = $this->db->limit(1)->get_where('db_academic.attendance_bap'
                    ,array(
                        'ID_Attd' => $ID_Attd,
                        'Sesi' => $Sesi
                    ))->result_array();

                return print_r(json_encode($dataBAP));
            }
            else if($data_arr['action']=='updateExhange'){

                $EXID = $data_arr['EXID'];
                $dataUpdate = (array) $data_arr['dataUpdate'];


                // Get Semester Active
                $SemesterActive = $this->m_api->_getSemesterActive();

                // Cek bentrok
                $dataFilter  = array(
                    'SemesterID' => $SemesterActive['ID'],
                    'IsSemesterAntara' => '0',
                    'ClassroomID' =>  $dataUpdate['ClassroomID'],
                    'DayID' =>  $dataUpdate['DayID'],
                    'StartSessions' =>  $dataUpdate['StartSessions'],
                    'EndSessions' => $dataUpdate['EndSessions']
                );
                $dataConflict1 = $this->m_api->__checkSchedule($dataFilter);

                $dataGetRoom = $this->db->select('Room')->get_where('db_academic.classroom',array('ID' => $dataUpdate['ClassroomID']))->result_array();

                // Cek Bentrok Sesama Exchange
                $dataConflict2 = $this->db->query('SELECT exch.ID, exch.Date, exch.StartSessions, exch.EndSessions ,mk.NameEng AS CourseEng, mk.MKCode, s.ClassGroup, cl.Room  
                                                              FROM db_academic.schedule_exchange exch
                                                              LEFT JOIN db_academic.attendance attd ON (attd.ID = exch.ID_Attd)
                                                              LEFT JOIN db_academic.schedule s ON (s.ID = attd.ScheduleID)
                                                              LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = attd.ScheduleID)
                                                              LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                              LEFT JOIN db_academic.classroom cl ON (cl.ID = exch.ClassroomID)
                                                              WHERE exch.ID != "'.$EXID.'" AND exch.Date = "'.$dataUpdate['Date'].'"
                                                              AND exch.ClassroomID = "'.$dataUpdate['ClassroomID'].'" AND exch.Status = "2"
                                                              AND (("'.$dataUpdate['StartSessions'].'" >= exch.StartSessions  AND "'.$dataUpdate['StartSessions'].'" <= exch.EndSessions) OR
                                                                      ("'.$dataUpdate['EndSessions'].'" >= exch.StartSessions AND "'.$dataUpdate['EndSessions'].'" <= exch.EndSessions) OR
                                                                      ("'.$dataUpdate['StartSessions'].'" <= exch.StartSessions AND "'.$dataUpdate['EndSessions'].'" >= exch.EndSessions)
                                                                      ) ')->result_array();


                $DateStart = date("Y-m-d H:i:s", strtotime($dataUpdate['Date'].$dataUpdate['StartSessions']));
                $DateEnd = date("Y-m-d H:i:s", strtotime($dataUpdate['Date'].$dataUpdate['EndSessions']));
                $Room = $dataGetRoom[0]['Room'];


                $sql2 = 'select count(*) as Total from db_reservation.t_booking as a
                                 join db_employees.employees as b on a.CreatedBy = b.NIP
                                 where a.Status in(0,1) and (
                                    (a.`Start` >= "'.$DateStart.'" and a.`Start` < "'.$DateEnd.'" ) 
                                    or (a.`End` > "'.$DateStart.'" and a.`End` <= "'.$DateEnd.'" )
                                    or (
                                            a.`Start` <= "'.$DateStart.'" and a.`End` >= "'.$DateEnd.'"
                                        )
                                ) and a.Room = "'.$Room.'"';


                $query3=$this->db->query($sql2)->result_array();

                if(count($dataConflict1)>0 ||
                    count($dataConflict2)>0){
                    $result = array(
                        'Status' => 0,
                        'Schedule' => $dataConflict1,
                        'Exchange' => $dataConflict2,
                        'Vanue' => $query3[0]['Total']
                    );
                } else {
                    $this->db->where('ID', $EXID);
                    $this->db->update('db_academic.schedule_exchange',$dataUpdate);
                    $result = array(
                        'Status' => 1,
                        'Vanue' => $query3[0]['Total']
                    );
                }

                return print_r(json_encode($result));


            }
            else if($data_arr['action']=='delteExhange'){

                $Log_dataInsert = $data_arr['Logging'];
                $this->db->insert('db_notifikasi.logging',$Log_dataInsert);
                $insert_id_logging = $this->db->insert_id();

                // Insert logging
                $Log_arr_ins = array(
                    'IDLogging' => $insert_id_logging,
                    'UserID' => $data_arr['UserID']
                );
                // Send Notif To Kaprodi
                $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);

                // Cek apakah kaprodi sudah Approve atau belum
                if($data_arr['Updated1By']!='' && $data_arr['Updated1By']!=null){
                    // Insert logging
                    $Log_arr_ins = array(
                        'IDLogging' => $insert_id_logging,
                        'UserID' => $data_arr['Updated1By']
                    );
                    // Send Notif To Kaprodi
                    $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);
                }


                // ==== Action remove =====


                $EXID = $data_arr['EXID'];

                $this->db->where('ID', $EXID);
                $this->db->delete('db_academic.schedule_exchange');
                $this->db->reset_query();

                $this->db->where('EXID', $EXID);
                $this->db->delete('db_academic.schedule_exchange_prodi');

                return print_r(1);


            }
            else if($data_arr['action']=='cleanAttendance'){
                $Sesi = $data_arr['Sesi'];
                $ID_Attd = $data_arr['ID_Attd'];
                $User = $data_arr['User'];

                // Cek user yg dihapus


                if($User==0 || $User=='0'){

                    // Nullkan Attd Student
                    $this->db->where('ID_Attd',$ID_Attd);
                    $this->db->update('db_academic.attendance_students', array(
                        'M'.$Sesi => null,
                        'D'.$Sesi => ''
                    ));
                    $this->db->reset_query();

                    // Hapus Attd Lecturer
                    $this->db->where(array(
                        'ID_Attd' => $ID_Attd,
                        'Meet' => $Sesi
                    ));
                    $this->db->delete('db_academic.attendance_lecturers');
                    $this->db->reset_query();

                    // Null kan data Attendance
                    $this->db->where('ID',$ID_Attd);
                    $this->db->update('db_academic.attendance', array(
                        'Meet'.$Sesi => null,
                        'BAP'.$Sesi => null,
                        'Venue'.$Sesi => null,
                        'Equipments'.$Sesi => null
                    ));

                }
                else if($User==1 || $User=='1'){
                    // Nullkan Attd Student
                    $this->db->where('ID_Attd',$ID_Attd);
                    $this->db->update('db_academic.attendance_students', array(
                        'M'.$Sesi => null,
                        'D'.$Sesi => null
                    ));
                    $this->db->reset_query();
                }
                else if($User==2 || $User=='2'){
                    // Hapus Attd Lecturer
                    $this->db->where(array(
                        'ID_Attd' => $ID_Attd,
                        'Meet' => $Sesi
                    ));
                    $this->db->delete('db_academic.attendance_lecturers');
                    $this->db->reset_query();
                }


                return print_r(1);

            }
            else if($data_arr['action']=='readAttdSetting'){
                $SemesterID = $data_arr['SemesterID'];
                $data = $this->db
                    ->get_where('db_academic.attendance_setting'
                        ,array('SemesterID' => $SemesterID))->result_array();
                return print_r(json_encode($data));
            }

            // === Exam ===
            else if($data_arr['action']=='readAttendanceExam'){
                $ExamID = $data_arr['ID'];

//                $data = $this->db->query('SELECT ex.*, ats.Name FROM db_academic.exam_details ex
//                                                    LEFT JOIN db_academic.auth_students ats
//                                                    ON (ats.NPM = ex.NPM)
//                                                    WHERE ex.ExamID = "'.$ExamID.'"
//                                                    ORDER BY ex.NPM ASC')->result_array();

                $result = [];
                $data = $this->m_rest->getListStudentExam($ExamID);

                if($data['Status']==1 || $data['Status']=='1'){
                    $result = $data['DetailStudents'];
                }

                return print_r(json_encode($result));

            }

            else if($data_arr['action']=='insertAttdExam'){

                $arrAttd = $data_arr['arrAttd'];

                if(count($arrAttd)>0){
                    for($i=0;$i<count($arrAttd);$i++){
                        $d = (array) $arrAttd[$i];
                        $this->db->set('Status', $d['Status']);
                        $this->db->where('ID', $d['ExamDetailsID']);
                        $this->db->update('db_academic.exam_details');
                    }
                }



                return print_r(1);
            }

            // === Penutup Exam ===

        }
    }

    public function crudAnnouncement(){
        $data_arr = $this->getInputToken();
        if (count($data_arr) > 0) {

            if($data_arr['action']=='readStudent2Annc'){

                $selct = 'NPM,Name';

                if($data_arr['Year']!=0 && $data_arr['ProdiID']!=0){
                    $q = 'SELECT '.$selct.' FROM db_academic.auth_students WHERE Year = "'.$data_arr['Year'].'" 
                    AND ProdiID = "'.$data_arr['ProdiID'].'"  ORDER BY NPM ASC';
                }
                else if($data_arr['Year']==0 && $data_arr['ProdiID']!=0){
                    $q = 'SELECT '.$selct.' FROM db_academic.auth_students WHERE ProdiID = "'.$data_arr['ProdiID'].'"  ORDER BY NPM ASC';
                }
                else if($data_arr['Year']!=0 && $data_arr['ProdiID']==0){
                    $q = 'SELECT '.$selct.' FROM db_academic.auth_students WHERE Year = "'.$data_arr['Year'].'" ORDER BY NPM ASC';
                }
                else {
                    $q = 'SELECT '.$selct.' FROM db_academic.auth_students ORDER BY NPM ASC';
                }

                $dataStudent = $this->db->query($q)->result_array();

                return print_r(json_encode($dataStudent));

            }
            else if($data_arr['action']=='getStudentServerSide'){
                $Key = $data_arr['Key'];

                $data = $this->db->query('SELECT NPM, Name FROM db_academic.auth_students 
                                                            WHERE NPM LIKE "%'.$Key.'%" OR 
                                                            Name LIKE "%'.$Key.'%" ORDER BY NPM ASC LIMIT 10 ')->result_array();

                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='getLecturerServerSide'){
                $Key = $data_arr['Key'];
                $data = $this->db->query('SELECT em.NIP,em.Name FROM db_employees.employees em 
                                                    WHERE em.NIP LIKE "%'.$Key.'%" OR em.Name LIKE "%'.$Key.'%"
                                                     ORDER BY em.NIP ASC LIMIT 10 ')->result_array();
                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='createAnnouncement'){

                $max_execution_time = 360*5;
                ini_set('memory_limit', '-1');
                ini_set('max_execution_time', $max_execution_time); //60 seconds = 1 minutes

                $dataAnnc = (array) $data_arr['dataAnnc'];

                $this->db->insert('db_notifikasi.announcement',$dataAnnc);
                $insert_id = $this->db->insert_id();

                // Student
                $anncStd = (array) $data_arr['anncStd'];
                if(count($anncStd)>0){
                    for($i=0;$i<count($anncStd);$i++){
                        $d = (array) $anncStd[$i];
                        $insr = array(
                            'IDAnnc' => $insert_id,
                            'NPM' => $d['NPM']
                        );
                        $this->db->insert('db_notifikasi.announcement_student',$insr);
                    }
                }

                // Employees
                $anncLec = (array) $data_arr['anncLec'];
                if(count($anncLec)>0){
                    for($i=0;$i<count($anncLec);$i++){
                        $d = (array) $anncLec[$i];
                        $insr = array(
                            'IDAnnc' => $insert_id,
                            'NIP' => $d['NIP']
                        );
                        $this->db->insert('db_notifikasi.announcement_employees',$insr);
                    }
                }

                return print_r($insert_id);


            }
            else if($data_arr['action']=='showAnnc_UnreadOnly'){
                $User = $data_arr['User'];
                $UserID = $data_arr['UserID'];

                $db = ($User=='Std') ? 'announcement_student' : 'announcement_employees';
                $w = ($User=='Std') ? 'NPM' : 'NIP';

                $data = $this->db->query('SELECT * FROM db_notifikasi.'.$db.' u 
                                                   LEFT JOIN db_notifikasi.announcement anc ON (anc.ID = u.IDAnnc) 
                                                   WHERE u.Read = "0" AND u.'.$w.' = "'.$UserID.'" ORDER BY anc.ID DESC ')->result_array();

                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='showAnnouncementActive'){
                $dateNow = $this->m_rest->getDateNow();

                $User = $data_arr['User'];
                $UserID = $data_arr['UserID'];

                $db = ($User=='Std') ? 'announcement_student' : 'announcement_employees';
                $w = ($User=='Std') ? 'NPM' : 'NIP';

                $data = $this->db->query('SELECT * FROM db_notifikasi.'.$db.' annu 
                                                  LEFT JOIN db_notifikasi.announcement ann 
                                                  ON (annu.IDAnnc = ann.ID)
                                                  WHERE annu.'.$w.' = "'.$UserID.'" AND 
                                                  ann.Start <= "'.$dateNow.'" AND 
                                                  ann.End >= "'.$dateNow.'" ORDER BY ann.ID DESC ')->result_array();

                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='showAnnouncementSaved'){

                $User = $data_arr['User'];
                $UserID = $data_arr['UserID'];

                $db = ($User=='Std') ? 'db_notifikasi.announcement_student' : 'db_notifikasi.announcement_employees';
                $w = ($User=='Std') ? 'NPM' : 'NIP';

                $data = $this->db->query('SELECT * FROM '.$db.' annu 
                                                  LEFT JOIN db_notifikasi.announcement ann 
                                                  ON (annu.IDAnnc = ann.ID)
                                                  WHERE annu.'.$w.' = "'.$UserID.'" AND 
                                                  annu.Read = "2"  ORDER BY ann.ID DESC
                                                  ')->result_array();

                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='showCountAnnouncementSaved'){

                $User = $data_arr['User'];
                $UserID = $data_arr['UserID'];

                $db = ($User=='Std') ? 'db_notifikasi.announcement_student' : 'db_notifikasi.announcement_employees';
                $w = ($User=='Std') ? 'NPM' : 'NIP';

                $data = $this->db->query('SELECT COUNT(*) AS Total FROM '.$db.' annu 
                                                  LEFT JOIN db_notifikasi.announcement ann 
                                                  ON (annu.IDAnnc = ann.ID)
                                                  WHERE annu.'.$w.' = "'.$UserID.'" AND 
                                                  annu.Read = "2"
                                                  ')->result_array();

                return print_r(json_encode($data));

            }
            else if($data_arr['action']=='readDetailAnnc'){

                $IDAnnc = $data_arr['IDAnnc'];
                $User = $data_arr['User'];
                $UserID = $data_arr['UserID'];

                $db = ($User=='Std') ? 'db_notifikasi.announcement_student' : 'db_notifikasi.announcement_employees';
                $w = ($User=='Std') ? 'NPM' : 'NIP';

                $dataUsr = $this->db->get_where($db,array('IDAnnc' => $IDAnnc, $w => $UserID))->result_array();

                if($dataUsr[0]['Read']==0 || $dataUsr[0]['Read']=='0'){
                    // Update jadi read
                    $this->db->set('Read', '1');
                    $this->db->where(array('IDAnnc' => $IDAnnc, $w => $UserID));
                    $this->db->update($db);
                }



                $data = $this->db->get_where('db_notifikasi.announcement',array(
                    'ID' => $IDAnnc
                ))->result_array();

                $data[0]['FileURL'] = ($data[0]['File']!='' && $data[0]['File']!=null) ? base_url('uploads/announcement/'.$data[0]['File']) : '';


                $result = array(
                    'Annc' => $data,
                    'Status' =>$dataUsr
                );

                return print_r(json_encode($result));

            }
            else if($data_arr['action']=='saveDetailAnnc'){
                $IDAnnc = $data_arr['IDAnnc'];
                $User = $data_arr['User'];
                $UserID = $data_arr['UserID'];
                $Status = $data_arr['Status'];

                $db = ($User=='Std') ? 'db_notifikasi.announcement_student' : 'db_notifikasi.announcement_employees';
                $w = ($User=='Std') ? 'NPM' : 'NIP';

                // Update jadi read
                $this->db->set('Read', $Status);
                $this->db->where(array('IDAnnc' => $IDAnnc, $w => $UserID));
                $this->db->update($db);

                return print_r(1);

            }
            else if($data_arr['action']=='loadAnnouncement'){

                $data = $this->db->get_where('db_notifikasi.announcement',array(
                    'ID' => $data_arr['ID']
                ))->result_array();

                return print_r(json_encode($data));
            }
            else if($data_arr['action']=='updateAnnouncement'){
                $ID = $data_arr['ID'];
                $dataUpdate = (array) $data_arr['dataUpdate'];

                $this->db->where('ID', $ID);
                $this->db->update('db_notifikasi.announcement',$dataUpdate);

                return print_r($ID);

            }
            else if($data_arr['action']=='deleteAnnouncement'){
                $ID = $data_arr['ID'];

                $lf = $data_arr['LastFile'];

                // Cek apakah ada file atau tidak
                if(isset($lf) && $lf!='' && $lf!=null) {
                    // Delete last data dulu
                    if(is_file('./uploads/announcement/'.$lf)){
                        unlink('./uploads/announcement/'.$lf);
                    }
                }

                $tables = array('db_notifikasi.announcement_employees', 'db_notifikasi.announcement_student');
                $this->db->where('IDAnnc', $ID);
                $this->db->delete($tables);

                $this->db->reset_query();
                $this->db->where('ID', $ID);
                $this->db->delete('db_notifikasi.announcement');

                return print_r(1);
            }

        }
    }

    public function getAnnouncement(){
        $requestData= $_REQUEST;

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {

            $search = $requestData['search']['value'];

            $dataSearch = 'WHERE annc.Title LIKE "%'.$search.'%" OR annc.Message LIKE "%'.$search.'%" ';

        }

        $queryDefault = 'SELECT annc.*, em.Name FROM db_notifikasi.announcement annc
                                        LEFT JOIN db_employees.employees em ON (em.NIP = annc.CreatedBy)
                                        '.$dataSearch.'
                                        ORDER BY annc.ID DESC ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';



        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();

        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $file = ($row['File']!=null && $row['File']!='')
                ? '<a class="btn btn-sm btn-default" target="_blank" href="'.base_url('uploads/announcement/'.$row['File']).'"><i class="fa fa-download"></i></a>' : '';

            $dataStd = $this->db->query('SELECT ann.*, auts.Name FROM db_notifikasi.announcement_student ann 
                                                  LEFT JOIN db_academic.auth_students auts ON (auts.NPM = ann.NPM)
                                                  WHERE IDAnnc = "'.$row['ID'].'" ')->result_array();

            $tkn_std = $this->jwt->encode($dataStd,'UAP)(*');
            $swStd = (count($dataStd)>0)
                ? '<a href="javascript:void(0);" class="showUser" data-token="'.$tkn_std.'" data-user="std">'.count($dataStd).' Students</a><br/>' : '';

            $dataEmp = $this->db->query('SELECT ann.*, em.Name FROM db_notifikasi.announcement_employees ann 
                                                  LEFT JOIN db_employees.employees em ON (em.NIP = ann.NIP)
                                                  WHERE IDAnnc = "'.$row['ID'].'" ')->result_array();

            $tkn_em = $this->jwt->encode($dataEmp,'UAP)(*');
            $swEmp = (count($dataEmp)>0)
                ? '<a href="javascript:void(0);" class="showUser" data-token="'.$tkn_em.'" data-user="emp">'.count($dataEmp).' Employees</a>' : '';


            $nestedData[] = '<div style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div style="font-weight: bold;">'.$row['Title'].'</div>';
            $nestedData[] = '<div class="detail-message">'.$row['Message'].'</div>';
            $nestedData[] = '<div style="text-align:right;">'.$swStd.''.$swEmp.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$file.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.date('d M Y',strtotime($row['Start'])).' - '.date('d M Y',strtotime($row['End'])).'</div>';
            $nestedData[] = '<div style="text-align:center;"><a href="'.base_url('announcement/edit-announcement/'.$row['ID']).'" class="btn btn-sm btn-primary"><i class="fa fa-pencil"></i></a></div>';
            $nestedData[] = '<div style="text-align:right;"><b>'.$row['Name'].'</b><br/>'.
                date('d M Y H:i',strtotime($row['CreatedAt'])).'</div>';

            $no++;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval( count($queryDefaultRow) ),
            "data"            => $data
        );
        echo json_encode($json_data);


    }

    public function getSemesterOptionStudent($ClassOf){

        $dataSemester = $this->db->query('SELECT * FROM db_academic.semester s 
                                                    WHERE s.Year >= "'.$ClassOf.'" ORDER BY  s.ID ASC ')->result_array();

        return print_r(json_encode($dataSemester));

    }

    public function changePasswordStudent(){
        $data_arr = $this->getInputToken();

        $NPM = $data_arr['NPM'];

        $Old = $this->genratePassword($NPM,$data_arr['Old']);

        $check = $this->db->select('ID')->get_where('db_academic.auth_students',array(
            'NPM' => $NPM,
            'Password' => $Old
        ))->result_array();

        $result = 0;
        if(count($check)>0){
            $New = $this->genratePassword($NPM,$data_arr['New']);
            $this->db->set('Password', $New);
            $this->db->where('NPM', $NPM);
            $this->db->update('db_academic.auth_students');
            $result = 1;
        }

        return print_r(json_encode($result));

    }

    private function genratePassword($Username,$Password){

        $plan_password = $Username.''.$Password;
        $pas = md5($plan_password);
        $pass = sha1('jksdhf832746aiH{}{()&(*&(*'.$pas.'HdfevgyDDw{}{}{;;*766&*&*');

        return $pass;
    }

    public function getDetailCurriculum(){

        $data_arr = $this->getInputToken();

//        $data_arr = array(
//            'NPM' => 21150002,
//            'ProdiID' => 1,
//            'ClassOf' => 2015
//        );

        $NPM = $data_arr['NPM'];
        $ProdiID = $data_arr['ProdiID'];
        $ClassOf = $data_arr['ClassOf'];

        $db_ = 'ta_'.$ClassOf;

        $dataCID = $this->db->query('SELECT cd.Semester, mk.MKCode, mk.NameEng AS CoureEng, sp.ID AS SPID  FROM db_academic.curriculum_details cd 
                                              LEFT JOIN db_academic.curriculum cur ON (cur.ID = cd.CurriculumID)
                                              LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                              LEFT JOIN '.$db_.'.study_planning sp ON (sp.MKID = cd.MKID AND sp.NPM = "'.$NPM.'")
                                              WHERE cur.Year = "'.$ClassOf.'" AND cd.ProdiID = "'.$ProdiID.'"
                                              GROUP BY cd.ID
                                              ORDER BY cd.Semester ASC, mk.MKCode ASC
                                              ')->result_array();

        return print_r(json_encode($dataCID));


    }



    public function crudSemesterAntara(){
        $data_arr = $this->getInputToken();
        $dataTimeNow = $this->m_rest->getDateTimeNow();

        if($data_arr['action']=='readCourse'){

            $NPM = $data_arr['NPM'];
            $ProdiID = $data_arr['ProdiID'];
            $ClassOf = $data_arr['ClassOf'];
            $Semester = $data_arr['Semester'];

            $db_ = 'ta_'.$ClassOf;

            $w_semester = ($Semester!='' && $Semester!=0)
                ? ' AND cd.Semester = "'.$Semester.'" ' : ' ' ;

            $dataCID = $this->db->query('SELECT cd.Semester, mk.MKCode, mk.NameEng AS CoureEng, cd.ID AS CDID, cd.MKID, cd.TotalSKS AS Credit  FROM db_academic.curriculum_details cd 
                                              LEFT JOIN db_academic.curriculum cur ON (cur.ID = cd.CurriculumID)
                                              LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                              WHERE cur.Year = "'.$ClassOf.'" AND cd.ProdiID = "'.$ProdiID.'" '.$w_semester.'
                                              GROUP BY cd.ID
                                              ORDER BY cd.Semester ASC, mk.MKCode ASC
                                              ')->result_array();

            if(count($dataCID)>0){
                for($i=0;$i<count($dataCID);$i++){
                    $d = $dataCID[$i];

                    $dataC = $this->db->query('SELECT ID AS SPID, Score, Grade, GradeValue, Credit FROM '.$db_.'.study_planning sp 
                                                            WHERE sp.NPM = "'.$NPM.'" 
                                                            AND sp.CDID = "'.$d['CDID'].'" 
                                                            ORDER BY sp.Score DESC LIMIT 1 ')->result_array();

                    // Cek apakah sudah di ambil atau belum
                    $dataAmbil = $this->db->limit(1)->get_where('db_academic.sa_student_details',array(
                        'NPM' => $NPM,
                        'CDID' => $d['CDID']
                    ))->result_array();

                    $dataCID[$i]['DataGet'] = $dataAmbil;

                    $dataCID[$i]['SP'] = $dataC;

                }
            }

            return print_r(json_encode($dataCID));
        }
        else if($data_arr['action']=='readAcademicYearSA'){

            $data = $this->m_rest->_getSemesterAntaraActive();

            if(count($data)>0){
                $dataNow = $this->m_rest->getDateNow();
                $d = $data[0];

                $dataNow_str = strtotime($dataNow);
                $dataStart_str = strtotime($d['StartKRS']);
                $dataEnd_str = strtotime($d['EndKRS']);
                $ShowCourse = ($dataNow_str >= $dataStart_str && $dataNow_str <= $dataEnd_str) ? 1 : 0;

                $data[0]['ShowCourse'] = $ShowCourse;
            }

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='loadSettingSAAcademicYear'){
            $SASemesterID = $data_arr['SASemesterID'];

            $dataSemester = $this->db->get_where('db_academic.sa_academic_years',array(
                'SASemesterID' => $SASemesterID
            ))->result_array();

            return print_r(json_encode($dataSemester));

        }
        else if($data_arr['action']=='updateSAAcademicyear'){

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            $this->db->where('ID', $ID);
            $this->db->update('db_academic.sa_academic_years',$dataForm);

            return print_r(1);

        }
        else if($data_arr['action']=='enteredCourse'){

            // Semester Antara Active
            $dataSA = $this->m_rest->_getSemesterAntaraActive();

            if(count($dataSA)>0){

                $d_SA = $dataSA[0];

                $dataDetails = (array) $data_arr['dataDetails'];
                $NPM = $dataDetails['NPM'];



                // Cek apakah sudah
                $dataIns = array(
                    'SASemesterID' => $d_SA['ID'],
                    'NPM' => $NPM
                );

                $dataStd = $this->db->limit(1)->get_where('db_academic.sa_student',$dataIns)->result_array();


                if(count($dataStd)>0){
                    $IDSAStudent = $dataStd[0]['ID'];
                } else {
                    $dataIns['Mentor'] = $data_arr['Mentor'];
                    $this->db->insert('db_academic.sa_student',$dataIns);
                    $IDSAStudent = $this->db->insert_id();
                }

                // Insert Course
                $dataDetails['IDSAStudent'] = $IDSAStudent;
                $this->db->insert('db_academic.sa_student_details',$dataDetails);
            }

            return print_r(1);


        }
        else if($data_arr['action']=='enteredCourseByAcademic'){

            $dataDetails = (array) $data_arr['dataDetails'];
            $IDSAStudent = $dataDetails['IDSAStudent'];

            // Insert in Student Details
            $this->db->insert('db_academic.sa_student_details',$dataDetails);

            // Create in tagihan
            $this->createTagihanSemesterAntara($IDSAStudent);

            return print_r(1);

        }
        else if($data_arr['action']=='readSelectedCourse'){

            if(isset($data_arr['SASemesterID']) && $data_arr['SASemesterID']!=''){
                $SASemesterID = $data_arr['SASemesterID'];
            } else {
                $dataSA = $this->m_rest->_getSemesterAntaraActive();
                $SASemesterID = $dataSA[0]['ID'];
            }


            $NPM = $data_arr['NPM'];
            $db_ = 'ta_'.$data_arr['ClassOf'];


            // Cek apakah sudah
            $dataIns = array(
                'SASemesterID' => $SASemesterID,
                'NPM' => $NPM
            );

            $dataStd = $this->db->limit(1)->get_where('db_academic.sa_student',$dataIns)->result_array();



            if(count($dataStd)>0){
                $IDSAStudent = $dataStd[0]['ID'];

                $dataCID = $this->db->query('SELECT cd.Semester, mk.MKCode, mk.NameEng AS CoureEng, cd.ID AS CDID, cd.MKID, cd.TotalSKS AS Credit,  
                                                            ssd.Reson, ssd.Updated1At, ssd.Updated2At, ssd.Status, ssd.ID SSDID
                                                            FROM db_academic.sa_student_details ssd
                                                            LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = ssd.CDID)
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssd.MKID)
                                                            WHERE ssd.IDSAStudent = "'.$IDSAStudent.'" ')->result_array();

                if(count($dataCID)>0){
                    for($i=0;$i<count($dataCID);$i++){
                        $d = $dataCID[$i];

                        $dataC = $this->db->query('SELECT ID AS SPID, Score, Grade, GradeValue, Credit FROM '.$db_.'.study_planning sp 
                                                            WHERE sp.NPM = "'.$NPM.'" 
                                                            AND sp.CDID = "'.$d['CDID'].'" 
                                                            ORDER BY sp.Score DESC LIMIT 1 ')->result_array();

                        $dataCID[$i]['SP'] = $dataC;

                    }
                }

                $result = $dataCID;
            }
            else {
                $result = [];
            }

            return print_r(json_encode($result));
        }
        else if($data_arr['action']=='deleteCourseSA'){

            $SSDID = $data_arr['SSDID'];

            $this->db->where('ID', $SSDID);
            $this->db->delete('db_academic.sa_student_details');

            return print_r(1);

        }
        else if($data_arr['action']=='deleteCourseSAByAcademic'){
            $SSDID = $data_arr['SSDID'];

            $this->db->where('ID', $SSDID);
            $this->db->delete('db_academic.sa_student_details');
            $this->db->reset_query();

            $this->db->where('IDSSD', $SSDID);
            $this->db->delete('db_academic.sa_study_planning');
            $this->db->reset_query();


            $IDSAStudent = $data_arr['IDSAStudent'];
            // Create in tagihan
            $this->createTagihanSemesterAntara($IDSAStudent);

            return print_r(1);
        }
        else if($data_arr['action']=='saSend2Mentor'){

            $dataSA = $this->m_rest->_getSemesterAntaraActive();

            if(count($dataSA)>0){

                $d_SA = $dataSA[0];
                $NPM = $data_arr['NPM'];


                $dataIns = array(
                    'SASemesterID' => $d_SA['ID'],
                    'NPM' => $NPM
                );

                $dataStd = $this->db->limit(1)->get_where('db_academic.sa_student',$dataIns)->result_array();


                if(count($dataStd)>0){
                    $IDSAStudent = $dataStd[0]['ID'];

                    $this->db->set('RequestedAt', $this->m_rest->getDateTimeNow());
                    $this->db->where('ID', $IDSAStudent);
                    $this->db->update('db_academic.sa_student');

                    // Get dengan status == 0
                    $dataSD = $this->db->get_where('db_academic.sa_student_details',array(
                        'IDSAStudent' => $IDSAStudent,
                        'Status' => '0'
                    ))->result_array();

                    if(count($dataSD)>0){
                        foreach ($dataSD AS $item){

                            $this->db->set('Status', '1');
                            $this->db->where('ID', $item['ID']);
                            $this->db->update('db_academic.sa_student_details');
                            $this->db->reset_query();

                        }
                    }

                }

            }

            return print_r(1);

        }
        else if($data_arr['action']=='getListSemesterAntara'){

            $data = $this->db->order_by('SemesterID', 'ASC')->get('db_academic.semester_antara')->result_array();

            $result = [];
            if(count($data)>0){
                foreach ($data AS $item){

                    array_push($result,$item);

                    if($item['Status']==1 || $item['Status']=='1'){
                        break;
                    }
                }
            }

            return print_r(json_encode($result));

        }
        else if($data_arr['action']=='getListStudentSemesterAntara'){

            $SASemesterID = $data_arr['SASemesterID'];
            $NIP = $data_arr['NIP'];

            $data = $this->db->query('SELECT sa.*, ats.Name, em.Name AS MentorName FROM db_academic.sa_student sa 
                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = sa.NPM)
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = sa.Mentor)
                                                    WHERE sa.SASemesterID = "'.$SASemesterID.'"
                                                     AND sa.Mentor = "'.$NIP.'" ')->result_array();

            if(count($data)>0){
                for ($i=0;$i<count($data);$i++){
                    $d = $data[$i];
                    $dataDet = $this->db->query('SELECT ssd.*, mk.NameEng AS CourseEng,  mk.MKCode, cd.TotalSKS AS Credit FROM db_academic.sa_student_details ssd 
                                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssd.MKID)
                                                                LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = ssd.CDID)
                                                                WHERE ssd.IDSAStudent = "'.$d['ID'].'" 
                                                                ORDER BY mk.MKCode ASC ')->result_array();

                    $data[$i]['Details'] = $dataDet;
                }
            }

            return print_r(json_encode($data));


        }
        else if($data_arr['action']=='getListStudentSemesterAntara_Kaprodi'){

            $SASemesterID = $data_arr['SASemesterID'];
            $ProdiID = $data_arr['ProdiID'];
            $NIP = $data_arr['NIP'];


            // Load data student yang
            $data = $this->db->query('SELECT sa.*, ats.Name, em.Name AS MentorName FROM db_academic.sa_student sa 
                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = sa.NPM)
                                                    LEFT JOIN db_employees.employees em ON (em.NIP = sa.Mentor)
                                                    WHERE sa.SASemesterID = "'.$SASemesterID.'" AND ats.ProdiID = "'.$ProdiID.'"
                                                    ORDER BY sa.NPM ASC
                                                    ')->result_array();


            if(count($data)>0){
                for ($i=0;$i<count($data);$i++){
                    $d = $data[$i];
                    $dataDet = $this->db->query('SELECT ssd.*, mk.NameEng AS CourseEng,  mk.MKCode, cd.TotalSKS AS Credit FROM db_academic.sa_student_details ssd 
                                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssd.MKID)
                                                                LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = ssd.CDID)
                                                                WHERE ssd.IDSAStudent = "'.$d['ID'].'" 
                                                                ORDER BY mk.MKCode ASC ')->result_array();

                    $data[$i]['Details'] = $dataDet;
                }
            }

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='responSA'){

            $dateTime = $this->m_rest->getDateTimeNow();

            $ID = $data_arr['ID'];
            $Status = $data_arr['Status'];

            $col = ($Status==3 || $Status=='3' || $Status==-3 || $Status=='-3') ? 'Updated2At' : 'Updated1At';
            $dataUp = array(
                'Status' => $Status,
                $col =>$dateTime
            );

            $this->db->set($dataUp);
            $this->db->where('ID', $ID);
            $this->db->update('db_academic.sa_student_details');
            $this->db->reset_query();



            // Jika status == 3 maka create tagihan
            if($Status==3 || $Status=='3'){
                // Get data sa
                $data = $this->db->query('SELECT IDSAStudent FROM db_academic.sa_student_details WHERE ID = "'.$ID.'" ')->result_array();
                $this->createTagihanSemesterAntara($data[0]['IDSAStudent']);
            }

            return print_r(1);

        }
        else if($data_arr['action']=='responSA_ApproveAll'){

            $dateTime = $this->m_rest->getDateTimeNow();

            $IDSAStudent = $data_arr['IDSAStudent'];
            $Status = $data_arr['Status'];

            $col = ($Status==3 || $Status=='3') ? 'Updated2At' : 'Updated1At';
            $dataUp = array(
                'Status' => $Status,
                $col =>$dateTime
            );

            $this->db->set($dataUp);
            $this->db->where('IDSAStudent', $IDSAStudent);
            $this->db->update('db_academic.sa_student_details');

            if($Status==3 || $Status=='3'){
                $this->createTagihanSemesterAntara($IDSAStudent);
            }

            return print_r(1);
        }
        else if($data_arr['action']=='loadCourseSemesterAntara'){

            $SASemesterID = $data_arr['SASemesterID'];

            $data = $this->db->query('SELECT ssd.ID AS IDSSD, ssd.CDID, ssd.IDSAStudent, ssd.MKID, mk.NameEng AS CourseEng, ssd.Status,  
                                                mk.MKCode
                                                FROM db_academic.sa_student_details ssd
                                                LEFT JOIN db_academic.sa_student ss ON (ss.ID = ssd.IDSAStudent)
                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssd.MKID)
                                                WHERE ss.SASemesterID = "'.$SASemesterID.'" AND ssd.Status = "3"
                                                 GROUP BY ssd.CDID ')->result_array();

            if(count($data)>0){
                for ($i=0;$i<count($data);$i++){
                    $d = $data[$i];

                    $dataStd = $this->db->query('SELECT ssd.NPM, ats.Name FROM db_academic.sa_student_details ssd
                                                              LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssd.NPM)
                                                              WHERE ssd.CDID = "'.$d['CDID'].'" AND ssd.Status = "3" ORDER BY ssd.NPM ASC')->result_array();

                    $data[$i]['Students'] = $dataStd;

                }
            }

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='actionSASchedule'){

            $type = $data_arr['type'];

            if($type=='insert'){

                // Cek apakah group sudah ada atau blm
                $dataSch = (array) $data_arr['dataSch'];

                $SASemesterID = $dataSch['SASemesterID'];
                $ClassGroup = $dataSch['ClassGroup'];

                $dataCheck = $this->db->get_where('db_academic.sa_schedule',array(
                    'SASemesterID' => $SASemesterID,
                    'ClassGroup' => $ClassGroup
                ))->result_array();

                if(count($dataCheck)>0){
                    $result = array(
                        'Status' => -1,
                        'Message' => 'Class Group Sama'
                    );
                    return print_r(json_encode($result));
                } else {

                    $this->db->insert('db_academic.sa_schedule',$dataSch);
                    $ScheduleIDSA = $this->db->insert_id();

                    $ArrIDSSD = (array) $data_arr['ArrIDSSD'];

                    if(count($ArrIDSSD)>0){
                        for($i=0;$i<count($ArrIDSSD);$i++){
                            $dataSD = $this->db->select('ID,CDID,MKID')->get_where('db_academic.sa_student_details',
                                array('ID' => $ArrIDSSD[$i]))->result_array();

                            $arrIns = array(
                                'ScheduleIDSA' => $ScheduleIDSA,
                                'IDSSD' => $dataSD[0]['ID'],
                                'CDID' => $dataSD[0]['CDID'],
                                'MKID' => $dataSD[0]['MKID']
                            );
                            $this->db->insert('db_academic.sa_schedule_course',$arrIns);

                        }
                    }

                    // Cek Team Teaching
                    $TeamTeaching = $data_arr['TeamTeaching'];

                    if(count($TeamTeaching)>0){
                        for($i=0;$i<count($TeamTeaching);$i++){
                            $arrTTM = array(
                                'ScheduleIDSA' => $ScheduleIDSA,
                                'NIP' => $TeamTeaching[$i]
                            );
                            $this->db->insert('db_academic.sa_schedule_team_teaching',$arrTTM);
                        }
                    }


//                    print_r($dataSch);
//                    print_r($ClassGroup);

                    $result = array(
                        'Status' => 1
                    );
                    return print_r(json_encode($result));

                }




            }
            else if ($type=='update'){

                $ScheduleIDSA = $data_arr['ScheduleIDSA'];

                // Cek apakah group sudah ada atau blm
                $dataSch = (array) $data_arr['dataSch'];

                $SASemesterID = $dataSch['SASemesterID'];
                $ClassGroup = $dataSch['ClassGroup'];

                $dataCheck = $this->db->select('ID')->get_where('db_academic.sa_schedule',array(
                    'SASemesterID' => $SASemesterID,
                    'ClassGroup' => $ClassGroup
                ))->result_array();

                $gr = false;
                if(count($dataCheck)>0){
                    if($dataCheck[0]['ID']==$ScheduleIDSA){
                        $gr = true;
                    }
                } else {
                    $gr = true;
                }

                // Jika TRUE
                if($gr==true){

                    // update schedule
                    $this->db->where('ID', $ScheduleIDSA);
                    $this->db->update('db_academic.sa_schedule',$dataSch);
                    $this->db->reset_query();

                    // Remove data schedule
                    $tables = array('db_academic.sa_schedule_course', 'db_academic.sa_schedule_team_teaching');
                    $this->db->where('ScheduleIDSA', $ScheduleIDSA);
                    $this->db->delete($tables);
                    $this->db->reset_query();

                    // Insert ulang
                    $ArrIDSSD = (array) $data_arr['ArrIDSSD'];
                    if(count($ArrIDSSD)>0){
                        for($i=0;$i<count($ArrIDSSD);$i++){
                            $dataSD = $this->db->select('ID,CDID,MKID')->get_where('db_academic.sa_student_details',
                                array('ID' => $ArrIDSSD[$i]))->result_array();

                            if(count($dataSD)>0){
                                $arrIns = array(
                                    'ScheduleIDSA' => $ScheduleIDSA,
                                    'IDSSD' => $dataSD[0]['ID'],
                                    'CDID' => $dataSD[0]['CDID'],
                                    'MKID' => $dataSD[0]['MKID']
                                );
                                $this->db->insert('db_academic.sa_schedule_course',$arrIns);
                            }

                        }
                    }

                    // Cek Team Teaching
                    $TeamTeaching = $data_arr['TeamTeaching'];
                    if(count($TeamTeaching)>0){
                        for($i=0;$i<count($TeamTeaching);$i++){
                            $arrTTM = array(
                                'ScheduleIDSA' => $ScheduleIDSA,
                                'NIP' => $TeamTeaching[$i]
                            );
                            $this->db->insert('db_academic.sa_schedule_team_teaching',$arrTTM);
                        }
                    }

                    $result = array(
                        'Status' => 1
                    );
                    return print_r(json_encode($result));

                }
                else {
                    $result = array(
                        'Status' => -1
                    );
                    return print_r(json_encode($result));
                }

            }
            else if ($type=='remove'){
                $ScheduleIDSA = $data_arr['ScheduleIDSA'];

                // Cek in exam
                $dataExamCourse = $this->db->select('ExamIDSA')->get_where('db_academic.sa_exam_course',array(
                    'ScheduleIDSA' => $ScheduleIDSA
                ))->result_array();
                if(count($dataExamCourse)>0){
                    for($i=0;$i<count($dataExamCourse);$i++){
                        $d = $dataExamCourse[$i];
                        // Cek ada berapa exam di exam course jika cuma satu maka exampun ikut di hapus
                        $dataEx = $this->db->get_where('db_academic.sa_exam_course',array(
                            'ExamIDSA' => $d['ExamIDSA']
                        ))->result_array();

                        if(count($dataEx)==1){
                            $this->db->where('ID', $d['ExamIDSA']);
                            $this->db->delete('db_academic.sa_exam');
                            $this->db->reset_query();

                            // Remove Student
                            $this->db->where('ExamIDSA', $d['ExamIDSA']);
                            $this->db->delete('db_academic.sa_exam_student');
                            $this->db->reset_query();
                        }
                    }
                }

                $tables = array('db_academic.sa_schedule_course', 'db_academic.sa_schedule_team_teaching','db_academic.sa_exam_course');
                $this->db->where('ScheduleIDSA', $ScheduleIDSA);
                $this->db->delete($tables);
                $this->db->reset_query();

                $this->db->where('ID', $ScheduleIDSA);
                $this->db->delete('db_academic.sa_schedule');


                return print_r(1);
            }

        }
        else if($data_arr['action']=='loadTimetableSA'){

            $SASemesterID = $data_arr['SASemesterID'];

            $queryDefault = 'SELECT s.*, d.NameEng AS DayEng, cl.Room, em.Name, mk.NameEng AS CourseEng FROM db_academic.sa_schedule s 
                                      LEFT JOIN db_academic.days d ON (d.ID = s.DayID)
                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = s.ClassroomID)
                                      LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                      LEFT JOIN db_academic.sa_schedule_course ssc ON (ssc.ScheduleIDSA = s.ID)
                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssc.MKID)
                                      
                                      WHERE s.SASemesterID = "'.$SASemesterID.'" 
                                      GROUP BY s.ID
                                      ORDER BY s.ClassGroup ASC ';


            $data = $this->db->query($queryDefault)->result_array();
            if(count($data)>0) {
                for ($i = 0; $i < count($data); $i++) {
                    $ScheduleIDSA = $data[$i]['ID'];
                    $Course = $this->db->query('SELECT ssc.*
                                              FROM db_academic.sa_schedule_course ssc
                                              WHERE ssc.ScheduleIDSA = "'.$ScheduleIDSA.'" ')->result_array();

                    $Student = [];
                    if(count($Course)>0){
                        foreach ($Course AS $item){
                            // Student
                            $dataStd = $this->db->query('SELECT ssd.NPM, ats.Name FROM db_academic.sa_student_details ssd
                                                              LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssd.NPM)
                                                              WHERE ssd.CDID = "'.$item['CDID'].'" 
                                                              ORDER BY ssd.NPM ASC')->result_array();

                            if(count($dataStd)>0){
                                foreach ($dataStd AS $itm){
                                    $arrp = array(
                                        'NPM' => $itm['NPM'],
                                        'ScheduleIDSA' => $ScheduleIDSA
                                    );
                                    array_push($Student,$arrp);
                                }
                            }
                        }
                    }

                    usort($Student, function ($a, $b){return strcmp($a['NPM'], $b['NPM']);});

                    $data[$i]['Students'] = $Student;

                }

            }

            return print_r(json_encode($data));

        }

        else if($data_arr['action']=='academicYear'){

            $Type = $data_arr['Type'];
            $SASemesterID = $data_arr['SASemesterID'];

            $dataS = $this->db->get_where('db_academic.sa_academic_years',array(
                'SASemesterID' => $SASemesterID
            ))->result_array();

            $result = array(
                'Start' => $dataS[0]['StartUAS'],
                'End' => $dataS[0]['EndUAS']
            );

            if($Type=='uts'){
                $result = array(
                    'Start' => $dataS[0]['StartUTS'],
                    'End' => $dataS[0]['EndUTS']
                );
            }

            return print_r(json_encode($result));

        }
        else if($data_arr['action']=='addSAExam'){

            $dataForm = (array) $data_arr['dataForm'];
            $SASemesterID = $dataForm['SASemesterID'];

            $this->db->insert('db_academic.sa_exam',$dataForm);
            $ExamIDSA = $this->db->insert_id();

            $dataCourse = (array) $data_arr['dataCourse'];

            if(count($dataCourse)>0){
                for($i=0;$i<count($dataCourse);$i++){
                    $arrIns = array(
                        'ExamIDSA' => $ExamIDSA,
                        'ScheduleIDSA' => $dataCourse[$i]
                    );
                    $this->db->insert('db_academic.sa_exam_course',$arrIns);
                }
            }


            $dataStudent = (array) $data_arr['dataStudent'];
            if(count($dataStudent)>0){
                for($s=0;$s<count($dataStudent);$s++){
                    $d = (array) $dataStudent[$s];

                    // Cek apakah sudah ada atau blm
                    $arr = array(
                        'SASemesterID' => $SASemesterID,
                        'ExamIDSA' => $ExamIDSA,
                        'ScheduleIDSA' => $d['ScheduleIDSA'],
                        'NPM' => $d['NPM']
                    );

                    $dataSt = $this->db->select('ID')->get_where('db_academic.sa_exam_student',$arr)->result_array();

                    if(count($dataSt)<=0){
                        $this->db->insert('db_academic.sa_exam_student',$arr);
                    }
                }
            }

            return print_r(1);

        }
        else if($data_arr['action']=='editSAExam'){

            $ExamIDSA = $data_arr['ExamIDSA'];
            $dataForm = (array) $data_arr['dataForm'];
            $SASemesterID = $dataForm['SASemesterID'];

            $this->db->set($dataForm);
            $this->db->where('ID', $ExamIDSA);
            $this->db->update('db_academic.sa_exam');
            $this->db->reset_query();


            // Delete table
            $tables = array('db_academic.sa_exam_course', 'db_academic.sa_exam_student');
            $this->db->where('ExamIDSA', $ExamIDSA);
            $this->db->delete($tables);
            $this->db->reset_query();

            $dataCourse = (array) $data_arr['dataCourse'];

            if(count($dataCourse)>0){
                for($i=0;$i<count($dataCourse);$i++){
                    $arrIns = array(
                        'ExamIDSA' => $ExamIDSA,
                        'ScheduleIDSA' => $dataCourse[$i]
                    );
                    $this->db->insert('db_academic.sa_exam_course',$arrIns);
                }
            }

            $dataStudent = (array) $data_arr['dataStudent'];
            if(count($dataStudent)>0){
                for($s=0;$s<count($dataStudent);$s++){
                    $d = (array) $dataStudent[$s];

                    // Cek apakah sudah ada atau blm
                    $arr = array(
                        'SASemesterID' => $SASemesterID,
                        'ExamIDSA' => $ExamIDSA,
                        'ScheduleIDSA' => $d['ScheduleIDSA'],
                        'NPM' => $d['NPM']
                    );

                    $dataSt = $this->db->select('ID')->get_where('db_academic.sa_exam_student',$arr)->result_array();

                    if(count($dataSt)<=0){
                        $this->db->insert('db_academic.sa_exam_student',$arr);
                    }
                }
            }

            return print_r(1);

        }

        else if($data_arr['action']=='addStudentSA'){
            $dataForm = (array) $data_arr['dataForm'];
            $this->db->insert('db_academic.sa_student',$dataForm);

            return print_r(1);
        }
        else if($data_arr['action']=='rmStudentSA'){

            $IDSAStudent = $data_arr['IDSAStudent'];

            $dataStd = $this->db->limit(1)->get_where('db_academic.sa_student',array('ID' => $IDSAStudent))->result_array();

            if(count($dataStd)>0){

                $d = $dataStd[0];

                // Remove tagihan
                $this->db->where(array(
                    'PTID' => 5,
                    'SemesterID' => $d['SASemesterID'],
                    'NPM' => $d['NPM']
                ));
                $this->db->delete('db_finance.payment');
                $this->db->reset_query();

                // Remove tagihan
                $this->db->where(array(
                    'PTID' => 6,
                    'SemesterID' => $d['SASemesterID'],
                    'NPM' => $d['NPM']
                ));
                $this->db->delete('db_finance.payment');
                $this->db->reset_query();


                $tables = array('db_academic.sa_student_details', 'db_academic.sa_study_planning');
                $this->db->where('IDSAStudent', $IDSAStudent);
                $this->db->delete($tables);
                $this->db->reset_query();

                $this->db->where('ID', $IDSAStudent);
                $this->db->delete('db_academic.sa_student');
                $this->db->reset_query();

            }



            return print_r(1);
        }

        else if($data_arr['action']=='RemoveExamSA'){
            $ExamIDSA = $data_arr['ExamIDSA'];

            $this->db->where('ID', $ExamIDSA);
            $this->db->delete('db_academic.sa_exam');
            $this->db->reset_query();

            $tables = array('db_academic.sa_exam_course', 'db_academic.sa_exam_student');
            $this->db->where('ExamIDSA', $ExamIDSA);
            $this->db->delete($tables);

            return print_r(1);
        }
        else if($data_arr['action']=='forseRemoveSSC'){

            $dataForm = (array) $data_arr['dataForm'];

            $this->db->where($dataForm);
            $this->db->delete('db_academic.sa_schedule_course');

            return print_r(1);
        }

        else if($data_arr['action']=='SATimetables'){


            $NPM = $data_arr['NPM'];

            $dataStd = $this->db->query('SELECT ss.NPM, ss.Mentor, sa.Name, ss.ID AS IDSAStudent, sa.*  FROM db_academic.sa_student ss 
                                                    LEFT JOIN db_academic.semester_antara sa ON (sa.ID = ss.SASemesterID)
                                                    WHERE ss.NPM = "'.$NPM.'" ORDER BY ss.SASemesterID ASC ')->result_array();

            if(count($dataStd)>0){
                for ($i=0;$i<count($dataStd);$i++){
                    $d = $dataStd[$i];

                    $dataDetails = $this->db->query('SELECT ssd.ID AS IDSSD, ssd.Type, ssd.Status, ssd.Credit, mk.NameEng AS CourseEng, mk.MKCode  
                                                                FROM db_academic.sa_student_details ssd
                                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssd.MKID)
                                                                WHERE ssd.IDSAStudent = "'.$d['IDSAStudent'].'" AND ssd.Status = "3" ')->result_array();

                    if(count($dataDetails)>0){
                        for ($s=0;$s<count($dataDetails);$s++){
                            $ds = $dataDetails[$s];
                            $dataSchedule = $this->db->query('SELECT ss.ID AS ScheduleIDSA,ss.ClassGroup, ss.Start, ss.End, em.NIP, em.Name AS CoordinatorName, d.NameEng AS DayEng, cl.Room  FROM db_academic.sa_schedule ss
                                                                        LEFT JOIN db_academic.sa_schedule_course ssc ON (ss.ID = ssc.ScheduleIDSA)
                                                                        LEFT JOIN db_academic.days d ON (d.ID = ss.DayID)
                                                                        LEFT JOIN db_academic.classroom cl ON (cl.ID = ss.ClassroomID)
                                                                        LEFT JOIN db_employees.employees em ON (em.NIP = ss.Coordinator)
                                                                        WHERE ssc.IDSSD = "'.$ds['IDSSD'].'" ')
                                ->result_array();

                            if(count($dataSchedule)>0){
                                for($t=0;$t<count($dataSchedule);$t++){
                                    $dataSchedule[$t]['TeamTeaching'] = $this->db->query('SELECT em.NIP, em.Name FROM db_academic.sa_schedule_team_teaching sstt
                                                                                                LEFT JOIN db_employees.employees em ON (em.NIP = sstt.NIP)
                                                                                                WHERE sstt.ScheduleIDSA = "'.$dataSchedule[$t]['ScheduleIDSA'].'" ')->result_array();
                                }
                            }

                            $dataDetails[$s]['Schedule'] = $dataSchedule;
                        }
                    }
                    $dataStd[$i]['Course'] = $dataDetails;

                }
            }


            return print_r(json_encode($dataStd));


        }

        else if($data_arr['action']=='dataExamSA'){

            $NPM = $data_arr['NPM'];

            $dateNow = $this->m_rest->getDateNow();
            $dataStd = $this->db->query('SELECT ss.NPM, ss.Mentor, sa.Name, ss.ID AS IDSAStudent, sa.*, say.ShowUTSSchedule, say.ShowUASSchedule  FROM db_academic.sa_student ss 
                                                    LEFT JOIN db_academic.semester_antara sa ON (sa.ID = ss.SASemesterID)
                                                    LEFT JOIN db_academic.sa_academic_years say ON (say.SASemesterID = sa.ID)
                                                    WHERE ss.NPM = "'.$NPM.'" ORDER BY ss.SASemesterID ASC ')->result_array();

            if(count($dataStd)>0){
                for ($i=0;$i<count($dataStd);$i++){
                    $d = $dataStd[$i];

                    $dataDetails = $this->db->query('SELECT ssd.ID AS IDSSD, ssd.Type, ssd.Status, ssd.Credit, mk.NameEng AS CourseEng, mk.MKCode  
                                                                FROM db_academic.sa_student_details ssd
                                                                LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssd.MKID)
                                                                WHERE ssd.IDSAStudent = "'.$d['IDSAStudent'].'" AND ssd.Status = "3" ')->result_array();

                    if(count($dataDetails)>0){
                        for ($s=0;$s<count($dataDetails);$s++){
                            $ds = $dataDetails[$s];
                            $dataSchedule = $this->db->query('SELECT ss.ID AS ScheduleIDSA,ss.ClassGroup, ss.Start, ss.End, em.NIP, em.Name AS CoordinatorName, d.NameEng AS DayEng, cl.Room  
                                                                        FROM db_academic.sa_schedule ss
                                                                        LEFT JOIN db_academic.sa_schedule_course ssc ON (ss.ID = ssc.ScheduleIDSA)
                                                                        LEFT JOIN db_academic.days d ON (d.ID = ss.DayID)
                                                                        LEFT JOIN db_academic.classroom cl ON (cl.ID = ss.ClassroomID)
                                                                        LEFT JOIN db_employees.employees em ON (em.NIP = ss.Coordinator)
                                                                        WHERE ssc.IDSSD = "'.$ds['IDSSD'].'" ')->result_array();

                            if(count($dataSchedule)>0){
                                for($t=0;$t<count($dataSchedule);$t++){

                                    // Jadwal UTS
                                    $uts = [];
                                    $uas = [];
                                    if($d['ShowUTSSchedule']<=$dateNow){
                                        $uts = $this->db->query('SELECT se.ExamDate, se.Start, se.End, cl.Room FROM db_academic.sa_exam se 
                                                                    LEFT JOIN db_academic.sa_exam_student ses ON (ses.ExamIDSA = se.ID)
                                                                    LEFT JOIN  db_academic.classroom cl ON (cl.ID = se.ClassroomID)
                                                                    WHERE ses.NPM = "'.$NPM.'" AND ses.ScheduleIDSA = "'.$dataSchedule[$t]['ScheduleIDSA'].'" 
                                                                    AND se.Type = "uts" ')->result_array();
                                    }
                                    $dataSchedule[$t]['UTS'] = $uts;

                                    // Jadwal UTS Susulan
                                    $dataSchedule[$t]['UTS_RE'] = $this->db->query('SELECT se.ExamDate, se.Start, se.End, cl.Room FROM db_academic.sa_exam se 
                                                                    LEFT JOIN db_academic.sa_exam_student ses ON (ses.ExamIDSA = se.ID)
                                                                    LEFT JOIN  db_academic.classroom cl ON (cl.ID = se.ClassroomID)
                                                                    WHERE ses.NPM = "'.$NPM.'" AND ses.ScheduleIDSA = "'.$dataSchedule[$t]['ScheduleIDSA'].'" 
                                                                    AND se.Type = "re_uts" ')->result_array();


                                    if($d['ShowUASSchedule']<=$dateNow){
                                        $uas = $this->db->query('SELECT se.ExamDate, se.Start, se.End, cl.Room FROM db_academic.sa_exam se 
                                                                    LEFT JOIN db_academic.sa_exam_student ses ON (ses.ExamIDSA = se.ID)
                                                                    LEFT JOIN  db_academic.classroom cl ON (cl.ID = se.ClassroomID)
                                                                    WHERE ses.NPM = "'.$NPM.'" AND ses.ScheduleIDSA = "'.$dataSchedule[$t]['ScheduleIDSA'].'" 
                                                                    AND se.Type = "uas" ')->result_array();
                                    }
                                    // Jadwal UAS
                                    $dataSchedule[$t]['UAS'] = $uas;

                                    // Jadwal UAS Susulan
                                    $dataSchedule[$t]['UAS_RE'] = $this->db->query('SELECT se.ExamDate, se.Start, se.End, cl.Room FROM db_academic.sa_exam se 
                                                                    LEFT JOIN db_academic.sa_exam_student ses ON (ses.ExamIDSA = se.ID)
                                                                    LEFT JOIN  db_academic.classroom cl ON (cl.ID = se.ClassroomID)
                                                                    WHERE ses.NPM = "'.$NPM.'" AND ses.ScheduleIDSA = "'.$dataSchedule[$t]['ScheduleIDSA'].'" 
                                                                    AND se.Type = "re_uas" ')->result_array();


                                }
                            }

                            $dataDetails[$s]['Schedule'] = $dataSchedule;
                        }
                    }
                    $dataStd[$i]['Course'] = $dataDetails;

                }
            }


            return print_r(json_encode($dataStd));

        }

        else if($data_arr['action']=='SATimetablesLecturer'){

            $NIP = $data_arr['NIP'];
            $SASemesterID = $data_arr['SASemesterID'];

            $q1 = 'SELECT ss.ID AS ScheduleIDSA, ss.ClassGroup, ss.Coordinator, ss.Start, ss.End,   
                            mk.MKCode, mk.NameEng AS CourseEng, em.Name AS CoordinatorName, d.NameEng AS DayEng, cl.Room
                            FROM db_academic.sa_schedule ss
                            LEFT JOIN db_academic.sa_schedule_course ssc ON (ssc.ScheduleIDSA = ss.ID)
                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssc.MKID) 
                            LEFT JOIN db_employees.employees em ON (em.NIP = ss.Coordinator)
                            LEFT JOIN db_academic.days d ON (d.ID = ss.DayID)
                            LEFT JOIN db_academic.classroom cl ON (cl.ID = ss.ClassroomID)
                            WHERE ss.SASemesterID = "'.$SASemesterID.'"
                            AND ss.Coordinator = "'.$NIP.'" GROUP BY ss.ID ';

            $q2 = 'SELECT ss.ID AS ScheduleIDSA, ss.ClassGroup, ss.Coordinator, ss.Start, ss.End,   
                              mk.MKCode, mk.NameEng AS CourseEng, em.Name AS CoordinatorName, d.NameEng AS DayEng, cl.Room
                              FROM db_academic.sa_schedule ss
                              LEFT JOIN db_academic.sa_schedule_team_teaching sstt ON (sstt.ScheduleIDSA = ss.ID)
                              LEFT JOIN db_academic.sa_schedule_course ssc ON (ssc.ScheduleIDSA = ss.ID)
                              LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssc.MKID)
                              LEFT JOIN db_employees.employees em ON (em.NIP = ss.Coordinator)
                              LEFT JOIN db_academic.days d ON (d.ID = ss.DayID)
                              LEFT JOIN db_academic.classroom cl ON (cl.ID = ss.ClassroomID)
                              WHERE ss.SASemesterID = "'.$SASemesterID.'" 
                              AND sstt.NIP = "'.$NIP.'"  GROUP BY ss.ID ';

            $dataSchedule = $this->db->query($q1.' UNION '.$q2)->result_array();

            if(count($dataSchedule)>0){
                for ($i=0;$i<count($dataSchedule);$i++){
                    $d = $dataSchedule[$i];

                    $ScheduleIDSA = $d['ScheduleIDSA'];

                    // Cek Teamteaching
                    $dataSchedule[$i]['TeamTeaching'] = $this->db->query('SELECT em.NIP, em.Name FROM db_academic.sa_schedule_team_teaching sstt 
                                                              LEFT JOIN db_employees.employees em ON (em.NIP = sstt.NIP)
                                                              WHERE sstt.ScheduleIDSA = "'.$ScheduleIDSA.'" ')->result_array();

                    // Cek Student
                    $Student = [];
                     $dataCD = $this->db->query('SELECT * FROM db_academic.sa_schedule_course ssc 
                                                                                WHERE ssc.ScheduleIDSA = "'.$ScheduleIDSA.'" ')->result_array();

                     if(count($dataCD)>0){
                         foreach ($dataCD AS $item){
                             // Student
                             $dataStd = $this->db->query('SELECT ssd.NPM, ats.Name, p1.Status AS StatusBPP, p2.Status AS StatusCredit FROM db_academic.sa_student_details ssd
                                                              LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssd.NPM)
                                                              LEFT JOIN db_finance.payment p1 ON (p1.NPM = ssd.NPM AND p1.PTID = "5")
                                                              LEFT JOIN db_finance.payment p2 ON (p2.NPM = ssd.NPM AND p2.PTID = "6")
                                                              WHERE ssd.CDID = "'.$item['CDID'].'" AND ssd.Status = "3" AND p1.Status = "1" AND p2.Status = "1"
                                                              ORDER BY ssd.NPM ASC')->result_array();

                             if(count($dataStd)>0){
                                 foreach ($dataStd AS $itm){
                                     array_push($Student,$itm);
                                 }
                             }
                         }
                     }

                    usort($Student, function ($a, $b){return strcmp($a['NPM'], $b['NPM']);});

                    $dataSchedule[$i]['Students'] = $Student;


                }
            }

            return print_r(json_encode($dataSchedule));

        }
        else if($data_arr['action']=='inputAttendanceSA'){
            $dataAttd = (array) $data_arr['dataAttd'];

            for($i=0;$i<count($dataAttd);$i++){

                $d = (array) $dataAttd[$i];

                // Cek apakah sudah ada atau blm
                $dataCk = $this->db->select('ID')->limit(1)
                    ->get_where('db_academic.sa_attendance'
                    ,array(
                        'ScheduleIDSA' => $d['ScheduleIDSA'],
                        'UserID' => $d['UserID'],
                        'Meet' => $d['Meet']
                    ))->result_array();

                if(count($dataCk)>0){
                    $IDAttd = $dataCk[0]['ID'];
                    $this->db->where('ID', $IDAttd);
                    $this->db->update('db_academic.sa_attendance',$d);
                } else {
                    $d['EntredAt'] = $dataTimeNow;
                    $d['EntredBy'] = $d['UpdatedBy'];
                    $this->db->insert('db_academic.sa_attendance', $d);
                }

            }

            return print_r(1);
        }
        else if($data_arr['action']=='loadAttdStd'){

            $ScheduleIDSA = $data_arr['ScheduleIDSA'];
            $Meet = $data_arr['Meet'];
            $Type = $data_arr['Type'];

            $dataAttd = $this->db->get_where('db_academic.sa_attendance',array(
                'ScheduleIDSA' => $ScheduleIDSA,
                'Meet' => $Meet,
                'Type' => $Type
            ))->result_array();


            $result = [];
            if(count($dataAttd)>0){
                foreach ($dataAttd AS $item){
                    if($item['Status']=='1' || $item['Status']==1){
                        array_push($result,$item['UserID']);
                    }

                }
            }

            return print_r(json_encode($result));

        }
        else if($data_arr['action']=='checkLectAttd'){

            $dataCk = $this->db->select('Meet')->order_by('Meet','DESC')->get_where('db_academic.sa_attendance',array(
                'ScheduleIDSA' => $data_arr['ScheduleIDSA'],
                'Type' => $data_arr['Type']
            ))->result_array();

            $result = [];

            if(count($dataCk)>0){
                foreach ($dataCk AS $item){
                    array_push($result,$item['Meet']);
                }
            }

            return print_r(json_encode($result));

        }
        else if ($data_arr['action']=='inputAttendanceLecturerSA'){

            $inputAttd = (array) $data_arr['inputAttd'];

            // Cek apakah sudah input
            $dataCk = $this->db->select('ID')->limit(1)->get_where('db_academic.sa_attendance',array(
                'ScheduleIDSA' => $inputAttd['ScheduleIDSA'],
                'Meet' => $inputAttd['Meet'],
                'UserID' => $inputAttd['UserID']
            ))->result_array();

            if(count($dataCk)>0){
                $this->db->where('ID', $dataCk[0]['ID']);
                $this->db->update('db_academic.sa_attendance', $inputAttd);
            } else {
                $inputAttd['EntredAt'] = $dataTimeNow;
                $inputAttd['EntredBy'] = $inputAttd['UpdatedBy'];
                $this->db->insert('db_academic.sa_attendance', $inputAttd);
            }

            return print_r(1);

        }

        else if($data_arr['action']=='loadDocumentSA'){

            $ScheduleIDSA = $data_arr['ScheduleIDSA'];

            $dataDock = $this->db->get_where('db_academic.sa_schedule',array(
                'ID' => $ScheduleIDSA
            ))->result_array();

            return print_r(json_encode($dataDock));

        }

        else if($data_arr['action']=='loadDocumentSA_Score'){



            $ScheduleIDSA = $data_arr['ScheduleIDSA'];

            $dataDock = $this->db->get_where('db_academic.sa_schedule',array(
                'ID' => $ScheduleIDSA
            ))->result_array();


            if(count($dataDock)>0){
                $Student = [];
                $dataCD = $this->db->query('SELECT * FROM db_academic.sa_schedule_course ssc 
                                                     WHERE ssc.ScheduleIDSA = "'.$ScheduleIDSA.'" ')->result_array();
                if(count($dataCD)>0){
                    foreach ($dataCD AS $item){
                        $dataStd = $this->db->query('SELECT ssd.ID AS IDSSD, ssd.NPM, ats.Name, ssd.Evaluasi, ssd.UTS, ssd.UAS, ssd.ScoreNew, ssd.GradeNew, ssd.GradeValueNew
                                                               FROM db_academic.sa_student_details ssd
                                                              LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssd.NPM)
                                                              LEFT JOIN db_finance.payment p1 ON (p1.NPM = ssd.NPM AND p1.PTID = "5")
                                                              LEFT JOIN db_finance.payment p2 ON (p2.NPM = ssd.NPM AND p2.PTID = "6")
                                                              WHERE ssd.CDID = "'.$item['CDID'].'" AND ssd.Status = "3" AND p1.Status = "1" AND p2.Status = "1"
                                                              ORDER BY ssd.NPM ASC')->result_array();

                        if(count($dataStd)>0){
                            foreach ($dataStd AS $itm){
                                array_push($Student,$itm);
                            }
                        }
                    }

                }

                $dataDock[0]['Students'] = $Student;

                // Get Setting
                $SASemesterID = $data_arr['SASemesterID'];
                $dataAY = $this->db->get_where('db_academic.sa_academic_years',array(
                    'ID' => $SASemesterID
                ))->result_array();

                $dateNow = $this->m_rest->getDateNow();

                // Cek date UTS
                $dataDock[0]['A_Evaluasi'] = ($dataAY[0]['MaxInputTugas'] >= $dateNow) ? 1 : 0;
                $dataDock[0]['A_UTS'] = ($dataAY[0]['StartInputUTS'] <= $dateNow && $dataAY[0]['EndInputUTS'] >= $dateNow) ? 1 : 0;
                $dataDock[0]['A_UAS'] = ($dataAY[0]['StartInputUAS'] <= $dateNow && $dataAY[0]['EndInputUAS'] >= $dateNow) ? 1 : 0;



            }

            return print_r(json_encode($dataDock));

        }

        else if($data_arr['action']=='updateWeightages'){

            $ScheduleIDSA = $data_arr['ScheduleIDSA'];
            $dataUpdt = (array) $data_arr['dataUpdt'];

            $this->db->where('ID', $ScheduleIDSA);
            $this->db->update('db_academic.sa_schedule',$dataUpdt);

            return print_r(1);
        }
        else if($data_arr['action']=='updateScoreSA'){

            $dataStd = (array) $data_arr['dataStd'];

            for($i=0;$i<count($dataStd);$i++){
                $d = (array) $dataStd[$i];
                $IDSSD = $d['IDSSD'];
                $Update = (array) $d['Update'];

                $this->db->where('ID', $IDSSD);
                $this->db->update('db_academic.sa_student_details',$Update);
            }

            return print_r(1);


        }

    }

    function createTagihanSemesterAntara($IDSAStudent){

        $dataStd = $this->db->get_where('db_academic.sa_student',array('ID' => $IDSAStudent))->result_array();

        $SASemesterID = $dataStd[0]['SASemesterID'];

        $dataConf = $this->db->get_where('db_academic.sa_academic_years',array('SASemesterID' => $SASemesterID))->result_array();

        $NPM = $dataStd[0]['NPM'];
        $DiscountBPP = $dataConf[0]['DiscountBPP'];

        // Cek apakah BPP Sudah di set atau belim
        $arrCheckBPP = array(
            'PTID' => 5,
            'SemesterID' => $SASemesterID,
            'NPM' => $NPM
        );
        $dataCheckBPP = $this->db->get_where('db_finance.payment',$arrCheckBPP)->result_array();

        if(count($dataCheckBPP)<=0){

            // Get BPP
            $dataTagihanBPP = $this->db->query('SELECT * FROM db_finance.m_tuition_fee WHERE PTID = 2 AND NPM = "'.$NPM.'" LIMIT 1')->result_array();
            $InvoiceBPP = $DiscountBPP/100 * $dataTagihanBPP[0]['Invoice'];
            $dataInsrtBPP = array(
                'PTID' => 5,
                'SemesterID' => $SASemesterID,
                'NPM' => $NPM,
                'Invoice' => $InvoiceBPP,
                'Discount' => $DiscountBPP
            );
            $this->db->insert('db_finance.payment',$dataInsrtBPP);

        }



        // ===============

        // Cek apakah Credit Sudah di set atau belim
        $arrCheckCredit = array(
            'PTID' => 6,
            'SemesterID' => $SASemesterID,
            'NPM' => $NPM
        );
        $dataCheckCredit = $this->db->get_where('db_finance.payment',$arrCheckCredit)->result_array();

        // Invoice Credit
        $dataTagihanCredit = $this->db->query('SELECT * FROM db_finance.m_tuition_fee WHERE PTID = 3 AND NPM = "'.$NPM.'" LIMIT 1')->result_array();

        // Baca total credit
        $dataC = $this->db->query('SELECT SUM(Credit) AS TotalCredit FROM db_academic.sa_student_details 
                                      WHERE IDSAStudent = "'.$IDSAStudent.'" AND Status = "3" ')->result_array();
        $dataInsrtCredit = array(
            'PTID' => 6,
            'SemesterID' => $SASemesterID,
            'NPM' => $NPM,
            'Invoice' => $dataC[0]['TotalCredit'] * $dataTagihanCredit[0]['Invoice']
        );

        if(count($dataCheckCredit)>0){

            if($dataC[0]['TotalCredit']!=null && $dataC[0]['TotalCredit']>0){
                $IDP = $dataCheckCredit[0]['ID'];
                $this->db->set($dataInsrtCredit);
                $this->db->where('ID', $IDP);
                $this->db->update('db_finance.payment');
            }


        } else {
            if($dataC[0]['TotalCredit']!=null && $dataC[0]['TotalCredit']>0){
                $this->db->insert('db_finance.payment',$dataInsrtCredit);
            }
        }


    }


    function getTimetableSA(){
        $requestData= $_REQUEST;

        $data_arr = $this->getInputToken();

        $SASemesterID = $data_arr['SASemesterID'];

        $queryDefault = 'SELECT s.*, d.NameEng AS DayEng, cl.Room, em.Name, mk.NameEng AS CourseEng FROM db_academic.sa_schedule s 
                                      LEFT JOIN db_academic.days d ON (d.ID = s.DayID)
                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = s.ClassroomID)
                                      LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                      LEFT JOIN db_academic.sa_schedule_course ssc ON (ssc.ScheduleIDSA = s.ID)
                                      LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssc.MKID)
                                      
                                      WHERE s.SASemesterID = "'.$SASemesterID.'" 
                                      GROUP BY s.ID
                                      ORDER BY s.ClassGroup ASC ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++) {
            $nestedData = array();

            $row = $query[$i];

            $ScheduleIDSA = $row['ID'];

            // Get Team Teaching
            $TeamTeaching = $this->db->query('SELECT em.Name, em.NIP FROM db_academic.sa_schedule_team_teaching sstt
                                      LEFT JOIN db_employees.employees em ON (em.NIP = sstt.NIP)
                                      WHERE sstt.ScheduleIDSA = "'.$ScheduleIDSA.'" ')->result_array();

            $Lec = '<b>(Co) '.$row['Name'].'</b>';
            if(count($TeamTeaching)>0){
                foreach ($TeamTeaching AS $item){
                    $Lec = $Lec.'<div>- '.$item['Name'].'</div>';
                }
            }

            // Load Course
            $Course = $this->db->query('SELECT ssc.*
                                              FROM db_academic.sa_schedule_course ssc
                                              WHERE ssc.ScheduleIDSA = "'.$ScheduleIDSA.'" ')->result_array();

            $Student = [];
            if(count($Course)>0){
                foreach ($Course AS $item){
                    // Student
                    $dataStd = $this->db->query('SELECT ssd.NPM, ats.Name, p1.Status AS StatusBPP, p2.Status AS StatusCredit FROM db_academic.sa_student_details ssd
                                                              LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssd.NPM)
                                                              LEFT JOIN db_finance.payment p1 ON (p1.NPM = ssd.NPM AND p1.PTID = "5")
                                                              LEFT JOIN db_finance.payment p2 ON (p2.NPM = ssd.NPM AND p2.PTID = "6")
                                                              WHERE ssd.CDID = "'.$item['CDID'].'" AND ssd.Status = "3"
                                                              ORDER BY ssd.NPM ASC')->result_array();

                    if(count($dataStd)>0){
                        foreach ($dataStd AS $itm){
                            array_push($Student,$itm);
                        }
                    }
                }
            }
            usort($Student, function ($a, $b){return strcmp($a['NPM'], $b['NPM']);});

            $tokenStd = $this->jwt->encode($Student,'UAP)(*');

            $timeSc = substr($row['Start'],0,5).' - '.substr($row['End'],0,5);

            // Get jadwal UTS
            $dataUTS = $this->db->query('SELECT sec.ExamIDSA, se.ExamDate, se.Start, se.End, cl.Room FROM db_academic.sa_exam_course sec 
                                                      LEFT JOIN db_academic.sa_exam se ON (se.ID = sec.ExamIDSA)
                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = se.ClassroomID)
                                                      WHERE sec.ScheduleIDSA = "'.$ScheduleIDSA.'" AND se.Type = "uts" ')->result_array();
            $showUTS = '';
            if(count($dataUTS)>0){
                foreach ($dataUTS AS $item){
                    $t = substr($item['Start'],0,5).' - '.substr($item['End'],0,5);
                    $showUTS = $showUTS.'<div><a href="'.base_url('academic/semester-antara/setting-exam/'.$SASemesterID.'?edit='.$item['ExamIDSA']).'">'.date('l, d M Y',strtotime($item['ExamDate'])).'<br/>'.$t.'<br/>'.$item['Room'].'</a></div>';
                }
            }

            // Get jadwal UTS Susulan
            $dataUTS_RE = $this->db->query('SELECT sec.ExamIDSA, se.ExamDate, se.Start, se.End, cl.Room FROM db_academic.sa_exam_course sec 
                                                      LEFT JOIN db_academic.sa_exam se ON (se.ID = sec.ExamIDSA)
                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = se.ClassroomID)
                                                      WHERE sec.ScheduleIDSA = "'.$ScheduleIDSA.'" AND se.Type = "re_uts" ')->result_array();
            $showUTS_RE = '';
            if(count($dataUTS_RE)>0){
                foreach ($dataUTS_RE AS $item){
                    $t = substr($item['Start'],0,5).' - '.substr($item['End'],0,5);
                    $showUTS_RE = $showUTS_RE.'<div><a style="color: orangered;" href="'.base_url('academic/semester-antara/setting-exam/'.$SASemesterID.'?edit='.$item['ExamIDSA']).'">'.date('l, d M Y',strtotime($item['ExamDate'])).'<br/>'.$t.'<br/>'.$item['Room'].'</a></div>';
                }
            }

            // Get jadwal UAS
            $dataUAS = $this->db->query('SELECT sec.ExamIDSA, se.ExamDate, se.Start, se.End, cl.Room FROM db_academic.sa_exam_course sec 
                                                      LEFT JOIN db_academic.sa_exam se ON (se.ID = sec.ExamIDSA)
                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = se.ClassroomID)
                                                      WHERE sec.ScheduleIDSA = "'.$ScheduleIDSA.'" AND se.Type = "uas" ')->result_array();
            $showUAS = '';
            if(count($dataUAS)>0){
                foreach ($dataUAS AS $item){
                    $t = substr($item['Start'],0,5).' - '.substr($item['End'],0,5);
                    $showUAS = $showUAS.'<div><a href="'.base_url('academic/semester-antara/setting-exam/'.$SASemesterID.'?edit='.$item['ExamIDSA']).'">'.date('l, d M Y',strtotime($item['ExamDate'])).'<br/>'.$t.'<br/>'.$item['Room'].'</a></div>';
                }
            }

            // Get jadwal UAS Susulan
            $dataUAS_RE = $this->db->query('SELECT sec.ExamIDSA, se.ExamDate, se.Start, se.End, cl.Room FROM db_academic.sa_exam_course sec 
                                                      LEFT JOIN db_academic.sa_exam se ON (se.ID = sec.ExamIDSA)
                                                      LEFT JOIN db_academic.classroom cl ON (cl.ID = se.ClassroomID)
                                                      WHERE sec.ScheduleIDSA = "'.$ScheduleIDSA.'" AND se.Type = "re_uas" ')->result_array();
            $showUAS_RE = '';
            if(count($dataUAS_RE)>0){
                foreach ($dataUAS_RE AS $item){
                    $t = substr($item['Start'],0,5).' - '.substr($item['End'],0,5);
                    $showUAS_RE = $showUAS_RE.'<div><a style="color: orangered;" href="'.base_url('academic/semester-antara/setting-exam/'.$SASemesterID.'?edit='.$item['ExamIDSA']).'">'.date('l, d M Y',strtotime($item['ExamDate'])).'<br/>'.$t.'<br/>'.$item['Room'].'</a></div>';
                }
            }

            $nestedData[] = '<div style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$row['ClassGroup'].'</div>';
            $nestedData[] = '<div style="text-align:left;"><b><a href="'.base_url('academic/semester-antara/setting-timetable/'.$SASemesterID.'?edit='.$row['ID']).'">'.$row['CourseEng'].'</a></b></div>';
            $nestedData[] = '<div style="text-align:right;">'.$row['DayEng'].', '.$timeSc.'<br/>'.$row['Room'].'</div>';
            $nestedData[] = '<div style="text-align:left;">'.$Lec.'</div>';
            $nestedData[] = '<div style="text-align:center;">
                                <a href="javascript:void(0);" class="showStd" data-course="'.$row['ClassGroup'].' - '.$row['CourseEng'].'" 
                                data-token="'.$tokenStd.'">'.count($Student).'</a></div>';

            $nestedData[] = '<div style="text-align:center;">
                                    <div class="btn-group">
                                      <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        <i class="fa fa-edit"></i> <span class="caret"></span>
                                      </button>
                                      <ul class="dropdown-menu">
                                        <li><a href="#">Attendance</a></li>
                                        <li role="separator" class="divider"></li>
                                        <li><a href="#">Syllabus & RPS</a></li>
                                        <li><a href="#">Score</a></li>
                                      </ul>
                                    </div>
                                </div>';
            $nestedData[] = '<div style="text-align:right;">'.$showUTS.''.$showUTS_RE.'</div>';
            $nestedData[] = '<div style="text-align:right;">'.$showUAS.''.$showUAS_RE.'</div>';



            $data[] = $nestedData;
            $no++;

        }


        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval( count($queryDefaultRow) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    function getStudentSA(){
        $requestData= $_REQUEST;

        $data_arr = $this->getInputToken();

        $SASemesterID = $data_arr['SASemesterID'];

        $queryDefault = 'SELECT ss.ID AS IDSAStudent, ats.NPM, ats.Name, ats.Year AS ClassOf, ats.ProdiID, ss.Mentor, em.Name AS MentorName FROM db_academic.sa_student ss 
                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ss.NPM)
                                    LEFT JOIN db_employees.employees em ON (em.NIP = ss.Mentor)
                                    WHERE ss.SASemesterID = "'.$SASemesterID.'" ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();

        for($i=0;$i<count($query);$i++) {
            $nestedData = array();

            $row = $query[$i];

            // Get Course
            $IDSAStudent = $row['IDSAStudent'];

            $dataCourse = $this->db->query('SELECT ssd.*, mk.NameEng AS CourseEng, mk.MKCode FROM db_academic.sa_student_details ssd
                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssd.MKID)
                                                        WHERE ssd.IDSAStudent = "'.$IDSAStudent.'" ')->result_array();

            $viewCourse = '';
            if(count($dataCourse)>0){
                foreach ($dataCourse AS $item){
                    $n = ($item['Grade']!='' && $item['Grade']!=null)
                        ? ' <span class="label label-info">'.$item['Grade'].' | '.$item['Score'].'</span>' : '';

                    // 0 = plan, 1 = Need approve pa, 2 = Approve PA -2 = Rejected PA, 3 = Approved Kaprodi, -3 = Rejected Kaprodi
                    $Status = '<span class="sp-sts" style="color: #9e9e9e;">Plan</span>';
                    if($item['Status']==1 || $item['Status']=='1'){
                        $Status = '<span class="sp-sts" style="color: royalblue;">Need approve mentor</span>';
                    } else if($item['Status']==2 || $item['Status']=='2'){
                        $Status = '<span class="sp-sts" style="color: royalblue;">Need approve Kaprodi</span>';
                    } else if($item['Status']==-2 || $item['Status']=='-2'){
                        $Status = '<span class="sp-sts" style="color: #f44336;">Rejected by mentor</span>';
                    } else if($item['Status']==-3 || $item['Status']=='-3'){
                        $Status = '<span class="sp-sts" style="color: #f44336;">Rejected by Kaprodi</span>';
                    } else if($item['Status']==3 || $item['Status']=='3'){
                        $Status = '<span class="sp-sts" style="color: green;">Approved by Kaprodi</span>';
                    }

                    $viewCourse = $viewCourse.'<div style="margin-top: 5px;">- '.$item['MKCode'].' | '.$item['CourseEng'].' <span class="label label-default">Credit : '.$item['Credit'].'</span>'.$n.' | '.$Status.'</div>';
                }
            }

            $nestedData[] = '<div style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div style="text-align:left;"><a href="javascript:void(0);" class="showSAStudent" data-idstd="'.$row['IDSAStudent'].'" data-mentor="'.$row['Mentor'].'" data-prodi="'.$row['ProdiID'].'" data-npm="'.$row['NPM'].'" data-classof="'.$row['ClassOf'].'">'.$row['Name'].'</a><br/>'.$row['NPM'].'<br/><span style="font-size: 12px;"><i class="fa fa-user-o"></i> '.$row['MentorName'].'</span></div>';
            $nestedData[] = '<div style="text-align:left;">'.$viewCourse.'</div>';
            $nestedData[] = '<div style="text-align:center;"><button class="btn btn-default btn-default-danger btn-sm btn-removestd" data-npm="'.$row['NPM'].'" data-id="'.$IDSAStudent.'"><i class="fa fa-trash"></i></button></div>';

            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsFiltered" => intval( count($queryDefaultRow) ),
            "data"            => $data
        );
        echo json_encode($json_data);

    }

    public function getStudentList(){
        $Key = $this->input->get('Key');
        $SASemesterID = $this->input->get('SASemesterID');

        $dataStd = $this->db->query('SELECT ats.NPM, ats.Name, ma.NIP, ss.ID AS IDSAStudent FROM db_academic.auth_students ats 
                                                            LEFT JOIN db_academic.mentor_academic ma ON (ma.NPM = ats.NPM)
                                                            LEFT JOIN db_academic.sa_student ss ON (ss.NPM = ats.NPM AND ss.SASemesterID = "'.$SASemesterID.'")
                                                            WHERE ats.NPM LIKE "%'.$Key.'%" OR ats.Name LIKE "%'.$Key.'%" LIMIT 7 ')->result_array();

        return print_r(json_encode($dataStd));


    }

    public function checkConflict_Venue(){
        // Start & End : datetime
        $data_arr = $this->getInputToken();
        $Start = $data_arr['Start'];
        $End = $data_arr['End'];
        $roomname = $data_arr['RoomName'];
        $dataCheck = $this->m_reservation->checkBentrok2($Start,$End,'',$roomname);

        return print_r(json_encode($dataCheck));
    }


}