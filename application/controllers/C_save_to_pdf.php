<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_save_to_pdf extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->library('JWT');
        $this->load->library('pdf');
        $this->load->library('pdf_mc_table');

        $this->load->model('m_rest');
        $this->load->model('master/m_master');
        $this->load->model('report/m_save_to_pdf');


        date_default_timezone_set("Asia/Jakarta");
        setlocale(LC_ALL, 'id_ID.UTF8', 'id_ID.UTF-8', 'id_ID.8859-1', 'id_ID', 'IND.UTF8', 'IND.UTF-8', 'IND.8859-1', 'IND', 'Indonesian.UTF8', 'Indonesian.UTF-8', 'Indonesian.8859-1', 'Indonesian', 'Indonesia', 'id', 'ID', 'en_US.UTF8', 'en_US.UTF-8', 'en_US.8859-1', 'en_US', 'American', 'ENG', 'English');
    }

    private function getInputToken($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    public function getDateIndonesian($date){
        $e = (strtoupper(substr(PHP_OS, 0, 3)) === 'WIN') ? '%#d' : '%e';
        $data = strftime($e." %B %Y",strtotime($date));

        $result = str_replace('Pebruari', 'Februari', $data);
        $result = str_replace('Nopember', 'November', $result);

        return $result;

    }

    // ==== PDF Schedule ====
    public function schedulePDF(){

        $token = $this->input->get('token');

        if(!isset($token)){
            $token = $this->input->post('token');
        }

        $data_arr = $this->getInputToken($token);

        $pdf = new FPDF('l','mm','A4');

        $data_sch = $this->m_save_to_pdf->getScheduleByDay($data_arr['SemesterID'],$data_arr['DayID'],date("Y-m-d"));

        if(count($data_sch)>0){

            $DayNameEng = $data_sch['DetailsSemester']['DayNameEng'];
            $SemesterDetails = $data_sch['DetailsSemester']['SemesterName'];

            $pdf->AddPage();
            $this->header_schedule($pdf,$SemesterDetails,$DayNameEng);

            $dataCourse = $data_sch['DetailsCourse'];


            $deft_w = 10;
            if(count($dataCourse)>0){

                $no = 1;
                for($m=0;$m<count($dataCourse);$m++){

                    $d_course = $dataCourse[$m];

                    $w = (count($d_course['detailTeamTeaching'])>0) ? (count($d_course['detailTeamTeaching'])+1) * $deft_w : $deft_w;

                    $pdf->SetFillColor(255, 206, 206);
                    $lb = ($d_course['Label']=='Ex') ? true : false;

                    $pdf->Cell(10,$w,$no,1,0,'C',$lb);
                    $pdf->Cell(20,$w,$d_course['ClassGroup'],1,0,'C');

                    $course = (strlen(trim($d_course['Course']))>=55) ? substr(trim($d_course['Course']),0,55).'_' : trim($d_course['Course']);
                    $pdf->Cell(90,$w,' '.$course,1,0,'L');

                    $pdf->Cell(20,$w,substr($d_course['StartSessions'],0,5).' - '.substr($d_course['EndSessions'],0,5),1,0,'C');
                    $pdf->Cell(25,$w,$d_course['ClassRoom'],1,0,'C');
                    $pdf->SetFont('Arial','B',8);
                    $coor = (strlen(trim($d_course['Coordinator']))>20) ? substr(trim($d_course['Coordinator']),0,19).'_' : trim($d_course['Coordinator']) ;
                    $pdf->Cell(42,$deft_w,' (Co) '.$coor,1,0,'L');

                    $pdf->Cell(20,$deft_w,'',1,0,'L');
                    $pdf->Cell(15,$deft_w,'',1,0,'L');
                    $pdf->Cell(20,$deft_w,'',1,0,'L');
                    $pdf->Cell(15,$deft_w,'',1,1,'L');

                    $pdf->SetFont('Arial','',8);
                    if(count($d_course['detailTeamTeaching'])>0){
                        for($t=0;$t<count($d_course['detailTeamTeaching']);$t++){
                            $teamT = $d_course['detailTeamTeaching'][$t];
                            $teamT2 = (strlen(trim($teamT))>26) ? substr(trim($teamT),0,25).'_' : trim($teamT) ;
                            $pdf->Cell(165,$deft_w,'',0,0,'L');
                            $pdf->Cell(42,$deft_w,' '.$teamT2,1,0,'L');
                            $pdf->Cell(20,$deft_w,'',1,0,'L');
                            $pdf->Cell(15,$deft_w,'',1,0,'L');
                            $pdf->Cell(20,$deft_w,'',1,0,'L');
                            $pdf->Cell(15,$deft_w,'',1,1,'L');
                        }
                    }

                    if($pdf->GetY()>173){
                        $pdf->AddPage();
                        $this->header_schedule($pdf,$SemesterDetails,$DayNameEng);
                    }

                    $no += 1;
                }
            }
            else {
                $pdf->Cell(275,7,'Schedule Not yet',1,1,'C');
            }

        }

        $pdf->Output('I',$DayNameEng.'_schedule.pdf');
    }

    private function header_schedule($pdf,$SemesterDetails,$DayNameEng){
        $pdf->Image(base_url('images/icon/logo-hr.png'),10,10,50);

        $pdf->SetFont('Arial','B',10);

        $pdf->Cell(60,9,'',0,0,'C');
        $pdf->Cell(0,9,'Semester - '.$SemesterDetails,1,1,'C');


        $pdf->Ln(5);
        $pdf->Cell(0,5,''.$DayNameEng,0,1,'C');

        $pdf->SetFont('Arial','i',7);
        $pdf->Cell(275,5,'FM-UAP/AKD-08-05',0,1,'R');
//        $pdf->Cell(275,5,''.$DayNameEng,0,1,'R');
//        $pdf->Ln(5);

        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Arial','B',8);
        $pdf->Cell(10,7,'No',1,0,'C',true);
        $pdf->Cell(20,7,'Group',1,0,'C',true);
        $pdf->Cell(90,7,'Course',1,0,'C',true);
        $pdf->Cell(20,7,'Time',1,0,'C',true);
        $pdf->Cell(25,7,'Room',1,0,'C',true);
        $pdf->Cell(42,7,'Lecturers',1,0,'C',true);
        $pdf->Cell(20,7,'Time',1,0,'C',true);
        $pdf->Cell(15,7,'Taken',1,0,'C',true);
        $pdf->Cell(20,7,'Time',1,0,'C',true);
        $pdf->Cell(15,7,'Returned',1,1,'C',true);

        $pdf->SetFont('Arial','',8);
    }

    // +++++++++++++++++++++++++++++++++++++++++++++

    // ==== PDF Monitoring Attendance Lecturer ====

    public function monitoringAttdLecturer(){
        $token = $this->input->post('token');

        $data_arr = $this->getInputToken($token);

        $pdf = new FPDF('l','mm','A4');

        $pdf->AddPage();

        $this->headerDefaultLanscape($pdf);

        $h = 5;

        $pdf->SetFont('Times','B',8);
        $pdf->Cell(296,$h,'ATTENDANCE REPORT SEMESTER '.strtoupper($data_arr['Semester']),0,1,'C');
        $pdf->Cell(296,$h,'PROGRAMME STUDY : '.strtoupper($data_arr['Prodi']),0,1,'C');

        $pdf->Ln(5);

        $h = 5.5;

        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Times','B',8);
        $pdf->Cell(8,$h + $h,'No',1,0,'C',true);
        $pdf->Cell(22,$h + $h,'Code',1,0,'C',true);
        $pdf->Cell(85,$h + $h,'Course',1,0,'C',true);
        $pdf->Cell(15,$h + $h,'Group',1,0,'C',true);
        $pdf->Cell(50,$h + $h,'Lecturer',1,0,'C',true);

        $pdf->Cell(20,$h + $h,'Day',1,0,'C',true);
        $pdf->Cell(20,$h + $h,'Time',1,0,'C',true);
        $pdf->Cell(24,$h + $h,'Room',1,0,'C',true);
        $pdf->Cell(43,$h,'Session',1,1,'C',true);

        $pdf->Cell(244,$h,'',0,0,'C');
        $pdf->Cell(14,$h,'Target',1,0,'C',true);
        $pdf->Cell(14,$h,'Real',1,0,'C',true);
        $pdf->Cell(15,$h,'%',1,1,'C',true);

        $pdf->SetFont('Times','',8);
        $save2PDF = (array) $data_arr['save2PDF'];
        if(count($save2PDF)>0){
            $no = 1;
            for($s=0;$s<count($save2PDF);$s++){
                $d = (array) $save2PDF[$s];

                $newH = $h * count($d['Schedule']);

                $pdf->Cell(8,$newH,($no++),1,0,'C');
                $pdf->Cell(22,$newH,$d['MKCode'],1,0,'C');
                $pdf->Cell(85,$newH,' '.$d['MKNameEng'],1,0);
                $pdf->Cell(15,$newH,$d['ClassGroup'],1,0,'C');
                $pdf->Cell(50,$newH,' '.$d['Lecturer'],1,0);

                for($c=0;$c<count($d['Schedule']);$c++){
                    $dt = (array) $d['Schedule'][$c];

                    if($c!=0){
                        $pdf->Cell(180,$h,'',0,0,'C');
                    }

                    $pdf->Cell(20,$h,$dt['Day'],1,0,'C');
                    $pdf->Cell(20,$h,$dt['Time'],1,0,'C');
                    $pdf->Cell(24,$h,$dt['Room'],1,0,'C');

                    $pdf->Cell(14,$h,$dt['Target'],1,0,'C');
                    $pdf->Cell(14,$h,$dt['Real'],1,0,'C');
                    $pdf->Cell(15,$h,$dt['Percent'],1,1,'C');

                }



            }
        }



        $pdf->Output('I','Monitoring_Attendance_Lecturer.pdf');


    }

    // ++++++++++++++++++++++++++++++++++++++

    // ==== PDF Schedule Exchange =====

    public function scheduleExchange(){
        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);

        $pdf = new FPDF('l','mm','A4');

        $pdf->AddPage();

        $this->headerDefaultLanscape($pdf);

        $h = 5;

        $pdf->SetFont('Times','B',8);
        $pdf->Cell(296,$h,'SCHEDULE EXCHANGE',0,1,'C');

        $pdf->Ln(5);

        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Times','B',8);
        $pdf->Cell(8,$h + $h,'No',1,0,'C',true);
//        $pdf->Cell(22,$h + $h,'Code',1,0,'C',true);
        $pdf->Cell(86,$h + $h,'Course',1,0,'C',true);
        $pdf->Cell(15,$h + $h,'Group',1,0,'C',true);
        $pdf->Cell(10,$h + $h,'Sesi',1,0,'C',true);

        $pdf->Cell(38,$h + $h,'Reason',1,0,'C',true);
        $pdf->Cell(10,$h + $h,'Status',1,0,'C',true);


        $pdf->SetFillColor(202, 215, 255);
        $pdf->Cell(60,$h,'Schedule Exist',1,0,'C',true);

        $pdf->SetFillColor(255, 226, 226);
        $pdf->Cell(60,$h,'Exchange to',1,1,'C',true);

        $pdf->SetFillColor(202, 215, 255);
        $pdf->Cell(167,$h,'',0,0,'C');
        $pdf->Cell(34,$h,'Day, Time',1,0,'C',true);
        $pdf->Cell(26,$h,'Room',1,0,'C',true);

        $pdf->SetFillColor(255, 226, 226);
        $pdf->Cell(34,$h,'Day, Time',1,0,'C',true);
        $pdf->Cell(26,$h,'Room',1,1,'C',true);



        $pdf->SetFont('Times','',8);

        $ch_atas = 4;
        $ch = 4;

        $no = 1;
        for($i=0;$i<count($data_arr);$i++){

            $d = (array) $data_arr[$i];

            //27
            if(strlen($d['Reason'])>27){
                $r1 = substr($d['Reason'],0,26).'-';
                $r_2 = substr($d['Reason'],26,26);

                $r2 = (strlen($r_2)>27) ? $r_2.'_' : $r_2;


            } else {
                $r1 = $d['Reason'];
                $r2 = '';
            }

            $pdf->Cell(8,$ch_atas,($no++),'LRT',0,'C');
            $pdf->SetFont('Times','B',8);
            $pdf->Cell(86,$ch_atas,$d['Course'],'LRT',0,'L');
            $pdf->SetFont('Times','',8);
            $pdf->Cell(15,$ch_atas,$d['ClassGroup'],'LRT',0,'C');
            $pdf->Cell(10,$ch_atas,$d['A_Sesi'],'LRT',0,'C');
            $pdf->Cell(38,$ch_atas,$r1,'LRT',0,'L');

            if($d['Status']=='2'){
                $pdf->SetFont('ZapfDingbats');
                $pdf->Cell(10,$ch_atas,chr(52),'LRT',0,'C');
            } else {
                $pdf->Cell(10,$ch_atas,'-','LRT',0,'C');
            }



            $pdf->SetFont('Times','',8);
            $pdf->Cell(34,$ch_atas,$d['A_Date'],'LRT',0,'C');
            $pdf->Cell(26,$ch_atas,$d['A_Room'],'LRT',0,'C');
            $pdf->Cell(34,$ch_atas,$d['T_Date'],'LRT',0,'C');
            $pdf->Cell(26,$ch_atas,$d['T_Room'],'LRT',1,'C');


            $pdf->Cell(8,$ch,'','LRB',0,'C');
            $pdf->Cell(86,$ch,$d['Lecturer'],'LRB',0,'L');
            $pdf->Cell(15,$ch,'','LRB',0,'C');
            $pdf->Cell(10,$ch,'','LRB',0,'C');
            $pdf->Cell(38,$ch,$r2,'LRB',0,'L');
            $pdf->Cell(10,$ch,'','LRB',0,'C');
            $pdf->Cell(34,$ch,$d['A_Time'],'LRB',0,'C');
            $pdf->Cell(26,$ch,'','LRB',0,'C');
            $pdf->Cell(34,$ch,$d['T_Time'],'LRB',0,'C');
            $pdf->Cell(26,$ch,'','LRB',1,'C');
        }


        $pdf->Output('I','Schedule_Exchange.pdf');
    }



    // ++++++++++++++++++++++++++++++++++

    // ==== PDF Monitoring Students ===

    public function monitoringStudent(){

        $token = $this->input->post('token');

        $data_arr = $this->getInputToken($token);

        $course = (array) $data_arr['Course'][0];
        $student = (array) $data_arr['Student'];


        $pdf = new FPDF('l','mm','A4');

        $pdf->AddPage();
        $this->headerDefaultLanscape($pdf);


        $h = 4;

        $pdf->SetFont('Times','B',8);
//        $pdf->Cell(296,$h,'SCHEDULE EXCHANGE',0,1,'C');

        $pdf->Cell(25,$h,'Semester',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(123,$h,$course['Semester'],0,0,'L');
        $pdf->Cell(25,$h,'Code',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(123,$h,$course['MKCode'],0,1,'L');

        $pdf->Cell(25,$h,'Course',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(123,$h,$course['NameEng'],0,0,'L');
        $pdf->Cell(25,$h,'Class Group',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(123,$h,$course['ClassGroup'],0,1,'L');

        $pdf->Cell(25,$h,'Lecturer',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(123,$h,$course['Lecturer'],0,0,'L');
        $pdf->Cell(25,$h,'',0,0,'L');
        $pdf->Cell(5,$h,'',0,0,'C');
        $pdf->Cell(123,$h,'',0,1,'L');

        $pdf->Ln(5);

        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Times','B',8);
        $pdf->Cell(8,$h + $h,'No',1,0,'C',true);
        $pdf->Cell(18,$h + $h,'NIP',1,0,'C',true);
        $pdf->Cell(60,$h + $h,'Name',1,0,'C',true);

        $pdf->Cell(13,$h + $h,'Target',1,0,'C',true);
        $pdf->Cell(13,$h + $h,'Real',1,0,'C',true);
        $pdf->Cell(18.5,$h + $h,'%',1,0,'C',true);
        $pdf->Cell(30,$h+$h,'Day',1,0,'C',true);

        $pdf->Cell(126,$h,'Session',1,1,'C',true);

        $pdf->Cell(160.5,$h,'',0,0,'C');

        $pdf->Cell(9,$h,'1',1,0,'C',true);
        $pdf->Cell(9,$h,'2',1,0,'C',true);
        $pdf->Cell(9,$h,'3',1,0,'C',true);
        $pdf->Cell(9,$h,'4',1,0,'C',true);
        $pdf->Cell(9,$h,'5',1,0,'C',true);
        $pdf->Cell(9,$h,'6',1,0,'C',true);
        $pdf->Cell(9,$h,'7',1,0,'C',true);
        $pdf->Cell(9,$h,'8',1,0,'C',true);
        $pdf->Cell(9,$h,'9',1,0,'C',true);
        $pdf->Cell(9,$h,'10',1,0,'C',true);
        $pdf->Cell(9,$h,'11',1,0,'C',true);
        $pdf->Cell(9,$h,'12',1,0,'C',true);
        $pdf->Cell(9,$h,'13',1,0,'C',true);
        $pdf->Cell(9,$h,'14',1,1,'C',true);




        $pdf->SetFont('Times','',8);
        // Load Data
        $no =1;
        $hrow = 5;
        for($i=0;$i<count($student);$i++){

            $d = (array) $student[$i];

            $attd = $d['Attendance'];

            if(count($attd)>0){
                $hrowD = $hrow * count($attd);

                $pdf->Cell(8,$hrowD,($no++),1,0,'C');
                $pdf->Cell(18,$hrowD,$d['NPM'],1,0,'C');
                $pdf->Cell(60,$hrowD,' '.$d['Name'],1,0,'L');

                $pdf->Cell(13,$hrowD,$d['Target'],1,0,'C');
                $pdf->SetFont('Times','B',8);
                $pdf->Cell(13,$hrowD,$d['Total_Attd'],1,0,'C');
                $pdf->Cell(18.5,$hrowD,$d['Percent'],1,0,'C');
                $pdf->SetFont('Times','',8);

//            $pdf->SetTextColor(0,255,0);

                for($t=0;$t<count($attd);$t++){

                    if($t!=0){
                        $pdf->Cell(130.5,$hrow,'',0,0,'C');
                    }

                    $da = (array) $attd[$t];

                    $pdf->Cell(30,$hrow,' '.$da['DayEng'],1,0,'L');

                    if($da['M1']==1 || $da['M1']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M1']==2 || $da['M1']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M2']==1 || $da['M2']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M2']==2 || $da['M2']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M3']==1 || $da['M3']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M3']==2 || $da['M3']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M4']==1 || $da['M4']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M4']==2 || $da['M4']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M5']==1 || $da['M5']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M5']==2 || $da['M5']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M6']==1 || $da['M6']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M6']==2 || $da['M6']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M7']==1 || $da['M7']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M7']==2 || $da['M7']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M8']==1 || $da['M8']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M8']==2 || $da['M8']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M9']==1 || $da['M9']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M9']==2 || $da['M9']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M10']==1 || $da['M10']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M10']==2 || $da['M10']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M11']==1 || $da['M11']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M11']==2 || $da['M11']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M12']==1 || $da['M12']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M12']==2 || $da['M12']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M13']==1 || $da['M13']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M13']==2 || $da['M13']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,0,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,0,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                    if($da['M14']==1 || $da['M14']=='1'){
                        $pdf->SetTextColor(0, 147, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(52),1,1,'C');
                        $pdf->SetFont('Times','',8);
                    } else if($da['M14']==2 || $da['M14']=='2'){
                        $pdf->SetTextColor(147, 0, 0);
                        $pdf->SetFont('ZapfDingbats');
                        $pdf->Cell(9,$hrow,chr(54),1,1,'C');
                        $pdf->SetFont('Times','',8);
                    } else {
                        $pdf->SetTextColor(0, 0, 0);
                        $pdf->Cell(9,$hrow,'-',1,1,'C');
                    }
                    $pdf->SetTextColor(0, 0, 0);

                }
            }



        }

        $pdf->Output('I','Monitoring_Attendance_Students.pdf');
    }

    // ++++++++++++++++++++++++++++++++


    // ==== Monitoring Attendance By Range Date

    public function monitoringAttendanceByRangeDate(){


        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);
        $datawarek1 = $this->db->get_where('db_employees.employees em',
        "(em.PositionMain = '2.2' OR em.PositionOther1 = '2.2' OR em.PositionOther2 = '2.2' OR em.PositionOther3 = '2.2') AND  em.StatusEmployeeID = '1'")
        //array('PositionMain' => '2.2','StatusEmployeeID'=>'1' ))
        ->result_array();


     //print_r(datawarek1);
//        exit;

        $pdf = new FPDF('l','mm','A4');

        $pdf->AddPage();
        $this->headerDefaultLanscape($pdf);

        $totalTgl = count($data_arr['PDFarrDate']);
        $dateHeaderObj = (array) $data_arr['PDFarrDate'];
        $wTgl = 4;

        // 287
        $h = 3;

        $pdf->SetFont('Times','B',8);
        $pdf->Cell(287,$h,'Semester '.$data_arr['Semester'],0,1,'C');
        $pdf->Cell(287,$h,$data_arr['Employees'],0,1,'C');
        $this->header_monitoringAttendanceByRangeDate($pdf,$data_arr);


        $h_body = 5;
        if(count($data_arr['Details'])>0){
            $no = 1;
            for($t=0;$t<count($data_arr['Details']);$t++){
                $DetailsObj = (array) $data_arr['Details'];
                $d = (array) $DetailsObj[$t];

                $pdf->Cell(7,$h_body * count($d['Course']),$no,1,0,'C');
                $pdf->Cell(15,$h_body * count($d['Course']),$d['NIP'],1,0,'C');

                $LecName = (strlen($d['Name'])>34) ? substr($d['Name'],0,34).'_' : $d['Name'];

                $pdf->Cell(35,$h_body * count($d['Course']),$LecName,1,0,'L');

                //---- menghitung total sks per Dosen---
                $sumCredit = 0;
                for($c2=0;$c2<count($d['Course']);$c2++){
                  $cObj = (array) $d['Course'];
                  $c = (array) $cObj[$c2];
                  $sumCredit = $sumCredit + $c['Credit'];
                }
                $pdf->Cell(15,$h_body * count($d['Course']),$sumCredit,1,0,'C');


                // Cek Group
                for($r=0;$r<count($d['Course']);$r++){

                    if($r!=0){
                        $pdf->Cell(57+15,$h_body,'',0,0,'C');
                    }


                    $cObj = (array) $d['Course'];
                    $c = (array) $cObj[$r];


                    $pdf->Cell(10,$h_body,$c['Credit'],1,0,'C');
                    $CNameCourse = (strlen($c['NameEng'])>46) ? substr($c['NameEng'],0,46).'_' : $c['NameEng'];
                    $pdf->Cell(15,$h_body,$c['ClassGroup'],1,0,'C');
                    $pdf->Cell(40,$h_body,$CNameCourse,1,0,'L');


                    $AttdObj = (array) $c['Attendance'];

                    $totalSesi = 0;
                    for($i=0;$i<$totalTgl;$i++){
                        $sts = 0;
                        if(count($AttdObj)>0){

                            for($a=0;$a<count($AttdObj);$a++){
                                $d_Attd = $AttdObj[$a];
                                if($dateHeaderObj[$i]==$d_Attd){
                                    $sts = $sts + 1;
                                }
                            }
                        }
                        $totalSesi = $totalSesi + $sts;
                        $ssSts = ($sts!=0) ? $sts : '';
                        if($sts!=0){
                            $pdf->SetFillColor(153, 255, 153);
                        } else if(date('N', strtotime($dateHeaderObj[$i]))==6 || date('N', strtotime($dateHeaderObj[$i]))==7){
                            $pdf->SetFillColor(224, 224, 224);
                        } else {
                            $pdf->SetFillColor(255, 255, 255);
                        }
//                        $cl = ($sts!=0) ? true : false;
//                        $pdf->Cell($wTgl,$h_body,$ssSts,1,0,'C',true);
                        $pdf->Cell($wTgl,$h_body,$ssSts,1,0,'C',true);


                    }

                    $pdf->SetFont('Times','B',7);
                    $pdf->SetFillColor(255, 255, 204);
                    $pdf->Cell(10,$h_body,$totalSesi,1,0,'C',true);
                    $totalCredit = ($totalSesi!=0) ? $totalSesi * (int) $c['Credit'] : 0;
                    $pdf->Cell(15,$h_body,$totalCredit,1,1,'C',true);
                    $pdf->SetFont('Times','',7);



                }

                if($pdf->GetY()>173){
                    $pdf->AddPage();
                    $this->header_monitoringAttendanceByRangeDate($pdf,$data_arr);
                }

                $no++;

            }
        }
