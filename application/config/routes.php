<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$route['default_controller'] = 'c_login';
$route['404_override'] = 'dashboard/c_dashboard/page404';
$route['translate_uri_dashes'] = FALSE;


$route['navigation/(:num)'] = 'c_departement/navigation/$1';
$route['profile'] = 'c_dashboard/profile';
$route['page404'] = 'dashboard/c_dashboard/page404';

// FROM PORTAL
$route['uath/__portal4SignIn'] = 'c_login/portal4SignIn';

$route['uath/authUserPassword'] = 'c_login/authUserPassword';
$route['auth/authGoogle'] = 'c_login/authGoogle';
// $route['auth/gen_pass'] = 'c_login/gen_pass';
$route['auth/logMeOut'] = 'c_login/logMeOut';
$route['sendmail'] = 'c_login/sendmail';
$route['callback'] = 'c_login/callback';



$route['db/(:any)'] = 'auth/c_auth/db/$1';
$route['foto'] = 'auth/c_auth/foto';
$route['migration-students'] = 'auth/c_auth/migrationStudent';
$route['rekap/(:num)'] = 'auth/c_rekap/rekap_/$1';



// === Dashboard ===
$route['dashboard'] = 'dashboard/c_dashboard';
$route['profile/(:any)'] = 'dashboard/c_dashboard/profile/$1';
$route['change-departement'] = 'dashboard/c_dashboard/change_departement';


// === Academic ===
$route['academic/curriculum'] = 'page/academic/c_kurikulum/kurikulum';
$route['academic/kurikulum-detail'] = 'page/academic/c_kurikulum/kurikulum_detail';
$route['academic/kurikulum/add-kurikulum'] = 'page/academic/c_kurikulum/add_kurikulum';
$route['academic/kurikulum/loadPageDetailMataKuliah'] = 'page/academic/c_kurikulum/loadPageDetailMataKuliah';

$route['academic/kurikulum/data-conf'] = 'page/academic/c_kurikulum/getDataConf';
//$route['academic/kurikulum/getClassGroup'] = 'page/academic/c_kurikulum/getClassGroup';


$route['academic/kurikulum-detail-mk'] = 'page/academic/c_kurikulum/kurikulum_detail_mk';
$route['academic/courses'] = 'page/academic/c_matakuliah/mata_kuliah';
$route['academic/dataTableMK'] = 'page/academic/c_matakuliah/dataTableMK';

$route['academic/tesdb'] = 'page/academic/c_tahun_akademik/tesdb';
$route['academic/academic-year'] = 'page/academic/c_tahun_akademik/tahun_akademik';
$route['academic/tahun-akademik-table'] = 'page/academic/c_tahun_akademik/tahun_akademik_table';
$route['academic/detail-tahun-akademik'] = 'page/academic/c_tahun_akademik/page_detail_tahun_akademik';
$route['academic/modal-tahun-akademik'] = 'page/academic/c_tahun_akademik/modal_tahun_akademik';
$route['academic/tahun-akademik/(:any)'] = 'page/academic/c_tahun_akademik/tahun_akademik_detail/$1';

$route['academic/tahun-akademik-detail'] = 'page/academic/c_tahun_akademik/tahun_akademik_detail2';
$route['academic/tahun-akademik-detail-date'] = 'page/academic/c_tahun_akademik/tahun_akademik_detail_date';

$route['academic/semester-antara'] = 'page/academic/c_semester_antara';
$route['academic/semester-antara/details/(:num)'] = 'page/academic/c_semester_antara/loadDetails/$1';


$route['academic/ketersediaan-dosen'] = 'page/academic/c_akademik/ketersediaan_dosen';
$route['academic/ModalKetersediaanDosen'] = 'page/academic/c_akademik/Modal_KetersediaanDosen';

$route['academic/timetables'] = 'page/academic/c_jadwal';

$route['academic/timetables/list'] = 'page/academic/c_timetables/list_timetables';

$route['academic/study-planning'] = 'page/academic/c_study_planning';

$route['academic/references'] = 'page/academic/c_reference';


// Jadwal Ujian
$route['academic/__setPageJadwalUjian'] = 'page/academic/c_jadwal_ujian/setPageJadwal';
$route['academic/__setPageJadwal'] = 'page/academic/c_jadwal/setPageJadwal';
$route['academic/exam-schedule'] = 'page/academic/c_jadwal_ujian';

$route['academic/exam-schedule/list-exam'] = 'page/academic/c_jadwal_ujian/list_exam';
$route['academic/exam-schedule/list-waiting-approve'] = 'page/academic/c_jadwal_ujian/list_waiting_approve';
$route['academic/exam-schedule/set-exam-schedule'] = 'page/academic/c_jadwal_ujian/set_exam_schedule';
$route['academic/exam-schedule/edit-exam-schedule/(:num)'] = 'page/academic/c_jadwal_ujian/edit_exam_schedule/$1';

// ---- Score ----
$route['academic/score'] =  'page/academic/c_score';
$route['academic/inputScore'] =  'page/academic/c_score/inputScore';
$route['academic/score/monitoring-score'] =  'page/academic/c_score/monitoring_score';

// ---- Transcript ----
$route['academic/transcript'] =  'page/academic/c_transcript';
$route['academic/transcript/setting-transcript'] =  'page/academic/c_transcript/setting_transcript';

// ---- Final Project ----
$route['academic/final-project'] =  'page/academic/c_final_project';


// --- Modal Academic ----
$route['academic/modal-tahun-akademik-detail-prodi'] = 'page/academic/c_akademik/modal_tahun_akademik_detail_prodi';
$route['academic/modal-tahun-akademik-detail-lecturer'] = 'page/academic/c_akademik/modal_tahun_akademik_detail_lecturer';

// ======= human-resources ======
$route['human-resources/lecturers'] = 'page/database/c_database/lecturers';
$route['human-resources/employees'] = 'page/hr/c_employees/employees';
$route['human-resources/employees/input-employees'] = 'page/hr/c_employees/input_employees';
$route['human-resources/employees/edit-employees/(:num)'] = 'page/hr/c_employees/edit_employees/$1';
$route['human-resources/upload_photo'] = 'page/hr/c_employees/upload_photo';

$route['human-resources/monitoring-attendance/with-range-date'] = 'page/hr/c_employees/with_range_date';

// ====== Database =====
$route['database/lecturers'] = 'page/database/c_database/lecturers';
$route['database/lecturer-details/(:any)'] = 'page/database/c_database/lecturersDetails/$1';
$route['database/loadpagelecturersDetails'] = 'page/database/c_database/loadpagelecturersDetails';

$route['database/sendMailResetPassword'] = 'page/database/c_database/sendMailResetPassword';

$route['database/students'] = 'page/database/c_database/students';
$route['database/loadPageStudents'] = 'page/database/c_database/loadPageStudents';
$route['database/showStudent'] = 'page/database/c_database/showStudent';
$route['database/employees'] = 'page/database/c_database/employees';
$route['database/employees/form_input_add'] = 'page/database/c_database/form_input_employees';
$route['database/employees/form_input_add/(:any)'] = 'page/database/c_database/form_input_employees/$1';
$route['database/employees/form_input_submit'] = 'page/database/c_database/form_input_submit_employees';
$route['database/employees/changestatus'] = 'page/database/c_database/changestatus';

$route['database/students/edit-students/(:any)/(:any)/(:any)'] = 'page/database/c_database/edit_students/$1/$2/$3';





$route['database/mentor-academic'] = 'page/database/c_database/mentor_academic';


// --- Presensi ---
$route['academic/attendance/input-attendace'] = 'page/academic/c_presensi';
$route['academic/loadPagePresensi'] = 'page/academic/c_presensi/loadPagePresensi';

$route['academic/attendance/monitoring-attendace-lecturer'] = 'page/academic/c_presensi/monitoring_lecturer';
$route['academic/attendance/monitoring-attendace-student'] = 'page/academic/c_presensi/monitoring_student';
$route['academic/attendance/monitoring-schedule-exchange'] = 'page/academic/c_presensi/monitoring_exchange';


// -- master student -- 
$route['academic/master/student'] = 'page/academic/c_m_student';
$route['academic/master/showStudent'] = 'page/academic/c_m_student/showStudent';
$route['academic/master/loadPageStudents'] = 'page/academic/c_m_student/loadPageStudents';
$route['academic/master/FormStudents'] = 'page/academic/c_m_student/FormStudents';
// $route['academic/master/form_input_student'] = 'page/academic/c_m_student/form_input_student';
$route['academic/master/edit-student'] = 'page/academic/c_m_student/edit_student';

// --- Admission ----
// --- Master ----

// test routes from db
require_once( BASEPATH .'database/DB.php' );
$db =& DB();
$query = $db->get('db_admission.cfg_sub_menu');
$result = $query->result();
foreach( $result as $row )
{
	$Slug = $row->Slug;
	$Slug = explode('/', $Slug);
	if (in_array('(:any)', $Slug)) {
	   $a = count($Slug) - 1;
	   $URI = '';
	   for ($i=0; $i < $a; $i++) { 
	   	$URI .= $Slug[$i].'/';
	   }
	   $route[ $URI.'(:any)' ] = $row->Controller;
	}
	elseif(in_array('(:num)', $Slug)) {
		$a = count($Slug) - 1;
		$URI = '';
		for ($i=0; $i < $a; $i++) { 
			$URI .= $Slug[$i].'/';
		}
		$route[ $URI.'(:num)' ] = $row->Controller;
	}
	else
	{
		$route[ $row->Slug ] = $row->Controller;
	}

}
// test routes from db

