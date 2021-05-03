<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class C_rest2 extends CI_Controller {

    function __construct()
    {
        parent::__construct();
        header('Access-Control-Allow-Origin: *');
        header('Content-Type: application/json');
        $this->load->model('m_api');
        $this->load->model('m_rest');
        $this->load->model('akademik/m_tahun_akademik');
        $this->load->model('master/m_master');
        $this->load->library('JWT');
        $this->load->library('google');
    }

    private function getInputToken()
    {
        $token = $this->input->post('token');
        $key = "s3Cr3T-G4N";
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

    public function is_url_exist($url){
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
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

    public function send_notif_browser()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                /*
                    Parameter
                    var data = {
                        'auth' => 's3Cr3T-G4N',
                        'Logging' : {fieldTable}, //   CreatedBy,URL,Title,Description required
                        'To' : {
                            'NIP' : [], berbentuk array indeks // boleh salah satu
                            'Div' : [], berbentuk array indeks // boleh salah satu
                        },
                        'Email' : 'Yes', Field ini bersifat tentative
                    };
        
                */
                if (array_key_exists('Logging', $dataToken) &&  array_key_exists('To', $dataToken)) {
                    $arr_to = array();
                    $arr_to_email = array();
                    $Logging = (array) json_decode(json_encode($dataToken['Logging']),true);
                        if (!array_key_exists('CreatedBy', $Logging)  && !array_key_exists('Title', $Logging) && !array_key_exists('Description', $Logging) ) {
                            echo '{"status":"999","message":"Parameter not match"}';
                            die();
                        }

                            // Data Employees
                            $CreatedBy = $Logging['CreatedBy'];
                            $G_emp = $this->m_master->caribasedprimary('db_employees.employees','NIP',$CreatedBy);

                        if (!array_key_exists('Icon', $Logging)) {
                               $url = base_url('uploads/employees/'.$G_emp[0]['Photo']);
                               $img_profile = ($this->is_url_exist($url) && $G_emp[0]['Photo']!='')
                                   ? $url
                                   : url_pas.'/images/icon/lecturer.png';
                               $Logging['Icon'] = $img_profile;   
                        }


                        if (!array_key_exists('CreatedName', $Logging)) {
                            $Logging['CreatedName'] = $G_emp[0]['Name']; 
                        }

                        if (!array_key_exists('CreatedAt', $Logging)) {
                            $Logging['CreatedAt'] = date('Y-m-d H:i:s'); 
                        }

                        // check url Logging terisi
                            $bool = false;
                            $arr_url = array('URLDirect','URLDirectStudent','URLDirectLecturer','URLDirectLecturerKaprodi');
                            for ($i=0; $i < count($arr_url); $i++) { 
                                if (!$bool) {
                                    if (array_key_exists($arr_url[$i], $Logging)) {
                                        // check terisi atau tidak
                                        if ($Logging[$arr_url[$i]] != '' && $Logging[$arr_url[$i]] != null && (!empty($Logging[$arr_url[$i]]))) {
                                            $bool = true;
                                        }
                                    }
                                }
                            }

                            if (!$bool) {
                                echo '{"status":"999","message":"Error in parameter URL"}';
                                die();
                            }

                        // check parameter To
                            $To = (array) json_decode(json_encode($dataToken['To']),true);
                            if (array_key_exists('Div', $To) || array_key_exists('NIP', $To) ) {
                                $Div = $To['Div'];
                                if (array_key_exists('Div',$To)) {
                                    if (!is_array($Div)) {
                                        echo '{"status":"999","message":"Error in parameter To"}';
                                        die();
                                    }
                                    else
                                    {
                                        $Div = (array) json_decode(json_encode($To['Div']),true);
                                        for ($i=0; $i < count($Div); $i++) { 
                                           $sql = 'select a.NIP,a.Name,SPLIT_STR(a.PositionMain, ".", 1) as PositionMain1,
                                                   SPLIT_STR(a.PositionMain, ".", 2) as PositionMain2,
                                                         a.StatusEmployeeID
                                                    FROM   db_employees.employees as a
                                                    where ( SPLIT_STR(a.PositionMain, ".", 1) = ? or SPLIT_STR(a.PositionOther1, ".", 1) = ? or SPLIT_STR(a.PositionOther2, ".", 1) = ? or SPLIT_STR(a.PositionOther3, ".", 1) = ? ) and a.StatusEmployeeID != -1        
                                                ';
                                            $query=$this->db->query($sql, array($Div[$i]))->result_array();
                                            for ($j=0; $j < count($query); $j++) { 
                                                $NIP = $query[$j]['NIP'];
                                                // search in arr_to
                                                    $bool =true;
                                                    for ($k=0; $k < count($arr_to); $k++) { 
                                                        if ($NIP == $arr_to[$k]) {
                                                           $bool =false;
                                                           break;
                                                        }
                                                    }

                                                    if ($bool) {
                                                        $arr_to[] = $NIP;
                                                    }
                                            }
                                        }
                                    }
                                }

                                if (array_key_exists('NIP',$To)) {
                                    $NIP = $To['NIP'];
                                    if (!is_array($NIP)) {
                                        echo '{"status":"999","message":"Error in parameter To"}';
                                        die();
                                    }
                                    else
                                    {
                                        $NIP_arr = (array) json_decode(json_encode($To['NIP']),true);
                                        for ($i=0; $i < count($NIP_arr); $i++) {
                                            $NIP = $NIP_arr[$i]; 
                                            $bool =true;
                                            for ($k=0; $k < count($arr_to); $k++) { 
                                                if ($NIP == $arr_to[$k]) {
                                                   $bool =false;
                                                   break;
                                                }
                                            }

                                            if ($bool) {
                                                $arr_to[] = $NIP;
                                            }
                                        }
                                    }

                                }
                                
                            }
                            else
                            {
                                echo '{"status":"999","message":"Error in parameter To"}';
                                die();
                            }    

                    //============= Logging ==========
                    // Insert Logging
                        $this->db->insert('db_notifikasi.logging',$Logging);
                        $insert_id_logging = $this->db->insert_id();  

                    // insert ke user
                       for ($i=0; $i < count($arr_to); $i++) { 
                            $Log_arr_ins = array(
                                'IDLogging' => $insert_id_logging,
                                'UserID' => $arr_to[$i],
                            );
                            $this->db->insert('db_notifikasi.logging_user',$Log_arr_ins);
                            // fill arr_to_email
                            $G_emp = $this->m_master->SearchNameNIP_Employees_PU_Holding($arr_to[$i]);
                            if (count($G_emp) > 0) {
                                if ($G_emp[0]['EmailPU'] != '') {
                                    $arr_to_email[] = $G_emp[0]['EmailPU'];
                                }
                                
                            }
                            
                        }     

                    if (array_key_exists('Email', $dataToken)) {
                        if ($dataToken['Email'] == 'Yes') {
                            // send email
                            $data = array(
                                'auth' => 's3Cr3T-G4N',
                                'to' => implode(',', $arr_to_email),
                                'subject' => strip_tags($Logging['Title']),
                                'text' => $Logging['Description'],
                            );

                            $url = url_pas.'rest/__sendEmail';
                            $token = $this->jwt->encode($data,"UAP)(*");
                            $this->m_master->apiservertoserver($url,$token);
                        }
                        else
                        {
                            // No send Email
                        }
                    }

                    echo json_encode(array(1));
                }
                else
                {
                    echo '{"status":"999","message":"Parameter not match"}';
                }
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        catch(Exception $e) {
             // handling orang iseng
             echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function remove_file()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $DeleteDb = $dataToken['DeleteDb'];
                $filePath = $dataToken['filePath'];
                $filePath = str_replace('-', '\\', $filePath);
                $bool = false;
                $DeleteDb = (array) json_decode(json_encode($DeleteDb),true);
                if ($DeleteDb['auth'] == 'Yes') {
                    /* Type Field 
                        0 : String
                        1 : Array
                    */
                        $detail = $DeleteDb['detail'];
                        $table = $detail['table'];
                        $idtable = $detail['idtable'];
                        $field = $detail['field'];
                        $typefield = $detail['typefield'];
                        $delimiter = $detail['delimiter'];
                        $fieldwhere = $detail['fieldwhere'];
                      $G_data = $this->m_master->caribasedprimary($table,$fieldwhere,$idtable);
                      if ($typefield == 0) {
                          if ($delimiter != '' && $delimiter != null) {
                             $arr_file = explode($delimiter, $G_data[0][$field]);
                             // get filename
                             $arr_temp = explode('\\', $filePath);
                             $keyArr = count($arr_temp) - 1;
                             $filename = $arr_temp[$keyArr];
                             $arr_rs = array();
                             for ($i=0; $i < count($arr_file); $i++) { 
                                if ($filename != $arr_file[$i]) {
                                    $arr_rs[] = $arr_file[$i];
                                }
                             }

                             $rs = (count($arr_rs) == 0) ? '' : implode($delimiter, $arr_rs);
                             $dataSave = array(
                                $field => $rs
                             );

                             $this->db->where($fieldwhere,$idtable);
                             $this->db->update($table,$dataSave);
                             $bool = true;
                          }
                      }
                      else if ($typefield == 1) {
                          $arr_file = (array) json_decode($G_data[0][$field],true);
                          // get filename
                          $arr_temp = explode('\\', $filePath);
                          $keyArr = count($arr_temp) - 1;
                          $filename = $arr_temp[$keyArr];

                          $arr_rs = array();
                          for ($i=0; $i < count($arr_file); $i++) { 
                             if ($filename != $arr_file[$i]) {
                                 $arr_rs[] = $arr_file[$i];
                             }
                          }

                          $rs = (count($arr_rs) == 0) ? NULL : json_encode($arr_rs);
                          $dataSave = array(
                             $field => $rs
                          );
                          $this->db->where($fieldwhere,$idtable);
                          $this->db->update($table,$dataSave);
                          $bool = true;
                      }

                }
                $path = FCPATH.'uploads\\'.$filePath;
                unlink($path);
                echo json_encode(1);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        catch(Exception $e) {
             // handling orang iseng
             echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function get_data_po($Status)
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {

                    //check action
                    $whereaction = '';
                    $joinaction = '';
                    $fieldaction = '';
                    $OrderBY = '';
                    if (array_key_exists('action', $dataToken)) {
                        if ($dataToken['action'] == 'forspb') {
                           $fieldaction = ', poi.InvoicePO,poi.InvoicePayPO,InvoiceLeftPO,poi.Status as StatusPOI,poi.ID as ID_poi ';
                           $joinaction = ' join db_purchasing.po_invoice_status as poi on a.Code = poi.Code_po_create ';
                           // $whereaction = ' and StatusPOI = 0';
                           $whereaction = ' and POPrint_Approve IS NOT NULL and POPrint_Approve != "" ';
                           $OrderBY = 'StatusPOI asc,';
                        }
                        
                    }

                    // get Department
                    $IDDepartementPUBudget = $dataToken['IDDepartementPUBudget'];
                    $WhereFiltering = '';
                    // $WhereFiltering2 = '';
                    if ($IDDepartementPUBudget != 'NA.4') {
                        $NIP = $dataToken['sessionNIP'];
                        $WhereFiltering = ' and (Departement = "'.$IDDepartementPUBudget.'" or JsonStatus2 REGEXP \'"NIP":"[[:<:]]'.$NIP.'[[:>:]]"\' or  JsonStatus REGEXP \'"NIP":"[[:<:]]'.$NIP.'[[:>:]]"\' ) ';
                        // $WhereFiltering2 .= ' or JsonStatus REGEXP \'"NIP":"[[:<:]]'.$NIP.'[[:>:]]"\'';
                    }

                    if (array_key_exists('Years', $dataToken)) {
                        $WhereFiltering .= ' and Year = "'.$dataToken['Years'].'" ';
                    }

                    if (array_key_exists('Month', $dataToken)) {
                        if ($dataToken['Month'] != 'all') {
                            $WhereFiltering .= ' and MONTH(CreatedAt) = '.(int)$dataToken['Month'];
                        }
                    }

                     $this->load->model('budgeting/m_pr_po');
                    $requestData= $_REQUEST;
                    $StatusQuery = ($Status == 'All') ? '' : ' and Status = '.$Status;
                    $sqltotalData = 'select count(*) as total  from (
                                select if(a.TypeCreate = 1,"PO","SPK") as TypeCode,a.Code,a.ID_pre_po_supplier,b.CodeSupplier,
                                    c.NamaSupplier,c.PICName as PICSupplier,c.Alamat as AlamatSupplier,
                                    a.JsonStatus,
                                    if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = -1,"Reject","Cancel") ) )) as StatusName,a.CreatedBy,d.Name as NameCreateBy,a.CreatedAt,a.PostingDate,g.PRCode,h.JsonStatus as JsonStatus2,h.Year,h.Departement,a.Status,a.POPrint_Approve '.$fieldaction.'
                                from db_purchasing.po_create as a
                                left join db_purchasing.pre_po_supplier as b on a.ID_pre_po_supplier = b.ID
                                left join db_purchasing.m_supplier as c on b.CodeSupplier = c.CodeSupplier
                                left join db_employees.employees as d on a.CreatedBy = d.NIP
                                left join db_purchasing.po_detail as e on a.Code = e.Code
                                left join db_purchasing.pre_po_detail as f on e.ID_pre_po_detail = f.ID
                                left join db_budgeting.pr_detail as g on f.ID_pr_detail = g.ID
                                join db_budgeting.pr_create as h on h.PRCode = g.PRCode
                                '.$joinaction.'
                                group by a.Code     
                            )aa
                           ';

                    $sqltotalData.= ' where (Code LIKE "%'.$requestData['search']['value'].'%" or TypeCode LIKE "'.$requestData['search']['value'].'%" or NamaSupplier LIKE "%'.$requestData['search']['value'].'%" or CodeSupplier LIKE "'.$requestData['search']['value'].'%"
                          or NameCreateBy LIKE "'.$requestData['search']['value'].'%" or CreatedBy LIKE "'.$requestData['search']['value'].'%" 
                          or PRCode LIKE "'.$requestData['search']['value'].'%"  
                        ) '.$StatusQuery.$WhereFiltering.$whereaction ;

                    $querytotalData = $this->db->query($sqltotalData)->result_array();
                    $totalData = $querytotalData[0]['total'];

                    $StatusQuery = ($Status == 'All') ? '' : ' and Status = '.$Status;
                    $sql = 'select * from (
                                select a.ID as ID_po_create,if(a.TypeCreate = 1,"PO","SPK") as TypeCode,a.Code,a.ID_pre_po_supplier,b.CodeSupplier,
                                    c.NamaSupplier,c.PICName as PICSupplier,c.Alamat as AlamatSupplier,
                                    a.JsonStatus,
                                    if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = -1,"Reject","Cancel") ) )) as StatusName,a.CreatedBy,d.Name as NameCreateBy,a.CreatedAt,a.PostingDate,g.PRCode,h.JsonStatus as JsonStatus2,h.Year,h.Departement,a.Status,a.POPrint_Approve '.$fieldaction.'
                                from db_purchasing.po_create as a
                                left join db_purchasing.pre_po_supplier as b on a.ID_pre_po_supplier = b.ID
                                left join db_purchasing.m_supplier as c on b.CodeSupplier = c.CodeSupplier
                                left join db_employees.employees as d on a.CreatedBy = d.NIP
                                left join db_purchasing.po_detail as e on a.Code = e.Code
                                left join db_purchasing.pre_po_detail as f on e.ID_pre_po_detail = f.ID
                                left join db_budgeting.pr_detail as g on f.ID_pr_detail = g.ID
                                join db_budgeting.pr_create as h on h.PRCode = g.PRCode
                                '.$joinaction.'
                                group by a.Code      
                            )aa
                           ';

                    $sql.= ' where (Code LIKE "%'.$requestData['search']['value'].'%" or TypeCode LIKE "'.$requestData['search']['value'].'%" or NamaSupplier LIKE "%'.$requestData['search']['value'].'%" or CodeSupplier LIKE "'.$requestData['search']['value'].'%"
                          or NameCreateBy LIKE "'.$requestData['search']['value'].'%" or CreatedBy LIKE "'.$requestData['search']['value'].'%" 
                          or PRCode LIKE "'.$requestData['search']['value'].'%"  
                        ) '.$StatusQuery.$WhereFiltering.$whereaction ;
                    $sql.= ' ORDER BY '.$OrderBY.' ID_po_create Desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
                    $query = $this->db->query($sql)->result_array();

                    $No = $requestData['start'] + 1;
                    $G_Approver = $this->m_pr_po->Get_m_Approver_po();
                    if (array_key_exists('length', $dataToken)) {
                        $Count_G_Approver = $dataToken['length'];
                    }
                    else
                    {
                        $Count_G_Approver = count($G_Approver);
                    }
                    $data = array();
                    for($i=0;$i<count($query);$i++){
                        $nestedData=array();
                        $row = $query[$i];
                        $nestedData[] = $No;
                        $nestedData[] = $row['Code'];
                        $nestedData[] = $row['TypeCode'];
                        $nestedData[] = $row['CodeSupplier'].' || '.$row['NamaSupplier'];
                        $nestedData[] = $row['StatusName'];
                        $nestedData[] = '';
                        $JsonStatus = (array)json_decode($row['JsonStatus'],true);
                        $arr = array();
                        if (count($JsonStatus) > 0) {
                            for ($j=1; $j < count($JsonStatus); $j++) {
                                // $getName = $this->m_master->caribasedprimary('db_employees.employees','NIP',$JsonStatus[$j]['NIP']);
                                $getName = $this->m_master->SearchNameNIP_Employees_PU_Holding($JsonStatus[$j]['NIP']);
                                $Name = $getName[0]['Name'];
                                $StatusInJson = $JsonStatus[$j]['Status'];
                                switch ($StatusInJson) {
                                    case '1':
                                        $stjson = '<i class="fa fa-check" style="color: green;"></i>';
                                        break;
                                    case '2':
                                        $stjson = '<i class="fa fa-times" aria-hidden="true" style="color: red;"></i>';
                                        break;
                                    default:
                                        $stjson = "-";
                                        break;
                                }
                                $arr[] = $stjson.'<br>'.'Approver : '.$Name.'<br>'.'Approve At : '.$JsonStatus[$j]['ApproveAt'];
                            }
                        }

                        $c = $Count_G_Approver - count($arr);
                        for ($l=0; $l < $c; $l++) { 
                             $arr[] = '-';
                        }

                        $nestedData = array_merge($nestedData,$arr);
                        $nestedData[] = $row['NameCreateBy'].'<br>'.'At : '.$row['CreatedAt'];
                        // find PR in po_detail
                            $arr_temp = array();
                            $sql_get_pr = 'select a.ID,a.ID_m_catalog,b.Item,c.ID as ID_pre_po_detail,d.Code,a.PRCode
                            from db_budgeting.pr_detail as a join db_purchasing.m_catalog as b on a.ID_m_catalog = b.ID
                            left join db_purchasing.pre_po_detail as c on a.ID = c.ID_pr_detail
                            left join db_purchasing.po_detail as d on c.ID = d.ID_pre_po_detail
                            where d.Code = ?
                            ';
                            $query_get_pr=$this->db->query($sql_get_pr, array($row['Code']))->result_array();
                            for ($j=0; $j < count($query_get_pr); $j++) { 
                                if (count($arr_temp) == 0) {
                                    $arr_temp[] = $query_get_pr[$j]['PRCode'];
                                    // $arr_temp[] = ['POPrint_Approve' => $row['POPrint_Approve']];
                                    
                                }
                                else
                                {
                                    // check exist
                                    $bool = true;
                                    for ($k=0; $k < count($arr_temp); $k++) { 
                                        if ($arr_temp[$k]==$query_get_pr[$j]['PRCode']) {
                                            $bool = false;    
                                            break;
                                        }
                                    }

                                    if ($bool) {
                                        $arr_temp[] = $query_get_pr[$j]['PRCode'];
                                        // $arr_temp[] = ['POPrint_Approve' => $row['POPrint_Approve']];
                                    }

                                }
                            }

                            // pass Invoice PO
                            if (array_key_exists('InvoicePO', $row)) {
                                $arr_temp[] = array(
                                    'InvoicePO' => $row['InvoicePO'],
                                    'InvoicePayPO' => $row['InvoicePayPO'],
                                    'InvoiceLeftPO' => $row['InvoiceLeftPO'],
                                    'StatusPOI' => $row['StatusPOI'],
                                    'ID_poi' => $row['ID_poi'],
                                );
                            }
                        $arr_temp[] = ['POPrint_Approve' => $row['POPrint_Approve']];    
                        $nestedData[] = $arr_temp;
                        $data[] = $nestedData;
                        $No++;
                    }

                    $json_data = array(
                        "draw"            => intval( $requestData['draw'] ),
                        "recordsTotal"    => intval($totalData),
                        "recordsFiltered" => intval($totalData ),
                        "data"            => $data,
                    );
                    echo json_encode($json_data);
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function Get_data_po_by_Code()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('budgeting/m_pr_po');
                    $Code = $dataToken['Code'];
                    $data = $this->m_pr_po->Get_data_po_by_Code($Code);
                    echo json_encode($data);
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function Get_supplier_po_by_Code()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('budgeting/m_pr_po');
                    $Code = $dataToken['Code'];
                    $data = $this->m_pr_po->Get_supplier_po_by_Code($Code);
                    echo json_encode($data);
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function ajax_terbilang()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $terbilang = $this->m_master->moneySay($dataToken['bilangan']);
                    $terbilang = trim(ucwords($terbilang));
                    echo json_encode($terbilang);
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function ajax_dayOfDate()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $rs = '';
                    $hari = array ( 1 =>    'Senin',
                                'Selasa',
                                'Rabu',
                                'Kamis',
                                'Jumat',
                                'Sabtu',
                                'Minggu'
                            );
                    $Date = $dataToken['Date'];
                    $Date = date("Y-m-d", strtotime($Date));
                    $num = date('N', strtotime($Date)); 
                    $NameDay = $hari[$num];
                    echo json_encode($NameDay);
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function Get_spk_pembukaan()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $rs = '';
                    $Date = $dataToken['Date'];
                    // get hari
                        $data = array(
                              'Date' => $Date,
                              'auth' => 's3Cr3T-G4N', 
                        ); 
                        $url = url_pas.'rest2/__ajax_dayOfDate';
                        $token = $this->jwt->encode($data,"UAP)(*");
                        // $this->m_master->apiservertoserver($url,$token);
                        $NameDay = $this->m_master->apiservertoserver($url,$token);
                        // print_r($NameDay);die();
                    // terbilang tanggal
                         $bilangan = date("d", strtotime($Date));
                         $bilangan = (int) $bilangan;
                         $data = array(
                               'bilangan' => $bilangan,
                               'auth' => 's3Cr3T-G4N', 
                         ); 
                         $url = url_pas.'rest2/__ajax_terbilang';
                         $token = $this->jwt->encode($data,"UAP)(*");
                         // $this->m_master->apiservertoserver($url,$token);
                         $Tanggal = $this->m_master->apiservertoserver($url,$token);
                         // print_r($bilangan);die();

                    // Nama Bulan
                        $Date_ = date("Y-m-d", strtotime($Date));
                        $DateIndo = $this->m_master->getIndoBulan($Date_);
                        $DateIndo = explode(' ', $DateIndo);
                        $NmBulan = $DateIndo[1];

                    // terbilang tahun
                         $bilangan = date("Y", strtotime($Date));
                         $data = array(
                               'bilangan' => $bilangan,
                               'auth' => 's3Cr3T-G4N', 
                         ); 
                         $url = url_pas.'rest2/__ajax_terbilang';
                         $token = $this->jwt->encode($data,"UAP)(*");
                         // $this->m_master->apiservertoserver($url,$token);
                         $Tahun = $this->m_master->apiservertoserver($url,$token);          
                    $rs = 'Pada hari ini '.$NameDay[0].', tanggal '.$Tanggal[0].' Bulan '.$NmBulan.', tahun '.$Tahun[0].', kami yang bertanda tangan dibawah ini :';     
                    echo json_encode($rs);
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function approve_po()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('budgeting/m_pr_po');
                    $rs = array('Status' => 1,'Change' => 0,'msg' => '');
                    $po_data = $dataToken['po_data'];
                    $CheckPerubahanData = $this->m_pr_po->CheckPerubahanData_PO_Created($po_data);
                    if ($CheckPerubahanData) {
                        $Code = $dataToken['Code'];
                        $CodeUrl = str_replace('/', '-', $Code);
                        $approval_number = $dataToken['approval_number'];
                        $NIP = $dataToken['NIP'];
                        $G_emp = $this->m_master->SearchNameNIP_Employees_PU_Holding($NIP);
                        $NameFor_NIP = $G_emp[0]['Name'];
                        $action = $dataToken['action'];

                        // get data
                        $G_data = $this->m_master->caribasedprimary('db_purchasing.po_create','Code',$Code);
                        $urlS = ($G_data[0]['TypeCreate'] == 1) ? 'po' : 'spk';

                        $keyJson = $approval_number - 1; // get array index json
                        $JsonStatus = (array)json_decode($G_data[0]['JsonStatus'],true);

                        // get data update to approval
                        $arr_upd = $JsonStatus[$keyJson];

                        if ($arr_upd['NIP'] == $NIP || $arr_upd['Representedby'] == $NIP) {
                            $arr_upd['Status'] = ($action == 'approve') ? 1 : 2;
                            $arr_upd['ApproveAt'] = ($action == 'approve') ? date('Y-m-d H:i:s') : '-';
                            $JsonStatus[$keyJson] = $arr_upd;
                            $datasave = array(
                                'JsonStatus' => json_encode($JsonStatus),
                            );

                            // check all status for update data
                            $boolApprove = true;
                            for ($i=0; $i < count($JsonStatus); $i++) { 
                                $arr = $JsonStatus[$i];
                                $Status = $arr['Status'];
                                if ($Status == 2 || $Status == 0) {
                                    $boolApprove = false;
                                    break;
                                }
                            }

                            if ($boolApprove) {
                                $datasave['Status'] = 2;
                                $datasave['PostingDate'] = date('Y-m-d H:i:s');
                            }
                            else
                            {
                                $boolReject = false;
                                for ($i=0; $i < count($JsonStatus); $i++) { 
                                    $arr = $JsonStatus[$i];
                                    $Status = $arr['Status'];
                                    if ($Status == 2) {
                                        $boolReject = true;
                                        break;
                                    }
                                }

                                if ($boolReject) {
                                    $NoteDel = $dataToken['NoteDel'];
                                    $Notes = $G_data[0]['Notes']."\n".$NoteDel;
                                    $datasave['Status'] = -1;
                                    // $datasave['Notes'] = $Notes;
                                }
                                else
                                {
                                    // Notif to next step approval & User
                                        $NIPApprovalNext = $JsonStatus[($keyJson+1)]['NIP'];
                                        // Send Notif for next approval
                                            // send revisi or not
                                            $RevisiOrNotNotif = $this->m_master->__RevisiOrNotNotif($Code,'db_purchasing.po_circulation_sheet','Code');

                                            $data = array(
                                                'auth' => 's3Cr3T-G4N',
                                                'Logging' => array(
                                                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Approval '.$RevisiOrNotNotif.' PO/SPK : '.$Code,
                                                                'Description' => 'Please approve '.$RevisiOrNotNotif.' PO/SPK '.$Code,
                                                                'URLDirect' => 'global/purchasing/transaction/'.$urlS.'/list/'.$CodeUrl,
                                                                'CreatedBy' => $NIP,
                                                              ),
                                                'To' => array(
                                                          'NIP' => array($NIPApprovalNext),
                                                        ),
                                                'Email' => 'No', 
                                            );

                                            $url = url_pas.'rest2/__send_notif_browser';
                                            $token = $this->jwt->encode($data,"UAP)(*");
                                            $this->m_master->apiservertoserver($url,$token);

                                            // send email is holding or warek keatas
                                                 $this->m_master->send_email_budgeting_holding($NIPApprovalNext,'NA.4',$data['Logging']['URLDirect'],$data['Logging']['Description']);

                                        // Send Notif for user 
                                            $data = array(
                                                'auth' => 's3Cr3T-G4N',
                                                'Logging' => array(
                                                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  PO/SPK '.$Code.' has been Approved',
                                                                'Description' => 'PO/SPK '.$Code.' has been approved by '.$NameFor_NIP,
                                                                'URLDirect' => 'global/purchasing/transaction/'.$urlS.'/list/'.$CodeUrl,
                                                                'CreatedBy' => $NIP,
                                                              ),
                                                'To' => array(
                                                          'NIP' => array($JsonStatus[0]['NIP']),
                                                        ),
                                                'Email' => 'No', 
                                            );

                                            $url = url_pas.'rest2/__send_notif_browser';
                                            $token = $this->jwt->encode($data,"UAP)(*");
                                            $this->m_master->apiservertoserver($url,$token); 
                                }
                            }

                            $this->db->where('Code',$Code);
                            $this->db->update('db_purchasing.po_create',$datasave); 

                            // insert to po_circulation_sheet
                                $Desc = ($arr_upd['Status'] == 1) ? 'Approve' : 'Reject';
                                if (array_key_exists('Status', $datasave)) {
                                    if ($datasave['Status'] == 2) {
                                        $Desc = "All Approve and posting date at : ".$datasave['PostingDate'];

                                        // Notif All Approve to JsonStatus allkey
                                            $arr_to = array();
                                            for ($i=0; $i < count($JsonStatus); $i++) { 
                                                $arr_to[] = $JsonStatus[$i]['NIP'];
                                            }

                                            $data = array(
                                                'auth' => 's3Cr3T-G4N',
                                                'Logging' => array(
                                                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> PO/SPK '.$Code.' has been done',
                                                                'Description' => 'PO/SPK '.$Code.' has been done',
                                                                'URLDirect' => 'global/purchasing/transaction/'.$urlS.'/list/'.$CodeUrl,
                                                                'CreatedBy' => $NIP,
                                                              ),
                                                'To' => array(
                                                          'NIP' => $arr_to,
                                                        ),
                                                'Email' => 'No', 
                                            );

                                            $url = url_pas.'rest2/__send_notif_browser';
                                            $token = $this->jwt->encode($data,"UAP)(*");
                                            $this->m_master->apiservertoserver($url,$token);

                                        // Notif to Purchasing 
                                            $data = array(
                                                'auth' => 's3Cr3T-G4N',
                                                'Logging' => array(
                                                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> PO/SPK '.$Code.' has been done',
                                                                'Description' => 'PO/SPK '.$Code.' has been done',
                                                                'URLDirect' => 'global/purchasing/transaction/'.$urlS.'/list/'.$CodeUrl,
                                                                'CreatedBy' => $NIP,
                                                              ),
                                                'To' => array(
                                                          'Div' => array(4),
                                                        ),
                                                'Email' => 'No', 
                                            );

                                            $url = url_pas.'rest2/__send_notif_browser';
                                            $token = $this->jwt->encode($data,"UAP)(*");
                                            $this->m_master->apiservertoserver($url,$token);

                                        // insert to po_invoice_status
                                            $Code_po_create = $Code;
                                            $G_po_detail = $this->m_master->caribasedprimary('db_purchasing.po_detail','Code',$Code_po_create);
                                            $InvoicePO = 0; // tambah dengan biaya lain-lain
                                            // $AnotherCost = $G_data[0]['AnotherCost'];
                                            // $InvoicePO = $InvoicePO + $AnotherCost;
                                            for ($i=0; $i < count($G_po_detail); $i++) { 
                                               $InvoicePO = $InvoicePO + $G_po_detail[$i]['SubTotal'];
                                            }
                                        // check po_invoice_status already exist or not
                                             $G_po_invoice_status = $this->m_master->caribasedprimary('db_purchasing.po_invoice_status','Code_po_create',$Code_po_create);
                                             if (count($G_po_invoice_status) > 0 ) {
                                                  $InvoicePayPO = $G_po_invoice_status[0]['InvoicePayPO']; 
                                                  $InvoiceLeftPO = $InvoicePO - $InvoicePayPO;

                                                  $dtSave = array(
                                                    'InvoicePO' => $InvoicePO,
                                                    'InvoicePayPO' => $InvoicePayPO,
                                                    'InvoiceLeftPO' => $InvoiceLeftPO,
                                                  );
                                                  $this->db->where('Code_po_create',$Code_po_create);
                                                  $this->db->update('db_purchasing.po_invoice_status',$dtSave); 
                                              }
                                              else{
                                                $InvoicePayPO = 0; 
                                                $InvoiceLeftPO = $InvoicePO;
                                                $dtSave = array(
                                                  'Code_po_create' => $Code_po_create,  
                                                  'InvoicePO' => $InvoicePO,
                                                  'InvoicePayPO' => $InvoicePayPO,
                                                  'InvoiceLeftPO' => $InvoiceLeftPO,
                                                );
                                                $this->db->insert('db_purchasing.po_invoice_status',$dtSave);
                                              }   

                                    }
                                }

                                if ($arr_upd['Status'] == 2) {
                                    if ($dataToken['NoteDel'] != '' || $dataToken['NoteDel'] != null) {
                                        $Desc .= '<br>{'.$dataToken['NoteDel'].'}';
                                    }

                                    // Notif Reject to JsonStatus key 0
                                        // Send Notif for user 
                                            $data = array(
                                                'auth' => 's3Cr3T-G4N',
                                                'Logging' => array(
                                                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> PO/SPK '.$Code.' has been Rejected',
                                                                'Description' => 'PO/SPK '.$Code.' has been Rejected by '.$NameFor_NIP,
                                                                'URLDirect' => 'global/purchasing/transaction/'.$urlS.'/list/'.$CodeUrl,
                                                                'CreatedBy' => $NIP,
                                                              ),
                                                'To' => array(
                                                          'NIP' => array($JsonStatus[0]['NIP']),
                                                        ),
                                                'Email' => 'No', 
                                            );

                                            $url = url_pas.'rest2/__send_notif_browser';
                                            $token = $this->jwt->encode($data,"UAP)(*");
                                            $this->m_master->apiservertoserver($url,$token);
                                }
                                
                                $this->m_pr_po->po_circulation_sheet($Code,$Desc,$NIP);

                        }
                        else
                        {
                            $msg = 'Not Authorize';
                        }


                    }
                    else
                    {
                        $rs['Change'] = 1;
                    }

                    echo json_encode($rs);

                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }



    // ==== CRM ====
    public function crudFormCRM(){

        $data_arr = $this->getInputToken();

        if($data_arr['action']=='insertCRM'){

            $dataInsert = (array) $data_arr['dataInsert'];

            $this->db->insert('db_admission.crm',$dataInsert);

            return print_r(1);

        }

    }

    public function crudCRMPeriode(){

        $data_arr = $this->getInputToken2();

        if($data_arr['action']=='readCRMPeriode'){

            // $data = $this->db->order_by('Year','DESC')->get('db_admission.crm_period')->result_array();
            $sql = 'select * from db_admission.crm_period order by Status Desc,Year Desc';
            $data=$this->db->query($sql, array())->result_array();

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='insertCRMPeriode'){
            $Year = $data_arr['Year'];

            // Cek apakah data ada jika ada maka tidak insert
            $dataCk = $this->db->get_where('db_admission.crm_period',array(
                'Year' => $Year
            ))->result_array();

            $result = array(
              'Status' => '0'
            );
            if(count($dataCk)<=0){
                $this->db->insert('db_admission.crm_period',array(
                    'Year' => $Year,
                    'Name' => $data_arr['Name']
                ));

                $insert_id = $this->db->insert_id();

                $result = array(
                    'Status' => '1'
                );

                $this->m_master->__fillTA_Capacity($insert_id);

            }

            return print_r(json_encode($result));
        }
        else if($data_arr['action']=='publishCRMPeriode'){
            $ID = $data_arr['ID'];

            $this->db->set('Status', '0');
            $this->db->update('db_admission.crm_period');
            $this->db->reset_query();

            $this->db->set('Status', '1');
            $this->db->where('ID', $ID);
            $this->db->update('db_admission.crm_period');

            return print_r(1);
        }
        else if($data_arr['action']=='removeCRMPeriode'){
            $ID = $data_arr['ID'];

            // Cek penggunaan ID
            $dataCk_team = $this->db->limit(1)->get_where('db_admission.crm_team',array(
                'PeriodID' => $ID
            ))->result_array();

            $dataCk_crm = $this->db->limit(1)->get_where('db_admission.crm',array(
                'PeriodID' => $ID
            ))->result_array();

            if(count($dataCk_team)>0 || count($dataCk_crm)>0){
                $result = array(
                    'Status' => '0'
                );
            } else {
                $this->db->where('ID', $ID);
                $this->db->delete('db_admission.crm_period');
                $result = array(
                    'Status' => '1'
                );
            }

            return print_r(json_encode($result));

        }
        else if($data_arr['action']=='activeCRMPeriode'){
            $data = $this->db->limit(1)->get_where('db_admission.crm_period',
                array(
                    'Status' => '1'
                ))->result_array();
            return print_r(json_encode($data));
        }
        else if($data_arr['action']=='getAllTeamListCRMPeriode'){
            $PeriodID = $data_arr['PeriodID'];
            $NIP = $data_arr['NIP'];

            // Get data Team
            $dataTeam = $this->db->query('SELECT ct.* FROM db_admission.crm_team ct 
                                            LEFT JOIN db_admission.crm_team_member ctm ON (ct.ID = ctm.CRMTeamID) 
                                            WHERE ct.PeriodID = "'.$PeriodID.'" AND  ctm.NIP = "'.$NIP.'" LIMIT 1')->result_array();

            $result = [];
            if(count($dataTeam)>0){
                $d = $dataTeam[0];
                $data = $this->db->query('SELECT c.*, IF(LENGTH(em.Name)>16, SUBSTRING(em.Name, 1, 15), em.Name) AS SalesName,  
                                                cs.Description AS StatusDesc, csl.ClassMobile AS StatusCalss FROM db_admission.crm c
                                                LEFT JOIN db_employees.employees em ON (em.NIP = c.NIP)
                                                LEFT JOIN db_admission.crm_status cs ON (cs.ID = c.Status)
                                                LEFT JOIN db_admission.crm_status_label csl ON (csl.ID = cs.LabelID)
                                                WHERE  c.PeriodID = "'.$PeriodID.'" 
                                                AND c.CRMTeamID = "'.$d['ID'].'" ORDER BY c.Name ASC ')->result_array();

                if(count($data)>0){
                    $res = array(
                        'dataTeam' => $dataTeam,
                        'dataCRM' => $data
                    );

                    array_push($result,$res);
                }

            }



            return print_r(json_encode($result));

        }
    }

    public function crudCRMTeam(){
        $data_arr = $this->getInputToken2();

        if($data_arr['action']=='insertCRMTeam'){

            $team = (array) $data_arr['team'];
            $member = (array) $data_arr['member'];

            $this->db->insert('db_admission.crm_team',$team);
            $CRMTeamID = $this->db->insert_id();

            $dataIns = array(
                'CRMTeamID' => $CRMTeamID,
                'NIP' => $data_arr['Coordinator'],
                'Status' => '1'
            );

            $this->db->insert('db_admission.crm_team_member',$dataIns);

            if(count($member)>0){
                for($i=0;$i<count($member);$i++){

                    if($member[$i]!=$data_arr['Coordinator']){
                        $dataIns = array(
                            'CRMTeamID' => $CRMTeamID,
                            'NIP' => $member[$i]
                        );

                        $this->db->insert('db_admission.crm_team_member',$dataIns);
                    }



                }
            }

            return print_r(1);

        }
        else if($data_arr['action']=='updateCRMTeam'){

            $ID = $data_arr['ID'];

            $team = (array) $data_arr['team'];
            $member = (array) $data_arr['member'];

            $this->db->where('ID', $ID);
            $this->db->update('db_admission.crm_team',$team);
            $this->db->reset_query();

            $this->db->where('CRMTeamID', $ID);
            $this->db->delete('db_admission.crm_team_member');
            $this->db->reset_query();



            $dataIns = array(
                'CRMTeamID' => $ID,
                'NIP' => $data_arr['Coordinator'],
                'Status' => '1'
            );

            $this->db->insert('db_admission.crm_team_member',$dataIns);

            if(count($member)>0){


                for($i=0;$i<count($member);$i++){

                    if($member[$i]!=$data_arr['Coordinator']){
                        $dataIns = array(
                            'CRMTeamID' => $ID,
                            'NIP' => $member[$i]
                        );

                        $this->db->insert('db_admission.crm_team_member',$dataIns);
                    }
                }
            }

            return print_r(1);

        }
        else if($data_arr['action']=='readCRMTeam'){
            $PeriodID = $data_arr['PeriodID'];

            // Apakah ada NIP, jika ada nip artinya hanya untuk satu team
            if(isset($data_arr['NIP'])){
                $NIP = $data_arr['NIP'];



            } else {
                $data = $this->db->query('SELECT ct.*, cp.Year, cp.Status FROM db_admission.crm_team ct 
                                                LEFT JOIN db_admission.crm_period cp ON (cp.ID = ct.PeriodID)
                                                WHERE ct.PeriodID = "'.$PeriodID.'" ')->result_array();

                if(count($data)>0){
                    for($i=0;$i<count($data);$i++){

                        $data[$i]['Member'] = $this->db->query('SELECT ctm.*, em.Name AS MemberName FROM db_admission.crm_team_member ctm 
                                                                      LEFT JOIN db_employees.employees em 
                                                                      ON (em.NIP = ctm.NIP) 
                                                                      WHERE ctm.CRMTeamID = "'.$data[$i]['ID'].'" 
                                                                      ORDER BY ctm.Status DESC ')->result_array();

                    }
                }
            }



            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='removeCRMTeam'){

            $CRMTeamID = $data_arr['ID'];

            $dataCk = $this->db->get_where('db_admission.crm',array(
                'CRMTeamID' => $CRMTeamID
            ))->result_array();

            $result = array('Status' => '0');

            if(count($dataCk)<=0){
                // Remove Team
                $this->db->where('CRMTeamID', $CRMTeamID);
                $this->db->delete('db_admission.crm_team_member');
                $this->db->reset_query();

                $this->db->where('ID', $CRMTeamID);
                $this->db->delete('db_admission.crm_team');

                $result = array('Status' => '1');
            }

            return print_r(json_encode($result));


        }
    }

    public function crudMarketingActivity(){
        $data_arr = $this->getInputToken2();

        if($data_arr['action']=='ins_MA'){

            $ID = $data_arr['ID'];

            $dataForm = (array) $data_arr['dataForm'];

//            print_r($dataForm);exit;

            $dateEvent = $dataForm['Start'];
            $Month = explode('-',$dateEvent)[1];
            $Year = explode('-',$dateEvent)[0];
            $dataForm['Month'] = $Month;
            $dataForm['Year'] = $Year;

            if($ID!='' && $ID!=null){
                // Update
                $this->db->where('ID', $ID);
                $this->db->update('db_admission.marketing_activity', $dataForm);
            } else {
                // Insert
                $this->db->insert('db_admission.marketing_activity', $dataForm);
                $ID = $this->db->insert_id();
            }


            // Participant
            $this->db->where('MAID', $ID);
            $this->db->delete('db_admission.marketing_activity_participants');
            $this->db->reset_query();

            $Participants = (array) $data_arr['Participants'];
            if(count($Participants)>0){
                for($i=0;$i<count($Participants);$i++){
                    $arrIns = array(
                        'MAID' => $ID,
                        'NIP' => $Participants[$i]
                    );
                    $this->db->insert('db_admission.marketing_activity_participants', $arrIns);
                }
            }

            return print_r(1);

        }
        else if($data_arr['action']=='readMonthYear_MA'){
            $dataMonth = $this->db->query('SELECT ma.Month FROM db_admission.marketing_activity ma 
                                                            GROUP BY ma.Month ORDER BY ma.Month ASC')->result_array();

            $dataYear = $this->db->query('SELECT ma.Year FROM db_admission.marketing_activity ma 
                                                            GROUP BY ma.Year ORDER BY ma.Year DESC')->result_array();

            $result = array('Month' => $dataMonth,'Year' => $dataYear);

            return print_r(json_encode($result));
        }
        else if($data_arr['action']=='filter_MA'){

            $Month = $data_arr['Month'];
            $Year = $data_arr['Year'];

            $data = $this->db->query('SELECT * FROM db_admission.marketing_activity ma 
                                                  WHERE ma.Month = "'.$Month.'" 
                                                  AND ma.Year = "'.$Year.'"  ')->result_array();

            if(count($data)>0){
                for ($i=0;$i<count($data);$i++){
                    $data[$i]['Participants'] = $this->db->query('SELECT map.*, em.Name FROM db_admission.marketing_activity_participants map
                                                                    LEFT JOIN db_employees.employees em ON (em.NIP = map.NIP)
                                                                    WHERE map.MAID = "'.$data[$i]['ID'].'" ')->result_array();
                }
            }

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='remove_MA'){
            $ID = $data_arr['ID'];

            $dataCheck = $this->db->get_where('db_admission.crm',array(
                'MAID' => $ID
            ))->result_array();

            $result = array('Status'=>'0');
            if(count($dataCheck)<=0){
                // Remove
                $this->db->where('MAID', $ID);
                $this->db->delete('db_admission.marketing_activity_participants');
                $this->db->reset_query();

                $this->db->where('ID', $ID);
                $this->db->delete('db_admission.marketing_activity');

                $result = array('Status'=>'1');
            }

            return print_r(json_encode($result));

        }

        else if($data_arr['action']=='readActiveNow_MA'){

            $DateNow = $this->m_rest->getDateNow();

            $data = $this->db->query('SELECT * FROM db_admission.marketing_activity ma WHERE ma.Start <= "'.$DateNow.'" AND ma.End >= "'.$DateNow.'" ')->result_array();

            return print_r(json_encode($data));

        }

    }

    public function crudContact(){
        $data_arr = $this->getInputToken2();

        if($data_arr['action']=='insertContat'){

            $dataForm = (array) $data_arr['dataForm'];
            $this->db->insert('db_admission.contact',$dataForm);
            return print_r(json_encode(array('Status'=> '1')));

        }
        else if($data_arr['action']=='updateContat'){
            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];
            $this->db->where('ID', $ID);
            $this->db->update('db_admission.contact',$dataForm);
            return print_r(json_encode(array('Status'=> '1')));

        }
        else if($data_arr['action']=='searchContact'){
            $key = $data_arr['key'];

            $query = 'SELECT c.*, s.SchoolName, s.CityID, em.Name AS CreatedBy_Name, s.CityName FROM db_admission.contact c  
                                              LEFT JOIN db_admission.school s ON (s.ID = c.SchoolID)
                                              LEFT JOIN db_employees.employees em ON (em.NIP = c.CreatedBy)
                                              ';

            if($key!=''){

                $des = explode('schid:',$key);

                if(count($des)>1){
                    $data = $this->db->query($query.' WHERE s.ID = "'.$des[1].'" ORDER BY c.Name ASC LIMIT 20 ')->result_array();
                } else {
                    $data = $this->db->query($query.' WHERE c.Name LIKE "%'.$key.'%" 
                                              OR c.Phone LIKE "%'.$key.'%"
                                              OR c.Email LIKE "%'.$key.'%"
                                              OR s.SchoolName LIKE "%'.$key.'%" ORDER BY c.Name ASC LIMIT 20 ')->result_array();
                }

            } else {
                $data = $this->db->query($query.' ORDER BY c.Name ASC LIMIT 7 ')->result_array();
            }

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='removeContact'){
            $ID = $data_arr['ID'];
            $this->db->where('ID', $ID);
            $this->db->delete('db_admission.contact');
            return print_r(1);
        }
        else if($data_arr['action']=='listAllContact'){

            $Key = $data_arr['Key'];

            if($Key!=''){

                $data = $this->db->query('SELECT cnt.*, r.RegionName, s.SchoolName FROM db_admission.contact cnt
                                                          LEFT JOIN db_admission.school s ON (s.ID = cnt.SchoolID)
                                                          LEFT JOIN db_admission.region r ON (s.CityID = r.RegionID)
                                                          WHERE cnt.Name LIKE "%'.$Key.'%" OR cnt.Phone LIKE "%'.$Key.'%" 
                                                          OR r.RegionName LIKE "%'.$Key.'%" OR s.SchoolName LIKE "%'.$Key.'%"
                                                          ORDER BY cnt.Name ASC ')->result_array();

            } else {
                $data = $this->db->query('SELECT cnt.*, r.RegionName, s.SchoolName FROM db_admission.contact cnt
                                                          LEFT JOIN db_admission.school s ON (s.ID = cnt.SchoolID)
                                                          LEFT JOIN db_admission.region r ON (s.CityID = r.RegionID)
                                                          ORDER BY cnt.Name ASC ')->result_array();
            }

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='viewDetailContact'){

            $ID = $data_arr['ID'];
            $data = $this->db->query('SELECT cnt.*, r.RegionName, s.SchoolName FROM db_admission.contact cnt
                                                          LEFT JOIN db_admission.school s ON (s.ID = cnt.SchoolID)
                                                          LEFT JOIN db_admission.region r ON (s.CityID = r.RegionID)
                                                          WHERE cnt.ID = "'.$ID.'" ')->result_array();

            return print_r(json_encode($data));


        }
    }

    public function crudProspectiveStudents(){

        $data_arr = $this->getInputToken2();

        if($data_arr['action']=='insert_PS'){

            $dataForm = (array) $data_arr['dataForm'];

            //cek email
            $dataEmail = $dataForm['Email'];
            $dataEm = $this->db->query('SELECT * FROM db_admission.crm WHERE Email LIKE "'.$dataEmail.'" ')->result_array();

            $result = array(
                'Status' => 0,
                'Message' => 'Email already exists'
            );
            if(count($dataEm)<=0){
                $this->db->insert('db_admission.crm',$dataForm);
                $result = array(
                    'Status' => 1,
                    'Message' => 'Data saved'
                );
            }



            return print_r(json_encode($result));

        }
        else if($data_arr['action']=='update_PS'){

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            $this->db->where('ID', $ID);
            $this->db->update('db_admission.crm',$dataForm);

            $result = array(
                'Status' => 1,
                'Message' => 'Data saved'
            );

            return print_r(json_encode($result));

        }
        else if($data_arr['action']=='read_PS'){

            $PeriodID = $data_arr['PeriodID'];
            $data = $this->db->query('SELECT c.*, em.Name AS NameProspect_by FROM db_admission.crm c 
                                                LEFT JOIN db_employees.employees em ON (em.NIP = c.NIP)
                                                WHERE c.PeriodID = "'.$PeriodID.'" ')->result_array();

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='read2Full_PS'){
            $ID = $data_arr['ID'];
            $data = $this->db->query('SELECT c.*, em.Name AS SalesName, s.CityID, s.SchoolName, rms.SchoolMajor,  
                                                cs.Description AS StatusDesc, csl.Class AS StatusClass, 
                                                csl.ClassMobile AS StatusClassMobile 
                                                FROM db_admission.crm c
                                                LEFT JOIN db_employees.employees em ON (em.NIP = c.NIP)
                                                LEFT JOIN db_admission.school s ON (s.ID = c.SchoolID)
                                                LEFT JOIN db_admission.crm_status cs ON (cs.ID = c.Status)
                                                LEFT JOIN db_admission.crm_status_label csl ON (csl.ID = cs.LabelID)
                                                LEFT JOIN db_admission.register_major_school rms ON (rms.ID = c.PathwayID)
                                                WHERE c.ID = "'.$ID.'" ')->result_array();

            return print_r(json_encode($data));
        }
        else if($data_arr['action']=='insertStatus_PS'){

            $dataForm = (array) $data_arr['dataForm'];
            $this->db->insert('db_admission.crm_status', $dataForm);
            return print_r(1);

        }
        else if($data_arr['action']=='updateStatus_PS'){

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            $this->db->where('ID', $ID);
            $this->db->update('db_admission.crm_status',$dataForm);
            return print_r(1);
        }
        else if($data_arr['action']=='removeCRM_PS'){



        }
        else if($data_arr['action']=='status_PS'){

            $data = $this->db->query('SELECT cs.*,csl.Name AS LabelName, csl.Class AS LabelClass FROM db_admission.crm_status cs 
                                                LEFT JOIN db_admission.crm_status_label csl ON (csl.ID = cs.LabelID)')->result_array();

            return print_r(json_encode($data));

        }
        else if($data_arr['action']=='removeStatus_PS'){

            $ID = $data_arr['ID'];
            // Cek apakah IDnya di pakai
            $dataCheck = $this->db->get_where('db_admission.crm',array('Status'=>$ID))->result_array();

            if(count($dataCheck)>0){
                $Status = '0';
            } else {
                $this->db->where('ID', $ID);
                $this->db->delete('db_admission.crm_status');
                $Status = '1';
            }

            return print_r(json_encode(array('Status' => $Status)));



        }
        else if($data_arr['action']=='insertCommentF_SP'){

            $dataForm = (array) $data_arr['dataForm'];
            $this->db->insert('db_admission.crm_followup', $dataForm);
            return print_r(1);

        }
        else if($data_arr['action']=='updateCommentF_SP'){

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            $this->db->where('ID', $ID);
            $this->db->update('db_admission.crm_followup',$dataForm);
            return print_r(1);

        }
        else if($data_arr['action']=='viewFollowUp_SP'){
            $CRMID = $data_arr['CRMID'];
            $data = $this->db->get_where('db_admission.crm_followup',array('CRMID' => $CRMID))->result_array();

            return print_r(json_encode($data));
        }

    }

    public function getPathway(){
        $data = $this->db->get_where('db_admission.register_major_school',array(
            'Active' => 1
        ))->result_array();

        return print_r(json_encode($data));

    }

    // ==== PENUTUP CRM ====


    public function show_info_pr()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $rs = array();
                $PRCode = $dataToken['PRCode'];
                $sql = 'select a.Desc,a.Date,b.NIP,b.Name from db_budgeting.pr_circulation_sheet as a 
                        join db_employees.employees as b on a.By = b.NIP
                        where a.PRCode = ?
                        ';
                $query=$this->db->query($sql, array($PRCode))->result_array();
                $rs['PR_Process'] = $query;

                $rs['PR_Status_Summary'] = $this->m_master->caribasedprimary('db_purchasing.pr_status','PRCode',$PRCode);
                $sql = 'select a.ID,a.ID_m_catalog,b.Item,c.ID as ID_pre_po_detail,d.Code
                        from db_budgeting.pr_detail as a join db_purchasing.m_catalog as b on a.ID_m_catalog = b.ID
                        left join db_purchasing.pre_po_detail as c on a.ID = c.ID_pr_detail
                        left join db_purchasing.po_detail as d on c.ID = d.ID_pre_po_detail
                        where a.PRCode = ?
                        '; 
                $query=$this->db->query($sql, array($PRCode))->result_array();
                $rs['PR_link_PO'] = $query;
                echo json_encode($rs);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function show_info_po()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $rs = array();
                $Code = $dataToken['Code'];
                $sql = 'select a.Desc,a.Date,b.NIP,b.Name from db_purchasing.po_circulation_sheet as a 
                        join db_employees.employees as b on a.By = b.NIP
                        where a.Code = ?
                        ';
                $query=$this->db->query($sql, array($Code))->result_array();
                $rs['po_circulation_sheet'] = $query;
                $po_invoice_status = $this->m_master->caribasedprimary('db_purchasing.po_invoice_status','Code_po_create',$Code);
                $rs['po_invoice_status'] = $po_invoice_status;        
                echo json_encode($rs);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function show_info_payment()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $rs = array();
                $ID_payment = $dataToken['ID_payment'];
                $whereField = 'where a.ID_payment = ?';
                $sql = 'select a.Desc,a.Date,b.NIP,b.Name from db_payment.payment_circulation_sheet as a 
                        join db_employees.employees as b on a.By = b.NIP
                       '.$whereField.' 
                        ';
                $query=$this->db->query($sql, array($ID_payment))->result_array();
                $rs['payment_circulation_sheet'] = $query;
                echo json_encode($rs);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function reject_pr_from_another()
    {
        $msg = '';
        $Reload = 0;
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $BoolReload = false;
                $this->load->model('budgeting/m_budgeting');
                $this->load->model('budgeting/m_pr_po');
                $PRCode = $dataToken['PRCode'];
                $NIP = $dataToken['NIP'];

                // check PR Item belum di proses pada PO
                    $Bool = $this->m_pr_po->check_pr_item_In_po($PRCode);
                    if ($Bool) {
                        $datasave['Status'] = 3;
                        $this->db->where('PRCode',$PRCode);
                        $this->db->update('db_budgeting.pr_create',$datasave);
                        $G_data = $this->m_master->caribasedprimary('db_budgeting.pr_create','PRCode',$PRCode);
                        $G_emp = $this->m_master->SearchNameNIP_Employees_PU_Holding($NIP);
                        $NameFor_NIP = $G_emp[0]['Name'];
                        $JsonStatus = (array)json_decode($G_data[0]['JsonStatus'],true);

                        // Send Notif for user 
                            $data = array(
                                'auth' => 's3Cr3T-G4N',
                                'Logging' => array(
                                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>'.$PRCode.' has been Rejected by '.$NameFor_NIP,
                                                'Description' => 'PR '.$PRCode.' has been Rejected by '.$NameFor_NIP,
                                                'URLDirect' => 'budgeting_pr',
                                                'CreatedBy' => $NIP,
                                              ),
                                'To' => array(
                                          'NIP' => array($JsonStatus[0]['NIP']),
                                        ),
                                'Email' => 'No', 
                            );

                            $url = url_pas.'rest2/__send_notif_browser';
                            $token = $this->jwt->encode($data,"UAP)(*");
                            $this->m_master->apiservertoserver($url,$token);

                        $Desc = 'Reject by '.$NameFor_NIP.'<br>{'.$dataToken['NoteDel'].'}';    
                        $this->m_pr_po->pr_circulation_sheet($PRCode,$Desc,$NIP);
                    }
                    else
                    {
                        $msg = 'PR is being processed, cant action reject';
                    }

                    echo json_encode($msg);
                
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function cancel_pr_item_from_another()
    {
        $rs = array ('msg' => '','reload' => 0);
        $Reload = 0;
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $BoolReload = false;
                $this->load->model('budgeting/m_budgeting');
                $this->load->model('budgeting/m_pr_po');
                $ID_pr_detail = $dataToken['ID_pr_detail'];
                $NIP = $dataToken['NIP'];
                // get PRCode first
                $G_pr_detail = $this->m_master->caribasedprimary('db_budgeting.pr_detail','ID',$ID_pr_detail);
                $PRCode = $G_pr_detail[0]['PRCode'];

                $G_data = $this->m_master->caribasedprimary('db_budgeting.pr_create','PRCode',$PRCode);
                $G_emp = $this->m_master->SearchNameNIP_Employees_PU_Holding($NIP);
                $NameFor_NIP = $G_emp[0]['Name'];
                $JsonStatus = (array)json_decode($G_data[0]['JsonStatus'],true);

                // yang boleh di cancel adalah item pr yang dalam status po_nya adalah cancel dan item pr yang belum di proses po
                // jika item pr di cancel yang belum masuk proses po maka status pr akan otomatis reject
                    $__G_po_detail = $this->m_pr_po->check_po_status_by_item_pr_detail($ID_pr_detail);
                    if ($__G_po_detail == 4) {
                        $Bool = $this->m_pr_po->check_pr_item_In_po($PRCode); // auto reject if true
                        if ($Bool) {
                            $datasave['Status'] = 3;
                            $this->db->where('PRCode',$PRCode);
                            $this->db->update('db_budgeting.pr_create',$datasave);

                            // Send Notif for user 
                                $data = array(
                                    'auth' => 's3Cr3T-G4N',
                                    'Logging' => array(
                                                    'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>'.$PRCode.' has been Rejected by '.$NameFor_NIP,
                                                    'Description' => 'PR '.$PRCode.' has been Rejected by '.$NameFor_NIP,
                                                    'URLDirect' => 'budgeting_pr',
                                                    'CreatedBy' => $NIP,
                                                  ),
                                    'To' => array(
                                              'NIP' => array($JsonStatus[0]['NIP']),
                                            ),
                                    'Email' => 'No', 
                                );

                                $url = url_pas.'rest2/__send_notif_browser';
                                $token = $this->jwt->encode($data,"UAP)(*");
                                $this->m_master->apiservertoserver($url,$token);

                                $rs['reload'] = 1;

                        }

                        // cek status first
                            $Status = $G_pr_detail[0]['Status'];
                            if ($Status == 1) {
                                $arr = array($ID_pr_detail);
                                $this->m_pr_po->ReturnAllBudgetFromID_pr_detail($arr);

                                $ID_m_catalog = $G_pr_detail[0]['ID_m_catalog'];
                                $G_m_catalog = $this->m_master->caribasedprimary('db_purchasing.m_catalog','ID',$ID_m_catalog);
                                
                                $Desc = 'Item '.$G_m_catalog[0]['Item'].' cancel by '.$NameFor_NIP.'<br>{'.$dataToken['NoteDel'].'}';    
                                $this->m_pr_po->pr_circulation_sheet($PRCode,$Desc,$NIP);

                                // update ke pr_status dan pr_status_detail
                                    $this->m_pr_po->__cancel_item_by_id_pr_detail($ID_pr_detail,$PRCode);
                            }
                            else
                            {
                                $rs['msg'] = 'Item has canceled,cant action';
                            }

                    }
                    else
                    {
                        $G_pr_status_detail= $this->m_master->caribasedprimary('db_purchasing.pr_status_detail','ID_pr_detail',$ID_pr_detail); // auto reject if true
                        $Bool = ($G_pr_status_detail[0]['Status'] == 0) ? true : false;
                        if ($Bool) {
                            $Bool2 = $this->m_pr_po->check_pr_item_In_po($PRCode); // auto reject if true
                            if ($Bool2) {
                                $datasave['Status'] = 3;
                                $this->db->where('PRCode',$PRCode);
                                $this->db->update('db_budgeting.pr_create',$datasave);

                                // Send Notif for user 
                                    $data = array(
                                        'auth' => 's3Cr3T-G4N',
                                        'Logging' => array(
                                                        'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>'.$PRCode.' has been Rejected by '.$NameFor_NIP,
                                                        'Description' => 'PR '.$PRCode.' has been Rejected by '.$NameFor_NIP,
                                                        'URLDirect' => 'budgeting_pr',
                                                        'CreatedBy' => $NIP,
                                                      ),
                                        'To' => array(
                                                  'NIP' => array($JsonStatus[0]['NIP']),
                                                ),
                                        'Email' => 'No', 
                                    );

                                    $url = url_pas.'rest2/__send_notif_browser';
                                    $token = $this->jwt->encode($data,"UAP)(*");
                                    $this->m_master->apiservertoserver($url,$token);

                                    $rs['reload'] = 1;
                            }
                            
                           // cek status first
                               $Status = $G_pr_detail[0]['Status'];
                               if ($Status == 1) {
                                   $arr = array($ID_pr_detail);
                                   $this->m_pr_po->ReturnAllBudgetFromID_pr_detail($arr);

                                   $ID_m_catalog = $G_pr_detail[0]['ID_m_catalog'];
                                   $G_m_catalog = $this->m_master->caribasedprimary('db_purchasing.m_catalog','ID',$ID_m_catalog);
                                   
                                   $Desc = 'Item '.$G_m_catalog[0]['Item'].' cancel by '.$NameFor_NIP.'<br>{'.$dataToken['NoteDel'].'}';    
                                   $this->m_pr_po->pr_circulation_sheet($PRCode,$Desc,$NIP);

                                   // update ke pr_status dan pr_status_detail
                                       $this->m_pr_po->__cancel_item_by_id_pr_detail($ID_pr_detail,$PRCode);
                               }
                               else
                               {
                                   $rs['msg'] = 'Item has canceled,cant action';
                               }     

                        }
                        else
                        {
                            $rs['msg'] = 'Item is being processed';
                        }
                    }

                    echo json_encode($rs);
                
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function getCategoryCatalog($Active = null)
    {
        $sql = 'select * from db_purchasing.m_category_catalog';
        if ($Active == 'All') {
            $sql.= '';
        }
        elseif ($Active == 1 || $Active == 0) {
            $sql.= ' where Active = '.$Active;
        }

        $query=$this->db->query($sql, array())->result_array();
        echo json_encode($query);
    }

    public function spb_for_po()
    {
        $rs = array ('msg' => '','dt' => array());
        $Reload = 0;
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $Code = $dataToken['Code'];
                $sql = 'select * from db_purchasing.spb_created where Code = ? and Status = 2 order by ID asc';
                $query=$this->db->query($sql, array($Code))->result_array();
                $rs['dt'] = $query; 
                // check SPB on process or not
                $sql = 'select * from db_purchasing.spb_created where Code = ? and Status in(0,1-1)';
                $query=$this->db->query($sql, array($Code))->result_array();
                if (count($query) > 0) {
                   $rs['msg'] = 'SPB pada po ini lagi on process, tidak bisa buat SPB'; 
                }            
                echo json_encode($rs);
                
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function Get_data_spb_grpo()
    {
        $rs = array ('msg' => '','dt' => array(),'change'=>0);
        try {
            $this->load->model('budgeting/m_spb');
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $Code = $dataToken['Code'];
                $dt = $this->m_spb->get_spb_gr_by_po($Code);
                echo json_encode($dt);

            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function Get_data_payment_user()
    {
        $rs = array ('msg' => '','dt' => array(),'change'=>0);
        try {
            $this->load->model('budgeting/m_global');
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $ID_payment = $dataToken['ID_payment'];
                $dt = $this->m_global->Get_data_payment_user($ID_payment);
                echo json_encode($dt);

            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        //catch exception
        catch(Exception $e) {
          // handling orang iseng
          echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function get_data_spb()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('budgeting/m_pr_po');
                //check action
               $fieldaction = ', spb.ID_payment,spb.Status as StatusSPB,spb.Departement as DepartementSPB,spb.JsonStatus as JsonStatus3,spb.Code as CodeSPB,spb.CreatedBy as SPBCreatedBy,e_spb.Name as SPBNameCreatedBy,if(spb.Status = 0,"Draft",if(spb.Status = 1,"Issued & Approval Process",if(spb.Status =  2,"Approval Done",if(spb.Status = -1,"Reject","Cancel") ) )) as StatusNameSPB,t_spb_de.NameDepartement as NameDepartementSPB,spb.CreatedAt as CreatedAtSPB,spb.ID_template_pay ';
               // $joinaction = ' right join db_purchasing.spb_created as spb on spb.Code_po_create = a.Code
               $joinaction = ' right join (select a.ID as ID_payment_,a.Type,a.Code,a.Code_po_create,a.Departement,a.UploadIOM,a.NoIOM,a.JsonStatus,a.Notes,a.Status,a.Print_Approve,a.CreatedBy,a.CreatedAt,a.LastUpdatedBy,a.LastUpdatedAt,a.ID_template as ID_template_pay,b.* from db_payment.payment as a join db_payment.spb as b on a.ID = b.ID_payment where a.Type = "Spb")
                                as spb on spb.Code_po_create = a.Code
                               left join db_employees.employees as e_spb on e_spb.NIP = spb.CreatedBy
                               join (
                               select * from (
                               select CONCAT("AC.",ID) as ID, NameEng as NameDepartement from db_academic.program_study where Status = 1
                               UNION
                               select CONCAT("NA.",ID) as ID, Division as NameDepartement from db_employees.division where StatusDiv = 1
                               UNION
                               select CONCAT("FT.",ID) as ID, NameEng as NameDepartement from db_academic.faculty where StBudgeting = 1
                               ) aa
                               ) as t_spb_de on spb.Departement = t_spb_de.ID
                            ';
               $whereaction = ' and StatusSPB != 0';

                // get Department
                $IDDepartementPUBudget = $dataToken['IDDepartementPUBudget'];
                $WhereFiltering = '';
                if ($IDDepartementPUBudget != 'NA.9') {
                    $NIP = $dataToken['sessionNIP'];
                    $WhereFiltering = ' and (Departement = "'.$IDDepartementPUBudget.'" or JsonStatus2 REGEXP \'"NIP":"[[:<:]]'.$NIP.'[[:>:]]"\' or  JsonStatus REGEXP \'"NIP":"[[:<:]]'.$NIP.'[[:>:]]"\' or  DepartementSPB = "'.$IDDepartementPUBudget.'" or JsonStatus3 REGEXP \'"NIP":"[[:<:]]'.$NIP.'[[:>:]]"\' ) ';
                }

                if (array_key_exists('Years', $dataToken)) {
                    $WhereFiltering .= ' and (Year = "'.$dataToken['Years'].'" or YEAR(CreatedAtSPB) = "'.$dataToken['Years'].'" ) ';
                }

                if (array_key_exists('Month', $dataToken)) {
                    if ($dataToken['Month'] != 'all') {
                        $WhereFiltering .= ' and MONTH(CreatedAtSPB) = '.(int)$dataToken['Month'];
                    }
                }
                // print_r($dataToken);die();
                if (array_key_exists('SelectTemplate', $dataToken)) {
                     if ($dataToken['SelectTemplate'] != '%' && $dataToken['SelectTemplate'] != '') {
                        $WhereFiltering .= ' and (ID_template_PR = '.$dataToken['SelectTemplate'].' or ID_template_pay = '.$dataToken['SelectTemplate'].' )';
                     }
                 }
                 
                $requestData = $_REQUEST;
                $StatusQuery = '';
                $sqltotalData = 'select count(*) as total  from (
                            select if(a.TypeCreate = 1,"PO","SPK") as TypeCode,a.Code,a.ID_pre_po_supplier,b.CodeSupplier,
                                c.NamaSupplier,c.PICName as PICSupplier,c.Alamat as AlamatSupplier,
                                a.JsonStatus,
                                if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = -1,"Reject","Cancel") ) )) as StatusName,a.CreatedBy,d.Name as NameCreateBy,a.CreatedAt,a.PostingDate,g.PRCode,h.JsonStatus as JsonStatus2,h.Year,h.ID_template as ID_template_PR,h.Departement,a.Status'.$fieldaction.'
                            from db_purchasing.po_create as a
                            left join db_purchasing.pre_po_supplier as b on a.ID_pre_po_supplier = b.ID
                            left join db_purchasing.m_supplier as c on b.CodeSupplier = c.CodeSupplier
                            left join db_employees.employees as d on a.CreatedBy = d.NIP
                            left join db_purchasing.po_detail as e on a.Code = e.Code
                            left join db_purchasing.pre_po_detail as f on e.ID_pre_po_detail = f.ID
                            left join db_budgeting.pr_detail as g on f.ID_pr_detail = g.ID
                            left join db_budgeting.pr_create as h on h.PRCode = g.PRCode
                            '.$joinaction.'

                        )aa
                       ';

                $sqltotalData.= ' where (Code LIKE "%'.$requestData['search']['value'].'%" or TypeCode LIKE "'.$requestData['search']['value'].'%" or NamaSupplier LIKE "%'.$requestData['search']['value'].'%" or CodeSupplier LIKE "'.$requestData['search']['value'].'%"
                      or SPBNameCreatedBy LIKE "'.$requestData['search']['value'].'%" or SPBCreatedBy LIKE "'.$requestData['search']['value'].'%" 
                      or PRCode LIKE "'.$requestData['search']['value'].'%"  or CodeSPB LIKE "'.$requestData['search']['value'].'%"
                    ) '.$StatusQuery.$WhereFiltering.$whereaction ;
                // print_r($sqltotalData);die();    
                $querytotalData = $this->db->query($sqltotalData)->result_array();
                $totalData = $querytotalData[0]['total'];

                $sql = 'select * from (
                            select a.ID as ID_po_create,if(a.TypeCreate = 1,"PO","SPK") as TypeCode,a.Code,a.ID_pre_po_supplier,b.CodeSupplier,
                                c.NamaSupplier,c.PICName as PICSupplier,c.Alamat as AlamatSupplier,
                                a.JsonStatus,
                                if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = -1,"Reject","Cancel") ) )) as StatusName,a.CreatedBy,d.Name as NameCreateBy,a.CreatedAt,a.PostingDate,g.PRCode,h.JsonStatus as JsonStatus2,h.Year,h.ID_template as ID_template_PR,h.Departement,a.Status'.$fieldaction.'
                            from db_purchasing.po_create as a
                            left join db_purchasing.pre_po_supplier as b on a.ID_pre_po_supplier = b.ID
                            left join db_purchasing.m_supplier as c on b.CodeSupplier = c.CodeSupplier
                            left join db_employees.employees as d on a.CreatedBy = d.NIP
                            left join db_purchasing.po_detail as e on a.Code = e.Code
                            left join db_purchasing.pre_po_detail as f on e.ID_pre_po_detail = f.ID
                            left join db_budgeting.pr_detail as g on f.ID_pr_detail = g.ID
                            left join db_budgeting.pr_create as h on h.PRCode = g.PRCode
                            '.$joinaction.'
                        )aa
                       ';

                $sql.= ' where (Code LIKE "%'.$requestData['search']['value'].'%" or TypeCode LIKE "'.$requestData['search']['value'].'%" or NamaSupplier LIKE "%'.$requestData['search']['value'].'%" or CodeSupplier LIKE "'.$requestData['search']['value'].'%"
                      or SPBNameCreatedBy LIKE "'.$requestData['search']['value'].'%" or SPBCreatedBy LIKE "'.$requestData['search']['value'].'%" 
                      or PRCode LIKE "'.$requestData['search']['value'].'%" or CodeSPB LIKE "'.$requestData['search']['value'].'%" 
                    ) '.$StatusQuery.$WhereFiltering.$whereaction ;
                    // print_r($sql);die();
                $sql.= ' ORDER BY ID_payment Desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
                $query = $this->db->query($sql)->result_array();

                $No = $requestData['start'] + 1;
                $G_Approver = $this->m_pr_po->Get_m_Approver();
                if (array_key_exists('length', $dataToken)) {
                    $Count_G_Approver = $dataToken['length'];
                }
                else
                {
                    $Count_G_Approver = count($G_Approver);
                }
                $data = array();
                for($i=0;$i<count($query);$i++){
                    $nestedData=array();
                    $row = $query[$i];
                    $nestedData[] = $No;
                    $nestedData[] = $row['Code'];
                    $nestedData[] = $row['NameDepartementSPB'];
                    // $nestedData[] = $row['CodeSupplier'].' || '.$row['NamaSupplier'];
                    $nestedData[] = $row['StatusNameSPB'];
                    $nestedData[] = '';
                    $JsonStatus = (array)json_decode($row['JsonStatus3'],true);
                    $arr = array();
                    if (count($JsonStatus) > 0) {
                        for ($j=1; $j < count($JsonStatus); $j++) {
                            $getName = $this->m_master->caribasedprimary('db_employees.employees','NIP',$JsonStatus[$j]['NIP']);
                            $Name = $getName[0]['Name'];
                            $StatusInJson = $JsonStatus[$j]['Status'];
                            switch ($StatusInJson) {
                                case '1':
                                    $stjson = '<i class="fa fa-check" style="color: green;"></i>';
                                    break;
                                case '2':
                                    $stjson = '<i class="fa fa-times" aria-hidden="true" style="color: red;"></i>';
                                    break;
                                default:
                                    $stjson = "-";
                                    break;
                            }
                            $arr[] = $stjson.'<br>'.'Approver : '.$Name.'<br>'.'Approve At : '.$JsonStatus[$j]['ApproveAt'];
                        }
                    }

                    $c = $Count_G_Approver - count($arr);
                    for ($l=0; $l < $c; $l++) { 
                         $arr[] = '-';
                    }

                    $nestedData = array_merge($nestedData,$arr);
                    $nestedData[] = $row['SPBNameCreatedBy'].'<br>'.'At : '.$row['CreatedAtSPB'];
                    // find PR in po_detail
                        $arr_temp = array();
                        $sql_get_pr = 'select a.ID,a.ID_m_catalog,b.Item,c.ID as ID_pre_po_detail,d.Code,a.PRCode
                        from db_budgeting.pr_detail as a join db_purchasing.m_catalog as b on a.ID_m_catalog = b.ID
                        left join db_purchasing.pre_po_detail as c on a.ID = c.ID_pr_detail
                        left join db_purchasing.po_detail as d on c.ID = d.ID_pre_po_detail
                        where d.Code = ?
                        ';
                        $query_get_pr=$this->db->query($sql_get_pr, array($row['Code']))->result_array();
                        if (count($query_get_pr)  == 0) {
                            $arr_temp[] = array();
                        }
                        else
                        {
                            for ($j=0; $j < count($query_get_pr); $j++) { 
                                if (count($arr_temp) == 0) {
                                    $arr_temp[] = $query_get_pr[$j]['PRCode'];
                                }
                                else
                                {
                                    // check exist
                                    $bool = true;
                                    for ($k=0; $k < count($arr_temp); $k++) { 
                                        if ($arr_temp[$k]==$query_get_pr[$j]['PRCode']) {
                                            $bool = false;    
                                            break;
                                        }
                                    }

                                    if ($bool) {
                                        $arr_temp[] = $query_get_pr[$j]['PRCode'];
                                    }

                                }
                            }
                        }
                        
                        // pass data spb
                        $arr_temp[] = array(
                            'CodeSPB' => $row['CodeSPB'],
                            'StatusSPB' => $row['StatusSPB'],
                            'TypeCode' => $row['TypeCode'],
                            'ID_payment' => $row['ID_payment'],
                        );

                    $nestedData[] = $arr_temp;
                    $data[] = $nestedData;
                    $No++;
                }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalData ),
                    "data"            => $data,
                );
                echo json_encode($json_data);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        catch(Exception $e) {
             // handling orang iseng
             echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function approve_spb()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('budgeting/m_pr_po');
                    $this->load->model('budgeting/m_spb');
                    $rs = array('Status' => 1,'Change' => 0,'msg' => '');
                    $Code = $dataToken['Code'];
                    $CodeUrl = str_replace('/', '-', $Code);
                    $approval_number = $dataToken['approval_number'];
                    $NIP = $dataToken['NIP'];
                    $G_emp = $this->m_master->SearchNameNIP_Employees_PU_Holding($NIP);
                    $NameFor_NIP = $G_emp[0]['Name'];
                    $action = $dataToken['action'];

                    // get code_po
                    $po_data = $dataToken['po_data'];
                    $po_data = json_decode(json_encode($po_data),true);
                    $po_create = $po_data['po_create'];
                    $Code_po_create = '';
                    if (count($po_create) > 0) {
                        $Code_po_create = $po_create[0]['Code'];
                    }
                   
                    // get data
                    $sql = 'select a.ID as ID_payment_,a.Type,a.Code,a.Code_po_create,a.Departement,a.UploadIOM,a.NoIOM,a.JsonStatus,a.Notes,a.Status,a.Print_Approve,a.CreatedBy,a.CreatedAt,a.LastUpdatedBy,a.LastUpdatedAt,b.* from db_payment.payment as a join db_payment.spb as b on a.ID = b.ID_payment where a.Type = "Spb" and a.Code = ?';
                    $query=$this->db->query($sql, array($Code))->result_array();
                    $G_data = $query;

                    $keyJson = $approval_number - 1; // get array index json
                    $JsonStatus = (array)json_decode($G_data[0]['JsonStatus'],true);

                    // get data update to approval
                    $arr_upd = $JsonStatus[$keyJson];

                    if ($arr_upd['NIP'] == $NIP || $arr_upd['Representedby'] == $NIP) {
                        $arr_upd['Status'] = ($action == 'approve') ? 1 : 2;
                        $arr_upd['ApproveAt'] = ($action == 'approve') ? date('Y-m-d H:i:s') : '-';
                        $JsonStatus[$keyJson] = $arr_upd;
                        $datasave = array(
                            'JsonStatus' => json_encode($JsonStatus),
                        );

                        // check all status for update data
                        $boolApprove = true;
                        for ($i=0; $i < count($JsonStatus); $i++) { 
                            $arr = $JsonStatus[$i];
                            $Status = $arr['Status'];
                            if ($Status == 2 || $Status == 0) {
                                $boolApprove = false;
                                break;
                            }
                        }

                        if ($boolApprove) {
                            $datasave['Status'] = 2;
                            $datasave['PostingDate'] = date('Y-m-d H:i:s');
                        }
                        else
                        {
                            $boolReject = false;
                            for ($i=0; $i < count($JsonStatus); $i++) { 
                                $arr = $JsonStatus[$i];
                                $Status = $arr['Status'];
                                if ($Status == 2) {
                                    $boolReject = true;
                                    break;
                                }
                            }

                            if ($boolReject) {
                                $NoteDel = $dataToken['NoteDel'];
                                $Notes = $G_data[0]['Notes']."\n".$NoteDel;
                                $datasave['Status'] = -1;
                                // $datasave['Notes'] = $Notes;
                            }
                            else
                            {
                                // Notif to next step approval & User
                                    $NIPApprovalNext = $JsonStatus[($keyJson+1)]['NIP'];
                                    // Send Notif for next approval
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Approval SPB : '.$Code,
                                                            'Description' => 'Please approve SPB '.$Code,
                                                            'URLDirect' => 'global/purchasing/transaction/spb/list/'.$CodeUrl,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => array($NIPApprovalNext),
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token);

                                        // send email is holding or warek keatas
                                             $this->m_master->send_email_budgeting_holding($NIPApprovalNext,'NA.4',$data['Logging']['URLDirect'],$data['Logging']['Description']);

                                    // Send Notif for user 
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  SPB '.$Code.' has been Approved',
                                                            'Description' => 'SPB '.$Code.' has been approved by '.$NameFor_NIP,
                                                            'URLDirect' => 'global/purchasing/transaction/spb/list/'.$CodeUrl,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => array($JsonStatus[0]['NIP']),
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token); 
                            }
                        }

                        $this->db->where('Code',$Code);
                        $this->db->update('db_payment.payment',$datasave); 

                            $Desc = ($arr_upd['Status'] == 1) ? 'SPB{'.$Code.'} Approve' : 'SPB Code : '.$Code.' Reject';
                            if (array_key_exists('Status', $datasave)) {
                                if ($datasave['Status'] == 2) {
                                    $Desc = 'SPB{'.$Code."} All Approve and posting date at : ".$datasave['PostingDate'];

                                    // Notif All Approve to JsonStatus allkey
                                        $arr_to = array();
                                        for ($i=0; $i < count($JsonStatus); $i++) { 
                                            $arr_to[] = $JsonStatus[$i]['NIP'];
                                        }

                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> SPB '.$Code.' has been done',
                                                            'Description' => 'SPB '.$Code.' has been done',
                                                            'URLDirect' => 'global/purchasing/transaction/spb/list/'.$CodeUrl,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => $arr_to,
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token);

                                        $G_div = $this->m_budgeting->SearchDepartementBudgeting('NA.4');
                                        $CodeDept = $G_div[0]['Code'];
                                        $sqlAP = "SELECT a.NIP,a.Name,SPLIT_STR(a.PositionMain, '.', 1) as PositionMain1,
                                                       SPLIT_STR(a.PositionMain, '.', 2) as PositionMain2,
                                                             a.StatusEmployeeID
                                                FROM   db_employees.employees as a
                                                where SPLIT_STR(a.PositionMain, '.', 1) = 9 and SPLIT_STR(a.PositionMain, '.', 2) = 12";
                                        $queryAP=$this->db->query($sqlAP, array())->result_array();
                                        if (count($queryAP) > 0) {
                                            $key = "UAP)(*";
                                            $token = $this->jwt->encode($G_data[0]['ID_payment_'],$key);
                                            $CodeUrl2 = $token;
                                            $NIPAP =  $queryAP[0]['NIP'];
                                            $URLDirectAP = 'finance_ap/create_ap?token='.$CodeUrl2;

                                            $data = array(
                                                'auth' => 's3Cr3T-G4N',
                                                'Logging' => array(
                                                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> '.$G_data[0]['Type'].' of '.$CodeDept.' has been done for approval',
                                                                'Description' => $G_data[0]['Type'].' of '.$CodeDept.'',
                                                                'URLDirect' => $URLDirectAP,
                                                                'CreatedBy' => $NIP,
                                                              ),
                                                'To' => array(
                                                          'NIP' => array($NIPAP),
                                                        ),
                                                'Email' => 'No', 
                                            );

                                            $url = url_pas.'rest2/__send_notif_browser';
                                            $token = $this->jwt->encode($data,"UAP)(*");
                                            $this->m_master->apiservertoserver($url,$token);
                                        }

                                }
                            }

                            if ($arr_upd['Status'] == 2) {
                                if ($dataToken['NoteDel'] != '' || $dataToken['NoteDel'] != null) {
                                    $Desc .= '<br>{'.$dataToken['NoteDel'].'}';
                                }

                                // Notif Reject to JsonStatus key 0
                                    // Send Notif for user 
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> SPB '.$Code.' has been Rejected',
                                                            'Description' => 'SPB '.$Code.' has been Rejected by '.$NameFor_NIP,
                                                            'URLDirect' => 'global/purchasing/transaction/spb/list/'.$CodeUrl,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => array($JsonStatus[0]['NIP']),
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token);
                            }

                            if (count($po_create) > 0) {
                                 $this->m_pr_po->po_circulation_sheet($Code_po_create,$Desc.'<br><b> SPB '.$Code.'</b>',$NIP);
                            }
                                $this->m_spb->payment_circulation_sheet($G_data[0]['ID_payment_'],$Desc,$NIP);

                    }
                    else
                    {
                        $msg = 'Not Authorize';
                    }

                    echo json_encode($rs);

                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function get_data_payment()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('budgeting/m_pr_po');
                //check action
               $fieldaction = ', pay.ID_payment,pay.Status as StatusPay,pay.Departement as DepartementPay,pay.JsonStatus as JsonStatus3,pay.Code as CodeSPB,pay.CreatedBy as PayCreatedBy,e_spb.Name as PayNameCreatedBy,if(pay.Status = 0,"Draft",if(pay.Status = 1,"Issued & Approval Process",if(pay.Status =  2,"Approval Done",if(pay.Status = -1,"Reject","Cancel") ) )) as StatusNamepay,t_spb_de.NameDepartement as NameDepartementPay,pay.Perihal,pay.Type as TypePay,pay.CreatedAt as PayCreateAt,pay.DateNeededAP,pay.ID_template_pay  ';
               $joinaction = ' right join (
                                        select a.ID as ID_payment_,a.Type,a.Code,a.Code_po_create,a.Departement,a.UploadIOM,a.NoIOM,a.JsonStatus,a.Notes,a.Status,a.Print_Approve,a.CreatedBy,a.CreatedAt,a.LastUpdatedBy,a.LastUpdatedAt,a.ID_template as ID_template_pay,b.* from db_payment.payment as a join
                                        ( select ID_payment,Perihal,"-" as DateNeededAP from db_payment.spb
                                          UNION 
                                          select ID_payment,Perihal,Date_Needed  from db_payment.bank_advance
                                          UNION 
                                          select ID_payment,Perihal,Date_Needed  from db_payment.cash_advance  
                                        )
                        as b on a.ID = b.ID_payment
                         )
                                as pay on pay.Code_po_create = a.Code
                               left join db_employees.employees as e_spb on e_spb.NIP = pay.CreatedBy
                               join (
                               select * from (
                               select CONCAT("AC.",ID) as ID, NameEng as NameDepartement from db_academic.program_study where Status = 1
                               UNION
                               select CONCAT("NA.",ID) as ID, Division as NameDepartement from db_employees.division where StatusDiv = 1
                               UNION
                               select CONCAT("FT.",ID) as ID, NameEng as NameDepartement from db_academic.faculty where StBudgeting = 1
                               ) aa
                               ) as t_spb_de on pay.Departement = t_spb_de.ID
                            ';
               $whereaction = ' and StatusPay = 2';

                // get Department
                $WhereFiltering = ' and ID_payment not in (select ID_payment from db_budgeting.ap where Status = 2)';
                if (array_key_exists('TypePaymentSelect', $dataToken)) {
                    if ($dataToken['TypePaymentSelect'] != '%') {
                        $WhereFiltering .=  ' and TypePay = "'.$dataToken['TypePaymentSelect'].'"';
                    }
                }

                if (array_key_exists('SelectTemplate', $dataToken)) {
                    if ($dataToken['SelectTemplate'] != '%' && $dataToken['SelectTemplate'] != '') {
                       $WhereFiltering .= ' and (ID_template_PR = '.$dataToken['SelectTemplate'].' or ID_template_pay = '.$dataToken['SelectTemplate'].' )';
                    }
                }
                 
                $requestData = $_REQUEST;
                // $StatusQuery = ' and Status = 2';
                $StatusQuery = '';
                $sqltotalData = 'select count(*) as total  from (
                            select if(a.TypeCreate = 1,"PO","SPK") as TypeCode,a.Code,a.ID_pre_po_supplier,b.CodeSupplier,
                                c.NamaSupplier,c.PICName as PICSupplier,c.Alamat as AlamatSupplier,
                                a.JsonStatus,
                                if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = -1,"Reject","Cancel") ) )) as StatusName,a.CreatedBy,d.Name as NameCreateBy,a.CreatedAt,a.PostingDate,g.PRCode,h.JsonStatus as JsonStatus2,h.ID_template as ID_template_PR,h.Departement,a.Status'.$fieldaction.'
                            from db_purchasing.po_create as a
                            left join db_purchasing.pre_po_supplier as b on a.ID_pre_po_supplier = b.ID
                            left join db_purchasing.m_supplier as c on b.CodeSupplier = c.CodeSupplier
                            left join db_employees.employees as d on a.CreatedBy = d.NIP
                            left join db_purchasing.po_detail as e on a.Code = e.Code
                            left join db_purchasing.pre_po_detail as f on e.ID_pre_po_detail = f.ID
                            left join db_budgeting.pr_detail as g on f.ID_pr_detail = g.ID
                            left join db_budgeting.pr_create as h on h.PRCode = g.PRCode
                            '.$joinaction.'
                        )aa
                       ';

                $sqltotalData.= ' where (Code LIKE "'.$requestData['search']['value'].'%" or TypeCode LIKE "'.$requestData['search']['value'].'%" or NamaSupplier LIKE "'.$requestData['search']['value'].'%" or CodeSupplier LIKE "'.$requestData['search']['value'].'%"
                      or PayNameCreatedBy LIKE "'.$requestData['search']['value'].'%" or PayCreatedBy LIKE "'.$requestData['search']['value'].'" 
                      or PRCode LIKE "'.$requestData['search']['value'].'%"  or CodeSPB LIKE "'.$requestData['search']['value'].'%"
                      or TypePay LIKE "'.$requestData['search']['value'].'%" or NameDepartementPay LIKE "'.$requestData['search']['value'].'%"
                      or ID_payment = "'.$requestData['search']['value'].'"
                    ) '.$StatusQuery.$WhereFiltering.$whereaction ;
  
                $querytotalData = $this->db->query($sqltotalData)->result_array();
                $totalData = $querytotalData[0]['total'];

                $sql = 'select * from (
                            select a.ID as ID_po_create,if(a.TypeCreate = 1,"PO","SPK") as TypeCode,a.Code,a.ID_pre_po_supplier,b.CodeSupplier,
                                c.NamaSupplier,c.PICName as PICSupplier,c.Alamat as AlamatSupplier,
                                a.JsonStatus,
                                if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = -1,"Reject","Cancel") ) )) as StatusName,a.CreatedBy,d.Name as NameCreateBy,a.CreatedAt,a.PostingDate,g.PRCode,h.JsonStatus as JsonStatus2,h.ID_template as ID_template_PR,h.Departement,a.Status'.$fieldaction.'
                            from db_purchasing.po_create as a
                            left join db_purchasing.pre_po_supplier as b on a.ID_pre_po_supplier = b.ID
                            left join db_purchasing.m_supplier as c on b.CodeSupplier = c.CodeSupplier
                            left join db_employees.employees as d on a.CreatedBy = d.NIP
                            left join db_purchasing.po_detail as e on a.Code = e.Code
                            left join db_purchasing.pre_po_detail as f on e.ID_pre_po_detail = f.ID
                            left join db_budgeting.pr_detail as g on f.ID_pr_detail = g.ID
                            left join db_budgeting.pr_create as h on h.PRCode = g.PRCode
                            '.$joinaction.'
                        )aa
                       ';

                $sql.= ' where (Code LIKE "'.$requestData['search']['value'].'%" or TypeCode LIKE "'.$requestData['search']['value'].'%" or NamaSupplier LIKE "'.$requestData['search']['value'].'%" or CodeSupplier LIKE "'.$requestData['search']['value'].'%"
                      or PayNameCreatedBy LIKE "'.$requestData['search']['value'].'%" or PayCreatedBy LIKE "'.$requestData['search']['value'].'" 
                      or PRCode LIKE "'.$requestData['search']['value'].'%" or CodeSPB LIKE "'.$requestData['search']['value'].'%" 
                      or TypePay LIKE "'.$requestData['search']['value'].'%" or NameDepartementPay LIKE "'.$requestData['search']['value'].'%"
                      or ID_payment = "'.$requestData['search']['value'].'"
                    ) '.$StatusQuery.$WhereFiltering.$whereaction ;
                // $sql.= ' ORDER BY PayCreateAt Desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
                $sql.= ' ORDER BY DateNeededAP asc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';

                $query = $this->db->query($sql)->result_array();

                $No = $requestData['start'] + 1;
                
                $data = array();
                for($i=0;$i<count($query);$i++){
                    $nestedData=array();
                    $row = $query[$i];
                    $nestedData[] = $No;
                    $nestedData[] = $row['Code'];
                    $nestedData[] = $row['NameDepartementPay'];
                    // $nestedData[] = $row['CodeSupplier'].' || '.$row['NamaSupplier'];
                    $nestedData[] = $row['StatusNamepay'];
                    $nestedData[] = '';
                    $nestedData[] = $row['PayNameCreatedBy'];

                    // find PR in po_detail
                        $arr_temp = array();
                        $sql_get_pr = 'select a.ID,a.ID_m_catalog,b.Item,c.ID as ID_pre_po_detail,d.Code,a.PRCode
                        from db_budgeting.pr_detail as a join db_purchasing.m_catalog as b on a.ID_m_catalog = b.ID
                        left join db_purchasing.pre_po_detail as c on a.ID = c.ID_pr_detail
                        left join db_purchasing.po_detail as d on c.ID = d.ID_pre_po_detail
                        where d.Code = ?
                        ';
                        $query_get_pr=$this->db->query($sql_get_pr, array($row['Code']))->result_array();
                        if (count($query_get_pr)  == 0) {
                            $arr_temp[] = array();
                        }
                        else
                        {
                            for ($j=0; $j < count($query_get_pr); $j++) { 
                                if (count($arr_temp) == 0) {
                                    $arr_temp[] = $query_get_pr[$j]['PRCode'];
                                }
                                else
                                {
                                    // check exist
                                    $bool = true;
                                    for ($k=0; $k < count($arr_temp); $k++) { 
                                        if ($arr_temp[$k]==$query_get_pr[$j]['PRCode']) {
                                            $bool = false;    
                                            break;
                                        }
                                    }

                                    if ($bool) {
                                        $arr_temp[] = $query_get_pr[$j]['PRCode'];
                                    }

                                }
                            }
                        }
                        // pass data spb
                        $arr_temp[] = array(
                            'CodeSPB' => $row['CodeSPB'],
                            'StatusPay' => $row['StatusPay'],
                            'TypePay' => $row['TypePay'],
                            'ID_payment' => $row['ID_payment'],
                            'Perihal' => $row['Perihal'],
                            'DateNeededAP' => $row['DateNeededAP'],
                            'NamaSupplier' => $row['NamaSupplier'],
                        );

                    $nestedData[] = $arr_temp;
                    $data[] = $nestedData;
                    $No++;
                }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalData ),
                    "data"            => $data,
                );
                echo json_encode($json_data);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        catch(Exception $e) {
             // handling orang iseng
             echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function reject_payment_from_fin()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $rs = array('Status' => 1,'Change' => 0,'msg' => '');
                    $this->load->model('budgeting/m_pr_po');
                    $this->load->model('budgeting/m_spb');
                    $ID_payment = $dataToken['ID_payment'];
                    $NIP = $dataToken['NIP'];
                    $NoteDel = $dataToken['NoteDel'];
                    $Desc_circulationSheet = 'Reject{'.$NoteDel.'}';
                    $arr = array(
                        'Status' => -1,
                    );
                    $this->db->where('ID',$ID_payment);
                    $this->db->update('db_payment.payment',$arr);
                    $G_data = $this->m_master->caribasedprimary('db_payment.payment','ID',$ID_payment);
                    // insert to spb_circulation_sheet
                        $this->m_spb->payment_circulation_sheet($ID_payment,$Desc_circulationSheet);
                        if ($G_data[0]['Code_po_create'] != '' && $G_data[0]['Code_po_create'] != null) {
                            // insert to po_circulation_sheet
                                $this->m_pr_po->po_circulation_sheet($G_data[0]['Code_po_create'],$Desc_circulationSheet);  
                        }

                    echo json_encode($rs);    
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function paid_payment_from_fin()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $rs = array('Status' => 1,'Change' => 0,'msg' => '');
                    /*
                        1. Check by po atau tidak
                        2. jika by po maka dapatkan data budget_left dari pr
                        a. check budget left yang tersedia masih cukup atau tidak pada database untuk verifikasi beserta dengan po_invoice_status
                        b. check type payment, untuk link data invoice dari table type payment
                        c. kurangi budget budget left pada using dan kurangi juga pada value
                        d.kurangi juga pada InvoiceLeftPO di po_invoice status
                        3.kurangi budget budget left pada using dan kurangi juga pada value
                    */
                    $ID_payment = $dataToken['ID_payment'];
                    $po_payment_data = $dataToken['po_payment_data'];
                    $po_payment_data = json_decode(json_encode($po_payment_data),true);
                    if (array_key_exists('dtspb', $po_payment_data)) {
                        $dt = $po_payment_data['dtspb'];
                    }
                    else
                    {
                        $dt = $po_payment_data['payment'];
                    }
                    
                    $Invoice = $dt[0]['Detail'][0]['Invoice'];
                    $Code_po_create =  $dt[0]['Code_po_create'];
                    if ($Code_po_create != '' && $Code_po_create != null) {
                        $po_data = $dataToken['po_data'];
                        $po_data = json_decode(json_encode($po_data),true);
                        $po_create = $po_data['po_create'];
                        $PRCode = $po_create[0]['Code'];
                        $po_detail = $po_data['po_detail'];
                        $bool = true;
                        $Total = 0;
                        for ($i=0; $i < count($po_detail); $i++) { 
                            $ID_budget_left = $po_detail[$i]['ID_budget_left'];
                            $G = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
                            $ValueInvoice = $G[0]['Value'];
                            $Total += $ValueInvoice;
                        }

                        $InvoiceLeft = $Invoice;
                        if ($Total >= $Invoice) {
                             // insert ke table ap dulu
                                $UploadVoucher = '';
                                if (array_key_exists('UploadVoucher', $_FILES)) {
                                    $UploadVoucher = $this->m_master->uploadDokumenMultiple('Voucher_'.uniqid(),'UploadVoucher',$path = './uploads/finance');
                                }
                              
                              $UploadVoucher = json_encode($UploadVoucher);
                              $dtime =  date('Y-m-d H:i:s');
                              $arr = array(
                                'ID_payment' => $ID_payment,
                                'Status' => 2,
                                'JsonStatus' => json_encode(array()),
                                'CreatedBy' => $dataToken['NIP'],
                                'CreatedAt' => $dtime,
                                'PostingDate' => $dtime,
                                'Code' => '',
                                'NoVoucher' => $dataToken['NoVoucher'],
                                'UploadVoucher' => $UploadVoucher,
                              );
                              $this->db->insert('db_budgeting.ap',$arr);
                              $ID_ap = $this->db->insert_id();

                              for ($i=0; $i < count($po_detail); $i++) {
                                  if ($InvoiceLeft <= 0) {
                                      break;
                                  } 
                                  $ID_budget_left = $po_detail[$i]['ID_budget_left'];
                                  /*
                                    Note : jika terjadi perubahan harga pada po maka update data using dulu dengan keterangan dibawah
                                  */

                                  $SubTotal_PO = $po_detail[$i]['Subtotal'];
                                  $Subtotal_PR =  $po_detail[$i]['Subtotal_PR'];
                                  if ($SubTotal_PO != $Subtotal_PR) {
                                     /*
                                           using kurangi dengan Subtotal_PR terlebih dahulu, lalu baru ditambahkan dengan  SubTotal_PO
                                     */

                                     $_G = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
                                      $_ValueUsing= $_G[0]['Using'];
                                      $_ValueUsing = $_ValueUsing - $Subtotal_PR;
                                      $_ValueUsing = $_ValueUsing + $SubTotal_PO;
                                      $arr_save['Using'] = $_ValueUsing;
                                      $this->db->where('ID',$ID_budget_left);
                                      $this->db->update('db_budgeting.budget_left',$arr_save);
                                  }   

                                  $G = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
                                  $ValueUsing= $G[0]['Using'];
                                  $ValueInvoice= $G[0]['Value'];
                                  $InvoiceAP = 0;   
                                  // $bool2 = true;
                                  if ($ValueUsing >= $InvoiceLeft) {
                                      if ($InvoiceLeft <= $SubTotal_PO) {
                                        $ValueInvoice = $ValueInvoice - $InvoiceLeft;
                                        $ValueUsing = $ValueUsing - $InvoiceLeft;
                                        $InvoiceAP = $InvoiceLeft; 
                                        $InvoiceLeft = $InvoiceLeft - $InvoiceLeft;
                                      }
                                      else
                                      {
                                        $ValueInvoice = $ValueInvoice - $SubTotal_PO;
                                        $ValueUsing = $ValueUsing - $SubTotal_PO;
                                        $InvoiceAP = $SubTotal_PO; 
                                        $InvoiceLeft = $InvoiceLeft - $SubTotal_PO;
                                      }
                                      
                                  }
                                  else
                                  {
                                    $ValueInvoice = $ValueInvoice - $ValueUsing;
                                    $InvoiceAP = $ValueUsing;
                                    $ValueUsing = $ValueUsing - $ValueUsing;
                                    $InvoiceLeft = $InvoiceLeft - $ValueUsing;
                                  }

                                  // insert ke budget_payment
                                  $arr_ap = array(
                                    'ID_ap' => $ID_ap,
                                    'ID_budget_left' => $ID_budget_left,
                                    'Invoice' => $InvoiceAP,
                                  );
                                  $this->db->insert('db_budgeting.budget_payment',$arr_ap);

                                  // update budget_left
                                  $arr_budget_left = array(
                                    'Value' =>  $ValueInvoice,
                                    'Using' => $ValueUsing,                                   
                                  );

                                  $this->db->where('ID',$ID_budget_left);
                                  $this->db->update('db_budgeting.budget_left',$arr_budget_left);
                              }

                              // update po_invoice_status
                              $G_po_invoice_status = $this->m_master->caribasedprimary('db_purchasing.po_invoice_status','Code_po_create',$Code_po_create);
                              $InvoiceLeftPO = $G_po_invoice_status[0]['InvoiceLeftPO'];
                              $InvoiceLeftPO = $InvoiceLeftPO - $Invoice;
                              $InvoicePayPO = $G_po_invoice_status[0]['InvoicePayPO'];
                              $arr_po_invoice_status = array();
                              if ($InvoiceLeftPO <= 0) {
                                  $arr_po_invoice_status['Status'] = 1;
                              }
                             $arr_po_invoice_status['InvoiceLeftPO'] = $InvoiceLeftPO;
                             $arr_po_invoice_status['InvoicePayPO'] = $InvoicePayPO + $Invoice;
                             $this->db->where('Code_po_create',$Code_po_create);
                             $this->db->update('db_purchasing.po_invoice_status',$arr_po_invoice_status);
                        }
                        else
                        {
                            $rs['Status'] = 0;
                            $rs['msg'] = 'Post Budget pada PR Code : '.$PRCode.' tidak mencukupi untuk melakukan pembayaran';
                        }

                    }
                    else
                    {
                        // NON PO
                        $DetailPayment = $dt[0]['Detail'];
                        $DetailPaymentType = $DetailPayment[0]['Detail'];
                        $Invoice = $DetailPayment[0]['Invoice'];
                        $Total = 0;
                        // get total budget left
                        for ($i=0; $i < count($DetailPaymentType); $i++) { 
                            $ID_budget_left = $DetailPaymentType[$i]['ID_budget_left'];
                            $G = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
                            $ValueInvoice = $G[0]['Value'];
                            $Total += $ValueInvoice;
                        }

                        $InvoiceLeft = $Invoice;
                        if ($Total >= $Invoice) {
                            // insert ke table ap dulu
                            $UploadVoucher = '';
                            if (array_key_exists('UploadVoucher', $_FILES)) {
                                $UploadVoucher = $this->m_master->uploadDokumenMultiple('Voucher_'.uniqid(),'UploadVoucher',$path = './uploads/finance');
                            }
                            $UploadVoucher = json_encode($UploadVoucher);
                            $dtime =  date('Y-m-d H:i:s');

                            $arr = array(
                              'ID_payment' => $ID_payment,
                              'Status' => 2,
                              'JsonStatus' => json_encode(array()),
                              'CreatedBy' => $dataToken['NIP'],
                              'CreatedAt' => $dtime,
                              'PostingDate' => $dtime,
                              'Code' => '',
                              'NoVoucher' => $dataToken['NoVoucher'],
                              'UploadVoucher' => $UploadVoucher,
                            );
                            $this->db->insert('db_budgeting.ap',$arr);
                            $ID_ap = $this->db->insert_id();

                            for ($i=0; $i < count($DetailPaymentType); $i++) { 
                                if ($InvoiceLeft <= 0) {
                                    break;
                                }

                                $ID_budget_left = $DetailPaymentType[$i]['ID_budget_left'];
                                $InvoiceByr =  $DetailPaymentType[$i]['Invoice'];

                                $G = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
                                $ValueUsing= $G[0]['Using'];
                                $ValueInvoice= $G[0]['Value'];
                                $InvoiceAP = 0;   

                                if ($ValueUsing >= $InvoiceByr) {
                                    $ValueInvoice = $ValueInvoice - $InvoiceByr;
                                    $ValueUsing = $ValueUsing - $InvoiceByr;
                                    $InvoiceAP = $InvoiceByr;  
                                    $InvoiceLeft = $InvoiceLeft - $InvoiceByr;
                                    // $bool2 = false;
                                }
                                else
                                {
                                  $ValueInvoice = $ValueInvoice - $ValueUsing;
                                  $InvoiceAP = $ValueUsing;
                                  $ValueUsing = $ValueUsing - $ValueUsing;
                                  $InvoiceLeft = $InvoiceLeft - $ValueUsing;
                                }

                                // insert ke budget_payment
                                $arr_ap = array(
                                  'ID_ap' => $ID_ap,
                                  'ID_budget_left' => $ID_budget_left,
                                  'Invoice' => $InvoiceAP,
                                );
                                $this->db->insert('db_budgeting.budget_payment',$arr_ap);

                                // update budget_left
                                $arr_budget_left = array(
                                  'Value' =>  $ValueInvoice,
                                  'Using' => $ValueUsing,                                   
                                );

                                $this->db->where('ID',$ID_budget_left);
                                $this->db->update('db_budgeting.budget_left',$arr_budget_left);
                            }

                        }
                        else
                        {
                            $rs['Status'] = 0;
                            $rs['msg'] = 'Post Budget mencukupi untuk melakukan pembayaran';
                        }

                        // print_r($dt);die();
                    }

                    echo json_encode($rs);
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function get_data_payment_type()
    {
        try {
            $dataToken = $this->getInputToken2();
            $auth = $this->m_master->AuthAPI($dataToken);
            if ($auth) {
                $this->load->model('budgeting/m_pr_po');
                //check action

                $JoinRealisasi = '';
                if ($dataToken['Type'] == 'Bank Advance') {
                   $JoinRealisasi = 'select ID_bank_advance as ID_payment_type,JsonStatus,Status  from db_payment.bank_advance_realisasi';
                }
                elseif ($dataToken['Type'] == 'Cash Advance') {
                    $JoinRealisasi = 'select ID_cash_advance as ID_payment_type,JsonStatus,Status  from db_payment.cash_advance_realisasi';
                }
                elseif ($dataToken['Type'] == 'Petty Cash') {
                    // $JoinRealisasi = 'select ID_petty_cash as ID_payment_type,JsonStatus,Status  from db_payment.petty_cash_realisasi';
                }

               $fieldaction = ', pay.ID_payment,pay.Status as StatusPay,pay.Departement as DepartementPay,pay.JsonStatus as JsonStatus3,pay.Code as CodeSPB,pay.CreatedBy as PayCreatedBy,e_spb.Name as PayNameCreatedBy,if(pay.Status = 0,"Draft",if(pay.Status = 1,"Issued & Approval Process",if(pay.Status =  2,"Approval Done",if(pay.Status = -1,"Reject","Cancel") ) )) as StatusNamepay,t_spb_de.NameDepartement as NameDepartementPay,pay.Perihal,pay.Type as TypePay,pay.CreatedAt as PayCreateAt,pay.ID_template_pay, 
                    t_realisasi.ID_payment_type,t_realisasi.JsonStatus as JsonStatusRealisasi,t_realisasi.Status as StatusRealisasi';
               $joinaction = ' right join (
                                        select a.ID as ID_payment_,a.Type,a.Code,a.Code_po_create,a.Departement,a.UploadIOM,a.NoIOM,a.JsonStatus,a.Notes,a.Status,a.Print_Approve,a.CreatedBy,a.CreatedAt,a.ID_template as ID_template_pay,a.LastUpdatedBy,a.LastUpdatedAt,b.* from db_payment.payment as a join
                                        ( select ID_payment,Perihal,ID as ID_payment_type  from db_payment.spb
                                          UNION 
                                          select ID_payment,Perihal,ID as ID_payment_type  from db_payment.bank_advance
                                          UNION 
                                          select ID_payment,Perihal,ID as ID_payment_type  from db_payment.cash_advance  
                                          #UNION 
                                          #select ID_payment,Perihal,ID as ID_payment_type  from db_payment.petty_cash 
                                        )
                        as b on a.ID = b.ID_payment
                        where a.Type = "'.$dataToken['Type'].'"
                         )
                                as pay on pay.Code_po_create = a.Code
                               left join db_employees.employees as e_spb on e_spb.NIP = pay.CreatedBy
                               join (
                               select * from (
                               select CONCAT("AC.",ID) as ID, NameEng as NameDepartement from db_academic.program_study where Status = 1
                               UNION
                               select CONCAT("NA.",ID) as ID, Division as NameDepartement from db_employees.division where StatusDiv = 1
                               UNION
                               select CONCAT("FT.",ID) as ID, NameEng as NameDepartement from db_academic.faculty where StBudgeting = 1
                               ) aa
                               ) as t_spb_de on pay.Departement = t_spb_de.ID
                               left join (
                                  '.$JoinRealisasi.'
                               ) as t_realisasi on t_realisasi.ID_payment_type = pay.ID_payment_type
                            ';
               $whereaction = ' and StatusPay != 0';

                // get Department
                $IDDepartementPUBudget = $dataToken['IDDepartementPUBudget'];
                $WhereFiltering = '';
                if ($IDDepartementPUBudget != 'NA.9') {
                    $NIP = $dataToken['sessionNIP'];
                    $WhereFiltering = ' and (Departement = "'.$IDDepartementPUBudget.'" or JsonStatus2 REGEXP \'"NIP":"[[:<:]]'.$NIP.'[[:>:]]"\' or  JsonStatus REGEXP \'"NIP":"[[:<:]]'.$NIP.'[[:>:]]"\' or  DepartementPay = "'.$IDDepartementPUBudget.'" or JsonStatus3 REGEXP \'"NIP":"[[:<:]]'.$NIP.'[[:>:]]"\' ) ';
                }

                if (array_key_exists('Years', $dataToken)) {
                    $WhereFiltering .= ' and (Year = "'.$dataToken['Years'].'" or YEAR(PayCreateAt) = "'.$dataToken['Years'].'" ) ';
                }

                if (array_key_exists('Month', $dataToken)) {
                    if ($dataToken['Month'] != 'all') {
                        $WhereFiltering .= ' and MONTH(PayCreateAt) = '.(int)$dataToken['Month'];
                    }
                }
                if (array_key_exists('SelectTemplate', $dataToken)) {
                    if ($dataToken['SelectTemplate'] != '%' && $dataToken['SelectTemplate'] != '') {
                       $WhereFiltering .= ' and (ID_template_PR = '.$dataToken['SelectTemplate'].' or ID_template_pay = '.$dataToken['SelectTemplate'].' )';
                    }
                }
                $requestData = $_REQUEST;
                // $StatusQuery = ' or Status = 2';
                $StatusQuery = '';
                $sqltotalData = 'select count(*) as total  from (
                            select if(a.TypeCreate = 1,"PO","SPK") as TypeCode,a.Code,a.ID_pre_po_supplier,b.CodeSupplier,
                                c.NamaSupplier,c.PICName as PICSupplier,c.Alamat as AlamatSupplier,
                                a.JsonStatus,
                                if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = -1,"Reject","Cancel") ) )) as StatusName,a.CreatedBy,d.Name as NameCreateBy,a.CreatedAt,a.PostingDate,g.PRCode,h.ID_template as ID_template_PR,h.JsonStatus as JsonStatus2,h.Year,h.Departement,a.Status'.$fieldaction.'
                            from db_purchasing.po_create as a
                            left join db_purchasing.pre_po_supplier as b on a.ID_pre_po_supplier = b.ID
                            left join db_purchasing.m_supplier as c on b.CodeSupplier = c.CodeSupplier
                            left join db_employees.employees as d on a.CreatedBy = d.NIP
                            left join db_purchasing.po_detail as e on a.Code = e.Code
                            left join db_purchasing.pre_po_detail as f on e.ID_pre_po_detail = f.ID
                            left join db_budgeting.pr_detail as g on f.ID_pr_detail = g.ID
                            left join db_budgeting.pr_create as h on h.PRCode = g.PRCode
                            '.$joinaction.'
                        )aa
                       ';

                $sqltotalData.= ' where (Code LIKE "%'.$requestData['search']['value'].'%" or TypeCode LIKE "'.$requestData['search']['value'].'%" or NamaSupplier LIKE "%'.$requestData['search']['value'].'%" or CodeSupplier LIKE "'.$requestData['search']['value'].'%"
                      or PayNameCreatedBy LIKE "'.$requestData['search']['value'].'%" or PayCreatedBy LIKE "'.$requestData['search']['value'].'%" 
                      or PRCode LIKE "'.$requestData['search']['value'].'%"  or CodeSPB LIKE "'.$requestData['search']['value'].'%"
                      or TypePay LIKE "'.$requestData['search']['value'].'%" or NameDepartementPay LIKE "'.$requestData['search']['value'].'%"
                      or Perihal LIKE "'.$requestData['search']['value'].'%"
                    ) '.$StatusQuery.$WhereFiltering.$whereaction ;
                // print_r($sqltotalData);die();    
                $querytotalData = $this->db->query($sqltotalData)->result_array();
                $totalData = $querytotalData[0]['total'];

                $sql = 'select * from (
                            select a.ID as ID_po_create,if(a.TypeCreate = 1,"PO","SPK") as TypeCode,a.Code,a.ID_pre_po_supplier,b.CodeSupplier,
                                c.NamaSupplier,c.PICName as PICSupplier,c.Alamat as AlamatSupplier,
                                a.JsonStatus,
                                if(a.Status = 0,"Draft",if(a.Status = 1,"Issued & Approval Process",if(a.Status =  2,"Approval Done",if(a.Status = -1,"Reject","Cancel") ) )) as StatusName,a.CreatedBy,d.Name as NameCreateBy,a.CreatedAt,a.PostingDate,g.PRCode,h.ID_template as ID_template_PR,h.JsonStatus as JsonStatus2,h.Year,h.Departement,a.Status'.$fieldaction.'
                            from db_purchasing.po_create as a
                            left join db_purchasing.pre_po_supplier as b on a.ID_pre_po_supplier = b.ID
                            left join db_purchasing.m_supplier as c on b.CodeSupplier = c.CodeSupplier
                            left join db_employees.employees as d on a.CreatedBy = d.NIP
                            left join db_purchasing.po_detail as e on a.Code = e.Code
                            left join db_purchasing.pre_po_detail as f on e.ID_pre_po_detail = f.ID
                            left join db_budgeting.pr_detail as g on f.ID_pr_detail = g.ID
                            left join db_budgeting.pr_create as h on h.PRCode = g.PRCode
                            '.$joinaction.'
                        )aa
                       ';

                $sql.= ' where (Code LIKE "%'.$requestData['search']['value'].'%" or TypeCode LIKE "'.$requestData['search']['value'].'%" or NamaSupplier LIKE "%'.$requestData['search']['value'].'%" or CodeSupplier LIKE "'.$requestData['search']['value'].'%"
                      or PayNameCreatedBy LIKE "'.$requestData['search']['value'].'%" or PayCreatedBy LIKE "'.$requestData['search']['value'].'%" 
                      or PRCode LIKE "'.$requestData['search']['value'].'%" or CodeSPB LIKE "'.$requestData['search']['value'].'%" 
                      or TypePay LIKE "'.$requestData['search']['value'].'%" or NameDepartementPay LIKE "'.$requestData['search']['value'].'%"
                      or Perihal LIKE "'.$requestData['search']['value'].'%"
                    ) '.$StatusQuery.$WhereFiltering.$whereaction ;
                $sql.= ' ORDER BY PayCreateAt Desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
                $query = $this->db->query($sql)->result_array();

                $No = $requestData['start'] + 1;
                $G_Approver = $this->m_pr_po->Get_m_Approver();
                if (array_key_exists('length', $dataToken)) {
                    $Count_G_Approver = $dataToken['length'];
                }
                else
                {
                    $Count_G_Approver = count($G_Approver);
                }
                $data = array();
                for($i=0;$i<count($query);$i++){
                    $nestedData=array();
                    $row = $query[$i];
                    $nestedData[] = $No;
                    $nestedData[] = $row['Code'];
                    $nestedData[] = $row['NameDepartementPay'];
                    // $nestedData[] = $row['CodeSupplier'].' || '.$row['NamaSupplier'];
                    $nestedData[] = $row['StatusNamepay'];
                    $arr_realisasi = array(
                        'ID_payment_type' => $row['ID_payment_type'],
                        'JsonStatusRealisasi' => $row['JsonStatusRealisasi'],
                        'StatusRealisasi' => $row['StatusRealisasi'],
                    );

                    $nestedData[] = $arr_realisasi;
                    $nestedData[] = '';
                    $JsonStatus = (array)json_decode($row['JsonStatus3'],true);
                    $arr = array();
                    if (count($JsonStatus) > 0) {
                        for ($j=1; $j < count($JsonStatus); $j++) {
                            $getName = $this->m_master->caribasedprimary('db_employees.employees','NIP',$JsonStatus[$j]['NIP']);
                            $Name = $getName[0]['Name'];
                            $StatusInJson = $JsonStatus[$j]['Status'];
                            switch ($StatusInJson) {
                                case '1':
                                    $stjson = '<i class="fa fa-check" style="color: green;"></i>';
                                    break;
                                case '2':
                                    $stjson = '<i class="fa fa-times" aria-hidden="true" style="color: red;"></i>';
                                    break;
                                default:
                                    $stjson = "-";
                                    break;
                            }
                            $arr[] = $stjson.'<br>'.'Approver : '.$Name.'<br>'.'Approve At : '.$JsonStatus[$j]['ApproveAt'];
                        }
                    }

                    $c = $Count_G_Approver - count($arr);
                    for ($l=0; $l < $c; $l++) { 
                         $arr[] = '-';
                    }

                    $nestedData = array_merge($nestedData,$arr);
                    $nestedData[] = $row['PayNameCreatedBy'].'<br>'.'At : '.$row['PayCreateAt'];
                    // find PR in po_detail
                        $arr_temp = array();
                        $sql_get_pr = 'select a.ID,a.ID_m_catalog,b.Item,c.ID as ID_pre_po_detail,d.Code,a.PRCode
                        from db_budgeting.pr_detail as a join db_purchasing.m_catalog as b on a.ID_m_catalog = b.ID
                        left join db_purchasing.pre_po_detail as c on a.ID = c.ID_pr_detail
                        left join db_purchasing.po_detail as d on c.ID = d.ID_pre_po_detail
                        where d.Code = ?
                        ';
                        $query_get_pr=$this->db->query($sql_get_pr, array($row['Code']))->result_array();
                        if (count($query_get_pr)  == 0) {
                            $arr_temp[] = array();
                        }
                        else
                        {
                            for ($j=0; $j < count($query_get_pr); $j++) { 
                                if (count($arr_temp) == 0) {
                                    $arr_temp[] = $query_get_pr[$j]['PRCode'];
                                }
                                else
                                {
                                    // check exist
                                    $bool = true;
                                    for ($k=0; $k < count($arr_temp); $k++) { 
                                        if ($arr_temp[$k]==$query_get_pr[$j]['PRCode']) {
                                            $bool = false;    
                                            break;
                                        }
                                    }

                                    if ($bool) {
                                        $arr_temp[] = $query_get_pr[$j]['PRCode'];
                                    }

                                }
                            }
                        }

                        // pass data spb
                        $arr_temp[] = array(
                            'CodeSPB' => $row['CodeSPB'],
                            'StatusPay' => $row['StatusPay'],
                            'TypePay' => $row['TypePay'],
                            'ID_payment' => $row['ID_payment'],
                            'Perihal' => $row['Perihal'],
                        );

                    $nestedData[] = $arr_temp;
                    $data[] = $nestedData;
                    $No++;
                }

                $json_data = array(
                    "draw"            => intval( $requestData['draw'] ),
                    "recordsTotal"    => intval($totalData),
                    "recordsFiltered" => intval($totalData ),
                    "data"            => $data,
                );
                echo json_encode($json_data);
            }
            else
            {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
            }
        }
        catch(Exception $e) {
             // handling orang iseng
             echo '{"status":"999","message":"Not Authorize"}';
        }
    }

    public function approve_payment()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('budgeting/m_pr_po');
                    $this->load->model('budgeting/m_spb');
                    $rs = array('Status' => 1,'Change' => 0,'msg' => '');
                    $ID_payment = $dataToken['ID_payment'];
                    $key = "UAP)(*";
                    $token = $this->jwt->encode($ID_payment,$key);

                    $CodeUrl = $token;
                    $approval_number = $dataToken['approval_number'];
                    $NIP = $dataToken['NIP'];
                    $G_emp = $this->m_master->SearchNameNIP_Employees_PU_Holding($NIP);
                    $NameFor_NIP = $G_emp[0]['Name'];
                    $action = $dataToken['action'];

                    // get code_po
                    $po_data = $dataToken['po_data'];
                    $po_data = json_decode(json_encode($po_data),true);
                    $po_create = $po_data['po_create'];
                    $Code_po_create = '';
                    if (count($po_create) > 0) {
                        $Code_po_create = $po_create[0]['Code'];
                    }
                   
                    // get data
                    $sql = 'select a.ID as ID_payment_,a.Type,a.Code,a.Code_po_create,a.Departement,a.UploadIOM,a.NoIOM,a.JsonStatus,a.Notes,a.Status,a.Print_Approve,a.CreatedBy,a.CreatedAt,a.LastUpdatedBy,a.LastUpdatedAt from db_payment.payment as a where a.ID = ?';
                    $query=$this->db->query($sql, array($ID_payment))->result_array();
                    $G_data = $query;

                    $urlType = '';
                    switch ($G_data[0]['Type']) {
                        case 'Bank Advance':
                            $urlType = 'ba';
                            break;
                        case 'Cash Advance':
                            $urlType = 'ca';
                            break;
                        case 'Petty Cash':
                             $urlType = 'pc';
                            break;    
                        default:
                            # code...
                            break;
                    }

                    $keyJson = $approval_number - 1; // get array index json
                    $JsonStatus = (array)json_decode($G_data[0]['JsonStatus'],true);

                    // get data update to approval
                    $arr_upd = $JsonStatus[$keyJson];

                    if ($arr_upd['NIP'] == $NIP || $arr_upd['Representedby'] == $NIP) {
                        $arr_upd['Status'] = ($action == 'approve') ? 1 : 2;
                        $arr_upd['ApproveAt'] = ($action == 'approve') ? date('Y-m-d H:i:s') : '-';
                        $JsonStatus[$keyJson] = $arr_upd;
                        $datasave = array(
                            'JsonStatus' => json_encode($JsonStatus),
                        );

                        // check all status for update data
                        $boolApprove = true;
                        for ($i=0; $i < count($JsonStatus); $i++) { 
                            $arr = $JsonStatus[$i];
                            $Status = $arr['Status'];
                            if ($Status == 2 || $Status == 0) {
                                $boolApprove = false;
                                break;
                            }
                        }

                        if ($boolApprove) {
                            $datasave['Status'] = 2;
                            $datasave['PostingDate'] = date('Y-m-d H:i:s');
                        }
                        else
                        {
                            $boolReject = false;
                            for ($i=0; $i < count($JsonStatus); $i++) { 
                                $arr = $JsonStatus[$i];
                                $Status = $arr['Status'];
                                if ($Status == 2) {
                                    $boolReject = true;
                                    break;
                                }
                            }

                            if ($boolReject) {
                                $NoteDel = $dataToken['NoteDel'];
                                $Notes = $G_data[0]['Notes']."\n".$NoteDel;
                                $datasave['Status'] = -1;
                                // $datasave['Notes'] = $Notes;
                            }
                            else
                            {
                                // Notif to next step approval & User
                                    $NIPApprovalNext = $JsonStatus[($keyJson+1)]['NIP'];
                                    // Send Notif for next approval
                                        $RevisiOrNotNotif = $this->m_master->__RevisiOrNotNotif($ID_payment,'db_payment.payment_circulation_sheet','ID_payment');
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Approval '.$RevisiOrNotNotif.$G_data[0]['Type'],
                                                            'Description' => 'Please approve '.$RevisiOrNotNotif.$G_data[0]['Type'],
                                                            'URLDirect' => 'global/purchasing/transaction/'.$urlType.'/list/'.$CodeUrl,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => array($NIPApprovalNext),
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token);

                                        // send email is holding or warek keatas
                                             $this->m_master->send_email_budgeting_holding($NIPApprovalNext,'NA.4',$data['Logging']['URLDirect'],$data['Logging']['Description']);

                                    // Send Notif for user 
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>'.$G_data[0]['Type'].' has been Approved',
                                                            'Description' => $G_data[0]['Type'].' has been approved by '.$NameFor_NIP,
                                                            'URLDirect' => 'global/purchasing/transaction/'.$urlType.'/list/'.$CodeUrl,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => array($JsonStatus[0]['NIP']),
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token); 
                            }
                        }

                        $this->db->where('ID',$ID_payment);
                        $this->db->update('db_payment.payment',$datasave); 

                            $Desc = ($arr_upd['Status'] == 1) ? 'Approve' : 'Reject';
                            if (array_key_exists('Status', $datasave)) {
                                if ($datasave['Status'] == 2) {
                                    $Desc = "All Approve and posting date at : ".$datasave['PostingDate'];

                                    // Notif All Approve to JsonStatus allkey
                                        $arr_to = array();
                                        for ($i=0; $i < count($JsonStatus); $i++) { 
                                            $arr_to[] = $JsonStatus[$i]['NIP'];
                                        }

                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> '.$G_data[0]['Type'].' has been done',
                                                            'Description' => $G_data[0]['Type'].' has been done',
                                                            'URLDirect' => 'global/purchasing/transaction/'.$urlType.'/list/'.$CodeUrl,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => $arr_to,
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token);


                                        // notif to ap team atau kasubag fin
                                            $sqlAP = "SELECT a.NIP,a.Name,SPLIT_STR(a.PositionMain, '.', 1) as PositionMain1,
                                                           SPLIT_STR(a.PositionMain, '.', 2) as PositionMain2,
                                                                 a.StatusEmployeeID
                                                    FROM   db_employees.employees as a
                                                    where SPLIT_STR(a.PositionMain, '.', 1) = 9 and SPLIT_STR(a.PositionMain, '.', 2) = 12";
                                            $queryAP=$this->db->query($sqlAP, array())->result_array();
                                            if (count($queryAP) > 0) {
                                                $NIPAP =  $queryAP[0]['NIP'];
                                                $URLDirectAP = 'finance_ap/create_ap?token='.$CodeUrl;

                                                $data = array(
                                                    'auth' => 's3Cr3T-G4N',
                                                    'Logging' => array(
                                                                    'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> '.$G_data[0]['Type'].' of Purchasing has been done for approval',
                                                                    'Description' => $G_data[0]['Type'].' of Purchasing',
                                                                    'URLDirect' => $URLDirectAP,
                                                                    'CreatedBy' => $NIP,
                                                                  ),
                                                    'To' => array(
                                                              'NIP' => array($NIPAP),
                                                            ),
                                                    'Email' => 'No', 
                                                );

                                                $url = url_pas.'rest2/__send_notif_browser';
                                                $token = $this->jwt->encode($data,"UAP)(*");
                                                $this->m_master->apiservertoserver($url,$token);
                                            }
                                }
                            }

                            if ($arr_upd['Status'] == 2) {
                                if ($dataToken['NoteDel'] != '' || $dataToken['NoteDel'] != null) {
                                    $Desc .= '<br>{'.$dataToken['NoteDel'].'}';
                                }

                                // Notif Reject to JsonStatus key 0
                                    // Send Notif for user 
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> '.$G_data[0]['Type'].' has been Rejected',
                                                            'Description' => $G_data[0]['Type'].' has been Rejected by '.$NameFor_NIP,
                                                            'URLDirect' => 'global/purchasing/transaction/'.$urlType.'/list/'.$CodeUrl,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => array($JsonStatus[0]['NIP']),
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token);
                            }

                            if (count($po_create) > 0) {
                                 $this->m_pr_po->po_circulation_sheet($Code_po_create,$Desc.'<br><b>'.$G_data[0]['Type'].'</b>',$NIP);
                            }
                                $this->m_spb->payment_circulation_sheet($G_data[0]['ID_payment_'],$Desc,$NIP);

                    }
                    else
                    {
                        $msg = 'Not Authorize';
                    }

                    echo json_encode($rs);

                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }



    public function approve_payment_realisasi()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('budgeting/m_pr_po');
                    $this->load->model('budgeting/m_spb');
                    $rs = array('Status' => 1,'Change' => 0,'msg' => '');
                    $ID_payment = $dataToken['ID_payment'];
                    $key = "UAP)(*";
                    $token = $this->jwt->encode($ID_payment,$key);
                    $CodeUrl2 = $token;

                    $ID_Realisasi = $dataToken['ID_Realisasi'];

                    $payment_data = $dataToken['payment_data'];
                    $payment_data = json_decode(json_encode($payment_data),true);
                    if (array_key_exists('payment', $payment_data)) {
                        $payment_ = $payment_data['payment'];
                    }
                    else
                    {
                        $payment_ = $payment_data['dtspb'];
                    }
                    $FinanceAP = $payment_[0]['FinanceAP'];
                    $ID_ap = $FinanceAP[0]['ID']; 
                    $key = "UAP)(*";
                    $token = $this->jwt->encode($ID_ap,$key);

                    $CodeUrl = $token;
                    $approval_number = $dataToken['approval_number'];
                    $NIP = $dataToken['NIP'];
                    $G_emp = $this->m_master->SearchNameNIP_Employees_PU_Holding($NIP);
                    $NameFor_NIP = $G_emp[0]['Name'];
                    $action = $dataToken['action'];
                   
                    // get data
                    $sql = 'select a.ID as ID_payment_,a.Type,a.Code,a.Code_po_create,a.Departement,a.UploadIOM,a.NoIOM,a.JsonStatus,a.Notes,a.Status,a.Print_Approve,a.CreatedBy,a.CreatedAt,a.LastUpdatedBy,a.LastUpdatedAt from db_payment.payment as a where a.ID = ?';
                    $query=$this->db->query($sql, array($ID_payment))->result_array();
                    $G_data = $query;

                    $urlType = '';
                    $tblupdate = '';
                    $Dp = $G_data[0]['Departement'];
                    switch ($G_data[0]['Type']) {
                        case 'Bank Advance':
                            $urlType = 'bank_advance';
                            if ($Dp == 'NA.4') {
                                $urlType = 'ba';
                            }
                            // get data realisasi
                            $G_data_realisasi = $this->m_master->caribasedprimary('db_payment.bank_advance_realisasi','ID',$ID_Realisasi);
                            $JsonStatus = (array)json_decode($G_data_realisasi[0]['JsonStatus'],true);
                            $tblupdate = 'db_payment.bank_advance_realisasi';
                            break;
                        case 'Cash Advance':
                            $urlType = 'cashadvance';
                            if ($Dp == 'NA.4') {
                                $urlType = 'ca';
                            }
                            // get data realisasi
                            $G_data_realisasi = $this->m_master->caribasedprimary('db_payment.cash_advance_realisasi','ID',$ID_Realisasi);
                            $JsonStatus = (array)json_decode($G_data_realisasi[0]['JsonStatus'],true);
                            $tblupdate = 'db_payment.cash_advance_realisasi';
                            break;
                        // case 'Petty Cash':
                        //      $urlType = 'pc';
                        //      // get data realisasi
                        //      $G_data_realisasi = $this->m_master->caribasedprimary('db_payment.petty_cash_realisasi','ID',$ID_Realisasi);
                        //      $JsonStatus = (array)json_decode($G_data_realisasi[0]['JsonStatus'],true);
                        //      $tblupdate = 'db_payment.petty_cash_realisasi';
                        //     break;    
                        default:
                            die();
                            break;
                    }

                    $keyJson = $approval_number - 1; // get array index json

                    // get data update to approval
                    $arr_upd = $JsonStatus[$keyJson];

                    if ($arr_upd['NIP'] == $NIP || $arr_upd['Representedby'] == $NIP) {
                        $arr_upd['Status'] = ($action == 'approve') ? 1 : 2;
                        $arr_upd['ApproveAt'] = ($action == 'approve') ? date('Y-m-d H:i:s') : '-';
                        $JsonStatus[$keyJson] = $arr_upd;
                        $datasave = array(
                            'JsonStatus' => json_encode($JsonStatus),
                        );

                        // check all status for update data
                        $boolApprove = true;
                        for ($i=0; $i < count($JsonStatus); $i++) { 
                            $arr = $JsonStatus[$i];
                            $Status = $arr['Status'];
                            if ($Status == 2 || $Status == 0) {
                                $boolApprove = false;
                                break;
                            }
                        }

                        if ($boolApprove) {
                            $datasave['Status'] = 2;
                        }
                        else
                        {
                            $boolReject = false;
                            for ($i=0; $i < count($JsonStatus); $i++) { 
                                $arr = $JsonStatus[$i];
                                $Status = $arr['Status'];
                                if ($Status == 2) {
                                    $boolReject = true;
                                    break;
                                }
                            }

                            if ($boolReject) {
                                $NoteDel = $dataToken['NoteDel'];
                                $Notes = $NoteDel;
                                $datasave['Status'] = -1;
                                // $datasave['Notes'] = $Notes;
                            }
                            else
                            {
                                // Notif to next step approval & User
                                    $NIPApprovalNext = $JsonStatus[($keyJson+1)]['NIP'];
                                    $UrlDirect = 'budgeting_menu/pembayaran/'.$urlType.'/'.$CodeUrl2;
                                    if ($Dp == 'NA.4') {
                                         $UrlDirect = 'global/purchasing/transaction/'.$urlType.'/list/'.$CodeUrl2;
                                    }
                                   
                                    $b_check = $this->m_master->NonDiv(9,$NIPApprovalNext);
                                    if ($b_check) {
                                        $UrlDirect = 'finance_ap/global/'.$CodeUrl;
                                    }

                                    // Send Notif for next approval
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Realisasi Approval '.$G_data[0]['Type'],
                                                            'Description' => 'Please approve '.$G_data[0]['Type'],
                                                            'URLDirect' => $UrlDirect,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => array($NIPApprovalNext),
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token);

                                    // Send Notif for user
                                        $UrlDirect = 'budgeting_menu/pembayaran/'.$urlType.'/'.$CodeUrl2;
                                        if ($Dp == 'NA.4') {
                                             $UrlDirect = 'global/purchasing/transaction/'.$urlType.'/list/'.$CodeUrl2;
                                        }
                                        $b_check = $this->m_master->NonDiv(9,$JsonStatus[0]['NIP']);
                                        if ($b_check) {
                                            $UrlDirect = 'finance_ap/global/'.$CodeUrl;
                                        } 
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> Realisasi '.$G_data[0]['Type'].' has been Approved',
                                                            'Description' => $G_data[0]['Type'].' has been approved by '.$NameFor_NIP,
                                                            'URLDirect' => $UrlDirect,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => array($JsonStatus[0]['NIP']),
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token); 
                            }
                        }

                        $this->db->where('ID',$ID_Realisasi);
                        $this->db->update($tblupdate,$datasave); 

                            $Desc = ($arr_upd['Status'] == 1) ? 'Realisasi Approve' : 'Realisasi Reject';
                            if (array_key_exists('Status', $datasave)) {
                                if ($datasave['Status'] == 2) {
                                    $Desc = "Realisasi Approve and finished at : ".date('Y-m-d H:i:s');

                                    // update budget left
                                        // if (array_key_exists('payment_data', $dataToken)) {
                                            // $payment_data = $dataToken['payment_data'];
                                            // $payment_data = json_decode(json_encode($payment_data),true);
                                            // $payment_ = $payment_data['payment'];
                                            // $FinanceAP = $payment_[0]['FinanceAP'];
                                            // $ID_ap = $FinanceAP[0]['ID']; 
                                            $Detail = $payment_[0]['Detail'];
                                            $DetailPay = $Detail[0]['Detail'];
                                            for ($i=0; $i < count($DetailPay); $i++) { 
                                                $Invoice1 = $DetailPay[$i]['Invoice']; // invoice pengajuan
                                                $Realisasi = $DetailPay[$i]['Realisasi'];
                                                $Invoice2 = $Realisasi[0]['InvoiceRealisasi']; // InvoiceRealisasi
                                                $ID_budget_left = $DetailPay[$i]['ID_budget_left'];
                                                if ($Invoice1 != $Invoice2) {
                                                    /*
                                                        Note : 
                                                        Invoice1 selalu lebih besar dari $Invoice2
                                                        
                                                    */
                                                    $Reason = 'Pengembalian';    
                                                    $Pengembalian =  $Invoice1 - $Invoice2;  // add auto pengembalian
                                                    // insert ke table db_budgeting.budget_payment
                                                    $data_arr_payment = array(
                                                        'Type' => 'Add',
                                                        'ID_ap' => $ID_ap,
                                                        'ID_budget_left' => $ID_budget_left,
                                                        'Invoice' => $Pengembalian,
                                                        'Reason' => $Reason,
                                                    );

                                                    $this->db->insert('db_budgeting.budget_payment',$data_arr_payment);

                                                    // update budget left
                                                    $G_budget_left_re = $this->m_master->caribasedprimary('db_budgeting.budget_left','ID',$ID_budget_left);
                                                    $Value1 = $G_budget_left_re[0]['Value'];
                                                    $ValueUPD = $Value1 + $Pengembalian;
                                                    $data_arr_budget = array(
                                                        'Value' => $ValueUPD, 
                                                    );

                                                    $this->db->where('ID',$ID_budget_left);
                                                    $this->db->update('db_budgeting.budget_left',$data_arr_budget);
                                                }
                                            }
                                        // }
                                    
                                        for ($i=0; $i < count($JsonStatus); $i++) {
                                            $NIPJson =  $JsonStatus[$i]['NIP'];
                                            $UrlDirect = 'budgeting_menu/pembayaran/'.$urlType.'/'.$CodeUrl2;
                                            if ($Dp == 'NA.4') {
                                                 $UrlDirect = 'global/purchasing/transaction/'.$urlType.'/list/'.$CodeUrl2;
                                            }
                                            // $UrlDirect = 'global/purchasing/transaction/'.$urlType.'/list/'.$CodeUrl;
                                            $b_check = $this->m_master->NonDiv(9,$NIPJson);
                                            if ($b_check) {
                                                $UrlDirect = 'finance_ap/global/'.$CodeUrl;
                                            }

                                            $data = array(
                                                'auth' => 's3Cr3T-G4N',
                                                'Logging' => array(
                                                                'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>Realisasi '.$G_data[0]['Type'].' has been done',
                                                                'Description' => $G_data[0]['Type'].' has been done',
                                                                'URLDirect' => $UrlDirect,
                                                                'CreatedBy' => $NIP,
                                                              ),
                                                'To' => array(
                                                          'NIP' => array($JsonStatus[$i]['NIP']),
                                                        ),
                                                'Email' => 'No', 
                                            );

                                            $url = url_pas.'rest2/__send_notif_browser';
                                            $token = $this->jwt->encode($data,"UAP)(*");
                                            $this->m_master->apiservertoserver($url,$token); 
                                        }

                                }
                            }

                            if ($arr_upd['Status'] == 2) {
                                if ($dataToken['NoteDel'] != '' || $dataToken['NoteDel'] != null) {
                                    $Desc .= '<br>{'.$dataToken['NoteDel'].'}';
                                }

                                // Notif Reject to JsonStatus key 0
                                    // Send Notif for user
                                        $UrlDirect = 'budgeting_menu/pembayaran/'.$urlType.'/'.$CodeUrl2;
                                        if ($Dp == 'NA.4') {
                                             $UrlDirect = 'global/purchasing/transaction/'.$urlType.'/list/'.$CodeUrl2;
                                        }
                                        $b_check = $this->m_master->NonDiv(9,$JsonStatus[0]['NIP']);
                                        if ($b_check) {
                                            $UrlDirect = 'finance_ap/global/'.$CodeUrl;
                                        }  
                                        $data = array(
                                            'auth' => 's3Cr3T-G4N',
                                            'Logging' => array(
                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>Realisasi '.$G_data[0]['Type'].' has been Rejected',
                                                            'Description' => $G_data[0]['Type'].' has been Rejected by '.$NameFor_NIP,
                                                            'URLDirect' => $UrlDirect,
                                                            'CreatedBy' => $NIP,
                                                          ),
                                            'To' => array(
                                                      'NIP' => array($JsonStatus[0]['NIP']),
                                                    ),
                                            'Email' => 'No', 
                                        );

                                        $url = url_pas.'rest2/__send_notif_browser';
                                        $token = $this->jwt->encode($data,"UAP)(*");
                                        $this->m_master->apiservertoserver($url,$token);
                            }
    
                            $this->m_spb->payment_circulation_sheet($G_data[0]['ID_payment_'],$Desc,$NIP);

                    }
                    else
                    {
                        $msg = 'Not Authorize';
                    }

                    echo json_encode($rs);

                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function approve_payment_user()
    {
        $msg = '';
        $Reload = 0;
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('budgeting/m_budgeting');
                    $this->load->model('budgeting/m_pr_po');
                    $this->load->model('budgeting/m_spb');
                    $ID_payment = $dataToken['ID_payment'];
                    $approval_number = $dataToken['approval_number'];
                    // check data telah berubah atau tidak
                       $DtExisting = json_decode(json_encode($dataToken['DtExisting']),true);
                       $DtExisting_encode = json_encode($DtExisting);
                       // print_r($DtExisting);
                       // load data payment
                       $data = array(
                           'auth' => 's3Cr3T-G4N',
                           'ID_payment' => $ID_payment,
                       );
                       // print_r($data);
                       $url = url_pas.'rest2/__Get_data_payment_user';
                       $token = $this->jwt->encode($data,"UAP)(*");
                       $DtExisting_Load = $this->m_master->apiservertoserver($url,$token);
                       $DtExisting_Load = json_encode($DtExisting_Load);
                       if ($DtExisting_Load == $DtExisting_encode) {
                            $key = "UAP)(*";
                            $token = $this->jwt->encode($ID_payment,$key);
                            $CodeUrl = $token;
                            $approval_number = $dataToken['approval_number'];
                            $NIP = $dataToken['NIP'];
                            $G_emp = $this->m_master->SearchNameNIP_Employees_PU_Holding($NIP);
                            $NameFor_NIP = $G_emp[0]['Name'];
                            $action = $dataToken['action'];

                            // get data
                            $sql = 'select a.ID as ID_payment_,a.Type,a.Code,a.Code_po_create,a.Departement,a.UploadIOM,a.NoIOM,a.JsonStatus,a.Notes,a.Status,a.Print_Approve,a.CreatedBy,a.CreatedAt,a.LastUpdatedBy,a.LastUpdatedAt from db_payment.payment as a where a.ID = ?';
                            $query=$this->db->query($sql, array($ID_payment))->result_array();
                            $G_data = $query;
                            $urlType = '';
                            switch ($G_data[0]['Type']) {
                                case 'Bank Advance':
                                    $urlType = 'bank_advance';
                                    break;
                                case 'Cash Advance':
                                    $urlType = 'cashadvance';
                                    break;
                                case 'Petty Cash':
                                     $urlType = 'pettycash';
                                    break;
                                case 'Spb':
                                     $urlType = 'spb';
                                    break;         
                                default:
                                    # code...
                                    break;
                            }

                            $keyJson = $approval_number - 1; // get array index json
                            $JsonStatus = (array)json_decode($G_data[0]['JsonStatus'],true);

                            // get data update to approval
                            $arr_upd = $JsonStatus[$keyJson];

                            if ($arr_upd['NIP'] == $NIP || $arr_upd['Representedby'] == $NIP) {
                                $arr_upd['Status'] = ($action == 'approve') ? 1 : 2;
                                $arr_upd['ApproveAt'] = ($action == 'approve') ? date('Y-m-d H:i:s') : '-';
                                $JsonStatus[$keyJson] = $arr_upd;
                                $datasave = array(
                                    'JsonStatus' => json_encode($JsonStatus),
                                );

                                // check all status for update data
                                $boolApprove = true;
                                for ($i=0; $i < count($JsonStatus); $i++) { 
                                    $arr = $JsonStatus[$i];
                                    $Status = $arr['Status'];
                                    if ($Status == 2 || $Status == 0) {
                                        $boolApprove = false;
                                        break;
                                    }
                                }

                                if ($boolApprove) {
                                    $datasave['Status'] = 2;
                                    $datasave['PostingDate'] = date('Y-m-d H:i:s');
                                }
                                else
                                {
                                    $boolReject = false;
                                    for ($i=0; $i < count($JsonStatus); $i++) { 
                                        $arr = $JsonStatus[$i];
                                        $Status = $arr['Status'];
                                        if ($Status == 2) {
                                            $boolReject = true;
                                            break;
                                        }
                                    }

                                    if ($boolReject) {
                                        $NoteDel = $dataToken['NoteDel'];
                                        $Notes = $G_data[0]['Notes']."\n".$NoteDel;
                                        $datasave['Status'] = -1;
                                        // $datasave['Notes'] = $Notes;
                                    }
                                    else
                                    {
                                        // Notif to next step approval & User
                                            $NIPApprovalNext = $JsonStatus[($keyJson+1)]['NIP'];
                                            if (array_key_exists('payment', $DtExisting)) {
                                                $IDdiv = $DtExisting['payment'][0]['Departement'];
                                            }
                                            else
                                            {
                                                $IDdiv = $DtExisting['dtspb'][0]['Departement'];
                                            }

                                            $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                                            $CodeDept = $G_div[0]['Code'];
                                           
                                            // Send Notif for next approval
                                                // send revisi or not
                                                $RevisiOrNotNotif = $this->m_master->__RevisiOrNotNotif($ID_payment,'db_payment.payment_circulation_sheet','ID_payment');
                                                $data = array(
                                                    'auth' => 's3Cr3T-G4N',
                                                    'Logging' => array(
                                                                    'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>  Approval '.$G_data[0]['Type'].' of '.$CodeDept,
                                                                    'Description' => 'Please approve '.$G_data[0]['Type'].' of '.$CodeDept,
                                                                    'URLDirect' => 'budgeting_menu/pembayaran/'.$urlType.'/'.$CodeUrl,
                                                                    'CreatedBy' => $NIP,
                                                                  ),
                                                    'To' => array(
                                                              'NIP' => array($NIPApprovalNext),
                                                            ),
                                                    'Email' => 'No', 
                                                );

                                                $url = url_pas.'rest2/__send_notif_browser';
                                                $token = $this->jwt->encode($data,"UAP)(*");
                                                $this->m_master->apiservertoserver($url,$token);

                                                // send email is holding or warek keatas
                                                     $this->m_master->send_email_budgeting_holding($NIPApprovalNext,$IDdiv,$data['Logging']['URLDirect'],$data['Logging']['Description']);

                                            // Send Notif for user 
                                                $data = array(
                                                    'auth' => 's3Cr3T-G4N',
                                                    'Logging' => array(
                                                                    'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i>'.$G_data[0]['Type'].' has been Approved',
                                                                    'Description' => $G_data[0]['Type'].' has been approved by '.$NameFor_NIP,
                                                                    'URLDirect' => 'budgeting_menu/pembayaran/'.$urlType.'/'.$CodeUrl,
                                                                    'CreatedBy' => $NIP,
                                                                  ),
                                                    'To' => array(
                                                              'NIP' => array($JsonStatus[0]['NIP']),
                                                            ),
                                                    'Email' => 'No', 
                                                );

                                                $url = url_pas.'rest2/__send_notif_browser';
                                                $token = $this->jwt->encode($data,"UAP)(*");
                                                $this->m_master->apiservertoserver($url,$token); 
                                    }
                                }

                                $this->db->where('ID',$ID_payment);
                                $this->db->update('db_payment.payment',$datasave); 

                                    $Desc = ($arr_upd['Status'] == 1) ? 'Approve' : 'Reject';
                                    if (array_key_exists('Status', $datasave)) {
                                        if ($datasave['Status'] == 2) {
                                            $Desc = "All Approve and posting date at : ".$datasave['PostingDate'];

                                            // Notif All Approve to JsonStatus allkey
                                                $arr_to = array();
                                                for ($i=0; $i < count($JsonStatus); $i++) { 
                                                    $arr_to[] = $JsonStatus[$i]['NIP'];
                                                }

                                                $data = array(
                                                    'auth' => 's3Cr3T-G4N',
                                                    'Logging' => array(
                                                                    'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> '.$G_data[0]['Type'].' has been done',
                                                                    'Description' => $G_data[0]['Type'].' has been done',
                                                                    'URLDirect' => 'budgeting_menu/pembayaran/'.$urlType.'/'.$CodeUrl,
                                                                    'CreatedBy' => $NIP,
                                                                  ),
                                                    'To' => array(
                                                              'NIP' => $arr_to,
                                                            ),
                                                    'Email' => 'No', 
                                                );

                                                $url = url_pas.'rest2/__send_notif_browser';
                                                $token = $this->jwt->encode($data,"UAP)(*");
                                                $this->m_master->apiservertoserver($url,$token);

                                                // notif to ap team atau kasubag fin
                                                    if (array_key_exists('payment', $DtExisting)) {
                                                        $IDdiv = $DtExisting['payment'][0]['Departement'];
                                                    }
                                                    else
                                                    {
                                                        $IDdiv = $DtExisting['dtspb'][0]['Departement'];
                                                    }

                                                    $G_div = $this->m_budgeting->SearchDepartementBudgeting($IDdiv);
                                                    $CodeDept = $G_div[0]['Code'];
                                                    $sqlAP = "SELECT a.NIP,a.Name,SPLIT_STR(a.PositionMain, '.', 1) as PositionMain1,
                                                                   SPLIT_STR(a.PositionMain, '.', 2) as PositionMain2,
                                                                         a.StatusEmployeeID
                                                            FROM   db_employees.employees as a
                                                            where SPLIT_STR(a.PositionMain, '.', 1) = 9 and SPLIT_STR(a.PositionMain, '.', 2) = 12";
                                                    $queryAP=$this->db->query($sqlAP, array())->result_array();
                                                    if (count($queryAP) > 0) {
                                                        $NIPAP =  $queryAP[0]['NIP'];
                                                        $URLDirectAP = 'finance_ap/create_ap?token='.$CodeUrl;

                                                        $data = array(
                                                            'auth' => 's3Cr3T-G4N',
                                                            'Logging' => array(
                                                                            'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> '.$G_data[0]['Type'].' of '.$CodeDept.' has been done for approval',
                                                                            'Description' => $G_data[0]['Type'].' of '.$CodeDept.'',
                                                                            'URLDirect' => $URLDirectAP,
                                                                            'CreatedBy' => $NIP,
                                                                          ),
                                                            'To' => array(
                                                                      'NIP' => array($NIPAP),
                                                                    ),
                                                            'Email' => 'No', 
                                                        );

                                                        $url = url_pas.'rest2/__send_notif_browser';
                                                        $token = $this->jwt->encode($data,"UAP)(*");
                                                        $this->m_master->apiservertoserver($url,$token);
                                                    }

                                        }
                                    }

                                    if ($arr_upd['Status'] == 2) {
                                        if ($dataToken['NoteDel'] != '' || $dataToken['NoteDel'] != null) {
                                            $Desc .= '<br>{'.$dataToken['NoteDel'].'}';
                                        }

                                        // Notif Reject to JsonStatus key 0
                                            // Send Notif for user 
                                                $data = array(
                                                    'auth' => 's3Cr3T-G4N',
                                                    'Logging' => array(
                                                                    'Title' => '<i class="fa fa-check-circle margin-right" style="color:green;"></i> '.$G_data[0]['Type'].' has been Rejected',
                                                                    'Description' => $G_data[0]['Type'].' has been Rejected by '.$NameFor_NIP,
                                                                    'URLDirect' => 'budgeting_menu/pembayaran/'.$urlType.'/'.$CodeUrl,
                                                                    'CreatedBy' => $NIP,
                                                                  ),
                                                    'To' => array(
                                                              'NIP' => array($JsonStatus[0]['NIP']),
                                                            ),
                                                    'Email' => 'No', 
                                                );

                                                $url = url_pas.'rest2/__send_notif_browser';
                                                $token = $this->jwt->encode($data,"UAP)(*");
                                                $this->m_master->apiservertoserver($url,$token);
                                    }

                                    $this->m_spb->payment_circulation_sheet($G_data[0]['ID_payment_'],$Desc,$NIP);

                            }
                            else
                            {
                                $Reload = 1;
                                $msg = 'Not Authorize';
                            }
                       }
                       else
                       {
                        $Reload = 1;
                        $msg = 'The data was not approve and will do to reload and resubmit';
                       }
                       
                       echo json_encode(array('Reload' => $Reload,'msg' => $msg));  
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function load_budget_real_detail_byMonthYear()
    {
        try {
                $dataToken = $this->getInputToken2();
                $auth = $this->m_master->AuthAPI($dataToken);
                if ($auth) {
                    $this->load->model('budgeting/m_budgeting');
                    $this->load->model('budgeting/m_pr_po');
                    $ID_budget_left = $dataToken['ID_budget_left'];
                    $Year = $dataToken['Year'];
                    $Month = $dataToken['Month'];
                    $rs = array();
                    $sql = '
                            select aa.*,bb.Name as  NameCreatedPayment from (
                                select a.ID as ID_ap,a.Code as CodeAP,a.ID_payment,a.PostingDate,a.CreatedBy as CreatedPayment,
                                c.Type as TypePayment,c.Code as CodeSPB,c.Code_po_create
                                from db_budgeting.ap as a join (select * from db_budgeting.budget_payment) as b on a.ID = b.ID_ap
                                join db_payment.payment as c on a.ID_payment = c.ID
                                where YEAR(a.PostingDate) = '.$Year.' and MONTH(a.PostingDate) = '.$Month.' and b.ID_budget_left = '.$ID_budget_left.'
                                group by b.ID_ap
                                UNION
                                select a.ID,"","",a.CreateAt,a.CreateBy,"Revisi","",""
                                from db_budgeting.budget_adjustment as a
                                where YEAR(a.CreateAt) = '.$Year.' and MONTH(a.CreateAt) = '.$Month.' and a.ID_budget_left = '.$ID_budget_left.'
                            ) aa
                            join db_employees.employees as bb on bb.NIP = aa.CreatedPayment
                            order by aa.PostingDate asc
                           ';
                     $query=$this->db->query($sql, array())->result_array();
                     $rs = $query;
                     for ($i=0; $i < count($rs); $i++) { 
                         $TypePayment = $rs[$i]['TypePayment'];
                         $ID_ap = $rs[$i]['ID_ap'];
                         if ($TypePayment != 'Revisi') {
                             $__bp = $this->m_master->caribasedprimary('db_budgeting.budget_payment','ID_ap',$ID_ap);
                             $Tot = 0;
                             for ($j=0; $j < count($__bp); $j++) { 
                                $Type = $__bp[$j]['Type'];
                                if ($ID_budget_left == $__bp[$j]['ID_budget_left']) {
                                    if ($Type == 'Less') {
                                        $Tot = $Tot - $__bp[$j]['Invoice'];
                                    }
                                    else
                                    {
                                        $Tot = $Tot + $__bp[$j]['Invoice'];
                                    }
                                }
                                
                             }

                              $rs[$i]['bpd'] = $__bp;
                              $rs[$i]['Invoice'] = $Tot;
                         }
                         else
                         {
                            $__bp = $this->m_master->caribasedprimary('db_budgeting.budget_adjustment','ID',$ID_ap);
                            if ($__bp[0]['Type'] =='Less') { // kasih minus untuk yg less agar dikelompokan ke kolom less
                                $__bp[0]['Invoice'] = '-'.$__bp[0]['Invoice'];
                            }
                            //check mutasi atau tidak
                            $G_ma = $this->m_master->caribasedprimary('db_budgeting.budget_mutasi','ID_budget_adjustment_a',$ID_ap);
                            $G_mb = $this->m_master->caribasedprimary('db_budgeting.budget_mutasi','ID_budget_adjustment_b',$ID_ap);
                            if (count($G_ma) > 0 || count($G_mb) > 0) {
                               $rs[$i]['TypePayment'] = 'Mutasi';
                                $d = $G_ma;
                                if (count($d) > 0 ) {
                                   $stMutasi = 'Mutasi ke '; 
                                   $ID_budget_adjustment_b = $d[0]['ID_budget_adjustment_b'];
                                   $dd = $this->m_master->caribasedprimary('db_budgeting.budget_adjustment','ID',$ID_budget_adjustment_b);
                                   $ID_budget_left_b = $dd[0]['ID_budget_left'];
                                   $dt_b = $this->m_pr_po->Get_DataBudgeting_by_ID_budget_left($ID_budget_left_b);
                                   $stMutasi .=  $dt_b[0]['NameHeadAccount'].'-'.$dt_b[0]['RealisasiPostName'].'('.$dt_b[0]['CodeDepartment'].')';
                                }
                                else
                                {
                                    $stMutasi = 'DiMutasi dari';
                                    $d = $G_mb;
                                    $ID_budget_adjustment_a = $d[0]['ID_budget_adjustment_a'];
                                    $dd = $this->m_master->caribasedprimary('db_budgeting.budget_adjustment','ID',$ID_budget_adjustment_a);
                                    $ID_budget_left_a = $dd[0]['ID_budget_left'];
                                    $dt_b = $this->m_pr_po->Get_DataBudgeting_by_ID_budget_left($ID_budget_left_a);
                                    $stMutasi .=  $dt_b[0]['NameHeadAccount'].'-'.$dt_b[0]['RealisasiPostName'].'('.$dt_b[0]['CodeDepartment'].')';
                                }

                                $__bp[0]['detail'] = $stMutasi;
                            }
                            else
                            {
                                $__bp[0]['detail'] = '';
                            }
                            $rs[$i]['bpd'] = $__bp;
                            $rs[$i]['Invoice'] = $__bp[0]['Invoice'];
                            
                         }
                     }
                    
                    echo json_encode($rs);
                }
                else
                {
                    // handling orang iseng
                    echo '{"status":"999","message":"Not Authorize"}';
                }
            }
            catch(Exception $e) {
                 // handling orang iseng
                 echo '{"status":"999","message":"Not Authorize"}';
            }
    }

    public function load_budget_onprocess_detail_byMonthYear()
    {
        try {
               $dataToken = $this->getInputToken2();
               $auth = $this->m_master->AuthAPI($dataToken);
               if ($auth) {
                   $this->load->model('budgeting/m_budgeting');
                   $this->load->model('budgeting/m_pr_po');
                   $ID_budget_left = $dataToken['ID_budget_left'];
                   $Year = $dataToken['Year'];
                   $Month = $dataToken['Month'];
                   $rs = array();
                   $sql = 'select * from (
                                select a.Type,a.Code,b.Perihal,a.Code_po_create,a.CreatedBy as CreatedPayment,c.Name as NameCreatedPayment,b.ID_payment,b.Invoice,a.CreatedAt from db_payment.payment as a
                                join 
                                    (
                                        select a.ID_payment,a.Perihal,sum(b.Invoice) as Invoice  from db_payment.spb as a
                                        join db_payment.spb_detail as b on a.ID = b.ID_spb 
                                        where b.ID_budget_left = '.$ID_budget_left.'
                                        group by a.ID_payment
                                       UNION 
                                       select a.ID_payment,a.Perihal,sum(b.Invoice) as Invoice from db_payment.bank_advance as a
                                       join db_payment.bank_advance_detail as b on a.ID = b.ID_bank_advance 
                                       where b.ID_budget_left = '.$ID_budget_left.' group by a.ID_payment
                                       UNION 
                                       select a.ID_payment,a.Perihal,sum(b.Invoice) as Invoice from db_payment.cash_advance  as a
                                       join db_payment.cash_advance_detail as b on a.ID = b.ID_cash_advance 
                                       where b.ID_budget_left = '.$ID_budget_left.' group by a.ID_payment
                                       #UNION 
                                       #select a.ID_payment,a.Perihal,sum(b.Invoice) as Invoice from db_payment.petty_cash 
                                       #as a
                                       #join db_payment.petty_cash_detail as b on a.ID = b.ID_petty_cash 
                                       #where b.ID_budget_left = '.$ID_budget_left.' group by a.ID_payment

                                    ) as b
                                    on a.ID = b.ID_payment
                                join db_employees.employees as c on a.CreatedBy = c.NIP
                                where b.ID_payment not in (
                                            select ap.ID_payment from db_budgeting.ap as ap
                                            join db_budgeting.budget_payment as bp on ap.ID = bp.ID_ap
                                            where bp.ID_budget_left = '.$ID_budget_left.' group by bp.ID_ap 
                                        )
                                       AND
                                       YEAR(a.CreatedAt) = '.$Year.' and MONTH(a.CreatedAt) = '.$Month.' 
                                UNION
                                select "Purchase Request",a.PRCode,"","",a.CreatedBy,c.Name,NULL, b.SubTotal,a.CreatedAt as Invoice from db_budgeting.pr_create as a
                                join (
                                    select d.ID as ID_payment,a.PRCode, a.SubTotal from db_budgeting.pr_detail as a
                                    left join db_purchasing.pre_po_detail as b on a.ID = b.ID_pr_detail
                                    left join db_purchasing.po_detail as c on b.ID = c.ID_pre_po_detail
                                    left join db_payment.payment as d on d.Code_po_create = c.Code
                                    where a.ID_budget_left = '.$ID_budget_left.' and a.Status = 1
                                    group by a.PRCode

                                ) as b
                                on a.PRCode = b.PRCode
                                join db_employees.employees as c on a.CreatedBy = c.NIP
                                left join (
                                            select ap.ID_payment from db_budgeting.ap as ap
                                            join db_budgeting.budget_payment as bp on ap.ID = bp.ID_ap
                                            where bp.ID_budget_left != '.$ID_budget_left.' group by bp.ID_ap 
                                        )   as ap on ap.ID_payment = b.ID_payment
                                where YEAR(a.CreatedAt) = '.$Year.' and MONTH(a.CreatedAt) = '.$Month.' 

                        ) cc order by  CreatedAt asc,Code asc       
                            
                        ';
                        // print_r($sql);die();
                    $query=$this->db->query($sql, array())->result_array();
                    $rs = $query;
                   echo json_encode($rs);
               }
               else
               {
                   // handling orang iseng
                   echo '{"status":"999","message":"Not Authorize"}';
               }
           }
           catch(Exception $e) {
                // handling orang iseng
                echo '{"status":"999","message":"Not Authorize"}';
           }
    }

    public function Supplier_DataIntable_server_side()
    {
        $action = $this->input->post('action');
        $condition = ' and a.Approval = 1';

        $requestData= $_REQUEST;
        $sql = 'select count(*) as total from db_purchasing.m_supplier as a where a.Active = 1 '.$condition;
        $query = $this->db->query($sql)->result_array();
        $totalData = $query[0]['total'];
        $No = $requestData['start'] + 1;

        $sql = 'select a.*,b.Name as NameCreated,c.CategoryName
                from db_purchasing.m_supplier as a 
                join db_employees.employees as b on a.CreatedBy = b.NIP
                join db_purchasing.m_categorysupplier as c on a.CategorySupplier = c.ID
               ';

        $sql.= ' where ( a.CodeSupplier LIKE "'.$requestData['search']['value'].'%" or a.NamaSupplier LIKE "%'.$requestData['search']['value'].'%" or a.PICName LIKE "'.$requestData['search']['value'].'%" or a.DetailInfo LIKE "%'.$requestData['search']['value'].'%" or c.CategoryName LIKE "'.$requestData['search']['value'].'%" or a.CategorySupplier LIKE "%'.$requestData['search']['value'].'%" or b.Name LIKE "%'.$requestData['search']['value'].'%" or a.DetailItem LIKE "%'.$requestData['search']['value'].'%"
                ) and a.Active = 1 and c.Active = 1'.$condition;
        $sql.= ' ORDER BY a.ID Desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
        $query = $this->db->query($sql)->result_array();

        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];
            $nestedData[] = $No;
             $nestedData[] = $row['CategoryName'];
            $nestedData[] = $row['CodeSupplier'];
            $nestedData[] = '<label>'.$row['NamaSupplier'].'</label><br>'.$row['Website'].'<br>'.'PIC : '.$row['PICName'].'<br>'.'Alamat : '.$row['Alamat'];
            $nestedData[] = 'Telp : '.$row['NoTelp'].' <br> Hp : '.$row['NoHp'];
            $DetailInfo = $row['DetailInfo'];
            $DetailInfo = json_decode($DetailInfo);
            $temp = '';
            if ($DetailInfo != "" || $DetailInfo != null) {
                $temp = '<ul>';
                foreach ($DetailInfo as $key => $value) {
                    $temp .= '<li>'.$key.' :  '.$value.'</li>';
                }

                $temp .= '</ul>';

            }

            $nestedData[] = $temp;
            $DetailItem = $row['DetailItem'];
            $DetailItem = json_decode($DetailItem);
            $temp = '';
            if ($DetailItem != "" || $DetailItem != null) {
                $temp = '<ul>';
                foreach ($DetailItem as $key => $value) {
                    $temp .= '<li>'.$key.' :  '.$value.'</li>';
                }

                $temp .= '</ul>';

            }
            $nestedData[] = $temp;

            if ($action == 'All_approval') {
                $btn = '<button type="button" class="btn btn-warning btn-edit btn-edit-supplier" code="'.$row['ID'].'"> <i class="fa fa-pencil-square-o" aria-hidden="true"></i> </button>&nbsp <button type="button" class="btn btn-danger btn-delete btn-delete-supplier" code="'.$row['ID'].'"> <i class="fa fa-trash" aria-hidden="true"></i> </button>';
            }
            elseif ($action == 'non_approval')
            {
                $btn = '<button type="button" class="btn btn-default btn-edit btn-approve-supplier" code="'.$row['ID'].'"> <i class="fa fa-handshake-o" aria-hidden="true"></i> Approve</button>';
            }
            else
            {
                $btn = '';
            }

            $nestedData[] = $row['NameCreated'];
            $nestedData[] = $btn;
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

    public function LoadTemplate_Budgeting()
    {
      try {
       $dataToken = $this->getInputToken2();
       $auth = $this->m_master->AuthAPI($dataToken);
        if ($auth) {
            $where = ' where b.Active = 1 and ( b.StartDate <= CURDATE() and b.EndDate >= CURDATE() )';
            if (array_key_exists('Active', $dataToken)) {
                $where = ' where b.Active = '.$dataToken['Active'];
            }
            $get = $this->m_master->caribasedprimary('db_budgeting.cfg_dateperiod','Activated',1);
            $Year = $get[0]['Year'];
            $where .= ($where != '') ? ' and b.Year = '.$Year : ' where b.Year = '.$Year ;

            $sql = 'select b.ID,a.Name,b.ID_m_template,b.StartDate,b.EndDate,b.JsonStatus as JsonStatusDefault,b.CreatedBy,c.Name as NameCreatedBy,b.CreatedAt,b.LastUpdatedBy,d.Name as LastUpdatedName,b.LastUpdatedAt
                from db_budgeting.m_template as a 
                join db_budgeting.t_template as b on a.ID = b.ID_m_template
                join db_employees.employees as c on b.CreatedBy = c.NIP
                left join db_employees.employees as d on b.LastUpdatedBy = d.NIP
                '.$where.'
                order by b.ID desc
            ';
            $query=$this->db->query($sql, array())->result_array(); 
            echo json_encode($query);
        }
       }
       catch(Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
       }
    }

    public function getNotification()
    {
        $requestData= $_REQUEST;

        $dataSearch = '';
        if( !empty($requestData['search']['value']) ) {
            $search = $requestData['search']['value'];
            $dataSearch = 'AND ( l.Title LIKE "%'.$search.'%" OR l.Description LIKE "%'.$search.'%"
                           OR l.CreatedName LIKE "%'.$search.'%"  OR l.CreatedBy LIKE "%'.$search.'%")';
        }

        $dataToken = $this->getInputToken2();
        $NIP = $dataToken['NIP'];
        if (array_key_exists('Alumni', $dataToken) && $dataToken['Alumni'] == 'yes') {
            $dataSearch .= ' And ( l.URLDirectAlumni != NULL or l.URLDirectAlumni != "" ) ';
        }
        $queryDefault = 'SELECT l.*,lu.StatusRead,lu.ShowNotif,lu.ID as ID_logging_user
                              FROM db_notifikasi.logging_user lu
                              LEFT JOIN db_notifikasi.logging l ON (l.ID = lu.IDLogging)
                              WHERE lu.UserID = "'.$NIP.'" '.$dataSearch.'
                              ORDER BY l.CreatedAt DESC ';
        // print_r($queryDefault);die();

        $sql = $queryDefault.' LIMIT '.$requestData['start'].','.$requestData['length'].' ';

        $query = $this->db->query($sql)->result_array();
        // $queryDefaultRow = $this->db->query($queryDefault)->result_array();
        $queryDefaultRow = $this->db->query(
                'select count(*) as total from (
                        SELECT 1
                        FROM db_notifikasi.logging_user lu
                        LEFT JOIN db_notifikasi.logging l ON (l.ID = lu.IDLogging)
                        WHERE lu.UserID = "'.$NIP.'" '.$dataSearch.'
                    ) ss'
        )->result_array()[0]['total'];

        $no = $requestData['start'] + 1;
        $data = array();

        for($i=0;$i<count($query);$i++) {
            $nestedData = array();
            $row = $query[$i];

            $URLDirect = $row['URLDirect'];
            // $urlDirect_lect = (in_array('14.6',$this->session->userdata('AllPosition'))
            //     || in_array('14.5',$this->session->userdata('AllPosition')))
            //     ? $row['URLDirectLecturerKaprodi']
            //     : $row['URLDirectLecturer'];

            // $urlDirect_lect = (
            // ($row['URLDirectLecturerKaprodi']==null || $row['URLDirectLecturerKaprodi']=='') &&
            // ($row['URLDirectLecturer']==null || $row['URLDirectLecturer']=='')
            // ) ? $row['URLDirect'] : base_url().''.$urlDirect_lect;

            // $loginPUIS = (
            //     ($row['URLDirectLecturerKaprodi']==null || $row['URLDirectLecturerKaprodi']=='') &&
            //     ($row['URLDirectLecturer']==null || $row['URLDirectLecturer']=='')
            // ) ? '1' : '0';
            $loginPUIS  = 1;

            $user = '<b>'.$row['CreatedName'].'</b>
                        <br/>
                        <a href="'.url_pas.$URLDirect.'" class="NotificationLinkRead" data-puis="'.$loginPUIS.'" data-href="'.$URLDirect.'" id_logging_user = "'.$row['ID_logging_user'].'">'.$row['Title'].'</a>
                        <p style="font-size: 12px;color: #9e9e9e;">'.$row['Description'].'</p>
                        <div style="text-align: right;font-size: 12px;color: #9e9e9e;">'.date('l, d M Y H:i:s',strtotime($row['CreatedAt'])).'</div>';

            $nestedData[] = '<div style="text-align:center;"><img src="'.$row['Icon'].'" style="width: 100%;max-width: 40px;border: 1px solid #FFFFFF;"></div>';
            $nestedData[] = '<div>'.$user.'</div>';
            $nestedData[] = $row['StatusRead'];
            $nestedData['data'] = $this->jwt->encode($row,"UAP)(*");
            $no++;

            $data[] = $nestedData;

        }

        $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            // "recordsTotal"    => intval(count($queryDefaultRow)),
            "recordsTotal"    => intval($queryDefaultRow),
            // "recordsFiltered" => intval( count($queryDefaultRow) ),
            "recordsFiltered" => intval($queryDefaultRow),
            "data"            => $data
        );

        echo json_encode($json_data);
    }

    public function get_data_kerja_sama_perguruan_tinggi()
    {
        try {
               $dataToken = $this->getInputToken2();
               $auth = $this->m_master->AuthAPI($dataToken);
               if ($auth) {
                $WhereFiltering = '';
                $WhereFiltering2 = '';
                $requestData = $_REQUEST;
                switch ($dataToken['mode']) {
                    case 'DataKerjaSama':
                         if (array_key_exists('SearchPerjanjian', $dataToken)) {
                             $SearchPerjanjian = $dataToken['SearchPerjanjian'];
                             // print_r($SearchPerjanjian);die();
                             if (count($SearchPerjanjian) > 0) {
                                 $FwhereAnd = ($WhereFiltering == '') ? ' where ' : ' And';
                                 $WhereFiltering .= $FwhereAnd.' ( b.Type ="'.$SearchPerjanjian[0].'" '; 
                                 for ($i=0; $i < count($SearchPerjanjian); $i++) { 
                                     $WhereFiltering .= ' or b.Type ="'.$SearchPerjanjian[$i].'" '; 
                                 }

                                 $WhereFiltering .= ')';
                             }
                         }

                         if (array_key_exists('SearchKategori', $dataToken)) {
                             $FwhereAnd = ($WhereFiltering == '') ? ' where ' : ' And';
                             if ($dataToken['SearchKategori'] != '' && $dataToken['SearchKategori'] != '%') {
                                 $WhereFiltering .= $FwhereAnd.' a.Kategori = "'.$dataToken['SearchKategori'].'" '; 
                             }
                             
                         }

                         if (array_key_exists('SearchTingkat', $dataToken)) {
                             $FwhereAnd = ($WhereFiltering == '') ? ' where ' : ' And';
                             if ($dataToken['SearchTingkat'] != '' && $dataToken['SearchTingkat'] != '%') {
                                 $WhereFiltering .= $FwhereAnd.' a.Tingkat = "'.$dataToken['SearchTingkat'].'" '; 
                             }
                         }

                         if (array_key_exists('StartDate', $dataToken) && array_key_exists('EndDate', $dataToken)) {
                             $FwhereAnd = ($WhereFiltering == '') ? ' where ' : ' And';
                             if ($dataToken['StartDate'] != '') {
                                 $WhereFiltering .= $FwhereAnd.' a.StartDate >= "'.$dataToken['StartDate'].'" and a.EndDate <= "'.$dataToken['EndDate'].'" '; 
                             }
                         }

                         if (array_key_exists('Active', $dataToken)) {
                             if ($dataToken['Active'] == 1) {
                                 $dd = ' a.EndDate >= "'.date('Y-m-d').'" ';
                             }
                             else
                             {
                                $dd = ' a.EndDate < "'.date('Y-m-d').'" ';
                             }
                             $FwhereAnd = ($WhereFiltering == '') ? ' where ' : ' And';
                             $WhereFiltering .= $FwhereAnd.$dd;
                         }

                         $sqltotalData = 'select count(*) as total from (

                         select count(*) as total from db_cooperation.kerjasama as a
                                                         join db_cooperation.k_perjanjian as b on a.ID = b.KerjasamaID
                                                         join db_cooperation.ker_department as c on a.ID = c.KerjasamaID
                                                         '.$WhereFiltering.'
                                     ';
                        $WhereORAnd = ($WhereFiltering == '') ? ' where ' : ' And'; 
                        $sqltotalData .= $WhereORAnd.' (           
                               a.Lembaga LIKE "'.$requestData['search']['value'].'%"
                          )';

                         $sqltotalData .= ' GROUP BY a.ID ) cc';                  
                         $querytotalData = $this->db->query($sqltotalData)->result_array();
                         $totalData = $querytotalData[0]['total'];

                         $sql = 'select b.KerjasamaID,a.Lembaga,a.Kategori,a.Tingkat,a.BuktiName,a.BuktiUpload,a.StartDate,a.EndDate,a.Desc,
                                 (
                                 select GROUP_CONCAT( CONCAT(Type,"--",Upload,"--",ID) ) from db_cooperation.k_perjanjian where KerjasamaID = a.ID group by KerjasamaID
                                 ) as Perjanjian,
                                 (
                                 select GROUP_CONCAT( CONCAT(z.Departement,"--",x.Code) ) from db_cooperation.ker_department as z
                                 join (
                                  select CONCAT("AC.",ID) as ID,  CONCAT("Study ",NameEng) as NameDepartement,Code as Code from db_academic.program_study where Status = 1
                                                 UNION
                                                 select CONCAT("NA.",ID) as ID, Division as NameDepartement,Abbreviation as Code from db_employees.division where StatusDiv = 1
                                                 UNION
                                                 select CONCAT("FT.",ID) as ID, CONCAT("Faculty ",NameEng) as NameDepartement,Abbr as Code from db_academic.faculty where StBudgeting = 1
                                 ) as x on z.Departement = x.ID
                                 where KerjasamaID = a.ID group by KerjasamaID
                                 ) as DepartmentKS
                                 from db_cooperation.kerjasama as a
                                 join db_cooperation.k_perjanjian as b on a.ID = b.KerjasamaID
                                 join db_cooperation.ker_department as c on a.ID = c.KerjasamaID
                                 '.$WhereFiltering;

                             $sql .= $WhereORAnd.' (           
                                    a.Lembaga LIKE "'.$requestData['search']['value'].'%"
                             )';
                             $sql .= ' GROUP BY a.ID';
                             $queryPass = $sql;
                             // encode query pass
                             $queryPass = $this->jwt->encode($queryPass,"UAP)(*"); 
                             // print_r($sql);die();
                             $sql.= ' ORDER BY a.ID desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
                             $query = $this->db->query($sql)->result_array();
                             $No = $requestData['start'] + 1;
                             $data = array();
                             for($i=0;$i<count($query);$i++){
                                 $nestedData=array();
                                 $row = $query[$i];
                                 $nestedData[] = $No;
                                 $nestedData[] = $row['Lembaga'].'<br/>'.'<div style = "color : red">Kategori : '.$row['Kategori'].'</div>'.'<div style = "color : red">Tingkat : '.$row['Tingkat'].'</div>';
                                 $nestedData[] = nl2br($row['BuktiName']).'--'.$row['BuktiUpload'];
                                 $nestedData[] = 'Start : '.$row['StartDate'].'<br/>End : '.$row['EndDate'];
                                 $nestedData[] = $row['Perjanjian'];
                                 $nestedData[] = $row['DepartmentKS'];
                                 $nestedData[] = $row['KerjasamaID'];

                                 // token to Pass Data to form
                                 $tokenEdit = [
                                      'KerjasamaID' =>  $row['KerjasamaID'],
                                      'Lembaga' => $row['Lembaga'],
                                      'Kategori' => $row['Kategori'],
                                      'Tingkat' => $row['Tingkat'],
                                      'BuktiName' => $row['BuktiName'],
                                      'BuktiUpload' => $row['BuktiUpload'],
                                      'StartDate' => $row['StartDate'],
                                      'EndDate' => $row['EndDate'],
                                      'Perjanjian' => $row['Perjanjian'],
                                      'DepartmentKS' => $row['DepartmentKS'],
                                      'Desc' => $row['Desc'],
                                 ];
                                 // encode data
                                 $tokenEdit = $this->jwt->encode($tokenEdit,"UAP)(*");
                                 $nestedData[] = $tokenEdit; 

                                 $data[] = $nestedData;
                                 $No++;
                             }

                             $json_data = array(
                                 "draw"            => intval( $requestData['draw'] ),
                                 "recordsTotal"    => intval($totalData),
                                 "recordsFiltered" => intval($totalData ),
                                 "data"            => $data,
                                 'queryPass'       => $queryPass,
                             );
                             echo json_encode($json_data);  
                        break;
                    case 'DataKegiatan':
                         if (array_key_exists('SearchPerjanjian', $dataToken)) {
                             $SearchPerjanjian = $dataToken['SearchPerjanjian'];
                             // print_r($SearchPerjanjian);die();
                             if (count($SearchPerjanjian) > 0) {
                                 $FwhereAnd = ($WhereFiltering2 == '') ? ' where ' : ' And';
                                 $WhereFiltering2 .= $FwhereAnd.' ( b.Type ="'.$SearchPerjanjian[0].'" '; 
                                 for ($i=0; $i < count($SearchPerjanjian); $i++) { 
                                     $WhereFiltering2 .= ' or b.Type ="'.$SearchPerjanjian[$i].'" '; 
                                 }

                                 $WhereFiltering2 .= ')';
                             }
                         }

                         if (array_key_exists('SearchKategori', $dataToken)) {
                             $FwhereAnd = ($WhereFiltering2 == '') ? ' where ' : ' And';
                             if ($dataToken['SearchKategori'] != '' && $dataToken['SearchKategori'] != '%') {
                                 $WhereFiltering2 .= $FwhereAnd.' a.Kategori = "'.$dataToken['SearchKategori'].'" '; 
                             }
                             
                         }

                         if (array_key_exists('SearchTingkat', $dataToken)) {
                             $FwhereAnd = ($WhereFiltering2 == '') ? ' where ' : ' And';
                             if ($dataToken['SearchTingkat'] != '' && $dataToken['SearchTingkat'] != '%') {
                                 $WhereFiltering2 .= $FwhereAnd.' a.Tingkat = "'.$dataToken['SearchTingkat'].'" '; 
                             }
                         }

                         if (array_key_exists('StartDate', $dataToken) && array_key_exists('EndDate', $dataToken)) {
                             $FwhereAnd = ($WhereFiltering == '') ? ' where ' : ' And';
                             if ($dataToken['StartDate'] != '') {
                                 $WhereFiltering .= $FwhereAnd.' z.StartDate >= "'.$dataToken['StartDate'].'" and z.EndDate <= "'.$dataToken['EndDate'].'" '; 
                             }
                         }

                         if (array_key_exists('SearchKategoriKegiatan', $dataToken)) {
                             $FwhereAnd = ($WhereFiltering == '') ? ' where ' : ' And';
                             if ($dataToken['SearchKategoriKegiatan'] != '' && $dataToken['SearchKategoriKegiatan'] != '%') {
                                 $WhereFiltering .= $FwhereAnd.' z.Kategori_kegiatan="'.$dataToken['SearchKategoriKegiatan'].'" '; 
                             }
                             
                         }

                         if (array_key_exists('Active', $dataToken)) {
                             if ($dataToken['Active'] == 1) {
                                 $dd = ' a.EndDate >= "'.date('Y-m-d').'" ';
                             }
                             else
                             {
                                $dd = ' a.EndDate < "'.date('Y-m-d').'" ';
                             }
                             $FwhereAnd = ($WhereFiltering2 == '') ? ' where ' : ' And';
                             $WhereFiltering2 .= $FwhereAnd.$dd;
                         }

                         if (array_key_exists('SearchLembaga', $dataToken)) {
                             $FwhereAnd = ($WhereFiltering2 == '') ? ' where ' : ' And';
                             if ($dataToken['SearchLembaga'] != '' && $dataToken['SearchLembaga'] != '%') {
                                 $WhereFiltering2 .= $FwhereAnd.' a.ID = "'.$dataToken['SearchLembaga'].'" '; 
                             }
                             
                         }

                         $sqltotalData = '
                            select count(*) as total from(
                                select z.ID,z.JudulKegiatan,z.BentukKegiatan,z.ManfaatKegiatan,z.StartDate,z.EndDate,z.Kategori_kegiatan,z.FileLain from db_cooperation.kegiatan as z
                                join (
                                    select a.ID from db_cooperation.kerjasama as a
                                     join db_cooperation.k_perjanjian as b on a.ID = b.KerjasamaID
                                     join db_cooperation.ker_department as c on a.ID = c.KerjasamaID
                                    '.$WhereFiltering2.' GROUP BY a.ID
                                ) as x
                                on z.KerjasamaID = x.ID
                            ) as z
                            ';

                        $WhereORAnd = ($WhereFiltering == '') ? ' where ' : ' And'; 
                        $sqltotalData .= $WhereFiltering.$WhereORAnd.' (           
                               z.JudulKegiatan LIKE "'.$requestData['search']['value'].'%" or
                               z.BentukKegiatan LIKE "'.$requestData['search']['value'].'%" or
                               z.ManfaatKegiatan LIKE "'.$requestData['search']['value'].'%"
                          )';
                            
                         $querytotalData = $this->db->query($sqltotalData)->result_array();
                         $totalData = $querytotalData[0]['total'];

                         $sql = '
                                    select z.ID,z.JudulKegiatan,z.BentukKegiatan,z.ManfaatKegiatan,z.StartDate,z.EndDate,z.Desc,z.KerjasamaID,z.SemesterID,
                                    z.Kategori_kegiatan,z.FileLain,
                                    x.Lembaga, x.Kategori,x.Tingkat,
                                    (
                                    select GROUP_CONCAT( CONCAT(zz.Departement,"--",x.Code) ) from db_cooperation.keg_department as zz
                                    join (
                                     select CONCAT("AC.",ID) as ID,  CONCAT("Study ",NameEng) as NameDepartement,Code as Code from db_academic.program_study where Status = 1
                                                    UNION
                                                    select CONCAT("NA.",ID) as ID, Division as NameDepartement,Abbreviation as Code from db_employees.division where StatusDiv = 1
                                                    UNION
                                                    select CONCAT("FT.",ID) as ID, CONCAT("Faculty ",NameEng) as NameDepartement,Abbr as Code from db_academic.faculty where StBudgeting = 1
                                    ) as x on zz.Departement = x.ID
                                    where KegiatanID = z.ID group by KegiatanID
                                    ) as DepartmentKS
                                    from db_cooperation.kegiatan as z
                                    join (
                                        select b.KerjasamaID,a.Lembaga,a.Kategori,a.Tingkat,a.BuktiName,a.BuktiUpload,a.StartDate,a.EndDate,a.Desc,
                                             (
                                             select GROUP_CONCAT( CONCAT(Type,"--",Upload,"--",ID) ) from db_cooperation.k_perjanjian where KerjasamaID = a.ID group by KerjasamaID
                                             ) as Perjanjian,
                                             (
                                             select GROUP_CONCAT( CONCAT(zz.Departement,"--",x.Code) ) from db_cooperation.ker_department as zz
                                             join (
                                              select CONCAT("AC.",ID) as ID,  CONCAT("Study ",NameEng) as NameDepartement,Code as Code from db_academic.program_study where Status = 1
                                                             UNION
                                                             select CONCAT("NA.",ID) as ID, Division as NameDepartement,Abbreviation as Code from db_employees.division where StatusDiv = 1
                                                             UNION
                                                             select CONCAT("FT.",ID) as ID, CONCAT("Faculty ",NameEng) as NameDepartement,Abbr as Code from db_academic.faculty where StBudgeting = 1
                                             ) as x on zz.Departement = x.ID
                                             where KerjasamaID = a.ID group by KerjasamaID
                                             ) as DepartmentKS
                                             from db_cooperation.kerjasama as a
                                             join db_cooperation.k_perjanjian as b on a.ID = b.KerjasamaID
                                             join db_cooperation.ker_department as c on a.ID = c.KerjasamaID
                                             '.$WhereFiltering2.' GROUP BY a.ID

                                    ) as x
                                    on x.KerjasamaID = z.KerjasamaID
                                ';
                         $queryPass = $sql;
                         $queryPass = $this->jwt->encode($queryPass,"UAP)(*"); 

                         $WhereORAnd = ($WhereFiltering == '') ? ' where ' : ' And'; 
                         $sql .= $WhereFiltering.$WhereORAnd.' (           
                                z.JudulKegiatan LIKE "'.$requestData['search']['value'].'%" or
                                z.BentukKegiatan LIKE "'.$requestData['search']['value'].'%" or
                                z.ManfaatKegiatan LIKE "'.$requestData['search']['value'].'%"
                           )';

                             $sql.= ' ORDER BY z.ID desc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
                             $query = $this->db->query($sql)->result_array();
                             $No = $requestData['start'] + 1;
                             $data = array();
                             for($i=0;$i<count($query);$i++){
                                 $nestedData=array();
                                 $row = $query[$i];
                                 $nestedData[] = $No;
                                 $TokenLinkSearch = $this->jwt->encode($row['Lembaga'],"UAP)(*");
                                 //$nestedData[] = '<a href="'.base_url().'cooperation/kerjasama-perguruan-tinggi/master/'.$TokenLinkSearch.'">'.$row['Lembaga'].'</a><br/>'.'<div style = "color : red">Kategori : '.$row['Kategori'].'</div>'.'<div style = "color : red">Tingkat : '.$row['Tingkat'].'</div>';
                                 $nestedData[] = '<a href="javascript:void(0)">'.$row['Lembaga'].'</a><br/>'.'<div style = "color : red">Kategori : '.$row['Kategori'].'</div>'.'<div style = "color : red">Tingkat : '.$row['Tingkat'].'</div>';
                                 $nestedData[] = $row['JudulKegiatan'].'<br/>'.'<div style = "color : red">Kategori : '.$row['Kategori_kegiatan'].'</div>';
                                 $nestedData[] = $row['BentukKegiatan'];
                                 $nestedData[] = $row['ManfaatKegiatan'];
                                 $nestedData[] = 'Start : '.$row['StartDate'].'<br/>End : '.$row['EndDate'];
                                 $nestedData[] = $row['DepartmentKS'];
                                 $nestedData[] = $row['KerjasamaID'];
                                 $nestedData[] = $row['ID'];
                                 // token to Pass Data to form
                                 $tokenEdit = [
                                      'KerjasamaID' =>  $row['KerjasamaID'],
                                      'Lembaga' => $row['Lembaga'],
                                      'JudulKegiatan' => $row['JudulKegiatan'],
                                      'BentukKegiatan' => $row['BentukKegiatan'],
                                      'ManfaatKegiatan' => $row['ManfaatKegiatan'],
                                      'StartDate' => $row['StartDate'],
                                      'EndDate' => $row['EndDate'],
                                      'DepartmentKS' => $row['DepartmentKS'],
                                      'Desc' => $row['Desc'],
                                      'ID' => $row['ID'],
                                      'SemesterID' => $row['SemesterID'],
                                      'Kategori_kegiatan' => $row['Kategori_kegiatan'],
                                      'FileLain' => $row['FileLain'],
                                 ];
                                 // encode data
                                 $tokenEdit = $this->jwt->encode($tokenEdit,"UAP)(*");
                                 $nestedData[] = $tokenEdit; 

                                 $data[] = $nestedData;
                                 $No++;
                             }

                             $json_data = array(
                                 "draw"            => intval( $requestData['draw'] ),
                                 "recordsTotal"    => intval($totalData),
                                 "recordsFiltered" => intval($totalData ),
                                 "data"            => $data,
                                 'queryPass'       => $queryPass,
                             );
                             echo json_encode($json_data);
                        break;
                    case 'DataKerjaSamaAggregator':

                    $WhereFiltering = '';
                    // filter year
                    if (array_key_exists('filterYear', $dataToken)) {
                        $Year = date('Y');
                        if ($dataToken['filterYear'] == -3 || $dataToken['filterYear'] == '-3') {
                            $Year3 = $Year - 2;
                            // $WhereFiltering =  'where Year(x.EndDate) >= '.$Year3;
                            $WhereFiltering =  'where Year(x.EndDate) >= '.$Year3.' and Year(x.EndDate) <= '.$Year;
                        }
                        elseif ($dataToken['filterYear'] == -5 || $dataToken['filterYear'] == '-5') {
                           $Year3 = $Year - 4;
                           // $WhereFiltering =  'where Year(x.EndDate) >= '.$Year3;
                           $WhereFiltering =  'where Year(x.EndDate) >= '.$Year3.' and Year(x.EndDate) <= '.$Year;
                        }
                        elseif ($dataToken['filterYear'] > 0) {
                            $Year3 = $dataToken['filterYear'];
                            $WhereFiltering =  'where Year(x.EndDate) = '.$Year3;
                        }

                        // print_r($WhereFiltering);
                        // print_r($dataToken);die();
                    }

                        
                        $sqltotalData = 'select count(*) as total from (
                                    select z.ID,z.JudulKegiatan,z.BentukKegiatan,z.ManfaatKegiatan,z.Kategori_kegiatan,x.Lembaga,x.EndDate,yy.Departement
                                    from db_cooperation.kegiatan as z
                                        join db_cooperation.kerjasama as x on z.KerjasamaID = x.ID
                                        join db_cooperation.keg_department as yy on z.ID = yy.KegiatanID
                            ';
                        
                        if (array_key_exists('ProdiID', $dataToken)) {
                            $WhereORAnd = ($WhereFiltering == '') ? ' where ' : ' And';
                            $WhereFiltering .= $WhereORAnd.' yy.Departement = "AC.'.$dataToken['ProdiID'].'" ';
                            // $sqltotalData .= $WhereFiltering;  
                        }
                        if (array_key_exists('Kategori_kegiatan', $dataToken)) {
                            $WhereORAnd = ($WhereFiltering == '') ? ' where ' : ' And';
                            if ($dataToken['Kategori_kegiatan'] != '%' && $dataToken['Kategori_kegiatan'] != '') {
                                 $WhereFiltering .= $WhereORAnd.' z.Kategori_kegiatan = "'.$dataToken['Kategori_kegiatan'].'" ';
                            }
                            // $sqltotalData .= $WhereFiltering;  
                        }
                        $sqltotalData .= $WhereFiltering;  
                        $WhereORAnd = ($WhereFiltering == '') ? ' where ' : ' And'; 
                        $sqltotalData .= $WhereORAnd.' (           
                               z.JudulKegiatan LIKE "'.$requestData['search']['value'].'%" or
                               z.BentukKegiatan LIKE "'.$requestData['search']['value'].'%" or
                               z.ManfaatKegiatan LIKE "'.$requestData['search']['value'].'%" or
                               x.Lembaga LIKE "'.$requestData['search']['value'].'%"
                          ) group by z.ID ) xx';
                          // print_r($sqltotalData);die();
                        $querytotalData = $this->db->query($sqltotalData)->result_array();
                        $totalData = $querytotalData[0]['total'];

                        $sql = 'select z.ID,z.JudulKegiatan,z.BentukKegiatan,z.ManfaatKegiatan,z.StartDate,z.EndDate as keg_enddate,x.EndDate,z.Kategori_kegiatan,x.Lembaga,z.KerjasamaID,
                                if(x.Tingkat = "Nasional",1,0) as Nasional,if(x.Tingkat = "Internasional",1,0) as Internasional,
                                if(x.Tingkat= "Lokal",1,0) as Lokal,x.BuktiName,x.BuktiUpload,z.SemesterID,y.Name as SemesterName,x.Kategori
                                from db_cooperation.kegiatan as z
                                join (
                                    select b.KerjasamaID,a.Lembaga,a.Kategori,a.Tingkat,a.BuktiName,a.BuktiUpload,a.StartDate,a.EndDate,a.Desc,
                                         (
                                         select GROUP_CONCAT( CONCAT(Type,"--",Upload,"--",ID) ) from db_cooperation.k_perjanjian where KerjasamaID = a.ID group by KerjasamaID
                                         ) as Perjanjian,
                                         (
                                         select GROUP_CONCAT( CONCAT(zz.Departement,"--",x.Code) ) from db_cooperation.ker_department as zz
                                         join (
                                          select CONCAT("AC.",ID) as ID,  CONCAT("Study ",NameEng) as NameDepartement,Code as Code from db_academic.program_study where Status = 1
                                                         UNION
                                                         select CONCAT("NA.",ID) as ID, Division as NameDepartement,Abbreviation as Code from db_employees.division where StatusDiv = 1
                                                         UNION
                                                         select CONCAT("FT.",ID) as ID, CONCAT("Faculty ",NameEng) as NameDepartement,Abbr as Code from db_academic.faculty where StBudgeting = 1
                                         ) as x on zz.Departement = x.ID
                                         where KerjasamaID = a.ID group by KerjasamaID
                                         ) as DepartmentKS
                                         from db_cooperation.kerjasama as a
                                         join db_cooperation.k_perjanjian as b on a.ID = b.KerjasamaID
                                         join db_cooperation.ker_department as c on a.ID = c.KerjasamaID
                                         GROUP BY a.ID

                                ) as x
                                on x.KerjasamaID = z.KerjasamaID
                                join db_academic.semester as y on z.SemesterID = y.ID
                                join db_cooperation.keg_department as yy on z.ID = yy.KegiatanID
                        ';
                        $sql .= $WhereFiltering;   
                        $queryPass = $sql;
                        $queryPass = $this->jwt->encode($queryPass,"UAP)(*"); 
                        $WhereORAnd = ($WhereFiltering == '') ? ' where ' : ' And'; 
                        $sql .= $WhereORAnd.' (           
                               z.JudulKegiatan LIKE "'.$requestData['search']['value'].'%" or
                               z.BentukKegiatan LIKE "'.$requestData['search']['value'].'%" or
                               z.ManfaatKegiatan LIKE "'.$requestData['search']['value'].'%" or
                               x.Lembaga LIKE "'.$requestData['search']['value'].'%"
                          )';
                      $sql.= ' group by z.ID ORDER BY z.StartDate asc LIMIT '.$requestData['start'].' , '.$requestData['length'].' ';
                      $query = $this->db->query($sql)->result_array();
                      //print_r($this->db->last_query());die();
                      $No = $requestData['start'] + 1;
                      $data = array();
                      for($i=0;$i<count($query);$i++){
                          $nestedData=array();
                          $row = $query[$i];
                          $nestedData[] = $No;
                          $nestedData[] = $row['Lembaga'];
                          $nestedData[] = $row['Internasional'];
                          $nestedData[] = $row['Nasional'];
                          $nestedData[] = $row['Lokal'];
                          $nestedData[] = $row['BentukKegiatan'];
                          $nestedData[] = nl2br($row['BuktiName']);
                          $nestedData[] = $row['BuktiUpload'];
                          $nestedData[] = $this->m_master->getDateIndonesian($row['EndDate']);
                          $nestedData[] = $row['SemesterName'];
                          $nestedData[] = $row['JudulKegiatan'];
                          $nestedData[] = $row['ManfaatKegiatan'];
                          $nestedData[] = $this->m_master->getDateIndonesian($row['StartDate']);
                          // $nestedData[] = $row['Kategori'];
                          $nestedData[] = $row['Kategori_kegiatan'];
                          $nestedData[] = $row['KerjasamaID'];
                          $nestedData[] = $row['ID'];
                          $Durasi = $this->m_master->dateDiffDays ($row['StartDate'], $row['EndDate']);
                          $nestedData[] = $Durasi;
                          $data[] = $nestedData;
                          $No++;
                      }

                      $json_data = array(
                          "draw"            => intval( $requestData['draw'] ),
                          "recordsTotal"    => intval($totalData),
                          "recordsFiltered" => intval($totalData ),
                          "data"            => $data,
                          'queryPass'       => $queryPass,
                      );
                      echo json_encode($json_data);
                        break;
                    default:
                        # code...
                        break;
                }
                  
               }
               else
               {
                   // handling orang iseng
                   echo '{"status":"999","message":"Not Authorize"}';
               }
        }
       catch(Exception $e) {
            // handling orang iseng
            echo '{"status":"999","message":"Not Authorize"}';
       }
    }

    public function get_data_formulir_no_ref()
    {
    	$dataToken = $this->getInputToken2();
    	$Status = $dataToken['Status'];
    	$Year = $dataToken['Year'];
        $AndWhere = '';
        if (array_key_exists('TypeFormulir', $dataToken)) {
           $AndWhere .= ' and TypeFormulir = "'.$dataToken['TypeFormulir'].'" ';
        }
    	$sql = 'select * from db_admission.formulir_number_global  where Status = ? and Years = ?'.$AndWhere;
    	$query=$this->db->query($sql, array($Status,$Year))->result_array();

    	echo json_encode($query);
    }
}
