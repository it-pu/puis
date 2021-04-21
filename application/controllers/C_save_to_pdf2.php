<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_save_to_pdf2 extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        $this->load->library('JWT');
        $config=array('orientation'=>'P','size'=>'A4');
        $this->load->library('mypdf',$config);

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

    public function tuitionFeeAdmission()
    {
        $token = $this->input->post('token');
        $input = $this->getInputToken($token);
        $this->load->model('master/m_master');
        $this->load->model('finance/m_finance');
        $ID_register_formulir = $input['ID_register_formulir'];
        $getData = $this->m_finance->tuition_fee_calon_mhs_by_ID($ID_register_formulir,'p.Status = "Approved"');
        $cicilan = $this->m_master->caribasedprimary('db_finance.payment_pre','ID_register_formulir',$ID_register_formulir);
        $this->Tuition_PDF_SendEmail($getData,$cicilan);
    }

    private function Tuition_PDF_SendEmail($Personal,$arr_cicilan)
    {
        $this->load->model('master/m_master');
        $this->load->model('finance/m_finance');
        $Sekolah = $Personal[0]['SchoolName'];
        $TuitionFee = $this->m_finance->getTuitionFee_calon_mhs($Personal[0]['ID_register_formulir']);
        $arr_temp = array('filename' => '');
        $filename = 'Tuition_fee_'.$Personal[0]['FormulirCode'].'.pdf';
        $getData = $this->m_master->showData_array('db_admission.set_label_token_off');

        $config=array('orientation'=>'P','size'=>'A5');
        $this->load->library('mypdf',$config);
        $this->mypdf->SetMargins(10,10,10,10);
        $this->mypdf->SetAutoPageBreak(true, 0);
        $this->mypdf->AddPage();
        // Logo
        $this->mypdf->Image('./images/logo_tr.png',10,10,50);

        $setFont = 8;

        // date
        $DateIndo = $this->m_master->getIndoBulan(date('Y-m-d'));
        $this->mypdf->SetXY(150,20);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, 'Jakarta, '.$DateIndo, 0, 0, 'L', 0);

        // Line break
        $this->mypdf->Ln(20);

        $this->mypdf->SetXY(22,29);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, 'Nomor', 0, 1, 'L', 0);

        $this->mypdf->SetXY(22,35);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, 'Hal', 0, 1, 'L', 0);

        $this->mypdf->SetXY(42,29);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, ':', 0, 1, 'L', 0);

        $this->mypdf->SetXY(42,35);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, ':', 0, 1, 'L', 0);

        $getNumber = $this->m_master->caribasedprimary('db_finance.register_admisi','ID_register_formulir',$Personal[0]['ID_register_formulir']);
        $No_Surat = $this->m_finance->ShowNumberTuitionFee( $getNumber[0]['No_Surat'] );
        $this->mypdf->SetXY(45,29);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, $No_Surat.'/MKT-PMB-B-19/PU/X/2018', 0, 1, 'L', 0);

        $this->mypdf->SetXY(45,35);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, 'Surat Keputusan Penerimaan Beasiswa di Podomoro University', 0, 1, 'L', 0);


        $setXAwal = 22;
        $setYAwal = 45;
        $setJarakY = 5;
        $setJarakX = 40;
        $setFontIsian = 12;

        // isian
        $setY = $setYAwal;
        $setX = $setXAwal;

        // label
        $this->mypdf->SetXY($setX,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','B',$setFont);
        $this->mypdf->Cell(0, 0, 'Kepada Yth.', 0, 1, 'L', 0);

        // Nama
        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','B',$setFont);
        $this->mypdf->Cell(0, 0, $Personal[0]['Name'].'-'.$Personal[0]['FormulirCode'], 0, 1, 'L', 0); 

        // Address
        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','B',$setFont);
        $this->mypdf->Cell(0, 0, $Personal[0]['Address'], 0, 1, 'L', 0); 

        // City
        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, $Personal[0]['RegionAddress'].' '.$Personal[0]['ProvinceAddress'], 0, 1, 'L', 0); 

        // School
        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, $Personal[0]['SchoolName'], 0, 1, 'L', 0); 

        // Hp
        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, 'No.Tlp/Hp     : '.$Personal[0]['PhoneNumber'], 0, 1, 'L', 0); 

        // Hp
        $setXvalue = $setX;
        $setY = $setY + 7;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(0, 0, 'Dengan hormat,', 0, 1, 'L', 0);

        // cek potongan discount
        $chkDiscount = 0;
        $arr_discount = array();
        $arr_discount2 = array();
        $NameTbl = $Personal[0]['Name'].'-'.$Personal[0]['FormulirCode'];
        foreach ($Personal[0] as $key => $value) {
            $key = explode('-', $key);
            if ($key[0] == 'Discount') {
                if ($value > 0 ) {
                   $chkDiscount = 1;
                   $arr_discount[$key[1]]['discount'] = $value;
                }
                $arr_discount2[$key[1]]['discount'] = $value;
            }
        }

        // get potongan lain
          foreach ($Personal[0] as $key => $value) {
            $key = explode('-', $key);
             if ($key[0] == 'PotonganLain') {
                $ptname = $key[1];
                $str_arr_discount = '';
                $TotalPotonganLain = 0;
                $BoolFindKey = false;
                foreach ($arr_discount as $keya => $valuea) {
                  if ($keya == $ptname) {
                    $dataPotongan =  $value;
                    if (count($dataPotongan) > 0) {
                      $str_arr_discount .= $dataPotongan[0]['DiscountName'].'('.$ptname.')'.' : '.number_format($dataPotongan[0]['DiscountValue'],2,',','.');
                      $TotalPotonganLain += $dataPotongan[0]['DiscountValue'];
                      for ($zz=1; $zz < count($dataPotongan); $zz++) { 
                        $str_arr_discount .= ', '.$dataPotongan[$zz]['DiscountName'].' : '.number_format($dataPotongan[$zz]['DiscountValue'],2,',','.');
                        $TotalPotonganLain += $dataPotongan[$zz]['DiscountValue'];
                      }
                    }
                    $BoolFindKey = true;
                    break;
                  }
                }

                if ($BoolFindKey) {
                  if ($str_arr_discount != '') {
                    if (array_key_exists($ptname, $arr_discount)) {
                        $arr_discount[$ptname]['potonganLain'] = ' dan '.$str_arr_discount;
                        $arr_discount2[$ptname]['totalPotonganLain'] = $TotalPotonganLain;
                    }
                    else
                    {
                      $arr_discount[$ptname]['potonganLain'] = $str_arr_discount;
                      $arr_discount2[$ptname]['totalPotonganLain'] = $TotalPotonganLain;
                    }

                    $chkDiscount = 1;
                  }
                }
                else
                {
                    $dataPotongan =  $value;
                    if (count($dataPotongan) > 0) {
                      $str_arr_discount .= $dataPotongan[0]['DiscountName'].'('.$ptname.')'.' : '.number_format($dataPotongan[0]['DiscountValue'],2,',','.');
                      $TotalPotonganLain += $dataPotongan[0]['DiscountValue'];
                      for ($zz=1; $zz < count($dataPotongan); $zz++) { 
                        $str_arr_discount .= ', '.$dataPotongan[$zz]['DiscountName'].' : '.number_format($dataPotongan[$zz]['DiscountValue'],2,',','.');
                        $TotalPotonganLain += $dataPotongan[$zz]['DiscountValue'];
                      }

                      if ($str_arr_discount != '') {
                        if (array_key_exists($ptname, $arr_discount)) {
                            $arr_discount[$ptname]['potonganLain'] = ' dan '.$str_arr_discount;
                            $arr_discount2[$ptname]['totalPotonganLain'] = $TotalPotonganLain;
                        }
                        else
                        {
                          $arr_discount[$ptname]['potonganLain'] = $str_arr_discount;
                          $arr_discount2[$ptname]['totalPotonganLain'] = $TotalPotonganLain;
                        }

                        $chkDiscount = 1;
                      }
                    }
                }
            }

          }

        if ($chkDiscount == 1) {
            $Status = 'rata-rata raport kelas XI';
            if ($Personal[0]['RangkingRapor'] != 0) {
                $Status = 'berdasarkan Rangking paralel '.$Personal[0]['RangkingRapor'].' kelas XI';
            }

            $setXvalue = $setX;
            $setY = $setY + 2;
            $this->mypdf->SetXY($setXvalue,$setY);
            $this->mypdf->SetTextColor(0,0,0);
            $this->mypdf->SetFont('Arial','',$setFont);
            // MultiCell( 140, 2, $arr_value[$getRowDB], 0,'L');
            $this->mypdf->MultiCell(0, 5, 'Selamat, Anda mendapatkan beasiswa potongan di Podomoro University tahun akademik '.$Personal[0]['NamaTahunAkademik'].' '.$Status.', dengan rincian sebagai berikut:', 0,'L');

            $setY = $setY + 10;
            $height = 5;
            $this->mypdf->SetXY($setX,$setY);
            $this->mypdf->SetFillColor(255, 255, 255);
            $this->mypdf->Cell(50,$height,'Nama Lengkap - Nomor Formulir',1,0,'C',true);
            $this->mypdf->Cell(40,$height,'Program Study',1,0,'C',true);
            $this->mypdf->Cell(80,$height,'Beasiswa',1,1,'C',true);

            $ProdiTbl = $Personal[0]['NamePrody'];
            foreach ($arr_discount as $key => $value) {
                $setY = $setY + $height;
                $this->mypdf->SetXY($setX,$setY);
                $this->mypdf->SetFillColor(255, 255, 255);
                $this->mypdf->Cell(50,$height,$NameTbl,1,0,'C',true);
                $this->mypdf->Cell(40,$height,$ProdiTbl,1,0,'C',true);
                $rsShow = '';
                if (array_key_exists('discount', $value)) {
                   $rsShow = 'Beasiswa Pot '.$key.' '.(int)$value['discount'].'%';
                }
               
                if (array_key_exists('potonganLain', $value)) {
                  $rsShow .= $value['potonganLain'];
                }
                $this->mypdf->Cell(80,$height,$rsShow,1,1,'C',true);
            }

        }

        $setXvalue = $setX;
        $setY = $setY + 7;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('Total pembayaran untuk <b>"Semester Pertama"</b> dalam 1x pembayaran :');

        $setY = $setY + 5;
        $height = 5;

        $this->mypdf->SetXY($setX,$setY);
        $this->mypdf->SetFillColor(255, 255, 255);
        $this->mypdf->SetFont('Arial','B',$setFont);
        $this->mypdf->Cell(50,$height,'Pembayaran Semester 1',1,0,'C',true);
        $this->mypdf->Cell(25,$height,'SPP',1,0,'C',true);
        $this->mypdf->Cell(25,$height,'BPP Semester',1,0,'C',true);
        $this->mypdf->Cell(25,$height,'Biaya SKS',1,0,'C',true);
        $this->mypdf->Cell(25,$height,'Lain-lain',1,0,'C',true);
        $this->mypdf->Cell(25,$height,'Total Biaya',1,1,'C',true);

        $setY = $setY + $height;
        $this->mypdf->SetXY($setX,$setY);
        $this->mypdf->SetFillColor(255, 255, 255);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->Cell(50,$height,'Biaya Normal',1,0,'L',true);

        // get tuition fee

           $sql23 = 'select a.Abbreviation,b.Cost from db_finance.payment_type as a join db_finance.tuition_fee as b on a.ID = b.PTID where ClassOf = ? and ProdiID = ? and b.Pay_Cond = ? ';
           $query23=$this->db->query($sql23, array($Personal[0]['SetTa'],$Personal[0]['ID_program_study'],$Personal[0]['Pay_Cond']))->result_array();
           $totalTuitionFee = 0;
           $arr_pay = array();

           // get SKS
           $ID_program_study = $Personal[0]['ID_program_study'];
           $ccc = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ID_program_study);
           $Credit = $ccc[0]['DefaultCredit'];

            foreach ($query23 as $keya) {
                $arr_pay[$keya['Abbreviation']] = $keya['Cost'];
                if ($keya['Abbreviation'] == 'Credit') {
                    $CreditHarga = $keya['Cost'] * $Credit;
                    $this->mypdf->Cell(25,$height,number_format($CreditHarga,2,',','.'),1,0,'L',true);
                    $totalTuitionFee = $totalTuitionFee + $CreditHarga;
                }
                else
                {
                    $this->mypdf->Cell(25,$height,number_format($keya['Cost'],2,',','.'),1,0,'L',true);
                    $totalTuitionFee = $totalTuitionFee + $keya['Cost'];
                }

            }
            // total
                 $this->mypdf->Cell(25,$height,number_format($totalTuitionFee,2,',','.'),1,0,'L',true);


            $setY = $setY + $height;
            $this->mypdf->SetXY($setX,$setY);
            $this->mypdf->SetFillColor(255, 255, 255);
            $this->mypdf->SetFont('Arial','',$setFont);
            $this->mypdf->Cell(50,$height,'Beasiswa yang diterima',1,0,'L',true);

            $totalTuitionFee = 0;
            // print_r($arr_discount2);die();
            foreach ($arr_discount2 as $key => $value) {

                foreach ($arr_pay as $keya => $valuea) {

                    if ($keya == $key) {
                        if ($key == 'Credit') {
                            $cost = $Credit * $valuea;
                            if (array_key_exists('discount', $value)) {
                               $cost = $value['discount'] * $cost / 100;
                            }
                          
                            // check ada potongan lain atau tidak
                              if (array_key_exists('totalPotonganLain', $arr_discount2[$key])) {
                                $cost = $cost +  $arr_discount2[$key]['totalPotonganLain'];
                              }

                            $this->mypdf->Cell(25,$height,number_format($cost,2,',','.'),1,0,'L',true);
                        }
                        else
                        {
                            $cost = $valuea;
                            if (array_key_exists('discount', $value)) {
                               $cost = $value['discount'] * $cost / 100;
                            }

                            // check ada potongan lain atau tidak
                              if (array_key_exists('totalPotonganLain', $arr_discount2[$key])) {
                                $cost = $cost +  $arr_discount2[$key]['totalPotonganLain'];
                              }

                            $this->mypdf->Cell(25,$height,number_format($cost,2,',','.'),1,0,'L',true);
                        }
                        $totalTuitionFee = $totalTuitionFee + $cost;
                    }
                }

            }
            $this->mypdf->Cell(25,$height,number_format($totalTuitionFee,2,',','.'),1,0,'L',true);


        $setY = $setY + $height;
        $this->mypdf->SetXY($setX,$setY); 
        $this->mypdf->SetFillColor(255, 255, 255);
        $this->mypdf->SetFont('Arial','B',$setFont);
        $this->mypdf->Cell(50,$height,'Biaya yang harus dibayar',1,0,'L',true);
        $totalTuitionFee = 0;
        $PTIDSelect = $this->m_master->showData_array('db_finance.payment_type');
        for ($i=0; $i < count($PTIDSelect); $i++) {
            foreach ($Personal[0] as $key => $value) {
                if ($PTIDSelect[$i]['Abbreviation'] == $key ) {
                    $this->mypdf->Cell(25,$height,number_format($Personal[0][$key],2,',','.'),1,0,'L',true);
                    $totalTuitionFee = $totalTuitionFee + $Personal[0][$key];
                } 
            } 
           
        }

        $this->mypdf->Cell(25,$height,number_format($totalTuitionFee,2,',','.'),1,0,'L',true); 

        $setXvalue = $setX;
        $setY = $setY + 7;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('Jadwal pembayaran untuk semester pertama dengan cicilan :');

         $setY = $setY + $height;

        $this->mypdf->SetXY($setX,$setY); 
        $this->mypdf->SetFillColor(226, 226, 226);
        $this->mypdf->Cell(40,$height,'Pembayaran',1,0,'C',true);
        $this->mypdf->Cell(60,$height,'Tanggal',1,0,'C',true);
        $this->mypdf->Cell(70,$height,'Jumlan',1,1,'C',true);

        $cicilan_tulis = array('Cicilan Pertama','Cicilan Kedua','Cicilan Ketiga','Cicilan Keempat','Cicilan Kelima','Cicilan Keenam','Cicilan Ketujuh');

        for ($i=0; $i < count($arr_cicilan); $i++) {
            $setY = $setY + $height; 
            $this->mypdf->SetXY($setX,$setY); 
            $this->mypdf->SetFillColor(255, 255, 255);
            $this->mypdf->Cell(40,$height,$cicilan_tulis[$i],1,0,'L',true);
            $Deadline = date('Y-m-d', strtotime($arr_cicilan[$i]['Deadline']));
            $this->mypdf->Cell(60,$height,$this->m_master->getIndoBulan($Deadline),1,0,'L',true);
            $this->mypdf->Cell(70,$height,'Rp '.number_format($arr_cicilan[$i]['Invoice'],2,',','.'),1,1,'L',true);

        }


        $setXvalue = $setX;
        $setY = $setY + 7;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('Pembayaran dapat dilakukan melalui transfer ke Bank BCA : ');

        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('-. Atas Nama');

        $setXvalue = $setXvalue + 25;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML(':');

        $setXvalue = $setXvalue + 3;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('<b>Yayasan Pendidikan Agung Podomro</b>');

        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('-. Nomor Account');

        $setXvalue = $setXvalue + 25;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML(':');

        $setXvalue = $setXvalue + 3;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('<b>161.3888.555</b>');

        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('-. Keterangan');

        $setXvalue = $setXvalue + 25;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML(':');

        $setXvalue = $setXvalue + 3;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('<b>'.$NameTbl.'</b>');


        
        $setXvalue = $setX;
        $setY = $setY + 10;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('Note: Mohon bukti pembayaran difax ke nomor 021-29200455 atau diemail ke admissions@podomorouniversity.com dengan subyek');

        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('<b>Pembayaran Uang Kuliah atas Nama '.$NameTbl.'.');

        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('Untuk info lebih lanjut dapat menghubungi Podomoro University di 021-29200456 ext 101-103/HP : 0821 1256 4900');

        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('Selamat bergabung di Keluarga Besar Podomoro university');

        $setXvalue = $setX;
        $setY = $setY + 10;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('Hormat Kami,');

        $setXvalue = $setX;
        $setY = $setY + 15;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('Dept. of Admissions and Marketing');

        $setXvalue = $setX;
        $setY = $setY + 10;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('<b>Perhatian:</b>');

        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('1.');
        $setXvalue = $setXvalue + 3;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->MultiCell(0, 5, 'Beasiswa berlaku untuk pembayaran sesuai dengan tanggal yang telah ditentukan di atas. Apabila melewati batas waktu yang telah ditentukan maka mengikuti program pembayaran pada gelombang tersebut.', 0,'L');

        $setXvalue = $setX;
        $setY = $setY + 10;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('2.');
        $setXvalue = $setXvalue + 3;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->MultiCell(0, 5, 'Pembayaran dianggap valid saat dana efektif pada rekening YPAP, bukan berdasarkan tanggal slip setoran / bukti transfer.', 0,'L');

        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('3.');
        $setXvalue = $setXvalue + 3;
        // $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('Jika sampai kegiatan perkuliahan dimulai masih ada kewajiban biaya studi yang belum diselesaikan, maka mahasiswa tersebut dianggap');
        $setXvalue = $setX + 3;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('<u>mengundurkan diri</u>');

        $setXvalue = $setX;
        $setY = $setY + 5;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->writeHTML('4.');
        $setXvalue = $setXvalue + 3;
        $this->mypdf->SetXY($setXvalue,$setY);
        $this->mypdf->SetTextColor(0,0,0);
        $this->mypdf->SetFont('Arial','',$setFont);
        $this->mypdf->MultiCell(0, 5, 'Surat ini dicetak otomatis oleh komputer dan tidak memerlukan tanda tangan pejabat yang berwenang.', 0,'L');
        // $this->mypdf->Output($filename,'I');
        $this->mypdf->Output($filename,'D');
    }

}