$route['admission/database/loadPageStudents'] = 'page/database/c_database/loadPageStudents_admission';

$route['admission/config/submit_upload_announcement'] = 'page/admission/c_master/submit_upload_announcement';

$route['admission/config/set-tgl-register-online'] = 'page/admission/c_master/page_set_tgl_register'; // db menu

$route['admission/master/data_cfg_deadline'] = 'page/admission/c_master/data_cfg_deadline';
$route['admission/master/modalform_set_tgl_register'] = 'page/admission/c_master/modalform_set_tgl_register';
$route['admission/master/modalform_set_tgl_register/save'] = 'page/admission/c_master/submit_cfg_deadline';

$route['admission/config/set-max-cicilan'] = 'page/admission/c_master/page_set_max_cicilan'; // db menu

$route['admission/master/modalform_set_max_cicilan'] = 'page/admission/c_master/modalform_set_max_cicilan';
$route['admission/master/modalform_set_max_cicilan/save'] = 'page/admission/c_master/submit_cfg_cicilan';
$route['admission/master/data_cfg_cicilan'] = 'page/admission/c_master/data_cfg_cicilan';
$route['admission/master-calon-mahasiswa/showAutoComplete'] = 'page/admission/c_master/load_data_autocomplete_calon_mahasiswa';

$route['admission/master/modalform_sekolah'] = 'page/admission/c_master/modalform_sekolah';
$route['admission/master/modalform_sekolah/save'] = 'page/admission/c_master/submit_sekolah';


$route['admission/config/sdaerah/master-sma'] = 'page/admission/c_master/sma'; // db menu
$route['admission/config/sdaerah/master-sma/(:any)'] = 'page/admission/c_master/sma/$1'; // db menu
$route['admission/config/sdaerah/integration'] = 'page/admission/c_master/sma_integration';

$route['admission/master-sma/table'] = 'page/admission/c_master/sma_table';

$route['admission/config/set-email'] = 'page/admission/c_master/config_set_email'; // db menu
$route['admission/master-config/testing_email'] = 'page/admission/c_master/testing_email';
$route['admission/master-config/save_email'] = 'page/admission/c_master/save_email';
$route['admission/config/total-account'] = 'page/admission/c_master/total_account'; // db menu
$route['admission/master-config/loadTableTotalAccount'] = 'page/admission/c_master/load_table_total_account';
$route['admission/master-config/modalform/(:any)'] = 'page/admission/c_master/modalform/$1';
$route['admission/master-config/submit_count_account'] = 'page/admission/c_master/submit_count_account';

$route['admission/config/email-to'] = 'page/admission/c_master/email_to'; // db menu

$route['admission/master-config/loadTableEmailTo'] = 'page/admission/c_master/load_table_email_to';
$route['admission/master-config/submit_email_to'] = 'page/admission/c_master/submit_email_to';
$route['admission/master-config/lama-pembayaran'] = 'page/admission/c_master/lama_pembayaran';
$route['admission/master-config/loadTableMaster/(:any)'] = 'page/admission/c_master/load_table_master/$1';
$route['admission/master-config/submit_lama_pembayaran'] = 'page/admission/c_master/submit_lama_pembayaran';
$route['admission/config/harga-formulir/online'] = 'page/admission/c_master/harga_formulir_online'; // db menu
$route['admission/master-config/submit_harga_formulir_online'] = 'page/admission/c_master/submit_harga_formulir_online'; // db menu
$route['admission/config/harga-formulir/offline'] = 'page/admission/c_master/harga_formulir_offline';
$route['admission/master-config/submit_harga_formulir_offline'] = 'page/admission/c_master/submit_harga_formulir_offline';
$route['admission/config/sdaerah/wilayah'] = 'page/admission/c_master/global_wilayah'; // dbmenu
$route['admission/master-config/loadTableMasterNoAction/(:any)'] = 'page/admission/c_master/loadTableMasterNoAction/$1';
$route['admission/master/jenis-tempat-tinggal'] = 'page/admission/c_master/jenis_tempat_tinggal'; // db menu
$route['admission/master-config/submit_jenis_tempat_tinggal'] = 'page/admission/c_master/submit_jenis_tempat_tinggal';
$route['admission/master/pendapatan'] = 'page/admission/c_master/pendapatan'; // db menu
$route['admission/master-config/submit_Pendapatan'] = 'page/admission/c_master/submit_pendapatan';
$route['admission/config/set-print-label'] = 'page/admission/c_master/set_print_label'; // db menu
$route['admission/master-config/testing_print_label_token'] = 'page/admission/c_master/testing_print_label_token';
$route['admission/master-config/save_set_print_label'] = 'page/admission/c_master/save_set_print_label';



$route['admission/master/agama'] = 'page/admission/c_master/agama'; // db menu

$route['admission/master-global/loadTableMasterAgama'] = 'page/admission/c_master/load_table_master_agama';
$route['admission/master/tipe-sekolah'] = 'page/admission/c_master/tipe_sekolah'; // db menu
$route['admission/master-global/loadTableMasterTipeSekolah'] = 'page/admission/c_master/load_table_tipe_sekolah';
$route['admission/master/document-checklist'] = 'page/admission/c_master/document_checklist'; // db menu
$route['admission/master-registration/submit_document_checklist'] = 'page/admission/c_master/submit_document_checklist';

$route['admission/config/number-formulir/online'] = 'page/admission/c_master/formulir_online'; // db menu

$route['admission/master-registration/loadDataFormulirOnline'] = 'page/admission/c_master/loadDataFormulirOnline';
$route['admission/master-registration/getJsonFormulirOnline'] = 'page/admission/c_master/get_json_formulir_online';
$route['admission/master-registration/GenerateFormulirOnline'] = 'page/admission/c_master/generate_formulir_online';

$route['admission/config/number-formulir/offline'] = 'page/admission/c_master/formulir_offline';

$route['admission/master-registration/loadDataFormulirOffline'] = 'page/admission/c_master/loadDataFormulirOffline';
$route['admission/master-registration/getJsonFormulirOffline'] = 'page/admission/c_master/get_json_formulir_offline';
$route['admission/master-registration/GenerateFormulirOffline'] = 'page/admission/c_master/generate_formulir_offline';
$route['admission/master/jacket-size'] = 'page/admission/c_master/jacket_size'; // db menu
$route['admission/master-register/submit_jacket_size'] = 'page/admission/c_master/submit_jacket_size';
$route['admission/master/jurusan-sekolah'] = 'page/admission/c_master/jurusan_sekolah'; // db menu
$route['admission/master-config/submit_jurusan_sekolah'] = 'page/admission/c_master/submit_jurusan_sekolah';
$route['admission/master/ujian-masuk-per-prody'] = 'page/admission/c_master/ujian_masuk_per_prody'; // db menu
$route['admission/master-registration/ujian-masuk-per-prody/modalform'] = 'page/admission/c_master/modalform_ujian_masuk_per_prody';
$route['admission/master-registration/ujian-masuk-per-prody/loadTable'] = 'page/admission/c_master/table_ujian_masuk_per_prody';
$route['admission/master-registration/ujian-masuk-per-prody/submit'] = 'page/admission/c_master/submit_ujian_masuk_per_prody';

$route['admission/config/virtual-account/page-create-va'] = 'page/admission/c_master/page_create_va'; // db menu

$route['admission/master-registration/generate_va'] = 'page/admission/c_master/generate_va';
$route['admission/master-registration/loadDataVA-available'] = 'page/admission/c_master/loadDataVA_available';
$route['admission/master/event'] = 'page/admission/c_master/event'; // db menu
$route['admission/master-registration/modalform_event'] = 'page/admission/c_master/modalform_event';
$route['admission/master-registration/event/table_event'] = 'page/admission/c_master/table_event';
$route['admission/master-registration/modalform_event/save'] = 'page/admission/c_master/modalform_event_save';
$route['admission/master/sumber-iklan'] = 'page/admission/c_master/sumber_iklan'; // db menu
$route['admission/master-register/submit_source_from_event'] = 'page/admission/c_master/submit_source_from_event';
$route['admission/config/virtual-account/page-recycle-va'] = 'page/admission/c_master/page_recycle_va'; // db menu
$route['admission/master-registration/loadDataVA-deleted/(:num)'] = 'page/admission/c_master/loadDataVA_deleted/$1';
$route['admission/master-registration/virtual-account/page-recycle-va/submit_recycle_va'] = 'page/admission/c_master/submit_recycle_va';
$route['admission/master/program-beasiswa/jalur-prestasi-akademik'] = 'page/admission/c_master/jalur_prestasi_akademik'; // db menu
$route['admission/master-registration/jpa/table_jpa'] = 'page/admission/c_master/table_jpa';
$route['admission/master-registration/modalform_jpa'] = 'page/admission/c_master/modalform_jpa';
$route['admission/master-registration/submit_jpa'] = 'page/admission/c_master/submit_jpa';
$route['admission/master/program-beasiswa/jalur-prestasi-akademik-umum'] = 'page/admission/c_master/jalur_prestasi_akademik_umum'; // db menu
$route['admission/master-registration/modalform_jpau'] = 'page/admission/c_master/modalform_jpau';
$route['admission/master-registration/jpau/table_jpau'] = 'page/admission/c_master/table_jpau';
$route['admission/master-registration/submit_jpau'] = 'page/admission/c_master/submit_jpau';
$route['admission/master/program-beasiswa/jalur-prestasi-bidang-or-seni'] = 'page/admission/c_master/jalur_prestasi_bidang_or_seni';
$route['admission/master-registration/jpok/table_jpok'] = 'page/admission/c_master/table_jpok';
$route['admission/master-registration/modalform_jpok'] = 'page/admission/c_master/modalform_jpok';
$route['admission/master-registration/submit_jpok'] = 'page/admission/c_master/submit_jpok';




