<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_finance extends CI_Model {

   private $data = array();
   function __construct()
   {
       parent::__construct();
   }

   public function getEmailnURLCheckbox($arr,$delimiter)
   {
    $this->load->library('JWT');
    $key = "UAP)(*";
    $arr_temp = array();
    for ($i=0; $i < count($arr); $i++) {
      $temp = explode($delimiter, $arr[$i]);
      $url = $this->jwt->encode($temp[0].";".$temp[2],$key);
      $arr_temp[] = array('email'=>$temp[2],'url' => $url);
    }
    return $arr_temp;
   }

   public function ProcessSaveDataVerification($arrData)
   {
      $arrData = explode(",", $arrData);
      for ($i=0; $i < count($arrData); $i++) {
        $temp = explode(";", $arrData[$i]);
        if ($temp[0] != 'nothing') {
          if ($temp[1] == 'null') {
            // insert data ke db register_verification
            $this->saveData_register_verification($temp[0]);
            $this->load->model('master/m_master');
            $query = $this->m_master->caribasedprimary('db_admission.register_verification','RegisterID',$temp[0]);
            $id_register_verification = $query[0]['ID'];
            $this->saveDataRegisterVerified($id_register_verification);
          }
          else
          {
            // dapatkan id register verification dahulu
            $this->load->model('master/m_master');
            $query = $this->m_master->caribasedprimary('db_admission.register_verification','RegisterID',$temp[0]);
            $id_register_verification = $query[0]['ID'];
            $this->saveDataRegisterVerified($id_register_verification);
          }
        }
      }
   }

   public function saveData_register_verification($registerID)
   {
    $dataSave = array(
            'RegisterID' => $registerID,
                    );

    $this->db->insert('db_admission.register_verification', $dataSave);
   }

   public function saveDataRegisterVerified($register_verified)
   {
    $getFormulirCode = $this->getFormulirCode('online');
    $dataSave = array(
            'RegVerificationID' => $register_verified,
            'FormulirCode' => $getFormulirCode,
            'VerificationBY' => $this->session->userdata('NIP'),
            'VerificationAT' => date('Y-m-d H:i:s'),
                    );
    $this->db->insert('db_admission.register_verified', $dataSave);
   }

   public function getFormulirCode($tipeFormulirCode = null,$year = null)
    {
      // get Year
      $this->load->model('master/m_master');
      if ($year == null) {
        $G_ta = $this->m_master->showData_array('db_admission.set_ta');
        $year = $G_ta[0]['Ta'];
      }

     if ($tipeFormulirCode == 'online') { // online
         $sql = "select FormulirCode from db_admission.formulir_number_online_m where Status = 0 and Years ='".$year."' order by ID asc limit 1";
     }
     else{
       $sql = "select FormulirCode from db_admission.formulir_number_offline_m where Status = 0 and Years ='".$year."' order by ID asc limit 1";
     }
     $query=$this->db->query($sql, array())->result_array();
     $FormulirCode = $query[0]['FormulirCode'];

     if ($tipeFormulirCode == 'online') { // online
       $this->updateStatusFormulirCodeOnline($FormulirCode,$year);
     }
     else
     {
       $this->updateStatusFormulirCodeOffline($FormulirCode);
     }

     return $FormulirCode;
    }

   public function updateStatusFormulirCodeOnline($FormulirCode,$year = null)
   {
    // $sql = "update db_admission.formulir_number_online_m set Status = 1 where FormulirCode = '".$FormulirCode."'";
    // $query=$this->db->query($sql, array()); // tidak tau fungsi ini untuk apa

    // link ke formulir global
      if ($year != null) {
        $sql = "select FormulirCodeGlobal from db_admission.formulir_number_global where Status = 0 and Years ='".$year."' 
                and TypeFormulir = 'On'
        order by ID asc limit 1";
        $query=$this->db->query($sql, array())->result_array();
        $FormulirCodeGlobal = $query[0]['FormulirCodeGlobal'];

        // get no kwitansi
        // $NoKwitansi = 'ON-0001';
        /* Note  
          ON : Fixed,
          0001 : Increment
        */
        $sqlKwitansi = 'select NoKwitansi from db_admission.formulir_number_online_m where NoKwitansi is not null and NoKwitansi != 0
                        and Years = "'.$year.'"
                        order by NoKwitansi desc,ID Desc  
                        Limit 1
                        ';
        $queryKwitansi=$this->db->query($sqlKwitansi, array())->result_array();
        $in = 1;
        if ( count($queryKwitansi) > 0 ) {
          $in = $queryKwitansi[0]['NoKwitansi'];
          $in = $in+1;
        }
        else{
          $sqlKwitansi2 = 'select NoKwitansi from db_admission.formulir_number_online_m where Status = 1
                        and Years ="'.$year.'"
                        order by ID Desc  
                        ';
          $queryKwitansi2=$this->db->query($sqlKwitansi2, array())->result_array();

          $in = count($queryKwitansi2);
          $in = $in+1;
        }

        // update No_Ref pada formulir number online dengan FormulirCodeGlobal
        $dataSave = array(
          'No_Ref' => $FormulirCodeGlobal,
          'Status' => 1,
          'NoKwitansi' => $in,
        );

        $this->db->where('FormulirCode',$FormulirCode);
        $this->db->update('db_admission.formulir_number_online_m',$dataSave);

        // update status pada formulir_number_global
           $sql = "update db_admission.formulir_number_global set Status = 1 where FormulirCodeGlobal = '".$FormulirCodeGlobal."'";
           $query=$this->db->query($sql, array());
      }

   }

   public function updateStatusFormulirCodeOffline($FormulirCode)
   {
    $sql = "update db_admission.formulir_number_offline_m set Status = 1 where FormulirCode = '".$FormulirCode."'";
    $query=$this->db->query($sql, array());
   }

   public function loadData_calon_mahasiswa_created($limit, $start,$Nama,$selectProgramStudy,$Sekolah)
   {
     $arr_temp = array('data' => array());
     if($Nama != '%') {
         $Nama = '"%'.$Nama.'%"';
     }
     else
     {
       $Nama = '"%"';
     }

     if($selectProgramStudy != '%') {
       $selectProgramStudy = '"%'.$selectProgramStudy.'%"';
     }
     else
     {
       $selectProgramStudy = '"%"';
     }

     if($Sekolah != '%') {
       $Sekolah = '"%'.$Sekolah.'%"';
     }
     else
     {
       $Sekolah = '"%"';
     }

       $sql = 'select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,g.JenisTempatTinggal,
           h.ctr_name as CountryAddress,i.ProvinceName as ProvinceAddress,j.RegionName as RegionAddress,k.DistrictName as DistrictsAddress,
           a.District as DistrictAddress,a.Address,a.ZipCode,a.PhoneNumber,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
           n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto
           from db_admission.register_formulir as a
           JOIN db_admission.register_verified as b
           ON a.ID_register_verified = b.ID
           JOIN db_admission.register_verification as c
           ON b.RegVerificationID = c.ID
           JOIN db_admission.register as d
           ON c.RegisterID = d.ID
           JOIN db_admission.country as e
           ON a.NationalityID = e.ctr_code
           JOIN db_employees.religion as f
           ON a.ReligionID = f.IDReligion
           JOIN db_admission.register_jtinggal_m as g
           ON a.ID_register_jtinggal_m = g.ID
           JOIN db_admission.country as h
           ON a.ID_country_address = h.ctr_code
           JOIN db_admission.province as i
           ON a.ID_province = i.ProvinceID
           JOIN db_admission.region as j
           ON a.ID_region = j.RegionID
           JOIN db_admission.district as k
           ON a.ID_districts = k.DistrictID
           JOIN db_admission.school_type as l
           ON l.sct_code = a.ID_school_type
           JOIN db_admission.register_major_school as m
           ON m.ID = a.ID_register_major_school
           JOIN db_admission.school as n
           ON n.ID = d.SchoolID
           join db_academic.program_study as o
           on o.ID = a.ID_program_study
           where d.Name like '.$Nama.' and d.SchoolID like '.$Sekolah.' and a.ID_program_study like '.$selectProgramStudy.' and a.ID in (select ID_register_formulir from db_admission.register_nilai where Status = "Verified") LIMIT '.$start. ', '.$limit;
          $query=$this->db->query($sql, array())->result_array();
          return $query;
   }

   public function getVerified_Nilai($ID_register_formulir)
   {
    $sql = 'select a.*,b.Name from db_admission.register_nilai as a
            join db_employees.employees as b
            on b.NIP = a.VerifiedBY
            where a.ID_register_formulir = ? limit 1';
    $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
    return $query;
   }

   public function submit_approved_nilai_rapor($input)
   {
    for ($i=0; $i < count($input); $i++) {
      $dataSave = array(
              'ApprovedBY' => $this->session->userdata('NIP'),
              'Status' => 'Approved',
              'ApprovedAT' => date('Y-m-d'),
                      );
      $this->db->where('ID_register_formulir',$input[$i]);
      $this->db->update('db_admission.register_nilai', $dataSave);
    }
   }

   public function set_tuition_fee_approve($input)
   {
    for ($i=0; $i < count($input); $i++) {
      $dataSave = array(
              'ApprovedBY' => $this->session->userdata('NIP'),
              'Status' => 'Approved',
              'ApprovedAT' => date('Y-m-d'),
                      );
      $this->db->where('ID_register_formulir',$input[$i]);
      $this->db->update('db_finance.payment_register', $dataSave);
      // generate pdf
      $this->PDF_tuition_fee_approved($input[$i]);
    }
   }

   public function update_payment_pre($BilingID)
   {
      $dataSave = array(
              'Status' => 1,
              'UpdateAt' => date('Y-m-d H:i:s'),
              'DatePayment' => date('Y-m-d H:i:s'),
                      );
      $this->db->where('BilingID',$BilingID);
      $this->db->update('db_finance.payment_pre', $dataSave);
   }

   public function proses_cicilan($ID_register_formulir,$data_register)
   {
    $msg = 'Your Payment is complete.';
    $checkCicilan = $this->checkCicilan($ID_register_formulir,0);
    if (count($checkCicilan) > 0) {
      $Invoice = $checkCicilan[0]['Invoice'];
      $Deadline = $checkCicilan[0]['Deadline'];
      $ID = $checkCicilan[0]['ID'];
      $p = $this->create_va_Payment($Invoice,$Deadline, $data_register[0]['Name'], $data_register[0]['Email'],$data_register[0]['VA_number']);
      $this->updateCicilanBiling($p,$ID);
      $msg = 'Please continue to pay the next installment with VA Number : '.$data_register[0]['VA_number']. ' <br> as much as Rp '.number_format($checkCicilan[0]['Invoice'],2,',','.');
    }

    return $msg;

   }

   public function updateCicilanBiling($data,$ID)
   {
      $dataSave = array(
              'BilingID' => $data['msg']['trx_id'],
                      );
      $this->db->where('ID',$ID);
      $this->db->update('db_finance.payment_pre', $dataSave);
   }

   public function checkCicilan($ID_register_formulir,$status)
   {
    $sql = 'select * from db_finance.payment_pre where ID_register_formulir = ? and Status = ? order by ID asc limit 1';
    $query=$this->db->query($sql, array($ID_register_formulir,$status))->result_array();
    return $query;
   }

   public function checkPayment_admisi($ID_register_formulir)
   {
    $sql = 'select * from db_finance.payment_pre where ID_register_formulir = ? order by ID asc';
    $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
    // for ($i=0; $i < count($query); $i++) { 
    //   $query[$i]['Refund'] = $this->m_master->caribasedprimary('db_finance.register_refund','ID_register_formulir',$query[$i]['ID_register_formulir']);
    // }
    if (count($query) > 0) {
      $query[0]['Refund'] = $this->db->query(
          'select rr.*,emp.Name as NameEMP from  db_finance.register_refund as rr join db_employees.employees as emp on emp.NIP = rr.UpdateBy
          where ID_register_formulir = '.$query[0]['ID_register_formulir'].'
           '
      )->result_array();
    }
    
    return $query;
   }

   public function checkPayment_admisi2($ID_register_formulir)
   {
    $arr_result = array();
    $sql = 'select * from db_finance.payment_pre where ID_register_formulir = ? order by ID asc';
    $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
    $this->load->model('admission/m_admission');
    $dataTuitionFee =  $this->m_admission->tuitionFeeIntake_ALL($ID_register_formulir);
    $getFormulirCode = $this->m_admission->getDataPersonal($ID_register_formulir);
    $FormulirCode = $getFormulirCode[0]['FormulirCode'];
    // find formulir code pada db_admission.to_be_mhs
    $this->load->model('master/m_master');
    $get = $this->m_master->caribasedprimary('db_admission.to_be_mhs','FormulirCode',$FormulirCode);
    $RefundChk = $this->db->query(
          'select rr.*,emp.Name as NameEMP from  db_finance.register_refund as rr join db_employees.employees as emp on emp.NIP = rr.UpdateBy
          where ID_register_formulir = '.$ID_register_formulir.'
           '
      )->result_array();
    if (count($get) > 0) {
      $arr_result = array('data' => $query,'action' => 0,'dataRefund' => $RefundChk); // already generate
    }
    else
    {
      if (count($RefundChk) > 0) {
        $arr_result = array('data' => $query,'action' => 2,'dataRefund' => $RefundChk); // refund
      }
      else
      {
        $arr_result = array('data' => $query,'action' => 1,'dataRefund' => $RefundChk); 
      }

    }

    $arr_result['dataTuitionFee'] = $dataTuitionFee;

    return $arr_result;
   }

   public function create_va_Payment($payment = null,$DeadLinePayment = null, $Name = null, $Email = null,$VA_number = null,$description = 'Pembayaran Uang Kuliah',$tableRoutes = 'db_finance.payment_pre')
   {
       $arr = array();
       $arr['status'] = false;
       $arr['msg'] = '';
       if ($payment != null) {
           include_once APPPATH.'third_party/bni/BniEnc.php';
           // FROM BNI
           $this->load->model('master/m_master');
           //$aa = $this->m_master->showData_array('db_va.cfg_bank');
            $client_id = VA_client_id;
           // $client_id = $aa[0]['client_id'];
            $secret_key = VA_secret_key;
           // $secret_key = $aa[0]['secret_key'];
            $url = VA_url;
           // $url = $aa[0]['url'];
           $getVANumber = $VA_number;
           $datetime_expired = $DeadLinePayment;
           // $payment = str_replace('.', '', $payment);

           if ($getVANumber != null) {
               $data_asli = array(
                   'client_id' => $client_id,
                   'trx_id' => mt_rand(), // fill with Billing ID
                   'trx_amount' => $payment,
                   'billing_type' => 'c',
                   'datetime_expired' => $datetime_expired, // billing will be expired in 2 hours
                   'virtual_account' => $getVANumber,
                   'customer_name' => $Name,
                   'customer_email' => $Email,
                   'customer_phone' => '+622129200456',
                   'description' => $description,
                   'type' => 'createbilling',
               );

               $hashed_string = BniEnc::encrypt(
                   $data_asli,
                   $client_id,
                   $secret_key
               );

               $data = array(
                   'client_id' => $client_id,
                   'data' => $hashed_string,
               );

               $response = $this->get_content($url, json_encode($data));
               $response_json = json_decode($response, true);

               if ($response_json['status'] !== '000') {
                   $arr['status'] = false;
                   $arr['msg'] = '';
               }
               else {
                   $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
                   $this->insert_va_log($data_asli,$tableRoutes);
                   $arr['status'] = true;
                   $arr['msg'] = $data_asli;
               }
           }
           else
           {
               $arr['status'] = false;
               $arr['msg'] = '';
           }
       }

       return $arr;
   }

   private function get_content($url, $post = '') {
       $usecookie = __DIR__ . "/cookie.txt";
       $header[] = 'Content-Type: application/json';
       $header[] = "Accept-Encoding: gzip, deflate";
       $header[] = "Cache-Control: max-age=0";
       $header[] = "Connection: keep-alive";
       $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
       curl_setopt($ch, CURLOPT_HEADER, false);
       curl_setopt($ch, CURLOPT_VERBOSE, false);
       // curl_setopt($ch, CURLOPT_NOBODY, true);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
       curl_setopt($ch, CURLOPT_ENCODING, true);
       curl_setopt($ch, CURLOPT_AUTOREFERER, true);
       curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

       curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

       if ($post)
       {
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       }

       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

       $rs = curl_exec($ch);

       if(empty($rs)){
           var_dump($rs, curl_error($ch));
           curl_close($ch);
           return false;
       }
       curl_close($ch);
       return $rs;
   }

   public function insert_va_log($data,$routes_table = 'db_admission.register')
   {
       $dataSave = array(
               'trx_id' => $data['trx_id'],
               'virtual_account' => $data['virtual_account'],
               'customer_name' => $data['customer_name'],
               'customer_email' => $data['customer_email'],
               'customer_phone' => $data['customer_phone'],
               'billing_type' => $data['billing_type'],
               'trx_amount' => $data['trx_amount'],
               'datetime_expired' => $data['datetime_expired'],
               'description' => $data['description'],
               'Status' => 0,
               'Created' => date('Y-m-d H:i:s'),
               'routes_table' => $routes_table,
                       );

       $this->db->insert('db_va.va_log', $dataSave);
   }

   public function updateCicilanDeadline($data,$ID)
   {
      $dataSave = array(
              'BilingID' => $data['trx_id'],
              'Deadline' => $data['datetime_expired'],
                      );
      $this->db->where('ID',$ID);
      $this->db->update('db_finance.payment_pre', $dataSave);
   }

   public function update_va_log($data,$routes_table = 'db_admission.register')
   {
       $dataSave = array(
               'customer_name' => $data['customer_name'],
               'customer_email' => $data['customer_email'],
               'customer_phone' => $data['customer_phone'],
               'trx_amount' => $data['trx_amount'],
               'datetime_expired' => $data['datetime_expired'],
               'description' => $data['description'],
               'Status' => 2,
               'Created' => date('Y-m-d H:i:s'),
               'routes_table' => $routes_table,
                       );
       $this->db->where('trx_id',$data['trx_id']);
       $this->db->update('db_va.va_log', $dataSave);
   }

   public function update_va_Payment($payment = null,$DeadLinePayment = null, $Name = null, $Email = null,$BilingID = null,$routes_table = 'db_finance.payment_pre',$desc = 'Pembayaran Uang Kuliah')
   {
       $arr = array();
       $arr['status'] = false;
       $arr['msg'] = '';
       if ($payment != null) {
           include_once APPPATH.'third_party/bni/BniEnc.php';
           // FROM BNI
           $client_id = VA_client_id;
           $secret_key = VA_secret_key;
           $url = VA_url;
           $datetime_expired = $DeadLinePayment;

           if ($BilingID != null) {
               $data_asli = array(
                   'client_id' => $client_id,
                   'trx_id' => $BilingID, // fill with Billing ID
                   'trx_amount' => $payment,
                   'customer_name' => $Name,
                   'customer_email' => $Email,
                   'customer_phone' => '+622129200456',
                   'datetime_expired' => $datetime_expired, // billing will be expired in 2 hours
                   'description' => $desc,
                   'type' => 'updateBilling',

               );

               $hashed_string = BniEnc::encrypt(
                   $data_asli,
                   $client_id,
                   $secret_key
               );

               $data = array(
                   'client_id' => $client_id,
                   'data' => $hashed_string,
               );

               $response = $this->get_content2($url, json_encode($data));
               $response_json = json_decode($response, true);

               if ($response_json['status'] !== '000') {

                   $arr['status'] = false;
                   $arr['msg'] = '';
               }
               else {
                   $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
                   $this->update_va_log($data_asli,$routes_table);
                   $arr['status'] = true;
                   $arr['msg'] = $data_asli;
               }
           }
           else
           {
               $arr['status'] = false;
               $arr['msg'] = '';
           }
       }

       return $arr;
   }

   private function get_content2($url, $post = '') {
       $usecookie = __DIR__ . "/cookie2.txt";
       $header[] = 'Content-Type: application/json';
       $header[] = "Accept-Encoding: gzip, deflate";
       $header[] = "Cache-Control: max-age=0";
       $header[] = "Connection: keep-alive";
       $header[] = "Accept-Language: en-US,en;q=0.8,id;q=0.6";

       $ch = curl_init();
       curl_setopt($ch, CURLOPT_URL, $url);
       curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
       curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
       curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
       curl_setopt($ch, CURLOPT_HEADER, false);
       curl_setopt($ch, CURLOPT_VERBOSE, false);
       // curl_setopt($ch, CURLOPT_NOBODY, true);
       curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
       curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
       curl_setopt($ch, CURLOPT_ENCODING, true);
       curl_setopt($ch, CURLOPT_AUTOREFERER, true);
       curl_setopt($ch, CURLOPT_MAXREDIRS, 5);

       curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 6.1) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/37.0.2062.120 Safari/537.36");

       if ($post)
       {
           curl_setopt($ch, CURLOPT_POST, true);
           curl_setopt($ch, CURLOPT_POSTFIELDS, $post);
       }

       curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);

       $rs = curl_exec($ch);

       if(empty($rs)){
           var_dump($rs, curl_error($ch));
           curl_close($ch);
           return false;
       }
       curl_close($ch);
       return $rs;
   }

   public function checkBiling($biling)
   {
       include_once APPPATH.'third_party/bni/BniEnc.php';
       $arr_temp = array();
       // include_once APPPATH.'third_party/bni/BniEnc.php';
       $client_id = VA_client_id;
       $secret_key = VA_secret_key;
       $url = VA_url;

           $data_asli = array(
               'client_id' => $client_id,
               'trx_id' => $biling, // fill with Billing ID
               'type' => 'inquirybilling',
           );
           $hashed_string = BniEnc::encrypt(
               $data_asli,
               $client_id,
               $secret_key
           );

           $data = array(
               'client_id' => $client_id,
               'data' => $hashed_string,
           );

           $response = $this->get_content($url, json_encode($data));
           $response_json = json_decode($response, true);
           if ($response_json['status'] !== '000') {
               // $arr_temp[$i]['msg'] = $response_json['status'];
               $arr_temp['msg'] = $response_json;
               $arr_temp['trx_id'] = $biling;

           }
           else {
               $data_response = BniEnc::decrypt($response_json['data'], $client_id, $secret_key);
               // $arr_temp['msg'] = $response_json['status'];
               $arr_temp['msg'] = $data_response;
               $arr_temp['trx_id'] = $biling;
           }

       return $arr_temp;
   }

   public function getTuitionFee($ID_register_formulir)
   {
    $sql = 'select d.id as ID_register_formulir,b.ProdiID,a.Description,b.Cost,c.Discount,c.Pay_tuition_fee from db_finance.payment_type as a
            join db_finance.tuition_fee as b on a.ID = b.PTID
            join db_admission.register_formulir as d
            on d.ID_program_study = b.ProdiID
            join db_finance.payment_register as c
            on c.ID_register_formulir = d.ID
            where c.PTID = a.ID and d.ID = ? and b.ClassOf = YEAR(CURDATE())';
      $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
      return $query;
   }

   private function PDF_tuition_fee_approved($ID_register_formulir)
   {
       //error_reporting(0);
       $this->load->model('master/m_master');
       $this->load->model('admission/m_admission');

       $query = $this->m_admission->getDataPersonal($ID_register_formulir);
       $query2 = $this->m_master->caribasedprimary('db_admission.school','ID',$query[0]['SchoolID']);

       $data = $this->getTuitionFee($ID_register_formulir);
       $query3 = $this->m_master->caribasedprimary('db_academic.program_study','ID',$data[0]['ProdiID']);

       $arr_temp = array('filename' => '');
       $filename = 'Tuition_fee.pdf';
       $getData = $this->m_master->showData_array('db_admission.set_label_token_off');
       $setXAwal = 10;
       $setYAwal = 18;
       $setJarakY = 5;
       $setJarakX = 40;
       $setFontIsian = 12;
           $config=array('orientation'=>'P','size'=>'A4');
           $this->load->library('mypdf',$config);
           $this->mypdf->SetMargins(10,10,10,10);
           $this->mypdf->SetAutoPageBreak(true, 0);
           $this->mypdf->AddPage();
           // Logo
           $this->mypdf->Image('./images/logo_tr.png',10,10,50);
           $this->mypdf->SetFont('Arial','B',10);
           $this->mypdf->Text(150, 17, 'Formulir Number : '.$query[0]['FormulirCode']);

           // Line break
           $this->mypdf->Ln(20);

           // isian
           $setY = $setYAwal + 20;
           $setX = $setXAwal;

           // label
           $this->mypdf->SetXY($setX,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, 'Nama', 0, 1, 'L', 0);

           // titik dua
           $setXtitik2 = $setX+$setJarakX;
           $this->mypdf->SetXY($setXtitik2,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$setFontIsian);
           $this->mypdf->Cell(0, 0, ":", 0, 1, 'L', 0);

           // value
           $setXvalue = $setXtitik2 + 2;
           $this->mypdf->SetXY($setXvalue,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, $query[0]['Name'], 0, 1, 'L', 0);

           $setY = $setY + 8;

           // label
           $this->mypdf->SetXY($setX,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, 'Sekolah', 0, 1, 'L', 0);

           // titik dua
           $setXtitik2 = $setX+$setJarakX;
           $this->mypdf->SetXY($setXtitik2,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$setFontIsian);
           $this->mypdf->Cell(0, 0, ":", 0, 1, 'L', 0);

           // value
           $setXvalue = $setXtitik2 + 2;
           $this->mypdf->SetXY($setXvalue,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, $query2[0]['SchoolName'], 0, 1, 'L', 0);

           $setY = $setY + 8;
           // label
           $this->mypdf->SetXY($setX,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, 'Program Studi', 0, 1, 'L', 0);

           // titik dua
           $setXtitik2 = $setX+$setJarakX;
           $this->mypdf->SetXY($setXtitik2,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$setFontIsian);
           $this->mypdf->Cell(0, 0, ":", 0, 1, 'L', 0);

           // value
           $setXvalue = $setXtitik2 + 2;
           $this->mypdf->SetXY($setXvalue,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, $query3[0]['Name'], 0, 1, 'L', 0);

           $setY = $setY + 8;
           // label
           $this->mypdf->SetXY($setX,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, 'Virtual Account', 0, 1, 'L', 0);

           // titik dua
           $setXtitik2 = $setX+$setJarakX;
           $this->mypdf->SetXY($setXtitik2,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$setFontIsian);
           $this->mypdf->Cell(0, 0, ":", 0, 1, 'L', 0);

           // value
           $setXvalue = $setXtitik2 + 2;
           $this->mypdf->SetXY($setXvalue,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           $this->mypdf->SetFont('Arial','',$getData[0]['setFont1']);
           $this->mypdf->Cell(0, 0, $query[0]['VA_number'], 0, 1, 'L', 0);

            $setY = $setY + 10;

           $this->mypdf->SetXY($setX,$setY);
           $this->mypdf->SetFillColor(226, 226, 226);
           $this->mypdf->Cell(8,9,'No',1,0,'C',true);
           $this->mypdf->Cell(45,9,'Type Biaya Kuliah',1,0,'C',true);
           $this->mypdf->Cell(60,9,'Harga',1,0,'C',true);
           $this->mypdf->Cell(25,9,'Diskon',1,0,'C',true);
           $this->mypdf->Cell(50,9,'Pembayaran',1,1,'C',true);

          $t = 0;
          for ($i=0; $i < count($data); $i++) {
            $no = $i + 1;
            $this->mypdf->SetFillColor(255, 255, 255);
            $this->mypdf->Cell(8,9,$no,1,0,'C',true);
            $this->mypdf->Cell(45,9,$data[$i]['Description'],1,0,'L',true);
            $this->mypdf->Cell(60,9,'Rp '.number_format($data[$i]['Cost'],2,',','.'),1,0,'L',true);
            $this->mypdf->Cell(25,9,$data[$i]['Discount'].'%',1,0,'L',true);
            $this->mypdf->Cell(50,9,'Rp '.number_format($data[$i]['Pay_tuition_fee'],2,',','.'),1,1,'L',true);
            $t = $t + $data[$i]['Pay_tuition_fee'];
          }

          $this->mypdf->Cell(8,9,'',0,0,'C',true);
          $this->mypdf->Cell(45,9,'',0,0,'L',true);
          $this->mypdf->Cell(60,9,'',0,0,'L',true);
          $this->mypdf->Cell(25,9,'',0,0,'L',true);
          $this->mypdf->Cell(50,9,'Rp '.number_format($t,2,',','.'),0,1,'L',true);

          $this->mypdf->Cell(25,5,'Note : ',0,1,'L',true);
          $this->mypdf->SetFont('Arial','',9);
          $this->mypdf->Cell(100,5,'* Biaya kuliah per semester : Biaya BPP + (Biaya per SKS (Credit) * Jumlah SKS) +  Biaya lain-lain persemester,',0,1,'L',true);
          $this->mypdf->Cell(100,5,'* Jika calon mahasiswa tidak lulus Ujian Nasional (UN) maka biaya yang telah dibayarkan akan dikembalikan dan ',0,1,'L',true);
          $this->mypdf->Cell(100,5,'  dipotong biaya administrasi sebesar Rp 500.000,00 setelah menunjukan surat keterangan dari sekolah, ',0,1,'L',true);
          $this->mypdf->Cell(100,5,'* Apabila diterima di Perguruan Tinggi Negri (PTN) yaitu UI,ITB,UNPAD,UNDIP,IPB,UGM,UNAIR,ITS melalui ',0,1,'L',true);
          $this->mypdf->Cell(100,5,'  jalur SNMPTN & SBMPTN (tidak termasuk jalur Ujian Mandiri, program diploma & politeknik negri) maka biaya yang telah',0,1,'L',true);
          $this->mypdf->Cell(100,5,'  dibayarkan akan dikembalikan & dipotong biaya administrasi Rp 1.500.000,00 ',0,1,'L',true);
          $this->mypdf->Cell(100,5,'  (dengan menunjukan surat penerimaan dari universitas terkait) ',0,1,'L',true);


           $this->mypdf->Line(20, 280, 190, 280);
           $setY = 282;
           $this->mypdf->SetFont('Arial','',6);
           $this->mypdf->SetXY(40,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           // $this->mypdf->SetFillColor(0,0,0);
           $this->mypdf->Cell(190, 5, 'Admission Office :  Central Park Mall, Lantai 3, Unit 112, Podomoro City, JL Letjen S. Parman Kav.28, Jakarta Barat 11470', 0, 1, 'L', 0);
           $setY = 285;
           $this->mypdf->SetFont('Arial','',6);
           $this->mypdf->SetXY(43,$setY);
           $this->mypdf->SetTextColor(0,0,0);
           // $this->mypdf->SetFillColor(0,0,0);
           $this->mypdf->Cell(190, 5, 'Telp : (021) 292 00 456    Email : admission@podomorouniversity.ac.id   Website : www.podomorouniversity.ac.id', 0, 1, 'L', 0);

           $path = './document';
           $path = $path.'/'.$filename;
           $this->mypdf->Output($path,'F');

           $this->load->model('m_sendemail');
           $text = 'Dear '.$query[0]['Name'].',<br><br>
                       Plase find attached your payment.<br>
                   ';
           $to = $query[0]['Email'];
           $subject = "Podomoro University Tuition Fee";
           $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text,$path);
   }

   public function tuition_fee_calon_mhs($input)
   {
      $arr = array();
      for ($i=0; $i < count($input); $i++) {
        $temp = $this->tuition_fee_calon_mhs_by_ID($input[$i]);
        $arr[] = $temp;
      }

      return $arr;
   }

   public function getPaymentType_Cost_created_calon_mhs($ID_register_formulir)
   {
    $sql = 'select a.*,b.Description,b.Abbreviation from db_finance.payment_admisi as a join db_finance.payment_type as b on a.PTID = b.ID where a.ID_register_formulir = ?';
    $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
    return $query;
   }

   public function getRangking_calon_mhs($ID_register_formulir)
   {
    $sql= "select a.*,b.Attachment from db_admission.register_rangking as a left join db_admission.register_document as b
           on a.FileRapor = b.ID where a.ID_register_formulir = ?
          ";
    $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
    return $query;
   }

   public function getTuitionFee_calon_mhs($ID_register_formulir)
   {
    $sql = 'select d.id as ID_register_formulir,b.ProdiID,a.Description,b.Cost,c.Discount,c.Pay_tuition_fee from db_finance.payment_type as a
            join db_finance.tuition_fee as b on a.ID = b.PTID
            join db_admission.register_formulir as d
            on d.ID_program_study = b.ProdiID
            join db_finance.payment_admisi as c
            on c.ID_register_formulir = d.ID
            where c.PTID = a.ID and d.ID = ? and b.ClassOf = YEAR(CURDATE())';
      $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
      return $query;
   }

   public function getNumberSuratTuitionFee($ID_register_formulir)
   {
    $sql = 'select a.SetTa
            from db_finance.register_admisi as f
            LEFT JOIN db_admission.register_formulir as e
            on f.ID_register_formulir = e.ID
            LEFT JOIN db_admission.register_verified as c
            on c.ID = e.ID_register_verified
            LEFT JOIN db_admission.register_verification as z
            on c.RegVerificationID = z.ID
            left join db_admission.register as a
            on z.RegisterID = a.ID
            where e.ID = ?
            ';
    $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
    $set_ta = $query[0]['SetTa'];
    $sql = 'select f.*
            from db_finance.register_admisi as f
            LEFT JOIN db_admission.register_formulir as e
            on f.ID_register_formulir = e.ID
            LEFT JOIN db_admission.register_verified as c
            on c.ID = e.ID_register_verified
            LEFT JOIN db_admission.register_verification as z
            on c.RegVerificationID = z.ID
            left join db_admission.register as a
            on z.RegisterID = a.ID
            where a.SetTa = ? and  f.No_Surat IS NOT NULL order by f.No_Surat desc limit 1
            ';
    $query=$this->db->query($sql, array($set_ta))->result_array();
    if (count($query) > 0 ) {
        $No_Surat = $query[0]['No_Surat'] + 1;
    }
    else
    {
      $No_Surat = 1;
    }

    return $No_Surat;
   }

   public function ShowNumberTuitionFee($string)
   {
    $max = 3;
    $len = strlen($string);
    for ($i=0; $i < ($max - $len); $i++) {
        $string = '0'.$string;
    }
    return $string;
   }

   public function process_tuition_fee_calon_mhs($arrDataPersonal,$arrDataInvoice)
   {
    // print_r($arrDataInvoice);die();
       $errorMSG = '';
       # Create VA untuk set payment & # save data
       $payment = $arrDataInvoice[0]['Invoice'];
       $DeadLinePayment = $arrDataInvoice[0]['Deadline'];
       // $process = $this->create_va_Payment($payment,$DeadLinePayment);

      // get number surat
       $No_Surat = $this->getNumberSuratTuitionFee($arrDataPersonal[0]['ID_register_formulir']);

       // if ($this->session->userdata('finance_auth_Policy_SYS') == 1) {
       //     $process = $this->create_va_Payment($payment,$DeadLinePayment, $arrDataPersonal[0]['Name'], $arrDataPersonal[0]['Email'],$arrDataPersonal[0]['VA_number']);
       //      if ($process['status']) {
       //          $BilingID = $process['msg']['trx_id'];
       //          $dataSave = array(
       //                  'BilingID' => $BilingID,
       //                          );
       //          $this->db->where('ID_register_formulir',$arrDataPersonal[0]['ID_register_formulir']);
       //          $this->db->where('Deadline',$DeadLinePayment);
       //          $this->db->update('db_finance.payment_pre', $dataSave);

       //          $dataSave = array(
       //                  'Status' => 'Approved',
       //                  'No_Surat' => $No_Surat,
       //                  'ApprovedBY' => $this->session->userdata('NIP'),
       //                  'ApprovedAT' => date('Y-m-d'),
       //                          );
       //          $this->db->where('ID_register_formulir',$arrDataPersonal[0]['ID_register_formulir']);
       //          $this->db->update('db_finance.register_admisi', $dataSave);

       //      }
       //      else
       //      {
       //          $errorMSG = 'Error activated virtual account, please try again';
       //      }
       // }
       // else
       // {
       //     $dataSave = array(
       //             'Status' => 'Approved',
       //             'No_Surat' => $No_Surat,
       //             'ApprovedBY' => $this->session->userdata('NIP'),
       //             'ApprovedAT' => date('Y-m-d'),
       //                     );
       //     $this->db->where('ID_register_formulir',$arrDataPersonal[0]['ID_register_formulir']);
       //     $this->db->update('db_finance.register_admisi', $dataSave);
       // }

       $dataSave = array(
               'Status' => 'Approved',
               'No_Surat' => $No_Surat,
               'ApprovedBY' => $this->session->userdata('NIP'),
               'ApprovedAT' => date('Y-m-d'),
                       );
       $this->db->where('ID_register_formulir',$arrDataPersonal[0]['ID_register_formulir']);
       $this->db->update('db_finance.register_admisi', $dataSave);


       return $errorMSG;
   }

   public function tuition_fee_calon_mhs_by_ID($ID_register_formulir,$status = 'p.Status = "Created"')
   {
       $arr_temp = array();
       $sql = 'select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,
            f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
            n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
            if((select count(*) as total from db_admission.register_nilai where Status = "Verified" and ID_register_formulir = a.ID limit 1) > 0,"Rapor","Ujian")
            as status1,p.CreateAT,p.CreateBY,d.VA_number,b.FormulirCode,
            a.Address,a.ID_province as IDPRovAddress,a.ID_region as IDRegionAddress,
            a.PhoneNumber,d.SetTa,px.No_Ref,p.Pay_Cond
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
            on b.FormulirCode = px.FormulirCode
            where '.$status.' and a.ID = ? group by a.ID';
       $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
       // print_r($query);die();
       $this->load->model('master/m_master');
       $this->load->model('admission/m_admission');
       for ($i=0; $i < count($query); $i++) {
         $DiskonSPP = 0;
         // get Price

              // $arr_temp2 = $this->m_admission->tuitionFeeIntake_ALL($query[$i]['ID_register_formulir']);
         
             $getPaymentType_Cost = $this->getPaymentType_Cost_created_calon_mhs($query[$i]['ID_register_formulir']);
             $arr_temp2 = array();
             for ($k=0; $k < count($getPaymentType_Cost); $k++) {
               
               // check meiliki potongan lain atau tidak
              $getPotonganLain =  $this->m_master->caribasedprimary('db_finance.payment_admisi_potongan_lain','ID_payment_admisi',$getPaymentType_Cost[$k]['ID']);

               $arr_temp2 = $arr_temp2 + array(
                 $getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Pay_tuition_fee'],
                 'Discount-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Discount'],
                 'PotonganLain-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPotonganLain,
               );
             }

             // get region and province
                $RegionAddress = '';
                $ProvinceAddress = '';
                $getRegion = $this->m_master->caribasedprimary('db_admission.region','ID',$query[$i]['IDRegionAddress']);
                $getProv = $this->m_master->caribasedprimary('db_admission.province','ProvinceID',$query[$i]['IDPRovAddress']);
                if (count($getRegion) > 0 && count($getProv) > 0) {
                  $RegionAddress = $getRegion[0]['RegionName'];
                  $ProvinceAddress = $getProv[0]['ProvinceName'];
                }

              // tahun akademik
                 $TahunAkademik = $this->m_master->caribasedprimary('db_academic.semester','Year',$query[$i]['SetTa']);
                 $NamaTahunAkademik = $query[$i]['SetTa'];
                 for ($b=0; $b < count($TahunAkademik); $b++) {
                   if ($TahunAkademik[$b]['Code'] == 1) {
                     $NamaTahunAkademik = $TahunAkademik[$b]['Name'];
                     break;
                   }
                 }

              // cicilan
              // $Cicilan = $this->checkPayment_admisi($query[$i]['ID_register_formulir']); // nggk ke pake disini


         if ($query[$i]['status1'] == 'Rapor') {
           // check rangking
             $getRangking = $this->getRangking_calon_mhs($query[$i]['ID_register_formulir']);
             $getRangking = $getRangking[0]['Rangking'];

             $arr_temp[$i] = array(
               'ID_register_formulir' => $query[$i]['ID_register_formulir'],
               'Email' => $query[$i]['Email'],
               'Name' => $query[$i]['Name'],
               'NamePrody' => $query[$i]['NamePrody'],
               'SchoolName' => $query[$i]['SchoolName'],
               'Status1' => $query[$i]['status1'],
               'VA_number' => $query[$i]['VA_number'],
               // 'DiskonSPP' => $DiskonSPP,
               'RangkingRapor' => $getRangking,
               'FormulirCode' => ($query[$i]['No_Ref'] == "" || $query[$i]['No_Ref'] == null) ? $query[$i]['FormulirCode'] : $query[$i]['No_Ref'],
               'Address' => $query[$i]['Address'],
               'RegionAddress' => $RegionAddress,
               'ProvinceAddress' => $ProvinceAddress,
               'PhoneNumber' => $query[$i]['PhoneNumber'],
               'NamaTahunAkademik' => $NamaTahunAkademik,
               'ID_program_study' => $query[$i]['ID_program_study'],
               'SetTa' => $query[$i]['SetTa'],
               'Pay_Cond' => $query[$i]['Pay_Cond']

             );
         }
         else
         {
             $arr_temp[$i] = array(
               'ID_register_formulir' => $query[$i]['ID_register_formulir'],
               'Email' => $query[$i]['Email'],
               'Name' => $query[$i]['Name'],
               'NamePrody' => $query[$i]['NamePrody'],
               'SchoolName' => $query[$i]['SchoolName'],
               'Status1' => $query[$i]['status1'],
               'VA_number' => $query[$i]['VA_number'],
               // 'DiskonSPP' => $DiskonSPP,
               'RangkingRapor' => 0,
               'FormulirCode' => ($query[$i]['No_Ref'] == "" || $query[$i]['No_Ref'] == null) ? $query[$i]['FormulirCode'] : $query[$i]['No_Ref'],
               'Address' => $query[$i]['Address'],
               'RegionAddress' => $RegionAddress,
               'ProvinceAddress' => $ProvinceAddress,
               'PhoneNumber' => $query[$i]['PhoneNumber'],
               'NamaTahunAkademik' => $NamaTahunAkademik,
               'ID_program_study' => $query[$i]['ID_program_study'],
               'SetTa' => $query[$i]['SetTa'],
               'Pay_Cond' => $query[$i]['Pay_Cond']

             );
         }

         $arr_temp[$i] = $arr_temp[$i] + $arr_temp2;
       }
       return $arr_temp;
   }

   public function count_get_tagihan_mhs($ta,$prodi,$PTID,$NPM)
   {
    $this->load->model('master/m_master');
    $ta1 = explode('.', $ta);
    $ta = $ta1[1];
    $db = 'ta_'.$ta.'.students';
    $db2 = 'ta_'.$ta;
    $field = 'StatusStudentID';
    $value = 3;
    $NPM = ($NPM == "" || $NPM == null) ? '' : ' and a.NPM = "'.$NPM.'"';
    $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);

    $queryAdd = '';
    if ($PTID == 3) {
      $queryAdd = ' and a.NPM in (select NPM from db_finance.payment where PTID = 2 and SemesterID = '.$SemesterID[0]['ID'].' and Status = "1")';
    }
    if ($prodi == '') {
     $sql = 'select count(*) as total from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.tuition_fee as c
             on a.ProdiID = c.ProdiID
             where a.StatusStudentID in (3,2,8)  and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.ClassOf = ? and c.PTID = ? '.$NPM.$queryAdd.'
             and b.Pay_Cond = c.Pay_Cond order by a.NPM asc';
     $Data_mhs=$this->db->query($sql, array($PTID,$SemesterID[0]['ID'],$ta,$PTID))->result_array();
    }
    else
    {
      $sql = 'select count(*) as total from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.tuition_fee as c
              on a.ProdiID = c.ProdiID
              where a.StatusStudentID in (3,2,8)  and a.ProdiID = ? and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.ClassOf = ? and c.PTID = ? '.$NPM.$queryAdd.'
               and b.Pay_Cond = c.Pay_Cond order by a.NPM asc';
      $Data_mhs=$this->db->query($sql, array($prodi,$PTID,$SemesterID[0]['ID'],$ta,$PTID))->result_array();
    }

    return $Data_mhs[0]['total'];
   }

   public function count_get_tagihan_mhs2($ta,$prodi,$PTID,$NPM,$Semester)
   {
    $this->load->model('master/m_master');
    $ta1 = explode('.', $ta);
    $ta = $ta1[1];
    $db = 'ta_'.$ta.'.students';
    $db2 = 'ta_'.$ta;
    $field = 'StatusStudentID';
    $value = 3;
    $NPM = ($NPM == "" || $NPM == null) ? '' : ' and a.NPM = "'.$NPM.'"';
    // $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
    $SemesterID = $Semester;

    // find semester id to get semester in m_tuition_fee
        $GetSemester = $this->m_master->GetSemester($ta,$Semester);
        if ($PTID != 2 && $PTID != 3 && $PTID != 5 && $PTID != 6 ) {
          $GetSemester = 1;
        }

    $queryAdd = '';
    if ($PTID == 3) {
      $queryAdd = ' and a.NPM in (select NPM from db_finance.payment where PTID = 2 and SemesterID = '.$Semester.' and Status = "1")';
    }
    if ($prodi == '') {
     $sql = 'select count(*) as total from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.m_tuition_fee as c
             on a.NPM = c.NPM
             where a.StatusStudentID in (3,2,8)  and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.Semester = ? and c.PTID = ? '.$NPM.$queryAdd.'
               order by a.NPM asc';
     $Data_mhs=$this->db->query($sql, array($PTID,$Semester,$GetSemester,$PTID))->result_array();
    }
    else
    {
      $sql = 'select count(*) as total from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.m_tuition_fee as c
                   on a.NPM = c.NPM
                   where a.StatusStudentID in (3,2,8)  and a.ProdiID = ? and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.Semester = ? and c.PTID = ? '.$NPM.$queryAdd.'
                     order by a.NPM asc';
      $Data_mhs=$this->db->query($sql, array($prodi,$PTID,$Semester,$GetSemester,$PTID))->result_array();
    }

    if ($PTID == 5 || $PTID == 6) {
      $queryAdd = '';
      $G_data = $this->m_master->caribasedprimary('db_academic.semester_antara','SemesterID',$Semester);
      $SASemesterID = $G_data[0]['ID'];
      $PTIDSelected = ($PTID == 5) ? 2 : 3;
      $GetSemester = $this->m_master->GetSemester($ta,$Semester);
      if ($PTIDSelected != 2 && $PTIDSelected != 3 && $PTIDSelected != 5 && $PTIDSelected != 6 ) {
        $GetSemester = 1;
      }
      if ($prodi == '') {
        $sql = 'select count(*) as total from '.$db.' as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_student as sast on sast.NPM = b.NPM
                join db_finance.m_tuition_fee as c
                on a.NPM = c.NPM
                where a.StatusStudentID in (3,2,8)  and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.Semester = ? and c.PTID = ? '.$NPM.$queryAdd.'
                and a.NPM in (select NPM from db_academic.sa_student_details where `Status` = "3")
                  order by a.NPM asc';
                  // print_r($sql);die();
        $Data_mhs=$this->db->query($sql, array($PTID,$SASemesterID,$GetSemester,$PTIDSelected))->result_array();
      }
      else
      {
        $sql = 'select count(*) as total from '.$db.' as a join db_academic.auth_students as b on a.NPM = b.NPM
                    join db_academic.sa_student as sast on sast.NPM = b.NPM
                    join db_finance.m_tuition_fee as c
                     on a.NPM = c.NPM
                     where a.StatusStudentID in (3,2,8)  and a.ProdiID = ? and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.Semester = ? and c.PTID = ? '.$NPM.$queryAdd.'
                     and a.NPM in (select NPM from db_academic.sa_student_details where `Status` = "3")
                       order by a.NPM asc';
        $Data_mhs=$this->db->query($sql, array($prodi,$PTID,$SASemesterID,$GetSemester,$PTIDSelected))->result_array();
      }
    }

    return $Data_mhs[0]['total'];
   }

   public function get_tagihan_mhs($ta,$prodi,$PTID,$NPM,$limit, $start)
   {
    // error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');
    $ta1 = explode('.', $ta);
    $ta = $ta1[1];
    $db = 'ta_'.$ta.'.students';
    $db2 = 'ta_'.$ta;
    $field = 'StatusStudentID';
    $value = 3;
    $NPM = ($NPM == "" || $NPM == null) ? '' : ' and a.NPM = "'.$NPM.'"';
    $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);

    $queryAdd = '';
    if ($PTID == 3) {
      $queryAdd = ' and a.NPM in (select NPM from db_finance.payment where PTID = 2 and SemesterID = '.$SemesterID[0]['ID'].' and Status = "1")';
    }
    if ($prodi == '') {
     $sql = 'select a.*,b.EmailPU,b.Pay_Cond,b.Bea_BPP,b.Bea_Credit,c.Cost from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.tuition_fee as c
             on a.ProdiID = c.ProdiID
             where a.StatusStudentID in (3,2,8)  and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.ClassOf = ? and c.PTID = ? '.$NPM.$queryAdd.'
             and b.Pay_Cond = c.Pay_Cond order by a.NPM asc
             LIMIT '.$start. ', '.$limit;
     $Data_mhs=$this->db->query($sql, array($PTID,$SemesterID[0]['ID'],$ta,$PTID))->result_array();
    }
    else
    {
      $sql = 'select a.*,b.EmailPU,b.Pay_Cond,b.Bea_BPP,b.Bea_Credit,c.Cost from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.tuition_fee as c
              on a.ProdiID = c.ProdiID
              where a.StatusStudentID in (3,2,8)  and a.ProdiID = ? and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.ClassOf = ? and c.PTID = ? '.$NPM.$queryAdd.'
               and b.Pay_Cond = c.Pay_Cond order by a.NPM asc
              LIMIT '.$start. ', '.$limit;
      $Data_mhs=$this->db->query($sql, array($prodi,$PTID,$SemesterID[0]['ID'],$ta,$PTID))->result_array();
    }

    // get Number VA Mahasiswa
    $Const_VA = $this->m_master->showData_array('db_va.master_va');

    // $SemesterID = $SemesterID[0]['ID'];
    $Discount = $this->m_master->showData_array('db_finance.discount');
    for ($i=0; $i < count($Data_mhs); $i++) {
      $array = array('SemesterID' => $SemesterID[0]['ID'], 'SemesterName' => $SemesterID[0]['Name']);
      $Data_mhs[$i] = $Data_mhs[$i] + $array;
      $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$Data_mhs[$i]['ProdiID']);
      $array = array('ProdiEng' => $ProdiEng[0]['NameEng']);
      $Data_mhs[$i] = $Data_mhs[$i] + $array;

      // get IPS Mahasiswa
        $IPS = $this->getIPSMahasiswa($db2,$Data_mhs[$i]['NPM']);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPS' => $IPS);

      // get IPS Mahasiswa
        $IPK = $this->getIPKMahasiswa($db2,$Data_mhs[$i]['NPM']);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPK' => $IPK);

      // get VA Mahasiwa
        $VA = $Const_VA[0]['Const_VA'].$Data_mhs[$i]['NPM'];
        $Data_mhs[$i] = $Data_mhs[$i] + array('VA' => $VA);

      // get sks yang diambil
         $Credit = $this->getSKSMahasiswa($db2,$Data_mhs[$i]['NPM']);
         $Data_mhs[$i] = $Data_mhs[$i] + array('Credit' => $Credit);

    }
    $arr['Data_mhs'] = $Data_mhs;
    $arr['Discount'] = $Discount;
    return $arr;
   }

   public function get_tagihan_mhs2($ta,$prodi,$PTID,$NPM,$Semester,$limit, $start)
   {
    // error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');
    $ta1 = explode('.', $ta);
    $ta = $ta1[1];
    $db = 'ta_'.$ta.'.students';
    $db2 = 'ta_'.$ta;
    $field = 'StatusStudentID';
    $value = 3;
    $NPM = ($NPM == "" || $NPM == null) ? '' : ' and a.NPM = "'.$NPM.'"';
    $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','ID',$Semester);
    // $SemesterID = $Semester;
    $queryAdd = '';
    // find semester id to get semester in m_tuition_fee
            $GetSemester = $this->m_master->GetSemester($ta,$Semester);
            if ($PTID != 2 && $PTID != 3 && $PTID != 5 && $PTID != 6) {
              $GetSemester = 1;
            }

    if ($PTID == 3) {
      $queryAdd = ' and a.NPM in (select NPM from db_finance.payment where PTID = 2 and SemesterID = '.$Semester.' and Status = "1")';
    }
    if ($prodi == '') {
     $sql = 'select a.*,b.EmailPU,c.Invoice as Cost,c.Discount,b.Pay_Cond from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.m_tuition_fee as c
             on a.NPM = c.NPM
             where a.StatusStudentID in (3,2,8)  and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.Semester = ? and c.PTID = ? '.$NPM.$queryAdd.'
               order by a.NPM asc
             LIMIT '.$start. ', '.$limit;
     $Data_mhs=$this->db->query($sql, array($PTID,$Semester,$GetSemester,$PTID))->result_array();
    }
    else
    {
      $sql = 'select a.*,b.EmailPU,c.Invoice as Cost,c.Discount,b.Pay_Cond from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM left join db_finance.m_tuition_fee as c
              on a.NPM = c.NPM
              where a.StatusStudentID in (3,2,8)  and a.ProdiID = ? and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.Semester = ? and c.PTID = ? '.$NPM.$queryAdd.'
                order by a.NPM asc
              LIMIT '.$start. ', '.$limit;
      $Data_mhs=$this->db->query($sql, array($prodi,$PTID,$Semester,$GetSemester,$PTID))->result_array();
    }

    if ($PTID == 5 || $PTID == 6) {
      $queryAdd = '';
      $G_data = $this->m_master->caribasedprimary('db_academic.semester_antara','SemesterID',$Semester);
      $SASemesterID = $G_data[0]['ID'];
      $PTIDSelected = ($PTID == 5) ? 2 : 3;
      $GetSemester = $this->m_master->GetSemester($ta,$Semester);
      $SemesterID = $this->m_master->caribasedprimary('db_academic.semester_antara','SemesterID',$Semester);
      if ($PTIDSelected != 2 && $PTIDSelected != 3 && $PTIDSelected != 5 && $PTIDSelected != 6 ) {
        $GetSemester = 1;
      }

      if ($prodi == '') {
       $sql = 'select a.*,b.EmailPU,c.Invoice as Cost,c.Discount,b.Pay_Cond from '.$db.' as a
                join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_student as sast on sast.NPM = b.NPM
                join db_finance.m_tuition_fee as c
               on a.NPM = c.NPM
               where a.StatusStudentID in (3,2,8)  and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.Semester = ? and c.PTID = ? '.$NPM.$queryAdd.'
               and a.NPM in (select NPM from db_academic.sa_student_details where `Status` = "3")
                 order by a.NPM asc
               LIMIT '.$start. ', '.$limit;
       $Data_mhs=$this->db->query($sql, array($PTID,$SASemesterID,$GetSemester,$PTIDSelected))->result_array();
      }
      else
      {
        $sql = 'select a.*,b.EmailPU,c.Invoice as Cost,c.Discount,b.Pay_Cond from '.$db.' as a
                join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_student as sast on sast.NPM = b.NPM
                join db_finance.m_tuition_fee as c
                on a.NPM = c.NPM
                where a.StatusStudentID in (3,2,8)  and a.ProdiID = ? and a.NPM not in (select NPM from db_finance.payment where PTID = ? and SemesterID = ?) and c.Semester = ? and c.PTID = ? '.$NPM.$queryAdd.'
                  and a.NPM in (select NPM from db_academic.sa_student_details where `Status` = "3")
                  order by a.NPM asc
                LIMIT '.$start. ', '.$limit;
        $Data_mhs=$this->db->query($sql, array($prodi,$PTID,$SASemesterID,$GetSemester,$PTIDSelected))->result_array();
      }

    }

    // get Number VA Mahasiswa
    $Const_VA = $this->m_master->showData_array('db_va.master_va');

    // $SemesterID = $SemesterID[0]['ID'];
    $Discount = $this->m_master->showData_array('db_finance.discount');
    for ($i=0; $i < count($Data_mhs); $i++) {
      $array = array('SemesterID' => $SemesterID[0]['ID'], 'SemesterName' => $SemesterID[0]['Name']);
      $Data_mhs[$i] = $Data_mhs[$i] + $array;

      $array = array('GetSemester' => $GetSemester);
      $Data_mhs[$i] = $Data_mhs[$i] + $array;

      $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$Data_mhs[$i]['ProdiID']);
      $array = array('ProdiEng' => $ProdiEng[0]['NameEng']);
      $Data_mhs[$i] = $Data_mhs[$i] + $array;

      // get IPS Mahasiswa
        $IPS = $this->getIPSMahasiswaBySemester($db2,$Data_mhs[$i]['NPM'],$Semester);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPS' => $IPS);

      // get IPK Mahasiswa
        $IPK = $this->getIPKMahasiswaBySemester($db2,$Data_mhs[$i]['NPM'],$Semester);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPK' => $IPK);

      // get VA Mahasiwa
        $VA = $Const_VA[0]['Const_VA'].$Data_mhs[$i]['NPM'];
        $Data_mhs[$i] = $Data_mhs[$i] + array('VA' => $VA);

      // get sks yang diambil
         $Credit = $this->getSKSMahasiswaBySemester($db2,$Data_mhs[$i]['NPM'],$Semester);
         $Credit_Detail = $this->getSKSMahasiswaBySemester_detail($db2,$Data_mhs[$i]['NPM'],$Semester);
         if ($PTID == 5 || $PTID == 6) { // Semester Antara
           $Credit = $this->getSKSMahasiswaBySemester_antara($db2,$Data_mhs[$i]['NPM'],$SemesterID[0]['ID']);
           // Get Discount
           $G_data_= $this->m_master->caribasedprimary('db_academic.sa_academic_years','SASemesterID',$SemesterID[0]['ID']);
           if ($PTID == 5) {
             $Data_mhs[$i]['Discount'] = number_format((float)$G_data_[0]['DiscountBPP'], 1, '.', '');
           }
           else
           {
            $Data_mhs[$i]['Discount'] = number_format((float)0, 1, '.', '');
           }
         }
         $Data_mhs[$i] = $Data_mhs[$i] + array('Credit' => $Credit);
         $Data_mhs[$i] = $Data_mhs[$i] + array('Credit_Detail' => $Credit_Detail);
      // update Bea_BPP & Bea_Credit
         // $Data_mhs[$i]['Bea_BPP']

    }
    $arr['Data_mhs'] = $Data_mhs;
    $arr['Discount'] = $Discount;
    return $arr;
   }

   public function getSKSMahasiswa($db,$NPM)
   {
     // get semester desc
        $sql = 'select ID from db_academic.semester where Status = 1 order by ID desc Limit 1';
        $query = $this->db->query($sql, array())->result_array();
        $SemesterID = $query[0]['ID'];

      $sql = 'select * from '.$db.'.study_planning where NPM = ? and SemesterID = ?';
      $query = $this->db->query($sql, array($NPM,$SemesterID))->result_array();

      $Credit = 0;
      for ($j=0; $j < count($query); $j++) {
       $CreditSub = $query[$j]['Credit'];
       $Credit = $Credit + $CreditSub;
      }

      return $Credit;

   }

   public function getSKSMahasiswaBySemester($db,$NPM,$SemesterID)
   {
      $sql = 'select b.TotalSKS,a.* from '.$db.'.study_planning as a
              left join db_academic.curriculum_details as b
              on a.CDID = b.ID
            where NPM = ? and SemesterID = ? and TransferCourse = "0"';
      $query = $this->db->query($sql, array($NPM,$SemesterID))->result_array();

      $Credit = 0;
      for ($j=0; $j < count($query); $j++) {
       $CreditSub = $query[$j]['TotalSKS'];
       $Credit = $Credit + $CreditSub;
      }

      return $Credit;

   }

   public function getSKSMahasiswaBySemester_detail($db,$NPM,$SemesterID)
   {
      $arr = array();
      $sql = 'select b.TotalSKS,a.* from '.$db.'.study_planning as a
              left join db_academic.curriculum_details as b
              on a.CDID = b.ID
            where NPM = ? and SemesterID = ? and TransferCourse = "0" and a.TypeSchedule = "Br"';
      $query = $this->db->query($sql, array($NPM,$SemesterID))->result_array();

      $Credit = 0;
      for ($j=0; $j < count($query); $j++) {
       $CreditSub = $query[$j]['TotalSKS'];
       $Credit = $Credit + $CreditSub;
      }

      $arr['CreditBr'] = $Credit;

      $sql = 'select b.TotalSKS,a.* from '.$db.'.study_planning as a
              left join db_academic.curriculum_details as b
              on a.CDID = b.ID
            where NPM = ? and SemesterID = ? and TransferCourse = "0" and a.TypeSchedule = "Ul"';
      $query = $this->db->query($sql, array($NPM,$SemesterID))->result_array();

      $Credit = 0;
      for ($j=0; $j < count($query); $j++) {
       $CreditSub = $query[$j]['TotalSKS'];
       $Credit = $Credit + $CreditSub;
      }

      $arr['CreditUl'] = $Credit;

      return $arr;

   }

   public function getSKSMahasiswaBySemester_antara($db,$NPM,$SemesterID)
   {
      $sql = 'select a.SASemesterID,a.NPM,b.Credit,c.SemesterID
              from db_academic.sa_student as a join
              db_academic.sa_student_details as b on a.ID = b.IDSAStudent
              join db_academic.semester_antara as c on a.SASemesterID = c.ID
              where a.NPM = ? and c.ID = ? and b.Status = "3"
              ';
      $query = $this->db->query($sql, array($NPM,$SemesterID))->result_array();

      $Credit = 0;
      for ($j=0; $j < count($query); $j++) {
       $Credit = $Credit + $query[$j]['Credit'];
      }

      return $Credit;

   }

  public function getIPKMahasiswaBySemester($db,$NPM,$SemesterID)
  {
    error_reporting(0);
    $IPK = 0;
    // hitung IPK
      // get query IPK
        $sql = 'select * from '.$db.'.study_planning where NPM = ? and SemesterID < '.$SemesterID;
        $query = $this->db->query($sql, array($NPM))->result_array();

      // proses perhitungan IPK
        $GradeValueCredit = 0;
        $Credit = 0;
        for ($j=0; $j < count($query); $j++) {
         $GradeValue = $query[$j]['GradeValue'];
         $CreditSub = $query[$j]['Credit'];
         $GradeValueCredit = $GradeValueCredit + ($GradeValue * $CreditSub);
         $Credit = $Credit + $CreditSub;
        }

      $IPK = $GradeValueCredit / $Credit;
      $IPK = (is_nan($IPK)) ? 0 : $IPK;
      return $IPK;
  }

  public function getIPKMahasiswa($db,$NPM)
  {
    error_reporting(0);
    $IPK = 0;
    // hitung IPK
      // get query IPK
        $sql = 'select * from '.$db.'.study_planning where NPM = ?';
        $query = $this->db->query($sql, array($NPM))->result_array();

      // proses perhitungan IPK
        $GradeValueCredit = 0;
        $Credit = 0;
        for ($j=0; $j < count($query); $j++) {
         $GradeValue = $query[$j]['GradeValue'];
         $CreditSub = $query[$j]['Credit'];
         $GradeValueCredit = $GradeValueCredit + ($GradeValue * $CreditSub);
         $Credit = $Credit + $CreditSub;
        }

      $IPK = $GradeValueCredit / $Credit;
      $IPK = (is_nan($IPK)) ? 0 : $IPK;
      return $IPK;
  }

   public function getIPSMahasiswa($db,$NPM)
   {
    error_reporting(0);
    $IPS = 0;
    // hitung IPS
      // get semester desc
        $sql = 'select ID from db_academic.semester where Status = 0 order by ID desc Limit 1';
        $query = $this->db->query($sql, array())->result_array();
        $SemesterID = $query[0]['ID'];

      // get query IPS
        $sql = 'select * from '.$db.'.study_planning where NPM = ? and SemesterID = ? ';

        // print_r($sql);
        $query = $this->db->query($sql, array($NPM,$SemesterID))->result_array();
        if (count($query) == 0) {
          $IPS = 0;
          return $IPS;
        }

      // proses perhitungan IPS
        $GradeValueCredit = 0;
        $Credit = 0;
        for ($j=0; $j < count($query); $j++) {
         $GradeValue = $query[$j]['GradeValue'];
         $CreditSub = $query[$j]['Credit'];
         $GradeValueCredit = $GradeValueCredit + ($GradeValue * $CreditSub);
         $Credit = $Credit + $CreditSub;
        }

      $IPS = $GradeValueCredit / $Credit;
      $IPS = (is_nan($IPS)) ? 0 : $IPS;
      return $IPS;
   }

   public function getIPSMahasiswaBySemester($db,$NPM,$SemesterID)
   {
    error_reporting(0);
    $SemesterID = $SemesterID - 1;
    $IPS = 0;
      // get query IPS
        $sql = 'select * from '.$db.'.study_planning where NPM = ? and SemesterID = ? ';

        // print_r($sql);
        $query = $this->db->query($sql, array($NPM,$SemesterID))->result_array();
        if (count($query) == 0) {
          $IPS = 0;
          return $IPS;
        }

      // proses perhitungan IPS
        $GradeValueCredit = 0;
        $Credit = 0;
        for ($j=0; $j < count($query); $j++) {
         $GradeValue = $query[$j]['GradeValue'];
         $CreditSub = $query[$j]['Credit'];
         $GradeValueCredit = $GradeValueCredit + ($GradeValue * $CreditSub);
         $Credit = $Credit + $CreditSub;
        }

      $IPS = $GradeValueCredit / $Credit;
      $IPS = (is_nan($IPS)) ? 0 : $IPS
      return $IPS;
   }

   public function getDeadlineTagihanDB($field,$SemesterID)
   {
    $sql = 'select '.$field.' from db_academic.academic_years where SemesterID = ?';
    $query=$this->db->query($sql, array($SemesterID))->result_array();
    return $query[0][$field];
   }

   public function getVANumberMHS($NPM)
   {
    $this->load->model('master/m_master');
    $a = $this->m_master->showData_array('db_va.master_va');
    $Const_VA = $a[0]['Const_VA'].$NPM;
    return $Const_VA;
   }


   public function insertaDataPayment($PTID,$SemesterID,$NPM,$Invoice,$Discount,$Status = "0",$UpdatedBy = null)
   {
    $dataSave = array(
        'PTID' => $PTID,
        'SemesterID' => $SemesterID,
        'NPM' => $NPM,
        'Invoice' => $Invoice,
        'Discount' => $Discount,
        'Status' => $Status,
        'UpdatedBy' => $UpdatedBy
    );
      $this->db->insert('db_finance.payment', $dataSave);
      $insertId = $this->db->insert_id();


      // get payment proof untuk update ID jika telah dicancel
         $sql = 'select * from db_finance.payment_proof where PTID = ? and SemesterID = ? and NPM = ?';
         $query=$this->db->query($sql, array($PTID,$SemesterID,$NPM))->result_array();
         if (count($query) > 0) {
           $ID = $query[0]['ID'];
           $dataUpdate = array(
              'ID_payment' => $insertId,
           );

           $this->db->where('ID',$ID);
           $this->db->update('db_finance.payment_proof',$dataUpdate);
         }
      return  $insertId;
   }

   public function insertaDataPaymentStudents($ID_payment,$Invoice,$BilingID,$Deadline,$Status = 0)
   {
    $dataSave = array(
        'ID_payment' => $ID_payment,
        'Invoice' => $Invoice,
        'BilingID' => $BilingID,
        'Deadline' => $Deadline,
        'Status' => $Status
    );
      $this->db->insert('db_finance.payment_students', $dataSave);
   }

   public function count_get_created_tagihan_mhs_not_approved($ta,$prodi,$PTID,$NIM,$Semester)
   {
    $arr = array();
    $this->load->model('master/m_master');

    // join dengan table auth terlebih dahulu
    $ProdiSplit = explode('.', $prodi);
    $ProdiSplit = $ProdiSplit[0];
    $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    $ProdiID = ($ProdiSplit == '' || $ProdiSplit == Null) ? '' : ' and b.ProdiID = '.$ProdiSplit;
    $NIM = ($NIM == '' || $NIM == Null) ? 'where a.NPM like "%"' : ' where  a.NPM = '.$NIM;
    // $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','ID',$Semester);
    // $SemesterID = $SemesterID[0]['ID'];
    $SemesterID = $Semester;
    if ($ta == '') {
      $ta1 = $ta;
    }
    else
    {
      $ta = explode('.', $ta);
      $ta1 = $ta[1];
    }

    $policyStatus = 'and a.Status = "0"';
    // if ($this->session->userdata('finance_auth_Policy_SYS') ==  0) {
    //   $policyStatus = '';
    // }

    if ($ta1 == '') {
      $sql = '
              select sum(total) as total from(
                select count(*) as total
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$ProdiID.'  '.$policyStatus.'
                 and b.StatusStudentID in (3,2,8)
                UNION ALL
                select count(*) as total
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_academic_years as c on a.SemesterID = c.SASemesterID
                join db_academic.semester_antara as sa on sa.ID = c.SASemesterID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$ProdiID.' and sa.SemesterID = ? '.$policyStatus.'
                 and b.StatusStudentID in (3,2,8)
              ) aa
              '
              ;
              // print_r($sql);die();
      $query=$this->db->query($sql, array($SemesterID))->result_array();

    }
    else
    {
      $sql = 'select sum(total) as total from(
                select count(*) as total
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$ProdiID.' and b.Year = ?  '.$policyStatus.'
                 and b.StatusStudentID in (3,2,8)
                 UNION ALL
                select count(*) as total
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_academic_years as c on a.SemesterID = c.SASemesterID
                join db_academic.semester_antara as sa on sa.ID = c.SASemesterID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$ProdiID.' and b.Year = ? and sa.SemesterID = ? '.$policyStatus.'
                 and b.StatusStudentID in (3,2,8)
              )aa
             ';
      $query=$this->db->query($sql, array($ta1,$ta1,$SemesterID))->result_array();
    }
    // print_r($query);die();
    return $query[0]['total'];

   }

   public function get_created_tagihan_mhs_not_approved($ta,$prodi,$PTID,$NIM,$Semester,$limit, $start)
   {
    // error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');

    // join dengan table auth terlebih dahulu
    $ProdiSplit = explode('.', $prodi);
    $ProdiSplit = $ProdiSplit[0];
    $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    $NIM = ($NIM == '' || $NIM == Null) ? 'where a.NPM like "%"' : ' where  a.NPM = '.$NIM;
    $ProdiID = ($ProdiSplit == '' || $ProdiSplit == Null) ? '' : ' and b.ProdiID = '.$ProdiSplit;
    // $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','ID',$Semester);
    // $SemesterID = $SemesterID[0]['ID'];
    $SemesterID = $Semester;
    if ($ta == '') {
      $ta1 = $ta;
    }
    else
    {
      $ta = explode('.', $ta);
      $ta1 = $ta[1];
    }

    $Status = 'and a.Status = "0"';

    if ($ta1 == '') {
      $sql = 'select * from (
                select a.*, b.Year,b.EmailPU,b.Pay_Cond,c.Name as NameSemester, d.Description
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$ProdiID.'  '.$Status.'
                and b.StatusStudentID in (3,2,8)
                UNION
                select a.*, b.Year,b.EmailPU,b.Pay_Cond,sa.Name as NameSemester, d.Description
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_academic_years as c on a.SemesterID = c.SASemesterID
                join db_academic.semester_antara as sa on sa.ID = c.SASemesterID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$ProdiID.' and sa.SemesterID = ? '.$Status.'
                and b.StatusStudentID in (3,2,8)
              ) aa
              LIMIT '.$start. ', '.$limit;

      $query=$this->db->query($sql, array($SemesterID))->result_array();

    }
    else
    {
      $sql = 'select * from (
                select a.*, b.Year,b.EmailPU,b.Pay_Cond,c.Name as NameSemester, d.Description
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$ProdiID.' and b.Year = ?  '.$Status.'
                and b.StatusStudentID in (3,2,8)
                UNION
                select a.*, b.Year,b.EmailPU,b.Pay_Cond,sa.Name as NameSemester, d.Description
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_academic_years as c on a.SemesterID = c.SASemesterID
                join db_academic.semester_antara as sa on sa.ID = c.SASemesterID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$ProdiID.' and b.Year = ? and sa.SemesterID = ? '.$Status.'
                and b.StatusStudentID in (3,2,8)
              ) aa
              LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array($ta1,$ta1,$SemesterID))->result_array();
    }

    // get Number VA Mahasiswa
        $Const_VA = $this->m_master->showData_array('db_va.master_va');

    // get all data to join db ta
    for ($i=0; $i < count($query); $i++) {
      $Year = $query[$i]['Year'];
      $db = 'ta_'.$Year.'.students';
      $dt = $this->m_master->caribasedprimary($db,'NPM',$query[$i]['NPM']);
      // get IPS Mahasiswa
         $IPS = $this->getIPSMahasiswaBySemester('ta_'.$Year,$query[$i]['NPM'],$SemesterID);

      // get IPS Mahasiswa
         $IPK = $this->getIPKMahasiswaBySemester('ta_'.$Year,$query[$i]['NPM'],$SemesterID);

      // ge VA Mahasiwa
         $VA = $Const_VA[0]['Const_VA'].$query[$i]['NPM'];

      // get sks yang diambil
         // $Credit = $this->getSKSMahasiswaBySemester('ta_'.$Year,$query[$i]['NPM'],$query[$i]['SemesterID']);
         // get sks yang diambil
            $Credit = $this->getSKSMahasiswaBySemester('ta_'.$Year,$query[$i]['NPM'],$SemesterID);
            if ($query[$i]['PTID'] == 5 || $query[$i]['PTID'] == 6) { // Semester Antara
              $Credit = $this->getSKSMahasiswaBySemester_antara('ta_'.$Year,$query[$i]['NPM'],$query[$i]['SemesterID']);
            }

      // Detail Payment & cek cancel
         $DetailPayment = $this->detail_payment_std($query[$i]['ID']);
         $cancelPay = $this->getCancel($query[$i]['PTID'],$query[$i]['SemesterID'],$query[$i]['NPM']);
         $potonganLain = $this->getPotonganLain($query[$i]['PTID'],$query[$i]['SemesterID'],$query[$i]['NPM']);

      // cek Payment Proof
              $payment_proof = $this->m_master->caribasedprimary('db_finance.payment_proof','ID_payment',$query[$i]['ID']);
              for ($z=0; $z < count($payment_proof); $z++) {
                  // get nama bank
                  $G_bank = $this->m_master->caribasedprimary('db_finance.bank','ID',$payment_proof[$z]['ID_bank']);
                  $payment_proof[$z]['NmBank'] = $G_bank[0]['Name'];
              }



      if($prodi == '' || $prodi == Null){
        $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);

        $arr[] = array(
            'PaymentID' => $query[$i]['ID'],
            'PTID'  => $query[$i]['PTID'],
            'PTIDDesc' => $query[$i]['Description'],
            'SemesterID' => $query[$i]['SemesterID'],
            'SemesterName' => $query[$i]['NameSemester'],
            'NPM' => $query[$i]['NPM'],
            'Nama' => $dt[0]['Name'],
            'EmailPU' => $query[$i]['EmailPU'],
            'InvoicePayment' => $query[$i]['Invoice'],
            'Discount' => $query[$i]['Discount'],
            'StatusPayment' => $query[$i]['Status'],
            'ProdiID' => $dt[0]['ProdiID'],
            'ProdiEng' => $ProdiEng[0]['NameEng'],
            'Year' => $Year,
            'IPS' => $IPS,
            'IPK' => $IPK,
            'DetailPayment' => $DetailPayment,
            'VA' => $VA,
            'Credit' => $Credit,
            'Pay_Cond' => $query[$i]['Pay_Cond'],
            'cancelPay' => $cancelPay,
            'payment_proof' => $payment_proof,
            'potonganLain' => $potonganLain,
        );
      }
      else
      {
        $prodi = explode('.', $prodi);
        $prodi = $prodi[0];
        if ($prodi == $dt[0]['ProdiID']) {
          $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);
          $arr[] = array(
              'PaymentID' => $query[$i]['ID'],
              'PTID'  => $query[$i]['PTID'],
              'PTIDDesc' => $query[$i]['Description'],
              'SemesterID' => $query[$i]['SemesterID'],
              'SemesterName' => $query[$i]['NameSemester'],
              'NPM' => $query[$i]['NPM'],
              'Nama' => $dt[0]['Name'],
              'EmailPU' => $query[$i]['EmailPU'],
              'InvoicePayment' => $query[$i]['Invoice'],
              'Discount' => $query[$i]['Discount'],
              'StatusPayment' => $query[$i]['Status'],
              'ProdiID' => $dt[0]['ProdiID'],
              'ProdiEng' => $ProdiEng[0]['NameEng'],
              'Year' => $Year,
              'IPS' => $IPS,
              'IPK' => $IPK,
              'DetailPayment' => $DetailPayment,
              'VA' => $VA,
              'Credit' => $Credit,
              'Pay_Cond' => $query[$i]['Pay_Cond'],
              'cancelPay' => $cancelPay,
              'payment_proof' => $payment_proof,
              'potonganLain' => $potonganLain,
          );
        }
      }

    }
    return $arr;
   }

   public function getCancel($PTID,$SemesterID,$NPM){
    $sql = 'select a.*,b.Name from db_finance.payment_s_cancel as a left join db_employees.employees as b on a.CancelBy = b.NIP where PTID = ? and SemesterID = ? and NPM = ?';
    $query=$this->db->query($sql, array($PTID,$SemesterID,$NPM))->result_array();
    return $query;
   }

   public function count_get_created_tagihan_mhs($ta,$prodi,$PTID,$NIM,$Semester,$StatusPayment,$ChangeStatus)
   {
    $arr = array();
    $this->load->model('master/m_master');
    // join dengan table auth terlebih dahulu
    $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    $prodiex = explode(".", $prodi);
    $prodiex = $prodiex[0];
    $prodiex = ($prodi == '' || $prodi == Null) ? '' : ' and b.ProdiID = '.$prodiex;
    $NIM = ($NIM == '' || $NIM == Null) ? 'where a.NPM like "%"' : ' where  a.NPM = '.$NIM;
    // $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','ID',$Semester);
    $SemesterID = $Semester;
    if ($ta == '') {
      $ta1 = $ta;
    }
    else
    {
      $ta = explode('.', $ta);
      $ta1 = $ta[1];
    }

    $AddWhereStatusPayment = '';
    switch ($StatusPayment) {
      case '':
        $AddWhereStatusPayment = '';
        break;
      case '1':
        $AddWhereStatusPayment = ' and a.Invoice = (select sum(Invoice) from db_finance.payment_students where ID_payment = a.ID and Status = 1)';
        break;
      case '0':
        $AddWhereStatusPayment = ' and a.Invoice >= (select sum(Invoice) from db_finance.payment_students where ID_payment = a.ID and Status = 0)';
        break;
      case '-1':
        $AddWhereStatusPayment = ' and a.ID not in (select z.ID_payment from db_finance.payment_students as z join db_finance.payment_student_details as x on z.ID = x.ID_payment_students )';
        break;
      default:
        $AddWhereStatusPayment = '';
        break;
    }

    if ($ChangeStatus != '') {
      if ($ChangeStatus == 1) {
        $ChangeStatus = ' and (a.ToChange = "'.$ChangeStatus.'"  or a.ToChange = "2" )';
      }
      else
      {
        $ChangeStatus = ' and a.ToChange = "'.$ChangeStatus.'"';
      }

    }
    // if ($ChangeStatus != '') {
    //   $ChangeStatus = ' and a.ToChange = "'.$ChangeStatus.'"';
    // }

    if ($ta1 == '') {
      $sql = 'select sum(total) as total from (
                select count(*) as total
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$prodiex.' and c.ID = ?
                and b.StatusStudentID in (3,2,8)
                '.$AddWhereStatusPayment.$ChangeStatus.'
                UNION ALL
                select count(*) as total
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_academic_years as c on a.SemesterID = c.SASemesterID
                join db_academic.semester_antara as sa on sa.ID = c.SASemesterID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$prodiex.' and sa.SemesterID = ?
                and b.StatusStudentID in (3,2,8)
                '.$AddWhereStatusPayment.$ChangeStatus.'
              ) aa
              ';
      $query=$this->db->query($sql, array($SemesterID,$SemesterID))->result_array();

    }
    else
    {
      $sql = 'select sum(total) as total from(
                select count(*) as total
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$prodiex.' and b.Year = ? and c.ID = ?
                and b.StatusStudentID in (3,2,8)
                '.$AddWhereStatusPayment.$ChangeStatus.'
                UNION ALL
                select count(*) as total
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_academic_years as c on a.SemesterID = c.SASemesterID
                join db_academic.semester_antara as sa on sa.ID = c.SASemesterID
                join db_finance.payment_type as d on a.PTID = d.ID '.$NIM.$PTID.$prodiex.' and b.Year = ? and sa.SemesterID = ?
                and b.StatusStudentID in (3,2,8)
                '.$AddWhereStatusPayment.$ChangeStatus.'
              ) aa
              ';
      $query=$this->db->query($sql, array($ta1,$SemesterID,$ta1,$SemesterID))->result_array();
    }
    // print_r($sql);die();
    return $query[0]['total'];

   }

   public function get_created_tagihan_mhs($ta,$prodi,$PTID,$NIM,$Semester,$StatusPayment,$ChangeStatus,$limit, $start)
   {
    // error_reporting(0);

    // print_r($limit.'<br>');
    // print_r($start);die();

    $arr = array();
    $this->load->model('master/m_master');

    // join dengan table auth terlebih dahulu
    $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    $prodiex = explode(".", $prodi);
    $prodiex = $prodiex[0];
    $prodiex = ($prodi == '' || $prodi == Null) ? '' : ' and b.ProdiID = '.$prodiex;
    $NIM = ($NIM == '' || $NIM == Null) ? 'where a.NPM like "%"' : ' where  a.NPM = '.$NIM;
    // $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','ID',$Semester);
    $SemesterID = $Semester;
    if ($ta == '') {
      $ta1 = $ta;
    }
    else
    {
      $ta = explode('.', $ta);
      $ta1 = $ta[1];
    }

    $AddWhereStatusPayment = '';
    switch ($StatusPayment) {
      case '':
        $AddWhereStatusPayment = '';
        break;
      case '1':
        $AddWhereStatusPayment = ' and a.Invoice = (select sum(Invoice) from db_finance.payment_students where ID_payment = a.ID and Status = 1)';
        break;
      case '0':
        $AddWhereStatusPayment = ' and a.Invoice >= (select sum(Invoice) from db_finance.payment_students where ID_payment = a.ID and Status = 0)';
        break;
      case '-1':
        $AddWhereStatusPayment = ' and a.ID not in (select z.ID_payment from db_finance.payment_students as z join db_finance.payment_student_details as x on z.ID = x.ID_payment_students )';
        break;
      default:
        $AddWhereStatusPayment = '';
        break;
    }

    if ($ChangeStatus != '') {
      if ($ChangeStatus == 1) {
        $ChangeStatus = ' and (a.ToChange = "'.$ChangeStatus.'"  or a.ToChange = "2" )';
      }
      else
      {
        // nothing
        $ChangeStatus = ' and a.ToChange = "'.$ChangeStatus.'"';
      }

    }

    // if ($ChangeStatus != '') {
    //   $ChangeStatus = ' and a.ToChange = "'.$ChangeStatus.'"';
    // }

    if ($ta1 == '') {
      $sql = 'select * from (
                select a.*, b.Year,b.EmailPU,b.Pay_Cond,c.Name as NameSemester, d.Description,pp.ID as ID_payment_proof
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID
                left join db_finance.payment_proof as pp on a.ID = pp.ID_payment
                '.$NIM.$PTID.$prodiex.' and c.ID = ?
                and b.StatusStudentID in (3,2,8) '.$AddWhereStatusPayment.$ChangeStatus.'
                group by a.PTID,a.SemesterID,a.NPM
                UNION
                select a.*, b.Year,b.EmailPU,b.Pay_Cond,sa.Name as NameSemester, d.Description,pp.ID as ID_payment_proof
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_academic_years as c on a.SemesterID = c.SASemesterID
                join db_academic.semester_antara as sa on sa.ID = c.SASemesterID
                join db_finance.payment_type as d on a.PTID = d.ID
                left join db_finance.payment_proof as pp on a.ID = pp.ID_payment
                '.$NIM.$PTID.$prodiex.' and sa.SemesterID = ?
                and b.StatusStudentID in (3,2,8) '.$AddWhereStatusPayment.$ChangeStatus.'
                group by a.PTID,a.SemesterID,a.NPM
              ) aa
              order by ID_payment_proof desc,Status asc LIMIT '.$start. ', '.$limit; // and c.ID = ?
      $query=$this->db->query($sql, array($SemesterID,$SemesterID))->result_array();

    }
    else
    {
      $sql = 'select * from (
                select a.*, b.Year,b.EmailPU,b.Pay_Cond,c.Name as NameSemester, d.Description,pp.ID as ID_payment_proof
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID
                left join db_finance.payment_proof as pp on a.ID = pp.ID_payment
                '.$NIM.$PTID.$prodiex.' and b.Year = ? and c.ID = ?
                and b.StatusStudentID in (3,2,8) '.$AddWhereStatusPayment.$ChangeStatus.'
                group by a.PTID,a.SemesterID,a.NPM
                UNION
                select a.*, b.Year,b.EmailPU,b.Pay_Cond,sa.Name as NameSemester, d.Description,pp.ID as ID_payment_proof
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.sa_academic_years as c on a.SemesterID = c.SASemesterID
                join db_academic.semester_antara as sa on sa.ID = c.SASemesterID
                join db_finance.payment_type as d on a.PTID = d.ID
                left join db_finance.payment_proof as pp on a.ID = pp.ID_payment
                '.$NIM.$PTID.$prodiex.' and b.Year = ? and sa.SemesterID = ?
                and b.StatusStudentID in (3,2,8) '.$AddWhereStatusPayment.$ChangeStatus.'
                group by a.PTID,a.SemesterID,a.NPM
              ) aa
               order by ID_payment_proof desc,Status asc LIMIT '.$start. ', '.$limit; // and c.ID = ?
      $query=$this->db->query($sql, array($ta1,$SemesterID,$ta1,$SemesterID))->result_array();
    }
    // print_r($sql);die();
    // get Number VA Mahasiswa
        $Const_VA = $this->m_master->showData_array('db_va.master_va');

    // get all data to join db ta
    for ($i=0; $i < count($query); $i++) {
      $Year = $query[$i]['Year'];
      $db = 'ta_'.$Year.'.students';
      $dt = $this->m_master->caribasedprimary($db,'NPM',$query[$i]['NPM']);
      // get IPS Mahasiswa
         $IPS = $this->getIPSMahasiswaBySemester('ta_'.$Year,$query[$i]['NPM'],$SemesterID);

      // get IPK Mahasiswa
         // $IPK = $this->getIPKMahasiswa('ta_'.$Year,$query[$i]['NPM']);
         $IPK = $this->getIPKMahasiswaBySemester('ta_'.$Year,$query[$i]['NPM'],$SemesterID);

      // ge VA Mahasiwa
         $VA = $Const_VA[0]['Const_VA'].$query[$i]['NPM'];

      // get sks yang diambil
         $Credit = $this->getSKSMahasiswaBySemester('ta_'.$Year,$query[$i]['NPM'],$SemesterID);
         if ($query[$i]['PTID'] == 5 || $query[$i]['PTID'] == 6) { // Semester Antara
           $Credit = $this->getSKSMahasiswaBySemester_antara('ta_'.$Year,$query[$i]['NPM'],$query[$i]['SemesterID']);
         }

      // cek cancel
         $cancelPay = $this->getCancel($query[$i]['PTID'],$query[$i]['SemesterID'],$query[$i]['NPM']);
         $potonganLain = $this->getPotonganLain($query[$i]['PTID'],$query[$i]['SemesterID'],$query[$i]['NPM']);

      // cek Payment Proof
              $payment_proof = $this->m_master->caribasedprimary('db_finance.payment_proof','ID_payment',$query[$i]['ID']);
              for ($z=0; $z < count($payment_proof); $z++) {
                  // get nama bank
                  $G_bank = $this->m_master->caribasedprimary('db_finance.bank','ID',$payment_proof[$z]['ID_bank']);
                  $payment_proof[$z]['NmBank'] = $G_bank[0]['Name'];
              }

      if($prodi == '' || $prodi == Null){
        $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);

        $arr[] = array(
            'PaymentID' => $query[$i]['ID'],
            'PTID'  => $query[$i]['PTID'],
            'PTIDDesc' => $query[$i]['Description'],
            'SemesterID' => $query[$i]['SemesterID'],
            'SemesterName' => $query[$i]['NameSemester'],
            'NPM' => $query[$i]['NPM'],
            'Nama' => $dt[0]['Name'],
            'EmailPU' => $query[$i]['EmailPU'],
            'InvoicePayment' => $query[$i]['Invoice'],
            'Discount' => $query[$i]['Discount'],
            'StatusPayment' => $query[$i]['Status'],
            'ProdiID' => $dt[0]['ProdiID'],
            'ProdiEng' => $ProdiEng[0]['NameEng'],
            'Year' => $Year,
            'IPS' => $IPS,
            'IPK' => $IPK,
            'DetailPayment' => $this->detail_payment_std($query[$i]['ID']),
            'VA' => $VA,
            'Credit' => $Credit,
            'Pay_Cond' => $query[$i]['Pay_Cond'],
            'cancelPay' => $cancelPay,
            'payment_proof' => $payment_proof,
            'potonganLain' => $potonganLain,
        );
      }
      else
      {
        $prodi = explode('.', $prodi);
        $prodi = $prodi[0];
        if ($prodi == $dt[0]['ProdiID']) {
          $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);
          $arr[] = array(
              'PaymentID' => $query[$i]['ID'],
              'PTID'  => $query[$i]['PTID'],
              'PTIDDesc' => $query[$i]['Description'],
              'SemesterID' => $query[$i]['SemesterID'],
              'SemesterName' => $query[$i]['NameSemester'],
              'NPM' => $query[$i]['NPM'],
              'Nama' => $dt[0]['Name'],
              'EmailPU' => $query[$i]['EmailPU'],
              'InvoicePayment' => $query[$i]['Invoice'],
              'Discount' => $query[$i]['Discount'],
              'StatusPayment' => $query[$i]['Status'],
              'ProdiID' => $dt[0]['ProdiID'],
              'ProdiEng' => $ProdiEng[0]['NameEng'],
              'Year' => $Year,
              'IPS' => $IPS,
              'IPK' => $IPK,
              'DetailPayment' => $this->detail_payment_std($query[$i]['ID']),
              'VA' => $VA,
              'Credit' => $Credit,
              'Pay_Cond' => $query[$i]['Pay_Cond'],
              'cancelPay' => $cancelPay,
              'payment_proof' => $payment_proof,
              'potonganLain' => $potonganLain,
          );
        }
      }

    }
    return $arr;
   }

   public function updatePaymentApprove($Input)
   {
    for ($i=0; $i < count($Input); $i++) {
      $dataSave = array(
              'Status' =>"1",
              'UpdateAt' => date('Y-m-d H:i:s'),
              'UpdatedBy' => $this->session->userdata('NIP')
                      );
      $this->db->where('ID',$Input[$i]->PaymentID);
      $this->db->update('db_finance.payment', $dataSave);
    }
   }

   public function updatePaymentunApprove($Input)
   {
    $msg = '';
    for ($i=0; $i < count($Input); $i++) {
      // check Mahasiswa telah melakukan transaksi atau belum
       $NPM = $Input[$i]->NPM;
       $SemesterID = $Input[$i]->semester;
       $PTID = $Input[$i]->PTID;
       $sql = 'select count(*) as total from db_academic.std_krs where SemesterID = ? and NPM = ?';
       $query=$this->db->query($sql, array($SemesterID,$NPM))->result_array();
       $count = $query[0]['total'];
       if ($PTID == 2) {
         if ($count == 0) {
           $dataSave = array(
                   'Status' =>"0",
                   'UpdateAt' => date('Y-m-d H:i:s'),
                   'UpdatedBy' => $this->session->userdata('NIP'),
                           );
           $this->db->where('ID',$Input[$i]->PaymentID);
           $this->db->update('db_finance.payment', $dataSave);
         }
         else
         {
          // if ($msg == '') {
          //   $msg = '<ul>';
          // }
          // $msg .= '<li>Proses UnApprove ditolak, Mohon cek Transaksi KRS pada Mahasiswa dengan NPM : '.$Input[$i]->NPM.'</li>';
          $msg = 'Mahasiswa telah melakukan KRS Online';
         }
       }
       else
       {
        $dataSave = array(
                'Status' =>"0",
                'UpdateAt' => date('Y-m-d H:i:s'),
                'UpdatedBy' => $this->session->userdata('NIP'),
                        );
        $this->db->where('ID',$Input[$i]->PaymentID);
        $this->db->update('db_finance.payment', $dataSave);
       }

    }

    return $msg;
   }

   public function updatePaymentunApprove_after_confirm($Input)
   {
    $msg = '';
    for ($i=0; $i < count($Input); $i++) {
      // check Mahasiswa telah melakukan transaksi atau belum
       $NPM = $Input[$i]->NPM;
       $SemesterID = $Input[$i]->semester;
       $PTID = $Input[$i]->PTID;
       $sql = 'select count(*) as total from db_academic.std_krs where SemesterID = ? and NPM = ?';
       $query=$this->db->query($sql, array($SemesterID,$NPM))->result_array();
       $count = $query[0]['total'];
       if ($PTID == 2) {
         $dataSave = array(
                 'Status' =>"0",
                 'UpdateAt' => date('Y-m-d H:i:s'),
                 'UpdatedBy' => $this->session->userdata('NIP'),
                         );
         $this->db->where('ID',$Input[$i]->PaymentID);
         $this->db->update('db_finance.payment', $dataSave);
       }
       else
       {
        $dataSave = array(
               'Status' =>"0",
               'UpdateAt' => date('Y-m-d H:i:s'),
               'UpdatedBy' => $this->session->userdata('NIP'),
                        );
        $this->db->where('ID',$Input[$i]->PaymentID);
        $this->db->update('db_finance.payment', $dataSave);
       }

    }

    return $msg;
   }

   public function assign_to_change_status_mhs($Input)
   {
    $msg = '';
    for ($i=0; $i < count($Input); $i++) {
      // check Mahasiswa telah melakukan transaksi atau belum
       $NPM = $Input[$i]->NPM;
       $SemesterID = $Input[$i]->semester;
       $PTID = $Input[$i]->PTID;
       $dataSave = array(
               'ToChange' =>1,
               'UpdateAt' => date('Y-m-d H:i:s'),
               'UpdatedBy' => $this->session->userdata('NIP'),
                       );
       $this->db->where('ID',$Input[$i]->PaymentID);
       $this->db->update('db_finance.payment', $dataSave);

    }

    return $msg;
   }

   public function update_payment_MHS($BilingID,$ID_payment)
   {
    // update payment_students
        $dataSave = array(
                'Status' =>1,
                'UpdateAt' => date('Y-m-d H:i:s'),
                        );
        $this->db->where('BilingID',$BilingID);
        $this->db->update('db_finance.payment_students', $dataSave);

      $getData3 = $this->findDatapayment_studentsBaseID_payment($ID_payment);
      if (count($getData3) == 0) {
        $sql = 'select count(*) as total from db_finance.payment where Status = 0 and ID = ?';
        $query=$this->db->query($sql, array($ID_payment))->result_array();
        if ($query[0]['total'] == 0) {
            $dataSave = array(
                       'Status' =>"1",
                       'UpdateAt' => date('Y-m-d H:i:s'),
                       'UpdatedBy' => "0"
                               );
               $this->db->where('ID',$ID_payment);
               $this->db->update('db_finance.payment', $dataSave);
        }
      }

   }

   public function delete_id_table($ID,$table)
   {
       $sql = "delete from db_finance.".$table." where ID = ".$ID;
       $query=$this->db->query($sql, array());
   }

   public function inserData_master_tagihan_mhs($TypePembayaran,$Prodi,$Cost,$ClassOf,$Pay_Cond)
   {
    $dataSave = array(
        'PTID' => $TypePembayaran,
        'ProdiID' => $Prodi,
        'ClassOf' => $ClassOf,
        'Cost' => $Cost,
        'Pay_Cond' => $Pay_Cond
    );
      $this->db->insert('db_finance.tuition_fee', $dataSave);
   }

   public function editData_master_tagihan_mhs($TypePembayaran,$Prodi,$Cost,$ClassOf,$ID)
   {
      $dataSave = array(
          'PTID' => $TypePembayaran,
          'ProdiID' => $Prodi,
          'ClassOf' => $ClassOf,
          'Cost' => $Cost,
      );
      $this->db->where('ID',$ID);
      $this->db->update('db_finance.tuition_fee', $dataSave);
   }

   public function updateTagihanMhsList($input)
   {
    for ($i=0; $i < count($input); $i++) {
      $ID = $input[$i]->id;
      $Cost = $input[$i]->Cost;
      $dataSave = array(
          'Cost' => $Cost,
      );
      $this->db->where('ID',$ID);
      $this->db->update('db_finance.tuition_fee', $dataSave);
    }
   }

   public function deleteTagihanMHSByProdiYear($input)
   {
    $ProdiID = $input['ProdiID'];
    $ClassOf = $input['ClassOf'];
    $bintang = $input['bintang'];
    $bintang = strlen($bintang);
    $sql = "delete from db_finance.tuition_fee where ProdiID = ".$ProdiID.' and ClassOf = "'.$ClassOf.'" and Pay_Cond = ?';
    $query=$this->db->query($sql, array($bintang));
   }

   public function inserData_discount($Discount)
   {
      $dataSave = array(
          'Discount' => $Discount,
      );
      $this->db->insert('db_finance.discount', $dataSave);
   }

   public function editData_discount($Discount,$ID)
   {
    $dataSave = array(
        'Discount' => $Discount,
    );
    $this->db->where('ID',$ID);
    $this->db->update('db_finance.discount', $dataSave);
   }

   public function cancel_created_tagihan_mhs($input)
   {
    $this->load->model('master/m_master');
    $arr = array();
    $arr['msg'] = '';
    $now = date('Y-m-d H:i:s');
    for ($i=0; $i < count($input); $i++) {
      $PTID = $input[$i]->PTID;
      $SemesterID = $input[$i]->semester;
      $NPM = $input[$i]->NPM;
      // Closed VA dahulu
          // check Status VA
              // cari Biling ID
                if ($this->session->userdata('finance_auth_Policy_SYS') == 0) {
                  $bstatus = '';
                }
                else
                {
                  $bstatus = 'and b.Status  = 0';
                }
                $sql = 'select * from db_finance.payment as a join db_finance.payment_students as b
                        on a.ID = b.ID_payment where a.NPM = ? and a.SemesterID = ? and a.PTID = ? '.$bstatus.' order by b.ID asc limit 1';
                $query=$this->db->query($sql, array($NPM,$SemesterID,$PTID))->result_array();
                if (count($query) > 0 ) {
                  $BilingID = $query[0]['BilingID'];
                  if ($BilingID != 0) {
                    $checkVa = $this->checkBiling($BilingID);
                    // print_r($checkVa);
                    // die();
                    // va status  = 1 => active
                    // va status = 2 => Inactive
                    if ($checkVa['msg']['va_status'] != 2) {
                        // cancel VA
                       $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
                       $trx_amount = $getData[0]['trx_amount'];
                       $desc = 'Closed '.$getData[0]['description'];
                       $datetime_expired = $now;
                       $customer_name = $getData[0]['customer_name'];
                       $customer_email = $getData[0]['customer_email'];
                       $update = $this->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_students',$desc);
                       if ($update['status'] == 1) {
                         // triger VA closed berhasil, update va_log status = 2 // auto dari update_va_Payment
                         // delete data pada table payment dan payment_students
                            // action delete belum benar
                         // $this->delete_id_table($query[0]['ID_payment'],'payment_students');
                         $sqlDelete = "delete from db_finance.payment_students where ID_payment = ".$query[0]['ID_payment'];
                         $queryDelete=$this->db->query($sqlDelete, array());
                         $this->delete_id_table($query[0]['ID_payment'],'payment');

                       }
                       else
                       {
                         $arr['msg'] .= 'Va tidak bisa di cancel, error koneksi ke BNI <br>';
                       }
                    }
                    else
                    {
                         //$this->delete_id_table($query[0]['ID_payment'],'payment_students');
                         $sqlDelete = "delete from db_finance.payment_students where ID_payment = ".$query[0]['ID_payment'];
                         $queryDelete=$this->db->query($sqlDelete, array());
                         $this->delete_id_table($query[0]['ID_payment'],'payment');

                    }
                  }
                  else
                  {
                    $sqlDelete = "delete from db_finance.payment_students where ID_payment = ".$query[0]['ID_payment'];
                    $queryDelete=$this->db->query($sqlDelete, array());
                    $this->delete_id_table($query[0]['ID_payment'],'payment');
                  }

                }
        }
        return $arr;
   }

   public function cancel_created_tagihan_mhs2($input,$Reason)
   {
    $this->load->model('master/m_master');
    $arr = array();
    $arr['msg'] = '';
    $now = date('Y-m-d H:i:s');
    for ($i=0; $i < count($input); $i++) {
      $PTID = $input[$i]->PTID;
      $SemesterID = $input[$i]->semester;
      $NPM = $input[$i]->NPM;

      // clear potongan dahulu
      $this->db->where('NPM',$NPM);
      $this->db->where('PTID',$PTID);
      $this->db->where('SemesterID',$SemesterID);
      $this->db->delete('db_finance.payment_student_potongan');

      // Closed VA dahulu
          // check Status VA
              // cari Biling ID
                if ($this->session->userdata('finance_auth_Policy_SYS') == 0) {
                  $bstatus = '';
                }
                else
                {
                  $bstatus = 'and b.Status  = 0';
                }
                $sql = 'select a.*,b.ID as ID_payment_students,b.BilingID,b.ID_payment from db_finance.payment as a join db_finance.payment_students as b
                        on a.ID = b.ID_payment where a.NPM = ? and a.SemesterID = ? and a.PTID = ? '.$bstatus.' order by b.ID asc limit 1';
                $query=$this->db->query($sql, array($NPM,$SemesterID,$PTID))->result_array();
                // save reason
                $PTIDR = $query[0]['PTID'];
                $SemesterIDR = $query[0]['SemesterID'];
                $NPMR = $query[0]['NPM'];
                $dataSave = array(
                  'PTID' => $PTIDR,
                  'SemesterID' => $SemesterIDR,
                  'NPM' => $NPMR,
                  'Reason' => $Reason,
                  'CancelAt' => date('Y-m-d H:i:s'),
                  'CancelBy' => $this->session->userdata('NIP'),
                );
                $this->db->insert('db_finance.payment_s_cancel',$dataSave);

                $ID_payment_s_cancel = $this->db->insert_id();

                if (count($query) > 0 ) {
                  $BilingID = $query[0]['BilingID'];
                  if ($BilingID != 0) {
                    $checkVa = $this->checkBiling($BilingID);
                    // va status  = 1 => active
                    // va status = 2 => Inactive
                    if ($checkVa['msg']['va_status'] != 2) {
                        // cancel VA
                       $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
                       $trx_amount = $getData[0]['trx_amount'];
                       $desc = 'Closed '.$getData[0]['description'];
                       $datetime_expired = $now;
                       $customer_name = $getData[0]['customer_name'];
                       $customer_email = $getData[0]['customer_email'];
                       $update = $this->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_students',$desc);
                       if ($update['status'] == 1) {
                         // triger VA closed berhasil, update va_log status = 2 // auto dari update_va_Payment
                         // delete data pada table payment dan payment_students
                            // action delete belum benar
                         // $this->delete_id_table($query[0]['ID_payment'],'payment_students');
                         $sqlDelete = "delete from db_finance.payment_students where ID_payment = ".$query[0]['ID_payment'];
                         $queryDelete=$this->db->query($sqlDelete, array());
                         $this->delete_id_table($query[0]['ID_payment'],'payment');

                       }
                       else
                       {
                         $arr['msg'] .= 'Va tidak bisa di cancel, error koneksi ke BNI <br>';
                       }
                    }
                    else
                    {
                         //$this->delete_id_table($query[0]['ID_payment'],'payment_students');
                         $sqlDelete = "delete from db_finance.payment_students where ID_payment = ".$query[0]['ID_payment'];
                         $queryDelete=$this->db->query($sqlDelete, array());
                         $this->delete_id_table($query[0]['ID_payment'],'payment');

                    }
                  }
                  else
                  {
                    $sqlDelete = "delete from db_finance.payment_students where ID_payment = ".$query[0]['ID_payment'];
                    $queryDelete=$this->db->query($sqlDelete, array());
                    $this->delete_id_table($query[0]['ID_payment'],'payment');
                  }

                  for ($z=0; $z < count($query); $z++) { 
                    $ID_payment_students = $query[$z]['ID_payment_students'];
                    $dx = $this->m_master->caribasedprimary('db_finance.payment_student_details','ID_payment_students',$ID_payment_students);
                    for ($v=0; $v < count($dx); $v++) { 
                        $dataSaveRecycle = [
                            'ID_payment_s_cancel' =>   $ID_payment_s_cancel,
                            'UniqueGroupBy' =>  $dx[$v]['UniqueGroupBy']  ,
                            'Pay' =>  $dx[$v]['Pay'] ,
                            'Pay_Date' => $dx[$v]['Pay_Date']  ,
                            'P_Created_By' => $dx[$v]['Created_By']  ,
                            'P_Created_At' => $dx[$v]['Created_At']  ,
                        ];

                        $this->db->insert('db_finance.payment_student_details_cancel',$dataSaveRecycle);
                    }
                  }

                }
        }
        return $arr;
   }

   public function findDatapayment_studentsBaseID_payment($ID_payment,$Status = 0)
   {
    $sql = 'select * from db_finance.payment_students where ID_payment = ? and Status = ? order by ID asc';
    $query=$this->db->query($sql, array($ID_payment,$Status))->result_array();
    return $query;
   }

   public function updateCicilanMHS($BilingID,$trx_amount,$datetime_expired)
   {
    $dataSave = array(
            'Invoice' => $trx_amount,
            'Deadline' => $datetime_expired,
            'UpdateAt' => date('Y-m-d H:i:s'),
                    );
    $this->db->where('BilingID',$BilingID);
    $this->db->update('db_finance.payment_students', $dataSave);
   }

   public function updateCicilanMHS_admission($BilingID,$trx_amount,$datetime_expired)
   {
    $dataSave = array(
            'Invoice' => $trx_amount,
            'Deadline' => $datetime_expired,
            // 'UpdateAt' => date('Y-m-d H:i:s'),
                    );
    $this->db->where('BilingID',$BilingID);
    $this->db->update('db_finance.payment_pre', $dataSave);
   }

   public function updatePaymentStudentsFromCicilan($BilingID,$ID)
   {
    $dataSave = array(
            'BilingID' => $BilingID,
                    );
    $this->db->where('ID',$ID);
    $this->db->update('db_finance.payment_students', $dataSave);
   }

   public function edit_cicilan_tagihan_mhs_submit($Input)
   {
    $this->load->model('master/m_master');
    $arr = array();
    $arr['msg']  = '';
    for ($i=0; $i < count($Input); $i++) {
      // check yang memiliki bilingId
      // jika memiliki bilingID maka update VA, jika tidak maka update database aja
      if ($Input[$i]->BilingID != 0) {
        // update VA
        $BilingID = $Input[$i]->BilingID;
        $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
        // get datetime
        $expDatetime = $getData[0]['datetime_expired'];
        $now = date('Y-m-d H:i:s');
        $chkdate = $this->m_master->chkTgl($now,$expDatetime);
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;
        $customer_name = $getData[0]['customer_name'];
        $desc = 'Add,Expired Biling '.$getData[0]['description'];
        $customer_email = $getData[0]['customer_email'];
        $VA_number = $getData[0]['virtual_account'];
        if (!$chkdate) {
         $create_va_Payment = $this->create_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$VA_number,$desc,'db_finance.payment_students');
         if ($create_va_Payment['status']) {
           // update biling and Deadline di payment Student
           $dataSave = array(
                   'BilingID' =>$create_va_Payment['msg']['trx_id'],
                   'Invoice' => $trx_amount,
                   'Deadline' => $datetime_expired,
                   'UpdateAt' => date('Y-m-d H:i:s'),
                           );
           $this->db->where('BilingID',$BilingID);
           $this->db->update('db_finance.payment_students', $dataSave);
         }

        }
        else
        {
          $update = $this->m_finance->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_students',$desc);
          if ($update['status'] == 1) {
            // update data pada table db_finance.payment_students
              $this->m_finance->updateCicilanMHS($BilingID,$trx_amount,$datetime_expired);
          }
          else
          {
            $arr['msg'] .= 'Va tidak bisa di update, error koneksi ke BNI with Name : '.$customer_name.'<br>';
          }
        }

      }
      else
      {
        $BilingID = $Input[$i]->BilingID;
        $ID = $Input[$i]->ID;
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;

        // check data pembayaran
        $count_pembayaran = $this->db->where('ID_payment_students',$ID)->count_all_results('db_finance.payment_student_details');

        $number_cicilan = $this->edit_cicilan_tagihan_mhs_submit_get_number_cicilan($ID);

        if ($count_pembayaran == 0) {
           $this->m_finance->UpdateCicilanbyID($ID,$BilingID,$trx_amount,$datetime_expired);
        }
        else
        {
          $arr['msg'] .= 'Cicilan ke  : '.$number_cicilan.' telah dilakukan pembayaran <br>';
        }
      }
    }

    return $arr;

   }

   private function edit_cicilan_tagihan_mhs_submit_get_number_cicilan($ID_payment_students){
    $x = -1;
    $dt = $this->m_master->caribasedprimary('db_finance.payment_students','ID',$ID_payment_students);

    $ID_payment =  $dt[0]['ID_payment'];

    $get =  $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$ID_payment);

    for ($i=0; $i < count($get); $i++) { 
      if ($get[$i]['ID'] == $ID_payment_students) {
        $x = $i+1;  
        break;
      }
    }

    if ($x == -1) {
      return false;
    }
    else
    {
      return $x;
    }
   }

   public function UpdateCicilanbyID($ID,$BilingID,$trx_amount,$datetime_expired)
   {
    $dataSave = array(
            'Invoice' => $trx_amount,
            'Deadline' => $datetime_expired,
            'UpdateAt' => date('Y-m-d H:i:s'),
            'BilingID' => $BilingID,
                    );
    $this->db->where('ID',$ID);
    $this->db->update('db_finance.payment_students', $dataSave);
   }

   public function UpdateCicilan_admission_byID($ID,$BilingID,$trx_amount,$datetime_expired)
   {
    $dataSave = array(
            'Invoice' => $trx_amount,
            'Deadline' => $datetime_expired,
            'UpdateAt' => date('Y-m-d H:i:s'),
            'BilingID' => $BilingID,
                    );
    $this->db->where('ID',$ID);
    $this->db->update('db_finance.payment_pre', $dataSave);
   }

   public function UpdateCicilan_admission_byID2($ID,$BilingID,$trx_amount,$datetime_expired)
   {
    $dataSave = array(
            'Invoice' => $trx_amount,
            'Deadline' => $datetime_expired,
            'BilingID' => $BilingID,
                    );
    $this->db->where('ID',$ID);
    $this->db->update('db_finance.payment_pre', $dataSave);
   }

   public function delete_cicilan_tagihan_mhs_submit($Input)
   {
    $this->load->model('master/m_master');
    $arr = array();
    $arr['msg']  = '';
    $ID_payment = '';
    for ($i=0; $i < count($Input); $i++) {
      // check yang memiliki bilingId
      // jika memiliki bilingID maka update VA, jika tidak maka update database aja
      if ($Input[$i]->BilingID != 0) {
        $BilingID = $Input[$i]->BilingID;
        $getData0= $this->m_master->caribasedprimary('db_finance.payment_students','BilingID',$BilingID);
        $ID_payment = $getData0[0]['ID_payment'];
        $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
        $desc = 'Closed '.$getData[0]['description'];
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = date('Y-m-d H:i:s');
        $customer_name = $getData[0]['customer_name'];
        $customer_email = $getData[0]['customer_email'];
        $update = $this->m_finance->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_students',$desc);
        if ($update['status'] == 1) {
          // delete data pada table db_finance.payment_students
            $ID = $Input[$i]->ID;
            $this->m_finance->delete_id_table($ID,'payment_students');
        }
        else
        {
          // $arr['msg'] .= 'Va tidak bisa di update, error koneksi ke BNI with Name : '.$customer_name.'<br>';
          $ID = $Input[$i]->ID;
          $this->m_finance->delete_id_table($ID,'payment_students');
          $arr['msg'] .= '';
        }
      }
      else
      {
        $BilingID = $Input[$i]->BilingID;
        $ID = $Input[$i]->ID;
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;

        $getData0= $this->m_master->caribasedprimary('db_finance.payment_students','ID',$ID);
        $ID_payment = $getData0[0]['ID_payment'];
        $this->m_finance->delete_id_table($ID,'payment_students');
      }
    }
    $this->m_finance->delete_id_table($ID_payment,'payment');
    return $arr;
   }

   public function get_pembayaran_mhs($ta,$prodi,$PTID,$NIM,$Semester,$limit, $start)
   {
    error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');

    // join dengan table auth terlebih dahulu
    $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    $NIM = ($NIM == '' || $NIM == Null) ? 'where a.NPM like "%"' : ' where  a.NPM = '.$NIM;
    $Semester = ($Semester == '' || $Semester == Null) ? '' : ' and a.SemesterID = '.$Semester;
    /*$SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
    $SemesterID = $SemesterID[0]['ID'];*/
    if ($ta == '') {
      $ta1 = $ta;
    }
    else
    {
      $ta = explode('.', $ta);
      $ta1 = $ta[1];
    }

    if ($ta1 == '') {
      $sql = 'select a.*, b.Year,b.EmailPU,c.Name as NameSemester, d.Description,e.ID as ID_payment_students,e.BilingID,e.Invoice as InvoiceStudents,e.UpdateAt as TimeBayar
              from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
              join db_academic.semester as c on a.SemesterID = c.ID
              join db_finance.payment_type as d on a.PTID = d.ID join db_finance.payment_students as e on a.ID = e.ID_payment '.$NIM.$PTID.$Semester.'
              and e.Status = 1 order by e.ID asc LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array())->result_array();

    }
    else
    {
      $sql = 'select a.*, b.Year,b.EmailPU,c.Name as NameSemester, d.Description,e.ID as ID_payment_students,e.BilingID,e.Invoice as InvoiceStudents,e.UpdateAt as TimeBayar
              from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
              join db_academic.semester as c on a.SemesterID = c.ID
              join db_finance.payment_type as d on a.PTID = d.ID join db_finance.payment_students as e on a.ID = e.ID_payment '.$NIM.$PTID.$Semester.' and b.Year = ? and e.Status = 1
              order by e.ID asc LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array($ta1))->result_array();
    }

    // get Number VA Mahasiswa
        $Const_VA = $this->m_master->showData_array('db_va.master_va');

    for ($i=0; $i < count($query); $i++) {
      $Year = $query[$i]['Year'];
      $db = 'ta_'.$Year.'.students';
      $dt = $this->m_master->caribasedprimary($db,'NPM',$query[$i]['NPM']);
      if($prodi == '' || $prodi == Null){
        $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);

        // get IPS Mahasiswa
          $IPS = $this->getIPSMahasiswa('ta_'.$Year,$query[$i]['NPM']);

        // get IPS Mahasiswa
          $IPK = $this->getIPKMahasiswa('ta_'.$Year,$query[$i]['NPM']);

          // ge VA Mahasiwa
             $VA = $Const_VA[0]['Const_VA'].$query[$i]['NPM'];

        // cek cicilan atau tidak
          $DetailPayment = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$query[$i]['ID']);
          if (count($DetailPayment) == 1) {
            $Cicilan = 'Tidak Cicilan';
          }
          else
          {
            $a = 1;
            for ($j=0; $j < count($DetailPayment); $j++) {
              if ($DetailPayment[$j]['ID'] == $query[$i]['ID_payment_students']) {
                // get all data to join db ta
                $Cicilan = 'Cicilan ke ';
                // print_r($sql);
                  $Cicilan .= $a;
                  break;
              }
              $a++;
            }
          }


        $arr[] = array(
            'PaymentID' => $query[$i]['ID'],
            'PTID'  => $query[$i]['PTID'],
            'PTIDDesc' => $query[$i]['Description'],
            'SemesterID' => $query[$i]['SemesterID'],
            'SemesterName' => $query[$i]['NameSemester'],
            'NPM' => $query[$i]['NPM'],
            'Nama' => $dt[0]['Name'],
            'EmailPU' => $query[$i]['EmailPU'],
            'InvoicePayment' => $query[$i]['Invoice'],
            'Discount' => $query[$i]['Discount'],
            'StatusPayment' => $query[$i]['Status'],
            'ProdiID' => $dt[0]['ProdiID'],
            'ProdiEng' => $ProdiEng[0]['NameEng'],
            'Year' => $Year,
            'IPS' => $IPS,
            'IPK' => $IPK,
            'Cicilan' => $Cicilan,
            'BilingID' => $query[$i]['BilingID'],
            'Time' => $query[$i]['TimeBayar'],
            'InvoiceStudents' => $query[$i]['InvoiceStudents'],
            'ID_payment_students' => $query[$i]['ID_payment_students'],
            'VA' => $VA,
        );
      }
      else
      {
        $prodi = explode('.', $prodi);
        $prodi = $prodi[0];
        if ($prodi == $dt[0]['ProdiID']) {
          $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);
          $arr[] = array(
              'PaymentID' => $query[$i]['ID'],
              'PTID'  => $query[$i]['PTID'],
              'PTIDDesc' => $query[$i]['Description'],
              'SemesterID' => $query[$i]['SemesterID'],
              'SemesterName' => $query[$i]['NameSemester'],
              'NPM' => $query[$i]['NPM'],
              'Nama' => $dt[0]['Name'],
              'EmailPU' => $query[$i]['EmailPU'],
              'InvoicePayment' => $query[$i]['Invoice'],
              'Discount' => $query[$i]['Discount'],
              'StatusPayment' => $query[$i]['Status'],
              'ProdiID' => $dt[0]['ProdiID'],
              'ProdiEng' => $ProdiEng[0]['NameEng'],
              'Year' => $Year,
              'Cicilan' => $Cicilan,
              'BilingID' => $query[$i]['BilingID'],
              'Time' => $query[$i]['TimeBayar'],
              'InvoiceStudents' => $query[$i]['InvoiceStudents'],
              'ID_payment_students' => $query[$i]['ID_payment_students'],
              'VA' => $VA,
          );
        }
      }

    }
    return $arr;
   }

   public function checkMasterTagihanExisting($TypePembayaran,$Prodi,$ClassOf,$Pay_Cond)
   {
    $sql= 'select * from db_finance.tuition_fee where PTID = ? and ProdiID = ? and ClassOf = ? and Pay_Cond = ?';
    $query=$this->db->query($sql, array($TypePembayaran,$Prodi,$ClassOf,$Pay_Cond))->result_array();
    if (count($query) > 0) {
      // existing
      return false;
    }
    else
    {
      // nothing
      return true;
    }
   }

   public function cari_va($VA)
   {
    $rs = array('msg' => '');
    $sql = 'select * from db_va.va_log where virtual_account = ? order by ID desc limit 1';
    $query=$this->db->query($sql, array($VA))->result_array();
    if (count($query) > 0) {
      $sql1 = 'select * from db_va.va_log where virtual_account = ? and Status != 1 order by ID desc limit 1';
      $query1=$this->db->query($sql1, array($VA))->result_array();
      if (count($query1) > 0) {
        // check datetime expired sudah melewati waktu atau belum
        $datetime_expired = $query1[0]['datetime_expired'];
        // print_r($datetime_expired);
        $sql2 = 'select * from (
                  select now() as ac
                )aa
                where ? > ac';
                $query2=$this->db->query($sql2, array($datetime_expired))->result_array();
            if (count($query2) > 0) {
                  // get bilingID
                  $this->load->model('master/m_master');
                  switch ($query1[0]['routes_table']) {
                    case 'db_finance.payment_students':
                      $getData = $this->m_master->caribasedprimary('db_finance.payment_students','BilingID',$query1[0]['trx_id']);
                      if ($getData > 0) {
                        // get Informasi Mahasiswa
                           $GetPayment = $this->m_master->caribasedprimary('db_finance.payment','ID',$getData[0]['ID_payment']);
                           $NPM = $GetPayment[0]['NPM'];
                           $data = $this->m_master->PaymentgetMahasiswaByNPM($NPM);
                           $PTIDDesc = $data['PTIDDesc'];
                           $SemesterName = $data['SemesterName'];
                           $Nama = $data['Nama'];
                           $EmailPU = $data['EmailPU'];
                           $ProdiEng = $data['ProdiEng'];
                           $Invoice = $getData[0]['Invoice'];
                           $BilingID = $query1[0]['trx_id'];
                           $Status = 'Active';
                           $rs['data'] = array(
                                'ProdiEng' => $ProdiEng,
                                'PTIDDesc' => $PTIDDesc,
                                'Nama' => $Nama,
                                'NPM' => $NPM,
                                'VA' => $VA,
                                'EmailPU' => $EmailPU,
                                'BilingID' => $BilingID,
                                'Invoice' => $Invoice,
                                'Status' => $Status,
                                'Expired' => $datetime_expired,
                                'SemesterName' => $SemesterName

                           );
                      }
                      else
                      {
                        $rs['msg'] = 'VA dengan number '.$VA.' available';
                      }

                      break;
                    case 'db_admission.register':
                      $getData = $this->m_master->caribasedprimary('db_admission.register','BilingID',$query1[0]['trx_id']);
                      if ($getData > 0) {
                        // get Informasi
                           //  $arr_temp = array();
                           //  foreach ($getData as $key => $value) {
                           //    $arr_temp[] = $value;
                           //  }
                           // $rs['data'] = $arr_temp;
                            $rs['data'] = array(
                                 'ProdiEng' => 'Register Online',
                                 'PTIDDesc' => 'Pembayaran Formulir Online',
                                 'Nama' => $getData[0]['Name'],
                                 'NPM' => 'RegisterOnline',
                                 'VA' => $VA,
                                 'EmailPU' => $getData[0]['Email'],
                                 'BilingID' => $getData[0]['BilingID'],
                                 'Invoice' => $getData[0]['PriceFormulir'],
                                 'Status' => 'Active',
                                 'Expired' => $getData[0]['Datetime_expired'],
                                 'SemesterName' => ''

                            );
                      }
                      else
                      {
                        $rs['msg'] = 'VA dengan number '.$VA.' available';
                      }

                      break;
                    case 'db_finance.payment_pre':
                      $getData = $this->m_master->caribasedprimary('db_finance.payment_pre','BilingID',$query1[0]['trx_id']);
                      if ($getData > 0) {
                        // get Informasi Mahasiswa
                           $this->load->model('admission/m_admission');
                           $GetInformasi = $this->m_admission->getDataPersonal($getData[0]['ID_register_formulir']);
                           $NPM = $GetInformasi[0]['FormulirCode'];
                           $PTIDDesc = 'Pembayaran Uang Masuk Kuliah';
                           $SemesterName = '';
                           $Nama = $GetInformasi[0]['Name'];
                           $EmailPU = $GetInformasi[0]['Email'];
                           $ProdiEng = $GetInformasi[0]['NameProdyEng'];
                           $Invoice = $getData[0]['Invoice'];
                           $BilingID = $query1[0]['trx_id'];
                           $Status = 'Active';
                           $rs['data'] = array(
                                'ProdiEng' => $ProdiEng,
                                'PTIDDesc' => $PTIDDesc,
                                'Nama' => $Nama,
                                'NPM' => $NPM,
                                'VA' => $VA,
                                'EmailPU' => $EmailPU,
                                'BilingID' => $BilingID,
                                'Invoice' => $Invoice,
                                'Status' => $Status,
                                'Expired' => $datetime_expired,
                                'SemesterName' => $SemesterName

                           );
                      }
                      else
                      {
                        $rs['msg'] = 'VA dengan number '.$VA.' available';
                      }

                      break;
                    default:
                      # code...
                      break;
                  }

            }
            else{
              $rs['msg'] = 'VA dengan number '.$VA.' available';
            }
      }
      else
      {
        $rs['msg'] = 'VA dengan number '.$VA.' available';
      }
    }
    else
    {
      $rs['msg'] = 'VA dengan number '.$VA.' tidak ditemukan pada transaksi database';
    }

    return $rs;

   }

   public function findPaymentBaseUnique($PTID,$SemesterID,$NPM)
   {
    $sql = 'select * from db_finance.payment where PTID = ? and SemesterID = ? and NPM = ?';
    $query=$this->db->query($sql, array($PTID,$SemesterID,$NPM))->result_array();
    return $query;
   }

   public function count_get_list_telat_bayar_mhs($ta,$prodi,$PTID,$NIM)
   {
    error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');

    // join dengan table auth terlebih dahulu
    $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    $NIM = ($NIM == '' || $NIM == Null) ? 'where a.NPM like "%"' : ' where  a.NPM = '.$NIM;
    $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
    $SemesterID = $SemesterID[0]['ID'];
    if ($ta == '') {
      $ta1 = $ta;
    }
    else
    {
      $ta = explode('.', $ta);
      $ta1 = $ta[1];
    }

    if ($ta1 == '') {
      $sql = 'select count(*) as total
              from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
              join db_academic.semester as c on a.SemesterID = c.ID
              join db_finance.payment_type as d on a.PTID = d.ID join db_finance.payment_students as e
              on a.ID = e.ID_payment '.$NIM.$PTID.' and c.ID = ?  and e.Status = 0 and DATE_FORMAT(e.Deadline,"%Y-%m-%d") <= curdate() group by a.ID';
      $query=$this->db->query($sql, array($SemesterID))->result_array();
    }
    else
    {
      $sql = 'select count(*) as total
              from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
              join db_academic.semester as c on a.SemesterID = c.ID
              join db_finance.payment_type as d on a.PTID = d.ID
              join db_finance.payment_students as e
              on a.ID = e.ID_payment
              '.$NIM.$PTID.' and b.Year = ? and c.ID = ? and e.Status = 0 and DATE_FORMAT(e.Deadline,"%Y-%m-%d") <= curdate() group by a.ID';
      $query=$this->db->query($sql, array($ta1,$SemesterID))->result_array();
    }

    return $query[0]['total'];

   }

   public function get_list_telat_bayar_mhs($ta,$prodi,$PTID,$NIM,$limit, $start)
   {
      // error_reporting(0);
      $arr = array();
      $this->load->model('master/m_master');

      // join dengan table auth terlebih dahulu
      $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
      $NIM = ($NIM == '' || $NIM == Null) ? 'where a.NPM like "%"' : ' where  a.NPM = '.$NIM;
      $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
      $SemesterID = $SemesterID[0]['ID'];
      if ($ta == '') {
        $ta1 = $ta;
      }
      else
      {
        $ta = explode('.', $ta);
        $ta1 = $ta[1];
      }

      if ($ta1 == '') {
        $sql = 'select a.*, b.Year,b.EmailPU,b.Pay_Cond,c.Name as NameSemester, d.Description
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID join db_finance.payment_students as e
                on a.ID = e.ID_payment '.$NIM.$PTID.' and c.ID = ?  and e.Status = 0 and DATE_FORMAT(e.Deadline,"%Y-%m-%d") <= curdate() group by a.ID LIMIT '.$start. ', '.$limit;
        $query=$this->db->query($sql, array($SemesterID))->result_array();
      }
      else
      {
        $sql = 'select a.*, b.Year,b.EmailPU,b.Pay_Cond,c.Name as NameSemester, d.Description
                from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                join db_academic.semester as c on a.SemesterID = c.ID
                join db_finance.payment_type as d on a.PTID = d.ID
                join db_finance.payment_students as e
                on a.ID = e.ID_payment
                '.$NIM.$PTID.' and b.Year = ? and c.ID = ? and e.Status = 0 and DATE_FORMAT(e.Deadline,"%Y-%m-%d") <= curdate() group by a.ID LIMIT '.$start. ', '.$limit;
        $query=$this->db->query($sql, array($ta1,$SemesterID))->result_array();
      }

      // get Number VA Mahasiswa
          $Const_VA = $this->m_master->showData_array('db_va.master_va');

      // get all data to join db ta
      for ($i=0; $i < count($query); $i++) {
        $Year = $query[$i]['Year'];
        $db = 'ta_'.$Year.'.students';
        $dt = $this->m_master->caribasedprimary($db,'NPM',$query[$i]['NPM']);

        // get VA Mahasiwa
           $VA = $Const_VA[0]['Const_VA'].$query[$i]['NPM'];

        if($prodi == '' || $prodi == Null){
          $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);
          $sql1 = '';
          $arr[] = array(
              'PaymentID' => $query[$i]['ID'],
              'PTID'  => $query[$i]['PTID'],
              'PTIDDesc' => $query[$i]['Description'],
              'SemesterID' => $query[$i]['SemesterID'],
              'SemesterName' => $query[$i]['NameSemester'],
              'NPM' => $query[$i]['NPM'],
              'Nama' => $dt[0]['Name'],
              'EmailPU' => $query[$i]['EmailPU'],
              'InvoicePayment' => $query[$i]['Invoice'],
              'ProdiID' => $dt[0]['ProdiID'],
              'ProdiEng' => $ProdiEng[0]['NameEng'],
              'Year' => $Year,
              'DetailPayment' => $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$query[$i]['ID']),
              'VA' => $VA,
              'Pay_Cond' => $query[$i]['Pay_Cond'],
          );
        }
        else
        {
          $prodi = explode('.', $prodi);
          $prodi = $prodi[0];
          if ($prodi == $dt[0]['ProdiID']) {
            $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$dt[0]['ProdiID']);
            $arr[] = array(
                'PaymentID' => $query[$i]['ID'],
              'PTID'  => $query[$i]['PTID'],
              'PTIDDesc' => $query[$i]['Description'],
              'SemesterID' => $query[$i]['SemesterID'],
              'SemesterName' => $query[$i]['NameSemester'],
              'NPM' => $query[$i]['NPM'],
              'Nama' => $dt[0]['Name'],
              'EmailPU' => $query[$i]['EmailPU'],
              'InvoicePayment' => $query[$i]['Invoice'],
              'ProdiID' => $dt[0]['ProdiID'],
              'ProdiEng' => $ProdiEng[0]['NameEng'],
              'Year' => $Year,
              'DetailPayment' => $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$query[$i]['ID']),
              'VA' => $VA,
              'Pay_Cond' => $query[$i]['Pay_Cond'],
            );
          }
        }

      }
      return $arr;
   }

   public function GroupingNPM($data)
   {
       $temp = array();
       $rs = array();
       for ($i=0; $i < count($data); $i++) {
           $find = 0;
           for ($k=0; $k < count($temp); $k++) {
                if ($data[$i]->NPM == $temp[$k]['NPM']) {
                    $find = 1;
                    // print_r('$data[$i]->NPM = '.$data[$i]->NPM.' $temp[$k]["NPM"] = '. $temp[$k]['NPM'].' ');
                    break;
                }
           }

           if ($find == 0) {
               $temp2 = array('NPM' => $data[$i]->NPM,'PTID' => $data[$i]->PTID, 'InvoicePayment' => $data[$i]->InvoicePayment, 'InvoiceStudents' => $data[$i]->InvoiceStudents,'SemesterID' => $data[$i]->SemesterID,'PaymentID' => $data[$i]->PaymentID,'Nama' => $data[$i]->Nama,'ProdiEng' => $data[$i]->ProdiEng);
               for ($j=($i+1); $j <count($data) ; $j=$j+1) {
                       if ($data[$i]->NPM == $data[$j]->NPM) {
                           if ($data[$i]->PTID == $data[$j]->PTID) {
                               $InvoiceStudents = $data[$j]->InvoiceStudents + $temp2['InvoiceStudents'];
                               $temp2['InvoiceStudents'] = $InvoiceStudents;
                           }
                       }

               }
               $temp[] = $temp2;
           }

       }

       // print_r($temp);

       for ($i=0; $i < count($temp); $i++) {
           $find = 0;
           for ($k=0; $k < count($rs); $k++) {
                if ($temp[$i]['NPM'] == $rs[$k]['NPM']) {
                    $find = 1;
                    break;
                }
           }
           if ($find == 0) {
                  $temp2 = array('NPM' => $temp[$i]['NPM'],'Nama' => $temp[$i]['Nama'],'ProdiEng' => $temp[$i]['ProdiEng'],'SPP' => '', 'SPPKet' => '', 'BPP' => '','BPPKet' => '', 'Credit' => '','CreditKet' => '','Another' => '','AnotherKet' => '');
                  if ($temp[$i]['PTID'] == 1) {
                       $temp2['SPP'] = $temp[$i]['InvoiceStudents'];
                       if ($temp[$i]['InvoiceStudents'] >= $temp[$i]['InvoicePayment']) {
                           $temp2['SPPKet'] = 'Lunas';
                       }
                       else
                       {
                           $temp2['SPPKet'] = 'Belum Lunas';
                       }
                  }
                  elseif ($temp[$i]['PTID'] == 2) {
                       $temp2['BPP'] = $temp[$i]['InvoiceStudents'];
                       if ($temp[$i]['InvoiceStudents'] >= $temp[$i]['InvoicePayment']) {
                           $temp2['BPPKet'] = 'Lunas';
                       }
                       else
                       {
                           $temp2['BPPKet'] = 'Belum Lunas';
                       }
                  }
                  elseif ($temp[$i]['PTID'] == 3) {
                       $temp2['Credit'] = $temp[$i]['InvoiceStudents'];
                       if ($temp[$i]['InvoiceStudents'] >= $temp[$i]['InvoicePayment']) {
                           $temp2['CreditKet'] = 'Lunas';
                       }
                       else
                       {
                           $temp2['CreditKet'] = 'Belum Lunas';
                       }
                  }
                  elseif ($temp[$i]['PTID'] == 4) {
                       $temp2['Another'] = $temp[$i]['InvoiceStudents'];
                       if ($temp[$i]['InvoiceStudents'] >= $temp[$i]['InvoicePayment']) {
                           $temp2['AnotherKet'] = 'Lunas';
                       }
                       else
                       {
                           $temp2['AnotherKet'] = 'Belum Lunas';
                       }
                  }

                   $SemesterID = $temp[$i]['SemesterID'];
                   $NPM = $temp[$i]['NPM'];

                   for ($j=($i+1); $j < count($temp); $j = $j +1) {
                       if ($temp[$i]['NPM']== $temp[$j]['NPM']) {
                         if ($temp[$j]['PTID'] == 1) {
                              // $temp2['SPP'] = $temp[$j]['InvoiceStudents'];
                              $PTID = $temp[$j]['PTID'];

                              $Payment =$this->m_finance->findPaymentBaseUnique($PTID,$SemesterID,$NPM);
                              $PaymentID = $Payment[0]['ID'];

                              $payment_students = $this->m_master->caribasedprimary('db_admission.payment_students','ID_payment',$PaymentID);
                              $InvoiceStudents = 0;
                              $InvoicePayment = $Payment[0]['Invoice'];
                              for ($z=0; $z < count($payment_students); $z++) {
                                   $InvoiceStudents = $InvoiceStudents + $payment_students[$z]['Invoice'];
                              }

                              $temp2['SPP'] = $InvoiceStudents;
                              if ($InvoiceStudents >= $InvoicePayment) {
                                  $temp2['SPPKet'] = 'Lunas';
                              }
                              else
                              {
                                  $temp2['SPPKet'] = 'Belum Lunas';
                              }
                              /*if ($temp[$j]['InvoiceStudents'] >= $temp[$j]['InvoicePayment']) {
                                  $temp2['SPPKet'] = 'Lunas';
                              }
                              else
                              {
                                  $temp2['SPPKet'] = 'Belum Lunas';
                              }*/
                         }
                         elseif ($temp[$j]['PTID'] == 2) {
                               $PTID = $temp[$j]['PTID'];

                               $Payment =$this->m_finance->findPaymentBaseUnique($PTID,$SemesterID,$NPM);
                               $PaymentID = $Payment[0]['ID'];

                               $payment_students = $this->m_master->caribasedprimary('db_admission.payment_students','ID_payment',$PaymentID);
                               $InvoiceStudents = 0;
                               $InvoicePayment = $Payment[0]['Invoice'];
                               for ($z=0; $z < count($payment_students); $z++) {
                                    $InvoiceStudents = $InvoiceStudents + $payment_students[$z]['Invoice'];
                               }

                               $temp2['BPP'] = $InvoiceStudents;
                               if ($InvoiceStudents >= $InvoicePayment) {
                                   $temp2['BPPKet'] = 'Lunas';
                               }
                               else
                               {
                                   $temp2['BPPKet'] = 'Belum Lunas';
                               }
                              /*$temp2['BPP'] = $temp[$j]['InvoiceStudents'];
                              if ($temp[$j]['InvoiceStudents'] >= $temp[$j]['InvoicePayment']) {
                                  $temp2['BPPKet'] = 'Lunas';
                              }
                              else
                              {
                                  $temp2['BPPKet'] = 'Belum Lunas';
                              }*/
                         }
                         elseif ($temp[$j]['PTID'] == 3) {
                               $PTID = $temp[$j]['PTID'];

                               $Payment =$this->m_finance->findPaymentBaseUnique($PTID,$SemesterID,$NPM);
                               $PaymentID = $Payment[0]['ID'];

                               $payment_students = $this->m_master->caribasedprimary('db_admission.payment_students','ID_payment',$PaymentID);
                               $InvoiceStudents = 0;
                               $InvoicePayment = $Payment[0]['Invoice'];
                               for ($z=0; $z < count($payment_students); $z++) {
                                    $InvoiceStudents = $InvoiceStudents + $payment_students[$z]['Invoice'];
                               }

                               $temp2['Credit'] = $InvoiceStudents;
                               if ($InvoiceStudents >= $InvoicePayment) {
                                   $temp2['CreditKet'] = 'Lunas';
                               }
                               else
                               {
                                   $temp2['CreditKet'] = 'Belum Lunas';
                               }
                              /*$temp2['Credit'] = $temp[$j]['InvoiceStudents'];
                              if ($temp[$j]['InvoiceStudents'] >= $temp[$j]['InvoicePayment']) {
                                  $temp2['CreditKet'] = 'Lunas';
                              }
                              else
                              {
                                  $temp2['CreditKet'] = 'Belum Lunas';
                              }*/
                         }
                         elseif ($temp[$i]['PTID'] == 4) {
                              $PTID = $temp[$j]['PTID'];

                              $Payment =$this->m_finance->findPaymentBaseUnique($PTID,$SemesterID,$NPM);
                              $PaymentID = $Payment[0]['ID'];

                              $payment_students = $this->m_master->caribasedprimary('db_admission.payment_students','ID_payment',$PaymentID);
                              $InvoiceStudents = 0;
                              $InvoicePayment = $Payment[0]['Invoice'];
                              for ($z=0; $z < count($payment_students); $z++) {
                                   $InvoiceStudents = $InvoiceStudents + $payment_students[$z]['Invoice'];
                              }

                              $temp2['Another'] = $InvoiceStudents;
                              if ($InvoiceStudents >= $InvoicePayment) {
                                  $temp2['AnotherKet'] = 'Lunas';
                              }
                              else
                              {
                                  $temp2['AnotherKet'] = 'Belum Lunas';
                              }

                              /*$temp2['Another'] = $temp[$j]['InvoiceStudents'];
                              if ($temp[$j]['InvoiceStudents'] >= $temp[$j]['InvoicePayment']) {
                                  $temp2['AnotherKet'] = 'Lunas';
                              }
                              else
                              {
                                  $temp2['AnotherKet'] = 'Belum Lunas';
                              }*/
                         }
                       }
                   }
                   $rs[] = $temp2;
           }
       }

      // print_r($rs);

       return $rs;
   }

   public function getPriceBaseBintang($selectPTID,$ProdiID,$Year,$Pay_Cond)
   {
    $sql = 'select * from db_finance.tuition_fee where PTID = ? and ProdiID = ? and ClassOf = ? and Pay_Cond = ?';
    $query=$this->db->query($sql, array($selectPTID,$ProdiID,$Year,$Pay_Cond))->result_array();
    return $query[0]['Cost'];
   }

   public function count_mahasiswa_list($ta,$prodi,$NPM)
   {
    // error_reporting(0);
    $this->load->model('master/m_master');
    $ta1 = explode('.', $ta);
    $ta = $ta1[1];
    $db = 'ta_'.$ta.'.students';
    $db2 = 'ta_'.$ta;
    $field = 'StatusStudentID';
    $value = 3;
    $NPM = ($NPM == "" || $NPM == null) ? '' : ' and a.NPM = "'.$NPM.'"';

    $queryAdd = '';
    if ($prodi == '') {
     $sql = 'select count(*) as total from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM
             where a.StatusStudentID in (3,2,8)   '.$NPM.$queryAdd.'
             order by a.NPM asc';
      // print_r($sql);
     $Data_mhs=$this->db->query($sql, array())->result_array();
    }
    else
    {
      $sql = 'select count(*) as total from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM
              where a.StatusStudentID in (3,2,8)  and a.ProdiID = ? '.$NPM.$queryAdd.'
              order by a.NPM asc';
      $Data_mhs=$this->db->query($sql, array($prodi))->result_array();
    }

    return $Data_mhs[0]['total'];
   }

   public function mahasiswa_list($ta,$prodi,$NPM,$limit, $start)
   {
    // error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');
    $No = $start + 1;
    $ta1 = explode('.', $ta);
    $ta = $ta1[1];
    $db = 'ta_'.$ta.'.students';
    $db2 = 'ta_'.$ta;
    // $field = 'StatusStudentID';
    // $value = 3;
    $NPM = ($NPM == "" || $NPM == null) ? '' : ' and a.NPM = "'.$NPM.'"';

    $queryAdd = '';
    if ($prodi == '') {
     $sql = 'select a.*,b.EmailPU,b.Pay_Cond,b.Bea_BPP,b.Bea_Credit,b.PaymentShow,b.PaymentShowTextMSG from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM
             where a.StatusStudentID like "%%"   '.$NPM.$queryAdd.'
             order by a.NPM asc
             LIMIT '.$start. ', '.$limit;
      // print_r($sql);
     $Data_mhs=$this->db->query($sql, array())->result_array();
    }
    else
    {
      $sql = 'select a.*,b.EmailPU,b.Pay_Cond,b.Bea_BPP,b.Bea_Credit,b.PaymentShow,b.PaymentShowTextMSG from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM
              where a.StatusStudentID like "%%"  and a.ProdiID = ? '.$NPM.$queryAdd.'
              order by a.NPM asc
              LIMIT '.$start. ', '.$limit;
      $Data_mhs=$this->db->query($sql, array($prodi))->result_array();
    }

    // get Number VA Mahasiswa
    $Const_VA = $this->m_master->showData_array('db_va.master_va');

    // $SemesterID = $SemesterID[0]['ID'];
    $Discount = $this->m_master->showData_array('db_finance.discount');
    $db = 'ta_'.$ta;
    for ($i=0; $i < count($Data_mhs); $i++) {
      $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$Data_mhs[$i]['ProdiID']);
      $array = array('ProdiEng' => $ProdiEng[0]['NameEng']);
      $Data_mhs[$i] = $Data_mhs[$i] + $array;

      // No
        $Data_mhs[$i] = $Data_mhs[$i] + array('No' => $No);

      // get VA Mahasiwa
        $VA = $Const_VA[0]['Const_VA'].$Data_mhs[$i]['NPM'];
        $Data_mhs[$i] = $Data_mhs[$i] + array('VA' => $VA);

      // get IPS Mahasiswa
        $IPS = $this->getIPSMahasiswa($db,$Data_mhs[$i]['NPM']);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPS' => $IPS);

      // get IPS Mahasiswa
        $IPK = $this->getIPKMahasiswa($db,$Data_mhs[$i]['NPM']);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPK' => $IPK);

      // get Status
          $StatusStudentID =   $Data_mhs[$i]['StatusStudentID'];
          $aa = $this->m_master->caribasedprimary('db_academic.status_student','ID',$StatusStudentID);
          $Data_mhs[$i] = $Data_mhs[$i] + array('StatusStudentName' => $aa[0]['Description']);

      // get sks yang diambil
         $Credit = $this->getSKSMahasiswa($db2,$Data_mhs[$i]['NPM']);
         $Data_mhs[$i] = $Data_mhs[$i] + array('Credit' => $Credit);
      $No++;
    }
    $arr['Data_mhs'] = $Data_mhs;
    $arr['Discount'] = $Discount;
    return $arr;
   }

   public function getCountAllPayment_admission()
   {
    $sql = 'select count(*) as total from db_finance.register_admisi where Status = "Approved" and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund) ';
    $query=$this->db->query($sql, array())->result_array();
    return $query[0]['total'];
   }

   public function edit_cicilan_tagihan_admission_submit($Input)
   {
    $this->load->model('master/m_master');
    $arr = array();
    $arr['msg']  = '';
    for ($i=0; $i < count($Input); $i++) {
      // check yang memiliki bilingId
      // jika memiliki bilingID maka update VA, jika tidak maka update database aja
      if ($Input[$i]->BilingID != 0) {
        // update VA
        $BilingID = $Input[$i]->BilingID;
        $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
        // get datetime
        $expDatetime = $getData[0]['datetime_expired'];
        $now = date('Y-m-d H:i:s');
        $chkdate = $this->m_master->chkTgl($now,$expDatetime);
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;
        $customer_name = $getData[0]['customer_name'];
        $desc = 'Add,Expired Biling '.$getData[0]['description'];
        $customer_email = $getData[0]['customer_email'];
        $VA_number = $getData[0]['virtual_account'];
        if (!$chkdate) {
         $create_va_Payment = $this->create_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$VA_number,$desc,'db_finance.payment_pre');
         if ($create_va_Payment['status']) {
           // update biling and Deadline di payment Student
           $dataSave = array(
                   'BilingID' =>$create_va_Payment['msg']['trx_id'],
                   'Invoice' => $trx_amount,
                   'Deadline' => $datetime_expired,
                   'UpdateAt' => date('Y-m-d H:i:s'),
                           );
           $this->db->where('BilingID',$BilingID);
           $this->db->update('db_finance.payment_students', $dataSave);
         }

        }
        else
        {
          $update = $this->m_finance->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_pre',$desc);
          if ($update['status'] == 1) {
            // update data pada table db_finance.payment_students
              $this->m_finance->updateCicilanMHS_admission($BilingID,$trx_amount,$datetime_expired);
          }
          else
          {
            $arr['msg'] .= 'Va tidak bisa di update, error koneksi ke BNI with Name : '.$customer_name.'<br>';
          }
        }

      }
      else
      {
        $BilingID = $Input[$i]->BilingID;
        $ID = $Input[$i]->ID;
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;
        $this->m_finance->UpdateCicilan_admission_byID($ID,$BilingID,$trx_amount,$datetime_expired);
      }
    }

    return $arr;

   }

   public function edit_cicilan_tagihan_admission_submit2($Input)
   {
    $this->load->model('master/m_master');
    $arr = array();
    $arr['msg']  = '';
    $keterangan = $this->input->post('keterangan');
    $ID_register_formulir = $this->input->post('ID_register_formulir');
    for ($i=0; $i < count($Input); $i++) {
      // check yang memiliki bilingId
      // jika memiliki bilingID maka update VA, jika tidak maka update database aja
      if ($Input[$i]->BilingID != 0) {
        // update VA
        $BilingID = $Input[$i]->BilingID;
        $getData= $this->m_master->caribasedprimary('db_va.va_log','trx_id',$BilingID);
        // get datetime
        $expDatetime = $getData[0]['datetime_expired'];
        $now = date('Y-m-d H:i:s');
        $chkdate = $this->m_master->chkTgl($now,$expDatetime);
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;
        $customer_name = $getData[0]['customer_name'];
        $desc = 'Add,Expired Biling '.$getData[0]['description'];
        $customer_email = $getData[0]['customer_email'];
        $VA_number = $getData[0]['virtual_account'];
        if (!$chkdate) {
         $create_va_Payment = $this->create_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$VA_number,$desc,'db_finance.payment_pre');
         if ($create_va_Payment['status']) {
           // update biling and Deadline di payment Student
           $dataSave = array(
                   'BilingID' =>$create_va_Payment['msg']['trx_id'],
                   'Invoice' => $trx_amount,
                   'Deadline' => $datetime_expired,
                   'UpdateAt' => date('Y-m-d H:i:s'),
                           );
           $this->db->where('BilingID',$BilingID);
           $this->db->update('db_finance.payment_students', $dataSave);
         }

        }
        else
        {
          $update = $this->m_finance->update_va_Payment($trx_amount,$datetime_expired, $customer_name, $customer_email,$BilingID,'db_finance.payment_pre',$desc);
          if ($update['status'] == 1) {
            // update data pada table db_finance.payment_students
              $this->m_finance->updateCicilanMHS_admission($BilingID,$trx_amount,$datetime_expired);
          }
          else
          {
            $arr['msg'] .= 'Va tidak bisa di update, error koneksi ke BNI with Name : '.$customer_name.'<br>';
          }
        }

      }
      else
      {
        $BilingID = $Input[$i]->BilingID;
        $ID = $Input[$i]->ID;
        $trx_amount = $Input[$i]->Invoice;
        $datetime_expired = $Input[$i]->Deadline;
        $this->m_finance->UpdateCicilan_admission_byID2($ID,$BilingID,$trx_amount,$datetime_expired);
      }
    }

    // update db_finance.register_admisi and db_finance.register_admisi_rev
    $dataGet = $this->m_master->caribasedprimary('db_finance.register_admisi_rev','ID_register_formulir',$ID_register_formulir);
    $count = count($dataGet);
    $arr_Count = $count - 1;
    $RevNo = (count($dataGet) == 0) ? 1 : $dataGet[$arr_Count]['RevNo'] + 1;
    $dataSave = array(
        'ID_register_formulir' => $ID_register_formulir,
        'RevNo' => $RevNo,
        'Note' => $keterangan,
        'RevBy' => $this->session->userdata('NIP'),
        'RevAt' => date('Y-m-d H:i:s'),
    );
    $this->db->insert('db_finance.register_admisi_rev', $dataSave);
    $insert_id = $this->db->insert_id();

    $dataSave = array(
            'RevID' => $insert_id,
                    );
    $this->db->where('ID_register_formulir',$ID_register_formulir);
    $this->db->update('db_finance.register_admisi', $dataSave);

    return $arr;

   }

   public function get_report_pembayaran_mhs($ta,$prodi,$NIM,$Semester,$Status,$limit, $start)
   {
    // error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');
    // print_r($start.' - '.$limit);die();

    $arrDB = array();
    $sqlDB = 'show databases like "%ta_2%"';
    $queryDB=$this->db->query($sqlDB, array())->result_array();
    foreach ($queryDB as $key) {
      foreach ($key as $keyB ) {
        $arrDB[] = $keyB;
      }

    }

    rsort($arrDB);
    // print_r($arrDB);die();

    $CountLimit = $start + $limit;
    $CountLimit = ($CountLimit > count($queryDB)) ? count($queryDB) : $CountLimit;

    // join dengan table auth terlebih dahulu
    // $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    $PTID = '';
    $NIM = ($NIM == '' || $NIM == Null) ? '' : ' and a.NPM = '.$NIM;
    $prodi = ($prodi == '' || $prodi == Null) ? '' : ' and a.ProdiID = '.$prodi;
    $Semester = ($Semester == '' || $Semester == Null) ? '' : ' and SemesterID = '.$Semester;

    // get Number VA Mahasiswa
        $Const_VA = $this->m_master->showData_array('db_va.master_va');
        // $No = $start + 1;
        $No = 1;
    for ($x=$start; $x < $CountLimit; $x++) {
      $dbget = $arrDB[$x];

      if ($ta == '') {
        $ex = explode('_', $dbget);
        $ta1 = $ex[1];
      }
      else
      {
        $ta = explode('.', $ta);
        $ta1 = $ta[1];
        $x = $CountLimit;
      }

      // print_r($ta1);

      // get Data Mahasiswa
      $sql = 'select a.NPM,a.Name,b.NameEng from ta_'.$ta1.'.students as a join db_academic.program_study as b on a.ProdiID = b.ID where StatusStudentID in (3,2,8) '.$NIM.$prodi;
      $query=$this->db->query($sql, array())->result_array();
      // print_r($query);
      for ($u=0; $u < count($query); $u++) {
        $arrMHS = array(
            'No' => $No,
            'NPM' => $query[$u]['NPM'],
            'Name' => $query[$u]['Name'],
            'ProdiENG' => $query[$u]['NameEng'],
            'Year' => $ta1,
        );

        // cek BPP
        $sqlBPP = 'select * from db_finance.payment where PTID = 2 and NPM = ? '.$Semester; //  limit 1
        $queryBPP=$this->db->query($sqlBPP, array($query[$u]['NPM']))->result_array();
        $arrBPP = array(
          'BPP' => '0',
          'PayBPP' => '0',
          'SisaBPP' => '0',
          'DetailPaymentBPP' => '',
          'DueDateBPP' => '',
          'AgingBPP' => 'BPP : 0',
        );
          if (count($queryBPP) > 0) {
              for ($t=0; $t < count($queryBPP); $t++) {
                // cek payment students
                $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryBPP[$t]['ID']);
                $PayBPP = 0;
                $SisaBPP = 0;
                $last = count($Q_invStudent) - 1;
                $DueDateBPP = ($Q_invStudent[$last]['Deadline'] != '' && $Q_invStudent[$last]['Deadline'] != null && strpos($Q_invStudent[$last]['Deadline'],'0000-00-00') === false) ? date('d M Y H:i:s', strtotime($Q_invStudent[$last]['Deadline'])) : '';
                for ($r=0; $r < count($Q_invStudent); $r++) {
                  if ($Q_invStudent[$r]['Status'] == 1) { // lunas
                    $PayBPP = $PayBPP + $Q_invStudent[$r]['Invoice'];
                  }
                  else
                  {
                    $SisaBPP = $SisaBPP + $Q_invStudent[$r]['Invoice'];
                  }
                }

                $Aging = 'BPP : 0';
                if ($DueDateBPP != '' && $SisaBPP > 0) {
                  $Aging = 'BPP : '.$this->m_master->dateDiffDays_(date('Y-m-d', strtotime($Q_invStudent[$last]['Deadline'])),date('Y-m-d'));
                }

                $arrBPP = array(
                  'BPP' => (int)$queryBPP[$t]['Invoice'],
                  'PayBPP' => (int)$PayBPP,
                  'SisaBPP' => (int)$SisaBPP,
                  'DetailPaymentBPP' => $Q_invStudent,
                  'DueDateBPP' => $DueDateBPP,
                  'AgingBPP' => $Aging,
                );

              }
          }

        // cek Credit
        $sqlCr = 'select * from db_finance.payment where PTID = 3 and NPM = ? '.$Semester; // limit 1
        $queryCr=$this->db->query($sqlCr, array($query[$u]['NPM']))->result_array();
        $arrCr = array(
          'Cr' => '0',
          'PayCr' => '0',
          'SisaCr' => '0',
          'DetailPaymentCr' => '',
          'DueDateCR' => '',
          'AgingCr' => 'Credit : 0',
        );
          if (count($queryCr) > 0) {
              for ($t=0; $t < count($queryCr); $t++) {
                // cek payment students
                $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryCr[$t]['ID']);
                $PayCr = 0;
                $SisaCr = 0;
                $last = count($Q_invStudent) - 1;
                $DueDateCR = ($Q_invStudent[$last]['Deadline'] != '' && $Q_invStudent[$last]['Deadline'] != null && strpos($Q_invStudent[$last]['Deadline'],'0000-00-00') === false) ? date('d M Y H:i:s', strtotime($Q_invStudent[$last]['Deadline'])) : '';
                for ($r=0; $r < count($Q_invStudent); $r++) {
                  if ($Q_invStudent[$r]['Status'] == 1) { // lunas
                    $PayCr = $PayCr + $Q_invStudent[$r]['Invoice'];
                  }
                  else
                  {
                    $SisaCr = $SisaCr + $Q_invStudent[$r]['Invoice'];
                  }
                }

                $Aging = 'Credit : 0';
                if ($DueDateCR != '' && $SisaCr > 0) {
                  $Aging = 'Credit : '.$this->m_master->dateDiffDays_(date('Y-m-d', strtotime($Q_invStudent[$last]['Deadline'])),date('Y-m-d'));
                }

                $arrCr = array(
                  'Cr' => (int)$queryCr[$t]['Invoice'],
                  'PayCr' => (int)$PayCr,
                  'SisaCr' => (int)$SisaCr,
                  'DetailPaymentCr' => $Q_invStudent,
                  'DueDateCR' => $DueDateCR,
                  'AgingCr' => $Aging,
                );

              }
          }

          // cek lain-lain
          $sqlAn = 'select * from db_finance.payment where PTID = 4 and NPM = ? '.$Semester; // limit 1
          $queryAn=$this->db->query($sqlAn, array($query[$u]['NPM']))->result_array();
          $arrAn = array(
            'An' => '0',
            'PayAn' => '0',
            'SisaAn' => '0',
            'DetailPaymentAn' => '',
            'DueDateAn' => '',
            'AgingAn' => 'lain-lain : 0',
          );
            if (count($queryAn) > 0) {
                for ($t=0; $t < count($queryAn); $t++) {
                  // cek payment students
                  $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryAn[$t]['ID']);
                  $PayAn = 0;
                  $SisaAn = 0;
                  $last = count($Q_invStudent) - 1;
                  $DueDateAn = ($Q_invStudent[$last]['Deadline'] != '' && $Q_invStudent[$last]['Deadline'] != null && strpos($Q_invStudent[$last]['Deadline'],'0000-00-00') === false) ? date('d M Y H:i:s', strtotime($Q_invStudent[$last]['Deadline'])) : '';
                  for ($r=0; $r < count($Q_invStudent); $r++) {
                    if ($Q_invStudent[$r]['Status'] == 1) { // lunas
                      $PayAn = $PayAn + $Q_invStudent[$r]['Invoice'];
                    }
                    else
                    {
                      $SisaAn = $SisaAn + $Q_invStudent[$r]['Invoice'];
                    }
                  }

                  $Aging = 'lain-lain : 0';
                  if ($DueDateAn != '' && $SisaAn > 0) {
                    $Aging = 'lain-lain : '.$this->m_master->dateDiffDays_(date('Y-m-d', strtotime($Q_invStudent[$last]['Deadline'])),date('Y-m-d'));
                  }

                  $arrAn = array(
                    'An' => (int)$queryAn[$t]['Invoice'],
                    'PayAn' => (int)$PayAn,
                    'SisaAn' => (int)$SisaAn,
                    'DetailPaymentAn' => $Q_invStudent,
                    'DueDateAn' => $DueDateAn,
                    'AgingAn' => $Aging,
                  );

                }
            }

            // cek SPP
            $sqlSPP = 'select * from db_finance.payment where PTID = 1 and NPM = ? '.$Semester; // limit 1
            $querySPP=$this->db->query($sqlSPP, array($query[$u]['NPM']))->result_array();
            $arrSPP = array(
              'SPP' => '0',
              'PaySPP' => '0',
              'SisaSPP' => '0',
              'DetailPaymentSPP' => '',
              'DueDateSPP' => '',
              'AgingSPP' => 'SPP : 0',
            );
              if (count($querySPP) > 0) {
                  for ($t=0; $t < count($querySPP); $t++) {
                    // cek payment students
                    $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$querySPP[$t]['ID']);
                    $PaySPP = 0;
                    $SisaSPP = 0;
                    $last = count($Q_invStudent) - 1;
                    $DueDateSPP = ($Q_invStudent[$last]['Deadline'] != '' && $Q_invStudent[$last]['Deadline'] != null && strpos($Q_invStudent[$last]['Deadline'],'0000-00-00') === false) ? date('d M Y H:i:s', strtotime($Q_invStudent[$last]['Deadline'])) : '';
                    for ($r=0; $r < count($Q_invStudent); $r++) {
                      if ($Q_invStudent[$r]['Status'] == 1) { // lunas
                        $PaySPP = $PaySPP + $Q_invStudent[$r]['Invoice'];
                      }
                      else
                      {
                        $SisaSPP = $SisaSPP + $Q_invStudent[$r]['Invoice'];
                      }
                    }

                    $Aging = 'SPP : 0';
                    if ($DueDateSPP != '' && $SisaAn > 0) {
                      $Aging = 'SPP : '.$this->m_master->dateDiffDays_(date('Y-m-d', strtotime($Q_invStudent[$last]['Deadline'])),date('Y-m-d'));
                    }

                    $arrSPP = array(
                      'SPP' => (int)$querySPP[$t]['Invoice'],
                      'PaySPP' => (int)$PaySPP,
                      'SisaSPP' => (int)$SisaSPP,
                      'DetailPaymentSPP' => $Q_invStudent,
                      'DueDateSPP' => $DueDateSPP,
                      'AgingSPP' => $Aging,
                    );

                  }
              }

          // if($Status == 0) // tidak lunas
          switch ($Status) {
            case '': // All
              $arr[] = $arrMHS + $arrBPP + $arrCr + $arrAn + $arrSPP;
              break;
            case 0:  // Tidak Lunas
              if ($arrBPP['DetailPaymentBPP'] == '' || $arrCr['DetailPaymentCr'] == '' ||  $arrBPP['SisaBPP'] > 0 || $arrCr['SisaCr'] > 0  ||  $arrSPP['SisaSPP'] > 0 || $arrAn['SisaAn'] > 0 ) {
                $arr[] = $arrMHS + $arrBPP + $arrCr + $arrAn + $arrSPP;
              }
              break;
            case 1:  // Lunas
              if ($arrBPP['DetailPaymentBPP'] != '' && $arrCr['DetailPaymentCr'] != '' &&  $arrBPP['SisaBPP'] == 0 && $arrCr['SisaCr'] == 0   &&  $arrSPP['SisaSPP'] == 0 && $arrAn['SisaAn'] == 0 ) {
                $arr[] = $arrMHS + $arrBPP + $arrCr + $arrAn + $arrSPP;
              }
              // $arr[] = $arrMHS + $arrBPP + $arrCr ;
              break;
            default:
              # code...
              break;
          }

        // if ($arrBPP['SisaBPP'] > 0 || $arrCr['SisaCr'] > 0) {
        //     $arr[] = $arrMHS + $arrBPP + $arrCr ;
        // }

        $No++;
      } // loop per mhs

    }
    return $arr;
   }


   public function get_report_pembayaran_mhs2($ta,$prodi,$NIM,$Semester,$Status,$limit, $start,$StatusMHS)
   {
    // error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');
    $this->load->model('statistik/m_statistik');
    // print_r($start.' - '.$limit);die();

    $arrDB = array();
    $sqlDB = 'show databases like "%ta_2%"';
    $queryDB=$this->db->query($sqlDB, array())->result_array();
    foreach ($queryDB as $key) {
      foreach ($key as $keyB ) {
        $arrDB[] = $keyB;
      }

    }

    rsort($arrDB);
    // print_r($arrDB);die();

    $CountLimit = $start + $limit;
    $CountLimit = ($CountLimit > count($queryDB)) ? count($queryDB) : $CountLimit;

    // join dengan table auth terlebih dahulu
    // $PTID = ($PTID == '' || $PTID == Null) ? '' : ' and a.PTID = '.$PTID;
    if ($StatusMHS == "") {
      // $sMhs = ' StatusStudentID != "'.$StatusMHS.'"';
      // $sMhs = '';
      $sMhs = 'StatusStudentID in (3,2,8)';
    }
    else
    {
      $sMhs = ' StatusStudentID = "'.$StatusMHS.'"';
    }
    $PTID = '';
    if ($sMhs == '') {
      $NIM = ($NIM == '' || $NIM == Null) ? '' : ' a.NPM = '.$NIM;
    }
    else
    {
      $NIM = ($NIM == '' || $NIM == Null) ? '' : ' and a.NPM = '.$NIM;
    }

    if ($sMhs == '' && $NIM == '') {
      $prodi = ($prodi == '' || $prodi == Null) ? '' : ' a.ProdiID = '.$prodi;
    }
    else
    {
      $prodi = ($prodi == '' || $prodi == Null) ? '' : ' and a.ProdiID = '.$prodi;
    }
    $Semester = ($Semester == '' || $Semester == Null) ? '' : ' and SemesterID = '.$Semester;
    $where = '';
    // get Number VA Mahasiswa
        $Const_VA = $this->m_master->showData_array('db_va.master_va');
        // $No = $start + 1;
        $No = 1;
    for ($x=$start; $x < $CountLimit; $x++) {
      $dbget = $arrDB[$x];

      if ($ta == '') {
        $ex = explode('_', $dbget);
        $ta1 = $ex[1];
      }
      else
      {
        $ta = explode('.', $ta);
        $ta1 = $ta[1];
        $x = $CountLimit;
      }

      if ($sMhs != '' || $NIM != '' || $prodi != '') {
       $where = ' where ';
      }

      // get Data Mahasiswa
      $sql = 'select a.NPM,a.Name,b.NameEng from ta_'.$ta1.'.students as a join db_academic.program_study as b on a.ProdiID = b.ID '.$where.$sMhs.' '.$NIM.$prodi;
      //print_r($sql);die();
      $query=$this->db->query($sql, array())->result_array();
      // print_r($query);
      for ($u=0; $u < count($query); $u++) {
        $arrMHS = array(
            'No' => $No,
            'NPM' => $query[$u]['NPM'],
            'Name' => $query[$u]['Name'],
            'ProdiENG' => $query[$u]['NameEng'],
            'Year' => $ta1,
        );

        // cek BPP
        $sqlBPP = 'select * from db_finance.payment where PTID = 2 and NPM = ? '.$Semester; //  limit 1
        $queryBPP=$this->db->query($sqlBPP, array($query[$u]['NPM']))->result_array();
        $arrBPP = array(
          'BPP' => '0',
          'PayBPP' => '0',
          'SisaBPP' => '0',
          'DetailPaymentBPP' => '',
          'DueDateBPP' => '',
          'AgingBPP' => 'BPP : 0',
        );
          if (count($queryBPP) > 0) {
              for ($t=0; $t < count($queryBPP); $t++) {
                // cek payment students
                $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryBPP[$t]['ID']);
                $PayBPP = 0;
                $SisaBPP = 0;
                $last = count($Q_invStudent) - 1;
                $DueDateBPP = ($Q_invStudent[$last]['Deadline'] != '' && $Q_invStudent[$last]['Deadline'] != null && strpos($Q_invStudent[$last]['Deadline'],'0000-00-00') === false) ? date('d M Y H:i:s', strtotime($Q_invStudent[$last]['Deadline'])) : '';
                //for ($r=0; $r < count($Q_invStudent); $r++) {
                  // if ($Q_invStudent[$r]['Status'] == 1) { // lunas
                  //   $PayBPP = $PayBPP + $Q_invStudent[$r]['Invoice'];
                  // }
                  // else
                  // {
                  //   $SisaBPP = $SisaBPP + $Q_invStudent[$r]['Invoice'];
                  // }
                //}

                $Invoice = $queryBPP[$t]['Invoice'];
                $get_pay = $this->m_statistik->get_pay_arrDataPS($Q_invStudent,$Invoice);

                $PayBPP = $get_pay['Pay'];
                $SisaBPP = $get_pay['Sisa'];

                $Aging = 'BPP : 0';
                if ($DueDateBPP != '' && $SisaBPP > 0) {
                  $Aging = 'BPP : '.$this->m_master->dateDiffDays_(date('Y-m-d', strtotime($Q_invStudent[$last]['Deadline'])),date('Y-m-d'));
                }

                $arrBPP = array(
                  'BPP' => (int)$queryBPP[$t]['Invoice'],
                  'PayBPP' => (int)$PayBPP,
                  'SisaBPP' => (int)$SisaBPP,
                  'DetailPaymentBPP' => $Q_invStudent,
                  'DueDateBPP' => $DueDateBPP,
                  'AgingBPP' => $Aging,
                );

              }
          }

        // cek Credit
        $sqlCr = 'select * from db_finance.payment where PTID = 3 and NPM = ? '.$Semester; // limit 1
        $queryCr=$this->db->query($sqlCr, array($query[$u]['NPM']))->result_array();
        $arrCr = array(
          'Cr' => '0',
          'PayCr' => '0',
          'SisaCr' => '0',
          'DetailPaymentCr' => '',
          'DueDateCR' => '',
          'AgingCr' => 'Credit : 0',
        );
          if (count($queryCr) > 0) {
              for ($t=0; $t < count($queryCr); $t++) {
                // cek payment students
                $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryCr[$t]['ID']);
                $PayCr = 0;
                $SisaCr = 0;
                $last = count($Q_invStudent) - 1;
                $DueDateCR = ($Q_invStudent[$last]['Deadline'] != '' && $Q_invStudent[$last]['Deadline'] != null && strpos($Q_invStudent[$last]['Deadline'],'0000-00-00') === false) ? date('d M Y H:i:s', strtotime($Q_invStudent[$last]['Deadline'])) : '';
                // for ($r=0; $r < count($Q_invStudent); $r++) {
                //   if ($Q_invStudent[$r]['Status'] == 1) { // lunas
                //     $PayCr = $PayCr + $Q_invStudent[$r]['Invoice'];
                //   }
                //   else
                //   {
                //     $SisaCr = $SisaCr + $Q_invStudent[$r]['Invoice'];
                //   }
                // }

                $Invoice = $queryCr[$t]['Invoice'];
                $get_pay = $this->m_statistik->get_pay_arrDataPS($Q_invStudent,$Invoice);

                $PayCr = $get_pay['Pay'];
                $SisaCr = $get_pay['Sisa'];

                $Aging = 'Credit : 0';
                if ($DueDateCR != '' && $SisaCr > 0) {
                  $Aging = 'Credit : '.$this->m_master->dateDiffDays_(date('Y-m-d', strtotime($Q_invStudent[$last]['Deadline'])),date('Y-m-d'));
                }

                $arrCr = array(
                  'Cr' => (int)$queryCr[$t]['Invoice'],
                  'PayCr' => (int)$PayCr,
                  'SisaCr' => (int)$SisaCr,
                  'DetailPaymentCr' => $Q_invStudent,
                  'DueDateCR' => $DueDateCR,
                  'AgingCr' => $Aging,
                );

              }
          }

          // cek lain-lain
          $sqlAn = 'select * from db_finance.payment where PTID = 4 and NPM = ? '.$Semester; // limit 1
          $queryAn=$this->db->query($sqlAn, array($query[$u]['NPM']))->result_array();
          $arrAn = array(
            'An' => '0',
            'PayAn' => '0',
            'SisaAn' => '0',
            'DetailPaymentAn' => '',
            'DueDateAn' => '',
            'AgingAn' => 'lain-lain : 0',
          );
            if (count($queryAn) > 0) {
                for ($t=0; $t < count($queryAn); $t++) {
                  // cek payment students
                  $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryAn[$t]['ID']);
                  $PayAn = 0;
                  $SisaAn = 0;
                  $last = count($Q_invStudent) - 1;
                  $DueDateAn = ($Q_invStudent[$last]['Deadline'] != '' && $Q_invStudent[$last]['Deadline'] != null && strpos($Q_invStudent[$last]['Deadline'],'0000-00-00') === false) ? date('d M Y H:i:s', strtotime($Q_invStudent[$last]['Deadline'])) : '';
                  // for ($r=0; $r < count($Q_invStudent); $r++) {
                  //   if ($Q_invStudent[$r]['Status'] == 1) { // lunas
                  //     $PayAn = $PayAn + $Q_invStudent[$r]['Invoice'];
                  //   }
                  //   else
                  //   {
                  //     $SisaAn = $SisaAn + $Q_invStudent[$r]['Invoice'];
                  //   }
                  // }

                  $Invoice = $queryAn[$t]['Invoice'];
                  $get_pay = $this->m_statistik->get_pay_arrDataPS($Q_invStudent,$Invoice);

                  $PayAn = $get_pay['Pay'];
                  $SisaAn = $get_pay['Sisa'];

                  $Aging = 'lain-lain : 0';
                  if ($DueDateAn != '' && $SisaAn > 0) {
                    $Aging = 'lain-lain : '.$this->m_master->dateDiffDays_(date('Y-m-d', strtotime($Q_invStudent[$last]['Deadline'])),date('Y-m-d'));
                  }

                  $arrAn = array(
                    'An' => (int)$queryAn[$t]['Invoice'],
                    'PayAn' => (int)$PayAn,
                    'SisaAn' => (int)$SisaAn,
                    'DetailPaymentAn' => $Q_invStudent,
                    'DueDateAn' => $DueDateAn,
                    'AgingAn' => $Aging,
                  );

                }
            }

            // cek SPP
            $sqlSPP = 'select * from db_finance.payment where PTID = 1 and NPM = ? '.$Semester; // limit 1
            $querySPP=$this->db->query($sqlSPP, array($query[$u]['NPM']))->result_array();
            $arrSPP = array(
              'SPP' => '0',
              'PaySPP' => '0',
              'SisaSPP' => '0',
              'DetailPaymentSPP' => '',
              'DueDateSPP' => '',
              'AgingSPP' => 'SPP : 0',
            );
              if (count($querySPP) > 0) {
                  for ($t=0; $t < count($querySPP); $t++) {
                    // cek payment students
                    $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$querySPP[$t]['ID']);
                    $PaySPP = 0;
                    $SisaSPP = 0;
                    $last = count($Q_invStudent) - 1;
                    $DueDateSPP = ($Q_invStudent[$last]['Deadline'] != '' && $Q_invStudent[$last]['Deadline'] != null && strpos($Q_invStudent[$last]['Deadline'],'0000-00-00') === false) ? date('d M Y H:i:s', strtotime($Q_invStudent[$last]['Deadline'])) : '';
                    // for ($r=0; $r < count($Q_invStudent); $r++) {
                    //   if ($Q_invStudent[$r]['Status'] == 1) { // lunas
                    //     $PaySPP = $PaySPP + $Q_invStudent[$r]['Invoice'];
                    //   }
                    //   else
                    //   {
                    //     $SisaSPP = $SisaSPP + $Q_invStudent[$r]['Invoice'];
                    //   }
                    // }

                    $Invoice = $querySPP[$t]['Invoice'];
                    $get_pay = $this->m_statistik->get_pay_arrDataPS($Q_invStudent,$Invoice);

                    $PaySPP = $get_pay['Pay'];
                    $SisaSPP = $get_pay['Sisa'];

                    $Aging = 'SPP : 0';
                    if ($DueDateSPP != '' && $SisaAn > 0) {
                      $Aging = 'SPP : '.$this->m_master->dateDiffDays_(date('Y-m-d', strtotime($Q_invStudent[$last]['Deadline'])),date('Y-m-d'));
                    }

                    $arrSPP = array(
                      'SPP' => (int)$querySPP[$t]['Invoice'],
                      'PaySPP' => (int)$PaySPP,
                      'SisaSPP' => (int)$SisaSPP,
                      'DetailPaymentSPP' => $Q_invStudent,
                      'DueDateSPP' => $DueDateSPP,
                      'AgingSPP' => $Aging,
                    );

                  }
              }

          // if($Status == 0) // tidak lunas
          switch ($Status) {
            case '': // All
              $arr[] = $arrMHS + $arrBPP + $arrCr + $arrAn + $arrSPP;
              break;
            case 0:  // Tidak Lunas
              if ($arrBPP['DetailPaymentBPP'] == '' || $arrCr['DetailPaymentCr'] == '' ||  $arrBPP['SisaBPP'] > 0 || $arrCr['SisaCr'] > 0  ||  $arrSPP['SisaSPP'] > 0 || $arrAn['SisaAn'] > 0 ) {
                $arr[] = $arrMHS + $arrBPP + $arrCr + $arrAn + $arrSPP;
              }
              break;
            case 1:  // Lunas
              if ($arrBPP['DetailPaymentBPP'] != '' && $arrCr['DetailPaymentCr'] != '' &&  $arrBPP['SisaBPP'] == 0 && $arrCr['SisaCr'] == 0   &&  $arrSPP['SisaSPP'] == 0 && $arrAn['SisaAn'] == 0 ) {
                $arr[] = $arrMHS + $arrBPP + $arrCr + $arrAn + $arrSPP;
              }
              // $arr[] = $arrMHS + $arrBPP + $arrCr ;
              break;
            default:
              # code...
              break;
          }

        // if ($arrBPP['SisaBPP'] > 0 || $arrCr['SisaCr'] > 0) {
        //     $arr[] = $arrMHS + $arrBPP + $arrCr ;
        // }

        $No++;
      } // loop per mhs

    }
    return $arr;
   }

   public function mahasiswa_list_all($ta,$prodi,$NPM)
   {
    // error_reporting(0);
    $arr = array();
    $this->load->model('master/m_master');
    // $ta1 = explode('.', $ta);
    // $ta = $ta1[1];
    $db = 'ta_'.$ta.'.students';
    $db2 = 'ta_'.$ta;
    // $field = 'StatusStudentID';
    // $value = 3;
    $NPM = ($NPM == "" || $NPM == null) ? '' : ' and a.NPM = "'.$NPM.'"';

    $queryAdd = '';
    if ($prodi == '') {
     $sql = 'select a.*,b.EmailPU,b.Pay_Cond,b.Bea_BPP,b.Bea_Credit from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM
             where a.StatusStudentID like "%%"   '.$NPM.$queryAdd.'
             order by a.NPM asc
             ';
      // print_r($sql);
     $Data_mhs=$this->db->query($sql, array())->result_array();
    }
    else
    {
      $sql = 'select a.*,b.EmailPU,b.Pay_Cond,b.Bea_BPP,b.Bea_Credit from '.$db.' as a left join db_academic.auth_students as b on a.NPM = b.NPM
              where a.StatusStudentID like "%%"  and a.ProdiID = ? '.$NPM.$queryAdd.'
              order by a.NPM asc
              ';
      $Data_mhs=$this->db->query($sql, array($prodi))->result_array();
    }
    // get Number VA Mahasiswa
    $Const_VA = $this->m_master->showData_array('db_va.master_va');

    // $SemesterID = $SemesterID[0]['ID'];
    $Discount = $this->m_master->showData_array('db_finance.discount');
    $db = 'ta_'.$ta;
    for ($i=0; $i < count($Data_mhs); $i++) {
      $ProdiEng = $this->m_master->caribasedprimary('db_academic.program_study','ID',$Data_mhs[$i]['ProdiID']);
      $array = array('ProdiEng' => $ProdiEng[0]['NameEng']);
      $Data_mhs[$i] = $Data_mhs[$i] + $array;

      // get VA Mahasiwa
        $VA = $Const_VA[0]['Const_VA'].$Data_mhs[$i]['NPM'];
        $Data_mhs[$i] = $Data_mhs[$i] + array('VA' => $VA);

      // get IPS Mahasiswa
        $IPS = $this->getIPSMahasiswa($db,$Data_mhs[$i]['NPM']);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPS' => $IPS);

      // get IPS Mahasiswa
        $IPK = $this->getIPKMahasiswa($db,$Data_mhs[$i]['NPM']);
        $Data_mhs[$i] = $Data_mhs[$i] + array('IPK' => $IPK);

      // get Status
          $StatusStudentID =   $Data_mhs[$i]['StatusStudentID'];
          $aa = $this->m_master->caribasedprimary('db_academic.status_student','ID',$StatusStudentID);
          $Data_mhs[$i] = $Data_mhs[$i] + array('StatusStudentName' => $aa[0]['Description']);

      // get sks yang diambil
         $Credit = $this->getSKSMahasiswa($db2,$Data_mhs[$i]['NPM']);
         $Data_mhs[$i] = $Data_mhs[$i] + array('Credit' => $Credit);

    }
    return $Data_mhs;
   }

   public function getSaleFormulirOffline($SelectSetTa,$SelectSortBy)
   {
    $SelectSortBy = explode('.', $SelectSortBy);
    switch ($SelectSortBy) {
      case 'No_Ref':
      case 'FormulirCode':
        $SelectSortBy = ' order by a.FormulirCodeGlobal asc';
        break;
      case 'DateSale':
        $SelectSortBy = ' order by b.DateFin asc';
        break;
      default:
        $SelectSortBy = '';
        break;
    }
     $sql = 'select a.FormulirCodeGlobal,a.Years,a.Status as StatusGlobalFormulir,
              b.No_Ref,b.Sales,b.PIC,b.DateFin,b.FullName
              from db_admission.formulir_number_global as a
              LEFT JOIN
              (
                             select a.No_Ref,c.Name as Sales,b.PIC,b.DateFin,b.FullName
                             from db_admission.formulir_number_offline_m as a
                             join db_admission.sale_formulir_offline as b
                             on a.FormulirCode = b.FormulirCodeOffline
                             left join db_employees.employees as c
                             on c.NIP = b.PIC
                             where a.Years = ?
              ) as b
              on a.FormulirCodeGlobal = b.No_Ref
               ';
     $query=$this->db->query($sql, array($SelectSetTa))->result_array();
     return $query;
   }

   public function getSaleFormulirONOFFBetwwen_fin($SelectSetTa,$SelectSortBy,$dateRange1,$dateRange2)
   {
    $SelectSortBy = explode('.', $SelectSortBy);
    switch ($SelectSortBy[1]) {
      case 'No_Ref':
      case 'FormulirCode':
        $SelectSortBy = ' order by a.FormulirCodeGlobal asc';
        break;
      case 'DateSale':
        $SelectSortBy = ' order by b.DateFin asc';
        break;
      default:
        $SelectSortBy = '';
        break;
    }
     $sql = 'select a.FormulirCodeGlobal,a.Years,a.Status as StatusGlobalFormulir,
              b.No_Ref,b.Sales,b.PIC,b.DateFin,b.FullName,b.NoKwitansi,b.FormulirType
              from db_admission.formulir_number_global as a
              LEFT JOIN
              (
                             select a.No_Ref,c.Name as Sales,b.PIC,b.DateFin,b.FullName,b.NoKwitansi,"Off" as FormulirType
                             from db_admission.formulir_number_offline_m as a
                             left join db_admission.sale_formulir_offline as b
                             on a.FormulirCode = b.FormulirCodeOffline
                             left join db_employees.employees as c
                             on c.NIP = b.PIC
                             where a.Years = ?
                             UNION
                             select a.No_Ref,"","",DATE_FORMAT(b.VerificationAT, "%Y-%m-%d") as DateFin,c.Name,a.NoKwitansi,"ON" as FormulirType
                             from db_admission.formulir_number_online_m as a
                             join db_admission.register_verified as b on a.FormulirCode = b.FormulirCode
                             join db_admission.register_verification as d on d.ID =  b.RegVerificationID
                             join db_admission.register as c on c.ID = d.RegisterID
                             where a.Years = ?
              ) as b
              on a.FormulirCodeGlobal = b.No_Ref
              where b.DateFin >= "'.$dateRange1.'" and b.DateFin <= "'.$dateRange2.'" and a.Years = ?
               '.$SelectSortBy;
               //print_r($sql);die();
     $query=$this->db->query($sql, array($SelectSetTa,$SelectSetTa,$SelectSetTa))->result_array();
     return $query;
   }

   public function getSaleFormulirOfflineBetwwen_fin($SelectSetTa,$SelectSortBy,$dateRange1,$dateRange2)
   {
    $SelectSortBy = explode('.', $SelectSortBy);
    switch ($SelectSortBy[1]) {
      case 'No_Ref':
      case 'FormulirCode':
        $SelectSortBy = ' order by a.FormulirCodeGlobal asc';
        break;
      case 'DateSale':
        $SelectSortBy = ' order by b.DateFin asc';
        break;
      default:
        $SelectSortBy = '';
        break;
    }
     $sql = 'select a.FormulirCodeGlobal,a.Years,a.Status as StatusGlobalFormulir,
              b.No_Ref,b.Sales,b.PIC,b.DateFin,b.FullName
              from db_admission.formulir_number_global as a
              LEFT JOIN
              (
                             select a.No_Ref,c.Name as Sales,b.PIC,b.DateFin,b.FullName
                             from db_admission.formulir_number_offline_m as a
                             join db_admission.sale_formulir_offline as b
                             on a.FormulirCode = b.FormulirCodeOffline
                             left join db_employees.employees as c
                             on c.NIP = b.PIC
                             where a.Years = ?
              ) as b
              on a.FormulirCodeGlobal = b.No_Ref
              where b.DateFin >= "'.$dateRange1.'" and b.DateFin <= "'.$dateRange2.'" and a.Years = ?
               '.$SelectSortBy;
               //print_r($sql);die();
     $query=$this->db->query($sql, array($SelectSetTa,$SelectSetTa))->result_array();
     return $query;
   }


   public function getSaleFormulirOfflineMonth_fin($SelectSetTa,$SelectSortBy,$Month,$Year)
   {
    $SelectSortBy = explode('.', $SelectSortBy);
    switch ($SelectSortBy[1]) {
      case 'No_Ref':
      case 'FormulirCode':
        $SelectSortBy = ' order by a.FormulirCodeGlobal asc';
        break;
      case 'DateSale':
        $SelectSortBy = ' order by b.DateFin asc';
        break;
      default:
        $SelectSortBy = '';
        break;
    }
     $sql = 'select a.FormulirCodeGlobal,a.Years,a.Status as StatusGlobalFormulir,
              b.No_Ref,b.Sales,b.PIC,b.DateFin,b.FullName
              from db_admission.formulir_number_global as a
              LEFT JOIN
              (
                             select a.No_Ref,c.Name as Sales,b.PIC,b.DateFin,b.FullName
                             from db_admission.formulir_number_offline_m as a
                             join db_admission.sale_formulir_offline as b
                             on a.FormulirCode = b.FormulirCodeOffline
                             left join db_employees.employees as c
                             on c.NIP = b.PIC
                             where a.Years = ?
              ) as b
              on a.FormulirCodeGlobal = b.No_Ref
              where  Month(b.DateFin) = "'.$Month.'" and Year(b.DateFin) = "'.$Year.'" and a.Years = ?
               '.$SelectSortBy;
               //print_r($sql);die();
     $query=$this->db->query($sql, array($SelectSetTa,$SelectSetTa))->result_array();
     return $query;
   }

   public function getSaleFormulirOffONMonth_fin($SelectSetTa,$SelectSortBy,$Month,$Year)
   {
    $SelectSortBy = explode('.', $SelectSortBy);
    switch ($SelectSortBy[1]) {
      case 'No_Ref':
      case 'FormulirCode':
        $SelectSortBy = ' order by a.FormulirCodeGlobal asc';
        break;
      case 'DateSale':
        $SelectSortBy = ' order by b.DateFin asc';
        break;
      default:
        $SelectSortBy = '';
        break;
    }
     $sql = 'select a.FormulirCodeGlobal,a.Years,a.Status as StatusGlobalFormulir,
              b.No_Ref,b.Sales,b.PIC,b.DateFin,b.FullName,b.NoKwitansi,b.FormulirType
              from db_admission.formulir_number_global as a
              LEFT JOIN
              (
                             select a.No_Ref,c.Name as Sales,b.PIC,b.DateFin,b.FullName,b.NoKwitansi,"Off" as FormulirType
                             from db_admission.formulir_number_offline_m as a
                             join db_admission.sale_formulir_offline as b
                             on a.FormulirCode = b.FormulirCodeOffline
                             left join db_employees.employees as c
                             on c.NIP = b.PIC
                             where a.Years = ?
                             UNION
                             select a.No_Ref,"","",DATE_FORMAT(b.VerificationAT, "%Y-%m-%d") as DateFin,c.Name,a.NoKwitansi,"ON" as FormulirType
                             from db_admission.formulir_number_online_m as a
                             join db_admission.register_verified as b on a.FormulirCode = b.FormulirCode
                             join db_admission.register_verification as d on d.ID =  b.RegVerificationID
                             join db_admission.register as c on c.ID = d.RegisterID
                             where a.Years = ?
              ) as b
              on a.FormulirCodeGlobal = b.No_Ref
              where  Month(b.DateFin) = "'.$Month.'" and Year(b.DateFin) = "'.$Year.'" and a.Years = ?
               '.$SelectSortBy;
               //print_r($sql);die();
     $query=$this->db->query($sql, array($SelectSetTa,$SelectSetTa,$SelectSetTa))->result_array();
     return $query;
   }

   public function getSaleFormulirOfflineAll_fin($SelectSetTa,$SelectSortBy)
   {
      $SelectSortBy = explode('.', $SelectSortBy);
      switch ($SelectSortBy[1]) {
        case 'No_Ref':
        case 'FormulirCode':
          $SelectSortBy = ' order by a.FormulirCodeGlobal asc';
          break;
        case 'DateSale':
          $SelectSortBy = ' order by b.DateFin asc';
          break;
        default:
          $SelectSortBy = '';
          break;
      }
     $sql = 'select a.FormulirCodeGlobal,a.Years,a.Status as StatusGlobalFormulir,
              b.No_Ref,b.Sales,b.PIC,b.DateFin,b.FullName
              from db_admission.formulir_number_global as a
              LEFT JOIN
              (
                             select a.No_Ref,c.Name as Sales,b.PIC,b.DateFin,b.FullName
                             from db_admission.formulir_number_offline_m as a
                             join db_admission.sale_formulir_offline as b
                             on a.FormulirCode = b.FormulirCodeOffline
                             left join db_employees.employees as c
                             on c.NIP = b.PIC
                             where a.Years = ?
              ) as b
              on a.FormulirCodeGlobal = b.No_Ref
              where a.Years = ?
               '.$SelectSortBy;
               //print_r($sql);die();
     $query=$this->db->query($sql, array($SelectSetTa,$SelectSetTa))->result_array();
     return $query;
   }

   public function getSaleFormulirOffOnnAll_fin($SelectSetTa,$SelectSortBy)
   {
      $SelectSortBy = explode('.', $SelectSortBy);
      switch ($SelectSortBy[1]) {
        case 'No_Ref':
        case 'FormulirCode':
          $SelectSortBy = ' order by a.FormulirCodeGlobal asc';
          break;
        case 'DateSale':
          $SelectSortBy = ' order by b.DateFin asc';
          break;
        default:
          $SelectSortBy = '';
          break;
      }
     $sql = 'select a.FormulirCodeGlobal,a.Years,a.Status as StatusGlobalFormulir,
              b.No_Ref,b.Sales,b.PIC,b.DateFin,b.FullName,b.NoKwitansi,b.FormulirType
              from db_admission.formulir_number_global as a
              LEFT JOIN
              (
                             select a.No_Ref,c.Name as Sales,b.PIC,b.DateFin,b.FullName,b.NoKwitansi,"Off" as FormulirType
                             from db_admission.formulir_number_offline_m as a
                             join db_admission.sale_formulir_offline as b
                             on a.FormulirCode = b.FormulirCodeOffline
                             left join db_employees.employees as c
                             on c.NIP = b.PIC
                             where a.Years = ?
                             UNION
                             select a.No_Ref,"","",DATE_FORMAT(b.VerificationAT, "%Y-%m-%d") as DateFin,c.Name,a.NoKwitansi,"ON" as FormulirType
                             from db_admission.formulir_number_online_m as a
                             join db_admission.register_verified as b on a.FormulirCode = b.FormulirCode
                             join db_admission.register_verification as d on d.ID =  b.RegVerificationID
                             join db_admission.register as c on c.ID = d.RegisterID
                             where a.Years = ?
              ) as b
              on a.FormulirCodeGlobal = b.No_Ref
              where a.Years = ?
               '.$SelectSortBy;
               //print_r($sql);die();
     $query=$this->db->query($sql, array($SelectSetTa,$SelectSetTa,$SelectSetTa))->result_array();
     return $query;
   }

   public function getPayment_Daily_admission($Year,$DailyTgl)
   {
    $arr_result = array();
    $sql = 'select * from (
            select * from (
            select * from (
            select a.ID as ID_register_formulir2,a.ID_program_study,o.Name as NamePrody,d.Name,1 as StatusTbl
            from db_admission.register_formulir as a
            left JOIN db_admission.register_verified as b
            ON a.ID_register_verified = b.ID
            left JOIN db_admission.register_verification as c
            ON b.RegVerificationID = c.ID
            left JOIN db_admission.register as d
            ON c.RegisterID = d.ID
            left join db_academic.program_study as o
            on o.ID = a.ID_program_study
            where d.SetTa = "'.$Year.'" group by a.ID

            ) aa
            join db_finance.payment_pre as bb
            on aa.ID_register_formulir2 = bb.ID_register_formulir
            where bb.Status = 1 and bb.DatePayment like "'.$DailyTgl.'%"
            ORDER BY bb.ID_register_formulir,bb.ID
            ) aa
            UNION
            select a.ID,a.ID_ProgramStudy,o.Name as NamePrody,a.FullName,0,"","",a.Price_Form,"",1,"",a.DateFin,""
            from db_admission.sale_formulir_offline as a join db_academic.program_study  as o on
            a.ID_ProgramStudy = o.ID where a.DateFin like "'.$DailyTgl.'%" and SUBSTRING(a.FormulirCodeOffline, 1, 2) = "'.substr($Year, 2,4).'"
            UNION
              select a.ID as ID_register_formulir2,a.ID_program_study,o.Name as NamePrody,d.Name,1 as StatusTbl,"","", (0 - rr.Price),"",1,"",rr.UpdateAt,rr.UpdateAt
              from db_admission.register_formulir as a 
              JOIN db_admission.register_verified as b ON a.ID_register_verified = b.ID 
              JOIN db_admission.register_verification as c ON b.RegVerificationID = c.ID 
              JOIN db_admission.register as d ON c.RegisterID = d.ID 
              join db_academic.program_study as o on o.ID = a.ID_program_study
              join db_finance.register_refund as rr on rr.ID_register_formulir = a.ID 
              where d.SetTa = "'.$Year.'" and rr.UpdateAt like "'.$DailyTgl.'%"
              group by a.ID 
            ) bb ORDER BY ID_program_study asc ,ID_register_formulir asc,ID asc
        ';
        // print_r($sql);die();
        $query=$this->db->query($sql, array())->result_array();
        for ($i=0; $i < count($query); $i++) {
          $ID_program_study1 = $query[$i]['ID_program_study'];
          $arr = array(
              'data' => array(),
              'subtotal' => 0,
          );
          $data = $arr['data'];
          $subtotal = $arr['subtotal'];
          $Pembayaranke = 1;
          $data[] = $query[$i] + array('Pembayaranke' => $Pembayaranke);
          $subtotal = $subtotal + $query[$i]['Invoice'];
          $ID_register_formulir1 = $query[$i]['ID_register_formulir'];

          for ($j=$i + 1; $j < count($query); $j++) { // search by prodi
            $ID_program_study2 = $query[$j]['ID_program_study'];
            if ($ID_program_study1 == $ID_program_study2) {
              $ID_register_formulir2 = $query[$j]['ID_register_formulir'];
              if ($ID_register_formulir1 == $ID_register_formulir2) {
                $Pembayaranke++;
                $data[] = $query[$j] + array('Pembayaranke' => $Pembayaranke);
                $subtotal = $subtotal + $query[$j]['Invoice'];

                // search by ID_register_formulir
                for ($k=$j+1; $k < count($query); $k++) {
                  $ID_register_formulir3 = $query[$k]['ID_register_formulir'];
                  if ($ID_register_formulir3 == $ID_register_formulir2) {
                    $Pembayaranke++;
                    $data[] = $query[$j] + array('Pembayaranke' => $Pembayaranke);
                    $subtotal = $subtotal + $query[$j]['Invoice'];
                  }
                  else
                  {
                    $j = $k -1;
                    $ID_register_formulir1 = $ID_register_formulir3;
                    $Pembayaranke =0;
                    break;
                  }
                  $j = $k;
                }

              }
              else
              {
                $Pembayaranke =  1;
                $data[] = $query[$j] + array('Pembayaranke' => $Pembayaranke,'ID_register_formulir2j' => $ID_register_formulir2,'ID_register_formulir1i'=>$ID_register_formulir1);
                $subtotal = $subtotal + $query[$j]['Invoice'];
                $ID_register_formulir1 = $ID_register_formulir2;
              }

            }
            else
            {
              $i = $j-1;
              break;
            }
            $i = $j;
          }

          $arr = array(
              'data' => $data,
              'subtotal' => $subtotal,
          );

          $arr_result[] = $arr;

        }
        // print_r($arr_result);die();
         return $arr_result;
   }

   public function getPayment_Daily_mhs($Semester,$DailyTgl)
   {
      $arr_result = array();
      $SemesterID = explode('.', $Semester);
      $SemesterID = $SemesterID[0];
      $this->load->model('master/m_master');
      $sql = 'select * from (
                  select a.*, b.Name as NamaMHS,b.Year,b.EmailPU,b.Pay_Cond,c.Name as NameSemester, d.Description ,e.DatePayment as DatePayment_STD,e.ID_payment,b.ProdiID,f.Name as NamePrody,e.ID as ID_payment_students
              ,e.Invoice as PaymentMhs_STD,psd.Pay as PaymentMhs,psd.Pay_Date as DatePayment
                    from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                    join db_academic.semester as c on a.SemesterID = c.ID
                    join db_finance.payment_type as d on a.PTID = d.ID
                    join db_finance.payment_students as e on a.ID = e.ID_payment
                    join db_academic.program_study as f on b.ProdiID = f.ID
                    join db_finance.payment_student_details as psd on psd.ID_payment_students = e.`ID`
                    where a.SemesterID = ? and psd.Pay_Date like "'.$DailyTgl.'%"
                    UNION
                    select a.*, b.Name as NamaMHS,b.Year,b.EmailPU,b.Pay_Cond,sa.Name as NameSemester, d.Description ,e.DatePayment as DatePayment_STD,e.ID_payment,b.ProdiID,f.Name as NamePrody,e.ID as ID_payment_students
                            ,e.Invoice as PaymentMhs_STD,psd.Pay as PaymentMhs,psd.Pay_Date as DatePayment
                                  from db_finance.payment as a join db_academic.auth_students as b on a.NPM = b.NPM
                                  join db_academic.sa_academic_years as c on a.SemesterID = c.SASemesterID
                                  join db_academic.semester_antara as sa on sa.ID = c.SASemesterID
                                  join db_finance.payment_type as d on a.PTID = d.ID
                                  join db_finance.payment_students as e on a.ID = e.ID_payment
                                  join db_academic.program_study as f on b.ProdiID = f.ID
                                  join db_finance.payment_student_details as psd on psd.ID_payment_students = e.`ID`
                                  where sa.SemesterID = ? and psd.Pay_Date like "'.$DailyTgl.'%"
              ) aa
              order by ProdiID asc,NPM asc,ID_payment asc,ID_payment_students asc';
      // print_r($sql);die();
              //and a.SemesterID = ? and e.DatePayment like "'.$DailyTgl.'%"
      $query=$this->db->query($sql, array($SemesterID,$SemesterID))->result_array();
      $f_Pembayaranke = function($ID_payment,$ID_payment_students){
         $sql = 'select * from db_finance.payment_students where ID_payment = ?';
         $query=$this->db->query($sql, array($ID_payment))->result_array();
         if (count($query) > 0) {
           $payno = 0;
           for ($i=0; $i < count($query); $i++) {
            if ($query[$i]['ID'] == $ID_payment_students) {
                $payno = $i+1;
                break;
              }
           }
           return $payno;
         }
         return false;
      };

      $f_findSemester = function($SemesterID,$Year){
          $sql = 'select * from db_academic.semester order by ID asc';
          $query=$this->db->query($sql, array())->result_array();
          $semester = 0;
          // finc sequence year
          $S_year = 0;
          for ($i=0; $i < count($query); $i++) {
            if ($query[$i]['Year'] == $Year && $query[$i]['Code'] == 1) {
              $S_year = $i + 1;
              break;
            }
          }

          $S_semester = 0;
          for ($i=0; $i < count($query); $i++) {
            if ($query[$i]['ID'] == $SemesterID) {
              $S_semester = $i + 1;
              break;
            }
          }
          $semester = $S_semester -  $S_year + 1 ;
          return $semester;
      };

      for ($i=0; $i < count($query); $i++) {
        $ID_program_study1 = $query[$i]['ProdiID'];
        $arr = array(
            'data' => array(),
            'subtotal' => 0,
        );
        $data = $arr['data'];
        $subtotal = $arr['subtotal'];
        $Pembayaranke = $f_Pembayaranke($query[$i]['ID_payment'],$query[$i]['ID_payment_students']); // query berdasarkan ID payment
        // jika Semester Antara
        if ($query[$i]['PTID'] == 5 || $query[$i]['PTID'] == 6) {
          $G_data = $this->m_master->caribasedprimary('db_academic.semester_antara','ID',$query[$i]['SemesterID']);
          $SemesterID = $G_data[0]['SemesterID'];
          $semesterCount = $f_findSemester($SemesterID,$query[$i]['Year']);
        }
        else
        {
          $semesterCount = $f_findSemester($query[$i]['SemesterID'],$query[$i]['Year']);
        }

        $data[] = $query[$i] + array('Pembayaranke' => $Pembayaranke,'semesterCount' => $semesterCount);
        // $subtotal = $subtotal + $query[$i]['PaymentMhs'];
        $subtotal = $subtotal + $query[$i]['PaymentMhs'];
        $NPM1 = $query[$i]['NPM'];

        for ($j=$i + 1; $j < count($query); $j++) { // search by prodi
          $ID_program_study2 = $query[$j]['ProdiID'];
          if ($ID_program_study1 == $ID_program_study2) {
            $Pembayaranke =  $f_Pembayaranke($query[$j]['ID_payment'],$query[$j]['ID_payment_students']); // query berdasarkan ID payment
            if ($query[$j]['PTID'] == 5 || $query[$j]['PTID'] == 6) {
              $G_data = $this->m_master->caribasedprimary('db_academic.semester_antara','ID',$query[$j]['SemesterID']);
              $SemesterID = $G_data[0]['SemesterID'];
              $semesterCount = $f_findSemester($SemesterID,$query[$j]['Year']);
            }
            else
            {
              $semesterCount = $f_findSemester($query[$j]['SemesterID'],$query[$j]['Year']);
            }
            $data[] = $query[$j] + array('Pembayaranke' => $Pembayaranke,'semesterCount' => $semesterCount);
            $subtotal = $subtotal + $query[$j]['PaymentMhs'];

          }
          else
          {
            $i = $j-1;
            break;
          }
          $i = $j;
        }

        $arr = array(
            'data' => $data,
            'subtotal' => $subtotal,
        );

        $arr_result[] = $arr;

      }
      // print_r($arr_result);die();
       return $arr_result;
   }

   public function GetRequestChangeStatus_Mhs($SemesterID)
   {
    $sql = 'select a.ID,a.PTID,b.Description,a.SemesterID,c.Name as NameSemester,a.NPM,d.Name as    NameMHS,d.ProdiID,e.NameEng,a.Invoice,a.Status,d.EmailPU,f.Description as StatusMhs,
            a.ToChange,if(a.Invoice <= (select sum(Invoice) as Tot from db_finance.payment_students where ID_payment = a.ID and Status = 1 limit 1),"Lunas","Belum Lunas" ) as StatusPay,
            (select sum(Invoice) as Tot from db_finance.payment_students where ID_payment = a.ID and Status = 1 limit 1) as Payment
             from db_finance.payment as a left join db_finance.payment_type as b on a.PTID = b.ID
            left join db_academic.semester as c on a.SemesterID = c.ID
            left join db_academic.auth_students as d on a.NPM = d.NPM
            left join db_academic.program_study as e on d.ProdiID = e.ID
            left join db_academic.status_student as f on d.StatusStudentID = f.ID
            where (a.ToChange = 1 or a.ToChange = 2) and a.SemesterID = "'.$SemesterID.'" order by d.StatusStudentID asc,a.NPM
            ';
      $query=$this->db->query($sql, array())->result_array();
      return $query;
   }

   public function PaymentTidakLunas($SemesterID)
   {
    $sql = 'select a.ID,a.PTID,b.Description,a.SemesterID,c.Name as NameSemester,a.NPM,d.Name as    NameMHS,d.ProdiID,e.NameEng,a.Invoice,a.Status,d.EmailPU,
            a.ToChange,if(a.Invoice <= (select sum(Invoice) as Tot from db_finance.payment_students where ID_payment = a.ID and Status = 1 limit 1),"Lunas","Belum Lunas" ) as StatusPay,
            (select sum(Invoice) as Tot from db_finance.payment_students where ID_payment = a.ID and Status = 1 limit 1) as Payment
             from db_finance.payment as a left join db_finance.payment_type as b on a.PTID = b.ID
            left join db_academic.semester as c on a.SemesterID = c.ID
            left join db_academic.auth_students as d on a.NPM = d.NPM
            left join db_academic.program_study as e on d.ProdiID = e.ID
            where a.Invoice >= (select sum(Invoice) as Tot from db_finance.payment_students where ID_payment = a.ID and Status = 0 limit 1) and a.SemesterID = "'.$SemesterID.'" AND d.StatusStudentID in (3,2,8) order by a.NPM
            ';
      $query=$this->db->query($sql, array())->result_array();
      return $query;
   }

   public function save_potongan($dataForm){
    $rs = ['status' => 0,'msg' => ''];

    $tbl_payment = $this->db->query(
       'select * from db_finance.payment
        where NPM = "'.$dataForm['NPM'].'" and PTID = '.$dataForm['PTID'].' and SemesterID = '.$dataForm['SemesterID'].'
       '
    )->result_array()[0];

    $tbl_payment_std = $this->findDatapayment_studentsBaseID_payment($tbl_payment['ID'],1);
    if (count($tbl_payment_std) == 0) {

      // check data telah melakukan potongan
      $checkPotongan = $this->db->query(
        'select * from db_finance.payment_student_potongan
        where NPM = "'.$dataForm['NPM'].'" and PTID = '.$dataForm['PTID'].' and SemesterID = '.$dataForm['SemesterID'].'
        '
      )->result_array();

      if (count($checkPotongan) > 0) {
        $rs['msg'] = 'Student already got another discount payment, please cancel payment first';
        return $rs;
      }

      $data = $dataForm['data'];
      $total = 0;
      for ($i=0; $i < count($data); $i++) { 
        $dataSave = $data[$i];
        $dataSave = $dataSave + [
          'NPM' => $dataForm['NPM'] ,
          'PTID' => $dataForm['PTID'] ,
          'SemesterID' => $dataForm['SemesterID'] ,
          'UpdatedBy' => $this->session->userdata('NIP'),
          'UpdateAt' => date('Y-m-d H:i:s'),
        ];

        $this->db->insert('db_finance.payment_student_potongan',$dataSave);
        $total += $data[$i]['DiscountValue'];

      }

      $InvoiceUpdated = $tbl_payment['Invoice'] - $total;
      $this->db->where('ID',$tbl_payment['ID']);
      $this->db->update('db_finance.payment',['Invoice' => $InvoiceUpdated,'UpdateAt' => date('Y-m-d H:i:s'),'UpdatedBy' => $this->session->userdata('NIP') ]);

      $dataPaymentStd = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$tbl_payment['ID']);
      // delete table payment student
      $this->db->where('ID_payment',$tbl_payment['ID']);
      $this->db->delete('db_finance.payment_students');

       $this->db->insert('db_finance.payment_students',[
        'ID_payment' => $tbl_payment['ID'],
        'Invoice' => $InvoiceUpdated,
        'BilingID' => 0,
        'Deadline' => $dataPaymentStd[0]['Deadline']
      ]);

      $rs['status'] = 1;
    }
    else
    {
      $rs['msg'] = 'Student have been paid the payment';
    }

    return $rs;

    
   }

   public function getPotonganLain($PTID,$SemesterID,$NPM){
    $sql = 'select a.*,b.Name from db_finance.payment_student_potongan as a
            join db_employees.employees as b on a.UpdatedBy = b.NIP
     where NPM = "'.$NPM.'" and SemesterID = '.$SemesterID.' and PTID = '.$PTID.' ';
    $query=$this->db->query($sql, array($PTID,$SemesterID,$NPM))->result_array();
    return $query;
   }

   public function deposit_saldo_uang_tititpan_mhs($NPM){
    $this->load->model('m_master');
    $get = $this->db->query(
            '
              select ( sum(Credit) - sum(Debit) ) as left_saldo, sum(Credit) as CreditTotal,sum(Debit) as DebitTotal
              from db_finance.trans_deposit 
              where NPM = "'.$NPM.'"
            '
          )->row()->left_saldo;

    return (!empty($get) && $get > 0) ? $get : 0; 
   }

   public function get_deposit_saldo_uang_tititpan($TA){
      $rs = [];
      $listMHSDeposit = $this->db->select('b.NPM,b.Name,c.Code as CodeProdi')
                                 ->join('db_academic.auth_students  b','a.NPM = b.NPM','join')
                                 ->join('db_academic.program_study c','b.ProdiID = c.ID','join')
                                 ->where('b.Year',$TA)
                                 ->group_by('a.NPM')
                                 ->get('db_finance.trans_deposit a')->result_array();

      for ($i=0; $i < count($listMHSDeposit); $i++) { 
        $NPM = $listMHSDeposit[$i]['NPM'];
        $left_saldo = $this->deposit_saldo_uang_tititpan_mhs($NPM);

        if ( (!empty($left_saldo)) && $left_saldo > 0  ) {
          $listMHSDeposit[$i]['Saldo'] = $left_saldo;
          $rs[] = $listMHSDeposit[$i];
        }
      }

      return $rs;
      
   }

   public function rekap_std_by_status($StatusStudentID){
      $rs = [];
      $this->load->model('m_master');
      // distinct month
      $dataMonthly = $this->db->select('YEAR(EffectiveDateStatus) as Yearly, MONTH(EffectiveDateStatus) as Monthly')
                                 ->where('StatusStudentID',$StatusStudentID)
                                 ->group_by('YEAR(EffectiveDateStatus), MONTH(EffectiveDateStatus)')
                                 ->order_by('EffectiveDateStatus','desc')
                                 ->get('db_academic.auth_students a')->result_array();
      
      // add NULL EffectiveDateStatus
      $dataMonthly2 = $this->db->select(' EffectiveDateStatus as Yearly, EffectiveDateStatus as Monthly')
                                 ->where('StatusStudentID',$StatusStudentID)
                                 ->where('EffectiveDateStatus is NULL')
                                 ->group_by('YEAR(EffectiveDateStatus), MONTH(EffectiveDateStatus)')
                                 ->get('db_academic.auth_students a')->result_array();

      $dataMonthly = $this->m_master->TwoArraysObjectJoin($dataMonthly,$dataMonthly2); // join

      $rs = [
          'StatusStudentID' => $StatusStudentID,
          'StatusStudentName' => $this->db->select('Description')
                                     ->where('ID',$StatusStudentID)
                                     ->get('db_academic.status_student')->row()->Description,
          'dataPerMonth' => [],

      ];

      for ($i=0; $i < count($dataMonthly); $i++) { 
        $Yearly = $dataMonthly[$i]['Yearly'];
        $Monthly = $dataMonthly[$i]['Monthly'];

        if ($Yearly != NULL && !empty($Yearly)) {
          $NameSemester = $Yearly.'/'.($Yearly+1);
          // get semester name by year
          $dataMHS =  $this->db->select('a.NPM,a.Name,c.Name as ProdiName,a.Year,a.EffectiveDateStatus')
                                     ->join('db_academic.program_study c','a.ProdiID = c.ID','join')
                                     ->where('a.StatusStudentID',$StatusStudentID)
                                     ->where('YEAR(EffectiveDateStatus) = '.$Yearly)
                                     ->where('MONTH(EffectiveDateStatus) = '.$Monthly)
                                     ->get('db_academic.auth_students a')->result_array();
        }
        else
        {
          // $NameSemester = 'EffectiveDateStatus belum diisi';
          $NameSemester = '-';
          // get semester name by year
          $dataMHS =  $this->db->select('a.NPM,a.Name,c.Name as ProdiName,a.Year,a.EffectiveDateStatus')
                                     ->join('db_academic.program_study c','a.ProdiID = c.ID','join')
                                     ->where('a.StatusStudentID',$StatusStudentID)
                                     ->where('a.EffectiveDateStatus is NULL')
                                     ->get('db_academic.auth_students a')->result_array();

        }

         $arr = [
            'endNameSemester' => $NameSemester,
            'data' => []
          ];

        // get All payment dari semester 1
        for ($j=0; $j < count($dataMHS); $j++) { 
          $getTA = $dataMHS[$j]['Year'];
          $dataMHS[$j]['ALLPayment'] = $this->Payment_SemesterStart_to_End($dataMHS[$j]['NPM'],$getTA);
        }

        $arr['data'] = $dataMHS;
        $rs['dataPerMonth'][] = $arr;
      }

      return $rs;

   }

   public function Payment_SemesterStart_to_End($NPM,$TA=''){
      $rs = [];
      if ($TA == '') {
        $TA =  $this->db->select('Year')
                       ->where('NPM',$NPM)
                       ->get('db_academic.auth_students')->row()->Year;
      }


      $YearSemesterEnd = $this->db->select('EffectiveDateStatus')
                                  ->where('NPM',$NPM)
                                  ->get('db_academic.auth_students')->row()->EffectiveDateStatus;

      $t = (!empty($YearSemesterEnd) && $YearSemesterEnd != NULL) ? true : false;
      $YearSemesterEnd = (!empty($YearSemesterEnd) && $YearSemesterEnd != NULL) ? date('Y', strtotime($YearSemesterEnd)) : 14;
     

      $Semester = 1;
      $get_Semester =  $this->db->select('*')
                                   ->where('Year >= '.$TA)
                                   ->order_by('Year','asc')
                                   ->order_by('Code','asc')
                                   ->get('db_academic.semester')->result_array();
      // TITIPAN
      $TITIPAN = $this->deposit_saldo_uang_tititpan_mhs($NPM);
      $temp = [
        'NPM' => $NPM,
        'TITIPAN' => $TITIPAN,
        'Payment' => []
      ];

      for ($i=0; $i < count($get_Semester); $i++) { 
        $SemesterID = $get_Semester[$i]['ID'];
         $JUMLAH_TAGIHAN = 0;
         $JUMLAH_PENERIMAAN = 0;

         $data_Payment =  $this->db->select('*')
                                   ->where('SemesterID',$SemesterID)
                                   ->where('NPM',$NPM)
                                   ->get('db_finance.payment')->result_array();

          for ($j=0; $j < count($data_Payment); $j++) { 
            $JUMLAH_TAGIHAN = $JUMLAH_TAGIHAN + $data_Payment[$j]['Invoice'];
            $ID_payment = $data_Payment[$j]['ID'];
            //$get_payment_student = $this->findDatapayment_studentsBaseID_payment($ID_payment,1);
            $get_payment_student = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$ID_payment);

            for ($k=0; $k < count($get_payment_student); $k++) { 
              // $JUMLAH_PENERIMAAN = $JUMLAH_PENERIMAAN + $get_payment_student[$k]['Invoice'];
              $ID_payment_students = $get_payment_student[$k]['ID'];

              $SumTotal =  $this->db->query(
                '
                  select sum(Pay) as SumTotal from db_finance.payment_student_details
                  where ID_payment_students = '.$ID_payment_students.'                
                '
              )->row()->SumTotal;

              $SumTotal = (empty($SumTotal)) ? 0 : $SumTotal;

              $JUMLAH_PENERIMAAN = $JUMLAH_PENERIMAAN + $SumTotal;

            }
          }

          $temp['Payment'][] = [
              'SemesterID' => $SemesterID,
              'Semester' => $Semester,
              'JUMLAH_TAGIHAN' => $JUMLAH_TAGIHAN,
              'JUMLAH_PENERIMAAN' => $JUMLAH_PENERIMAAN,
          ];

          if ($t) {
             // get last semester
             $LastSemester = $this->db->select('*')
                                    ->where('Year',$YearSemesterEnd)
                                    ->where('Code',2)
                                    ->get('db_academic.semester')->result_array();

             if (count($LastSemester) == 0) {
                break;
              }
              else{
                $LastSemester = $LastSemester[0]['ID'];
                if ($SemesterID >= $LastSemester) {
                  break;
                }
              }                       
             
          }
          else
          {
            if ($Semester >= $YearSemesterEnd) {
              break;
            }
          }
          $Semester++;                         
      }
     
      $rs = $temp;
      return $rs;

   }

   public function report_rekap_std($StatusStudentArr){
      $rs = [];

      for ($i=0; $i < count($StatusStudentArr); $i++) { 
        $data = $this->rekap_std_by_status($StatusStudentArr[$i]);
        $rs[] = $data;
      }

      return $rs;
   }

   public function detail_payment_std($ID_payment) {
    $DetailPayment = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$ID_payment);
    for ($i=0; $i < count($DetailPayment); $i++) { 
      $payment_student_details = $this->m_master->caribasedprimary('db_finance.payment_student_details','ID_payment_students',$DetailPayment[$i]['ID']);
      $DetailPayment[$i]['payment_student_details'] = $payment_student_details;
    }

    return $DetailPayment;

   }

   public function _count_left_paymet($ID_payment){
      $left_payment = 0;
      $total_invoice = 0;
      $have_pay = 0;
      $payment = $this->m_master->caribasedprimary('db_finance.payment','ID',$ID_payment);
      if (count($payment) > 0) {
        $Invoice = $payment[0]['Invoice'];

        $payment_students = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$ID_payment);
        for ($i=0; $i < count($payment_students) ; $i++) { 
          if ($payment_students[$i]['Status'] == 1 || $payment_students[$i]['Status'] == '1') {
            $have_pay += $payment_students[$i]['Invoice'];
          }
          else{
            $payment_student_details = $this->m_master->caribasedprimary('db_finance.payment_student_details','ID_payment_students',$payment_students[$i]['ID']);

            if (count($payment_student_details) > 0) {
              for ($j=0; $j < count($payment_student_details); $j++) { 
                $have_pay += $payment_student_details[$j]['Pay'];
              }
            }
          }
        }

        $total_invoice = $Invoice;

        $left_payment = abs($total_invoice - $have_pay);

        return ['left_payment' => $left_payment,'total_invoice' => $total_invoice, 'have_pay' => $have_pay];
      }

      return false;
      
   }




}
