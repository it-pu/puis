<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_crm extends Admission_Controler {
    public $data = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('master/m_master');
        $this->load->model('admission/m_admission');
        $this->load->model('m_sendemail');
        $this->data['department'] = parent::__getDepartement(); 
        $this->load->model('m_api');
        $this->load->model('marketing/m_marketing');
        $this->data['NameMenu'] = $this->GlobalData['NameMenu'];
    }

    public function menu_crm($page){
        $data['department'] = parent::__getDepartement();
        $data['page'] = $page;
        $content = $this->load->view('page/'.$this->data['department'].'/crm/menu_crm',$data,true);
        parent::temp($content);
    }

    public function index()
    {
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$this->data['department'].'/crm/prospective_students',$this->data,true);
        $this->menu_crm($page);

        //        $content = $this->load->view('page/'.$this->data['department'].'/crm/page',$this->data,true);

    }

    public function CRMTeam(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$this->data['department'].'/crm/crmteam',$this->data,true);
        $this->menu_crm($page);
    }

    public function marketing_activity(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$this->data['department'].'/crm/marketing_activity',$this->data,true);
        $this->menu_crm($page);
    }

    public function contact(){
        $data['department'] = parent::__getDepartement();
        $page = $this->load->view('page/'.$this->data['department'].'/crm/contact',$this->data,true);
        $this->menu_crm($page);
    }

    public function crmpage()
    {
        $this->auth_ajax();
        $arr_result = array('html' => '','jsonPass' => '');
        $uri = $this->uri->segment(3);
        $content = $this->load->view('page/'.$this->data['department'].'/crm/'.$uri,$this->data,true);
        $arr_result['html'] = $content;
        echo json_encode($arr_result);
    }

    public function import_data_crm()
    {
        $msg = '';
        if(isset($_FILES["fileData"]["name"]))
        {
            $path = $_FILES["fileData"]["tmp_name"];
            $arr_insert = array();
            $arr_insert_auth = array();
            include APPPATH.'third_party/PHPExcel/PHPExcel.php';
            $excel2 = PHPExcel_IOFactory::createReader('Excel2007');
            $excel2 = $excel2->load($path); // Empty Sheet
            $objWorksheet = $excel2->setActiveSheetIndex(0);
            $CountRow = $objWorksheet->getHighestRow();
            for ($i=2; $i < ($CountRow + 1); $i++) {
                $temp = array();
                $ID_Numbering = $objWorksheet->getCellByColumnAndRow(0, $i)->getCalculatedValue();
                $find = $this->m_master->caribasedprimary('db_admission.crm_data','ID_Numbering',$ID_Numbering);
                if (count($find) > 0) {
                    continue;
                }
                $Candidate_Name = $objWorksheet->getCellByColumnAndRow(1, $i)->getCalculatedValue();
                $Candidate_Name = strtolower($Candidate_Name);
                $Candidate_Name = ucwords($Candidate_Name);
                $Regional = $objWorksheet->getCellByColumnAndRow(2, $i)->getCalculatedValue();
                $Regional = strtolower($Regional);
                $Regional = ucwords($Regional);
                $School = $objWorksheet->getCellByColumnAndRow(3, $i)->getCalculatedValue();
                $School = strtolower($School);
                $School = ucwords($School);
                $Class = $objWorksheet->getCellByColumnAndRow(4, $i)->getCalculatedValue();
                $Pathway = $objWorksheet->getCellByColumnAndRow(5, $i)->getCalculatedValue();
                $Gender = $objWorksheet->getCellByColumnAndRow(6, $i)->getCalculatedValue();
                $Gender = substr($Gender, 0,1);
                $Gender = ($Gender == '') ? null : $Gender;
                $Prospect_Year = $objWorksheet->getCellByColumnAndRow(7, $i)->getCalculatedValue();
                $BirthPlace = $objWorksheet->getCellByColumnAndRow(8, $i)->getCalculatedValue();
                $BirthPlace = strtolower($BirthPlace);
                $BirthPlace = ucwords($BirthPlace);
                $BirthDate = $objWorksheet->getCellByColumnAndRow(9, $i)->getCalculatedValue();
                $BirthDate = ($BirthDate == '' || $BirthDate == null || $BirthDate == trim($BirthDate) && strpos($BirthDate, ' ') !== false) ? null : date('Y-m-d',PHPExcel_Shared_Date::ExcelToPHP($BirthDate));
                $HomeAddress = $objWorksheet->getCellByColumnAndRow(11, $i)->getCalculatedValue();
                $HomeAddress = strtolower($HomeAddress);
                $HomeAddress = ucwords($HomeAddress);
                $Phone = $objWorksheet->getCellByColumnAndRow(12, $i)->getCalculatedValue();
                $MobilePhone = $objWorksheet->getCellByColumnAndRow(13, $i)->getCalculatedValue();
                $Email = $objWorksheet->getCellByColumnAndRow(14, $i)->getCalculatedValue();
                $Email = strtolower($Email);
                $Instagram = $objWorksheet->getCellByColumnAndRow(15, $i)->getCalculatedValue();
                $Instagram = strtolower($Instagram);
                $Line = $objWorksheet->getCellByColumnAndRow(16, $i)->getCalculatedValue();
                $Line = strtolower($Line);
                $ParentName = $objWorksheet->getCellByColumnAndRow(17, $i)->getCalculatedValue();
                $ParentName = strtolower($ParentName);
                $ParentName = ucwords($ParentName);
                $ParentMobile = $objWorksheet->getCellByColumnAndRow(18, $i)->getCalculatedValue();
                $Q1A = $objWorksheet->getCellByColumnAndRow(19, $i)->getCalculatedValue();
                $Q1A = strtolower($Q1A);
                $Q1A = ucwords($Q1A);
                $Q1B = $objWorksheet->getCellByColumnAndRow(20, $i)->getCalculatedValue();
                $Q1B = strtolower($Q1B);
                $Q1B = ucwords($Q1B);
                $Q2Ya = $objWorksheet->getCellByColumnAndRow(21, $i)->getCalculatedValue();
                $Q2Ya = strtolower($Q2Ya);
                $Q2Ya = ucwords($Q2Ya);
                $Q2Belum = $objWorksheet->getCellByColumnAndRow(22, $i)->getCalculatedValue();
                $Q2Belum = strtolower($Q2Belum);
                $Q2Belum = ucwords($Q2Belum);
                $Q3Ya = $objWorksheet->getCellByColumnAndRow(23, $i)->getCalculatedValue();
                $Q3Ya = strtolower($Q3Ya);
                $Q3Ya = ucwords($Q3Ya);
                $Q3Tidak = $objWorksheet->getCellByColumnAndRow(24, $i)->getCalculatedValue();
                $Q3Tidak = strtolower($Q3Tidak);
                $Q3Tidak = ucwords($Q3Tidak);
                $Q4 = $objWorksheet->getCellByColumnAndRow(25, $i)->getCalculatedValue();
                $Q4 = strtolower($Q4);
                $Q4 = ucwords($Q4);
                $Q5A = $objWorksheet->getCellByColumnAndRow(26, $i)->getCalculatedValue();
                $Q5A = strtolower($Q5A);
                $Q5A = ucwords($Q5A);
                $Q6Ya = $objWorksheet->getCellByColumnAndRow(27, $i)->getCalculatedValue();
                $Q6Ya = strtolower($Q6Ya);
                $Q6Ya = ucwords($Q6Ya);
                $Q6Tidak = $objWorksheet->getCellByColumnAndRow(28, $i)->getCalculatedValue();
                $Q6Tidak = strtolower($Q6Tidak);
                $Q6Tidak = ucwords($Q6Tidak);
                $Q6Rangking = $objWorksheet->getCellByColumnAndRow(29, $i)->getCalculatedValue();
                $Q6Rangking = strtolower($Q6Rangking);
                $Q6Rangking = ucwords($Q6Rangking);
                $Q7Ya = $objWorksheet->getCellByColumnAndRow(30, $i)->getCalculatedValue();
                $Q7Ya = strtolower($Q7Ya);
                $Q7Ya = ucwords($Q7Ya);
                $Q7Tidak = $objWorksheet->getCellByColumnAndRow(31, $i)->getCalculatedValue();
                $Q7Tidak = strtolower($Q7Tidak);
                $Q7Tidak = ucwords($Q7Tidak);
                $Q7JuaraKe = $objWorksheet->getCellByColumnAndRow(32, $i)->getCalculatedValue();
                $Q7JuaraKe = strtolower($Q7JuaraKe);
                $Q7JuaraKe = ucwords($Q7JuaraKe);
                $Q8 = $objWorksheet->getCellByColumnAndRow(33, $i)->getCalculatedValue();
                $Q8 = strtolower($Q8);
                $Q8 = ucwords($Q8);
                $Q9 = $objWorksheet->getCellByColumnAndRow(34, $i)->getCalculatedValue();
                $Q9 = strtolower($Q9);
                $Q9 = ucwords($Q9);
                $Q10 = $objWorksheet->getCellByColumnAndRow(35, $i)->getCalculatedValue();
                $Q10 = strtolower($Q10);
                $Q10 = ucwords($Q10);
                $Q11A = $objWorksheet->getCellByColumnAndRow(36, $i)->getCalculatedValue();
                $Q11A = strtolower($Q11A);
                $Q11A = ucwords($Q11A);
                $Q12A = $objWorksheet->getCellByColumnAndRow(37, $i)->getCalculatedValue();
                $Q12A = strtolower($Q12A);
                $Q12A = ucwords($Q12A);
                $Q12B = $objWorksheet->getCellByColumnAndRow(38, $i)->getCalculatedValue();
                $Q12B = strtolower($Q12B);
                $Q12B = ucwords($Q12B);
                $SchoolID = $objWorksheet->getCellByColumnAndRow(41, $i)->getCalculatedValue();
                $Fu1 = $objWorksheet->getCellByColumnAndRow(42, $i)->getCalculatedValue();
                $Fu2 = $objWorksheet->getCellByColumnAndRow(43, $i)->getCalculatedValue();
                $Fu3 = $objWorksheet->getCellByColumnAndRow(44, $i)->getCalculatedValue();
                $Fu4 = $objWorksheet->getCellByColumnAndRow(45, $i)->getCalculatedValue();
                $StatusFU = $objWorksheet->getCellByColumnAndRow(46, $i)->getCalculatedValue();
                $SetReminder = $objWorksheet->getCellByColumnAndRow(47, $i)->getCalculatedValue();
                $SalesPerson = $objWorksheet->getCellByColumnAndRow(48, $i)->getCalculatedValue();
                $ItemType = $objWorksheet->getCellByColumnAndRow(49, $i)->getCalculatedValue();
                $Path = $objWorksheet->getCellByColumnAndRow(50, $i)->getCalculatedValue();

                $temp = array(
                    'ID_Numbering' => $ID_Numbering,    
                    'Candidate_Name' => $Candidate_Name,    
                    'Regional' => $Regional,    
                    'School' => $School,    
                    'Class' => $Class,    
                    'Pathway' => $Pathway,    
                    'Gender' => $Gender,    
                    'Prospect_Year' => $Prospect_Year,    
                    'BirthPlace' => $BirthPlace,    
                    'BirthDate' => $BirthDate,    
                    'HomeAddress' => $HomeAddress,    
                    'Phone' => $Phone,    
                    'MobilePhone' => $MobilePhone,    
                    'Email' => $Email,    
                    'Instagram' => $Instagram,    
                    'Line' => $Line,    
                    'ParentName' => $ParentName,    
                    'ParentMobile' => $ParentMobile,    
                    'Q1A' => $Q1A,    
                    'Q1B' => $Q1B,    
                    'Q2Ya' => $Q2Ya,    
                    'Q2Belum' => $Q2Belum,    
                    'Q3Ya' => $Q3Ya,    
                    'Q3Tidak' => $Q3Tidak,    
                    'Q4' => $Q4,    
                    'Q5A' => $Q5A,    
                    'Q6Ya' => $Q6Ya,    
                    'Q6Tidak' => $Q6Tidak,    
                    'Q6Rangking' => $Q6Rangking,    
                    'Q7Ya' => $Q7Ya,    
                    'Q7Tidak' => $Q7Tidak,    
                    'Q7JuaraKe' => $Q7JuaraKe,    
                    'Q8' => $Q8,    
                    'Q9' => $Q9,    
                    'Q10' => $Q10,    
                    'Q11A' => $Q11A,
                    'Q12A' => $Q12A,    
                    'Q12B' => $Q12B,    
                    'SchoolID' => $SchoolID,
                    'Fu1' => $Fu1,    
                    'Fu2' => $Fu2,    
                    'Fu3' => $Fu3,    
                    'Fu4' => $Fu4,    
                    'StatusFU' => $StatusFU,    
                    'SetReminder' => $SetReminder,    
                    'SalesPerson' => $SalesPerson,    
                    'ItemType' => $ItemType,    
                    'Path' => $Path,    
                );
                $this->db->insert('db_admission.crm_data',$temp);
            }
        }
        else
        {
            $msg = 'There is no file uploaded';
        }

        echo json_encode($msg);
    }

    public function showdata_crm2()
    {
        $requestData= $_REQUEST;
        $where = ' where ID_Numbering LIKE "%'.$requestData['search']['value'].'%" or Candidate_Name LIKE "'.$requestData['search']['value'].'%" or Regional LIKE "'.$requestData['search']['value'].'%"
               or School LIKE "'.$requestData['search']['value'].'%"  
               or Pathway LIKE "'.$requestData['search']['value'].'%"  
               or Prospect_Year LIKE "'.$requestData['search']['value'].'%"  
               or Phone LIKE "%'.$requestData['search']['value'].'%"  
               or MobilePhone LIKE "%'.$requestData['search']['value'].'%"  
               or Email LIKE "'.$requestData['search']['value'].'%"  
               or ParentName LIKE "'.$requestData['search']['value'].'%"  
                ';
        $sqltotalData = 'select count(*) as total from db_admission.crm_data '.$where;
        $querytotalData = $this->db->query($sqltotalData)->result_array();
        $totalData = $querytotalData[0]['total'];

        $sql = 'select ID,ID_Numbering,Candidate_Name,Regional,School,Pathway,Gender,Prospect_Year,Phone,MobilePhone,Email,ParentName
                from db_admission.crm_data
               ';
        $sql.= $where;
        $sql.= ' ORDER BY ID_Numbering asc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
        $query = $this->db->query($sql)->result_array();

        $No = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];
            $nestedData[] = $No;
            $nestedData[] = $row['ID_Numbering'];
            $nestedData[] = $row['Candidate_Name'];
            $nestedData[] = $row['Regional'];
            $nestedData[] = $row['School'];
            $nestedData[] = $row['Pathway'];
            $nestedData[] = $row['Gender'];
            $nestedData[] = $row['Prospect_Year'];
            $nestedData[] = $row['Phone'];
            $nestedData[] = $row['MobilePhone'];
            $nestedData[] = $row['Email'];
            $nestedData[] = $row['ParentName'];
            $nestedData[] = '';
            $nestedData[] = $row['ID'];
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

    public function showdata_crm($PeriodID){
        $requestData= $_REQUEST;

        $orderBy = ' GROUP BY c.Name ASC ';
        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = ' AND ( c.Name LIKE "%'.$search.'%" 
            OR c.NIP LIKE "%'.$search.'%"
             OR s.SchoolName LIKE "%'.$search.'%"
             OR em.Name LIKE "%'.$search.'%"
              OR rms.SchoolMajor LIKE "%'.$search.'%" ) ';
        }

        // FlagSales = 0 adalah jalur beasiswa

        $queryDefault = 'SELECT c.*, s.SchoolName, r.RegionName, rms.SchoolMajor, cp.Year AS PeriodYear, em.Name AS PICName FROM db_admission.crm c 
                                  LEFT JOIN db_admission.school s ON (s.ID = c.SchoolID)
                                  LEFT JOIN db_admission.region r ON (r.RegionID = s.CityID)
                                  LEFT JOIN db_admission.register_major_school rms ON (rms.ID = c.PathwayID)
                                  LEFT JOIN db_admission.crm_period cp ON (cp.ID = c.PeriodID)
                                  LEFT JOIN db_employees.employees em ON (em.NIP = c.NIP)
                                  WHERE (c.Status = 6 or FlagSales = 0) AND c.PeriodID = "'.$PeriodID.'" '.$dataSearch.' '.$orderBy.' ';

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];

            $showBeasiswa = '';
            if ($row['FlagSales'] == 0) {
                $$showBeasiswa = '<br/><i class="fa fa-circle" style="color:#bdc80e;"></i> <span style =  "color:blue;">Jalur Beasiswa</span>';

            }

            $dataDetail = '<textarea class="hide" id="det_'.$no.'">'.json_encode($row).'</textarea>';
            $nestedData[] = '<div style="text-align:center;">'.$no.'</div>';
            $nestedData[] = '<div>'.$row['Name'].'<br/>'.$row['Phone'].' | '.$row['Email'].'<br/>'.$showBeasiswa.'</div>';
            $nestedData[] = '<div>'.$row['SchoolName'].'<br/>'.$row['RegionName'].'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$row['SchoolMajor'].'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$row['Gender'].'</div>';
            $nestedData[] = '<div style="text-align:center;">'.$row['PeriodYear'].'</div>';
            $nestedData[] = '<div>Ayah : '.$row['FatherName'].'<br/>Ibu : '.$row['MotherName'].'</div>';
            $nestedData[] = '<div style="text-align: center;"><button class="btn btn-default btnShowDet" data-no="'.$no.'">Choose</button>'.$dataDetail.'</div>';
            $nestedData[] = ['dataAll' => $row];
            
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

    public function deletecrm_by_id()
    {
        $msg = '';
        $this->auth_ajax();
        $input = $this->getInputToken();
        $ID = $input['ID'];
        $this->m_master->delete_id_table($ID,'crm_data');
        echo json_encode(array('asd','asdas','asdsad'));
    }

    public function uploadDocumentPS(){

        $ID = $this->input->get('id');
        $name = $this->input->get('name').'.pdf';

        // Cek apakah sudah ada filenya atau blm
        $dataFile = $this->db->select('File')->get_where('db_admission.crm',array(
            'ID' => $ID
        ))->result_array();

        if($dataFile[0]['File']!=null || $dataFile[0]['File']!=''){
            $file = './uploads/crm/'.$dataFile[0]['File'];
            if(file_exists($file)){
                unlink($file);
            }
        }


        $config['upload_path']          = './uploads/crm/';
        $config['allowed_types']        = 'pdf';
        $config['max_size']             = 8000; // 8 mb
        $config['file_name']            = $name;

        $this->load->library('upload', $config);
        if ( ! $this->upload->do_upload('userfile')){
            $error = array('error' => $this->upload->display_errors());
            return print_r(json_encode($error));
        }
        else {

            $success = array('success' => $this->upload->data());

            $this->db->where('ID', $ID);
            $this->db->update('db_admission.crm',array(
                'File' => $name
            ));


            return print_r(json_encode($success));
        }

    }

}