$route['admission/master/sales-koordinator-wilayah'] = 'page/admission/c_master/sales_koordinator_wilayah_page'; // db menu
$route['admission/master-registration/modalform_sales_koordinator'] = 'page/admission/c_master/sales_koordinator_wilayah_modal_form';
$route['admission/master-registration/modalform_sales_koordinator/save'] = 'page/admission/c_master/modalform_sales_koordinator_save';
$route['admission/master-registration/sales_koordinator/pagination/(:num)'] = 'page/admission/c_master/sales_koordinator_pagination/$1';
$route['admission/master-registration/DataFormulirOffline/downloadPDFToken'] = 'page/admission/c_master/downloadPDFToken';
$route['admission/config/upload-pdf-per-pengumuman'] = 'page/admission/c_master/upload_pengumuman';
$route['admission/submit_set_tahun_ajaran'] = 'page/admission/c_master/submit_set_tahun_ajaran';
$route['admission/master-registration/reset_va'] = 'page/admission/c_master/reset_va';


$route['fileGet/(:any)'] = 'api/c_global/fileGet/$1';
$route['download/(:any)'] = 'api/c_global/download/$1';
$route['download_template/(:any)'] = 'api/c_global/download_template/$1';
$route['download_anypath'] = 'api/c_global/download_anypath';
$route['fileGetAny/(:any)'] = 'api/c_global/fileGetAny/$1';
$route['autocompleteAllUser'] = 'api/c_global/autocompleteAllUser';




$route['admission/master-registration/biaya-kuliah'] = 'page/admission/c_master/biaya_kuliah';
$route['admission/config/menu-previleges'] = 'page/admission/c_master/menu_previleges'; // db menu
$route['admission/master-config/modalform_previleges'] = 'page/admission/c_master/modal_form_previleges';
$route['admission/master-config/menu-previleges/get_menu'] = 'page/admission/c_master/get_menu';
$route['admission/master-config/menu-previleges/get_menu/save'] = 'page/admission/c_master/get_menu_save_menu';
$route['admission/master-config/menu-previleges/get_submenu/save'] = 'page/admission/c_master/get_submenu_save_menu';
$route['admission/master-config/menu-previleges/get_submenu/show'] = 'page/admission/c_master/get_submenu_show';
$route['admission/master-config/menu-previleges/get_submenu/update'] = 'page/admission/c_master/get_submenu_update';
$route['admission/master-config/menu-previleges/get_submenu/delete'] = 'page/admission/c_master/get_submenu_delete';
$route['admission/master-config/autocompleteuser'] = 'page/admission/c_master/autocompleteuser';
$route['admission/master-config/menu-previleges/get_submenu_by_menu'] = 'page/admission/c_master/get_submenu_by_menu';
$route['admission/master-config/menu-previleges/user/save'] = 'page/admission/c_master/save_user_previleges';
$route['admission/master-config/menu-previleges/get_previleges_user/show'] = 'page/admission/c_master/get_previleges_user_show';
$route['admission/master-config/menu-previleges/previleges_user/update'] = 'page/admission/c_master/previleges_user_update';
$route['admission/master-config/menu-previleges/previleges_user/delete'] = 'page/admission/c_master/previleges_user_delete';

// menu & group
$route['admission/master-config/menu-previleges/getGroupPrevileges'] = 'page/admission/c_master/getGroupPrevileges';
$route['admission/master-config/menu-previleges/groupuser/save'] = 'page/admission/c_master/groupuser_save';
$route['admission/master-config/menu-previleges/get_previleges_group/show'] = 'page/admission/c_master/get_previleges_group_show';
$route['admission/master-config/menu-previleges/modalform_group_user'] = 'page/admission/c_master/modalform_group_user';
$route['admission/master-config/menu-previleges/save_group_user'] = 'page/admission/c_master/save_group_user';
$route['admission/master-config/menu-previleges/update_group_user'] = 'page/admission/c_master/update_group_user';
$route['admission/master-config/menu-previleges/config/groupuser/delete'] = 'page/admission/c_master/delete_group_user';
$route['admission/edit_auth_user'] = 'page/admission/c_master/edit_auth_user';
$route['admission/add_auth_user'] = 'page/admission/c_master/add_auth_user';
$route['admission/delete_authUser'] = 'page/admission/c_master/delete_authUser';
$route['admission/config/getAuthDataTables'] = 'page/admission/c_master/getAuthDataTables';





$route['admission/dashboard'] = 'page/admission/c_admission/dashboard';
$route['readNotificationDivision'] = 'dashboard/c_dashboard/readNotificationDivision';


// proses
$route['admission/proses-calon-mahasiswa/dokumen/dokumen-upload'] = 'page/admission/c_admission/verifikasi_dokumen_calon_mahasiswa'; // db menu
$route['admission/proses-calon-mahasiswa/verifikasi-dokument/register_document_table/pagination/(:num)'] = 'page/admission/c_admission/pagination_calon_mahasiswa/$1';
$route['admission/proses-calon-mahasiswa/verifikasi-dokument/proses_document'] = 'page/admission/c_admission/proses_document';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-jadwal-ujian'] = 'page/admission/c_admission/set_jadwal_ujian';
$route['admission/proses-calon-mahasiswa/set-jadwal-ujian/load_table'] = 'page/admission/c_admission/set_jadwal_ujian_load_table';
$route['admission/proses-calon-mahasiswa/set-jadwal-ujian/load_table_getjsonApi'] = 'page/admission/c_admission/set_jadwal_ujian_load_table_getJsonApi';
$route['admission/proses-calon-mahasiswa/set-jadwal-ujian/save'] = 'page/admission/c_admission/set_jadwal_ujian_save';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/daftar-jadwal-ujian'] = 'page/admission/c_admission/daftar_jadwal_ujian';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/daftar-jadwal-ujian/load-data-now'] = 'page/admission/c_admission/daftar_jadwal_ujian_load_data_now';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/daftar-jadwal-ujian/pagination/(:num)'] = 'page/admission/c_admission/daftar_jadwal_ujian_load_data_paging/$1';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-nilai-ujian'] = 'page/admission/c_admission/set_nilai_ujian';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-nilai-ujian/pagination/(:num)'] = 'page/admission/c_admission/set_nilai_ujian_load_data_paging/$1';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-nilai-ujian/save'] = 'page/admission/c_admission/set_nilai_ujian_save';
$route['admission/proses-calon-mahasiswa/jadwal-ujian/set-ujian'] = 'page/admission/c_admission/set_ujian'; // db menu
$route['admission/proses-calon-mahasiswa/loadData_calon_mahasiswa/(:num)'] = 'page/admission/c_admission/loadData_calon_mahasiswa/$1';
$route['admission/proses-calon-mahasiswa/submit_ikut_ujian'] = 'page/admission/c_admission/submit_ikut_ujian';
$route['admission/proses-calon-mahasiswa/dokumen/input-nilai-rapor'] = 'page/admission/c_admission/input_nilai_rapor'; // db menu

$route['admission/proses-calon-mahasiswa/set-nilai-rapor/pagination/(:num)'] = 'page/admission/c_admission/set_nilai_rapor_load_data_paging/$1';
$route['admission/proses-calon-mahasiswa/set-nilai-rapor/save'] = 'page/admission/c_admission/set_nilai_rapor_save';
$route['admission/proses-calon-mahasiswa/dokumen/cancel-nilai-rapor'] = 'page/admission/c_admission/cancel_nilai_lapor'; // db menu
$route['admission/proses-calon-mahasiswa/loaddata_nilai_calon_mahasiswa/(:num)'] = 'page/admission/c_admission/loaddata_nilai_calon_mahasiswa/$1';
$route['admission/proses-calon-mahasiswa/submit_cancel_nilai_rapor'] = 'page/admission/c_admission/submit_cancel_nilai_rapor';
$route['admission/proses-calon-mahasiswa/set_tuition_fee'] = 'page/admission/c_admission/set_tuition_fee'; // db menu
$route['admission/proses-calon-mahasiswa/set_tuition_fee/input/(:num)'] = 'page/admission/c_admission/set_tuition_fee_input/$1';
$route['admission/proses-calon-mahasiswa/set_tuition_fee/save'] = 'page/admission/c_admission/set_tuition_fee_save';
$route['admission/proses-calon-mahasiswa/set_tuition_fee/delete/(:num)'] = 'page/admission/c_admission/set_tuition_fee_delete/$1';
$route['admission/proses-calon-mahasiswa/set_tuition_fee/delete_data'] = 'page/admission/c_admission/set_tuition_fee_delete_data';
$route['admission/proses-calon-mahasiswa/set_tuition_fee/approved/(:num)'] = 'page/admission/c_admission/set_tuition_fee_approved/$1';
$route['admission/proses-calon-mahasiswa/cicilan'] = 'page/admission/c_admission/cicilan'; // db menu
$route['admission/proses-calon-mahasiswa/cicilan_data/(:num)'] = 'page/admission/c_admission/cicilan_data/$1';
$route['admission/proses-calon-mahasiswa/submit_edit_deadline_cicilan'] = 'page/admission/c_admission/submit_edit_deadline_cicilan';
$route['admission/proses-calon-mahasiswa/checkdata-calon-mahasiswa'] = 'page/admission/c_admission/page_data_calon_mahasiswa'; // db menu
$route['admission/proses-calon-mahasiswa/data-calon-mhs/(:num)'] = 'page/admission/c_admission/data_calon_mahasiswa/$1';
$route['admission/detailPayment'] = 'page/admission/c_admission/detailPayment';

