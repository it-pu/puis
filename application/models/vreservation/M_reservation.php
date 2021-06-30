<?php

defined('BASEPATH') OR exit('No direct script access allowed');

class M_reservation extends CI_Model {


    function __construct()
    {
        parent::__construct();

    }

    public function getdataMenu()
    {
        $sql = "select * from db_reservation.cfg_menu";
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function saveMenu($menu)
    {
        $dataSave = array(
            'Menu' => ucwords($menu),
        );
        $this->db->insert('db_reservation.cfg_menu', $dataSave);
    }

    public function saveSubMenu($menu,$sub_menu1,$sub_menu2,$chkPrevileges,$Slug,$Controller)
    {
        $sub_menu2 = ($sub_menu2 == '') ? 'empty' : $sub_menu2;
        // print_r($chkPrevileges);
        $dataSave = array();
        $dataSave['ID_Menu'] = $menu;
        $dataSave['SubMenu1'] = ucwords($sub_menu1);
        $dataSave['SubMenu2'] = ucwords($sub_menu2);
        $dataSave['Slug'] = $Slug;
        $dataSave['Controller'] = $Controller;

        for ($i=0; $i < count($chkPrevileges) ; $i++) {
            switch ($chkPrevileges[$i]) {
                case 'Read':
                    $dataSave['read'] = 1;
                    break;
                case 'Write':
                    $dataSave['write'] = 1;
                    break;
                case 'Update':
                    $dataSave['update'] = 1;
                    break;
                case 'Delete':
                    $dataSave['delete'] = 1;
                    break;
                default:
                    $dataSave['read'] = 0;
                    $dataSave['write'] = 0;
                    $dataSave['update'] = 0;
                    $dataSave['delete'] = 0;
                    break;
            }
        }
        // print_r($dataSave);
        $this->db->insert('db_reservation.cfg_sub_menu', $dataSave);
    }

    public function showSubmenu()
    {
        $sql = "select a.Menu,b.* from db_reservation.cfg_menu as a
          join db_reservation.cfg_sub_menu as b
          on a.ID = b.ID_Menu";
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function updateSubMenu($input)
    {
        // $dataArr = array();
        $ID_Menu = '';
        $Menu = '';
        $SubMenu1 = '';
        $SubMenu2 = '';
        $read = '';
        $write = '';
        $update = '';
        $delete = '';
        $ID = '';
        $query = '';
        $Slug = '';
        $Controller = '';

        if(array_key_exists("Menu",$input))
        {
            $ID_Menu = $input['ID_Menu'];
            $Menu = $input['Menu'];
            $sql = "update db_reservation.cfg_menu set Menu = ? where ID = ? ";
            $query=$this->db->query($sql, array($Menu,$ID_Menu));
        }

        if(array_key_exists("Slug",$input))
        {
            $ID_Menu = $input['ID_Menu'];
            $Slug = $input['Slug'];
            $sql = "update db_reservation.cfg_sub_menu set Slug = ? where ID = ? ";
            $query=$this->db->query($sql, array($Slug,$ID_Menu));
        }

        if(array_key_exists("Controller",$input))
        {
            $ID_Menu = $input['ID_Menu'];
            $Controller = $input['Controller'];
            $sql = "update db_reservation.cfg_sub_menu set Controller = ? where ID = ? ";
            $query=$this->db->query($sql, array($Controller,$ID_Menu));
        }

        if(array_key_exists("SubMenu1",$input))
        {
            $ID = $input['ID'];
            $SubMenu1 = $input['SubMenu1'];
            $sql = "update db_reservation.cfg_sub_menu set SubMenu1 = ? where ID = ? ";
            $query=$this->db->query($sql, array($SubMenu1,$ID));
        }

        if(array_key_exists("SubMenu2",$input))
        {
            $ID = $input['ID'];
            $SubMenu2 = $input['SubMenu2'];
            $sql = "update db_reservation.cfg_sub_menu set SubMenu2 = ? where ID = ? ";
            $query=$this->db->query($sql, array($SubMenu2,$ID));
        }

        if(array_key_exists("read",$input))
        {
            $ID = $input['ID'];
            $read = $input['read'];
            $sql = "update db_reservation.cfg_sub_menu set `read` = ? where ID = ? ";
            $query=$this->db->query($sql, array($read,$ID));
        }

        if(array_key_exists("write",$input))
        {
            $ID = $input['ID'];
            $write = $input['write'];
            $sql = "update db_reservation.cfg_sub_menu set `write` = ? where ID = ? ";
            $query=$this->db->query($sql, array($write,$ID));
        }

        if(array_key_exists("update",$input))
        {
            $ID = $input['ID'];
            $update = $input['update'];
            $sql = "update db_reservation.cfg_sub_menu set `update` = ? where ID = ? ";
            $query=$this->db->query($sql, array($update,$ID));
        }

        if(array_key_exists("delete",$input))
        {
            $ID = $input['ID'];
            $delete = $input['delete'];
            $sql = "update db_reservation.cfg_sub_menu set `delete` = ? where ID = ? ";
            $query=$this->db->query($sql, array($delete,$ID));
        }

    }

    public function deleteSubMenu($input)
    {
        $sql = "delete from db_reservation.cfg_sub_menu where ID = ".$input['ID'];
        $query=$this->db->query($sql, array());
    }

    public function get_submenu_by_menu($input)
    {
        $ID_Menu = $input['Menu'];
        $GroupUser = $input['GroupUser'];
        $sql = "select a.Menu,b.* from db_reservation.cfg_menu as a
      join db_reservation.cfg_sub_menu as b
      on a.ID = b.ID_Menu where b.ID_Menu = ?
      and b.ID not in (select ID_cfg_sub_menu from db_reservation.cfg_rule_g_user where cfg_group_user = ?)";
        $query=$this->db->query($sql, array($ID_Menu,$GroupUser))->result_array();
        return $query;
    }

    public function get_previleges_group_show($GroupID)
    {
        $sql = 'SELECT d.GroupAuth, b.Menu,c.SubMenu1,c.SubMenu2,c.ID_Menu,a.ID_cfg_sub_menu,a.ID as ID_previleges,a.`read`,a.`write`,a.`update`,
a.`delete`,c.`read` as readMenu,c.`update` as updateMenu,c.`write` as writeMenu,c.`delete` as deleteMenu from db_reservation.cfg_rule_g_user as a
            join db_reservation.cfg_group_user as d
            on a.cfg_group_user = d.ID
            join db_reservation.cfg_sub_menu as c
            on a.ID_cfg_sub_menu = c.ID
            join db_reservation.cfg_menu as b
            on b.ID = c.ID_Menu where d.ID = ? ';
        $query=$this->db->query($sql, array($GroupID))->result_array();
        return $query;
    }

    public function save_groupuser_previleges($input)
    {
        $ID_GroupUSer = $input['ID_GroupUSer'];
        $checkbox = $input['checkbox'];
        $data = array();
        $increment = 0;
        for ($i=0; $i < count($checkbox); $i++) {
            $value = strtolower($checkbox[$i]->value);
            $ID_cfg_sub_menu = $checkbox[$i]->ID;

            // check data pertama
            if (count($data) == 0) {
                $data[$increment] = array(
                    'cfg_group_user' => $ID_GroupUSer,
                    'ID_cfg_sub_menu' => $ID_cfg_sub_menu,
                    $value => 1,
                );
                continue;
            }

            if (count($data) > 0) {
                // check data ada pada array
                $check = false;
                for ($j=0; $j < count($data); $j++) {
                    if ($data[$j]['ID_cfg_sub_menu'] == $ID_cfg_sub_menu) {
                        $data[$j][$value] = 1;
                        $check = true;
                        break;
                    }
                }

                if ($check) {
                    continue;
                }

                // check data tidak ada pada array
                for ($j=0; $j < count($data); $j++) {
                    if ($data[$j]['ID_cfg_sub_menu'] != $ID_cfg_sub_menu) {
                        $check = true;
                        break;
                    }
                }

                if ($check) {
                    $increment++;
                    $data[$increment] = array(
                        'cfg_group_user' => $ID_GroupUSer,
                        'ID_cfg_sub_menu' => $ID_cfg_sub_menu,
                        $value => 1,
                    );
                    continue;
                }

            }

        }

        // print_r($data);
        // save data
        for ($i=0; $i < count($data); $i++) {
            $dataSave = array();
            foreach ($data[$i] as $key => $value) {
                $dataSave[$key] = $value;
                // $dataSave = array($key=>$value);
            }
            $this->db->insert('db_reservation.cfg_rule_g_user', $dataSave);
        }
    }

    public function previleges_groupuser_update($input)
    {
        // $dataArr = array();
        $read = '';
        $write = '';
        $update = '';
        $delete = '';
        $ID = '';
        $query = '';

        if(array_key_exists("read",$input))
        {
            $ID = $input['ID'];
            $read = $input['read'];
            $sql = "update db_reservation.cfg_rule_g_user set `read` = ? where ID = ? ";
            $query=$this->db->query($sql, array($read,$ID));
        }

        if(array_key_exists("write",$input))
        {
            $ID = $input['ID'];
            $write = $input['write'];
            $sql = "update db_reservation.cfg_rule_g_user set `write` = ? where ID = ? ";
            $query=$this->db->query($sql, array($write,$ID));
        }

        if(array_key_exists("update",$input))
        {
            $ID = $input['ID'];
            $update = $input['update'];
            $sql = "update db_reservation.cfg_rule_g_user set `update` = ? where ID = ? ";
            $query=$this->db->query($sql, array($update,$ID));
        }

        if(array_key_exists("delete",$input))
        {
            $ID = $input['ID'];
            $delete = $input['delete'];
            $sql = "update db_reservation.cfg_rule_g_user set `delete` = ? where ID = ? ";
            $query=$this->db->query($sql, array($delete,$ID));
        }
    }

    public function previleges_groupuser_delete($input)
    {
        $sql = "delete from db_reservation.cfg_rule_g_user where ID = ".$input['ID'];
        $query=$this->db->query($sql, array());
    }

    public function getCountAllDataAuth()
    {
        $sql = 'select count(*) as total from db_reservation.previleges_guser';
        $query=$this->db->query($sql, array())->result_array();
        return $query[0]['total'];
    }

    public function get_m_equipment_additional($available = '> 0')
    {
        $sql = 'select a.ID as ID_add,a.*,b.*,c.Division from db_reservation.m_equipment_additional as a join db_reservation.m_equipment as b
        on a.ID_m_equipment = b.ID join db_employees.division as c on a.Owner = c.ID where a.Qty '.$available;
        $query=$this->db->query($sql, array());
        return $query->result_array();
    }

    public function get_m_equipment_additional_check_date($Start,$End,$available = '> 0')
    {
        $rs = array();
        $sql = 'select a.ID as ID_add,a.*,b.*,c.Division from db_reservation.m_equipment_additional as a join db_reservation.m_equipment as b
        on a.ID_m_equipment = b.ID join db_employees.division as c on a.Owner = c.ID where a.Qty '.$available;
        $query=$this->db->query($sql, array())->result_array();
        for ($i=0; $i < count($query); $i++) { 
            // for Update Qty
            $Qty0 = $query[$i]['Qty'];
            $Qty = $this->getQtyperDateTime($query[$i]['ID_add'],$Qty0,$Start,$End);
            $query[$i]['Qty'] = $Qty;
            if ($Qty > 0) {
                $rs[] = $query[$i];
            }
        }


        return $rs;
    }

    public function getQtyperDateTime($ID_equipment_add,$Qty_stock,$Start,$End,$Status = 'c.Status != 2')
    {
        $Qty = $Qty_stock;
        $sql = 'select a.*,c.Qty from db_reservation.t_booking as a
                                 join db_employees.employees as b on a.CreatedBy = b.NIP
                                 join db_reservation.t_booking_eq_additional as c on a.ID = c.ID_t_booking
                                 where '.$Status.' and (
                                    (a.`Start` >= "'.$Start.'" and a.`Start` < "'.$End.'" ) or (a.`End` > "'.$Start.'" and a.`End` <= "'.$End.'" )
                                    or (
                                            a.`Start` <= "'.$Start.'" and a.`End` >= "'.$End.'"
                                        )
                                ) and c.ID_equipment_additional = ?';
        $query=$this->db->query($sql, array($ID_equipment_add))->result_array();
        for ($i=0; $i < count($query); $i++) { 
            $Qty = $Qty - $query[$i]['Qty'];
        }

        return $Qty;
    }

    public function get_m_additional_personel()
    {
        $sql = 'select a.*,b.* from db_reservation.m_additional_personel as a join db_employees.division as b
        on a.ID_division = b.ID';
        $query=$this->db->query($sql, array());
        return $query->result_array();
    }

    public function getDataClassroomAcademic($NameDay,$date)
    {
        $arr_result = array();
        $this->load->model('m_api');
        $date2 = $date;
        $date2 = ($date2 == null) ? date('Y-m-d') : $date2;
        
        // cek academic years apakah periode ujian atau tidak
            $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
            $SemesterID = $SemesterID[0]['ID'];

            $sqlWaktu = 'select * from db_academic.academic_years where SemesterID = ? and (utsStart <="'.$date2.'" and utsEnd >= "'.$date2.'")';
            $queryWaktu=$this->db->query($sqlWaktu, array($SemesterID))->result_array(); // dalam ujian

            $sqlWaktu2 = 'select * from db_academic.academic_years where SemesterID = ? and (uasStart <="'.$date2.'" and uasEnd >= "'.$date2.'")';
            $queryWaktu2=$this->db->query($sqlWaktu2, array($SemesterID))->result_array(); // dalam ujian
            if (count($queryWaktu) == 0) {
                if (count($queryWaktu2) > 0) {
                    $sql = "select a.ExamClassroomID,a.ID as ID_exam,a.ExamDate,a.ExamStart as StartSessions,a.ExamEnd as EndSessions,TIMEDIFF(CONCAT(curdate(),' ',a.ExamEnd), CONCAT(curdate(),' ',a.ExamStart)) as time,
                            b.Name as NamaDosen,c.ScheduleID,d.NameEng as NamaHari,e.Room,f.MKID,g.NameEng as NamaMataKuliah
                            from db_academic.exam as a
                            join db_employees.employees as b
                            on a.Pengawas1 = b.NIP
                            join db_academic.exam_details as c
                            on a.ID = c.ExamID
                            join db_academic.days as d
                            on d.ID = a.DayID
                            join db_academic.classroom as e
                            on e.ID = a.ExamClassroomID
                            join db_academic.schedule_details_course as f
                            on f.ScheduleID = c.ScheduleID
                            join db_academic.mata_kuliah as g
                            on g.ID = f.MKID
                            where d.NameEng = ?
                            and a.ExamDate = '".$date2."'
                            and a.`Status` = '1'
                            and a.SemesterID = '".$SemesterID."'
                            group by c.ScheduleID
                    ";
                }
                else
                {
                    $sql = "select a.Room,b.NameEng,c.StartSessions,c.EndSessions,TIMEDIFF(CONCAT(curdate(),' ',EndSessions), CONCAT(curdate(),' ',StartSessions)) as time,
                            e.Name as NamaDosen,g.NameEng as NamaMataKuliah,d.ID as ScheduleID
                            from db_academic.classroom as a join db_academic.schedule_details as c
                            on a.ID = c.ClassroomID
                            join db_academic.days as b
                            on c.DayID = b.ID
                            left join db_academic.schedule as d on d.ID = c.ScheduleID
                            left join db_employees.employees as e on d.Coordinator = e.NIP
                            left join (select * from db_academic.schedule_details_course group by ScheduleID) as f on f.ScheduleID = d.ID
                            left join db_academic.mata_kuliah as g on g.ID = f.MKID
                            where '".$date2."' >= (select z.kuliahStart from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) 
                            and '".$date2."' <= (select z.kuliahEnd from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1)
                            and b.NameEng = ? and d.SemesterID = '".$SemesterID."'
                            and c.ScheduleID not in (select a.ScheduleID from db_academic.attendance as a join db_academic.schedule_exchange as b
                            on a.ID = b.ID_Attd where b.Status = '2' and b.DateOriginal = '".$date2."')
                            order by a.Room";
                }

                
            }
            else
            {
                $sql = "select a.ExamClassroomID,a.ID as ID_exam,a.ExamDate,a.ExamStart as StartSessions,a.ExamEnd as EndSessions,TIMEDIFF(CONCAT(curdate(),' ',a.ExamEnd), CONCAT(curdate(),' ',a.ExamStart)) as time,
                        b.Name as NamaDosen,c.ScheduleID,d.NameEng as NamaHari,e.Room,f.MKID,g.NameEng as NamaMataKuliah
                        from db_academic.exam as a
                        join db_employees.employees as b
                        on a.Pengawas1 = b.NIP
                        join db_academic.exam_details as c
                        on a.ID = c.ExamID
                        join db_academic.days as d
                        on d.ID = a.DayID
                        join db_academic.classroom as e
                        on e.ID = a.ExamClassroomID
                        join db_academic.schedule_details_course as f
                        on f.ScheduleID = c.ScheduleID
                        join db_academic.mata_kuliah as g
                        on g.ID = f.MKID
                        where d.NameEng = ?
                        and a.ExamDate = '".$date2."'
                        and a.`Status` = '1'
                        and a.SemesterID = '".$SemesterID."'
                        group by c.ScheduleID
                ";
            }
   
        $query=$this->db->query($sql, array($NameDay))->result_array();
        // print_r($query);die(); 

        $date = ($date == null) ? '' : '  and DATE_FORMAT(a.`Start`,"%Y-%m-%d") = "'.$date.'"';
        $sql2 = 'select a.*,b.Name from db_reservation.t_booking as a
         join db_employees.employees as b on a.CreatedBy = b.NIP
         where a.Status in(0,1)  '.$date;
        $query2=$this->db->query($sql2, array())->result_array();

        if (count($queryWaktu) == 0 && count($queryWaktu2) == 0 ) {
           $sql3 = 'select a.Room,b.NameEng,c.StartSessions,c.EndSessions,TIMEDIFF(CONCAT(curdate()," ",EndSessions), CONCAT(curdate()," ",StartSessions)) as time,
                   f.Name as NamaDosen,h.NameEng as NamaMataKuliah,e.ID as ScheduleID 
                   from db_academic.classroom as a join db_academic.schedule_exchange as c
                   on a.ID = c.ClassroomID
                   join db_academic.days as b
                   on c.DayID = b.ID 
                   left join db_academic.attendance as d on d.ID = c.ID_Attd
                   left join db_academic.schedule as e on e.ID = d.ScheduleID
                   left join db_employees.employees as f on e.Coordinator = f.NIP
                   left join (select * from db_academic.schedule_details_course group by ScheduleID) as g on g.ScheduleID = e.ID
                   left join db_academic.mata_kuliah as h on h.ID = g.MKID
                   where c.Status = "2" and c.Date ="'.$date2.'"';
            $query3=$this->db->query($sql3, array())->result_array();       
        }
        else
        {
            $query3 = array();
        }


        

        for ($i=0; $i < count($query); $i++) { 
            $time = $query[$i]['time'];
            $time = explode(':', $time);
            $time = ($time[0] * 60) + $time[1];
            $colspan = $time / 30;
            $colspan = (int)$colspan;
            $a = $time % 30;
            if ($a > 0) {
                $colspan++;
            }

            $start = $query[$i]['StartSessions'];
            $start = explode(':', $start);
            $start = $start[0].':'.$start[1];
            $startWr = $start;
            $end = $query[$i]['EndSessions'];
            $end = explode(':', $end);
            $end = $end[0].':'.$end[1];
            $endWr = $end;

            // get jumlah Mahasiswa
            $arrMhs = $this->m_api->__getStudentByScheduleID($query[$i]['ScheduleID']);
            $jumlahMHS = count($arrMhs);

            $agenda = 'Study';
            if (count($queryWaktu) > 0 || count($queryWaktu2) > 0) {
                $agenda = 'Exam';
            }

                $varprocess = function($time)
                {
                    $t = explode(':', $time);
                    $t2 = $t[1];
                    $t1 = $t[0];
                    $bool = 1;
                    if ($t2 != '00' && $t2 != '30') {
                        $t2 = (int) $t2;
                        if ($t2 > 0 && $t2 < 30) {
                            $t2 = '30';
                            $bool = 0;
                        }
                        elseif ($t2 > 30) {
                            $t2 = '00';
                            $t1 = (int) $t1;
                            $t1++;
                            // make two digit
                            $l = strlen($t1);
                            for ($a=0; $a < 2-$l; $a++) { 
                                $t1 = '0'.$t1;
                            }
                            $bool = 0;

                        }
                    }
                    $arr = array('bool' => $bool,'time' => $t1.':'.$t2);
                    return $arr;
                };

                $timeGet1 = $varprocess($start);
                $start = $timeGet1['time'];
                $timeGet2 = $varprocess($end);
                $end = $timeGet2['time'];

                $chkcolspan = $timeGet1['bool'] * $timeGet2['bool'];
                if ($timeGet1['bool'] == 0) {
                    $colspan = $colspan - 1;
                }

            $dt = array(
                'user'  => 'Academic TimeTables',
                'start' => $start,
                'startWr' => $startWr,
                'end'   => $end,
                'endWr'   => $endWr,
                'time'  => $time,
                'colspan' => $colspan,
                'agenda' => $agenda,
                'room' => $query[$i]['Room'],
                'approved' => 1,
                'NIP' => '0',
                'ID' => '0',
                'NamaMataKuliah' => $query[$i]['NamaMataKuliah'],
                'NamaDosen' => $query[$i]['NamaDosen'],
                'jumlahMHS' => $jumlahMHS
            );
            $arr_result[] = $dt;
        }

        for ($i=0; $i < count($query2); $i++) { 
            $dt = array(
                'user'  => $query2[$i]['Name'],
                'start' => $query2[$i]['Start'],
                'startWr' => $query2[$i]['Start'],
                'end'   => $query2[$i]['End'],
                'endWr'   => $query2[$i]['End'],
                'time'  => $query2[$i]['Time'],
                'colspan' => $query2[$i]['Colspan'],
                'agenda' => $query2[$i]['Agenda'],
                'room' => $query2[$i]['Room'],
                'approved' => $query2[$i]['Status'],
                'NIP' => $query2[$i]['CreatedBy'],
                'ID' => $query2[$i]['ID'],
                //'NameEng' => $query[$i]['NameEng'],
            );
            $arr_result[] = $dt;
        }

        for ($i=0; $i < count($query3); $i++) { 
            $time = $query3[$i]['time'];
            $time = explode(':', $time);
            $time = ($time[0] * 60) + $time[1];
            $colspan = $time / 30;
            $colspan = (int)$colspan;
            $a = $time % 30;
            if ($a > 0) {
                $colspan++;
            }

            $start = $query3[$i]['StartSessions'];
            $start = explode(':', $start);
            $start = $start[0].':'.$start[1];
            $startWr = $start;
            $end = $query3[$i]['EndSessions'];
            $end = explode(':', $end);
            $end = $end[0].':'.$end[1];
            $endWr = $end;

            // get jumlah Mahasiswa
            // $this->load->model('m_api');
            $arrMhs = $this->m_api->__getStudentByScheduleID($query3[$i]['ScheduleID']);
            $jumlahMHS = count($arrMhs);

            $varprocess = function($time)
            {
                $t = explode(':', $time);
                $t2 = $t[1];
                $t1 = $t[0];
                $bool = 1;
                if ($t2 != '00' && $t2 != '30') {
                    $t2 = (int) $t2;
                    if ($t2 > 0 && $t2 < 30) {
                        $t2 = '30';
                        $bool = 0;
                    }
                    elseif ($t2 > 30) {
                        $t2 = '00';
                        $t1 = (int) $t1;
                        $t1++;
                        // make two digit
                        $l = strlen($t1);
                        for ($a=0; $a < 2-$l; $a++) { 
                            $t1 = '0'.$t1;
                        }
                        $bool = 0;

                    }
                }
                $arr = array('bool' => $bool,'time' => $t1.':'.$t2);
                return $arr;
            };

            $timeGet1 = $varprocess($start);
            $start = $timeGet1['time'];
            $timeGet2 = $varprocess($end);
            $end = $timeGet2['time'];

            $chkcolspan = $timeGet1['bool'] * $timeGet2['bool'];
            if ($timeGet1['bool'] == 0) {
                $colspan = $colspan - 1;
            }

            $dt = array(
                'user'  => 'Academic TimeTables EX',
                'start' => $start,
                'startWr' => $startWr,
                'end'   => $end,
                'endWr'   => $endWr,
                'time'  => $time,
                'colspan' => $colspan,
                'agenda' => 'Study',
                'room' => $query3[$i]['Room'],
                'approved' => 1,
                'NIP' => '0',
                'ID' => '0',
                'NamaMataKuliah' => $query3[$i]['NamaMataKuliah'],
                'NamaDosen' => $query3[$i]['NamaDosen'],
                'jumlahMHS' => $jumlahMHS
                //'NameEng' => $query[$i]['NameEng'],
            );
            $arr_result[] = $dt;
        }

        return $arr_result;
    }

    public function checkBentrok($Start,$End,$chk_e_multiple,$Room,$NotIDMyself = '')
    {
        $bool = true;
        $NotIDMyself = ($NotIDMyself == '') ? '' : ' and a.ID != '.$NotIDMyself;

        $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
        $SemesterID = $SemesterID[0]['ID'];

        for ($xx=0; $xx < 1; $xx++) {  // check twice

            $TimeStart = date("H:i:s", strtotime($Start));
            $TimeEnd = date("H:i:s", strtotime($End));

            if ($chk_e_multiple == '') {
                // check academic timeline
                $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $Start);
                $NameDay = $datetime->format('l');
                $date2 = date("Y-m-d", strtotime($Start));

                // cek academic years apakah periode ujian atau tidak
                    $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
                    $SemesterID = $SemesterID[0]['ID'];

                    $sqlWaktu = 'select * from db_academic.academic_years where SemesterID = ? and (utsStart <="'.$date2.'" and utsEnd >= "'.$date2.'")';
                    $queryWaktu=$this->db->query($sqlWaktu, array($SemesterID))->result_array(); // dalam ujian

                    $sqlWaktu2 = 'select * from db_academic.academic_years where SemesterID = ? and (uasStart <="'.$date2.'" and uasEnd >= "'.$date2.'")';
                    $queryWaktu2=$this->db->query($sqlWaktu2, array($SemesterID))->result_array(); // dalam ujian

                if (count($queryWaktu) == 0) {
                    if (count($queryWaktu2) > 0) {
                        $sql = "select count(*) as total from
                               (select a.ExamClassroomID,a.ID as ID_exam
                                from db_academic.exam as a
                                join db_employees.employees as b
                                on a.Pengawas1 = b.NIP
                                join db_academic.exam_details as c
                                on a.ID = c.ExamID
                                join db_academic.days as d
                                on d.ID = a.DayID
                                join db_academic.classroom as e
                                on e.ID = a.ExamClassroomID
                                join db_academic.schedule_details_course as f
                                on f.ScheduleID = c.ScheduleID
                                join db_academic.mata_kuliah as g
                                on g.ID = f.MKID
                                where d.NameEng = '".$NameDay."'
                                and a.ExamDate = '".$date2."'
                                and a.`Status` = '1'
                                and a.SemesterID = '".$SemesterID."'
                                and (
                                    (a.ExamStart >= '".$TimeStart."'  and a.ExamStart < '".$TimeEnd."' ) 
                                   or  (a.ExamEnd > '".$TimeStart."'  and a.ExamEnd <= '".$TimeEnd."' )
                                   or (
                                            a.ExamStart <= '".$TimeStart."' and a.ExamEnd >= '".$TimeEnd."'
                                         )
                                ) and e.Room  = '".$Room."' 
                                group by c.ScheduleID
                               )
                                aa
                        ";
                    }
                    else
                    {
                        $sql = 'select count(*) as total from db_academic.classroom as a join db_academic.schedule_details as c
                                on a.ID = c.ClassroomID
                                join db_academic.days as b
                                on c.DayID = b.ID
                                left join db_academic.schedule as zd on zd.ID = c.ScheduleID
                                where "'.$date2.'" >= (select z.kuliahStart from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) 
                                and "'.$date2.'" <= (select z.kuliahEnd from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) and zd.SemesterID = "'.$SemesterID.'"
                                and b.NameEng = "'.$NameDay.'" and (
                                    (c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )
                                    or (
                                            c.StartSessions <= "'.$TimeStart.'" and c.EndSessions >= "'.$TimeEnd.'"
                                         )
                                ) and a.Room = "'.$Room.'" and c.ScheduleID not in (select a.ScheduleID from db_academic.attendance as a join db_academic.schedule_exchange as b
                        on a.ID = b.ID_Attd where b.Status = "2" and b.DateOriginal = "'.$date2.'")';
                        // print_r($sql);die();
                    }
                }
                else
                {
                    $sql = "select count(*) as total from
                               (select a.ExamClassroomID,a.ID as ID_exam
                                from db_academic.exam as a
                                join db_employees.employees as b
                                on a.Pengawas1 = b.NIP
                                join db_academic.exam_details as c
                                on a.ID = c.ExamID
                                join db_academic.days as d
                                on d.ID = a.DayID
                                join db_academic.classroom as e
                                on e.ID = a.ExamClassroomID
                                join db_academic.schedule_details_course as f
                                on f.ScheduleID = c.ScheduleID
                                join db_academic.mata_kuliah as g
                                on g.ID = f.MKID
                                where d.NameEng = '".$NameDay."'
                                and a.ExamDate = '".$date2."'
                                and a.`Status` = '1'
                                and a.SemesterID = '".$SemesterID."'
                                and (
                                    (a.ExamStart >= '".$TimeStart."'  and a.ExamStart < '".$TimeEnd."' ) 
                                   or  (a.ExamEnd > '".$TimeStart."'  and a.ExamEnd <= '".$TimeEnd."' )
                                    or (
                                            a.ExamStart <= '".$TimeStart."' and a.ExamEnd >= '".$TimeEnd."'
                                        )
                                ) and e.Room  = '".$Room."' 
                                group by c.ScheduleID
                               )
                                aa
                        ";
                } // exit cek date academic

                $query=$this->db->query($sql, array())->result_array();

                if ($query[0]['total'] > 0) {
                    $bool = false;

                }
                else
                {
                    $sql3 = 'select count(*) as total
                            from db_academic.classroom as a join db_academic.schedule_exchange as c
                            on a.ID = c.ClassroomID
                            join db_academic.days as b
                            on c.DayID = b.ID where c.Status = "2" and c.Date ="'.$date2.'" and (
                                (c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )
                                or (
                                            c.StartSessions <= "'.$TimeStart.'" and c.EndSessions >= "'.$TimeEnd.'"
                                        )
                            ) and a.Room = "'.$Room.'"';
                    //print_r($sql3);die();
                    $query3=$this->db->query($sql3, array())->result_array();
                    if ($query3[0]['total'] > 0) {
                        $bool = false;
                    }
                    else
                    {
                        $sql2 = 'select count(*) as total from db_reservation.t_booking as a
                                 join db_employees.employees as b on a.CreatedBy = b.NIP
                                 where a.Status in(0,1) and (
                                    (a.`Start` >= "'.$Start.'" and a.`Start` < "'.$End.'" ) or (a.`End` > "'.$Start.'" and a.`End` <= "'.$End.'" )
                                    or (
                                            a.`Start` <= "'.$Start.'" and a.`End` >= "'.$End.'"
                                        )
                                ) and a.Room = "'.$Room.'"'.' '.$NotIDMyself;
                        $query2=$this->db->query($sql2, array())->result_array();
                         if ($query2[0]['total'] > 0) {
                            $bool = false;
                         }
                         else
                         {
                            if (count($queryWaktu) == 0 && count($queryWaktu2) == 0) {
                                $sql = 'select count(*) as total from db_academic.classroom as a join db_academic.schedule_details as c
                                        on a.ID = c.ClassroomID
                                        join db_academic.days as b
                                        on c.DayID = b.ID
                                        left join db_academic.schedule as zd on zd.ID = c.ScheduleID
                                        where "'.$date2.'" >= (select z.kuliahStart from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) 
                                        and "'.$date2.'" <= (select z.kuliahEnd from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) and zd.SemesterID = "'.$SemesterID.'" 
                                        and b.NameEng = "'.$NameDay.'" and (
                                            (c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )
                                            or (
                                                c.StartSessions <= "'.$TimeStart.'" and c.EndSessions >= "'.$TimeEnd.'"
                                            )
                                        ) and a.Room = "'.$Room.'" and c.ScheduleID not in (select a.ScheduleID from db_academic.attendance as a join db_academic.schedule_exchange as b
                                on a.ID = b.ID_Attd where b.Status = "2" and b.DateOriginal = "'.$date2.'")';
                                $query=$this->db->query($sql, array())->result_array();
                                if ($query[0]['total'] > 0) {
                                   $bool = false;
                                }
                            }
                         } 
                    }
                          
                }
            }
            else
            {
                // // looping
                // // check academic timeline untuk data satu
                // $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $Start);
                // $NameDay = $datetime->format('l');
                // $date2 = date("Y-m-d", strtotime($Start));

                // $sql = 'select count(*) as total from db_academic.classroom as a join db_academic.schedule_details as c
                //         on a.ID = c.ClassroomID
                //         join db_academic.days as b
                //         on c.DayID = b.ID
                //         left join db_academic.schedule as zd on zd.ID = c.ScheduleID
                //         where "'.$date2.'" >= (select z.kuliahStart from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) 
                //         and "'.$date2.'" <= (select z.kuliahEnd from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) and zd.SemesterID = "'.$SemesterID.'" 
                //         and b.NameEng = "'.$NameDay.'" and ((c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )) and a.Room = "'.$Room.'" and c.ID not in (select a.ScheduleID from db_academic.attendance as a join db_academic.schedule_exchange as b
                // on a.ID = b.ID_Attd where b.Status = "2" and b.DateOriginal = "'.$date2.'")';
                // $query=$this->db->query($sql, array())->result_array();

                // if ($query[0]['total'] > 0) {
                //     $bool = false;

                // }
                // else
                // {
                //     // $sql3 = 'select count(*) as total
                //     //         from db_academic.classroom as a join db_academic.schedule_exchange as c
                //     //         on a.ID = c.ClassroomID
                //     //         join db_academic.days as b
                //     //         on c.DayID = b.ID where c.Status = "1" and c.Date ="'.$date2.'"';
                //     // $query3=$this->db->query($sql3, array())->result_array();
                //     $sql3 = 'select count(*) as total
                //             from db_academic.classroom as a join db_academic.schedule_exchange as c
                //             on a.ID = c.ClassroomID
                //             join db_academic.days as b
                //             on c.DayID = b.ID where c.Status = "2" and c.Date ="'.$date2.'" and ((c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )) and a.Room = "'.$Room.'"';
                //     //print_r($sql3);die();
                //     $query3=$this->db->query($sql3, array())->result_array();
                //     if ($query3[0]['total'] > 0) {
                //         $bool = false;
                //     }
                //     else
                //     {
                //         $sql2 = 'select count(*) as total from db_reservation.t_booking as a
                //                  join db_employees.employees as b on a.CreatedBy = b.NIP
                //                  where a.Status = 1 and ((a.`Start` >= "'.$Start.'" and a.`Start` < "'.$End.'" ) or (a.`End` > "'.$Start.'" and a.`End` <= "'.$End.'" )) and a.Room = "'.$Room.'"'.' '.$NotIDMyself;
                //         $query2=$this->db->query($sql2, array())->result_array();
                //          if ($query2[0]['total'] > 0) {
                //             $bool = false;
                //          } 
                //     }
                          
                // }

                // for ($i=0; $i < count($chk_e_multiple) ; $i++) { 
                //     $getDate = $chk_e_multiple[$i];
                //     $Start = $chk_e_multiple[$i].' '.$TimeStart;
                //     $End = $chk_e_multiple[$i].' '.$TimeEnd;
                //     // check academic timeline
                //     $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $Start);
                //     $NameDay = $datetime->format('l');

                //     $sql = 'select count(*) as total from db_academic.classroom as a join db_academic.schedule_details as c
                //             on a.ID = c.ClassroomID
                //             join db_academic.days as b
                //             on c.DayID = b.ID
                //             left join db_academic.schedule as zd on zd.ID = c.ScheduleID
                //             where "'.$getDate.'" >= (select z.kuliahStart from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) 
                //             and "'.$getDate.'" <= (select z.kuliahEnd from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) and zd.SemesterID = "'.$SemesterID.'"
                //             and b.NameEng = "'.$NameDay.'" and ((c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )) and a.Room = "'.$Room.'" and c.ID not in (select a.ScheduleID from db_academic.attendance as a join db_academic.schedule_exchange as b
                //              on a.ID = b.ID_Attd where b.Status = "2" and b.DateOriginal = "'.$getDate.'")';
                //     $query=$this->db->query($sql, array())->result_array();
                //     if ($query[0]['total'] > 0) {
                //         $bool = false;
                //         break;

                //     }
                //     else
                //     {
                //         // $sql3 = 'select count(*) as total
                //         //         from db_academic.classroom as a join db_academic.schedule_exchange as c
                //         //         on a.ID = c.ClassroomID
                //         //         join db_academic.days as b
                //         //         on c.DayID = b.ID where c.Status = "1" and c.Date ="'.$getDate.'"';
                //         // $query3=$this->db->query($sql3, array())->result_array();
                //         $sql3 = 'select count(*) as total
                //                 from db_academic.classroom as a join db_academic.schedule_exchange as c
                //                 on a.ID = c.ClassroomID
                //                 join db_academic.days as b
                //                 on c.DayID = b.ID where c.Status = "2" and c.Date ="'.$getDate.'" and ((c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )) and a.Room = "'.$Room.'"';
                //         //print_r($sql3);die();
                //         $query3=$this->db->query($sql3, array())->result_array();
                //         if ($query3[0]['total'] > 0) {
                //             $bool = false;
                //         }
                //         else
                //         {
                //             $sql2 = 'select count(*) as total from db_reservation.t_booking as a
                //                      join db_employees.employees as b on a.CreatedBy = b.NIP
                //                      where a.Status in(0,1) and ((a.`Start` >= "'.$Start.'" and a.`Start` < "'.$End.'" ) or (a.`End` > "'.$Start.'" and a.`End` <= "'.$End.'" )) and a.Room = "'.$Room.'" '.$NotIDMyself;
                //             $query2=$this->db->query($sql2, array())->result_array();
                //              if ($query2[0]['total'] > 0) {
                //                 $bool = false;
                //                 break;
                //              } 
                //         }
                              
                //     }
                // }
            }
            usleep( 500 );
        }

        return $bool;
    }

    public function checkBentrok2($Start,$End,$chk_e_multiple,$Room,$NotIDMyself = '')
    {
        $array_bool = array('bool' => true);
        $NotIDMyself = ($NotIDMyself == '') ? '' : ' and a.ID != '.$NotIDMyself;

        $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
        $SemesterID = $SemesterID[0]['ID'];

        for ($xx=0; $xx < 1; $xx++) {  // check twice

            $TimeStart = date("H:i:s", strtotime($Start));
            $TimeEnd = date("H:i:s", strtotime($End));

            // check academic timeline
            $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $Start);
            $NameDay = $datetime->format('l');
            $date2 = date("Y-m-d", strtotime($Start));

            // cek academic years apakah periode ujian atau tidak
                $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
                $SemesterID = $SemesterID[0]['ID'];

                $sqlWaktu = 'select * from db_academic.academic_years where SemesterID = ? and (utsStart <="'.$date2.'" and utsEnd >= "'.$date2.'")';
                $queryWaktu=$this->db->query($sqlWaktu, array($SemesterID))->result_array();

                $sqlWaktu2 = 'select * from db_academic.academic_years where SemesterID = ? and (uasStart <="'.$date2.'" and uasEnd >= "'.$date2.'")';
                $queryWaktu2=$this->db->query($sqlWaktu2, array($SemesterID))->result_array();

            if (count($queryWaktu) == 0) {
                if (count($queryWaktu2) > 0) {
                    $sql = "select count(*) as total from
                           (select a.ExamClassroomID,a.ID as ID_exam
                            from db_academic.exam as a
                            join db_employees.employees as b
                            on a.Pengawas1 = b.NIP
                            join db_academic.exam_details as c
                            on a.ID = c.ExamID
                            join db_academic.days as d
                            on d.ID = a.DayID
                            join db_academic.classroom as e
                            on e.ID = a.ExamClassroomID
                            join db_academic.schedule_details_course as f
                            on f.ScheduleID = c.ScheduleID
                            join db_academic.mata_kuliah as g
                            on g.ID = f.MKID
                            where d.NameEng = '".$NameDay."'
                            and a.ExamDate = '".$date2."'
                            and a.`Status` = '1'
                            and a.SemesterID = '".$SemesterID."'
                            and (
                                (a.ExamStart >= '".$TimeStart."'  and a.ExamStart < '".$TimeEnd."' ) 
                               or  (a.ExamEnd > '".$TimeStart."'  and a.ExamEnd <= '".$TimeEnd."' )
                               or (
                                            a.ExamStart <= '".$TimeStart."' and a.ExamEnd >= '".$TimeEnd."'
                                  )
                            ) and e.Room  = '".$Room."' 
                            group by c.ScheduleID
                           )
                            aa
                    ";
                }
                else
                {
                    $sql = 'select count(*) as total from db_academic.classroom as a join db_academic.schedule_details as c
                            on a.ID = c.ClassroomID
                            join db_academic.days as b
                            on c.DayID = b.ID
                            left join db_academic.schedule as zd on zd.ID = c.ScheduleID
                            where "'.$date2.'" >= (select z.kuliahStart from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) 
                            and "'.$date2.'" <= (select z.kuliahEnd from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) and zd.SemesterID = "'.$SemesterID.'"
                            and b.NameEng = "'.$NameDay.'" and (
                                (c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )
                                or (
                                            c.StartSessions <= "'.$TimeStart.'" and c.EndSessions >= "'.$TimeEnd.'"
                                  )
                            ) and a.Room = "'.$Room.'" and c.ID not in (select a.ScheduleID from db_academic.attendance as a join db_academic.schedule_exchange as b
                    on a.ID = b.ID_Attd where b.Status = "2" and b.DateOriginal = "'.$date2.'")';
                }
            }
            else
            {
                $sql = "select count(*) as total from
                           (select a.ExamClassroomID,a.ID as ID_exam
                            from db_academic.exam as a
                            join db_employees.employees as b
                            on a.Pengawas1 = b.NIP
                            join db_academic.exam_details as c
                            on a.ID = c.ExamID
                            join db_academic.days as d
                            on d.ID = a.DayID
                            join db_academic.classroom as e
                            on e.ID = a.ExamClassroomID
                            join db_academic.schedule_details_course as f
                            on f.ScheduleID = c.ScheduleID
                            join db_academic.mata_kuliah as g
                            on g.ID = f.MKID
                            where d.NameEng = '".$NameDay."'
                            and a.ExamDate = '".$date2."'
                            and a.`Status` = '1'
                            and a.SemesterID = '".$SemesterID."'
                            and (
                                (a.ExamStart >= '".$TimeStart."'  and a.ExamStart < '".$TimeEnd."' ) 
                               or  (a.ExamEnd > '".$TimeStart."'  and a.ExamEnd <= '".$TimeEnd."' )
                               or (
                                           a.ExamStart <= '".$TimeStart."' and a.ExamEnd >= '".$TimeEnd."'
                                 )
                            ) and e.Room  = '".$Room."' 
                            group by c.ScheduleID
                           )
                            aa
                    ";
            } // exit cek date academic

            $query=$this->db->query($sql, array())->result_array();

            if ($query[0]['total'] > 0) {
                $array_bool = array(
                    'type' => 'academic',
                    'bool' => false
                );
            }
            else
            {
                $sql3 = 'select count(*) as total
                        from db_academic.classroom as a join db_academic.schedule_exchange as c
                        on a.ID = c.ClassroomID
                        join db_academic.days as b
                        on c.DayID = b.ID where c.Status = "2" and c.Date ="'.$date2.'" and (
                            (c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )
                            or (
                                           c.StartSessions <= "'.$TimeStart.'" and c.EndSessions >= "'.$TimeEnd.'"
                                 )
                        ) and a.Room = "'.$Room.'"';
                //print_r($sql3);die();
                $query3=$this->db->query($sql3, array())->result_array();
                if ($query3[0]['total'] > 0) {
                    $array_bool = array(
                        'type' => 'academic',
                        'bool' => false
                    );

                }
                else
                {
                    $sql2 = 'select count(*) as total from db_reservation.t_booking as a
                             join db_employees.employees as b on a.CreatedBy = b.NIP
                             where a.Status in(0,1) and (
                                (a.`Start` >= "'.$Start.'" and a.`Start` < "'.$End.'" ) or (a.`End` > "'.$Start.'" and a.`End` <= "'.$End.'" ) or (
                                            a.Start <= "'.$Start.'" and a.end >= "'.$End.'"
                                         )
                            ) and a.Room = "'.$Room.'"'.' '.$NotIDMyself;
                    $query2=$this->db->query($sql2, array())->result_array();
                     if ($query2[0]['total'] > 0) {
                        $array_bool = array(
                            'type' => 'venue',
                            'bool' => false
                        );
                     }
                     else
                     {
                        if (count($queryWaktu) == 0 && count($queryWaktu2) == 0) {
                            $sql = 'select count(*) as total from db_academic.classroom as a join db_academic.schedule_details as c
                                    on a.ID = c.ClassroomID
                                    join db_academic.days as b
                                    on c.DayID = b.ID
                                    left join db_academic.schedule as zd on zd.ID = c.ScheduleID
                                    where "'.$date2.'" >= (select z.kuliahStart from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) 
                                    and "'.$date2.'" <= (select z.kuliahEnd from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) and zd.SemesterID = "'.$SemesterID.'" 
                                    and b.NameEng = "'.$NameDay.'" and (
                                        (c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )
                                        or (
                                                c.StartSessions <= "'.$TimeStart.'" and c.EndSessions >= "'.$TimeEnd.'"
                                             )
                                    ) and a.Room = "'.$Room.'" and c.ID not in (select a.ScheduleID from db_academic.attendance as a join db_academic.schedule_exchange as b
                            on a.ID = b.ID_Attd where b.Status = "2" and b.DateOriginal = "'.$date2.'")';
                            // print_r($sql);die();
                            $query=$this->db->query($sql, array())->result_array();
                            if ($query[0]['total'] > 0) {
                               $array_bool = array(
                                   'type' => 'academic',
                                   'bool' => false
                               );
                            }
                        }
                        
                     } 
                }
                      
            }
            usleep( 500 );
        }

        return $array_bool;
    }

