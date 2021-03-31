<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_finance extends Finnance_Controler {

    private $data = array();

    function __construct()
    {
        parent::__construct();
        $this->data['department'] = parent::__getDepartement();
        $this->load->model('finance/m_finance');
        $this->load->model('m_sendemail');
        $this->load->model('admission/m_admission');
        $this->load->model('master/m_master');
        // get academic year admission
            $t = $this->m_master->showData_array('db_admission.set_ta');
            $this->data['academic_year_admission'] = $t[0]['Ta'];
    }


    public function temp($content)
    {
        parent::template($content);
    }

    public function index()
    {
        $data['department'] = parent::__getDepartement();
        $content = "test";
        $this->temp($content);
    }

    public function formulir_registration_online_page()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/penerimaan_pembayaran/formulir_registration_online_page',$this->data,true);
        $this->temp($content);
    }

    public function formulir_registration_offline_page()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/penerimaan_pembayaran/formulir_registration_offline_page',$this->data,true);
        $this->temp($content);
    }

    public function monitoring_yudisium()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/yudisium/monitoring_yudisium','',true);
        $this->temp($content);
    }

    public function confirmed_verfikasi_pembayaran_registration_online()
    {
        $input = $this->getInputToken();
        $arrdata = $input['arrdata'];
        $getEmailnURL = $this->getEmailnURL($arrdata);
        $SendEmail = $this->SendEmailToCandidate($getEmailnURL);
        $saveData = $this->SaveDataVerification($arrdata);
        //return print_r(json_encode($this->data));
    }

    public function getEmailnURL($arrdata)
    {
        $arr = explode(",", $arrdata);
        return $getEmailnURL = $this->m_finance->getEmailnURLCheckbox($arr,";");
        
    }

    public function SendEmailToCandidate($arr_email_url)
    {
        for ($i=0; $i < count($arr_email_url); $i++) {
            if ($arr_email_url[$i]['email'] != "nothing") {
                /*$text = 'Dear Candidate,<br><br>
                            Please click link below to get <strong>Formulir Registration</strong> :<br>
                            '.$this->GlobalVariableAdi['url_registration']."formulir-registration/".$arr_email_url[$i]['url'].'
                        ';*/
                $text = 'Dear Candidate,<br><br>
                            Please click link below to login your portal <br>
                            '.$this->GlobalVariableAdi['url_registration']."login/".'
                        ';        
                $to = $arr_email_url[$i]['email'];
                $subject = "Link Formulir Registration Podomoro University";
                $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
                /*if ($sendEmail['status'] == 0) {
                    var_dump($sendEmail['msg']);
                }*/
            }
        }
    }

    public function SaveDataVerification($arrdata)
    {
        $this->m_finance->ProcessSaveDataVerification($arrdata);
    }

    public function nilai_rapor_page()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/approved/nilai_rapor_page',$this->data,true);
        $this->temp($content);
    }

    public function loaddata_nilai_calon_mahasiswa_verified($page = null)
    {
         $input =  $this->getInputToken();
         $Nama = $input['Nama'];
         $selectProgramStudy = $input['selectProgramStudy'];
         $Sekolah = $input['Sekolah'];
         $this->load->library('pagination');
         $config = $this->config_pagination_default_ajax(1000,25,4);
         $this->pagination->initialize($config);
         $page = $this->uri->segment(4);
         $start = ($page - 1) * $config["per_page"];
         $this->data['url_registration'] = $this->GlobalVariableAdi['url_registration'];
         $this->data['datadb'] = $this->m_finance->loadData_calon_mahasiswa_created($config["per_page"], $start,$Nama,$selectProgramStudy,$Sekolah);
         $this->data['mataujian'] = $this->m_admission->select_mataUjian($selectProgramStudy);
         $this->data['grade'] = $this->m_admission->showData('db_academic.grade');
        $this->data['no'] = $start + 1;
        $this->data['chkActive'] = 1;
        $content = $this->load->view('page/'.$this->data['department'].'/approved/table_nilai_calon_mahasiswa',$this->data,true);

         $output = array(
         'pagination_link'  => $this->pagination->create_links(),
         'loadtable'   => $content,
         );
         echo json_encode($output);
    }

    public function submit_approved_nilai_rapor()
    {
        $input = $this->getInputToken();
        $this->m_finance->submit_approved_nilai_rapor($input['chkValue']);

        $text = 'Dear Team,<br><br>
                    Finance Team telah approve nilai rapor kandidat mahasiswa<br>
                    Silahkan cek pada portal Anda.
                ';
        $query = $this->m_master->caribasedprimary('db_admission.email_to','Function','Admisi');             
        $to = $query[0]['EmailTo'];
        $subject = "Podomoro University Notify";
        $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);

        echo json_encode( array('msg' => 'Data berhasil disimpan') );
    }

    public function tuition_fee()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/approved/tuition_fee',$this->data,true);
        $this->temp($content);
    }

    public function tuition_fee_approve($page = null)
    {
        $input = $this->getInputToken();
        $FormulirCode = $input['FormulirCode'];
        // get grade
        $this->load->library('pagination');
        $config = $this->config_pagination_default_ajax($this->m_admission->count_getDataCalonMhsTuitionFee_delete($FormulirCode,'p.Status = "Created"'),5,5);
        $this->pagination->initialize($config);
        $page = $this->uri->segment(5);
        $start = ($page - 1) * $config["per_page"];

        // $this->data['payment_type'] = json_encode($this->m_master->showData_array('db_finance.payment_type'));
        $this->data['payment_type'] = json_encode($this->m_master->caribasedprimary('db_finance.payment_type','Type','0'));
        $this->data['getDataCalonMhs'] = json_encode($this->m_admission->getDataCalonMhsTuitionFee_delete($config["per_page"], $start,$FormulirCode,'p.Status = "Created"'));
        $content = $this->load->view('page/'.$this->data['department'].'/approved/page_tuition_fee_approve',$this->data,true);
        $output = array(
        'pagination_link'  => $this->pagination->create_links(),
        'loadtable'   => $content,
        'Grade' => $this->m_master->showData_array('db_academic.grade'),
        );
        echo json_encode($output);
    }

    public function approve_save()
    {
        $input = $this->getInputToken();
        //$this->m_finance->set_tuition_fee_approve($input);
       
        $getData = $this->m_finance->tuition_fee_calon_mhs_by_ID($input[0]);

        $cicilan = $this->m_master->caribasedprimary('db_finance.payment_pre','ID_register_formulir',$input[0]);
        $proses = $this->m_finance->process_tuition_fee_calon_mhs($getData,$cicilan);
        // $proses = '';
        if ($proses == '') {
            // create pdf & send email
            $this->m_admission->Tuition_PDF_SendEmail($getData,$cicilan);
        }
    }

    // public function Tuition_PDF_SendEmail($Personal,$arr_cicilan)
    // {
    //     $Sekolah = $Personal[0]['SchoolName'];
    //     $TuitionFee = $this->m_finance->getTuitionFee_calon_mhs($Personal[0]['ID_register_formulir']);
    //     $arr_temp = array('filename' => '');
    //     $filename = 'Tuition_fee_'.$Personal[0]['FormulirCode'].'.pdf';
    //     $getData = $this->m_master->showData_array('db_admission.set_label_token_off');

    //     $config=array('orientation'=>'P','size'=>'A5');
    //     $this->load->library('mypdf',$config);
    //     $this->mypdf->SetMargins(10,10,10,10);
    //     $this->mypdf->SetAutoPageBreak(true, 0);
    //     $this->mypdf->AddPage();
    //     // Logo
    //     $this->mypdf->Image('./images/logo_tr.png',10,10,50);

    //     $setFont = 8;

    //     // date
    //     $DateIndo = $this->m_master->getIndoBulan(date('Y-m-d'));
    //     $this->mypdf->SetXY(150,20);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, 'Jakarta, '.$DateIndo, 0, 0, 'L', 0);

    //     // Line break
    //     $this->mypdf->Ln(20);

    //     $this->mypdf->SetXY(22,29);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, 'Nomor', 0, 1, 'L', 0);

    //     $this->mypdf->SetXY(22,35);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, 'Hal', 0, 1, 'L', 0);

    //     $this->mypdf->SetXY(42,29);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, ':', 0, 1, 'L', 0);

    //     $this->mypdf->SetXY(42,35);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, ':', 0, 1, 'L', 0);

    //     $getNumber = $this->m_master->caribasedprimary('db_finance.register_admisi','ID_register_formulir',$Personal[0]['ID_register_formulir']);
    //     $No_Surat = $this->m_finance->ShowNumberTuitionFee( $getNumber[0]['No_Surat'] );
    //     $this->mypdf->SetXY(45,29);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, $No_Surat.'/MKT-PMB-B-19/PU/X/2018', 0, 1, 'L', 0);

    //     $this->mypdf->SetXY(45,35);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, 'Surat Keputusan Penerimaan Beasiswa di Podomoro University', 0, 1, 'L', 0);


    //     $setXAwal = 22;
    //     $setYAwal = 45;
    //     $setJarakY = 5;
    //     $setJarakX = 40;
    //     $setFontIsian = 12;

    //     // isian
    //     $setY = $setYAwal;
    //     $setX = $setXAwal;

    //     // label
    //     $this->mypdf->SetXY($setX,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','B',$setFont);
    //     $this->mypdf->Cell(0, 0, 'Kepada Yth.', 0, 1, 'L', 0);

    //     // Nama
    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','B',$setFont);
    //     $this->mypdf->Cell(0, 0, $Personal[0]['Name'].'-'.$Personal[0]['FormulirCode'], 0, 1, 'L', 0); 

    //     // Address
    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','B',$setFont);
    //     $this->mypdf->Cell(0, 0, $Personal[0]['Address'], 0, 1, 'L', 0); 

    //     // City
    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, $Personal[0]['RegionAddress'].' '.$Personal[0]['ProvinceAddress'], 0, 1, 'L', 0); 

    //     // School
    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, $Personal[0]['SchoolName'], 0, 1, 'L', 0); 

    //     // Hp
    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, 'No.Tlp/Hp     : '.$Personal[0]['PhoneNumber'], 0, 1, 'L', 0); 

    //     // Hp
    //     $setXvalue = $setX;
    //     $setY = $setY + 7;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(0, 0, 'Dengan hormat,', 0, 1, 'L', 0);

    //     // cek potongan discount
    //     $chkDiscount = 0;
    //     $arr_discount = array();
    //     $arr_discount2 = array();
    //     $NameTbl = $Personal[0]['Name'].'-'.$Personal[0]['FormulirCode'];
    //     foreach ($Personal[0] as $key => $value) {
    //         $key = explode('-', $key);
    //         if ($key[0] == 'Discount') {
    //             if ($value > 0 ) {
    //                $chkDiscount = 1;
    //                $arr_discount[$key[1]] = $value;
    //             }
    //             $arr_discount2[$key[1]] = $value;
    //         }
    //     }

    //     if ($chkDiscount == 1) {
    //         $Status = 'rata-rata raport kelas XI';
    //         if ($Personal[0]['RangkingRapor'] != 0) {
    //             $Status = 'Rangking paralel '.$Personal[0]['RangkingRapor'].' kelas XI';
    //         }

    //         $setXvalue = $setX;
    //         $setY = $setY + 2;
    //         $this->mypdf->SetXY($setXvalue,$setY);
    //         $this->mypdf->SetTextColor(0,0,0);
    //         $this->mypdf->SetFont('Arial','',$setFont);
    //         // MultiCell( 140, 2, $arr_value[$getRowDB], 0,'L');
    //         $this->mypdf->MultiCell(0, 5, 'Selamat, Anda mendapatkan beasiswa potongan di Podomoro University tahun akademik '.$Personal[0]['NamaTahunAkademik'].' berdasarkan '.$Status.', dengan rincian sebagai berikut:', 0,'L');

    //         $setY = $setY + 10;
    //         $height = 5;
    //         $this->mypdf->SetXY($setX,$setY); 
    //         $this->mypdf->SetFillColor(255, 255, 255);
    //         $this->mypdf->Cell(50,$height,'Nama Lengkap - Nomor Formulir',1,0,'C',true);
    //         $this->mypdf->Cell(40,$height,'Program Study',1,0,'C',true);
    //         $this->mypdf->Cell(80,$height,'Beasiswa',1,1,'C',true);

    //         $ProdiTbl = $Personal[0]['NamePrody'];
    //         foreach ($arr_discount as $key => $value) {
    //             $setY = $setY + $height;
    //             $this->mypdf->SetXY($setX,$setY); 
    //             $this->mypdf->SetFillColor(255, 255, 255);
    //             $this->mypdf->Cell(50,$height,$NameTbl,1,0,'C',true);
    //             $this->mypdf->Cell(40,$height,$ProdiTbl,1,0,'C',true);
    //             $this->mypdf->Cell(80,$height,'Beasiswa Pot '.$key.' '.(int)$value.'%',1,1,'C',true);
    //         } 

    //     }

    //     $setXvalue = $setX;
    //     $setY = $setY + 7;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('Total pembayaran untuk <b>"Semester Pertama"</b> dalam 1x pembayaran :');

    //     $setY = $setY + 5;
    //     $height = 5;
        
    //     $this->mypdf->SetXY($setX,$setY); 
    //     $this->mypdf->SetFillColor(255, 255, 255);
    //     $this->mypdf->SetFont('Arial','B',$setFont);
    //     $this->mypdf->Cell(50,$height,'Pembayaran Semester 1',1,0,'C',true);
    //     $this->mypdf->Cell(25,$height,'SPP',1,0,'C',true);
    //     $this->mypdf->Cell(25,$height,'BPP Semester',1,0,'C',true); 
    //     $this->mypdf->Cell(25,$height,'Biaya SKS',1,0,'C',true); 
    //     $this->mypdf->Cell(25,$height,'Lain-lain',1,0,'C',true); 
    //     $this->mypdf->Cell(25,$height,'Total Biaya',1,1,'C',true); 

    //     $setY = $setY + $height;
    //     $this->mypdf->SetXY($setX,$setY); 
    //     $this->mypdf->SetFillColor(255, 255, 255);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->Cell(50,$height,'Biaya Normal',1,0,'L',true);
        
    //     // get tuition fee
        
    //        $sql23 = 'select a.Abbreviation,b.Cost from db_finance.payment_type as a join db_finance.tuition_fee as b on a.ID = b.PTID where ClassOf = ? and ProdiID = ?';
    //        $query23=$this->db->query($sql23, array($Personal[0]['SetTa'],$Personal[0]['ID_program_study']))->result_array();
    //        $totalTuitionFee = 0;
    //        $arr_pay = array();

    //        // get SKS
    //        $ID_program_study = $Personal[0]['ID_program_study'];
    //        $ccc = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ID_program_study);
    //        $Credit = $ccc[0]['DefaultCredit'];

    //         foreach ($query23 as $keya) {
    //             $arr_pay[$keya['Abbreviation']] = $keya['Cost'];
    //             if ($keya['Abbreviation'] == 'Credit') {
    //                 $CreditHarga = $keya['Cost'] * $Credit;
    //                 $this->mypdf->Cell(25,$height,number_format($CreditHarga,2,',','.'),1,0,'L',true);
    //                 $totalTuitionFee = $totalTuitionFee + $CreditHarga;
    //             }
    //             else
    //             {
    //                 $this->mypdf->Cell(25,$height,number_format($keya['Cost'],2,',','.'),1,0,'L',true);
    //                 $totalTuitionFee = $totalTuitionFee + $keya['Cost'];
    //             }
                
    //         }
    //         // total
    //              $this->mypdf->Cell(25,$height,number_format($totalTuitionFee,2,',','.'),1,0,'L',true);


    //         $setY = $setY + $height;
    //         $this->mypdf->SetXY($setX,$setY); 
    //         $this->mypdf->SetFillColor(255, 255, 255);
    //         $this->mypdf->SetFont('Arial','',$setFont);
    //         $this->mypdf->Cell(50,$height,'Beasiswa yang diterima',1,0,'L',true);

    //         $totalTuitionFee = 0;
    //         foreach ($arr_discount2 as $key => $value) {

    //             foreach ($arr_pay as $keya => $valuea) {

    //                 if ($keya == $key) {
    //                     if ($key == 'Credit') {
    //                         $cost = $Credit * $valuea;
    //                         $cost = $value * $cost / 100;
    //                         $this->mypdf->Cell(25,$height,number_format($cost,2,',','.'),1,0,'L',true);
    //                     }
    //                     else
    //                     {
    //                         $cost = $value * $valuea / 100;
    //                         $this->mypdf->Cell(25,$height,number_format($cost,2,',','.'),1,0,'L',true);
    //                     }
    //                     $totalTuitionFee = $totalTuitionFee + $cost;
    //                 }
    //             }
                
    //         }
    //         $this->mypdf->Cell(25,$height,number_format($totalTuitionFee,2,',','.'),1,0,'L',true); 


    //     $setY = $setY + $height;
    //     $this->mypdf->SetXY($setX,$setY); 
    //     $this->mypdf->SetFillColor(255, 255, 255);
    //     $this->mypdf->SetFont('Arial','B',$setFont);
    //     $this->mypdf->Cell(50,$height,'Biaya yang harus dibayar',1,0,'L',true);
    //     $totalTuitionFee = 0;
    //     $PTIDSelect = $this->m_master->showData_array('db_finance.payment_type');
    //     for ($i=0; $i < count($PTIDSelect); $i++) {
    //         foreach ($Personal[0] as $key => $value) {
    //             if ($PTIDSelect[$i]['Abbreviation'] == $key ) {
    //                 $this->mypdf->Cell(25,$height,number_format($Personal[0][$key],2,',','.'),1,0,'L',true);
    //                 $totalTuitionFee = $totalTuitionFee + $Personal[0][$key];
    //             } 
    //         } 
           
    //     }

    //     $this->mypdf->Cell(25,$height,number_format($totalTuitionFee,2,',','.'),1,0,'L',true); 

    //     $setXvalue = $setX;
    //     $setY = $setY + 7;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('Jadwal pembayaran untuk semester pertama dengan cicilan :');

    //      $setY = $setY + $height;

    //     $this->mypdf->SetXY($setX,$setY); 
    //     $this->mypdf->SetFillColor(226, 226, 226);
    //     $this->mypdf->Cell(40,$height,'Pembayaran',1,0,'C',true);
    //     $this->mypdf->Cell(60,$height,'Tanggal',1,0,'C',true);
    //     $this->mypdf->Cell(70,$height,'Jumlan',1,1,'C',true);

    //     $cicilan_tulis = array('Cicilan Pertama','Cicilan Kedua','Cicilan Ketiga','Cicilan Keempat','Cicilan Kelima','Cicilan Keenam','Cicilan Ketujuh');

    //     for ($i=0; $i < count($arr_cicilan); $i++) {
    //         $setY = $setY + $height; 
    //         $this->mypdf->SetXY($setX,$setY); 
    //         $this->mypdf->SetFillColor(255, 255, 255);
    //         $this->mypdf->Cell(40,$height,$cicilan_tulis[$i],1,0,'L',true);
    //         $Deadline = date('Y-m-d', strtotime($arr_cicilan[$i]['Deadline']));
    //         $this->mypdf->Cell(60,$height,$this->m_master->getIndoBulan($Deadline),1,0,'L',true);
    //         $this->mypdf->Cell(70,$height,'Rp '.number_format($arr_cicilan[$i]['Invoice'],2,',','.'),1,1,'L',true);

    //     }


    //     $setXvalue = $setX;
    //     $setY = $setY + 7;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('Pembayaran dapat dilakukan melalui transfer ke Bank BCA : ');

    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('-. Atas Nama');

    //     $setXvalue = $setXvalue + 25;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML(':');

    //     $setXvalue = $setXvalue + 3;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('<b>Yayasan Pendidikan Agung Podomro</b>');

    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('-. Nomor Account');

    //     $setXvalue = $setXvalue + 25;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML(':');

    //     $setXvalue = $setXvalue + 3;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('<b>161.3888.555</b>');

    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('-. Keterangan');

    //     $setXvalue = $setXvalue + 25;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML(':');

    //     $setXvalue = $setXvalue + 3;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('<b>'.$NameTbl.'</b>');


        
    //     $setXvalue = $setX;
    //     $setY = $setY + 10;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('Note: Mohon bukti pembayaran difax ke nomor 021-29200455 atau diemail ke admissions@podomorouniversity.com dengan subyek');

    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('<b>Pembayaran Uang Kuliah atas Nama '.$NameTbl.'.');

    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('Untuk info lebih lanjut dapat menghubungi Podomoro University di 021-29200456 ext 101-103/HP : 0821 1256 4900');

    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('Selamat bergabung di Keluarga Besar Podomoro university');

    //     $setXvalue = $setX;
    //     $setY = $setY + 10;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('Hormat Kami,');

    //     $setXvalue = $setX;
    //     $setY = $setY + 15;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('Dept. of Admissions and Marketing');

    //     $setXvalue = $setX;
    //     $setY = $setY + 10;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('<b>Perhatian:</b>');

    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('1.');
    //     $setXvalue = $setXvalue + 3;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->MultiCell(0, 5, 'Beasiswa berlaku untuk pembayaran sesuai dengan tanggal yang telah ditentukan di atas. Apabila melewati batas waktu yang telah ditentukan maka mengikuti program pembayaran pada gelombang tersebut.', 0,'L');

    //     $setXvalue = $setX;
    //     $setY = $setY + 10;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('2.');
    //     $setXvalue = $setXvalue + 3;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->MultiCell(0, 5, 'Pembayaran dianggap valid saat dana efektif pada rekening YPAP, bukan berdasarkan tanggal slip setoran / bukti transfer.', 0,'L');

    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('3.');
    //     $setXvalue = $setXvalue + 3;
    //     // $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('Jika sampai kegiatan perkuliahan dimulai masih ada kewajiban biaya studi yang belum diselesaikan, maka mahasiswa tersebut dianggap');
    //     $setXvalue = $setX + 3;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('<u>mengundurkan diri</u>');

    //     $setXvalue = $setX;
    //     $setY = $setY + 5;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->writeHTML('4.');
    //     $setXvalue = $setXvalue + 3;
    //     $this->mypdf->SetXY($setXvalue,$setY);
    //     $this->mypdf->SetTextColor(0,0,0);
    //     $this->mypdf->SetFont('Arial','',$setFont);
    //     $this->mypdf->MultiCell(0, 5, 'Surat ini dicetak otomatis oleh komputer dan tidak memerlukan tanda tangan pejabat yang berwenang.', 0,'L');

    //      $path = './document';
    //      $path = $path.'/'.$filename;
    //      $this->mypdf->Output($path,'F');
    //      // echo json_encode($filename);
    //      $text = 'Dear '.$Personal[0]['Name'].',<br><br>
    //                  Plase find attached your Tuition Fee.<br>
    //                  For Detail your payment, please see in '.url_registration."login/";
    //      if($_SERVER['SERVER_NAME']!='localhost' && $_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {            
    //         // $to = $Personal[0]['Email'].','.'admission@podomorouniversity.ac.id';
    //         $to = 'admission@podomorouniversity.ac.id';
    //      }
    //      else
    //      {
    //         $to = 'alhadirahman22@gmail.com,alhadi.rahman@podomorouniversity.ac.id';
    //      }
    //      $subject = "Podomoro University Tuition Fee";
    //      $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text,$path);

    // }

    public function tuition_fee_approved()
    {
        $input = $this->getInputToken();
        $FormulirCode = $input['FormulirCode'];

        $this->load->library('pagination');
        $config = $this->config_pagination_default_ajax($this->m_admission->count_getDataCalonMhsTuitionFee_delete($FormulirCode,'p.Status = "Approved"'),10,5);
        $this->pagination->initialize($config);
        $page = $this->uri->segment(5);
        $start = ($page - 1) * $config["per_page"];

        // $this->data['payment_type'] = json_encode($this->m_master->showData_array('db_finance.payment_type'));
        $this->data['payment_type'] = json_encode($this->m_master->caribasedprimary('db_finance.payment_type','Type','0'));
        $this->data['getDataCalonMhs'] = json_encode($this->m_admission->getDataCalonMhsTuitionFee_approved($config["per_page"], $start,$FormulirCode,'p.Status = "Approved"'));
        $content = $this->load->view('page/'.$this->data['department'].'/approved/page_tuition_fee_approved',$this->data,true);
        $output = array(
        'pagination_link'  => $this->pagination->create_links(),
        'loadtable'   => $content,
        );
        echo json_encode($output);
    }

    public function page_set_tagihan_mhs()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_set_tagihan_mhs',$this->data,true);
        $this->temp($content);
    }

    public function page_set_potongan_lain(){
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_set_potongan_lain',$this->data,true);
        $this->temp($content);
    }

    public function ajax_set_potongan_lain(){
        $rs = ['status' => 0,'msg' => ''];
        $dataToken = json_decode(json_encode($this->getInputToken()),true) ;
        $rs = $this->m_finance->save_potongan($dataToken);
        echo json_encode($rs);
    }

    public function get_tagihan_mhs()
    {
        $input = $this->getInputToken();

        $this->load->library('pagination');
        // count all
           if ($input['PTID'] == 5 || $input['PTID'] == 6) { // semester antara
               // search SemesterID Semester Antara
               $G_data = $this->m_master->caribasedprimary('db_academic.semester_antara','ID',$input['Semester']);
               if (count($G_data) > 0) {
                   $input['Semester'] = $G_data[0]['SemesterID'];
               }
           } 

        $count = $this->m_finance->count_get_tagihan_mhs2($input['ta'],$input['prodi'],$input['PTID'],$input['NPM'],$input['Semester']);

        $config = $this->config_pagination_default_ajax($count,10,3);
        $this->pagination->initialize($config);
        $page = $this->uri->segment(3);
        $start = ($page - 1) * $config["per_page"];

        // $data = $this->m_finance->get_tagihan_mhs($input['ta'],$input['prodi'],$input['PTID'],$input['NPM'],$config["per_page"], $start);
        $data = $this->m_finance->get_tagihan_mhs2($input['ta'],$input['prodi'],$input['PTID'],$input['NPM'],$input['Semester'],$config["per_page"], $start);
        $output = array(
        'pagination_link'  => $this->pagination->create_links(),
        'loadtable'   => $data,
        'total' => $count,
        );
        echo json_encode($output);
    }

    public function submit_tagihan_mhs()
    {
        $msg = '';
        $Input = $this->getInputToken();
        $Input = $Input['arrValueCHK'];
        /*print_r($Input[0]->PTID);
        die();*/
        $countSuccess = 0;
        $countSuccessVA = 1;
        // create va
            $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
            for ($i=0; $i < count($Input); $i++) { 
                // get Deadline
                $fieldEND = '';
                $desc = '';
                switch ($Input[$i]->PTID) {
                    case '1':
                        $fieldEND = '';
                        $desc = '';
                        break;
                    case '2':
                        $fieldEND = 'bayarBPPEnd';
                        $desc = 'Pembayaran BPP';
                        break;
                    case '3':
                        $fieldEND = 'bayarEnd';
                        $desc = 'Pembayaran SKS';
                        break;    
                    default:
                        $fieldEND = '';
                        $desc = '';
                        break;
                }

                if ($fieldEND == 'bayarEnd' || $fieldEND == 'bayarBPPEnd')
                {
                    $getDeadlineTagihanDB = $this->m_finance->getDeadlineTagihanDB($fieldEND,$Input[$i]->semester);
                    // check Deadline Tagihan telah melewati tanggal sekarang atau belum
                    if ($this->session->userdata('finance_auth_Policy_SYS') == 1) {
                        $chkTgl = $this->m_master->checkTglNow($getDeadlineTagihanDB);
                    }
                    else
                    {
                        $chkTgl = true;
                    }
                    
                    if ($chkTgl) {
                        $getDataMhsBYNPM = $this->m_master->getDataMhsBYNPM($Input[$i]->NPM,$Input[$i]->ta);
                        $payment = str_replace("Rp.","", $Input[$i]->Invoice);
                        $payment = trim(str_replace(",-","", $payment));
                        $payment = trim(str_replace(".","", $payment));
                        $DeadLinePayment = $getDeadlineTagihanDB.' 23:59:00';

                        // proses langsung pembayaran gratis atau = 0
                        if ($payment == 0) {
                            $aa = $this->m_finance->insertaDataPayment($Input[$i]->PTID,$Input[$i]->semester,$Input[$i]->NPM,$payment,$Input[$i]->Discount,"1",0);
                            $ab = $this->m_finance->insertaDataPaymentStudents($aa,$payment,0,$DeadLinePayment,1);
                        }
                        else
                        {
                            $Name = $getDataMhsBYNPM[0]['Name'];
                            $Email = $getDataMhsBYNPM[0]['EmailPU'];
                            $VA_number = $this->m_finance->getVANumberMHS($Input[$i]->NPM);

                            // cek VA policy
                            if ($this->session->userdata('finance_auth_Policy_SYS') == 1) {
                                $create_va = $this->m_finance->create_va_Payment($payment,$DeadLinePayment, $Name, $Email,$VA_number,$description = $desc,$tableRoutes = 'db_finance.payment_students');
                                if ($create_va['status']) {
                                    // After create va insert data to db_finance.payment  and db_finance.payment_students
                                    $countSuccessVA++;
                                    $aa = $this->m_finance->insertaDataPayment($Input[$i]->PTID,$Input[$i]->semester,$Input[$i]->NPM,$payment,$Input[$i]->Discount);
                                    $ab = $this->m_finance->insertaDataPaymentStudents($aa,$payment,$create_va['msg']['trx_id'],$create_va['msg']['datetime_expired']);
                                }
                                else
                                {
                                    $msg .= 'Tidak bisa Create VA dengan Nama : '.$Name.' dan NPM : '.$Input[$i]->NPM.'<br>';
                                }
                            }
                            else
                            {
                                $countSuccessVA++;
                                $aa = $this->m_finance->insertaDataPayment($Input[$i]->PTID,$Input[$i]->semester,$Input[$i]->NPM,$payment,$Input[$i]->Discount);
                                $ab = $this->m_finance->insertaDataPaymentStudents($aa,$payment,0,$DeadLinePayment);
                            }
                            
                        }
                    }
                    else
                    {
                        $msg = 'Tanggal Deadline telah melewati tanggal sekarang. <br> Data tidak dapat di Proses';
                    }

                    
                }
                else
                {
                    $getDataMhsBYNPM = $this->m_master->getDataMhsBYNPM($Input[$i]->NPM,$Input[$i]->ta);
                    $payment = str_replace("Rp.","", $Input[$i]->Invoice);
                    $payment = trim(str_replace(",-","", $payment));
                    $payment = trim(str_replace(".","", $payment));
                    
                    if ($Input[$i]->PTID == 5 || $Input[$i]->PTID == 6) {
                       $G_data = $this->m_master->caribasedprimary('db_academic.sa_academic_years','SASemesterID',$Input[$i]->semester);
                       $DeadLinePayment = $G_data[0]['EndPayment'].' 23:59:00';
                    }
                    else
                    {
                        $DeadLinePayment = $Input[$i]->Deadline.' 23:59:00';
                    }

                    if ($payment == 0) {
                        $aa = $this->m_finance->insertaDataPayment($Input[$i]->PTID,$Input[$i]->semester,$Input[$i]->NPM,$payment,$Input[$i]->Discount,"1",0);
                        $ab = $this->m_finance->insertaDataPaymentStudents($aa,$payment,0,$DeadLinePayment,1);
                    }
                    else
                    {
                        $Name = $getDataMhsBYNPM[0]['Name'];
                        $Email = $getDataMhsBYNPM[0]['EmailPU'];
                        $VA_number = $this->m_finance->getVANumberMHS($Input[$i]->NPM);

                        // cek VA policy
                        if ($this->session->userdata('finance_auth_Policy_SYS') == 1) {
                            $create_va = $this->m_finance->create_va_Payment($payment,$DeadLinePayment, $Name, $Email,$VA_number,$description = $desc,$tableRoutes = 'db_finance.payment_students');
                            if ($create_va['status']) {
                                // After create va insert data to db_finance.payment  and db_finance.payment_students
                                $countSuccessVA++;
                                $aa = $this->m_finance->insertaDataPayment($Input[$i]->PTID,$Input[$i]->semester,$Input[$i]->NPM,$payment,$Input[$i]->Discount);
                                $ab = $this->m_finance->insertaDataPaymentStudents($aa,$payment,$create_va['msg']['trx_id'],$create_va['msg']['datetime_expired']);
                            }
                            else
                            {
                                $msg .= 'Tidak bisa Create VA dengan Nama : '.$Name.' dan NPM : '.$Input[$i]->NPM.'<br>';
                            }
                        }
                        else
                        {
                            $countSuccessVA++;
                            $aa = $this->m_finance->insertaDataPayment($Input[$i]->PTID,$Input[$i]->semester,$Input[$i]->NPM,$payment,$Input[$i]->Discount);
                            $ab = $this->m_finance->insertaDataPaymentStudents($aa,$payment,0,$DeadLinePayment);
                        }
                        
                    }

                }
                
            }
            echo json_encode($msg);

    }

    public function page_cek_tagihan_mhs($page = '')
    {
        $this->data['NPM'] = $page;
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_cek_tagihan_mhs',$this->data,true);
        $this->temp($content);
    }

    public function get_created_tagihan_mhs($page = null)
    {
        $input = $this->getInputToken();
        $this->load->library('pagination');
        if (!array_key_exists('StatusPayment', $input)) {
            $input['StatusPayment'] = '';
        }

        if (!array_key_exists('ChangeStatus', $input)) {
            $input['ChangeStatus'] = '';
        }

        if ($input['PTID'] == 5 || $input['PTID'] == 6) { // semester antara
            // search SemesterID Semester Antara
            $G_data = $this->m_master->caribasedprimary('db_academic.semester_antara','ID',$input['Semester']);
            if (count($G_data) > 0) {
                $input['Semester'] = $G_data[0]['SemesterID'];
            }
        }

        if (!array_key_exists('Semester', $input)) {
            $sms = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
            $input['Semester'] = $sms[0]['ID'];
        }

        // count
        $count = $this->m_finance->count_get_created_tagihan_mhs($input['ta'],$input['prodi'],$input['PTID'],$input['NIM'],$input['Semester'],$input['StatusPayment'],$input['ChangeStatus']);
        $config = $this->config_pagination_default_ajax($count,15,3);
        $this->pagination->initialize($config);
        $page = $this->uri->segment(3);
        $start = ($page - 1) * $config["per_page"];
        $data = $this->m_finance->get_created_tagihan_mhs($input['ta'],$input['prodi'],$input['PTID'],$input['NIM'],$input['Semester'],$input['StatusPayment'],$input['ChangeStatus'],$config["per_page"], $start);
        $output = array(
        'pagination_link'  => $this->pagination->create_links(),
        'loadtable'   => $data,
        'totaldata'   => $count,
        );
        echo json_encode($output);
    }

    public function get_created_tagihan_mhs_not_approved($page = null)
    {
        $input = $this->getInputToken();
        $this->load->library('pagination');
        // count
        if (!array_key_exists('Semester', $input)) {
            $sms = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
            $input['Semester'] = $sms[0]['ID'];
        }

        $count = $this->m_finance->count_get_created_tagihan_mhs_not_approved($input['ta'],$input['prodi'],$input['PTID'],$input['NIM'],$input['Semester']);
        $config = $this->config_pagination_default_ajax($count,5,3);
        $this->pagination->initialize($config);
        $page = $this->uri->segment(3);
        $start = ($page - 1) * $config["per_page"];
        $data = $this->m_finance->get_created_tagihan_mhs_not_approved($input['ta'],$input['prodi'],$input['PTID'],$input['NIM'],$input['Semester'],$config["per_page"], $start);
        $output = array(
        'pagination_link'  => $this->pagination->create_links(),
        'loadtable'   => $data,
        'totaldata'   => $count,
        );
        echo json_encode($output);
    }

    public function approved_created_tagihan_mhs()
    {
        $Input = $this->getInputToken();
        $Input = $Input['arrValueCHK'];
        $this->m_finance->updatePaymentApprove($Input);
        
    }

    public function unapproved_created_tagihan_mhs()
    {
        $Input = $this->getInputToken();
        $Input = $Input['arrValueCHK'];
        $proses = $this->m_finance->updatePaymentunApprove($Input);
        echo json_encode($proses);
    }

    public function unapproved_created_tagihan_mhs_after_confirm()
    {
        $Input = $this->getInputToken();
        $Input = $Input['arrValueCHK'];
        $proses = $this->m_finance->updatePaymentunApprove_after_confirm($Input);
        echo json_encode($proses);
    }

    public function assign_to_change_status_mhs()
    {
        $Input = $this->getInputToken();
        $Input = $Input['arrValueCHK'];
        $proses = $this->m_finance->assign_to_change_status_mhs($Input);
        echo json_encode($proses);
    }

    public function cancel_tagihan_mhs()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_cancel_tagihan_mhs',$this->data,true);
        $this->temp($content);
    }

    public function cancel_created_tagihan_mhs()
    {
        $Input = $this->getInputToken();
        $Reason = $Input['Reason'];
        $Input = $Input['arrValueCHK'];
        $proses = $this->m_finance->cancel_created_tagihan_mhs2($Input,$Reason);
        echo json_encode($proses);
    }

    public function set_cicilan_tagihan_mhs()
    {
        $max_cicilan= $this->m_master->showData_array('db_admission.cfg_cicilan');
        $this->data['max_cicilan'] = $max_cicilan[0]['max_cicilan'];
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_set_cicilan_tagihan_mhs',$this->data,true);
        $this->temp($content);
    }

    public function set_cicilan_tagihan_mhs_submit()
    {
        $Input = $this->getInputToken();
        $fieldEND = '';
        $bool = true;
        $DeadLinePayment = '';
        $ID = $Input[0]->ID;
        for ($i=0; $i < count($Input); $i++) { 
                        // get Deadline
                        $fieldEND = '';
                        $desc = '';
                        switch ($Input[$i]->PTID) {
                            case '1':
                                $fieldEND = '';
                                $desc = '';
                                break;
                            case '2':
                                $fieldEND = 'bayarBPPEnd';
                                $desc = 'Pembayaran BPP';
                                break;
                            case '3':
                                $fieldEND = 'bayarEnd';
                                $desc = 'Pembayaran SKS';
                                break;    
                            default:
                                $fieldEND = '';
                                $desc = '';
                                break;
                        }

            if ($fieldEND == 'bayarEnd' || $fieldEND == 'bayarBPPEnd') {
                    if ($DeadLinePayment == '') {
                        $getDeadlineTagihanDB = $this->m_finance->getDeadlineTagihanDB($fieldEND,$Input[$i]->SemesterID);
                        $DeadLinePayment = $getDeadlineTagihanDB.' 23:59:00';  
                    }            
                       
                    // check Deadline Input telah melewati tanggal Deadline
                    if ($this->session->userdata('finance_auth_Policy_SYS') == 1) {
                        $aaa = $this->m_master->chkTgl($Input[$i]->Deadline,$DeadLinePayment);
                    }
                    else
                    {
                        $aaa = true;
                    }
                    
                    if (!$aaa) {
                        $bool = false;
                        break;
                    }
            }            
            
        }

        if ($bool) {
               // cari Biling ID status = 0
               $a = $this->m_finance->findDatapayment_studentsBaseID_payment($ID);
               if(count($a) != 1)
               {
                echo json_encode('Data ini telah diset cicilan, sehingga proses dihentikan.');
                //break;
                die();
               }
               else
               {
                    $now = date('Y-m-d H:i:s');
                    for ($i=0; $i < count($Input); $i++) { 
                      // update cicilan untuk array 0 atau array pertama
                      if ($i == 0) {
                        if ($this->session->userdata('finance_auth_Policy_SYS') == 1) {
                            // update va existing
                            $BilingID = $a[0]['BilingID'];
                            $checkVa = $this->m_finance->checkBiling($BilingID);
                            if ($checkVa['msg']['va_status'] != 2) {
                                $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
                                $trx_amount = $Input[$i]->Payment;
                                $datetime_expired = $Input[$i]->Deadline;
                                $customer_name = $getData[0]['customer_name'];
                                $customer_email = $getData[0]['customer_email'];
                                $update = $this->m_finance->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_students',$desc);
                                if ($update['status'] == 1) {
                                  // update data pada table db_finance.payment_students
                                    $this->m_finance->updateCicilanMHS($BilingID,$trx_amount,$datetime_expired);
                                }
                                else
                                {
                                  $arr['msg'] .= 'Va tidak bisa di update, error koneksi ke BNI <br>';
                                }
                            }
                            else
                            {
                                $sqlDelete = 'delete from db_finance.payment_students where BilingID = "'.$BilingID.'"';
                                $queryDelete=$this->db->query($sqlDelete, array());

                                $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
                                $VA_number = $getData[0]['virtual_account'];
                                $trx_amount = $Input[$i]->Payment;
                                $datetime_expired = $Input[$i]->Deadline;
                                $customer_name = $getData[0]['customer_name'];
                                $customer_email = $getData[0]['customer_email'];
                                // $update = $this->m_finance->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_students',$desc);
                                $update = $this->m_finance->create_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$VA_number,'Re-'.$desc,'db_finance.payment_students');
                                if ($update['status']) {
                                  // update data pada table db_finance.payment_students
                                    // $this->m_finance->updateCicilanMHS($BilingID,$trx_amount,$datetime_expired);
                                    $this->m_finance->insertaDataPaymentStudents($ID,$Input[$i]->Payment,$update['msg']['trx_id'],$Input[$i]->Deadline);
                                }
                                else
                                {
                                  $arr['msg'] .= 'Va tidak bisa di update, error koneksi ke BNI <br>';
                                }
                            }
                        } // exit if va status
                        else
                        {
                            // update data pada table db_finance.payment_students
                              $trx_amount = $Input[$i]->Payment;
                              $datetime_expired = $Input[$i]->Deadline;
                              $ID_psStudent = $a[0]['ID'];
                              $this->m_finance->UpdateCicilanbyID($ID_psStudent,0,$trx_amount,$datetime_expired);
                              //$this->m_finance->updateCicilanMHS(0,$trx_amount,$datetime_expired);

                        }
                        
                      }
                      else
                      {
                        $this->m_finance->insertaDataPaymentStudents($ID,$Input[$i]->Payment,"0",$Input[$i]->Deadline);
                      } 

                    }
                    echo json_encode(''); 
               }
        }
        else
        {
            echo json_encode('Tanggal yang anda input melewati tanggal akademik : '.$DeadLinePayment);
        }  

    }

    public function edit_cicilan_tagihan_mhs()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_edit_cicilan_tagihan_mhs',$this->data,true);
        $this->temp($content);
    }

    public function edit_cicilan_tagihan_mhs_submit()
    {
        $Input = $this->getInputToken();
        $msg = '';
        $proses = $this->m_finance->edit_cicilan_tagihan_mhs_submit($Input);
        $msg = $proses['msg'];
        echo json_encode($msg);

    }

    public function delete_cicilan_tagihan_mhs_submit()
    {
        $Input = $this->getInputToken();
        $msg = '';
        $proses = $this->m_finance->delete_cicilan_tagihan_mhs_submit($Input);
        $msg = $proses['msg'];
        echo json_encode($msg);
    }

    public function penerimaan_tagihan_mhs()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/penerimaan_pembayaran/penerimaan_pembayaran_mhs',$this->data,true);
        $this->temp($content);
    }

    public function get_pembayaran_mhs($page = null)
    {
        $input = $this->getInputToken();
        $this->load->library('pagination');
        $config = $this->config_pagination_default_ajax(1000,100,3);
        $this->pagination->initialize($config);
        $page = $this->uri->segment(3);
        $start = ($page - 1) * $config["per_page"];
        $data = $this->m_finance->get_pembayaran_mhs($input['ta'],$input['prodi'],$input['PTID'],$input['NIM'],$input['Semester'],$config["per_page"], $start);
        $output = array(
        'pagination_link'  => $this->pagination->create_links(),
        'loadtable'   => $data,
        );
        echo json_encode($output);
    }

    public function check_va()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/checkva/page_check_va',$this->data,true);
        $this->temp($content);
    }

    public function check_va_cari()
    {   
        $arr = array('msg' => '' , 'rs' => '');
        $Input = $this->getInputToken();
        $VA = $Input['VA'];
        // cari va desc dengan status = 0 pada table va_log
            $va_log = $this->m_finance->cari_va($VA);
            if ($va_log['msg'] == '') {
                $data = $va_log['data'];
                $arr['rs'] = $data;
            }
            else
            {
                $arr['msg'] = $va_log['msg'];
            }

        echo json_encode($arr);    
    }

    public function export_excel($token)
    {
        $key = "UAP)(*";
        $input = (array) $this->jwt->decode($token,$key);
        // print_r($input);
        $Semester = $input['Semester'];
        $Semester = explode('.', $Semester);
        $Semester = $Semester[1];
        $data = $input['Data'];
        $dataGenerate = $this->GroupingNPM($data);

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
        $excel2 = $excel2->load('./uploads/finance/TemplatePembayaran.xlsx'); // Empty Sheet
        $excel2->setActiveSheetIndex(0);

        $excel3 = $excel2->getActiveSheet();
        $excel3->setCellValue('A2', 'Rekap Penerimaan & AGING '.$Semester);

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
          'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ),
          'borders' => array(
            'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
            'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
            'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
            'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
          )
        );

        // start dari A7
        $a = 8;
        for ($i=0; $i < count($dataGenerate); $i++) {
           $no = $i + 1;  
           $excel3->setCellValue('A'.$a, $no); 
           $excel3->setCellValue('B'.$a, $dataGenerate[$i]['Nama']);
           $excel3->setCellValue('C'.$a, $dataGenerate[$i]['NPM']);
           $excel3->setCellValue('D'.$a, $dataGenerate[$i]['ProdiEng']);
           $excel3->setCellValue('E'.$a, $dataGenerate[$i]['SPP']);
           $excel3->setCellValue('F'.$a, $dataGenerate[$i]['Another']);
           $excel3->setCellValue('G'.$a, $dataGenerate[$i]['BPP']);
           $excel3->setCellValue('H'.$a, $dataGenerate[$i]['BPPKet']);
           $excel3->setCellValue('I'.$a, $dataGenerate[$i]['Credit']);
           $excel3->setCellValue('J'.$a, $dataGenerate[$i]['CreditKet']);

           // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
           $excel3->getStyle('A'.$a)->applyFromArray($style_row);
           $excel3->getStyle('B'.$a)->applyFromArray($style_row);
           $excel3->getStyle('C'.$a)->applyFromArray($style_row);
           $excel3->getStyle('D'.$a)->applyFromArray($style_row);
           $excel3->getStyle('E'.$a)->applyFromArray($style_row);
           $excel3->getStyle('F'.$a)->applyFromArray($style_row);
           $excel3->getStyle('G'.$a)->applyFromArray($style_row);
           $excel3->getStyle('H'.$a)->applyFromArray($style_row);
           $excel3->getStyle('I'.$a)->applyFromArray($style_row);
           $excel3->getStyle('J'.$a)->applyFromArray($style_row);
           $excel3->getStyle('K'.$a)->applyFromArray($style_row);

           $a = $a + 1; 
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
        // We'll be outputting an excel file  
        header('Content-type: application/vnd.ms-excel'); // jalan ketika tidak menggunakan ajax
        // It will be called file.xlss
        header('Content-Disposition: attachment; filename="file.xlsx"'); // jalan ketika tidak menggunakan ajax
        //$filename = 'PenerimaanPembayaran.xlsx';
        //$objWriter->save('./document/'.$filename);
        $objWriter->save('php://output'); // jalan ketika tidak menggunakan ajax

    }

    /*public function export_excel()
    {
        $input = $this->getInputToken();
        // print_r($input);
        $Semester = $input['Semester'];
        $Semester = explode('.', $Semester);
        $Semester = $Semester[1];
        $data = $input['Data'];
        $dataGenerate = $this->GroupingNPM($data);

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
        $excel2 = $excel2->load('./uploads/finance/TemplatePembayaran.xlsx'); // Empty Sheet
        $excel2->setActiveSheetIndex(0);

        $excel3 = $excel2->getActiveSheet();
        $excel3->setCellValue('A2', 'Rekap Penerimaan & AGING '.$Semester);

        // Buat sebuah variabel untuk menampung pengaturan style dari isi tabel
        $style_row = array(
          'alignment' => array(
            'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
          ),
          'borders' => array(
            'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
            'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
            'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
            'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
          )
        );

        // start dari A7
        $a = 8;
        for ($i=0; $i < count($dataGenerate); $i++) {
           $no = $i + 1;  
           $excel3->setCellValue('A'.$a, $no); 
           $excel3->setCellValue('B'.$a, $dataGenerate[$i]['Nama']);
           $excel3->setCellValue('C'.$a, $dataGenerate[$i]['NPM']);
           $excel3->setCellValue('D'.$a, $dataGenerate[$i]['ProdiEng']);
           $excel3->setCellValue('E'.$a, $dataGenerate[$i]['SPP']);
           $excel3->setCellValue('F'.$a, $dataGenerate[$i]['Another']);
           $excel3->setCellValue('G'.$a, $dataGenerate[$i]['BPP']);
           $excel3->setCellValue('H'.$a, $dataGenerate[$i]['BPPKet']);
           $excel3->setCellValue('I'.$a, $dataGenerate[$i]['Credit']);
           $excel3->setCellValue('J'.$a, $dataGenerate[$i]['CreditKet']);

           // Apply style row yang telah kita buat tadi ke masing-masing baris (isi tabel)
           $excel3->getStyle('A'.$a)->applyFromArray($style_row);
           $excel3->getStyle('B'.$a)->applyFromArray($style_row);
           $excel3->getStyle('C'.$a)->applyFromArray($style_row);
           $excel3->getStyle('D'.$a)->applyFromArray($style_row);
           $excel3->getStyle('E'.$a)->applyFromArray($style_row);
           $excel3->getStyle('F'.$a)->applyFromArray($style_row);
           $excel3->getStyle('G'.$a)->applyFromArray($style_row);
           $excel3->getStyle('H'.$a)->applyFromArray($style_row);
           $excel3->getStyle('I'.$a)->applyFromArray($style_row);
           $excel3->getStyle('J'.$a)->applyFromArray($style_row);
           $excel3->getStyle('K'.$a)->applyFromArray($style_row);

           $a = $a + 1; 
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
        // We'll be outputting an excel file  
        // header('Content-type: application/vnd.ms-excel'); // jalan ketika tidak menggunakan ajax
        // It will be called file.xlss
        // header('Content-Disposition: attachment; filename="file.xls"'); // jalan ketika tidak menggunakan ajax
        $filename = 'PenerimaanPembayaran.xlsx';
        $objWriter->save('./document/'.$filename);
        // $objWriter->save('php://output'); // jalan ketika tidak menggunakan ajax
        echo json_encode($filename);
    }*/

    public function import_price_list_mhs()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/master/page_import_price_list_mhs',$this->data,true);
        $this->temp($content);
    }

    public function submit_import_price_list_mhs()
    {
        // print_r($_FILES);
        if(isset($_FILES["fileData"]["name"]))
        {
          $path = $_FILES["fileData"]["tmp_name"];
          $arr_insert = array();
          include APPPATH.'third_party/PHPExcel/PHPExcel.php';
          $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
          $excel2 = $excel2->load($path); // Empty Sheet
          $objWorksheet = $excel2->setActiveSheetIndex(0);
          $CountRow = $objWorksheet->getHighestRow();
          $CountRow = $CountRow + 1;
          $Pay_Cond = $this->input->post('Pay_Cond');
         
          for ($i=2; $i < $CountRow; $i++) {
            $temp = array();
            $NPM = $objWorksheet->getCellByColumnAndRow(0, $i)->getCalculatedValue();
            $temp = array(
              'NPM' => $NPM,
              'Pay_Cond' => $Pay_Cond,
            );
            $arr_insert[] = $temp;
          }
          $this->db->update_batch('db_academic.auth_students', $arr_insert, 'NPM');
          echo json_encode(array('status'=> 1,'msg' => ''));
        }
        else
        {
          exit('No direct script access allowed');
        }
    }

    public function list_telat_bayar()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_list_telat_bayar',$this->data,true);
        $this->temp($content);
    }

    public function get_list_telat_bayar($page = null)
    {
        $input = $this->getInputToken();
        $this->load->library('pagination');
        // count
        $count = $this->m_finance->count_get_list_telat_bayar_mhs($input['ta'],$input['prodi'],$input['PTID'],$input['NIM']);
        $config = $this->config_pagination_default_ajax($count,100,3);
        $this->pagination->initialize($config);
        $page = $this->uri->segment(3);
        $start = ($page - 1) * $config["per_page"];
        $data = $this->m_finance->get_list_telat_bayar_mhs($input['ta'],$input['prodi'],$input['PTID'],$input['NIM'],$config["per_page"], $start);
        $output = array(
        'pagination_link'  => $this->pagination->create_links(),
        'loadtable'   => $data,
        );
        echo json_encode($output);
    }

    public function edit_telat_bayar($token)
    {
        $key = "UAP)(*";
        $input = (array) $this->jwt->decode($token,$key);
        $this->data['NPM'] = $input['NPM'];
        $this->data['PaymentID'] = $input['PaymentID'];
        $this->data['semester'] = $input['semester'];
        $this->data['PTID'] = $input['PTID'];
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_edit_telat_bayar',$this->data,true);
        $this->temp($content);

    }

    public function import_pembayaran_manual()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_import_pembayaran_manual',$this->data,true);
        $this->temp($content);
    }

    public function import_pembayaran_lain()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_import_pembayaran_lain',$this->data,true);
        $this->temp($content);
    }

    public function submit_import_pembayaran_lain()
    {
        // print_r($_FILES);
        if(isset($_FILES["fileData"]["name"]))
        {
          $path = $_FILES["fileData"]["tmp_name"];
          include APPPATH.'third_party/PHPExcel/PHPExcel.php';
          $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
          $excel2 = $excel2->load($path); // Empty Sheet
          $objWorksheet = $excel2->setActiveSheetIndex(0);
          $CountRow = $objWorksheet->getHighestRow();
          $CountRow = $CountRow + 1;
          $selectPTID = $this->input->post('selectPTID');
          $selectSemester = $this->input->post('selectSemester');
          $selectSemester = explode('.', $selectSemester);
          $maba = $this->input->post('maba');
         
          for ($i=2; $i < $CountRow; $i++) {
            $temp = array();
            $NPM = $objWorksheet->getCellByColumnAndRow(0, $i)->getCalculatedValue();
            $PTID = $selectPTID;
            $SemesterID = $selectSemester[0];
            $sql = 'select ID from db_finance.payment where PTID = "'.$PTID.'"  and SemesterID = "'.$SemesterID.'" and NPM = "'.$NPM.'" and Status = "1" ';
            $query=$this->db->query($sql, array())->result_array();

            $ID_payment = $query[0]['ID'];
            $dataSave = array(
                    'Status' => 1,
                    'BilingID' => 0,
                            );
            $this->db->where('ID_payment',$ID_payment);
            $this->db->where('Status',0);
            $this->db->update('db_finance.payment_students', $dataSave);
            
          }
          
          echo json_encode(array('status'=> 1,'msg' => ''));
        }
        else
        {
          exit('No direct script access allowed');
        }
    }

    public function submit_import_pembayaran_manual()
    {
        // print_r($_FILES);
        if(isset($_FILES["fileData"]["name"]))
        {
          $path = $_FILES["fileData"]["tmp_name"];
          include APPPATH.'third_party/PHPExcel/PHPExcel.php';
          $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
          $excel2 = $excel2->load($path); // Empty Sheet
          $objWorksheet = $excel2->setActiveSheetIndex(0);
          $CountRow = $objWorksheet->getHighestRow();
          $CountRow = $CountRow + 1;
          $selectPTID = $this->input->post('selectPTID');
          $selectSemester = $this->input->post('selectSemester');
          $selectSemester = explode('.', $selectSemester);
          $selectSemester = $selectSemester[0];
          $maba = $this->input->post('maba');
         
          for ($i=2; $i < $CountRow; $i++) {
            $temp = array();
            $NPM = $objWorksheet->getCellByColumnAndRow(0, $i)->getCalculatedValue();
            // get payment
               $auth = $this->m_master->caribasedprimary('db_academic.auth_students','NPM',$NPM);
               $Pay_Cond = $auth[0]['Pay_Cond'];
               $Year = $auth[0]['Year'];
               $db = 'ta_'.$Year.'.students';
               $ta = $this->m_master->caribasedprimary($db,'NPM',$NPM);
               $ProdiID = $ta[0]['ProdiID'];

               //$payment = $this->m_finance->getPriceBaseBintang($selectPTID,$ProdiID,$Year,$Pay_Cond);
               // check PTID, jika SKS / Credit dikali per sks yang diambil // 3 credit
                  // check checklist mahasiswa baru atau tidak
               $payment = $objWorksheet->getCellByColumnAndRow(3, $i)->getCalculatedValue();
                if ($selectPTID == 3) {
                    if ($maba == 1) {
                        $ProStuDefaultCredit = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                        $DefaultCredit = $ProStuDefaultCredit[0]['DefaultCredit'];
                        //$payment = (int)$payment * (int)$DefaultCredit;
                    }
                    else
                    {
                        // '.$db.'.study_planning
                        $Credit = $this->m_finance->getSKSMahasiswa('ta_'.$Year,$NPM);
                        //$payment = (int)$payment * (int)$Credit;
                    }
                }

            $aa = $this->m_finance->insertaDataPayment($selectPTID,$selectSemester,$NPM,$payment,0,"1",$this->session->userdata('NIP'));
            if ($aa != 0) {
                $bb = $this->m_finance->insertaDataPaymentStudents($aa,$payment,0,'0000-00-00 00:00:00',1);
            }
            
          }
          
          echo json_encode(array('status'=> 1,'msg' => ''));
        }
        else
        {
          exit('No direct script access allowed');
        }
    }

    public function import_beasiswa_mahasiswa()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/master/page_import_beasiswa_mahasiswa',$this->data,true);
        $this->temp($content);
    }

    public function  submit_import_beasiswa_mahasiswa()
    {
        // print_r($_FILES);
        if(isset($_FILES["fileData"]["name"]))
        {
          $path = $_FILES["fileData"]["tmp_name"];
          $arr_insert = array();
          include APPPATH.'third_party/PHPExcel/PHPExcel.php';
          $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
          $excel2 = $excel2->load($path); // Empty Sheet
          $objWorksheet = $excel2->setActiveSheetIndex(0);
          $CountRow = $objWorksheet->getHighestRow();
          $CountRow = $CountRow + 1;
          $selectPTID = $this->input->post('selectPTID');
          
          for ($i=2; $i < $CountRow; $i++) {
            $temp = array();
            $NPM = $objWorksheet->getCellByColumnAndRow(0, $i)->getCalculatedValue();
            $Discount = $objWorksheet->getCellByColumnAndRow(3, $i)->getCalculatedValue();

            // replace
            $Discount = $this->m_master->replaceKomaToTitik($Discount);
            if ($selectPTID == 2) {
               $temp = array(
                             'NPM' => $NPM,
                             'Bea_BPP' => $Discount,
                           );
            }
            elseif ($selectPTID == 3) {
                $temp = array(
                              'NPM' => $NPM,
                              'Bea_Credit' => $Discount,
                            );
            }
            $arr_insert[] = $temp;
          }

          $this->db->update_batch('db_academic.auth_students', $arr_insert, 'NPM');
          echo json_encode(array('status'=> 1,'msg' => ''));
        }
        else
        {
          exit('No direct script access allowed');
        }
    }

    public function mahasiswa()
    {
        if ($this->input->is_ajax_request()) {
            $dataToken = $this->getInputToken();
            $action = $dataToken['action'];
            switch ($action) {
                case 'SaveMenuPayment':
                    $rs = ['status' => 0,'msg' => ''];
                    $NPM = $dataToken['NPM'];
                    $data = $dataToken['data'];
                    $this->db->where('NPM',$NPM);
                    $this->db->update('db_academic.auth_students',$data);
                    if ($this->db->affected_rows() > 0 ){
                        $rs['status'] = 1;
                    }
                    else
                    {
                        $rs['msg'] = 'Data cannot be save, please contact IT';
                    }
                    echo json_encode($rs);
                    break;
                
                default:
                    # code...
                    break;
            }
        }
        else
        {
            $getSemester = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
            $data['getSemester'] = $getSemester;
            $content = $this->load->view('page/'.$this->data['department'].'/master/page_master_mahasiswa',$this->data,true);
            $this->temp($content);
        }
        
    }

    public function mahasiswa_list($page = null)
    {
        $input = $this->getInputToken();
        $this->load->library('pagination');
        // cari count
        $count = $this->m_finance->count_mahasiswa_list($input['ta'],$input['prodi'],$input['NPM']);
        $config = $this->config_pagination_default_ajax($count,5,4);   
        $this->pagination->initialize($config);
        $page = $this->uri->segment(4);
        $start = ($page - 1) * $config["per_page"];

        $data = $this->m_finance->mahasiswa_list($input['ta'],$input['prodi'],$input['NPM'],$config["per_page"], $start);
        $output = array(
        'pagination_link'  => $this->pagination->create_links(),
        'loadtable'   => $data,
        );
        echo json_encode($output);
    }

    public function edited_bea_bpp()
    {
        $input = $this->getInputToken();
        $dataSave = array(
                'Bea_BPP' => $input['Discount'],
                        );
        $this->db->where('NPM',$input['Npm']);
        $this->db->update('db_academic.auth_students', $dataSave);
    }

    public function edited_bea_credit()
    {
        $input = $this->getInputToken();
        $dataSave = array(
                'Bea_Credit' => $input['Discount'],
                        );
        $this->db->where('NPM',$input['Npm']);
        $this->db->update('db_academic.auth_students', $dataSave);
    }

    public function edited_pay_cond()
    {
        $input = $this->getInputToken();
        $dataSave = array(
                'Pay_Cond' => $input['bintang'],
                        );
        $this->db->where('NPM',$input['Npm']);
        $this->db->update('db_academic.auth_students', $dataSave);
    }

    public function download_log_va()
    {
       $content = $this->load->view('page/'.$this->data['department'].'/download_log_va/page_download_log_va',$this->data,true);
       $this->temp($content);
    }

    public function listfile_va()
    {
        $generate = $this->m_master->loadData_limit500('db_va.va_log_sftp','ID','desc');
        echo json_encode($generate);
    }

    public function page_master_discount()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/master/page_master_discount',$this->data,true);
        $this->temp($content);
    }

    public function load_discount()
    {
        $generate = $this->m_master->showData_array('db_finance.discount');
        echo json_encode($generate);
    }

    public function modalform_discount()
    {
        $input = $this->getInputToken();
        $this->data['action'] = $input['Action'];
        $this->data['id'] = $input['CDID'];
        if ($input['Action'] == 'edit') {
            $this->data['getData'] = $this->m_master->caribasedprimary('db_finance.discount','ID',$input['CDID']);
        }
        echo $this->load->view('page/'.$this->data['department'].'/master/modalform_discount',$this->data,true);
    }

    public function sbmt_discount()
    {
        $input = $this->getInputToken();
        switch ($input['Action']) {
            case 'add':
                $this->m_finance->inserData_discount($input['Discount']);
                break;
            case 'edit':
                $this->m_finance->editData_discount($input['Discount'],$input['CDID']);
                break;
            case 'delete':
                $this->m_finance->delete_id_table($input['CDID'],'discount');
                break;
            default:
                # code...
                break;
        }
    }

    public function testApprove()
    {
        $sql = 'select * from db_finance.payment where Status = "1" and DATE_FORMAT(UpdateAt,"%Y%m%d") = 20180808';
        $query=$this->db->query($sql, array())->result_array();
        for ($i=0; $i < count($query); $i++) { 
            $ID_payment = $query[$i]['ID'];
            $dataSave = array(
                    'Status' => 1,
                    'BilingID' => 0,
                            );
            $this->db->where('ID_payment',$ID_payment);
            $this->db->where('Status',0);
            $this->db->update('db_finance.payment_students', $dataSave);
        }
    }

    public function bayar_manual_mahasiswa()
    {
        $input = $this->getInputToken();
        $IDStudent = $input['IDStudent'];
        $bayar = $input['bayar'];
        if ($bayar == 1) {
            $dataSave = array(
                    'Status' => 1,
                    'BilingID' => 0,
                    'UpdateAt' => date('Y-m-d H:i:s'),
                    'DatePayment' => $input['DatePayment'],
                            );
            $this->db->where('ID',$IDStudent);
            $this->db->where('Status',0);
            $this->db->update('db_finance.payment_students', $dataSave);
        }
        else
        {
            $dataSave = array(
                    'Status' => 0,
                    'BilingID' => 0,
                    'UpdateAt' => date('Y-m-d H:i:s'),
                    'DatePayment' => null,
                            );
            $this->db->where('ID',$IDStudent);
            $this->db->where('Status',1);
            $this->db->update('db_finance.payment_students', $dataSave);
        }

        // cek lunas atau tidak
            $GetDataPaymentSt = $this->m_master->caribasedprimary('db_finance.payment_students','ID',$IDStudent);
            $ID_payment = $GetDataPaymentSt[0]['ID_payment'];
            $GetDataPaymentSt = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$ID_payment);
            $total = 0;
            for ($i=0; $i < count($GetDataPaymentSt); $i++) { 
                if ($GetDataPaymentSt[$i]['Status'] == 1) {
                    $total = (int)$total + (int)$GetDataPaymentSt[$i]['Invoice'];
                }
            }

            // print_r($total);die();
            $GetDataPayment = $this->m_master->caribasedprimary('db_finance.payment','ID',$ID_payment);

            // check ToChange
                $ToChange = ($GetDataPayment[0]['ToChange'] == 1) ? 2 : 0; 

            $Invoice = $GetDataPayment[0]['Invoice'];
            if ($total >= $Invoice) {
                $dataSave = array(
                        'Status' =>"1",
                        // 'ToChange' => 0,
                        'ToChange' => $ToChange,
                        'UpdateAt' => date('Y-m-d H:i:s'),
                        'UpdatedBy' => $this->session->userdata('NIP'),
                                );
                $this->db->where('ID',$ID_payment);
                $this->db->update('db_finance.payment', $dataSave);
            }
        
    }

    public function set_tuition_fee_delete_data()
    {
      $input = $this->getInputToken();
      $ID_register_formulir = $input[0];
      $InputReason = $this->input->post('InputReason');
      $dataGet = $this->m_master->caribasedprimary('db_finance.register_admisi_rev','ID_register_formulir',$ID_register_formulir);
      $count = count($dataGet);
      $arr_Count = $count - 1;
      $RevNo = (count($dataGet) == 0) ? 1 : $dataGet[$arr_Count]['RevNo'] + 1;
      $dataSave = array(  
          'ID_register_formulir' => $ID_register_formulir,
          'RevNo' => $RevNo,
          'Note' => 'Cancel / Reject, '.$InputReason,
          'RevBy' => $this->session->userdata('NIP'),
          'RevAt' => date('Y-m-d H:i:s'),
      );
      $this->db->insert('db_finance.register_admisi_rev', $dataSave);
      $this->m_admission->set_tuition_fee_delete_data($input);

      // save di register_admisi_rev

      // send email to admission
      $getEmailDB = $this->m_master->caribasedprimary('db_admission.email_to','Function','Admisi');
      $text = "Dear Team,<br><br>
                    Please following below list of prospective student bills rejected by Finance, <br><br>
                    <table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'><tr><td width=35 valign=top style='width:26.6pt;border:solid windowtext 1.0pt;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>NO<o:p></o:p></p></td><td width=270 valign=top style='width:202.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>Nama<o:p></o:p></p></td><td width=162 valign=top style='width:121.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>Prody<o:p></o:p></p></td><td width=156 valign=top style='width:116.9pt;border:solid windowtext 1.0pt;border-left:none;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>Formulir Code<o:p></o:p></p></td></tr>
                ";    
      $arr_list = array();
      for ($i=0; $i < count($input); $i++) { 
          $GetDataMHS = $this->m_admission->getDataPersonal($input[$i]);
          $No = $i + 1;
          $Name = $GetDataMHS[0]['Name'];
          $Prody = $GetDataMHS[0]['NameProdyIND'];
          $FormulirCode = $GetDataMHS[0]['FormulirCode'];
          $text .= "<tr><td width=35 valign=top style='width:26.6pt;border:solid windowtext 1.0pt;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>".$No."<o:p></o:p></p></td><td width=270 valign=top style='width:202.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>".$Name."<o:p></o:p></p></td><td width=162 valign=top style='width:121.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>".$Prody."<o:p></o:p></p></td><td width=156 valign=top style='width:116.9pt;border:solid windowtext 1.0pt;border-left:none;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>".$FormulirCode."<o:p></o:p></p></td></tr>";
      }
      $text .= '</table>';
      

      // $table = "<table class=MsoTableGrid border=1 cellspacing=0 cellpadding=0 style='border-collapse:collapse;border:none'><tr><td width=35 valign=top style='width:26.6pt;border:solid windowtext 1.0pt;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>NO<o:p></o:p></p></td><td width=270 valign=top style='width:202.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>Nama<o:p></o:p></p></td><td width=162 valign=top style='width:121.5pt;border:solid windowtext 1.0pt;border-left:none;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>Prody<o:p></o:p></p></td><td width=156 valign=top style='width:116.9pt;border:solid windowtext 1.0pt;border-left:none;padding:0in 5.4pt 0in 5.4pt'><p class=MsoNormal>Formulir Code<o:p></o:p></p></td></tr></table>";
      $Email = 'alhadi.rahman@podomorouniversity.ac.id';
      if($_SERVER['SERVER_NAME']!='localhost' && $_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {  
        $Email = 'admission@podomorouniversity.ac.id';
      }
      $to = $Email;
      $subject = "Podomoro University Notification Reject Tuition Fee";
      $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
    }

    public function bayar_manual_mahasiswa_formulironline()
    {
        $input = $this->getInputToken();
        $Year = $input['Year'];
        $tgl = $input['tgl'];
        $sql = "select FormulirCode from db_admission.formulir_number_online_m where Status = 0 and Years ='".$Year."' order by ID asc limit 1";
        $query=$this->db->query($sql, array())->result_array();
        // get formulir online di global
        $sql2 = "select FormulirCodeGlobal from db_admission.formulir_number_global where Status = 0 and Years ='".$Year."' 
                and TypeFormulir = 'On'
                order by ID asc limit 1";
        $query2=$this->db->query($sql2, array())->result_array();
        if (count($query) > 0 && count($query2) > 0) {
              $RegID = $input['RegID'];
              // update token & password sama
                $getData__ = $this->m_master->caribasedprimary('db_admission.register','ID',$RegID);
                $MomenUnix = $getData__[0]['MomenUnix'];
                $Email = $getData__[0]['Email'];
                $Pass = $this->m_master->genratePassword($Email,$MomenUnix);

              $dataSave = array(
                    'BilingID' => 0,
                    'Token' => $Pass,
                    'Password' => $Pass,
                            );
            $this->db->where('ID',$RegID);
            $this->db->update('db_admission.register', $dataSave);

            // insert to another table
            $getData = $this->m_master->caribasedprimary('db_admission.register','ID',$RegID);
            $Email = $getData[0]['Email'];
            $RegisterID = $getData[0]['ID'];
            $this->m_master->saveDataToVerification_offline($RegisterID);
            $getData = $this->m_master->caribasedprimary('db_admission.register_verification','RegisterID',$RegisterID);
            $RegVerificationID = $getData[0]['ID'];
            $FormulirCode = $this->m_finance->getFormulirCode('online',$Year);
            // save data to register_verified
            $this->m_master->saveDataRegisterVerified($RegVerificationID,$FormulirCode,$tgl,$this->session->userdata('NIP'));
            if ($_SERVER['SERVER_NAME'] != 'pcam.podomorouniversity.ac.id') {
                $Email = 'alhadirahman22@gmail.com';
            }
            $text = 'Dear Candidate,<br><br>
                        Your payment has been received,<br>
                        Please click link below to login your portal <br>
                        '.url_registration."login/".' <br><br>
                        Username : '.$Email.' <br>
                        Password / Token : '.$MomenUnix.'
                    ';        
            $to = $Email;
            $subject = "Podomoro University Formulir Registration";
            $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text);
            echo json_encode(1);
        }
        else
        {
            echo json_encode(0);
        }

    }

    public function penerimaan_pembayaran_biaya()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/penerimaan_pembayaran/penerimaan_pembayaran_admission',$this->data,true);
        $this->temp($content);
    }

    public function getPayment_admission()
    {
        $requestData= $_REQUEST;
        // print_r($requestData);
        $reqTahun = $this->input->post('tahun');
        // $totalData = $this->m_finance->getCountAllPayment_admission();
        // get total data
        $sqlTotalData = 'select count(*) as total from (
                select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,
                f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
                n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
                if((select count(*) as total from db_admission.register_nilai where Status = "Approved" and ID_register_formulir = a.ID limit 1) > 0,"Rapor","Ujian")
                as status1,p.CreateAT,p.CreateBY,b.FormulirCode,p.TypeBeasiswa,p.FileBeasiswa,
                if( (select count(*) as total from db_finance.payment_pre where ID_register_formulir = a.ID limit 1) > 1,"Cicilan","Tidak Cicilan") as cicilan,
                if((select count(*) as total from db_finance.payment_pre where `Status` = 0 and ID_register_formulir = a.ID limit 1) = 0 ,"Lunas","Belum Lunas") as StatusPayment,px.No_Ref,
                if(
                  (select count(*) as total from db_finance.register_refund where ID_register_formulir = a.ID ) > 0,"Refund",""
                ) as CekRefund
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
                left join db_admission.formulir_number_offline_m as px
                on px.FormulirCode = b.FormulirCode
                where p.Status = "Approved"  and d.SetTa = "'.$reqTahun.'" group by a.ID

                ) SubQuery
            ';
        $sqlTotalData.= ' where (Name LIKE "'.$requestData['search']['value'].'%" or NamePrody LIKE "%'.$requestData['search']['value'].'%"
                        or FormulirCode LIKE "'.$requestData['search']['value'].'%" or SchoolName LIKE "%'.$requestData['search']['value'].'%"
                        or StatusPayment LIKE "'.$requestData['search']['value'].'%" or cicilan LIKE "'.$requestData['search']['value'].'%"
                        or No_Ref LIKE "'.$requestData['search']['value'].'%" 
                        )
                        ';
        $queryTotalData = $this->db->query($sqlTotalData)->result_array();
        $totalData = $queryTotalData[0]['total'];

        $sql = 'select * from (
                select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,
                f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
                n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
                if((select count(*) as total from db_admission.register_nilai where Status = "Approved" and ID_register_formulir = a.ID limit 1) > 0,"Rapor","Ujian")
                as status1,p.CreateAT,p.CreateBY,b.FormulirCode,p.TypeBeasiswa,p.FileBeasiswa,p.Pay_Cond,
                if( (select count(*) as total from db_finance.payment_pre where ID_register_formulir = a.ID limit 1) > 1,"Cicilan","Tidak Cicilan") as cicilan,
                if((select count(*) as total from db_finance.payment_pre where `Status` = 0 and ID_register_formulir = a.ID limit 1) = 0 ,"Lunas","Belum Lunas") as StatusPayment,px.No_Ref,
                if(
                  (select count(*) as total from db_finance.register_refund where ID_register_formulir = a.ID ) > 0,"Refund",""
                ) as CekRefund
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
                left join db_admission.formulir_number_offline_m as px
                on px.FormulirCode = b.FormulirCode
                where p.Status = "Approved"  and d.SetTa = "'.$reqTahun.'" group by a.ID

                ) SubQuery
            ';

        $sql.= ' where (Name LIKE "'.$requestData['search']['value'].'%" or NamePrody LIKE "%'.$requestData['search']['value'].'%"
                or FormulirCode LIKE "'.$requestData['search']['value'].'%" or SchoolName LIKE "%'.$requestData['search']['value'].'%"
                or StatusPayment LIKE "'.$requestData['search']['value'].'%" or cicilan LIKE "'.$requestData['search']['value'].'%"
                or No_Ref LIKE "'.$requestData['search']['value'].'%" 
                )
                ';
        $sql.= ' ORDER BY StatusPayment ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $No = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            // $nestedData[] = '<input type="checkbox" name="id[]" value="'.$row['ID_register_formulir'].'">';
            $nestedData[] = $No;
            $nestedData[] = $row['NamePrody'];
            $nestedData[] = $this->m_master->setBintang_HTML($row['Pay_Cond']).'<br/>'.$row['Name'].'<br>'.$row['Email'];
            $FormulirCode = ($row['No_Ref'] != "" || $row['No_Ref'] != null ) ? $row['FormulirCode'].' / '.$row['No_Ref'] : $row['FormulirCode'];
            $nestedData[] = $FormulirCode;

            // get tagihan
            $getTagihan = $this->m_admission->getPaymentType_Cost_created($row['ID_register_formulir']);
            $tagihan = '';
            for ($j=0; $j < count($getTagihan); $j++) { 
                $tagihan .= $getTagihan[$j]['Abbreviation'].' : '.'Rp '.number_format($getTagihan[$j]['Pay_tuition_fee'],2,',','.').'<br>';
            }

            $nestedData[] = $tagihan;
            $nestedData[] = $row['cicilan'];
            $nestedData[] = '<button class="btn btn-inverse btn-notification btn-show" id-register-formulir = "'.$row['ID_register_formulir'].'" email = "'.$row['Email'].'" Nama = "'.$row['Name'].'">Show</button>';
            // refund or not
            $refundShow = '';
            if (!empty($row['CekRefund'])) {
                $refundShow = '<br/><label style = "color:red;">Refund</label>';
            }

            $nestedData[] = $row['StatusPayment'].$refundShow;
            $nestedData[] = '<button class = "btn btn-primary btn-payment" id-register-formulir = "'.$row['ID_register_formulir'].'" Nama = "'.$row['Name'].'">Action</button>';
           
            $data[] = $nestedData;
            $No++;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }


    public function getPayment_admission_edit_cicilan()
    {
        $requestData= $_REQUEST;
        $totalData = $this->m_finance->getCountAllPayment_admission();

        $sql = 'select * from (
                select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,
                f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
                n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
                if((select count(*) as total from db_admission.register_nilai where Status = "Approved" and ID_register_formulir = a.ID limit 1) > 0,"Rapor","Ujian")
                as status1,p.CreateAT,p.CreateBY,b.FormulirCode,p.TypeBeasiswa,p.FileBeasiswa,p.Pay_Cond,
                if( (select count(*) as total from db_finance.payment_pre where ID_register_formulir = a.ID limit 1) > 1,"Cicilan","Tidak Cicilan") as cicilan,
                if((select count(*) as total from db_finance.payment_pre where `Status` = 0 and ID_register_formulir = a.ID limit 1) = 0 ,"Lunas","Belum Lunas") as StatusPayment,px.No_Ref,p.RevID,
                if(
                  (select count(*) as total from db_finance.register_refund where ID_register_formulir = a.ID ) > 0,"Refund",""
                ) as CekRefund
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
                left join db_admission.formulir_number_offline_m as px
                on px.FormulirCode = b.FormulirCode
                where p.Status = "Approved" group by a.ID

                ) SubQuery
            ';

        $sql.= ' where (Name LIKE "'.$requestData['search']['value'].'%" or NamePrody LIKE "%'.$requestData['search']['value'].'%"
                or FormulirCode LIKE "'.$requestData['search']['value'].'%" or SchoolName LIKE "%'.$requestData['search']['value'].'%"
                or StatusPayment LIKE "'.$requestData['search']['value'].'%" or cicilan LIKE "'.$requestData['search']['value'].'%"
                or No_Ref LIKE "'.$requestData['search']['value'].'%"
                )
                and FormulirCode not in (select FormulirCode from db_admission.to_be_mhs) and CekRefund = ""
                ';
        $sql.= ' ORDER BY StatusPayment ASC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $nestedData[] = '<input type="checkbox" name="id[]" value="'.$row['ID_register_formulir'].'" Nama = "'.$row['Name'].'">';
            $nestedData[] = $row['NamePrody'];
            $nestedData[] = $this->m_master->setBintang_HTML($row['Pay_Cond']).'<br/>'.$row['Name'].'<br>'.$row['Email'];
            $FormulirCode = ($row['No_Ref'] != "" || $row['No_Ref'] != null ) ? $row['FormulirCode'].' / '.$row['No_Ref'] : $row['FormulirCode'];
            $nestedData[] = $FormulirCode;
            // get tagihan
            $getTagihan = $this->m_admission->getPaymentType_Cost_created($row['ID_register_formulir']);
            $tagihan = '';
            for ($j=0; $j < count($getTagihan); $j++) { 
                $tagihan .= $getTagihan[$j]['Abbreviation'].' : '.'Rp '.number_format($getTagihan[$j]['Pay_tuition_fee'],2,',','.').'<br>';
            }

            $nestedData[] = $tagihan;
            $nestedData[] = $row['cicilan'];
            $nestedData[] = '<button class="btn btn-inverse btn-notification btn-show" id-register-formulir = "'.$row['ID_register_formulir'].'" email = "'.$row['Email'].'" Nama = "'.$row['Name'].'">Show</button>';

            $Revision = $row['RevID'];
            $RevWr = '';
            if ($Revision != 0) {
                $getData = $this->m_master->caribasedprimary('db_finance.register_admisi_rev','ID_register_formulir',$row['ID_register_formulir']);
                $RevWr = '<a href = "javascript:void(0)" class = "showModal" id-register-formulir = "'.$row['ID_register_formulir'].'">Revision '.count($getData).'x';
            }
            $nestedData[] = $row['StatusPayment'].'<br>'.$RevWr;
            $btn = '<button class="btn btn-danger btn-sm btn-delete btn_cancel_tui" id-register-formulir = "'.$row['ID_register_formulir'].'"><i class="fa fa-trash" aria-hidden="true"></i> Cancel</button>';  

            $nestedData[] = $btn;
            $data[] = $nestedData;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getPayment_detail_admission()
    {
        $input = $this->getInputToken();
        $data = $this->m_finance->checkPayment_admisi($input['ID_register_formulir']);
        echo json_encode($data);
    }

    public function getPayment_detail_admission2()
    {
        $input = $this->getInputToken();
        $data = $this->m_finance->checkPayment_admisi2($input['ID_register_formulir']);
        echo json_encode($data);
    }

    public function bayar_manual_mahasiswa_admission()
    {
        $input = $this->getInputToken();
        $IDStudent = $input['IDStudent'];
        $bayar = $input['bayar'];
        if ($bayar == 1) {
            $dataSave = array(
                    'Status' => 1,
                    'BilingID' => 0,
                    'UpdateAt' => date('Y-m-d H:i:s'),
                    'DatePayment' => $input['DatePayment'],
                            );
            $this->db->where('ID',$IDStudent);
            $this->db->where('Status',0);
            $this->db->update('db_finance.payment_pre', $dataSave);
        }
        else
        {
            $dataSave = array(
                    'Status' => 0,
                    'BilingID' => 0,
                    'UpdateAt' => date('Y-m-d H:i:s'),
                            );
            $this->db->where('ID',$IDStudent);
            $this->db->where('Status',1);
            $this->db->update('db_finance.payment_pre', $dataSave);
        }
        
    }

    public function approved_edit()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/approved/edit_tagihan_cicilan',$this->data,true);
        $this->temp($content);
    }

    public function approved_edit_submit()
    {
        $Input = $this->getInputToken();
        $msg = '';
        $proses = $this->m_finance->edit_cicilan_tagihan_admission_submit2($Input);
        $msg = $proses['msg'];
        echo json_encode($msg);
    }

    public function pageRegistrationList(){
        if ($this->input->is_ajax_request()) {
            $this->registrationListData();
        }
        else
        {
            $content = $this->load->view('page/'.$this->data['department'].'/report_admission/pageRegistrationList',$this->data,true);
            $this->temp($content);
        }
        
    }

    public function formulir_registration_offline_serverSide()
    {
        $requestData= $_REQUEST;
        $reqTahun = $this->input->post('tahun');
        $StatusJual = $this->input->post('StatusJual');
        $No = $requestData['start'] + 1;
        $totalData = $this->m_admission->totalDataFormulir_offline4($reqTahun,$requestData,$StatusJual);

        if($StatusJual != '%') {
          $StatusJual = ' and b.StatusJual = '.$StatusJual;
        }
        else
        {
          $StatusJual = ''; 
        }

        $sql = 'select a.NameCandidate,a.Email,a.SchoolName,b.FormulirCode,b.No_Ref,a.StatusReg,b.Years,b.Status as StatusUsed, b.StatusJual,
                  b.FullName as NamaPembeli,b.PhoneNumber as PhoneNumberPembeli,b.HomeNumber as HomeNumberPembeli,b.Email as EmailPembeli,b.Sales,b.PIC as SalesNIP,b.SchoolNameFormulir,b.CityNameFormulir,b.DistrictNameFormulir,b.TypePay,
                  b.ID as ID_sale_formulir_offline,b.Price_Form,b.DateSale,b.src_name,b.NameProdi,b.NoKwitansi,b.DateFin
                  from (
                  select a.Name as NameCandidate,a.Email,z.SchoolName,c.FormulirCode,a.StatusReg
                  from db_admission.register as a 
                  join db_admission.register_verification as b
                  on a.ID = b.RegisterID
                  join db_admission.register_verified as c
                  on c.RegVerificationID = b.ID
                  join db_admission.school as z
                  on z.ID = a.SchoolID
                  where a.StatusReg = 1
                  ) as a right JOIN
                  (
                  select a.FormulirCode,a.No_Ref,a.Years,a.Status,a.StatusJual,b.FullName,b.HomeNumber,b.PhoneNumber,b.DateSale,b.NoKwitansi,b.DateFin,
                  b.Email,c.Name as Sales,b.PIC,b.ID,b.Price_Form,z.SchoolName as SchoolNameFormulir,z.CityName as  CityNameFormulir,z.DistrictName as DistrictNameFormulir,b.TypePay,
                  if(b.source_from_event_ID = 0,"", (select src_name from db_admission.source_from_event where ID = b.source_from_event_ID and Active = 1 limit 1) ) as src_name,b.ID_ProgramStudy,y.Name as NameProdi
                  from db_admission.formulir_number_offline_m as a
                  left join db_admission.sale_formulir_offline as b
                  on a.FormulirCode = b.FormulirCodeOffline
                  left join db_employees.employees as c
                  on c.NIP = b.PIC
                  left join db_admission.school as z
                  on z.ID = b.SchoolID
                  left join db_academic.program_study as y
                  on b.ID_ProgramStudy = y.ID
                  )
                  as b
                  on a.FormulirCode = b.FormulirCode
            ';

        $sql.= 'where Years = "'.$reqTahun.'" and 
                        (
                          b.FormulirCode like "'.$requestData['search']['value'].'%" or
                          b.No_Ref like "'.$requestData['search']['value'].'%" or
                          b.Sales like "'.$requestData['search']['value'].'%" or
                          a.NameCandidate like "'.$requestData['search']['value'].'%" or
                          b.SchoolNameFormulir like "%'.$requestData['search']['value'].'%" or
                          b.NameProdi like "'.$requestData['search']['value'].'%" or
                          b.src_name like "'.$requestData['search']['value'].'%" or
                          b.FullName like "'.$requestData['search']['value'].'%" or
                          b.DateSale like "'.$requestData['search']['value'].'%" or
                          b.NoKwitansi like "'.$requestData['search']['value'].'%"
                        ) '.$StatusJual.'';
        $sql.= ' order by b.NoKwitansi desc LIMIT '.$requestData['start'].' ,'.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];
            $nestedData[] = $No;
            $nestedData[] = $row['FormulirCode'];
            $nestedData[] = $row['No_Ref'];
            $nestedData[] = $row['NoKwitansi'];
            $nestedData[] = $row['NameProdi'];
            $aa = ($row['StatusUsed'] == 0) ? '<div style="color:  green;">No</div>' : '<div style="color:  red;">Yes</div>';
            $nestedData[] = $aa;
            $aa = ($row['StatusJual'] == 0) ? '<div style="color:  green;">IN</div>' : '<div style="color:  red;">Sold Out</div>';
            $nestedData[] = $aa;
            $nestedData[] = $row['Sales'];
            $nestedData[] = number_format($row['Price_Form'],0,',','.');
            $nestedData[] = $row['DateSale'];
            $nestedData[] = ($row['DateFin'] == '' || $row['DateFin'] == null || $row['DateFin'] == '0000-00-00') ? '<div class="row" style="margin-top: 10px">
                          <div class="col-md-12">
                            <span IDget = "'.$row['ID_sale_formulir_offline'].'" class="btn btn-xs btn-primary btn-setdate">
                             <i class="fa fa-user-o"></i> Set Date Finance
                           </span>
                          </div>
                        </div>' : '<div class = "row"><div class = "col-md-12">'.$row['DateFin'].'</div></div>'.'<div class="row" style="margin-top: 10px">
                          <div class="col-md-12">
                            <span IDget = "'.$row['ID_sale_formulir_offline'].'" class="btn btn-xs btn-primary btn-setdate">
                             <i class="fa fa-user-o"></i> Set Date Finance
                           </span>
                          </div>
                        </div>';
            $nestedData[] = $row['NamaPembeli'].'<br>'.$row['PhoneNumberPembeli'].'<br>'.$row['EmailPembeli'].'<br>'.$row['SchoolNameFormulir'].'<br>'.$row['DistrictNameFormulir'].' '.$row['CityNameFormulir'];
            $nestedData[] = $row['src_name'];
            $action = '';
            if ($row['ID_sale_formulir_offline'] != null || $row['ID_sale_formulir_offline'] != '')
            {
              $action = '<div class="row" style="margin-top: 10px">
                          <div class="col-md-12">
                            <span ref = "'.$row['No_Ref'].'" NamaLengkap = "'.$row['NamaPembeli'].'" class="btn btn-xs btn-print" phonehome = "'.$row['HomeNumberPembeli'].'" hp = "'.$row['PhoneNumberPembeli'].'" jurusan = "'.$row['NameProdi'].'" pembayaran ="Pembelian Formulir Pendaftaran('.$row['NameProdi'].')" jenis= "'.$row['TypePay'].'" jumlah = "'.$row['Price_Form'].'" date = "'.$row['DateSale'].'" formulir = "'.$row['FormulirCode'].'" NoKwitansi = "'.$row['NoKwitansi'].'">
                             <i class="fa fa-print"></i> Kwitansi
                           </span>
                          </div>
                        </div>
                        ';
            }

            $nestedData[] = $action;
            $data[] = $nestedData;
            $No++;
        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData ),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function copy_last_tuition_fee()
    {
        $input = $this->getInputToken();
        if ($input['verify'] == 'CreateTuitionFee') {
            $year = date('Y');
            $YearNext = (int) $year + 1;
            $msg = '';
            $get = $this->m_master->caribasedprimary('db_finance.tuition_fee','ClassOf',$YearNext);
            if (count($get) == 0) {
                $sql = "INSERT INTO db_finance.tuition_fee (ID, PTID, ProdiID, ClassOf,Cost,Pay_Cond)
                        SELECT null, PTID, ProdiID, ?,Cost,Pay_Cond
                        FROM db_finance.tuition_fee 
                        WHERE ClassOf = ?";
                $query=$this->db->query($sql, array($YearNext,$year));   
            }
            else
             {
                $msg = 'The data is already exist';
             }
             echo json_encode($msg);
        }
        else
        {
            exit('No direct script access allowed');
        }
    }

    public function save_tgl_formulir_offline()
    {
        $input = $this->getInputToken();
        $formUpdate = $input['data'];
        $ID = $input['ID'];
        $bf = $this->m_master->caribasedprimary('db_admission.sale_formulir_offline','ID',$ID);
        // cek data telah pernah diupdate atau belum
        $booltrigger = ($bf[0]['DateFin'] == null || $bf[0]['DateFin'] == '') ? true : false;
        $this->db->where('ID',$ID);
        $this->db->update('db_admission.sale_formulir_offline', $formUpdate);
        if ($booltrigger) {
                $get = $this->m_master->caribasedprimary('db_admission.sale_formulir_offline','ID',$ID);
                // cek data exist di table register_verified
                $f = $this->m_master->caribasedprimary('db_admission.register_verified','RegVerificationID',$get[0]['FormulirCodeOffline']);
                if (count($f) == 1) {
                    $s = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','FormulirCode',$get[0]['FormulirCodeOffline']);
                    $ta = $s[0]['Years'];
                    $month = date('m', strtotime($get[0]['DateFin']));
                    $year = date('Y', strtotime($get[0]['DateFin']));
                    $action = 'add';
                    $c = $this->m_master->caribasedprimary('db_admission.register_formulir','ID_register_verified',$f[0]['ID']);
                    $ProdiID = $c[0]['ID_program_study'];
                    $url = url_pas.'rest/__trigger_formulir';
                    $data = array(
                            'ta' => $ta,
                            'month' => $month,
                            'year' => $year,
                            'action' => $action,
                            'ProdiID' => $ProdiID,
                            'auth' => 's3Cr3T-G4N',
                        );
                    $Input = $this->jwt->encode($data,"UAP)(*");
                    $data = array(
                        'data' => $Input,
                    );

                    $this->m_master->get_content($url, json_encode($data));
                }
        }
        else
        {
                $get = $this->m_master->caribasedprimary('db_admission.sale_formulir_offline','ID',$ID);

                // cek data exist di table register_verified
                $f = $this->m_master->caribasedprimary('db_admission.register_verified','RegVerificationID',$get[0]['FormulirCodeOffline']);
                if (count($f) == 1) {
                    // delete dahulu
                        $s = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','FormulirCode',$get[0]['FormulirCodeOffline']);
                        $ta = $s[0]['Years'];
                        $month = date('m', strtotime($bf[0]['DateFin']));
                        $year = date('Y', strtotime($bf[0]['DateFin']));
                        $action = 'delete';
                        $c = $this->m_master->caribasedprimary('db_admission.register_formulir','ID_register_verified',$f[0]['ID']);
                        $ProdiID = $c[0]['ID_program_study'];
                        $url = url_pas.'rest/__trigger_formulir';
                        $data = array(
                                'ta' => $ta,
                                'month' => $month,
                                'year' => $year,
                                'action' => $action,
                                'ProdiID' => $ProdiID,
                                'auth' => 's3Cr3T-G4N',
                            );
                        $Input = $this->jwt->encode($data,"UAP)(*");
                        $data = array(
                            'data' => $Input,
                        );

                        $this->m_master->get_content($url, json_encode($data));

                    // add sesuai bulan dan tanggal
                        $month = date('m', strtotime($get[0]['DateFin']));
                        $year = date('Y', strtotime($get[0]['DateFin']));
                        $action = 'add';
                        $ProdiID = $c[0]['ID_program_study'];
                        $url = url_pas.'rest/__trigger_formulir';
                        $data = array(
                                'ta' => $ta,
                                'month' => $month,
                                'year' => $year,
                                'action' => $action,
                                'ProdiID' => $ProdiID,
                                'auth' => 's3Cr3T-G4N',
                            );
                        $Input = $this->jwt->encode($data,"UAP)(*");
                        $data = array(
                            'data' => $Input,
                        );

                        $this->m_master->get_content($url, json_encode($data));
                }
        }
            
    }

    public function verify_bukti_bayar()
    {
        $input = $this->getInputToken();
        $ID = $input['idtable'];
        $G_data = $this->m_master->caribasedprimary('db_finance.payment_proof','ID',$ID);
        $FileUpload = (array) json_decode($G_data[0]['FileUpload'],true);
        for ($i=0; $i < count($FileUpload); $i++) {
             $FileUpload[$i]['VerifyFinance'] = 1; 
        }

        $dataSave = array(
            'VerifyFinance' => 1,
            'FileUpload' => json_encode($FileUpload),
            'VerifyBy' => $this->session->userdata('NIP'),
        );

        $this->db->where('ID',$ID);
        $this->db->update('db_finance.payment_proof',$dataSave);
        echo json_encode('');
    }

    public function reject_bukti_bayar()
    {
        $input = $this->getInputToken();
        $ID = $input['idtable'];
        $ReasonCancel = $input['ReasonCancel'];
        $G_data = $this->m_master->caribasedprimary('db_finance.payment_proof','ID',$ID);
        $FileUpload = (array) json_decode($G_data[0]['FileUpload'],true);
        for ($i=0; $i < count($FileUpload); $i++) {
             $FileUpload[$i]['VerifyFinance'] = 2; 
        }

        $dataSave = array(
            'VerifyFinance' => 2,
            'ReasonCancel' => $ReasonCancel,
            'FileUpload' => json_encode($FileUpload),
            'VerifyBy' => $this->session->userdata('NIP'),
        );

        $this->db->where('ID',$ID);
        $this->db->update('db_finance.payment_proof',$dataSave);
        echo json_encode('');
    }

    public function page_set_deposit_mhs()
    {
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_set_deposit_mhs',$this->data,true);
        $this->temp($content);
    }

    public function actionDeposit(){
        header('Content-Type: application/json');
        $rs = ['status' => -1,'msg' => ''];
        $dataToken = json_decode(json_encode($this->getInputToken()),true);
        $data = $dataToken['data'];
        $data['FillBy'] = $this->session->userdata('NIP');
        $data['FillAt'] = date('Y-m-d H:i:s');
        // insert di trans_deposit
        $this->db->insert(
            'db_finance.trans_deposit',$data
        );

        $action = $dataToken['action'];
        $NPM = $data['NPM'];
        // update auth student
        if ($action == 'Credit') {
            $this->db->query(
                'update db_academic.auth_students set Deposit = Deposit + '.$data['Credit'].' where NPM = "'.$NPM.'" '
            );
        }
        else
        {
            $this->db->query(
                'update db_academic.auth_students set Deposit = Deposit - '.$data['Debit'].' where NPM = "'.$NPM.'" '
            );
        }
        
        $rs = ['status' => 1,'msg' => ''];
        echo json_encode($rs);
    }

    public function page_set_bayar($NPM = '',$SemesterID = '',$paymentid=''){
        $this->data['NPM'] = $NPM;
        $this->data['SemesterID'] = $SemesterID;
        $this->data['paymentid'] = $paymentid;
        $content = $this->load->view('page/'.$this->data['department'].'/tagihan_mahasiswa/page_set_bayar',$this->data,true);
        $this->temp($content);
    }

    public function bayar_kuliah(){
        $this->input->is_ajax_request() or exit('No direct post submit allowed!');
        $rs = ['status' => 0,'msg' => '','reload' => 0 ];

        $this->load->library('form_validation');
        $data_token = $this->getInputToken();
        
        $this->form_validation->set_data($data_token);

        $this->form_validation->set_rules('paymentid', 'Pilihan payment', 'trim|required');
        $this->form_validation->set_rules('Pay', 'Nominal', 'trim|required|greater_than_equal_to[10000]');
        $this->form_validation->set_rules('Pay_Date', 'Tanggal Bayar', 'trim|required');

        if ($this->form_validation->run() === true) {
            $count_left_paymet = $this->m_finance->_count_left_paymet($data_token['paymentid']);

            if ($count_left_paymet) {
                if ($data_token['Pay'] > $count_left_paymet['left_payment']) {
                    $rs = ['status' => 0,'msg' => 'Nominal melebihi dari sisa tagihan','reload' => 0 ];
                }
                else
                {
                    $payment_students_blm_lunas = $this->db->where('ID_payment',$data_token['paymentid'])->where('Status',0)->get('db_finance.payment_students');
                    if ($payment_students_blm_lunas) {
                        $UniqueGroupBy = $this->m_master->generate_random_letters(6).'-'.date('YmdHis');

                        $payment_students_blm_lunas = $payment_students_blm_lunas->result_array();
                        
                        $pay_loop = $data_token['Pay'];
                        for ($i=0; $i < count($payment_students_blm_lunas); $i++) { 
                            $ID_payment_students = $payment_students_blm_lunas[$i]['ID'];
                            $Invoice_cicilan = $payment_students_blm_lunas[$i]['Invoice'];

                            // check left
                            $have_pay_details = $this->db->select(' sum(Pay) as total ')
                                                         ->where('ID_payment_students',$ID_payment_students)
                                                         ->get('db_finance.payment_student_details')
                                                         ->row()->total;
                            $have_pay_details =  (int)$have_pay_details;

                            $left_pay_details = $Invoice_cicilan - $have_pay_details;

                            if ($left_pay_details > $pay_loop) {
                                $dataSave = [
                                    'ID_payment_students' => $ID_payment_students,
                                    'UniqueGroupBy' => $UniqueGroupBy,
                                    'Pay' => $pay_loop,
                                    'Pay_Date' =>  $data_token['Pay_Date'],
                                    'Created_By' => $this->session->userdata('NIP'),
                                    'Created_At' => date('Y-m-d H:i:s'),
                                ];

                                $this->db->insert('db_finance.payment_student_details',$dataSave);
                                break;
                            }
                            else
                            {
                                // skip insert if zero
                                if ($left_pay_details <= 0) {
                                   $dataSave = [
                                       'ID_payment_students' => $ID_payment_students,
                                       'UniqueGroupBy' => $UniqueGroupBy,
                                       'Pay' => $left_pay_details,
                                       'Pay_Date' =>  $data_token['Pay_Date'],
                                       'Created_By' => $this->session->userdata('NIP'),
                                       'Created_At' => date('Y-m-d H:i:s'),
                                   ];
                                   $this->db->insert('db_finance.payment_student_details',$dataSave);
                                }
                                
                                $this->db->where('ID',$ID_payment_students);
                                $this->db->update('db_finance.payment_students',['Status' => 1]);

                                $pay_loop = $pay_loop - $left_pay_details;
                            }   
                            
                        }

                        $payment_students_blm_lunas_after = $this->db->where('ID_payment',$data_token['paymentid'])->where('Status',0)->get('db_finance.payment_students')->result_array();

                        if (count($payment_students_blm_lunas_after) == 0 ) {
                            $payment =  $this->db->where('ID',$data_token['paymentid'])->get('db_finance.payment')->row();
                            $dataSave = [
                                'Status' =>"1",
                                // 'ToChange' => 0,
                                'ToChange' => ($payment->ToChange == 1 ) ? 2 : 0,
                                'UpdateAt' => date('Y-m-d H:i:s'),
                                'UpdatedBy' => $this->session->userdata('NIP'),
                            ];

                            $this->db->where('ID',$data_token['paymentid']);
                            $this->db->update('db_finance.payment', $dataSave);
                        }

                        $rs = ['status' => 1,'msg' => '','reload' => 1  ];
                    }
                    else
                    {
                        $rs = ['status' => 0,'msg' => 'Data telah lunas','reload' => 1  ];
                    }
                }
            }
        }
        else
        {
            $rs = ['status' => 0,'msg' => validation_errors(),'reload' => 0  ];
        }

        echo json_encode($rs);
    }

    public function sync_payment(){
        $this->input->is_ajax_request() or exit('No direct post submit allowed!');
        $rs = ['status' => 0,'msg' => 'error'];

        if ($this->session->userdata('NIP') == '2018018') {

            $payment_students = $this->db->select('a.*,c.UpdatedBy')
                                         ->join('db_finance.payment c', 'a.ID_payment = c.ID', 'join')
                                         ->where('a.Status',1)
                                         ->where('a.ID not in (select ID_payment_students from db_finance.payment_student_details)')
                                        // ->limit(5, 0)
                                         ->get('db_finance.payment_students a')
                                         ->result_array();
                               
            $x = 0;

            $xEnd = 100;

            $arr_dataSave = [];
            for ($i=0; $i < count($payment_students); $i++) { 

                $UniqueGroupBy = $this->m_master->generate_random_letters(6).'-'.date('YmdHis');

                $dataSave = [
                    'ID_payment_students' => $payment_students[$i]['ID'] ,
                    'UniqueGroupBy' => $UniqueGroupBy,
                    'Pay' => $payment_students[$i]['Invoice'] ,
                    'Pay_Date' =>  $payment_students[$i]['DatePayment'] ,
                    'Created_By' => $payment_students[$i]['UpdatedBy'] ,
                    'Created_At' =>  $payment_students[$i]['UpdateAt'] ,
                ];

                $arr_dataSave[] = $dataSave;

                $x++;
                
                if ($x ==  $xEnd ) {
                   $this->db->insert_batch('db_finance.payment_student_details', $arr_dataSave); 
                   $x = 0;
                   $arr_dataSave = [];
                }
            }

            if (count($arr_dataSave) > 0) {
                $this->db->insert_batch('db_finance.payment_student_details', $arr_dataSave); 
            }

            $rs = ['status' => 1,'msg' => ''];
        }

        echo json_encode($rs);
    }

}
