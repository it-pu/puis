<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

function compareDate($first,$second){
	$firstDateMonth = date("m",strtotime($first));
	$firstDateYear = date("y",strtotime($first));
	$secondDateMonth = date("m",strtotime($second));
	$secondDateYear = date("y",strtotime($second));
	$dateName = "";
	
	if($firstDateYear != $secondDateYear){
		$dateName = date("D d M Y",strtotime($first))." - ".date("D d M Y",strtotime($second));
	}else{
		if($firstDateMonth == $secondDateMonth){
			$dateName = date("D d",strtotime($first))." - ".date("D d M Y",strtotime($second));
		}else{
			$dateName = date("D d M",strtotime($first))." - ".date("D d M Y",strtotime($second));
		}
	}
	return $dateName;
}


function labelWApproval($number){
	switch ($number) {
		case 1:
			return "<span class='btn btn-xs btn-info'><i class='fa fa-hourglass'></i> Process</span>";
			break;
		case 2:
			return "<span class='btn btn-xs btn-primary'><i class='fa fa-check-square-o'></i> Approved</span>";
			break;
		case 3:
			return "<span class='btn btn-xs btn-danger'><i class='fa fa-times'></i> Rejected</span>";
			break;
		
		default:
			return "<span class='btn btn-xs btn-default'><i class='fa fa-question'></i> Unknow</span>";
			break;
	}
}

function labelApproval($number){
	switch ($number) {
		case 1:
			return "<span class='btn btn-xs btn-info'><i class='fa fa-exclamation-triangle'></i> Need Approval</span>";
			break;
		case 2:
			return "<span class='btn btn-xs btn-primary'><i class='fa fa-check-square-o'></i> Approved</span>";
			break;
		case 3:
			return "<span class='btn btn-xs btn-danger'><i class='fa fa-times'></i> Rejected</span>";
			break;
		
		default:
			return "<span class='btn btn-xs btn-default'><i class='fa fa-question'></i> Unknow</span>";
			break;
	}
}


function labelProfileDB($table,$data){
	$CI = & get_instance();
	$CI->load->model('General_model');
	$results = $CI->General_model->fetchData($table,$data)->row();
	return $results;
}