    public function getCountApprove()
    {
        $sql = 'select count(*) as total from db_reservation.t_booking where Status = 0 and Start >= timestamp(DATE_SUB(NOW(), INTERVAL 30 MINUTE))';
        $query=$this->db->query($sql, array())->result_array();
        return $query[0]['total'];

    }

    // public function getDataT_bookingByUser($Start = null,$Status = 0,$both = '')
    // {
    //     $arr_result = array();
    //     $this->load->model('master/m_master');
    //     $Start = ($Start == null) ? ' and Start >= timestamp(DATE_SUB(NOW(), INTERVAL 30 MINUTE))' : ' and Start like "%'.$Start.'%"';
    //     if ($both == '') {
    //         $sql = 'select a.*,b.Name from db_reservation.t_booking as a join db_employees.employees as b on a.CreatedBy = b.NIP where a.Status = ?
    //                  '.$Start.' and a.CreatedBy = ? ';
    //         $query=$this->db->query($sql, array($Status,$this->session->userdata('NIP')))->result_array();         
    //     }
    //     else
    //     {
    //         $sql = 'select a.*,b.Name from db_reservation.t_booking as a join db_employees.employees as b on a.CreatedBy = b.NIP where a.Status like "%"
    //                  '.$Start.' and a.CreatedBy = ? ';
    //         $query=$this->db->query($sql, array($this->session->userdata('NIP')))->result_array();           
    //     }
        
        
    //     // print_r($query);die();
    //     for ($i=0; $i < count($query); $i++) { 
    //         $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['Start']);
    //         $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['End']);
    //         $StartNameDay = $Startdatetime->format('l');
    //         $EndNameDay = $Enddatetime->format('l');
    //         $Time = $query[$i]['Time'].' Minutes';
    //         $ID_equipment_add = '-';
    //         $Name_equipment_add = '-';
    //         if ($query[$i]['ID_equipment_add'] != '' || $query[$i]['ID_equipment_add'] != null) {
    //             $ID_equipment_add = explode(',', $query[$i]['ID_equipment_add']);
    //             $Name_equipment_add = '<ul>';
    //             for ($j=0; $j < count($ID_equipment_add); $j++) { 
    //                 $get = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_add[$j]);
    //                 // print_r($ID_equipment_add);die();
    //                 $ID_m_equipment = $get[0]['ID_m_equipment'];
    //                 $Owner = $get[0]['Owner'];
    //                 $getX = $this->m_master->caribasedprimary('db_employees.division','ID',$Owner);
    //                 $Owner = $getX[0]['Division'];