// req victor_HRD, minta dihapus 16-04-2021 - m
//         $pdf->Ln(9);
//         $h = 4.5;
//         $w_ttd = 50;
//         $pdf->SetFillColor(224, 224, 224);
//         $pdf->SetFont('Times','',7);
// //        $pdf->Cell(287,$h,'Semester '.$data_arr['Semester'],1,1,'C');
//         $pdf->Cell($w_ttd,$h,'Reported by',1,0,'C',true);
//         $pdf->Cell($w_ttd,$h,'Acknowledge by',1,0,'C',true);
//         $pdf->Cell($w_ttd,$h,'Acknowledge by',1,1,'C',true);
//
//         $pdf->Cell($w_ttd,$h,'Date : ',1,0,'L');
//         $pdf->Cell($w_ttd,$h,'Date : ',1,0,'L');
//         $pdf->Cell($w_ttd,$h,'Date : ',1,1,'L');
//         $h = 19;
//         $pdf->Cell($w_ttd,$h,'',1,0,'L');
//         $pdf->Cell($w_ttd,$h,'',1,0,'L');
//         $pdf->Cell($w_ttd,$h,'',1,1,'L');
//
//         $h = 4.5;
//         $pdf->SetFont('Times','B',7);
//         $pdf->Cell($w_ttd,$h,'',1,0,'C');
//         // Get Kabag Akademik id 6.11
//         $dataKabagAkademik = $this->m_save_to_pdf->getEmployeesByPositionMain('6.11');
//         $kabag = (count($dataKabagAkademik)>0) ? $dataKabagAkademik[0]['Name'] : '' ;
//         $pdf->Cell($w_ttd,$h,$kabag,1,0,'C');
//
//         // $dataRektorat1 = $this->m_save_to_pdf->getEmployeesByPositionMain('2.2');
//         // $Rektorat1 = (count($dataRektorat1)>0) ? $dataRektorat1[0]['Name'] : '' ;
//         $warek1 = $datawarek1[0]['TitleAhead'].' '.$datawarek1[0]['Name'].' '.$datawarek1[0]['TitleBehind'];
//         //$pdf->Cell($fillFull,$h,$warek1,$border,1,'L');
//         $pdf->Cell($w_ttd,$h,$warek1,1,1,'C');
//
//         $pdf->SetFont('Times','',7);
//         $pdf->Cell($w_ttd,$h,'Staff SAS',1,0,'C');
//         $pdf->Cell($w_ttd,$h,'Kabag. Administrasi Perkuliahan',1,0,'C');
//         $pdf->Cell($w_ttd,$h,'Wakil Rektor I Bidang Akademik',1,1,'C');
//

        $pdf->Ln(5);
        $pdf->SetFont('Times','B',10);
        $pdf->Cell(190, 7, '*This document was issued electronically and is therefore valid without signature.', 0, 1, 'L');

        $pdf->Ln(5);
        $pdf->SetFont('Times','I',7);
        $pdf->Cell(287,$h,'Download On : '.date("l, d F Y H:i:s").' | '.chr(169).' Podomoro University',0,0,'C');

        $pdf->Output('I','Monitoring_Attendance_Range_Date.pdf');
    }

    private function header_monitoringAttendanceByRangeDate($pdf,$data_arr){
        $totalTgl = count($data_arr['PDFarrDate']);
        $dateHeaderObj = (array) $data_arr['PDFarrDate'];

        $wTgl = 4;

        $pdf->Ln(5);

        $pdf->SetFont('Times','B',7);
        $pdf->SetFillColor(153, 204, 255);
        $h_header = 4;
        // 287
        $pdf->Cell(7,$h_header,'No','TRL',0,'C',true);
        $pdf->Cell(15,$h_header,'NIP','TRL',0,'C',true);
        $pdf->Cell(35,$h_header,'Name','TRL',0,'C',true);
        $pdf->Cell(15,$h_header,'Total','TRL',0,'C',true);
        $pdf->Cell(10,$h_header,'Credit','TRL',0,'C',true);
        $pdf->Cell(15,$h_header,'Group','TRL',0,'C',true);
        $pdf->Cell(40,$h_header,'Course','TRL',0,'C',true);



        $pdf->Cell(($wTgl * $totalTgl),$h_header,$data_arr['RangeDate'],1,0,'C',true);
        $pdf->Cell(10,$h_header,'Total','TRL',0,'C',true);
        $pdf->Cell(15,$h_header,'','TRL',1,'C',true);


        $pdf->Cell(7,$h_header,'','BRL',0,'C',true);
        $pdf->Cell(15,$h_header,'','BRL',0,'C',true);
        $pdf->Cell(35,$h_header,'','BRL',0,'C',true);
        $pdf->Cell(15,$h_header,'Credit','BRL',0,'C',true);
        $pdf->Cell(10,$h_header,'','BRL',0,'C',true);
        $pdf->Cell(15,$h_header,'','BRL',0,'C',true);
        $pdf->Cell(40,$h_header,'','BRL',0,'C',true);


        for($i=0;$i<$totalTgl;$i++){
            $dateHeader = $dateHeaderObj[$i];
            if(date('N', strtotime($dateHeader))==6 || date('N', strtotime($dateHeader))==7){
                $pdf->SetFillColor(255, 153, 153);
            }
            $pdf->Cell($wTgl,$h_header,''.substr($dateHeader,8,2),1,0,'C',true);
            $pdf->SetFillColor(153, 204, 255);

        }

        $pdf->Cell(10,$h_header,'Sesi','BRL',0,'C',true);
        $pdf->Cell(15,$h_header,'Sesi x Credit','BRL',1,'C',true);

        $pdf->SetFont('Times','',7);
    }

    // ++++++++++++++++++++++++++++++++++++++++++++++++++++++++

    private function headerDefaultLanscape($pdf){
        $pdf->Image(base_url('images/icon/logo-hr.png'),10,10,50);

        $pdf->SetFont('Times','B',11);

        $pdf->Ln(1);



        $pdf->Cell(296,5,'Universitas Agung Podomoro',0,1,'C');

        $h = 4;
        $pdf->SetFont('Times','',8);
        $pdf->Cell(296,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28 Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');
        $pdf->Cell(296,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(10,27,296,27);

        $pdf->Ln(7);

    }

    private function headerDefaultPotret($pdf,$NoForm){
        $pdf->Image(base_url('images/icon/favicon.png'),15,13,15);

        $pdf->SetFont('Times','I',7);
        $pdf->Cell(195,1,$NoForm,0,1,'R');

        $h = 5;

        $pdf->SetFont('Times','B',14);
        $pdf->Cell(195,$h,'Universitas Agung Podomoro',0,1,'C');

        $pdf->Ln(1);

        $pdf->SetFont('Times','',10);
        $pdf->Cell(195,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');
        $pdf->Cell(195,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');
        $pdf->Cell(195,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(10,35,205,35);

        $pdf->Ln(7);
    }

    // ========== Exam PDF =========

    public function filterDocument(){
        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);

//        print_r($data_arr);exit;


        if($data_arr['DocumentType']==1){

            if(isset($data_arr['IsSemesterSA']) && ($data_arr['IsSemesterSA']=='1' || $data_arr['IsSemesterSA']==1)){
                $dataExam = $this->m_save_to_pdf->getExamSchedule_SA($data_arr['SASemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            } else {
                // Get data exam
                $dataExam = $this->m_save_to_pdf->getExamSchedule($data_arr['SemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            }

//            print_r($dataExam);exit;

            $pdf = new FPDF('l','mm','A4');

            if(count($dataExam)>0){
                for($ex=0;$ex<count($dataExam);$ex++){
                    if(count($dataExam[$ex]['Course'])>0){
                        for($i=0;$i<count($dataExam[$ex]['Course']);$i++){
                            // membuat halaman baru
                            $pdf->AddPage();
                            $dataDetailExam = $dataExam[$ex];
                            $dataCourse = $dataExam[$ex]['Course'][$i];

                            $this->news_event($pdf,$data_arr,$dataDetailExam,$dataCourse);

                            $pdf->SetFont('Times','',7);
                            $pdf->Ln(9);
                            $pdf->Cell(135,1,'Page : '.$pdf->PageNo().' of {nb}',0,0,'R');
                            $pdf->Cell(17,1,'',0,0);
                            $pdf->Cell(135,1,'Page : '.$pdf->PageNo().' of {nb}',0,1,'R');
                            $pdf->AliasNbPages();

                        }
                    }
                }
            }

            $pdf->Output('document_penyerahan_berkas.pdf','I');
        }
        else if($data_arr['DocumentType']==2){

            if(isset($data_arr['IsSemesterSA']) && ($data_arr['IsSemesterSA']=='1' || $data_arr['IsSemesterSA']==1)){
                $dataExam = $this->m_save_to_pdf->getExamSchedule_SA($data_arr['SASemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            } else {
                // Get data exam
                $dataExam = $this->m_save_to_pdf->getExamSchedule($data_arr['SemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            }


            $pdf = new FPDF('P','mm','A4');

            if(count($dataExam)>0){
                for($ex=0;$ex<count($dataExam);$ex++){
                    if(count($dataExam[$ex]['Course'])>0){
                        for($i=0;$i<count($dataExam[$ex]['Course']);$i++){
                            // membuat halaman baru
                            $pdf->AddPage();
                            $dataDetailExam = $dataExam[$ex];
                            $dataCourse = $dataExam[$ex]['Course'][$i];

                            $this->news_implementation($pdf,$data_arr,$dataDetailExam,$dataCourse);

                            $pdf->SetFont('Times','',7);
                            $pdf->Ln(9);
                            $pdf->Cell(195,1,'Page : '.$pdf->PageNo().' of {nb}',0,1,'R');
                            $pdf->AliasNbPages();

                        }
                    }
                }
            }

            $pdf->Output('document_pelaksaan_ujian.pdf','I');
        }
        else if($data_arr['DocumentType']==3){

            if(isset($data_arr['IsSemesterSA']) && ($data_arr['IsSemesterSA']=='1' || $data_arr['IsSemesterSA']==1)){
                $dataExam = $this->m_save_to_pdf->getExamScheduleWithStudent_SA($data_arr['SASemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            } else {
                // Get data exam
                $dataExam = $this->m_save_to_pdf->getExamScheduleWithStudent($data_arr['SemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            }

            $pdf = new FPDF('P','mm','A4');

            if(count($dataExam)>0){

                for($ex=0;$ex<count($dataExam);$ex++){
                    if(count($dataExam[$ex]['Course'])>0){
                        for($i=0;$i<count($dataExam[$ex]['Course']);$i++){
                            // membuat halaman baru
                            $pdf->AddPage();
                            $dataDetailExam = $dataExam[$ex];
                            $dataCourse = $dataExam[$ex]['Course'][$i];

                            $this->header_exam_attendance_students($pdf,$data_arr,$dataDetailExam,$dataCourse);
                            $this->exam_attendance_students($pdf,$data_arr,$dataDetailExam,$dataCourse);

                            $pdf->SetFont('Times','',7);
                            $pdf->Ln(5);
                            $pdf->Cell(195,1,'Page : '.$pdf->PageNo().' of {nb}',0,1,'R');
                            $pdf->AliasNbPages();

                        }
                    }
                }

            }

            $pdf->Output('document_attendance_exam_student.pdf','I');
        }
        else if($data_arr['DocumentType']==4){

            if(isset($data_arr['IsSemesterSA']) && ($data_arr['IsSemesterSA']=='1' || $data_arr['IsSemesterSA']==1)){
                $dataExam = $this->m_save_to_pdf->getExamSchedule_SA($data_arr['SASemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            } else {
                // Get data exam
                $dataExam = $this->m_save_to_pdf->getExamSchedule($data_arr['SemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            }

            $pdf = new FPDF('P','mm','A4');
            if(count($dataExam)>0){
                $pdf->SetMargins(3,3,10);
                $pdf->AddPage();

//                $this->header_attendance_pengawas($pdf,$data_arr);
                $this->header_attendance_pengawas_p($pdf,$data_arr);


                for($ex=0;$ex<count($dataExam);$ex++){

                    $dataDetailExam = $dataExam[$ex];

//                    $this->attendance_pengawas($pdf,$data_arr,$dataDetailExam);
                    $this->attendance_pengawas_p($pdf,$data_arr,$dataDetailExam);
                }
            }

            $pdf->Output('document_pengawas.pdf','I');

        }
        else if($data_arr['DocumentType']==5){

            if(isset($data_arr['IsSemesterSA']) && ($data_arr['IsSemesterSA']=='1' || $data_arr['IsSemesterSA']==1)){
                $dataExam = $this->m_save_to_pdf->getExamScheduleWithStudent_SA($data_arr['SASemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            } else {
                // Get data exam
                $dataExam = $this->m_save_to_pdf->getExamScheduleWithStudent($data_arr['SemesterID'],$data_arr['Type'],$data_arr['ExamDate']);
            }

            if(count($dataExam)>0){

                $pdf = new FPDF('L','mm','A4');

                if(count($dataExam)==1){
                    $this->exam_template_map($pdf,$data_arr,$dataExam[0],'');
                } else {
                    for($i=0;$i<count($dataExam);$i++){

                        $data2 = (isset($dataExam[$i + 1])) ? $dataExam[$i + 1] : '';

                        $this->exam_template_map($pdf,$data_arr,$dataExam[$i],$data2);

                        $i += 1;

                    }
                }

                $pdf->Output('document_map.pdf','I');

            } else {
                echo "Attribut / Data is empty";
            }

        }


    }

    private function news_event($pdf,$data_arr,$dataDetailExam,$dataCourse){

//        $pdf->SetMargins(5,5,0);

        $pdf->Rect(3, 7, 140, 185);
        $pdf->Rect(155, 7, 140, 185);

        $pdf->Image(base_url('images/icon/favicon.png'),5,10,13);
        $pdf->Image(base_url('images/icon/favicon.png'),158,10,13);

        $h=7;

        $pdf->SetFont('Times','B',12);
        $pdf->Cell(133,$h,'Universitas Agung Podomoro',0,0,'C');
        $pdf->Cell(20,$h,'',0,0);
        $pdf->Cell(132,$h,'Universitas Agung Podomoro',0,1,'C');

        $h=5;

        $pdf->SetFont('Times','',10);
        $pdf->Cell(135,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');

        $pdf->Cell(135,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');

        $pdf->Cell(135,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(3,33,143,33);
        $pdf->Line(155,33,295,33);

        $pdf->Ln(4);

        $pdf->SetFont('Times','B',10);

        $Semester = explode(' ',$data_arr['Semester']);
        $xam_t = ($data_arr['Type']=='uts' || $data_arr['Type']=='UTS') ? 'MID EXAM' : 'FINAL EXAM';
        $xam_h = (trim($Semester[1])=='Ganjil') ? 'ODD' : 'EVEN';

        $Antara = (isset($data_arr['IsSemesterSA']) && ($data_arr['IsSemesterSA']==1 || $data_arr['IsSemesterSA']=='1')) ? ' ANTARA' : '';

        $pdf->Cell(135,$h,$xam_t.' PAPER HANDOVER',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,$xam_t.' PAPER HANDOVER',0,1,'C');


        $pdf->Cell(135,$h,$xam_h.' ACADEMIC YEAR '.strtoupper(trim($Semester[0])).''.$Antara,0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,$xam_h.' ACADEMIC YEAR '.strtoupper(trim($Semester[0])).''.$Antara,0,1,'C');

        $pdf->SetFont('Times','',10);

        $pdf->Ln(4);

        $pdf->Cell(135,$h,'It has been handover the '.strtolower($xam_t).' paper',0,0,'L');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'It has been handover the '.strtolower($xam_t).' paper',0,1,'L');

        $pdf->Ln(4);

        $w_t = 30;
        $w_pars = 5;
        $w_fill = 100;
        $w_space = 17;


        $course = (strlen($dataCourse['Course'])>=43) ? substr($dataCourse['Course'],0,43).'_': $dataCourse['Course'] ;

        $pdf->Cell($w_t,$h,'Course',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['MKCode'].' - '.$course,0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell($w_t,$h,'Course',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['MKCode'].' - '.$course,0,1,'L');

        $pdf->Cell($w_t,$h,'Class Group',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['ClassGroup'],0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell($w_t,$h,'Class Group',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['ClassGroup'],0,1,'L');


        $pdf->Cell($w_t,$h,'Study Program',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['Prodi'],0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell($w_t,$h,'Study Program',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['Prodi'],0,1,'L');



        $pdf->Cell($w_t,$h,'Lecturer',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['Lecturere'],0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell($w_t,$h,'Lecturer',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['Lecturere'],0,1,'L');


        $pdf->Cell($w_t,$h,'Day, Date',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,date("l, d F Y", strtotime($dataDetailExam['ExamDate'])),0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell($w_t,$h,'Day, Date',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,date("l, d F Y", strtotime($dataDetailExam['ExamDate'])),0,1,'L');


        $pdf->Cell($w_t,$h,'Time',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,substr($dataDetailExam['ExamStart'],0,5).' - '.substr($dataDetailExam['ExamEnd'],0,5),0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell($w_t,$h,'Time',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,substr($dataDetailExam['ExamStart'],0,5).' - '.substr($dataDetailExam['ExamEnd'],0,5),0,1,'L');

        $pdf->Cell($w_t,$h,'Room',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataDetailExam['Room'],0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell($w_t,$h,'Room',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataDetailExam['Room'],0,1,'L');

        $pdf->Cell($w_t,$h,'Total Script',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,'_ _ _ _ _ _ _ _ _ _ _ _ _ _',0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell($w_t,$h,'Total Script',0,0,'L');
        $pdf->Cell($w_pars,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,'_ _ _ _ _ _ _ _ _ _ _ _ _ _',0,1,'L');


        $pdf->Ln(10);

        $pdf->Line(25,107,130,107);
        $pdf->Line(180,107,280,107);

        $pdf->SetFont('Times','B',11);
        $pdf->Cell(135,$h,'Taking',0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell(135,$h,'Taking',0,1,'L');

        $pdf->SetFont('Times','B',10);
        $pdf->Cell(67.5,$h,'Delivered by',0,0,'C');
        $pdf->Cell(67.5,$h,'Received by',0,0,'C');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell(67.5,$h,'Delivered by',0,0,'C');
        $pdf->Cell(67.5,$h,'Received by',0,1,'C');

        $pdf->Ln(15);
        $pdf->SetFont('Times','',10);
        $pdf->Cell(67.5,$h,'( _ _ _ _ _ _ _ _ _ _ )',0,0,'C');
        $pdf->Cell(67.5,$h,'( _ _ _ _ _ _ _ _ _ _ )',0,0,'C');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell(67.5,$h,'( _ _ _ _ _ _ _ _ _ _ )',0,0,'C');
        $pdf->Cell(67.5,$h,'( _ _ _ _ _ _ _ _ _ _ )',0,1,'C');

        $pdf->Ln(10);

        $pdf->Line(25,147,130,147);
        $pdf->Line(180,147,280,147);

        $pdf->SetFont('Times','B',11);
        $pdf->Cell(135,$h,'Return',0,0,'L');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell(135,$h,'Return',0,1,'L');

        $pdf->SetFont('Times','B',10);
        $pdf->Cell(67.5,$h,'Delivered by',0,0,'C');
        $pdf->Cell(67.5,$h,'Received by',0,0,'C');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell(67.5,$h,'Delivered by',0,0,'C');
        $pdf->Cell(67.5,$h,'Received by',0,1,'C');

        $pdf->Ln(15);
        $pdf->SetFont('Times','',10);
        $pdf->Cell(67.5,$h,'( _ _ _ _ _ _ _ _ _ _ )',0,0,'C');
        $pdf->Cell(67.5,$h,'( _ _ _ _ _ _ _ _ _ _ )',0,0,'C');
        $pdf->Cell($w_space,$h,'',0,0);
        $pdf->Cell(67.5,$h,'( _ _ _ _ _ _ _ _ _ _ )',0,0,'C');
        $pdf->Cell(67.5,$h,'( _ _ _ _ _ _ _ _ _ _ )',0,1,'C');

    }

    private function news_implementation($pdf,$data_arr,$dataDetailExam,$dataCourse){

//        $pdf->Rect(10, 7, 195, 280);

        $pdf->Image(base_url('images/icon/favicon.png'),15,13,15);

        $pdf->SetFont('Times','I',7);
        $pdf->Cell(195,1,'FM-UAP/AKD-13-05',0,1,'R');

        $h = 5;

        $pdf->SetFont('Times','B',14);
        $pdf->Cell(195,$h,'Universitas Agung Podomoro',0,1,'C');

        $pdf->Ln(1);

        $pdf->SetFont('Times','',10);
        $pdf->Cell(195,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');
        $pdf->Cell(195,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');
        $pdf->Cell(195,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(10,35,205,35);

        $pdf->Ln(10);

        $pdf->SetFont('Times','B',12);
        $pdf->Cell(195,$h,'BERITA ACARA PELAKSANAAN UJIAN',0,1,'C');
//        $pdf->Cell(195,$h,'ACADEMIC YEAR '.strtoupper($data_arr['Semester']),0,1,'C');

        $examType = ($data_arr['Type']=='UTS' || $data_arr['Type']=='uts')
            ? 'Ujian Tengah Semester GANJIL' : 'Ujian Akhir Semester GENAP';

        $pdf->Ln(5);
        $pdf->SetFont('Times','',10);
        $pdf->Cell(195,$h,'Pada hari ini telah dilaksanakan  '.$examType.', Tahun Akademik '.$data_arr['Semester'],0,1,'L');

        $w_sp1 = 10;
        $w_label = 40;
        $w_sp2 = 5;
        $w_fill = 140;

        $pdf->Ln(5);

        $h = 7;

        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Hari / Tanggal',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,date("l / d F Y", strtotime($dataDetailExam['ExamDate'])),0,1,'L');

        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Waktu Ujian',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,substr($dataDetailExam['ExamStart'],0,5).' - '.substr($dataDetailExam['ExamEnd'],0,5),0,1,'L');

        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Program Studi',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['Prodi'],0,1,'L');

        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Kelas (Group)',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['ClassGroup'],0,1,'L');

        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Kode Mata Kuliah',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['MKCode'],0,1,'L');

        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Mata Kuliah',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['Course'],0,1,'L');

        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Dosen',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataCourse['Lecturere'],0,1,'L');

        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Ruangan',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,$dataDetailExam['Room'],0,1,'L');


        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Jumlah Peserta hadir',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,'_ _ _ _ _ _ orang',0,1,'L');

        $pdf->Cell($w_sp1,$h,'',0,0,'L');
        $pdf->Cell($w_label,$h,'Jumlah Peserta tidak hadir',0,0,'L');
        $pdf->Cell($w_sp2,$h,':',0,0,'C');
        $pdf->Cell($w_fill,$h,'_ _ _ _ _ _ orang',0,1,'L');

        $pdf->Ln(5);
        $pdf->SetFont('Times','B',10);
        $pdf->SetFillColor(226, 226, 226);
        $pdf->Cell($w_sp1,$h,'',0,0,'C');
        $pdf->Cell(10,$h,'No',1,0,'C',true);
        $pdf->Cell(30,$h,'NIM',1,0,'C',true);
        $pdf->Cell(50,$h,'Nama',1,0,'C',true);
        $pdf->Cell(95,$h,'Keterangan',1,1,'C',true);

//        $pdf->SetFont('Times','',10);
        for($s=1;$s<=5;$s++){
            $pdf->Cell($w_sp1,$h,'',0,0,'C');
            $pdf->Cell(10,$h,$s,1,0,'C');
            $pdf->Cell(30,$h,'',1,0,'C');
            $pdf->Cell(50,$h,'',1,0,'C');
            $pdf->Cell(95,$h,'',1,1,'C');
        }

        $pdf->Ln(5);

        $pdf->Cell(195,$h,'Catatan khusus selama ujian berlangsung :',0,1,'L');
        for($s=1;$s<=4;$s++){
            $pdf->Cell(195,$h,'','B',1,'L');
        }

        $pdf->Ln(5);
        $pdf->SetFont('Times','',10);

        $pdf->Cell(195,$h,'Jakarta, '.date("d F Y", strtotime($dataDetailExam['ExamDate'])),0,1,'R');

        $pdf->Cell(97.5,$h,'Pengawas Ujian 1',0,0,'C');
        $pdf->Cell(97.5,$h,'Pengawas Ujian 2',0,1,'C');

        $pdf->Ln(15);

        $pdf->Cell(97.5,$h,'(_ _ _ _ _ _ _ _ _ _ _ _ _ _)',0,0,'C');
        $pdf->Cell(97.5,$h,'(_ _ _ _ _ _ _ _ _ _ _ _ _ _)',0,1,'C');

    }

    private function header_exam_attendance_students($pdf,$data_arr,$dataDetailExam,$dataCourse){

        $this->headerDefaultPotret($pdf,'FM-UAP/AKD-13-03A');
        $Semester = explode(' ',$data_arr['Semester']);

        $xam_t = ($data_arr['Type']=='uts' || $data_arr['Type']=='UTS') ? 'MID EXAM' : 'FINAL EXAM';
        $xam_h = (trim($Semester[1])=='Ganjil') ? 'ODD' : 'EVEN';

        $Antara = (isset($data_arr['IsSemesterSA']) && ($data_arr['IsSemesterSA']==1 || $data_arr['IsSemesterSA']=='1')) ? ' ANTARA' : '';

        $h = 5;
        $pdf->SetFont('Times','B',10);
        $pdf->Cell(195,$h,'ATTENDANCE '.$xam_t,0,1,'C');
        $pdf->Cell(195,$h,$xam_h.' SEMESTER ACADEMIC YEAR '.strtoupper(trim($Semester[0])).''.$Antara,0,1,'C');

        $pdf->Ln(5);
        $pdf->SetFont('Times','',10);
    }

    private function exam_attendance_students($pdf,$data_arr,$dataDetailExam,$dataCourse){

        $h = 5;
        $w_label_r = 22;
        $w_sp_r = 3;
        $w_fill_r = 95;

        $w_label_l = 23;
        $w_sp_l = 3;
        $w_fill_l = 50;


        $pdf->Cell($w_label_r,$h,'Code',0,0,'L');
        $pdf->Cell($w_sp_r,$h,':',0,0,'C');
        $pdf->Cell($w_fill_r,$h,$dataCourse['MKCode'],0,0,'L');

        $pdf->Cell($w_label_l,$h,'Study Program',0,0,'L');
        $pdf->Cell($w_sp_l,$h,':',0,0,'C');
        $pdf->Cell($w_fill_l,$h,$dataCourse['Prodi'],0,1,'L');

        $dCourse = (strlen($dataCourse['Course'])>=50) ? substr($dataCourse['Course'],0,50).'_' : $dataCourse['Course'];
        $pdf->Cell($w_label_r,$h,'Course',0,0,'L');
        $pdf->Cell($w_sp_r,$h,':',0,0,'C');
        $pdf->Cell($w_fill_r,$h,$dCourse,0,0,'L');

        $pdf->Cell($w_label_l,$h,'Day, Date',0,0,'L');
        $pdf->Cell($w_sp_l,$h,':',0,0,'C');
        $pdf->Cell($w_fill_l,$h,date("l, d M Y", strtotime($dataDetailExam['ExamDate'])),0,1,'L');




        $pdf->Cell($w_label_r,$h,'Class Group',0,0,'L');
        $pdf->Cell($w_sp_r,$h,':',0,0,'C');
        $pdf->Cell($w_fill_r,$h,$dataCourse['ClassGroup'],0,0,'L');

        $pdf->Cell($w_label_l,$h,'Time',0,0,'L');
        $pdf->Cell($w_sp_l,$h,':',0,0,'C');
        $pdf->Cell($w_fill_l,$h,substr($dataDetailExam['ExamStart'],0,5).' - '.substr($dataDetailExam['ExamEnd'],0,5),0,1,'L');

        $pdf->Cell($w_label_r,$h,'Lecturer',0,0,'L');
        $pdf->Cell($w_sp_r,$h,':',0,0,'C');
        $pdf->Cell($w_fill_r,$h,$dataCourse['Lecturere'],0,0,'L');

        $pdf->Cell($w_label_l,$h,'Room',0,0,'L');
        $pdf->Cell($w_sp_l,$h,':',0,0,'C');
        $pdf->Cell($w_fill_l,$h,$dataDetailExam['Room'],0,1,'L');

        $pdf->Ln(5);
        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Times','B',10);
        $h = 7;
        $pdf->Cell(10,$h,'No',1,0,'C',true);
        $pdf->Cell(25,$h,'NIM',1,0,'C',true);
        $pdf->Cell(110,$h,'Name',1,0,'C',true);
        $pdf->Cell(30,$h,'Sign',1,0,'C',true);
        $pdf->Cell(20,$h,'Score',1,1,'C',true);

        $pdf->SetFont('Times','',10);
//        $h = 5;

        if(count($dataCourse['DetailStudents'])){
            $no =1;
            for($t=0;$t<count($dataCourse['DetailStudents']);$t++){
                $d = $dataCourse['DetailStudents'][$t];
                $pdf->Cell(10,$h,$no,1,0,'C');
                $pdf->Cell(25,$h,$d['NPM'],1,0,'C');
                $pdf->Cell(110,$h,ucwords(strtolower($d['Name'])),1,0,'L');
                $pdf->Cell(30,$h,'',1,0,'C');
                $pdf->Cell(20,$h,'',1,1,'C');

                if($pdf->GetY()>=242){
                    $pdf->AddPage();
                    $this->header_exam_attendance_students($pdf,$data_arr,$dataDetailExam,$dataCourse);
                }

                $no++;
            }
        }

        // Kosong
//        for($k=1;$k<=2;$k++){
//            $pdf->Cell(10,$h,'',1,0,'C');
//            $pdf->Cell(25,$h,'',1,0,'C');
//            $pdf->Cell(110,$h,'',1,0,'L');
//            $pdf->Cell(30,$h,'',1,0,'C');
//            $pdf->Cell(20,$h,'',1,1,'C');
//        }

        $pdf->Ln(5);
        $pdf->Cell(150,$h,'',0,0,'C');
        $pdf->Cell(45,$h,'Sign by Lecturer',0,1,'C');

        $pdf->Ln(15);

        $pdf->Cell(150,$h,'',0,0,'C');
        $pdf->Cell(45,$h,'( _ _ _ _ _ _ _ _ _ )',0,1,'C');


    }


    // =============== PENGAWAS

    private function header_attendance_pengawas($pdf,$data_arr){
        $pdf->Image(base_url('images/icon/favicon.png'),15,5,15);

        $h = 5;

        $pdf->SetFont('Times','B',14);
        $pdf->Cell(282,$h,'Universitas Agung Podomoro',0,1,'C');

        $pdf->Ln(1);

        $pdf->SetFont('Times','',10);
        $pdf->Cell(282,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');
        $pdf->Cell(282,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');
        $pdf->Cell(282,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(10,25,292,25);

        $pdf->Ln(5);

        $pdf->SetFont('Times','B',12);
        $pdf->Cell(282,$h,date('l, d F Y',strtotime($data_arr['ExamDate'])),0,1,'C');
        $pdf->SetFont('Times','',10);

        $pdf->Ln(4);

        $h = 7;
        $pdf->SetFillColor(226, 226, 226);
        $pdf->Cell(25,$h,'Time','TLR',0,'C',true);
        $pdf->Cell(20,$h,'Room','TLR',0,'C',true);

        $pdf->Cell(70,$h,'Invigilator 1',1,0,'C',true);
        $pdf->Cell(70,$h,'Invigilator 2',1,0,'C',true);

        $pdf->Cell(22,$h,'Group','TLR',0,'C',true);
        $pdf->Cell(75,$h,'Course','TLR',1,'C',true);



        $pdf->Cell(25,$h,'','BLR',0,'C',true);
        $pdf->Cell(20,$h,'','BLR',0,'C',true);

        $pdf->Cell(40,$h,'Name',1,0,'C',true);
        $pdf->Cell(15,$h,'Taken',1,0,'C',true);
        $pdf->Cell(15,$h,'Returned',1,0,'C',true);
        $pdf->Cell(40,$h,'Name',1,0,'C',true);
        $pdf->Cell(15,$h,'Taken',1,0,'C',true);
        $pdf->Cell(15,$h,'Returned',1,0,'C',true);

        $pdf->Cell(22,$h,'','BLR',0,'C',true);
        $pdf->Cell(75,$h,'','BLR',1,'C',true);

    }

    private function header_attendance_pengawas_p($pdf,$data_arr){
        $pdf->Image(base_url('images/icon/favicon.png'),15,5,15);

        $h = 5;

        $pdf->SetFont('Times','B',12);
        $pdf->Cell(205,$h,'Universitas Agung Podomoro',0,1,'C');

        $pdf->Ln(1);

        $pdf->SetFont('Times','',10);
        $pdf->Cell(205,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');
        $pdf->Cell(205,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');
        $pdf->Cell(205,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(3,25,207,25);

        $pdf->Ln(5);

        $pdf->SetFont('Times','B',10);
        $pdf->Cell(198,$h,date('l, d F Y',strtotime($data_arr['ExamDate'])),0,1,'C');
        $pdf->SetFont('Times','B',7);

        $pdf->Ln(4);

        $h = 7;
        $pdf->SetFillColor(226, 226, 226);
        $pdf->Cell(15,$h,'Time','TLR',0,'C',true);
        $pdf->Cell(15,$h,'Room','TLR',0,'C',true);

        $pdf->Cell(49,$h,'Invigilator 1',1,0,'C',true);
        $pdf->Cell(49,$h,'Invigilator 2',1,0,'C',true);

        $pdf->Cell(15,$h,'Group','TLR',0,'C',true);
        $pdf->Cell(62,$h,'Course','TLR',1,'C',true);



        $pdf->Cell(15,$h,'','BLR',0,'C',true);
        $pdf->Cell(15,$h,'','BLR',0,'C',true);

        $pdf->Cell(25,$h,'Name',1,0,'C',true);
        $pdf->Cell(12,$h,'Taken',1,0,'C',true);
        $pdf->Cell(12,$h,'Returned',1,0,'C',true);

        $pdf->Cell(25,$h,'Name',1,0,'C',true);
        $pdf->Cell(12,$h,'Taken',1,0,'C',true);
        $pdf->Cell(12,$h,'Returned',1,0,'C',true);

        $pdf->Cell(15,$h,'','BLR',0,'C',true);
        $pdf->Cell(62,$h,'','BLR',1,'C',true);

    }

    // ==============

    private function attendance_pengawas($pdf,$data_arr,$dataDetailExam){

        $totalCourse = count($dataDetailExam['Course']);
        $h = 7;
        $h2 = $h * $totalCourse;
        for($i=0;$i<$totalCourse;$i++){


            $time = substr($dataDetailExam['ExamStart'],0,5).' - '.substr($dataDetailExam['ExamEnd'],0,5);

            $group = $dataDetailExam['Course'][$i]['ClassGroup'];
            $datacourse = $dataDetailExam['Course'][$i]['Course'];
            $course = (strlen($datacourse)>=45) ? substr($datacourse,0,45).'_' : $datacourse;

            $data_p1 = $dataDetailExam['Name_P1'];
            $data_p2 = $dataDetailExam['Name_P2'];

            $Name_P1 = (strlen($data_p1)>=20) ? substr($data_p1,0,20).'_' : $data_p1;
            $Name_P2 = (strlen($data_p2)>=20) ? substr($data_p2,0,20).'_' : $data_p2;


            if($i==0){


                $pdf->Cell(25,$h2,$time,1,0,'C');
                $pdf->Cell(20,$h2,$dataDetailExam['Room'],1,0,'C');
                $pdf->Cell(40,$h2,$Name_P1,1,0,'L');
                $pdf->Cell(15,$h2,'',1,0,'C');
                $pdf->Cell(15,$h2,'',1,0,'C');
                $pdf->Cell(40,$h2,$Name_P2,1,0,'L');
                $pdf->Cell(15,$h2,'',1,0,'C');
                $pdf->Cell(15,$h2,'',1,0,'C');

            } else {
                $pdf->Cell(185,$h2,'',0,0,'C');
            }


            $pdf->Cell(22,$h,$group,1,0,'C');
            $pdf->Cell(75,$h,$course,1,1,'L');

        }

    }
    private function attendance_pengawas_p($pdf,$data_arr,$dataDetailExam){

        $pdf->SetFont('Times','',7);

        $totalCourse = count($dataDetailExam['Course']);
        $h = 10;
        $h2 = $h * $totalCourse;
        for($i=0;$i<$totalCourse;$i++){


            $time = substr($dataDetailExam['ExamStart'],0,5).' - '.substr($dataDetailExam['ExamEnd'],0,5);

            $group = $dataDetailExam['Course'][$i]['ClassGroup'];
            $datacourse = $dataDetailExam['Course'][$i]['Course'];
            $course = (strlen($datacourse)>=45) ? substr($datacourse,0,45).'_' : $datacourse;

            $data_p1 = $dataDetailExam['Name_P1'];
            $data_p2 = $dataDetailExam['Name_P2'];

            $Name_P1 = (strlen($data_p1)>=20) ? substr($data_p1,0,20).'_' : $data_p1;
            $Name_P2 = (strlen($data_p2)>=20) ? substr($data_p2,0,20).'_' : $data_p2;


            if($i==0){

                $ex_time = explode(' ',$time);
                $pdf->Cell(15,$h2,$time,1,0,'C');
                $pdf->Cell(15,$h2,$dataDetailExam['Room'],1,0,'C');
                $pdf->Cell(25,$h2,$Name_P1,1,0,'L');
                $pdf->Cell(12,$h2,'',1,0,'C');
                $pdf->Cell(12,$h2,'',1,0,'C');
                $pdf->Cell(25,$h2,$Name_P2,1,0,'L');
                $pdf->Cell(12,$h2,'',1,0,'C');
                $pdf->Cell(12,$h2,'',1,0,'C');

            } else {
                $pdf->Cell(128,$h2,'',0,0,'C');
            }


            $pdf->Cell(15,$h,$group,1,0,'C');
            $pdf->Cell(62,$h,$course,1,1,'L');

        }

    }

    private function exam_template_map($pdf,$data_arr,$dataDetailExam,$dataDetailExam_2){

        $pdf->SetMargins(5,5,0);
        $pdf->AddPage();

        $totalGroup = count($dataDetailExam['Course']);
        $totalGroup_2 = ($dataDetailExam_2!='')? count($dataDetailExam_2['Course']) : 1 ;
        $h_fx = ($totalGroup>=$totalGroup_2) ? $totalGroup : $totalGroup_2;
        $h_border = ($h_fx * 8) + 100;
        $pdf->Rect(3, 3, 140, $h_border);
        $pdf->Rect(155, 3, 140, $h_border);

        $pdf->Image(base_url('images/icon/favicon.png'),5,6,12);
        $pdf->Image(base_url('images/icon/favicon.png'),158,6,12);

        $h=6;

        $pdf->SetFont('Times','B',13);
        $pdf->Cell(133,$h,'Universitas Agung Podomoro',0,0,'C');
        $pdf->Cell(20,$h,'',0,0);
        $pdf->Cell(132,$h,'Universitas Agung Podomoro',0,1,'C');

        $h=3.9;

        $pdf->SetFont('Times','',9);
        $pdf->Cell(135,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');

        $pdf->Cell(135,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');

        $pdf->Cell(135,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(3,25,143,25);
        $pdf->Line(155,25,295,25);

        $h=3.9;

        $pdf->Ln(5);
        $pdf->SetFont('Times','B',10);

        $xam_t = ($data_arr['Type']=='uts' || $data_arr['Type']=='UTS') ? 'MID EXAM' : 'FINAL EXAM';
        $xam_h = ($dataDetailExam['CodeSemester']=='1' || $dataDetailExam['CodeSemester']==1
        || $dataDetailExam['CodeSemester']=='3' || $dataDetailExam['CodeSemester']==3) ? 'ODD' : 'EVEN';

        $pdf->Cell(135,$h,$xam_t,0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,$xam_t,0,1,'C');

        $pdf->Cell(135,$h,'EVEN SEMESTER',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'EVEN SEMESTER',0,1,'C');

        $Semester = explode(' ',$data_arr['Semester']);
        $Antara = (isset($data_arr['IsSemesterSA']) && ($data_arr['IsSemesterSA']==1 || $data_arr['IsSemesterSA']=='1')) ? ' ANTARA' : '';
        $pdf->Cell(135,$h,$xam_h.' ACADEMIC YEAR '.strtoupper(trim($Semester[0])).''.$Antara,0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,$xam_h.' ACADEMIC YEAR '.strtoupper(trim($Semester[0])).''.$Antara,0,1,'C');

        //++++++++++++++++++++++++++++++++++++++++++++
        $pdf->Ln(5);
        $pdf->SetFont('Times','',10);
        $h=5;

        $w_label_left_1 = 25;
        $w_sp_left_1 = 7;
        $w_fill_left_1 = 45;

        $w_label_left_2 = 25;
        $w_sp_left_2 = 7;
        $w_fill_left_2 = 35;

        $w_space = 8;

        $w_label_right_1 = 25;
        $w_sp_right_1 = 7;
        $w_fill_right_1 = 45;

        $w_label_right_2 = 25;
        $w_sp_right_2 = 7;
        $w_fill_right_2 = 35;

        //======================================================

        $pdf->Cell($w_label_left_1,$h,'Exam',0,0,'L');
        $pdf->Cell($w_sp_left_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_1,$h,$xam_t." (".strtoupper($data_arr['Type']).")",0,0,'L');

        $pdf->Cell($w_label_left_2,$h,'',0,0,'L');
        $pdf->Cell($w_sp_left_2,$h,'',0,0,'C');
        $pdf->Cell($w_fill_left_2,$h,'',0,0,'L');

        $pdf->Cell($w_space,$h,'',0,0);


        $pdf->Cell($w_label_right_1,$h,'Exam',0,0,'L');
        $pdf->Cell($w_sp_right_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_1,$h,$xam_t." (".strtoupper($data_arr['Type']).")",0,0,'L');

        $pdf->Cell($w_label_right_2,$h,'',0,0,'L');
        $pdf->Cell($w_sp_right_2,$h,'',0,0,'C');
        $pdf->Cell($w_fill_right_2,$h,'',0,1,'L');

        //======================================================

        $pdf->Cell($w_label_left_1,$h,'Day, Date',0,0,'L');
        $pdf->Cell($w_sp_left_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_1,$h,date('l, d M Y',strtotime($data_arr['ExamDate'])),0,0,'L');

        $pdf->Cell($w_label_left_2,$h,'Exam Type',0,0,'L');
        $pdf->Cell($w_sp_left_2,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_2,$h,'Open / Close',0,0,'L');

        $pdf->Cell($w_space,$h,'',0,0);


        $pdf->Cell($w_label_right_1,$h,'Day, Date',0,0,'L');
        $pdf->Cell($w_sp_right_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_1,$h,date('l, d M Y',strtotime($data_arr['ExamDate'])),0,0,'L');

        $pdf->Cell($w_label_right_2,$h,'Exam Type',0,0,'L');
        $pdf->Cell($w_sp_right_2,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_2,$h,'Open / Close',0,1,'L');

        //======================================================

        $time_1 = substr($dataDetailExam['ExamStart'],0,5).' - '.substr($dataDetailExam['ExamEnd'],0,5);
        $time_2 = ($dataDetailExam_2!='') ? substr($dataDetailExam_2['ExamStart'],0,5).' - '.substr($dataDetailExam_2['ExamEnd'],0,5) : '-';

        $pdf->Cell($w_label_left_1,$h,'Time',0,0,'L');
        $pdf->Cell($w_sp_left_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_1,$h,$time_1,0,0,'L');

        $pdf->Cell($w_label_left_2,$h,'Calculator',0,0,'L');
        $pdf->Cell($w_sp_left_2,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_2,$h,'Yes / No',0,0,'L');

        $pdf->Cell($w_space,$h,'',0,0);


        $pdf->Cell($w_label_right_1,$h,'Time',0,0,'L');
        $pdf->Cell($w_sp_right_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_1,$h,$time_2,0,0,'L');

        $pdf->Cell($w_label_right_2,$h,'Calculator',0,0,'L');
        $pdf->Cell($w_sp_right_2,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_2,$h,'Yes / No',0,1,'L');

        //======================================================

        $room_1 = $dataDetailExam['Room'];
        $room_2 = ($dataDetailExam_2!='') ? $dataDetailExam_2['Room'] : '-';

        $pdf->Cell($w_label_left_1,$h,'Room',0,0,'L');
        $pdf->Cell($w_sp_left_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_1,$h,$room_1,0,0,'L');

        $pdf->Cell($w_label_left_2,$h,'Dictionary',0,0,'L');
        $pdf->Cell($w_sp_left_2,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_2,$h,'Yes / No',0,0,'L');

        $pdf->Cell($w_space,$h,'',0,0);


        $pdf->Cell($w_label_right_1,$h,'Room',0,0,'L');
        $pdf->Cell($w_sp_right_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_1,$h,$room_2,0,0,'L');

        $pdf->Cell($w_label_right_2,$h,'Dictionary',0,0,'L');
        $pdf->Cell($w_sp_right_2,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_2,$h,'Yes / No',0,1,'L');

        //======================================================
        $pdf->Ln(5);

        $invigilator_1_1 = $dataDetailExam['Name_P1'];
        $invigilator_1_2 = ($dataDetailExam_2!='') ? $dataDetailExam_2['Name_P1'] : '-' ;

        $pdf->Cell($w_label_left_1,$h,'Invigilator 1',0,0,'L');
        $pdf->Cell($w_sp_left_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_1+$w_label_left_2+$w_sp_left_2+$w_fill_left_2,$h,$invigilator_1_1,0,0,'L');

        $pdf->Cell($w_space,$h,'',0,0);

        $pdf->Cell($w_label_right_1,$h,'Invigilator 1',0,0,'L');
        $pdf->Cell($w_sp_right_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_1+$w_label_right_2+$w_sp_right_2+$w_fill_right_2,$h,$invigilator_1_2,0,1,'L');

        //======================================================

        $invigilator_2_1 = $dataDetailExam['Name_P2'];
        $invigilator_2_2 = ($dataDetailExam_2!='') ? $dataDetailExam_2['Name_P2'] : '-' ;

        $pdf->Cell($w_label_left_1,$h,'Invigilator 2',0,0,'L');
        $pdf->Cell($w_sp_left_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_1+$w_label_left_2+$w_sp_left_2+$w_fill_left_2,$h,$invigilator_2_1,0,0,'L');

        $pdf->Cell($w_space,$h,'',0,0);

        $pdf->Cell($w_label_right_1,$h,'Invigilator 2',0,0,'L');
        $pdf->Cell($w_sp_right_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_1+$w_label_right_2+$w_sp_right_2+$w_fill_right_2,$h,$invigilator_2_2,0,1,'L');

        //======================================================

        $pdf->Ln(3);
        $pdf->Cell($w_label_left_1,$h,'Total Exam',0,0,'L');
        $pdf->Cell($w_sp_left_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_left_1+$w_label_left_2+$w_sp_left_2+$w_fill_left_2,$h,' _ _ _ _ _ _ ',0,0,'L');

        $pdf->Cell($w_space,$h,'',0,0);

        $pdf->Cell($w_label_right_1,$h,'Total Exam',0,0,'L');
        $pdf->Cell($w_sp_right_1,$h,':',0,0,'C');
        $pdf->Cell($w_fill_right_1+$w_label_right_2+$w_sp_right_2+$w_fill_right_2,$h,' _ _ _ _ _ _ ',0,1,'L');

        //======================================

        $pdf->Ln(4);

        $pdf->SetFont('Times','B',9);
        $h = 6;
        $pdf->setFillColor(255,255,102);

        $pdf->Cell(20,$h,'Group',1,0,'C',true);
        $pdf->Cell(20,$h,'Code',1,0,'C',true);
        $pdf->Cell(88,$h,'Course',1,0,'C',true);
        $pdf->Cell(7,$h,'Std',1,0,'C',true);


        $pdf->Cell(17,$h,'',0,0);

        $pdf->Cell(20,$h,'Group',1,0,'C',true);
        $pdf->Cell(20,$h,'Code',1,0,'C',true);
        $pdf->Cell(88,$h,'Course',1,0,'C',true);
        $pdf->Cell(7,$h,'Std',1,1,'C',true);


        // --------------

        $pdf->SetFont('Times','',8);
        $h = 4;
        for($c=0;$c<$h_fx;$c++){
            $course1 = (isset($dataDetailExam['Course'][$c])) ? $dataDetailExam['Course'][$c] : '' ;
            $course2 = (isset($dataDetailExam_2['Course'][$c])) ? $dataDetailExam_2['Course'][$c] : '' ;

            $border_1 = ($course1!='') ? : 0;
            $border_2 = ($course2!='') ? : 0;

            $group_1 = ($course1!='') ? $course1['ClassGroup'] :'';
            $group_2 = ($course2!='') ? $course2['ClassGroup'] :'';

            $code_1 = ($course1!='') ? $course1['MKCode'] :'';
            $code_2 = ($course2!='') ? $course2['MKCode'] :'';

            $c_name_1 = ($course1!='') ? $course1['Course'] :'';
            $c_name_2 = ($course2!='') ? $course2['Course'] :'';

            $std_1 = ($course1!='') ? count($course1['DetailStudents']) :'';
            $std_2 = ($course2!='') ? count($course2['DetailStudents']) :'';

            $pdf->Cell(20,$h,$group_1,$border_1,0,'C');
            $pdf->Cell(20,$h,$code_1,$border_1,0,'C');
            $pdf->Cell(88,$h,$c_name_1,$border_1,0,'L');
            $pdf->Cell(7,$h,$std_1,$border_1,0,'C');


            $pdf->Cell(17,$h,'',0,0);

            $pdf->Cell(20,$h,$group_2,$border_2,0,'C');
            $pdf->Cell(20,$h,$code_2,$border_2,0,'C');
            $pdf->Cell(88,$h,$c_name_2,$border_2,0,'L');
            $pdf->Cell(7,$h,$std_2,$border_2,1,'C');

        }


        $pdf->Ln(5);

        $pdf->SetFont('Times','I',7);
        $pdf->Cell(135,$h,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' Podomoro University',0,0,'R');

        $pdf->Cell(17,$h,'',0,0);

        $pdf->Cell(135,$h,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' Podomoro University',0,1,'R');


    }



    //===========================

    private function header_exam_layout($pdf,$dataExam,$dataCourse){


        $pdf->Image(base_url('images/icon/logo-l.png'),10,10,30);

        $pdf->SetFont('Arial','B',10);

        $exam = ($dataExam['Type']=='uts' || $dataExam['Type']=='UTS') ? 'MID EXAM '.strtoupper($dataExam['Semester'])
            : 'FINAL EXAM '.strtoupper($dataExam['Semester']) ;

        $pdf->Cell(45,9,'',0,0,'C');
        $pdf->Cell(230,9,'SEATING MAP '.$exam,1,1,'C');

        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(0,5,'Page : '.$pdf->PageNo().' of {nb}',0,1,'R');

        $pdf->SetFont('Arial','',10);

        $dataCourseName = $dataCourse['CourseEng'];
        $course = (strlen($dataCourseName)>=55) ? substr($dataCourseName,0,55).'_' : $dataCourseName;

        $space_header = 5;
        $pdf->Cell(45,$space_header,'',0,0);
        $pdf->Cell(20,$space_header,'Course',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(122,$space_header, $dataCourse['MKCode'].' - '.$course,0,0);
        $pdf->Cell(20,$space_header,'Date',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(62,$space_header,date('l, d F, Y',strtotime($dataExam['ExamDate'])),0,1);

        $pdf->Cell(45,$space_header,'',0,0);
        $pdf->Cell(20,$space_header,'Pengawas 1',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(122,$space_header,$dataExam['Name_P1'],0,0);
        $pdf->Cell(20,$space_header,'Time',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(62,$space_header,substr($dataExam['ExamStart'],0,5).' - '.substr($dataExam['ExamEnd'],0,5),0,1);

        $pdf->Cell(45,$space_header,'',0,0);
        $pdf->Cell(20,$space_header,'Pengawas 2',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(122,$space_header,$dataExam['Name_P2'],0,0);
        $pdf->Cell(20,$space_header,'Room',0,0);
        $pdf->Cell(3,$space_header,':',0,0,'C');
        $pdf->Cell(62,$space_header,$dataExam['Room'],0,1);

        $pdf->Cell(275,7,'',0,1);
        $pdf->Cell(275,0.3,'',1,1);

        // Lecturer
        if($dataExam['LectureDesk']=='left'){
            $pdf->Image(base_url('images/icon/lecturerdesk.png'),25,50,30);
        } else {
            $pdf->Image(base_url('images/icon/lecturerdesk.png'),250,50,30);
        }


        $pdf->SetFillColor(226, 226, 226);
        $pdf->Cell(275,7,'',0,1);
        $pdf->Cell(55,5,'',0,0);
        $pdf->Cell(170,7,'Board',1,1,'C',true);

        $pdf->Cell(275,15,'',0,1);

        $pdf->AliasNbPages();



        $base_x = 10;
        $base_y = 75;
        $koor_x = $base_x;
        $koor_y = $base_y;
        $no=1;

        $jml_deret = $dataExam['DeretForExam']; // Dinamis
        $total_w = 275;
        $space = 2;
        $width = ($total_w - (($jml_deret - 1) * $space)) / $jml_deret;

        $height_panel = 7;
        $pdf->SetFont('Arial','',10);
        $pdf->setFillColor(255,255,102);

        // Total Students
        $array_stf = $dataCourse['DetailStudent'];
        $totalStudent = count($array_stf);

        for($m=0;$m<$totalStudent;$m++){
            $d_Std = $array_stf[$m];

            // NIM
            $pdf->SetXY($koor_x,$koor_y);
            $pdf->Cell($width,$height_panel,$d_Std['NPM'],1,0,'C',true);

            // Name
            $pdf->SetXY($koor_x,($koor_y+7));
            $exName = explode(' ',$d_Std['Name']);
            $nm = (count($exName)>=3) ? trim($exName[0]).' '.trim($exName[1]) : $d_Std['Name'];
            $exp_name = explode(" ",$nm);
            $name_fil = (count($exp_name)>3) ? $exp_name[0].' '.$exp_name[1] : $nm;
            $name = (strlen($name_fil)<=15) ? $name_fil : $exp_name[0];
            $pdf->Cell($width,7,$name,1,0,'C');

            $koor_x = $koor_x + $space;
            if($no%$jml_deret==0){
                $koor_x = $base_x;
                $koor_y = $koor_y + (($height_panel * 2) + 5);

                if($pdf->GetY()>170){
                    // membuat halaman baru
//                    $this->header_exam_layout($pdf,$dataExam,$dataCourse);
                    $pdf->SetFont('Arial','',10);
                    $pdf->setFillColor(255,255,102);
                    $koor_x = 10;
                    $koor_y = 10;
                    $pdf->AddPage();

                }
            } else {
                $koor_x = $koor_x+$width;
            }

            $no++;
        }

    }

    public function exam_layout($TypeSemester,$ExamID){

        $data = $this->m_save_to_pdf->getExamByID($TypeSemester,$ExamID);

//        print_r($data);exit;

        if(count($data)>0){

            $pdf = new FPDF('l','mm','A4');


            $dataExam = $data[0];

            for($i=0;$i<count($dataExam['Course']);$i++){
                $dataCourse = $dataExam['Course'][$i];


                // membuat halaman baru
                $pdf->AddPage();
                $this->header_exam_layout($pdf,$dataExam,$dataCourse);
            }

            //        $pdf->Output('Study_Card.pdf','D');
            $pdf->Output('layout.pdf','I');

        } else {

        }


    }

    private function header_exam($pdf){

        $pdf->Image(base_url('images/icon/logo-l.png'),10,10,25);

        $pdf->SetFont('Arial','B',12);

        $pdf->Cell(35,11,'',0,0,'C');
        $pdf->Cell(150,5,'UNIVERSITAS AGUNG PODOMORO',0,1,'C');

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(35,5,'',0,0,'C');
        $pdf->Cell(150,5,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');
        $pdf->Cell(35,5,'',0,0,'C');
        $pdf->Cell(150,5,'Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');
        $pdf->Cell(35,5,'',0,0,'C');
        $pdf->Cell(150,5,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Cell(185,7,'',0,1);
        $pdf->Cell(185,0.3,'',1,1);

    }

    // Naskah Soal dan Lembar Jawaban
    public function draft_questions_answer_sheet(){
        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);

        $pdf = new FPDF('l','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();

        $pdf->SetMargins(5,5,0);

        $totalGroup = count($data_arr['Course']);
        $h_border = ($totalGroup * 8) + 100;
        $pdf->Rect(3, 7, 140, $h_border);
        $pdf->Rect(155, 7, 140, $h_border);

        $pdf->Image(base_url('images/icon/favicon.png'),5,10,13);
        $pdf->Image(base_url('images/icon/favicon.png'),158,10,13);

        $h=5;

        $pdf->SetFont('Times','B',11);
        $pdf->Cell(133,$h,'Universitas Agung Podomoro',0,0,'C');
        $pdf->Cell(20,$h,'',0,0);
        $pdf->Cell(132,$h,'Universitas Agung Podomoro',0,1,'C');

        $h=3.5;

        $pdf->SetFont('Times','',8);
        $pdf->Cell(135,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');

        $pdf->Cell(135,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');

        $pdf->Cell(135,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(3,27,143,27);
        $pdf->Line(155,27,295,27);

        $pdf->Ln(4);

        $pdf->SetFont('Times','B',8);

        $pdf->Cell(135,$h,'DRAFT QUESTIONS',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'ANSWER SHEET',0,1,'C');

        $xam_t = ($data_arr['Exam']=='uts' || $data_arr['Exam']=='UTS') ? 'MID EXAM' : 'FINAL EXAM';

        $pdf->Cell(135,$h,'ATTENDANCE '.$xam_t,0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'ATTENDANCE '.$xam_t,0,1,'C');

        $pdf->Cell(135,$h,'EVEN SEMESTER',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'EVEN SEMESTER',0,1,'C');

        $pdf->Cell(135,$h,'ACADEMIC YEAR '.$data_arr['Semester'],0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'ACADEMIC YEAR '.$data_arr['Semester'],0,1,'C');

        $pdf->Ln(5);

        //++++++++++++++++++++++++++++++++++++++++++++

        $pdf->SetFont('Times','',9);
        $h=5;

        $pdf->Cell(25,$h,'Exam',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(105,$h,$data_arr['Exam'],0,0,'L');

        $pdf->Cell(20,$h,'',0,0);


        $pdf->Cell(40,$h,'Exam',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(90,$h,$data_arr['Exam'],0,1,'L');

        //======================================


        $pdf->Cell(25,$h,'Day, Date ',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(105,$h,$data_arr['Date'],0,0,'L');

        $pdf->Cell(20,$h,'',0,0);

        $pdf->Cell(40,$h,'Day, Date ',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(90,$h,$data_arr['Date'],0,1,'L');

        //======================================

        $pdf->Cell(25,$h,'Time, Room',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(105,$h,$data_arr['Time'].', '.$data_arr['Room'],0,0,'L');

        $pdf->Cell(20,$h,'',0,0);

        $pdf->Cell(40,$h,'Time, Room',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(90,$h,$data_arr['Time'].', '.$data_arr['Room'],0,1,'L');


        //======================================

        $pdf->Cell(25,$h,'Total Script',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(105,$h,'_ _ _ _ _ _ _ _ _ _ _ _',0,0,'L');

        $pdf->Cell(20,$h,'',0,0);

        $pdf->Cell(40,$h,'Number of Answer Sheet',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(90,$h,'_ _ _ _ _ _ _ _ _ _ _ _',0,1,'L');


        //=============================================

        $pdf->Ln(6);
//        $pdf->Rect(10, 75, 135, 23);
//        $pdf->Rect(156, 75, 135, 23);

        //======================================

        $pdf->Cell(25,$h,'Pengawas 1',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(105,$h,$data_arr['Pengawas_1'],0,0,'L');

        $pdf->Cell(20,$h,'',0,0);

        $pdf->Cell(40,$h,'Pengawas 1',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(90,$h,$data_arr['Pengawas_1'],0,1,'L');

        //======================================

        $pdf->Cell(25,$h,'Pengawas 2',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(105,$h,$data_arr['Pengawas_2'],0,0,'L');

        $pdf->Cell(20,$h,'',0,0);

        $pdf->Cell(40,$h,'Pengawas 2',0,0,'L');
        $pdf->Cell(2,$h,':',0,0,'C');
        $pdf->Cell(90,$h,$data_arr['Pengawas_2'],0,1,'L');



        //======================================

        $pdf->Ln(7);

        $pdf->SetFont('Times','B',9);
        $h = 6;
        $pdf->setFillColor(255,255,102);

        $pdf->Cell(20,$h,'Group',1,0,'C',true);
        $pdf->Cell(108,$h,'Course',1,0,'C',true);
        $pdf->Cell(7,$h,'Std',1,0,'C',true);


        $pdf->Cell(17,$h,'',0,0);

        $pdf->Cell(20,$h,'Group',1,0,'C',true);
        $pdf->Cell(108,$h,'Course',1,0,'C',true);
        $pdf->Cell(7,$h,'Std',1,1,'C',true);


        // --------------

        $pdf->SetFont('Times','',8);
        $h = 4;

        for($i=0;$i<$totalGroup;$i++){

            $c = (array) $data_arr['Course'][$i];

            $courseName = (strlen($c['CourseEng'])>=65) ? substr($c['CourseEng'],0,65).'_' : $c['CourseEng'];

            $pdf->Cell(20,$h,$c['ClassGroup'],'LRT',0,'C');
            $pdf->SetFont('Times','B',8);
//            $pdf->Cell(108,$h,$c['MKCode'].' - ','LRT',0,'L');
            $pdf->Cell(108,$h,$c['MKCode'].' - '.$courseName,'LRT',0,'L');
            $pdf->SetFont('Times','',8);
            $pdf->Cell(7,$h,count($c['DetailsStudent']),'LRT',0,'C');


            $pdf->Cell(17,$h,'',0,0);

            $pdf->Cell(20,$h,$c['ClassGroup'],'LRT',0,'C');
            $pdf->SetFont('Times','B',8);
            $pdf->Cell(108,$h,$c['MKCode'].' - '.$courseName,'LRT',0,'L');
            $pdf->SetFont('Times','',8);
            $pdf->Cell(7,$h,count($c['DetailsStudent']),'LRT',1,'C');


            // *******


            $pdf->Cell(20,$h,'','LRB',0,'C');
            $pdf->Cell(108,$h,'(Co) '.$c['Coordinator'],'LRB',0,'L');
            $pdf->Cell(7,$h,'','LRB',0,'C');

            $pdf->Cell(17,$h,'',0,0);

            $pdf->Cell(20,$h,'','LRB',0,'C');
            $pdf->Cell(108,$h,'(Co) '.$c['Coordinator'],'LRB',0,'L');
            $pdf->Cell(7,$h,'','LRB',1,'C');
        }



        $pdf->Ln(5);

        $pdf->SetFont('Times','I',7);
        $pdf->Cell(135,$h,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' Podomoro University',0,0,'R');

        $pdf->Cell(17,$h,'',0,0);

        $pdf->Cell(135,$h,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' Podomoro University',0,1,'R');


        $pdf->Output('draft_questions_answer_sheet.pdf','I');
    }

    // Daftar Hadir
    public function attendance_list(){

        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);

//        print_r($data_arr);
//        exit;



        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru


        $dataExam = (array) $data_arr['Exam'];
        $totalCourse = count($data_arr['Course']);

        $width_attd = 9;

        for($c=0;$c<$totalCourse;$c++){
            $pdf->AddPage();
            $this->header_exam($pdf);
            $dataCourse = (array) $data_arr['Course'][$c];
            $this->header_attendance($pdf,$dataExam,$dataCourse);

            $this->header_attd_table($pdf);
            // Data Student
            $totalStd = count($dataCourse['DetailsStudent']);
            $no = 1;
            for($t=0;$t<$totalStd;$t++){
                $st_detail = (array) $dataCourse['DetailsStudent'][$t];

                $pdf->Cell(10,$width_attd,$no++,1,0,'C');
                $pdf->Cell(25,$width_attd,$st_detail['NPM'],1,0,'C');
                $pdf->Cell(95,$width_attd,' '.ucwords(strtolower($st_detail['Name'])),1,0,'L');
                $pdf->Cell(35,$width_attd,'',1,0,'C');
                $pdf->Cell(20,$width_attd,'',1,1,'C');

                if($pdf->GetY()>=265){
                    $pdf->AddPage();
                    $this->header_attd_table($pdf);
                }
            }


            $pdf->SetFont('Arial','I',9);
            $pdf->Cell(185,5,'',0,1,'');
            $pdf->Cell(130,3,'',0,0,'R');
            $pdf->Cell(55,3,'Sign by Lecturer : ',0,1,'C');

            $pdf->Cell(185,17,'',0,1,'');

            $pdf->Cell(130,3,'',0,0,'R');
            $pdf->Cell(55,3,'( ...................... )',0,1,'C');


            $pdf->Cell(185,7,'',0,1,'');
            $pdf->SetFont('Times','I',7);
            $pdf->Cell(185,3,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' Podomoro University',0,1,'R');


        }

        $pdf->Output('attendance_exam_list.pdf','I');

    }

    // Berita Acara dan Bukti Serah Terima berkas
    public function news_event2(){
        $pdf = new FPDF('l','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();

        $pdf->SetMargins(5,5,0);

        $totalGroup = 3;
        $h_border = ($totalGroup * 8) + 100;
        $pdf->Rect(3, 7, 140, $h_border);
        $pdf->Rect(155, 7, 140, $h_border);

        $pdf->Image(base_url('images/icon/favicon.png'),5,10,13);
        $pdf->Image(base_url('images/icon/favicon.png'),158,10,13);

        $h=5;

        $pdf->SetFont('Times','B',11);
        $pdf->Cell(133,$h,'Universitas Agung Podomoro',0,0,'C');
        $pdf->Cell(20,$h,'',0,0);
        $pdf->Cell(132,$h,'Universitas Agung Podomoro',0,1,'C');

        $h=3.5;

        $pdf->SetFont('Times','',8);
        $pdf->Cell(135,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'APL Tower Lt. 5, Podomoro City Jln. LetJend. S. Parman Kav. 28',0,1,'C');

        $pdf->Cell(135,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'Tlp: 021 292 00456 Fax: 021 292 00455',0,1,'C');

        $pdf->Cell(135,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'website : www.podomorouniversity.ac.id, email : admissions@podomorouniversity.ac.id',0,1,'C');

        $pdf->Line(3,27,143,27);
        $pdf->Line(155,27,295,27);

        $pdf->Ln(4);

        $pdf->SetFont('Times','B',8);

        $pdf->Cell(135,$h,'DRAFT QUESTIONS',0,0,'C');
        $pdf->Cell(17,$h,'',0,0);
        $pdf->Cell(135,$h,'BERITA ACARA PENYERAHAN BERKAS UJIAN',0,1,'C');

        $pdf->Output('news_event.pdf','I');
    }


    // Berita Acara
    public function news_event1(){

        $pdf = new FPDF('l','mm','A5');
        // membuat halaman baru
        $pdf->AddPage();

        $pdf->Rect(10, 7, 35, 25);
        $pdf->Rect(45, 7, 115, 25);
        $pdf->Rect(160, 7, 35, 25);

        $pdf->Image(base_url('images/icon/logo-l.png'),15,8,25);

        $pdf->SetFont('Arial','B',16);
        $pdf->Cell(35,11,'',0,0,'C');
        $pdf->Cell(115,6,'FINAL EXAMINATION',0,0,'C');
        $pdf->Cell(35,6,'',0,1,'C');

        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(35,11,'',0,0,'C');
        $pdf->Cell(115,6,'EVEN SEMESTER',0,0,'C');
        $pdf->Cell(35,6,'',0,1,'C');

        $pdf->Cell(35,11,'',0,0,'C');
        $pdf->Cell(115,6,'ACADEMIC YEAR 2017/2018',0,0,'C');
        $pdf->Cell(35,6,'',0,1,'C');

        $pdf->Cell(185,7,'',0,1);
//        $pdf->Cell(185,0.3,'',1,1);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(185,7,'',0,1);

        $pdf->Cell(20,7,'Code',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'HOT0014',0,0);

        $pdf->Cell(25,7,'Study Program',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'HBP',0,1);

        // ---

        $pdf->Cell(20,7,'Subject',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'Food and Beverage - Service Theory',0,0);

        $pdf->Cell(25,7,'Group',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'HBP8A',0,1);

        // ---

        $pdf->Cell(20,7,'Day, date',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'Monday, 25 June 2018',0,0);

        $pdf->Cell(25,7,'Exam Type',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'Open / Close',0,1);

        // ---

        $pdf->Cell(20,7,'Time',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'08:00 - 09:40',0,0);

        $pdf->Cell(25,7,'Calculator',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'Yes / No',0,1);

        // ---

        $pdf->Cell(20,7,'Room',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(90,7,'504',0,0);

        $pdf->Cell(25,7,'Dictionary',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(40,7,'Yes / No',0,1);


        // ------------
        $pdf->Rect(10, 85, 185, 30);
        $pdf->Cell(185,10,'',0,1);
        $pdf->SetFont('Arial','B',12);
        $pdf->Cell(185,10,'Note test execution',0,1,'C');

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(5,7,'',0,0);
        $pdf->Cell(45,7,'Number of Participants',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(130,7,'_____________',0,1);

        $pdf->Cell(5,7,'',0,0);
        $pdf->Cell(45,7,'The number in attendance',0,0);
        $pdf->Cell(5,7,':',0,0,'C');
        $pdf->Cell(130,7,'_____________',0,1);

        $pdf->SetFont('Arial','I',9);
        $pdf->Cell(185,13,'Note : *) Strikethrough the unnecessary items',0,1,'L');


        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(185,3,'Download On : '.date("d M Y H:i:s").' | '.chr(169).' IT Podomoro University',0,1,'R');

//        $pdf->Output('Study_Card.pdf','D');
        $pdf->Output();

    }

    private function header_attendance($pdf,$dataExam,$dataCourse){

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(185,7,'',0,1);

        $xam_t = ($dataExam['Exam']=='uts' || $dataExam['Exam']=='UTS') ? 'MID EXAM' : 'FINAL EXAM';

        $pdf->Cell(185,5,'ATTENDANCE '.$xam_t,0,1,'C');
        $pdf->Cell(185,5,'EVEN SEMESTER ACADEMIC YEAR '.$dataExam['Semester'],0,1,'C');

        $pdf->Cell(185,7,'',0,1,'');
        $pdf->SetFont('Arial','',9);
//        $pdf->Cell(25,5,'Code',0,0);
//        $pdf->Cell(3,5,':',0,0,'C');
//        $pdf->Cell(81,5,$dataCourse['MKCode'],0,0);
//
//        $pdf->Cell(25,5,'Study Program',0,0);
//        $pdf->Cell(3,5,':',0,0,'C');
//        $pdf->Cell(50,5,'HBP',0,1);

        // ---

        $cName = (strlen($dataCourse['CourseEng'])>=40)
            ? substr($dataCourse['CourseEng'],0,40).'_'
            : $dataCourse['CourseEng'];

        $pdf->Cell(25,5,'Code | Course',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,$dataCourse['MKCode'].' - '.$cName,0,0);

        $pdf->Cell(25,5,'Day, date',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,$dataExam['Date'],10,1);

        // ---

        $pdf->Cell(25,5,'Group',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'HBP8A',0,0);

        $pdf->Cell(25,5,'Time',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,$dataExam['Time'],0,1);

        // ---

        $pdf->Cell(25,5,'Lecturer/s',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,$dataCourse['Coordinator'],0,0);

        $pdf->Cell(25,5,'Room',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,$dataExam['Room'],0,1);
    }

    private function header_attd_table($pdf){
        $pdf->Cell(185,3,'',0,1,'');

        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(185,7,'Page : '.$pdf->PageNo().' of {nb}',0,1,'R');
        $pdf->AliasNbPages();

        $width_attd = 9;
        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(10,$width_attd,'No',1,0,'C',true);
        $pdf->Cell(25,$width_attd,'NPM',1,0,'C',true);
        $pdf->Cell(95,$width_attd,'Name',1,0,'C',true);
        $pdf->Cell(35,$width_attd,'Sign',1,0,'C',true);
        $pdf->Cell(20,$width_attd,'Score',1,1,'C',true);
        $pdf->SetFont('Arial','',10);
    }




    // ========= Score ==========

    private function header_report_exam($pdf,$exam){
        $pdf->SetFont('Arial','I',7);
        $pdf->Cell(0,7,'Page : '.$pdf->PageNo().' of {nb}',0,0,'R');
        $pdf->AliasNbPages();

        $pdf->SetFont('Arial','B',10);
        $pdf->Cell(185,7,'',0,1);
        $pdf->Cell(185,5,'Food and Beverage - Service Theory',0,1,'C');
        $pdf->Cell(185,5,'HOT0014',0,1,'C');
//        $pdf->Cell(185,5,'EVEN SEMESTER ACADEMIC YEAR 2017/2018',0,1,'C');

        $pdf->Cell(185,7,'',0,1,'');
        $pdf->SetFont('Arial','',9);
        $pdf->Cell(25,5,'Code',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'HOT0014',0,0);

        $pdf->Cell(25,5,'Study Program',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'HBP',0,1);

        // ---

        $pdf->Cell(25,5,'Course',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'Food and Beverage - Service Theory',0,0);

        $pdf->Cell(25,5,'Day, date',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'Monday, 25 Desember 2018',10,1);

        // ---

        $pdf->Cell(25,5,'Code',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'HOT0014',0,0);

        $pdf->Cell(25,5,'Study Program',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'HBP',0,1);

        // ---

        $pdf->Cell(25,5,'Group',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'HBP8A',0,0);

        $pdf->Cell(25,5,'Time',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'08:00 - 09:40',0,1);

        // ---

        $pdf->Cell(25,5,'Lecturer/s',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(81,5,'Michael Yadisaputra',0,0);

        $pdf->Cell(25,5,'Room',0,0);
        $pdf->Cell(3,5,':',0,0,'C');
        $pdf->Cell(50,5,'504',0,1);
    }

    // UTS
    public function report_uts(){

        $pdf = new FPDF('p','mm','A4');
        // membuat halaman baru
        $pdf->AddPage();
        $this->header_exam($pdf);

        $this->header_report_exam($pdf,'UTS');


//        $pdf->Output('Study_Card.pdf','D');
        $pdf->Output();

    }

    public function getpdfkwitansi($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        // print_r($data_arr);

        $pdf = new FPDF('l','mm','A5');
        // membuat halaman baru
        $pdf->AddPage();

        $pdf->SetMargins(10,10,10,10);
        $pdf->SetAutoPageBreak(true, 0);

        // Logo
        $pdf->Image('./images/new_logo_pu.png',5,5,50);

        $pdf->SetFont('Arial','',17);
        $pdf->Cell(200, 0, 'Tanda Terima Pembayaran Mahasiswa', 0, 1, 'C', 0);

        $pdf->SetFont('Arial','',15);
        $pdf->Cell(200, 15, 'Tahun Akademik', 0, 1, 'C', 0);

        $pdf->SetFont('Arial','',15);
        $pdf->Cell(200, 0, $data_arr['semester'], 0, 1, 'C', 0);

        $pdf->Line(10, 30, 200, 30);

        $pdf->Cell(200, 7, '', 0, 1, 'C', 0);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(100, 5, '', 0, 0, 'C');
        $this->load->model('master/m_master');
        $date = date('Y-m-d');
        $date = $this->m_master->getIndoBulan($date);
        $pdf->Cell(90, 5, 'Tanggal Cetak : '.$date, 0, 1, 'R');

        $pdf->Cell(35, 7, 'Nama', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['nama'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'NPM', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['npm'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Tipe Pembayaran', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['ptid'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Program Studi', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['prodi'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Virtual Account', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['va'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'BilingID', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['bilingid'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Invoice', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['invoice'], 0, 1, 'L');

        $pdf->Cell(35, 7, 'Waktu Pembayaran', 0, 0, 'L');
        $pdf->Cell(5, 7, ':', 0, 0, 'L');
        $pdf->Cell(100, 7, $data_arr['Time'], 0, 1, 'L');

        $pdf->Cell(200, 7, '', 0, 1, 'C', 0);
        $pdf->Cell(190, 7, 'Note : ', 0, 1, 'L');
        $pdf->Cell(190, 7, 'Tanda terima ini merupakan bukti pembayaran yang diterbitkan oleh Podomoro University.', 0, 1, 'L');
        $pdf->Cell(190, 7, 'Jika terdapat kekeliruan harap hubungi bagian Admisi dibawah ini.', 0, 1, 'L');

        $pdf->Line(10, 135, 200, 135);
        $setY = 135;
        $pdf->SetFont('Arial','',6);
        $pdf->SetXY(40,$setY);
        $pdf->SetTextColor(0,0,0);
        $pdf->Cell(190, 5, 'Admission Office :  Central Park Mall, Lantai 3, Unit 112, Podomoro City, JL Letjen S. Parman Kav.28, Jakarta Barat 11470', 0, 1, 'L', 0);

        $setY = 138;
        $pdf->SetFont('Arial','',6);
        $pdf->SetXY(43,$setY);
        $pdf->SetTextColor(0,0,0);
        // $this->mypdf->SetFillColor(0,0,0);
        $pdf->Cell(190, 5, 'Telp : (021) 292 00 456    Email : admission@podomorouniversity.ac.id   Website : www.podomorouniversity.ac.id', 0, 1, 'L', 0);

        $pdf->AliasNbPages();

        $pdf->Output();
    }


    // ===== Rekap Exam Schedule =====
    public function recapExamSchedule(){

        $pdf = new FPDF('P','mm','A4');
        $pdf->AddPage();
        $this->headerDefaultPotret($pdf,'FM-UAP/AKD-13-02');

        $h = 6;
        $pdf->SetFont('Times','B',10);
        $pdf->Cell(195,$h,'Schedule Mid Exam Odd Semester Academic Year 2018/2019',0,1,'C');

        $pdf->Ln(5);
        // 195
        $pdf->SetFillColor(226, 226, 226);
        $pdf->SetFont('Times','B',8);
        $pdf->Cell(7,$h,'No',1,0,'C',true);
        $pdf->Cell(30,$h,'Date, Time',1,0,'C',true);
        $pdf->Cell(50,$h,'Course',1,0,'C',true);
        $pdf->Cell(30,$h,'Lecturer',1,0,'C',true);
        $pdf->Cell(15,$h,'Group',1,0,'C',true);
        $pdf->Cell(10,$h,'Std',1,0,'C',true);
        $pdf->Cell(18,$h,'Room',1,0,'C',true);
        $pdf->Cell(35,$h,'Invigilator',1,1,'C',true);


        $pdf->SetFont('Times','',8);
        $h = 4;

        $pdf->Cell(7,$h,'1000','LRT',0,'C');
        $pdf->Cell(30,$h,'Monday, 08 Oct 2018','LRT',0,'C');
        $pdf->Cell(50,$h,'Course','LRT',0,'L');
        $pdf->Cell(30,$h,'Lecturer','LRT',0,'L');
        $pdf->Cell(15,$h,'Group','LRT',0,'C');
        $pdf->Cell(10,$h,'Std','LRT',0,'C');
        $pdf->Cell(18,$h,'Room','LRT',0,'C');
        $pdf->Cell(35,$h,'Invigilator','LRT',1,'L');

        $pdf->Cell(7,$h,'','LRB',0,'C');
        $pdf->Cell(30,$h,'08:00 - 09:10 | 100 mnt','LRB',0,'C');
        $pdf->Cell(50,$h,'Course','LRB',0,'L');
        $pdf->Cell(30,$h,'Lecturer','LRB',0,'L');
        $pdf->Cell(15,$h,'Group','LRB',0,'C');
        $pdf->Cell(10,$h,'Std','LRB',0,'C');
        $pdf->Cell(18,$h,'Room','LRB',0,'C');
        $pdf->Cell(35,$h,'Invigilator','LRB',1,'L');


        $pdf->Output('document_recap_exam_schedule.pdf','I');
    }

    // ==== Penutup Rekap Exam Schedule =====


    // ====== Transcript Semestera =======

    public function temp_transcript(){

        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);

        $datawarek1 = $this->db->get_where('db_employees.employees em',
        "(em.PositionMain = '2.2' OR em.PositionOther1 = '2.2' OR em.PositionOther2 = '2.2' OR em.PositionOther3 = '2.2') AND  em.StatusEmployeeID = '1'");

        $dataStudent = $this->m_save_to_pdf->getTranscript($data_arr['DBStudent'],$data_arr['NPM']);
        $dataStudent['DetailCourse'] = $this->m_rest->getTranscript(substr($data_arr['DBStudent'],3,4),$data_arr['NPM'],'ASC');

        $Student = $dataStudent['Student'][0];
        $dataTempTr = $dataStudent['TempTranscript'][0];

        $pdf = new FPDF('P','mm','A4');

        // membuat halaman baru
        $pdf->SetMargins(10,5,10);
        $pdf->AddPage();

        $this->header_temp_transcript($pdf,$dataTempTr);

        $h=3.5;

        $tr = 'TRANSKRIP SEMENTARA';

        $pdf->Cell(190,$h,$tr,0,1,'C');

        $pdf->SetFont('dinprolight','',7);
        $pdf->Cell(190,$h,'No. : '.$dataTempTr['No'],0,1,'C');

        $border = 0;

        $l_left = 38;
        $sp_left = 1;
        $fill_left = 75;

        $l_right = 22;
        $sp_right = 1;
        $fill_right = 53;

        $h=3;
        $pdf->SetFont('dinpromedium','',7);
        $pdf->Ln(1);

        $pdf->Cell($l_left,$h,'Nama',$border,0,'L');
        $pdf->Cell($sp_left,$h,':',$border,0,'C');
        $pdf->Cell($fill_left,$h,($Student['Name']),$border,0,'L');
        $pdf->Cell($l_right,$h,'Fakultas',$border,0,'L');
        $pdf->Cell($sp_right,$h,':',$border,0,'C');
        $pdf->Cell($fill_right,$h,$Student['FacultyName'],$border,1,'L');

        $pdf->Cell($l_left,$h,'NIM',$border,0,'L');
        $pdf->Cell($sp_left,$h,':',$border,0,'C');
        $pdf->Cell($fill_left,$h,$Student['NPM'],$border,0,'L');
        $pdf->Cell($l_right,$h,'Program Studi',$border,0,'L');
        $pdf->Cell($sp_right,$h,':',$border,0,'C');
        $pdf->Cell($fill_right,$h,ucwords(strtolower($Student['Prodi'])),$border,1,'L');

        $pdf->Cell($l_left,$h,'Tempat dan Tanggal Lahir',$border,0,'L');
        $pdf->Cell($sp_left,$h,':',$border,0,'C');
        $pdf->Cell($fill_left,$h,ucwords(strtolower($Student['PlaceOfBirth'])).', '.$this->getDateIndonesian($Student['DateOfBirth']),$border,1,'L');

        $pdf->Ln(2);

        $this->headerTable('ind',$pdf);

//        print_r($dataStudent);exit;

        $this->body_temp_transcript('ind',$pdf,$dataStudent,$dataTempTr);


        // +++++++ ENGLISH ++++++++

        // membuat halaman baru
        $pdf->SetMargins(10,5,10);
        $pdf->AddPage();

        $this->header_temp_transcript($pdf,$dataTempTr);

        $h=2.5;

        $tr = 'RECORD OF ACADEMIC ACHIEVEMENT';

        $pdf->Cell(190,$h,$tr,0,1,'C');

        $pdf->SetFont('dinprolight','',7);
        $pdf->Cell(190,$h,'No. : '.$dataTempTr['No'],0,1,'C');

        $border = 0;


        $h=3;
        $pdf->SetFont('dinpromedium','',7);
        $pdf->Ln(1);

        $pdf->Cell($l_left,$h,'Name',$border,0,'L');
        $pdf->Cell($sp_left,$h,':',$border,0,'C');
        $pdf->Cell($fill_left,$h,($Student['Name']),$border,0,'L');
        $pdf->Cell($l_right,$h,'Faculty',$border,0,'L');
        $pdf->Cell($sp_right,$h,':',$border,0,'C');
        $pdf->Cell($fill_right,$h,$Student['FacultyName'],$border,1,'L');


        $pdf->Cell($l_left,$h,'Student ID',$border,0,'L');
        $pdf->Cell($sp_left,$h,':',$border,0,'C');
        $pdf->Cell($fill_left,$h,$Student['NPM'],$border,0,'L');
        $pdf->Cell($l_right,$h,'Department',$border,0,'L');
        $pdf->Cell($sp_right,$h,':',$border,0,'C');
        $pdf->Cell($fill_right,$h,ucwords(strtolower($Student['ProdiEng'])),$border,1,'L');

        $pdf->Cell($l_left,$h,'Place, Date of Birth',$border,0,'L');
        $pdf->Cell($sp_left,$h,':',$border,0,'C');
        $pdf->Cell($fill_left,$h,ucwords(strtolower($Student['PlaceOfBirth'])).', '.date('F j, Y',strtotime($Student['DateOfBirth'])),$border,1,'L');

        $pdf->Ln(1.5);

        $this->headerTable('eng',$pdf);
        $this->body_temp_transcript('eng',$pdf,$dataStudent,$dataTempTr);




        $nameF = str_replace(' ','_',($Student['Name']));
        $pdf->Output('TEMP_TRNSCPT_'.$Student['NPM'].'_'.$nameF.'.pdf','I');
    }

    private function headerTable($lang,$pdf){
        $border = 1;

        $w_no = 8;
        $w_smt = 8;
        $w_kode = 20;
        $w_mk = 103;
        $w_f = 11;
        $w_fv = 18;
        $h=4.3;
        $pdf->SetFillColor(226, 226, 226);

        if($lang=='ind') {
            $pdf->Cell($w_no,$h,'No.',$border,0,'C',true);
            $pdf->Cell($w_kode,$h,'Kode',$border,0,'C',true);
            $pdf->Cell($w_mk+$w_smt,$h,'Mata Kuliah',$border,0,'C',true);
            $pdf->Cell($w_f,$h,'SKS',$border,0,'C',true);
            $pdf->Cell($w_f,$h,'Nilai',$border,0,'C',true);
            $pdf->Cell($w_f,$h,'Bobot',$border,0,'C',true);
            $pdf->Cell($w_fv,$h,'SKS X Bobot',$border,1,'C',true);
        } else {
            $pdf->Cell($w_no,$h,'No.',$border,0,'C',true);
            $pdf->Cell($w_kode,$h,'Code',$border,0,'C',true);
            $pdf->Cell($w_mk+$w_smt,$h,'Course',$border,0,'C',true);
            $pdf->Cell($w_f,$h,'Credit',$border,0,'C',true);
            $pdf->Cell($w_f,$h,'Grade',$border,0,'C',true);
            $pdf->Cell($w_f,$h,'Score',$border,0,'C',true);
            $pdf->Cell($w_fv,$h,'Point',$border,1,'C',true);
        }



        $pdf->SetFont('dinprolight','',7);
    }

    private function header_temp_transcript($pdf,$dataTempTr){
        $pdf->SetFont('dinpromedium','',6);

        $pdf->Image(base_url('images/logo.png'),10,3,35);

        $pdf->SetXY(100,3);
        $pdf->Cell(100,5,$dataTempTr['NoForm'],0,1,'R');

    }

    private function body_temp_transcript($lang,$pdf,$dataStudent,$dataTempTr){



        $mk = ($lang=='ind')? '' : 'Eng';

        $Student = $dataStudent['Student'][0];
        $Transcript = $dataStudent['Transcript'][0];
        $DetailCourse = $dataStudent['DetailCourse']['dataCourse'];
        $Result = $dataStudent['DetailCourse']['dataIPK'];
        $Warek1 = $dataStudent['WaRek1'][0];

        $border = 1;
        $w_no = 8;
        $w_smt = 8;
        $w_kode = 20;
        $w_mk = 103;
        $w_f = 11;
        $w_fv = 18;
        $h=5;

        $no = 1;
        for($i=0;$i<count($DetailCourse);$i++){
            $d = $DetailCourse[$i];

            $y = $pdf->GetY();
            $pdf->Cell($w_no,$h,($no++),$border,0,'C');
//            $pdf->Cell($w_smt,$h,'1',$border,0,'C');
            $pdf->Cell($w_kode,$h,$d['MKCode'],$border,0,'C');
            $pdf->Cell($w_mk+$w_smt,$h,$d['Course'.$mk],$border,0,'L');
            $pdf->Cell($w_f,$h,$d['Credit'],$border,0,'C');
            $pdf->Cell($w_f,$h,$d['Grade'],$border,0,'C');
//            $pdf->Cell($w_f,$h, (is_int($d['GradeValue'])) ? $d['GradeValue'] : number_format($d['GradeValue'],2),$border,0,'C');
//            $pdf->Cell($w_fv,$h, (is_int($d['Point'])) ? $d['Point'] : number_format($d['Point'],2),$border,1,'C');
            $pdf->Cell($w_f,$h, number_format($d['GradeValue'],2),$border,0,'C');
            $pdf->Cell($w_fv,$h, number_format($d['Point'],2),$border,1,'C');
//            $y = $pdf->GetY();

//            if($pdf->GetY()>287){
            if($y>231.7){
                // membuat halaman baru
                $pdf->SetMargins(10,5,10);
                $pdf->AddPage();
                $this->header_temp_transcript($pdf,$dataTempTr);
                $pdf->Ln(15);
                $this->headerTable($lang,$pdf);
            }

        }

        $pdf->SetFont('dinpromedium','',8);
        $pdf->Cell($w_smt+$w_no+$w_kode+$w_mk,$h,'Total',$border,0,'R',true);
        $pdf->Cell($w_f,$h,$Result['TotalSKS'],$border,0,'C',true);
        $pdf->Cell($w_f,$h,'-',$border,0,'C',true);
        $pdf->Cell($w_f,$h,'-',$border,0,'C',true);
        $pdf->Cell($w_fv,$h,$Result['TotalPoint'],$border,1,'C',true);

        $ipkLabel = ($lang=='ind')? 'Indeks Prestasi Kumulatif' : 'Cummulative Grade Point Average';
        $h = 6;

        $pdf->Cell($w_smt+$w_no+$w_kode+$w_mk+(3*$w_f)+$w_fv,$h,$ipkLabel.' : '.$Result['IPK'],$border,1,'C',true);


        $h = 3.5;
        $pdf->Ln(3.5);
        $border = 0;
        $pdf->SetFont('dinprolight','',8);
        $pdf->Cell($w_smt+$w_no+$w_kode+$w_mk,$h,'',$border,0,'R');

        $dateT = ($lang=='ind') ? $this->getDateIndonesian($dataTempTr['Date']) : date('F j, Y',strtotime($dataTempTr['Date']));
        $pdf->Cell((3*$w_f)+$w_fv,$h,ucwords(strtolower($dataTempTr['Place'])).', '.$dateT,$border,1,'L');


        $ttdb = ($lang=='ind')? 'Wakil Rektor I Bidang Akademik' : 'Vice Rector of Academic Affairs';
        $pdf->Cell($w_smt+$w_no+$w_kode+$w_mk,$h,'',$border,0,'R');
        $pdf->Cell((3*$w_f)+$w_fv,$h,$ttdb,$border,1,'L');

        $pdf->Ln(15);

        $nm = $Warek1['TitleAhead'].' '.ucwords(strtolower($Warek1['Name'])).', '.$Warek1['TitleBehind'];
        $pdf->Cell($w_smt+$w_no+$w_kode+$w_mk,$h,'',$border,0,'R');
        $pdf->Cell((3*$w_f)+$w_fv,$h,trim($nm),$border,1,'L');
    }

    // ====== Transcript =======

    public function transcript(){

        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);

        $dataStudent = $this->m_save_to_pdf->getTranscript($data_arr['DBStudent'],$data_arr['NPM']);



        $dataStudent['DetailCourse'] = $this->m_rest->getTranscript(substr($data_arr['DBStudent'],3,4),$data_arr['NPM'],'ASC');


        $dataTranscript = $this->db->get('db_academic.setting_transcript')->result_array();

        $Student = $dataStudent['Student'][0];
        $Transcript = $dataStudent['Transcript'][0];

        $pdf = new FPDF('P','mm','legal');
        //$pdf = new FPDF('P','mm',array(215,330));  // novie

        $pdf->AddFont('dinproExpBold','','dinproExpBold.php');

        // membuat halaman baru
        $margin_left = 15.315;
        $pdf->SetMargins($margin_left,40.5,10);
        $pdf->AddPage();

        $pdf->SetFont('dinpromedium','',7);
        $pdf->SetXY(10,3);
        $pdf->Cell(115,7,'',0,0,'L');
        $pdf->Cell(27,7,'Nomor Ijazah Nasional / ',0,0,'L');
        $pdf->SetFont('dinlightitalic','',7);
        $pdf->Cell(25,7,'National Certificate Number',0,0,'L');
        $pdf->SetFont('dinpromedium','',7);
        $pdf->Cell(15,7,' : ',0,0,'C');
        $pdf->Cell(15,7,$Student['CNN'],0,1,'R');

        $pdf->SetFont('dinpromedium','',7);
        $pdf->SetXY(10,7);
        $pdf->Cell(120,7,'',0,0,'L');
        $pdf->Cell(32,7,'Nomor Transkrip Akademik / ',0,0,'L');
        $pdf->SetFont('dinlightitalic','',7);
        $pdf->Cell(20,7,' Transcript Number',0,0,'L');
        $pdf->SetFont('dinpromedium','',7);
        $pdf->Cell(5,7,' : ',0,0,'C');
        $pdf->Cell(20,7,$Student['CSN'],0,1,'R');

        $pdf->SetXY($margin_left,40); // novie

        $label_l = 35;
        $sparator_l = 1;
        $fill_l = 61;

        $label_r = 38;
        $sparator_r = 1;
        $fill_r = 55;
        $h=3.3;
        $ln = 1;
        $border = 0;

        $f_title = 8;
        $f_title_i = 7;
        $pdf->SetFont('dinpromedium','',$f_title);
        $pdf->Cell($label_l,$h,'Nama',$border,0,'L');
        $pdf->Cell($sparator_l,$h,':',$border,0,'C');
        $pdf->Cell($fill_l,$h,($Student['Name']),$border,0,'L');
        $pdf->Cell($label_r,$h,'Program Pendidikan',$border,0,'L');
        $pdf->Cell($sparator_r,$h,':',$border,0,'C');
        $pdf->Cell($fill_r,$h,$Student['GradeDesc'],$border,1,'L');

        $pdf->SetFont('dinlightitalic','',$f_title_i);
        $pdf->Cell($label_l,$h,'Name',$border,0,'L');
        $pdf->Cell($sparator_l,$h,'',$border,0,'C');
        $pdf->Cell($fill_l,$h,'',$border,0,'L');
        $pdf->Cell($label_r,$h,'Educational Program',$border,0,'L');
        $pdf->Cell($sparator_r,$h,':',$border,0,'C');
        $pdf->Cell($fill_r,$h,$Student['GradeDescEng'],$border,1,'L');

        $pdf->Ln($ln);

        $pdf->SetFont('dinpromedium','',$f_title);
        $pdf->Cell($label_l,$h,'Tempat dan Tanggal Lahir',$border,0,'L');
        $pdf->Cell($sparator_l,$h,':',$border,0,'C');
        $pdf->Cell($fill_l,$h,ucwords(strtolower($Student['PlaceOfBirth'])).', '.$this->getDateIndonesian($Student['DateOfBirth']),$border,0,'L');
        $pdf->Cell($label_r,$h,'Program Studi',$border,0,'L');
        $pdf->Cell($sparator_r,$h,':',$border,0,'C');
        $pdf->Cell($fill_r,$h,$Student['Prodi'],$border,1,'L');

        $pdf->SetFont('dinlightitalic','',$f_title_i);
        $pdf->Cell($label_l,$h,'Place and Date of Birth',$border,0,'L');
        $pdf->Cell($sparator_l,$h,':',$border,0,'C');
        $pdf->Cell($fill_l,$h,ucwords(strtolower($Student['PlaceOfBirth'])).', '.date('F j, Y',strtotime($Student['DateOfBirth'])),$border,0,'L');
        $pdf->Cell($label_r,$h,'Study Program',$border,0,'L');
        $pdf->Cell($sparator_r,$h,':',$border,0,'C');
        $pdf->Cell($fill_r,$h,$Student['ProdiEng'],$border,1,'L');

        $pdf->Ln($ln);

        $pdf->SetFont('dinpromedium','',$f_title);
        $pdf->Cell($label_l,$h,'Nomor Induk Mahasiswa',$border,0,'L');
        $pdf->Cell($sparator_l,$h,':',$border,0,'C');
        $pdf->Cell($fill_l,$h,$Student['NPM'],$border,0,'L');
        $pdf->Cell($label_r,$h,'Tanggal Yudisium',$border,0,'L');
        $pdf->Cell($sparator_r,$h,':',$border,0,'C');
        $pdf->Cell($fill_r,$h,$this->getDateIndonesian($dataTranscript[0]['DateOfYudisium']),$border,1,'L');



        $pdf->SetFont('dinlightitalic','',$f_title_i);
        $pdf->Cell($label_l,$h,'Student Identification Number',$border,0,'L');
        $pdf->Cell($sparator_l,$h,'',$border,0,'C');
        $pdf->Cell($fill_l,$h,'',$border,0,'L');


        //$pdf->SetFont('dinpromedium','',$f_title);
        // $pdf->Cell($label_r,$h,'Perguruan Tinggi',$border,0,'L');
        //$pdf->Cell($sparator_r,$h,'',$border,0,'C');
        //$pdf->Cell($fill_r,$h,'',$border,1,'L');
        //$pdf->Cell($label_l+$sparator_l+$fill_l,$h,'',0,0,'L');

        $pdf->SetFont('dinlightitalic','',$f_title_i);
        $pdf->Cell($label_r,$h,'Date of Yudisium',$border,0,'L');
        $pdf->Cell($sparator_r,$h,':',$border,0,'C');
        //$pdf->Cell($fill_r,$h,$this->getDateIndonesian($dataTranscript[0]['DateOfYudisium']),$border,1,'L');
        $pdf->Cell($fill_r,$h,date('F j, Y',strtotime($dataTranscript[0]['DateOfYudisium'])),$border,1,'L');


        // Table
        $pdf->Ln(3);

        $w_no = 13;
        $w_course = 105;
        $w_credit = 15;
        $w_grade = 15;
        $w_score = 15;
        $w_point = 23;

        $font_medium = 8;
        $font_medium_i = 7;


        $border_fill = 'LR';

        $this->header_transcript_table($pdf);

        $DetailStudent = $dataStudent['DetailCourse']['dataCourse'];
        $no=1;
        for($i=0;$i<count($DetailStudent);$i++){

            $ds = $DetailStudent[$i];

            $this->spasi_transcript_table($pdf,'T');

            $h = 3;
            $pdf->SetFont('dinproExpBold','',$font_medium);
            $ytext = $pdf->GetY()+3.5;
            $x_ = ($i<9) ? 21 : 20;
            $pdf->Text($x_,$ytext,($no++));
            $pdf->Cell($w_no,$h,'',$border_fill,0,'C');
            $pdf->Cell($w_course,$h,$ds['Course'],$border_fill,0,'L');

            $ytext = $pdf->GetY()+3.5;
            $xtext = $pdf->GetX()+7;
            $pdf->Text($xtext,$ytext,$ds['Credit']);
            $pdf->Cell($w_credit,$h,'',$border_fill,0,'C');

            $ytext = $pdf->GetY()+3.5;
            $xtext = $pdf->GetX()+6.5;
            $pdf->Text($xtext,$ytext,$ds['Grade']);
            $pdf->Cell($w_grade,$h,'',$border_fill,0,'C');

            $ytext = $pdf->GetY()+3.5;
            $xtext = $pdf->GetX()+6.3;
            $pdf->Text($xtext,$ytext,str_replace(',','.',$ds['GradeValue']));
            $pdf->Cell($w_score,$h,'',$border_fill,0,'C');

            $ytext = $pdf->GetY()+3.5;
            $xtext = $pdf->GetX()+9.5;
            $pdf->Text($xtext,$ytext,str_replace(',','.',$ds['Point']));
            $pdf->Cell($w_point,$h,'',$border_fill,1,'C');

            $pdf->SetFont('dinlightitalic','',$font_medium_i);
            $pdf->Cell($w_no,$h,'',$border_fill,0,'C');
            $pdf->Cell($w_course,$h,$ds['CourseEng'],$border_fill,0,'L');
            $pdf->Cell($w_credit,$h,'',$border_fill,0,'C');
            $pdf->Cell($w_grade,$h,'',$border_fill,0,'C');
            $pdf->Cell($w_score,$h,'',$border_fill,0,'C');
            $pdf->Cell($w_point,$h,'',$border_fill,1,'C');

            $this->spasi_transcript_table($pdf,'B');

            if($pdf->GetY()>=320){ // novie
//                $pdf->SetMargins($margin_left,40.5,10);
                $pdf->SetMargins($margin_left,18,10);
                $pdf->AddPage();
//                $pdf->SetXY(10,43.5);
                $this->header_transcript_table($pdf);
            }
        }


        $dataIPK = $dataStudent['DetailCourse']['dataIPK'];

        $DataGraduation = $this->m_save_to_pdf->getGraduation(number_format($dataIPK['IPK'],2,'.',''));

        $this->spasi_transcript_table($pdf,'TR');
        $pdf->SetFont('dinproExpBold','',$font_medium);
        $pdf->Cell($w_course+$w_no,$h,'Jumlah',$border_fill,0,'R');
        $ytext = $pdf->GetY()+3.5;
        $xtext = $pdf->GetX()+5.5;
        $pdf->Text($xtext,$ytext,$dataIPK['TotalSKS']);
        $pdf->Cell($w_credit,$h,'',$border_fill,0,'C');

        $ytext = $pdf->GetY()+3.5;
        $xtext = $pdf->GetX()+7;
        $pdf->Text($xtext,$ytext,'-');
        $pdf->Cell($w_grade,$h,'',$border_fill,0,'C');
        $ytext = $pdf->GetY()+3.5;
        $xtext = $pdf->GetX()+7;
        $pdf->Text($xtext,$ytext,'-');
        $pdf->Cell($w_score,$h,'',$border_fill,0,'C');

        $ytext = $pdf->GetY()+3.5;
        $xtext = $pdf->GetX()+7.5;
        $pdf->Text($xtext,$ytext,$dataIPK['TotalPoint']);
        $pdf->Cell($w_point,$h,'',$border_fill,1,'C');

        $pdf->SetFont('dinlightitalic','',$font_medium_i);
        $pdf->Cell($w_course+$w_no,$h,'Total',$border_fill,0,'R');
        $pdf->Cell($w_credit,$h,'',$border_fill,0,'C');
        $pdf->Cell($w_grade,$h,'',$border_fill,0,'C');
        $pdf->Cell($w_score,$h,'',$border_fill,0,'C');
        $pdf->Cell($w_point,$h,'',$border_fill,1,'C');
        $this->spasi_transcript_table($pdf,'BR');

        $pdf->Ln(3);
        $totalW = $w_course+$w_no+$w_credit+$w_grade+$w_score+$w_point;
        $w_Div = $totalW/2;
        $w_R_label = 35.5;
        $w_R_sparator = 5;
        $w_R_fill = 52.5;

        $h = 1.5;
        $pdf->Cell($w_Div,$h,'','LRT',0,'L');
        $pdf->Cell($w_Div,$h,'','LRT',1,'L');

        //$IPKFinal = $Result['IPK'];
        $IPKFinal = $dataIPK['IPK'];
        // if(strlen($Result['IPK'])==2) {
        //     $IPKFinal = $Result['IPK'].'00';
        // } else if(strlen($Result['IPK'])==3){
        //     $IPKFinal = $Result['IPK'].'0';
        // }

        $h=3;
        $pdf->SetFont('dinpromedium','',$font_medium);
        $pdf->Cell($w_R_label,$h,' Indeks Prestasi Kumulatif','L',0,'L');
        $pdf->Cell($w_R_sparator,$h,':',0,0,'L');
        $pdf->Cell($w_R_fill,$h,$IPKFinal,'R',0,'L');
        $pdf->Cell($w_R_label,$h,' Predikat Kelulusan','L',0,'L');
        $pdf->Cell($w_R_sparator,$h,':',0,0,'C');
        $pdf->Cell($w_R_fill,$h,$DataGraduation[0]['Description'],'R',1,'L');

        $h=3;
        $pdf->SetFont('dinlightitalic','',$font_medium_i);
        $pdf->Cell($w_R_label,$h,' Grade Point Average','L',0,'L');
        $pdf->Cell($w_R_sparator,$h,'',0,0,'C');
        $pdf->Cell($w_R_fill,$h,'','R',0,'L');
        $pdf->Cell($w_R_label,$h,' Graduation Honor','L',0,'L');
        $pdf->Cell($w_R_sparator,$h,':',0,0,'C');
        $pdf->Cell($w_R_fill,$h,$DataGraduation[0]['DescriptionEng'],'R',1,'L');

        $h = 1.5;
        $pdf->Cell($w_Div,$h,'','LRB',0,'L');
        $pdf->Cell($w_Div,$h,'','LRB',1,'L');


        $pdf->SetFont('dinpromedium','',$font_medium);
        $h = 1.5;
        $pdf->Cell($totalW,$h,'','LRT',1,'L');
        $h=3;
        $SkripsiInd = ($Student['TitleInd']!='' && $Student['TitleInd']!=null) ? $Student['TitleInd'] : '-';
        $SkripsiEng = ($Student['TitleEng']!='' && $Student['TitleEng']!=null) ? $Student['TitleEng'] : '-';

        $yA = $pdf->GetY();

        $pdf->Cell($w_R_label,$h,'Judul Skripsi / ',0,0,'L');
        $pdf->Cell($w_R_sparator,$h,':',0,0,'C');
        $pdf->MultiCell($w_R_fill+$w_Div-2,$h,$SkripsiInd,0);

        $pdf->SetFont('dinlightitalic','',$font_medium_i);
//        $pdf->SetFont('dinprolight','',$font_medium_i);
        $pdf->Cell($w_R_label,$h,'Thesis Title',0,0,'L');
        $pdf->Cell($w_R_sparator,$h,':',0,0,'C');
        $pdf->MultiCell($w_R_fill+$w_Div-2,$h,$SkripsiEng,0);

        $yA2 = $pdf->GetY();
//        $pdf->SetLineWidth(0.2);
        $pdf->Line($margin_left, $yA, $margin_left, $yA2);
        $pdf->Line($margin_left+$w_R_label+$w_R_sparator+$w_R_fill+$w_Div, $yA, $margin_left+$w_R_label+$w_R_sparator+$w_R_fill+$w_Div, $yA2);
        $h = 1.5;
        $pdf->Cell($totalW,$h,'','LRB',1,'L');
        $h=3;
        $y = $pdf->GetY();
        $pdf->Ln(17);

        $min = 25;
        $borderttd = 0;

        if($Student['FacultyID']!=4 || $Student['FacultyID']!='4'){

            $pdf->SetFont('dinpromedium','',$font_medium);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'Tempat dan Tanggal Diterbitkan',$borderttd,1,'L');


            $pdf->SetFont('dinlightitalic','',$font_medium_i);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'Place and Date Issued',$borderttd,1,'L');

            $pdf->SetFont('dinpromedium','',$font_medium);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,ucwords(strtolower($Transcript['PlaceIssued'])).', '.$this->getDateIndonesian($Transcript['DateIssued']),$borderttd,1,'L');

            $pdf->SetFont('dinlightitalic','',$font_medium_i);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,ucwords(strtolower($Transcript['PlaceIssued'])).',  '.date('F j, Y',strtotime($Transcript['DateIssued'])),$borderttd,1,'L');

            $pdf->Ln(5);

            $pdf->SetFont('dinpromedium','',$font_medium);
            $pdf->Cell($w_Div+$min,$h,'Wakil Rektor I Bidang Akademik',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'Dekan',$borderttd,1,'L');

            $pdf->SetFont('dinlightitalic','',$font_medium_i);
            $pdf->Cell($w_Div+$min,$h,'Vice Rector of Academic Affairs',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'Dean',$borderttd,1,'L');

            $pdf->Ln(17);


            $titleA = ($Student['TitleAhead']!='') ? $Student['TitleAhead'].'' : '';
            $titleB = ($Student['TitleBehind']!='') ? $Student['TitleBehind'] : '' ;

            $Dekan = $titleA.''.$Student['Dekan'].','.$titleB;

            $Rektorat = $dataStudent['Rektorat'][0];
            $titleARektor = ($Rektorat['TitleAhead']!='')? $Rektorat['TitleAhead'].'' : '';
            $titleBRektor = ($Rektorat['TitleBehind']!='')? $Rektorat['TitleBehind'] : '';
            $Rektor = $titleARektor.''.$Rektorat['Name'].', '.$titleBRektor;

            // Foto
            $pdf->SetFont('dinpromedium','',$font_medium);
            $pdf->Cell($w_Div+$min,$h,$Rektor,$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,$Dekan,$borderttd,1,'L');

            $pdf->SetFont('dinpromedium','',$font_medium_i);
            $pdf->Cell($w_Div+$min,$h,'NIP : '.$Rektorat['NIP'],$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'NIP : '.$Student['NIP'],$borderttd,1,'L');

            // $pdf->Rect(85, $y+5, 40, 58);
            $pdf->Rect(85, $y+16, 40, 48);

        } else {

            $min = 5;
            $borderttd = 0;

            $pdf->SetFont('dinpromedium','',$font_medium);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'Tempat dan Tanggal Diterbitkan',$borderttd,1,'L');


            $pdf->SetFont('dinlightitalic','',$font_medium_i);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'Place and Date Issued',$borderttd,1,'L');

            $pdf->SetFont('dinpromedium','',$font_medium);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,ucwords(strtolower($Transcript['PlaceIssued'])).', '.$this->getDateIndonesian($Transcript['DateIssued']),$borderttd,1,'L');

            $pdf->SetFont('dinlightitalic','',$font_medium_i);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,ucwords(strtolower($Transcript['PlaceIssued'])).',  '.date('F j, Y',strtotime($Transcript['DateIssued'])),$borderttd,1,'L');

            $pdf->Ln(5);

            $pdf->SetFont('dinpromedium','',$font_medium);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'Wakil Rektor I Bidang Akademik',$borderttd,1,'L');

            $pdf->SetFont('dinlightitalic','',$font_medium_i);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'Vice Rector of Academic Affairs',$borderttd,1,'L');

            $pdf->Ln(17);

            $Rektorat = $dataStudent['Rektorat'][0];

            $titleARektor = ($Rektorat['TitleAhead']!='')? $Rektorat['TitleAhead'].'' : '';
            $titleBRektor = ($Rektorat['TitleBehind']!='')? $Rektorat['TitleBehind'] : '';
            $Rektor = $titleARektor.''.$Rektorat['Name'].', '.$titleBRektor;

            // Foto
            $pdf->SetFont('dinpromedium','',$font_medium);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,$Rektor,$borderttd,1,'L');

            $pdf->SetFont('dinpromedium','',$font_medium_i);
            $pdf->Cell($w_Div+$min,$h,'',$borderttd,0,'L');
            $pdf->Cell($w_Div-$min,$h,'NIP : '.$Rektorat['NIP'],$borderttd,1,'L');

            // $pdf->Rect(61, $y+5, 40, 58);
            $pdf->Rect(61, $y+16, 40, 48);

        }



        $nameF = str_replace(' ','_',($Student['Name']));
        $pdf->Output('TRNSCPT_'.$Student['NPM'].'_'.$nameF.'.pdf','I');
    }

    private function header_transcript_table($pdf){

        $w_no = 13;
        $w_course = 105;
        $w_credit = 15;
        $w_grade = 15;
        $w_score = 15;
        $w_point = 23;


        $border_fill = 'LR';

        $this->spasi_transcript_table($pdf,'T');


        $h = 3;
        $pdf->SetFont('dinpromedium','',9);


        $pdf->Cell($w_no,$h,'',$border_fill,0,'C');
        $pdf->Cell($w_course,$h,'Mata Kuliah',$border_fill,0,'C');
        $pdf->Cell($w_credit,$h,'SKS',$border_fill,0,'C');
        $pdf->Cell($w_grade,$h,'Nilai',$border_fill,0,'C');
        $pdf->Cell($w_score,$h,'Angka',$border_fill,0,'C');
        $pdf->Cell($w_point,$h,'SKS x Angka',$border_fill,1,'C');
        $ytext = $pdf->GetY()+0.5;
        $pdf->Text(19,$ytext,'No.');

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell($w_no,$h,'',$border_fill,0,'C');
        $pdf->Cell($w_course,$h,'Course',$border_fill,0,'C');
        $pdf->Cell($w_credit,$h,'Credit',$border_fill,0,'C');
        $pdf->Cell($w_grade,$h,'Grade',$border_fill,0,'C');
        $pdf->Cell($w_score,$h,'Score',$border_fill,0,'C');
        $pdf->Cell($w_point,$h,'Point',$border_fill,1,'C');

        $this->spasi_transcript_table($pdf,'B');
    }

    private function spasi_transcript_table($pdf,$desc){

        $w_no = 13;
        $w_course = 105;
        $w_credit = 15;
        $w_grade = 15;
        $w_score = 15;
        $w_point = 23;

        $border_fill_t = 'LRT';
        $border_fill_b = 'LRB';

        if(strtolower($desc)=='t'){
            $h = 0.6;

            $pdf->Cell($w_no,$h,'',$border_fill_t,0,'C');
            $pdf->Cell($w_course,$h,'',$border_fill_t,0,'C');
            $pdf->Cell($w_credit,$h,'',$border_fill_t,0,'C');
            $pdf->Cell($w_grade,$h,'',$border_fill_t,0,'C');
            $pdf->Cell($w_score,$h,'',$border_fill_t,0,'C');
            $pdf->Cell($w_point,$h,'',$border_fill_t,1,'C');
        } else if(strtolower($desc)=='b') {
            $h = 0.6;

            $pdf->Cell($w_no,$h,'',$border_fill_b,0,'C');
            $pdf->Cell($w_course,$h,'',$border_fill_b,0,'C');
            $pdf->Cell($w_credit,$h,'',$border_fill_b,0,'C');
            $pdf->Cell($w_grade,$h,'',$border_fill_b,0,'C');
            $pdf->Cell($w_score,$h,'',$border_fill_b,0,'C');
            $pdf->Cell($w_point,$h,'',$border_fill_b,1,'C');
        } else if(strtolower($desc)=='tr'){
            $h = 1.3;

            $pdf->Cell($w_course+$w_no,$h,'',$border_fill_t,0,'C');
            $pdf->Cell($w_credit,$h,'',$border_fill_t,0,'C');
            $pdf->Cell($w_grade,$h,'',$border_fill_t,0,'C');
            $pdf->Cell($w_score,$h,'',$border_fill_t,0,'C');
            $pdf->Cell($w_point,$h,'',$border_fill_t,1,'C');
        } else if(strtolower($desc)=='br') {
            $h = 1.3;

            $pdf->Cell($w_course+$w_no,$h,'',$border_fill_b,0,'C');
            $pdf->Cell($w_credit,$h,'',$border_fill_b,0,'C');
            $pdf->Cell($w_grade,$h,'',$border_fill_b,0,'C');
            $pdf->Cell($w_score,$h,'',$border_fill_b,0,'C');
            $pdf->Cell($w_point,$h,'',$border_fill_b,1,'C');
        }
    }

    // ====== Penutup Transcript =======


    // ====== Ijazah =======
  public function ijazah(){
        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);

        $dataIjazah = $this->m_save_to_pdf->getIjazah($data_arr['DBStudent'],$data_arr['NPM']);

        $pdf = new FPDF('L','mm','A4');
        $pdf->AddFont('dinproExpBold','','dinproExpBold.php');
        // membuat halaman baru
        $pdf->SetMargins(20.5,10.5,10);
        $pdf->AddPage();
//        $pdf->Image(base_url('images/i.jpg'),0,0,300);
        $h = 3;
        $Ijazah = $dataIjazah['Ijazah'][0];
        $Student = $dataIjazah['Student'][0];
        $border = 0;
        $full_width = 266.5;
        $w_left = 205;
        $w_right = $full_width - $w_left;
        $pdf->SetY(8); //novie
        $pdf->SetFont('dinpromedium','',8);
        $pdf->Cell($w_left,$h,'Nomor Keputusan Akreditasi Program Studi : '.$Student['NoSKBANPT'],$border,0,'L');
        $pdf->Cell($w_right,$h,'Nomor Ijazah Nasional : '.$Student['CNN'],$border,1,'L');
        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell($w_left,$h,'Study Program Accreditation Number',$border,0,'L');
        $pdf->Cell($w_right,$h,'National Certificate Number',$border,1,'L');
        $pdf->Ln(29);
        $fn_b = 11.5;
        $fn_i = 10;
        $x = 60;
        $h = 4;
        $full_width = 212;
        $pdf->SetXY($x,40.5); //novie
        $pdf->SetFont('dinpromedium','',$fn_b);
        $pdf->Cell($full_width,$h,'Memberikan Ijazah Kepada',$border,1,'C');
        $pdf->SetX($x);
        $pdf->SetFont('dinlightitalic','',$fn_i);
        $pdf->Cell($full_width,$h,'This certificate is awarded to',$border,1,'C');
        $pdf->SetX($x);
//        $pdf->SetFont('dinpromedium','',20);
        $pdf->SetFont('dinproExpBold','',20);
        $pdf->Cell($full_width,13,($Student['Name']),$border,1,'C');
        $pdf->Ln(1.5);
        $x = 67;
        $ln = 1.5;
        $label = 70;
        $sp = 1.5;
        $fill = 100;
        $fillFull = $label + $sp + $fill;
        $border = 0;
        // ===== TTL =====
        $pdf->SetX($x);
        $pdf->SetFont('dinpromedium','',$fn_b);
        $pdf->Cell($label,$h,'Tempat dan Tanggal Lahir',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['PlaceOfBirth'].', '.$this->getDateIndonesian($Student['DateOfBirth']),$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('dinlightitalic','',$fn_i);
        $pdf->Cell($label,$h,'Place and Date of Birth',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['PlaceOfBirth'].', '.date('F j, Y',strtotime($Student['DateOfBirth'])),$border,1,'L');
        $pdf->Ln($ln);
        // ===== NIM =====
        $pdf->SetX($x);
        $pdf->SetFont('dinpromedium','',$fn_b);
        $pdf->Cell($label,$h,'Nomor Induk Mahasiswa',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['NPM'],$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('dinlightitalic','',$fn_i);
        $pdf->Cell($label,$h,'Student Identification Number',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['NPM'],$border,1,'L');
        $pdf->Ln($ln);

        // ===== KTP/ NIK ====
        $pdf->SetX($x);
        $pdf->SetFont('dinpromedium','',$fn_b);
        $pdf->Cell($label,$h,'Nomor Induk Kependudukan',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['KTPNumber'],$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('dinlightitalic','',$fn_i);
        $pdf->Cell($label,$h,'Citizen Identification Number',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['KTPNumber'],$border,1,'L');
        $pdf->Ln($ln);

        // ===== Prodi =====
        $pdf->SetX($x);
        $pdf->SetFont('dinpromedium','',$fn_b);
        $pdf->Cell($label,$h,'Program Studi',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['Prodi'],$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('dinlightitalic','',$fn_i);
        $pdf->Cell($label,$h,'Study Program',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,1,'L');
        $pdf->Ln($ln);
        // ===== Program Pendidikan =====
        $pdf->SetX($x);
        $pdf->SetFont('dinpromedium','',$fn_b);
        $pdf->Cell($label,$h,'Program Pendidikan',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['GradeDesc'],$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('dinlightitalic','',$fn_i);
        $pdf->Cell($label,$h,'Educational Program',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['GradeDescEng'],$border,1,'L');
        $pdf->Ln($ln);
        // ===== Tanggal Yudisium =====
        $pdf->SetX($x);
        $pdf->SetFont('dinpromedium','',$fn_b);
        $pdf->Cell($label,$h,'Tanggal Yudisium',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
//        $pdf->Cell($fill,$h,date('d/m/Y',strtotime($Ijazah['DateOfYudisium'])),$border,1,'L');
        $pdf->Cell($fill,$h,$this->getDateIndonesian($Ijazah['DateOfYudisium']),$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('dinlightitalic','',$fn_i);
        $pdf->Cell($label,$h,'Date of Yudisium',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,date('F j, Y',strtotime($Ijazah['DateOfYudisium'])),$border,1,'L');
//        $pdf->Cell($fill,$h,date('d/m/Y',strtotime($Ijazah['DateOfYudisium'])),$border,1,'L');
        $pdf->Ln(4);
        // ===== Ket 1 =====
        // 171.5
        $pdf->SetX($x);
        $pdf->SetFont('dinpromedium','',$fn_b);
        $pdf->Cell(135,$h,'Dengan demikian yang bersangkutan berhak memakai gelar Akademik : ',$border,0,'L');
        $pdf->Cell(15,$h,'',$border,0,'L');
        $pdf->Cell(50,$h,$Student['Degree'].' ('.$Student['TitleDegree'].')',$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('dinlightitalic','',$fn_i);
        $pdf->Cell(135,$h,'Henceforth the conferee is entitled to use the academic degree of',$border,0,'L');
        $pdf->Cell(15,$h,'',$border,0,'L');
        $pdf->Cell(50,$h,$Student['DegreeEng'].' ('.$Student['TitleDegreeEng'].')',$border,1,'L');
        $pdf->Ln($ln);
        // ===== Ket 2 =====
        $pdf->SetX($x);
        $pdf->SetFont('dinpromedium','',$fn_b);
        $pdf->Cell($fillFull,$h,'dan diberikan hak serta wewenang yang berhubungan dengan gelar yang dimilikinya.',$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('dinlightitalic','',$fn_i);
        $pdf->Cell($fillFull,$h,'and conferred the right and privileges pertaining to this degree.',$border,1,'L');
        $y = $pdf->GetY()+7;
        $pdf->Ln(15);
        // 171.5

      // Rektor
      $Rektorat = $dataIjazah['Rektorat'][0];

      $titleARektor = ($Rektorat['TitleAhead']!='')? $Rektorat['TitleAhead'].'' : '';
      $titleBRektor = ($Rektorat['TitleBehind']!='')? $Rektorat['TitleBehind'] : '';
      $komaRektor = ($titleBRektor!='') ? ',' : '';
      $Rektor = $titleARektor.''.$Rektorat['Name'].', '.$titleBRektor;

        if($Student['FacultyID']!=4 || $Student['FacultyID']!='4'){

            // Tanda tangan
            $pdf->SetX($x+10);
            $pdf->SetFont('dinpromedium','',$fn_b);
            $pdf->Cell(171.5,$h,$Ijazah['PlaceIssued'].', '.$this->getDateIndonesian($Ijazah['DateIssued']),$border,1,'L');
            $pdf->SetX($x+10);
            $pdf->SetFont('dinlightitalic','',$fn_i);
            $pdf->Cell(171.5,$h,$Ijazah['PlaceIssued'].', '.date('F j, Y',strtotime($Ijazah['DateIssued'])),$border,1,'L');
            $pdf->Ln(3);
            $pdf->SetX($x+10);
            $pdf->SetFont('dinpromedium','',$fn_b);
            $pdf->Cell(138.5,$h,'Rektor',$border,0,'L');
            $pdf->Cell(41,$h,'Dekan',$border,1,'L');
            $pdf->SetFont('dinlightitalic','',$fn_i);
            $pdf->SetX($x+10);
            $pdf->Cell(138.5,$h,'Rector',$border,0,'L');
            $pdf->Cell(41,$h,'Dean',$border,1,'L');
            $pdf->Ln(13);

            // Dekan --
            $titleA = ($Student['TitleAhead']!='') ? $Student['TitleAhead'].'' : '';
            $titleB = ($Student['TitleBehind']!='') ? $Student['TitleBehind'] : '' ;
            $Dekan = $titleA.''.$Student['Dekan'].', '.$titleB;

            // ----
            $pdf->SetFont('dinpromedium','',$fn_b);
            $yy = 9.2; // novie
            $xx = 57;
            $ytext = $pdf->GetY()+$yy;
            $xtext = $pdf->GetX()+$xx;
            $pdf->Text($xtext,$ytext,$Rektor);
            $ytext = $pdf->GetY()+$yy+4;
            $xtext = $pdf->GetX()+$xx;
            $pdf->SetFont('dinpromedium','',$fn_b-2);
            $pdf->Text($xtext,$ytext,'NIP : '.$Rektorat['NIP']);
            $pdf->SetFont('dinpromedium','',$fn_b);
            $ytext = $pdf->GetY()+$yy;
            $xtext = $pdf->GetX()+$xx+138;
            $pdf->Text($xtext,$ytext,$Dekan);
            $ytext = $pdf->GetY()+$yy+4;
            $xtext = $pdf->GetX()+$xx+138;
            $pdf->SetFont('dinpromedium','',$fn_b-2);
            $pdf->Text($xtext,$ytext,'NIP : '.$Student['NIP']);
            //foto
            $pdf->Rect($x+95, $y, 40, 58);

        } else {

            // Tanda tangan
            $pdf->SetX($x+120);
            $pdf->SetFont('dinpromedium','',$fn_b);
            $pdf->Cell(171.5,$h,$Ijazah['PlaceIssued'].', '.$this->getDateIndonesian($Ijazah['DateIssued']),$border,1,'L');
            $pdf->SetX($x+120);
            $pdf->SetFont('dinlightitalic','',$fn_i);
            $pdf->Cell(171.5,$h,$Ijazah['PlaceIssued'].', '.date('F j, Y',strtotime($Ijazah['DateIssued'])),$border,1,'L');

            $pdf->Ln(3);
            $pdf->SetX($x+120);
            $pdf->SetFont('dinpromedium','',$fn_b);
            $pdf->Cell(138.5,$h,'Rektor',$border,1,'L');
            $pdf->SetFont('dinlightitalic','',$fn_i);
            $pdf->SetX($x+120);
            $pdf->Cell(138.5,$h,'Rector',$border,1,'L');
            $pdf->Ln(13);


            $pdf->SetFont('dinpromedium','',$fn_b);
            $yy = 9.2; // novie
            $xx = 57 + 110;
            $ytext = $pdf->GetY()+$yy;
            $xtext = $pdf->GetX()+$xx;
            $pdf->Text($xtext,$ytext,$Rektor);
            $ytext = $pdf->GetY()+$yy+4;
            $xtext = $pdf->GetX()+$xx;
            $pdf->SetFont('dinpromedium','',$fn_b-2);
            $pdf->Text($xtext,$ytext,'NIP : '.$Rektorat['NIP']);
            $pdf->SetFont('dinpromedium','',$fn_b);



            //foto
            $pdf->Rect($x+47, $y, 38, 55); //Update Irfan

        }







        $nameF = str_replace(' ','_',($Student['Name']));
        $pdf->Output('IJAZAH_'.$Student['NPM'].'_'.$nameF.'.pdf','I');
    }



//====================== tambahan TGL 17-01-2019 SKLS ==========================
//==============================================================================
  public function skls(){
        $token = $this->input->post('token');
        $data_arr = $this->getInputToken($token);
        $filterSemester = $data_arr['Semester'];

        $dataSkls = $this->m_save_to_pdf->getSkls($data_arr['DBStudent'],$data_arr['NPM']);

         // Get Semester
        $dataSmt = $this->db->query('SELECT * FROM db_academic.semester WHERE ID = "'.$filterSemester.'" ')->result_array();

        $datawarek1 = $this->db->get_where('db_employees.employees em',
        "(em.PositionMain = '2.2' OR em.PositionOther1 = '2.2' OR em.PositionOther2 = '2.2' OR em.PositionOther3 = '2.2') AND  em.StatusEmployeeID = '1'")
        //array('PositionMain' => '2.2','StatusEmployeeID'=>'1' ))
        ->result_array();
        //print_r($dataSmt); exit;

        $dtsmtr = $dataSmt[0]['Name'];
        $tahun = substr($dtsmtr,0,-6);

        if ($dataSmt[0]['Code'] == '1'){
            $angkasmter = 'Ganjil';
        }else {
            $angkasmter = 'Genap';
        }

        $pdf = new FPDF('P','mm','A4');
        $pdf->SetMargins(20.5,10.5,10);
        $pdf->AddPage();
        $h = 0;
        $Skls = $dataSkls['Skls'][0];
        $Student = $dataSkls['Student'][0];
        $border = 0;

        // ========buat tanggal header yudisium ===================
        // ========================================================
        $pdf->Ln(15);
        $fn_b = 10;
        $fn_i = 9;
        $x = 22;
        $h = 4;
        $border = 0;
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell(171.5,$h,$Skls['PlaceIssued'].', '.$this->getDateIndonesian($Skls['DateOfYudisium']),$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_i);
        $pdf->Cell(171.5,$h,'No : '.$Student['SKLN'],$border,1,'L');
        // $pdf->Cell($w_right,$h,'Nomor Seri Ijazah : '.$Student['CSN'],$border,1,'L');
        $pdf->SetX($x);
        // ========================================================


        $pdf->Ln(29);
        $fn_b = 10; //untuk ukuran huruf
        $fn_i = 9;
        $fn_e = 9; // untuk ukuran font
        $x = 40;
        $h = 4;

        $full_width = 140;
        $pdf->SetXY($x,45.5);//untuk jarak rata kanan dan jarak header
        $pdf->SetFont('Arial','BU',$fn_b);
        $pdf->Cell($full_width,$h,'SURAT KETERANGAN LULUS SEMENTARA',$border,1,'C');
        $pdf->SetX($x);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell($full_width,$h,'To Whom It May Concern',$border,1,'C');
        $pdf->SetX($x);
        $pdf->Cell(10,7,'',0,1);//memberikan enter/jarak ke bawah


        // ======================= Keterangan 1 ============================
        // ======================================================================================
        $x = 22;
        $ln = 1.5;
        $pdf->Ln(4);
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell(135,$h,'Yang bertandatangan di bawah ini menerangkan bahwa : ',$border,0,'L');
        $pdf->Cell(15,$h,'',$border,0,'L');
        $pdf->Cell(10,4,'',0,1);//memberikan enter/jarak ke bawah
        $pdf->SetX($x);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell(135,$h,'This is  to certify that',$border,0,'L');
        $pdf->Cell(15,$h,'',$border,0,'L');
        $pdf->Cell(10,7,'',0,1);
        $pdf->Ln(6);
        //========================================================================================
        //========================================================================================
        $pdf->Ln(1.5);
        $x = 22;
        $ln = 1.5;
        $label = 65; // memberikan jarak : dengan tulisan
        $sp = 3.5;
        $fill = 100;
        $fillFull = $label + $sp + $fill;
        $border = 0;
        $Kelamin = '';

        // ===== Name =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($label,$h,'Nama  ',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        // $pdf->Cell($fill,$h,$Student['Name'],$border,0,'L');
        // $pdf->SetX(65);
        $pdf->Cell($fill,$h,($Student['Name']),$border,0,'L');

        $pdf->SetX(5);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell(68,$h,'/ Name',$border,1,'C');

        $pdf->Ln($ln);
        // ===== TTL =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($label,$h,'Tempat, Tanggal Lahir',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['PlaceOfBirth'].', '.$this->getDateIndonesian($Student['DateOfBirth']),$border,0,'L');
        $pdf->SetX(61);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell($label,$h,'/  Date of birth',$border,1,'L');
        $pdf->Ln(1.5);
        // ===== NIM =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($label,$h,'Nomor Induk Mahasiswa',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['NPM'],$border,0,'L');
        $pdf->SetX(64);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell($label,$h,'/  Student ',$border,1,'L');
        $pdf->SetX(22);
        $pdf->Cell($label,$h,'ID Number',$border,0,'L');
        $pdf->Ln(5);

         // ===== Fakultas/Faculty =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($label,$h,'Fakultas',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['Faculty'],$border,0,'L');

        if ($Student['Faculty'][0]=='T')
        {
            $pdf->SetX(117);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['FacultyEng'],$border,0,'L');
        }else{
            $pdf->SetX(118);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['FacultyEng'],$border,0,'L');
        }

        $pdf->SetX(5);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell(78,$h,'/  Faculty',$border,1,'C');
        $pdf->Ln($ln);

        // ===== Prodi =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($label,$h,'Program Studi',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['Prodi'],$border,0,'L');

        // print_r($Student['Prodi']);exit();

        if ($Student['Prodi'][0]=='A')
        {
            $pdf->SetX(108);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,0,'L');
        }
        else if ($Student['Prodi'][0]=='K')
        {
            $pdf->SetX(118);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,0,'L');
        }
        else if ($Student['Prodi'][0]=='B')
        {
            $pdf->SetX(119);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,0,'L');
        }
        else if ($Student['Prodi'][0]=='H')
        {
            $pdf->SetX(115);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,0,'L');
        }
        else if ($Student['Prodi'][0]=='P')
        {
            $pdf->SetX(143);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,0,'L');
        }
        else if ($Student['Prodi'][0]=='D')
        {
            $pdf->SetX(116);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,0,'L');
        }
        else if ($Student['Prodi']=='Teknik Lingkungan')
        {
            $pdf->SetX(123);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,0,'L');
        }
         else if ($Student['Prodi']=='Teknik Konstruksi Bangunan')
        {
            $pdf->SetX(137);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,0,'L');
        }

        $pdf->SetX(47);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell(67,$h,'/  Study Program',$border,0,'L');

        if ($Student['Prodi'][0]=='M')
        {
            $pdf->SetX(152);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,'/',$border,1,'L');

            $pdf->SetX(90.5);
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($fill,$h,$Student['ProdiEng'],$border,0,'L');
            // $pdf->Ln(4);
        }
        $pdf->Ln(5.5);

        // ===== Program Pendidikan =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($label,$h,'Program Pendidikan',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$Student['GradeDesc'],$border,0,'L');

        // print_r($Student['GradeDesc'][0]);exit();

        if ($Student['GradeDesc'][0]=='S')
        {
            $pdf->SetX(112);// S1
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,' /',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['GradeDescEng'],$border,0,'L');
        }else{
            $pdf->SetX(117);// D4
            $pdf->SetFont('Arial','I',$fn_e);
            $pdf->Cell($sp,$h,' /',$border,0,'L');
            $pdf->Cell($fill,$h,$Student['GradeDescEng'],$border,0,'L');
        }

        $pdf->SetX(56);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell($label,$h,'/  Educational',$border,1,'L');
        $pdf->SetX(22.1);
        $pdf->Cell($label,$h,'Program',$border,1,'L');
        $pdf->Ln($ln);
        // ===== Status =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($label,$h,'Status',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,'Terakreditasi',$border,0,'L');

        $pdf->SetX(113);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell($sp,$h,'/  Accredited',$border,0,'L');

        // $pdf->SetX(37);
        // $pdf->SetFont('Arial','I',$fn_e);
        // $pdf->Cell($label,$h,'/ Status',$border,1,'L');
        $pdf->Ln(5);
        // ===== Tanggal Yudisium =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($label,$h,'Tanggal Yudisium',$border,0,'L');
        $pdf->Cell($sp,$h,':',$border,0,'C');
        $pdf->Cell($fill,$h,$this->getDateIndonesian($Skls['DateOfYudisium']),$border,0,'L');

        $pdf->SetX(53);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell($label,$h,'/  Date of Conferral',$border,1,'L');
        $pdf->Cell(10,5,'',0,1);//memberikan enter/jarak ke bawah

        // ===== Ket 2 =====
        // $pdf->SetX($x);
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($fillFull,$h,'Adalah benar mahasiswa yang telah menempuh studi dan menyelesaikan seluruh persyaratan kelulusan',$border,1,'L');
        $pdf->SetX($x);
        $pdf->Cell($fillFull,$h,'menjadi '.$Student['Degree'].' pada Semester '.$angkasmter.' Tahun Akademik '.$tahun,$border,1,'L');
        $pdf->SetX($x);
        $pdf->Cell($fillFull,$h,'di Universitas Agung Podomoro.',$border,1,'L');
        $pdf->Ln(1);

        if ($Student['Gender'][0]=='P')
        {
            $Kelamin='Her'; //kolom disesuaikan
        }else{
            $Kelamin='His';
        }

        if ($dataSmt[0]['Code'] == '1'){
            $angkasmterx = 'Odd';
        }else {
            $angkasmterx = 'Even';
        }

        $pdf->SetX($x);
        $pdf->SetFont('Arial','I',$fn_e);
        $pdf->Cell($fillFull,$h,'Had completed '.$Kelamin.' studies and qualification to earn Bachelor Degree in the '.$angkasmterx.' Semester of Academic Year',$border,1,'L');
        $pdf->SetX($x);
        $pdf->Cell($fillFull,$h, $tahun.' at Podomoro University.',$border,1,'L');
        $y = $pdf->GetY()+7;
        $pdf->Ln(4);
        // $pdf->Cell(10,5,'',0,1);//memberikan enter/jarak ke bawah

        // ===== Ket 2 =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($fillFull,$h,'Ijazah yang bersangkutan masih dalam proses.',$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('Arial','I',$fn_e);


        if ($Student['Gender'][0]=='P')
        {
            $Kelamin='Her'; //kolom disesuaikan
        }else{
            $Kelamin='His';
        }

        // print_r($Kelamin);exit();
        $pdf->Cell($fillFull,$h,$Kelamin. ' certificate is in process.',$border,1,'L');
        $y = $pdf->GetY()+7;
        $pdf->Ln(4);
        // $pdf->Cell(10,5,'',0,1);//memberikan enter/jarak ke bawah

        // ===== Ket 2 =====
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($fillFull,$h,'Demikian Surat Keterangan ini dibuat untuk dapat dipergunakan sebagaimana mestinya.',$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('Arial','I',$fn_e);

        $pdf->Cell($fillFull,$h,'This letter is issued by the Academic Administration and should be used accordingly.',$border,1,'L');
        $y = $pdf->GetY()+7;
        $pdf->Ln(15);

        //================ Tanda tangan =======================
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($fillFull,$h,'Hormat kami,',$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('Arial','I',$fn_i);
        $pdf->Cell($fillFull,$h,'Best Regards,',$border,1,'L');
        $y = $pdf->GetY()+7;
        $pdf->Ln(35);

        $pdf->SetX($x);
        $pdf->SetFont('Arial','BU',$fn_b);


        $warek1 = $datawarek1[0]['TitleAhead'].' '.$datawarek1[0]['Name'].', '.$datawarek1[0]['TitleBehind'];
        $pdf->Cell($fillFull,$h,$warek1,$border,1,'L');
        //================ hormat kami ========================
        //================ Tanda tangan =======================
        $pdf->SetX($x);
        $pdf->SetFont('Arial','',$fn_b);
        $pdf->Cell($fillFull,$h,'Wakil Rektor I Bidang Akademik',$border,1,'L');
        $pdf->SetX($x);
        $pdf->SetFont('Arial','I',$fn_i);
        $pdf->Cell($fillFull,$h,'Vice Rector of Academic Affairs',$border,1,'L');
        $y = $pdf->GetY()+7;
        $pdf->Ln(15);
        //================ hormat kami ========================


        $nameF = str_replace(' ','_',($Student['Name']));
        $pdf->Output('SKLS_'.$Student['NPM'].'_'.$nameF.'.pdf','I');
    }
//===================================================================
//===================================================================



    function GenerateWord()
    {
        //Get a random word
        $nb=rand(3,10);
        $w='';
        for($i=1;$i<=$nb;$i++)
            $w.=chr(rand(ord('a'),ord('z')));
        return $w;
    }

    function GenerateSentence()
    {
        //Get a random sentence
        $nb=rand(1,10);
        $s='';
        for($i=1;$i<=$nb;$i++)
            $s.=$this->GenerateWord().' ';
        return substr($s,0,-1);
    }

    // ======= SKPI =======
    public function diploma_supplement(){

        $pdf = new Pdf_mc_table('P','mm','A4');

        $this->SKPI_page1($pdf);
        $this->SKPI_page2($pdf);
        $this->SKPI_page3($pdf);
        $this->SKPI_page4($pdf);
        $this->SKPI_page5($pdf);
        $this->SKPI_page6($pdf);


        $pdf->Output('skpi.pdf','I');
    }


    private function SKPI_page1($pdf){
        // membuat halaman baru
        $pdf->SetMargins(10,3,10);

        $pdf->AddPage();
        $pdf->Ln(30);

        $space_bt = 0.7;
        $R = 226;
        $G = 226;
        $B = 226;

        $pdf->SetFillColor($R, $G, $B);
        $pdf->SetFont('dinpromedium','',17);

        $pdf->Cell(190,3.5,'','T',1,'L');

        $h = 5.5;

        $pdf->Cell(120,$h,'SURAT KETERANGAN',0,0,'L');
        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(70,$h,'NOMOR : SKPI/MNS1/2013/BNN00324',0,1,'C',TRUE);

        $pdf->SetFont('dinpromedium','',17);
        $pdf->Cell(0,$h,'PENDAMPING IJAZAH',0,1,'L');

        $pdf->SetFont('dinlightitalic','',12);
        $pdf->Cell(190,$h,'Diploma Supplement',0,1,'L');

        $pdf->Cell(190,3,'','B',1,'L');

        $tx = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s
                standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a
                type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining
                essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum';
        $pdf->Ln(1.5);
        $pdf->SetFont('dinprolight','',9);
        $pdf->SetWidths(array(190));
        $pdf->Row(array($tx),0,4);

        $pdf->Ln(2);
        $pdf->Row(array(strlen($tx).' - '.$tx),0,4);

        $pdf->Cell(190,2,'','B',1,'L');
        $pdf->Ln(1.5);
        $h = 4.5;
        $pdf->SetFont('dinpromedium','',11);
        $pdf->Cell(190,$h,'01. INFORMASI TENTANG IDENTITAS DIRI PEMEGANG SKPI',0,1,'L');
        $pdf->SetFont('dinlightitalic','',10);
        $pdf->Cell(190,$h,'01. Information Identifying the Holder of Diploma Suppliyer',0,1,'L');


        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'NAMA LENGKAP',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'TAHUN LULUS',0,1,'L');

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,'Full Name',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'Year of Completion',0,1,'L');

        $h = 6;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' Nandang Mulyadi',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' 2016',0,1,'L',true);


        $pdf->Ln(3);

        $h = 3.5;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'TEMPAT DAN TANGGAL LAHIR',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'GELAR',0,1,'L');
        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,'Date and Place of Birth',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'Name of Qualification',0,1,'L');

        $h = 3.5;
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' Jakarta, 29 Januari 1994',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Sarjana Ekonomi (SE)',0,1,'L',true);

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,' Jakarta, January 29, 1994',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Bachelor In Management',0,1,'L',true);
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);



        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'NOMOR INDUK MAHASISWA',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'NOMOR IJAZAH',0,1,'L');

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,'Student Identification Number',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'Diploma Number',0,1,'L');

        $h = 6;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' 31140005',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' MNS/2013/31140005',0,1,'L',true);


        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        $pdf->Cell(190,2,'','B',1,'L');
        $pdf->Ln(2);
        $h = 4.5;
        $pdf->SetFont('dinpromedium','',11);
        $pdf->Cell(190,$h,'02. INFORMASI TENTANG IDENTITAS PENYELENGGARA PROGRAM',0,1,'L');
        $pdf->SetFont('dinlightitalic','',10);
        $pdf->Cell(190,$h,'02. Information Identifying the Awarding Institution',0,1,'L');


        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'SK PENDIRIAN PERGURUAN TINGGI',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'PERSYARATAN PENERIMAAN',0,1,'L');

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,'Awarding Institution\'s License',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'Entry Requirements',0,1,'L');

        $h = 3.5;
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' No : 55/D/O/1996, Tanggal 8 Agustus 1996',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Lulusan pendidikan menengah atas / sederajat',0,1,'L',true);

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,' No : 55/D/O/1996, Date August 8, 1996',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Graduate from heigh school or similar level of education',0,1,'L',true);
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);


        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++
        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'NAMA PERGURUAN TINGGI',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'BAHASA PENGANTAR KULIAH',0,1,'L');

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,'Awarding Institution',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'Language of Intrusion',0,1,'L');

        $h = 3.5;
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' Universitas Agung Podomoro',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Indonesia',0,1,'L',true);

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,' Agung Podomoro University',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Indonesian',0,1,'L',true);
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);


        // ++++++++++++++++++++++
        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'PROGRAM STUDI',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'SISTEM PENILAIAN',0,1,'L');

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,'Major',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'Grading System',0,1,'L');

        $h = 3.5;
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' Manajemen',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Skala 1-4; A=4, B=3, C=2, D=1',0,1,'L',true);

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,' Management',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Scale 1-4; A=4, B=3, C=2, D=1',0,1,'L',true);
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);

        // +++++++++++++++++++++++++++

        $pdf->Ln(1);
        $h = 3.5;
        $this->minSpaceSKPICustom($pdf,$space_bt,true,$R, $G, $B);
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' Kelas : Reguler',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' LAMA STUDI REGULER',0,1,'L');

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,' Class : Reguler',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Reguler Length of Study',0,1,'L');
        $this->minSpaceSKPICustom($pdf,$space_bt,true,$R, $G, $B);

        // +++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++

        $pdf->Ln(1);
        $h = 3.5;
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' Program Manajemen',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' 8 Semester',0,1,'L',true);

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,' Program Management',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' 8 Semesters',0,1,'L',true);
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);

        // ++++++++++++++++++++++++++++++++++++++++++++++

        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'JENIS & JENJANG PENDIDIKAN',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'JENIS & JENJANG PENDIDIKAN LANJUTAN',0,1,'L');

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,'Type & Level of Education',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'Access to Further Study',0,1,'L');

        $h = 3.5;
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' Akademik & Sarjana (Strata 1)',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Program Magister & Doktoral',0,1,'L',true);

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,' Academic & Bachelor Degree',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' master & Doctoral Program',0,1,'L',true);
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);

        // ++++++++++++++++++++++
        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'JENJANG KUALIFIKASI SESUAI KKNI',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'STATUS PROFESI (BILA ADA)',0,1,'L');

        $pdf->SetFont('dinlightitalic','',8);
        $pdf->Cell(90,$h,'Lefel of Qualification the National Qualification Framework',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'Profesional Status (If Applicable)',0,1,'L');

        $h = 6;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,' Level 6',0,0,'L',true);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' -'.$pdf->GetY(),0,1,'L',true);
    }

    private function SKPI_page2($pdf){

        // membuat halaman baru
        $this->newPageSKPI($pdf);

        $R = 226;
        $G = 226;
        $B = 226;
        $h_max = 270;

        $pdf->SetFillColor($R, $G, $B);

        $pdf->Cell(190,2,'','B',1,'L');
        $pdf->Ln(3);
        $h = 4.5;
        $pdf->SetFont('dinpromedium','',11);
        $pdf->Cell(190,$h,'03. INFORMASI TENTANG KULAIFIKASI DAN HASIL YANG DICAPAI',0,1,'L');
        $pdf->SetFont('dinlightitalic','',10);
        $pdf->Cell(190,$h,'03. Information Identifying the Qualification and Outcomes Obtained',0,1,'L');

        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(90,$h,'A. CAPAIAN PEMBELAJARAN',0,0,'L');
        $pdf->SetFont('dinpromediumitalic','',9);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'A. Learning Outcomes',0,1,'L');

        // +++++++++++
        $pdf->Ln(3);
        $space_bt = 1.5;
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);
        $h = 3.5;
        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(90,$h,' SARJANA EKONOMI : MANAJEMEN',0,0,'L',true);
        $pdf->SetFont('dinpromediumitalic','',9);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Management Bachelor Level',0,1,'L',true);


        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(90,$h,' (KKNI Level 6)',0,0,'L',true);
        $pdf->SetFont('dinpromediumitalic','',9);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' (KKNI Level 6)',0,1,'L',true);
        $this->minSpaceSKPI($pdf,$space_bt,true,$R, $G, $B);

        // ==========KEMAMPUAN KERJA============
        $pdf->Ln(3);
        $h = 5.5;
        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(90,$h,' KEMAMPUAN KERJA',0,0,'L',true);
        $pdf->SetFont('dinpromediumitalic','',9);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,' Working Capability',0,1,'L',true);

        $pdf->SetFont('dinprolight','',9);


        $this->loadMultyCellWithNumber($pdf,8);

        //===========================================

        // ==========PENGUASAAN PENGETAHUAN============

        // Cek apakah membuat halaman baru atau tidak
        $newH = $pdf->GetY()+15;
        if($newH>$h_max){
            $this->newPageSKPI($pdf);
            $newH = $pdf->GetY()+5;
        }

        $pdf->SetXY(10,$newH);

        $h = 5.5;
        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(90,$h,' PENGUASAAN PENGETAHUAN',0,0,'L',true);

        $pdf->SetX(110);
        $pdf->SetFont('dinpromediumitalic','',9);
        $pdf->Cell(90,$h,' Knowledge Competencies',0,1,'L',true);

        $this->loadMultyCellWithNumber($pdf,8);

        //===========================================


        // ==========SIKAP KHUSUS============
        $y = $pdf->GetY()+15;
        if($y>$h_max){
            $this->newPageSKPI($pdf);
            $y = $pdf->GetY()+5;
        }

        $pdf->SetXY(10,$y);

        $h = 5.5;
        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(90,$h,' SIKAP KHUSUS',0,0,'L',true);

        $pdf->SetX(110);
        $pdf->SetFont('dinpromediumitalic','',9);
        $pdf->Cell(90,$h,' Special Attitude',0,1,'L',true);

        $this->loadMultyCellWithNumber($pdf,4);

    }

    private function SKPI_page3($pdf){
        // membuat halaman baru
        $pdf->SetMargins(10,3,10);

        $pdf->AddPage();
        $pdf->Ln(30);

        $R = 226;
        $G = 226;
        $B = 226;

        $h_fill = 3.5;
        $h_max = 242;

        $pdf->SetFillColor($R, $G, $B);

        $pdf->Cell(190,2,'','B',1,'L');
        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(90,$h,'B. AKTIVITAS PRESTASI DAN PENGHARGAAN',0,0,'L');
        $pdf->SetFont('dinpromediumitalic','',9);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'B. Activities, Achievements and Awards',0,1,'L');

        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'Pemegang Surat Keterangan Pendamping Ijazah ini memiliki ',0,0,'L');
        $pdf->SetFont('dinlightitalic','',9);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'The holder of this supplement has the following professional',0,1,'L');

        $pdf->SetFont('dinprolight','',9);
        $pdf->Cell(90,$h,'sertifikat professional : ',0,0,'L');
        $pdf->SetFont('dinlightitalic','',9);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'certifications : ',0,1,'L');


        // +++++++++++++++++++++++++++++++++++
        $tx = 'typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ev';
        $tx2 = 'Lorem Ipsum is simply dummy text of the printing and ';

        $y = $pdf->GetY()+3;
        $y2 = $pdf->GetY()+3;

        $nb=0;
        $nb2=0;

        for($i=1;$i<=3;$i++){

            $t1 = ($i%2==0) ? $tx2 : $tx ;
            $t2 = ($i%2==0) ? $tx : $tx2 ;

            //Calculate the height of the row

            $nb =max($nb,$pdf->NbLines(83,$t1));
            $nb2 =max($nb2,$pdf->NbLines(83,$t2));

            $h=$h_fill*$nb;
            $h2=$h_fill*$nb2;

            $hC = ($h > $h2) ? $h : $h2;

            //Issue a page break first if needed
            $pdf->CheckPageBreak($hC);


            $pdf->SetFont('dinprolight','',9);
            // Indo
            $pdf->SetXY(10,$y);
            $pdf->MultiCell(7, $h_fill, $i, 0, 'C',false);
            $pdf->SetXY(17,$y);
            $pdf->MultiCell(83, $h_fill, $y.' - '.$t1, 0, 'L',false);
            $y = $pdf->GetY()+1;

            // Eng
            $pdf->SetFont('dinlightitalic','',9);
            $pdf->SetXY(110,$y2);
            $pdf->MultiCell(7, $h_fill, $i, 0, 'C',false);
            $pdf->SetXY(117,$y2);
            $pdf->MultiCell(83, $h_fill, $y2.' -'.$t2, 0, 'L',false);
            $y2 = $pdf->GetY()+1;

            if($y2>$h_max){
                $pdf->SetMargins(10,3,10);
                $pdf->AddPage();
                $pdf->Ln(30);

                $y = $pdf->GetY()+5;
                $y2 = $pdf->GetY()+5;
                $pdf->Cell(190,2,'','B',1,'L');
            }
        }

        // +++++++++++++++++++++++++++++++++++
        $y = $pdf->GetY()+10;
        $y2 = $pdf->GetY()+10;
        // Indo
        $pdf->SetFont('dinprolight','',9);
        $pdf->SetXY(10,$y);
        $pdf->MultiCell(90, $h_fill,'Mahasiswa Universitas Agung Podomoro telah mengikuti program atau telah memenuhi tanggung jawab sebagai berikut ini :', 0, 'L',false);

        // Eng
        $pdf->SetFont('dinlightitalic','',9);
        $pdf->SetXY(110,$y2);
        $pdf->MultiCell(90, $h_fill,'The students of Agung Podomoro University were involved in the following programs / fulfilled the folowing responsibilities :', 0, 'L',false);


        // +++++++++++++++++++++++++++++++++++
        $y = $pdf->GetY()+5;
        $y2 = $pdf->GetY()+5;

        $nb=0;
        $nb2=0;

        for($i=1;$i<=4;$i++){

            $t1 = ($i%2==0) ? $tx2 : $tx ;
            $t2 = ($i%2==0) ? $tx : $tx2 ;

            //Calculate the height of the row

            $nb =max($nb,$pdf->NbLines(83,$t1));
            $nb2 =max($nb2,$pdf->NbLines(83,$t2));

            $h=$h_fill*$nb;
            $h2=$h_fill*$nb2;

            $hC = ($h > $h2) ? $h : $h2;

            //Issue a page break first if needed
            $pdf->CheckPageBreak($hC);


            $pdf->SetFont('dinprolight','',9);
            // Indo
            $pdf->SetXY(10,$y);
            $pdf->MultiCell(7, $h_fill, $i, 0, 'C',false);
            $pdf->SetXY(17,$y);
            $pdf->MultiCell(83, $h_fill, $y.' - '.$t1, 0, 'L',false);
            $y = $pdf->GetY()+1;

            // Eng
            $pdf->SetFont('dinlightitalic','',9);
            $pdf->SetXY(110,$y2);
            $pdf->MultiCell(7, $h_fill, $i, 0, 'C',false);
            $pdf->SetXY(117,$y2);
            $pdf->MultiCell(83, $h_fill, $y2.' -'.$t2, 0, 'L',false);
            $y2 = $pdf->GetY()+1;

            if($y2>$h_max){
                $pdf->SetMargins(10,3,10);
                $pdf->AddPage();
                $pdf->Ln(30);

                $y = $pdf->GetY()+5;
                $y2 = $pdf->GetY()+5;
                $pdf->Cell(190,2,'','B',1,'L');
            }
        }

        // ++++++++++++++++++
        $y = $pdf->GetY()+10;
        $y2 = $pdf->GetY()+10;
        // Indo
        $pdf->SetFont('dinprolight','',9);
        $pdf->SetXY(10,$y);
        $pdf->MultiCell(90, $h_fill,'Catatan : Program-program tersebut di atas terdiri atas kegiatan untuk mrngrmbangkan soft skills mahasiswa. Daftar kegiatan ko-kurikuler dan ekstra-kulikuler yang diikuti oleh pemegang SKPI ini terlampir.', 0, 'L',false);

        // Eng
        $pdf->SetFont('dinlightitalic','',9);
        $pdf->SetXY(110,$y2);
        $pdf->MultiCell(90, $h_fill,'Note : The above-mentioned programs comprise of activities that develop student\'s soft skill. A list of co-curricular and extra curricular activities taken by the holder of this supplement is attached.', 0, 'L',false);



    }

    private function SKPI_page4($pdf){
        // membuat halaman baru
        $pdf->SetMargins(10,3,10);

        $pdf->AddPage();
        $pdf->Ln(30);

        $R = 226;
        $G = 226;
        $B = 226;

        $h_fill= 3.5;
        $h_max = 242;
        $pdf->SetFillColor($R, $G, $B);

        $pdf->Cell(190,2,'','B',1,'L');
        $pdf->Ln(3);

        $h = 4.5;
        $pdf->SetFont('dinpromedium','',11);
        $pdf->Cell(190,$h,'04. INFORMASI TENTANG PENDIDIKAN TINGGI DI INDONESIA',0,1,'L');
        $pdf->SetFont('dinlightitalic','',10);
        $pdf->Cell(190,$h,'04. Information on the Indonesia Higher Education System and the Indonesian National Qualification Framework',0,1,'L');

        $pdf->Ln(3);
        $h = 3.5;
        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(90,$h,'SISTEM PENDIDIKAN TINGGI DI INDONESIA',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->SetFont('dinpromediumitalic','',9);
        $pdf->Cell(90,$h,'Higher Edocation System In Indonesia',0,1,'L');

        $t = '
                Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.

                The standard chunk of Lorem Ipsum used since the 1500s is reproduced below for those interested. Sections 1.10.32 and 1.10.33 from "de Finibus Bonorum et Malorum" by Cicero are also reproduced in their exact original form, accompanied by English versions from the 1914 translation by H. Rackham.

                It is a long established fact that a reader will be distracted by the readable content of a page when looking at its layout. The point of using Lorem Ipsum is that it has a more-or-less normal distribution of letters, as opposed to using \'Content here, content here\', making it look like readable English. Many desktop publishing packages and web page editors now use Lorem Ipsum as their default model text, and a search for \'lorem ipsum\' will uncover many web sites still in their infancy. Various versions have evolved over the years, sometimes by accident, sometimes on purpose (injected humour and the like).
                ';

        $y2 = $pdf->GetY()+4;
        $pdf->SetFont('dinprolight','',9);
        $pdf->SetXY(10,$y2);
        $pdf->MultiCell(90, $h_fill, $t, 0, 'L',false);

        $pdf->SetFont('dinlightitalic','',9);
        $pdf->SetXY(110,$y2);
        $pdf->MultiCell(90, $h_fill, $t, 0, 'L',false);



    }

    private function SKPI_page5($pdf){
        // membuat halaman baru
        $pdf->SetMargins(10,3,10);

        $pdf->AddPage();
        $pdf->Ln(30);

        $R = 226;
        $G = 226;
        $B = 226;

        $h_fill= 3.5;
        $h_max = 242;
        $pdf->SetFillColor($R, $G, $B);

        $pdf->Cell(190,2,'','B',1,'L');
        $pdf->Ln(3);

        $h = 4.5;
        $pdf->SetFont('dinpromedium','',11);
        $pdf->Cell(190,$h,'05. KERANGKA KUALIFIKASI NASIONAL INDONESIA (KKNI)',0,1,'L');
        $pdf->SetFont('dinlightitalic','',10);
        $pdf->Cell(190,$h,'05. Indonesian National Qualification Framework',0,1,'L');

    }

    private function SKPI_page6($pdf){
        // membuat halaman baru
        $this->newPageSKPI($pdf);

        $R = 226;
        $G = 226;
        $B = 226;

        $h_fill= 3.5;
        $h_max = 242;
        $pdf->SetFillColor($R, $G, $B);

        $pdf->Cell(190,2,'','B',1,'L');
        $pdf->Ln(3);

        $h = 4.5;
        $pdf->SetFont('dinpromedium','',11);
        $pdf->Cell(190,$h,'06. PENGESAHAN SKPI',0,1,'L');
        $pdf->SetFont('dinlightitalic','',10);
        $pdf->Cell(190,$h,'06. SKPI Legalization',0,1,'L');


        $h = 4;
        $pdf->Ln(15);
        $pdf->SetFont('dinprolight','',12);
        $pdf->Cell(190,$h,'JAKARTA, 20 DESEMBER 2019',0,1,'L');
        $pdf->SetFont('dinlightitalic','',10);
        $pdf->Cell(190,$h,'Jakarta, December 20, 2019',0,1,'L');

        $pdf->Ln(20);
        $pdf->SetFont('dinpromedium','',12);
        $pdf->Cell(190,$h,'NANDANG MULYADI SO, SE, S.KOM, MM, MBA, PH.D',0,1,'L');
        $pdf->Ln(2);
        $pdf->SetFont('dinprolight','',10);
        $pdf->Cell(190,$h,'DEKAN SEKOLAH MANAJEMENT PODOMORO UNIVERSITY',0,1,'L');
        $pdf->SetFont('dinlightitalic','',9);
        $pdf->Cell(190,$h,'Dean School of Business Management',0,1,'L');

        $pdf->Ln(4);
        $pdf->SetFont('dinprolight','',10);
        $pdf->Cell(190,$h,'NOMOR INDUK PEGAWAI : 2017090',0,1,'L');
        $pdf->SetFont('dinlightitalic','',9);
        $pdf->Cell(190,$h,'Employee ID Number',0,1,'L');

        $pdf->Ln(7);
        $pdf->Cell(190,2,'','B',1,'L');
        $pdf->Ln(7);


        $h = 3.5;
        $pdf->SetFont('dinpromedium','',9);
        $pdf->Cell(90,$h,'CATATAN RESMI',0,0,'L');
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->SetFont('dinpromediumitalic','',9);
        $pdf->Cell(90,$h,'Official Notes',0,1,'L');

        $this->loadMultyCellWithNumber($pdf,4);

        $pdf->Ln(15);
        $pdf->SetFont('dinpromedium','',8);
        $pdf->Cell(130,$h,'',0,0,'L');
        $pdf->Cell(60,$h,'ALAMAT',0,1,'L');
        $pdf->SetFont('dinpromediumitalic','',7);
        $pdf->Cell(130,$h,'',0,0,'L');
        $pdf->Cell(60,$h,'Contact Details',0,1,'L');


        $pdf->Ln(3);
        $pdf->SetFont('dinprolight','',8);
        $pdf->Cell(130,$h,'',0,0,'L');
        $pdf->Cell(60,$h,'UNIVERSITAS AGUNG PODOMORO',0,1,'L');
        $pdf->SetFont('dinlightitalic','',7);
        $pdf->Cell(130,$h,'',0,0,'L');
        $pdf->Cell(60,$h,'Agung Podomoro University',0,1,'L');

        $alamat = 'Located in: Central Park
Address: Jl. Letjen S. Parman No.28, RT.12/RW.6, Tj. Duren Sel., Grogol petamburan, Kota Jakarta Barat, Daerah Khusus Ibukota Jakarta 11470
Province: Jakarta
Founded: 2014
Phone: (021) 29200456';

        $y = $pdf->GetY()+2;
        $pdf->SetFont('dinprolight','',9);
        $pdf->SetXY(140,$y);
        $pdf->MultiCell(60, $h_fill, $alamat, 0, 'L',false);
//        $h = 3.5;
//        $pdf->SetFont('dinpromedium','',9);
//        $pdf->Cell(120,$h,'CATATAN RESMI',1,0,'L');
//        $pdf->Cell(10,$h,'',1,0,'L');
//        $pdf->SetFont('dinpromediumitalic','',9);
//
//        $pdf->SetFont('dinpromedium','',9);
//        $pdf->Cell(60,$h,'UNIVERSITAS AGUNG PODOMORO',1,1,'L');

    }

    private function minSpaceSKPI($pdf,$h,$color,$R,$G,$B){

        $pdf->SetFillColor($R,$G,$B);
        $pdf->Cell(90,$h,'',0,0,'L',$color);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'',0,1,'L',$color);
    }

    private function minSpaceSKPICustom($pdf,$h,$color,$R,$G,$B){

        $pdf->SetFillColor($R,$G,$B);
        $pdf->Cell(90,$h,'',0,0,'L',$color);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(90,$h,'',0,1,'L');
    }

    private function loadMultyCellWithNumber($pdf,$lengthData){

        $h_fill= 3.5;
        $h_max = 270;

        $tx = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ev';

        $tx2 = 'Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry\'s standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting';

        $tx3 = 'Contrary to popular belief, Lorem Ipsum is not simply random text. It has roots in a piece of classical Latin literature from 45 BC, making it over 2000 years old. Richard McClintock, a Latin professor at Hampden-Sydney College in Virginia, looked up one of the more obscure Latin words, consectetur, from a Lorem Ipsum passage, and going through the cites of the word in classical literature, discovered the undoubtable source. Lorem Ipsum comes from sections 1.10.32 and 1.10.33 of "de Finibus Bonorum et Malorum" (The Extremes of Good and Evil) by Cicero, written in 45 BC. This book is a treatise on the theory of ethics, very popular during the Renaissance. The first line of Lorem Ipsum, "Lorem ipsum dolor sit amet..", comes from a line in section 1.10.32.';

//        $lengthData = 7;

        $lineBreakLeft = 0;
        $lineBreakRight = 0;

        $lineBreakLeft2 = 0;
        $lineBreakRight2 = 0;

        $no_left = 1;
        // Bagian Kiri
        $y_left = $pdf->GetY()+5;
        $y_right = $pdf->GetY()+5;
        for($i=0;$i<$lengthData;$i++){
            $nb=0;
            $t = ($i%2==0) ? $tx : $tx3 ;
            $t1 = substr($t,0,280);

            $nb =max($nb,$pdf->NbLines(83,$t1));
            $h=$h_fill*$nb;

            if($y_left+$h > $h_max){
                $lineBreakLeft = $i;
                break;
            }

            // Indo
            $pdf->SetFont('dinprolight','',9);
            $pdf->SetXY(10,$y_left);
            $pdf->MultiCell(7, $h_fill, ($no_left++), 0, 'C',false);
            $pdf->SetXY(17,$y_left);
            $pdf->MultiCell(83, $h_fill, $t1, 0, 'L',false);
            $y_left = $y_left+$h+1;

        }

        $no_right = 1;
        for($i=0;$i<$lengthData;$i++){
            $nb=0;
            $t = ($i%2==0) ? $tx : $tx2 ;
            $t1 = substr($t,0,280);

            $nb =max($nb,$pdf->NbLines(83,$t1));
            $h=$h_fill*$nb;

            if($y_right+$h > $h_max){
                $lineBreakRight = $i;
                break;
            }

            // Eng
            $pdf->SetFont('dinlightitalic','',9);
            $pdf->SetXY(110,$y_right);
            $pdf->MultiCell(7, $h_fill, ($no_right++), 0, 'C',false);
            $pdf->SetXY(117,$y_right);
            $pdf->MultiCell(83, $h_fill, $nb.' - '.$t1, 0, 'L',false);
            $y_right = $y_right+$h+1;
        }


        // Cek apakah masih ada kelanjutannya atau tidak untuk page 2
        if(($lineBreakLeft !=0 || $lineBreakRight!=0) && ($lineBreakLeft < $lengthData || $lineBreakRight < $lengthData)){
            $this->newPageSKPI($pdf);

            $y_left = $pdf->GetY()+5;
            $y_right = $pdf->GetY()+5;
            $pdf->Cell(190,2,'','B',1,'L');

            if($lineBreakLeft !=0 && $lineBreakLeft < $lengthData){
                // Looping sekali lagi
                // Bagian Kiri
                for($i2=$lineBreakLeft;$i2<$lengthData;$i2++){
                    $nb=0;
                    $t = ($i2%2==0) ? $tx : $tx3 ;
                    $t1 = substr($t,0,280);

                    $nb =max($nb,$pdf->NbLines(83,$t1));
                    $h=$h_fill*$nb;

                    if($y_left+$h > $h_max){
                        $lineBreakLeft2 = $i2;
                        break;
                    }

                    // Indo
                    $pdf->SetFont('dinprolight','',9);
                    $pdf->SetXY(10,$y_left);
                    $pdf->MultiCell(7, $h_fill, ($no_left++), 0, 'C',false);
                    $pdf->SetXY(17,$y_left);
                    $pdf->MultiCell(83, $h_fill, $t1, 0, 'L',false);
                    $sp = ($i2==$lineBreakLeft) ? 4 : 1;
                    $y_left = $y_left+$h+$sp;
                }
            }

            if($lineBreakRight!=0 && $lineBreakRight < $lengthData){
                // Bagian Kanan
                for($i2=$lineBreakRight;$i2<$lengthData;$i2++){
                    $nb=0;
                    $t = ($i2%2==0) ? $tx : $tx3 ;
                    $t1 = substr($t,0,280);

                    $nb =max($nb,$pdf->NbLines(83,$t1));
                    $h=$h_fill*$nb;

                    if($y_right+$h > $h_max){
                        $lineBreakRight2 = $i2;
                        break;
                    }
                    // Eng
                    $pdf->SetFont('dinlightitalic','',9);
                    $pdf->SetXY(110,$y_right);
                    $pdf->MultiCell(7, $h_fill, ($no_right++), 0, 'C',false);
                    $pdf->SetXY(117,$y_right);
                    $pdf->MultiCell(83, $h_fill, $t1, 0, 'L',false);
                    $sp = ($i2==$lineBreakRight) ? 4 : 1;
                    $y_right = $y_right+$h+$sp;
                }
            }


        }


        // Cek apakah masih ada kelanjutannya atau tidak untuk page 3
        if(($lineBreakLeft2 !=0 || $lineBreakRight2!=0) && ($lineBreakLeft2 < $lengthData || $lineBreakRight2 < $lengthData)){
            $this->newPageSKPI($pdf);

            $y_left = $pdf->GetY()+5;
            $y_right = $pdf->GetY()+5;
            $pdf->Cell(190,2,'','B',1,'L');

            if($lineBreakLeft2 !=0 && $lineBreakLeft2 < $lengthData){
                // Looping sekali lagi
                // Bagian Kiri
                for($i3=$lineBreakLeft2;$i3<$lengthData;$i3++){

                    $t = ($i3%2==0) ? $tx : $tx3 ;
                    $t1 = substr($t,0,280);

                    // Indo
                    $pdf->SetFont('dinprolight','',9);
                    $pdf->SetXY(10,$y_left);
                    $pdf->MultiCell(7, $h_fill, ($no_left++), 0, 'C',false);
                    $pdf->SetXY(17,$y_left);
                    $pdf->MultiCell(83, $h_fill, $t1, 0, 'L',false);
                    $y_left = $y_left+1;
                }
            }

            if($lineBreakRight2!=0 && $lineBreakRight2 < $lengthData){
                // Bagian Kanan
                for($i3=$lineBreakRight2;$i3<$lengthData;$i3++){

                    $t = ($i3%2==0) ? $tx2 : $tx3 ;
                    $t1 = substr($t,0,280);
                    // Eng
                    $pdf->SetFont('dinlightitalic','',9);
                    $pdf->SetXY(110,$y_right);
                    $pdf->MultiCell(7, $h_fill, ($no_right++), 0, 'C',false);
                    $pdf->SetXY(117,$y_right);
                    $pdf->MultiCell(83, $h_fill, $t1, 0, 'L',false);
                    $y_right = $y_right+1;
                }
            }

        }

    }

    private function newPageSKPI($pdf){
        $pdf->SetMargins(10,3,10);
        $pdf->AddPage();
        $pdf->Ln(30);
    }

    public function export_kwitansi_formuliroffline()
    {
        $token = $this->input->post('token');
        $input = $this->getInputToken($token);
        $this->load->model('master/m_master');
        $taGet = $this->m_master->showData_array('db_admission.set_ta');
        $ta = substr($taGet[0]['Ta'], 2,2);
        $InputDate = $input['date'];
        $InputDate = explode('-', $InputDate);
        $bulanRomawi = $this->m_master->romawiNumber($InputDate[1]);

        $maxLen = 4;
        $NoKwitansi = $input['NumForm'];
        $aa = strlen($NoKwitansi);
        for ($i=0; $i < ( $maxLen - $aa ); $i++) {
            $NoKwitansi = '0'.$NoKwitansi;
        }

        // check tahun berdasarkan No_Ref
         $G_dt = $this->m_master->caribasedprimary('db_admission.formulir_number_global','FormulirCodeGlobal',$input['NoFormRef']);
         $YearsWR_MKT = substr($G_dt[0]['Years'], 2,2);

        // $nomorWr = $InputDate[0].' / '.$bulanRomawi.' / FRM'.' / '.'MKT-PU-'.$ta.' / '.$NoKwitansi;
        $nomorWr = $G_dt[0]['Years'].' / '.$bulanRomawi.' / FRM'.' / '.'MKT-PU-'.$YearsWR_MKT.' / '.$NoKwitansi;

            $fpdf = new Fpdf('L', 'mm', array(216, 140));
            //$fpdf = new Fpdf('P', 'mm', 'A4');
            //$fpdf = new Fpdf('P', 'mm', array(215,140));

            //$fpdf->SetMargins(0, 0);
            //$fpdf->SetDisplayMode('real');
            $fpdf->SetAutoPageBreak(true, 0);
            $fpdf->AddPage();

            //====================== WATERMARK ======================
            // if ($data['print'] > 2) {
            //     $fpdf->SetTextColor(209, 209, 209);
            //     $fpdf->SetFont('Arial', '', 100);
            //     $fpdf->Text(42, 70, 'C O P Y');
            // }
            // rahmat 23 Februari 2016
            //====================== Nomor Form ======================
            $fpdf->SetFont('Arial', 'B', 8);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->SetXY(1, 5);
            $fpdf->Cell(206, 7, 'FM-UAP/KEU-06-03', 0, 0, 'R');

            // End rahmat

            //====================== HEADER ======================
            $fpdf->SetFont('Arial', 'B', 18);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->SetXY(5, 5);
            $fpdf->ln(1);
            $fpdf->Cell(206, 7, 'TANDA TERIMA PEMBAYARAN', 0, 0, 'C');
            $fpdf->ln(7);
            // $fpdf->Line(65, 12.5, 163, 12.5);
            $fpdf->SetFont('Arial', '', 12);
            $fpdf->Cell(206, 15, 'Nomor: '.$nomorWr, 0, 0, 'C');

            // //====================== CONTENT ======================
            // if ($data['form'] == NULL) {
            //     $no_lbl = 'NIM';
            //     $no_txt = $data['nim'];
            // }
            // else {
            //     $no_lbl = 'No Form';
            //     $no_txt = $data['form'];
            // }

            // $fpdf->SetFont('Arial', '', 14);
            // $fpdf->Text(23, 28, 'Telah terima dari,');
            $no_lbl = 'No Form';

            $fpdf->SetFont('Arial', '', 11);
            $fpdf->Text(23, 36, $no_lbl);
            $fpdf->Text(23, 43, 'Nama lengkap');
            // $fpdf->Text(23, 50, 'Tlp / HP');
            // $fpdf->Text(23, 57, 'Jurusan');
            $fpdf->Text(23, 50, 'Pembayaran');
            $fpdf->Text(23, 57, 'Jumlah');
            $fpdf->Text(23, 64, 'Cara Pembayaran');
            $fpdf->Text(23, 71, 'Terbilang');

            $fpdf->Text(63, 36, ':');
            $fpdf->Text(63, 43, ':');
            // $fpdf->Tex63t(59, 50, ':');
            // $fpdf->Text(59, 57, ':');
            $fpdf->Text(63, 50, ':');
            $fpdf->Text(63, 57, ':');
            $fpdf->Text(63, 64, ':');
            $fpdf->Text(63, 71, ':');

            $terbilang = $this->m_master->moneySay($input['jumlah']).'Rupiah';
            $terbilang = trim(ucwords($terbilang));
            $fpdf->Text(69, 36, $input['NoFormRef'] );
            $fpdf->Text(69, 43, $input['namalengkap']);
            // $fpdf->Text(64, 50, $input['hp']);
            // $fpdf->Text(64, 57, $input['jurusan']);
            $fpdf->Text(69, 50, $input['pembayaran']);
            $fpdf->Text(69, 57, 'Rp '.number_format($input['jumlah'],2,',','.').',-');
            $fpdf->Text(69, 64, $input['jenis']);
            $fpdf->Text(69, 71, $terbilang);




            $fpdf->Line(69, 37, 195, 37);
            $fpdf->Line(69, 44, 195, 44);
            // $fpdf->Line(63, 51, 195, 51);
            // $fpdf->Line(63, 58, 195, 58);
            $fpdf->Line(69, 51, 195, 51);
            $fpdf->Line(69, 58, 195, 58);
            $fpdf->Line(69, 65, 195, 65);
            $fpdf->Line(69, 72, 195, 72);

            //====================== FOOTER / SIGN ======================
            $printDate = $this->m_master->getIndoBulan(date('Y-m-d'));
            $fpdf->SetFont('Arial', '', 14);
            $fpdf->SetXY(140, 92);
            $fpdf->Cell(60, 5, 'Jakarta, '.$printDate, 0, 0, 'C');
            $fpdf->SetXY(140, 116);
            $fpdf->SetFont('Arial', 'U', 14);
            $fpdf->Cell(60, 5, '( '.$this->session->userdata('Name').' )', 0, 0, 'C');

            //====================== FINISH ======================


        $fpdf->Output('receipt.pdf','I');
    }

    /* PR Budgeting */
    public function print_prdeparment()
    {
        try {
          $token = $this->input->post('token');
          $this->load->model('budgeting/m_budgeting');
          $this->load->model('budgeting/m_pr_po');
          $this->load->model('master/m_master');
          $this->load->model('budgeting/m_global');

          $input = $this->getInputToken($token);
          $PRCode = $input['PRCode'];
          $PRCodeReplace = str_replace('/', '-', $PRCode);
          $filename = '__'.$PRCodeReplace.'.pdf';

          $pr_create = $this->m_pr_po->GetPR_CreateByPRCode($PRCode);
          $pr_detail = $this->m_pr_po->GetPR_DetailByPRCode($PRCode);

          $fpdf = new Pdf_mc_table('L', 'mm', 'A4');
          $fpdf->SetMargins(10,10,10,10);
          $fpdf->AddPage();

          $x = 10;
          $y = 30;
          $FontIsianHeader = 8;
          $FontIsian = 7;
          $rect_h = 20;

          // Logo
          $fpdf->Image('./images/new_logo_pu.png',10,10,50);

          // note from finance
          $fpdf->Rect($x,$y,130,$rect_h);
          $fpdf->SetXY(($x+2),($y+2) );
          $fpdf->SetFont('Arial','b',$FontIsianHeader);
          $fpdf->Cell(0, 0, 'Notes From Finance : ', 0, 1, 'L', 0);

          // Header Purchase Requisition(PR)
          $hx = 180;
          $hy = 15;
          $fpdf->SetXY($hx,$hy);
          $fpdf->SetFont('Arial','b',10);
          $fpdf->Cell(0, 0, 'Purchase Requisition (PR)', 0, 1, 'L', 0);

          $hy += 5;
          $fpdf->SetXY($hx,$hy);
          $fpdf->SetFont('Arial','b',$FontIsianHeader);
          $fpdf->Cell(0, 0, 'No : '.$PRCode, 0, 1, 'L', 0);

          $DatePR = date("d M Y", strtotime($pr_create[0]['CreatedAt']));
          $hy += 5;
          $fpdf->SetXY($hx,$hy);
          $fpdf->SetFont('Arial','b',$FontIsianHeader);
          $fpdf->Cell(0, 0, 'Date : '.$DatePR, 0, 1, 'L', 0);

          // Department & Post Budget
          $Department = $this->m_budgeting->SearchDepartementBudgeting($pr_create[0]['Departement']);
          $Department = $Department[0]['Code'];
          $hx = 150;
          $hy = 30;
          $fpdf->Rect($hx,$hy,130,$rect_h);
          $hx = $hx + 2;
          $hy = $hy + 2;
          $fpdf->SetXY($hx,$hy);
          $fpdf->SetFont('Arial','b',$FontIsianHeader);
          $fpdf->Cell(0, 0, 'Department : '.$Department, 0, 1, 'L', 0);

          // print_r($pr_detail);die();
          $arr_postName = [];
          for ($i=0; $i < count($pr_detail); $i++) {
                $PostName = $pr_detail[$i]['NameHeadAccount'].'['.$pr_detail[$i]['NameDepartement'].']';
                $bool = true;
                for ($j=0; $j < count($arr_postName); $j++) {
                    if ($PostName == $arr_postName[$j]) {
                        $bool = false;
                        break;
                    }
                }

                if ($bool) {
                    $arr_postName[] = $PostName;
                }

          }

          $strpostname = implode(',',$arr_postName);

          $hy += 5;
          $fpdf->SetXY($hx,$hy);
          $fpdf->SetFont('Arial','b',$FontIsianHeader);
          $fpdf->Cell(0, 0, 'Post Budget : '.$strpostname, 0, 1, 'L', 0);

          // make table
            $border = 1;
            // header
            $w_no = 8;
            $w_smt = 8;
            $w_desc = 35;
            $w_spec = 55;
            $w_need = 30;
            $w_date_needed = 25;
            $w_pph = 25;
            $w_qty = 22;
            $w_pricest = 35;
            $w_totalammount = 35;
            $h=4.4;
            $y += $rect_h+2;
            $fpdf->SetXY($x,$y);
            $fpdf->SetFillColor(255, 255, 255);
             $fpdf->Cell($w_no,$h,'No.',$border,0,'C',true);
             $fpdf->Cell($w_desc,$h,'Item',$border,0,'C',true);
             $fpdf->Cell($w_spec,$h,'Specification',$border,0,'C',true);
             $fpdf->Cell($w_date_needed,$h,'Date Need',$border,0,'C',true);
             $fpdf->Cell($w_need,$h,'Description',$border,0,'C',true);
             $fpdf->Cell($w_qty,$h,'Quantity',$border,0,'C',true);
             $fpdf->Cell($w_pph,$h,'PPN',$border,0,'C',true);
             $fpdf->Cell($w_pricest,$h,'Price Estimated',$border,0,'C',true);
             $fpdf->Cell($w_totalammount,$h,'Total Amount',$border,1,'C',true);

             // content
              $no = 1;
              $fpdf->SetFont('Arial','',$FontIsian);
              $total = 0;
              $fpdf->SetWidths(array($w_no,$w_desc,$w_spec,$w_date_needed,$w_need,$w_qty,$w_pph,$w_pricest,$w_totalammount));
              $fpdf->SetLineHeight(5);
              $fpdf->SetAligns(array('C','L','L','C','L','C','C','C','C'));
             for ($i=0; $i < count($pr_detail); $i++) {

                $DetailCatalog = (array) json_decode($pr_detail[$i]['DetailCatalog']);
                $Spec = '';
                $arr = array();
                foreach ($DetailCatalog as $key => $value) {
                    $arr[] = $key.' : '.$value;
                }

                $Spec = implode(',', $arr);
                if ($pr_detail[$i]['Spec_add'] != '' || $pr_detail[$i]['Spec_add'] != null) {
                   $Spec = implode(',', $arr)."\n".$pr_detail[$i]['Spec_add'];
                }


                $DateNeeded = date("d M Y", strtotime($pr_detail[0]['DateNeeded']));
                // if ($pr_detail[$i]['Need'] != '' || $pr_detail[$i]['Need'] != null) {
                //     $DateNeeded .= "\n".'Need : '.$pr_detail[$i]['Need'];
                // }

                $UnitCost = 'Rp '.number_format($pr_detail[$i]['UnitCost'],2,',','.');
                $Subtotal= 'Rp '.number_format($pr_detail[$i]['SubTotal'],2,',','.');
                $fpdf->Row(array(
                   $no,
                   $pr_detail[$i]['Item'],
                   $Spec,
                   $DateNeeded,
                   $pr_detail[$i]['Need'],
                   $pr_detail[$i]['Qty'],
                   (int)$pr_detail[$i]['PPH'].'%',
                   $UnitCost,
                   $Subtotal,

                ));

                $total = $total + $pr_detail[$i]['SubTotal'];
                $no++;
                $y += $h;
             }

             $Max = 5;
             $h=4.4;
             for ($i=0; $i <$Max - count($pr_detail) ; $i++) {
                 $fpdf->Cell($w_no,$h,'' ,$border,0,'C',true);
                 $fpdf->Cell($w_desc,$h,'',$border,0,'C',true);
                 $fpdf->Cell($w_spec,$h,'',$border,0,'L',true);
                 $fpdf->Cell($w_date_needed,$h,'',$border,0,'C',true);
                 $fpdf->Cell($w_need,$h,'',$border,0,'C',true);
                 $fpdf->Cell($w_qty,$h,'',$border,0,'C',true);
                 $fpdf->Cell($w_pph,$h,'',$border,0,'C',true);
                 $fpdf->Cell($w_pricest,$h,'',$border,0,'C',true);
                 $fpdf->Cell($w_totalammount,$h,'',$border,1,'C',true);
                 $y += $h;
             }

             $y = $fpdf->GetY();
             $x = $x +$w_no+$w_desc+$w_pph+$w_spec;
             $totAfterPPN = $total;
             $totAfterPPN= 'Rp '.number_format($totAfterPPN,2,',','.');
             $total= 'Rp '.number_format($total,2,',','.');
             $fpdf->SetXY($x,$y);
             $fpdf->Cell(($w_date_needed+$w_need+$w_qty+$w_pricest),$h,'Total',$border,0,'C',true);
             $fpdf->Cell($w_totalammount,$h,$total,$border,1,'C',true);
             // total setelah ppn
             // $y += $h;
             // $fpdf->SetXY($x,$y);
             // $fpdf->SetFillColor(255, 255, 255);
             // $fpdf->Cell(($w_date_needed+$w_qty+$w_pricest),$h,'Total setelah PPN ',$border,0,'C',true);
             // $fpdf->Cell($w_totalammount,$h,$totAfterPPN,$border,1,'C',true);

             // Notes
             $y += 10;
             $x = 10;
             $JsonStatus = (array) json_decode($pr_create[0]['JsonStatus'],true);
             $JsonStatus = $this->m_global->FilteringDoubleApproval($JsonStatus);

             $maxWrec = 210;
             $Wrec = $maxWrec - ( (count($JsonStatus)) * 5 );
             $fpdf->Rect($x,$y,$Wrec,15);
             $fpdf->SetXY(($x+2),($y+2) );
             $fpdf->SetFont('Arial','b',$FontIsianHeader);
             $fpdf->Cell(0, 0, 'Notes : ', 0, 0, 'L', 0);

             $fpdf->SetXY(($x+2),($y+5) );
             $fpdf->SetFont('Arial','',$FontIsian);
             $fpdf->MultiCell(($Wrec - 10), 3, $pr_create[0]['Notes'], 0, 1, 'L', 0);

             // signature
             $y = $fpdf->getY()+10;
             // $x = $Wrec + 20;
             $w_requested = 20;
             $w_approved = 35;
             $h_signature = 15;
             $fpdf->SetXY($x,$y);
             $fpdf->SetFont('Arial','',$FontIsian);
             $fpdf->SetFillColor(226, 226, 226);
             // $fpdf->Cell($w_requested,$h,'Requested By',$border,0,'C',true);

             for ($i=0; $i < count($JsonStatus); $i++) {
                 if ($JsonStatus[$i]['Visible'] == 'Yes') {
                      $fpdf->Cell($w_approved,$h,$JsonStatus[$i]['NameTypeDesc'],$border,0,'C',true);
                  }

             }

             $y += $h;
             $fpdf->SetXY($x,$y);
             $fpdf->SetFillColor(255, 255, 255);
             // $fpdf->Cell($w_requested,$h_signature,'',$border,0,'C',true);
             $Sx = $x;
             for ($i=0; $i < count($JsonStatus); $i++) {
                if ($JsonStatus[$i]['Visible'] == 'Yes') {
                      $Approver = $JsonStatus[$i]['NIP'];
                      $G_CreatedBy = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Approver);
                      $Signatures = $G_CreatedBy[0]['Signatures'];
                      // print_r($Signatures);
                     if (file_exists('./uploads/signature/'.$Signatures)) {
                        $fpdf->Cell($w_approved,$h_signature,'',$border,0,'C',true);
                        $fpdf->Image('./uploads/signature/'.$Signatures,$Sx,$fpdf->GetY(),$w_approved,$h_signature);
                     }
                     else
                     {
                        $fpdf->Cell($w_approved,$h_signature,'',$border,0,'C',true);
                     }

                     $Sx += $w_approved;

                }
             }
             // die();
             $CreatedBy = $pr_create[0]['CreatedBy'];
             $G_CreatedBy = $this->m_master->caribasedprimary('db_employees.employees','NIP',$CreatedBy);
             $NameRequester = $G_CreatedBy[0]['Name'];
             $y += $h_signature;
             $fpdf->SetXY($x,$y);
             $fpdf->SetFillColor(255, 255, 255);
             // $fpdf->Cell($w_requested,$h,$NameRequester,$border,0,'C',true);
             for ($i=0; $i < count($JsonStatus); $i++) {
                if ($JsonStatus[$i]['Visible'] == 'Yes') {
                 $Approver = $JsonStatus[$i]['NIP'];
                 $G_CreatedBy = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Approver);
                 $NameApprover = $G_CreatedBy[0]['Name'];
                 $fpdf->Cell($w_approved,$h,$NameApprover,$border,0,'C',true);
                }
             }

             $y += $h;
             $fpdf->SetXY($x,$y);
             $fpdf->SetFont('Arial','b',$FontIsianHeader);
             $fpdf->SetFillColor(255, 255, 255);
             // $fpdf->Cell($w_requested,$h,'Date : ',$border,0,'L',true);
             for ($i=0; $i < count($JsonStatus); $i++) {
                if ($JsonStatus[$i]['Visible'] == 'Yes') {
                 $ApproveAt = $JsonStatus[$i]['ApproveAt'];
                  $ApproveAt = date("d M Y", strtotime($ApproveAt));
                 $fpdf->Cell($w_approved,$h,'Date : '.$ApproveAt,$border,0,'L',true);
                }
             }


             // watermark
                // if ($pr_create[0]['Status'] ==  2) {
                //     $fpdf->SetFont('Arial','B',50);
                //     $fpdf->SetTextColor(255,192,203);
                //     $fpdf->RotatedText(35,190,'Approve',35);
                // }
                // elseif ($pr_create[0]['Status'] ==  3) {
                //     $fpdf->SetFont('Arial','B',50);
                //     $fpdf->SetTextColor(255,192,203);
                //     $fpdf->RotatedText(35,190,'Reject',35);
                // }


             // show image in the next page
                 $arr_image = array();
                 for ($i=0; $i < count($pr_detail); $i++) {
                     if ($pr_detail[$i]['UploadFile'] == '' || $pr_detail[$i]['UploadFile'] == null) {
                         $PhotoCatalog = $pr_detail[$i]['Photo'];
                         if ($PhotoCatalog != '' && $PhotoCatalog != null) {
                             $url_arr = array(
                                'url' => './uploads/budgeting/catalog/'.$PhotoCatalog,
                                'Name' => $PhotoCatalog,
                             );
                             // search name is exist
                             $bool = false;
                             for ($j=0; $j < count($arr_image); $j++) {
                                 $Name = $arr_image[$j]['Name'];
                                 if ($Name == $url_arr['Name']) {
                                     $bool = true;
                                     break;
                                 }
                             }
                             if (!$bool) {
                                 $arr_image[] = $url_arr;
                             }

                         }
                     }
                     else
                     {
                        $Photo = $pr_detail[$i]['UploadFile'];
                        $Photo = (array)json_decode($Photo,true);

                        for ($ll=0; $ll < count($Photo); $ll++) {
                            $url_arr = array(
                               'url' => './uploads/budgeting/pr/'.$Photo[$ll],
                               'Name' => $Photo[$ll],
                            );
                            // search name is exist
                            $bool = false;
                            for ($j=0; $j < count($arr_image); $j++) {
                                $Name = $arr_image[$j]['Name'];
                                if ($Name == $url_arr['Name']) {
                                    $bool = true;
                                    break;
                                }
                            }
                            if (!$bool) {
                                $arr_image[] = $url_arr;
                            }
                        }

                     }
                 }
                 // end show image in the next page

            // for ($i=0; $i < count($arr_image); $i++) {
            //     $fpdf->AddPage();
            //     $fpdf->Image($arr_image[$i]['url'],100,40,100);
            // }

          $fpdf->Output($filename,'I');


        } catch (Exception $e) {
            // handling orang iseng
            echo $e;
            // echo '{"status":"999","message":"jangan iseng :D"}';
        }
    }

    /* End PR Budgeting */

    public function PrintIDCard()
    {
        try {
          $token = $this->input->post('token');
          $input = $this->getInputToken($token);
          $input = (array) json_decode(json_encode($input),true);
          $customlayout=array('53.98','85.60');
          $pdf = new FPDF('P','mm',$customlayout);
          for ($i=0; $i < count($input); $i++) {
             $pdf->AddFont('dinproExpBold','','dinproExpBold.php');
             $pdf->AddPage();
             $pdf->SetAutoPageBreak(true, 0);

             // $ConverToPath = function($PathURL)
             // {
             //    $rs = '';
             //    $values = parse_url($PathURL);
             //    $Path = $values['path'];
             //    $Path = str_replace('/puis', '', $Path);
             //    $rs = $Path;
             //    return $rs;
             // };
             // $pathFileImage = $ConverToPath($input[$i]['PathFoto']);
             // $pdf->Image(FCPATH.$pathFileImage,15.5,23,23);

             $pathFileImage = str_replace('https', 'http', $input[$i]['PathFoto']);
             $pdf->Image($pathFileImage,15.5,23,23);

             // $template_format = ($input[$i]['type'] == 'student') ? base_url('images/id_mhs.png') : base_url('images/id_emp.png');
             // $template_format = ($input[$i]['type'] == 'student') ? base_url('images/id_mhs2.png') : base_url('images/id_emp2.png');
             $template_format = ($input[$i]['type'] == 'student') ? './images/id_mhs2.png' : './images/id_emp2.png';
             $pdf->Image($template_format,0,0,54);
             $pdf->SetXY(10,54.5);
             $pdf->SetFont('dinproExpBold','',12);
             $pdf->SetTextColor(0, 0, 0);
             $pdf->Cell(0,5,$input[$i]['Name'],0,0,'C');
             $pdf->SetXY(10,59.5);
             $pdf->SetFont('dinpromedium','',10);
             // $pdf->SetTextColor(255,255,255);
             $pdf->Cell(0,5,$input[$i]['NPM'],0,0,'C');
             $pdf->SetXY(10,63);
             $pdf->SetFont('dinpromedium','',5);
             // $pdf->SetTextColor(255,255,255);
             // $pdf->Cell(0,5,$input[$i]['email'],0,0,'C');
          }

         $pdf->Output('I','ID_Card.pdf');


        } catch (Exception $e) {
            // handling orang iseng
            echo $e;
            // echo '{"status":"999","message":"jangan iseng :D"}';
        }
    }

    public function create_idCard(){

        $customlayout=array('53.98','85.60');

        $pdf = new FPDF('P','mm',$customlayout);
        $pdf->AddFont('dinproExpBold','','dinproExpBold.php');

        $pdf->AddPage();

        $pdf->SetAutoPageBreak(true, 0);


        $pdf->Image(base_url('images/45.png'),10,26,34);
        $pdf->Image(base_url('images/id_emp.png'),0,0,54);

        $pdf->SetXY(10,63);
        $pdf->SetFont('dinproExpBold','',14);
        $pdf->SetTextColor(247, 194, 74);
        $pdf->Cell(0,5,'Nandang Mulyadi',0,0,'C');

        $pdf->SetXY(10,69);
        $pdf->SetFont('dinpromedium','',12);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(0,5,'2017090',0,0,'C');

        $pdf->SetXY(10,73);
        $pdf->SetFont('dinpromedium','',5);
        $pdf->SetTextColor(255,255,255);
        $pdf->Cell(0,5,'nandang.mulyadi@podomorouniversity.ac.id',0,0,'C');

        $pdf->Output('I','Monitoring_Attendance_Lecturer.pdf');
    }


    public function view_format_laporan($token){

        $data_arr = $this->getInputToken($token);

        $listID = $data_arr['listID'];
        $stat_id = $data_arr['stat_id'];

        $dataLaporan = $this->db->query('SELECT a.Isi_laporan, b.Nama_format
                   FROM db_research.litabmas_isi_laporan AS a
                   LEFT JOIN db_research.master_format_laporan AS b ON (a.Jenis_format = b.ID)
                   WHERE a.ID = "'.$listID.'" AND a.Csf = "'.$stat_id.'" ')->result_array();

        if($stat_id = "2") {

            if(count($dataLaporan)>0){

                $d = $dataLaporan[0];

                $pdf = new FPDF('P','mm','A4');
                $pdf->AddPage();

                $pdf->Ln(7);
                $pdf->SetFont('Arial','',14);
                $pdf->Cell(0,5,$d['Nama_format'],0,1,'C');

                $pdf->Ln(15);
                $pdf->SetFont('Arial','',12);
                $pdf->Cell(0,5,$d['Isi_laporan']);

                $pdf->SetAutoPageBreak(true, 0);
                $pdf->Output('I','preview_format_laporan.pdf');

            }
            else {
                echo 'data not yet';
            }
        }
        else {

            if(count($dataLaporan)>0){

                $d = $dataLaporan[0];

                $pdf = new FPDF('P','mm','A4');
                $pdf->AddPage();

                $pdf->Ln(7);
                $pdf->SetFont('Arial','',14);
                $pdf->Cell(0,5,$d['Nama_format'],0,1,'C');

                $pdf->Ln(15);
                $pdf->SetFont('Arial','',12);
                $pdf->Cell(0,5,$d['Isi_laporan']);

                $pdf->SetAutoPageBreak(true, 0);
                $pdf->Output('I','preview_format_laporan.pdf');

            }
            else {
                echo 'data not yet';
            }

        }

    }

    public function view_format_laporan_pkm($token){

        $data_arr = $this->getInputToken($token);

        $listID = $data_arr['listID'];
        $stat_id = $data_arr['stat_id'];

        $dataLaporan = $this->db->query('SELECT a.Isi_laporan, b.Nama_format
                   FROM db_research.pengabdian_masyarakat_isi_laporan AS a
                   LEFT JOIN db_research.master_format_laporan AS b ON (a.Jenis_format = b.ID)
                   WHERE a.ID = "'.$listID.'" AND a.Csf = "'.$stat_id.'" ')->result_array();

        if($stat_id = "2") {

            if(count($dataLaporan)>0){

                $d = $dataLaporan[0];

                $pdf = new FPDF('P','mm','A4');
                $pdf->AddPage();

                $pdf->Ln(7);
                $pdf->SetFont('Arial','',14);
                $pdf->Cell(0,5,$d['Nama_format'],0,1,'C');

                $pdf->Ln(15);
                $pdf->SetFont('Arial','',12);
                $pdf->Cell(0,5,$d['Isi_laporan']);

                $pdf->SetAutoPageBreak(true, 0);
                $pdf->Output('I','preview_format_laporan.pdf');

            }
            else {
                echo 'data not yet';
            }
        }
        else {

            if(count($dataLaporan)>0){

                $d = $dataLaporan[0];

                $pdf = new FPDF('P','mm','A4');
                $pdf->AddPage();

                $pdf->Ln(7);
                $pdf->SetFont('Arial','',14);
                $pdf->Cell(0,5,$d['Nama_format'],0,1,'C');

                $pdf->Ln(15);
                $pdf->SetFont('Arial','',12);
                $pdf->Cell(0,5,$d['Isi_laporan']);

                $pdf->SetAutoPageBreak(true, 0);
                $pdf->Output('I','preview_format_laporan.pdf');

            }
            else {
                echo 'data not yet';
            }

        }

    }


    public function suratMengajar($token){


        $data_arr = $this->getInputToken($token);

        $SemesterID = $data_arr['SemesterID'];
        $NIP = $data_arr['NIP'];

        // Cek urutan
        $dataQue = $this->db->limit(1)->get_where('db_employees.surat_mengajar',array(
            'SemesterID' => $SemesterID,
            'NIP' => $NIP
        ))->result_array();


        if(count($dataQue)>0){
            $Queue = $dataQue[0]['Queue'];
        } else {
            $dataTotalQue = $this->db->get_where('db_employees.surat_mengajar',array(
                'SemesterID' => $SemesterID
            ))->result_array();

            $Queue = count($dataTotalQue) + 1;

            $arrInsQue = array(
                'Queue' => $Queue,
                'SemesterID' => $SemesterID,
                'NIP' => $NIP
            );

            $this->db->insert('db_employees.surat_mengajar',$arrInsQue);

        }

        $dataLect = $this->db->query('SELECT em.*, ps.Name AS ProdiName
                                                  FROM db_employees.employees em
                                                  LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                                                  WHERE em.NIP = "'.$NIP.'" LIMIT 1')->result_array();

        // Get Semester
        $dataSmt = $this->db->query('SELECT s.Name, ay.kuliahStart FROM db_academic.semester s LEFT
                                            JOIN db_academic.academic_years ay ON (ay.SemesterID = s.ID)
                                            WHERE s.ID = "'.$SemesterID.'" ')->result_array();

        if(count($dataLect)>0){

            $d = $dataLect[0];

            $dateGen = ($dataSmt[0]['kuliahStart']!='' && $dataSmt[0]['kuliahStart']!=null)
                ? date('Y-m-d',strtotime('+14 day',strtotime($dataSmt[0]['kuliahStart'])))
                : '';

            $bln = ($dateGen!='') ? explode('-',$dateGen)[1] : '';
            $thn = ($dateGen!='') ? explode('-',$dateGen)[0] : '';

            // Get Mata kuliah
            $dataMK = $this->db->query('SELECT mk.Name, cd.TotalSKS AS Credit, ssc.Credit AS CreditResult FROM db_academic.schedule s
                                                  LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                  LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                  LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                  LEFT JOIN db_academic.schedule_share_credit ssc ON (ssc.ScheduleID = s.ID AND ssc.NIP = s.Coordinator)
                                                  WHERE s.SemesterID = "'.$SemesterID.'" AND s.Coordinator = "'.$NIP.'" GROUP BY s.ID
                                                  UNION ALL
                                                  SELECT mk2.Name, cd.TotalSKS AS Credit, ssc.Credit AS CreditResult FROM db_academic.schedule_details_course sdc2
                                                  LEFT JOIN db_academic.schedule s2 ON (s2.ID = sdc2.ScheduleID)
                                                  LEFT JOIN db_academic.mata_kuliah mk2 ON (mk2.ID = sdc2.MKID)
                                                  LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc2.CDID)
                                                  LEFT JOIN db_academic.schedule_team_teaching stt ON (sdc2.ScheduleID = stt.ScheduleID)
                                                  LEFT JOIN db_academic.schedule_share_credit ssc ON (ssc.ScheduleID = s2.ID AND ssc.NIP = stt.NIP)
                                                  WHERE s2.SemesterID = "'.$SemesterID.'" AND stt.NIP = "'.$NIP.'" GROUP BY s2.ID ')->result_array();


            // Get PHR -> 2.2
            $dataPHR = $this->db->limit(1)->select('NIP, Name, TitleAhead, TitleBehind, Signature')->get_where('db_employees.employees em',
        "(em.PositionMain = '2.2' OR em.PositionOther1 = '2.2' OR em.PositionOther2 = '2.2' OR em.PositionOther3 = '2.2') AND  em.StatusEmployeeID = '1'")->result_array();


            $NamePHR_a = (count($dataPHR)>0) ? trim($dataPHR[0]['TitleAhead']).' ' : '';
            $NamePHR_b = (count($dataPHR)>0) ? ' '.trim($dataPHR[0]['TitleBehind']) : '';
            $NamePHR = (count($dataPHR)>0) ? $NamePHR_a.''.trim($dataPHR[0]['Name']).''.$NamePHR_b : '';
            $NIPPHR = (count($dataPHR)>0) ? $dataPHR[0]['NIP'] : '';
            $Signature = (count($dataPHR)>0) ? $dataPHR[0]['Signature'] : '';



            $th = ($d['TitleAhead']!='' && $d['TitleAhead']!=null) ? trim($d['TitleAhead']).' ': '';
            $Name = $th.''.trim($d['Name']).' '.trim($d['TitleBehind']);

            $pdf = new FPDF('P','mm','A4');


            $pdf->AddPage();

            $pdf->SetAutoPageBreak(true, 0);
            $pdf->Image('./images/FA_letterhead_a4_r2.jpg',0,0,210);
            // $pdf->Image(base_url('images/FA_letterhead_a4_r2.jpg'),0,0,210);


            $pdf->Ln(30);
            $pdf->SetFont('Arial','B',13);
            $pdf->Cell(0,5,'SURAT TUGAS',0,1,'C');
            $pdf->SetFont('Arial','',11);
            $pdf->Ln(1);
            $dataNum = $this->m_rest->genrateNumberingString($Queue,3);
            $blnRomawi = $this->m_master->romawiNumber($bln);
            $pdf->Cell(0,5,'Nomor : '.$dataNum.'/UAP/SKU/'.$blnRomawi.'/'.$thn,0,1,'C');

            $pdf->Ln(17);
            $pdf->Cell(0,5,'Universitas Agung Podomoro menugaskan kepada :',0,1,'L');

            $pdf->Ln(7);
            $h = 5;
            $pdf->Cell(10,$h,'',0,0,'L');
            $pdf->Cell(30,$h,'Nama',0,0,'L');
            $pdf->Cell(5,$h,':',0,0,'C');
            $pdf->Cell(145,$h,$Name,0,1,'L');

            $pdf->Cell(10,$h,'',0,0,'L');
            $pdf->Cell(30,$h,'NIP',0,0,'L');
            $pdf->Cell(5,$h,':',0,0,'C');
            $pdf->Cell(145,$h,$NIP,0,1,'L');

            $pdf->Cell(10,$h,'',0,0,'L');
            $pdf->Cell(30,$h,'Program Studi',0,0,'L');
            $pdf->Cell(5,$h,':',0,0,'C');
            $pdf->Cell(145,$h,$d['ProdiName'],0,1,'L');

            $pdf->Ln(7);
            $pdf->Cell(0,5,'Sebagai pengajar pada Semester '.$dataSmt[0]['Name'].' untuk mata kuliah :',0,1,'L');

            $pdf->Ln(7);
            $h = 9;
            $pdf->SetFillColor(38, 75, 135);
            $pdf->SetTextColor(255, 255, 255);
            $pdf->Cell(10,$h,'No',1,0,'C',true);
            $pdf->Cell(125,$h,'Nama Mata Kuliah',1,0,'C',true);
            $pdf->Cell(20,$h,'SKS MK',1,0,'C',true);
            $pdf->Cell(20,$h,'SKS BKD',1,0,'C',true);
            $pdf->Cell(15,$h,'Sesi',1,1,'C',true);

            //Living the Family Business/Social Entrepreneurship Experience
            $pdf->SetTextColor(0, 0, 0);
            $h = 7;
            $pdf->SetFont('Arial','',9);

            $no=1;
            $isPageOne = true;
//            print_r($dataMK[0]);
//            exit();
//            foreach ($dataMK AS $item){
//                array_push($dataMK,$item);
//            }
            foreach ($dataMK AS $item){

                $Credit = ($item['CreditResult']!='' && $item['CreditResult']!=null) ? $item['CreditResult'] : '-' ;

                $y = $pdf->GetY();

                $pdf->Cell(10,$h,$no,1,0,'C');
                $pdf->Cell(125,$h,$item['Name'],1,0,'L');
                $pdf->Cell(20,$h,$item['Credit'],1,0,'C');
                $pdf->Cell(20,$h,$Credit,1,0,'C');
                $pdf->Cell(15,$h,'14',1,1,'C');

//                if($no<=8){
//
//                }

                if($y>=172){
                    $pdf->AddPage();

                    $pdf->SetAutoPageBreak(true, 0);
                    $pdf->Image('./images/FA_letterhead_a4_r2.jpg',0,0,210);
                    $pdf->Ln(25);
                }

                $no++;
            }


            $pdf->Ln(7);
            $pdf->SetFont('Arial','',11);
            $pdf->Cell(0,5,'Demikian Surat Tugas ini diberikan untuk dapat dilaksanakan sebagaimana mestinya. ',0,1,'L');


            $pdf->SetFont('Arial','',11);
            $y = $pdf->GetY()+20;


            $pdf->Image(base_url('images/cap.png'),130,$y+6,40);
            if($Signature!=''){
              $pdf->Image('./uploads/signature/'.$Signature,130,$y+6,40);
            }


            $pdf->SetXY(130,$y);
            $pdf->Cell(60,5,'Jakarta, '.$this->getDateIndonesian($dateGen),0,1,'L');
            $pdf->SetXY(130,$y+5);
            $pdf->SetFont('Arial','B',11);
            $pdf->Cell(60,5,'An Rektor',0,1,'L');

//            $logo = file_get_contents('uploads/signature/2617100.svg');
//            $pdf->MemImage($logo, 50, 30);
//            print_r($logo);die();

            $pdf->SetXY(130,$y+30);
            $pdf->Cell(60,5,$NamePHR,0,1,'L');
            $pdf->SetXY(130,$y+35);
            $pdf->Cell(60,5,'NIP : '.$NIPPHR,0,1,'L');


            $pdf->SetFont('Arial','',10);
            $pdf->SetXY(10,$y+45);
            $pdf->Cell(60,5,'Tembusan Yth.',0,1,'L');

            $pdf->SetXY(17,$y+51);
            $pdf->Cell(60,5,'1. Rektor',0,1,'L');

            $pdf->SetXY(17,$y+56);
            $pdf->Cell(60,5,'2. Kaprodi '.$d['ProdiName'],0,1,'L');


            $pdf->Output('I','Tugas_Mengajar.pdf');
        } else {
            echo 'data not yet';
        }




    }

    public function suratTugasKeluar($token){

        $data_arr = $this->getInputToken($token);

        $NIP = $data_arr['NIP'];
        $IDRequest = $data_arr['IDRequest'];

        $dataLect = $this->db->query('SELECT em.*, ps.Name AS ProdiName, fs.Name AS NameFak
                                                  FROM db_employees.employees em
                                                  LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                                                  LEFT JOIN db_academic.faculty fs ON (ps.FacultyID = fs.FacultyID)
                                                  WHERE em.NIP = "'.$NIP.'" LIMIT 1')->result_array();

        $dataRequest = $this->db->limit(1)->get_where('db_employees.request_document',array(
            'IDRequest' => $IDRequest,
            'NIP' => $NIP
        ))->result_array();


        if(count($dataRequest)>0){

            $d = $dataLect[0];

            $time1 = date('H:i', strtotime($dataRequest[0]['StartDate']));
            $time2x = date('H:i', strtotime($dataRequest[0]['EndDate']));

            $daftar_hari = array(
                'Sunday' => 'Minggu',
                'Monday' => 'Senin',
                'Tuesday' => 'Selasa',
                'Wednesday' => 'Rabu',
                'Thursday' => 'Kamis',
                'Friday' => 'Jumat',
                'Saturday' => 'Sabtu'
            );

            $namahari = date('l', strtotime($dataRequest[0]['StartDate']));
            $starthari =  $daftar_hari[''.$namahari];

            $namahari2 = date('l', strtotime($dataRequest[0]['EndDate']));
            $starthari2 =  $daftar_hari[''.$namahari2];

            if($starthari == $starthari2) {
                    $hariyax = date('l', strtotime($dataRequest[0]['StartDate']));
                    $hariday =  $daftar_hari[''.$hariyax];
            }else {
                    $hariday = $starthari.' - '.$starthari2;

            }

            $date1 = $this->getDateIndonesian($dataRequest[0]['StartDate']);
            $date2 = $this->getDateIndonesian($dataRequest[0]['EndDate']);

            if($date1 == $date2) {
                    $tanggalx = $this->getDateIndonesian($dataRequest[0]['StartDate']);
            }else {
                    $tanggalx = $date1.' s/d '.$date2;

            }

            if($time2x == '00:00') {
                $time2 = 'Selesai';
            }else {
                $time2 = date('H:i', strtotime($dataRequest[0]['EndDate']));

            }

            $DateConfirm = date('dd M yy', strtotime($dataRequest[0]['DateConfirm']));
            $description = $dataRequest[0]['DescriptionAddress'];
            $nametask = trim($dataRequest[0]['ForTask']);

            $dataEmploy = $this->db->limit(1)->select('Name,NIP,TitleAhead,TitleBehind')->get_where('db_employees.employees',array(
                'NIP' => $NIP
            ))->result_array();

            $Name_a = (count($dataEmploy)>0) ? trim($dataEmploy[0]['TitleAhead']).' ' : '';
            $Name_b = (count($dataEmploy)>0) ? ' '.trim($dataEmploy[0]['TitleBehind']) : '';
            $Name = (count($dataEmploy)>0) ? $Name_a.''.trim($dataEmploy[0]['Name']).''.$Name_b : '';
            $NIP = (count($dataEmploy)>0) ? $dataEmploy[0]['NIP'] : '';

            $dataPHR = $this->db->limit(1)->select('NIP, Name, TitleAhead, TitleBehind')->get_where('db_employees.employees',array('PositionMain'=>'2.2', 'StatusEmployeeID' => 1))->result_array();

            $NamePHR_a = (count($dataPHR)>0) ? trim($dataPHR[0]['TitleAhead']).' ' : '';
            $NamePHR_b = (count($dataPHR)>0) ? ' '.trim($dataPHR[0]['TitleBehind']) : '';
            $NamePHR = (count($dataPHR)>0) ? $NamePHR_a.''.trim($dataPHR[0]['Name']).''.$NamePHR_b : '';
            $NIPPHR = (count($dataPHR)>0) ? $dataPHR[0]['NIP'] : '';


        $bln = date('m',strtotime($dataRequest[0]['RequestDate']));
        $thn = date('Y',strtotime($dataRequest[0]['RequestDate']));

        // Get Number
        $dataNumbering = $this->db->query('SELECT count(*) as total FROM db_employees.request_document WHERE IDTypeFiles = 15 AND Year(RequestDate) = '.$thn .' and IDRequest <= '.$dataRequest[0]['IDRequest'])->result_array();

        $dataNum = $this->m_rest->genrateNumberingString($dataNumbering[0]['total'],3);

        $pdf = new FPDF('P','mm','A4');

        $pdf->AddPage();
        $pdf->SetAutoPageBreak(true, 0);

        $pdf->Image('./images/FA_letterhead_a4_r2.jpg',0,0,210);

        $pdf->Ln(30);
        $pdf->SetFont('Arial','B',13);
        $pdf->Cell(0,5,'SURAT TUGAS',0,1,'C');
        $pdf->SetFont('Arial','',10);
        $pdf->Ln(1);

        $blnRomawi = $this->m_master->romawiNumber($bln);
        $pdf->Cell(0,5,'Nomor : '.$dataNum.'/UAP/R/SKU/'.$blnRomawi.'/'.$thn,0,1,'C');

        $h = 5;
        $pdf->Ln(10);
        $pdf->Cell(0,$h,'Universitas Agung Podomoro menugaskan kepada : ',0,1,'L');


        $pdf->Ln(3);

        //$pdf->Cell(10,$h,'',0,0,'L');
        //$pdf->Cell(30,$h,'Nama',0,0,'L');
        //$pdf->Cell(5,$h,':',0,0,'C');
        //$pdf->Cell(145,$h,trim($NamePHR),0,1,'L');

        //$pdf->Cell(10,$h,'',0,0,'L');
        //$pdf->Cell(30,$h,'NIP',0,0,'L');
        //$pdf->Cell(5,$h,':',0,0,'C');
        //$pdf->Cell(145,$h,$NIPPHR,0,1,'L');

        //$pdf->Cell(10,$h,'',0,0,'L');
        //$pdf->Cell(30,$h,'Program Studi',0,0,'L');
        //$pdf->Cell(5,$h,':',0,0,'C');
        //$pdf->Cell(145,$h,$d['ProdiName'],0,1,'L');

        //$pdf->SetFont('Arial','B',11);
        //$pdf->Ln(3);
        //$pdf->Cell(0,$h,'Menugaskan Kepada : ',0,1,'C');
        //$pdf->Ln(3);

        $pdf->SetFont('Arial','',10);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(30,$h,'Nama',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(145,$h,trim($Name),0,1,'L');

        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(30,$h,'NIP',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(145,$h,$NIP,0,1,'L');

        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(30,$h,'Program Studi',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(145,$h,$d['ProdiName'],0,1,'L');

        //$pdf->SetFont('Arial','B',10);
        $pdf->Ln(3);
        //$pdf->MultiCell(145,5,$h,'Untuk menghadiri '.$nametask.' pada :',0,1,'L');
        $pdf->MultiCell(185, $h,$nametask.' pada :', 0, 'L',false);

        $pdf->Ln(3);
        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(30,$h,'Hari',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(145,$h,$hariday,0,1,'L');

        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(30,$h,'Tanggal',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        //$pdf->Cell(145,$h,$this->getDateIndonesian($dataRequest[0]['StartDate']).' s/d '.$this->getDateIndonesian($dataRequest[0]['EndDate']),0,1,'L');
        $pdf->Cell(145,$h,$tanggalx,0,1,'L');

        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(30,$h,'Waktu',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');
        $pdf->Cell(145,$h,$time1.' - '.$time2.'',0,1,'L');

        $pdf->Cell(10,$h,'',0,0,'L');
        $pdf->Cell(30,$h,'Tempat',0,0,'L');
        $pdf->Cell(5,$h,':',0,0,'C');

        $pdf->MultiCell(145,5,$description);
//        $pdf->Cell(145,$h,'Hotel Bidakara Jakarta, Ruang Birawa, Jl. Jend. Gatot Subroto Kav. 71-73, Pancoran, RT.8/RW.8, Menteng Dalam, Tebet, Kota Jakarta Selatan, DKI Jakarta 12870',0,1,'L');


        $pdf->Ln(7);
        $pdf->Cell(0,$h,'Demikian surat tugas ini diberikan untuk dapat dilaksanakan sebagaimana mestinya.',0,1,'L');

        $pdf->SetFont('Arial','',11);
        $y = $pdf->GetY()+20;

        // Get PHR -> 2.2
        // $dataPHR = $this->db->limit(1)->select('NIP, Name, TitleAhead, TitleBehind, Signature')->get_where('db_employees.employees em',
        //                   "(em.PositionMain = '2.2' OR em.PositionOther1 = '2.2' OR em.PositionOther2 = '2.2' OR em.PositionOther3 = '2.2') AND  em.StatusEmployeeID = '1'")->result_array();

        $dataPHR = $this->db->limit(1)->select('NIP, Name, TitleAhead, TitleBehind, Signature')->get_where('db_employees.employees em',array('NIP' => $dataRequest[0]['UserConfirm']))->result_array();

        $NamePHR_a = (count($dataPHR)>0) ? trim($dataPHR[0]['TitleAhead']).' ' : '';
        $NamePHR_b = (count($dataPHR)>0) ? ' '.trim($dataPHR[0]['TitleBehind']) : '';
        $NamePHR = (count($dataPHR)>0) ? $NamePHR_a.''.trim($dataPHR[0]['Name']).''.$NamePHR_b : '';
        $NIPPHR = (count($dataPHR)>0) ? $dataPHR[0]['NIP'] : '';
        $Signature = (count($dataPHR)>0 && $dataPHR[0]['Signature']!='') ? $dataPHR[0]['Signature'] : '';

        $pdf->Image('./images/cap.png',130,$y+1,40);
        if($Signature!=''){
            $pdf->Image('./uploads/signature/'.$Signature,130,$y+4,40);
        }


        $pdf->Ln(11);
        $w = 115;
        $w2 = 75;
        $pdf->Cell($w,$h,'',0,0,'L');
        $pdf->Cell($w2,$h,'Jakarta, '.$this->getDateIndonesian($dataRequest[0]['DateConfirm']),0,1,'L');

        $pdf->Cell($w,$h,'',0,0,'L');
        $pdf->Cell($w2,$h,'An Rektor',0,1,'L');

        $pdf->Cell($w,$h,'',0,0,'L');
        $pdf->Cell($w2,$h,'',0,1,'L');

        $pdf->Ln(17);

        $pdf->Cell($w,$h,'',0,0,'L');
        $pdf->Cell($w2,$h,$NamePHR,0,1,'L');

        $pdf->Cell($w,$h,'',0,0,'L');
        $pdf->Cell($w2,$h,'NIP : '.$NIPPHR,0,1,'L');

        $pdf->SetFont('Arial','',10);
        $pdf->SetXY(10,$y+45);
        $pdf->Cell(60,5,'Tembusan Yth.',0,1,'L');

        $pdf->SetXY(17,$y+51);
        $pdf->Cell(60,5,'1. Rektor',0,1,'L');

        $pdf->SetXY(17,$y+56);
        $pdf->Cell(60,5,'2. Dekan '.$d['NameFak'],0,1,'L');

        $pdf->SetXY(17,$y+61);
        $pdf->Cell(60,5,'3. Kaprodi '.$d['ProdiName'],0,1,'L');


        $pdf->Output('I','Tugas_Keluar.pdf');

        } else {
            echo 'data not yet';
        }

    }

    public function export_kwitansi_formulironline()
    {
        $token = $this->input->post('token');
        $input = $this->getInputToken($token);
        $this->load->model('master/m_master');
        $taGet = $this->m_master->showData_array('db_admission.set_ta');
        $ta = substr($taGet[0]['Ta'], 2,2);
        $InputDate = $input['date'];
        $InputDate = explode('-', $InputDate);
        $bulanRomawi = $this->m_master->romawiNumber($InputDate[1]);

        $maxLen = 4;
        $NoKwitansi = $input['NumForm'];
        $aa = strlen($NoKwitansi);
        for ($i=0; $i < ( $maxLen - $aa ); $i++) {
            $NoKwitansi = '0'.$NoKwitansi;
        }

        $NoKwitansi = 'ON-'.$NoKwitansi;

        // check tahun berdasarkan No_Ref
         $G_dt = $this->m_master->caribasedprimary('db_admission.formulir_number_global','FormulirCodeGlobal',$input['NoFormRef']);
         $YearsWR_MKT = substr($G_dt[0]['Years'], 2,2);

        // $nomorWr = $InputDate[0].' / '.$bulanRomawi.' / FRM'.' / '.'MKT-PU-'.$ta.' / '.$NoKwitansi;
        $nomorWr = $G_dt[0]['Years'].' / '.$bulanRomawi.' / FRM'.' / '.'MKT-PU-'.$YearsWR_MKT.' / '.$NoKwitansi;

            $fpdf = new Fpdf('L', 'mm', array(216, 140));
            //$fpdf = new Fpdf('P', 'mm', 'A4');
            //$fpdf = new Fpdf('P', 'mm', array(215,140));

            //$fpdf->SetMargins(0, 0);
            //$fpdf->SetDisplayMode('real');
            $fpdf->SetAutoPageBreak(true, 0);
            $fpdf->AddPage();

            //====================== WATERMARK ======================
            // if ($data['print'] > 2) {
            //     $fpdf->SetTextColor(209, 209, 209);
            //     $fpdf->SetFont('Arial', '', 100);
            //     $fpdf->Text(42, 70, 'C O P Y');
            // }
            // rahmat 23 Februari 2016
            //====================== Nomor Form ======================
            $fpdf->SetFont('Arial', 'B', 8);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->SetXY(1, 5);
            $fpdf->Cell(206, 7, 'FM-UAP/KEU-06-03', 0, 0, 'R');

            // End rahmat

            //====================== HEADER ======================
            $fpdf->SetFont('Arial', 'B', 18);
            $fpdf->SetTextColor(0, 0, 0);
            $fpdf->SetXY(5, 5);
            $fpdf->ln(1);
            $fpdf->Cell(206, 7, 'TANDA TERIMA PEMBAYARAN', 0, 0, 'C');
            $fpdf->ln(7);
            // $fpdf->Line(65, 12.5, 163, 12.5);
            $fpdf->SetFont('Arial', '', 12);
            $fpdf->Cell(206, 15, 'Nomor: '.$nomorWr, 0, 0, 'C');

            // //====================== CONTENT ======================
            // if ($data['form'] == NULL) {
            //     $no_lbl = 'NIM';
            //     $no_txt = $data['nim'];
            // }
            // else {
            //     $no_lbl = 'No Form';
            //     $no_txt = $data['form'];
            // }

            // $fpdf->SetFont('Arial', '', 14);
            // $fpdf->Text(23, 28, 'Telah terima dari,');
            $no_lbl = 'No Form';

            $fpdf->SetFont('Arial', '', 11);
            $fpdf->Text(23, 36, $no_lbl);
            $fpdf->Text(23, 43, 'Nama lengkap');
            // $fpdf->Text(23, 50, 'Tlp / HP');
            // $fpdf->Text(23, 57, 'Jurusan');
            $fpdf->Text(23, 50, 'Pembayaran');
            $fpdf->Text(23, 57, 'Jumlah');
            $fpdf->Text(23, 64, 'Cara Pembayaran');
            $fpdf->Text(23, 71, 'Terbilang');

            $fpdf->Text(63, 36, ':');
            $fpdf->Text(63, 43, ':');
            // $fpdf->Tex63t(59, 50, ':');
            // $fpdf->Text(59, 57, ':');
            $fpdf->Text(63, 50, ':');
            $fpdf->Text(63, 57, ':');
            $fpdf->Text(63, 64, ':');
            $fpdf->Text(63, 71, ':');

            $terbilang = $this->m_master->moneySay($input['jumlah']).'Rupiah';
            $terbilang = trim(ucwords($terbilang));
            $fpdf->Text(69, 36, $input['NoFormRef'] );
            $fpdf->Text(69, 43, $input['namalengkap']);
            // $fpdf->Text(64, 50, $input['hp']);
            // $fpdf->Text(64, 57, $input['jurusan']);
            $fpdf->Text(69, 50, $input['pembayaran']);
            $fpdf->Text(69, 57, 'Rp '.number_format($input['jumlah'],2,',','.').',-');
            $fpdf->Text(69, 64, $input['jenis']);
            $fpdf->Text(69, 71, $terbilang);

            $fpdf->Line(69, 37, 195, 37);
            $fpdf->Line(69, 44, 195, 44);
            // $fpdf->Line(63, 51, 195, 51);
            // $fpdf->Line(63, 58, 195, 58);
            $fpdf->Line(69, 51, 195, 51);
            $fpdf->Line(69, 58, 195, 58);
            $fpdf->Line(69, 65, 195, 65);
            $fpdf->Line(69, 72, 195, 72);

            //====================== FOOTER / SIGN ======================
            $printDate = $this->m_master->getIndoBulan(date('Y-m-d'));
            $fpdf->SetFont('Arial', '', 14);
            $fpdf->SetXY(140, 92);
            $fpdf->Cell(60, 5, 'Jakarta, '.$printDate, 0, 0, 'C');
            $fpdf->SetXY(140, 116);
            $fpdf->SetFont('Arial', 'U', 14);
            $fpdf->Cell(60, 5, '( '.$this->session->userdata('Name').' )', 0, 0, 'C');

            //====================== FINISH ======================


        $fpdf->Output('receipt.pdf','I');
    }


    // Surat Keterangan bebas perpus
    public function skbp($token){

        $data = $this->getInputToken($token);

        if(isset($data['NPM'])){

            // Cek udah ada apa blm
            $dataCekNomor = $this->db->get_where('db_academic.final_project_proof',array(
                'Type' => 'lib',
                'NPM' => $data['NPM']
            ))->result_array();

            if(count($dataCekNomor)>0){
                $DownloadCount = $dataCekNomor[0]['DownloadCount'] + 1;
                $this->db->where('ID', $dataCekNomor[0]['ID']);
                $this->db->update('db_academic.final_project_proof',array(
                    'DownloadCount' => $DownloadCount,
                    'DownloadAt' => $this->m_rest->getDateTimeNow()
                ));
                $this->db->reset_query();

                $noSurat = $dataCekNomor[0]['NoSurat'];
                $noSurat_Month = $dataCekNomor[0]['Month'];
                $noSurat_Year = $dataCekNomor[0]['Year'];
            } else {

                $countNomor = $this->db->get_where('db_academic.final_project_proof',array(
                    'Type' => 'lib',
                    'Year' => date("Y")
                ))->result_array();

                $DownloadCount = 1;
                $noSurat = (count($countNomor)>0) ? count($countNomor) + 1 : 1;
                $noSurat_Month = date("n");
                $noSurat_Year = date("Y");

                $this->db->insert('db_academic.final_project_proof',array(
                    'NPM' => $data['NPM'],
                    'Type' => 'lib',
                    'NoSurat' => $noSurat,
                    'Month' => $noSurat_Month,
                    'Year' => $noSurat_Year,
                    'DownloadCount' => $DownloadCount,
                    'DownloadAt' => $this->m_rest->getDateTimeNow()
                ));

            }


            $dataCk = $this->db->query('SELECT ats.Name, ats.NPM, ats.EmailPU, ats.Year, fpc.Cl_Library_At, ps.Name AS Prodi FROM db_academic.auth_students ats
                                                LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                LEFT JOIN db_academic.final_project_clearance fpc ON (fpc.NPM = ats.NPM)
                                                WHERE ats.NPM = "'.$data['NPM'].'" AND fpc.Cl_Library = "1"')->result_array();

            if(count($dataCk)>0){
                $d = $dataCk[0];

                $DB = 'ta_'.$d['Year'];
                $d2 = $this->db->get_where($DB.'.students',array(
                    'NPM' => $data['NPM']
                ))->result_array()[0];

                $pdf = new FPDF('P','mm','A4');
                $pdf->SetMargins(20.5,15,20.5);
                $pdf->AddPage();
                $h = 5;
                $border = 0;

                // total width : 189

                $pdf->Image('./images/new_logo_pu.png',20,15,50);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(0,$h,'FM-UAP/LIB-10-02',$border,1,'R');

                $pdf->Ln(15);
                $pdf->Cell(0,$h,'PERPUSTAKAAN',$border,1,'L');
                $pdf->Cell(0,$h,'PODOMORO UNIVERSITY',$border,1,'L');

                $pdf->Ln(5);
                $pdf->SetFont('Arial','BU',12);
                $pdf->Cell(0,$h,'SURAT KETERANGAN BEBAS PUSTAKA',$border,1,'C');

                $pdf->SetFont('Arial','',10);
                $pdf->Cell(0,$h,'Nomor : '.$noSurat.'/UAP/PERPUS-SKBP/'.$noSurat_Month.'/'.$noSurat_Year,$border,1,'C');

                $pdf->Ln(9);
                $pdf->SetFont('Arial','',9);
                $pdf->Cell(0,$h,'Perpustakaan PodomoroUniversity dengan ini menerangkan bahwa : ',$border,1,'L');

                $pdf->Ln(5);

                $StudentName = ucwords(strtolower($d['Name']));
                $pdf->Cell(40,$h,'Nama',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(125,$h,$StudentName,$border,1,'L');

                $pdf->Cell(40,$h,'NIM',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(125,$h,$d['NPM'],$border,1,'L');

                $pdf->Cell(40,$h,'Prodi / Unit',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(125,$h,$d['Prodi'],$border,1,'L');

                $pdf->Cell(40,$h,'Email PU',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(125,$h,$d['EmailPU'],$border,1,'L');

                $pdf->Cell(40,$h,'Email Lain',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(125,$h,$d2['Email'],$border,1,'L');

                $pdf->Cell(40,$h,'Telepon',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(125,$h,$d2['Phone'],$border,1,'L');

                $pdf->Cell(40,$h,'HP',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(125,$h,$d2['HP'],$border,1,'L');


                $pdf->Ln(5);
                $pdf->MultiCell(169, $h, 'Terhitung tanggal :  '.$this->getDateIndonesian($d['Cl_Library_At']).' dinyatakan telah bebas dari seluruh kewajiban yang berkenaan dengan perpustakaan dan telah memenuhi syarat bebas pustaka yakni :', 0, 'J',false);

                $pdf->Ln(3);
                $pdf->Cell(9,$h,'',$border,0,'C');
                $pdf->Cell(160,$h,'- Telah mengembalikan seluruh pinjaman buku perpustakaan',$border,1,'L');
                $pdf->Cell(9,$h,'',$border,0,'C');
                $pdf->Cell(160,$h,'- Telah menyerahkan laporan akhir/skripsi/tesis beserta CD soft copy*',$border,1,'L');

                $pdf->Ln(3);
                $pdf->MultiCell(169, $h, 'Demikian surat keterangan ini untuk dipergunakan sebagaimana mestinya. ', 0, 'J',false);


//        $border = 1;
                $pdf->Ln(15);
                $pdf->Cell(110,$h,'',$border,0,'L');
                $pdf->Cell(60,$h,'Jakarta, '.$this->getDateIndonesian($d['Cl_Library_At']),$border,1,'L');
                $pdf->Cell(110,$h,'',$border,0,'L');
                $pdf->Cell(60,$h,'Kepala Perpustakaan',$border,1,'L');



                // Get kabag Library
                $dataKabagLib = $this->db->get_where('db_employees.employees',array(
                    'PositionMain' => '11.11',
                    'StatusEmployeeID' => '1',
                ))->result_array();

                $kabagLib = '';
                $signature = '';
                if(count($dataKabagLib)>0){
                    $title_h = ($dataKabagLib[0]['TitleAhead']!='' && $dataKabagLib[0]['TitleAhead']!=null)
                        ? $dataKabagLib[0]['TitleAhead'].' ' : '';

                    $title_b = ($dataKabagLib[0]['TitleBehind']!='' && $dataKabagLib[0]['TitleBehind']!=null)
                        ? ' '.$dataKabagLib[0]['TitleBehind'] : '';

                    $kabagLib = $title_h.$dataKabagLib[0]['Name'].$title_b;
                    $signature = $dataKabagLib[0]['Signatures'];
                }

                $pdf->Image('./uploads/signature/'.$signature,$pdf->GetX()+110,$pdf->GetY(),20);

                $pdf->Ln(15);

                $pdf->Cell(110,$h,'',$border,0,'L');
                $pdf->Cell(60,$h,$kabagLib,$border,1,'L');


                $pdf->Ln(25);
                $pdf->Cell(0,$h,'*) Untuk mahasiswa yang telah sidang skripsi/tesis/disertasi',$border,1,'L');


                $pdf->SetFont('Arial','I',7);
                $pdf->Ln(10);
                $h = 3;
                $pdf->Cell(0,$h,chr(169).' Podomoro University | Downloaded on '.date("d M Y H:i:s"),$border,1,'R');
                $pdf->Cell(0,$h,'Jumlah Download : '.$DownloadCount,$border,1,'R');

                $nameF = str_replace(' ','_',$StudentName);
                $pdf->Output('SKBP__'.$nameF.'.pdf','I');


            }

        }





    }

    // clearance form
    public function clearance_form($token){
//        $pdf = new FPDF('P','mm','A4');
        $data = $this->getInputToken($token);

        if(isset($data['NPM'])){

            $NPM = $data['NPM'];

            $dataStudent = $this->db->query('SELECT ats.NPM, ats.Name, ats.Year, ps.NameEng AS ProdiEng, el.DescriptionEng,
                                                        s.Name AS SemesterName, fpc.TrialDate,
                                                        em1.Name AS Cl_Academic_Name,
                                                        em2.Name AS Cl_Library_Name,
                                                        em3.Name AS Cl_Finance_Name,
                                                        em4.Name AS Cl_Kaprodi_Name,
                                                        em5.Name AS Cl_StdLife_Name,
                                                        fp.TitleInd, fp.TitleEng
                                                        FROM db_academic.auth_students ats
                                                        LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                        LEFT JOIN db_academic.education_level el ON (el.ID = ps.EducationLevelID)
                                                        LEFT JOIN db_academic.final_project fp ON (fp.NPM = ats.NPM)
                                                        LEFT JOIN db_academic.final_project_clearance fpc ON (fpc.NPM = ats.NPM)
                                                        LEFT JOIN db_academic.semester s ON (s.ID = fpc.SemesterID)

                                                        LEFT JOIN db_employees.employees em1 ON (em1.NIP = fpc.Cl_Academic_By)
                                                        LEFT JOIN db_employees.employees em2 ON (em2.NIP = fpc.Cl_Library_By)
                                                        LEFT JOIN db_employees.employees em3 ON (em3.NIP = fpc.Cl_Finance_By)
                                                        LEFT JOIN db_employees.employees em4 ON (em4.NIP = fpc.Cl_Kaprodi_By)
                                                        LEFT JOIN db_employees.employees em5 ON (em5.NIP = fpc.Cl_StdLife_By)

                                                        WHERE ats.NPM = "'.$NPM.'"')->result_array();

            if(count($dataStudent)>0){


                $d = $dataStudent[0];

                // Get tanggal ujian
                $dataUjian = $this->db->query('SELECT fps.Date FROM db_academic.final_project_schedule_student fpss
                                                          LEFT JOIN db_academic.final_project_schedule fps ON (fps.ID = fpss.FPSID)
                                                          WHERE fpss.NPM = "'.$NPM.'" ORDER BY fps.ID DESC LIMIT 1 ')->result_array();

                $TrialDate = (count($dataUjian)>0)
                    ? $this->getDateIndonesian($dataUjian[0]['Date'])
                    : '';

                $dataTranscript = $this->m_rest->getTranscript($d['Year'],$NPM,'ASC');

                $dataIPK = $dataTranscript['dataIPK'];
                $arr_mkD = $dataIPK['MK_D'];
                $arr_mkWajib_SKS = $dataIPK['MK_Wajib_SKS'];


                $pdf = new Pdf_mc_table('P', 'mm', 'A4');
                $pdf->SetMargins(10.5,10,20.5); // w = 179
                $pdf->AddPage();
                $h = 10;
                $border = 0;

                $pdf->Image('./images/new_logo_pu.png',10,10,50);
                $pdf->Ln(15);

                $pdf->SetFont('Arial','B',12);
                $pdf->Cell(0,$h,'Judicium Clearance Form','B',1,'C');

                $pdf->Ln(7);
                $h = 5;
                $pdf->SetFont('Arial','',9);

                // 94.5 - 20 = 74.5
                $pdf->Cell(35,$h,'Name',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(140,$h,ucwords(strtolower($d['Name'])),$border,1,'L');


                $pdf->Cell(35,$h,'NIM',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(140,$h,$d['NPM'],$border,1,'L');


                $pdf->Cell(35,$h,'Study Program',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(140,$h,$d['ProdiEng'],$border,1,'L');

                $pdf->Cell(35,$h,'Educational Program',$border,0,'L');
                $pdf->Cell(4,$h,':',$border,0,'C');
                $pdf->Cell(140,$h,$d['DescriptionEng'],$border,1,'L');


                $pdf->Ln(7);
                $pdf->Cell(0,$h,'Register to take part in the Judicium event for the semester : '.$d['SemesterName'],$border,1,'L');
                $pdf->Cell(0,$h,'Has completed the Judicium registration requirements :',$border,1,'L');

                $pdf->Ln(7);


                $pdf->SetWidths(Array(10,140,29));
                $pdf->SetLineHeight(5);
                $pdf->SetAligns(array('C','L','C'));


                $pdf->Row(Array(
                    '1',
                    "Academic Requirements Fulfilment.
            Total Credit Taken : ".$dataIPK['TotalSKS']."
            Number of Courses With D Score : ".count($arr_mkD)."
            Compulsory Courses : ".$arr_mkWajib_SKS,
                    "Approved By ".$d['Cl_Academic_Name']
                ));

                $pdf->Row(Array(
                    '2',
                    'Date of Final Exam : '.$TrialDate,
                    "Approved By ".$d['Cl_Kaprodi_Name']
                ));

                $pdf->Row(Array(
                    '3',
                    "Title of Final Project \nIndonesian : ".$d['TitleInd']." \nEnglish : ".$d['TitleEng'],
                    "Approved By ".$d['Cl_Kaprodi_Name']
                ));

                $pdf->Row(Array(
                    '4',
                    " - The Submission of Hardcopy / Softcopy of Final Project Report.\n - Submission of all required documents to the Library: Book Loans, Book Contributions, Book Loan Penalties, etc.*",
                    "Approved By ".$d['Cl_Library_Name']
                ));

                $pdf->Row(Array(
                    '5',
                    "Fulfilment of payment obligation",
                    "Approved By ".$d['Cl_Finance_Name']
                ));

                $pdf->Row(Array(
                    '6',
                    "Fulfilment of student life",
                    "Approved By ".$d['Cl_StdLife_Name']
                ));



                $pdf->SetFont('Arial','I',7);
                $pdf->Ln(10);
                $h = 3;
                $pdf->Cell(35,$h,'*This is a computer generate letter, no signature is required.',$border,0,'L');
                $pdf->Cell(0,$h,chr(169).' Podomoro University | Downloaded on '.date("d M Y H:i:s"),$border,1,'R');


                $nameF = str_replace(' ','_',ucwords(strtolower($d['Name'])));
                $pdf->Output('Judicium_Clearance_Form_'.$nameF.'.pdf','I');
            } else {
                echo 'No NIM';
            }

        } else {
            echo 'No NIM';
        }

    }



    public function cetakSKPI($token){
        $this->load->library('Qrcode/qrlib');
        $data = $this->getInputToken($token);

        if(isset($data['NPM'])){

            $NPM = $data['NPM'];

            $dataStd = $this->db->query('SELECT ats.*,
                                                ps.NameEng AS ProdiEng, ps.Degree, ps.TitleDegree,
                                                ps.DegreeEng, ps.TitleDegreeEng, el.DescriptionEng AS ProdiLevelEng,
                                                em.NIP AS DekanNIP,em.Name AS DekanName, em.TitleAhead, em.TitleBehind, em.Signatures, f.NameEng AS FacultyName,
                                                el.MasaStudi, el2.DescriptionEng AS ProdiLevelFutureEng, jl.NoSKPI, jd.JudiciumsDate AS SKPI_JudiciumsDate
                                                FROM db_academic.auth_students ats
                                                LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                LEFT JOIN db_academic.judiciums_list jl ON (jl.NPM = ats.NPM)
                                                LEFT JOIN db_academic.judiciums jd ON (jl.JID = jd.ID)
                                                LEFT JOIN db_academic.faculty f ON (f.ID = ps.FacultyID)
                                                LEFT JOIN db_employees.employees em ON (em.NIP = f.NIP)
                                                LEFT JOIN db_academic.education_level el ON (el.ID = ps.EducationLevelID)
                                                LEFT JOIN db_academic.education_level el2 ON (el2.ID = ps.EducationLevelIDFuture)
                                                WHERE ats.NPM = "'.$NPM.'" ')->result_array();
            $d = $dataStd[0];

//            print_r($d);exit;

            if($d['NoSKPI']!='' && $d['NoSKPI']!=null){

                $db = 'ta_'.$d['Year'];
                $dataTableTa = $this->db->query('SELECT * FROM '.$db.'.students s WHERE s.NPM = "'.$NPM.'" ')->result_array();
                $d2 = $dataTableTa[0];

                $dataPT = $this->db->limit(1)
                    ->get('db_academic.identitas')->result_array()[0];



                $pdf = new FPDF('P','mm','A4');
                $pdf->AddFont('dinpromedium','','dinpromedium.php');
                $pdf->AddFont('dinpromediumitalic','','dinpromediumitalic.php');
                $pdf->AddFont('dinprolight','','dinprolight.php');
                $pdf->AddFont('dinlightitalic','','dinlightitalic.php');
                $pdf->SetMargins(10,10,10);
                $pdf->AddPage();
                $h = 5.5;
                $h_1 = 4;
                $h_2 = 5;
                $border = 0;
                $fontHeader = 13;
                $fontHeader_1 = 11;
                $fontBody = 10;
                $fontBody_1 = 9;
                $fullWidth = 190;

                $midWidth = 90;
                $midWidth_space = 10;
                $rowSpace = 2;

                // Background Cell
                $R = 226;
                $G = 226;
                $B = 226;

                $URLQrCode = url_sign_out.'search-people/detail-student/'.$NPM;

                // QRcode::png($data_arr['data'],'./images/SKPI/SKPI-QRCode.png','L', 10, 4);
                $t = QRcode::png($URLQrCode,false,'L', 10, 4);
                $pic = 'data://text/plain;base64,' . $t;
                // extract dimensions from image
                // $info = getimagesize($pic);
                // $enc = base64_encode($t);

//            QRcode::png($URLQrCode, './images/SKPI/frame.png', 'L', 10, 2);
                $pdf->Image('./images/new_logo_pu.png',10,10,50);
                $pdf->Image($pic,176,12.5,17,NULL,'png');
                // $pdf->Image(url_pas.'images/SKPI/SKPI-QRCode.png',176,12.5,17);
                $pdf->Image('./images/SKPI/frame-qrcode.png',174.5,11,20);

                $pdf->Ln(17);

                $pdf->SetFont('dinpromedium','',$fontHeader);
                $pdf->Cell(0,$h,'DIPLOMA SUPPLEMENT',$border,1,'C');

                $InputDate = explode('-', $d['SKPI_JudiciumsDate']);
                $bulanRomawi = ($d['SKPI_JudiciumsDate']!='' && $d['SKPI_JudiciumsDate']!=null) ? $this->m_master->romawiNumber($InputDate[1]) : '';
                $Year2Number = ($d['SKPI_JudiciumsDate']!='' && $d['SKPI_JudiciumsDate']!=null) ? $InputDate[0] : '';

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell(0,$h,'Number : '.str_pad($d['NoSKPI'], 4, '0', STR_PAD_LEFT).'/UAP/SKPI-'.$d['CertificateSerialNumber'].'/'.$bulanRomawi.'/'.$Year2Number,$border,1,'C');

                $pdf->SetFont('dinprolight','',7);
//                $pdf->Cell(0,2,$URLQrCode,$border,1,'R');
                $pdf->Ln(4);
                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->MultiCell($fullWidth,$h,"The Diploma Supplement accompanies a higher education certificate providing a standardized description on the nature, level, context, content and status of the studies completed by its holder.",0);


                // ====== BAGIAN 1 Dengan Foto ========


                $b_spasi1 = 30;

                $h = 5;
                $pdf->Ln(4);
                $pdf->SetLineWidth(0.1);
                $pdf->SetDash(2,2);
                $pdf->Cell($fullWidth,3,'','T',1,'L');
                $pdf->Image('./images/SKPI/user.png',10,$pdf->GetY(),5);
                $pdf->SetFont('dinpromedium','',$fontHeader_1);
                $pdf->Cell(7,$h,'',$border,0,'L');
                $pdf->Cell(183,$h,'Information Identifying Diploma Supplement Holder',$border,1,'L');
                $pdf->Ln(3);
                $pdf->Cell($fullWidth,$h,'','T',1,'L');

                // ======

                // $path = './uploads/students/'.$db.'/'.$d2['Photo'];
                // if(!file_exists($path)){
                //     $path = './images/icon/userfalse.png';
                // }

                $path =  base_url().'uploads/students/'.$db.'/'.$d2['Photo'];
                $path = str_replace('https', 'http', $path);
                if ( empty($d2['Photo']) || $d2['Photo'] == '' || !$this->m_master->is_url_exist($path)   ) {
                    $path = './images/icon/userfalse.png';
                }

                $pdf->Image($path,10,$pdf->GetY(),25);

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($b_spasi1,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth - $b_spasi1,$h_1,'Full Name',$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_1,'Graduation Year',$border,1,'L');

                $pdf->SetFillColor($R, $G, $B);
                $pdf->SetFont('dinpromedium','',$fontBody_1);
                $pdf->Cell($b_spasi1,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth - $b_spasi1,$h_2,$d['Name'],$border,0,'L',true);
                $pdf->Cell($midWidth_space,$h_2,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_2,$d['GraduationYear'],$border,1,'L',true);
                $pdf->Ln($rowSpace);

                // ======

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($b_spasi1,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth - $b_spasi1,$h_1,'Place, Date of Birth',$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_1,'Diploma Number',$border,1,'L');

                $pdf->SetFillColor($R, $G, $B);
                $pdf->SetFont('dinpromedium','',$fontBody_1);
                $pdf->Cell($b_spasi1,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth - $b_spasi1,$h_2,$d2['PlaceOfBirth'].', '.date('F d, Y',strtotime($d2['DateOfBirth'])),$border,0,'L',true);
                $pdf->Cell($midWidth_space,$h_2,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_2,$d['CertificateSerialNumber'],$border,1,'L',true);
                $pdf->Ln($rowSpace);

                // ======

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($b_spasi1,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth - $b_spasi1,$h_1,'Student Identification Number',$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_1,'Degree',$border,1,'L');

                $pdf->SetFillColor($R, $G, $B);
                $pdf->SetFont('dinpromedium','',$fontBody_1);
                $pdf->Cell($b_spasi1,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth - $b_spasi1,$h_2,$d2['NPM'],$border,0,'L',true);
                $pdf->Cell($midWidth_space,$h_2,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_2,$d['Degree'].' ('.$d['TitleDegree'].')',$border,1,'L',true);

                $pdf->SetFillColor($R, $G, $B);
                $pdf->SetFont('dinlightitalic','',$fontBody_1);
                $pdf->Cell($midWidth_space+$midWidth,$h_2,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_2,$d['DegreeEng'].' ('.$d['TitleDegreeEng'].')',$border,1,'L',true);
                $pdf->Ln($rowSpace);

                // ====== BAGIAN 2 ========

                $h = 5;
                $pdf->Ln(4);
                $pdf->SetLineWidth(0.1);
                $pdf->SetDash(2,2);
                $pdf->Cell($fullWidth,3,'','T',1,'L');
                $pdf->Image('./images/SKPI/gradu.png',10,$pdf->GetY(),5);
                $pdf->SetFont('dinpromedium','',$fontHeader_1);
                $pdf->Cell(7,$h,'',$border,0,'L');
                $pdf->Cell(183,$h,'Information Identifying the Awarding Institution',$border,1,'L');
                $pdf->Ln(3);
                $pdf->Cell($fullWidth,$h,'','T',1,'L');

                // ======

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($midWidth,$h_1,"Awarding Institution's Licence",$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_1,'Language of Instruction',$border,1,'L');

                $pdf->SetFillColor($R, $G, $B);
                $pdf->SetFont('dinpromedium','',$fontBody_1);
                $pdf->Cell($midWidth,$h_2,$dataPT['NoAkta'],$border,0,'L',true);
                $pdf->Cell($midWidth_space,$h_2,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_2,'Indonesian and English',$border,1,'L',true);


                $pdf->Ln($rowSpace);

                // ======

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($midWidth,$h_1,"Higher Education Institution's Name",$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_1,'Grading System',$border,1,'L');

                $pdf->SetFillColor($R, $G, $B);
                $pdf->SetFont('dinpromedium','',$fontBody_1);
                $pdf->Cell($midWidth,$h_2,$dataPT['Nama'],$border,0,'L',true);
                $pdf->Cell($midWidth_space,$h_2,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_2,'According to Degree Certificate',$border,1,'L',true);
                $pdf->Ln($rowSpace);

                // ======

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($midWidth,$h_1,"Major",$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_1,'Regular Length of Study',$border,1,'L');

                $pdf->SetFillColor($R, $G, $B);
                $pdf->SetFont('dinpromedium','',$fontBody_1);
                $pdf->Cell($midWidth,$h_2,$d['ProdiEng'],$border,0,'L',true);
                $pdf->Cell($midWidth_space,$h_2,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_2,$d['MasaStudi'].' Years',$border,1,'L',true);
                $pdf->Ln($rowSpace);

                // ======

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($midWidth,$h_1,"Type and Level of Education",$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_1,'Entry Requirements',$border,1,'L');

                $pdf->SetFillColor($R, $G, $B);
                $pdf->SetFont('dinpromedium','',$fontBody_1);
                $pdf->Cell($midWidth,$h_2,$d['ProdiLevelEng'],$border,0,'L',true);
                $pdf->Cell($midWidth_space,$h_2,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_2,'Graduate from High School of Similar Level of Education',$border,1,'L',true);
                $pdf->Ln($rowSpace);

                // ======

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($midWidth,$h_1,"Level of Qualification in the KKNI",$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_1,'Access to Further Study',$border,1,'L');

                $pdf->SetFillColor($R, $G, $B);
                $pdf->SetFont('dinpromedium','',$fontBody_1);
                $pdf->Cell($midWidth,$h_2,'Level Six (6)',$border,0,'L',true);
                $pdf->Cell($midWidth_space,$h_2,'',$border,0,'L');
                $pdf->Cell($midWidth,$h_2,$d['ProdiLevelFutureEng'],$border,1,'L',true);
                $pdf->Ln($rowSpace);

                // ====== BAGIAN 3 ========

                $h = 5;
                $pdf->Ln(4);
                $pdf->SetLineWidth(0.1);
                $pdf->SetDash(2,2);
                $pdf->Cell($fullWidth,3,'','T',1,'L');
                $pdf->Image('./images/SKPI/diamon.png',10,$pdf->GetY(),5);
                $pdf->SetFont('dinpromedium','',$fontHeader_1);
                $pdf->Cell(7,$h,'',$border,0,'L');
                $pdf->Cell(183,$h,'Activities, Achievements and Awards with Predicate : B',$border,1,'L');
                $pdf->Ln(3);
                $pdf->Cell($fullWidth,$h,'','T',1,'L');

                // ======


                $dataAch_1 = $this->db->query('SELECT COUNT(*) AS Total FROM db_studentlife.student_achievement_student sas
                                                              LEFT JOIN db_studentlife.student_achievement sa ON (sa.ID = sas.SAID)
                                                              WHERE sa.isSKPI = 1 AND sas.NPM = "'.$d['NPM'].'" AND (sa.CategID = 1 OR sa.CategID = 5) ')
                    ->result_array();

                $dataAch_2 = $this->db->query('SELECT COUNT(*) AS Total FROM db_studentlife.student_achievement_student sas
                                                              LEFT JOIN db_studentlife.student_achievement sa ON (sa.ID = sas.SAID)
                                                              WHERE sa.isSKPI = 1 AND sas.NPM = "'.$d['NPM'].'" AND sa.CategID = 2 ')
                    ->result_array();

                $dataAch_3 = $this->db->query('SELECT COUNT(*) AS Total FROM db_studentlife.student_achievement_student sas
                                                              LEFT JOIN db_studentlife.student_achievement sa ON (sa.ID = sas.SAID)
                                                              WHERE sa.isSKPI = 1 AND sas.NPM = "'.$d['NPM'].'" AND sa.CategID = 3 ')
                    ->result_array();


                $dataAch_4 = $this->db->query('SELECT COUNT(*) AS Total FROM db_studentlife.student_achievement_student sas
                                                              LEFT JOIN db_studentlife.student_achievement sa ON (sa.ID = sas.SAID)
                                                              WHERE sa.isSKPI = 1 AND sas.NPM = "'.$d['NPM'].'" AND sa.CategID = 6 ')
                    ->result_array();

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($midWidth-30,$h_1,'Achievement and Awards',$border,0,'L');
                $pdf->Cell($midWidth-60,$h_1,': '.$dataAch_1[0]['Total'],$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth-30,$h_1,'Participation in Organization',$border,0,'L');
                $pdf->Cell($midWidth-60,$h_1,': '.$dataAch_3[0]['Total'],$border,1,'L');
                $pdf->Ln($rowSpace);

                // ======

                $pdf->SetFont('dinprolight','',$fontBody);
                $pdf->Cell($midWidth-30,$h_1,'Training / Seminar / Workshop',$border,0,'L');
                $pdf->Cell($midWidth-60,$h_1,': '.$dataAch_2[0]['Total'],$border,0,'L');
                $pdf->Cell($midWidth_space,$h_1,'',$border,0,'L');
                $pdf->Cell($midWidth-30,$h_1,'Internship',$border,0,'L');
                $pdf->Cell($midWidth-60,$h_1,': '.$dataAch_4[0]['Total'],$border,1,'L');
                $pdf->Ln($rowSpace * 3);

                // ======

                $spasiTdd = 120;
                $pdf->Ln(11);



                if($d['DekanName']!='' && $d['DekanName']!=null){
                    $t_a = ($d['TitleAhead']!='' && $d['TitleAhead']!=null) ? $d['TitleAhead'].' ' : '';
                    $t_b = ($d['TitleBehind']!='' && $d['TitleBehind']!=null) ? ' '.$d['TitleBehind'] : '';
                    $t_name = $d['DekanName'];
                    $ttd_jabatan = 'Dean of '.$d['FacultyName'];
                    $ttd_NIP = $d['DekanNIP'];
                    $ttd_Signatures = $d['Signatures'];
                } else {
                    $dataWarek =  $this->db->query(
                        'select * from db_employees.employees
                         where StatusEmployeeID = 1
                         and
                         (
                            PositionMain = "2.2" or
                            PositionOther1 = "2.2" or
                            PositionOther2 = "2.2" or
                            PositionOther3 = "2.2"
                         )

                         limit 1
                        '
                    )->result_array()[0];

                    $t_a = ($dataWarek['TitleAhead']!='' && $dataWarek['TitleAhead']!=null)
                        ? $dataWarek['TitleAhead'].' ' : '';
                    $t_b = ($dataWarek['TitleBehind']!='' && $dataWarek['TitleBehind']!=null)
                        ? ' '.$dataWarek['TitleBehind'] : '';
                    $t_name = $dataWarek['Name'];
                    $ttd_jabatan = 'Vice Rector I';
                    $ttd_NIP = $dataWarek['NIP'];
                    $ttd_Signatures = $dataWarek['Signatures'];

                }

                $ttd_name = $t_a.$t_name.$t_b;


                $pathSignature = './uploads/signature/'.$ttd_Signatures;
                if(file_exists($pathSignature)){
                    $pdf->Image('./images/cap.png',$spasiTdd+20,$pdf->GetY()-2,40);
                    $pdf->Image($pathSignature,$spasiTdd + 10,$pdf->GetY(),40);
                }


                $pdf->SetFont('dinpromedium','',$fontBody);
                $pdf->Cell($spasiTdd,$h_1,'',$border,0,'L');
                $pdf->Cell($fullWidth - $spasiTdd,$h_1,'Jakarta, '.date('F d, Y',strtotime($d['GraduationDate'])),$border,1,'L');


                $pdf->Ln(24.5);



                $pdf->SetFont('dinpromedium','',$fontHeader_1);
                $pdf->Cell($spasiTdd,$h_1,'',$border,0,'L');
                $pdf->Cell($fullWidth - $spasiTdd,$h_1,$ttd_name,$border,1,'L');
                $pdf->Ln(1);
                $pdf->SetFont('dinprolight','',$fontBody_1);
                $pdf->Cell($spasiTdd,$h_1,'',$border,0,'L');
                $pdf->Cell($fullWidth - $spasiTdd,$h_1,$ttd_jabatan,$border,1,'L');
                $pdf->SetFont('dinprolight','',$fontBody_1);
                $pdf->Cell($spasiTdd,$h_1,'',$border,0,'L');
                $pdf->Cell($fullWidth - $spasiTdd,$h_1,'Employee ID Number : '.$ttd_NIP,$border,1,'L');


                $StudentName = $d['NPM'];
                $nameF = str_replace(' ','_',$StudentName);

                $pdf->Output('SKPI__'.$nameF.'.pdf','I');

            } else {
                echo 'Students are not yet registered in the graduation';
            }



        }

    }

    public function shareSurvey($token){
        $this->load->library('Qrcode/qrlib');
        $data_arr = $this->getInputToken($token);

        $pdf = new FPDF('P','mm','A4');
        $pdf->AddFont('dinpromedium','','dinpromedium.php');
        $pdf->AddFont('dinpromediumitalic','','dinpromediumitalic.php');
        $pdf->AddFont('dinprolight','','dinprolight.php');
        $pdf->AddFont('dinlightitalic','','dinlightitalic.php');
        $pdf->SetMargins(10,10,10);
        $pdf->AddPage();


//        $pdf->Cell(183,5,'Information Identifying Diploma Supplement Holder',1,1,'L');
//
//        $pdf->Cell(91.5,5,'Information Identifying Diploma Supplement Holder',1,1,'L');
//        $pdf->Cell(58.5,5,'Information ',1,1,'L');

        $pdf->Image('./images/FA_letterhead_a4_r2.jpg',0,0,210);


        $URLQrCode = $data_arr['shareLink'];

        $t = QRcode::png($URLQrCode,false,'L', 10, 4);
        $pic = 'data://text/plain;base64,' . $t;
        $pdf->Image($pic,68.5,41,66,NULL,'png');
        $pdf->Image('./images/SKPI/frame-qrcode.png',68.5,41,66);

        $pdf->setFillColor(230, 236, 255);
        $pdf->SetFont('dinprolight','',15);
        $pdf->SetXY(10,130.5);
        $pdf->Cell(0,15,$URLQrCode,0,1,'C',true);
//
        $pdf->SetTextColor(255,0,0);
        $pdf->SetFont('dinpromedium','',9);
        $pdf->SetXY(10,145.5);
        $pdf->Cell(0,5,'*) Tidak ada angka 0 (nol) di dalam URL',0,1,'C',true);
//
//
        $pdf->Ln(13);

        $dataSurv = $this->db->get_where('db_it.surv_survey',array('ID'=>$data_arr['ID']))
            ->result_array();

        $pdf->SetTextColor(0,0,0);
        $pdf->SetFont('dinpromedium','',13);
        $pdf->Cell(40,5,'Judul',0,0,'L');
        $pdf->Cell(10,5,':',0,0,'C');
        $pdf->SetXY(60,$pdf->GetY());
        $pdf->MultiCell(140,5,$dataSurv[0]['Title'],0);

        $pdf->Ln(5);

        $pdf->Cell(40,5,'Tanggal Publikasi',0,0,'L');
        $pdf->Cell(10,5,':',0,0,'C');
        $pdf->SetXY(60,$pdf->GetY());
        $pdf->MultiCell(140,5,date('d F yy', strtotime($dataSurv[0]['StartDate'])).' - '.
            date('d F yy', strtotime($dataSurv[0]['EndDate'])),0);


        $StudentName = '123123';
        $nameF = str_replace(' ','_',$StudentName);

        $pdf->Output('SKPI__'.$nameF.'.pdf','I');

    }

    public function recapitulation_salaries(){

        $pdf = new FPDF('L','mm','A4');
        $pdf->SetMargins(5,5,5);
        $pdf->AddPage();

        $pdf->Image('./images/new_logo_pu.png',5,3,50);

        $pdf->SetFont('Arial','',9);

        $border = 1;
        $h = 5;
        $x = $pdf->GetX();
        $y = $pdf->GetY();
        $pdf->Cell(40,$h,$x.' - '.$y,$border,0,'L');

        $pdf->Output('SKBP__.pdf','I');
    }


}