// --- update admisi setelah request 20180731
$route['admission/proses-calon-mahasiswa/set_input_tuition_fee_submit'] = 'page/admission/c_admission/set_input_tuition_fee_submit';
$route['admission/proses-calon-mahasiswa/getDataPersonal_Candidate'] = 'page/admission/c_admission/getDataPersonal_Candidate';
$route['admission/proses-calon-mahasiswa/getDataPersonal_Candidate_to_be_mhs'] = 'page/admission/c_admission/getDataPersonal_Candidate_to_be_mhs';
$route['admission/proses-calon-mahasiswa/generate_to_be_mhs'] = 'page/admission/c_admission/generate_to_be_mhs';


$route['admission/distribusi-formulir/formulir-offline'] = 'page/admission/c_admission/distribusi_formulir_offline'; // db menu
$route['admission/distribusi-formulir/formulir-offline/pagination/(:num)'] = 'page/admission/c_admission/pagination_formulir_offline/$1';
$route['admission/distribusi-formulir/formulir-offline/submit_sellout'] = 'page/admission/c_admission/submit_sellout_formulir_offline/$1';

$route['admission/distribusi-formulir/formulir-online'] = 'page/admission/c_admission/distribusi_formulir_online'; // db menu
$route['admission/distribusi-formulir/formulir-online/pagination/(:num)'] = 'page/admission/c_admission/pagination_formulir_online/$1';
$route['admission/distribusi-formulir/formulir-offline/save'] = 'page/admission/c_admission/formulir_offline_sale_save';
$route['admission/distribusi-formulir/formulir-offline/selectPIC'] = 'page/admission/c_admission/formulir_offline_salect_PIC';
$route['admission/distribusi-formulir/offline/LoadListPenjualan'] = 'page/admission/c_admission/LoadListPenjualanoffline';
$route['admission/distribusi-formulir/offline/LoadListPenjualan/serverSide'] = 'page/admission/c_admission/LoadListPenjualanoffline_serverSide';
$route['admission/distribusi-formulir/offline/LoadInputPenjualan'] = 'page/admission/c_admission/LoadInputPenjualanoffline';
$route['admission/distribusi-formulir/offline/LoadImportInputPenjualan'] = 'page/admission/c_admission/LoadImportInputPenjualan';
$route['admission/distribusi-formulir/offline/submit_import_excel_penjualan_formulir_offline'] = 'page/admission/c_admission/submit_import_excel_penjualan_formulir_offline';
$route['admission/distribusi-formulir/offline/submit_import_excel_kwitansi_penjualan_formulir_offline'] = 'page/admission/c_admission/submit_import_excel_kwitansi_penjualan_formulir_offline';


$route['admission/mastercalonmahasiswa/generate-nim'] = 'page/admission/c_admission/generatenim';
$route['admission/mastercalonmahasiswa/submit_import_excel_File_generate_nim'] = 'page/admission/c_admission/submit_import_excel_File_generate_nim';
$route['admission/export_kwitansi_formuliroffline'] = 'c_save_to_pdf/export_kwitansi_formuliroffline';
$route['admission/export_PenjualanFormulirData'] = 'c_save_to_excel/export_PenjualanFormulirData';
$route['admission/export_PenjualanFormulirFinance'] = 'c_save_to_excel/export_PenjualanFormulirFinance';
$route['admission/export_PengembalianFormulirData'] = 'c_save_to_excel/export_PengembalianFormulirData';





// ---Finance----

$route['finance/dashboard_getoutstanding_today'] =  'dashboard/c_dashboard/dashboard_getoutstanding_today';
$route['finance/dashboard'] =  'dashboard/c_dashboard/finance_dashboard';
$route['finance/summary_payment'] =  'dashboard/c_dashboard/summary_payment';
$route['finance/summary_payment_admission'] =  'dashboard/c_dashboard/summary_payment_admission';
$route['finance/summary_payment_formulir'] =  'dashboard/c_dashboard/summary_payment_formulir';


$route['finance/master/tagihan-mhs'] =  'page/finance/c_tuition_fee/tuition_fee';
$route['finance/master/modal-tagihan-mhs'] =  'page/finance/c_tuition_fee/modal_tagihan_mhs';
$route['finance/master/modal-tagihan-mhs-submit'] =  'page/finance/c_tuition_fee/modal_tagihan_mhs_submit';
$route['finance/master/edited-tagihan-mhs-submit'] =  'page/finance/c_tuition_fee/edited_tagihan_mhs_submit';
$route['finance/master/deleted-tagihan-mhs-submit'] =  'page/finance/c_tuition_fee/deleted_tagihan_mhs_submit';
$route['finance/master/mahasiswa'] =  'page/finance/c_finance/mahasiswa';
$route['finance/master/import_price_list_mhs'] =  'page/finance/c_finance/import_price_list_mhs';
$route['finance/master/import_beasiswa_mahasiswa'] =  'page/finance/c_finance/import_beasiswa_mahasiswa';
$route['finance/master/mahasiswa_list/(:num)'] =  'page/finance/c_finance/mahasiswa_list/$1';
$route['finance/master/edited-bea-bpp'] =  'page/finance/c_finance/edited_bea_bpp';
$route['finance/master/edited-bea-credit'] =  'page/finance/c_finance/edited_bea_credit';
$route['finance/master/edited-pay-cond'] =  'page/finance/c_finance/edited_pay_cond';
$route['finance/master/discount'] =  'page/finance/c_finance/page_master_discount';
$route['finance/master/load_discount'] =  'page/finance/c_finance/load_discount';
$route['finance/master/modalform_discount'] =  'page/finance/c_finance/modalform_discount';
$route['finance/master/sbmt_discount'] =  'page/finance/c_finance/sbmt_discount';



$route['finance/admission/penerimaan-pembayaran/formulir-registration/online'] =  'page/finance/c_finance/formulir_registration_online_page';
$route['finance/confirmed-verifikasi-pembayaran-registration_online'] =  'page/finance/c_finance/confirmed_verfikasi_pembayaran_registration_online';
$route['finance/admission/penerimaan-pembayaran/formulir-registration/offline'] =  'page/finance/c_finance/formulir_registration_offline_page';
$route['finance/admission/distribusi-formulir/offline/LoadListPenjualan/serverSide'] =  'page/finance/c_finance/formulir_registration_offline_serverSide';


$route['finance/admission/approved/nilai-rapor'] =  'page/finance/c_finance/nilai_rapor_page';
$route['finance/approved/loaddata_nilai_calon_mahasiswa_verified/(:num)'] =  'page/finance/c_finance/loaddata_nilai_calon_mahasiswa_verified/$1';
$route['finance/approved/submit_approved_nilai_rapor'] =  'page/finance/c_finance/submit_approved_nilai_rapor';
$route['finance/admission/approved/tuition-fee'] =  'page/finance/c_finance/tuition_fee';
$route['finance/approved/tuition-fee/approve/(:num)'] =  'page/finance/c_finance/tuition_fee_approve/$1';
$route['finance/approved/tuition-fee/approve_save'] =  'page/finance/c_finance/approve_save';
$route['finance/approved/tuition-fee/approved/(:num)'] =  'page/finance/c_finance/tuition_fee_approved/$1';
$route['finance/admission/set_tuition_fee/delete_data'] =  'page/finance/c_finance/set_tuition_fee_delete_data';
$route['finance/bayar_manual_mahasiswa_formulironline'] =  'page/finance/c_finance/bayar_manual_mahasiswa_formulironline';
$route['finance/admission/penerimaan-pembayaran/biaya'] =  'page/finance/c_finance/penerimaan_pembayaran_biaya';
$route['finance/getPayment_admission'] =  'page/finance/c_finance/getPayment_admission';
$route['finance/getPayment_admission_edit_cicilan'] =  'page/finance/c_finance/getPayment_admission_edit_cicilan';

$route['finance/getPayment_detail_admission'] =  'page/finance/c_finance/getPayment_detail_admission';
$route['finance/getPayment_detail_admission2'] =  'page/finance/c_finance/getPayment_detail_admission2';
$route['finance/admission/approved/edit'] =  'page/finance/c_finance/approved_edit';
$route['finance/admission/approved/edit_submit'] =  'page/finance/c_finance/approved_edit_submit';