    //                 $Qty = $get[0]['Qty'];
    //                 $get = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
    //                 $Name_equipment_add .= '<li>'.$get[0]['Equipment'].' by '.$Owner.'['.$Qty.']</li>';
    //             }
    //             $Name_equipment_add .= '</ul>';
    //         }

    //         $ID_add_personel = '-';
    //         $Name_add_personel = '-';
    //         if ($query[$i]['ID_add_personel'] != '' || $query[$i]['ID_add_personel'] != null) {
    //             $ID_add_personel = explode(',', $query[$i]['ID_add_personel']);
    //             $Name_add_personel = '<ul>';
    //             for ($j=0; $j < count($ID_add_personel); $j++) { 
    //                 $get = $this->m_master->caribasedprimary('db_employees.division','ID',$ID_add_personel[$j]);
    //                 $Name_add_personel .= '<li>'.$get[0]['Division'].'</li>';
    //             }

    //             $Name_add_personel .= '</ul>';
    //         }

    //         $Reqdatetime = DateTime::createFromFormat('Y-m-d', $query[$i]['Req_date']);
    //         $ReqdateNameDay = $Reqdatetime->format('l');

    //         $MarkomSupport = '<label>No</Label>';
    //         if ($query[$i]['MarcommSupport'] != '') {
    //             $MarkomSupport = '<ul>';
    //             $dd = explode(',', $query[$i]['MarcommSupport']);
    //                 $dzx = array();
    //             // split for note
    //                 for ($xz=0; $xz < count($dd); $xz++) { 
    //                     $pos1 = stripos($dd[$xz], 'Note');
    //                     $exitLoop = false;
    //                     if ($pos1 !== false) {
    //                         $temp = array();
    //                         for ($ixx = $xz; $ixx < count($dd); $ixx++) { 
    //                             $temp[] = $dd[$ixx];
    //                         }
    //                         $dzx[] = implode(',', $temp);
    //                         $exitLoop = true;
    //                     }
    //                     else
    //                     {
    //                         $dzx[] = $dd[$xz];
    //                     }

    //                     if ($exitLoop) {
    //                         break;
    //                     }
    //                 }

    //                 $dd = $dzx;
    //             for ($zx=0; $zx < count($dd); $zx++) {
    //                 $a = 'How are you?';

    //                 if (strpos($dd[$zx], 'Graphic Design') !== false) {
    //                      $pos = strpos($dd[$zx],'[');
    //                      $li = substr($dd[$zx], 0,$pos);
    //                      $posE = strpos($dd[$zx],']');
    //                      $ISIe = substr($dd[$zx], ($pos+1), $posE);
    //                      $length = strlen($ISIe);
    //                      $ISIe = substr($ISIe, 0, ($length - 1));
    //                      // print_r($ISIe);die();
    //                      $MarkomSupport .= '<li>'.$li;
    //                      $FileMarkom = explode(';', $ISIe);
    //                      $MarkomSupport .= '<ul>';
    //                      for ($vc=0; $vc < count($FileMarkom); $vc++) { 
    //                         $MarkomSupport .= '<li>'.'<a href="'.base_url("fileGetAny/vreservation-".$FileMarkom[$vc]).'" target="_blank"></i>'.$FileMarkom[$vc].'</a>';
    //                      }
    //                      $MarkomSupport .= '</ul></li>';
    //                 } 
    //                 else{
    //                   $MarkomSupport .= '<li>'.$dd[$zx].'</li>';  
    //                 }
                    
    //             }
    //             $MarkomSupport .= '</ul>';

    //         }

    //         $arr_result[] = array(
    //                 'Start' => $StartNameDay.', '.$query[$i]['Start'],
    //                 'End' => $EndNameDay.', '.$query[$i]['End'],
    //                 'Time' => $Time,
    //                 'Agenda' => $query[$i]['Agenda'],
    //                 'Room' => $query[$i]['Room'],
    //                 'Equipment_add' => $Name_equipment_add,
    //                 'Persone_add' => $Name_add_personel,
    //                 'Req_date' => $query[$i]['Name'].'<br>'.$ReqdateNameDay.', '.$query[$i]['Req_date'],
    //                 'Req_layout' => $query[$i]['Req_layout'],
    //                 'ID' => $query[$i]['ID'],
    //                 'Status' => $query[$i]['Status'],
    //                 'MarkomSupport' => $MarkomSupport
    //         );
    //     }

    //     return $arr_result;         
    // }

