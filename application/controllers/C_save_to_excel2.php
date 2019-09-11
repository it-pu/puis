<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_save_to_excel2 extends CI_Controller
{

    function __construct()
    {
        parent::__construct();
        $this->load->library('JWT');
//        include APPPATH.'third_party/PHPExcel/PHPExcel.php';
        date_default_timezone_set("Asia/Jakarta");
        $this->load->model('report/m_save_to_excel');
        $this->load->model('master/m_master');

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

    public function excel_rasio_dosen_mahasiswa()
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
        $Filename = 'excel_rasio_dosen_mahasiswa.xlsx';
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

        $excel->setActiveSheetIndex(0)->setCellValue('A1', "3.b. Rasio Dosen terhadap Mahasiswa");

        // make header
        $r = 3;
        $st = 0;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "No");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;

        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Prodi");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;

        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Jumlah Dosen");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $st++;

        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Jumlah Mahasiswa");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);


        $st++;

        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Jumlah Mahasiswa TA");
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_col);

        $r++;

        $jmlLecturer = 0;
        $jmlMahasiwa = 0;
        for ($i=0; $i < count($passToExcel); $i++) { 
            $st = 0; 
            $No = $i+1;
            $Prodi = $passToExcel[$i]['Name'];
            $TotalLecturer = $passToExcel[$i]['TotalLecturer'];
            $TotalMahasiwa = $passToExcel[$i]['TotalMahasiwa'];
            $JmlMhsTA = 0;

            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $No );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $Prodi );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $TotalLecturer );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $TotalMahasiwa );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);


            $st++;
            $huruf = $this->m_master->HurufColExcelNumber($st);
            $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $JmlMhsTA );
            $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

            $jmlLecturer += $TotalLecturer;
            $jmlMahasiwa += $TotalMahasiwa;

            $r++;

        }

        // total
        $st = 0;
        $stUntil = $st + 2 - 1;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $huruf_ = $this->m_master->HurufColExcelNumber($stUntil);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, "Jumlah");
        $excel->getActiveSheet()->mergeCells($huruf.$r.':'.$huruf_.$r);
        $excel->getActiveSheet()->getStyle($huruf.$r.':'.$huruf_.$r)->applyFromArray($style_row);

        $st = $stUntil + 1;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $jmlLecturer );
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

        $st++;
        $huruf = $this->m_master->HurufColExcelNumber($st);
        $excel->setActiveSheetIndex(0)->setCellValue($huruf.$r, $jmlMahasiwa );
        $excel->getActiveSheet()->getStyle($huruf.$r)->applyFromArray($style_row);

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

            $r++;

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

}