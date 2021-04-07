<?php

defined('BASEPATH') or exit('No direct script access allowed');

class M_kb_log extends CI_Model {
	function __construct() {
	   parent::__construct();
	   $this->columns = isset($this->subdata['tbl_kb_log']) ? $this->subdata['tbl_kb_log']['columns'] : [];
	   $this->sqlFrom = '
	   					select a.*,b.Name as ActionByName,kt.Type,kt.id as kb_type_id,qdj.Abbr as DepartmentCode,
	   					qdj.ID as IDDepartment
	   					from db_employees.knowledge_base_log as a 
	   					join db_employees.employees as b on a.ActionBy = b.NIP
	   					join db_employees.kb_type as kt on kt.ID = a.IDType
	   					'.$this->m_master->QueryDepartmentJoin('kt.IDDivision').' 
	                    ';
	}

	function get_all($start = 0, $length, $filter = array(), $order = array()) {
	    $this->filtered($filter);
	    if ($order) {
	        $order['column'] = $this->columns[$order['column']]['name'];
	        $this->db->order_by($order['column'], $order['dir']);
	    }
	    $data = $this->db->select('ActionBy,ActionByName, ActionAt,Action,Type,kb_type_id,DepartmentCode,Desc,File,Status')
	            ->limit($length, $start);

	    return $this->db->get('( '.$this->sqlFrom .' ) as summary');
	}

	private function get_value_search_file($urlSearch){
		$search = '';
		$pathReplace =  '/puis/fileGetAny/kb-'; // localhost
		$pathReplace2 =  '/fileGetAny/kb-'; // domain
	    $url_components = parse_url($urlSearch); 
	    if (!array_key_exists('host', $url_components)) {
	    	return $search;
	    }
	    else
	    {
	    	$path = $url_components['path'];
	    	$search =  str_replace($pathReplace, '', $path);
	    	$search =  str_replace($pathReplace2, '', $search);
	    	return $search;
	    }

	    return '';
	}

	private function filtered($filter = array()){
	    if ($filter) {
	        $this->db->group_start();
	        foreach ($filter as $column => $value) {

	        	if (is_int($column)) {
	        		if ($value  == '%') {
	        			$value = '';
	        		}

	        		if ($column == 6) {
	        			$value =  $this->get_value_search_file($value);	
	        		}

	        		$this->db->like('IFNULL(' . $this->columns[$column]['name'] . ',"")', $value);
	        	}
	            
	        }
	        $this->db->group_end();
	    }
	}

	public function get_total($filter = array()){
	    $this->filtered($filter);
	    $data = $this->db->count_all_results('( '.$this->sqlFrom .' ) as summary');
	    return $data;
	}


}