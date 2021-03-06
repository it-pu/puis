<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_save_to_excel2 extends CI_Controller
{
    public $data = [];    

    function __construct()
    {
        parent::__construct();
        $this->load->library('JWT');
//        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model('report/m_save_to_excel');
        $this->load->model('master/m_master');

        $this->init_variable();

    }


    private function init_variable(){
        $this->data['keyM'] = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z');
    }

    private function getInputToken($token)
    {
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    private function getInputToken2()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token,$key);
        return $data_arr;
    }

    public function excel_dosen_tidak_tetap()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $Input = (array) $this->jwt->decode($token,$key);
        $passToExcel = $Input['passToExcel'];
        $passToExcel = json_decode(json_encode($passToExcel),true);

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes

        // Panggil class PHPExcel nya
        $excel = new PHPExcel();
        $Filename = 'excel-dosen-tidak-tetap.xlsx';
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

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

        
        $excel->setActiveSheetIndex(0)->setCellValue('A1', "Tabel 3.a.4. Dosen Tidak Tetap");
        // make header
        $r = 3;
        $rUntil = $r + 2 -1;
        $st = 0;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "No");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;

        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Pendidikan");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $stUntil = $st + 6 - 1;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $huruf_ = $this->m_master->HurufColExcelNumber($stUntil);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Jabatan Akademik");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf_.$r);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf_.$r)->applyFromArray($style_col);

        $st = 2;
        $r++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Asisten Ahli");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Lektor");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Lektor Kepala");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Guru Besar");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Tenaga Pengajar");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Jumlah");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $r++;
        for ($i=0; $i < count($passToExcel); $i++) { 
            $st = 0; 
            $No = $i+1;
            $Pendidikan = $passToExcel[$i]['Level'].' - '.$passToExcel[$i]['Description'];
            $Total = 0;
             $huruf = $this->m_master->HurufColExcelNumber($st);
             $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $No );
             $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Pendidikan );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $details = $passToExcel[$i]['details'];
            for ($j=0; $j < count($details); $j++) { 
                $st++;
                $huruf = $this->m_master->HurufColExcelNumber($st);
                $count = count($details[$j]['dataEmployees']);
                $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $count );
                $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);
                $Total += $count;
            }

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Total );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);    

            $r++;

        }

        $excel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='.$Filename); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');

    }

    public function excel_seleksi_mahasiswa_baru()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $Input = (array) $this->jwt->decode($token,$key);
        $passToExcel = $Input['passToExcel'];
        $passToExcel = json_decode(json_encode($passToExcel),true);

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes

        // Panggil class PHPExcel nya
        $excel = new PHPExcel();
        $Filename = 'excel_seleksi_mahasiswa_baru.xlsx';
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

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

        
        $excel->setActiveSheetIndex(0)->setCellValue('A1', "Tabel 2.a. Seleksi Mahasiswa Baru");
        // make header
        $r = 3;
        $rUntil = $r + 2 -1;
        $st = 0;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "No");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;

        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Prodi");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;

        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Daya Tampung");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $stUntil = $st + 2 - 1;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $huruf_ = $this->m_master->HurufColExcelNumber($stUntil);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Jumlah Calon Mahasiswa");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf_.$r);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf_.$r)->applyFromArray($style_col);

        $st = $stUntil+1;
        $stUntil = $st + 2 - 1;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $huruf_ = $this->m_master->HurufColExcelNumber($stUntil);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Jumlah Mahasiswa Baru");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf_.$r);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf_.$r)->applyFromArray($style_col);

        $st = $stUntil+1;
        $stUntil = $st + 2 - 1;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $huruf_ = $this->m_master->HurufColExcelNumber($stUntil);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Jumlah Mahasiswa");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf_.$r);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf_.$r)->applyFromArray($style_col);

        $r++;
        $st = 3;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Pendaftar");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Lulus Seleksi");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Reguler");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Transfer");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Reguler");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Transfer");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $r++;
        $Arr_total = [0,0,0,0,0,0,0];
        for ($i=0; $i < count($passToExcel); $i++) { 
            $st = 0;
            $No = $i + 1;
            $ProdiName = $passToExcel[$i]['ProdiName'];
            $Capacity = $passToExcel[$i]['Capacity'];
            $Registrant = $passToExcel[$i]['Registrant'];
            $PassSelection = $passToExcel[$i]['PassSelection'];
            $Regular = $passToExcel[$i]['Regular'];
            $Transfer = $passToExcel[$i]['Transfer'];
            $Regular2 = $passToExcel[$i]['Regular2'];
            $Transfer2 = $passToExcel[$i]['Transfer2'];

            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $No );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $ProdiName );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Capacity );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Registrant );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $PassSelection );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Regular );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Transfer );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Regular2 );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Transfer2 );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $Arr_total[0] = $Arr_total[0] + $Capacity;
            $Arr_total[1] = $Arr_total[1] + $Registrant;
            $Arr_total[2] = $Arr_total[2] + $PassSelection;
            $Arr_total[3] = $Arr_total[3] + $Regular;
            $Arr_total[4] = $Arr_total[4] + $Transfer;
            $Arr_total[5] = $Arr_total[5] + $Regular2;
            $Arr_total[6] = $Arr_total[6] + $Transfer2;

            $r++;

        }

        $st = 2;
        for ($i=0; $i <count($Arr_total) ; $i++) { 
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Arr_total[$i] );    
            $st++;
        }

        $excel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='.$Filename); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');
    }

    public function excel_aps_program_study()
    {

        $data_arr = $this->getInputToken2();

//        print_r($data_arr);exit;

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes

        // Panggil class PHPExcel nya
        $excel = new PHPExcel();
        $Filename = 'excel_aps_program_study.xlsx';

        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

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

        $excel->setActiveSheetIndex(0)->setCellValue('A1', "Tabel Daftar Program Studi di Unit Pengelola Program Studi (UPPS)");

        $excel->setActiveSheetIndex(0)->setCellValue('A3', "No");
        $excel->getActiveSheet()->mergeCells('A3:A4');
        $excel->getActiveSheet()->getStyle('A3:A4')->applyFromArray($style_col);

        $excel->setActiveSheetIndex(0)->setCellValue('B3', "Jenis Program");
        $excel->getActiveSheet()->mergeCells('B3:B4');
        $excel->getActiveSheet()->getStyle('B3:B4')->applyFromArray($style_col);

        $excel->setActiveSheetIndex(0)->setCellValue('C3', "Nama Program Studi");
        $excel->getActiveSheet()->mergeCells('C3:C4');
        $excel->getActiveSheet()->getStyle('C3:C4')->applyFromArray($style_col);

        $excel->setActiveSheetIndex(0)->setCellValue('D3', "Akreditasi Program Studi");
        $excel->getActiveSheet()->mergeCells('D3:F3');
        $excel->getActiveSheet()->getStyle('D3:F3')->applyFromArray($style_col);

        $excel->setActiveSheetIndex(0)->setCellValue('D4', "Status/ Peringkat");
        $excel->getActiveSheet()->getStyle('D4')->applyFromArray($style_col);

        $excel->setActiveSheetIndex(0)->setCellValue('E4', "No. dan Tgl. SK");
        $excel->getActiveSheet()->getStyle('E4')->applyFromArray($style_col);

        $excel->setActiveSheetIndex(0)->setCellValue('F4', "Tgl. Kadaluarsa");
        $excel->getActiveSheet()->getStyle('F4')->applyFromArray($style_col);


        $excel->setActiveSheetIndex(0)->setCellValue('G3', "Jumlah Mahasiswa");
        $excel->getActiveSheet()->mergeCells('G3:G4');
        $excel->getActiveSheet()->getStyle('G3:G4')->applyFromArray($style_col);

        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(10);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(35);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(45);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(20);

        $r = 5;
        if(count($data_arr)>0){



            foreach ($data_arr AS $item){
                $item = (array) $item;
                $excel->setActiveSheetIndex(0)->setCellValue('A'.$r, $item['No'] );
                $excel->setActiveSheetIndex(0)->setCellValue('B'.$r, $item['JenisProgram'] );
                $excel->setActiveSheetIndex(0)->setCellValue('C'.$r, $item['Prodi'] );
                $excel->setActiveSheetIndex(0)->setCellValue('D'.$r, $item['Status'] );
                $excel->setActiveSheetIndex(0)->setCellValue('E'.$r, $item['SK'] );
                $excel->setActiveSheetIndex(0)->setCellValue('F'.$r, $item['TglSK'] );
                $excel->setActiveSheetIndex(0)->setCellValue('G'.$r, $item['Mhs'] );

                $excel->getActiveSheet()->getStyle('A'.$r)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('B'.$r)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('C'.$r)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('D'.$r)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('E'.$r)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('F'.$r)->applyFromArray($style_row);
                $excel->getActiveSheet()->getStyle('G'.$r)->applyFromArray($style_row);

                $r = $r + 1;


            }

        }



        $excel->setActiveSheetIndex(0);

        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='.$Filename); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');


    }

    public function excel_kerjasama_perguruan_tinggi()
    {
    	$token = $this->input->post('token');
    	$key = "UAP)(*";
    	$Input = (array) $this->jwt->decode($token,$key);
    	$passToExcel = $Input['passToExcel'];
    	$passToExcel = $this->jwt->decode($passToExcel,$key);
    	$sql = $passToExcel.' group by z.ID ORDER BY z.StartDate asc';
    	$data = $this->db->query($sql)->result_array();
    	include APPPATH.'third_party/PHPExcel/PHPExcel.php';
    	ini_set('memory_limit', '-1');
    	ini_set('max_execution_time', 600); //600 seconds = 10 minutes

    	// Panggil class PHPExcel nya
    	$excel = new PHPExcel();
    	$Filename = 'excel-kerjasama-perguruan-tinggi.xlsx';
    	// Buat sebuah variabel untuk menampung pengaturan style dari header tabel
    	$style_col = array(
    	    'font' => array('bold' => true), // Set font nya jadi bold
    	    'alignment' => array(
    	        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
    	        'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
    	    ),
    	    'borders' => array(
    	        'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
    	        'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
    	        'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
    	        'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
    	    )
    	);

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

    	$excel->setActiveSheetIndex(0)->setCellValue('A1', "Tabel 1.c Kerjasama Perguruan Tinggi");

    	$r = 7;
    	$rUntil = $r + 2 -1;
    	$st = 0;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "No");
    	$excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
    	$excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);
    	
    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Lembaga Mitra Kerjasama");
    	$excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
    	$excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Kategori");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$stUntil = $st+2;
    	$hurufUntil = $this->m_master->HurufColExcelNumber($stUntil);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Tingkat *)");
    	$excel->getActiveSheet()->mergeCells($huruf.$r.':'.$hurufUntil.$r);
    	$excel->getActiveSheet()->getStyle($huruf.$r.':'.$hurufUntil.$r)->applyFromArray($style_col);

    	$st = $stUntil+1;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Bentuk Kegiatan/ Manfaat");
    	$excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
    	$excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Bukti Kerjasama");
    	$excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
    	$excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Masa Berlaku (Tahun Berakhir, YYYY)");
    	$excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);
    	$excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->getAlignment()->setWrapText(true);

    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Semester");
    	$excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
    	$excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

    	$r++;
    	$st = 3;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Internasional");
    	$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Nasional");
    	$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Wilayah/ Lokal");
    	$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

    	$c_int = 0;
    	$c_nas = 0;
    	$c_lokal = 0;
    	$r++;
    	// print_r($data);die();
    	for ($i=0; $i < count($data); $i++) { 
    		$No = $i+1;
    		$st = 0;
    		$huruf = $this->m_master->HurufColExcelNumber($st);
    		$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $No );
    		$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

    		$Lembaga = $data[$i]['Lembaga'];
    		$st++;
    		$huruf = $this->m_master->HurufColExcelNumber($st);
    		$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Lembaga );
    		$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $data[$i]['Kategori_kegiatan'] );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

    		$Internasional = '';
    		if ($data[$i]['Internasional'] == 1) {
    			// print_r('Internasional == '.$data[$i]['Internasional'].';Kategori == '.$data[$i]['Kategori'].'<br/>');
        			// if ($data[$i]['Kategori'] == 'Tridarma') {

        			// 	$c_int++;
        			// 	// print_r('int => '.$c_int.'<br/>');
        			// }
                    $c_int++;
    			$Internasional = 'V';
    		}
    		
    		$Nasional = '';
    		if ($data[$i]['Nasional'] == 1 ) {
    			// print_r('Nasional == '.$data[$i]['Nasional'].';Kategori == '.$data[$i]['Kategori'].'<br/>');
        			// if ($data[$i]['Kategori'] == 'Tridarma') {
        			// 	$c_nas++;
        			// 	// print_r('nas => '.$c_nas.'<br/>');
        			// }
                    $c_nas++;
    			$Nasional = 'V';
    		}

    		$Lokal = '';
    		if ($data[$i]['Lokal'] == 1) {
    			// print_r('Lokal == '.$data[$i]['Lokal'].';Kategori == '.$data[$i]['Kategori'].'<br/>');
        			// if ($data[$i]['Kategori'] == 'Tridarma') {
        			// 	$c_lokal++;
        			// 	// print_r('lokal => '.$c_lokal.'<br/>');
        			// }
                    $c_lokal++;
    			$Lokal = 'V';
    		}

    		$st++;
    		$huruf = $this->m_master->HurufColExcelNumber($st);
    		$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Internasional );
    		$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

    		$st++;
    		$huruf = $this->m_master->HurufColExcelNumber($st);
    		$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Nasional );
    		$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

    		$st++;
    		$huruf = $this->m_master->HurufColExcelNumber($st);
    		$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Lokal );
    		$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

    		$st++;
    		$huruf = $this->m_master->HurufColExcelNumber($st);
    		$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $data[$i]['BentukKegiatan'] );
    		$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

    		$st++;
    		$huruf = $this->m_master->HurufColExcelNumber($st);
    		$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $data[$i]['BuktiName'] );
    		$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

    		$st++;
    		$huruf = $this->m_master->HurufColExcelNumber($st);
    		$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $this->m_master->getDateIndonesian($data[$i]['EndDate']) );
    		$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

    		$st++;
    		$huruf = $this->m_master->HurufColExcelNumber($st);
    		$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $data[$i]['SemesterName'] );
    		$excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

    		$r++;
    	}
    	// die();
    	$r = 2;
    	$st = 0;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, 'Jumlah kerjasama tridharma tingkat internasional =' );
    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $c_int );

    	$r++;
    	$st = 0;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, 'Jumlah kerjasama tridharma tingkat nasional =' );
    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $c_nas );

    	$r++;
    	$st = 0;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, 'Jumlah kerjasama tridharma tingkat wilayah/lokal =' );
    	$st++;
    	$huruf = $this->m_master->HurufColExcelNumber($st);
    	$excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $c_lokal );

        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(8);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(25);
    	$excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        foreach(range('D','F') as $columnID) {
            $excel->getActiveSheet()->getColumnDimension($columnID)
                ->setAutoSize(true);
        }
    	foreach(range('G','J') as $columnID) {
    	    $excel->getActiveSheet()->getColumnDimension($columnID)
                  ->setWidth(25);
    	}

    	$excel->setActiveSheetIndex(0);
    	header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    	header('Content-Disposition: attachment; filename='.$Filename); // Set nama file excel nya
    	header('Cache-Control: max-age=0');

    	$write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
    	$write->save('php://output');
    }

    public function aps_excel_kerjasama_tridarma()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $Input = (array) $this->jwt->decode($token,$key);
        $passToExcel = $Input['passToExcel'];
        $passToExcel = $this->jwt->decode($passToExcel,$key);
        $sql = $passToExcel.' ORDER BY z.StartDate asc';
        $data = $this->db->query($sql)->result_array();
        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        ini_set('memory_limit', '-1');
        ini_set('max_execution_time', 600); //600 seconds = 10 minutes

        // Panggil class PHPExcel nya
        $excel = new PHPExcel();
        $Filename = 'excel-kerjasama-perguruan-tinggi.xlsx';
        // Buat sebuah variabel untuk menampung pengaturan style dari header tabel
        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

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

        $excel->setActiveSheetIndex(0)->setCellValue('A1', "Tabel 1 Kerjasama");

        $r = 4;
        $rUntil = $r + 2 -1;
        $st = 0;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "No");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);
        
        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Lembaga Mitra Kerjasama");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Kategori");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $stUntil = $st+2;
        $hurufUntil = $this->m_master->HurufColExcelNumber($stUntil);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Tingkat *)");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$hurufUntil.$r);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$hurufUntil.$r)->applyFromArray($style_col);

        $st = $stUntil+1;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Judul Kegiatan Kerjasama");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Manfaat bagi PS yang diakreditasi");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Waktu dan Durasi");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Bukti Kerjasama");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Masa Berlaku (Tahun Berakhir, YYYY)");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Semester");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf.$rUntil);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf.$rUntil)->applyFromArray($style_col);

        $r++;
        $st = 3;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Internasional");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Nasional");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Wilayah/ Lokal");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $r++;
        $c_nas = 0;
        $c_int = 0;
        $c_lokal = 0;
        for ($i=0; $i < count($data); $i++) { 
            $No = $i+1;
            $row = $data[$i];
            $st = 0;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $No );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $Lembaga = $data[$i]['Lembaga'];
            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Lembaga );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $data[$i]['Kategori_kegiatan'] );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $Internasional = '';
            if ($data[$i]['Internasional'] == 1) {
                // print_r('Internasional == '.$data[$i]['Internasional'].';Kategori == '.$data[$i]['Kategori'].'<br/>');
                if ($data[$i]['Kategori_kegiatan'] == 'Tridarma') {

                    $c_int++;
                    // print_r('int => '.$c_int.'<br/>');
                }
                $Internasional = 'V';
            }
            
            $Nasional = '';
            if ($data[$i]['Nasional'] == 1 ) {
                // print_r('Nasional == '.$data[$i]['Nasional'].';Kategori == '.$data[$i]['Kategori'].'<br/>');
                if ($data[$i]['Kategori_kegiatan'] == 'Tridarma') {
                    $c_nas++;
                    // print_r('nas => '.$c_nas.'<br/>');
                }
                $Nasional = 'V';
            }

            $Lokal = '';
            if ($data[$i]['Lokal'] == 1) {
                // print_r('Lokal == '.$data[$i]['Lokal'].';Kategori == '.$data[$i]['Kategori'].'<br/>');
                if ($data[$i]['Kategori_kegiatan'] == 'Tridarma') {
                    $c_lokal++;
                    // print_r('lokal => '.$c_lokal.'<br/>');
                }
                $Lokal = 'V';
            }

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Internasional );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Nasional );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Lokal );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $data[$i]['JudulKegiatan'] );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $data[$i]['ManfaatKegiatan'] );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $Waktu = $this->m_master->getDateIndonesian($row['StartDate']).' - '.$this->m_master->getDateIndonesian($row['EndDate']);
            $Durasi = $this->m_master->dateDiffDays ($row['StartDate'], $row['EndDate']);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Waktu." \n ".$Durasi.' days' );
            $excel->getActiveSheet()->getStyle($huruf.$r)->getAlignment()->setWrapText(true);
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $data[$i]['BuktiName'] );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $this->m_master->getDateIndonesian($data[$i]['EndDate']) );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $data[$i]['SemesterName'] );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $r++;
        }
        // die();
        $excel->getActiveSheet()->getColumnDimension('A')->setWidth(5);
        $excel->getActiveSheet()->getColumnDimension('B')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('C')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('D')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('E')->setWidth(15);
        $excel->getActiveSheet()->getColumnDimension('F')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('G')->setWidth(50);
        $excel->getActiveSheet()->getColumnDimension('H')->setWidth(40);
        $excel->getActiveSheet()->getColumnDimension('I')->setWidth(20);
        $excel->getActiveSheet()->getColumnDimension('J')->setWidth(35);
        $excel->getActiveSheet()->getColumnDimension('K')->setWidth(20);
        // foreach(range('B','E') as $columnID) {
        //     $excel->getActiveSheet()->getColumnDimension($columnID)
        //         // ->setAutoSize(true);
        //         ->setWidth(15);
        // }

        $excel->setActiveSheetIndex(0);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename='.$Filename); // Set nama file excel nya
        header('Cache-Control: max-age=0');

        $write = PHPExcel_IOFactory::createWriter($excel, 'Excel2007');
        $write->save('php://output');

    }

    private function standard_style(){

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

        $style_col = array(
            'font' => array('bold' => true), // Set font nya jadi bold
            'alignment' => array(
                'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER, // Set text jadi ditengah secara horizontal (center)
                'vertical' => PHPExcel_Style_Alignment::VERTICAL_CENTER // Set text jadi di tengah secara vertical (middle)
            ),
            'borders' => array(
                'top' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border top dengan garis tipis
                'right' => array('style'  => PHPExcel_Style_Border::BORDER_THIN),  // Set border right dengan garis tipis
                'bottom' => array('style'  => PHPExcel_Style_Border::BORDER_THIN), // Set border bottom dengan garis tipis
                'left' => array('style'  => PHPExcel_Style_Border::BORDER_THIN) // Set border left dengan garis tipis
            )
        );

        return ['style_row' => $style_row,'style_col' => $style_col];
    }


    public function reportFin_deposit(){
        $this->load->model('finance/m_finance');
        $input =  $this->getInputToken2();
        $TA = $input['TA'];
        $GetDateNow = date('Y-m-d');
        $data = $this->m_finance->get_deposit_saldo_uang_tititpan($TA);

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
        $excel2 = $excel2->load('./uploads/finance/Template_uang_titipan.xlsx'); // Empty Sheet
        $excel2->setActiveSheetIndex(0);

        $excel3 = $excel2->getActiveSheet();

        $subject_A2 = 'REKAP UANG TITIPAN TA '.$TA.'/'.($TA+1);
        $DatePrint = date('d M Y', strtotime($GetDateNow));
        $excel3->setCellValue('A2', $subject_A2);
        $excel3->setCellValue('B3', 'sd '.$DatePrint);

        $style = $this->standard_style();
        $style_row = $style['style_row'];
        $style_col = $style['style_col'];


        $Filename = 'UangTititpanMHS_TA_'.$TA.'.xlsx';
        $a = 7;
        $Total = 0;
        $no = 0;

        for ($i=0; $i < count($data); $i++) { 
           $no++;
           $excel3->setCellValue('A'.$a, $no);
           $excel3->setCellValue('B'.$a, $data[$i]['Name']);
           $excel3->setCellValue('C'.$a, $data[$i]['NPM']);
           $excel3->setCellValue('D'.$a, $data[$i]['CodeProdi']);
           $excel3->setCellValue('E'.$a, $data[$i]['Saldo']);

           $excel3->getStyle('A'.$a)->applyFromArray($style_row);
           $excel3->getStyle('B'.$a)->applyFromArray($style_row);
           $excel3->getStyle('C'.$a)->applyFromArray($style_row);
           $excel3->getStyle('D'.$a)->applyFromArray($style_row);
           $excel3->getStyle('E'.$a)->applyFromArray($style_row);

           $Total = $Total + $data[$i]['Saldo'];
           $a++;     
        }

        $excel3->setCellValue('A'.$a, 'Total ');
        $excel3->setCellValue('E'.$a, $Total);
        $excel3->mergeCells('A'.$a.':D'.$a);
        $excel3->getStyle('A'.$a)->applyFromArray($style_col);
        $excel3->getStyle('B'.$a)->applyFromArray($style_col);
        $excel3->getStyle('C'.$a)->applyFromArray($style_col);
        $excel3->getStyle('D'.$a)->applyFromArray($style_col);
        $excel3->getStyle('E'.$a)->applyFromArray($style_col);

        $objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
        header('Content-type: application/vnd.ms-excel'); // jalan ketika tidak menggunakan ajax
        header('Content-Disposition: attachment; filename="'.$Filename.'"'); // jalan ketika tidak menggunakan ajax
        $objWriter->save('php://output');
    }

    private function reportFin_rekap_subject_A2($StatusStudentArr){
        $text = 'REKAP MAHASISWA ';
        $Name = $this->db->select('Description')
                 ->where('ID',$StatusStudentArr[0])
                 ->get('db_academic.status_student')->row()->Description;
        $text .= $Name;         
        for ($i=1; $i < count($StatusStudentArr); $i++) { 
           $Name = $this->db->select('Description')
                    ->where('ID',$StatusStudentArr[$i])
                    ->get('db_academic.status_student')->row()->Description;
            $text = $text.', '.$Name;
        }

        return $text;
    }

    private function reportFin_rekap_makeHeaderTable($a,$subjectTable,$excel3){
        $style = $this->standard_style();
        $style_row = $style['style_row'];
        $style_col = $style['style_col'];

        $excel3->setCellValue('A'.$a, $subjectTable);
        $excel3->mergeCells('A'.$a.':'.'I'.$a);
        $a++;
        $excel3->setCellValue('A'.$a, 'NO');
        $excel3->setCellValue('B'.$a, 'Nama');
        $excel3->setCellValue('C'.$a, 'NPM');
        $excel3->setCellValue('D'.$a, 'PROGRAM STUDI');
        $excel3->setCellValue('E'.$a, 'BULAN');
        $excel3->setCellValue('F'.$a, 'SEMESTER');
        $excel3->setCellValue('G'.$a, 'JUMLAH TAGIHAN');
        $excel3->setCellValue('H'.$a, 'JUMLAH PENERIMAAN');
        $excel3->setCellValue('I'.$a, 'TITIPAN');

        $excel3->getStyle('A'.$a)->applyFromArray($style_col);
        $excel3->getStyle('B'.$a)->applyFromArray($style_col);
        $excel3->getStyle('C'.$a)->applyFromArray($style_col);
        $excel3->getStyle('D'.$a)->applyFromArray($style_col);
        $excel3->getStyle('E'.$a)->applyFromArray($style_col);
        $excel3->getStyle('F'.$a)->applyFromArray($style_col);
        $excel3->getStyle('G'.$a)->applyFromArray($style_col);
        $excel3->getStyle('H'.$a)->applyFromArray($style_col);
        $excel3->getStyle('I'.$a)->applyFromArray($style_col);

        foreach(range('G','H') as $columnID) {
            $excel3->getColumnDimension($columnID)
                // ->setAutoSize(true);
                ->setWidth(25);
        }
    }

    public function reportFin_rekap(){
        $this->load->model('finance/m_finance');
        $input =  $this->getInputToken2();
        $data = $this->m_finance->report_rekap_std($input['StatusStudentArr']);
        $GetDateNow = date('Y-m-d');
        $DatePrint = date('d M Y', strtotime($GetDateNow));

        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
        $excel2 = $excel2->load('./uploads/finance/TemplateRekapByStatusSTD.xlsx'); // Empty Sheet
        $excel2->setActiveSheetIndex(0);

        $excel3 = $excel2->getActiveSheet();

        $subject_A2 = $this->reportFin_rekap_subject_A2($input['StatusStudentArr']);
        $excel3->setCellValue('A2', $subject_A2);
        $excel3->setCellValue('A3', 'sd '.$DatePrint);

        $style = $this->standard_style();
        $style_row = $style['style_row'];
        $style_col = $style['style_col'];

        $Filename = $subject_A2.'.xlsx';
        $a = 6;

        for ($i=0; $i < count($data); $i++) {
            $HeadsubjectTable = 'REKAP MAHASISWA  '.$data[$i]['StatusStudentName'];
            $dataPerMonth = $data[$i]['dataPerMonth'];
            for ($j=0; $j < count($dataPerMonth); $j++) { 
                $subjectTable = $HeadsubjectTable.' '.$dataPerMonth[$j]['endNameSemester'];
                $this->reportFin_rekap_makeHeaderTable($a,$subjectTable,$excel3); // make header table
                $a = $a + 2;
                $get_data =  $dataPerMonth[$j]['data'];
                $No = 0;
                $totalTagihan = 0;
                $totalPenerimaan = 0;
                $totalTitipan = 0;
                for ($k=0; $k < count($get_data); $k++) { // isi data
                    $No++;
                    $excel3->setCellValue('A'.$a, $No);
                    $excel3->setCellValue('B'.$a, $get_data[$k]['Name']);
                    $excel3->setCellValue('C'.$a, $get_data[$k]['NPM']);
                    $excel3->setCellValue('D'.$a, $get_data[$k]['ProdiName']);
                    $EffectiveDateStatus = '';
                    if (!empty($get_data[$k]['EffectiveDateStatus'])) {
                        $EffectiveDateStatus = date('M-y', strtotime($get_data[$k]['EffectiveDateStatus']));
                    }
                    $excel3->setCellValue('E'.$a, $EffectiveDateStatus);
                    
                    $Payment = $get_data[$k]['ALLPayment']['Payment'];
                    $TotalRowpayment = count($Payment);
                    // TITIPAN
                    $excel3->setCellValue('I'.$a, $get_data[$k]['ALLPayment']['TITIPAN']);

                    $rUntil = $a + ($TotalRowpayment - 1);

                    $excel3->mergeCells('A'.$a.':'.'A'.$rUntil);
                    $excel3->mergeCells('B'.$a.':'.'B'.$rUntil);
                    $excel3->mergeCells('C'.$a.':'.'C'.$rUntil);
                    $excel3->mergeCells('D'.$a.':'.'D'.$rUntil);
                    $excel3->mergeCells('E'.$a.':'.'E'.$rUntil);
                    $excel3->mergeCells('I'.$a.':'.'I'.$rUntil);
              
                    $excel3->getStyle('A'.$a.':'.'A'.$rUntil)->applyFromArray($style_row);
                    $excel3->getStyle('B'.$a.':'.'B'.$rUntil)->applyFromArray($style_row);
                    $excel3->getStyle('C'.$a.':'.'C'.$rUntil)->applyFromArray($style_row);
                    $excel3->getStyle('D'.$a.':'.'D'.$rUntil)->applyFromArray($style_row);
                    $excel3->getStyle('E'.$a.':'.'E'.$rUntil)->applyFromArray($style_row);
                    $excel3->getStyle('I'.$a.':'.'I'.$rUntil)->applyFromArray($style_row);

                    $totalTitipan = $get_data[$k]['ALLPayment']['TITIPAN'];

                    for ($l=0; $l < count($Payment); $l++) { 
                        $excel3->setCellValue('F'.$a, $Payment[$l]['Semester']);
                        $excel3->setCellValue('G'.$a, $Payment[$l]['JUMLAH_TAGIHAN']);
                        $excel3->setCellValue('H'.$a, $Payment[$l]['JUMLAH_PENERIMAAN']);

                        $excel3->getStyle('F'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('G'.$a)->applyFromArray($style_row);
                        $excel3->getStyle('H'.$a)->applyFromArray($style_row);

                        $totalTagihan =  $totalTagihan + $Payment[$l]['JUMLAH_TAGIHAN'];
                        $totalPenerimaan =  $totalPenerimaan + $Payment[$l]['JUMLAH_PENERIMAAN'];
                        $a++;
                    }

                }

                $excel3->setCellValue('A'.$a, 'Total ');
                $excel3->setCellValue('G'.$a, $totalTagihan);
                $excel3->setCellValue('H'.$a, $totalPenerimaan);
                $excel3->setCellValue('I'.$a, $totalTitipan);
                $excel3->mergeCells('A'.$a.':F'.$a);
                $excel3->getStyle('A'.$a)->applyFromArray($style_col);
                $excel3->getStyle('B'.$a)->applyFromArray($style_col);
                $excel3->getStyle('C'.$a)->applyFromArray($style_col);
                $excel3->getStyle('D'.$a)->applyFromArray($style_col);
                $excel3->getStyle('E'.$a)->applyFromArray($style_col);
                $excel3->getStyle('F'.$a)->applyFromArray($style_col);
                $excel3->getStyle('G'.$a)->applyFromArray($style_col);
                $excel3->getStyle('H'.$a)->applyFromArray($style_col);
                $excel3->getStyle('I'.$a)->applyFromArray($style_col);

                $a = $a + 2;
            } 
            
        }

        $objWriter = PHPExcel_IOFactory::createWriter($excel2, 'Excel2007');
        header('Content-type: application/vnd.ms-excel'); // jalan ketika tidak menggunakan ajax
        header('Content-Disposition: attachment; filename="'.$Filename.'"'); // jalan ketika tidak menggunakan ajax
        $objWriter->save('php://output');


    }

}