$route['finance/tagihan-mhs/set-tagihan-mhs'] =  'page/finance/c_finance/page_set_tagihan_mhs';
$route['finance/get_tagihan_mhs/(:num)'] =  'page/finance/c_finance/get_tagihan_mhs/$1';
$route['finance/submit_tagihan_mhs'] =  'page/finance/c_finance/submit_tagihan_mhs';
$route['finance/tagihan-mhs/cek-tagihan-mhs/(:num)'] =  'page/finance/c_finance/page_cek_tagihan_mhs/$1';
$route['finance/tagihan-mhs/cek-tagihan-mhs'] =  'page/finance/c_finance/page_cek_tagihan_mhs';
$route['finance/get_created_tagihan_mhs/(:num)'] =  'page/finance/c_finance/get_created_tagihan_mhs/$1';
$route['finance/get_created_tagihan_mhs_not_approved/(:num)'] =  'page/finance/c_finance/get_created_tagihan_mhs_not_approved/$1';
$route['finance/approved_created_tagihan_mhs'] =  'page/finance/c_finance/approved_created_tagihan_mhs';
$route['finance/unapproved_created_tagihan_mhs'] =  'page/finance/c_finance/unapproved_created_tagihan_mhs';
$route['finance/tagihan-mhs/cancel-tagihan-mhs'] =  'page/finance/c_finance/cancel_tagihan_mhs';
$route['finance/cancel_created_tagihan_mhs'] =  'page/finance/c_finance/cancel_created_tagihan_mhs';
$route['finance/tagihan-mhs/set-cicilan-tagihan-mhs'] =  'page/finance/c_finance/set_cicilan_tagihan_mhs';
$route['finance/tagihan-mhs/set-cicilan-tagihan-mhs/submit'] =  'page/finance/c_finance/set_cicilan_tagihan_mhs_submit';
$route['finance/tagihan-mhs/edit-cicilan-tagihan-mhs'] =  'page/finance/c_finance/edit_cicilan_tagihan_mhs';
$route['finance/tagihan-mhs/set-edit-cicilan-tagihan-mhs/submit'] =  'page/finance/c_finance/edit_cicilan_tagihan_mhs_submit';
$route['finance/tagihan-mhs/set-delete-cicilan-tagihan-mhs/submit'] =  'page/finance/c_finance/delete_cicilan_tagihan_mhs_submit';
$route['finance/tagihan-mhs/penerimaan-tagihan-mhs'] =  'page/finance/c_finance/penerimaan_tagihan_mhs';
$route['finance/get_pembayaran_mhs/(:num)'] =  'page/finance/c_finance/get_pembayaran_mhs/$1';
//$route['finance/export_excel'] =  'page/finance/c_finance/export_excel';
$route['finance/export_excel'] =  'C_save_to_excel/export_excel_payment_received';
$route['finance/export_excel_report'] =  'C_save_to_excel/export_excel_report_finance';


$route['finance/check-va'] =  'page/finance/c_finance/check_va';
$route['finance/check-va-cari'] =  'page/finance/c_finance/check_va_cari';

$route['finance/tagihan-mhs/submit_import_price_list_mhs'] =  'page/finance/c_finance/submit_import_price_list_mhs';
$route['finance/tagihan-mhs/list-telat-bayar'] =  'page/finance/c_finance/list_telat_bayar';
$route['finance/get_list_telat_bayar/(:num)'] =  'page/finance/c_finance/get_list_telat_bayar/$1';
$route['finance/edit_telat_bayar/(:any)'] =  'page/finance/c_finance/edit_telat_bayar/$1';
$route['finance/tagihan-mhs/import_pembayaran_manual'] =  'page/finance/c_finance/import_pembayaran_manual';
$route['finance/tagihan-mhs/submit_import_pembayaran_manual'] =  'page/finance/c_finance/submit_import_pembayaran_manual';
$route['finance/bayar_manual_mahasiswa'] =  'page/finance/c_finance/bayar_manual_mahasiswa';
$route['finance/bayar_manual_mahasiswa_admission'] =  'page/finance/c_finance/bayar_manual_mahasiswa_admission';



$route['finance/tagihan-mhs/import_pembayaran_lain'] =  'page/finance/c_finance/import_pembayaran_lain';
$route['finance/tagihan-mhs/submit_import_pembayaran_lain'] =  'page/finance/c_finance/submit_import_pembayaran_lain';
$route['finance/tagihan-mhs/report'] =  'page/finance/c_finance/report';
$route['finance/get_reporting/(:num)'] =  'page/finance/c_finance/get_reporting/$1';




$route['finance/tagihan-mhs/submit_import_beasiswa_mahasiswa'] =  'page/finance/c_finance/submit_import_beasiswa_mahasiswa';
$route['finance/download-log-va'] =  'page/finance/c_finance/download_log_va';
$route['finance/listfile_va'] =  'page/finance/c_finance/listfile_va';


// -- config --
$route['finance/config/policysys'] =  'page/finance/c_config/policysys';
$route['finance/config/policy_sys_json_data'] =  'page/finance/c_config/policy_sys_json_data';
$route['finance/config/policysys/modalform'] =  'page/finance/c_config/policy_sys_modalform';
$route['finance/config/policysys/submit'] =  'page/finance/c_config/policy_sys_submit';


// --- Student Life ----
$route['student-life/diploma-supplement'] =  'page/student-life/c_studentlife/diploma_supplement';
$route['student-life/diploma-supplement/list-student'] =  'page/student-life/c_studentlife/diploma_supplement';


// --- LPMI ----
$route['lpmi/lecturer-evaluation/list-lecturer'] =  'page/lpmi/c_lpmi/edom_list_lecturer';
$route['lpmi/lecturer-evaluation/list-question'] =  'page/lpmi/c_lpmi/edom_list_question';
$route['lpmi/lecturer-evaluation/crud-question/(:any)/(:num)'] =  'page/lpmi/c_lpmi/crudQuestion/$1/$2';


// ---global---
$route['loadDataRegistrationBelumBayar'] =  'api/C_global/loadDataRegistrationBelumBayar';
$route['loadDataRegistrationTelahBayar'] =  'api/C_global/load_data_registration_telah_bayar';
$route['loadDataRegistrationFormulirOffline'] =  'api/C_global/load_data_registration_formulir_offline';
$route['get_detail_cicilan_fee_admisi'] =  'api/C_global/get_detail_cicilan_fee_admisi';


// ---- Save to PDF ---
$route['save2pdf/schedule-pdf'] =  'c_save_to_pdf/schedulePDF';
$route['save2pdf/monitoringAttdLecturer'] =  'c_save_to_pdf/monitoringAttdLecturer';
$route['save2pdf/scheduleExchange'] =  'c_save_to_pdf/scheduleExchange';
$route['save2pdf/monitoringStudent'] =  'c_save_to_pdf/monitoringStudent';
$route['save2pdf/monitoringAttendanceByRangeDate'] =  'c_save_to_pdf/monitoringAttendanceByRangeDate';

$route['save2pdf/filterDocument'] =  'c_save_to_pdf/filterDocument';
$route['save2pdf/recapExamSchedule'] =  'c_save_to_pdf/recapExamSchedule';

$route['save2pdf/listStudentsFromCourse'] =  'c_save_to_pdf/listStudentsFromCourse';

$route['save2pdf/exam-layout/(:num)'] =  'c_save_to_pdf/exam_layout/$1';
$route['save2pdf/draft_questions_answer_sheet'] =  'c_save_to_pdf/draft_questions_answer_sheet';
$route['save2pdf/draft-questions'] =  'c_save_to_pdf/draft_questions';
$route['save2pdf/answer-sheet'] =  'c_save_to_pdf/answer_sheet';
$route['save2pdf/news-event'] =  'c_save_to_pdf/news_event';
$route['save2pdf/attendance-list'] =  'c_save_to_pdf/attendance_list';
$route['save2pdf/transcript'] =  'c_save_to_pdf/transcript';
$route['save2pdf/temp_transcript'] =  'c_save_to_pdf/temp_transcript';
$route['save2pdf/ijazah'] =  'c_save_to_pdf/ijazah';
$route['save2pdf/diploma_supplement'] =  'c_save_to_pdf/diploma_supplement';

$route['save2pdf/report-uts'] =  'c_save_to_pdf/report_uts';

$route['save2pdf/getpdfkwitansi/(:any)'] =  'c_save_to_pdf/getpdfkwitansi/$1';

// ---- Save to EXCEL
$route['save2excel/test'] =  'c_save_to_excel/test2';
$route['save2excel/monitoring_score'] =  'c_save_to_excel/monitoring_score';
$route['save2excel/cumulative-recap'] =  'c_save_to_excel/cumulative_recap';
$route['save2excel/student-recap'] =  'c_save_to_excel/student_recap';


// ====== API ======
$route['api/__getKurikulumByYear'] = 'api/c_api/getKurikulumByYear';
$route['api/__getBaseProdi'] = 'api/c_api/getProdi';
$route['api/__getBaseProdiSelectOption'] = 'api/c_api/getProdiSelectOption';
$route['api/__getBaseProdiSelectOptionAll'] = 'api/c_api/getProdiSelectOptionAll';
$route['api/__geteducationLevel'] = 'api/c_api/geteducationLevel';

$route['api/__crudConfig'] = 'api/c_api/crudConfig';


$route['api/__getMKByID'] = 'api/c_api/getMKByID';
$route['api/__getSemesterActive'] = 'api/c_api/getSemesterActive';
$route['api/__getSemester'] = 'api/c_api/getSemester';
$route['api/__getLecturer'] = 'api/c_api/getLecturer';
$route['api/__getStudents'] = 'api/c_api/getStudents';
$route['api/__getStudentsAdmission'] = 'api/c_api/getStudentsAdmission';


$route['api/__getAllMK'] = 'api/c_api/getAllMK';

$route['api/__getEmployees'] = 'api/c_api/getEmployees';
$route['api/employees/searchnip/(:any)'] = 'api/c_api/searchnip_employees/$1';

