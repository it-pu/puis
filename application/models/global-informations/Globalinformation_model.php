<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class Globalinformation_model extends CI_Model{

    public function fetchStudentsPS($count=false,$single=false,$param='',$start='',$limit='',$order=''){
    	$where='';
        if(!empty($param)){
            $where = 'WHERE ';
            $counter = 0;
            foreach ($param as $key => $value) {
                if($counter==0){
                    $where = $where.$value['field']." ".$value['data'];
                }
                else{
                    $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                }
                $counter++;
            }
        }

        $lims="";
        if($start!="" || $limit!=""){
            $lims = " LIMIT {$start},{$limit}";	
        }

        $sorted = " order by ".(!empty($order) ? $order : " NPM desc");

        if($count){
            $psquery = 'call db_academic.fetchStudentsTotal("'.$where.'")';
            $query = $this->db->query($psquery);
            $value = $query->row();
        }else{
            $psquery = 'call db_academic.fetchStudents("'.$where.'" , "'.$lims.'","'.$sorted.'")';
        	$query = $this->db->query($psquery);
            //var_dump($this->db->last_query());
        	if($single){
        		$value = $query->row();
        	}else{
        		$value = $query->result();
        	}
        }
    	
        //limit execute time
        mysqli_next_result( $this->db->conn_id );
        $query->free_result(); 
        //end limit execute time
    	return $value;
    }

    public function detailStudent($tablename,$data){
    	$this->db->select("a.*,b.Nama as religionName, c.ctr_name as nationalityName, e.ProvinceName");
    	$this->db->from($tablename." a");
    	$this->db->join("db_admission.agama b","b.ID=a.ReligionID","left");
    	$this->db->join("db_admission.country c","c.ctr_code=a.NationalityID","left");
    	$this->db->join("db_admission.province_region d","d.ID=a.ProvinceID","left");
    	$this->db->join("db_admission.province e","d.ProvinceID=e.ProvinceID","left");
    	$this->db->where($data);
    	$query = $this->db->get();
    	return $query;
    }


    public function fetchLecturer($count=false,$param='',$start='',$limit='',$order=''){
        $where='';
        if(!empty($param)){
            $where = 'WHERE ';
            $counter = 0;
            foreach ($param as $key => $value) {
                if($counter==0){
                    $where = $where.$value['field']." ".$value['data'];
                }
                else{
                    $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                }
                $counter++;
            }
        }

        $lims="";
        if($start!="" || $limit!=""){
            $lims = " LIMIT {$start},{$limit}";	
        }

        if($count){
            $select = "count(*) as Total";
        }else{
            $select = "em.*, ps.NameEng AS ProdiNameEng, ps.DegreeEng as ProdiDegree, es.Description as EmpStatus, r.Religion as EmpReligion, le.Level as EmpLevelEduName, le.Description as EmpLevelDesc, lap.Position as EmpAcaName";
        }
        $sorted = " order by ".(!empty($order) ? $order : 'em.ID DESC');
        $string = "SELECT {$select}
				   FROM db_employees.employees em
				   LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
				   LEFT JOIN db_employees.employees_status es ON (es.IDStatus = em.StatusLecturerID)
				   LEFT JOIN db_employees.religion r ON (r.IDReligion = em.ReligionID)
				   LEFT JOIN db_employees.level_education le ON (le.ID = em.LevelEducationID)
				   LEFT JOIN db_employees.lecturer_academic_position lap ON (lap.ID = em.LecturerAcademicPositionID)
                   {$where} {$sorted} {$lims} ";

        
        $value  = $this->db->query($string);
     	//var_dump($this->db->last_query());
     	return $value;
    }


    public function fetchEmployee($count=false,$param='',$start='',$limit='',$order=''){
        $where='';
        if(!empty($param)){
            $where = 'WHERE ';
            $counter = 0;
            foreach ($param as $key => $value) {
                if($counter==0){
                    $where = $where.$value['field']." ".$value['data'];
                }
                else{
                    $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                }
                $counter++;
            }
        }

        $lims="";
        if($start!="" || $limit!=""){
            $lims = " LIMIT {$start},{$limit}";	
        }

        if($count){
            $select = "count(*) as Total";
        }else{
            $select = "em.*, el.Name as ProdiDegree, el.DescriptionEng as ProdiDegreeEng, ps.NameEng AS ProdiNameEng, es.Description as EmpStatus, r.Religion as EmpReligion, le.Level as EmpLevelEduName, le.Description as EmpLevelDesc, lap.Position as EmpAcaName, d.Division as DivisionMain, p.Position as PositionMain, (case when (DATE_FORMAT(em.DateOfBirth,'%m-%d') = DATE_FORMAT(now(),'%m-%d') ) then 1 else null end ) as isMyBirthday";
        }
        $sorted = " order by ".(!empty($order) ? $order : 'em.ID DESC');
        
        $string = "SELECT {$select}
				   FROM db_employees.employees em
				   LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                   LEFT JOIN db_academic.education_level el ON (ps.EducationLevelID = el.ID)
                   LEFT JOIN db_employees.employees_status es ON (es.IDStatus = em.StatusEmployeeID)
				   LEFT JOIN db_employees.religion r ON (r.IDReligion = em.ReligionID)
				   LEFT JOIN db_employees.level_education le ON (le.ID = em.LevelEducationID)
				   LEFT JOIN db_employees.lecturer_academic_position lap ON (lap.ID = em.LecturerAcademicPositionID)
                   LEFT JOIN db_employees.division d on (d.ID = SUBSTRING_INDEX(em.PositionMain,'.',1) )
                   LEFT JOIN db_employees.position p on (p.ID = SUBSTRING_INDEX(em.PositionMain,'.',-1) )
                   {$where} {$sorted} {$lims} ";
        
        $value  = $this->db->query($string);
     	//var_dump($this->db->last_query());
     	return $value;
    }



    public function fetchStudentTranscript($npm){
        if(!empty($npm)){
            $transcriptPS = $this->db->query("call db_academic.fetchStudentTranscript(".$npm.")");
            $transcript = $transcriptPS->result();
            //limit execute time
            mysqli_next_result( $this->db->conn_id );
            $transcriptPS->free_result(); 
            //end limit execute time

            $json = array();
            if(!empty($transcript)){
                $semesno = 1;$currTermSession = "";$currTermYear=0;$no=1;
                $totalCredit=0;$totalGrade=0;$totalPoint=0;
                //get courses by semester has been take
                $termcode="";$termyear=0;
                $coursesByTerm = array();
                $termName = "";
                foreach ($transcript as $v) {
                    if($currTermSession != $v->TermSession){
                        $explodeTerm = explode(" - ", $v->Term);
                        if(!empty($explodeTerm)){
                            $trmName = $explodeTerm[0];
                        }else{$trmName=$v->Term;}
                        $termName = "Semester ".$semesno;
                        $coursesByTerm[$termName] = array();
                        $coursesByTerm[$termName] = array("Semester"=>$semesno,"Session"=>$v->TermSession,"Term"=>$v->Term);
                        $semesno++;
                        $no=1;
                    }
                    $cno = $no;
                    $coursesByTerm[$termName]['courses'][] = $v;

                    $currTermSession = $v->TermSession;
                    $no++;
                }

                //calculate GPA
                if(!empty($coursesByTerm)){
                    $totalIPS = 0;$IPK=0;$LastTerm="";$Credit=0;
                    foreach ($coursesByTerm as $key => $value) {
                        $parent = $coursesByTerm[$key];
                        $LastTerm = $parent['Term'];
                        $totalCredit = 0;$totalGrade=0;$IPS=0;$totalPoint=0;
                        foreach ($parent['courses'] as $c) {
                            $Score = round($c->Score,2);
                            $GradeValue = round($c->GradeValue,2);
                            $Point = round($c->Point,2);
                            $totalCredit = $totalCredit + $c->Credit;
                            $totalGrade  = $totalGrade + $GradeValue;
                            $totalPoint  = $totalPoint + $Point;
                            $IPS  = round($totalPoint / $totalCredit,2);
                        }
                        $GPAS = array("TotalGrade"=>$totalGrade,"TotalCredit"=>$totalCredit,"TotalPoint"=>$totalPoint,"IPS"=>$IPS);
                        $coursesByTerm[$key]['CalculateSemes'] = $GPAS;
                        $totalIPS = $totalIPS + $IPS;
                        $totalSemester = count($coursesByTerm);
                        $IPK = $totalIPS / $totalSemester;
                        $Credit = $totalCredit + $Credit;
                    }
                    $coursesByTerm['GPA'] = array("IPS"=>round($totalIPS,2),"IPK"=>round($IPK,2),"TotalCredit"=>$Credit,"LastSemester"=>$totalSemester,"LastTerm"=>$LastTerm);
                }

                $json = $coursesByTerm;
            }

            return $json;
        }
        return false;
    }


    public function fetchSubjectType($count=false,$param='',$start='',$limit='',$order=''){
        $where='';
        if(!empty($param)){
            $where = 'WHERE ';
            $counter = 0;
            foreach ($param as $key => $value) {
                if($counter==0){
                    $where = $where.$value['field']." ".$value['data'];
                }
                else{
                    $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                }
                $counter++;
            }
        }

        $lims="";
        if($start!="" || $limit!=""){
            $lims = " LIMIT {$start},{$limit}"; 
        }

        if($count){
            $select = "count(*) as Total";
        }else{
            $select = "*";
        }
        $sorted = " order by ".(!empty($order) ? $order : 'a.ID DESC');
        
        $string = "SELECT {$select}
                   FROM db_mail_blast.subject_type as a
                   {$where} {$sorted} {$lims} ";
        
        $value  = $this->db->query($string);
        //var_dump($this->db->last_query());
        return $value;
    }


    /* Added by Adhi 2020-01-16 */
    public function fetchTotalDataStudent($param){
        $where='';
          if(!empty($param)){
              $where = 'WHERE ';
              $counter = 0;
              foreach ($param as $key => $value) {
                  if($counter==0){
                      $where = $where.$value['field']." ".$value['data'];
                  }
                  else{
                      $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                  }
                  $counter++;
              }
          }

          $psquery = 'call db_academic.fetchStudentsTotal("'.$where.'" )';
          $query = $this->db->query($psquery);

          $value = $query->row();

          mysqli_next_result( $this->db->conn_id );
          $query->free_result(); 

          return $value;
    }
    /* end Added by Adhi */


/*SEND MAIL CONFIG*/    
    public function sendMail($data=null){
        $result = array();
        //Get config default smtp mail
        $this->db->from("db_mail_blast.cog_mail");
        $this->db->where(array("isActive"=>1));
        $query = $this->db->get();
        $config = $query->row();
        if(!empty($config)){
            $config_mail = array(
                'protocol' => 'smtp',
                'smtp_host' => $config->smtp_host,
                'smtp_port' => $config->smtp_port,
                'smtp_user' => $config->smtp_mail,
                'smtp_pass' => $config->smtp_mail_pass,
                'mailtype' => 'html',
                'charset' => 'iso-8859-1',
                'wordwrap' => TRUE
            );

            $message_text = "";
            $template_message = $config->template_message;

            if(preg_match_all('/{{+(.*?)}}/', $template_message, $matches)){
                if(count($matches) > 0){
                    $message_data_replace = $template_message;
                    foreach ($matches[0] as $m) {                        
                        if($m == "{{message_subject}}"){
                            $message_data_replace = preg_replace('/'.$m.'/',$data['subject'],$message_data_replace);
                        }
                        if($m == "{{message_mail}}"){
                            $message_data_replace = preg_replace('/'.$m.'/',$data['message'],$message_data_replace);
                        }
                        $message_text=$message_data_replace;
                    }
                }
            }

            $max_execution_time = 630;
            ini_set('memory_limit', '-1');
            ini_set('max_execution_time', $max_execution_time); //60 seconds = 1 minutes
            $TiketID = mt_rand();// unique number
            $this->load->library('email', $config_mail);

            $this->email->set_newline("\r\n");
            $this->email->from($data['from'] , $data['from_label']);
            $this->email->to($data['to']);
            if(!empty($data['cc'])){
                $this->email->cc($data['cc']);
            }
            if(!empty($data['bcc'])){
                $this->email->bcc($data['bcc']);
            }
            $this->email->subject($data['subject'].' - '.$TiketID);
            $this->email->message($message_text);
            if($this->email->send()){
                $result['status'] = 1;
                $result['msg'] = "Email Send";
            }else{
                $result['status'] = 0;
                $result['msg'] = $this->email->print_debugger();
            }
            return $result;
        }

        return $result['status']=0;

    }
/*END SEND MAIL CONFIG*/

/*ACCESS ROLE*/
    public function fetchAccessRole($count=false,$param='',$start='',$limit='',$order=''){
        $where='';
        if(!empty($param)){
            $where = 'WHERE ';
            $counter = 0;
            foreach ($param as $key => $value) {
                if($counter==0){
                    $where = $where.$value['field']." ".$value['data'];
                }
                else{
                    $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                }
                $counter++;
            }
        }

        $lims="";
        if($start!="" || $limit!=""){
            $lims = " LIMIT {$start},{$limit}"; 
        }

        if($count){
            $select = "count(*) as Total";
        }else{
            $select = "a.*, d.Division, p.Position";
        }
        $sorted = " order by ".(!empty($order) ? $order : 'a.ID DESC');
        
        $string = "SELECT {$select}
                   FROM db_mail_blast.role_mail as a
                   LEFT JOIN db_employees.division d on (d.ID = SUBSTRING_INDEX(a.PositionMain,'.',1) )
                   LEFT JOIN db_employees.position p on (p.ID = SUBSTRING_INDEX(a.PositionMain,'.',-1) )
                   {$where} {$sorted} {$lims} ";
        
        $value  = $this->db->query($string);
        //var_dump($this->db->last_query());
        return $value;
    }
/*END ACCESS ROLE*/
}