    public function getForReturn_eq()
    {
        $arr_result = array();
        $this->load->model('master/m_master');
            $add_where = '';

            $DivisionID = $this->session->userdata('PositionMain');
            $DivisionID = $DivisionID['IDDivision'];
            // print_r($DivisionID);
        $StatusQ = 'a.Status = 1';     
        
        $sql = 'select a.*,b.Name from db_reservation.t_booking as a join db_employees.employees as b on a.CreatedBy = b.NIP
                join db_reservation.t_booking_eq_additional as c on a.ID = c.ID_t_booking
                 where '.$StatusQ.''.$add_where.' and c.ID not in (select ID_t_booking_eq_additional from db_reservation.t_return_eq) and c.Status = 1 group by a.ID
                order by a.Status asc,a.Start asc';
                // print_r($sql);die();
        $query=$this->db->query($sql, array())->result_array();
        $NIP = '@';
        for ($i=0; $i < count($query); $i++) {
            $Detail = $this->getDataPass($query[$i]['ID']);
            $Detail = implode('@@', $Detail);
            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');
            $Time = $query[$i]['Time'].' Minutes';

            $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$query[$i]['Room']);
            // cek ApproveAccess
            $Status1 = $query[$i]['Status1'];
            $Status = $query[$i]['Status'];
            //$MarcommStatus = $query[$i]['MarcommStatus'];
                $ApproveAccess = function($getRoom,$Status1,$Status,$CreatedBy,$DivisionID){
                    // if ($MarcommStatus == 1) {
                    //     return $find = 0;
                    // }

                    $PositionMain = '@';
                    $IDDivision = $DivisionID;
                    $Position = '@';
                    $NIP = '@';
                    // get Category Room to approver
                        $ApproveAccess = 0;
                        $ID_group_user = $this->session->userdata('ID_group_user');
                        $getPolicy = $this->m_master->caribasedprimary('db_reservation.cfg_policy','ID_group_user',$ID_group_user);
                        $CategoryRoom = $getPolicy[0]['CategoryRoom'];
                        $CategoryRoom = json_decode($CategoryRoom);
                        $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
                        $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
                        // find access
                            $find = 1;
                                if ($find == 1) {
                                    // get status 
                                    if ($Status1 == 0) {
                                       // find approver1
                                           $Approver1 = $getDataCategoryRoom[0]['Approver1'];
                                           $Approver1 = json_decode($Approver1);

                                           $dataApprover = array();
                                           // find by ID_group_user
                                                // $CreatedBy = $query[$i]['CreatedBy'];
                                                $cc = $this->m_master->caribasedprimary('db_reservation.previleges_guser','NIP',$CreatedBy);
                                                $dd = $this->m_master->caribasedprimary('db_employees.employees','NIP',$CreatedBy);
                                                $ID_group_user = (count($cc) > 0) ? $cc[0]['G_user'] : '';
                                            // stop loop
                                            $getLoop = true;    
                                           for ($l=0; $l < count($Approver1); $l++) {
                                                   if ($ID_group_user == $Approver1[$l]->UserType) {
                                                       // get TypeApprover
                                                       $TypeApprover = $Approver1[$l]->TypeApprover;
                                                       switch ($TypeApprover) {
                                                           case 'Position':
                                                               // get Division to access position approval
                                                                   $DivisionCreated = $dd[0]['PositionMain'];
                                                                   $DivisionCreated = explode(".", $DivisionCreated);

                                                                   $IDPositionApprover = $Approver1[$l]->Approver;

                                                                   if ($DivisionCreated[0] == 15) { // if prodi
                                                                       // find prodi
                                                                       $gg = $this->m_master->caribasedprimary('db_academic.program_study','AdminID',$CreatedBy);
                                                                       if (count($gg) > 0) {
                                                                           for ($k=0; $k < count($gg); $k++) { 
                                                                               $Kaprodi = $gg[$k]['KaprodiID'];
                                                                               if ($Kaprodi == $NIP) {
                                                                                   $find++;
                                                                                   $getLoop = false;     
                                                                                   break;
                                                                               }
                                                                           }
                                                                           
                                                                       }
                                                                    }
                                                                    else
                                                                    {
                                                                        // find by division and position
                                                                        if ($DivisionCreated[0] == $IDDivision) {
                                                                           // compare Position
                                                                           if ($IDPositionApprover == $Position) {
                                                                               $find++;
                                                                               $getLoop = false;    
                                                                               break;
                                                                           }
                                                                        }
                                                                    }
                                                               break;
                                                           
                                                           case 'Division':
                                                               if ($Approver1[$l]->Approver == $IDDivision) {
                                                                   $find++;
                                                                   $getLoop = false;    
                                                                   break;
                                                               }
                                                               break;

                                                           case 'Employees':
                                                               if ($NIP == $Approver1[$l]->Approver) {
                                                                   $find++;
                                                                   $getLoop = false;    
                                                                   break;
                                                               }
                                                               break;    
                                                       }
                                                   }

                                                   if (!$getLoop) { // stop loop
                                                       break;
                                                   }
                                           } // end loop for

                                    }
                                    else
                                    {
                                        $find = $find + 2;  
                                    }
                                }

                                if ($find == 3) {
                                   // find approver2
                                       $Approver2 = $getDataCategoryRoom[0]['Approver2'];
                                       $Approver2 = json_decode($Approver2);
                                       $DivisionID = $this->session->userdata('PositionMain');
                                       $DivisionID = $DivisionID['IDDivision'];
                                       if ($Status == 0) {
                                            for ($l=0; $l < count($Approver2); $l++) { 
                                                if ($DivisionID == $Approver2[$l]) {
                                                    $find++;    
                                                    break;
                                                }
                                            }
                                       }
                                       else
                                       {
                                        $find = $find + 2;
                                       }
                                       
                                }
                    $ApproveAccess = $find;
                    return $ApproveAccess;            
                };

                $StatusBooking = '';
                $CaseApproveAccess = $ApproveAccess($getRoom,$Status1,$Status,$query[$i]['CreatedBy'],$DivisionID);
                switch ($CaseApproveAccess) {
                    case 0:
                         $StatusBooking = 'Awaiting approval Marcomm Division';
                         break;
                    case 1:
                    case 2:
                        $NameApprover = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        $StatusBooking = 'Awaiting approval 1 : '.$NameApprover;
                        break;
                    case 3:
                    case 4:
                        $NameApprover = $this->Get_Approver(2,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        $StatusBooking = 'Awaiting approval 2 : '.$NameApprover;
                        break;
                    case 5:
                        $StatusBooking = 'Approved';
                        break;    
                    default:
                        # code...
                        break;
                }

            $ID_equipment_add = '-';
            $Name_equipment_add = '-';
            $boolAuthEq = 0;
            if ($query[$i]['ID_equipment_add'] != '' || $query[$i]['ID_equipment_add'] != null) {
                $ID_equipment_add = explode(',', $query[$i]['ID_equipment_add']);
                $Name_equipment_add = '<ul style = "margin-left : -28px">';
                $btnEquipment = '';
                for ($j=0; $j < count($ID_equipment_add); $j++) {
                    $btnEquipment = '';
                    // qty penggunaan
                    $gett_booking_eq_additional = $this->gett_booking_eq_additional($ID_equipment_add[$j],$query[$i]['ID']);
                    if ($gett_booking_eq_additional[0]['Status'] == 0) {
                        $Status_eq_additional = 'Not Confirm';
                    }
                    elseif ($gett_booking_eq_additional[0]['Status'] == 1) {
                        $Status_eq_additional = 'Confirm';
                    }
                    else
                    {
                        $Status_eq_additional = 'Reject';
                    }
                    $Qty = $gett_booking_eq_additional[0]['Qty'];
                    $ID_equipment_additional = $gett_booking_eq_additional[0]['ID_equipment_additional'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
                    $OwnerID = $get[0]['Owner'];
                    $getX = $this->m_master->caribasedprimary('db_employees.division','ID',$OwnerID);
                    $Owner = $getX[0]['Division'];
                        if ($this->session->userdata('ID_group_user') < 3) {
                            if ($query[$i]['Status'] != 1) {
                                $btnEquipment = '<input type = "checkbox" class = "chkEquipment_'.$query[$i]['ID'].'" value = "'.$ID_equipment_add[$j].'">';
                            }
                            if ($gett_booking_eq_additional[0]['Status'] == 1) {
                                if ($boolAuthEq == 0) {
                                    $boolAuthEq = 1;
                                }
                            }
                            
                        }
                        else
                        {
                            if ($DivisionID == $OwnerID) {
                                if ($query[$i]['Status'] != 1) {
                                    $btnEquipment = '<input type = "checkbox" class = "chkEquipment_'.$query[$i]['ID'].'" value = "'.$ID_equipment_add[$j].'">';
                                }
                                if ($gett_booking_eq_additional[0]['Status'] == 1) {
                                    if ($boolAuthEq == 0) {
                                        $boolAuthEq = 1;
                                    }
                                }
                                
                                
                            } 
                        }
                        

                    $ID_m_equipment = $get[0]['ID_m_equipment'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                    $Name_equipment_add .= '<li>'.$get[0]['Equipment'].' by '.$Owner.'['.$Qty.']&nbsp ('.$Status_eq_additional.') &nbsp'.$btnEquipment.'</li>';
                    // print_r('-div:-'.$DivisionID.'=OW:'.$OwnerID.'--ID:'.$query[$i]['ID'].'---');
                }
                $Name_equipment_add .= '</ul>';
                // print_r('---'.$boolAuthEq.'---');
                if ($boolAuthEq == 1) {
                    if ($query[$i]['Status'] != 1) {
                        $Name_equipment_add .= '<div class = "row"><div class = "col-md-6">
                                        <span class="btn btn-primary btn-xs btn_eq_additional_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Confirm">
                                            <i class="fa fa-pencil-square-o"></i> Confirm
                                           </span>
                                                </div>
                                            <div class = "col-md-6">
                                                <span class="btn btn-danger btn-xs btn_eq_additional_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Reject">
                                                    <i class="fa fa-pencil-square-o"></i> Reject
                                                   </span>
                                            </div>    
                                            </div>';
                    }
                }
                
            }

            $ID_add_personel = '-';
            $Name_add_personel = '-';

            if ($query[$i]['ID_add_personel'] != '' || $query[$i]['ID_add_personel'] != null) {
                $Name_add_personel = $query[$i]['ID_add_personel'];
            }

            $Reqdatetime = DateTime::createFromFormat('Y-m-d', $query[$i]['Req_date']);
            $ReqdateNameDay = $Reqdatetime->format('l');

            $MarkomSupport = '<label>No</Label>';
            $boolAuthMarkom = 0;
            if ($query[$i]['MarcommSupport'] != '') {
                $MarkomSupport = '<ul style = "margin-left : -28px">';
                $dd = explode(',', $query[$i]['MarcommSupport']);
                $dzx = array();
                // split for note
                    for ($xz=0; $xz < count($dd); $xz++) { 
                        $pos1 = stripos($dd[$xz], 'Note');
                        $exitLoop = false;
                        if ($pos1 !== false) {
                            $temp = array();
                            for ($ixx = $xz; $ixx < count($dd); $ixx++) { 
                                $temp[] = $dd[$ixx];
                            }
                            $dzx[] = implode(',', $temp);
                            $exitLoop = true;
                        }
                        else
                        {
                            $dzx[] = $dd[$xz];
                        }

                        if ($exitLoop) {
                            break;
                        }
                    }

                    $dd = $dzx;

                $btnMarkomSupport = '';
                for ($zx=0; $zx < count($dd); $zx++) {
                    $a = 'How are you?';
                    if ($this->session->userdata('ID_group_user') <= 3) {
                        if ($this->session->userdata('ID_group_user') != 3) {
                            $btnMarkomSupport = '<button class = "btn btn-danger btnMarkomSupport btn-xs" MarcommSupport = "'.$dd[$zx].'" ><i class="fa fa-times"></i> </button>';
                        }
                        else
                        {
                            if ($CaseApproveAccess == 2 || $CaseApproveAccess == 4) {
                                $btnMarkomSupport = '<button class = "btn btn-danger btnMarkomSupport btn-xs" MarcommSupport = "'.$dd[$zx].'" ><i class="fa fa-times"></i> </button>';
                            }
                        }
                        
                    }
                    else
                    {
                        if ($CaseApproveAccess == 0) {
                            $PositionMain = $this->session->userdata('PositionMain');
                            $IDDivision = $PositionMain['IDDivision'];
                            if ($IDDivision == 17) {
                                 $btnMarkomSupport = '<button class = "btn btn-danger btnMarkomSupport btn-xs" MarcommSupport = "'.$dd[$zx].'" ><i class="fa fa-times"></i> </button>';
                            } 
                            
                        }
                    }

                    if (strpos($dd[$zx], 'Graphic Design') !== false) {
                         $pos = strpos($dd[$zx],'[');
                         $li = substr($dd[$zx], 0,$pos);
                         $posE = strpos($dd[$zx],']');
                         $ISIe = substr($dd[$zx], ($pos+1), $posE);
                         $length = strlen($ISIe);
                         $ISIe = substr($ISIe, 0, ($length - 1));
                         // print_r($ISIe);die();
                         $MarkomSupport .= '<li>'.$li.'&nbsp'.$btnMarkomSupport;
                         $FileMarkom = explode(';', $ISIe);
                         $MarkomSupport .= '<ul style = "margin-left : -28px">';
                         for ($vc=0; $vc < count($FileMarkom); $vc++) { 
                            $MarkomSupport .= '<li>'.'<a href="'.base_url("fileGetAny/vreservation-".$FileMarkom[$vc]).'" target="_blank"></i>'.$FileMarkom[$vc].'</a>';
                         }
                         $MarkomSupport .= '</ul></li>';
                    } 
                    else{
                        if (strpos($dd[$zx], 'Note') !== false) {
                            $pos = strpos($dd[$zx],':');
                            $dd[$zx] = substr($dd[$zx], 0,$pos+1).'<br>'.substr($dd[$zx], $pos+1,strlen($dd[$zx]));
                        }
                      $MarkomSupport .= '<li>'.$dd[$zx].'&nbsp'.$btnMarkomSupport.'</li>';  
                    }
                    
                }
                $MarkomSupport .= '</ul>';
            }

            $DivisionID = $this->session->userdata('PositionMain');
            $DivisionID = $DivisionID['IDDivision'];
            if ($MarkomSupport != '' && $DivisionID == 17) {
                $boolAuthMarkom = 1;
            }

            $KetAdditional = $query[$i]['KetAdditional'];
            $KetAdditional = json_decode($KetAdditional);
            $Participant = '<ul><li>Participant Qty : '.$query[$i]['ParticipantQty'].'</li>';
            if (count($KetAdditional) > 0) {
                foreach ($KetAdditional as $key => $value) {
                    if ($value != "" || $value != null) {
                        $Participant .= '<li>'.str_replace("_", " ", $key).' : '.$value.'</li>';
                    }
                }
            }
            $Participant .= '</ul>';
            $boolAuth = $this->boolAuthData($query[$i]['CreatedBy'],$CaseApproveAccess,$boolAuthEq,$boolAuthMarkom);
            if ($boolAuth) {
                   $arr_result[] = array(
                           'Start' => $StartNameDay.', '.$query[$i]['Start'],
                           'End' => $EndNameDay.', '.$query[$i]['End'],
                           'Time' => $Time,
                           'Agenda' => $query[$i]['Agenda'],
                           'Room' => $query[$i]['Room'],
                           'Equipment_add' => $Name_equipment_add,
                           'Persone_add' => $Name_add_personel,
                           'Req_date' => $query[$i]['Name'].'<br>'.$ReqdateNameDay.', '.$query[$i]['Req_date'],
                           'Req_layout' => $query[$i]['Req_layout'],
                           'ID' => $query[$i]['ID'],
                           'Status' => $query[$i]['Status'],
                           'MarkomSupport' => $MarkomSupport,
                           'Participant' => $Participant,
                           'ApproveAccess' => $CaseApproveAccess,
                           'StatusBooking' => $StatusBooking,
                           'CreatedBy' => $query[$i]['CreatedBy'],
                           'Detail' => $Detail,
                   );
            }   
            
        }

        return $arr_result;  
    }

    public function getDataT_booking_Api($ID_t_booking = '',$DivisionID = '',$end = '')
    {
        $arr_result = array();
        $this->load->model('master/m_master');
            $add_where = '';
            if ($ID_t_booking != '') {
                $add_where = ' and a.ID = "'.$ID_t_booking.'"';
            }

            if ($DivisionID == '') {
                $DivisionID = $this->session->userdata('PositionMain');
                $DivisionID = $DivisionID['IDDivision'];
            }
            // print_r($DivisionID);
        
        $StatusQ = 'a.Status like "%"';    
        if ($end == '') {
            $Start = ' and Start >= timestamp(DATE_SUB(NOW(), INTERVAL 30 MINUTE))';
        }
        else{
            $end = ' and End >= timestamp(DATE_SUB(NOW(), INTERVAL '.$end.' MINUTE)) ';
            $Start = $end;
            $StatusQ = 'a.Status = 1'; 
        }    
        
        $sql = 'select a.*,b.Name from db_reservation.t_booking as a join db_employees.employees as b on a.CreatedBy = b.NIP where '.$StatusQ.''.$add_where.'
                 '.$Start.' order by a.Status asc,a.Start asc';
        $query=$this->db->query($sql, array())->result_array();
        $NIP = '@';
        for ($i=0; $i < count($query); $i++) {
            $Detail = $this->getDataPass($query[$i]['ID']);
            $Detail = implode('@@', $Detail);
            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');
            $Time = $query[$i]['Time'].' Minutes';

            $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$query[$i]['Room']);
            // cek ApproveAccess
            $Status1 = $query[$i]['Status1'];
            $Status = $query[$i]['Status'];
            //$MarcommStatus = $query[$i]['MarcommStatus'];
                $ApproveAccess = function($getRoom,$Status1,$Status,$CreatedBy,$DivisionID){

                    $PositionMain = '@';
                    $IDDivision = $DivisionID;
                    $Position = '@';
                    $NIP = '@';
                    // get Category Room to approver
                        $ApproveAccess = 0;
                        // $ID_group_user = $this->session->userdata('ID_group_user');
                        $ID_group_user = $this->m_master->caribasedprimary('db_reservation.previleges_guser','NIP',$CreatedBy);
                        $ID_group_user = $ID_group_user[0]['G_user'];

                        $getPolicy = $this->m_master->caribasedprimary('db_reservation.cfg_policy','ID_group_user',$ID_group_user);
                        $CategoryRoom = $getPolicy[0]['CategoryRoom'];
                        $CategoryRoom = json_decode($CategoryRoom);
                        $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
                        $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
                        // find access
                            $find = 1;
                                if ($find == 1) {
                                    // get status 
                                    if ($Status1 == 0) {
                                       // find approver1
                                           $Approver1 = $getDataCategoryRoom[0]['Approver1'];
                                           $Approver1 = json_decode($Approver1);

                                           $dataApprover = array();
                                           // find by ID_group_user
                                                // $CreatedBy = $query[$i]['CreatedBy'];
                                                $cc = $this->m_master->caribasedprimary('db_reservation.previleges_guser','NIP',$CreatedBy);
                                                $dd = $this->m_master->caribasedprimary('db_employees.employees','NIP',$CreatedBy);
                                                $ID_group_user = (count($cc) > 0) ? $cc[0]['G_user'] : '';
                                            // stop loop
                                            $getLoop = true;    
                                           for ($l=0; $l < count($Approver1); $l++) {
                                                   if ($ID_group_user == $Approver1[$l]->UserType) {
                                                       // get TypeApprover
                                                       $TypeApprover = $Approver1[$l]->TypeApprover;
                                                       switch ($TypeApprover) {
                                                           case 'Position':
                                                               // get Division to access position approval
                                                                   $DivisionCreated = $dd[0]['PositionMain'];
                                                                   $DivisionCreated = explode(".", $DivisionCreated);

                                                                   $IDPositionApprover = $Approver1[$l]->Approver;

                                                                   if ($DivisionCreated[0] == 15) { // if prodi
                                                                       // find prodi
                                                                       $gg = $this->m_master->caribasedprimary('db_academic.program_study','AdminID',$CreatedBy);
                                                                       if (count($gg) > 0) {
                                                                           for ($k=0; $k < count($gg); $k++) { 
                                                                               $Kaprodi = $gg[$k]['KaprodiID'];
                                                                               if ($Kaprodi == $NIP) {
                                                                                   $find++;
                                                                                   $getLoop = false;     
                                                                                   break;
                                                                               }
                                                                           }
                                                                           
                                                                       }
                                                                    }
                                                                    elseif ($DivisionCreated[0] == 34) {
                                                                        // find faculty
                                                                        $gg = $this->m_master->caribasedprimary('db_academic.faculty','AdminID',$CreatedBy);
                                                                        if (count($gg) > 0) {
                                                                            for ($k=0; $k < count($gg); $k++) { 
                                                                                $Kaprodi = $gg[$k]['NIP'];
                                                                                if ($Kaprodi == $NIP) {
                                                                                    $find++;
                                                                                    $getLoop = false;     
                                                                                    break;
                                                                                }
                                                                            }
                                                                            
                                                                        }
                                                                    }
                                                                    else
                                                                    {
                                                                        // find by division and position
                                                                        if ($DivisionCreated[0] == $IDDivision) {
                                                                           // compare Position
                                                                           if ($IDPositionApprover == $Position) {
                                                                               $find++;
                                                                               $getLoop = false;    
                                                                               break;
                                                                           }
                                                                        }
                                                                    }
                                                               break;
                                                           
                                                           case 'Division':
                                                               if ($Approver1[$l]->Approver == $IDDivision) {
                                                                   $find++;
                                                                   $getLoop = false;    
                                                                   break;
                                                               }
                                                               break;

                                                           case 'Employees':
                                                               if ($NIP == $Approver1[$l]->Approver) {
                                                                   $find++;
                                                                   $getLoop = false;    
                                                                   break;
                                                               }
                                                               break;    
                                                       }
                                                   }

                                                   if (!$getLoop) { // stop loop
                                                       break;
                                                   }
                                           } // end loop for

                                    }
                                    else
                                    {
                                        $find = $find + 2;  
                                    }
                                }

                                if ($find == 3) {
                                   // find approver2
                                       $Approver2 = $getDataCategoryRoom[0]['Approver2'];
                                       $Approver2 = json_decode($Approver2);
                                       $DivisionID = $this->session->userdata('PositionMain');
                                       $DivisionID = $DivisionID['IDDivision'];
                                       if ($Status == 0) {
                                            for ($zz=0; $zz < count($Approver2); $zz++) { 
                                                $rdata = $Approver2[$zz];
                                                $TypeApprover = $rdata->TypeApprover;
                                                switch ($TypeApprover) {
                                                    case 'Division':
                                                        if ($DivisionID == $rdata->Approver) {
                                                            $find++;    
                                                            break;
                                                        }
                                                        break;
                                                    case 'Employees':
                                                        $NIP = $this->session->userdata('NIP');
                                                        if ($NIP == $rdata->Approver) {
                                                            $find++;    
                                                            break;
                                                        }
                                                        break;

                                                }
                                            }
                                       }
                                       else
                                       {
                                        $find = $find + 2;
                                       }
                                       
                                }
                    $ApproveAccess = $find;
                    return $ApproveAccess;            
                };

                $StatusBooking = '';
                $CaseApproveAccess = $ApproveAccess($getRoom,$Status1,$Status,$query[$i]['CreatedBy'],$DivisionID);
                switch ($CaseApproveAccess) {
                    case 0:
                         $StatusBooking = 'Awaiting approval Marcomm Division';
                         break;
                    case 1:
                    case 2:
                        $StatusBooking = 'Awaiting approval 1';
                        break;
                    case 3:
                    case 4:
                        $StatusBooking = 'Awaiting approval 2';
                        break;
                    case 5:
                        $StatusBooking = 'Approved';
                        break;    
                    default:
                        # code...
                        break;
                }

            $ID_equipment_add = '-';
            $Name_equipment_add = '-';
            $boolAuthEq = 0;
            if ($query[$i]['ID_equipment_add'] != '' || $query[$i]['ID_equipment_add'] != null) {
                $ID_equipment_add = explode(',', $query[$i]['ID_equipment_add']);
                $Name_equipment_add = '<ul style = "margin-left : -28px">';
                $btnEquipment = '';
                for ($j=0; $j < count($ID_equipment_add); $j++) {
                    $btnEquipment = '';
                    // qty penggunaan
                    $gett_booking_eq_additional = $this->gett_booking_eq_additional($ID_equipment_add[$j],$query[$i]['ID']);
                    if ($gett_booking_eq_additional[0]['Status'] == 0) {
                        $Status_eq_additional = 'Not Confirm';
                    }
                    elseif ($gett_booking_eq_additional[0]['Status'] == 1) {
                        $Status_eq_additional = 'Confirm';
                    }
                    else
                    {
                        $Status_eq_additional = 'Reject';
                    }
                    $Qty = $gett_booking_eq_additional[0]['Qty'];
                    $ID_equipment_additional = $gett_booking_eq_additional[0]['ID_equipment_additional'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
                    $OwnerID = $get[0]['Owner'];
                    $getX = $this->m_master->caribasedprimary('db_employees.division','ID',$OwnerID);
                    $Owner = $getX[0]['Division'];
                        if ($DivisionID == $OwnerID) {
                            if ($query[$i]['Status'] != 1) {
                                $btnEquipment = '<input type = "checkbox" class = "chkEquipment_'.$query[$i]['ID'].'" value = "'.$ID_equipment_add[$j].'">';
                            }
                            if ($boolAuthEq == 0) {
                                $boolAuthEq = 1;
                            }
                            
                        } 

                    $ID_m_equipment = $get[0]['ID_m_equipment'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                    $Name_equipment_add .= '<li>'.$get[0]['Equipment'].' by '.$Owner.'['.$Qty.']&nbsp ('.$Status_eq_additional.') &nbsp'.$btnEquipment.'</li>';
                    // print_r('-div:-'.$DivisionID.'=OW:'.$OwnerID.'--ID:'.$query[$i]['ID'].'---');
                }
                $Name_equipment_add .= '</ul>';
                // print_r('---'.$boolAuthEq.'---');
                if ($boolAuthEq == 1) {
                    if ($query[$i]['Status'] != 1) {
                        $Name_equipment_add .= '<div class = "row"><div class = "col-md-6">
                                        <span class="btn btn-primary btn-xs btn_eq_additional_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Confirm">
                                            <i class="fa fa-pencil-square-o"></i> Confirm
                                           </span>
                                                </div>
                                            <div class = "col-md-6">
                                                <span class="btn btn-danger btn-xs btn_eq_additional_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Reject">
                                                    <i class="fa fa-pencil-square-o"></i> Reject
                                                   </span>
                                            </div>    
                                            </div>';
                    }
                }
                
            }

            $ID_add_personel = '-';
            $Name_add_personel = '-';

            if ($query[$i]['ID_add_personel'] != '' || $query[$i]['ID_add_personel'] != null) {
                $Name_add_personel = $query[$i]['ID_add_personel'];
            }

            $Reqdatetime = DateTime::createFromFormat('Y-m-d', $query[$i]['Req_date']);
            $ReqdateNameDay = $Reqdatetime->format('l');

            $MarkomSupport = '<label>No</Label>';
            $boolAuthMarkom = 0;
            if ($query[$i]['MarcommSupport'] != '') {
                $MarkomSupport = '<ul style = "margin-left : -28px">';
                $dd = explode(',', $query[$i]['MarcommSupport']);
                $dzx = array();
                // split for note
                    for ($xz=0; $xz < count($dd); $xz++) { 
                        $pos1 = stripos($dd[$xz], 'Note');
                        $exitLoop = false;
                        if ($pos1 !== false) {
                            $temp = array();
                            for ($ixx = $xz; $ixx < count($dd); $ixx++) { 
                                $temp[] = $dd[$ixx];
                            }
                            $dzx[] = implode(',', $temp);
                            $exitLoop = true;
                        }
                        else
                        {
                            $dzx[] = $dd[$xz];
                        }

                        if ($exitLoop) {
                            break;
                        }
                    }

                    $dd = $dzx;
                for ($zx=0; $zx < count($dd); $zx++) {
                    // check status
                     $Status_markom = '';
                     $btnMarkomSupport = '';
                     if (strpos($dd[$zx], 'Note') === false) {
                         $g_markom = $this->g_markom($dd[$zx],$query[$i]['ID']);
                         if ($g_markom[0]['StatusMarkom'] == 0) {
                             $Status_markom = '{Not Confirm}';
                         }
                         elseif ($g_markom[0]['StatusMarkom'] == 1) {
                             $Status_markom = '{Confirm}';
                         }
                         else
                         {
                             $Status_markom = '{Reject}';
                         }

                         //if ($g_markom[0]['StatusMarkom'] != 1) {
                            $btnMarkomSupport = '<input type = "checkbox" class = "MarkomSupport_'.$query[$i]['ID'].'" value = "'.$dd[$zx].'">';
                         //}
                         $dd[$zx] = $g_markom[0]['Name'];   
                     }
                     else
                     {
                        $pos = strpos($dd[$zx],':');
                        $dd[$zx] = substr($dd[$zx], 0,$pos+1).'<br>'.nl2br(substr($dd[$zx], $pos+1,strlen($dd[$zx])));
                     }

                     $MarkomSupport .= '<li>'.$dd[$zx].$Status_markom.'&nbsp'.$btnMarkomSupport.'</li>';    

                }
                $MarkomSupport .= '</ul>';
            }

            if ($MarkomSupport != '' && $DivisionID == 17) {
                $boolAuthMarkom = 1;
            }

            if ($boolAuthMarkom == 1) {
                if ($query[$i]['Status'] != 1) {
                    $MarkomSupport .= '<div class = "row"><div class = "col-md-6">
                                    <span class="btn btn-primary btn-xs btn_markom_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Confirm">
                                        <i class="fa fa-pencil-square-o"></i> Confirm
                                       </span>
                                            </div>
                                        <div class = "col-md-6">
                                            <span class="btn btn-danger btn-xs btn_markom_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Reject">
                                                <i class="fa fa-pencil-square-o"></i> Reject
                                               </span>
                                        </div>    
                                        </div>';
                }
            }

            $KetAdditional = $query[$i]['KetAdditional'];
            $KetAdditional = json_decode($KetAdditional);
            $Participant = '<ul><li>Participant Qty : '.$query[$i]['ParticipantQty'].'</li>';
            if (count($KetAdditional) > 0) {
                foreach ($KetAdditional as $key => $value) {
                    if ($value != "" || $value != null) {
                        $Participant .= '<li>'.str_replace("_", " ", $key).' : '.$value.'</li>';
                    }
                }
            }
            $Participant .= '</ul>';
            $boolAuth = $this->boolAuthData($query[$i]['CreatedBy'],$CaseApproveAccess,$boolAuthEq,$boolAuthMarkom);
            if ($boolAuth) {
                   $arr_result[] = array(
                           'Start' => $StartNameDay.', '.$query[$i]['Start'],
                           'End' => $EndNameDay.', '.$query[$i]['End'],
                           'Time' => $Time,
                           'Agenda' => $query[$i]['Agenda'],
                           'Room' => $query[$i]['Room'],
                           'Equipment_add' => $Name_equipment_add,
                           'Persone_add' => $Name_add_personel,
                           'Req_date' => $query[$i]['Name'].'<br>'.$ReqdateNameDay.', '.$query[$i]['Req_date'],
                           'Req_layout' => $query[$i]['Req_layout'],
                           'ID' => $query[$i]['ID'],
                           'Status' => $query[$i]['Status'],
                           'MarkomSupport' => $MarkomSupport,
                           'Participant' => $Participant,
                           'ApproveAccess' => $CaseApproveAccess,
                           'StatusBooking' => $StatusBooking,
                           'CreatedBy' => $query[$i]['CreatedBy'],
                           'Detail' => $Detail,
                   );
            }   
            
        }

        return $arr_result;  
    }

    public function g_markom($ID_m_markom,$ID_t_booking)
    {
        $sql = 'select a.ID as ID_m_markom,a.Name,b.Status as StatusMarkom,c.* from db_reservation.m_markom_support as a join db_reservation.t_markom_support as b on a.ID = b.ID_m_markom_support join db_reservation.t_booking as c on c.ID = b.ID_t_booking where a.ID = "'.$ID_m_markom.'" and c.ID = "'.$ID_t_booking.'"  ';
        $query=$this->db->query($sql, array())->result_array();  
        return $query;
    }

    public function getDataT_booking($Start = null,$Status = 0,$both = '')
    {
        $arr_result = array();
        $this->load->model('master/m_master');
        $NIP = $this->session->userdata('NIP');
            $add_where = '';
        $Start = ($Start == null) ? ' and Start >= timestamp(DATE_SUB(NOW(), INTERVAL 30 MINUTE))' : ' and Start like "%'.$Start.'%"';
        if ($both == '') {
            $sql = 'select a.*,b.Name from db_reservation.t_booking as a join db_employees.employees as b on a.CreatedBy = b.NIP where a.Status = ? '.$add_where.'
                     '.$Start.' order by a.Status asc,a.Start asc';
            $query=$this->db->query($sql, array($Status))->result_array();         
        }
        else
        {
            $sql = 'select a.*,b.Name from db_reservation.t_booking as a join db_employees.employees as b on a.CreatedBy = b.NIP where a.Status like "%" '.$add_where.'
                     '.$Start.' order by a.Status asc,a.Start asc';
            // print_r($sql);die();         
            $query=$this->db->query($sql, array())->result_array();           
        }
        for ($i=0; $i < count($query); $i++) {
            $Detail = $this->getDataPass($query[$i]['ID']);
            $Detail = implode('@@', $Detail);
            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');
            $Time = $query[$i]['Time'].' Minutes';

            $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$query[$i]['Room']);
            // cek ApproveAccess
            $Status1 = $query[$i]['Status1'];
            $Status = $query[$i]['Status'];
                $ApproveAccess = function($getRoom,$Status1,$Status,$CreatedBy){
                    // if ($MarcommStatus == 1) {
                    //     return $find = 0;
                    // }
                    $PositionMain = $this->session->userdata('PositionMain');
                    $IDDivision = $PositionMain['IDDivision'];
                    $Position = $PositionMain['IDPosition'];
                    $NIP = $this->session->userdata('NIP');
                    // get Category Room to approver
                        $ApproveAccess = 0;
                        $ID_group_user = $this->session->userdata('ID_group_user');
                        $getPolicy = $this->m_master->caribasedprimary('db_reservation.cfg_policy','ID_group_user',$ID_group_user);
                        $CategoryRoom = $getPolicy[0]['CategoryRoom'];
                        $CategoryRoom = json_decode($CategoryRoom);
                        $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
                        $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
                        // find access
                            $find = 1;
                                if ($find == 1) {
                                    // get status 
                                    if ($Status1 == 0) {
                                       // find approver1
                                           $Approver1 = $getDataCategoryRoom[0]['Approver1'];
                                           $Approver1 = json_decode($Approver1);

                                           $dataApprover = array();
                                           // find by ID_group_user
                                                // $CreatedBy = $query[$i]['CreatedBy'];
                                                $cc = $this->m_master->caribasedprimary('db_reservation.previleges_guser','NIP',$CreatedBy);
                                                $dd = $this->m_master->caribasedprimary('db_employees.employees','NIP',$CreatedBy);
                                                $ID_group_user = (count($cc) > 0) ? $cc[0]['G_user'] : '';
                                            // stop loop
                                            $getLoop = true;    
                                           for ($l=0; $l < count($Approver1); $l++) {
                                                   if ($ID_group_user == $Approver1[$l]->UserType) {
                                                       // get TypeApprover
                                                       $TypeApprover = $Approver1[$l]->TypeApprover;
                                                       switch ($TypeApprover) {
                                                           case 'Position':
                                                               // get Division to access position approval
                                                                   $DivisionCreated = $dd[0]['PositionMain'];
                                                                   $DivisionCreated = explode(".", $DivisionCreated);

                                                                   $IDPositionApprover = $Approver1[$l]->Approver;

                                                                   if ($DivisionCreated[0] == 15) { // if prodi
                                                                       // find prodi
                                                                       $gg = $this->m_master->caribasedprimary('db_academic.program_study','AdminID',$CreatedBy);
                                                                       if (count($gg) > 0) {
                                                                           for ($k=0; $k < count($gg); $k++) { 
                                                                               $Kaprodi = $gg[$k]['KaprodiID'];
                                                                               if ($Kaprodi == $NIP) {
                                                                                   $find++;
                                                                                   $getLoop = false;     
                                                                                   break;
                                                                               }
                                                                           }
                                                                           
                                                                       }
                                                                    }
                                                                    elseif ($DivisionCreated[0] == 34) {
                                                                        // find faculty
                                                                        $gg = $this->m_master->caribasedprimary('db_academic.faculty','AdminID',$CreatedBy);
                                                                        if (count($gg) > 0) {
                                                                            for ($k=0; $k < count($gg); $k++) { 
                                                                                $Kaprodi = $gg[$k]['NIP'];
                                                                                if ($Kaprodi == $NIP) {
                                                                                    $find++;
                                                                                    $getLoop = false;     
                                                                                    break;
                                                                                }
                                                                            }
                                                                            
                                                                        }
                                                                    }
                                                                    elseif ($DivisionCreated[0] == 33) {
                                                                        // find faculty
                                                                        $gg = $this->m_master->caribasedprimary('db_academic.faculty','Laboran',$CreatedBy);
                                                                        if (count($gg) > 0) {
                                                                            for ($k=0; $k < count($gg); $k++) { 
                                                                                $Kaprodi = $gg[$k]['NIP'];
                                                                                if ($Kaprodi == $NIP) {
                                                                                    $find++;
                                                                                    $getLoop = false;     
                                                                                    break;
                                                                                }
                                                                            }
                                                                            
                                                                        }
                                                                        else
                                                                        {
                                                                            $gg = $this->m_master->caribasedprimary('db_academic.program_study','Laboran',$CreatedBy);
                                                                            if (count($gg) > 0) {
                                                                                for ($k=0; $k < count($gg); $k++) { 
                                                                                    $Kaprodi = $gg[$k]['KaprodiID'];
                                                                                    if ($Kaprodi == $NIP) {
                                                                                        $find++;
                                                                                        $getLoop = false;     
                                                                                        break;
                                                                                    }
                                                                                }
                                                                                
                                                                            }
                                                                        }
                                                                    }
                                                                    elseif ($DivisionCreated[0] == 14) { // Lecturer
                                                                       $ProdiID = $dd[0]['ProdiID'];
                                                                       $G_Prodi = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                                                                       $Kaprodi = $G_Prodi[0]['KaprodiID'];
                                                                       if ($Kaprodi == $NIP) {
                                                                           $find++;
                                                                           $getLoop = false;     
                                                                           break;
                                                                       }

                                                                    }
                                                                    else
                                                                    {
                                                                        // find by division and position
                                                                        if ($DivisionCreated[0] == $IDDivision) {
                                                                           // compare Position
                                                                           if ($IDPositionApprover == $Position) {
                                                                               $find++;
                                                                               $getLoop = false;    
                                                                               break;
                                                                           }
                                                                        }
                                                                        else
                                                                        {
                                                                            $PositionOther = $this->session->userdata('PositionOther1');
                                                                            $IDDivisionOther = $PositionOther['IDDivisionOther1'];
                                                                            $PositionOther = $PositionOther['IDPositionOther1'];
                                                                            if ($DivisionCreated[0] == $IDDivisionOther) {
                                                                                // compare Position
                                                                                if ($IDPositionApprover == $PositionOther) {
                                                                                    $find++;
                                                                                    $getLoop = false;    
                                                                                    break;
                                                                                }
                                                                            }
                                                                            else
                                                                            {
                                                                                $PositionOther = $this->session->userdata('PositionOther2');
                                                                                $IDDivisionOther = $PositionOther['IDDivisionOther2'];
                                                                                $PositionOther = $PositionOther['IDPositionOther2'];
                                                                                if ($DivisionCreated[0] == $IDDivisionOther) {
                                                                                    // compare Position
                                                                                    if ($IDPositionApprover == $PositionOther) {
                                                                                        $find++;
                                                                                        $getLoop = false;    
                                                                                        break;
                                                                                    }
                                                                                }
                                                                                else
                                                                                {
                                                                                    $PositionOther = $this->session->userdata('PositionOther3');
                                                                                    $IDDivisionOther = $PositionOther['IDDivisionOther3'];
                                                                                    $PositionOther = $PositionOther['IDPositionOther3'];
                                                                                    if ($DivisionCreated[0] == $IDDivisionOther) {
                                                                                        // compare Position
                                                                                        if ($IDPositionApprover == $PositionOther) {
                                                                                            $find++;
                                                                                            $getLoop = false;    
                                                                                            break;
                                                                                        }
                                                                                    }
                                                                                }
                                                                            }
                                                                        }
                                                                    }
                                                               break;
                                                           
                                                           case 'Division':
                                                               if ($Approver1[$l]->Approver == $IDDivision) {
                                                                   $find++;
                                                                   $getLoop = false;    
                                                                   break;
                                                               }
                                                               break;

                                                           case 'Employees':
                                                               if ($NIP == $Approver1[$l]->Approver) {
                                                                   $find++;
                                                                   $getLoop = false;    
                                                                   break;
                                                               }
                                                               break;    
                                                       }
                                                   }

                                                   if (!$getLoop) { // stop loop
                                                       break;
                                                   }
                                           } // end loop for

                                    }
                                    else
                                    {
                                        $find = $find + 2;  
                                    }
                                }

                                if ($find == 3) {
                                   // find approver2
                                       $Approver2 = $getDataCategoryRoom[0]['Approver2'];
                                       $Approver2 = json_decode($Approver2);
                                       $DivisionID = $this->session->userdata('PositionMain');
                                       $DivisionID = $DivisionID['IDDivision'];
                                       if ($Status == 0) {
                                            for ($zz=0; $zz < count($Approver2); $zz++) { 
                                                $rdata = $Approver2[$zz];
                                                $TypeApprover = $rdata->TypeApprover;
                                                switch ($TypeApprover) {
                                                    case 'Division':
                                                        if ($DivisionID == $rdata->Approver) {
                                                            $find++;    
                                                            break;
                                                        }
                                                        break;
                                                    case 'Employees':
                                                        $NIP = $this->session->userdata('NIP');
                                                        if ($NIP == $rdata->Approver) {
                                                            $find++;    
                                                            break;
                                                        }
                                                        break;

                                                }
                                            }
                                       }
                                       else
                                       {
                                        $find = $find + 2;
                                       }
                                       
                                }
                    $ApproveAccess = $find;
                    return $ApproveAccess;            
                };

                $StatusBooking = '';
                $CaseApproveAccess = $ApproveAccess($getRoom,$Status1,$Status,$query[$i]['CreatedBy']);
                switch ($CaseApproveAccess) {
                    case 0:
                         $StatusBooking = 'Awaiting<br/> Approval Marcomm Division';
                         break;
                    case 1:
                    case 2:
                        // find nama approval
                        $NameApprover = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        $StatusBooking = 'Awaiting<br/><span style = "color:red;"> App 1 : '.$NameApprover.'</span>';
                        break;
                    case 3:
                    case 4:
                        $checkRuleDivisioOrNot = $this->checkRuleDivisioOrNot(1,$query[$i]['ID']);
                        if ($checkRuleDivisioOrNot) {
                            // division
                            $ApprovedBy1 =  $query[$i]['ApprovedBy1'];

                            if (strlen($ApprovedBy1) > 3) {
                                $Get_Em = $this->m_master->caribasedprimary('db_employees.employees','NIP',$ApprovedBy1);
                                $NameApprover1 = $Get_Em[0]['Name'];
                            }
                            else
                            {
                                $NameApprover1 = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                            }
                        }
                        else
                        {
                            $NameApprover1 = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        }
                        
                        // $NameApprover1 = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        $NameApprover2 = $this->Get_Approver(2,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        $StatusBooking = 'Awaiting<li><span style = "color:#0968b3;"> App 1 : '.$NameApprover1.'</span></li><li><span style = "color:red;"> App 2 : '.$NameApprover2.'</span></li>';
                        break;
                    case 5:
                        // $StatusBooking = 'Approved';
                        $checkRuleDivisioOrNot = $this->checkRuleDivisioOrNot(1,$query[$i]['ID']);
                        if ($checkRuleDivisioOrNot) {
                            // division
                            $ApprovedBy1 =  $query[$i]['ApprovedBy1'];

                            if (strlen($ApprovedBy1) > 3) {
                                $Get_Em = $this->m_master->caribasedprimary('db_employees.employees','NIP',$ApprovedBy1);
                                $NameApprover1 = $Get_Em[0]['Name'];
                            }
                            else
                            {
                                $NameApprover1 = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                            }
                        }
                        else
                        {
                            $NameApprover1 = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        }
                        // $NameApprover1 = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        // $NameApprover2 = $this->Get_Approver(2,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        $checkRuleDivisioOrNot = $this->checkRuleDivisioOrNot(2,$query[$i]['ID']);
                        if ($checkRuleDivisioOrNot) {
                            // division
                            $ApprovedBy1 =  $query[$i]['ApprovedBy1'];

                            if (strlen($ApprovedBy1) > 3) {
                                $Get_Em = $this->m_master->caribasedprimary('db_employees.employees','NIP',$ApprovedBy1);
                                $NameApprover2 = $Get_Em[0]['Name'];
                            }
                            else
                            {
                                $NameApprover2 = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                            }
                        }
                        else
                        {
                            $NameApprover2 = $this->Get_Approver(1,$getRoom[0]['ID_CategoryRoom'],$query[$i]['CreatedBy']);
                        }
                        $StatusBooking = 'Approved<li><span style = "color:#0968b3;">App 1 : '.$NameApprover1.'</span></li><li><span style = "color:#0968b3;">App 2 : '.$NameApprover2.'</span></li>';
                        break;    
                    default:
                        # code...
                        break;
                }

            $ID_equipment_add = '-';
            $Name_equipment_add = '-';
            $boolAuthEq = 0;
            if ($query[$i]['ID_equipment_add'] != '' || $query[$i]['ID_equipment_add'] != null) {
                $ID_equipment_add = explode(',', $query[$i]['ID_equipment_add']);
                $Name_equipment_add = '<ul style = "margin-left : -28px">';
                $btnEquipment = '';
                for ($j=0; $j < count($ID_equipment_add); $j++) {
                    $btnEquipment = '';
                    // qty penggunaan
                    $gett_booking_eq_additional = $this->gett_booking_eq_additional($ID_equipment_add[$j],$query[$i]['ID']);
                    //print_r($ID_equipment_add[$j].'--'.$query[$i]['ID']);
                    if ($gett_booking_eq_additional[0]['Status'] == 0) {
                        $Status_eq_additional = 'Not Confirm';
                    }
                    elseif ($gett_booking_eq_additional[0]['Status'] == 1) {
                        $Status_eq_additional = 'Confirm';
                    }
                    else
                    {
                        $Status_eq_additional = 'Reject';
                    }

                    $Qty = $gett_booking_eq_additional[0]['Qty'];
                    $ID_equipment_additional = $gett_booking_eq_additional[0]['ID_equipment_additional'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
                    $OwnerID = $get[0]['Owner'];
                    $getX = $this->m_master->caribasedprimary('db_employees.division','ID',$OwnerID);
                    $Owner = $getX[0]['Division'];
                    if ($this->session->userdata('ID_group_user') < 3) {
                        if ($query[$i]['Status'] != 1) {
                            $btnEquipment = '<input type = "checkbox" class = "chkEquipment_'.$query[$i]['ID'].'" value = "'.$ID_equipment_add[$j].'">';
                        }
                        $boolAuthEq = 1;
                        
                    }
                    else{
                        $DivisionID = $this->session->userdata('PositionMain');
                            $DivisionID = $DivisionID['IDDivision'];
                            if ($DivisionID == $OwnerID) {
                                if ($query[$i]['Status'] != 1) {
                                    $btnEquipment = '<input type = "checkbox" class = "chkEquipment_'.$query[$i]['ID'].'" value = "'.$ID_equipment_add[$j].'">';
                                }
                                $boolAuthEq = 1;
                            }
                    } 

                    $ID_m_equipment = $get[0]['ID_m_equipment'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                    $Name_equipment_add .= '<li>'.$get[0]['Equipment'].' by '.$Owner.'['.$Qty.']&nbsp ('.$Status_eq_additional.') &nbsp'.$btnEquipment.'</li>';
                }
                $Name_equipment_add .= '</ul>';
                if ($boolAuthEq == 1) {
                    if ($query[$i]['Status'] != 1) {
                        $Name_equipment_add .= '<div class = "row"><div class = "col-md-6">
                                        <span class="btn btn-primary btn-xs btn_eq_additional_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Confirm">
                                            <i class="fa fa-pencil-square-o"></i> Confirm
                                           </span>
                                                </div>
                                            <div class = "col-md-6">
                                                <span class="btn btn-danger btn-xs btn_eq_additional_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Reject">
                                                    <i class="fa fa-pencil-square-o"></i> Reject
                                                   </span>
                                            </div>    
                                            </div>';
                    }
                }
                
            }

            $ID_add_personel = '-';
            $Name_add_personel = '-';

            if ($query[$i]['ID_add_personel'] != '' || $query[$i]['ID_add_personel'] != null) {
                $Name_add_personel = $query[$i]['ID_add_personel'];
            }

            $Reqdatetime = DateTime::createFromFormat('Y-m-d', $query[$i]['Req_date']);
            $ReqdateNameDay = $Reqdatetime->format('l');

            $MarkomSupport = '<label>No</Label>';
            $boolAuthMarkom = 0;
            if ($query[$i]['MarcommSupport'] != '') {
                $MarkomSupport = '<ul style = "margin-left : -28px">';
                $dd = explode(',', $query[$i]['MarcommSupport']);
                $dzx = array();
                // split for note
                    for ($xz=0; $xz < count($dd); $xz++) { 
                        $pos1 = stripos($dd[$xz], 'Note');
                        $exitLoop = false;
                        if ($pos1 !== false) {
                            $temp = array();
                            for ($ixx = $xz; $ixx < count($dd); $ixx++) { 
                                $temp[] = $dd[$ixx];
                            }
                            $dzx[] = implode(',', $temp);
                            $exitLoop = true;
                        }
                        else
                        {
                            $dzx[] = $dd[$xz];
                        }

                        if ($exitLoop) {
                            break;
                        }
                    }

                    $dd = $dzx;

                for ($zx=0; $zx < count($dd); $zx++) {
                    // check status
                     $Status_markom = '';
                     $btnMarkomSupport = '';
                     if (strpos($dd[$zx], 'Note') === false) {
                         $g_markom = $this->g_markom($dd[$zx],$query[$i]['ID']);
                         if ($g_markom[0]['StatusMarkom'] == 0) {
                             $Status_markom = '{Not Confirm}';
                         }
                         elseif ($g_markom[0]['StatusMarkom'] == 1) {
                             $Status_markom = '{Confirm}';
                         }
                         else
                         {
                             $Status_markom = '{Reject}';
                         }

                         //if ($g_markom[0]['StatusMarkom'] != 1) {
                            if ($this->session->userdata('ID_group_user') < 3) {
                                if ($query[$i]['Status'] != 1) {
                                    $btnMarkomSupport = '<input type = "checkbox" class = "MarkomSupport_'.$query[$i]['ID'].'" value = "'.$dd[$zx].'">';
                                }
                               
                                if ($boolAuthMarkom == 0) {
                                    $boolAuthMarkom = 1;
                                }
                            }
                            else
                            {
                                $DivisionID = $this->session->userdata('PositionMain');
                                $DivisionID = $DivisionID['IDDivision'];
                                if ($DivisionID == 17) {
                                    if ($query[$i]['Status'] != 1) {
                                        $btnMarkomSupport = '<input type = "checkbox" class = "MarkomSupport_'.$query[$i]['ID'].'" value = "'.$dd[$zx].'">';
                                    }
                                    if ($boolAuthMarkom == 0) {
                                        $boolAuthMarkom = 1;
                                    }
                                }
                            }
                            
                            
                         //}
                         $dd[$zx] = $g_markom[0]['Name'];   
                     }
                     else
                     {
                        $pos = strpos($dd[$zx],':');
                        $dd[$zx] = substr($dd[$zx], 0,$pos+1).'<br>'.nl2br(substr($dd[$zx], $pos+1,strlen($dd[$zx])));
                     }

                     $MarkomSupport .= '<li>'.$dd[$zx].$Status_markom.'&nbsp'.$btnMarkomSupport.'</li>';    

                }
                $MarkomSupport .= '</ul>';
            }

            if ($boolAuthMarkom == 1) {
                if ($query[$i]['Status'] != 1) {
                    $MarkomSupport .= '<div class = "row"><div class = "col-md-6">
                                    <span class="btn btn-primary btn-xs btn_markom_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Confirm">
                                        <i class="fa fa-pencil-square-o"></i> Confirm
                                       </span>
                                            </div>
                                        <div class = "col-md-6">
                                            <span class="btn btn-danger btn-xs btn_markom_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Reject">
                                                <i class="fa fa-pencil-square-o"></i> Reject
                                               </span>
                                        </div>    
                                        </div>';
                }
            }
            

            $KetAdditional = $query[$i]['KetAdditional'];
            $KetAdditional = json_decode($KetAdditional);
            $Participant = '<ul><li>Participant Qty : '.$query[$i]['ParticipantQty'].'</li>';
            if ( is_array($KetAdditional) && count($KetAdditional) > 0) {
                foreach ($KetAdditional as $key => $value) {
                    if ($value != "" || $value != null) {
                        $Participant .= '<li>'.str_replace("_", " ", $key).' : '.$value.'</li>';
                    }
                }
            }
            $Participant .= '</ul>';
            // print_r($CaseApproveAccess);die();
            $boolAuth = $this->boolAuthData($query[$i]['CreatedBy'],$CaseApproveAccess,$boolAuthEq,$boolAuthMarkom);
            if ($boolAuth) {
                   $arr_result[] = array(
                           'Start' => $StartNameDay.', '.$query[$i]['Start'],
                           'End' => $EndNameDay.', '.$query[$i]['End'],
                           'Time' => $Time,
                           'Agenda' => $query[$i]['Agenda'],
                           'Room' => $query[$i]['Room'],
                           'Equipment_add' => $Name_equipment_add,
                           'Persone_add' => $Name_add_personel,
                           'Req_date' => $query[$i]['Name'].'<br>'.$ReqdateNameDay.', '.$query[$i]['Req_date'],
                           'Req_layout' => $query[$i]['Req_layout'],
                           'ID' => $query[$i]['ID'],
                           'Status' => $query[$i]['Status'],
                           'MarkomSupport' => $MarkomSupport,
                           'Participant' => $Participant,
                           'ApproveAccess' => $CaseApproveAccess,
                           'StatusBooking' => $StatusBooking,
                           'CreatedBy' => $query[$i]['CreatedBy'],
                           'Detail' => $Detail,
                   );
            }   
            
        }

        return $arr_result;         
    }

    public function getDataPass($ID)
    {
        $sql2 = 'select a.*,b.Name from db_reservation.t_booking as a
                 join db_employees.employees as b on a.CreatedBy = b.NIP
                 where a.ID = ? limit 1';
        $query2=$this->db->query($sql2, array($ID))->result_array();
        $arr = array();
        for ($i=0; $i < count($query2); $i++) {
                    $tgl = date("Y-m-d", strtotime($query2[$i]['Start'])); 
                    $dt = array(
                        'user'  => $query2[$i]['Name'],
                        'start' => $query2[$i]['Start'],
                        'end'   => $query2[$i]['End'],
                        'time'  => $query2[$i]['Time'],
                        'colspan' => $query2[$i]['Colspan'],
                        'agenda' => $query2[$i]['Agenda'],
                        'room' => $query2[$i]['Room'],
                        'approved' => $query2[$i]['Status'],
                        'NIP' => $query2[$i]['CreatedBy'],
                        'ID' => $query2[$i]['ID'],
                        'tgl' => $tgl,
                        //'NameEng' => $query[$i]['NameEng'],
                    );
            $arr= $dt;        
        }

        return $arr;
    }

    private function boolAuthData($CreatedBy,$CaseApproveAccess,$boolAuthEq,$boolAuthMarkom)
    {
        $bool = false;
        $arr = array();
        if ($this->session->userdata('ID_group_user') <= 3) {
            $bool = true;
        }
        else
        {
            if ($CreatedBy == $this->session->userdata('NIP')) {
                $arr[] = 1;
            }
            else
            {
                $arr[] = 0;
            }

            if ($CaseApproveAccess == 2 || $CaseApproveAccess == 4) {
                $arr[] = 1;
            }
            else
            {
                 $arr[] = 0;
            }

            $arr[] = $boolAuthEq;
            $arr[] = $boolAuthMarkom;
            if (in_array(1, $arr))
            {
                $bool = true;
            }
        }
        return $bool;
    }

    public function get_m_room_equipment($room)
    {
        $sql = 'select a.Room,a.qty,b.Equipment,a.Note from db_reservation.m_room_equipment as a join db_reservation.m_equipment as b on a.ID_m_equipment = b.ID and a.Room = ?';
        $query=$this->db->query($sql, array($room))->result_array();
        return $query;
    }

    public function get_m_room_equipment_all()
    {
        $sql = 'select a.ID,a.Room,a.qty,b.Equipment,c.Name as NameCreated,a.CreatedAt, d.Name as NameUpdated,a.UpdatedAt,a.Note from db_reservation.m_room_equipment as a join db_reservation.m_equipment as b on a.ID_m_equipment = b.ID join db_employees.employees as c on a.CreatedBy = c.NIP left join db_employees.employees as d on a.UpdatedBy = d.NIP';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function checkBentrokScheduleAPI()
    {
        $bool = true;
        $arr_result = array('ID' => '','bool' => $bool);
        // get data schedule details and compare with t_booking table
        $sql = 'select * from db_reservation.t_booking where Start >= timestamp(DATE_SUB(NOW(), INTERVAL 30 MINUTE))';
        $query=$this->db->query($sql, array())->result_array();

        $SemesterID = $this->m_master->caribasedprimary('db_academic.semester','Status',1);
        $SemesterID = $SemesterID[0]['ID'];

        for ($i=0; $i < count($query); $i++) {
             $Start = $query[$i]['Start'];
             $End = $query[$i]['End'];
             $Room = $query[$i]['Room'];
             $chk = $this->checkBentrok($Start,$End,'',$Room,$query[$i]['ID']);
             if (!$chk) {
                 $bool = false;
                 // break;
                 $arr_result = array('ID' => $query[$i]['ID'],'bool' => $bool);
                 break;
             }
             // get 
             // $TimeStart = date("H:i:s", strtotime($Start));
             // $TimeEnd = date("H:i:s", strtotime($End)); 
             // // check academic timeline
             //    $datetime = DateTime::createFromFormat('Y-m-d H:i:s', $Start);
             //    $NameDay = $datetime->format('l');
             //    $date2 = date("Y-m-d", strtotime($Start));

             //    $sql2 = 'select count(*) as total from db_academic.classroom as a join db_academic.schedule_details as c
             //            on a.ID = c.ClassroomID
             //            join db_academic.days as b
             //            on c.DayID = b.ID
             //            left join db_academic.schedule as zd on zd.ID = c.ScheduleID
             //            where "'.$date2.'" >= (select z.kuliahStart from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) 
             //            and "'.$date2.'" <= (select z.kuliahEnd from db_academic.academic_years as z,db_academic.semester as x where z.SemesterID = x.ID and x.Status = 1 LIMIT 1) and zd.SemesterID = "'.$SemesterID.'"
             //            and b.NameEng = "'.$NameDay.'" and (
             //                (c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )
             //                or (
             //                        c.StartSessions <= "'.$TimeStart.'" and c.EndSessions >= "'.$TimeEnd.'"
             //                    )
             //            ) and a.Room = "'.$Room.'" and c.ID not in (select a.ScheduleID from db_academic.attendance as a join db_academic.schedule_exchange as b
             //    on a.ID = b.ID_Attd where b.Status = "2" and b.DateOriginal = "'.$date2.'")';
             //    $query2=$this->db->query($sql2, array())->result_array();
             //    if ($query2[0]['total'] > 0) {
             //        // print_r('bentrok :'.$NameDay.', '.$TimeStart.'-'.$TimeEnd.' : '.$date2).'<br>';
             //        $bool = false;
             //        // break;
             //        $arr_result = array('ID' => $query[$i]['ID'],'bool' => $bool);
             //        break;
             //    }
             //    else
             //    {
             //        $sql3 = 'select count(*) as total
             //                from db_academic.classroom as a join db_academic.schedule_exchange as c
             //                on a.ID = c.ClassroomID
             //                join db_academic.days as b
             //                on c.DayID = b.ID where c.Status = "2" and c.Date ="'.$date2.'" and (
             //                    (c.StartSessions >= "'.$TimeStart.'" and c.StartSessions < "'.$TimeEnd.'" ) or (c.EndSessions > "'.$TimeStart.'" and c.EndSessions <= "'.$TimeEnd.'" )
             //                    or (
             //                            c.StartSessions <= "'.$TimeStart.'" and c.EndSessions >= "'.$TimeEnd.'"
             //                        )
             //                ) and a.Room = "'.$Room.'"';
             //        //print_r($sql3);die();
             //        $query3=$this->db->query($sql3, array())->result_array();
             //        if ($query3[0]['total'] > 0) {
             //            // print_r('bentrok :'.$NameDay.', '.$TimeStart.'-'.$TimeEnd.' : '.$date2);
             //            $bool = false;
             //            // break;
             //            $arr_result = array('ID' => $query[$i]['ID'],'bool' => $bool);
             //            break;
             //        }
             //        else
             //        {
             //            // print_r('Ok<br>');
             //        }
             //    }
        }

        // print_r($arr_result);die();
        return $arr_result;

    }

    public function getDataWithoutSuperAdmin()
    {
        $sql = 'select * from db_reservation.cfg_group_user where ID != 1';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function getDataWithoutSuperAdmin2($ID)
    {
        $sql = 'select * from db_reservation.cfg_group_user where ID >= "'.$ID.'"';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function chkAuthDB_Base_URL_vreservation($URL)
    {
        $a = explode('/', $URL);
        $b = count($a) - 1;
        $URISlug = 'and a.Slug = "'.$URL.'"';
        if ($a[$b] == 1) {
            $URISlug = '';
            for ($i=0; $i < count($b); $i++) { 
                $URISlug .= $a[$i].'/';
            }
            $URISlug = 'and a.Slug like "%'.$URISlug.'%"';
        }
        $sql = "select b.read,b.write,b.update,b.delete from db_reservation.cfg_sub_menu as a join db_reservation.cfg_rule_g_user as b on a.ID = b.ID_cfg_sub_menu
        join db_reservation.previleges_guser as c on c.G_user = b.cfg_group_user
        where c.NIP = ? ".$URISlug;
        $query=$this->db->query($sql, array($this->session->userdata('NIP')))->result_array();
        // print_r($query);die();
        return $query;
    }

    public function checkAuth_user_vreservation()
    {
        $base_url = base_url();
        $currentURL = current_url();
        $URL = str_replace($base_url,"",$currentURL);
        
        // get Access URL
        $getDataSess  = $this->session->userdata('menu_vreservation_grouping');
        $access = array(
            'read' => 0,
            'write' => 0,
            'update' => 0,
            'delete' => 0,
        );

        $p = $this->chkAuthDB_Base_URL_vreservation($URL);
        if (count($p) > 0 ) {
            $access = array(
                'read' => $p[0]['read'],
                'write' => $p[0]['write'],
                'update' => $p[0]['update'],
                'delete' => $p[0]['delete'],
            );
        }

        // print_r($access);die();

        $html = '<script type="text/javascript">
          var MyVarEbombAccess2;
        </script>
        ';
        if ($access['read'] == 0) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     MyVarEbombAccess2 = setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-read", function() {
                   $(".btn-read").remove();
                 });

                 $(document).ready(function () {
                     $(".btn-read").remove();
                     //window.location.href = base_url_js+"vreservation/dashboard/view";
                     $(document).ajaxComplete(function () {
                         $(".btn-read").remove();
                     });
                 });
                 setTimeout(function () {
                     clearTimeout(MyVarEbombAccess2);
                 },20000);
                 </script>
            ';
            echo $html;
        }

        if ($access['write'] == 0) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     MyVarEbombAccess2 = setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-add", function() {
                   $(".btn-add").remove();
                 });

                 $(document).ready(function () {
                     $(".btn-add").remove();
                     $(document).ajaxComplete(function () {
                        $(".btn-add").remove();
                     });
                 });
                 setTimeout(function () {
                     clearTimeout(MyVarEbombAccess2);
                 },20000);
                 </script>
            ';
            echo $html;
        }
        if ($access['update'] == 0) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     MyVarEbombAccess2 = setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-edit", function() {
                   $(".btn-edit").remove();
                 });