$route['api/__getEmployeesHR'] = 'api/c_api/getEmployeesHR';

$route['api/__setLecturersAvailability'] = 'api/c_api/setLecturersAvailability';
$route['api/__setLecturersAvailabilityDetail/(:any)'] = 'api/c_api/setLecturersAvailabilityDetail/$1';

$route['api/__changeTahunAkademik'] = 'api/c_api/changeTahunAkademik';

$route['api/__insertKurikulum'] = 'api/c_api/insertKurikulum';
$route['api/__getKurikulumSelectOption'] = 'api/c_api/getKurikulumSelectOption';
$route['api/__getKurikulumSelectOptionASC'] = 'api/c_api/getKurikulumSelectOptionASC';


$route['api/__getDosenSelectOption'] = 'api/c_api/getDosenSelectOption';
$route['api/__crudYearAcademic'] = 'api/c_api/crudYearAcademic';

$route['api/__crudKurikulum'] = 'api/c_api/crudKurikulum';
$route['api/__crudDetailMK'] = 'api/c_api/crudDetailMK';

$route['api/__getdetailKurikulum'] = 'api/c_api/getdetailKurikulum';
$route['api/__genrateMKCode'] = 'api/c_api/genrateMKCode';
$route['api/__cekMKCode'] = 'api/c_api/cekMKCode';

$route['api/__crudMataKuliah'] = 'api/c_api/crudMataKuliah';

$route['api/__crudTahunAkademik'] = 'api/c_api/crudTahunAkademik';
$route['api/__crudStatusStudents'] = 'api/c_api/crudStatusStudents';

$route['api/__crudDataDetailTahunAkademik'] = 'api/c_api/crudDataDetailTahunAkademik';

$route['api/__getAcademicYearOnPublish'] = 'api/c_api/getAcademicYearOnPublish';
$route['api/__getTimePerCredits'] = 'api/c_api/getTimePerCredits';

$route['api/__crudSchedule'] = 'api/c_api/crudSchedule';
$route['api/__getSchedulePerDay'] = 'api/c_api/getSchedulePerDay';
$route['api/__getSchedulePerSemester'] = 'api/c_api/getSchedulePerSemester';
$route['api/__getScheduleExam'] = 'api/c_api/getScheduleExam';
$route['api/__getScheduleExamWaitingApproval'] = 'api/c_api/getScheduleExamWaitingApproval';
$route['api/__getScheduleExamLecturer'] = 'api/c_api/getScheduleExamLecturer';

// === Timetables ====

$route['api/__getTimetables'] = 'api/c_api/getTimetables';


// === Penutup Timetables ====

$route['api/__getListCourseInScore'] = 'api/c_api/getListCourseInScore';
$route['api/__getMonScoreStd'] = 'api/c_api/getMonScoreStd';

$route['api/__crudProgramCampus'] = 'api/c_api/crudProgramCampus';
$route['api/__crudSemester'] = 'api/c_api/crudSemester';

$route['api/__getAllStudents'] = 'api/c_api/getAllStudents';

$route['api/__crudeStudent'] = 'api/c_api/crudeStudent';
$route['api/__getClassGroup'] = 'api/c_api/getClassGroup';
$route['api/__getClassGroupParalel'] = 'api/c_api/getClassGroupParalel';

$route['api/__crudClassroom'] = 'api/c_api/crudClassroom';
$route['api/__crudGrade'] = 'api/c_api/crudGrade';
$route['api/__crudRangeCredits'] = 'api/c_api/crudRangeCredits';
//$route['api/__crudStdSemester'] = 'api/c_api/crudStdSemester';
$route['api/__crudTimePerCredit'] = 'api/c_api/crudTimePerCredit';
$route['api/__checkSchedule'] = 'api/c_api/checkSchedule';

$route['api/__crudCourseOfferings'] = 'api/c_api/crudCourseOfferings';
$route['api/__crudLecturer'] = 'api/c_api/crudLecturer';
$route['api/__crudStudyPlanning'] = 'api/c_api/crudStudyPlanning';
$route['api/__getClassGroupAutoComplete/(:num)'] = 'api/c_api/getClassGroupAutoComplete/$1';
$route['api/__getScheduleIDByClassGroup/(:num)/(:any)'] = 'api/c_api/getScheduleIDByClassGroup/$1/$2';
$route['api/__crudPartime'] = 'api/c_api/crudPartime';
$route['api/__crudStudent'] = 'api/c_api/crudStudent';

$route['api/__filterStudents'] = 'api/c_api/filterStudents';
$route['api/__getFormulirOfflineAvailable/(:any)'] = 'api/c_api/getFormulirOfflineAvailable/$1';
$route['api/__getAutoCompleteSchool'] = 'api/c_api/AutoCompleteSchool';
$route['api/__getSumberIklan'] = 'api/c_api/getSumberIklan';
$route['api/__getPriceFormulirOffline'] = 'api/c_api/getPriceFormulirOffline';
$route['api/__getEvent'] = 'api/c_api/getEvent';
$route['api/__getDocument'] = 'api/c_api/getDocument';
$route['api/__getDocumentAdmisiMHS'] = 'api/c_api/getDocumentAdmisiMHS';


// get data SMA dan SMK per Wilayah
$route['api/__insertWilayahURLJson'] = 'api/c_api/insertWilayahURLJson';
$route['api/__insertSchoolURLJson'] = 'api/c_api/insertSchoolURLJson';
$route['api/__getWilayahURLJson'] = 'api/c_api/getWilayahURLJson';
$route['api/__getSMAWilayah'] = 'api/c_api/getSMAWilayah';

// get data untuk finance
$route['api/__getDataRegisterBelumBayar'] = 'api/c_api/getDataRegisterBelumBayar';
$route['api/__getDataRegisterTelahBayar'] = 'api/c_api/getDataRegisterTelahBayar';
$route['api/__cek_deadlineBPPSKS'] = 'api/c_api/cek_deadlineBPPSKS';
$route['api/__cek_deadline_paymentNPM'] = 'api/c_api/cek_deadline_paymentNPM';


$route['api/__crudTuitionFee'] = 'api/c_api/crudTuitionFee';
$route['api/__getEmployees/(:any)/(:any)'] = 'api/c_api/getEmployeesBy/$1/$2';

$route['api/__crudJadwalUjian'] = 'api/c_api/crudJadwalUjian';
$route['api/__crudEmployees'] = 'api/c_api/crudEmployees';
$route['api/__crudScore'] = 'api/c_api/crudScore';
$route['api/__crudAttendance'] = 'api/c_api/crudAttendance';
$route['api/__crudScheduleExchange'] = 'api/c_api/crudScheduleExchange';
$route['api/__crudLimitCredit'] = 'api/c_api/crudLimitCredit';

$route['api/__crudLecturerEvaluation'] = 'api/c_api/crudLecturerEvaluation';
$route['api/__getLecturerEvaluation'] = 'api/c_api/getLecturerEvaluation';

$route['api/database/__getListStudent'] = 'api/c_api/getListStudent';
$route['api/database/upload_photo_student'] = 'page/academic/c_akademik/upload_photo_student';

$route['api/database/__getListEmployees'] = 'api/c_api/getListEmployees';


$route['rest/__checkDateKRS'] = 'api/c_rest/checkDateKRS';
$route['rest/__getDetailKRS'] = 'api/c_rest/getDetailKRS';

$route['rest/__geTimetable'] = 'api/c_rest/geTimetable';
$route['rest/__getExamSchedule'] = 'api/c_rest/getExamSchedule';
$route['rest/__getKSM'] = 'api/c_rest/getKSM';
$route['rest/__getExamScheduleForStudent'] = 'api/c_rest/getExamScheduleForStudent';
$route['rest/__cek_deadline_paymentNPM'] = 'api/c_rest/cek_deadline_paymentNPM';


$route['api/__getProvinsi'] = 'api/c_api/getProvinsi';
$route['api/__getRegionByProv'] = 'api/c_api/getRegionByProv';
$route['api/__getDistrictByRegion'] = 'api/c_api/getDistrictByRegion';
$route['api/__getTypeSekolah'] = 'api/c_api/getTypeSekolah';
$route['api/__getNotification'] = 'api/c_api/getNotification';
$route['api/__getBasePaymentTypeSelectOption'] = 'api/c_api/getBasePaymentTypeSelectOption';
$route['api/__getBaseDiscountSelectOption'] = 'api/c_api/getBaseDiscountSelectOption';


$route['api/__getSMAWilayahApproval'] = 'api/c_api/getSMAWilayahApproval';
$route['api/__getNotification_divisi'] = 'api/c_api/getNotification_divisi';
$route['api/__getAgama'] = 'api/c_api/getAgama';
$route['api/__getDivision'] = 'api/c_api/getDivision';
$route['api/__getPosition'] = 'api/c_api/getPosition';
$route['api/__getStatusEmployee'] = 'api/c_api/getStatusEmployee';

$route['api/__crudKRSOnline'] = 'api/c_api/crudKRSOnline';

$route['api/__crudCombinedClass'] = 'api/c_api/crudCombinedClass';
$route['api/__getSimpleSearch'] = 'api/c_api/getSimpleSearch';
$route['api/__getSimpleSearchStudents'] = 'api/c_api/getSimpleSearchStudents';


$route['api/__getAllDepartementPU'] = 'api/c_api/getAllDepartementPU';

