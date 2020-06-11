<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
 
class M_log_content extends CI_Model{

	public function fetchLogContent($count=false,$param='',$start='',$limit='',$order=''){
        $where=''; $tablename ='';
        if(!empty($param)){
            $where = 'WHERE '; $conditionDate = '';
            $counter = 0; $notExistquery = '';
            foreach ($param as $key => $value) {
            	if($value['field'] == 'a.TypeContent'){
            		$tablename = $value['data'];
            	}
                if($counter==0){
                    $where = $where.$value['field']." ".$value['data'];                        
                }else{ 
                    $where = $where.$value['filter']." ".$value['field']." ".$value['data'];
                }
                $counter++;
            }
        }

        $lims="";
        if($start!="" || $limit!=""){
            $lims = " LIMIT {$start},{$limit}"; 
        }

        $groupby = '';
        $sorted = '';
        $order_by = '';
        if($count){
            $select = "count(DISTINCT a.NIP) as Total";
        }else{
            $select = "c.NIP, c.`Name`, a.TypeContent, a.ViewedAt, b.*
					,( select count(DISTINCT ContentID) from  db_employees.log_countable_content where NIP = a.NIP and TypeContent {$tablename}) as totalRead";
            $groupby = "GROUP BY a.NIP";
            $order_by = 'order by a.ContentID asc';
        }
        
        $string = " select {$select}
					from db_employees.log_countable_content a
					LEFT JOIN db_employees.user_qna b on a.ContentID = b.Id
					LEFT JOIN db_employees.employees c on c.NIP = a.NIP
                   {$where} {$groupby} {$order_by} {$lims}";
        $value  = $this->db->query($string);
        //var_dump($this->db->last_query());
        return $value;
	}

}