<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class M_statistik extends CI_Model {

    public $data = array();
    public $Year = array();

    function __construct()
    {
        parent::__construct();
        $this->load->model('m_master');
        $this->db_statistik = $this->load->database('statistik', true);
    }

    public function droptablerekapintake($Year)
    {
        $Table = 'rekapintake_'.$Year;
        $this->db_statistik->query('DROP TABLE IF EXISTS '.$Table);
    }

    public function droptablerekapintake_admission($Year)
    {
        $Table = 'rekapintakeadm_'.$Year;
        $this->db_statistik->query('DROP TABLE IF EXISTS '.$Table);
    }

    public function droptable($tblname)
    {
        $this->db_statistik->query('DROP TABLE IF EXISTS '.$tblname);
    }

    public function ShowRekapIntake($Year)
    {
        $arr_result = array();
        // passing variable to global
        $this->Year = $Year;

        //check table rekap exist
        $chk = $this->tableRekapExist($Year);
        if ($chk) {
            //show data in table
            $arr_result = $this->m_master->showData_array('db_statistik.rekapintake_'.$this->Year);
        }
        else
        {
            // create table
            $this->create_table();
            // insert data 
            $this->insert_data();
            $arr_result = $this->m_master->showData_array('db_statistik.rekapintake_'.$this->Year);
        }

        return $arr_result;
    }

    public function ShowRekapIntake_admission($Year)
    {
        $arr_result = array();
        // passing variable to global
        $this->Year = $Year;

        //check table rekap exist
        $chk = $this->tableRekap_admissionExist($Year);
        if ($chk) {
            //show data in table
            $arr_result = $this->m_master->showData_array('db_statistik.rekapintakeadm_'.$this->Year);
        }
        else
        {
            // create table
            $this->create_table_Rekap_admission();
            // insert data 
            $this->insert_data_Rekap_admission();
            $arr_result = $this->m_master->showData_array('db_statistik.rekapintakeadm_'.$this->Year);
        }

        return $arr_result;
    }

    private function insert_data()
    {
        $Year = $this->Year;
        $Table = 'rekapintake_'.$Year;
        $getColoumn = $this->m_master->getColumnTable('db_statistik.'.$Table);
        $getProdi = $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
        $arr_bulan = array(
            'Jan','Feb','March','April','May','June','July','August','Sep','Oct','Nov','Des'
        );
        for ($i=0; $i < count($getProdi); $i++) { 
            $ProdiID = $getProdi[$i]['ID'];
           // get per month
            $datasave = array('ProdiID' => $ProdiID);
            $field = $getColoumn['field'];
            for ($j=0; $j < count($field); $j++) { 
                if ($field[$j] != 'ID' && $field[$j] != 'ProdiID') {
                    $aa =  $field[$j];
                    if (strpos($aa, '_') !== false ) {
                        $monthQ = substr($aa, 0,strpos($aa, '_'));
                        $YearQ = $Year - 1;
                    }
                    else
                    {
                        $monthQ = $aa;
                        $YearQ = $Year;
                    }

                    // find  month number
                    for ($z=0; $z < count($arr_bulan); $z++) { 
                        if ($arr_bulan[$z] == $monthQ) {
                            $monthQ = $z  + 1;
                            break;
                        }
                    }
                    
                    $sql = 'select count(*) as total from (
                             select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, 
                            e.ID as ID_register_formulir,
                            if(a.StatusReg = 1, 
                            (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,"" ) as No_Ref,
                            if(a.StatusReg = 1, 
                            (select DateFin from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,a.RegisterAT ) as intakedate,
                            (select count(*) as total from db_finance.payment_pre where Status = 1 and ID_register_formulir = e.ID ) as C_bayar
                            from db_admission.register as a 
                            left join db_admission.school as b on a.SchoolID = b.ID 
                            LEFT JOIN db_admission.register_verification as z on a.ID = z.RegisterID 
                            LEFT JOIN db_admission.register_verified as c on z.ID = c.RegVerificationID 
                            LEFT JOIN db_admission.register_formulir as e on c.ID = e.ID_register_verified 
                            LEFT join db_academic.program_study as d on e.ID_program_study = d.ID 
                            left join db_admission.sale_formulir_offline as xz on c.FormulirCode = xz.FormulirCodeOffline  
                             where a.SetTa = "'.$Year.'" 
                            ) ccc where MONTH(intakedate) = "'.$monthQ.'" and Year(intakedate) = "'.$YearQ.'" and ID_program_study = ? and C_bayar > 0 and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
              join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
              join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
              join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
              join db_admission.register as reg on reg.ID = rve.RegisterID
              where reg.SetTa = "'.$Year.'" )';
                            $query=$this->db->query($sql, array($ProdiID))->result_array();
                            $total = $query[0]['total'];
                            $datasave[$field[$j]] = $total;
                            // if ($j == 3) {
                            //     print_r($YearQ);die();
                            // }
                            // print_r($monthQ);die();
                    
                }
            }
            // print_r($datasave);die();
            $this->db_statistik->insert($Table, $datasave);
        }
        $this->saveLastUpdated($Table);
    }

    private function insert_data_Rekap_admission()
    {
        $Year = $this->Year;
        $Table = 'rekapintakeadm_'.$Year;
        $getColoumn = $this->m_master->getColumnTable('db_statistik.'.$Table);
        $getProdi = $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
        $arr_bulan = array(
            'Jan','Feb','March','April','May','June','July','August','Sep','Oct','Nov','Des'
        );
        for ($i=0; $i < count($getProdi); $i++) { 
            $ProdiID = $getProdi[$i]['ID'];
           // get per month
            $datasave = array('ProdiID' => $ProdiID);
            $sql = 'select count(*) as total from (
                     select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, 
                    e.ID as ID_register_formulir,
                    if(a.StatusReg = 1, 
                    (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,"" ) as No_Ref,
                    if(a.StatusReg = 1, 
                    (select DateFin from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,a.RegisterAT ) as intakedate,
                    (select count(*) as total from db_finance.payment_pre where Status = 1 and ID_register_formulir = e.ID ) as C_bayar
                    from db_admission.register as a 
                    left join db_admission.school as b on a.SchoolID = b.ID 
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
                                  where reg.SetTa = "'.$Year.'" )
                    ';
                    $query=$this->db->query($sql, array($ProdiID))->result_array();
                    $total = $query[0]['total'];
                    $datasave['Total'] = $total;
            $this->db_statistik->insert($Table, $datasave);
        }
        $this->saveLastUpdated($Table);
    }

    private function saveLastUpdated($Table)
    {
        $datasave = array(
            'TableName' => $Table,
            'LastUpdated' => date('Y-m-d H:i:s'),
        );
        $c = $this->m_master->caribasedprimary('db_statistik.lastupdated','TableName',$Table);
        if (count($c) > 0) {
            $dataSave = array(
                'lastupdated' => date('Y-m-d H:i:s'),
            );
            $this->db->where('TableName', $Table);
            $this->db->update('db_statistik.lastupdated', $dataSave);
        }
        else
        {
            $this->db_statistik->insert('lastupdated', $datasave);
        }
    }

    private function create_table()
    {
        $Year = $this->Year;
        $Table = 'rekapintake_'.$Year;
        $this->db_statistik->query('CREATE TABLE '.$Table.' (
                                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                                  `ProdiID` int(11) DEFAULT NULL,
                                  `August_` int(11) DEFAULT NULL,
                                  `Sep_` int(11) DEFAULT NULL,
                                  `Oct_` int(11) DEFAULT NULL,
                                  `Nov_` int(11) DEFAULT NULL,
                                  `Des_` int(11) DEFAULT NULL,
                                  `Jan` int(11) DEFAULT NULL,
                                  `Feb` int(11) DEFAULT NULL,
                                  `March` int(11) DEFAULT NULL,
                                  `April` int(11) DEFAULT NULL,
                                  `May` int(11) DEFAULT NULL,
                                  `June` int(11) DEFAULT NULL,
                                  `July` int(11) DEFAULT NULL,
                                  `August` int(11) DEFAULT NULL,
                                  `Sep` int(11) DEFAULT NULL,
                                  `Oct` int(11) DEFAULT NULL,
                                  PRIMARY KEY (`ID`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1');
    }

    private function create_table_Rekap_admission()
    {
        $Year = $this->Year;
        $Table = 'rekapintakeadm_'.$Year;
        $this->db_statistik->query('CREATE TABLE '.$Table.' (
                                  `ID` int(11) NOT NULL AUTO_INCREMENT,
                                  `ProdiID` int(11) DEFAULT NULL,
                                  `Total` int(11) DEFAULT NULL,
                                  PRIMARY KEY (`ID`)
                                ) ENGINE=InnoDB DEFAULT CHARSET=latin1');
    }

    public function tableRekapExist($Year)
    {
        $sql = 'show tables like "rekapintake_'.$Year.'";';
        $query=$this->db_statistik->query($sql, array())->result_array();
        if (count($query) > 0) {
            return true;
        }
        else
        {
            return false;
        }

    }

    public function tableRekap_admissionExist($Year)
    {
        $sql = 'show tables like "rekapintakeadm_'.$Year.'";';
        $query=$this->db_statistik->query($sql, array())->result_array();
        if (count($query) > 0) {
            return true;
        }
        else
        {
            return false;
        }

    }

    public function trigger_formulir($ta,$month,$year,$ProdiID,$action)
    {
        $chk = $this->tableRekapExist($ta);
        if (!$chk) {
            $this->Year = $ta;
            $this->create_table();
            // insert data 
            $this->insert_data();
        }
        else
        {
            $arr_bulan = array(
                'Jan','Feb','March','April','May','June','July','August','Sep','Oct','Nov','Des'
            );
            $month = (int) $month;
            $month = $month - 1;//for indeks array
            $field = ($ta == $year) ? $arr_bulan[$month ] : $arr_bulan[$month ].'_';
            $Table = 'rekapintake_'.$ta;
            switch ($action) {
                case 'add':
                    $sql = 'update db_statistik.'.$Table.' set '.$field.' = '.$field.' + 1 where ProdiID = ?';
                    $query=$this->db_statistik->query($sql, array($ProdiID));
                    break;
                case 'delete':
                    $sql = 'update db_statistik.'.$Table.' set '.$field.' = '.$field.' - 1 where ProdiID = ?';
                    $query=$this->db_statistik->query($sql, array($ProdiID));
                    break;
                default:
                    # code...
                    break;
            }

        }
    }

    public function ShowRekapIntake_Beasiswa($Year)
    {
        $arr_result = array();
        // passing variable to global
        $this->Year = $Year;
        $tblname = 'rekapintake_bea_'.$this->Year;
        //check table rekap exist
        $chk = $this->table_Exist($tblname);
        if ($chk) {
            //show data in table
            $arr_result = $this->m_master->showData_array('db_statistik.rekapintake_bea_'.$this->Year);
        }
        else
        {
            // create table
            $this->create_table_rekapintake_bea_();
            // insert data 
            $this->insert_data_rekapintake_bea_();
            $arr_result = $this->m_master->showData_array('db_statistik.rekapintake_bea_'.$this->Year);
        }

        return $arr_result;
    }

    public function table_Exist($tblname)
    {
        $sql = 'show tables like "'.$tblname.'"';
        $query=$this->db_statistik->query($sql, array())->result_array();
        if (count($query) > 0) {
            return true;
        }
        else
        {
            return false;
        }
    }

    private function create_table_rekapintake_bea_()
    {
        $Year = $this->Year;
        $Table = 'rekapintake_bea_'.$Year;
        $this->db_statistik->query('CREATE TABLE '.$Table.' (
                                      `ID` int(11) NOT NULL AUTO_INCREMENT,
                                      `ProdiID` int(11) DEFAULT NULL,
                                      `Detail` longtext,
                                      PRIMARY KEY (`ID`)
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1'
                                  );
    }

    private function getPersentType()
    {
        $Year = $this->Year;
        $arr_result = array();
        $sql = 'select  DISTINCT(Discount) as total from (
                select f.Discount
                from db_admission.register as a 
                left join db_admission.school as b on a.SchoolID = b.ID 
                LEFT JOIN db_admission.register_verification as z on a.ID = z.RegisterID 
                LEFT JOIN db_admission.register_verified as c on z.ID = c.RegVerificationID 
                LEFT JOIN db_admission.register_formulir as e on c.ID = e.ID_register_verified 
                LEFT join db_academic.program_study as d on e.ID_program_study = d.ID 
                left join db_admission.sale_formulir_offline as xz on c.FormulirCode = xz.FormulirCodeOffline  
                join db_finance.payment_admisi as f on e.ID = f.ID_register_formulir    
                join db_finance.register_admisi as g on e.ID = g.ID_register_formulir
                 where a.SetTa = "'.$Year.'"and g.`Status` = "Approved"
                ) cc ORDER BY Discount asc';
        $query=$this->db_statistik->query($sql, array())->result_array();
        foreach ($query as $key) {
            foreach ($key as $keya => $value) {
                $arr_result[] = (int)$value;
            }
        }
        return $arr_result;        
    }

    private function insert_data_rekapintake_bea_()
    {
        $Year = $this->Year;
        $Table = 'rekapintake_bea_'.$Year;
        $getProdi = $this->m_master->caribasedprimary('db_academic.program_study','Status',1);
        $bea = $this->getPersentType();
        for ($i=0; $i < count($getProdi); $i++) {
            $datasave = array(); 
            $ProdiID = $getProdi[$i]['ID'];
            $fieldPersen = array();
            for ($j=0; $j < count($bea); $j++) { 
                $p = $bea[$j];
                $NmP = 'Beasiswa_'.$bea[$j].'%';
                $sql = 'select  count(*) as total from (
                        select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, 
                        e.ID as ID_register_formulir,f.Discount,
                        if(a.StatusReg = 1, 
                        (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,"" ) as No_Ref,
                        if(a.StatusReg = 1, 
                        (select DateFin from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,a.RegisterAT ) as intakedate,
                        (select count(*) as total from db_finance.payment_pre where Status = 1 and ID_register_formulir = e.ID ) as C_bayar
                        from db_admission.register as a 
                        left join db_admission.school as b on a.SchoolID = b.ID 
                        LEFT JOIN db_admission.register_verification as z on a.ID = z.RegisterID 
                        LEFT JOIN db_admission.register_verified as c on z.ID = c.RegVerificationID 
                        LEFT JOIN db_admission.register_formulir as e on c.ID = e.ID_register_verified 
                        LEFT join db_academic.program_study as d on e.ID_program_study = d.ID 
                        left join db_admission.sale_formulir_offline as xz on c.FormulirCode = xz.FormulirCodeOffline  
                        join db_finance.payment_admisi as f on e.ID = f.ID_register_formulir    
                        join db_finance.register_admisi as g on e.ID = g.ID_register_formulir
                         where a.SetTa = "'.$Year.'"  and g.`Status` = "Approved" and f.Discount = '.$p.' and e.ID_program_study = ?
                         group by ID_register_formulir
                        ) cc where C_bayar > 0 and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
              join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
              join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
              join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
              join db_admission.register as reg on reg.ID = rve.RegisterID
              where reg.SetTa = "'.$Year.'" )';
                        $query=$this->db_statistik->query($sql, array($ProdiID))->result_array();
                        $fieldPersen[$NmP] = $query[0]['total'];
            }

            $datasave = array(
                   'ProdiID' =>  $ProdiID,
                   'Detail' => json_encode($fieldPersen),
            );

            $this->db_statistik->insert($Table, $datasave);
        }

        $this->saveLastUpdated($Table);
        
    }

    public function ShowRekapIntake_School($Year)
    {
        $arr_result = array();
        // passing variable to global
        $this->Year = $Year;
        $tblname = 'rekapintake_sch_'.$this->Year;
        //check table rekap exist
        $chk = $this->table_Exist($tblname);
        if ($chk) {
            //show data in table
            $arr_result = $this->m_master->showData_array('db_statistik.rekapintake_sch_'.$this->Year);
        }
        else
        {
            // create table
            $this->create_table_rekapintake_sch_();
            // insert data 
            $this->insert_data_rekapintake_sch_();
            $arr_result = $this->m_master->showData_array('db_statistik.rekapintake_sch_'.$this->Year);
        }

        return $arr_result;
    }

    private function create_table_rekapintake_sch_()
    {
        $Year = $this->Year;
        $Table = 'rekapintake_sch_'.$Year;
        $this->db_statistik->query('CREATE TABLE '.$Table.' (
                                      `ID` int(11) NOT NULL AUTO_INCREMENT,
                                      `ProvinceID` varchar(25) DEFAULT NULL,
                                      `Qty` int(11) DEFAULT NULL,
                                      `Detail` longtext,
                                      PRIMARY KEY (`ID`)
                                    ) ENGINE=InnoDB DEFAULT CHARSET=latin1'
                                  );
    }

    private function insert_data_rekapintake_sch_()
    {
        $Year = $this->Year;
        $Table = 'rekapintake_sch_'.$Year;
        $getProv = $this->m_master->showData_array('db_admission.province');
        for ($i=0; $i < count($getProv); $i++) {
            $datasave = array(); 
            $Prov = $getProv[$i]['ProvinceID'];
            $Detail = array();
            $sql = '
                select  count(*) as total from (
                                        select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, 
                                        e.ID as ID_register_formulir,
                                        if(a.StatusReg = 1, 
                                        (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,"" ) as No_Ref,
                                        if(a.StatusReg = 1, 
                                        (select DateFin from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,a.RegisterAT ) as intakedate,
                                        (select count(*) as total from db_finance.payment_pre where Status = 1 and ID_register_formulir = e.ID ) as C_bayar
                                        from db_admission.register as a 
                                        left join db_admission.school as b on a.SchoolID = b.ID 
                                        LEFT JOIN db_admission.register_verification as z on a.ID = z.RegisterID 
                                        LEFT JOIN db_admission.register_verified as c on z.ID = c.RegVerificationID 
                                        LEFT JOIN db_admission.register_formulir as e on c.ID = e.ID_register_verified 
                                        LEFT join db_academic.program_study as d on e.ID_program_study = d.ID 
                                        left join db_admission.sale_formulir_offline as xz on c.FormulirCode = xz.FormulirCodeOffline  
                                         where a.SetTa = "'.$Year.'" and b.ProvinceID = "'.$Prov.'"
                                        ) cc where C_bayar > 0
                                        and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
                                                      join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
                                                      join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
                                                      join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
                                                      join db_admission.register as reg on reg.ID = rve.RegisterID
                                                      where reg.SetTa = "'.$Year.'" )
            ';
              $query=$this->db_statistik->query($sql, array())->result_array();
              $Qty = $query[0]['total'];
              if ($Qty > 0) {
                  $c = $this->m_master->caribasedprimary('db_admission.province_region','ProvinceID',$Prov);
                  if (count($c) > 0) {
                      for ($l=0; $l < count($c); $l++) { 
                          $RegionID = $c[$l]['RegionID'];
                          $sql = '
                              select  count(*) as total from (
                                                      select a.ID as RegisterID,a.Name,a.SchoolID,b.SchoolName,a.Email,a.VA_number,c.FormulirCode,e.ID_program_study,d.NameEng,d.Name as NamePrody, 
                                                      e.ID as ID_register_formulir,
                                                      if(a.StatusReg = 1, 
                                                      (select No_Ref from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,"" ) as No_Ref,
                                                      if(a.StatusReg = 1, 
                                                      (select DateFin from db_admission.formulir_number_offline_m where FormulirCode = c.FormulirCode limit 1) ,a.RegisterAT ) as intakedate,
                                                      (select count(*) as total from db_finance.payment_pre where Status = 1 and ID_register_formulir = e.ID ) as C_bayar
                                                      from db_admission.register as a 
                                                      left join db_admission.school as b on a.SchoolID = b.ID 
                                                      LEFT JOIN db_admission.register_verification as z on a.ID = z.RegisterID 
                                                      LEFT JOIN db_admission.register_verified as c on z.ID = c.RegVerificationID 
                                                      LEFT JOIN db_admission.register_formulir as e on c.ID = e.ID_register_verified 
                                                      LEFT join db_academic.program_study as d on e.ID_program_study = d.ID 
                                                      left join db_admission.sale_formulir_offline as xz on c.FormulirCode = xz.FormulirCodeOffline  
                                                       where a.SetTa = "'.$Year.'" and b.CityID = "'.$RegionID.'"
                                                      ) cc where C_bayar > 0
                                                      and ID_register_formulir not in (select ID_register_formulir from db_finance.register_refund as rr 
                                                                    join db_admission.register_formulir as rf on rf.ID = rr.ID_register_formulir
                                                                    join db_admission.register_verified as rv on rv.ID = rf.ID_register_verified
                                                                    join db_admission.register_verification as rve on rve.ID = rv.RegVerificationID
                                                                    join db_admission.register as reg on reg.ID = rve.RegisterID
                                                                    where reg.SetTa = "'.$Year.'" )
                          ';
                            $query=$this->db_statistik->query($sql, array())->result_array();
                            $Detail[$RegionID] = $query[0]['total'];
                      }
                  }
              }

              $datasave = array(
                'ProvinceID' => $Prov,
                'Qty' => $Qty,
                'Detail' => json_encode($Detail),
              );
            $this->db_statistik->insert($Table, $datasave);
        }

        $this->saveLastUpdated($Table);
    }

    public function ShowRekap_summary_payment_mhs()
    {
        $arr_result = array();
        $tblname = 'summary_payment_mhs';
        //check table rekap exist
        $chk = $this->table_Exist($tblname);
        if ($chk) {
            //show data in table
            $arr_result = $this->m_master->showData_array('db_statistik.'.$tblname);
        }
        else
        {
            // create table
            $this->create_table_summary_payment_mhs();
            // insert data 
            $this->insert_data_summary_payment_mhs();
            $arr_result = $this->m_master->showData_array('db_statistik.'.$tblname);
        }

        return $arr_result;
    }

    private function create_table_summary_payment_mhs()
    {
        $Table ='summary_payment_mhs';
        $this->db_statistik->query('CREATE TABLE '.$Table.' (
                          `ID` int(11) NOT NULL AUTO_INCREMENT,
                          `Paid_Off` longtext,
                          `Unpaid_Off` longtext,
                          `unsetPaid` longtext,
                          `Paid_Off_detail` longtext,
                          `Unpaid_Off_detail` longtext,
                          `Unset_Paid_detail` longtext,
                          `Payment_Detail` longtext,
                          PRIMARY KEY (`ID`)
                        ) ENGINE=InnoDB DEFAULT CHARSET=latin1'
                                  );
    }

    public function get_pay_arrDataPS($Q_invStudent,$Invoice){
        $Pay = 0;
        $Sisa = 0;
        for ($r=0; $r < count($Q_invStudent); $r++) { 
            $ID_payment_students = $Q_invStudent[$r]['ID'];
            $Q_payment_student_details =  $this->m_master->caribasedprimary('db_finance.payment_student_details','ID_payment_students',$ID_payment_students);
            for ($x=0; $x < count($Q_payment_student_details); $x++) { 
                 $Pay = $Pay + $Q_payment_student_details[$x]['Pay'];
            }
        }

        $Sisa = $Invoice - $Pay;

        return ['Sisa' => $Sisa ,'Pay' => $Pay];
    }

    private function insert_data_summary_payment_mhs()
    {
        $Table ='summary_payment_mhs';
        $arr_json = array();
        $arrDB = array();
        $sqlDB = 'show databases like "%ta_2%"';
        $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
        $SemesterYear = $SemesterID[0]['Year'];
        $queryDB=$this->db->query($sqlDB, array())->result_array();
        foreach ($queryDB as $key) {
          foreach ($key as $keyB ) {
            $YearDB = explode('_', $keyB);
            $YearDB = $YearDB[1];
            if ($SemesterYear >= $YearDB) {
                $arrDB[] = $keyB;
            }
          }
          
        }

        rsort($arrDB);
        $Year = 'ta_'.date('Y');
        $Semester = $SemesterID[0]['ID'];
        $Semester = ' and SemesterID = '.$Semester;
        $unk = 1;

        // get paid off
        $Paid_Off = array();
        $Unpaid_Off = array();
        $unsetPaid = array();
        
        $MHS_unsetPaid = [];
        $MHS_Paid_Off = [];
        $MHS_Unpaid_Off = [];

        $TotInvoice = 0;
        $TotPay = 0;
        $TotSisa = 0;


        $TotSPP = 0;
        $TotSPPPay = 0;
        $TotSPPSisa = 0;
        $dataDetailSPP = [
            'Paid_Off' => [],
            'Unpaid_Off' => [],
            'unsetPaid' => [],
        ];
        $countSPPBarChart = [
            'Paid_Off' => [],
            'Unpaid_Off' => [],
            'unsetPaid' => [],
        ];

        $TotBPP = 0;
        $TotBPPPay = 0;
        $TotBPPSisa = 0;
        $dataDetailBPP = [
            'Paid_Off' => [],
            'Unpaid_Off' => [],
            'unsetPaid' => [],
        ];
        $countBPPBarChart = [
            'Paid_Off' => [],
            'Unpaid_Off' => [],
            'unsetPaid' => [],
        ];

        $TotCredit = 0;
        $TotCreditPay = 0;
        $TotCreditSisa = 0;
        $dataDetailCredit = [
            'Paid_Off' => [],
            'Unpaid_Off' => [],
            'unsetPaid' => [],
        ];
        $countCreditBarChart = [
            'Paid_Off' => [],
            'Unpaid_Off' => [],
            'unsetPaid' => [],
        ];

        $TotAnother = 0;
        $TotAnotherPay = 0;
        $TotAnotherSisa = 0;
        $dataDetailAnother = [
            'Paid_Off' => [],
            'Unpaid_Off' => [],
            'unsetPaid' => [],
        ];
        $countAnotherBarChart = [
            'Paid_Off' => [],
            'Unpaid_Off' => [],
            'unsetPaid' => [],
        ];

        for ($i=0; $i < count($arrDB); $i++) { 
            // if ($arrDB[$i] != $Year) {

                $a_Paid_Off = 0;
                $a_Unpaid_Off = 0;
                $a_unsetPaid = 0;

                $SPP_Paid_Off = 0;
                $SPP_Unpaid_Off = 0;
                $SPP_unsetPaid = 0;

                $BPP_Paid_Off = 0;
                $BPP_Unpaid_Off = 0;
                $BPP_unsetPaid = 0;

                $Credit_Paid_Off = 0;
                $Credit_Unpaid_Off = 0;
                $Credit_unsetPaid = 0;

                $Another_Paid_Off = 0;
                $Another_Unpaid_Off = 0;
                $Another_unsetPaid = 0;

                    // get Data Mahasiswa
                    $sql = 'select a.NPM,a.Name,b.NameEng from '.$arrDB[$i].'.students as a join db_academic.program_study as b on a.ProdiID = b.ID where a.StatusStudentID in (3,2,8) ';
                    $query=$this->db->query($sql, array())->result_array();
                    for ($u=0; $u < count($query); $u++) { 

                        $DtMHSTransaction = [
                          'NPM' => $query[$u]['NPM'],
                          'TA' => $arrDB[$i],
                          'Name' => $query[$u]['Name'],
                          'Prodi' => $query[$u]['NameEng'],
                        ];

                      // cek BPP 
                      $sqlBPP = 'select * from db_finance.payment where PTID = 2 and NPM = ? '.$Semester; //  limit 1
                      $queryBPP=$this->db->query($sqlBPP, array($query[$u]['NPM']))->result_array();
                      $arrBPP = array(
                        'BPP' => '0',
                        'PayBPP' => '0',
                        'SisaBPP' => '0',
                        'DetailPaymentBPP' => '',
                      );
                        if (count($queryBPP) > 0) {
                            for ($t=0; $t < count($queryBPP); $t++) { 
                              // cek payment students
                              $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryBPP[$t]['ID']);

                              // get pay from table payment_student_details
                              $Invoice = $queryBPP[$t]['Invoice'];

                              $TotBPP = $TotBPP + $Invoice;

                              // for get total Invoice
                              $TotInvoice = $TotInvoice + $Invoice;

                              $get_pay = $this->get_pay_arrDataPS($Q_invStudent,$Invoice);

                              // for get total pay
                              $TotPay = $TotPay +  $get_pay['Pay'];

                              // for get total Sisa
                              $TotSisa = $TotSisa +  $get_pay['Sisa'];

                              $PayBPP = $get_pay['Pay'];
                              $SisaBPP = $get_pay['Sisa'];

                              $Bpp_DtMHSTransaction = $DtMHSTransaction;

                              $Bpp_DtMHSTransaction['Pay'] = $PayBPP;
                              $Bpp_DtMHSTransaction['Sisa'] = abs($SisaBPP);
                              $Bpp_DtMHSTransaction['Invoice'] = $Invoice;

                              if ($SisaBPP == 0) {
                                  array_push($dataDetailBPP['Paid_Off'],$Bpp_DtMHSTransaction);
                                  $BPP_Paid_Off++;
                              }
                              else
                              {
                                  array_push($dataDetailBPP['Unpaid_Off'],$Bpp_DtMHSTransaction);
                                  $BPP_Unpaid_Off++;
                              }

                              $TotBPPPay =  $TotBPPPay + $PayBPP;
                              $TotBPPSisa =  $TotBPPSisa + $SisaBPP;

                              $arrBPP = array(
                                'BPP' => (int)$queryBPP[$t]['Invoice'],
                                'PayBPP' => (int)$PayBPP,
                                'SisaBPP' => (int)$SisaBPP,
                                'DetailPaymentBPP' => $Q_invStudent,
                              );

                            }
                        }
                        else
                        {
                            array_push($dataDetailBPP['unsetPaid'],$DtMHSTransaction);
                            $BPP_unsetPaid++;
                        }

                      // cek Credit 
                      $sqlCr = 'select * from db_finance.payment where PTID = 3 and NPM = ? '.$Semester; // limit 1
                      $queryCr=$this->db->query($sqlCr, array($query[$u]['NPM']))->result_array();
                      $arrCr = array(
                        'Cr' => '0',
                        'PayCr' => '0',
                        'SisaCr' => '0',
                        'DetailPaymentCr' => '',
                      );
                        if (count($queryCr) > 0) {
                            for ($t=0; $t < count($queryCr); $t++) { 
                              // cek payment students
                              $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryCr[$t]['ID']);
                              
                              // get pay from table payment_student_details
                              $Invoice = $queryCr[$t]['Invoice'];

                              $TotCredit = $TotCredit + $Invoice;

                              // for get total Invoice
                              $TotInvoice = $TotInvoice + $Invoice;

                              $get_pay = $this->get_pay_arrDataPS($Q_invStudent,$Invoice);

                              // for get total pay
                              $TotPay = $TotPay +  $get_pay['Pay'];

                              // for get total Sisa
                              $TotSisa = $TotSisa +  $get_pay['Sisa'];

                              $PayCr = $get_pay['Pay'];
                              $SisaCr = $get_pay['Sisa'];

                              $Credit_DtMHSTransaction = $DtMHSTransaction;

                              $Credit_DtMHSTransaction['Pay'] = $PayCr;
                              $Credit_DtMHSTransaction['Sisa'] = abs($SisaCr);
                              $Credit_DtMHSTransaction['Invoice'] = $Invoice;

                              if ($SisaCr == 0) {
                                  array_push($dataDetailCredit['Paid_Off'],$Credit_DtMHSTransaction);
                                  $Credit_Paid_Off++;
                              }
                              else
                              {
                                  array_push($dataDetailCredit['Unpaid_Off'],$Credit_DtMHSTransaction);
                                  $Credit_Unpaid_Off++;
                              }

                              $TotCreditPay += $PayCr;
                              $TotCreditSisa += $SisaCr;

                              $arrCr = array(
                                'Cr' => (int)$queryCr[$t]['Invoice'],
                                'PayCr' => (int)$PayCr,
                                'SisaCr' => (int)$SisaCr,
                                'DetailPaymentCr' => $Q_invStudent,
                              );

                            }
                        }
                        else
                        {
                            array_push($dataDetailCredit['unsetPaid'],$DtMHSTransaction);
                            $Credit_unsetPaid++;
                        }


                        // cek SPP 
                        $sqlSPP = 'select * from db_finance.payment where PTID = 1 and NPM = ? '.$Semester; //  limit 1
                        $querySPP=$this->db->query($sqlSPP, array($query[$u]['NPM']))->result_array();
                        $arrSPP = array(
                          'SPP' => '0',
                          'PaySPP' => '0',
                          'SisaSPP' => '0',
                          'DetailPaymentSPP' => '',
                        );
                          if (count($querySPP) > 0) {
                              for ($t=0; $t < count($querySPP); $t++) { 
                                // cek payment students
                                $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$querySPP[$t]['ID']);
                                
                                // get pay from table payment_student_details
                                $Invoice = $querySPP[$t]['Invoice'];

                                // for get total SPP
                                $TotSPP =  $TotSPP + $Invoice;

                                // for get total Invoice
                                $TotInvoice = $TotInvoice + $Invoice;

                                $get_pay = $this->get_pay_arrDataPS($Q_invStudent,$Invoice);

                                // for get total pay
                                $TotPay = $TotPay +  $get_pay['Pay'];

                                // for get total Sisa
                                $TotSisa = $TotSisa +  $get_pay['Sisa'];

                                $PaySPP = $get_pay['Pay'];
                                $SisaSPP = $get_pay['Sisa'];

                                $SPP_DtMHSTransaction = $DtMHSTransaction;

                                $SPP_DtMHSTransaction['Pay'] = $PaySPP;
                                $SPP_DtMHSTransaction['Sisa'] = abs($SisaSPP);
                                $SPP_DtMHSTransaction['Invoice'] = $Invoice;

                                if ($SisaSPP == 0) {
                                    array_push($dataDetailSPP['Paid_Off'],$SPP_DtMHSTransaction);
                                    $SPP_Paid_Off++;
                                }
                                else
                                {
                                    array_push($dataDetailSPP['Unpaid_Off'],$SPP_DtMHSTransaction);
                                    $SPP_Unpaid_Off++;
                                }

                                $TotSPPPay = $TotSPPPay+$PaySPP;
                                $TotSPPSisa = $TotSPPSisa+$SisaSPP;
                                
                                $arrSPP = array(
                                  'SPP' => (int)$querySPP[$t]['Invoice'],
                                  'PaySPP' => (int)$PaySPP,
                                  'SisaSPP' => (int)$SisaSPP,
                                  'DetailPaymentSPP' => $Q_invStudent,
                                );

                              }
                          }
                          else
                          {
                            array_push($dataDetailSPP['unsetPaid'],$DtMHSTransaction);
                            $SPP_unsetPaid++;
                          }

                          // cek lain-lain 
                          $sqlAn = 'select * from db_finance.payment where PTID = 4 and NPM = ? '.$Semester; //  limit 1
                          $queryAn=$this->db->query($sqlAn, array($query[$u]['NPM']))->result_array();
                          $arrAn = array(
                            'An' => '0',
                            'PayAn' => '0',
                            'SisaAn' => '0',
                            'DetailPaymentAn' => '',
                          );
                            if (count($queryAn) > 0) {
                                for ($t=0; $t < count($queryAn); $t++) { 
                                  // cek payment students
                                  $Q_invStudent = $this->m_master->caribasedprimary('db_finance.payment_students','ID_payment',$queryAn[$t]['ID']);
                                  
                                  // get pay from table payment_student_details
                                  $Invoice = $queryAn[$t]['Invoice'];

                                  $TotAnother += $Invoice;

                                  // for get total Invoice
                                  $TotInvoice = $TotInvoice + $Invoice;

                                  $get_pay = $this->get_pay_arrDataPS($Q_invStudent,$Invoice);

                                  // for get total pay
                                  $TotPay = $TotPay +  $get_pay['Pay'];

                                  // for get total Sisa
                                  $TotSisa = $TotSisa +  $get_pay['Sisa'];

                                  $PayAn = $get_pay['Pay'];
                                  $SisaAn = $get_pay['Sisa'];

                                  $Another_DtMHSTransaction = $DtMHSTransaction;

                                  $Another_DtMHSTransaction['Pay'] = $PayAn;
                                  $Another_DtMHSTransaction['Sisa'] = abs($SisaAn);
                                  $Another_DtMHSTransaction['Invoice'] = $Invoice;

                                  if ($SisaAn == 0) {
                                      array_push($dataDetailAnother['Paid_Off'],$Another_DtMHSTransaction);
                                      $Another_Paid_Off++;
                                  }
                                  else
                                  {
                                      array_push($dataDetailAnother['Unpaid_Off'],$Another_DtMHSTransaction);
                                      $Another_Unpaid_Off++;
                                  }

                                  $TotAnotherPay += $PayAn;
                                  $TotAnotherSisa += $SisaAn;
                                    
                                  $arrAn = array(
                                    'An' => (int)$queryAn[$t]['Invoice'],
                                    'PayAn' => (int)$PayAn,
                                    'SisaAn' => (int)$SisaAn,
                                    'DetailPaymentAn' => $Q_invStudent,
                                  );

                                }
                            }
                            else
                            {
                                array_push($dataDetailAnother['unsetPaid'],$DtMHSTransaction);
                                $Another_unsetPaid++;
                            }

                        if ($arrBPP['DetailPaymentBPP'] == '' || $arrCr['DetailPaymentCr'] == '' || count($arrBPP['DetailPaymentBPP']) == 0 || count($arrCr['DetailPaymentCr']) == 0  ) { // unset paid
                          $a_unsetPaid = $a_unsetPaid + 1;

                          $DtMHSunsetPaid = [
                            'NPM' => $query[$u]['NPM'],
                            'TA' => $arrDB[$i],
                            'Name' => $query[$u]['Name'],
                            'Prodi' => $query[$u]['NameEng'],
                          ];

                          $MHS_unsetPaid[] = $DtMHSunsetPaid;

                        }
                        else
                        {
                            if ( $arrBPP['SisaBPP'] == 0 && $arrCr['SisaCr'] == 0 &&  $arrSPP['SisaSPP'] == 0 && $arrAn['SisaAn'] == 0) { // lunas
                              $a_Paid_Off = $a_Paid_Off + 1;

                              $DtMHSPaidOFF = [
                                'NPM' => $query[$u]['NPM'],
                                'TA' => $arrDB[$i],
                                'Name' => $query[$u]['Name'],
                                'Prodi' => $query[$u]['NameEng'],
                              ];

                              $MHS_Paid_Off[] = $DtMHSPaidOFF;

                            }
                            elseif ( $arrBPP['SisaBPP'] > 0 || $arrCr['SisaCr'] > 0 ||  $arrSPP['SisaSPP'] > 0 || $arrAn['SisaAn'] > 0) { // belum lunas
                              $a_Unpaid_Off = $a_Unpaid_Off + 1;

                              $DtMHSUNPaidOFF = [
                                'NPM' => $query[$u]['NPM'],
                                'TA' => $arrDB[$i],
                                'Name' => $query[$u]['Name'],
                                'Prodi' => $query[$u]['NameEng'],
                              ];

                              $MHS_Unpaid_Off[] = $DtMHSUNPaidOFF;

                            }     
                            
                        }  

                    } // loop per mhs    

                $strUnk = $unk.'.6818181818181817';
                $YearDB = explode('_', $arrDB[$i]);
                $YearDB = $YearDB[1];

                $Paid_Off[] = array($YearDB,$a_Paid_Off);
                $Unpaid_Off[] = array($YearDB,$a_Unpaid_Off);
                $unsetPaid[] = array($YearDB,$a_unsetPaid);

                array_push($countSPPBarChart['Paid_Off'],array($YearDB,$SPP_Paid_Off) );
                array_push($countSPPBarChart['Unpaid_Off'],array($YearDB,$SPP_Unpaid_Off) );
                array_push($countSPPBarChart['unsetPaid'],array($YearDB,$SPP_unsetPaid));

                array_push($countBPPBarChart['Paid_Off'], array($YearDB,$BPP_Paid_Off) );
                array_push($countBPPBarChart['Unpaid_Off'],array($YearDB,$BPP_Unpaid_Off) );
                array_push($countBPPBarChart['unsetPaid'],array($YearDB,$BPP_unsetPaid));

                array_push($countCreditBarChart['Paid_Off'],array($YearDB,$Credit_Paid_Off) );
                array_push($countCreditBarChart['Unpaid_Off'],array($YearDB,$Credit_Unpaid_Off) );
                array_push($countCreditBarChart['unsetPaid'],array($YearDB,$Credit_unsetPaid) );

                array_push($countAnotherBarChart['Paid_Off'],array($YearDB,$Another_Paid_Off) );
                array_push($countAnotherBarChart['Unpaid_Off'],array($YearDB,$Another_Unpaid_Off) );
                array_push($countAnotherBarChart['unsetPaid'],array($YearDB,$Another_unsetPaid) );

                $unk++;

            // }
        }

        $arr_json = array('Paid_Off'=> json_encode($Paid_Off),'Unpaid_Off' => json_encode($Unpaid_Off),'unsetPaid' => json_encode($unsetPaid),
            'Paid_Off_detail' => json_encode([
                'data_mhs' => $MHS_Paid_Off,
            ]),
            'Unpaid_Off_detail' => json_encode([
                'data_mhs' => $MHS_Unpaid_Off,
            ]),
            'Unset_Paid_detail' => json_encode([
                'data_mhs' => $MHS_unsetPaid,
            ]),
            'Payment_Detail' => json_encode([
                'TotInvoice' => $TotInvoice,
                'TotPay' => $TotPay,
                'TotSisa' => $TotSisa,
                'SPP' => [
                    'Tot' => $TotSPP,
                    'TotPay' => $TotSPPPay,
                    'TotSisa' => $TotSPPSisa,
                    'Detail' => $dataDetailSPP,
                    'BarChart' => $countSPPBarChart,
                ],
                'BPP' => [
                    'Tot' => $TotBPP,
                    'TotPay' => $TotBPPPay,
                    'TotSisa' => $TotBPPSisa,
                    'Detail' => $dataDetailBPP,
                    'BarChart' => $countBPPBarChart,
                ],
                'Credit' => [
                    'Tot' => $TotCredit,
                    'TotPay' => $TotCreditPay,
                    'TotSisa' => $TotCreditSisa,
                    'Detail' => $dataDetailCredit,
                    'BarChart' => $countCreditBarChart,
                ],
                'Another' => [
                    'Tot' => $TotAnother,
                    'TotPay' => $TotAnotherPay,
                    'TotSisa' => $TotAnotherSisa,
                    'Detail' => $dataDetailAnother,
                    'BarChart' => $countAnotherBarChart,
                ],
                
            ]), 
    );

        // $arr_json = array('Paid_Off'=> json_encode($Paid_Off),'Unpaid_Off' => json_encode($Unpaid_Off),'unsetPaid' => json_encode($unsetPaid));

        $this->db_statistik->insert($Table, $arr_json);
        $this->saveLastUpdated($Table);
        
    }


}