//v_reservation
$route['api/__m_equipment_additional'] = 'api/c_api/m_equipment_additional';
$route['api/get_time_opt_reservation'] = 'api/c_api/get_time_opt_reservation';
$route['api/__m_additional_personel'] = 'api/c_api/m_additional_personel';
$route['api/__room_equipment'] = 'api/c_api/room_equipment';
$route['api/__checkBentrokScheduleAPI'] = 'api/c_api/checkBentrokScheduleAPI';
$route['api/__crudClassroomVreservation'] = 'api/c_api/crudClassroomVreservation';

$route['api/__crudTranscript'] = 'api/c_api/crudTranscript';
$route['api/__getTranscript'] = 'api/c_api/getTranscript';

$route['api/__crudFinalProject'] = 'api/c_api/crudFinalProject';
$route['api/__getFinalProject'] = 'api/c_api/getFinalProject';

$route['api/__crudInvigilator'] = 'api/c_api/crudInvigilator';


$route['api/__crudConfigSKPI'] = 'api/c_api/crudConfigSKPI';


$route['__resetPasswordUser'] = 'c_login/resetPasswordUser';
// for inject //

//Venue Reservation // 
$route['venue_reservation'] = 'page/vreservation/c_global';
$query = $db->get('db_reservation.cfg_sub_menu');
$result = $query->result();
foreach( $result as $row )
{
	$Slug = $row->Slug;
	$Slug = explode('/', $Slug);
	if (in_array('(:any)', $Slug)) {
	   $a = count($Slug) - 1;
	   $URI = '';
	   for ($i=0; $i < $a; $i++) { 
	   	$URI .= $Slug[$i].'/';
	   }
	   $route[ $URI.'(:any)' ] = $row->Controller;
	}
	elseif(in_array('(:num)', $Slug)) {
		$a = count($Slug) - 1;
		$URI = '';
		for ($i=0; $i < $a; $i++) { 
			$URI .= $Slug[$i].'/';
		}
		$route[ $URI.'(:num)' ] = $row->Controller;
	}
	else
	{
		$route[ $row->Slug ] = $row->Controller;
	}

}

$route['venue_reservation'] = 'page/vreservation/c_global';
$route['vreservation/getroom'] = 'page/vreservation/c_global/getroom';
$route['vreservation/getschedule'] = 'page/vreservation/c_global/getschedule';
$route['vreservation/getschedule/(:any)'] = 'page/vreservation/c_global/getschedule/$1';
$route['vreservation/modal_form'] = 'page/vreservation/c_global/modal_form';
$route['vreservation/add_save_transaksi'] = 'page/vreservation/c_transaksi/add_save_transaksi';
$route['vreservation/getCountApprove'] = 'page/vreservation/c_global/getCountApprove';
$route['vreservation/vr_request'] = 'page/vreservation/c_transaksi/vr_request';
$route['vreservation/json_list_approve'] = 'page/vreservation/c_transaksi/json_list_approve';
$route['vreservation/approve_submit'] = 'page/vreservation/c_transaksi/approve_submit';
$route['vreservation/json_list_booking_by_user'] = 'page/vreservation/c_transaksi/json_list_booking_by_user';
$route['vreservation/cancel_submit'] = 'page/vreservation/c_transaksi/cancel_submit';
$route['vreservation/json_list_booking'] = 'page/vreservation/c_transaksi/json_list_booking';

$route['vreservation/config/modalform_previleges'] = 'page/vreservation/c_config/modal_form_previleges';
$route['vreservation/config/menu-previleges/get_menu'] = 'page/vreservation/c_config/get_menu';
$route['vreservation/config/menu-previleges/get_menu/save'] = 'page/vreservation/c_config/get_menu_save_menu';
$route['vreservation/config/menu-previleges/get_submenu/save'] = 'page/vreservation/c_config/get_submenu_save_menu';
$route['vreservation/config/menu-previleges/get_submenu/show'] = 'page/vreservation/c_config/get_submenu_show';
$route['vreservation/config/menu-previleges/get_submenu/update'] = 'page/vreservation/c_config/get_submenu_update';
$route['vreservation/config/menu-previleges/get_submenu/delete'] = 'page/vreservation/c_config/get_submenu_delete';
$route['vreservation/getGroupPrevileges'] = 'page/vreservation/c_config/getGroupPrevileges';
// $route['vreservation/getMenu'] = 'page/vreservation/c_config/getMenu';
$route['vreservation/getMenu'] = 'page/vreservation/c_config/getMenu';
$route['vreservation/config/menu-previleges/get_submenu_by_menu'] = 'page/vreservation/c_config/get_submenu_by_menu';
$route['vreservation/config/menu-previleges/get_previleges_group/show'] = 'page/vreservation/c_config/get_previleges_group_show';
$route['vreservation/config/menu-previleges/groupuser/save'] = 'page/vreservation/c_config/save_groupuser_previleges';
$route['vreservation/config/menu-previleges/groupuser/update'] = 'page/vreservation/c_config/previleges_groupuser_update';
$route['vreservation/config/menu-previleges/groupuser/delete'] = 'page/vreservation/c_config/previleges_groupuser_delete';
$route['vreservation/config/modalform_group_user'] = 'page/vreservation/c_config/modalform_group_user';
$route['vreservation/config/groupuser/save'] = 'page/vreservation/c_config/save_group_user';
$route['vreservation/config/groupuser/update'] = 'page/vreservation/c_config/update_group_user';
$route['vreservation/config/groupuser/delete'] = 'page/vreservation/c_config/delete_group_user';
$route['vreservation/config/getAuthDataTables'] = 'page/vreservation/c_config/getAuthDataTables';
$route['vreservation/add_auth_user'] = 'page/vreservation/c_config/add_auth_user';
$route['vreservation/delete_authUser'] = 'page/vreservation/c_config/delete_authUser';
$route['vreservation/edit_auth_user'] = 'page/vreservation/c_config/edit_auth_user';
$route['vreservation/master/modalform/(:any)'] = 'page/vreservation/c_master/modalform/$1';
$route['vreservation/master/loadTableMaster/(:any)'] = 'page/vreservation/c_master/load_table_master/$1';
$route['vreservation/master/submit_m_equipment'] = 'page/vreservation/c_master/submit_m_equipment';
$route['vreservation/master/additional_personel_json_data'] = 'page/vreservation/c_master/additional_personel_json_data';
$route['vreservation/master/additional_personel/modalform'] = 'page/vreservation/c_master/additional_personel_modal_form';
$route['vreservation/master/additional_personel/submit'] = 'page/vreservation/c_master/additional_personel_submit';
$route['vreservation/master/loaddataJSonEquipmentRoom'] = 'page/vreservation/c_master/loaddataJSonEquipmentRoom';
$route['vreservation/master/modal_form_equipmentroom'] = 'page/vreservation/c_master/modal_form_equipmentroom';
$route['vreservation/master/getDataEquipmentMaster'] = 'page/vreservation/c_master/getDataEquipmentMaster';
$route['vreservation/master/EquipmentRoom/submit'] = 'page/vreservation/c_master/EquipmentRoom_submit';
$route['vreservation/master/loaddataJSonEquipment_additional'] = 'page/vreservation/c_master/loaddataJSonEquipment_additional';
$route['vreservation/master/modal_form_equipmentadditional'] = 'page/vreservation/c_master/modal_form_equipmentadditional';
$route['vreservation/master/EquipmentAdditional/submit'] = 'page/vreservation/c_master/EquipmentAdditional_submit';
$route['vreservation/master/getRoomItem'] = 'page/vreservation/c_master/getRoomItem';
$route['vreservation/master/submit_select_venue_room'] = 'page/vreservation/c_master/submit_select_venue_room';


$route['vreservation/config/policy_json_data'] = 'page/vreservation/c_config/policy_json_data';
$route['vreservation/config/policy/modalform'] = 'page/vreservation/c_config/policy_modalform';
$route['vreservation/config/policy/submit'] = 'page/vreservation/c_config/policy_submit';


// test
$route['testApprove'] = 'page/finance/c_finance/testApprove';
$route['testInject'] = 'api/c_global/testInject';


// Pengawas Ujian
$route['invigilator'] = 'c_pengawas_ujian';


// Announcement
$route['announcement/list-announcement'] = 'page/announcement/c_announcement/list_announcement';



// budgeting & PR
$route['budgeting'] = 'page/budgeting/c_budgeting';

$query = $db->get('db_budgeting.cfg_sub_menu');
$result = $query->result();
foreach( $result as $row )
{
	$Slug = $row->Slug;
	$Slug = explode('/', $Slug);
	if (in_array('(:any)', $Slug)) {
	   $a = count($Slug) - 1;
	   $URI = '';
	   for ($i=0; $i < $a; $i++) { 
	   	$URI .= $Slug[$i].'/';
	   }
	   $route[ $URI.'(:any)' ] = $row->Controller;
	}
	elseif(in_array('(:num)', $Slug)) {
		$a = count($Slug) - 1;
		$URI = '';
		for ($i=0; $i < $a; $i++) { 
			$URI .= $Slug[$i].'/';
		}
		$route[ $URI.'(:num)' ] = $row->Controller;
	}
	else
	{
		$route[ $row->Slug ] = $row->Controller;
	}

}