                 $(document).ready(function () {
                     $(".btn-edit").remove();
                     $(document).ajaxComplete(function () {
                              $(".btn-edit").remove();
                     });
                 });
                 setTimeout(function () {
                     clearTimeout(MyVarEbombAccess2);
                 },20000);
                 </script>
            ';
            echo $html;
        }
        if ($access['delete'] == 0) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     MyVarEbombAccess2 = setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-delete", function() {
                   $(".btn-delete").remove();
                 });

                 waitForEl(".btn-Active", function() {
                   $(".btn-Active").remove();
                 });

                 $(document).ready(function () {
                    $(".btn-delete").remove();
                    $(".btn-Active").remove();
                    $(document).ajaxComplete(function () {
                        $(".btn-delete").remove();
                        $(".btn-Active").remove();
                    });
                     
                 });
                 setTimeout(function () {
                     clearTimeout(MyVarEbombAccess2);
                 },20000);
                 </script>
            ';
            echo $html;
        }


        // special menu & group
        $bool = true;
        foreach ($access as $key => $value) {
            if ($value == 0) {
                $bool = false;
                break;
            }
        }

        if (!$bool) {
            $html .= '<script type="text/javascript">
                 var waitForEl = function(selector, callback) {
                   if (jQuery(selector).length) {
                     callback();
                   } else {
                     MyVarEbombAccess2 = setTimeout(function() {
                       waitForEl(selector, callback);
                     }, 100);
                   }
                 };

                 waitForEl(".btn-delete-menu-auth", function() {
                    $(".btn-delete-menu-auth").remove();
                 });

                 waitForEl(".btn-edit-menu-auth", function() {
                   $(".btn-edit-menu-auth").remove();
                 });

                 waitForEl(".btn-edit-menu-auth", function() {
                   $(".btn-edit-menu-auth").remove();
                 })

                 waitForEl(".btn-add-menu-auth", function() {
                   $(".btn-add-menu-auth").remove();
                 });

                 waitForEl(".btn-delete-menu-auth", function() {
                   $(".btn-delete-menu-auth").remove();
                 });
                 setTimeout(function () {
                     clearTimeout(MyVarEbombAccess2);
                 },20000);
                 </script>
            ';
            echo $html;
        }

        return $html;
    }

    public function get__m_additional_personel()
    {
        $sql = 'select b.Division,a.ID as ID_m_additional_personel, b.ID as ID_division from db_reservation.m_additional_personel as a
        join db_employees.division as b on a.ID_division = b.ID
        ';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function get_JSonEquipment_additional()
    {
        $sql = 'select c.ID,a.Equipment,b.ID as ID_division,b.Division,c.Qty,c.ID_m_equipment,c.CreatedBy,c.CreatedAt,c.UpdatedBy,c.UpdatedAt,d.Name as NameCreated,
                e.Name as NameUpdated
                from db_reservation.m_equipment as a join db_reservation.m_equipment_additional as c on a.ID = c.ID_m_equipment
                join db_employees.division as b on c.`Owner` = b.ID join db_employees.employees as d on d.NIP = c.CreatedBy left join db_employees.employees as e 
                on e.NIP = c.UpdatedBy
        ';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function save_sess_policy_grouping()
    {
        $NIP = $this->session->userdata('NIP');
        $sql = 'select a.* from db_reservation.cfg_policy as a join db_reservation.cfg_group_user as b on a.ID_group_user = b.ID join db_reservation.previleges_guser as c 
                on b.ID = c.G_user where c.NIP = ? limit 1';
        $query=$this->db->query($sql, array($NIP))->result_array();
        $arr = array(
            'V_BookingDay' => 1,
            'ID_group_user' => 4,
        );

        if (count($query) > 0) {
            $arr = array(
                'V_BookingDay' => $query[0]['BookingDay'],
                'ID_group_user' => $query[0]['ID_group_user'],
            );
        }

        $this->session->set_userdata($arr);
    }

    public function OpCategorybyIN($CategoryRoom)
    {
        $sql = 'select * from db_reservation.category_room where ID in ('.$CategoryRoom.')';
        $query=$this->db->query($sql, array())->result_array();
        return $query;
    }

    public function gett_booking_eq_additional($ID_equipment_add,$ID)
    {
        $sql = 'select * from db_reservation.t_booking_eq_additional where ID_t_booking = ? and ID_equipment_additional = ?';
        $query=$this->db->query($sql, array($ID,$ID_equipment_add))->result_array();
        return $query;
    }

    public function chkQty_eq_additional($ID,$ID_equipment_add)
    {
        $bool = false;
        $this->load->model('master/m_master');
        $get = $this->gett_booking_eq_additional($ID_equipment_add,$ID);
        $qty = $get[0]['Qty'];
        $getM = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_add);
        $QtyOri = $getM[0]['Qty'];
        $r = $QtyOri - $qty ;
        if ($r >= 0) {
            $bool = true;
        }
        return $bool;
    }

    public function chkQty_eq_additional2($ID,$ID_equipment_add)
    {
        $bool = false;
        $this->load->model('master/m_master');
        $getM = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_add);
        $G_data = $this->m_master->caribasedprimary('db_reservation.t_booking','ID',$ID);
        $Start = date("Y-m-d H:i:s", strtotime($G_data[0]['Start'] ));
        $End = date("Y-m-d H:i:s", strtotime($G_data[0]['End'] ));
        $QtyOri = $getM[0]['Qty'];
        $getQtyperDateTime = $this->getQtyperDateTime($ID_equipment_add,$QtyOri,$Start,$End);
        $r = $getQtyperDateTime ;
        if ($r >= 0) {
            $bool = true;
        }
        return $bool;
    }

    public function remind_vreservation()
    {
        $arr_result = array('msg' => '','error' => false);
        $g = $this->getDataT_booking();
        print_r($g);die();

    }

    public function Email_invitation($files_invitation)
    {
        $rs = '';
        if ($files_invitation != '') {
            $files_invitation = explode(';', $files_invitation);
            for ($i=0; $i < count($files_invitation); $i++) {
                if ($i == 0) {
                    $rs .= '<br>Attachment : <ul>';
                } 
                $rs .= '<li><a href="'.base_url("fileGetAny/vreservation-".$files_invitation[$i]).'" target="_blank"></i>'.$files_invitation[$i].'</a></li>';

                // last
                if ( (count($files_invitation) - 1 ) == $i  ) {
                   $rs .= '</ul>';
                }
            }
        }
        return $rs;
    }

    public function venue__fill_feedback()
    {
        $arr_result =array();
        $sql = 'select a.*,b.Name,b.EmailPU,DATE_ADD(DATE_FORMAT(Now(),"%Y-%m-%d"),INTERVAL 2 DAY) as Datelimit    
                from db_reservation.t_booking as a 
                join db_employees.employees as b on a.CreatedBy = b.NIP 
                where a.Status = 1 and a.End <= NOW() and a.Feedback IS NULL and a.End >= DATE_SUB(NOW(), INTERVAL 1 DAY) order by a.CreatedBy asc';
        $query=$this->db->query($sql, array())->result_array();
        for ($i=0; $i < count($query); $i++) {
            $Detail = $this->getDataPass($query[$i]['ID']);
            $Detail = implode('@@', $Detail);
            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');
            $Time = $query[$i]['Time'].' Minutes';

            $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$query[$i]['Room']);

            $ID_equipment_add = '-';
            $Name_equipment_add = '-';
            $boolAuthEq = 0;
            if ($query[$i]['ID_equipment_add'] != '' || $query[$i]['ID_equipment_add'] != null) {
                $ID_equipment_add = explode(',', $query[$i]['ID_equipment_add']);
                $Name_equipment_add = '<ul style = "margin-left : -28px">';
                for ($j=0; $j < count($ID_equipment_add); $j++) {
                    $btnEquipment = '';
                    // qty penggunaan
                    $gett_booking_eq_additional = $this->gett_booking_eq_additional($ID_equipment_add[$j],$query[$i]['ID']);
                    //print_r($ID_equipment_add[$j].'--'.$query[$i]['ID']);
                    if ($gett_booking_eq_additional[0]['Status'] == 0) {
                        $Status_eq_additional = 'Not Confirm';
                    }
                    elseif ($gett_booking_eq_additional[0]['Status'] == 1) {
                        $Status_eq_additional = 'Confirm';
                    }
                    else
                    {
                        $Status_eq_additional = 'Reject';
                    }

                    $Qty = $gett_booking_eq_additional[0]['Qty'];
                    $ID_equipment_additional = $gett_booking_eq_additional[0]['ID_equipment_additional'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
                    $OwnerID = $get[0]['Owner'];
                    $getX = $this->m_master->caribasedprimary('db_employees.division','ID',$OwnerID);
                    $Owner = $getX[0]['Division'];

                    $ID_m_equipment = $get[0]['ID_m_equipment'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                    $Name_equipment_add .= '<li>'.$get[0]['Equipment'].' by '.$Owner.'['.$Qty.']&nbsp ('.$Status_eq_additional.')</li>';
                }
                $Name_equipment_add .= '</ul>';
                
            }

            $ID_add_personel = '-';
            $Name_add_personel = '-';

            if ($query[$i]['ID_add_personel'] != '' || $query[$i]['ID_add_personel'] != null) {
                $Name_add_personel = $query[$i]['ID_add_personel'];
            }

            $Reqdatetime = DateTime::createFromFormat('Y-m-d', $query[$i]['Req_date']);
            $ReqdateNameDay = $Reqdatetime->format('l');

            $MarkomSupport = '<label>No</Label>';
            if ($query[$i]['MarcommSupport'] != '') {
                $MarkomSupport = '<ul style = "margin-left : -28px">';
                $dd = explode(',', $query[$i]['MarcommSupport']);
                for ($zx=0; $zx < count($dd); $zx++) {
                    // check status
                     $Status_markom = '';
                     if (strpos($dd[$zx], 'Note') === false) {
                         $g_markom = $this->g_markom($dd[$zx],$query[$i]['ID']);
                         if ($g_markom[0]['StatusMarkom'] == 0) {
                             $Status_markom = '{Not Confirm}';
                         }
                         elseif ($g_markom[0]['StatusMarkom'] == 1) {
                             $Status_markom = '{Confirm}';
                         }
                         else
                         {
                             $Status_markom = '{Reject}';
                         }

                         $dd[$zx] = $g_markom[0]['Name'];   
                     }
                     else
                     {
                        $pos = strpos($dd[$zx],':');
                        $dd[$zx] = substr($dd[$zx], 0,$pos+1).'<br>'.substr($dd[$zx], $pos+1,strlen($dd[$zx]));
                     }

                     $MarkomSupport .= '<li>'.$dd[$zx].$Status_markom.'</li>';    

                }
                $MarkomSupport .= '</ul>';
            }
            

            $KetAdditional = $query[$i]['KetAdditional'];
            $KetAdditional = json_decode($KetAdditional);
            $Participant = '<ul><li>Participant Qty : '.$query[$i]['ParticipantQty'].'</li>';
            if (count($KetAdditional) > 0) {
                foreach ($KetAdditional as $key => $value) {
                    if ($value != "" || $value != null) {
                        $Participant .= '<li>'.str_replace("_", " ", $key).' : '.$value.'</li>';
                    }
                }
            }
            $Participant .= '</ul>';
            $arr_result[] = array(
                    'Start' => $StartNameDay.', '.$query[$i]['Start'],
                    'End' => $EndNameDay.', '.$query[$i]['End'],
                    'Time' => $Time,
                    'Agenda' => $query[$i]['Agenda'],
                    'Room' => $query[$i]['Room'],
                    //'Equipment_add' => $Name_equipment_add,
                    //'Persone_add' => $Name_add_personel,
                    //'Req_date' => $query[$i]['Name'].'<br>'.$ReqdateNameDay.', '.$query[$i]['Req_date'],
                    //'Req_layout' => $query[$i]['Req_layout'],
                    'ID' => $query[$i]['ID'],
                    //'Status' => $query[$i]['Status'],
                    //'MarkomSupport' => $MarkomSupport,
                    //'Participant' => $Participant,
                    'CreatedBy' => $query[$i]['CreatedBy'],
                    //'Detail' => $Detail,
                    'EmailPU' => $query[$i]['EmailPU'],
                    'Name' => $query[$i]['Name'],
                    'Datelimit' => $query[$i]['Datelimit'],
            );  
            
        }

        return $arr_result;   
    }

    public function get_list_eq_history()
    {
        $this->load->model('master/m_master');
        $g = $this->get_JSonEquipment_additional();
        $rs = array();
        for ($i=0; $i < count($g); $i++) { 
            $ID_m_equipment = $g[$i]['ID'];
            $sql = 'select count(*) as total from (
                        select ID_equipment_additional from db_reservation.t_booking_eq_additional where ID_equipment_additional = "'.$ID_m_equipment.'" and Status = 1 and ID_t_booking in (select ID from db_reservation.t_booking where Status = 1) group by ID_t_booking
                    ) aa';
            $query=$this->db->query($sql, array())->result_array();
            $g[$i]['Usage'] = $query[0]['total'];
            $rs[] = $g[$i];        
        }

        return $rs;
    }

    public function getUsagePerRoom($Room,$condition = array())
    {
        $add_where = '';
        if (array_key_exists('date1', $condition) && array_key_exists('date2', $condition)) {
            if ($condition['date1'] != '' && $condition['date2'] != '') {
                $add_where = ' and DATE_FORMAT(Start,"%Y-%m-%d") >= "'.$condition['date1'].'" and DATE_FORMAT(Start,"%Y-%m-%d") <= "'.$condition['date2'].'"';
            }
        }
        
        $sql = 'select count(*) as total from (
                    select Room from db_reservation.t_booking where Room = "'.$Room.'" and Status = 1 '.$add_where.'
                ) aa';
        $query=$this->db->query($sql, array())->result_array();
        return $query[0]['total'];
    }

    public function getDataT_info($ID_t_booking = '',$DivisionID= '',$condition = '')
    {
        $arr_result = array();
        $this->load->model('master/m_master');
            $add_where = '';
            if ($ID_t_booking != '') {
                $add_where = ' where a.ID = "'.$ID_t_booking.'"';
            }

            if ($condition != '') {
                if ($add_where == '') {
                   $add_where .= ' where '.$condition;
                }
                else
                {
                    $add_where .= ' and '.$condition;
                }
            }

            if ($DivisionID == '') {
                $DivisionID = $this->session->userdata('PositionMain');
                $DivisionID = $DivisionID['IDDivision'];
            }
            else
            {
                $DivisionID = 12;
            }
            
        
        $sql = 'select a.*,b.Name from db_reservation.t_booking as a join db_employees.employees as b on a.CreatedBy = b.NIP '.$add_where.' order by a.Status asc,a.Start Desc';
        // print_r($sql);die();
        $query=$this->db->query($sql, array())->result_array();
        $NIP = '@';
        for ($i=0; $i < count($query); $i++) {
            $Detail = $this->getDataPass($query[$i]['ID']);
            $Detail = implode('@@', $Detail);
            $Startdatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['Start']);
            $Enddatetime = DateTime::createFromFormat('Y-m-d H:i:s', $query[$i]['End']);
            $StartNameDay = $Startdatetime->format('l');
            $EndNameDay = $Enddatetime->format('l');
            $Time = $query[$i]['Time'].' Minutes';

            $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$query[$i]['Room']);
            // cek ApproveAccess
            $Status1 = $query[$i]['Status1'];
            $Status = $query[$i]['Status'];
            //$MarcommStatus = $query[$i]['MarcommStatus'];
                $ApproveAccess = function($getRoom,$Status1,$Status,$CreatedBy,$DivisionID){

                    $PositionMain = '@';
                    $IDDivision = $DivisionID;
                    $Position = '@';
                    $NIP = '@';
                    // get Category Room to approver
                        $ApproveAccess = 0;
                        $ID_group_user = $this->session->userdata('ID_group_user');
                        $getPolicy = $this->m_master->caribasedprimary('db_reservation.cfg_policy','ID_group_user',$ID_group_user);
                        $CategoryRoom = $getPolicy[0]['CategoryRoom'];
                        $CategoryRoom = json_decode($CategoryRoom);
                        $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
                        $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
                        // find access
                            $find = 1;
                                if ($find == 1) {
                                    // get status 
                                    if ($Status1 == 0) {
                                       // find approver1
                                           $Approver1 = $getDataCategoryRoom[0]['Approver1'];
                                           $Approver1 = json_decode($Approver1);

                                           $dataApprover = array();
                                           // find by ID_group_user
                                                // $CreatedBy = $query[$i]['CreatedBy'];
                                                $cc = $this->m_master->caribasedprimary('db_reservation.previleges_guser','NIP',$CreatedBy);
                                                $dd = $this->m_master->caribasedprimary('db_employees.employees','NIP',$CreatedBy);
                                                $ID_group_user = (count($cc) > 0) ? $cc[0]['G_user'] : '';
                                            // stop loop
                                            $getLoop = true;    
                                           for ($l=0; $l < count($Approver1); $l++) {
                                                   if ($ID_group_user == $Approver1[$l]->UserType) {
                                                       // get TypeApprover
                                                       $TypeApprover = $Approver1[$l]->TypeApprover;
                                                       switch ($TypeApprover) {
                                                           case 'Position':
                                                               // get Division to access position approval
                                                                   $DivisionCreated = $dd[0]['PositionMain'];
                                                                   $DivisionCreated = explode(".", $DivisionCreated);

                                                                   $IDPositionApprover = $Approver1[$l]->Approver;

                                                                   if ($DivisionCreated[0] == 15) { // if prodi
                                                                       // find prodi
                                                                       $gg = $this->m_master->caribasedprimary('db_academic.program_study','AdminID',$CreatedBy);
                                                                       if (count($gg) > 0) {
                                                                           for ($k=0; $k < count($gg); $k++) { 
                                                                               $Kaprodi = $gg[$k]['KaprodiID'];
                                                                               if ($Kaprodi == $NIP) {
                                                                                   $find++;
                                                                                   $getLoop = false;     
                                                                                   break;
                                                                               }
                                                                           }
                                                                           
                                                                       }
                                                                    }
                                                                    else
                                                                    {
                                                                        // find by division and position
                                                                        if ($DivisionCreated[0] == $IDDivision) {
                                                                           // compare Position
                                                                           if ($IDPositionApprover == $Position) {
                                                                               $find++;
                                                                               $getLoop = false;    
                                                                               break;
                                                                           }
                                                                        }
                                                                    }
                                                               break;
                                                           
                                                           case 'Division':
                                                               if ($Approver1[$l]->Approver == $IDDivision) {
                                                                   $find++;
                                                                   $getLoop = false;    
                                                                   break;
                                                               }
                                                               break;

                                                           case 'Employees':
                                                               if ($NIP == $Approver1[$l]->Approver) {
                                                                   $find++;
                                                                   $getLoop = false;    
                                                                   break;
                                                               }
                                                               break;    
                                                       }
                                                   }

                                                   if (!$getLoop) { // stop loop
                                                       break;
                                                   }
                                           } // end loop for

                                    }
                                    else
                                    {
                                        $find = $find + 2;  
                                    }
                                }

                                if ($find == 3) {
                                   // find approver2
                                       $Approver2 = $getDataCategoryRoom[0]['Approver2'];
                                       $Approver2 = json_decode($Approver2);
                                       $DivisionID = $this->session->userdata('PositionMain');
                                       $DivisionID = $DivisionID['IDDivision'];
                                       if ($Status == 0) {
                                            for ($l=0; $l < count($Approver2); $l++) { 
                                                if ($DivisionID == $Approver2[$l]) {
                                                    $find++;    
                                                    break;
                                                }
                                            }
                                       }
                                       else
                                       {
                                        $find = $find + 2;
                                       }
                                       
                                }
                    $ApproveAccess = $find;
                    return $ApproveAccess;            
                };

                $StatusBooking = '';
                $CaseApproveAccess = $ApproveAccess($getRoom,$Status1,$Status,$query[$i]['CreatedBy'],$DivisionID);
                switch ($CaseApproveAccess) {
                    case 0:
                         $StatusBooking = 'Awaiting approval Marcomm Division';
                         break;
                    case 1:
                    case 2:
                        $StatusBooking = 'Awaiting approval 1';
                        break;
                    case 3:
                    case 4:
                        $StatusBooking = 'Awaiting approval 2';
                        break;
                    case 5:
                        $StatusBooking = 'Approved';
                        break;    
                    default:
                        # code...
                        break;
                }

            $ID_equipment_add = '-';
            $Name_equipment_add = '-';
            $boolAuthEq = 0;
            if ($query[$i]['ID_equipment_add'] != '' || $query[$i]['ID_equipment_add'] != null) {
                $ID_equipment_add = explode(',', $query[$i]['ID_equipment_add']);
                $Name_equipment_add = '<ul style = "margin-left : -28px">';
                $btnEquipment = '';
                for ($j=0; $j < count($ID_equipment_add); $j++) {
                    $btnEquipment = '';
                    // qty penggunaan
                    $gett_booking_eq_additional = $this->gett_booking_eq_additional($ID_equipment_add[$j],$query[$i]['ID']);
                    if ($gett_booking_eq_additional[0]['Status'] == 0) {
                        $Status_eq_additional = 'Not Confirm';
                    }
                    elseif ($gett_booking_eq_additional[0]['Status'] == 1) {
                        $Status_eq_additional = 'Confirm';
                    }
                    else
                    {
                        $Status_eq_additional = 'Reject';
                    }
                    $Qty = $gett_booking_eq_additional[0]['Qty'];
                    $ID_equipment_additional = $gett_booking_eq_additional[0]['ID_equipment_additional'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment_additional','ID',$ID_equipment_additional);
                    $OwnerID = $get[0]['Owner'];
                    $getX = $this->m_master->caribasedprimary('db_employees.division','ID',$OwnerID);
                    $Owner = $getX[0]['Division'];
                        if ($DivisionID == $OwnerID) {
                            if ($query[$i]['Status'] != 1) {
                                $btnEquipment = '<input type = "checkbox" class = "chkEquipment_'.$query[$i]['ID'].'" value = "'.$ID_equipment_add[$j].'">';
                            }
                            if ($boolAuthEq == 0) {
                                $boolAuthEq = 1;
                            }
                            
                        } 

                    $ID_m_equipment = $get[0]['ID_m_equipment'];
                    $get = $this->m_master->caribasedprimary('db_reservation.m_equipment','ID',$ID_m_equipment);
                    $Name_equipment_add .= '<li>'.$get[0]['Equipment'].' by '.$Owner.'['.$Qty.']&nbsp ('.$Status_eq_additional.') &nbsp'.$btnEquipment.'</li>';
                    // print_r('-div:-'.$DivisionID.'=OW:'.$OwnerID.'--ID:'.$query[$i]['ID'].'---');
                }
                $Name_equipment_add .= '</ul>';
                // print_r('---'.$boolAuthEq.'---');
                if ($boolAuthEq == 1) {
                    if ($query[$i]['Status'] != 1) {
                        $Name_equipment_add .= '<div class = "row"><div class = "col-md-6">
                                        <span class="btn btn-primary btn-xs btn_eq_additional_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Confirm">
                                            <i class="fa fa-pencil-square-o"></i> Confirm
                                           </span>
                                                </div>
                                            <div class = "col-md-6">
                                                <span class="btn btn-danger btn-xs btn_eq_additional_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Reject">
                                                    <i class="fa fa-pencil-square-o"></i> Reject
                                                   </span>
                                            </div>    
                                            </div>';
                    }
                }
                
            }

            $ID_add_personel = '-';
            $Name_add_personel = '-';

            if ($query[$i]['ID_add_personel'] != '' || $query[$i]['ID_add_personel'] != null) {
                $Name_add_personel = $query[$i]['ID_add_personel'];
            }

            $Reqdatetime = DateTime::createFromFormat('Y-m-d', $query[$i]['Req_date']);
            $ReqdateNameDay = $Reqdatetime->format('l');

            $MarkomSupport = '<label>No</Label>';
            $boolAuthMarkom = 0;
            if ($query[$i]['MarcommSupport'] != '') {
                $MarkomSupport = '<ul style = "margin-left : -28px">';
                $dd = explode(',', $query[$i]['MarcommSupport']);
                for ($zx=0; $zx < count($dd); $zx++) {
                    // check status
                     $Status_markom = '';
                     $btnMarkomSupport = '';
                     if (strpos($dd[$zx], 'Note') === false) {
                         $g_markom = $this->g_markom($dd[$zx],$query[$i]['ID']);
                         if ($g_markom[0]['StatusMarkom'] == 0) {
                             $Status_markom = '{Not Confirm}';
                         }
                         elseif ($g_markom[0]['StatusMarkom'] == 1) {
                             $Status_markom = '{Confirm}';
                         }
                         else
                         {
                             $Status_markom = '{Reject}';
                         }

                         //if ($g_markom[0]['StatusMarkom'] != 1) {
                            $btnMarkomSupport = '<input type = "checkbox" class = "MarkomSupport_'.$query[$i]['ID'].'" value = "'.$dd[$zx].'">';
                         //}
                         $dd[$zx] = $g_markom[0]['Name'];   
                     }
                     else
                     {
                        $pos = strpos($dd[$zx],':');
                        $dd[$zx] = substr($dd[$zx], 0,$pos+1).'<br>'.nl2br(substr($dd[$zx], $pos+1,strlen($dd[$zx])));
                     }

                     $MarkomSupport .= '<li>'.$dd[$zx].$Status_markom.'&nbsp'.$btnMarkomSupport.'</li>';    

                }
                $MarkomSupport .= '</ul>';
            }

            if ($MarkomSupport != '' && $DivisionID == 17) {
                $boolAuthMarkom = 1;
            }

            if ($boolAuthMarkom == 1) {
                if ($query[$i]['Status'] != 1) {
                    $MarkomSupport .= '<div class = "row"><div class = "col-md-6">
                                    <span class="btn btn-primary btn-xs btn_markom_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Confirm">
                                        <i class="fa fa-pencil-square-o"></i> Confirm
                                       </span>
                                            </div>
                                        <div class = "col-md-6">
                                            <span class="btn btn-danger btn-xs btn_markom_submit" idtbooking ="'.$query[$i]['ID'].'" action = "Reject">
                                                <i class="fa fa-pencil-square-o"></i> Reject
                                               </span>
                                        </div>    
                                        </div>';
                }
            }

            $KetAdditional = $query[$i]['KetAdditional'];
            $KetAdditional = json_decode($KetAdditional);
            $Participant = '<ul><li>Participant Qty : '.$query[$i]['ParticipantQty'].'</li>';
            if (count($KetAdditional) > 0) {
                foreach ($KetAdditional as $key => $value) {
                    if ($value != "" || $value != null) {
                        $Participant .= '<li>'.str_replace("_", " ", $key).' : '.$value.'</li>';
                    }
                }
            }
            $Participant .= '</ul>';
            // $boolAuth = $this->boolAuthData($query[$i]['CreatedBy'],$CaseApproveAccess,$boolAuthEq,$boolAuthMarkom);
            $boolAuth = true;
            if ($boolAuth) {
                   $arr_result[] = array(
                           'Start' => $StartNameDay.', '.$query[$i]['Start'],
                           'End' => $EndNameDay.', '.$query[$i]['End'],
                           'Time' => $Time,
                           'Agenda' => $query[$i]['Agenda'],
                           'Room' => $query[$i]['Room'],
                           'Equipment_add' => $Name_equipment_add,
                           'Persone_add' => $Name_add_personel,
                           'Req_date' => $query[$i]['Name'].'<br>'.$ReqdateNameDay.', '.$query[$i]['Req_date'],
                           'Req_layout' => $query[$i]['Req_layout'],
                           'ID' => $query[$i]['ID'],
                           'Status' => $query[$i]['Status'],
                           'MarkomSupport' => $MarkomSupport,
                           'Participant' => $Participant,
                           'ApproveAccess' => $CaseApproveAccess,
                           'StatusBooking' => $StatusBooking,
                           'CreatedBy' => $query[$i]['CreatedBy'],
                           'Detail' => $Detail,
                   );
            }   
            
        }

        return $arr_result;  
    }


    public function Get_Approver($ApprverNumber,$ID_CategoryRoom,$CreatedBy)
    {
        $Name = '';
        $CategoryRoomByRoom = $ID_CategoryRoom;
        $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
        $Approver1 = $getDataCategoryRoom[0]['Approver1'];
        $Approver1 = json_decode($Approver1);
        $ID_group_user = $this->m_master->caribasedprimary('db_reservation.previleges_guser','NIP',$CreatedBy);
        $ID_group_user = $ID_group_user[0]['G_user'];
        $EM = $this->m_master->caribasedprimary('db_employees.employees','NIP',$CreatedBy);
        if ($ApprverNumber == 1) {
            for ($l=0; $l < count($Approver1); $l++) {
                // find by ID_group_user
                    if ($ID_group_user == $Approver1[$l]->UserType) {
                        // get TypeApprover
                        $TypeApprover = $Approver1[$l]->TypeApprover;
                        switch ($TypeApprover) {
                            case 'Position':
                                // get Division to access position approval
                                    $PositionMain = $EM[0]['PositionMain'];
                                    $ex = explode('.', $PositionMain);
                                    $IDDivision = $ex[0];
                                    $IDPositionApprover = $Approver1[$l]->Approver; 
                                    if ($IDDivision == 15 || $IDDivision == 33) { // if prodi
                                        $sqlgg = 'select * from db_academic.program_study where AdminID = ? or KaprodiID = ? or Laboran = ?';
                                        $gg=$this->db->query($sqlgg, array($CreatedBy,$CreatedBy,$CreatedBy))->result_array();
                                        if (count($gg) > 0) {
                                            for ($k=0; $k < count($gg); $k++) { 
                                                $Kaprodi = $gg[$k]['KaprodiID'];
                                                $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Kaprodi);
                                                for ($m=0; $m < count($getApprover1); $m++) { 
                                                    if ($getApprover1[$m]['StatusEmployeeID'] > 0) {
                                                        $Name =  $getApprover1[$m]['Name'];
                                                    }
                                                }
                                            }
                                            
                                        }
                                    }
                                    elseif ($IDDivision == 34 || $IDDivision == 33) {
                                        $sqlgg = 'select * from db_academic.faculty where AdminID = ? or NIP = ? or Laboran = ?';
                                        $gg=$this->db->query($sqlgg, array($CreatedBy,$CreatedBy,$CreatedBy))->result_array();
                                        if (count($gg) > 0) {
                                            for ($k=0; $k < count($gg); $k++) { 
                                                $Dekan = $gg[$k]['NIP'];
                                                $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Dekan);
                                                for ($m=0; $m < count($getApprover1); $m++) { 
                                                    if ($getApprover1[$m]['StatusEmployeeID'] > 0) {
                                                         $Name =  $getApprover1[$m]['Name'];
                                                    }
                                                }
                                            }
                                            
                                        }
                                    }
                                    elseif ($IDDivision == 14) { // lecturer
                                        $ProdiID = $EM[0]['ProdiID'];
                                        $G_Prodi = $this->m_master->caribasedprimary('db_academic.program_study','ID',$ProdiID);
                                        $Kaprodi = $G_Prodi[0]['KaprodiID'];
                                        $G_Kaprodi = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Kaprodi);
                                        $Name =  $G_Kaprodi[0]['Name'];
                                    }
                                    else
                                    {

                                        // find by division and position
                                        // print_r($IDPositionApprover);
                                        $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionMain',$IDDivision.'.'.$IDPositionApprover);
                                        $arr = array();
                                        for ($k=0; $k < count($getApprover1); $k++) {
                                            if ($getApprover1[$k]['StatusEmployeeID'] > 0) {
                                                $arr[] =  $getApprover1[$k];
                                            } 
                                           
                                        }

                                        $getApprover1 = $arr;

                                        if (count($getApprover1) == 0) {
                                            $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionOther1',$IDDivision.'.'.$IDPositionApprover);
                                            if (count($getApprover1) == 0) {
                                                $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionOther2',$IDDivision.'.'.$IDPositionApprover);
                                                if (count($getApprover1) == 0) {
                                                    $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','PositionOther3',$IDDivision.'.'.$IDPositionApprover);
                                                }
                                            }
                                        }

                                        for ($k=0; $k < count($getApprover1); $k++) {
                                            if ($getApprover1[$k]['StatusEmployeeID'] > 0) {
                                                $Name =  $getApprover1[$k]['Name'];
                                            } 
                                           
                                        }
                                    }
                                break;
                            
                            case 'Division':
                                $getApprover1 = $this->m_master->caribasedprimary('db_employees.division','ID',$Approver1[$l]->Approver);
                                for ($k=0; $k < count($getApprover1); $k++) {
                                   $Name =  $getApprover1[$k]['Abbreviation']; 
                                }
                                break;

                            case 'Employees':
                                $getApprover1 = $this->m_master->caribasedprimary('db_employees.employees','NIP',$Approver1[$l]->Approver);
                                for ($k=0; $k < count($getApprover1); $k++) {
                                   $Name =  $getApprover1[$k]['Name'];  
                                }
                                break;    
                        }
                    }
            } // end loop for
        }
        else
        {
            $Approver2Div = $getDataCategoryRoom[0]['Approver2'];
            $Approver2Div = json_decode($Approver2Div);
            for ($zz=0; $zz < count($Approver2Div); $zz++) { 
                $rdata = $Approver2Div[$zz];
                $TypeApprover = $rdata->TypeApprover;
                $bool = false;
                switch ($TypeApprover) {
                    case 'Division':
                        $DivisionApprove = $this->m_master->caribasedprimary('db_employees.division','ID',$rdata->Approver);
                        $Name = $DivisionApprove[0]['Abbreviation'];
                        $bool = true;
                        break;
                    case 'Employees':
                        $NIPAPP2 = $rdata->Approver;
                        $G_emp = $this->m_master->caribasedprimary('db_employees.employees','NIP',$NIPAPP2);
                        $Name = $G_emp[0]['Name'];
                        $bool = true;
                        break;

                }

                if ($bool) {
                    break;
                }
            }
        }
        
        return $Name;
    }

    public function checkRuleDivisioOrNot($ApprverNumber,$ID_t_booking)
    {
        $this->load->model('master/m_master');
        $Bool = false; // selain division
        $query = $this->m_master->caribasedprimary('db_reservation.t_booking','ID',$ID_t_booking);
        $Room = $query[0]['Room'];
        $getRoom = $this->m_master->caribasedprimary('db_academic.classroom','Room',$Room);
        $CategoryRoomByRoom = $getRoom[0]['ID_CategoryRoom'];
        $getDataCategoryRoom = $this->m_master->caribasedprimary('db_reservation.category_room','ID',$CategoryRoomByRoom);
        $Approver1 = $getDataCategoryRoom[0]['Approver1'];
        $Approver1 = json_decode($Approver1);
        $CreatedBy = $query[0]['CreatedBy'];
        $ID_group_user = $this->m_master->caribasedprimary('db_reservation.previleges_guser','NIP',$CreatedBy);
        $ID_group_user = $ID_group_user[0]['G_user'];
        if ($ApprverNumber == 1) {
            for ($l=0; $l < count($Approver1); $l++) {
                // find by ID_group_user
                    if ($ID_group_user == $Approver1[$l]->UserType) {
                        // get TypeApprover
                        $TypeApprover = $Approver1[$l]->TypeApprover;
                        if ($TypeApprover == 'Division') {
                            $Bool = true;
                            break;
                        }
                    }
            } // end loop for
        }
        else
        {
            $Approver2Div = $getDataCategoryRoom[0]['Approver2'];
            $Approver2Div = json_decode($Approver2Div);
            for ($zz=0; $zz < count($Approver2Div); $zz++) { 
                $rdata = $Approver2Div[$zz];
                $TypeApprover = $rdata->TypeApprover;
                if ($TypeApprover == 'Division') {
                    $Bool = true;
                    break;
                }
            }
        }
        return $Bool;
    }

    public function auth_approval($Username,$Password,$DivisionID){
        $plan_password = $Username.''.$Password;
        $pas = md5($plan_password);
        $pass = sha1('jksdhf832746aiH{}{()&(*&(*'.$pas.'HdfevgyDDw{}{}{;;*766&*&*');
        $sql = 'select count(*) as total
                from db_employees.employees
                where SPLIT_STR(PositionMain, ".", 1) = "'.$DivisionID.'" and NIP = "'.$Username.'"
                and Password = "'.$pass.'"
                ';
        $query = $this->db->query($sql,array())->result_array();
        if ($query[0]['total'] == 1) {
            return true;
        }
        else
        {
            return false;
        }
    }
}