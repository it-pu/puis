<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_admission extends CI_Model {

  public $data = array(
                      'ID_register_document' => null,
                      );

  public function __construct()
    {
        parent::__construct();
        $this->load->model('master/m_master');
    }

    public function count_calon_mahasiswa()
    {
        $sql = "select count(*) as total from (
                select count(*) as total from db_admission.register_formulir as a
                join db_admission.register_document as b ON
                a.ID = b.ID_register_formulir
                where b.Status != 'Done'
                GROUP BY a.ID
                ) as a
                ";
        $query=$this->db->query($sql, array())->result_array();
        $conVertINT = (int) $query[0]['total'];
        return $conVertINT;
    }

    // public function CountSelectDataCalonMahasiswa($tahun,$nama,$status,$FormulirCode)
    public function CountSelectDataCalonMahasiswa($nama,$status,$FormulirCode)
    {
      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

      if($nama != '%') {
          $nama = '"%'.$nama.'%"';
      }
      else
      {
        $nama = '"%"';
      }
      if($status == 'Belum Done') {
        $status = 'Status != "Done"';
      }
      else
      {
        $status = 'Status = "Done"';
      }

      //$tahun = 'year(RegisterAT) = '.$tahun;
      $sql = "select count(*) as total from (
                select * from (
                select a.ID,z.name as name_programstudy,b.FormulirCode,
                (select count(*) as total from db_admission.register_document
                where ".$status." and ID_register_formulir = a.ID
                GROUP BY ID_register_formulir limit 1) as document_undone,
                (select count(*) as total from db_admission.register_document
                where Status = 'Progress Checking' and ID_register_formulir = a.ID
                GROUP BY ID_register_formulir limit 1) as document_progress,
                a.ID_program_study,d.Name,a.IdentityCard,e.ctr_name as Nationality,concat(a.PlaceBirth,',',a.DateBirth) as PlaceDateBirth,g.JenisTempatTinggal,
                h.ctr_name as CountryAddress,i.ProvinceName as ProvinceAddress,j.RegionName as RegionAddress,k.DistrictName as DistrictsAddress,
                            a.District as DistrictAddress,a.Address,a.ZipCode,a.PhoneNumber,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,
                n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,IF(a.KPSReceiverStatus = 'YA',CONCAT('No KPS : ',a.NoKPS),'Tidak') as KPSReceiver,
                a.UploadFoto,d.RegisterAT,az.No_Ref
                from db_admission.register_formulir as a
                Left JOIN db_admission.register_verified as b
                ON a.ID_register_verified = b.ID
                Left JOIN db_admission.register_verification as c
                ON b.RegVerificationID = c.ID
                Left JOIN db_admission.register as d
                ON c.RegisterID = d.ID
                Left JOIN db_admission.country as e
                ON a.NationalityID = e.ctr_code
                Left JOIN db_admission.register_jtinggal_m as g
                ON a.ID_register_jtinggal_m = g.ID
                Left JOIN db_admission.country as h
                ON a.ID_country_address = h.ctr_code
                Left JOIN db_admission.province as i
                ON a.ID_province = i.ProvinceID
                Left JOIN db_admission.region as j
                ON a.ID_region = j.RegionID
                Left JOIN db_admission.district as k
                ON a.ID_districts = k.DistrictID
                Left JOIN db_admission.school_type as l
                ON l.sct_code = a.ID_school_type
                Left JOIN db_admission.register_major_school as m
                ON m.ID = a.ID_register_major_school
                Left JOIN db_admission.school as n
                ON n.ID = d.SchoolID
                Left JOIN db_academic.program_study as z
                on a.ID_program_study = z.id
                Left JOIN db_admission.formulir_number_offline_m as az
                on b.FormulirCode = az.FormulirCode
                ) as a
                where document_undone > 0 and Name like ".$nama."
                and FormulirCode not in(select FormulirCode from db_admission.to_be_mhs) and (FormulirCode like ".$FormulirCode." or No_Ref like ".$FormulirCode.")
              ) aa
              "; // query undone

        $query=$this->db->query($sql, array())->result_array();
        return $query[0]['total'];
    }

    // public function selectDataCalonMahasiswa($limit,$start,$tahun,$nama,$status,$FormulirCode)
    public function selectDataCalonMahasiswa($limit,$start,$nama,$status,$FormulirCode)
    {
      $arr_temp = array('data' => array());
      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

      if($nama != '%') {
          $nama = '"%'.$nama.'%"';
      }
      else
      {
        $nama = '"%"';
      }
      if($status == 'Belum Done') {
        $status = 'Status != "Done"';
      }
      else
      {
        $status = 'Status = "Done"';
      }

      //$tahun = 'year(RegisterAT) = '.$tahun;
      $sql = "select * from (
              select a.ID,z.name as name_programstudy,b.FormulirCode,
              (select count(*) as total from db_admission.register_document
              where ".$status." and ID_register_formulir = a.ID
              GROUP BY ID_register_formulir limit 1) as document_undone,
              (select count(*) as total from db_admission.register_document
              where Status = 'Progress Checking' and ID_register_formulir = a.ID
              GROUP BY ID_register_formulir limit 1) as document_progress,
              a.ID_program_study,d.Name,a.IdentityCard,e.ctr_name as Nationality,concat(a.PlaceBirth,',',a.DateBirth) as PlaceDateBirth,g.JenisTempatTinggal,
              h.ctr_name as CountryAddress,i.ProvinceName as ProvinceAddress,j.RegionName as RegionAddress,k.DistrictName as DistrictsAddress,
                          a.District as DistrictAddress,a.Address,a.ZipCode,a.PhoneNumber,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,
              n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,IF(a.KPSReceiverStatus = 'YA',CONCAT('No KPS : ',a.NoKPS),'Tidak') as KPSReceiver,
              a.UploadFoto,d.RegisterAT,az.No_Ref
              from db_admission.register_formulir as a
              Left JOIN db_admission.register_verified as b
              ON a.ID_register_verified = b.ID
              Left JOIN db_admission.register_verification as c
              ON b.RegVerificationID = c.ID
              Left JOIN db_admission.register as d
              ON c.RegisterID = d.ID
              Left JOIN db_admission.country as e
              ON a.NationalityID = e.ctr_code
              Left JOIN db_admission.register_jtinggal_m as g
              ON a.ID_register_jtinggal_m = g.ID
              Left JOIN db_admission.country as h
              ON a.ID_country_address = h.ctr_code
              Left JOIN db_admission.province as i
              ON a.ID_province = i.ProvinceID
              Left JOIN db_admission.region as j
              ON a.ID_region = j.RegionID
              Left JOIN db_admission.district as k
              ON a.ID_districts = k.DistrictID
              Left JOIN db_admission.school_type as l
              ON l.sct_code = a.ID_school_type
              Left JOIN db_admission.register_major_school as m
              ON m.ID = a.ID_register_major_school
              Left JOIN db_admission.school as n
              ON n.ID = d.SchoolID
              Left JOIN db_academic.program_study as z
              on a.ID_program_study = z.id
              Left JOIN db_admission.formulir_number_offline_m as az
              on b.FormulirCode = az.FormulirCode
              ) as a
              where document_undone > 0 and Name like ".$nama."
              and FormulirCode not in(select FormulirCode from db_admission.to_be_mhs) and (FormulirCode like ".$FormulirCode." or No_Ref like ".$FormulirCode.")
              order by document_progress desc
              LIMIT ".$start. ", ".$limit; // query undone

        $query=$this->db->query($sql, array())->result();
          $a = 0;
          foreach ($query as $key) { // foreach 1
            $ID_register_formulir = $key->ID;
            $sql2 = "select a.*, b.DocumentChecklist,b.Required from db_admission.register_document as a
              join db_admission.reg_doc_checklist as b
              on a.ID_reg_doc_checklist = b.ID where a.ID_register_formulir = ? ";
              $query2=$this->db->query($sql2, array($ID_register_formulir))->result();
              $arr_document = array();
              $b = 0;
              foreach ($query2 as $row) { // foreach 2
                  $arr_document[$b] = array(
                                          'ID_register_document' => $row->ID,
                                          'DocumentChecklist' => $row->DocumentChecklist,
                                          'Required' => $row->Required,
                                          'Attachment' => $row->Attachment,
                                          'Status' => $row->Status,
                  );
                  $arr_temp['data'][$a] = array(
                              'Name' => $key->Name,
                              'Email' => $key->Email,
                              'PhoneNumber' => $key->PhoneNumber,
                              'Name_programstudy' => $key->name_programstudy,
                              'Alamat' => $key->Address." Kelurahan ".$key->DistrictAddress." ".$key->DistrictsAddress." ".$key->RegionAddress." ".$key->ProvinceAddress,
                              'SMA' => $key->SchoolName." ".$key->SchoolRegion." ".$key->SchoolProvince,
                              'document' => $arr_document,
                              'FormulirCode' => $key->FormulirCode,
                              'No_Ref' => $key->No_Ref,
                  );

                  $b++;
              }  // exit foreach 2
              $a++;
          } // exit foreach 1
          return $arr_temp;
    }

    public function updateStatusVeriDokumen($data_arr,$Status)
    {
        for ($i=0; $i < count($data_arr); $i++) {
          $arr = explode(";", $data_arr[$i]);
          $ID = $arr[0];
          //$NamaFile = ($arr[1] == 'nothing' ? $NamaFile="" : $NamaFile=$arr[1]);
          $VerificationBY = $this->session->userdata('NIP');
          $VerificationAT = date("Y-m-d H:i:s");
          // $sql = "update db_admission.register_document set Status = ?,Attachment = ?, VerificationBY = ?, VerificationAT = ? where ID = ?";
          $sql = "update db_admission.register_document set Status = ?, VerificationBY = ?, VerificationAT = ? where ID = ?";
          // $query=$this->db->query($sql, array($Status,$NamaFile,$VerificationBY,$VerificationAT,$ID));
          $query=$this->db->query($sql, array($Status,$VerificationBY,$VerificationAT,$ID));
        }

    }

    public function getKeylinkURLFormulirRegistration($ID_Register = null,$email = null)
    {
      $this->load->model('m_master');
      $ID_register_document = $this->data['ID_register_document'];
      $callback = array();
      switch ($ID_Register) {
        case null:
          $sql = "select a.ID,a.Email from db_admission.register as a
                  join db_admission.register_verification as b
                  on a.ID = b.RegisterID
                  join db_admission.register_verified as c
                  on b.ID = c.RegVerificationID
                  join db_admission.register_formulir as d
                  on c.ID = d.ID_register_verified
                  join db_admission.register_document as e
                  on d.ID = e.ID_register_formulir
                  where e.ID = ? LIMIT 1";
          $query=$this->db->query($sql, array($ID_register_document))->result_array();
          $RegisterID = $query[0]['ID'];
          if ($email == null) {
            $query = $this->m_master->caribasedprimary('db_admission.register','ID',$RegisterID);
            $email = $query[0]['Email'];
          }
          $this->getlinkURLFormulirRegistration($RegisterID,$email);
          break;

        default:
          $this->load->library('JWT');
          $key = "UAP)(*";
          if ($email == null) {
            $query = $this->m_master->caribasedprimary('db_admission.register','ID',$ID_Register);
            $email = $query[0]['Email'];
          }
          $url = $this->jwt->encode($ID_Register.";".$email,$key);
          $callback = array('url' => $url,'email' => $email);
          $this->data['callback'] = $callback;
          break;
      }

      return $callback;
    }

    private function getlinkURLFormulirRegistration($ID_Register,$email)
    {
      $this->load->library('JWT');
      $key = "UAP)(*";
      if ($email == null) {
        $query = $this->m_master->caribasedprimary('db_admission.register','ID',$ID_Register);
        $email = $query[0]['Email'];
      }
      $url = $this->jwt->encode($ID_Register.";".$email,$key);
      $callback = array('url' => $url,'email' => $email);
      $this->data['callback'] = $callback;
    }

    public function checkAllstatusDoneVeriDoc($ID_register_document)
    {
      $check = TRUE;
      $query = $this->m_master->caribasedprimary('db_admission.register_document','ID',$ID_register_document);
      $ID_register_formulir = $query[0]['ID_register_formulir'];
      $query = $this->m_master->caribasedprimary('db_admission.register_document','ID_register_formulir',$ID_register_formulir);
      for ($i=0; $i < count($query); $i++) {
        $Status = $query[$i]['Status'];
        if ($Status != 'Done') {
          $check = FALSE;
          break;
        }
      }
      return $check;
    }

    public function totalDataFormulir_offline()
    {
      $sql = "select count(*) as total from (
              select a.Name as NameCandidate,a.Email,z.SchoolName,c.FormulirCode,a.StatusReg
              from db_admission.register as a
              join db_admission.register_verification as b
              on a.ID = b.RegisterID
              join db_admission.register_verified as c
              on c.RegVerificationID = b.ID
              join db_admission.school as z
              on z.ID = a.SchoolID
              where a.StatusReg = 1
              ) as a right JOIN db_admission.formulir_number_offline_m as b
              on a.FormulirCode = b.FormulirCode
              ";
      $query=$this->db->query($sql, array())->result_array();
      $conVertINT = (int) $query[0]['total'];
      return $conVertINT;
    }

    public function totalDataFormulir_offline3($tahun,$NomorFormulir,$NamaStaffAdmisi,$status,$statusJual,$NomorFormulirRef)
    {

      if($NomorFormulir != '%') {
          $NomorFormulir = '"%'.$NomorFormulir.'%"';
      }
      else
      {
        $NomorFormulir = '"%"';
      }

      if($NomorFormulirRef != '%') {
          $NomorFormulirRef = ' and b.No_Ref like "%'.$NomorFormulirRef.'%"';
      }
      else
      {
        $NomorFormulirRef = '';
      }

      if($NamaStaffAdmisi != '%') {
          $NamaStaffAdmisi = ' and b.Sales like "%'.$NamaStaffAdmisi.'%"';
      }
      else
      {
        $NamaStaffAdmisi = '';
      }
      if($status != '%') {
        // $status = '"%'.$status.'%"';
        // $status = 'StatusUsed != '.$status;
        $status = ' and b.Status = '.$status;
      }
      else
      {
        $status = '';
      }

      if($statusJual != '%') {
        // $status = '"%'.$status.'%"';
        // $status = 'StatusUsed != '.$status;
        $statusJual = ' and b.StatusJual = '.$statusJual;
      }
      else
      {
        $statusJual = '';
      }

      $sql = 'select count(*) as total from (
                  select a.NameCandidate,a.Email,a.SchoolName,b.FormulirCode,b.No_Ref,a.StatusReg,b.Years,b.Status as StatusUsed, b.StatusJual,
                                  b.FullName as NamaPembeli,b.PhoneNumber as PhoneNumberPembeli,b.HomeNumber as HomeNumberPembeli,b.Email as EmailPembeli,b.Sales,b.PIC as SalesNIP,b.SchoolNameFormulir,b.CityNameFormulir,b.DistrictNameFormulir,
                                  b.ID as ID_sale_formulir_offline,b.Price_Form,b.DateSale,b.src_name,b.NameProdi
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
                                  select a.FormulirCode,a.No_Ref,a.Years,a.Status,a.StatusJual,b.FullName,b.HomeNumber,b.PhoneNumber,b.DateSale,
                                  b.Email,c.Name as Sales,b.PIC,b.ID,b.Price_Form,z.SchoolName as SchoolNameFormulir,z.CityName as  CityNameFormulir,z.DistrictName as DistrictNameFormulir,
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
                                  where Years = "'.$tahun.'" and b.FormulirCode like '.$NomorFormulir.$NamaStaffAdmisi.$status.$statusJual.$NomorFormulirRef.'
              ) aa
              ';
      $query=$this->db->query($sql, array())->result_array();
      $conVertINT = (int) $query[0]['total'];
      return $conVertINT;
    }

    public function totalDataFormulir_offline4($reqTahun,$requestData,$statusJual)
    {

      if($statusJual != '%') {
        // $status = '"%'.$status.'%"';
        // $status = 'StatusUsed != '.$status;
        $statusJual = ' and b.StatusJual = '.$statusJual;
      }
      else
      {
        $statusJual = '';
      }

      // check session division
      $PositionMain = $this->session->userdata('PositionMain');
      $division = $PositionMain['IDDivision'];
        $queryDiv = "";
        switch ($division) {
          case 10:
            $queryDiv = ' where LEFT(c.PositionMain ,INSTR(c.PositionMain ,".")-1) = "'.$division.'"';
            break;
          case 18:
            $queryDiv = ' where LEFT(c.PositionMain ,INSTR(c.PositionMain ,".")-1) = "'.$division.'"';
            break;
          default:
            $queryDiv = "";
            break;
        }


      $sql = 'select count(*) as total from
              (
                select a.NameCandidate,a.Email,a.SchoolName,b.FormulirCode,b.No_Ref,a.StatusReg,b.Years,b.Status as StatusUsed,
                b.StatusJual, b.FullName as NamaPembeli,b.PhoneNumber as PhoneNumberPembeli,b.HomeNumber as HomeNumberPembeli,
                b.Email as EmailPembeli,b.Sales,b.PIC as SalesNIP,b.SchoolNameFormulir,b.CityNameFormulir,b.DistrictNameFormulir,
                b.ID as ID_sale_formulir_offline,b.Price_Form,b.DateSale,b.src_name,b.NameProdi,b.NoKwitansi from
                (
                  select a.Name as NameCandidate,a.Email,
                  z.SchoolName,c.FormulirCode,a.StatusReg from db_admission.register as a join db_admission.register_verification as b
                  on a.ID = b.RegisterID join db_admission.register_verified as c on c.RegVerificationID = b.ID join db_admission.school as z on z.ID = a.SchoolID where a.StatusReg = 1
                ) as a right JOIN
                (
                  select a.FormulirCode,a.No_Ref,a.Years,a.Status,a.StatusJual,b.FullName,b.HomeNumber,b.PhoneNumber,b.DateSale,b.NoKwitansi, b.Email,
                  c.Name as Sales,b.PIC,b.ID,b.Price_Form,z.SchoolName as SchoolNameFormulir,z.CityName as CityNameFormulir,z.DistrictName as DistrictNameFormulir,
                  if(b.source_from_event_ID = 0,"", (select src_name from db_admission.source_from_event where ID = b.source_from_event_ID and Active = 1 limit 1) ) as src_name,
                  b.ID_ProgramStudy,y.Name as NameProdi from db_admission.formulir_number_offline_m as a left join db_admission.sale_formulir_offline as b
                  on a.FormulirCode = b.FormulirCodeOffline left join db_employees.employees as c on c.NIP = b.PIC left join db_admission.school as z on z.ID = b.SchoolID
                  left join db_academic.program_study as y on b.ID_ProgramStudy = y.ID
                  '.$queryDiv.'
                ) as b on a.FormulirCode = b.FormulirCode where b.Years = "'.$reqTahun.'" AND
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
                  )'.$statusJual.'

              ) aa
              ';
              // print_r($sql);die();
      $query=$this->db->query($sql, array())->result_array();
      $conVertINT = (int) $query[0]['total'];
      return $conVertINT;
    }

    public function totalDataFormulir_offline2()
    {
      $sql = "select count(*) as total from db_admission.sale_formulir_offline
              ";
      $query=$this->db->query($sql, array())->result_array();
      $conVertINT = (int) $query[0]['total'];
      return $conVertINT;
    }

    public function totalDataFormulir_online($tahun = NULL,$NomorFormulir = NULL,$status = NULL)
    {
      $AddWhere = '';

      $sql = "select count(*) as total from (
              select c.FormulirCode
              from db_admission.register as a
              join db_admission.register_verification as b
              on a.ID = b.RegisterID
              join db_admission.register_verified as c
              on c.RegVerificationID = b.ID
              join db_admission.school as z
              on z.ID = a.SchoolID
              where a.StatusReg = 0
              ) as a right JOIN (
                select * from db_admission.formulir_number_online_m as b  where b.Years = ".$tahun."
              )as b on a.FormulirCode = b.FormulirCode
              left join db_admission.formulir_number_global as c on b.No_Ref = c.FormulirCodeGlobal
              ";
        

        if(!empty($NomorFormulir)){
          $WhereORAnd = ($AddWhere != '') ? ' Where ' : ' And ';
          $AddWhere .=  $WhereORAnd.' (b.FormulirCode like "'.$NomorFormulir.'" or b.No_Ref like "'.$NomorFormulir.'")'; // set for all query conditions
        } 

        if(!empty($status)){
          $WhereORAnd = ($AddWhere != '') ? ' Where ' : ' And ';
          if($status != '%') {
            $AddWhere = $WhereORAnd.' b.Status = '.$status;
          }
        }

        $sql .= $AddWhere;       
      $query=$this->db->query($sql, array())->result_array();
      $conVertINT = (int) $query[0]['total'];
      return $conVertINT;
    }

    public function selectDataDitribusiFormulirOnline($limit, $start,$tahun,$NomorFormulir,$status)
    {
      $arr_temp = array('data' => array());
      if($NomorFormulir != '%') {
          $NomorFormulir = '"%'.$NomorFormulir.'%"';
      }
      else
      {
        $NomorFormulir = '"%"';
      }

      if($status != '%') {
        // $status = '"%'.$status.'%"';
        // $status = 'StatusUsed != '.$status;
        $status = ' and b.Status = '.$status;
      }
      else
      {
        $status = '';
      }

        $sql = 'select a.ID,a.NameCandidate,a.Email,a.SchoolName,b.FormulirCode,a.StatusReg,b.Years,b.Status as StatusUsed,b.No_Ref,b.NoKwitansi,a.Phone,a.PriceFormulir,a.VerificationAT from (
          select a.Name as NameCandidate,a.Email,z.SchoolName,c.FormulirCode,a.StatusReg,a.Phone,a.ID,a.PriceFormulir,c.VerificationAT
          from db_admission.register as a
          join db_admission.register_verification as b
          on a.ID = b.RegisterID
          join db_admission.register_verified as c
          on c.RegVerificationID = b.ID
          left join db_admission.school as z
          on z.ID = a.SchoolID
          where a.StatusReg = 0
          ) as a right JOIN (
            select * from db_admission.formulir_number_online_m as b  where b.Years = "'.$tahun.'"
          )as b
          on a.FormulirCode = b.FormulirCode
          left join db_admission.formulir_number_global as c on b.No_Ref = c.FormulirCodeGlobal
          where (b.FormulirCode like '.$NomorFormulir.' or b.No_Ref like '.$NomorFormulir.')'.$status.' LIMIT '.$start. ', '.$limit;
           $query=$this->db->query($sql, array())->result_array();
           return $query;
    }

    public function selectDataDitribusiFormulirOffline($limit, $start,$tahun,$NomorFormulir,$NamaStaffAdmisi,$status,$statusJual,$NomorFormulirRef)
    {
      $arr_temp = array('data' => array());
      if($NomorFormulir != '%') {
          $NomorFormulir = '"%'.$NomorFormulir.'%"';
      }
      else
      {
        $NomorFormulir = '"%"';
      }

      if($NomorFormulirRef != '%') {
          $NomorFormulirRef = ' and b.No_Ref like "%'.$NomorFormulirRef.'%"';
      }
      else
      {
        $NomorFormulirRef = '';
      }

      if($NamaStaffAdmisi != '%') {
          $NamaStaffAdmisi = ' and b.Sales like "%'.$NamaStaffAdmisi.'%"';
      }
      else
      {
        $NamaStaffAdmisi = '';
      }
      if($status != '%') {
        // $status = '"%'.$status.'%"';
        // $status = 'StatusUsed != '.$status;
        $status = ' and b.Status = '.$status;
      }
      else
      {
        $status = '';
      }

      if($statusJual != '%') {
        // $status = '"%'.$status.'%"';
        // $status = 'StatusUsed != '.$status;
        $statusJual = ' and b.StatusJual = '.$statusJual;
      }
      else
      {
        $statusJual = '';
      }

        $sql = 'select a.NameCandidate,a.Email,a.SchoolName,b.FormulirCode,b.No_Ref,a.StatusReg,b.Years,b.Status as StatusUsed, b.StatusJual,
                b.FullName as NamaPembeli,b.PhoneNumber as PhoneNumberPembeli,b.HomeNumber as HomeNumberPembeli,b.Email as EmailPembeli,b.Sales,b.PIC as SalesNIP,b.SchoolNameFormulir,b.CityNameFormulir,b.DistrictNameFormulir,
                b.ID as ID_sale_formulir_offline,b.Price_Form,b.DateSale,b.src_name,b.NameProdi,b.NoKwitansi
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
                select a.FormulirCode,a.No_Ref,a.Years,a.Status,a.StatusJual,b.FullName,b.HomeNumber,b.PhoneNumber,b.DateSale,b.NoKwitansi,
                b.Email,c.Name as Sales,b.PIC,b.ID,b.Price_Form,z.SchoolName as SchoolNameFormulir,z.CityName as  CityNameFormulir,z.DistrictName as DistrictNameFormulir,
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
                where Years = "'.$tahun.'" and b.FormulirCode like '.$NomorFormulir.$NamaStaffAdmisi.$status.$statusJual.$NomorFormulirRef.' order by b.No_Ref asc LIMIT '.$start. ', '.$limit;
           $query=$this->db->query($sql, array())->result_array();
           return $query;
    }

    public function updateSelloutFormulir($data_arr)
    {
      $SellLinkBy = $this->session->userdata('NIP');

      for ($i=0; $i < count($data_arr); $i++) {
        if ($data_arr == 'nothing') {
          continue;
        }
        $sql = "update db_admission.formulir_number_offline_m set StatusJual = 1,SellLinkBy = ? where FormulirCode = ?";
        $query=$this->db->query($sql, array($SellLinkBy,$data_arr[$i]));
      }
    }

    public function getJadwalUjian()
    {
      $sql = "select c.Name,a.ID_ujian_perprody,DATE(a.DateTimeTest) as tanggal
              ,CONCAT((EXTRACT(HOUR FROM a.DateTimeTest)),':',(EXTRACT(MINUTE FROM a.DateTimeTest))) as jam,
              a.Lokasi from db_admission.register_jadwal_ujian as a
              join db_admission.ujian_perprody_m as b
              on a.ID_ujian_perprody = b.ID
              join db_academic.program_study as c
              on c.ID = b.ID_ProgramStudy
              GROUP BY c.Name,DATE(a.DateTimeTest)
              ";
      $query=$this->db->query($sql, array())->result_array();
      return $query;
    }

    public function save_jadwal_ujian($ID_ujian_perprody,$DateTimeTest,$Lokasi)
    {
      $dataSave = array(
              'ID_ujian_perprody' => $ID_ujian_perprody,
              'DateTimeTest' => $DateTimeTest,
              'Lokasi' => $Lokasi,
      );
      $this->db->insert('db_admission.register_jadwal_ujian', $dataSave);
    }

    public function getID_register_formulir_programStudy_arr($arr)
    {
      $arr_temp = array();
      for ($i=0; $i < count($arr); $i++) {
        $sql = "select ID from db_admission.register_formulir where ID_program_study = ? and ID in (select ID_register_formulir from db_admission.register_butuh_ujian)";
        $query=$this->db->query($sql, array($arr[$i]['ID_ProgramStudy']))->result_array();
        for ($j=0; $j < count($query); $j++) {
          $arr_temp[] = array('ID_register_formulir' => $query[$j]['ID'],'ID_register_jadwal_ujian' => $arr[$i]['ID_register_jadwal_ujian'],'ID_ProgramStudy'=>$arr[$i]['ID_ProgramStudy']);
        }
      }

      return $arr_temp;
    }

    public function get_arr_ID_ujian_per_prody($arr_ID_ProgramStudy)
    {
      $arr_temp = array('result' => '','data' => array());
      $arr = array();
      $x = 0;
      for ($i=0; $i <  count($arr_ID_ProgramStudy) ; $i++) {
        $sql = "select ID,ID_ProgramStudy from db_admission.ujian_perprody_m where ID_ProgramStudy = ? ";
        $query=$this->db->query($sql, array($arr_ID_ProgramStudy[$i]))->result_array();
        // print_r($query);
          if (count($query) == 0) {
            $arr_temp['result'] = 'Ujian Masuk Per Prody belum disetting, silahkan inputkan dulu pada Master Registration Ujian Per Prody';
            break;
          }
          else
          {
              for ($j=0; $j < count($query); $j++) {
                $arr[$x] = array('ID_ujian_perprody' =>$query[$j]['ID'], 'ID_ProgramStudy' => $query[$j]['ID_ProgramStudy'] );
                $x++;
              }
          }
      }
      $arr_temp['data'] = $arr;
      return $arr_temp;
    }

    public function saveDataJadwalUjian_returnArr($arr_ID_ujian_per_prody,$DateTimeTest,$Lokasi)
    {
      $arr_temp = array();
      $x = 0;
       // print_r($arr_ID_ujian_per_prody['data']);
      for ($i=0; $i < count($arr_ID_ujian_per_prody['data']); $i++) {
        try{
          $dataSave = array(
                  'ID_ujian_perprody' => $arr_ID_ujian_per_prody['data'][$i]['ID_ujian_perprody'],
                  'DateTimeTest' => $DateTimeTest,
                  'Lokasi' => $Lokasi,
          );
          $this->db->insert('db_admission.register_jadwal_ujian', $dataSave);
        }
        catch(Exception $e)
        {
          continue;
        }

        $sql = "select ID from db_admission.register_jadwal_ujian where ID_ujian_perprody = ? and DateTimeTest = ? and Lokasi = ?";
        $query=$this->db->query($sql, array($arr_ID_ujian_per_prody['data'][$i]['ID_ujian_perprody'],$DateTimeTest,$Lokasi))->result_array();
        $arr = array();
        for ($j=0; $j < count($query) ; $j++) {
          $arr_temp[$x] = array('ID_register_jadwal_ujian' => $query[0]['ID'],'ID_ProgramStudy' => $arr_ID_ujian_per_prody['data'][$i]['ID_ProgramStudy'] );
          $x++;
        }
      }

      return $arr_temp;
    }

    public function saveDataregister_formulir_jadwal_ujian($arr_id)
    {
      //error_reporting(0);
      for ($i=0; $i < count($arr_id); $i++) {
        try
        {
          // check ID_register_formulir sudah ada pada jadwal ujian atau belum
          $sql = 'select count(*) as total from db_admission.register_formulir_jadwal_ujian where ID_register_formulir = ?';
          $query=$this->db->query($sql, array($arr_id[$i]['ID_register_formulir']))->result_array();

          $sql2 = 'select count(*) as total from db_admission.ujian_perprody_m where ID_ProgramStudy = ?';
          $query2=$this->db->query($sql2, array($arr_id[$i]['ID_ProgramStudy']))->result_array();
          // print_r($query[0]['total']);
          if ($query[0]['total'] != $query2[0]['total']) {
            $dataSave = array(
                    'ID_register_jadwal_ujian' => $arr_id[$i]['ID_register_jadwal_ujian'],
                    'ID_register_formulir' => $arr_id[$i]['ID_register_formulir'],
            );
            $this->db->insert('db_admission.register_formulir_jadwal_ujian', $dataSave);
          }

        }
        catch(Exception $e)
        {
          continue;
        }
      }

    }

    public function daftar_jadwal_ujian_load_data_now()
    {
      $sql = 'select c.Name as prody,a.ID_ujian_perprody,DATE(a.DateTimeTest) as tanggal
        ,CONCAT((EXTRACT(HOUR FROM a.DateTimeTest)),":",(EXTRACT(MINUTE FROM a.DateTimeTest))) as jam,
        a.Lokasi,
        h.Name as NameCandidate,h.Email,i.SchoolName,f.FormulirCode,e.ID as ID_register_formulir,if(h.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = f.FormulirCode limit 1) ,""  ) as No_Ref
        from db_admission.register_jadwal_ujian as a
        join db_admission.ujian_perprody_m as b
        on a.ID_ujian_perprody = b.ID
        join db_academic.program_study as c
        on c.ID = b.ID_ProgramStudy
        join db_admission.register_formulir_jadwal_ujian as d
        ON a.ID = d.ID_register_jadwal_ujian
        JOIN db_admission.register_formulir as e
        on e.ID = d.ID_register_formulir
        join db_admission.register_verified as f
        on e.ID_register_verified = f.ID
        join db_admission.register_verification as g
        on g.ID = f.RegVerificationID
        join db_admission.register as h
        on h.ID = g.RegisterID
        join db_admission.school as i
        on i.ID = h.SchoolID
        where DATE(a.DateTimeTest) = CURDATE()
        GROUP BY c.Name,DATE(a.DateTimeTest),e.ID';
      $query=$this->db->query($sql, array())->result_array();
      return $query;
    }

    public function daftar_jadwal_ujian_load_data_paging($limit, $start,$Nama,$FormulirCode)
    {
      $where = 'where DATE(a.DateTimeTest) > CURDATE() ';
      if ($Nama != '') {
        $where .= ' and h.Name like "%'.$Nama.'%" or i.SchoolName like "%'.$Nama.'%"';
        if ($FormulirCode != '') {
            // Checking Formulir Code
              $Q = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','No_Ref',$FormulirCode);
              if (count($Q) == 0) {
                $Q = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','FormulirCode',$FormulirCode);
                if (count($Q) == 0) {
                  $FormulirCode = $Q[0]['FormulirCode'];
                }
                else
                {
                  $FormulirCode = $No_Formulir;
                }
              }
              else
              {
                $FormulirCode = $Q[0]['FormulirCode'];
              }

           $where .= ' and f.FormulirCode like "%'.$FormulirCode.'%"';
         }
      }
      else
      {
        if ($FormulirCode != '') {
          // Checking Formulir Code
            $Q = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','No_Ref',$FormulirCode);
            if (count($Q) == 0) {
              $Q = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','FormulirCode',$FormulirCode);
              if (count($Q) == 0) {
                $FormulirCode = $Q[0]['FormulirCode'];
              }
              else
              {
                $FormulirCode = $FormulirCode;
              }
            }
            else
            {
              $FormulirCode = $Q[0]['FormulirCode'];
            }
          $where .= ' and f.FormulirCode like "%'.$FormulirCode.'%"';
        }
      }

      $sql = 'select c.Name as prody,a.ID_ujian_perprody,DATE(a.DateTimeTest) as tanggal
        ,CONCAT((EXTRACT(HOUR FROM a.DateTimeTest)),":",(EXTRACT(MINUTE FROM a.DateTimeTest))) as jam,
        a.Lokasi,
        h.Name as NameCandidate,h.Email,i.SchoolName,f.FormulirCode,e.ID as ID_register_formulir,
        if(h.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = f.FormulirCode limit 1) ,""  ) as No_Ref
        from db_admission.register_jadwal_ujian as a
        join db_admission.ujian_perprody_m as b
        on a.ID_ujian_perprody = b.ID
        join db_academic.program_study as c
        on c.ID = b.ID_ProgramStudy
        join db_admission.register_formulir_jadwal_ujian as d
        ON a.ID = d.ID_register_jadwal_ujian
        JOIN db_admission.register_formulir as e
        on e.ID = d.ID_register_formulir
        join db_admission.register_verified as f
        on e.ID_register_verified = f.ID
        join db_admission.register_verification as g
        on g.ID = f.RegVerificationID
        join db_admission.register as h
        on h.ID = g.RegisterID
        join db_admission.school as i
        on i.ID = h.SchoolID
        '.$where.'
        GROUP BY c.Name,DATE(a.DateTimeTest),e.ID '.' LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array())->result_array();
      return $query;

    }

    public function count_daftar_set_nilai_ujian_load_data_paging($ID_ProgramStudy)
    {
      $sql = 'select count(*) as total from (
                select c.Name as prody
                from db_admission.register_jadwal_ujian as a
                RIGHT JOIN db_admission.ujian_perprody_m as b
                on a.ID_ujian_perprody = b.ID
                join db_academic.program_study as c
                on c.ID = b.ID_ProgramStudy
                join db_admission.register_formulir_jadwal_ujian as d
                ON a.ID = d.ID_register_jadwal_ujian
                JOIN db_admission.register_formulir as e
                on e.ID = d.ID_register_formulir
                join db_admission.register_verified as f
                on e.ID_register_verified = f.ID
                join db_admission.register_verification as g
                on g.ID = f.RegVerificationID
                join db_admission.register as h
                on h.ID = g.RegisterID
                where b.ID_ProgramStudy = ? and b.Active = 1 and d.ID not in(select ID_register_formulir_jadwal_ujian from db_admission.register_hasil_ujian)
                GROUP by e.ID
              )aa';
      $query=$this->db->query($sql, array($ID_ProgramStudy))->result_array();
      return $query[0]['total'];
    }

    public function daftar_set_nilai_ujian_load_data_paging($limit, $start,$ID_ProgramStudy)
    {
      $sql = 'select c.Name as prody,a.ID_ujian_perprody,DATE(a.DateTimeTest) as tanggal
              ,CONCAT((EXTRACT(HOUR FROM a.DateTimeTest)),":",(EXTRACT(MINUTE FROM a.DateTimeTest))) as jam,
              a.Lokasi,b.NamaUjian,b.Bobot,
              h.Name as NameCandidate,e.ID as ID_register_formulir,b.ID_ProgramStudy
              from db_admission.register_jadwal_ujian as a
              RIGHT JOIN db_admission.ujian_perprody_m as b
              on a.ID_ujian_perprody = b.ID
              join db_academic.program_study as c
              on c.ID = b.ID_ProgramStudy
              join db_admission.register_formulir_jadwal_ujian as d
              ON a.ID = d.ID_register_jadwal_ujian
              JOIN db_admission.register_formulir as e
              on e.ID = d.ID_register_formulir
              join db_admission.register_verified as f
              on e.ID_register_verified = f.ID
              join db_admission.register_verification as g
              on g.ID = f.RegVerificationID
              join db_admission.register as h
              on h.ID = g.RegisterID
              where b.ID_ProgramStudy = ? and b.Active = 1 and d.ID not in(select ID_register_formulir_jadwal_ujian from db_admission.register_hasil_ujian)
              GROUP by e.ID
              LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array($ID_ProgramStudy))->result_array();
      return $query;
    }

    public function select_mataUjian($ID_ProgramStudy)
    {
      $sql = 'select * from db_admission.ujian_perprody_m where ID_ProgramStudy = ?';
      $query=$this->db->query($sql, array($ID_ProgramStudy))->result_array();
      return $query;
    }

    public function saveDataNilaiUjian($arr)
    {
      for ($i=0; $i < count($arr['processs1']); $i++) {
        $sql = 'select c.ID from db_admission.ujian_perprody_m as a
                join db_admission.register_jadwal_ujian as b
                on a.ID = b.ID_ujian_perprody
                join db_admission.register_formulir_jadwal_ujian as c
                on c.ID_register_jadwal_ujian = b.ID
                where a.ID = ? and c.ID_register_formulir = ?';

        // print_r($arr[$i]->id_mataujian);
        $ID_ujian_perprody = $arr['processs1'][$i]->id_mataujian;
        $ID_register_formulir = $arr['processs1'][$i]->id_formulir;
        $query=$this->db->query($sql, array($ID_ujian_perprody,$ID_register_formulir))->result_array();
          for ($j=0; $j < count($query); $j++) {
              $ID_register_formulir_jadwal_ujian = $query[$j]['ID'];
              $dataSave = array(
                      'ID_register_formulir_jadwal_ujian' => $ID_register_formulir_jadwal_ujian,
                      'Value' => $arr['processs1'][$i]->value,
                      'CreateAT' => date('Y-m-d'),
                      'CreateBY' => $this->session->userdata('NIP'),
              );
              $this->db->insert('db_admission.register_hasil_ujian', $dataSave);
          }
      }

      // print_r($arr['kelulusan']);

      for ($i=0; $i < count($arr['kelulusan']); $i++) {
        $a = $arr['kelulusan'][$i];
        if ($a != '') {
          $arr_temp = explode(";", $a);
           // print_r($arr_temp);
          $Kelulusan = $arr_temp[0];
          $ID_register_formulir = $arr_temp[1];
          $dataSave = array(
                  'ID_register_formulir' => $ID_register_formulir,
                  'Kelulusan' => $Kelulusan,
          );
          $this->db->insert('db_admission.register_kelulusan_ujian', $dataSave);
        }
      }

    }

    public function showData($tabel)
    {
      $sql = "select * from ".$tabel;
      $query=$this->db->query($sql, array());
      return $query->result_array();
    }

    public function alreadyExistingEmail($Email)
    {
      $tt = $this->m_master->showData_array('db_admission.set_ta');
      $yy = $tt[0]['Ta'];
      $sql = "select count(*) as total from db_admission.register as a where a.Email = ? and SetTa = ?";
      $query=$this->db->query($sql, array($Email,$yy))->result_array();
      if ($query[0]['total'] > 0) {
        return false;
      }
      else
      {
        return true;
      }
    }

    public function inserData_formulir_offline_sale_save($input_arr)
    {
      // get no kwitansi terakhir
      $this->load->model('master/m_master');
      // $getDatax = $this->m_master->showData_array('db_admission.set_ta');
      // get year by Code Formulir
      $getDatax = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','FormulirCode',$input_arr['selectFormulirCode']);
      $sql = 'select a.* from db_admission.sale_formulir_offline as a
              left join db_admission.formulir_number_offline_m as b
              on a.FormulirCodeOffline = b.FormulirCode
              where b.Years = ?
              order by a.NoKwitansi desc limit 1';
      $query=$this->db->query($sql, array($getDatax[0]['Years']))->result_array();
      if (count($query) > 0) {
        $NoKwitansi = $query[0]['NoKwitansi'];
        $NoKwitansi = ($NoKwitansi != "") ? (int)$NoKwitansi + 1 : $NoKwitansi;
      }
      else
      {
        $NoKwitansi = 1;
      }

      $FullName = strtolower($input_arr['Name']);
      $dataSave = array(
              'FormulirCodeOffline' => $input_arr['selectFormulirCode'],
              'PIC' => $input_arr['PIC'],
              'ID_ProgramStudy' => $input_arr['selectProgramStudy'],
              'ID_ProgramStudy2' => $input_arr['selectProgramStudy2'],
              'FullName' => ucwords($FullName) ,
              'Gender' => $input_arr['selectGender'],
              'HomeNumber' => $input_arr['telp_rmh'],
              'PhoneNumber' => $input_arr['hp'],
              'Email' => strtolower($input_arr['email']),
              'SchoolID' => $input_arr['autoCompleteSchool'],
              'price_event_ID' => $input_arr['selectEvent'],
              'source_from_event_ID' => $input_arr['selectSourceFrom'],
              'Channel' => ($input_arr['tipeChannel'] == '' ||  empty($input_arr['tipeChannel']) ) ? 0 : $input_arr['tipeChannel'],
              'SchoolIDChanel' => $input_arr['autoCompleteSchoolChanel'],
              'Price_Form' => $input_arr['priceFormulir'],
              'CreateAT' => date('Y-m-d'),
              'DateSale' => $input_arr['tanggal'],
              'CreatedBY' => $this->session->userdata('NIP'),
              'NoKwitansi' => $NoKwitansi,
              'TypePay' => $input_arr['TypePay'],
              'ID_Crm' => $input_arr['ID_Crm'],
              // 'Price_Form' => $Kelulusan,
      );
      $this->db->insert('db_admission.sale_formulir_offline', $dataSave);
      $No_Ref = $input_arr['No_Ref'];

      if ($No_Ref == "") {
        $sql = 'select * from db_admission.formulir_number_offline_m order by No_Ref desc limit 1';
        $query=$this->db->query($sql, array())->result_array();
        if (count($query) == 0) {
          $this->load->model('master/m_master');
          $tt = $this->m_master->showData_array('set_ta');
          $yy = substr($tt[0]['Ta'],2,2);
          $No_Ref = $yy.'0001';
        }
        else
        {
          $No_Ref = $query[0]['No_Ref'] + 1;
        }
      }
      $this->updateSellOUTFormulirOffline($input_arr['selectFormulirCode'],$No_Ref);

        // UPDATE STATUS crm
        $this->db->reset_query();
        $this->db->set('Status', 7);
        $this->db->where('ID', $input_arr['ID_Crm']);
        $this->db->update('db_admission.crm');
        $this->db->reset_query();

      // print_r($input_arr);
    }

    public function editData_formulir_offline_sale_save($input_arr)
    {

        // Get ID CRM to update status
        $dataCRM = $this->db->select('ID_Crm')->get_where('db_admission.sale_formulir_offline',array(
            'ID' => $input_arr['CDID']
        ))->result_array();

        $ID_CRM = (count($dataCRM)>0 && $dataCRM[0]['ID_Crm']!='' && $dataCRM[0]['ID_Crm']!=null)
            ? $dataCRM[0]['ID_Crm']
            : '';

        if($ID_CRM!='' && $input_arr['ID_Crm']!=$ID_CRM){
            $this->db->set('Status', 6);
            $this->db->where('ID', $ID_CRM);
            $this->db->update('db_admission.crm');
            $this->db->reset_query();

            $this->db->set('Status', 7);
            $this->db->where('ID', $input_arr['ID_Crm']);
            $this->db->update('db_admission.crm');
            $this->db->reset_query();
        }



      $FullName = strtolower($input_arr['Name']);
      $dataSave = array(
              'FormulirCodeOffline' => $input_arr['selectFormulirCode'],
              'PIC' => $input_arr['PIC'],
              'ID_ProgramStudy' => $input_arr['selectProgramStudy'],
              'ID_ProgramStudy2' => $input_arr['selectProgramStudy2'],
              'FullName' => ucwords($FullName),
              'Gender' => $input_arr['selectGender'],
              'HomeNumber' => $input_arr['telp_rmh'],
              'PhoneNumber' => $input_arr['hp'],
              'Email' => strtolower($input_arr['email']),
              'SchoolID' => $input_arr['autoCompleteSchool'],
              'price_event_ID' => $input_arr['selectEvent'],
              'source_from_event_ID' => $input_arr['selectSourceFrom'],
              'Channel' => $input_arr['tipeChannel'],
              'SchoolIDChanel' => $input_arr['autoCompleteSchoolChanel'],
              'Price_Form' => $input_arr['priceFormulir'],
              'TypePay' => $input_arr['TypePay'],
              'DateSale' => $input_arr['tanggal'],
              'UpdateAT' => date('Y-m-d'),
              'UpdatedBY' => $this->session->userdata('NIP'),
              'ID_Crm' => $input_arr['ID_Crm'],
      );

      $this->db->where('ID',$input_arr['CDID']);
      $this->db->update('db_admission.sale_formulir_offline', $dataSave);

      $this->updateSellOUTFormulirOffline($input_arr['selectFormulirCode'],$input_arr['No_Ref']);
    }

    public function formulir_offline_salect_PIC($input_arr)
    {
      $this->load->model('m_api');

      $SchoolID = $input_arr['School'];
      if ($SchoolID != '') {
        // cari sales berdasarkan wilayah, jika tidak ada maka tampilkan sales secara keseluruhan
            $sql = 'select b.NIP,b.Name from db_admission.sales_school_m as a
                    join db_employees.employees as b
                    on a.SalesNIP = b.NIP where a.SchoolID = ? ';
            $query=$this->db->query($sql, array($SchoolID))->result_array();
            if (count($query) == 0) {
              $query = $this->m_api->getEmployeesBy('10','13');
            }
            // print_r($query);
      }
      else
      {
        // tampilkan sales secara keseluruhan
        // $query = $this->m_api->getEmployeesBy('10','13');
        $query = $this->m_api->getEmployeesPICAdmissionBy();
      }

      return $query;
    }

    public function formulir_offline_salect_PIC_renew($input_arr)
    {
      $this->load->model('m_api');
      $PositionMain = $this->session->userdata('PositionMain');
      $division = $PositionMain['IDDivision'];

        $queryDiv = "";
      $SchoolID = $input_arr['School'];
      if ($SchoolID != '') {
        // cari sales berdasarkan wilayah, jika tidak ada maka tampilkan sales secara keseluruhan
            $sql = 'select b.NIP,b.Name from db_admission.sales_school_m as a
                    join db_employees.employees as b
                    on a.SalesNIP = b.NIP where a.SchoolID = ? and b.StatusEmployeeID not in (-1,-2) ';
            $query=$this->db->query($sql, array($SchoolID))->result_array();
            if (count($query) == 0) {
              // $query = $this->m_api->getEmployeesBy('10','13');
              $sql = 'select NIP,Name from db_employees.employees where LEFT(PositionMain ,INSTR(PositionMain ,".")-1) = 10 and StatusEmployeeID not in (-1,-2)
                      union
                      select NIP,Name from db_employees.employees where LEFT(PositionMain ,INSTR(PositionMain ,".")-1) = 18  and StatusEmployeeID not in (-1,-2)
                    ';
              $query=$this->db->query($sql, array())->result_array();
            }
            // print_r($query);
      }
      else
      {
        $sql = 'select NIP,Name from db_employees.employees where LEFT(PositionMain ,INSTR(PositionMain ,".")-1) = 10 and StatusEmployeeID not in (-1,-2)
                union
                select NIP,Name from db_employees.employees where LEFT(PositionMain ,INSTR(PositionMain ,".")-1) = 18  and StatusEmployeeID not in (-1,-2)
              ';
        // tampilkan sales secara keseluruhan
        // $query = $this->m_api->getEmployeesBy('10','13');
        // $query = $this->m_api->getEmployeesPICAdmissionBy();
        // check session division
        $PositionMain = $this->session->userdata('PositionMain');
        $division = $PositionMain['IDDivision'];
        $queryDiv = "";
          switch ($division) {
            case 10:
              $sql = 'select NIP,Name from db_employees.employees where LEFT(PositionMain ,INSTR(PositionMain ,".")-1) = 10 and StatusEmployeeID not in (-1,-2)';
              break;
            case 18:
              $sql = 'select NIP,Name from db_employees.employees where LEFT(PositionMain ,INSTR(PositionMain ,".")-1) = 18 and StatusEmployeeID not in (-1,-2)';
              break;
            default:
              break;
          }

          $query=$this->db->query($sql, array())->result_array();
      }

      return $query;
    }

    public function updateSellOUTFormulirOffline($FormulirCodeOffline,$No_Ref = "")
    {
      $dataSave = array(
              'StatusJual' => 1,
              'No_Ref' => $No_Ref,
                      );
      $this->db->where('FormulirCode',$FormulirCodeOffline);
      $this->db->update('db_admission.formulir_number_offline_m', $dataSave);
    }

    public function count_loadData_calon_mahasiswa($Nama,$selectProgramStudy,$Sekolah,$No_Formulir)
    {
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

      if($No_Formulir == '') {
          $No_Formulir = '';
      }
      else
      {
        // Checking Formulir Code
          $Q = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','No_Ref',$No_Formulir);
          if (count($Q) == 0) {
            $Q = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','FormulirCode',$No_Formulir);
            if (count($Q) == 0) {
              $No_Formulir = $Q[0]['FormulirCode'];
            }
            else
            {
              $No_Formulir = $No_Formulir;
            }
          }
          else
          {
            $No_Formulir = $Q[0]['FormulirCode'];
          }

        $No_Formulir = ' and b.FormulirCode = "'.$No_Formulir.'"';
      }

        $sql = 'select count(*) as total from (
              select a.ID as ID_register_formulir
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
              left JOIN db_admission.register_jtinggal_m as g
              ON a.ID_register_jtinggal_m = g.ID
              left JOIN db_admission.country as h
              ON a.ID_country_address = h.ctr_code
              left JOIN db_admission.province as i
              ON a.ID_province = i.ProvinceID
              left JOIN db_admission.region as j
              ON a.ID_region = j.RegionID
              left JOIN db_admission.district as k
              ON a.ID_districts = k.DistrictID
              left JOIN db_admission.school_type as l
              ON l.sct_code = a.ID_school_type
              left JOIN db_admission.register_major_school as m
              ON m.ID = a.ID_register_major_school
              left JOIN db_admission.school as n
              ON n.ID = d.SchoolID
              left join db_academic.program_study as o
              on o.ID = a.ID_program_study
              where d.Name like '.$Nama.' and d.SchoolID like '.$Sekolah.' and a.ID_program_study like '.$selectProgramStudy.' and a.ID not in (select ID_register_formulir from db_admission.register_butuh_ujian) and  a.ID not in (select ID_register_formulir from db_admission.register_nilai) '.$No_Formulir.'
            ) aa';
           $query=$this->db->query($sql, array())->result_array();
           return $query[0]['total'];
    }

    public function loadData_calon_mahasiswa($limit, $start,$Nama,$selectProgramStudy,$Sekolah,$No_Formulir)
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

      if($No_Formulir == '') {
          $No_Formulir = '';
      }
      else
      {
        // Checking Formulir Code
          $Q = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','No_Ref',$No_Formulir);
          if (count($Q) == 0) {
            $Q = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','FormulirCode',$No_Formulir);
            if (count($Q) == 0) {
              $No_Formulir = $Q[0]['FormulirCode'];
            }
            else
            {
              $No_Formulir = $No_Formulir;
            }
          }
          else
          {
            $No_Formulir = $Q[0]['FormulirCode'];
          }

        $No_Formulir = ' and b.FormulirCode = "'.$No_Formulir.'"';
      }

        $sql = 'select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,g.JenisTempatTinggal,
            h.ctr_name as CountryAddress,i.ProvinceName as ProvinceAddress,j.RegionName as RegionAddress,k.DistrictName as DistrictsAddress,
            a.District as DistrictAddress,a.Address,a.ZipCode,a.PhoneNumber,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
            n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
             b.FormulirCode,  if(d.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = b.FormulirCode limit 1) ,""  ) as No_Ref
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
            left JOIN db_admission.register_jtinggal_m as g
            ON a.ID_register_jtinggal_m = g.ID
            left JOIN db_admission.country as h
            ON a.ID_country_address = h.ctr_code
            left JOIN db_admission.province as i
            ON a.ID_province = i.ProvinceID
            left JOIN db_admission.region as j
            ON a.ID_region = j.RegionID
            left JOIN db_admission.district as k
            ON a.ID_districts = k.DistrictID
            left JOIN db_admission.school_type as l
            ON l.sct_code = a.ID_school_type
            left JOIN db_admission.register_major_school as m
            ON m.ID = a.ID_register_major_school
            left JOIN db_admission.school as n
            ON n.ID = d.SchoolID
            left join db_academic.program_study as o
            on o.ID = a.ID_program_study
            where d.Name like '.$Nama.' and d.SchoolID like '.$Sekolah.' and a.ID_program_study like '.$selectProgramStudy.' and a.ID not in (select ID_register_formulir from db_admission.register_butuh_ujian) and  a.ID not in (select ID_register_formulir from db_admission.register_nilai) '.$No_Formulir.' LIMIT '.$start. ', '.$limit;
           $query=$this->db->query($sql, array())->result_array();
           return $query;
    }

    public function submit_ikut_ujian($input)
    {
      for ($i=0; $i < count($input); $i++) {
        $dataSave = array(
                'ID_register_formulir' => $input[$i],
        );
        $this->db->insert('db_admission.register_butuh_ujian', $dataSave);
      }
    }

    public function count_daftar_set_nilai_rapor_load_data_paging($ID_ProgramStudy,$FormulirCode)
    {
      if ($FormulirCode == "") {
        $addQ = 'where ID_program_study ="'.$ID_ProgramStudy.'"';
      }
      else
      {
        $addQ = 'where ( FormulirCode = "'.$FormulirCode.'" or No_Ref = "'.$FormulirCode.'")';
      }
      $sql = 'select count(*) as total from (
                  select * from
                  (
                    select a.Name as NameCandidate,a.Email, d.ID as ID_register_formulir, e.bobot as Bobot, e.NamaUjian,f.SchoolName,f.CityName,
                    c.FormulirCode,  if(a.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,""  ) as No_Ref,d.ID_program_study
                    from db_admission.register as a
                    join db_admission.register_verification as b
                    on a.ID = b.RegisterID
                    join db_admission.register_verified as c
                    on b.ID = c.RegVerificationID
                    join db_admission.register_formulir as d
                    on c.ID = d.ID_register_verified
                    join db_admission.ujian_perprody_m as e
                    on e.ID_ProgramStudy = d.ID_program_study
                    join db_admission.school as f
                    on a.SchoolID = f.ID
                    where e.Active = 1 and d.ID not in(select ID_register_formulir from db_admission.register_nilai)
                    and  d.ID not in (select ID_register_formulir from db_admission.register_butuh_ujian)
                    GROUP by d.ID
                  ) bb
                  '.$addQ.'
              ) aa';
      $query=$this->db->query($sql, array())->result_array();
      return $query[0]['total'];
    }

    public function daftar_set_nilai_rapor_load_data_paging($limit, $start,$ID_ProgramStudy,$FormulirCode)
    {
      $arr_temp = array();
      if ($FormulirCode == "") {
        $addQ = 'where ID_program_study ="'.$ID_ProgramStudy.'"';
      }
      else
      {
        $addQ = 'where ( FormulirCode like "'.$FormulirCode.'" or No_Ref = "'.$FormulirCode.'")';
      }
      $sql = 'select * from
              (
              select a.Name as NameCandidate,a.Email, d.ID as ID_register_formulir, e.bobot as Bobot, e.NamaUjian,f.SchoolName,f.CityName,
              c.FormulirCode,  if(a.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,""  ) as No_Ref,d.ID_program_study,g.Name as NamePrody
              from db_admission.register as a
              join db_admission.register_verification as b
              on a.ID = b.RegisterID
              join db_admission.register_verified as c
              on b.ID = c.RegVerificationID
              join db_admission.register_formulir as d
              on c.ID = d.ID_register_verified
              join db_admission.ujian_perprody_m as e
              on e.ID_ProgramStudy = d.ID_program_study
              join db_admission.school as f
              on a.SchoolID = f.ID
              join db_academic.program_study as g
              on e.ID_ProgramStudy = g.ID
              where e.Active = 1 and d.ID not in(select ID_register_formulir from db_admission.register_nilai)
              and  d.ID not in (select ID_register_formulir from db_admission.register_butuh_ujian)
              GROUP by d.ID
             )aa '.$addQ.'
              LIMIT '.$start. ', '.$limit;
      // print_r($sql);die();
      $query=$this->db->query($sql, array())->result_array();
      if (count($query) > 0) {
        $arr_temp = array('query' => $query,'Prodi' => ($FormulirCode == "") ? $ID_ProgramStudy : $query[0]['ID_program_study']  );
      }
      else
      {
        $arr_temp = array('query' => $query,'Prodi' => ($FormulirCode == "") ? $ID_ProgramStudy : ''  );
      }

      return $arr_temp;
    }

    public function saveDataNilaRapor($arr)
    {
      for ($i=0; $i < count($arr['processs1']); $i++) {
        $ID_ujian_perprody = $arr['processs1'][$i]->id_mataujian;
        $ID_register_formulir = $arr['processs1'][$i]->id_formulir;

        $bool = true;
        for ($j=0; $j < count($arr['rangking']); $j++) {
            $id_doc = $arr['rangking'][$j]->id_doc;
            if ($ID_register_formulir == $arr['rangking'][$j]->id_formulir) {
              if ($id_doc == '' || $id_doc == null ) {
                $bool = false;
                break;
              }
            }

        }

        if ($bool) {
            $dataSave = array(
                    'ID_ujian_perprody' => $ID_ujian_perprody,
                    'ID_register_formulir' => $ID_register_formulir,
                    'Value' => $arr['processs1'][$i]->value,
                    'Status' => 'Verified', // NOT VERIFY FROM KA PRODI
                    'CreateAT' => date('Y-m-d'),
                    'CreateBY' => $this->session->userdata('NIP'),
            );
            $this->db->insert('db_admission.register_nilai', $dataSave);
        }

      }

    }

    public function saveDataRangkingRapor($arr)
    {
      for ($j=0; $j < count($arr['rangking']); $j++) {
          $ID_register_formulir = $arr['rangking'][$j]->id_formulir;
          $Rangking = $arr['rangking'][$j]->rangking;
          $id_doc = $arr['rangking'][$j]->id_doc;
          if ($id_doc != '' || $id_doc != null) {
            $dataSave = array(
                    'ID_register_formulir' => $ID_register_formulir,
                    'Rangking' => $Rangking,
                    'FileRapor' => $id_doc,
            );
            $this->db->insert('db_admission.register_rangking', $dataSave);
          }
      }
    }

    public function saveDataRaporToFin($arr)
    {
      $arr = (array)json_decode(json_encode($arr),true);
      for ($i=0; $i < count($arr); $i++) {
        $dt = $arr[$i];
        // print_r($dt);
        $dt['CreateAT'] = date('Y-m-d');
        $dt['CreateBY'] = $this->session->userdata('NIP');
        $this->db->insert('db_admission.register_nilai_fin', $dt);
      }
    }

    public function count_loadData_calon_mahasiswa_created($Nama,$selectProgramStudy,$Sekolah,$FormulirCode)
    {
      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

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

        $sql = 'select count(*) as total from (
                  select a.ID as ID_register_formulir
                from db_admission.register_formulir as a
                Left JOIN db_admission.register_verified as b
                ON a.ID_register_verified = b.ID
                Left JOIN db_admission.register_verification as c
                ON b.RegVerificationID = c.ID
                Left JOIN db_admission.register as d
                ON c.RegisterID = d.ID
                Left JOIN db_admission.country as e
                ON a.NationalityID = e.ctr_code
                Left JOIN db_employees.religion as f
                ON a.ReligionID = f.IDReligion
                Left JOIN db_admission.register_jtinggal_m as g
                ON a.ID_register_jtinggal_m = g.ID
                Left JOIN db_admission.country as h
                ON a.ID_country_address = h.ctr_code
                Left JOIN db_admission.province as i
                ON a.ID_province = i.ProvinceID
                Left JOIN db_admission.region as j
                ON a.ID_region = j.RegionID
                Left JOIN db_admission.district as k
                ON a.ID_districts = k.DistrictID
                Left JOIN db_admission.school_type as l
                ON l.sct_code = a.ID_school_type
                Left JOIN db_admission.register_major_school as m
                ON m.ID = a.ID_register_major_school
                Left JOIN db_admission.school as n
                ON n.ID = d.SchoolID
                Left join db_academic.program_study as o
                on o.ID = a.ID_program_study
                left join db_admission.formulir_number_offline_m as pq
                on b.FormulirCode = pq.FormulirCode
                where d.Name like '.$Nama.' and d.SchoolID like '.$Sekolah.' and a.ID_program_study like '.$selectProgramStudy.' and a.ID in (select ID_register_formulir from db_admission.register_nilai where Status = "Verified")

                  and ( b.FormulirCode like '.$FormulirCode.' or pq.No_Ref like '.$FormulirCode.' )
              )aa';
           $query=$this->db->query($sql, array())->result_array();
           return $query[0]['total'];
    }

    public function loadData_calon_mahasiswa_created($limit, $start,$Nama,$selectProgramStudy,$Sekolah,$FormulirCode)
    {
      $arr_temp = array('data' => array());
      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

      if($Nama != '%') {
          $Nama = '"%'.$Nama.'%"';
      }
      else
      {
        $Nama = '"%"';
      }

      if($selectProgramStudy != '%') {
        // $selectProgramStudy = '"%'.$selectProgramStudy.'%"';
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

        $sql = ' select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,g.JenisTempatTinggal,
            h.ctr_name as CountryAddress,i.ProvinceName as ProvinceAddress,j.RegionName as RegionAddress,k.DistrictName as DistrictsAddress,
            a.District as DistrictAddress,a.Address,a.ZipCode,a.PhoneNumber,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
            n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
            b.FormulirCode,  if(d.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = b.FormulirCode limit 1) ,""  ) as No_Ref
            from db_admission.register_formulir as a
            Left JOIN db_admission.register_verified as b
            ON a.ID_register_verified = b.ID
            Left JOIN db_admission.register_verification as c
            ON b.RegVerificationID = c.ID
            Left JOIN db_admission.register as d
            ON c.RegisterID = d.ID
            Left JOIN db_admission.country as e
            ON a.NationalityID = e.ctr_code
            Left JOIN db_employees.religion as f
            ON a.ReligionID = f.IDReligion
            Left JOIN db_admission.register_jtinggal_m as g
            ON a.ID_register_jtinggal_m = g.ID
            Left JOIN db_admission.country as h
            ON a.ID_country_address = h.ctr_code
            Left JOIN db_admission.province as i
            ON a.ID_province = i.ProvinceID
            Left JOIN db_admission.region as j
            ON a.ID_region = j.RegionID
            Left JOIN db_admission.district as k
            ON a.ID_districts = k.DistrictID
            Left JOIN db_admission.school_type as l
            ON l.sct_code = a.ID_school_type
            Left JOIN db_admission.register_major_school as m
            ON m.ID = a.ID_register_major_school
            Left JOIN db_admission.school as n
            ON n.ID = d.SchoolID
            Left join db_academic.program_study as o
            on o.ID = a.ID_program_study
            left join db_admission.formulir_number_offline_m as pq
            on b.FormulirCode = pq.FormulirCode
            where d.Name like '.$Nama.' and d.SchoolID like '.$Sekolah.' and a.ID_program_study like '.$selectProgramStudy.' and a.ID in (select ID_register_formulir from db_admission.register_nilai where Status = "Verified")

              and ( b.FormulirCode like '.$FormulirCode.' or pq.No_Ref like '.$FormulirCode.' )
            LIMIT '.$start. ', '.$limit;

           $query=$this->db->query($sql, array())->result_array();
           for ($i=0; $i < count($query); $i++) {
             $dt = $this->m_master->caribasedprimary('db_finance.register_admisi','ID_register_formulir',$query[$i]['ID_register_formulir']);
             if (count($dt) > 0) {
               $query[$i]['fin'] = 0;
             }
             else
             {
               $query[$i]['fin'] = 1;
             }

             // get data nilai to finance
             $dt = $this->m_master->caribasedprimary('db_admission.register_nilai_fin','ID_register_formulir',$query[$i]['ID_register_formulir']);
             $query[$i]['Nilaifin'] = $dt;
           }
           return $query;
    }

    public function getValuePerid_ujian_register($ID_ujian_perprody,$ID_register_formulir)
    {
      $sql = "select * from db_admission.register_nilai where ID_ujian_perprody = ? and ID_register_formulir = ?";
      $query=$this->db->query($sql, array($ID_ujian_perprody,$ID_register_formulir))->result_array();
      return $query;
    }

     public function submit_cancel_nilai_rapor($input)
     {
      for ($i=0; $i < count($input); $i++) {
        $sql = "delete from db_admission.register_nilai where ID_register_formulir = ".$input[$i];
        $query=$this->db->query($sql, array());
      }
     }

     public function submit_cancel_nilai_rapor_finance($input)
     {
      for ($i=0; $i < count($input); $i++) {
        $sql = "delete from db_admission.register_nilai_fin where ID_register_formulir = ".$input[$i];
        $query=$this->db->query($sql, array());
      }
     }

     public function getDataDokumentRegister($ID_register_formulir)
     {
         $sql = "select a.ID,a.ID_register_formulir,a.ID_reg_doc_checklist,a.Status,a.Attachment,b.DocumentChecklist,
                 b.Required from db_admission.register_document as a
                 join db_admission.reg_doc_checklist as b
                 on b.ID = a.ID_reg_doc_checklist
                 where a.Status = 'Done' and a.ID_register_formulir = ?
             ";
         $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
         return $query;
     }

     public function getDocumentAdmisiMHS($NPM)
     {
         $sql = "select a.ID,a.NPM,a.ID_reg_doc_checklist,a.Status,a.Attachment,b.DocumentChecklist,
                 b.Required from db_admission.doc_mhs as a
                 join db_admission.reg_doc_checklist as b
                 on b.ID = a.ID_reg_doc_checklist
                 where a.Status = 'Done' and a.NPM = ?
             ";
         $query=$this->db->query($sql, array($NPM))->result_array();
         return $query;
     }

     public function getRangking($ID_register_formulir)
     {
      $sql= "select a.*,b.Attachment from db_admission.register_rangking as a left join db_admission.register_document as b
             on a.FileRapor = b.ID where a.ID_register_formulir = ?
            ";
      $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
      return $query;
     }

     public function submit_cancel_nilai_rapor_rangking($input)
     {
      for ($i=0; $i < count($input); $i++) {
        $sql = "delete from db_admission.register_rangking where ID_register_formulir = ".$input[$i];
        $query=$this->db->query($sql, array());
      }
     }


     private function excecuteSchemaTuitionFee($ID_program_study,$getBintangMaster,$CreditDefaultSmt1)
     {
      $arr = [];
      for ($i=0; $i < count($getBintangMaster); $i++) { 
        $JumlahBintang = $getBintangMaster[$i]['ID_bintang'];
        $Name = $getBintangMaster[$i]['Name'];
        $Desc = $getBintangMaster[$i]['Desc'];
        $getPayment = $this->getPaymentType_Cost($ID_program_study,$JumlahBintang);
        $detail = [];
        if (count($getPayment) > 0) {
          for ($j=0; $j < count($getPayment); $j++) { 
            if ($getPayment[$j]['Abbreviation'] == 'Credit') {
               $detail = $detail + array($getPayment[$j]['Abbreviation'] => (int)$getPayment[$j]['Cost'] * (int) $CreditDefaultSmt1.'.00');
             }
             else
             {
              $detail = $detail + array($getPayment[$j]['Abbreviation'] => $getPayment[$j]['Cost']);
             }
          }
        }
        else
        {
          $PaymentType = $this->m_master->caribasedprimary('db_finance.payment_type','Type','0');
          for ($j=0; $j < count($PaymentType); $j++) { 
            $detail = $detail + array($PaymentType[$j]['Abbreviation'] => '0.00');
          }
        }

        $arr[] = [
          'JumlahBintang' => $JumlahBintang,
          'Name' => $Name,
          'Desc' => $Desc,
          'TuitionFee' => $detail,
        ];

      }

      return $arr;
     }

     public function getDataCalonMhsTuitionFee($limit, $start,$FormulirCode)
     {
      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

      $arr_temp = array();
      $sql= 'select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,
              f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,g.JenisTempatTinggal,
              h.ctr_name as CountryAddress,i.ProvinceName as ProvinceAddress,j.RegionName as RegionAddress,k.DistrictName as DistrictsAddress,
              a.District as DistrictAddress,a.Address,a.ZipCode,a.PhoneNumber,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
              n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
              if((select count(*) as total from db_admission.register_nilai where Status = "Verified" and ID_register_formulir = a.ID limit 1) > 0,"Rapor","Ujian")
              as status1,b.FormulirCode,
              if(d.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = b.FormulirCode limit 1) ,""  ) as No_Ref
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
              left JOIN db_admission.register_jtinggal_m as g
              ON a.ID_register_jtinggal_m = g.ID
              left JOIN db_admission.country as h
              ON a.ID_country_address = h.ctr_code
              left JOIN db_admission.province as i
              ON a.ID_province = i.ProvinceID
              left JOIN db_admission.region as j
              ON a.ID_region = j.RegionID
              left JOIN db_admission.district as k
              ON a.ID_districts = k.DistrictID
              left JOIN db_admission.school_type as l
              ON l.sct_code = a.ID_school_type
              left JOIN db_admission.register_major_school as m
              ON m.ID = a.ID_register_major_school
              left JOIN db_admission.school as n
              ON n.ID = d.SchoolID
              left join db_academic.program_study as o
              on o.ID = a.ID_program_study
              left join db_admission.formulir_number_offline_m as px
              on b.FormulirCode = px.FormulirCode
              where ( a.ID in (select ID_register_formulir from db_admission.register_nilai where Status = "Verified")
              or a.ID in (select ID_register_formulir from db_admission.register_kelulusan_ujian where Kelulusan = "Lulus") ) and a.ID not in (select ID_register_formulir from db_finance.register_admisi)
              and ( b.FormulirCode like '.$FormulirCode.' or px.No_Ref like '.$FormulirCode.' )
              order by a.ID desc
              LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array())->result_array();

      $this->load->model('master/m_master');
      $jpa = $this->m_master->showData_array('db_admission.register_dsn_jpa');
      $getDiscount = $this->m_master->showData_array('db_finance.discount');
      $getBeasiswa = $this->m_master->showData_array('db_admission.register_dsn_type_m');
      $getMaxCicilan = $this->m_master->showData_array('db_admission.cfg_cicilan');

      // get Bintang
       $getBintangMaster  =  $this->m_master->showData_array('db_finance.tuition_fee_schema');

      for ($i=0; $i < count($query); $i++) {

        // get SKS
        $ID_program_study = $query[$i]['ID_program_study'];
        $ccc = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ID_program_study);
        $Credit = $ccc[0]['DefaultCredit'];

        $DiskonSPP = 0;
        // get Price
            $getPaymentType_Cost = $this->getPaymentType_Cost($query[$i]['ID_program_study']);
            $arr_temp2 = array();
            for ($k=0; $k < count($getPaymentType_Cost); $k++) {
              if ($getPaymentType_Cost[$k]['Abbreviation'] == 'Credit') {
                 $arr_temp2 = $arr_temp2 + array($getPaymentType_Cost[$k]['Abbreviation'] => (int)$getPaymentType_Cost[$k]['Cost'] * (int) $Credit.'.00');
               }
               else
               {
                $arr_temp2 = $arr_temp2 + array($getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Cost']);
               }

            }

          $Attachment = '';
          // get All Files Uploaded
             $Document = $this->getDataDokumentRegister($query[$i]['ID_register_formulir']);
             // get revision terakhir jika ada
               $NoteRev = '';
               $dataGet = $this->m_master->caribasedprimary('db_finance.register_admisi_rev','ID_register_formulir',$query[$i]['ID_register_formulir']);
               $count = count($dataGet);
               $arr_Count = $count - 1;
               if (count($dataGet) != 0) {
                $NoteRev = $dataGet[$arr_Count]['Note'];
               }

        // get schema payment
          $arrSchema = $this->excecuteSchemaTuitionFee($query[$i]['ID_program_study'],$getBintangMaster,$Credit);

        if ($query[$i]['status1'] == 'Rapor') {
          // check rangking
            $getRangking = $this->getRangking($query[$i]['ID_register_formulir']);
            // $Attachment = $getRangking[0]['Attachment'];
            $Attachment = (count($getRangking) == 0) ? 'Empty' : $getRangking[0]['Attachment'];
            $getRangking = $getRangking[0]['Rangking'];

          // get Discount
            for ($j=0; $j < count($jpa); $j++) {
              if ($getRangking >= $jpa[$j]['StartRange'] && $getRangking <= $jpa[$j]['EndRange'] ) {
                $DiskonSPP = $jpa[$j]['DiskonSPP'];
                break;
              }
            }

            $arr_temp[$i] = array(
              'ID_register_formulir' => $query[$i]['ID_register_formulir'],
              'Name' => $query[$i]['Name'],
              'NamePrody' => $query[$i]['NamePrody'],
              'SchoolName' => $query[$i]['SchoolName'],
              'Status1' => $query[$i]['status1'],
              'DiskonSPP' => $DiskonSPP.'.0',
              'RangkingRapor' => $getRangking,
              'getDiscount' =>$getDiscount,
              'FormulirCode' => $query[$i]['FormulirCode'],
              'No_Ref' => $query[$i]['No_Ref'],
              'Document' => $Document,
              'Attachment' => $Attachment,
              'getBeasiswa' => $getBeasiswa,
              'Email' => $query[$i]['Email'],
              'getMaxCicilan' => $getMaxCicilan,
              'NoteRev' => $NoteRev,
              'SchemaPayment' => $arrSchema,
            );
        }
        else
        {
            $arr_temp[$i] = array(
              'ID_register_formulir' => $query[$i]['ID_register_formulir'],
              'Name' => $query[$i]['Name'],
              'NamePrody' => $query[$i]['NamePrody'],
              'SchoolName' => $query[$i]['SchoolName'],
              'Status1' => $query[$i]['status1'],
              'DiskonSPP' => $DiskonSPP.'.0',
              'RangkingRapor' => 0,
              'getDiscount' =>$getDiscount,
              'FormulirCode' => $query[$i]['FormulirCode'],
              'No_Ref' => $query[$i]['No_Ref'],
              'Document' => $Document,
              'Attachment' => $Attachment,
              'getBeasiswa' => $getBeasiswa,
              'Email' => $query[$i]['Email'],
              'getMaxCicilan' => $getMaxCicilan,
              'NoteRev' => $NoteRev,
              'SchemaPayment' => $arrSchema,
            );
        }

        $arr_temp[$i] = $arr_temp[$i] + $arr_temp2;
      }
      return $arr_temp;

     }

     public function getPaymentType_Cost($ID_program_study,$selectedBintang = NULL) // get master tagihan
     {
      $this->load->model('master/m_master');
      // getTA
      $Q_ta = $this->m_master->showData_array('db_admission.set_ta');
      $year = $Q_ta[0]['Ta'];

      $whereBintang = '';
      if ($selectedBintang !== NULL) {
        $whereBintang  = ' and a.Pay_Cond = '.$selectedBintang;
      }

      $sql = 'select a.PTID,a.ProdiID,a.ClassOf,a.Cost,b.Description,b.Abbreviation from db_finance.tuition_fee as a join db_finance.payment_type as b
              on a.PTID = b.ID where a.ProdiID = "'.$ID_program_study.'" and a.ClassOf = '.$year.' '.$whereBintang.'
              order by b.ID asc';
      $query=$this->db->query($sql, array())->result_array();
      return $query;
     }

     public function getPaymentType_Cost_created($ID_register_formulir) // get tagihan yg telah dibuat
     {
      $sql = 'select a.*,b.Description,b.Abbreviation,c.ID as ID_payment_admisi,c.Pay_tuition_fee,
      c.Discount from db_finance.payment_admisi as c join db_finance.payment_type as b on c.PTID = b.ID join db_finance.register_admisi as a on c.ID_register_formulir = a.ID_register_formulir where a.ID_register_formulir = ?';
      $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
      return $query;
     }

     public function set_tuition_fee_save($input)
     {
      for ($i=0; $i < count($input); $i++) {
        // echo $input[$i]->PTID;
        $PTID = $input[$i]->PTID;
        $ID_register_formulir = $input[$i]->ID_register_formulir;
        $Discount = $input[$i]->Discount;
        $Pay_tuition_fee = $input[$i]->Pay_tuition_fee;
        $this->load->model('master/m_master');
        $Pay_tuition_fee = $this->m_master->ClearPricetoDB($Pay_tuition_fee);
        $dataSave = array(
                'PTID' => $PTID,
                'ID_register_formulir' => $ID_register_formulir,
                'Discount' => $Discount,
                'Pay_tuition_fee' => $Pay_tuition_fee,
                'CreateAT' => date('Y-m-d'),
                'CreateBY' => $this->session->userdata('NIP'),
                'Status' => 'Created',
        );
        $this->db->insert('db_finance.payment_register', $dataSave);
      }
     }

     public function count_getDataCalonMhsTuitionFee($FormulirCode)
     {

      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

      $sql= 'select count(*) as total
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
              left JOIN db_admission.register_jtinggal_m as g
              ON a.ID_register_jtinggal_m = g.ID
              left JOIN db_admission.country as h
              ON a.ID_country_address = h.ctr_code
              left JOIN db_admission.province as i
              ON a.ID_province = i.ProvinceID
              left JOIN db_admission.region as j
              ON a.ID_region = j.RegionID
              left JOIN db_admission.district as k
              ON a.ID_districts = k.DistrictID
              left JOIN db_admission.school_type as l
              ON l.sct_code = a.ID_school_type
              left JOIN db_admission.register_major_school as m
              ON m.ID = a.ID_register_major_school
              left JOIN db_admission.school as n
              ON n.ID = d.SchoolID
              left join db_academic.program_study as o
              on o.ID = a.ID_program_study
              left join db_admission.formulir_number_offline_m as px
              on b.FormulirCode = px.FormulirCode
              where ( a.ID in (select ID_register_formulir from db_admission.register_nilai where Status = "Verified")
              or a.ID in (select ID_register_formulir from db_admission.register_kelulusan_ujian where Kelulusan = "Lulus") ) and a.ID not in (select ID_register_formulir from db_finance.register_admisi) and ( b.FormulirCode like '.$FormulirCode.' or px.No_Ref like '.$FormulirCode.' )';
      $query=$this->db->query($sql, array())->result_array();
      return $query[0]['total'];
     }

     // public function count_getDataCalonMhsTuitionFee_delete($FormulirCode,$Status = 'p.Status = "Created" or p.Status = "Approved"')
     public function count_getDataCalonMhsTuitionFee_delete($FormulirCode,$Status = 'p.Status = "Created"')
     {

      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

      $sql= 'select count(*) as total from
              (
                 select a.ID
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
                 where ('.$Status.')
                 and ( b.FormulirCode like '.$FormulirCode.' or px.No_Ref like '.$FormulirCode.' )
                 and b.FormulirCode not in (select FormulirCode from db_admission.to_be_mhs)
                 group by a.ID
              ) aa
              ';
      $query=$this->db->query($sql, array())->result_array();
      if (count($query) > 0) {
        return $query[0]['total'];
      }
      else
      {
        return 0;
      }

     }

     // public function getDataCalonMhsTuitionFee_delete($limit, $start,$FormulirCode,$Status = 'p.Status = "Created" or p.Status = "Approved"')
     public function getDataCalonMhsTuitionFee_delete($limit, $start,$FormulirCode,$Status = 'p.Status = "Created"')
     {

      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

      $arr_temp = array();
      $sql= 'select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,
              f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
              n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
              if((select count(*) as total from db_admission.register_nilai where Status = "Verified" and ID_register_formulir = a.ID limit 1) > 0,"Rapor","Ujian")
              as status1,p.CreateAT,p.CreateBY,b.FormulirCode,p.TypeBeasiswa,p.FileBeasiswa,p.Desc,
              if(d.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = b.FormulirCode limit 1) ,""  ) as No_Ref
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
              where ('.$Status.')
              and ( b.FormulirCode like '.$FormulirCode.' or px.No_Ref like '.$FormulirCode.' )
              and b.FormulirCode not in (select FormulirCode from db_admission.to_be_mhs)
              group by a.ID
              order by p.ID desc
              LIMIT '.$start. ', '.$limit;
      $query=$this->db->query($sql, array())->result_array();
      $this->load->model('master/m_master');
      $jpa = $this->m_master->showData_array('db_admission.register_dsn_jpa');
      for ($i=0; $i < count($query); $i++) {
        $DiskonSPP = 0;
        // get Price
            $getPaymentType_Cost = $this->getPaymentType_Cost_created($query[$i]['ID_register_formulir']);
            $arr_temp2 = array();
            for ($k=0; $k < count($getPaymentType_Cost); $k++) {
              // $arr_temp2 = $arr_temp2 + array($getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Cost']);
              $arr_temp2 = $arr_temp2 + array(
                $getPaymentType_Cost[$k]['Abbreviation'] => 'Rp. '.number_format($getPaymentType_Cost[$k]['Pay_tuition_fee'],2,',','.'),
                'Discount-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Discount']
              );
            }

        // get file dan type beasiswa
           $getBeasiswa = $this->m_master->caribasedprimary('db_admission.register_dsn_type_m','ID',$query[$i]['TypeBeasiswa']);
           if (count($getBeasiswa) > 0) {
             $getBeasiswa = $getBeasiswa[0]['DiscountType'];
           }
           else
           {
            $getBeasiswa = '-';
           }

        // get File
          $getFile = $this->m_master->caribasedprimary('db_admission.register_document','ID',$query[$i]['FileBeasiswa']);
          if (count($getFile) > 0) {
            $getFile = $getFile[0]['Attachment'];
          }
          else
          {
           $getFile = '-';
          }

        if ($query[$i]['status1'] == 'Rapor') {
          // check rangking
            $getRangking = $this->getRangking($query[$i]['ID_register_formulir']);
            $getRangking = $getRangking[0]['Rangking'];

            $arr_temp[$i] = array(
              'ID_register_formulir' => $query[$i]['ID_register_formulir'],
              'Name' => $query[$i]['Name'],
              'NamePrody' => $query[$i]['NamePrody'],
              'SchoolName' => $query[$i]['SchoolName'],
              'Status1' => $query[$i]['status1'],
              // 'DiskonSPP' => $DiskonSPP,
              'RangkingRapor' => $getRangking,
              'FormulirCode' => $query[$i]['FormulirCode'],
              'No_Ref' => $query[$i]['No_Ref'],
              'getBeasiswa' => $getBeasiswa,
              'getFile' => $getFile,
              'Email' => $query[$i]['Email'],
              'Desc' => $query[$i]['Desc'],
              'CreateAT' => $this->m_master->getIndoBulan($query[$i]['CreateAT']),
            );
        }
        else
        {
            $arr_temp[$i] = array(
              'ID_register_formulir' => $query[$i]['ID_register_formulir'],
              'Name' => $query[$i]['Name'],
              'NamePrody' => $query[$i]['NamePrody'],
              'SchoolName' => $query[$i]['SchoolName'],
              'Status1' => $query[$i]['status1'],
              // 'DiskonSPP' => $DiskonSPP,
              'RangkingRapor' => 0,
              'FormulirCode' => $query[$i]['FormulirCode'],
              'No_Ref' => $query[$i]['No_Ref'],
              'getBeasiswa' => $getBeasiswa,
              'getFile' => $getFile,
              'Email' => $query[$i]['Email'],
              'Desc' => $query[$i]['Desc'],
              'CreateAT' => $this->m_master->getIndoBulan($query[$i]['CreateAT']),
            );
        }

        $arr_temp[$i] = $arr_temp[$i] + $arr_temp2;
      }
      return $arr_temp;

     }

     public function set_tuition_fee_delete_data($input,$approved = null)
     {
      $addQ = '';
      if ($approved != null) {
        $addQ = ' and Status = "'.$approved.'"';
      }
      for ($i=0; $i < count($input); $i++) {
        $sql = "delete from db_finance.register_admisi where ID_register_formulir = ".$input[$i].' '.$addQ;
        $query=$this->db->query($sql, array());

        $sql = "delete from db_finance.payment_admisi where ID_register_formulir = ".$input[$i];
        $query=$this->db->query($sql, array());

        $sql = "delete from db_finance.payment_pre where ID_register_formulir = ".$input[$i];
        $query=$this->db->query($sql, array());
      }
     }

     public function count_getDataCalonMhsTuitionFee_approved($FormulirCode)
     {

      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

      $sql= ' select count(*) as total from (
              select ID from db_finance.register_admisi
              where Status = "Approved"
              group by ID_register_formulir
            )aa';
           $query=$this->db->query($sql, array())->result_array();
      return $query[0]['total'];
     }

     public function tuitionFeeIntake_ALL($ID_register_formulir){
        $getPaymentType_Cost = $this->getPaymentType_Cost_created($ID_register_formulir);
        $arr_temp2 = array();
        for ($k=0; $k < count($getPaymentType_Cost); $k++) {
           
            // check meiliki potongan lain atau tidak
           $getPotonganLain =  $this->m_master->caribasedprimary('db_finance.payment_admisi_potongan_lain','ID_payment_admisi',$getPaymentType_Cost[$k]['ID_payment_admisi']);

          $arr_temp2 = $arr_temp2 + array(
            $getPaymentType_Cost[$k]['Abbreviation'] => number_format($getPaymentType_Cost[$k]['Pay_tuition_fee'],2,',','.'),
            'Discount-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Discount'],
            'PotonganLain-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPotonganLain,
          );
        }

        return $arr_temp2;
      }

      public function tuitionFeeIntake_ALL2($ID_register_formulir){
       $getPaymentType_Cost = $this->getPaymentType_Cost_created($ID_register_formulir);
       $arr_temp2 = array();
       for ($k=0; $k < count($getPaymentType_Cost); $k++) {
          
           // check meiliki potongan lain atau tidak
          $getPotonganLain =  $this->m_master->caribasedprimary('db_finance.payment_admisi_potongan_lain','ID_payment_admisi',$getPaymentType_Cost[$k]['ID_payment_admisi']);

         $arr_temp2 = $arr_temp2 + array(
           $getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Pay_tuition_fee'],
           'Discount-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Discount'],
           'PotonganLain-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPotonganLain,
         );
       }

      return $arr_temp2;
     }

    public function getDataCalonMhsTuitionFee_approved($limit, $start,$FormulirCode,$Status = 'p.Status = "Created" or p.Status = "Approved"')
    {
      if($FormulirCode != '%') {
          $FormulirCode = '"%'.$FormulirCode.'%"';
      }
      else
      {
        $FormulirCode = '"%"';
      }

     $arr_temp = array();
     $sql= 'select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,
             f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
             n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
             if((select count(*) as total from db_admission.register_nilai where Status = "Verified" and ID_register_formulir = a.ID limit 1) > 0,"Rapor","Ujian")
             as status1,p.CreateAT,p.CreateBY,b.FormulirCode,p.TypeBeasiswa,p.FileBeasiswa,p.Desc,p.Pay_Cond,
             if(d.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = b.FormulirCode limit 1) ,""  ) as No_Ref,p.RevID
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
              where ('.$Status.')
              and ( b.FormulirCode like '.$FormulirCode.' or px.No_Ref like '.$FormulirCode.' )
              and b.FormulirCode not in (select FormulirCode from db_admission.to_be_mhs)
             group by a.ID order by p.ID desc LIMIT '.$start. ', '.$limit;
     $query=$this->db->query($sql, array())->result_array();
     $this->load->model('master/m_master');
     $jpa = $this->m_master->showData_array('db_admission.register_dsn_jpa');
     for ($i=0; $i < count($query); $i++) {
       $DiskonSPP = 0;
       // get Price
          $arr_temp2 = $this->tuitionFeeIntake_ALL($query[$i]['ID_register_formulir']);
           // $getPaymentType_Cost = $this->getPaymentType_Cost_created($query[$i]['ID_register_formulir']);
           // $arr_temp2 = array();
           // for ($k=0; $k < count($getPaymentType_Cost); $k++) {
              
           //     // check meiliki potongan lain atau tidak
           //    $getPotonganLain =  $this->m_master->caribasedprimary('db_finance.payment_admisi_potongan_lain','ID_payment_admisi',$getPaymentType_Cost[$k]['ID_payment_admisi']);

           //   $arr_temp2 = $arr_temp2 + array(
           //     $getPaymentType_Cost[$k]['Abbreviation'] => number_format($getPaymentType_Cost[$k]['Pay_tuition_fee'],2,',','.'),
           //     'Discount-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Discount'],
           //     'PotonganLain-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPotonganLain,
           //   );
           // }

           // get file dan type beasiswa
            $getBeasiswa = $this->m_master->caribasedprimary('db_admission.register_dsn_type_m','ID',$query[$i]['TypeBeasiswa']);
            if (count($getBeasiswa) > 0) {
              $getBeasiswa = $getBeasiswa[0]['DiscountType'];
            }
            else
            {
             $getBeasiswa = '-';
            }

            // get File
            $getFile = $this->m_master->caribasedprimary('db_admission.register_document','ID',$query[$i]['FileBeasiswa']);
            if (count($getFile) > 0) {
              $getFile = $getFile[0]['Attachment'];
            }
            else
            {
             $getFile = '-';
            }

            // check Revision
            $rev = $this->m_master->caribasedprimary('db_finance.register_admisi_rev','ID_register_formulir',$query[$i]['ID_register_formulir']);

       if ($query[$i]['status1'] == 'Rapor') {
         // check rangking
           $getRangking = $this->getRangking($query[$i]['ID_register_formulir']);
           $getRangking = $getRangking[0]['Rangking'];

           $arr_temp[$i] = array(
            'ID_register_formulir' => $query[$i]['ID_register_formulir'],
            'Name' => $query[$i]['Name'],
            'NamePrody' => $query[$i]['NamePrody'],
            'SchoolName' => $query[$i]['SchoolName'],
            'Status1' => $query[$i]['status1'],
            // 'DiskonSPP' => $DiskonSPP,
            'RangkingRapor' => $getRangking,
            'FormulirCode' => $query[$i]['FormulirCode'],
            'No_Ref' => $query[$i]['No_Ref'],
            'getBeasiswa' => $getBeasiswa,
            'getFile' => $getFile,
            'Email' => $query[$i]['Email'],
            'Desc' => $query[$i]['Desc'],
            'Rev' => count($rev),
            'CreateAT' => $this->m_master->getIndoBulan($query[$i]['CreateAT']),
            'Pay_Cond' => $query[$i]['Pay_Cond'],
           );
       }
       else
       {
           $arr_temp[$i] = array(
             'ID_register_formulir' => $query[$i]['ID_register_formulir'],
             'Name' => $query[$i]['Name'],
             'NamePrody' => $query[$i]['NamePrody'],
             'SchoolName' => $query[$i]['SchoolName'],
             'Status1' => $query[$i]['status1'],
             // 'DiskonSPP' => $DiskonSPP,
             'RangkingRapor' => 0,
             'FormulirCode' => $query[$i]['FormulirCode'],
             'No_Ref' => $query[$i]['No_Ref'],
             'getBeasiswa' => $getBeasiswa,
             'getFile' => $getFile,
             'Email' => $query[$i]['Email'],
             'Desc' => $query[$i]['Desc'],
             'Rev'  =>count($rev),
             'CreateAT' => $query[$i]['CreateAT'],
             'CreateAT' => $this->m_master->getIndoBulan($query[$i]['CreateAT']),
             'Pay_Cond' => $query[$i]['Pay_Cond'],
           );
       }

       $arr_temp[$i] = $arr_temp[$i] + $arr_temp2;
     }
     return $arr_temp;

    }

    public function getDataCalonMhsCicilan($limit, $start)
    {
     $arr_temp = array();
     $sql= 'select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,
             f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,g.JenisTempatTinggal,
             h.ctr_name as CountryAddress,i.ProvinceName as ProvinceAddress,j.RegionName as RegionAddress,k.DistrictName as DistrictsAddress,
             a.District as DistrictAddress,a.Address,a.ZipCode,a.PhoneNumber,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
             n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
             if((select count(*) as total from db_admission.register_nilai where Status = "Approved" and ID_register_formulir = a.ID limit 1) > 0,"Rapor","Ujian")
             as status1,d.VA_number
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
             where a.ID in (select ID_register_formulir from db_finance.payment_pre)
             order by a.ID desc
            LIMIT '.$start. ', '.$limit;
     $query=$this->db->query($sql, array())->result_array();
     $this->load->model('master/m_master');
     for ($i=0; $i < count($query); $i++) {
       $getCicilan = $this->m_master->caribasedprimary('db_finance.payment_pre','ID_register_formulir',$query[$i]['ID_register_formulir']);
       $arr_temp[$i] = array(
          'ID_register_formulir' => $query[$i]['ID_register_formulir'],
          'VA_number' => $query[$i]['VA_number'],
          'Name' => $query[$i]['Name'],
          'NamePrody' => $query[$i]['NamePrody'],
          'SchoolName' => $query[$i]['SchoolName'],
          'Status1' => $query[$i]['status1'],
          'Email' => $query[$i]['Email'],
       );
       for ($j=0; $j < count($getCicilan); $j++) {
         $arr_temp2 = array();
         $arr_temp2 = array(
          'Cicilan_'.($j + 1) => array(
                                      'Invoice' => number_format($getCicilan[$j]['Invoice'],2,',','.'),
                                      'BilingID' => $getCicilan[$j]['BilingID'],
                                      'Status' => $getCicilan[$j]['Status'],
                                      'Deadline' => $getCicilan[$j]['Deadline'],
                                      'ID' => $getCicilan[$j]['ID'],
                                      )
        );
         if (count($getCicilan) == ($j+1)) {
            $a = $this->m_master->showData_array('db_admission.cfg_cicilan');
           if (count($getCicilan) != $a[0]['max_cicilan']) {
             $b = $a[0]['max_cicilan'] - count($getCicilan);
             for ($k=0; $k < $b; $k++) {
               $arr_temp3 = array();
                 $arr_temp3 = array(
                  'Cicilan_'.($j + 1 + $k + 1) => array(
                                              'Invoice' => '',
                                              'BilingID' => '',
                                              'Status' => '',
                                              'Deadline' => '',
                                              'ID' => '',
                                              )
                );
                $arr_temp2 = $arr_temp2 + $arr_temp3;
             }
           }
         }
         $arr_temp[$i] = $arr_temp[$i] + $arr_temp2;
       }
     }
     return $arr_temp;

    }

    public function getDataPersonal($ID_register_formulir)
    {

      $sql = "select a.*,c.FormulirCode,d.ID_program_study,e.Name as NameProdyIND,e.NameEng as NameProdyEng from db_admission.register as a join db_admission.register_verification as b

              on a.ID = b.RegisterID join db_admission.register_verified as c on b.ID = c.RegVerificationID
              join db_admission.register_formulir as d on d.ID_register_verified = c.ID
              join db_academic.program_study as e on d.ID_program_study = e.ID
              LEFT JOIN db_admission.sale_formulir_offline AS sfo ON (sfo.FormulirCodeOffline = c.FormulirCode)
              where d.ID = ?
            ";
      $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
      return $query;
    }

    public function cekTanggalTime($datetime)
    {
      $sql = 'select * from(select now() as a,"'.$datetime.'" as d) tbl
              where a > d';
      $query=$this->db->query($sql, array())->result_array();

      if (count($query) > 0) {
        return true;
      }
      else
      {
        return false;
      }
    }

    public function getDataCalonMhsAll($limit, $start,$input = null)
    {
      $arr_result = array();
      $Nama = '';
      $Sekolah = '';
      $Email = '';
      $BFormulir = $input['BFormulir'];
      $Ranking = '';
      $BKuliah = $input['BKuliah'];
      $Prody = '';
        if ($input != null) {
          $Nama = ' Name like "%'.$input['Nama'].'%"' ;
          $Sekolah = ($input['Sekolah'] != '') ? ' and SchoolID = '.$input['Sekolah'] : '';
          $Email = ($input['Email'] != '') ? ' and Email like "'.$input['Email'].'%"' : '';
          /*$BFormulir = ($input['BFormulir'] == 0) ? ' and FormulirCode is NULL' : '';
          if ($input['BFormulir'] == 1) {
            $BFormulir = ' and FormulirCode is NOT NULL';
          }*/
          if ($input['BFormulir'] == '%') {
            $BFormulir = '';
          }
          elseif($input['BFormulir'] == 1)
          {
            $BFormulir = ' and FormulirCode is NOT NULL';
          }
          elseif($input['BFormulir'] == 0)
          {
            $BFormulir = ' and FormulirCode is NULL';
          }

          $Ranking = ($input['Prody'] != '' ) ? ' and Rangking ='.$input['Ranking'] : '';
          if ($input['Ranking'] == '%') {
            $Ranking = '';
          }

          $v= ($input['BFormulir'] == '0') ? '= 0' : '!= 0';
          // $BKuliah = ($input['BKuliah'] != '%') ? ' and NotLunas '.$v : '';
          $Prody = ($input['Prody'] != '%' ) ? ' and ID_program_study ='.$input['Prody'] : '';

          if ($input['BKuliah'] != '%') {
            if ($input['BKuliah'] == 1) {
              $BKuliah = ' and NotLunas = 0 and FormulirCode is not null and chklunas > 0';
            }
            else
            {
              $BKuliah = '  and (chklunas != Cicilan) or chklunas = 0';
            }
          }
          else
          {
            $BKuliah = '';
          }

          $sql = 'select * from (
                  select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,e.ID as ID_register_formulir,e.UploadFoto,
                  f.Rangking,(select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID and aaa.Status = 0) as NotLunas,
                  (select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID and aaa.Status = 1) as chklunas,
                  (select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID ) as Cicilan
                  from db_admission.register as a
                  join db_admission.school as b
                  on a.SchoolID = b.ID
                  LEFT JOIN db_admission.register_verification as z
                  on a.ID = z.RegisterID
                  LEFT JOIN db_admission.register_verified as c
                  on z.ID = c.RegVerificationID
                  LEFT JOIN db_admission.register_formulir as e
                  on c.ID = e.ID_register_verified
                  LEFT join db_academic.program_study as d
                  on e.ID_program_study = d.ID
                  LEFT join db_admission.register_rangking as f
                  on e.ID = f.ID_register_formulir
                  ) ccc where '.$Nama.$Sekolah.$Email.$Ranking.$Prody.$BFormulir.$BKuliah.' order by ID_register_formulir desc
            LIMIT '.$start. ', '.$limit;
          $query=$this->db->query($sql, array())->result_array();
          $arr_result = $query;
        }
        return $arr_result;
    }

    private function searchDataPotonganObject($ID_register_formulir,$PTID,$dataInputPotonganLain){
      $arr = [];

      for ($i=0; $i < count($dataInputPotonganLain); $i++) { 
        if ($dataInputPotonganLain[$i]->ID_register_formulir == $ID_register_formulir && $dataInputPotonganLain[$i]->PTID == $PTID) {
          $arr[] = $dataInputPotonganLain[$i];
        }
      }

      return $arr;

    }

    public function set_input_tuition_fee_submit($input)
    {
      // print_r($input);die();
      $this->load->model('finance/m_finance');
      //save data to payment_register
      $this->load->model('master/m_master');
      $data2 = $input['data2'][0];
      $dataInputPotonganLain = $input['dataInputPotonganLain'];

      $temp = array();
      $temp2 = array();
      $temp['ID_register_formulir'] = $data2->id_formulir;
      $temp['TypeBeasiswa'] = $data2->getBeasiswa;
      $temp['FileBeasiswa'] = $data2->getDokumen;
      $temp['Desc'] = $data2->ket;
      $temp['Pay_Cond'] = $data2->Pay_Cond;
      $temp['CreateAT'] = date('Y-m-d');
      $temp['CreateBY'] = $this->session->userdata('NIP');
      $this->db->insert('db_finance.register_admisi', $temp);


      foreach ($data2 as $key => $value) {
            $a = explode('-', $key);
            if (count($a) > 1) {
              $typePayResult = $a[1];
              $get = $this->m_master->caribasedprimary('db_finance.payment_type','Abbreviation',$a[1]);
              $temp2['Discount'] = $value;
              $temp2['Pay_tuition_fee'] = $this->m_master->ClearPricetoDB($data2->$typePayResult);
              $temp2['PTID'] = $get[0]['ID'];
              $temp2['ID_register_formulir'] = $data2->id_formulir;

              $this->db->insert('db_finance.payment_admisi', $temp2);
              $ID_payment_admisi = $this->db->insert_id();

              // insert payment_admisi_potongan_lain
                $searchDataPotongan =  $this->searchDataPotonganObject($temp2['ID_register_formulir'],$temp2['PTID'],$dataInputPotonganLain);
                // print_r($searchDataPotongan);die();
                if (count($searchDataPotongan) > 0) {
                  $dataPotongan = $searchDataPotongan[0]->data;
                  for ($y=0; $y < count($dataPotongan); $y++) { 
                    $dataSavePotonganLain = [
                      'ID_payment_admisi' => $ID_payment_admisi,
                      'DiscountName' => $dataPotongan[$y]->DiscountName ,
                      'DiscountValue' => $dataPotongan[$y]->DiscountValue,
                      'Description' => $dataPotongan[$y]->Description,
                      'UpdatedBy' => $this->session->userdata('NIP'),
                      'UpdateAt' => date('Y-m-d H:i:s') ,
                    ];

                    $this->db->insert('db_finance.payment_admisi_potongan_lain', $dataSavePotonganLain);

                  }
                }
                
                


            }

      }


      // save data cicilan pada table payment_pre
        $ID_register_formulir = $data2->id_formulir;
        $data1 = $input['data1'];
        $arr2 = array();
        for ($i=0; $i < count($data1); $i++) {
          $temp3 = array();
          $temp3['ID_register_formulir'] = $ID_register_formulir;
          $temp3['Invoice'] = $data1[$i]->Payment;
          $temp3['Deadline'] = $data1[$i]->Deadline;
          $arr2[] = $temp3;
        }

        $this->db->insert_batch('db_finance.payment_pre', $arr2);

        /* 31-05-2019
          Perubahan set tuition fee tanpa approve finance
          dan PDF langsung cetak by admission
        */
        /* Adding  and update after insert */
          $No_Surat = $this->m_finance->getNumberSuratTuitionFee($data2->id_formulir);
          $dataSave__ = array(
                  'Status' => 'Approved',
                  'No_Surat' => $No_Surat,
                  'ApprovedBY' => $this->session->userdata('NIP'),
                  'ApprovedAT' => date('Y-m-d'),
                          );
          $this->db->where('ID_register_formulir',$data2->id_formulir);
          $this->db->update('db_finance.register_admisi', $dataSave__);


          /* Create PDF file */
          $getData = $this->m_finance->tuition_fee_calon_mhs_by_ID($data2->id_formulir,'Approved');
          $cicilan = $this->m_master->caribasedprimary('db_finance.payment_pre','ID_register_formulir',$data2->id_formulir);
          $this->Tuition_PDF_SendEmail($getData,$cicilan);
        /* End Adding */

    }

    public function Tuition_PDF_SendEmail($Personal,$arr_cicilan)
    {
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

        $cicilan_tulis = array('Cicilan Pertama','Cicilan Kedua','Cicilan Ketiga','Cicilan Keempat','Cicilan Kelima','Cicilan Keenam','Cicilan Ketujuh','Cicilan Kedelapan','Cicilan Kesembilan','Cicilan Kesepuluh');

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

         $path = './document';
         $path = $path.'/'.$filename;
         $this->mypdf->Output($path,'F');
         // echo json_encode($filename);
         $text = 'Dear '.$Personal[0]['Name'].',<br><br>
                     Plase find attached your Tuition Fee.<br>
                     For Detail your payment, please see in '.url_registration."login/";
         if($_SERVER['SERVER_NAME']!='localhost' && $_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
            // $to = $Personal[0]['Email'].','.'admission@podomorouniversity.ac.id';
            $to = 'admission@podomorouniversity.ac.id';
         }
         else
         {
            $to = 'alhadirahman22@gmail.com,alhadi.rahman@podomorouniversity.ac.id';
         }
         $subject = "Podomoro University Tuition Fee";
         $sendEmail = $this->m_sendemail->sendEmail($to,$subject,null,null,null,null,$text,$path);

    }

    public function getCountDataPersonal_Candidate_to_be_mhs($requestData,$reqTahun,$FormulirType = '%',$StatusPayment = '%')
    {
      $AddWhere = '';
      $AddWhere2 = ' chklunas in ("Lunas","Belum Lunas") and CekIntake = "Intake" and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
              join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
              join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
              join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
              join db_admission.register as reg on reg.ID = rve.RegisterID
              where reg.SetTa = "'.$reqTahun.'" ) ';
      if ($FormulirType != '%') {
         $AddWhere .= ' and a.StatusReg = '.$FormulirType.' ';
      }
      $sql = 'select count(*) as total from (
              select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, e.ID as ID_register_formulir,e.UploadFoto,
              xq.DiscountType,
              if(f.Rangking > 0 ,f.Rangking,"-") as Rangking,
              if(
                  (select count(*) as total from db_finance.payment_pre where `Status` = 0 and ID_register_formulir = e.ID limit 1) = 0 ,
                      if((select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID limit 1)
                           > 0 ,"Lunas","-"
                        )
                      ,
                      "Belum Lunas"
                ) as chklunas,
              (select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID ) as Cicilan
              ,xx.Name as NameSales,px.No_Ref,
              if(
                (select count(*) as total from db_finance.payment_pre where `Status` = 1 and ID_register_formulir = e.ID ) > 0,"Intake","Not Intake"
              ) as CekIntake
              from db_admission.register as a
              LEFT join db_admission.school as b
              on a.SchoolID = b.ID
              LEFT JOIN db_admission.register_verification as z
              on a.ID = z.RegisterID
              LEFT JOIN db_admission.register_verified as c
              on z.ID = c.RegVerificationID
              LEFT JOIN db_admission.register_formulir as e
              on c.ID = e.ID_register_verified
              LEFT join db_academic.program_study as d
              on e.ID_program_study = d.ID
              LEFT join db_admission.register_rangking as f
              on e.ID = f.ID_register_formulir
              left join db_admission.sale_formulir_offline as xz
                on c.FormulirCode = xz.FormulirCodeOffline
              LEFT JOIN db_employees.employees as xx
              on xz.PIC = xx.NIP
              LEFT JOIN db_finance.register_admisi as xy
              on e.ID = xy.ID_register_formulir
              LEFT JOIN db_admission.register_dsn_type_m as xq
              on xq.ID = xy.TypeBeasiswa
              left join db_admission.formulir_number_offline_m as px
              on px.FormulirCode = c.FormulirCode
              where a.SetTa = "'.$reqTahun.'" '.$AddWhere.'
            ) ccc
          ';
      if ($StatusPayment != '%') {
         $AddWhere2 .= ' and chklunas = "'.$StatusPayment.'" ';
      }
         
      $sql.= ' where (Name LIKE "'.$requestData['search']['value'].'%" or NamePrody LIKE "%'.$requestData['search']['value'].'%"
              or FormulirCode LIKE "'.$requestData['search']['value'].'%" or SchoolName LIKE "%'.$requestData['search']['value'].'%"
              #or chklunas LIKE "'.$requestData['search']['value'].'%" 
              or DiscountType LIKE "'.$requestData['search']['value'].'%"
              or NameSales LIKE "'.$requestData['search']['value'].'%"
              or No_Ref LIKE "'.$requestData['search']['value'].'%"
                )
             and '.$AddWhere2.' and FormulirCode not in (select FormulirCode from db_admission.to_be_mhs)';
      $query=$this->db->query($sql, array())->result_array();
      return $query[0]['total'];
    }

    public function getCountAllDataPersonal_Candidate($requestData,$reqTahun,$FormulirType = '%',$StatusPayment = '%')
    {
      $AddWhere = '';
      $AddWhere2 = '';
      if ($FormulirType != '%') {
         $AddWhere .= ' and a.StatusReg = '.$FormulirType.' ';
      }
      $sql = 'select count(*) as total from (
                select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, e.ID as ID_register_formulir,e.UploadFoto,
                xq.DiscountType,
                if(f.Rangking > 0 ,f.Rangking,"-") as Rangking,
                if(
                    (select count(*) as total from db_finance.payment_pre where `Status` = 0 and ID_register_formulir = e.ID limit 1) = 0 ,
                        if((select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID limit 1)
                             > 0 ,"Lunas","-"
                          )
                        ,
                        "Belum Lunas"
                  ) as chklunas,
                (select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID ) as Cicilan
                ,xx.Name as NameSales,
                if(a.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,(select No_Ref from db_admission.formulir_number_online_m where FormulirCode = c.FormulirCode limit 1)  ) as No_Ref,
                if(
                  (select count(*) as total from db_finance.payment_pre where `Status` = 1 and ID_register_formulir = e.ID ) > 0,"Intake","Not Intake"
                ) as CekIntake,
                if(
                  (select count(*) as total from db_finance.register_refund where ID_register_formulir = e.ID ) > 0,"Refund",""
                ) as CekRefund
                from db_admission.register as a
                join db_admission.school as b
                on a.SchoolID = b.ID
                LEFT JOIN db_admission.register_verification as z
                on a.ID = z.RegisterID
                LEFT JOIN db_admission.register_verified as c
                on z.ID = c.RegVerificationID
                LEFT JOIN db_admission.register_formulir as e
                on c.ID = e.ID_register_verified
                LEFT join db_academic.program_study as d
                on e.ID_program_study = d.ID
                LEFT join db_admission.register_rangking as f
                on e.ID = f.ID_register_formulir
                left join db_admission.sale_formulir_offline as xz
                  on c.FormulirCode = xz.FormulirCodeOffline
                LEFT JOIN db_employees.employees as xx
                on xz.PIC = xx.NIP
                LEFT JOIN db_finance.register_admisi as xy
                on e.ID = xy.ID_register_formulir
                LEFT JOIN db_admission.register_dsn_type_m as xq
                on xq.ID = xy.TypeBeasiswa
                where a.SetTa = "'.$reqTahun.'"  '.$AddWhere.'
              ) ccc';
      if ($StatusPayment != '%') {
        if ($StatusPayment == '-100') {
          
          $AddWhere2 .= ' and (FormulirCode = "" or FormulirCode is NULL ) ';
        }
        elseif ($StatusPayment == '100') {
           $AddWhere2 .= ' and FormulirCode != "" or FormulirCode is not NULL  ';
        }
        elseif($StatusPayment == 'Intake')
        {
            $AddWhere2 .= ' and CekIntake = "Intake" and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
              join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
              join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
              join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
              join db_admission.register as reg on reg.ID = rve.RegisterID
              where reg.SetTa = "'.$reqTahun.'"

            ) ';
        }
        else{
          $AddWhere2 .= ' and chklunas = "'.$StatusPayment.'" ';
        }
      }
      $sql.= ' where (Name LIKE "'.$requestData['search']['value'].'%" or NamePrody LIKE "%'.$requestData['search']['value'].'%"
              or FormulirCode LIKE "'.$requestData['search']['value'].'%" or SchoolName LIKE "%'.$requestData['search']['value'].'%"
              #or chklunas LIKE "'.$requestData['search']['value'].'%" 
              or DiscountType LIKE "'.$requestData['search']['value'].'%"
              or NameSales LIKE "'.$requestData['search']['value'].'%"
              or No_Ref LIKE "'.$requestData['search']['value'].'%"
              )
              '.$AddWhere2.'
              ';

      $query=$this->db->query($sql, array())->result_array();
      return $query[0]['total'];
    }

    public function getSaleFormulirOfflineBetwwen($date1,$date2,$SelectSetTa,$SelectSortBy)
    {
      // print_r($SelectSortBy);die();
      $sql = 'select * from (
                select a.FormulirCode,a.No_Ref,a.Years,a.Status,a.StatusJual,b.FullName,b.HomeNumber,b.PhoneNumber,b.DateSale,
                b.Email,c.Name as Sales,b.PIC,b.ID,b.Price_Form,z.SchoolName as SchoolNameFormulir,z.CityName as  CityNameFormulir,z.DistrictName as DistrictNameFormulir,b.Gender,
                if(b.source_from_event_ID = 0,"", (select src_name from db_admission.source_from_event where ID = b.source_from_event_ID and Active = 1 limit 1) ) as src_name,b.ID_ProgramStudy,
                y.Name as NameProdi1,b.Channel,
                if(b.ID_ProgramStudy2 = 0,"", (select Name from db_academic.program_study where ID = b.ID_ProgramStudy2 limit 1) ) as NameProdi2
                from db_admission.formulir_number_offline_m as a
                left join db_admission.sale_formulir_offline as b
                on a.FormulirCode = b.FormulirCodeOffline
                left join db_employees.employees as c
                on c.NIP = b.PIC
                left join db_admission.school as z
                on z.ID = b.SchoolID
                left join db_academic.program_study as y
                on b.ID_ProgramStudy = y.ID
                where b.DateSale >= "'.$date1.'" and b.DateSale <= "'.$date2.'" and a.Years = ? 
                UNION
                select a.FormulirCode,a.No_Ref,a.Years,a.Status,1,c.Name as FullName,c.Phone as HomeNumber,c.Phone as PhoneNumber,DATE_FORMAT(b.VerificationAT, "%Y-%m-%d") as DateSale,
                                c.Email,"Online" as Sales,"Online" as PIC,"" as ID,c.PriceFormulir as Price_Form,z.SchoolName as SchoolNameFormulir,z.CityName as  CityNameFormulir,z.DistrictName as DistrictNameFormulir,rf.Gender,
                                "Online" as src_name,rf.ID_program_study,
                                y.Name as NameProdi1,"Online" as Channel,
                                "" as NameProdi2
                                from db_admission.formulir_number_online_m as a
                                left join db_admission.register_verified as b on a.FormulirCode = b.FormulirCode
                                left join db_admission.register_formulir as rf on b.ID = rf.ID_register_verified
                                left join db_admission.register_verification as d on d.ID =  b.RegVerificationID
                                left join db_admission.register as c on c.ID = d.RegisterID
                                left join db_admission.school as z
                                on z.ID = c.SchoolID
                                left join db_academic.program_study as y
                                on rf.ID_program_study = y.ID
                                where DATE_FORMAT(b.VerificationAT, "%Y-%m-%d") >= "'.$date1.'" and DATE_FORMAT(b.VerificationAT, "%Y-%m-%d") <= "'.$date2.'" and a.Years = '.$SelectSetTa.'
              ) as a
                order by '.$SelectSortBy.' asc
                ';
      $query=$this->db->query($sql, array($SelectSetTa))->result_array();
      return $query;
    }

    public function getSaleFormulirOfflinePerMonth($SelectMonth,$SelectYear,$SelectSetTa,$SelectSortBy)
    {
      $sql = 'select * from (
              select a.FormulirCode,a.No_Ref,a.Years,a.Status,a.StatusJual,b.FullName,b.HomeNumber,b.PhoneNumber,b.DateSale,
                b.Email,c.Name as Sales,b.PIC,b.ID,b.Price_Form,z.SchoolName as SchoolNameFormulir,z.CityName as  CityNameFormulir,z.DistrictName as DistrictNameFormulir,b.Gender,
                if(b.source_from_event_ID = 0,"", (select src_name from db_admission.source_from_event where ID = b.source_from_event_ID and Active = 1 limit 1) ) as src_name,b.ID_ProgramStudy,
                y.Name as NameProdi1,b.Channel,
                if(b.ID_ProgramStudy2 = 0,"", (select Name from db_academic.program_study where ID = b.ID_ProgramStudy2 limit 1) ) as NameProdi2
                from db_admission.formulir_number_offline_m as a
                join db_admission.sale_formulir_offline as b
                on a.FormulirCode = b.FormulirCodeOffline
                left join db_employees.employees as c
                on c.NIP = b.PIC
                left join db_admission.school as z
                on z.ID = b.SchoolID
                left join db_academic.program_study as y
                on b.ID_ProgramStudy = y.ID
                where YEAR(b.DateSale) = "'.$SelectYear.'" AND MONTH(b.DateSale) = "'.$SelectMonth.'" and a.Years = ? 
                UNION
                select a.FormulirCode,a.No_Ref,a.Years,a.Status,1,c.Name as FullName,c.Phone as HomeNumber,c.Phone as PhoneNumber,DATE_FORMAT(b.VerificationAT, "%Y-%m-%d") as DateSale,
                                c.Email,"Online" as Sales,"Online" as PIC,"" as ID,c.PriceFormulir as Price_Form,z.SchoolName as SchoolNameFormulir,z.CityName as  CityNameFormulir,z.DistrictName as DistrictNameFormulir,rf.Gender,
                                "Online" as src_name,rf.ID_program_study,
                                y.Name as NameProdi1,"Online" as Channel,
                                "" as NameProdi2
                                from db_admission.formulir_number_online_m as a
                                left join db_admission.register_verified as b on a.FormulirCode = b.FormulirCode
                                left join db_admission.register_formulir as rf on b.ID = rf.ID_register_verified
                                left join db_admission.register_verification as d on d.ID =  b.RegVerificationID
                                left join db_admission.register as c on c.ID = d.RegisterID
                                left join db_admission.school as z
                                on z.ID = c.SchoolID
                                left join db_academic.program_study as y
                                on rf.ID_program_study = y.ID
                                where YEAR(b.VerificationAT) = "'.$SelectYear.'" and MONTH(b.VerificationAT) = "'.$SelectMonth.'" and a.Years = '.$SelectSetTa.'

                ) as a
                order by '.$SelectSortBy.' asc
                ';
      $query=$this->db->query($sql, array($SelectSetTa))->result_array();
      return $query;
    }

    public function getSaleFormulirOfflinePerTA($SelectSetTa)
    {
      $sql = 'select a.FormulirCode,a.No_Ref,a.Years,a.Status,a.StatusJual,b.FullName,b.HomeNumber,b.PhoneNumber,b.DateSale,
                b.Email,c.Name as Sales,b.PIC,b.ID,b.Price_Form,z.SchoolName as SchoolNameFormulir,z.CityName as  CityNameFormulir,z.DistrictName as DistrictNameFormulir,b.Gender,
                if(b.source_from_event_ID = 0,"", (select src_name from db_admission.source_from_event where ID = b.source_from_event_ID and Active = 1 limit 1) ) as src_name,b.ID_ProgramStudy,
                y.Name as NameProdi1,b.Channel,
                if(b.ID_ProgramStudy2 = 0,"", (select Name from db_academic.program_study where ID = b.ID_ProgramStudy2 limit 1) ) as NameProdi2
                from db_admission.formulir_number_offline_m as a
                join db_admission.sale_formulir_offline as b
                on a.FormulirCode = b.FormulirCodeOffline
                left join db_employees.employees as c
                on c.NIP = b.PIC
                left join db_admission.school as z
                on z.ID = b.SchoolID
                left join db_academic.program_study as y
                on b.ID_ProgramStudy = y.ID
                where a.Years = ? order by a.No_Ref asc
                ';
      $query=$this->db->query($sql, array($SelectSetTa))->result_array();
      return $query;
    }

    public function getRegisterData($date1,$date2,$SelectSetTa,$SelectSortBy)
    {
      $SelectSortBy = explode(".", $SelectSortBy);
      $SelectSortBy = ($SelectSortBy[1] == "No_Ref" || $SelectSortBy[1] == "FormulirCode") ? 'c.FormulirCode' : 'a.RegisterAT';
      $this->load->model('master/m_master');
      $sql = '
              select c.FormulirCode,a.RegisterAT,a.Name,e.Gender,e.PlaceBirth,e.DateBirth,ac.Nama as Agama,ad.ctr_name,d.NameEng,d.Name as NamaProdi, e.PhoneNumber,xz.HomeNumber,
            a.Email,ae.JacketSize,ab.src_name,CONCAT(e.Address," ",e.District," ",af.DistrictName) as alamat,ag.ProvinceName,
            xx.Name as NameSales,a.StatusReg,a.SchoolID,e.ID as ID_register_formulir,
            e.FatherName,e.FatherStatus,e.FatherPhoneNumber,ah.ocu_name as FatherJob,e.FatherAddress,e.MotherName,MotherStatus,e.MotherPhoneNumber,
            ai.ocu_name as MotherJob,e.MotherAddress
            from db_admission.register as a
            join db_admission.school as b
            on a.SchoolID = b.ID
            JOIN db_admission.register_verification as z
            on a.ID = z.RegisterID
            JOIN db_admission.register_verified as c
            on z.ID = c.RegVerificationID
            JOIN db_admission.register_formulir as e
            on c.ID = e.ID_register_verified
            LEFT join db_academic.program_study as d
            on e.ID_program_study = d.ID
            left join db_admission.sale_formulir_offline as xz
             on c.FormulirCode = xz.FormulirCodeOffline
            LEFT JOIN db_employees.employees as xx
            on xz.PIC = xx.NIP
            left join db_admission.source_from_event as ab
            on xz.source_from_event_ID = ab.ID
            LEFT JOIN db_admission.agama as ac
            on e.ReligionID = ac.ID
            left join db_admission.country as ad
            on ad.ctr_code = e.NationalityID
            LEFT JOIN db_admission.register_jacket_size_m as ae
            on ae.ID = e.ID_register_jacket_size_m
            LEFT JOIN db_admission.district af
            on af.DistrictID = e.ID_districts
            LEFT JOIN db_admission.province as ag
            on ag.ProvinceID = e.ID_province
            LEFT JOIN db_admission.occupation as ah
            on ah.ocu_code = e.Father_ID_occupation
            LEFT JOIN db_admission.occupation as ai
            on ai.ocu_code = e.Mother_ID_occupation
            where a.SetTa = ? and a.RegisterAT >= "'.$date1.'" and a.RegisterAT <= "'.$date2.'" order by '.$SelectSortBy.' asc
            ';
      $query=$this->db->query($sql, array($SelectSetTa))->result_array();
      for ($i=0; $i < count($query); $i++) {
          // check no ref jika offline
          $row = $query[$i];
          $Off = $row['StatusReg']; // 1 offline, 0 online
          $arr_temp = array('FormulirWrite' => $row['FormulirCode']);
          if ($Off == 1) {
            $get1 = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','FormulirCode',$row['FormulirCode']);
            $No_Ref = $get1[0]['No_Ref'];
            if ($No_Ref != "" || $No_Ref != null) {
              $arr_temp = array('FormulirWrite' => $No_Ref);
            }
          }

          $get2 = $this->m_master->caribasedprimary('db_admission.school','ID',$row['SchoolID']);
          $arr_temp = $arr_temp + array('SchoolName' => $get2[0]['SchoolName'],'CitySchool' => $get2[0]['CityName']);

          // find document
          $get3 = $this->m_master->caribasedprimary('db_admission.register_document','ID_register_formulir',$row['ID_register_formulir']);
          /*
            7 = Admission Statement
            2 = 1 Fotocopi Raport Semester Terakhir Yang Dilegalisir
            3 = 1 Fotocopi Ijazah Yang Dilegalisir
            ID = ""
            5 = 3 Pas Foto Terbaru Ukuran 3x4 Dengan Warna Latar Belakang Merah
            9 = 1 Surat Rekomendasi Dari Sekolah
            8 = 1 Surat Pernyataan Bebas Narkoba
            6 = 1 Lembar Essay Mengenai
          */
            for ($j=0; $j < count($get3); $j++) {
              switch ($get3[$j]['ID_reg_doc_checklist']) {
                case 7:
                  if ($get3[$j]['Status'] == "Done") {
                    $arr_temp = $arr_temp + array('ads_sta' => 'v');
                  }
                  else
                  {
                    $arr_temp = $arr_temp + array('ads_sta' => '');
                  }
                  break;
                case 2:
                  if ($get3[$j]['Status'] == "Done") {
                    $arr_temp = $arr_temp + array('raport' => 'v');
                  }
                  else
                  {
                    $arr_temp = $arr_temp + array('raport' => '');
                  }
                  break;
                case 3:
                  if ($get3[$j]['Status'] == "Done") {
                    $arr_temp = $arr_temp + array('Ijazah' => 'v');
                  }
                  else
                  {
                    $arr_temp = $arr_temp + array('Ijazah' => '');
                  }
                  break;
                case 5:
                  if ($get3[$j]['Status'] == "Done") {
                    $arr_temp = $arr_temp + array('Foto' => 'v');
                  }
                  else
                  {
                    $arr_temp = $arr_temp + array('Foto' => '');
                  }
                  break;
                case 9:
                  if ($get3[$j]['Status'] == "Done") {
                    $arr_temp = $arr_temp + array('Refletter' => 'v');
                  }
                  else
                  {
                    $arr_temp = $arr_temp + array('Refletter' =>'');
                  }
                  break;
                case 8:
                  if ($get3[$j]['Status'] == "Done") {
                    $arr_temp = $arr_temp + array('SuratNarkoba' => 'v');
                  }
                  else
                  {
                    $arr_temp = $arr_temp + array('SuratNarkoba' => '');
                  }
                  break;
              case 6:
                if ($get3[$j]['Status'] == "Done") {
                  $arr_temp = $arr_temp + array('Essay' => 'v');
                }
                else
                {
                  $arr_temp = $arr_temp + array('Essay' => '');
                }
                break;
                default:
                  # code...
                  break;
              }
            }

            $query[$i] = $query[$i] + $arr_temp;

      } // exit loop

      return $query;

    }

    public function getRegisterDataPermonth($SelectMonth,$SelectYear,$SelectSetTa,$SelectSortBy)
    {
      $SelectSortBy = explode(".", $SelectSortBy);
      $SelectSortBy = ($SelectSortBy[1] == "No_Ref" || $SelectSortBy[1] == "FormulirCode") ? 'c.FormulirCode' : 'a.RegisterAT';
      $this->load->model('master/m_master');
      $sql = '
              select c.FormulirCode,a.RegisterAT,a.Name,e.Gender,e.PlaceBirth,e.DateBirth,ac.Nama as Agama,ad.ctr_name,d.NameEng,d.Name as NamaProdi, e.PhoneNumber,xz.HomeNumber,
            a.Email,ae.JacketSize,ab.src_name,CONCAT(e.Address," ",e.District," ",af.DistrictName) as alamat,ag.ProvinceName,
            xx.Name as NameSales,a.StatusReg,a.SchoolID,e.ID as ID_register_formulir,
            e.FatherName,e.FatherStatus,e.FatherPhoneNumber,ah.ocu_name as FatherJob,e.FatherAddress,e.MotherName,MotherStatus,e.MotherPhoneNumber,
            ai.ocu_name as MotherJob,e.MotherAddress
            from db_admission.register as a
            join db_admission.school as b
            on a.SchoolID = b.ID
            JOIN db_admission.register_verification as z
            on a.ID = z.RegisterID
            JOIN db_admission.register_verified as c
            on z.ID = c.RegVerificationID
            JOIN db_admission.register_formulir as e
            on c.ID = e.ID_register_verified
            LEFT join db_academic.program_study as d
            on e.ID_program_study = d.ID
            left join db_admission.sale_formulir_offline as xz
             on c.FormulirCode = xz.FormulirCodeOffline
            LEFT JOIN db_employees.employees as xx
            on xz.PIC = xx.NIP
            left join db_admission.source_from_event as ab
            on xz.source_from_event_ID = ab.ID
            LEFT JOIN db_admission.agama as ac
            on e.ReligionID = ac.ID
            left join db_admission.country as ad
            on ad.ctr_code = e.NationalityID
            LEFT JOIN db_admission.register_jacket_size_m as ae
            on ae.ID = e.ID_register_jacket_size_m
            LEFT JOIN db_admission.district af
            on af.DistrictID = e.ID_districts
            LEFT JOIN db_admission.province as ag
            on ag.ProvinceID = e.ID_province
            LEFT JOIN db_admission.occupation as ah
            on ah.ocu_code = e.Father_ID_occupation
            LEFT JOIN db_admission.occupation as ai
            on ai.ocu_code = e.Mother_ID_occupation
            where a.SetTa = ? and YEAR(a.RegisterAT) = "'.$SelectYear.'" AND MONTH(a.RegisterAT) = "'.$SelectMonth.'" order by '.$SelectSortBy.' asc
            ';
      $query=$this->db->query($sql, array($SelectSetTa))->result_array();
      for ($i=0; $i < count($query); $i++) {
          // check no ref jika offline
          $row = $query[$i];
          $Off = $row['StatusReg']; // 1 offline, 0 online
          $arr_temp = array('FormulirWrite' => $row['FormulirCode']);
          if ($Off == 1) {
            $get1 = $this->m_master->caribasedprimary('db_admission.formulir_number_offline_m','FormulirCode',$row['FormulirCode']);
            $No_Ref = $get1[0]['No_Ref'];
            if ($No_Ref != "" || $No_Ref != null) {
              $arr_temp = array('FormulirWrite' => $No_Ref);
            }
          }

          $get2 = $this->m_master->caribasedprimary('db_admission.school','ID',$row['SchoolID']);
          $arr_temp = $arr_temp + array('SchoolName' => $get2[0]['SchoolName'],'CitySchool' => $get2[0]['CityName']);

          // find document
          $get3 = $this->m_master->caribasedprimary('db_admission.register_document','ID_register_formulir',$row['ID_register_formulir']);
          /*
            7 = Admission Statement
            2 = 1 Fotocopi Raport Semester Terakhir Yang Dilegalisir
            3 = 1 Fotocopi Ijazah Yang Dilegalisir
            ID = ""
            5 = 3 Pas Foto Terbaru Ukuran 3x4 Dengan Warna Latar Belakang Merah
            9 = 1 Surat Rekomendasi Dari Sekolah
            8 = 1 Surat Pernyataan Bebas Narkoba
            6 = 1 Lembar Essay Mengenai
          */

            if (count($get3) > 0) {
                for ($j=0; $j < count($get3); $j++) {
                  switch ($get3[$j]['ID_reg_doc_checklist']) {
                    case 7:
                      if ($get3[$j]['Status'] == "Done") {
                        $arr_temp = $arr_temp + array('ads_sta' => 'v');
                      }
                      else
                      {
                        $arr_temp = $arr_temp + array('ads_sta' => '');
                      }
                      break;
                    case 2:
                      if ($get3[$j]['Status'] == "Done") {
                        $arr_temp = $arr_temp + array('raport' => 'v');
                      }
                      else
                      {
                        $arr_temp = $arr_temp + array('raport' => '');
                      }
                      break;
                    case 3:
                      if ($get3[$j]['Status'] == "Done") {
                        $arr_temp = $arr_temp + array('Ijazah' => 'v');
                      }
                      else
                      {
                        $arr_temp = $arr_temp + array('Ijazah' => '');
                      }
                      break;
                    case 5:
                      if ($get3[$j]['Status'] == "Done") {
                        $arr_temp = $arr_temp + array('Foto' => 'v');
                      }
                      else
                      {
                        $arr_temp = $arr_temp + array('Foto' => '');
                      }
                      break;
                    case 9:
                      if ($get3[$j]['Status'] == "Done") {
                        $arr_temp = $arr_temp + array('Refletter' => 'v');
                      }
                      else
                      {
                        $arr_temp = $arr_temp + array('Refletter' =>'');
                      }
                      break;
                    case 8:
                      if ($get3[$j]['Status'] == "Done") {
                        $arr_temp = $arr_temp + array('SuratNarkoba' => 'v');
                      }
                      else
                      {
                        $arr_temp = $arr_temp + array('SuratNarkoba' => '');
                      }
                      break;
                  case 6:
                    if ($get3[$j]['Status'] == "Done") {
                      $arr_temp = $arr_temp + array('Essay' => 'v');
                    }
                    else
                    {
                      $arr_temp = $arr_temp + array('Essay' => '');
                    }
                    break;
                    default:
                      # code...
                      break;
                  }
                }
            }
            else
            {
                $arr_temp = $arr_temp + array('ads_sta' => '');
                $arr_temp = $arr_temp + array('raport' => '');
                $arr_temp = $arr_temp + array('Ijazah' => '');
                $arr_temp = $arr_temp + array('Foto' => '');
                $arr_temp = $arr_temp + array('Refletter' =>'');
                $arr_temp = $arr_temp + array('SuratNarkoba' => '');
                $arr_temp = $arr_temp + array('Essay' => '');
            }  

            $query[$i] = $query[$i] + $arr_temp;

      } // exit loop

      return $query;
    }

    public function getHasilUjian($ID_register_formulir)
    {
        $sql = 'select d.ID as ID_hasil_ujian,c.ID as ID_register_formulir_jadwal_ujian,a.NamaUjian,a.Bobot,d.Value from db_admission.ujian_perprody_m as a
            join db_admission.register_jadwal_ujian as b
            on a.ID = b.ID_ujian_perprody
            join db_admission.register_formulir_jadwal_ujian as c
            on c.ID_register_jadwal_ujian = b.ID
            join db_admission.register_hasil_ujian as d
            on c.ID = d.ID_register_formulir_jadwal_ujian
            where c.ID_register_formulir = ? ';
        $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
        return $query;
    }

    public function getkelulusan($ID_register_formulir)
    {
        $sql = 'select * from db_admission.register_kelulusan_ujian where ID_register_formulir = ?';
        $query=$this->db->query($sql, array($ID_register_formulir))->result_array();
        return $query;
    }

    public function getDataCalonMhsTuitionFee_approved_ALL($Year,$Prodi,$Status = 'p.Status = "Created" or p.Status = "Approved"')
    {
      $Prodi = ($Prodi == 0) ? '' : ' and a.ID_program_study = "'.$Prodi.'"';
     $arr_temp = array();
     $sql= 'select a.ID as ID_register_formulir,a.ID_program_study,o.Name as NamePrody,d.Name,a.Gender,a.IdentityCard,e.ctr_name as Nationality,
             f.Religion,concat(a.PlaceBirth,",",a.DateBirth) as PlaceDateBirth,d.Email,n.SchoolName,l.sct_name_id as SchoolType,m.SchoolMajor,e.ctr_name as SchoolCountry,
             n.ProvinceName as SchoolProvince,n.CityName as SchoolRegion,n.SchoolAddress,a.YearGraduate,a.UploadFoto,
             if((select count(*) as total from db_admission.register_nilai where Status = "Verified" and ID_register_formulir = a.ID limit 1) > 0,"Rapor","Ujian")
             as status1,p.CreateAT,p.CreateBY,b.FormulirCode,p.TypeBeasiswa,p.FileBeasiswa,p.Desc,
             if(d.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = b.FormulirCode limit 1) ,(select No_Ref from db_admission.formulir_number_online_m where FormulirCode = b.FormulirCode limit 1)  ) as No_Ref,p.RevID,n.CityName as CitySchool
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
              where ('.$Status.') and d.SetTa = ? '.$Prodi.'
             group by a.ID order by a.ID desc';
     $query=$this->db->query($sql, array($Year))->result_array();
     $this->load->model('master/m_master');
     $jpa = $this->m_master->showData_array('db_admission.register_dsn_jpa');
     for ($i=0; $i < count($query); $i++) {
       $DiskonSPP = 0;
       // get Price
          $arr_temp2 = $this->tuitionFeeIntake_ALL2($query[$i]['ID_register_formulir']);
           // $getPaymentType_Cost = $this->getPaymentType_Cost_created($query[$i]['ID_register_formulir']);
           // $arr_temp2 = array();
           // for ($k=0; $k < count($getPaymentType_Cost); $k++) {
           //   // $arr_temp2 = $arr_temp2 + array($getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Cost']);
           //   $arr_temp2 = $arr_temp2 + array(
           //     $getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Pay_tuition_fee'],
           //     'Discount-'.$getPaymentType_Cost[$k]['Abbreviation'] => $getPaymentType_Cost[$k]['Discount']
           //   );
           // }

           // get file dan type beasiswa
            $getBeasiswa = $this->m_master->caribasedprimary('db_admission.register_dsn_type_m','ID',$query[$i]['TypeBeasiswa']);
            if (count($getBeasiswa) > 0) {
              $getBeasiswa = $getBeasiswa[0]['DiscountType'];
            }
            else
            {
             $getBeasiswa = '-';
            }

            // get File
            $getFile = $this->m_master->caribasedprimary('db_admission.register_document','ID',$query[$i]['FileBeasiswa']);
            if (count($getFile) > 0) {
              $getFile = $getFile[0]['Attachment'];
            }
            else
            {
             $getFile = '-';
            }

            // check Revision
            $rev = $this->m_master->caribasedprimary('db_finance.register_admisi_rev','ID_register_formulir',$query[$i]['ID_register_formulir']);

            // event
              $Event = "";
              $ee = $this->m_master->caribasedprimary('db_admission.sale_formulir_offline','FormulirCodeOffline',$query[$i]['FormulirCode']);
              if (count($ee) > 0) {
                $source_from_event_ID = $ee[0]['source_from_event_ID'];
                $ff = $this->m_master->caribasedprimary('db_admission.source_from_event','ID',$source_from_event_ID);
                if (count($ff) > 0) {
                  $Event = $ff[0]['src_name'];
                }
              }

       if ($query[$i]['status1'] == 'Rapor') {
         // check rangking
           $getRangking = $this->getRangking($query[$i]['ID_register_formulir']);
           $getRangking = $getRangking[0]['Rangking'];

           $arr_temp[$i] = array(
            'ID_register_formulir' => $query[$i]['ID_register_formulir'],
            'Name' => $query[$i]['Name'],
            'NamePrody' => $query[$i]['NamePrody'],
            'SchoolName' => $query[$i]['SchoolName'],
            'Status1' => $query[$i]['status1'],
            // 'DiskonSPP' => $DiskonSPP,
            'RangkingRapor' => $getRangking,
            'FormulirCode' => $query[$i]['FormulirCode'],
            'No_Ref' => $query[$i]['No_Ref'],
            'getBeasiswa' => $getBeasiswa,
            'getFile' => $getFile,
            'Email' => $query[$i]['Email'],
            'Desc' => $query[$i]['Desc'],
            'Rev' => count($rev),
            'CitySchool' => $query[$i]['CitySchool'],
            'Event' => $Event,
           );
       }
       else
       {
           $arr_temp[$i] = array(
             'ID_register_formulir' => $query[$i]['ID_register_formulir'],
             'Name' => $query[$i]['Name'],
             'NamePrody' => $query[$i]['NamePrody'],
             'SchoolName' => $query[$i]['SchoolName'],
             'Status1' => $query[$i]['status1'],
             // 'DiskonSPP' => $DiskonSPP,
             'RangkingRapor' => 0,
             'FormulirCode' => $query[$i]['FormulirCode'],
             'No_Ref' => $query[$i]['No_Ref'],
             'getBeasiswa' => $getBeasiswa,
             'getFile' => $getFile,
             'Email' => $query[$i]['Email'],
             'Desc' => $query[$i]['Desc'],
             'Rev'  =>count($rev),
             'CitySchool' => $query[$i]['CitySchool'],
             'Event' => $Event,
           );
       }

       $arr_temp[$i] = $arr_temp[$i] + $arr_temp2;
     }
     return $arr_temp;

    }

    public function insert_to_Library($arr_data)
    {
      $this->db_server22 = $this->load->database('server22', TRUE);
      $member_since_date = date('Y-m-d');
      $aa = explode("-", $member_since_date);
      $expire_date = $aa[0] + 4;
      $expire_date = $expire_date.'-'.$aa[1].'-'.$aa[2];
      for ($i=0; $i < count($arr_data); $i++) {
        $dataSave = array(
            'member_id' => $arr_data[$i]['NPM'],
            'member_name' => $arr_data[$i]['Name'],
            'gender' => 0,
            'birth_date' => $arr_data[$i]['DateOfBirth'],
            'member_type_id' => 2,
            'member_address' => $arr_data[$i]['Address'],
            'member_mail_address' => $arr_data[$i]['EmailPU'],
            'member_email' => $arr_data[$i]['Email'],
            'inst_name' => 'Podomoro University',
            'member_since_date' => $member_since_date,
            'register_date' => $member_since_date,
            'expire_date' => $expire_date,
            'mpasswd' => $arr_data[$i]['Password_Old'],
            'input_date' =>$member_since_date,
        );
        $this->db_server22->insert('library.member', $dataSave);
      }

    }

    public function cekDBLibraryExistSTD($NPM)
    {
      $this->db_server22 = $this->load->database('server22', TRUE);
      $sql = 'select count(*) as total from library.member where member_id = "'.$NPM.'" ';
      $query=$this->db_server22->query($sql, array())->result_array();
      if ($query[0]['total'] > 0) {
        return true;
      }
      else
      {
        return false;
      }
    }

    public function DelDBLibraryExistSTD($NPM)
    {
      $this->db_server22 = $this->load->database('server22', TRUE);
      $this->db_server22->where('member_id',$NPM);
      $this->db_server22->delete('library.member'); 
      if ($this->db_server22->affected_rows() > 0 )
      {
        return TRUE;
      }
      else
      {
        return FALSE;
      }
    }

    public function getIntakeByYear($Year,$datechoose = null)
    {
      $this->load->model('master/m_master');
      $this->load->model('statistik/m_statistik');
      $rs = array();
      // Summary Intake Tahun sebelumnya dan Tahun by variable Year
      // Tahun by variable Year dengan data satu minggu sebelumnya
      /*
        Find Faculty active by program study
      */
       $G_Faculty = $this->m_master->facultyActiveByProgramStudy();
       $GetDateNow = ($datechoose == null ) ? date('Y-m-d') : $datechoose;
       $DateFiltering = $GetDateNow;
       $GetDateNow = $this->m_master->getIndoBulan($GetDateNow);
       // get date satu minggu sebelumnya
       $MingguWr = $this->m_master->GetDateAfterOrBefore($DateFiltering,'-7');
       $DateFiltering2 = $MingguWr;
       $MingguWr = $this->m_master->getIndoBulan($MingguWr);

       // get date last year
       $DateFiltering2  = explode('-', $DateFiltering);
       $DateFiltering2 = ($DateFiltering2[0] - 1).'-'.($DateFiltering2[1]).'-'.($DateFiltering2[2]);
       $DateFiltering2Write = $this->m_master->getIndoBulan($DateFiltering2);
       // print_r($DateFiltering2);die();
       for ($i=0; $i < count($G_Faculty); $i++) {
         $FacultyID = $G_Faculty[$i]['FacultyID'];
         $temp['Faculty'] = $G_Faculty[$i]['Name'];
         $temp['Prodi'] = array();
         $temp['Value'] = array(
            array(
              'Label'=> 'Intake Tahun Akademik '.($Year-1).' ('.$DateFiltering2Write.')',
              'Detail' => array(),
            ),
            array(
              'Label'=> 'Intake Tahun Akademik '.($Year).' ('.$GetDateNow.')',
              'Detail' => array(),
            ),
            array(
              'Label'=> 'Intake Tahun Akademik '.($Year).' Minggu sebelumnya ('.$MingguWr.')',
              'Detail' => array(),
            ),
            array(
              'Label'=> 'Perubahan',
              'Detail' => array(),
            ),

         );
         // find Program Study
         $G_program_study = $this->m_master->caribasedprimary('db_academic.program_study','FacultyID',$FacultyID);
         $arr_temp = array();
         for ($j=0; $j < count($G_program_study); $j++) {
           $temp['Prodi'][] = $G_program_study[$j]['Name'];
           // cari intake tahun lalu dulu berdasarkan tannggal yang dipilih
              $ProdiID = $G_program_study[$j]['ID'];
              // check table existing
               $ChkTblExist = $this->m_statistik->table_Exist('rekapintakeadm_'.($Year-1));
               // year 2018 ke bawah datanya tidak ada
               if (($Year-1) <= 2018) {
                if ($ChkTblExist) {
                  $query = $this->m_master->caribasedprimary('db_statistik.rekapintakeadm_'.($Year-1),'ProdiID',$ProdiID);
                  $total = 0;
                  if (count($query) > 0) {
                     $total = $query[0]['Total'];
                  }
                  $c1 = $total;
                }
                else
                {
                  $c1 = 0;
                }
               }
               else
               {
                $sql = 'select count(*) as total from (
                         select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody,
                        e.ID as ID_register_formulir,
                        if(a.StatusReg = 1,
                        (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,"" ) as No_Ref,
                        if(a.StatusReg = 1,
                        (select DateFin from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,a.RegisterAT ) as intakedate,
                        (select count(*) as total from db_finance.payment_pre where Status = 1 and ID_register_formulir = e.ID and Datepayment <= "'.$DateFiltering2.'") as C_bayar
                        from db_admission.register as a
                        join db_admission.school as b on a.SchoolID = b.ID
                        LEFT JOIN db_admission.register_verification as z on a.ID = z.RegisterID
                        LEFT JOIN db_admission.register_verified as c on z.ID = c.RegVerificationID
                        LEFT JOIN db_admission.register_formulir as e on c.ID = e.ID_register_verified
                        LEFT join db_academic.program_study as d on e.ID_program_study = d.ID
                        left join db_admission.sale_formulir_offline as xz on c.FormulirCode = xz.FormulirCodeOffline
                         where a.SetTa = "'.($Year-1).'"
                        ) ccc where ID_program_study = ? and C_bayar > 0
                        and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
              join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
              join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
              join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
              join db_admission.register as reg on reg.ID = rve.RegisterID
              where SetTa = "'.($Year-1).'" and rr.UpdateAt <= "'.$DateFiltering2.'" )
                        ';
                        $query=$this->db->query($sql, array($ProdiID))->result_array();
                         $total = $query[0]['total'];
                         $c1 = $total;
               }

              $temp['Value'][0]['Detail'][] = $c1;

            // cari intake Tahun by variable Year
              // special data 2018 inject summary
              if ($Year == 2018) {
                $query = $this->m_master->caribasedprimary('db_statistik.rekapintakeadm_'.$Year,'ProdiID',$ProdiID);
                $total = $query[0]['Total'];
              }
              else
              {
                $sql = 'select count(*) as total from (
                         select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody,
                        e.ID as ID_register_formulir,
                        if(a.StatusReg = 1,
                        (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,"" ) as No_Ref,
                        if(a.StatusReg = 1,
                        (select DateFin from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,a.RegisterAT ) as intakedate,
                        (select count(*) as total from db_finance.payment_pre where Status = 1 and ID_register_formulir = e.ID and Datepayment <= "'.$DateFiltering.'") as C_bayar
                        from db_admission.register as a
                        join db_admission.school as b on a.SchoolID = b.ID
                        LEFT JOIN db_admission.register_verification as z on a.ID = z.RegisterID
                        LEFT JOIN db_admission.register_verified as c on z.ID = c.RegVerificationID
                        LEFT JOIN db_admission.register_formulir as e on c.ID = e.ID_register_verified
                        LEFT join db_academic.program_study as d on e.ID_program_study = d.ID
                        left join db_admission.sale_formulir_offline as xz on c.FormulirCode = xz.FormulirCodeOffline
                         where a.SetTa = "'.$Year.'"
                        ) ccc where ID_program_study = ? and C_bayar > 0
                                  and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
                        join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
                        join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
                        join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
                        join db_admission.register as reg on reg.ID = rve.RegisterID
                        where SetTa = "'.$Year.'" and rr.UpdateAt <= "'.$DateFiltering.'" )
                        ';

                        $query=$this->db->query($sql, array($ProdiID))->result_array();
                        $total = $query[0]['total'];
              }
                $c2 = $total;
                $temp['Value'][1]['Detail'][] = $total;

            // cari intake Tahun by variable Year 1 Minggu sebelumnya
                // special data 2018 inject summary
                if ($Year == 2018) {
                  $query = $this->m_master->caribasedprimary('db_statistik.rekapintakeadm_'.$Year,'ProdiID',$ProdiID);
                  $total = $query[0]['Total'];
                }
                else
                {
                  $sql = 'select count(*) as total from (
                           select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody,
                          e.ID as ID_register_formulir,
                          if(a.StatusReg = 1,
                          (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,"" ) as No_Ref,
                          if(a.StatusReg = 1,
                          (select DateFin from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,a.RegisterAT ) as intakedate,
                          (select count(*) as total from db_finance.payment_pre where Status = 1 and ID_register_formulir = e.ID and Datepayment <= DATE_SUB("'.$DateFiltering.'", INTERVAL 7 DAY) ) as C_bayar
                          from db_admission.register as a
                          join db_admission.school as b on a.SchoolID = b.ID
                          LEFT JOIN db_admission.register_verification as z on a.ID = z.RegisterID
                          LEFT JOIN db_admission.register_verified as c on z.ID = c.RegVerificationID
                          LEFT JOIN db_admission.register_formulir as e on c.ID = e.ID_register_verified
                          LEFT join db_academic.program_study as d on e.ID_program_study = d.ID
                          left join db_admission.sale_formulir_offline as xz on c.FormulirCode = xz.FormulirCodeOffline
                           where a.SetTa = "'.$Year.'"
                          ) ccc where ID_program_study = ? and C_bayar > 0
                                    and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
                          join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
                          join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
                          join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
                          join db_admission.register as reg on reg.ID = rve.RegisterID
                          where SetTa = "'.$Year.'" and rr.UpdateAt <= DATE_SUB("'.$DateFiltering.'", INTERVAL 7 DAY) )

                          ';

                          $query=$this->db->query($sql, array($ProdiID))->result_array();
                          $total = $query[0]['total'];
                }
                $temp['Value'][2]['Detail'][] = $total;

                // perubahan
                  $d = $c2 - $total;
                  $d = abs($d);
                  $temp['Value'][3]['Detail'][] = $d;
         }

         $rs[] = $temp;
       }

       return $rs;
    }

    public function proses_agregator_seleksi_mhs_baru($Year,$Nasional=null) // Ket Nasional => Null = All, Nasinal dan Internasional = asing
    {
     
      /*
        Data yang ada pada database adalah data dari 2019 keatas
        Data yang 2019 kebawah dinputkan
        Field : 
          -- Daya tampung => table db_admission.ta_setting
          -- Pendaftar => Yang mengembalikan formulir
                          -> table db_admission.register_formulir
          -- Lulus Seleksi => Yang di generate to be mhs
                          -> table db_admission.to_be_mhs
          -- Jml Mhs Baru -> sama dengan lulus seleksi
          -- jml mhs -> sama dengan lulus seleksi
          -- transfer -> 0
          -- transfer -> 0
      */

        // cek data exist 
        $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
        for ($i=0; $i < count($G_prodi); $i++) { 
          $ProdiID = $G_prodi[$i]['ID'];
          $this->proses_agregator_seleksi_mhs_baru_by_prodi($Year,$ProdiID,$Nasional);
        }
         

    }

    public function proses_agregator_seleksi_mhs_baru_by_prodi($Year,$ProdiID,$Nasional=null)
    {
      $q_add = ($Nasional == null) ? '' : ' and Type = "'.$Nasional.'" ';
      $sql = 'select * from db_agregator.student_selection where Year = ? and ProdiID = ? '.$q_add;
      $query=$this->db->query($sql, array($Year,$ProdiID))->result_array();
      if (count($query) == 0) {
        $G_capacity = function($ProdiID,$Year){
           $sql2 = 'select b.Capacity from db_admission.crm_period as a
                   join db_admission.ta_setting as b on a.ID = b.ID_crm_period
                   where a.Year = ? and b.ProdiID = ?
                  ';
           $query2=$this->db->query($sql2, array($Year,$ProdiID))->result_array();
           return $query2[0]['Capacity'];
        };

        $G_Pendaftar = function($ProdiID,$Year,$Nasional){
          $q_add = '';
          if ($Nasional != null && $Nasional != '') {
            if ($Nasional == 'Nasional') {
              $q_add = ' and d.NationalityID = "001" ';
            }
            else
            {
               $q_add = ' and d.NationalityID != "001" ';
            }
          }
           $sql2 = 'select count(*) as total from (
             select a.ID from db_admission.register as a
             join db_admission.register_verification as b on a.ID = b.RegisterID
             join db_admission.register_verified as c on b.ID = c.RegVerificationID
             join db_admission.register_formulir as d on c.ID = d.ID_register_verified
             where a.SetTa = ? and d.ID_program_study = ? '.$q_add.'
             ) xx';
           $query2=$this->db->query($sql2, array($Year,$ProdiID))->result_array();
           return $query2[0]['total'];
        };

        $G_lulus = function($ProdiID,$Year,$Nasional)
        {
          $q_add = '';
          if ($Nasional != null && $Nasional != '') {
            if ($Nasional == 'Nasional') {
              $q_add = ' and d.NationalityID = "001" ';
            }
            else
            {
               $q_add = ' and d.NationalityID != "001" ';
            }
          }
           $sql2 = 'select count(*) as total from (
             select a.ID from db_admission.register as a
             join db_admission.register_verification as b on a.ID = b.RegisterID
             join db_admission.register_verified as c on b.ID = c.RegVerificationID
             join db_admission.register_formulir as d on c.ID = d.ID_register_verified
             join db_admission.to_be_mhs as e on e.FormulirCode = c.FormulirCode
             where a.SetTa = ? and d.ID_program_study = ? '.$q_add.'
             ) xx';
           $query2=$this->db->query($sql2, array($Year,$ProdiID))->result_array();
           return $query2[0]['total'];
        };

        $Capacity = $G_capacity($ProdiID,$Year,$Nasional);
        $EntredAt = date('Y-m-d H:i:s');
        $EntredBy = '0';
        $PassSelection = $G_lulus($ProdiID,$Year,$Nasional);
        // $ProdiID = '';
        $ProdiName = '';
        $Registrant = $G_Pendaftar($ProdiID,$Year,$Nasional);
        $Regular = $PassSelection;
        $Regular2 = $PassSelection;
        $TotalStudemt = $PassSelection;
        $Transfer = 0;
        $Transfer2 = 0;
        $Type = ($Nasional == null) ? 'Nasional' : $Nasional;
        $dataSave = [
          'Capacity' => $Capacity,
          'EntredAt' => $EntredAt,
          'EntredBy' => $EntredBy,
          'PassSelection' => $PassSelection,
          'ProdiID' => $ProdiID,
          'Registrant' => $Registrant,
          'Regular' => $Regular,
          'Regular2' => $Regular2,
          'TotalStudemt' => $TotalStudemt,
          'Transfer' => $Transfer,
          'Transfer2' => $Transfer2,
          'Type' => $Type,
          'Year' => $Year,
        ];

        $this->db->insert('db_agregator.student_selection',$dataSave);
      }
      else
      {
         $ID = $query[0]['ID'];
        /*
          angkatan >= 2019
            Data Capacity Fix dari Admission
            Pendaftar & lulus ambil dari data admission (auto update)
        */

        if ($Year >= 2019) {
          $G_capacity = function($ProdiID,$Year){
             $sql2 = 'select b.Capacity from db_admission.crm_period as a
                     join db_admission.ta_setting as b on a.ID = b.ID_crm_period
                     where a.Year = ? and b.ProdiID = ?
                    ';
             $query2=$this->db->query($sql2, array($Year,$ProdiID))->result_array();
             return $query2[0]['Capacity'];
          };

          $G_Pendaftar = function($ProdiID,$Year,$Nasional){
            $q_add = '';
            if ($Nasional != null && $Nasional != '') {
              if ($Nasional == 'Nasional') {
                $q_add = ' and d.NationalityID = "001" ';
              }
              else
              {
                 $q_add = ' and d.NationalityID != "001" ';
              }
            }
             $sql2 = 'select count(*) as total from (
               select a.ID from db_admission.register as a
               join db_admission.register_verification as b on a.ID = b.RegisterID
               join db_admission.register_verified as c on b.ID = c.RegVerificationID
               join db_admission.register_formulir as d on c.ID = d.ID_register_verified
               where a.SetTa = ? and d.ID_program_study = ? '.$q_add.'
               ) xx';
             $query2=$this->db->query($sql2, array($Year,$ProdiID))->result_array();
             return $query2[0]['total'];
          };

          $G_lulus = function($ProdiID,$Year,$Nasional)
          {
            $q_add = '';
            if ($Nasional != null && $Nasional != '') {
              if ($Nasional == 'Nasional') {
                $q_add = ' and d.NationalityID = "001" ';
              }
              else
              {
                 $q_add = ' and d.NationalityID != "001" ';
              }
            }
             $sql2 = 'select count(*) as total from (
               select a.ID from db_admission.register as a
               join db_admission.register_verification as b on a.ID = b.RegisterID
               join db_admission.register_verified as c on b.ID = c.RegVerificationID
               join db_admission.register_formulir as d on c.ID = d.ID_register_verified
               join db_admission.to_be_mhs as e on e.FormulirCode = c.FormulirCode
               where a.SetTa = ? and d.ID_program_study = ? '.$q_add.'
               ) xx';
             $query2=$this->db->query($sql2, array($Year,$ProdiID))->result_array();
             return $query2[0]['total'];
          };

          $Capacity = $G_capacity($ProdiID,$Year,$Nasional);
          $PassSelection = $G_lulus($ProdiID,$Year,$Nasional);
          $Registrant = $G_Pendaftar($ProdiID,$Year,$Nasional);
          $Regular = $PassSelection;
          $Regular2 = $PassSelection;
          $TotalStudemt = $PassSelection;

          $dataSave = [
            'Capacity' => $Capacity,
            'PassSelection' => $PassSelection,
            'Registrant' => $Registrant,
            'Regular' => $Regular,
            'Regular2' => $Regular2,
            'TotalStudemt' => $TotalStudemt,
          ];

          $this->db->where('ID',$ID);
          $this->db->update('db_agregator.student_selection',$dataSave);

        }
        else
        {
          $G_capacity = function($ProdiID,$Year){
             $sql2 = 'select b.Capacity from db_admission.crm_period as a
                     join db_admission.ta_setting as b on a.ID = b.ID_crm_period
                     where a.Year = ? and b.ProdiID = ?
                    ';
             $query2=$this->db->query($sql2, array($Year,$ProdiID))->result_array();
             return $query2[0]['Capacity'];
          };

          $dataSave = [
            'Capacity' => $G_capacity($ProdiID,$Year),
          ];

          $this->db->where('ID',$ID);
          $this->db->update('db_agregator.student_selection',$dataSave);
        }
      }
    }

    public function DetaiLResendEmail($RegisterID){
      $query = $this->db->query(
        'select a.*,c.Name as NameEmployee from db_admission.register_resend_email as a 
         join db_admission.register_send_email as b on a.ID_register_send_email = b.ID_register_send_email
         join db_employees.employees as c on c.NIP = a.Update_by
         where b.RegisterID = '.$RegisterID.' 
        '
      )->result_array();
      return $query;
    }

    public function DataEmailSend($RegisterID){
      $rs = '';
      $this->load->library('JWT');
      $G_dt = $this->m_master->caribasedprimary('db_admission.register_send_email','RegisterID',$RegisterID);
      if (count($G_dt) > 0) {
        $EmailData = $G_dt[0]['EmailData'];
        $EmailData = json_decode($EmailData,true);
        $rs = $this->jwt->encode($EmailData,"UAP)(*");
      }
      else
      {
        $G_registerID = $this->m_master->caribasedprimary('db_admission.register','ID',$RegisterID);
        $subject = "Podomoro University Registration";

        $datetime_expired = $G_registerID[0]['Datetime_expired'];
        $Name = $G_registerID[0]['Name'];

        $text = array($G_registerID[0]['MomenUnix'],$G_registerID[0]['PriceFormulir'],$datetime_expired,$Name);
        $EmailData = $this->get_email_admission_no_record($G_registerID[0]['Email'],$subject,$text);
        $rs = $this->jwt->encode($EmailData,"UAP)(*");
      }

      return $rs;
    }

    private function get_email_admission_no_record($to,$subject,$text){
      $arr = [];
      $this->load->model('m_sendemail');
      $config_email = $this->m_sendemail->loadEmailConfig();
      $getDeadline = $text[2];

      $msg = '<div style="margin:0;padding:10px 0;background-color:#ebebeb;font-size:14px;line-height:20px;font-family:Helvetica,sans-serif;width:100%">
                  <div class="adM">
                  <br>
                  </div>
                  <table style="width:600px;margin:0 auto;background-color:#ebebeb" border="0" cellpadding="0" cellspacing="0">
                      <tbody>
                      <tr>
                          <td></td>
                          <td style="background-color:#fff;padding:0 30px;color:#333;vertical-align:top;border:1px solid #cccccc;">
                          <br>
                          <div style="text-align: center;">
                              <img src="https://lh3.googleusercontent.com/mkqZdtpCm7IfWWrPdfxJBETqOTiEU09s3cr4tzfFwAGRl3WqH_pyo3yDGPKmpSHfMw1mSFU0JTRk-3yX9M7xAG5KiVHzuMS1DPHzFg=w500-h144-rw-no" style="max-width: 250px;">
                              <hr style="border-top: 1px solid #cccccc;"/>
                              <div style="font-family:Proxima Nova Semi-bold,Helvetica,sans-serif;font-weight:bold;font-size:24px;line-height:24px;color:#607D8B">Registration</div>
                          </div>
                          <br/>';
      $msg .= $config_email['text'];
      $style1 = '<div style="background: #00bcd414;border: 1px solid #2196f36e;min-height: 50px;width: 400px;margin: 0 auto;text-align: center;padding: 10px;">
                  <b style="color: blue;">Rek BCA : 161.3888.555</b>
                  <div style="color: red;"><b>';
      $style2 = '</b></div>
                  <span style="color: #827f7f;">';
      $style3 = '</span>
              </div>';
      $style4 = '<div style="background: #ffeb3b47;border: 1px solid #2196f36e;min-height: 10px;width: 270px;margin: 0 auto;text-align: center;padding: 10px;margin-top: 10px;">
                  <b style="color: #333;font-size: 16px;">';
      $style5   = '</b>
              </div>'; 
      $payment = "Rp. ".number_format($text[1],2,",",".");
      $msg = str_replace('[#Candidate]', $text[3], $msg);
      $msg = str_replace('[#payment]', $payment, $msg);
      $deadline = $getDeadline;
      $deadline = explode(' ', $deadline);
      $deadline0 = $this->m_master->getIndoBulan($deadline[0]);
      $deadline1 = $deadline[1];
      $deadline = $deadline0.' Time : '.$deadline1;
      
      $NoRek = "Atas Nama : Yayasan Pendidikan Agung Podomoro";

      // $msg = str_replace('[#deadline]', $deadline, $msg);

      $msg = str_replace('[#VA]', $NoRek, $msg);
      $msg = str_replace("[#styleheader1]", $style1, $msg);
      $msg = str_replace("[#styleheader2]", $style2, $msg);
      // $msg = str_replace("[#styleheader3]", $style3, $msg);
      $msg = str_replace("[#styleheader4]", $style4, $msg);
      $msg = str_replace("[#styleheader5]", $style5, $msg);

      $msg .= '<div style="background: #efefef; padding: 10px;border: 1px solid #cccccc;">
                  <strong>Note :</strong>
                  If we do not receive your payment until the time limit specified then Your Account will be suspended
              </div>
              <br><br>
              <p style="color:#EB6936;"><i>*) Do not reply, this email is sent automatically</i> </p>';
      $arr = [
        'to' => $to,
        'subject' => $subject,
        'msg' => $msg,
      ];

      return $arr;        
    }

    public function ResendEmail($RegisterID){
      $rs = ['status' => 0,'msg' => ''];
      $this->load->model('m_sendemail');
      // get old email
      $ID_register_send_email = NULL;
      $G_dt = $this->m_master->caribasedprimary('db_admission.register_send_email','RegisterID',$RegisterID);
      if (count($G_dt) > 0) {
        $EmailData = $G_dt[0]['EmailData'];
        $EmailData = json_decode($EmailData,true);
        $sendEmail = $this->m_sendemail->ResendEmail($EmailData['to'],$EmailData['subject'],null,null,null,null,$EmailData['msg']);
        $ID_register_send_email = $G_dt[0]['ID_register_send_email'];
      }
      else
      {
        // send email current data dan save data di register_send_email
        $G_registerID = $this->m_master->caribasedprimary('db_admission.register','ID',$RegisterID);
        $subject = "Podomoro University Registration";

        $datetime_expired = $G_registerID[0]['Datetime_expired'];
        $Name = $G_registerID[0]['Name'];

        $text = array($G_registerID[0]['MomenUnix'],$G_registerID[0]['PriceFormulir'],$datetime_expired,$Name);
        $sendEmail = $this->m_sendemail->resend_email_admission_no_record($G_registerID[0]['Email'],$subject,$text);
// as
        $dataSave = [
          'RegisterID' => $RegisterID,
          'EmailData' => json_encode($sendEmail['email']),
        ];

        $this->db->insert('db_admission.register_send_email',$dataSave);
        $ID_register_send_email = $this->db->insert_id();
      }

      if (isset($sendEmail) &&  !empty($sendEmail) && $sendEmail['status'] == 1 ) {
        // insert data to register_resend_email
        $dataSave = [
          'ID_register_send_email' => $ID_register_send_email,
          'Update_at' => date('Y-m-d H:i:s'),
          'Update_by' => $this->session->userdata('NIP'),
        ];

        $this->db->insert('db_admission.register_resend_email',$dataSave);
        $rs['status'] = 1;

      }

      return $rs;
    }

    private function getCountloadRefundData($ClassOf,$requestData){
     return  $q =  $this->db->query(
        'select count(*) as total from (
            select yy.* from (
                select a.ID as RegisterID,a.Name,a.SchoolID,a.Phone,b.SchoolName,a.Email,a.RegisterAT,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, e.ID as ID_register_formulir,
                if(a.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,(select No_Ref from db_admission.formulir_number_online_m where FormulirCode = c.FormulirCode limit 1)  ) as No_Ref,a.StatusReg,
                if(
                  (select count(*) as total from db_finance.payment_pre where `Status` = 1 and ID_register_formulir = e.ID ) > 0,"Intake","Not Intake"
                ) as CekIntake
                from db_admission.register as a
                LEFT join db_admission.school as b
                on a.SchoolID = b.ID
                LEFT JOIN db_admission.register_verification as z
                on a.ID = z.RegisterID
                LEFT JOIN db_admission.register_verified as c
                on z.ID = c.RegVerificationID
                LEFT JOIN db_admission.register_formulir as e
                on c.ID = e.ID_register_verified
                LEFT join db_academic.program_study as d
                on e.ID_program_study = d.ID
                LEFT join db_admission.register_rangking as f
                on e.ID = f.ID_register_formulir
                left join db_admission.sale_formulir_offline as xz
                  on c.FormulirCode = xz.FormulirCodeOffline
                LEFT JOIN db_employees.employees as xx
                on xz.PIC = xx.NIP
                LEFT JOIN db_finance.register_admisi as xy
                on e.ID = xy.ID_register_formulir
                LEFT JOIN db_admission.register_dsn_type_m as xq
                on xq.ID = xy.TypeBeasiswa
                where a.SetTa = "'.$ClassOf.'"
            ) as yy
            join db_finance.register_refund as rr on yy.ID_register_formulir = rr.ID_register_formulir
            join db_employees.employees as EMP on rr.UpdateBy = EMP.NIP
            where yy.CekIntake = "Intake" and 
            (
              yy.Name LIKE "'.$requestData['search']['value'].'%" or yy.NamePrody LIKE "%'.$requestData['search']['value'].'%" or yy.FormulirCode LIKE "'.$requestData['search']['value'].'%"      
                      or yy.No_Ref LIKE "'.$requestData['search']['value'].'%"
            )
         )xx;

        '
      )->result_array()[0]['total'];
    }

    public function loadRefundData($ClassOf,$requestData){
      $this->load->model('finance/m_finance');
      $No = $requestData['start'] + 1;
      $totalData = $this->getCountloadRefundData($ClassOf,$requestData);

      $sql = 'select yy.*,rr.Price as RefundPrice,rr.Desc as RefundDesc,EMP.Name as NameEMP_by,rr.UpdateAt from (
              select a.ID as RegisterID,a.Name,a.SchoolID,a.Phone,b.SchoolName,a.Email,a.RegisterAT,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, e.ID as ID_register_formulir,e.UploadFoto,
              xq.DiscountType,
              if(f.Rangking > 0 ,f.Rangking,"-") as Rangking,
              if(
                  (select count(*) as total from db_finance.payment_pre where `Status` = 0 and ID_register_formulir = e.ID limit 1) = 0 ,
                      if((select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID limit 1)
                           > 0 ,"Lunas","-"
                        )
                      ,
                      "Belum Lunas"
                ) as chklunas,
              (select count(*) as total from db_finance.payment_pre as aaa where aaa.ID_register_formulir =  e.ID ) as Cicilan
              ,xx.Name as NameSales,
              if(a.StatusReg = 1, (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,(select No_Ref from db_admission.formulir_number_online_m where FormulirCode = c.FormulirCode limit 1)  ) as No_Ref,a.StatusReg,
              if(
                (select count(*) as total from db_finance.payment_pre where `Status` = 1 and ID_register_formulir = e.ID ) > 0,"Intake","Not Intake"
              ) as CekIntake
              from db_admission.register as a
              LEFT join db_admission.school as b
              on a.SchoolID = b.ID
              LEFT JOIN db_admission.register_verification as z
              on a.ID = z.RegisterID
              LEFT JOIN db_admission.register_verified as c
              on z.ID = c.RegVerificationID
              LEFT JOIN db_admission.register_formulir as e
              on c.ID = e.ID_register_verified
              LEFT join db_academic.program_study as d
              on e.ID_program_study = d.ID
              LEFT join db_admission.register_rangking as f
              on e.ID = f.ID_register_formulir
              left join db_admission.sale_formulir_offline as xz
                on c.FormulirCode = xz.FormulirCodeOffline
              LEFT JOIN db_employees.employees as xx
              on xz.PIC = xx.NIP
              LEFT JOIN db_finance.register_admisi as xy
              on e.ID = xy.ID_register_formulir
              LEFT JOIN db_admission.register_dsn_type_m as xq
              on xq.ID = xy.TypeBeasiswa
              where a.SetTa = "'.$ClassOf.'"
            ) yy
            join db_finance.register_refund as rr on yy.ID_register_formulir = rr.ID_register_formulir
            join db_employees.employees as EMP on rr.UpdateBy = EMP.NIP
          ';

        $sql.= 'where yy.CekIntake = "Intake" and 
            (
              yy.Name LIKE "'.$requestData['search']['value'].'%" 
              or yy.NamePrody LIKE "%'.$requestData['search']['value'].'%" 
              or yy.FormulirCode LIKE "'.$requestData['search']['value'].'%"      
              or yy.No_Ref LIKE "'.$requestData['search']['value'].'%"
            )
                ';
        $sql.= ' ORDER BY RegisterID DESC LIMIT '.$requestData['start'].' ,'.$requestData['length'].' '; 

        $query = $this->db->query($sql)->result_array();
        $data = array();
        for($i=0;$i<count($query);$i++){
            $nestedData=array();
            $row = $query[$i];
            $nestedData[] = $No;
            $nestedData[] = [
              'No_Ref' => $row['No_Ref'],
              'FormulirCode' => $row['FormulirCode'],
              'Name' => $row['Name'],
              'Email' => $row['Email'],
            ];
            $ID_register_formulir =  $row['ID_register_formulir'];
            $__Payment = function($ID_register_formulir){
              $d = $this->m_finance->checkPayment_admisi($ID_register_formulir);
              return $d;
            };

            $nestedData[] = $__Payment($ID_register_formulir);
            $nestedData[] = $row['RefundPrice'];
            $nestedData[] = $row['RefundDesc'];
            $nestedData[] = $row['NameEMP_by'];
            $nestedData[] = $row['UpdateAt'];
            $nestedData[] = [];
            $data[] = $nestedData;
            $No++;

        }

        return $json_data = array(
            "draw"            => intval( $requestData['draw'] ),
            "recordsTotal"    => intval($totalData),
            "recordsFiltered" => intval($totalData ),
            "data"            => $data
        );
        
    }

    public function sumPotonganLainBydata($data){
      $total = 0;
      for ($i=0; $i < count($data); $i++) { 
        $total += $data[$i]['DiscountValue'];
      }

      return $total;
    }

    public function checkStepAfterFormulir($ID_register_verified = NULL,$FormulirCode=NULL){
      $param = ['ID_register_verified'=> $ID_register_verified,'FormulirCode' => $FormulirCode,'ID_register_formulir' => ''];
      $arr_Tbl_WhereSearch = [
        [
           'tbl' => 'db_admission.register_formulir',
           'primary' => 'ID_register_verified',
           'desc' => 'Process Data Formulir',
           'required' => 1,
           'relationTbl' => [],
           'return' => 'ID_register_formulir',
           'status' => ['status' => 0,'msg' => ''],
           'action' => 'No Action',
        ],
        [
           'tbl' => 'db_admission.register_document',
           'primary' => 'ID_register_formulir',
           'desc' => 'Process Data Document',
           'required' => 0,
           'relationTbl' => [],
           'return' => '',
           'status' => ['status' => 0,'msg' => ''],
           'action' => 'No Action',
        ],
        [
           'tbl' => 'db_admission.register_nilai',
           'primary' => 'ID_register_formulir',
           'desc' => 'Process Data Nilai',
           'required' => 1,
           'relationTbl' => ['db_admission.register_rangking','db_admission.register_nilai_fin'], 
           'return' => '',
           'status' => ['status' => 0,'msg' => ''],
           'action' => 'No Action',
        ],
        [
           'tbl' => 'db_finance.register_admisi',
           'primary' => 'ID_register_formulir',
           'desc' => 'Process Data Tuition Fee',
           'required' => 1,
           'relationTbl' => [
              'db_finance.payment_admisi','db_finance.payment_pre','db_finance.register_admisi_rev'
           ],
           'return' => '', 
           'status' => ['status' => 0,'msg' => ''], 
           'action' => 'No Action',
        ],
        [
           'tbl' => 'db_admission.to_be_mhs',
           'primary' => 'FormulirCode',
           'desc' => 'Process Data To Be MHS',
           'required' => 1,
           'relationTbl' => [],
           'return' => '',
           'status' => ['status' => 0,'msg' => ''],  
           'action' => 'No Action',
        ],
        
      ];

      for ($i=0; $i < count($arr_Tbl_WhereSearch); $i++) { 
        $arrTable = $arr_Tbl_WhereSearch[$i];
        $tbl = $arrTable['tbl'];
        $primary = $arrTable['primary'];
        $desc = $arrTable['desc'];
        $required = $arrTable['required'];
        $relationTbl = $arrTable['relationTbl'];
        
        $getData =  $this->m_master->caribasedprimary($tbl,$primary,$param[$primary]);
        // print_r($tbl);
        if (count($getData) > 0) {
          if ($arrTable['return'] != '') {
            $param[$arrTable['return']] = ($tbl == 'db_admission.register_formulir') ? $getData[0]['ID'] :$getData[0][$arrTable['return']];
          }

          $arr_Tbl_WhereSearch[$i]['status']['status'] = 1;
          $arr_Tbl_WhereSearch[$i]['status']['msg'] = $desc .' finish';

          $arr_Tbl_WhereSearch[$i]['param'] = $param;
        }
        else
        {
          $arr_Tbl_WhereSearch[$i]['status']['msg'] = $desc .' unfinish';
          $arr_Tbl_WhereSearch[$i]['param'] = $param;
        }

      }

      return $arr_Tbl_WhereSearch;
    }

}