$route['budgeting_configfinance/(:any)'] = 'page/budgeting/c_budgeting/configfinance/$1';
$route['budgeting/page/LoadTimePeriod'] = 'page/budgeting/c_budgeting/pageLoadTimePeriod';
$route['budgeting/time_period/modalform'] = 'page/budgeting/c_budgeting/modal_pageLoadTimePeriod';
$route['budgeting/time_period/modalform/save'] = 'page/budgeting/c_budgeting/modal_pageLoadTimePeriod_save';
$route['budgeting/table_cari/(:any)/(:any)/(:any)'] = 'page/budgeting/c_budgeting/LoadTable_db_budgeting_cari/$1/$2/$3/$4';
$route['budgeting/table_cari/(:any)/(:any)/(:any)/(:any)'] = 'page/budgeting/c_budgeting/LoadTable_db_budgeting_cari/$1/$2/$3/$4';
$route['budgeting/table_all/(:any)/(:any)'] = 'page/budgeting/c_budgeting/LoadTable_db_budgeting_all/$1/$2';
$route['budgeting/table_all/(:any)'] = 'page/budgeting/c_budgeting/LoadTable_db_budgeting_all/$1/$2';
$route['budgeting/page/loadMasterPost'] = 'page/budgeting/c_budgeting/pageloadMasterPost';
$route['budgeting/masterpost/modalform'] = 'page/budgeting/c_budgeting/modal_pageloadMasterPost';
$route['budgeting/masterpost/modalform/save'] = 'page/budgeting/c_budgeting/modal_pageloadMasterPost_save';
$route['budgeting/postrealisasi/modalform'] = 'page/budgeting/c_budgeting/modal_postrealisasi';
$route['budgeting/postrealisasi/modalform/save'] = 'page/budgeting/c_budgeting/save_postrealisasi';
$route['budgeting/get_cfg_postrealisasi'] = 'page/budgeting/c_budgeting/get_cfg_postrealisasi';
$route['budgeting/page/loadCodePrefix'] = 'page/budgeting/c_budgeting/loadCodePrefix';
$route['budgeting/save_codeprefix'] = 'page/budgeting/c_budgeting/save_codeprefix';
$route['budgeting/page/LoadSetPostDepartement'] = 'page/budgeting/c_budgeting/LoadSetPostDepartement';
$route['budgeting/page/LoadInputsetPostDepartement'] = 'page/budgeting/c_budgeting/LoadInputsetPostDepartement';
$route['budgeting/getPostDepartement'] = 'page/budgeting/c_budgeting/getPostDepartement';
$route['budgeting/getDomPostDepartement'] = 'page/budgeting/c_budgeting/getDomPostDepartement';
$route['budgeting/save-setpostdepartement'] = 'page/budgeting/c_budgeting/save_setpostdepartement';
$route['budgeting/getBudgetLastYearByCode'] = 'page/budgeting/c_budgeting/getBudgetLastYearByCode';
$route['budgeting/page/LogPostDepartement'] = 'page/budgeting/c_budgeting/LogPostDepartement';
$route['budgeting/DataLogPostDepartement'] = 'page/budgeting/c_budgeting/DataLogPostDepartement';
$route['budgeting/page/LoadSetUserRole'] = 'page/budgeting/c_budgeting/LoadSetUserRole';
$route['budgeting/page/LoadMasterUserRoleDepartement'] = 'page/budgeting/c_budgeting/LoadMasterUserRoleDepartement';
$route['budgeting/AutoCompletePostDepartement'] = 'page/budgeting/c_budgeting/AutoCompletePostDepartement';
$route['budgeting/save_cfg_set_userrole'] = 'page/budgeting/c_budgeting/save_cfg_set_userrole';
$route['budgeting/page/LoadSetUserActionDepartement'] = 'page/budgeting/c_budgeting/LoadSetUserActionDepartement';
$route['budgeting/get_cfg_set_roleuser/(:any)'] = 'page/budgeting/c_budgeting/get_cfg_set_roleuser/$1';
$route['budgeting/save_cfg_set_roleuser'] = 'page/budgeting/c_budgeting/save_cfg_set_roleuser';
$route['budgeting/EntryBudget/EntryBudget'] = 'page/budgeting/c_budgeting/EntryBudget';
$route['budgeting/getCreatorBudget'] = 'page/budgeting/c_budgeting/getCreatorBudget';
$route['budgeting/saveCreatorbudget'] = 'page/budgeting/c_budgeting/saveCreatorbudget';
$route['budgeting/EntryBudget/Approval'] = 'page/budgeting/c_budgeting/EntryBudget_Approval';
$route['budgeting/getLoadApprovalBudget'] = 'page/budgeting/c_budgeting/getLoadApprovalBudget';
$route['budgeting/EntryBudget/ListBudgetDepartement'] = 'page/budgeting/c_budgeting/ListBudgetDepartement';
$route['budgeting/getListBudgetingDepartement'] = 'page/budgeting/c_budgeting/getListBudgetingDepartement';
$route['budgeting/export_excel_budget_creator'] = 'C_save_to_excel/export_excel_budget_creator';
$route['budgeting/export_excel_budget_creator_all'] = 'C_save_to_excel/export_excel_budget_creator_all';
$route['budgeting/EntryBudget/BudgetLeft'] = 'page/budgeting/c_budgeting/BudgetLeft';
$route['budgeting/getListBudgetingRemaining'] = 'page/budgeting/c_budgeting/getListBudgetingRemaining';
$route['budgeting/detail_budgeting_remaining'] = 'page/budgeting/c_budgeting/detail_budgeting_remaining';
$route['budgeting/export_excel_budget_remaining'] = 'C_save_to_excel/export_excel_budget_remaining';


// Purchasing
$query = $db->get('db_purchasing.cfg_sub_menu');
$result = $query->result();
// print_r($result);die();
foreach( $result as $row )
{
	$Slug = $row->Slug;
	$Slug = explode('/', $Slug);
	if (in_array('(:any)', $Slug)) {
	   $a = count($Slug) - 1;
	   $URI = '';
	   for ($i=0; $i < $a; $i++) { 
	   	$URI .= $Slug[$i].'/';
	   }
	   $route[ $URI.'(:any)' ] = $row->Controller;
	}
	elseif(in_array('(:num)', $Slug)) {
		$a = count($Slug) - 1;
		$URI = '';
		for ($i=0; $i < $a; $i++) { 
			$URI .= $Slug[$i].'/';
		}
		$route[ $URI.'(:num)' ] = $row->Controller;
	}
	else
	{
		$route[ $row->Slug ] = $row->Controller;
	}

}

$route['purchasing/page/catalog/InputCatalog'] = 'page/purchasing/c_master/InputCatalog';
$route['purchasing/page/catalog/FormInput'] = 'page/purchasing/c_master/InputCatalog_FormInput';
$route['purchasing/page/catalog/saveFormInput'] = 'page/purchasing/c_master/InputCatalog_saveFormInput';
$route['purchasing/page/catalog/DataIntable'] = 'page/purchasing/c_master/Catalog_DataIntable';
$route['purchasing/page/catalog/DataIntable/server_side'] = 'page/purchasing/c_master/Catalog_DataIntable_server_side';
$route['purchasing/page/catalog/ApprovalCatalog'] = 'page/purchasing/c_master/ApprovalCatalog';
$route['purchasing/page/supplier/InputSupplier'] = 'page/purchasing/c_master/InputSupplier';
$route['purchasing/page/supplier/FormInput'] = 'page/purchasing/c_master/InputSupplier_FormInput';
$route['purchasing/page/supplier/saveFormInput'] = 'page/purchasing/c_master/InputSupplier_saveFormInput';
$route['purchasing/page/supplier/saveCategoryFormInput'] = 'page/purchasing/c_master/saveCategoryFormInput';
$route['purchasing/page/supplier/DataIntable'] = 'page/purchasing/c_master/Supplier_DataIntable';
$route['purchasing/page/supplier/DataIntable/server_side'] = 'page/purchasing/c_master/Supplier_DataIntable_server_side';
$route['purchasing/page/supplier/ApprovalSupplier'] = 'page/purchasing/c_master/ApprovalSupplier';
$route['purchasing/table_all/(:any)'] = 'page/purchasing/c_purchasing/LoadTable_db_purchasing_all/$1/$2';
$route['purchasing/table_all/(:any)/(:any)'] = 'page/purchasing/c_purchasing/LoadTable_db_purchasing_all/$1/$2';
// IT
$query = $db->get('db_it.cfg_sub_menu');
$result = $query->result();
// print_r($result);die();
foreach( $result as $row )
{
	$Slug = $row->Slug;
	$Slug = explode('/', $Slug);
	if (in_array('(:any)', $Slug)) {
	   $a = count($Slug) - 1;
	   $URI = '';
	   for ($i=0; $i < $a; $i++) { 
	   	$URI .= $Slug[$i].'/';
	   }
	   $route[ $URI.'(:any)' ] = $row->Controller;
	}
	elseif(in_array('(:num)', $Slug)) {
		$a = count($Slug) - 1;
		$URI = '';
		for ($i=0; $i < $a; $i++) { 
			$URI .= $Slug[$i].'/';
		}
		$route[ $URI.'(:num)' ] = $row->Controller;
	}
	else
	{
		$route[ $row->Slug ] = $row->Controller;
	}

}

$route['ApiServerToServer'] = 'c_login/ApiServerToServer';
$route['importFormulirManual'] = 'page/admission/c_admission/importFormulirManual';
$route['ImportupdateNoKwitansi'] = 'page/admission/c_admission/ImportupdateNoKwitansi';


// for inject //
$route['testadi'] = 'dashboard/c_dashboard/testadi';
$route['testadi2'] = 'c_login/testadi2';