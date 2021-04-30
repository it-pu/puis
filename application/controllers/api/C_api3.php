<?php
defined('BASEPATH') or exit('No direct script access allowed');

class C_api3 extends CI_Controller
{

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
        $data_arr = (array) $this->jwt->decode($token, $key);
        return $data_arr;
    }

    private function getInputToken2()
    {
        $token = $this->input->post('token');
        $key = "UAP)(*";
        $data_arr = (array) $this->jwt->decode($token, $key);
        return $data_arr;
    }

    public function is_url_exist($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_NOBODY, true);
        curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

        if ($code == 200) {
            $status = true;
        } else {
            $status = false;
        }
        curl_close($ch);
        return $status;
    }

    public function getListMenuAgregator($Type)
    {

        //        $data = $this->db->order_by('ID','ASC')->get('db_agregator.agregator_menu')->result_array();

        $data = $this->db->query('SELECT am.* FROM db_agregator.agregator_menu am
                                              LEFT JOIN db_agregator.agregator_menu_header amh
                                              ON (amh.ID = am.MHID)
                                              WHERE amh.Type = "' . $Type . '" ')->result_array();

        return print_r(json_encode($data));
    }

    public function crudTeamAgregagor()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'insertTeamAggr') {

            $dataForm = (array) $data_arr['dataForm'];
            $input = $data_arr['input'];
            $view = 'all';
            $Access = $this->m_master->encode_auth_access_aps_rs($input, $view, []);
            $dataForm['Access'] = $Access;
            $this->db->insert('db_agregator.agregator_user', $dataForm);
            $insert_id = $this->db->insert_id();

            $Member = (array) $data_arr['Member'];
            if (count($Member) > 0) {
                for ($i = 0; $i < count($Member); $i++) {

                    // Cek apakah NIP sudah ada atau blm
                    $dataCk = $this->db->get_where('db_agregator.agregator_user_member', array(
                        'NIP' => $Member[$i]
                    ))->result_array();

                    if (count($dataCk) <= 0) {
                        $arr = array(
                            'AUPID' => $insert_id,
                            'NIP' => $Member[$i]
                        );
                        $this->db->insert('db_agregator.agregator_user_member', $arr);
                    }
                }
            }

            return print_r(1);
        }
        if ($data_arr['action'] == 'insertTeamAggr_APS') {

            $dataForm = (array) $data_arr['dataForm'];
            $input = $data_arr['input'];
            $view = $data_arr['view'];
            $ProdiID = json_decode(json_encode($data_arr['ProdiID']), true);
            $Access = $this->m_master->encode_auth_access_aps_rs($input, $view, $ProdiID);
            $dataForm['Access'] = $Access;
            $this->db->insert('db_agregator.agregator_user_aps', $dataForm);
            $insert_id = $this->db->insert_id();

            $Member = (array) $data_arr['Member'];
            if (count($Member) > 0) {
                for ($i = 0; $i < count($Member); $i++) {

                    // Cek apakah NIP sudah ada atau blm
                    $dataCk = $this->db->get_where('db_agregator.agregator_user_member_aps', array(
                        'NIP' => $Member[$i]
                    ))->result_array();

                    if (count($dataCk) <= 0) {
                        $arr = array(
                            'AUPID' => $insert_id,
                            'NIP' => $Member[$i]
                        );
                        $this->db->insert('db_agregator.agregator_user_member_aps', $arr);
                    }
                }
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'updateTeamAggr') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];
            $input = $data_arr['input'];
            $view = 'all';
            $Access = $this->m_master->encode_auth_access_aps_rs($input, $view, []);
            $dataForm['Access'] = $Access;
            $this->db->where('ID', $ID);
            $this->db->update('db_agregator.agregator_user', $dataForm);
            $this->db->reset_query();


            $this->db->where('AUPID', $ID);
            $this->db->delete('db_agregator.agregator_user_member');
            $this->db->reset_query();

            $Member = (array) $data_arr['Member'];
            if (count($Member) > 0) {
                for ($i = 0; $i < count($Member); $i++) {
                    // Cek apakah NIP sudah ada atau blm
                    $dataCk = $this->db->get_where('db_agregator.agregator_user_member', array(
                        'NIP' => $Member[$i]
                    ))->result_array();

                    if (count($dataCk) <= 0) {
                        $arr = array(
                            'AUPID' => $ID,
                            'NIP' => $Member[$i]
                        );
                        $this->db->insert('db_agregator.agregator_user_member', $arr);
                    }
                }
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'updateTeamAggr_APS') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];
            $input = $data_arr['input'];
            $view = $data_arr['view'];
            $ProdiID = json_decode(json_encode($data_arr['ProdiID']), true);
            $Access = $this->m_master->encode_auth_access_aps_rs($input, $view, $ProdiID);
            $dataForm['Access'] = $Access;
            $this->db->where('ID', $ID);
            $this->db->update('db_agregator.agregator_user_aps', $dataForm);
            $this->db->reset_query();


            $this->db->where('AUPID', $ID);
            $this->db->delete('db_agregator.agregator_user_member_aps');
            $this->db->reset_query();

            $Member = (array) $data_arr['Member'];
            if (count($Member) > 0) {
                for ($i = 0; $i < count($Member); $i++) {
                    // Cek apakah NIP sudah ada atau blm
                    $dataCk = $this->db->get_where('db_agregator.agregator_user_member_aps', array(
                        'NIP' => $Member[$i]
                    ))->result_array();

                    if (count($dataCk) <= 0) {
                        $arr = array(
                            'AUPID' => $ID,
                            'NIP' => $Member[$i]
                        );
                        $this->db->insert('db_agregator.agregator_user_member_aps', $arr);
                    }
                }
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'readTeamAggr') {

            $data = $this->db->get('db_agregator.agregator_user')->result_array();
            $WhereFilter = '';
            if (array_key_exists('Type', $data_arr)) {
                $WhereFilter .= ' and b.Type ="' . $data_arr['Type'] . '" ';
            }
            for ($i = 0; $i < count($data); $i++) {

                // Get Menu Name
                $ArrMenu = json_decode($data[$i]['Menu']);

                $listMenu = [];
                for ($m = 0; $m < count($ArrMenu); $m++) {

                    // $dtm = $this->db->get_where('db_agregator.agregator_menu',array(
                    //     'ID' => $ArrMenu[$m]
                    // ))->result_array();
                    $sqldtm = 'select a.*,b.Type from db_agregator.agregator_menu as a
                              join db_agregator.agregator_menu_header as b on a.MHID = b.ID
                              where a.ID = ' . $ArrMenu[$m] . $WhereFilter . '
                    ';
                    $dtm = $this->db->query($sqldtm, array())->result_array();

                    if (count($dtm) > 0) {
                        array_push($listMenu, $dtm[0]);
                    }
                }

                $data[$i]['Member'] = $this->db->query('SELECT aum.*, em.Name FROM db_agregator.agregator_user_member aum
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = aum.NIP)
                                                            WHERE aum.AUPID = "' . $data[$i]['ID'] . '" ')->result_array();

                $data[$i]['DetailMenu'] = $listMenu;
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'readTeamAggr_APS') {

            $data = $this->db->get('db_agregator.agregator_user_aps')->result_array();
            $WhereFilter = '';
            if (array_key_exists('Type', $data_arr)) {
                $WhereFilter .= ' and b.Type ="' . $data_arr['Type'] . '" ';
            }
            for ($i = 0; $i < count($data); $i++) {

                // Get Menu Name
                $ArrMenu = json_decode($data[$i]['Menu']);

                $listMenu = [];
                for ($m = 0; $m < count($ArrMenu); $m++) {

                    $sqldtm = 'select a.*,b.Type from db_agregator.agregator_menu as a
                              join db_agregator.agregator_menu_header as b on a.MHID = b.ID
                              where a.ID = ' . $ArrMenu[$m] . $WhereFilter . '
                    ';
                    $dtm = $this->db->query($sqldtm, array())->result_array();

                    if (count($dtm) > 0) {
                        array_push($listMenu, $dtm[0]);
                    }
                }

                $data[$i]['Member'] = $this->db->query('SELECT aum.*, em.Name FROM db_agregator.agregator_user_member_aps aum
                                                            LEFT JOIN db_employees.employees em ON (em.NIP = aum.NIP)
                                                            WHERE aum.AUPID = "' . $data[$i]['ID'] . '" ')->result_array();

                $data[$i]['DetailMenu'] = $listMenu;
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removeTeamAggr') {
            $ID = $data_arr['ID'];

            $this->db->where('AUPID', $ID);
            $this->db->delete('db_agregator.agregator_user_member');
            $this->db->reset_query();

            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.agregator_user');

            return print_r(1);
        } else if ($data_arr['action'] == 'removeTeamAggr_APS') {
            $ID = $data_arr['ID'];

            $this->db->where('AUPID', $ID);
            $this->db->delete('db_agregator.agregator_user_member_aps');
            $this->db->reset_query();

            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.agregator_user_aps');

            return print_r(1);
        }
    }

    public function crudLembagaSurview()
    {
        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'readLembagaSurview') {

            $Type = $data_arr['Type'];

            $data = $this->db->get_where('db_agregator.lembaga_surview', array(
                'Type' => $Type
            ))->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'readLembagaAudit') {
            $data = $this->db->get('db_agregator.lembaga_audit')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateLembagaSurview') {

            $dataForm = array(
                'Lembaga' => $data_arr['Lembaga'],
                'Description' => $data_arr['Description'],
                'Type' => $data_arr['Type']
            );

            if ($data_arr['ID'] != '') {
                // Update
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_agregator.lembaga_surview', $dataForm);
                return print_r(1);
            } else {

                $squery = 'SELECT * FROM db_agregator.lembaga_surview WHERE Lembaga = "' . $data_arr['Lembaga'] . '" ';
                $dataTable = $this->db->query($squery, array())->result_array();

                if (count($dataTable) > 0) {
                    return print_r(0);
                } else {
                    // Insert
                    $this->db->insert('db_agregator.lembaga_surview', $dataForm);
                    return print_r(1);
                }
            }
        } else if ($data_arr['action'] == 'updateLembagaAudit') {

            $dataForm = array(
                'Lembaga' => $data_arr['Lembaga'],
                'Description' => $data_arr['Description']
            );

            if ($data_arr['ID'] != '') {
                // Update
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_agregator.lembaga_audit', $dataForm);
            } else {

                $squery = 'SELECT * FROM db_agregator.lembaga_audit WHERE Lembaga = "' . $data_arr['Lembaga'] . '" ';
                $dataTable = $this->db->query($squery, array())->result_array();

                if (count($dataTable) > 0) {
                    return print_r(0);
                } else {
                    // Insert
                    $this->db->insert('db_agregator.lembaga_audit', $dataForm);
                    return print_r(1);
                }
            }
        }
    }

    public function crudExternalAccreditation()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'updateNewAE') {

            $dataForm = (array) $data_arr['dataForm'];

            if ($data_arr['ID'] != '') {
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_agregator.external_accreditation', $dataForm);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_agregator.external_accreditation', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'viewListAE') {

            $requestData = $_REQUEST;

            $Previlege = $data_arr['Previlege'];

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' WHERE  ls.Lembaga LIKE "%' . $search . '%"
            OR ea.Type LIKE "%' . $search . '%"
             OR ea.Scope LIKE "%' . $search . '%"
              OR ea.Description LIKE "%' . $search . '%"  ';
            }

            $queryDefault = 'SELECT ea.*, ls.Lembaga FROM db_agregator.external_accreditation ea
                                        LEFT JOIN db_agregator.lembaga_surview ls ON (ls.ID = ea.LembagaID) ' . $dataSearch;

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $btnAction = ($Previlege == '1' || $Previlege == 1) ? '
                                                                       <div class="btn-group btnAction">
                                                                      <button type="button" class="btn btn-sm btn-default dropdown-toggle dropdown-menu-left" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fa fa-pencil"></i> <span class="caret"></span>
                                                                      </button>
                                                                      <ul class="dropdown-menu">
                                                                        <li><a href="javascript:void(0);" class="btnEditAE" data-no="' . $no . '"> <i class="fa fa fa-edit"></i> Edit</a></li>
                                                                        <li role="separator" class="divider"></li>
                                                                        <li><a href="javascript:void(0);" class="btnRemove" data-id="' . $row['ID'] . '"> <i class="fa fa fa-trash"></i> Delete</a></li>
                                                                      </ul>
                                                                    </div>
                                                                        <textarea class="hide" id="viewDetail_' . $no . '">' . json_encode($row) . '</textarea>' : '-';

                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Lembaga'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Type'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Scope'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row['Level'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . date('d M Y', strtotime($row['DueDate'])) . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $btnAction . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Description'] . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval(count($queryDefaultRow)),
                "recordsFiltered" => intval(count($queryDefaultRow)),
                "data"            => $data
            );
            echo json_encode($json_data);
        }
    }

    public function crudInternationalAccreditation()
    {

        $data_arr = $this->getInputToken2();
        if ($data_arr['action'] == 'updateIAP') {

            $dataForm = (array) $data_arr['dataForm'];
            if ($data_arr['ID'] != '') {
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_agregator.international_accreditation_prodi', $dataForm);
            } else {
                $this->db->insert('db_agregator.international_accreditation_prodi', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'viewListIA') {

            $requestData = $_REQUEST;
            $Previlege = $data_arr['Previlege'];

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' WHERE  ls.Lembaga LIKE "%' . $search . '%"
            OR ea.Type LIKE "%' . $search . '%"
             OR ea.Scope LIKE "%' . $search . '%"
              OR ea.Description LIKE "%' . $search . '%"  ';
            }

            $queryDefault = 'SELECT iap.*, ls.Lembaga, ps.Name AS ProdiName, ps.Code AS ProdiCode FROM db_agregator.international_accreditation_prodi iap
                                        LEFT JOIN db_agregator.lembaga_surview ls ON (ls.ID = iap.LembagaID)
                                        LEFT JOIN db_academic.program_study ps ON (ps.ID = iap.ProdiID)
                                         ' . $dataSearch;

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                //                $btnAction = ($Previlege=='1' || $Previlege==1) ? '<button class="btn btn-default btn-sm btnEdit" data-no="'.$no.'"><i class="fa fa-edit"></i></button><textarea class="hide" id="viewDetail_'.$no.'">'.json_encode($row).'</textarea>' : '-';

                $btnAction = ($Previlege == '1' || $Previlege == 1) ? '
                                                                       <div class="btn-group btnAction">
                                                                      <button type="button" class="btn btn-sm btn-default dropdown-toggle dropdown-menu-left" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fa fa-pencil"></i> <span class="caret"></span>
                                                                      </button>
                                                                      <ul class="dropdown-menu">
                                                                        <li><a href="javascript:void(0);" class="btnEdit" data-no="' . $no . '"><i class="fa fa fa-edit"></i>Edit</a></li>
                                                                        <li role="separator" class="divider"></li>
                                                                        <li><a href="javascript:void(0);" class="btnRemove" data-id="' . $row['ID'] . '" data-tb="db_agregator.international_accreditation_prodi"><i class="fa fa fa-trash"></i> Remove</a></li>
                                                                      </ul>
                                                                    </div>
                                                                        <textarea class="hide" id="viewDetail_' . $no . '">' . json_encode($row) . '</textarea>' : '-';

                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Lembaga'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row['ProdiName'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row['Status'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . date('d M Y', strtotime($row['DueDate'])) . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $btnAction . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Description'] . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval(count($queryDefaultRow)),
                "recordsFiltered" => intval(count($queryDefaultRow)),
                "data"            => $data
            );
            echo json_encode($json_data);
        }
    }

    public function crudAgregatorTB1()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'crudFEA') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            // Update
            if ($ID != '') {
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['updatedAt'] = $this->m_rest->getDateTimeNow();

                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.financial_external_audit', $dataForm);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_agregator.financial_external_audit', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'removeDataAgg') {

            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete($data_arr['Table']);

            // Remove File
            if (
                isset($data_arr['File']) && $data_arr['File'] != ''
                && is_file('./uploads/agregator/' . $data_arr['File'])
            ) {
                unlink('./uploads/agregator/' . $data_arr['File']);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'removeDataMasterSurvey') {

            $ID = $data_arr['ID'];

            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.lembaga_surview');
            return print_r(1);
        } else if ($data_arr['action'] == 'removeAkreditasi_eks') {

            $ID = $data_arr['ID'];

            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.external_accreditation');
            return print_r(1);
        } else if ($data_arr['action'] == 'removeMasterAudit') {

            $ID = $data_arr['ID'];

            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.lembaga_audit');
            return print_r(1);
        } else if ($data_arr['action'] == 'removeKerjasama') {

            $ID = $data_arr['ID'];

            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.lembaga_mitra_kerjasama');
            return print_r(1);
        } else if ($data_arr['action'] == 'viewListAKE') {

            $requestData = $_REQUEST;

            $Previlege = $data_arr['Previlege'];

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' WHERE  fae.Lembaga LIKE "%' . $search . '%"
            OR fae.Year LIKE "%' . $search . '%"
             OR fae.Opinion LIKE "%' . $search . '%"
              OR fae.Description LIKE "%' . $search . '%"  ';
            }

            $queryDefault = 'SELECT fae.*, lu.Lembaga FROM db_agregator.financial_external_audit fae
                                        LEFT JOIN db_agregator.lembaga_audit lu ON (lu.ID = fae.LembagaAuditID) ' . $dataSearch;

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();
            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                //                $btnAction = ($Previlege=='1' || $Previlege==1) ? '<button class="btn btn-default btn-sm btnEditAE" data-no="'.$no.'"><i class="fa fa-edit"></i></button><textarea class="hide" id="viewDetail_'.$no.'">'.json_encode($row).'</textarea>' : '-';

                $btnAction = ($Previlege == '1' || $Previlege == 1) ? '
                                                                       <div class="btn-group btnAction">
                                                                      <button type="button" class="btn btn-sm btn-default dropdown-toggle dropdown-menu-left" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fa fa-pencil"></i> <span class="caret"></span>
                                                                      </button>
                                                                      <ul class="dropdown-menu">
                                                                        <li><a href="javascript:void(0);" class="btnEditAE" data-no="' . $no . '"><i class="fa fa fa-edit"></i> Edit</a></li>
                                                                        <li role="separator" class="divider"></li>
                                                                        <li><a href="javascript:void(0);" class="btnRemove" data-id="' . $row['ID'] . '" data-tb="db_agregator.financial_external_audit"><i class="fa fa fa-trash"></i> Remove</a></li>
                                                                      </ul>
                                                                    </div>
                                                                        <textarea class="hide" id="viewDetail_' . $no . '">' . json_encode($row) . '</textarea>' : '-';

                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Lembaga'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row['Year'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Opinion'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $btnAction . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Description'] . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval(count($queryDefaultRow)),
                "recordsFiltered" => intval(count($queryDefaultRow)),
                "data"            => $data
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'updateLembagaMitraKerjasama') {

            $dataForm = array(
                'Lembaga' => $data_arr['Lembaga'],
                'Description' => $data_arr['Description']
            );

            if ($data_arr['ID'] != '') {
                // Update
                $this->db->where('ID', $data_arr['ID']);
                $this->db->update('db_agregator.lembaga_mitra_kerjasama', $dataForm);
            } else {
                $squery = 'SELECT * FROM db_agregator.lembaga_mitra_kerjasama WHERE Lembaga = "' . $data_arr['Lembaga'] . '" ';
                $dataTable = $this->db->query($squery, array())->result_array();

                if (count($dataTable) > 0) {
                    return print_r(0);
                } else {
                    // Insert
                    $this->db->insert('db_agregator.lembaga_mitra_kerjasama', $dataForm);
                    return print_r(1);
                }
            }
        } else if ($data_arr['action'] == 'readLembagaMitraKerjasama') {
            $data = $this->db->get('db_agregator.lembaga_mitra_kerjasama')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'crudKPT') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            $FileName = '';

            if ($ID != '') {
                // Update
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.university_collaboration', $dataForm);

                $dataFileName = $this->db->select('File')->get_where(
                    'db_agregator.university_collaboration',
                    array(
                        'ID' => $ID
                    )
                )->result_array();

                $FileName = (count($dataFileName) > 0) ? $dataFileName[0]['File'] : '';
            } else {
                // Insert
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_agregator.university_collaboration', $dataForm);
                $ID = $this->db->insert_id();
            }

            return print_r(json_encode(array(
                'ID' => $ID,
                'FileName' => $FileName
            )));
        } else if ($data_arr['action'] == 'viewListKPT') {

            $requestData = $_REQUEST;

            $Previlege = $data_arr['Previlege'];

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' WHERE  lmk.Lembaga LIKE "%' . $search . '%"
            OR uc.Tingkat LIKE "%' . $search . '%"
             OR uc.Benefit LIKE "%' . $search . '%"';
            }

            $queryDefault = 'SELECT uc.*, lmk.Lembaga FROM db_agregator.university_collaboration uc
                                        LEFT JOIN db_agregator.lembaga_mitra_kerjasama lmk ON (lmk.ID = uc.LembagaMitraID) ' . $dataSearch;

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();
            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                //                $btnAction = ($Previlege=='1' || $Previlege==1) ? '<button class="btn btn-default btn-sm btnEditAE" data-no="'.$no.'"><i class="fa fa-edit"></i></button><textarea class="hide" id="viewDetail_'.$no.'">'.json_encode($row).'</textarea>' : '-';

                $btnAction = ($Previlege == '1' || $Previlege == 1) ? '
                                                                       <div class="btn-group btnAction">
                                                                      <button type="button" class="btn btn-sm btn-default dropdown-toggle dropdown-menu-left" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fa fa-pencil"></i> <span class="caret"></span>
                                                                      </button>
                                                                      <ul class="dropdown-menu">
                                                                        <li><a href="javascript:void(0);" class="btnEditAE" data-no="' . $no . '"><i class="fa fa fa-edit"></i> Edit</a></li>
                                                                        <li role="separator" class="divider"></li>
                                                                        <li><a href="javascript:void(0);" class="btnRemove" data-id="' . $row['ID'] . '" data-file="' . $row['File'] . '" data-tb="db_agregator.university_collaboration"><i class="fa fa fa-trash"></i> Remove</a></li>
                                                                      </ul>
                                                                    </div>
                                                                        <textarea class="hide" id="viewDetail_' . $no . '">' . json_encode($row) . '</textarea>' : '-';

                if ($row['File'] == null) {
                    $links = '<p target="_blank" disabled>No File</p>';
                } else {
                    $links = '<a target="_blank" href="' . base_url('uploads/agregator/' . $row['File']) . '">Download Bukti</a>';
                }

                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Lembaga'] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row['Tingkat'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Benefit'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . date('d M Y', strtotime($row['DueDate'])) . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $links . '</div>';
                //$nestedData[] = '<div style="text-align:left;"><a target="_blank" href="'.base_url('uploads/agregator/'.$row['File']).'">Download Bukti</a></div>';
                $nestedData[] = '<div style="text-align:center;">' . $btnAction . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval(count($queryDefaultRow)),
                "recordsFiltered" => intval(count($queryDefaultRow)),
                "data"            => $data
            );
            echo json_encode($json_data);
        }
    }

    public function crudAgregatorTB2()
    {
        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'crudMHSBaru') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            // Update
            if ($ID != '') {
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['updatedAt'] = $this->m_rest->getDateTimeNow();

                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.student_selection', $dataForm);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_agregator.student_selection', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'crudMHSBaruAsing') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            // Update
            if ($ID != '') {
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['updatedAt'] = $this->m_rest->getDateTimeNow();

                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.student_selection_foreign', $dataForm);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_agregator.student_selection_foreign', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'filterYear') {
            // $data = $this->db->query('SELECT Year FROM db_agregator.student_selection GROUP BY Year ORDER BY Year ASC')->result_array();
            $data = [];
            $sql = "show databases like '" . 'ta_' . "%'";
            $query = $this->db->query($sql, array())->result_array();
            for ($i = 0; $i < count($query); $i++) {
                $variable = $query[$i];
                foreach ($variable as $key => $value) {
                    $ex = explode('_', $value);
                    $ta = $ex[1];
                    $data[] = array('Year' => $ta);
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'LoadDataToInputMHSBaru') {
            $this->load->model('admission/m_admission');
            $Year = $data_arr['Year'];
            $ProdiID = $data_arr['ProdiID'];
            $G_proses = $this->m_admission->proses_agregator_seleksi_mhs_baru_by_prodi($Year, $ProdiID);
            $sql = 'SELECT ss.*, ps.Name AS ProdiName, ps.Code AS ProdiCode FROM db_agregator.student_selection ss
                                                LEFT JOIN db_academic.program_study ps ON (ps.ID = ss.ProdiID)
                                                WHERE ss.Year = "' . $Year . '" and  ss.ProdiID = ? ';
            $query = $this->db->query($sql, array($ProdiID))->result_array();

            return print_r(json_encode($query));
        } else if ($data_arr['action'] == 'readDataMHSBaru') {
            $this->load->model('admission/m_admission');
            $Year = $data_arr['Year'];
            // insert data all ta ke db_agregator.student_selection
            $G_proses = $this->m_admission->proses_agregator_seleksi_mhs_baru($Year);

            $data = array();
            // get all prodi
            $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study', 'Status', 1);
            for ($i = 0; $i < count($G_prodi); $i++) {
                $sql = 'SELECT ss.*, ps.Name AS ProdiName, ps.Code AS ProdiCode FROM db_agregator.student_selection ss
                                                    LEFT JOIN db_academic.program_study ps ON (ps.ID = ss.ProdiID)
                                                    WHERE ss.Year = "' . $Year . '" and  ss.ProdiID = ? ';
                $query = $this->db->query($sql, array($G_prodi[$i]['ID']))->result_array();

                if (count($query) == 0) {
                    $temp = [
                        'Capacity' => null,
                        'EntredAt' => null,
                        'EntredBy' => null,
                        'ID' => null,
                        'PassSelection' => null,
                        'd_PassSelection' => '',
                        'ProdiCode' => $G_prodi[$i]['Code'],
                        'ProdiID' => $G_prodi[$i]['ID'],
                        'ProdiName' => $G_prodi[$i]['Name'],
                        'Registrant' => null,
                        'd_Registrant' => '',
                        'Regular' => null,
                        'd_Regular' => '',
                        'Regular2' => null,
                        'd_Regular2' => '',
                        'TotalStudemt' => null,
                        'Transfer' => null,
                        'd_Transfer' => '',
                        'Transfer2' => null,
                        'd_Transfer2' => '',
                        'Type' => null,
                        'UpdatedBy' => null,
                        'Year' => $Year,
                        'updatedAt' => null,
                    ];
                } else {
                    $dt = $query[0];
                    $dt['d_PassSelection'] = '';
                    $dt['d_Registrant'] = '';
                    $dt['d_Regular'] = '';
                    $dt['d_Regular2'] = '';
                    $dt['d_Transfer'] = '';
                    $dt['d_Transfer2'] = '';

                    $ProdiID = $G_prodi[$i]['ID'];
                    if ($dt['Registrant'] > 0) {
                        $sql2 = 'select * from (
                          select a.ID,a.Name,c.FormulirCode,onf.No_ref,"' . $G_prodi[$i]['Name'] . '" as ProdiName from db_admission.register as a
                          join db_admission.register_verification as b on a.ID = b.RegisterID
                          join db_admission.register_verified as c on b.ID = c.RegVerificationID
                          join db_admission.register_formulir as d on c.ID = d.ID_register_verified
                          join (
                               select FormulirCode,No_ref from db_admission.formulir_number_online_m
                               where Years = ' . $Year . '
                               UNION
                               select FormulirCode,No_ref from db_admission.formulir_number_offline_m
                               where Years = ' . $Year . '
                          ) onf on onf.FormulirCode = c.FormulirCode
                          where a.SetTa = ? and d.ID_program_study = ?
                          ) xx';
                        $query2 = $this->db->query($sql2, array($Year, $ProdiID))->result_array();
                        $token = $this->jwt->encode($query2, "UAP)(*");
                        $dt['d_Registrant'] = $token;
                    }

                    if ($dt['PassSelection'] > 0) {
                        $sql2 = 'select * from (
                          select a.ID,a.Name,c.FormulirCode,onf.No_ref,"' . $G_prodi[$i]['Name'] . '" as ProdiName,e.NPM from db_admission.register as a
                          join db_admission.register_verification as b on a.ID = b.RegisterID
                          join db_admission.register_verified as c on b.ID = c.RegVerificationID
                          join db_admission.register_formulir as d on c.ID = d.ID_register_verified
                          join db_admission.to_be_mhs as e on e.FormulirCode = c.FormulirCode
                          join (
                               select FormulirCode,No_ref from db_admission.formulir_number_online_m
                               where Years = ' . $Year . '
                               UNION
                               select FormulirCode,No_ref from db_admission.formulir_number_offline_m
                               where Years = ' . $Year . '
                          ) onf on onf.FormulirCode = c.FormulirCode
                          where a.SetTa = ? and d.ID_program_study = ?
                          ) xx';
                        $query2 = $this->db->query($sql2, array($Year, $ProdiID))->result_array();
                        $token = $this->jwt->encode($query2, "UAP)(*");
                        $dt['d_PassSelection'] = $token;
                        $dt['d_Regular'] = $token;
                        $dt['d_Regular2'] = $token;
                    }

                    $temp = $dt;
                }

                $data[] = $temp;
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'readDataMHSBaruByProdi') {

            // get tahun akademik smpe sekarang
            $rs = array();
            $arr_tahun_akademik = array();
            $stYear = 2014;
            $endYear = date('Y');
            for ($i = $stYear; $i <= $endYear; $i++) {
                $arr_tahun_akademik[] = $i;
            }

            // get prodi
            $filterProdi = $data_arr['filterProdi'];
            $filterProdiName = $data_arr['filterProdiName'];
            $exFPName = explode('-', $filterProdiName);
            $filterProdiName = trim($exFPName[1]);
            $arrExp = explode('.', $filterProdi);
            $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study', 'ID', $arrExp[0]);
            for ($i = 0; $i < count($arr_tahun_akademik); $i++) {
                $Year = $arr_tahun_akademik[$i];
                $sql = 'SELECT ss.*, ps.Name AS ProdiName, ps.Code AS ProdiCode FROM db_agregator.student_selection ss
                                                    LEFT JOIN db_academic.program_study ps ON (ps.ID = ss.ProdiID)
                                                    WHERE ss.Year = "' . $Year . '" and  ss.ProdiID = ? ';

                $query = $this->db->query($sql, array($arrExp[0]))->result_array();
                if (count($query) == 0) {
                    $temp = [
                        'Capacity' => null,
                        'EntredAt' => null,
                        'EntredBy' => null,
                        'ID' => null,
                        'PassSelection' => null,
                        'd_PassSelection' => '',
                        'ProdiCode' => $arrExp[1],
                        'ProdiID' => $arrExp[0],
                        'ProdiName' => $filterProdiName,
                        'Registrant' => null,
                        'd_Registrant' => '',
                        'Regular' => null,
                        'd_Regular' => '',
                        'Regular2' => null,
                        'd_Regular2' => '',
                        'TotalStudemt' => null,
                        'Transfer' => null,
                        'd_Transfer' => '',
                        'Transfer2' => null,
                        'd_Transfer2' => '',
                        'Type' => null,
                        'UpdatedBy' => null,
                        'Year' => $Year,
                        'updatedAt' => null,
                    ];
                } else {
                    // $temp = $query[0];
                    $dt = $query[0];
                    $dt['d_PassSelection'] = '';
                    $dt['d_Registrant'] = '';
                    $dt['d_Regular'] = '';
                    $dt['d_Regular2'] = '';
                    $dt['d_Transfer'] = '';
                    $dt['d_Transfer2'] = '';

                    $ProdiID = $G_prodi[0]['ID'];

                    if ($dt['Registrant'] > 0) {
                        $sql2 = 'select * from (
                          select a.ID,a.Name,c.FormulirCode,onf.No_ref,"' . $G_prodi[0]['Name'] . '" as ProdiName from db_admission.register as a
                          join db_admission.register_verification as b on a.ID = b.RegisterID
                          join db_admission.register_verified as c on b.ID = c.RegVerificationID
                          join db_admission.register_formulir as d on c.ID = d.ID_register_verified
                          join (
                               select FormulirCode,No_ref from db_admission.formulir_number_online_m
                               where Years = ' . $Year . '
                               UNION
                               select FormulirCode,No_ref from db_admission.formulir_number_offline_m
                               where Years = ' . $Year . '
                          ) onf on onf.FormulirCode = c.FormulirCode
                          where a.SetTa = ? and d.ID_program_study = ?
                          ) xx';
                        $query2 = $this->db->query($sql2, array($Year, $ProdiID))->result_array();
                        $token = $this->jwt->encode($query2, "UAP)(*");
                        $dt['d_Registrant'] = $token;
                    }

                    if ($dt['PassSelection'] > 0) {
                        $sql2 = 'select * from (
                          select a.ID,a.Name,c.FormulirCode,onf.No_ref,"' . $G_prodi[0]['Name'] . '" as ProdiName,e.NPM from db_admission.register as a
                          join db_admission.register_verification as b on a.ID = b.RegisterID
                          join db_admission.register_verified as c on b.ID = c.RegVerificationID
                          join db_admission.register_formulir as d on c.ID = d.ID_register_verified
                          join db_admission.to_be_mhs as e on e.FormulirCode = c.FormulirCode
                          join (
                               select FormulirCode,No_ref from db_admission.formulir_number_online_m
                               where Years = ' . $Year . '
                               UNION
                               select FormulirCode,No_ref from db_admission.formulir_number_offline_m
                               where Years = ' . $Year . '
                          ) onf on onf.FormulirCode = c.FormulirCode
                          where a.SetTa = ? and d.ID_program_study = ?
                          ) xx';
                        $query2 = $this->db->query($sql2, array($Year, $ProdiID))->result_array();
                        $token = $this->jwt->encode($query2, "UAP)(*");
                        $dt['d_PassSelection'] = $token;
                        $dt['d_Regular'] = $token;
                        $dt['d_Regular2'] = $token;
                    }

                    $temp = $dt;
                }

                $rs[] = $temp;
            }

            return print_r(json_encode($rs));
        } else if ($data_arr['action'] == 'readDataMHSBaruAsing') {

            // $Year = $data_arr['Year'];
            // $data = $this->db->query('SELECT ssf.*, ps.Name AS ProdiName, ps.Code AS ProdiCode FROM db_agregator.student_selection_foreign ssf
            //                                         LEFT JOIN db_academic.program_study ps ON (ps.ID = ssf.ProdiID)
            //                                         WHERE ssf.Year = "'.$Year.'" ')->result_array();


            $rs = array('header' => array(), 'body' => array(),);
            // show all ta
            $sql = "show databases like '" . 'ta_' . "%'";
            $query = $this->db->query($sql, array())->result_array();
            $temp = ['No', 'Program Studi'];
            for ($i = 0; $i < count($query); $i++) {
                $arr = $query[$i];
                $db_ = '';

                foreach ($arr as $key => $value) {
                    $db_ = $value;
                }

                if ($db_ != '') {
                    $ta_year = explode('_', $db_);
                    $ta_year = $ta_year[1];
                    $temp[] = $ta_year;
                }
            }

            $rs['header'] = $temp;

            // body
            // find prodi
            $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study', 'Status', 1);
            $body = array();
            for ($j = 0; $j < count($G_prodi); $j++) {
                $temp = [];
                // find count
                $ProdiID = $G_prodi[$j]['ID'];
                $ProdiName = $G_prodi[$j]['Name'];
                $temp[] = $ProdiName;
                for ($i = 0; $i < count($query); $i++) {
                    $arr = $query[$i];
                    $db_ = '';

                    foreach ($arr as $key => $value) {
                        $db_ = $value;
                    }

                    $sql1 = 'select count(*) as total from ' . $db_ . '.students where NationalityID !=  "001" and ProdiID = ? ';
                    $query1 = $this->db->query($sql1, array($ProdiID))->result_array();
                    $total = $query1[0]['total'];
                    $temp[] = $total;
                }

                $body[] = $temp;
            }

            $rs['body'] = $body;

            return print_r(json_encode($rs));
        } else if ($data_arr['action'] == 'readDataMHSBaruAsingByProdi') {
            $rs = array('header' => array(), 'body' => array(),);
            $ProdiID = $data_arr['ProdiID'];
            $ex = explode('.', $ProdiID);
            $ProdiID = $ProdiID[0];
            $ProdiName = $data_arr['ProdiName'];

            // show all ta
            $sql = "show databases like '" . 'ta_' . "%'";
            $query = $this->db->query($sql, array())->result_array();
            $temp_ta = [];
            for ($i = 0; $i < count($query); $i++) {
                $arr = $query[$i];
                $db_ = '';

                foreach ($arr as $key => $value) {
                    $db_ = $value;
                }

                if ($db_ != '') {
                    $ta_year = explode('_', $db_);
                    $ta_year = $ta_year[1];
                    $temp_ta[] = $ta_year;
                }
            }

            // header
            $temp = [
                [
                    'Name' => 'No',
                    'colspan' => 1,
                    'rowspan' => 2,
                    'dt' => []
                ],
                [
                    'Name' => 'Program Studi',
                    'colspan' => 1,
                    'rowspan' => 2,
                    'dt' => []
                ],
                [
                    'Name' => 'Jumlah Mahasiswa Aktif',
                    'colspan' => count($temp_ta),
                    'rowspan' => 1,
                    'dt' => $temp_ta
                ],
                [
                    'Name' => 'Jumlah Mahasiswa Asing Penuh Waktu',
                    'colspan' => count($temp_ta),
                    'rowspan' => 1,
                    'dt' => $temp_ta
                ],
                [
                    'Name' => 'Jumlah Mahasiswa Asing Paruh Waktu',
                    'colspan' => count($temp_ta),
                    'rowspan' => 1,
                    'dt' => $temp_ta
                ],

            ];

            $rs['header'] = $temp;

            // body
            $temp_isi = [1, $ProdiName];
            for ($i = 0; $i < count($query); $i++) { // Jumlah Mahasiswa Aktif
                $arr = $query[$i];
                $db_ = '';

                foreach ($arr as $key => $value) {
                    $db_ = $value;
                }

                $sql1 = 'select count(*) as total from ' . $db_ . '.students where NationalityID !=  "001" and ProdiID = ? ';
                $query1 = $this->db->query($sql1, array($ProdiID))->result_array();
                $total = $query1[0]['total'];
                $temp_isi[] = $total;
            }

            /*
                Jumlah Mahasiswa Asing Penuh Waktu = Data belum ada jadi diisin dengan 0
                Jumlah Mahasiswa Asing Paruh Waktu = Data belum ada jadi diisin dengan 0
            */

            for ($i = 0; $i < count($query); $i++) { // Jumlah Mahasiswa Asing Penuh Waktu
                $temp_isi[] = 0;
            }


            for ($i = 0; $i < count($query); $i++) { // Jumlah Mahasiswa Asing Paruh Waktu
                $temp_isi[] = 0;
            }

            $rs['body'] = $temp_isi;
            return print_r(json_encode($rs));
        } else if ($data_arr['action'] == 'getAllCourse') {

            $dataProdi = $this->db->select('ID, Name')->get_where('db_academic.program_study', array(
                'Status' => '1'
            ))->result_array();

            $CurriculumID = $data_arr['CurriculumID'];

            if (count($dataProdi) > 0) {
                // get data kurikulum
                for ($i = 0; $i < count($dataProdi); $i++) {
                    $d = $dataProdi[$i];
                    $dataCur = $this->db->query('SELECT cd.TotalSKS, mk.Name, mk.MKCode, mk.CourseType,cd.Semester FROM db_academic.curriculum_details cd
                                                            LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = cd.MKID)
                                                            WHERE cd.ProdiID = "' . $d['ID'] . '"
                                                             AND cd.CurriculumID = "' . $CurriculumID . '"
                                                             ORDER BY cd.Semester ASC')->result_array();

                    $dataProdi[$i]['Details'] = $dataCur;
                }
            }

            return print_r(json_encode($dataProdi));
        }
    }


    public function crudAgregatorTB3()
    {

        $data_arr = $this->getInputToken2();

        // Rekognisi Dosen
        if ($data_arr['action'] == 'save_rekognisi_dosen') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            if ($ID != '') {
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();

                // add bukti upload,buktiname dan tingkat
                $BuktiUpload = "";
                if (array_key_exists('BuktiUpload', $_FILES)) {
                    $Upload = $this->m_master->uploadDokumenMultiple(uniqid(), 'BuktiUpload', $path = './uploads/Agregator/Aps/');
                    //$Upload = json_encode($Upload);
                    $BuktiUpload = $Upload;
                }
                $dataForm['BuktiPendukungUpload'] = $BuktiUpload;
                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.rekognisi_dosen', $dataForm);
            } else {
                /*ADDED BY FEBRI @ DEC 2019*/
                $dataForm['isApproved'] = 2;
                $dataForm['approvedBy'] = $this->session->userdata('NIP') . "/" . $this->session->userdata('Name');;
                /*end ADDED BY FEBRI @ DEC 2019*/
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                // add bukti upload,buktiname dan tingkat
                $BuktiUpload = "";
                if (array_key_exists('BuktiUpload', $_FILES)) {
                    $Upload = $this->m_master->uploadDokumenMultiple(uniqid(), 'BuktiUpload', $path = './uploads/Agregator/Aps/');
                    //$Upload = json_encode($Upload);
                    $BuktiUpload = $Upload[0];
                }

                $dataForm['BuktiPendukungUpload'] = $BuktiUpload;
                $this->db->insert('db_agregator.rekognisi_dosen', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'readDataRekognisiDosen') {
            $data = $this->db->query('SELECT rd.*, em.Name FROM db_agregator.rekognisi_dosen rd
                                                LEFT JOIN db_employees.employees em ON (em.NIP = rd.NIP)
                                                ORDER BY em.Name ASC ')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removeDataRekognisiDosen') {
            $ID = $data_arr['ID'];

            // remove file is exist
            $G_data = $this->m_master->caribasedprimary('db_agregator.rekognisi_dosen', 'ID', $ID);
            if ($G_data[0]['BuktiPendukungUpload'] != '' && $G_data[0]['BuktiPendukungUpload'] != null) {
                $arr_file = (array) json_decode($G_data[0]['BuktiPendukungUpload'], true);
                if (count($arr_file) > 0) {
                    $filePath = 'Agregator\\Aps\\' . $arr_file[0]; // pasti ada file karena required
                    $path = FCPATH . 'uploads\\' . $filePath;
                    unlink($path);
                }
            }

            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.rekognisi_dosen');
            return print_r(1);
        } else if ($data_arr['action'] == 'readProduktivitasPenelitian') {

            $rs = array('header' => array(), 'body' => array());
            $Year = date('Y');
            $Year3 = $Year - 2;
            $arr_year = array();
            for ($i = $Year; $i >= $Year3; $i--) {
                $arr_year[] = $i;
            }
            $header = $arr_year;
            // print_r($arr_year);
            $body = array();
            //$G_research = $this->m_master->showData_array('db_research.sumber_dana');
            $G_research = $this->db->query('SELECT * FROM db_agregator.sumber_dana WHERE Status = "1" ')->result_array();
            for ($i = 0; $i < count($G_research); $i++) {
                $temp = array();
                $temp[] = $G_research[$i]['SumberDana'];
                $ID_sumberdana = $G_research[$i]['ID'];
                for ($j = 0; $j < count($arr_year); $j++) {
                    $Year_ = $arr_year[$j];
                    //$sql = 'select Judul_litabmas from db_research.litabmas where ID_sumberdana = ? and ID_thn_laks = ? ';
                    $sql = 'SELECT a.Judul_litabmas, b.Name
                            FROM db_research.litabmas AS a
                            LEFT JOIN db_employees.employees AS b ON (b.NIP = a.NIP) where a.ID_sumberdana = ? and a.ID_thn_laks = ? ';
                    $query = $this->db->query($sql, array($ID_sumberdana, $Year_))->result_array();

                    // $count = $query[0]['total'];
                    $temp[] = $query;
                    // $temp['SumberDana'] = $G_research[$i]['SumberDana'];
                }

                $body[] = $temp;
            }
            $rs['header'] = $header;
            $rs['body'] = $body;
            return print_r(json_encode($rs));
        } else if ($data_arr['action'] == 'readProduktivitasPkmDosen') {

            $rs = array('header' => array(), 'body' => array());
            $Year = date('Y');
            $Year3 = $Year - 2;
            $arr_year = array();
            for ($i = $Year; $i >= $Year3; $i--) {
                $arr_year[] = $i;
            }
            $header = $arr_year;
            // print_r($arr_year);
            $body = array();
            //$G_research = $this->m_master->showData_array('db_agregator.sumber_dana');
            $G_research = $this->db->query('SELECT * FROM db_agregator.sumber_dana WHERE Status = "1" ')->result_array();
            for ($i = 0; $i < count($G_research); $i++) {
                $temp = array();
                $temp[] = $G_research[$i]['SumberDana'];
                $ID_sumberdana = $G_research[$i]['ID'];
                for ($j = 0; $j < count($arr_year); $j++) {
                    $Year_ = $arr_year[$j];
                    //$sql = 'select Judul_PKM from db_research.pengabdian_masyarakat where ID_sumberdana = ? and ID_thn_laks = ? ';
                    $sql = 'SELECT a.Judul_PKM, b.Name
                            FROM db_research.pengabdian_masyarakat AS a
                            LEFT JOIN db_employees.employees AS b ON (b.NIP = a.NIP) where a.ID_sumberdana = ? and a.ID_thn_laks = ? ';
                    $query = $this->db->query($sql, array($ID_sumberdana, $Year_))->result_array();

                    //$count = $query[0]['total'];
                    //$temp[] = $count;
                    $temp[] = $query;
                }

                $body[] = $temp;
            }
            $rs['header'] = $header;
            $rs['body'] = $body;
            return print_r(json_encode($rs));
        }
    }

    public function crudAgregatorTB4()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'readSumberDana') {

            $data = $this->db->get('db_agregator.sumber_dana')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'readSumberDanaType') {

            $data = $this->db->get_where('db_agregator.sumber_dana_type', array(
                'SumberDanaID' => $data_arr['SumberDanaID']
            ))->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'readSumberDanaType_All') {
            $data = $this->db->query('SELECT sdt.*, sd.SumberDana FROM db_agregator.sumber_dana_type sdt
                                          LEFT JOIN db_agregator.sumber_dana sd ON (sdt.SumberDanaID = sd.ID) ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateSumberDana') {

            $ID = $data_arr['ID'];

            if ($ID != '') {
                // Update
                $this->db->set('SumberDana', $data_arr['SumberDana']);
                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.sumber_dana');
            } else {
                $this->db->insert('db_agregator.sumber_dana', array(
                    'SumberDana' => $data_arr['SumberDana']
                ));
                $ID = $this->db->insert_id();
            }

            return print_r(json_encode(array('ID' => $ID)));
        } else if ($data_arr['action'] == 'UpdateSumberDataType') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];
            if ($ID != '') {
                // Update
                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.sumber_dana_type', $dataForm);
            } else {
                // Insert
                $this->db->insert('db_agregator.sumber_dana_type', $dataForm);
                $ID = $this->db->insert_id();
            }
            return print_r(json_encode(array('ID' => $ID)));
        } else if ($data_arr['action'] == 'updatePerolehanDana') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];
            if ($ID != '') {
                // Update
                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.perolehan_dana', $dataForm);
            } else {
                // Insert
                $this->db->insert('db_agregator.perolehan_dana', $dataForm);
                $ID = $this->db->insert_id();
            }
            return print_r(json_encode(array('ID' => $ID)));
        } else if ($data_arr['action'] == 'readPerolehanDana') {

            $data = $this->db->query('SELECT pd.*, sd.SumberDana, sdt.Label AS SumberDanaType FROM db_agregator.perolehan_dana pd
                                              LEFT JOIN db_agregator.sumber_dana sd ON (sd.ID = pd.SumberDanaID)
                                              LEFT JOIN db_agregator.sumber_dana_type sdt ON (sdt.ID = pd.SumberDanaTypeID) ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removePerolehanDana') {

            $ID = $data_arr['ID'];

            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.perolehan_dana');

            return print_r(1);
        } else if ($data_arr['action'] == 'updatePenggunaanDana') {


            $dataForm = (array) $data_arr['dataForm'];

            $JPID = $dataForm['JPID'];
            $Year = $dataForm['Year'];

            $dataCk = $this->db->get_where('db_agregator.penggunaan_dana', array(
                'JPID' => $JPID,
                'Year' => $Year
            ))->result_array();


            //            $ID = $data_arr['ID'];
            $ID = (count($dataCk) > 0) ? $dataCk[0]['ID'] : '';


            if ($ID != '') {
                // Update
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.penggunaan_dana', $dataForm);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_agregator.penggunaan_dana', $dataForm);
                $ID = $this->db->insert_id();
            }

            return print_r(json_encode(array(
                'ID' => $ID
            )));
        } else if ($data_arr['action'] == 'updatePenggunaanDana_aps') {


            $dataForm = (array) $data_arr['dataForm'];
            // print_r($dataForm);die();
            $JPID = $dataForm['JPID'];
            $Year = $dataForm['Year'];
            $PriceUPPS = $dataForm['PriceUPPS'];
            $PricePS = $dataForm['PricePS'];
            $ProdiID = $dataForm['ProdiID'];
            $dataCk = $this->db->get_where('db_agregator.penggunaan_dana_aps', array(
                'JPID' => $JPID,
                'Year' => $Year,
                'PriceUPPS' => $PriceUPPS,
                'PricePS' => $PricePS,
                'ProdiID' => $ProdiID,
            ))->result_array();


            //            $ID = $data_arr['ID'];
            $ID = (count($dataCk) > 0) ? $dataCk[0]['ID'] : '';


            if ($ID != '') {
                // Update
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.penggunaan_dana_aps', $dataForm);
            } else {
                $JPID = $dataForm['JPID'];
                $Year = $dataForm['Year'];
                $ProdiID = $dataForm['ProdiID'];
                $dataCek = $this->db->get_where('db_agregator.penggunaan_dana_aps', array(
                    'JPID' => $JPID,
                    'Year' => $Year,
                    'ProdiID' => $ProdiID,
                ))->result_array();

                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                if (count($dataCek) > 0) {
                    $ID = $dataCek[0]['ID'];
                    $this->db->where('ID', $ID);
                    $this->db->update('db_agregator.penggunaan_dana_aps', $dataForm);
                } else {
                    $this->db->insert('db_agregator.penggunaan_dana_aps', $dataForm);
                    $ID = $this->db->insert_id();
                }
            }

            return print_r(json_encode(array(
                'ID' => $ID
            )));
        } else if ($data_arr['action'] == 'viewPenggunaanDana') {

            $Year = $data_arr['Year'];
            $Year1 = $data_arr['Year1'];
            $Year2 = $data_arr['Year2'];

            // Load Jenis P
            $dataJenis = $this->db->get('db_agregator.jenis_penggunaan')->result_array();

            $result = [];

            if (count($dataJenis) > 0) {


                for ($i = 0; $i < count($dataJenis); $i++) {
                    $d = $dataJenis[$i];

                    for ($y = 1; $y <= 3; $y++) {
                        if ($y == 1) {
                            $YearEx = $Year;
                        } else if ($y == 2) {
                            $YearEx = $Year1;
                        } else {
                            $YearEx = $Year2;
                        }

                        $dataPD = $this->db->query('SELECT pd.* FROM db_agregator.penggunaan_dana pd
                                                  WHERE pd.Year = "' . $YearEx . '" AND pd.JPID = "' . $d['ID'] . '" ')->result_array();

                        $dataJenis[$i]['th' . $y] = (count($dataPD) > 0) ? $dataPD[0]['Price'] : 0;
                    }
                }
            }

            return print_r(json_encode($dataJenis));
        } else if ($data_arr['action'] == 'viewPenggunaanDana_aps') {

            $Year = $data_arr['Year'];
            $Year1 = $data_arr['Year1'];
            $Year2 = $data_arr['Year2'];
            $Year3 = $data_arr['Year3'];
            $Year4 = $data_arr['Year4'];
            $Year5 = $data_arr['Year5'];
            $ProdiID = $data_arr['ProdiID'];

            // Load Jenis P
            $dataJenis = $this->db->get('db_agregator.jenis_penggunaan_aps')->result_array();

            $result = [];

            if (count($dataJenis) > 0) {


                for ($i = 0; $i < count($dataJenis); $i++) {
                    $d = $dataJenis[$i];

                    for ($y = 1; $y <= 3; $y++) {
                        if ($y == 1) {
                            $YearEx = $Year;
                        } else if ($y == 2) {
                            $YearEx = $Year1;
                        } else {
                            $YearEx = $Year2;
                        }

                        $dataPD = $this->db->query('SELECT pd.* FROM db_agregator.penggunaan_dana_aps pd
                                                  WHERE pd.Year = "' . $YearEx . '" AND pd.JPID = "' . $d['ID'] . '" and pd.ProdiID = "' . $ProdiID . '" ')->result_array();

                        $dataJenis[$i]['th' . $y] = (count($dataPD) > 0) ? $dataPD[0]['PriceUPPS'] : 0;
                        //$dataJenis[$i]['th'.$y] = (count($dataPD)>0) ? $dataPD[0]['PricePS'] : 0;
                    }
                }
                for ($i = 0; $i < count($dataJenis); $i++) {
                    $d = $dataJenis[$i];

                    for ($y = 4; $y <= 6; $y++) {
                        if ($y == 4) {
                            $YearEx = $Year3;
                        } else if ($y == 5) {
                            $YearEx = $Year4;
                        } else {
                            $YearEx = $Year5;
                        }

                        $dataPD = $this->db->query('SELECT pd.* FROM db_agregator.penggunaan_dana_aps pd
                                                  WHERE pd.Year = "' . $YearEx . '" AND pd.JPID = "' . $d['ID'] . '" and pd.ProdiID = "' . $ProdiID . '" ')->result_array();

                        $dataJenis[$i]['th' . $y] = (count($dataPD) > 0) ? $dataPD[0]['PricePS'] : 0;
                        //$dataJenis[$i]['th'.$y] = (count($dataPD)>0) ? $dataPD[0]['PricePS'] : 0;
                    }
                }
            }

            return print_r(json_encode($dataJenis));
        } else if ($data_arr['action'] == 'viewPenggunaanDanaYear') {
            $data = $this->db->query('SELECT pd.Year FROM db_agregator.penggunaan_dana pd
                                                  GROUP BY pd.Year ORDER BY pd.Year DESC ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'viewPenggunaanDanaYear_aps') {
            $data = $this->db->query('SELECT pd.Year FROM db_agregator.penggunaan_dana_aps pd
                                                  GROUP BY pd.Year ORDER BY pd.Year DESC ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removePenggunaanDana') {

            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_agregator.penggunaan_dana');

            return print_r(1);
        } else if ($data_arr['action'] == 'removePenggunaanDana_aps') {

            $this->db->where('ID', $data_arr['ID']);
            $this->db->delete('db_agregator.penggunaan_dana_aps');

            return print_r(1);
        } else if ($data_arr['action'] == 'updateJenisDana') {

            $ID = $data_arr['ID'];

            $dataForm = array('Jenis' => $data_arr['Jenis']);

            if ($ID != '') {
                // Update

                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.jenis_penggunaan', $dataForm);
            } else {
                $this->db->insert('db_agregator.jenis_penggunaan', $dataForm);
                $ID = $this->db->insert_id();
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'updateJenisDana_aps') {

            $ID = $data_arr['ID'];

            $dataForm = array('Jenis' => $data_arr['Jenis']);

            if ($ID != '') {
                // Update

                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.jenis_penggunaan_aps', $dataForm);
            } else {
                $this->db->insert('db_agregator.jenis_penggunaan_aps', $dataForm);
                $ID = $this->db->insert_id();
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'viewJenisDana') {

            $data = $this->db->get('db_agregator.jenis_penggunaan')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'viewJenisDana_aps') {

            $data = $this->db->get('db_agregator.jenis_penggunaan_aps')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'readYearSDNewSumberDana') {
            $data = $this->db->query('SELECT Year FROM db_agregator.perolehan_dana_2 pd
                                                      GROUP BY Year ORDER BY Year DESC')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'readNewSumberDana') {

            $dataTS = $this->db->get_where(
                'db_agregator.perolehan_dana_2',
                array('Year' => $data_arr['Year'])
            )->result_array();


            $y1 = (int) $data_arr['Year'] - 1;
            $dataTS1 = $this->db->get_where(
                'db_agregator.perolehan_dana_2',
                array('Year' => $y1)
            )->result_array();


            $y2 = (int) $data_arr['Year'] - 2;
            $dataTS2 = $this->db->get_where(
                'db_agregator.perolehan_dana_2',
                array('Year' => $y2)
            )->result_array();

            $result = array(
                'TS' => $dataTS,
                'TS1' => $dataTS1,
                'TS2' => $dataTS2,
            );

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'updateNewSumberDana') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];
            $Year = $dataForm['Year'];

            $result = 0;
            if ($ID != '') {
                // Update
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['updatedAt'] = $this->m_rest->getDateTimeNow();

                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.perolehan_dana_2', $dataForm);
                $result = 1;
            } else {
                // Cek apakah tahun sudah pernah di input atau blm;
                $dataY = $this->db->get_where('db_agregator.perolehan_dana_2', array(
                    'Year' => $Year
                ))->result_array();

                if (count($dataY) <= 0) {
                    $dataForm['EntredBy'] = $this->session->userdata('NIP');
                    $dataForm['EntredAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->where('ID', $ID);
                    $this->db->insert('db_agregator.perolehan_dana_2', $dataForm);
                    $result = 1;
                }
            }

            return print_r($result);
        }
    }

    public function crudqna()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'updateNewQNA') {

            $dataForm = (array) $data_arr['dataForm'];

            $ID = $data_arr['ID'];

            if ($ID != '') {
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('db_employees.user_qna', $dataForm);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_employees.user_qna', $dataForm);
                $ID = $this->db->insert_id();
            }

            return print_r(json_encode(array('ID' => $ID)));
        } else if ($data_arr['action'] == 'viewListQNA') {

            $requestData = $_REQUEST;

            $Previlege = $data_arr['Previlege'];
            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' WHERE  ls.Questions LIKE "%' . $search . '%"
            OR qna.Answers "%' . $search . '%"';
            }

            $queryDefault = 'SELECT qna.*, ls.Questions FROM db_employees.qna qna ' . $dataSearch;

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $btnAction = ($Previlege == '1' || $Previlege == 1) ? '
                                                                       <div class="btn-group btnAction">
                                                                      <button type="button" class="btn btn-sm btn-default dropdown-toggle dropdown-menu-left" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                                        <i class="fa fa-pencil"></i> <span class="caret"></span>
                                                                      </button>
                                                                      <ul class="dropdown-menu">
                                                                        <li><a href="javascript:void(0);" class="btnEditAE" data-no="' . $no . '">Edit</a></li>
                                                                        <li role="separator" class="divider"></li>
                                                                        <li><a href="javascript:void(0);" class="btnRemove" data-id="' . $row['ID'] . '" data-tb="db_agregator.external_accreditation">Remove</a></li>
                                                                      </ul>
                                                                    </div>
                                                                        <textarea class="hide" id="viewDetail_' . $no . '">' . json_encode($row) . '</textarea>' : '-';

                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Lembaga'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Type'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Scope'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Level'] . '</div>';
                $nestedData[] = '<div style="text-align:right;">' . date('d M Y', strtotime($row['DueDate'])) . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $btnAction . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['Description'] . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval(count($queryDefaultRow)),
                "recordsFiltered" => intval(count($queryDefaultRow)),
                "data"            => $data
            );
            echo json_encode($json_data);
        }
    }


    public function crudAgregatorTB5()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'updatePAM') {

            $ID = $data_arr['ID'];

            $dataForm = (array) $data_arr['dataForm'];

            if ($ID != '') {
                // Update
                /*ADDED BY FEBRI @ NOV 2019*/
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                /*END ADDED BY FEBRI @ NOV 2019*/
                $this->db->where('ID', $ID);
                $this->db->update('db_studentlife.student_achievement', $dataForm);

                $this->db->reset_query();

                $this->db->where('SAID', $ID);
                $this->db->delete('db_studentlife.student_achievement_student');
            } else {
                /*ADDED BY FEBRI @ NOV 2019*/
                $dataForm['isApproved'] = 2;
                $dataForm['approvedBy'] = $this->session->userdata('NIP') . '/' . $this->session->userdata('Name');
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                /*END ADDED BY FEBRI @ NOV 2019*/
                //$dataForm['EntredAt'] = $this->m_rest->getDateTimeNow();
                //                $this->db->insert('db_agregator.prestasi_mahasiswa',$dataForm);
                $this->db->insert('db_studentlife.student_achievement', $dataForm);
                $ID = $this->db->insert_id();
            }

            // Add Student
            $dataListStudent = (array) $data_arr['dataListStudent'];

            if (count($dataListStudent) > 0) {
                for ($i = 0; $i < count($dataListStudent); $i++) {
                    $d = (array) $dataListStudent[$i];
                    $arr = array(
                        'SAID' => $ID,
                        'NPM' => $d['NPM']
                    );
                    $this->db->insert('db_studentlife.student_achievement_student', $arr);
                }
            }


            $dataF = $this->db->get_where('db_studentlife.student_achievement', array('ID' => $ID))->result_array();
            $FileName = $dataF[0]['Certificate'];
            return print_r(json_encode(array('ID' => $ID, 'FileName' => $FileName)));
        } else if ($data_arr['action'] == 'viewDataPAM') {
            //UPDATED BY FEBRI @ DEC 2019
            $data = $this->db->query('SELECT a.*,b.Name as categName , (select approvedBy from db_studentlife.student_achievement c where c.approvedBy like "' . $this->session->userdata('NIP') . '%" and c.ID = a.ID) as isAbble
                                      FROM db_studentlife.student_achievement  a
                                      left join db_studentlife.categories_achievement b on b.ID = a.CategID
                                      ORDER BY Year, StartDate DESC')->result_array();
            //END UPDATED BY FEBRI


            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $ID = $data[$i]['ID'];
                    $data[$i]['DataStudent'] = $this->db->query('SELECT sas.*, ats.Name FROM db_studentlife.student_achievement_student sas
                                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM = sas.NPM)
                                                            WHERE sas.SAID = "' . $ID . '" ')->result_array();
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'viewDataPAM_APS') {
            $ProdiID = $data_arr['ProdiID'];
            $data = $this->db->query('SELECT sa.* FROM db_studentlife.student_achievement as sa

                        JOIN db_studentlife.student_achievement_student as sas on sas.SAID = sa.ID
                        JOIN db_academic.auth_students as aus on sas.NPM = aus.NPM

                        WHERE aus.ProdiID = ' . $ProdiID . '
                        group by sa.ID
                        ORDER BY sa.Year, sa.StartDate DESC')->result_array();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $ID = $data[$i]['ID'];
                    $data[$i]['DataStudent'] = $this->db->query('SELECT sas.*, ats.Name FROM db_studentlife.student_achievement_student sas
                                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = sas.NPM)
                                                                    WHERE sas.SAID = "' . $ID . '"
                                                                    and ats.ProdiID = ' . $ProdiID . '
                                                                    ')->result_array();
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateLamaStudy') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            $year = $dataForm['Year'];
            $ID_programpendik = $dataForm['ID_programpendik'];

            $squery = 'SELECT * FROM db_agregator.lama_studi_mahasiswa WHERE ID_programpendik = "' . $ID_programpendik . '" AND Year = "' . $year . '" ';
            $dataTable = $this->db->query($squery, array())->result_array();

            if (count($dataTable) > 0) {
                return print_r(0);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_agregator.lama_studi_mahasiswa', $dataForm);
                return print_r(1);
            }
        } else if ($data_arr['action'] == 'update_study') {

            $dataForm = (array) $data_arr['dataForm'];
            $year = $dataForm['Year'];
            $ID_programpendik = $dataForm['ID_programpendik'];

            $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
            $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
            $this->db->where('Year', $year);
            $this->db->where('ID_programpendik', $ID_programpendik);
            $this->db->update('db_agregator.lama_studi_mahasiswa', $dataForm);
            return print_r(1);
        } else if ($data_arr['action'] == 'viewPAM') {

            $data = $this->db->get_where('db_studentlife.student_achievement', array(
                'Type' => $data_arr['Type']
            ))->result_array();
            return print_r(json_encode($data));

            exit;

            $data = $this->db->get_where('db_agregator.prestasi_mahasiswa', array(
                'Type' => $data_arr['Type']
            ))->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'viewLamaStudyold') {

            $year = date('Y');
            $arr_year = array();
            for ($i = 0; $i < 3; $i++) {
                $arr_year[] = $year - $i;
            }
            $data = $this->db->query('SELECT a.ID,a.ID_programpendik, b.ID AS IDPrograms, b.NamaProgramPendidikan
                    FROM db_agregator.lama_studi_mahasiswa AS a
                    INNER JOIN db_agregator.program_pendidikan AS b ON (a.ID_programpendik = b.ID) Group by  a.ID_programpendik  order by a.ID_programpendik asc,a.Year desc ')->result_array();
            for ($i = 0; $i < count($data); $i++) {
                for ($j = 0; $j < count($arr_year); $j++) {
                    $sql = 'select * from db_agregator.lama_studi_mahasiswa where ID_programpendik = ' . $data[$i]['ID_programpendik'] . ' and Year = ' . $arr_year[$j];
                    $query = $this->db->query($sql, array())->result_array();
                    if (count($query) > 0) {
                        $data[$i]['Jumlah_lulusan_' . $arr_year[$j]] = $query[0]['Jumlah_lulusan'];
                        $data[$i]['Jumlah_masa_studi_' . $arr_year[$j]] = $query[0]['Jumlah_masa_studi'];
                    } else {
                        $data[$i]['Jumlah_lulusan_' . $arr_year[$j]] = 0;
                        $data[$i]['Jumlah_masa_studi_' . $arr_year[$j]] = 0;
                    }

                    $data[$i]['Year'] = $arr_year[$j];
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'viewLamaStudy') {

            $rs = array('header' => array(), 'body' => array());
            $header = array('No', 'Program Pendidikan');
            // dapatkan 3 tahun belakang
            $Year = date('Y');
            $Year3 = $Year - 2;
            for ($i = $Year; $i >= $Year3; $i--) {
                $header[] = (int)$i;
            }

            for ($i = $Year; $i >= $Year3; $i--) {
                $header[] = (int)$i;
            }
            $rs['header'] = $header;

            $ProgramPendidikan = array(
                "Doktor/ Doktor Terapan/ Subspesialis",
                "Magister/ Magister Terapan/ Spesialis",
                "Profesi 1 Tahun",
                "Profesi 2 Tahun",
                "Sarjana/ Diploma Empat/ Sarjana Terapan", // indeks 4 search ke database
                "Diploma Tiga",
                "Diploma Dua",
                "Diploma Satu",
            );

            $body = array();

            for ($i = 0; $i < count($ProgramPendidikan); $i++) {
                // define temp default
                $temp = array();
                $temp[] = array('show' => $ProgramPendidikan[$i], 'data' => '');
                if ($i == 4) {
                    for ($j = 2; $j < count($header); $j++) {
                        $get_tayear = $header[$j];
                        if ($j <= 4) { // Jumlah Lulusan pada by Year
                            $sql = 'select count(*) as total from db_academic.auth_students where GraduationYear = "' . $get_tayear . '" and StatusStudentID = ?';
                            $query = $this->db->query($sql, array(1))->result_array();
                            // get data detail
                            $sql1 = 'select NPM,Name from db_academic.auth_students where GraduationYear = "' . $get_tayear . '" and StatusStudentID = ?';
                            $query1 = $this->db->query($sql1, array(1))->result_array();
                            // encode token
                            $token = $this->jwt->encode($query1, "UAP)(*");
                            $temp[] = array('show' => $query[0]['total'], 'data' => $token); // Jumlah PS
                        } else // Rata-rata Masa Studi Lulusan pada
                        {
                            $arr_temp = [];
                            $sql = 'select NPM,Year,GraduationYear,GraduationDate,Tgl_msk from db_academic.auth_students where GraduationYear = "' . $get_tayear . '" and StatusStudentID = ?';
                            $query = $this->db->query($sql, array(1))->result_array();
                            if (count($query) > 0) {
                                for ($k = 0; $k < count($query); $k++) {
                                    if ($query[$k]['Tgl_msk'] != null && $query[$k]['Tgl_msk'] != '') {
                                        $date1 = strtotime($query[$k]['GraduationDate']);
                                        $date2 = strtotime($query[$k]['Tgl_msk']);
                                        $diff = abs($date1 - $date2);

                                        $day = $diff / (60 * 60 * 24); // in day
                                        $month = $day / 30;
                                        $Co = $month;
                                    } else {
                                        $Co = $query[$k]['GraduationYear'] - $query[$k]['Year'];
                                        $Co = $Co * 12; // dalam bulan
                                    }

                                    $arr_temp[] = $Co;
                                }

                                $rata_rata = array_sum($arr_temp) / count($arr_temp);
                                $temp[] = $rata_rata;
                            } else {
                                $temp[] = 0;
                            }
                        }
                    }
                } else {
                    for ($j = 2; $j < count($header); $j++) {
                        // $temp[] = 0;
                        if ($j <= 4) {
                            $temp[] = array('show' => 0, 'data' => '');
                        } else {
                            $temp[] = 0;
                        }
                    }
                }
                $body[] = $temp;
            }
            $rs['body'] = $body;
            return print_r(json_encode($rs));
        } else if ($data_arr['action'] == 'getloopdatastudy') {

            $data = $this->db->query('SELECT a.* FROM db_agregator.program_pendidikan AS a')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getDataStudentAcv') {

            $Year = $data_arr['Year'];

            // Get App Prodi
            $dataP = $this->db->select('ID, Name')->get_where('db_academic.program_study', array(
                'Status' => 1
            ))->result_array();

            if (count($dataP) > 0) {
                for ($i = 0; $i < count($dataP); $i++) {

                    for ($y = 0; $y <= 2; $y++) {

                        $YearWhere = $Year - $y;

                        // Get data lulusan

                        $dataStd = $this->db->query('SELECT NPM, Name, GraduationYear FROM db_academic.auth_students ats
                                                                WHERE ats.StatusStudentID = 1 AND ats.GraduationYear = "' . $YearWhere . '"
                                                                AND ats.ProdiID =  "' . $dataP[$i]['ID'] . '"
                                                                ORDER BY ats.NPM ASC ')->result_array();

                        $dataSertificate = [];
                        if (count($dataStd) > 0) {
                            foreach ($dataStd as $item) {

                                $dataSer = $this->db->query('SELECT sa.Event, sa.Year, sas.NPM, sa.Certificate FROM db_studentlife.student_achievement_student sas
                                                                            LEFT JOIN db_studentlife.student_achievement sa ON (sa.ID = sas.SAID)
                                                                            WHERE sas.NPM = "' . $item['NPM'] . '" ')->result_array();

                                if (count($dataSer) > 0) {
                                    for ($c = 0; $c < count($dataSer); $c++) {
                                        $dataSer[$c]['Name'] = $item['Name'];
                                        array_push($dataSertificate, $dataSer[$c]);
                                    }
                                }
                            }
                        }

                        $dataP[$i]['L_' . $YearWhere] = $dataStd;
                        $dataP[$i]['S_' . $YearWhere] = $dataSertificate;
                    }
                }
            }


            return print_r(json_encode($dataP));
        } else if ($data_arr['action'] == 'getPAMByID') {
            $ID = $data_arr['ID'];
            $dataAch = $this->db->get_where('db_studentlife.student_achievement', array('ID' => $ID))->result_array();

            $dataAchStd = $this->db->query('SELECT sas.NPM,ats.Name FROM db_studentlife.student_achievement_student sas
                                                        LEFT JOIN db_academic.auth_students ats ON (sas.NPM = ats.NPM)
                                                        WHERE sas.SAID = "' . $ID . '" ORDER BY ats.Name')->result_array();

            $arr = array(
                'dataAch' => $dataAch,
                'dataAchStd' => $dataAchStd
            );

            return print_r(json_encode($arr));
        } else if ($data_arr['action'] == 'removePAM') {

            // Get data
            $data = $this->db->get_where('db_studentlife.student_achievement', array('ID' => $data_arr['ID']))->result_array();

            if (count($data) > 0) {
                $old = $data[0]['Certificate'];
                if ($old != ''  && is_file('./uploads/certificate/' . $old)) {
                    unlink('./uploads/certificate/' . $old);
                }


                $this->db->where('SAID', $data_arr['ID']);
                $this->db->delete('db_studentlife.student_achievement_student');
                $this->db->reset_query();

                $this->db->where('ID', $data_arr['ID']);
                $this->db->delete('db_studentlife.student_achievement');
            }


            return print_r(1);
        } else if ($data_arr['action'] == 'getYearJasaAdopsi') {

            $db = $data_arr['db'];
            $data = $this->db->query('SELECT Year FROM ' . $db . ' GROUP BY Year ORDER BY Year DESC')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getDetailJasaAdopsi') {
            $db = $data_arr['db'];
            $Year = $data_arr['Year'];

            $result = [];
            for ($i = 0; $i <= 2; $i++) {

                $YearWhere = (int) $Year - $i;
                $data = $this->db->query('SELECT * FROM ' . $db . ' WHERE Year = "' . $YearWhere . '" ')->result_array();

                if (count($data) > 0) {
                    for ($a = 0; $a < count($data); $a++) {
                        $d = $data[$a];
                        $d['Year'] = $YearWhere;

                        array_push($result, $d);
                    }
                }
            }

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'viewIPK') {
            // error_reporting(0);
            $rs = array('header' => array(), 'body' => array());
            $header = array('No', 'Program Pendidikan', '');
            /*
                array 3 awal yang di insert adalah Jumlah Lulusan pada
                array 3 setelah itu yang di insert adalah Rata-rata IPK Lulusan pada

            */
            // dapatkan 3 tahun belakang
            $Year = date('Y');
            $Year3 = $Year - 2;
            for ($i = $Year; $i >= $Year3; $i--) {
                $header[] = (int)$i;
            }

            for ($i = $Year; $i >= $Year3; $i--) {
                $header[] = (int)$i;
            }
            $rs['header'] = $header;

            $ProgramPendidikan = array(
                "Doktor/ Doktor Terapan/ Subspesialis",
                "Magister/ Magister Terapan/ Spesialis",
                "Profesi 1 Tahun",
                "Profesi 2 Tahun",
                "Sarjana/ Diploma Empat/ Sarjana Terapan", // indeks 4 search ke database
                "Diploma Tiga",
                "Diploma Dua",
                "Diploma Satu",
            );

            $body = array();
            for ($i = 0; $i < count($ProgramPendidikan); $i++) {
                // define temp default
                $temp = array();
                // $temp[] = $ProgramPendidikan[$i];
                $temp[] = array('show' => $ProgramPendidikan[$i], 'data' => '');
                if ($i == 4) {
                    for ($j = 2; $j < count($header); $j++) {
                        if ($j == 2) {
                            $sql = 'select count(*) as total from db_academic.program_study where Status = 1 and EducationLevelID in(3,9)';
                            $query = $this->db->query($sql, array())->result_array();
                            // get data detail
                            $sql1 = 'select * from db_academic.program_study where Status = 1 and EducationLevelID in(3,9)';
                            $query1 = $this->db->query($sql1, array())->result_array();
                            // encode token
                            $token = $this->jwt->encode($query1, "UAP)(*");
                            $temp[] = array('show' => $query[0]['total'], 'data' => $token);
                            // $temp[] = $query[0]['total']; // Jumlah PS
                            continue;
                        } else {
                            if ($j <= 5) { // pembeda Jumlah Lulusan pada dan Rata-rata IPK Lulusan pada
                                $get_tayear = $header[$j]; // ex : 2014
                                $sql = 'select count(*) as total from db_academic.auth_students where GraduationYear = "' . $get_tayear . '" and StatusStudentID = ?';
                                $query = $this->db->query($sql, array(1))->result_array();
                                // get data detail
                                $sql1 = 'select NPM,Name from db_academic.auth_students where GraduationYear = "' . $get_tayear . '" and StatusStudentID = ?';
                                $query1 = $this->db->query($sql1, array(1))->result_array();
                                // encode token
                                $token = $this->jwt->encode($query1, "UAP)(*");
                                // $temp[] = $query[0]['total']; // Jumlah PS
                                $temp[] = array('show' => $query[0]['total'], 'data' => $token);
                            } else // pembeda Jumlah Lulusan pada dan Rata-rata IPK Lulusan pada
                            {
                                // cari NPM dulu yg lulusan
                                $get_tayear = $header[$j];
                                $sql = 'select NPM,Year from db_academic.auth_students where GraduationYear = "' . $get_tayear . '" and StatusStudentID = 1';
                                $query = $this->db->query($sql, array())->result_array();
                                $GradeValueCredit = 0;
                                $Credit = 0;
                                $IPK = 0;
                                for ($k = 0; $k < count($query); $k++) {
                                    $ta = 'ta_' . $query[$k]['Year'];
                                    $NPM = $query[$k]['NPM'];
                                    $sql1 = 'select * from ' . $ta . '.study_planning where NPM = ?';
                                    // print_r($sql1);
                                    $query1 = $this->db->query($sql1, array($NPM))->result_array();
                                    for ($l = 0; $l < count($query1); $l++) {
                                        $GradeValue = $query1[$l]['GradeValue'];
                                        $CreditSub = $query1[$l]['Credit'];
                                        $GradeValueCredit = $GradeValueCredit + ($GradeValue * $CreditSub);
                                        $Credit = $Credit + $CreditSub;
                                    }
                                }

                                $IPK = ($Credit == 0) ? 0 : $GradeValueCredit / $Credit;
                                // $temp[] = $IPK;
                                $temp[] = array('show' => $IPK, 'data' => '');
                            }
                        }
                    }
                } else {
                    for ($j = 2; $j < count($header); $j++) {
                        // $temp[] = 0;
                        $temp[] = array('show' => 0, 'data' => '');
                    }
                }
                $body[] = $temp;
            }

            $rs['body'] = $body;

            return print_r(json_encode($rs));
        } else if ($data_arr['action'] == 'getprogrampendik') {
            $data = $this->db->query('SELECT ID, NamaProgramPendidikan FROM db_agregator.program_pendidikan')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getpenilaian') {
            $data = $this->db->query('SELECT *  FROM db_agregator.aspek_penilaian')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'yearstudy') {
            $data = $this->db->query('SELECT ID, Year FROM db_academic.curriculum')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'get_years') {

            if (count($data_arr) > 0) {

                $filterAwaltahun = $data_arr['filterAwaltahun'];
                $data = $this->db->query('SELECT ID, YEAR FROM db_academic.curriculum WHERE YEAR > "' . $data_arr['filterAwaltahun'] . '" LIMIT 4')->result_array();
                return print_r(json_encode($data));
            }
        } else if ($data_arr['action'] == 'readPublikasiIlmiah') {

            $rs = array('header' => array(), 'body' => array());
            $Year = date('Y');
            $Year3 = $Year - 2;
            $arr_year = array();
            for ($i = $Year; $i >= $Year3; $i--) {
                $arr_year[] = $i;
            }
            $header = $arr_year;
            // print_r($arr_year);
            $body = array();
            $G_research = $this->db->query('SELECT * FROM db_research.jenis_forlap_publikasi')->result_array();
            for ($i = 0; $i < count($G_research); $i++) {
                $temp = array();
                $temp[] = $G_research[$i]['NamaForlap_publikasi'];
                $ID_sumberdana = $G_research[$i]['ID'];
                for ($j = 0; $j < count($arr_year); $j++) {
                    $Year_ = $arr_year[$j];
                    //$sql = 'select Judul_litabmas from db_research.litabmas where ID_sumberdana = ? and ID_thn_laks = ? ';
                    $sql = 'SELECT a.Judul, a.Tgl_terbit, b.Name
                            FROM db_research.publikasi AS a
                            LEFT JOIN db_employees.employees AS b ON (b.NIP = a.NIP)
                            WHERE a.ID_forlap_publikasi = ? and YEAR(a.Tgl_terbit) = ? ';
                    $query = $this->db->query($sql, array($ID_sumberdana, $Year_))->result_array();
                    $temp[] = $query;
                }

                $body[] = $temp;
            }
            $rs['header'] = $header;
            $rs['body'] = $body;
            return print_r(json_encode($rs));
        }

        //Waktu Tunggu lulusan
        else if ($data_arr['action'] == 'saveWTL') {

            $dataForm = (array) $data_arr['dataForm'];

            $dataForm['EntredBy'] = $this->session->userdata('NIP');
            $this->db->insert('db_agregator.waktu_tunggu_lulusan', $dataForm);
            //$ID = $this->db->insert_id();
            return print_r(1);
        } else if ($data_arr['action'] == 'update_waktu_tunggu') {

            $dataForm = (array) $data_arr['dataForm'];
            $year = $dataForm['Year'];
            $ID_programpendik = $dataForm['ID_programpendik'];

            $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
            $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
            $this->db->where('Year', $year);
            $this->db->where('ID_programpendik', $ID_programpendik);
            $this->db->update('db_agregator.waktu_tunggu_lulusan', $dataForm);
            return print_r(1);
        } else if ($data_arr['action'] == 'viewWaktuTunggu') {
            $year = date('Y');
            $arr_year = array();
            for ($i = 0; $i < 3; $i++) {
                $arr_year[] = $year - $i;
            }
            $data = $this->db->query('SELECT a.ID,a.ID_programpendik, b.ID AS IDPrograms, b.NamaProgramPendidikan
                    FROM db_agregator.waktu_tunggu_lulusan AS a
                    INNER JOIN db_agregator.program_pendidikan AS b ON (a.ID_programpendik = b.ID) Group by  a.ID_programpendik  order by a.ID_programpendik asc,a.Year desc ')->result_array();
            for ($i = 0; $i < count($data); $i++) {
                for ($j = 0; $j < count($arr_year); $j++) {
                    $sql = 'select * from db_agregator.waktu_tunggu_lulusan where ID_programpendik = ' . $data[$i]['ID_programpendik'] . ' and Year = ' . $arr_year[$j];
                    $query = $this->db->query($sql, array())->result_array();

                    if (count($query) > 0) {
                        $data[$i]['Masa_tunggu_' . $arr_year[$j]] = $query[0]['Masa_tunggu'];
                        //$data[$i]['Jumlah_masa_studi_'.$arr_year[$j]] = $query[0]['Jumlah_masa_studi'];
                    } else {
                        $data[$i]['Masa_tunggu_' . $arr_year[$j]] = 0;
                        //$data[$i]['Jumlah_masa_studi_'.$arr_year[$j]] = 0;
                    }

                    $data[$i]['Year'] = $arr_year[$j];
                }
            }
            return print_r(json_encode($data));
        }

        // Kesesuaian bidang kerja lulusan
        else if ($data_arr['action'] == 'saveKBKL') {

            $dataForm = (array) $data_arr['dataForm'];

            $year = $dataForm['Year'];
            $ID_programpendik = $dataForm['ID_programpendik'];

            $squery = 'SELECT * FROM db_agregator.kesesuaian_bidang_kerja WHERE ID_programpendik = "' . $ID_programpendik . '" AND Year = "' . $year . '" ';
            $dataTable = $this->db->query($squery, array())->result_array();

            if (count($dataTable) > 0) {
                return print_r(0);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_agregator.kesesuaian_bidang_kerja', $dataForm);
                return print_r(1);
            }
        } else if ($data_arr['action'] == 'viewKesesuaian') {
            $year = date('Y');
            $arr_year = array();
            for ($i = 0; $i < 3; $i++) {
                $arr_year[] = $year - $i;
            }
            $data = $this->db->query('SELECT a.ID, a.ID_programpendik, b.ID AS IDPrograms, b.NamaProgramPendidikan
                    FROM db_agregator.kesesuaian_bidang_kerja AS a
                    INNER JOIN db_agregator.program_pendidikan AS b ON (a.ID_programpendik = b.ID) Group by  a.ID_programpendik  order by a.ID_programpendik asc,a.Year desc ')->result_array();
            for ($i = 0; $i < count($data); $i++) {
                for ($j = 0; $j < count($arr_year); $j++) {
                    $sql = 'select * from db_agregator.kesesuaian_bidang_kerja where ID_programpendik = ' . $data[$i]['ID_programpendik'] . ' and Year = ' . $arr_year[$j];
                    $query = $this->db->query($sql, array())->result_array();

                    if (count($query) > 0) {
                        $data[$i]['Persentase_' . $arr_year[$j]] = $query[0]['Persentase'];
                        //$data[$i]['Jumlah_masa_studi_'.$arr_year[$j]] = $query[0]['Jumlah_masa_studi'];
                    } else {
                        $data[$i]['Persentase_' . $arr_year[$j]] = 0;
                        //$data[$i]['Jumlah_masa_studi_'.$arr_year[$j]] = 0;
                    }

                    $data[$i]['Year'] = $arr_year[$j];
                }
            }
            return print_r(json_encode($data));
        }

        //Teknologi Produk Karya
        else if ($data_arr['action'] == 'save_tekno_produk') {

            $dataForm = (array) $data_arr['dataForm'];
            $dataForm['EntredBy'] = $this->session->userdata('NIP');
            $this->db->insert('db_agregator.teknologi_produk_karya', $dataForm);
            return print_r(1);
        }

        //HKI Desain Produk
        else if ($data_arr['action'] == 'save_hki_produk') {

            $dataForm = (array) $data_arr['dataForm'];
            $dataForm['EntredBy'] = $this->session->userdata('NIP');
            $this->db->insert('db_agregator.hki_desain_produk', $dataForm);
            return print_r(1);
        }

        //HKI Desain Produk
        else if ($data_arr['action'] == 'save_hki_paten') {

            $dataForm = (array) $data_arr['dataForm'];
            $dataForm['EntredBy'] = $this->session->userdata('NIP');
            $this->db->insert('db_agregator.hki_paten_sederhana', $dataForm);
            return print_r(1);
        }

        //HKI Desain Produk
        else if ($data_arr['action'] == 'save_sitasi_karya') {

            $dataForm = (array) $data_arr['dataForm'];
            $dataForm['EntredBy'] = $this->session->userdata('NIP');
            $this->db->insert('db_agregator.sitasi_karya', $dataForm);
            return print_r(1);
        } else if ($data_arr['action'] == 'viewRasioKelulusanTepatWaktuDanRasioKeberhasilanStudi') {
            $rs = array();
            $TA = $data_arr['TA'];
            // header table 7 tahun dari TA
            $UntilYear = $TA + 7;
            $te = [];
            for ($i = $TA; $i <= $UntilYear; $i++) {
                $te[] = $i;
            }

            $Lte = $i - 1; // last year

            $header = [
                ['Name' => 'Data', 'Rowspan' => 2, 'Colspan' => 1, 'Sub' => []],
                ['Name' => 'Jumlah Mahasiswa per Angkatan pada Tahun', 'Rowspan' => 1, 'Colspan' => 8, 'Sub' => $te],
                ['Name' => 'Total', 'Colspan' => 1, 'Sub' => [], 'Rowspan' => 2],
            ];

            // show data per prodi in the table
            // $_data = ['Existing','Lulus','Ratio Tepat Waktu','Ratio Keberhasilan Studi'];
            $_data = ['Existing', 'Lulus', 'Persentase Ratio'];
            $G_prodi = $this->m_master->caribasedprimary('db_academic.program_study', 'Status', 1);
            $sql_prodi = 'select a.*,b.MasaStudi from db_academic.program_study as a join db_academic.education_level as b on a.EducationLevelID =  b.ID where a.Status = 1';
            $G_prodi = $this->db->query($sql_prodi, array())->result_array();

            /*
                    Ratio Tepat waktu yaitu 4 tahun
            */
            $dt = [];
            for ($i = 0; $i < count($G_prodi); $i++) {
                $ProdiID = $G_prodi[$i]['ID'];
                $MasaStudi = $G_prodi[$i]['MasaStudi'];
                $IndexMS = $MasaStudi; // array 1 adalah isian data lulus,existing etc
                // get all std ta by ProdiID tanpa status std
                $sql = 'select count(*) as total from (
                        select ID from db_academic.auth_students
                        where Year = ' . $TA . ' and ProdiID = ' . $ProdiID . '
                    )xx';
                $query = $this->db->query($sql, array())->result_array();
                $ExistingAwal = $query[0]['total'];
                $_dt = array(
                    'header' => $header,
                    'ProdiID' => $ProdiID,
                    'MasaStudi' => $MasaStudi,
                    'ProdiName' => $G_prodi[$i]['Name'],
                );
                $_getdt = [];
                $_ex = [];
                $_lu = [];
                for ($j = 0; $j < count($_data); $j++) {
                    $temp = [];
                    switch ($j) {
                        case 0: // existing
                            $temp[] = $_data[$j];
                            $TotalHor = 0;
                            // get 7 tahun ration
                            for ($k = 0; $k < count($te); $k++) {
                                if ($k == 0) {
                                    $temp[] = $ExistingAwal;
                                } else {
                                    $ss = [];
                                    $Yte = $te[$k] + 1;

                                    for ($z = $Yte; $z <= $Lte; $z++) {
                                        $ss[] = (string)$z;
                                    }

                                    $q_add = '';
                                    $sqlAdd = '';
                                    if (count($ss) > 0) {
                                        $q_add = implode(',', $ss);
                                        $q_add = ' and GraduationYear in(' . $q_add . ')';
                                        $sqlAdd = ' UNION
                                                    select ID from db_academic.auth_students
                                                    where Year = ' . $TA . $q_add . ' and GraduationYear is not NULL and  GraduationYear != ""
                                                    and ProdiID = ' . $ProdiID . '
                                                  ';
                                    }

                                    $sqlYear = 'select count(*) as total from (
                                                select ID from db_academic.auth_students
                                                where Year = ' . $TA . ' and ( GraduationYear IS NULL  or GraduationYear = "" )
                                                and ProdiID = ' . $ProdiID . '
                                                ' . $sqlAdd . '
                                            )xx ';

                                    $queryYear = $this->db->query($sqlYear, array())->result_array();

                                    $temp[] = $queryYear[0]['total'];
                                    $_ex[] = $queryYear[0]['total']; // get existing
                                    $TotalHor =  0;
                                }
                            }
                            $_ex[] = 0; // get existing
                            $temp[] = $TotalHor;
                            break;
                        case 1: // Lulus
                            $temp[] = $_data[$j];
                            $TotalHor = 0;
                            // get 7 tahun ration
                            for ($k = 0; $k < count($te); $k++) {
                                $Yte = $te[$k];
                                $sqlYear = 'select count(*) as total from
                                             (
                                                 select ID from db_academic.auth_students
                                                 where Year = ' . $TA . '
                                                 and ProdiID = ' . $ProdiID . '
                                                 and GraduationYear = ' . $Yte . '
                                             )xx
                                        ';
                                $queryYear = $this->db->query($sqlYear, array())->result_array();
                                $temp[] = $queryYear[0]['total'];
                                $_lu[] =  $queryYear[0]['total'];
                                $TotalHor +=  $queryYear[0]['total'];
                            }
                            $_lu[] = 0;
                            $temp[] = $TotalHor;
                            break;
                        case 2: //  Persentase Ratio
                            $temp[] = $_data[$j];
                            for ($k = 0; $k < count($te); $k++) {
                                if ($k == $IndexMS) {
                                    if ($ExistingAwal == 0) {
                                        $temp[] = 0;
                                    } else {
                                        $lulus = ($_lu[($k)] / $ExistingAwal) * 100;
                                        // > 50 = 4 && > 50 = 0
                                        // if ($lulus > 50) {
                                        //     $temp[] = 4;
                                        // }
                                        // else
                                        // {
                                        //    $temp[] = 0;
                                        // }
                                        $temp[] = $lulus;
                                    }
                                } else {
                                    $temp[] = 0;
                                }
                            }

                            $temp[] = 0;
                            break;
                        default:
                            # code...
                            break;
                    } // end switch

                    $_getdt[] = $temp;
                }

                $_dt['data'] = $_getdt; // add variable year
                $dt[] = $_dt; // insert ke table untuk body
            }

            $rs = $dt;
            return print_r(json_encode($rs));
        }

        // Table refrensi
        else if ($data_arr['action'] == 'readTableRef') {

            $Year = (int) $data_arr['Year'];

            $dataEd = $this->db->query('SELECT el.ID, el.Name, el.Description FROM db_academic.education_level el')->result_array();

            if (count($dataEd) > 0) {
                for ($j = 0; $j < count($dataEd); $j++) {

                    for ($i = 0; $i <= 2; $i++) {
                        $Year_where = $Year - $i;
                        $dataEd[$j]['BL_' . $Year_where] = $this->db->query('SELECT ats.NPM, ats.Name, ats.GraduationYear, ps.Name AS Prodi
                                          FROM db_academic.auth_students ats
                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                          WHERE ats.GraduationYear = "' . $Year_where . '"
                                          AND ats.StatusStudentID = "1"
                                          AND ps.EducationLevelID = "' . $dataEd[$j]['ID'] . '"
                                           ORDER BY ats.NPM')->result_array();

                        // Mendapatkan yang menjawab sesuai tahun form dan tahun kelulusan
                        $dataEd[$j]['BJ_' . $Year_where] = $this->db->query('SELECT ats.NPM, ats.Name, ats.GraduationYear, ps.Name AS Prodi
                                                                                       FROM db_studentlife.alumni_form af
                                                                                      LEFT JOIN db_academic.auth_students ats ON (ats.NPM = af.NPM)
                                                                                      LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                                                      WHERE af.Year = "' . $Year . '"
                                                                                      AND ats.GraduationYear = "' . $Year_where . '"
                                                                                      AND ats.StatusStudentID = "1"
                                                                                      AND ps.EducationLevelID = "' . $dataEd[$j]['ID'] . '"
                                                                                      ORDER BY ats.NPM ')->result_array();
                    }
                }
            }


            return print_r(json_encode($dataEd));
        } else if ($data_arr['action'] == 'readTableWaktuTungguLulus') {

            $Year = $data_arr['Year'];
            $dataEd = $this->db->query('SELECT el.ID, el.Name, el.Description FROM db_academic.education_level el')->result_array();

            if (count($dataEd) > 0) {
                for ($j = 0; $j < count($dataEd); $j++) {

                    for ($i = 0; $i <= 2; $i++) {

                        //
                        $Year_where = $Year - $i;


                        $dataStd  = $this->db->query('SELECT ats.NPM, ats.Name, ats.GraduationYear, ps.Name AS Prodi, ats.YudisiumDate
                                                                          FROM db_academic.auth_students ats
                                                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                                          WHERE ats.GraduationYear = "' . $Year_where . '"
                                                                          AND ats.StatusStudentID = "1"
                                                                          AND ps.EducationLevelID = "' . $dataEd[$j]['ID'] . '"
                                                                          ORDER BY ats.NPM')->result_array();



                        $TotalLama = 0;
                        $TotalPembagi = 0;
                        // Mendapatkan pekerjaan pertamanya
                        if (count($dataStd) > 0) {

                            for ($a = 0; $a < count($dataStd); $a++) {

                                $YudisiumDate_ex = ($dataStd[$a]['YudisiumDate'] != '' && $dataStd[$a]['YudisiumDate'] != null) ?
                                    explode('-', $dataStd[$a]['YudisiumDate']) : [];

                                $Experience = $this->db->query('SELECT ae.StartMonth, ae.StartYear FROM db_studentlife.alumni_experience ae
                                                                    WHERE ae.NPM = "' . $dataStd[$a]['NPM'] . '"  ORDER BY ae.ID ASC LIMIT 1 ')->result_array();


                                $LamaKerjaDalamBulan = 0;
                                if (count($Experience) > 0 && count($YudisiumDate_ex) > 0) {

                                    $Y_Month = $YudisiumDate_ex[1];
                                    $Y_Year = $YudisiumDate_ex[0];

                                    $J_Month = $Experience[0]['StartMonth'];
                                    $J_Year = $Experience[0]['StartYear'];

                                    $y_k = ($J_Year - $Y_Year);

                                    if ($y_k == 0 && $J_Month < $Y_Month) {
                                        $y = ($y_k - 1) * 12;
                                    } else if ($y_k > 0) {
                                        $y = ($y_k - 1) * 12;
                                    } else {
                                        $y = $y_k * 12;
                                    }

                                    //                                    $y = ($y_k>0 && $J_Month<$Y_Month) ? ($y_k - 1 ) * 12 : $y_k * 12;
                                    $m = ($J_Month >= $Y_Month) ? abs($J_Month - $Y_Month) : 12 - (abs($J_Month - $Y_Month));

                                    $LamaKerjaDalamBulan = $y + $m;

                                    $dataStd[$a]['Y'] = $y;
                                    $dataStd[$a]['M'] = $m;

                                    $TotalPembagi = $TotalPembagi + 1;
                                }

                                $dataStd[$a]['LamaWaktuTunggu'] = $LamaKerjaDalamBulan;
                                $dataStd[$a]['Name'] = ucwords(strtolower($dataStd[$a]['Name']));
                                $TotalLama = $TotalLama + $LamaKerjaDalamBulan;

                                $dataStd[$a]['Experience'] = $Experience;
                            }
                        }

                        $RataRata = ($TotalPembagi > 0) ? $TotalLama / $TotalPembagi : 0;
                        $dataEd[$j]['BL_' . $Year_where] = array(
                            'DetailStudent' => $dataStd,
                            'TotalPembagi' => $TotalPembagi,
                            'TotalLamaMenunggu' => $TotalLama,
                            'TotalStudent' => count($dataStd),
                            'RataRata' =>  $RataRata
                        );
                    }
                }
            }

            return print_r(json_encode($dataEd));
        } else if ($data_arr['action'] == 'readTableTempatKerjaLulusan') {
            $Year = $data_arr['Year'];
            $dataEd = $this->db->query('SELECT el.ID, el.Name, el.Description FROM db_academic.education_level el')->result_array();
            if (count($dataEd) > 0) {
                for ($j = 0; $j < count($dataEd); $j++) {

                    $dataStd  = $this->db->query('SELECT ats.NPM, ats.Name, ats.GraduationYear, ps.Name AS Prodi, ats.YudisiumDate
                                                                          FROM db_academic.auth_students ats
                                                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                                          WHERE ats.GraduationYear = "' . $Year . '"
                                                                          AND ats.StatusStudentID = "1"
                                                                          AND ps.EducationLevelID = "' . $dataEd[$j]['ID'] . '"
                                                                          ORDER BY ats.NPM')->result_array();

                    $Exp_L = [];
                    $Exp_N = [];
                    $Exp_M = [];
                    $dataStdRes = [];

                    if (count($dataStd) > 0) {
                        for ($a = 0; $a < count($dataStd); $a++) {

                            $Experience = $this->db->query('SELECT ae.StartMonth, ae.StartYear, ae.JobType, ae.JobLevelID, jl.Description FROM db_studentlife.alumni_experience ae
                                                                    LEFT JOIN db_studentlife.job_level jl ON (jl.ID = ae.JobLevelID)
                                                                    WHERE ae.NPM = "' . $dataStd[$a]['NPM'] . '"  ORDER BY ae.ID DESC LIMIT 1 ')->result_array();

                            $Job = '';
                            $JobDescription = '';
                            if (count($Experience) > 0 && $Experience[0]['JobType'] == '1') {
                                $Job = 'Bekerja';
                                $JobDescription = $Experience[0]['Description'];
                            } else if (count($Experience) > 0 && $Experience[0]['JobType'] == '2') {
                                $Job = 'Berwirausaha';
                                $JobDescription = $Experience[0]['Description'];
                            }
                            $dataStd[$a]['Job'] = $Job;
                            $dataStd[$a]['JobDescription'] = $JobDescription;
                            $dataStd[$a]['Experience'] = $Experience;

                            if (count($Experience) > 0 && ($Experience[0]['JobLevelID'] == '1' || $Experience[0]['JobLevelID'] == '4')) {
                                array_push($Exp_L, $dataStd[$a]);
                            } else if (count($Experience) > 0 && ($Experience[0]['JobLevelID'] == '2' || $Experience[0]['JobLevelID'] == '5')) {
                                array_push($Exp_N, $dataStd[$a]);
                            } else if (count($Experience) > 0 && ($Experience[0]['JobLevelID'] == '3' || $Experience[0]['JobLevelID'] == '6')) {
                                array_push($Exp_M, $dataStd[$a]);
                            }


                            if (count($Experience) > 0) {
                                array_push($dataStdRes, $dataStd[$a]);
                            }
                        }
                    }


                    $dataEd[$j]['Exp_L'] = $Exp_L;
                    $dataEd[$j]['Exp_N'] = $Exp_N;
                    $dataEd[$j]['Exp_M'] = $Exp_M;

                    $dataEd[$j]['StudentTotal'] = count($dataStdRes);
                    $dataEd[$j]['StudentDetail'] = $dataStdRes;
                }
            }

            return print_r(json_encode($dataEd));
        } else if ($data_arr['action'] == 'readTableKepuasanPenggunaLulusan') {

            $Year = $data_arr['Year'];
            $dataAspek = $this->db->query('SELECT * FROM db_studentlife.aspek_penilaian_kepuasan')->result_array();

            if (count($dataAspek) > 0) {

                for ($i = 0; $i < count($dataAspek); $i++) {
                    $dataDetails = $this->db->query('SELECT afd.*, ats.Name, ats.NPM, mc.Name AS Company FROM db_studentlife.alumni_form_details afd
                                                              LEFT JOIN db_studentlife.alumni_form af ON (af.ID = afd.FormID)
                                                              LEFT JOIN db_studentlife.alumni_experience ae ON (ae.ID = af.IDAE)
                                                              LEFT JOIN db_studentlife.master_company mc ON (mc.ID = ae.CompanyID)
                                                              LEFT JOIN db_academic.auth_students ats ON (ats.NPM = af.NPM)
                                                              WHERE af.Year = "' . $Year . '"
                                                              AND afd.APKID = "' . $dataAspek[$i]['ID'] . '" ')->result_array();


                    $Total_SB_D = [];
                    $Total_B_D = [];
                    $Total_C_D = [];
                    $Total_K_D = [];
                    if (count($dataDetails) > 0) {
                        for ($a = 0; $a < count($dataDetails); $a++) {
                            $d = $dataDetails[$a];
                            if ($d['Rate'] == '1') {
                                array_push($Total_K_D, $d);
                            } else if ($d['Rate'] == '2') {
                                array_push($Total_C_D, $d);
                            } else if ($d['Rate'] == '3') {
                                array_push($Total_B_D, $d);
                            } else if ($d['Rate'] == '4') {
                                array_push($Total_SB_D, $d);
                            }
                        }
                    }

                    $dataAspek[$i]['Total_SB_D'] = $Total_SB_D;
                    $dataAspek[$i]['Total_B_D'] = $Total_B_D;
                    $dataAspek[$i]['Total_C_D'] = $Total_C_D;
                    $dataAspek[$i]['Total_K_D'] = $Total_K_D;
                    $dataAspek[$i]['Details'] = $dataDetails;
                }
            }

            return print_r(json_encode($dataAspek));
        } else if ($data_arr['action'] == 'readKesesuaianBidangKerjaLulusan') {
            $Year = $data_arr['Year'];
            $dataEd = $this->db->query('SELECT el.ID, el.Name, el.Description FROM db_academic.education_level el')->result_array();

            if (count($dataEd) > 0) {
                for ($j = 0; $j < count($dataEd); $j++) {

                    for ($i = 0; $i <= 2; $i++) {

                        $Year_where = $Year - $i;

                        $dataStd  = $this->db->query('SELECT ats.NPM, ats.Name, ats.GraduationYear, ps.Name AS Prodi, ats.YudisiumDate
                                                                          FROM db_academic.auth_students ats
                                                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                                          WHERE ats.GraduationYear = "' . $Year_where . '"
                                                                          AND ats.StatusStudentID = "1"
                                                                          AND ps.EducationLevelID = "' . $dataEd[$j]['ID'] . '"
                                                                          ORDER BY ats.NPM')->result_array();

                        $TotalPembagi = 0;
                        $TotalKesesuaian = 0;
                        // Mendapatkan pekerjaan terakhirnya
                        if (count($dataStd) > 0) {

                            for ($a = 0; $a < count($dataStd); $a++) {
                                $Experience = $this->db->query('SELECT ae.StartMonth, ae.StartYear, ae.WorkSuitability FROM db_studentlife.alumni_experience ae
                                                                    WHERE ae.NPM = "' . $dataStd[$a]['NPM'] . '"  ORDER BY ae.ID DESC LIMIT 1 ')->result_array();


                                $TotalKesesuaianPerStd = 0;
                                if (count($Experience) > 0) {

                                    if ($Experience[0]['WorkSuitability'] != '' && $Experience[0]['WorkSuitability'] != null) {
                                        $WorkSuitability = (int) $Experience[0]['WorkSuitability'];
                                        $TotalKesesuaianPerStd = ($WorkSuitability > 0) ? 1 : 0;
                                    }



                                    $TotalPembagi = $TotalPembagi + 1;
                                }

                                $dataStd[$a]['Kesesuaian'] = (count($Experience) > 0) ? $Experience[0]['WorkSuitability'] : '-';
                                $dataStd[$a]['Name'] = ucwords(strtolower($dataStd[$a]['Name']));
                                $dataStd[$a]['Experience'] = $Experience;

                                $TotalKesesuaian = $TotalKesesuaian + $TotalKesesuaianPerStd;
                            }
                        }

                        $RataRata = ($TotalPembagi > 0) ? ($TotalKesesuaian / $TotalPembagi) * 100 : 0;
                        $dataEd[$j]['BL_' . $Year_where] = array(
                            'RataRata' => $RataRata,
                            'TotalKesesuaian' => $TotalKesesuaian,
                            'DetailStudent' => $dataStd,
                            'TotalPembagi' => $TotalPembagi,
                            'TotalStudent' => count($dataStd)
                        );
                    }
                }
            }

            return print_r(json_encode($dataEd));
        }
    }


    public function getsum_mahasiswa_asing()
    {

        $year = date('Y');
        $arr_year = array();
        for ($i = 0; $i < 4; $i++) {
            $arr_year[] = $year - $i;
        }
        //print_r($arr_year); exit();

        $Status = $this->input->get('s');

        $data = $this->db->select('ID, Code, Name')->get_where('db_academic.program_study', array(
            'Status' => 1
        ))->result_array();
        $dataMhs = $this->db->query('SELECT a.*, b.Name
                    FROM db_agregator.student_selection_foreign AS a
                    LEFT JOIN db_academic.program_study AS b ON (a.ProdiID = b.ID)
                    WHERE b.Status = 1 ')->result_array();

        if (count($data) > 0) {
            for ($i = 0; $i < count($data); $i++) {

                for ($j = 0; $j < count($arr_year); $j++) {

                    $dataMhs = $this->db->query('SELECT COUNT(*) AS Total FROM db_agregator.student_selection_foreign
                                          WHERE Year = ' . $arr_year[$j] . ' AND ProdiID = "' . $data[$i]['ID'] . '" ')->result_array();
                    //print_r($dataMhs); exit();

                    if (count($dataMhs) > 0) {
                        $data[$i]['Tahunmasuk_' . $arr_year[$j]] = $arr_year[$j];
                        $data[$i]['NameProdi'] = $data[$i]['Name'];
                        $data[$i]['TotalStudent_' . $arr_year[$j]] = $dataMhs[0]['Total'];
                    }


                    //========================
                    //$and2 = ($Status!='all') ? ' AND StatusForlap = "'.$Status.'" ' : '';
                    // Total Mahasiswa
                    //$dataMhs = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.auth_students
                    //                           WHERE Status = "1" AND ProdiID = "'.$data[$i]['ID'].'"  '.$and2)->result_array();
                    // $data[$i]['TotalMahasiwa'] = $dataMhs[0]['Total'];

                    // Total Lectrure
                    //$dataEmp = $this->db->query('SELECT COUNT(*) AS Total FROM db_employees.employees
                    //                          WHERE ProdiID = "'.$data[$i]['ID'].'"  '.$and2)->result_array();
                    //$data[$i]['TotalLecturer'] = $dataEmp[0]['Total'];

                }
            }
        }

        return print_r(json_encode($data));
    }


    public function getKecukupanDosen()
    {

        // Get Program Studi
        // Get Program Studi
        $data = $this->db->select('ID,Code,Name')->get_where('db_academic.program_study', array('Status' => 1))->result_array();

        if (count($data) > 0) {

            $dataLAP = $this->db->order_by('ID', 'DESC')->get_where('db_employees.level_education', array(
                'ID >' => 8
            ))->result_array();
            for ($i = 0; $i < count($data); $i++) {

                for ($j = 0; $j < count($dataLAP); $j++) {

                    $dataDetails = $this->db->query('SELECT em.NIP,  em.NUP, em.NIDN, em.NIDK, em.Name FROM db_employees.employees em
                                                                            WHERE em.ProdiID = "' . $data[$i]['ID'] . '"
                                                                            AND em.LevelEducationID = "' . $dataLAP[$j]['ID'] . '"
                                                                            AND ( em.StatusForlap = "1" OR em.StatusForlap = "2" ) ')->result_array();

                    $r = array('Level' => $dataLAP[$j]['Description'], 'Details' => $dataDetails);
                    $data[$i]['dataLecturers'][$j] = $r;

                    // dengan sub prodi
                    $dataSubs = $this->db->query('SELECT em.NIP,  em.NUP, em.NIDN, em.NIDK, em.Name FROM
                    db_employees.employees_sub_prodi esp
                    LEFT JOIN db_employees.employees em ON (em.NIP = esp.NIP)
                    WHERE esp.ProdiID = "' . $data[$i]['ID'] . '"
                    AND em.LevelEducationID = "' . $dataLAP[$j]['ID'] . '"
                    AND ( em.StatusForlap = "1" OR em.StatusForlap = "2" ) ')->result_array();

                    $r_sub = array('Level' => $dataLAP[$j]['Description'], 'Details' => array_merge($dataDetails, $dataSubs));
                    $data[$i]['dataLecturersSub'][$j] = $r_sub;
                }


                $dataL = $this->db->query('SELECT em.NIP,  em.NUP, em.NIDN, em.NIDK, em.Name FROM db_employees.employees em
                WHERE em.ProdiID = "' . $data[$i]['ID'] . '"
                AND em.Profession <> ""
                AND ( em.StatusForlap = "1" OR em.StatusForlap = "2" ) ')->result_array();
                $r = array('Level' => 'Profesi', 'Details' => $dataL);
                $data[$i]['dataLecturers'][2] = $r;

                // dengan sub prodi
                $dataSubs2 = $this->db->query('SELECT em.NIP,  em.NUP, em.NIDN, em.NIDK, em.Name FROM
                db_employees.employees_sub_prodi esp
                LEFT JOIN db_employees.employees em ON (em.NIP = esp.NIP)
                WHERE esp.ProdiID = "' . $data[$i]['ID'] . '"
                AND em.Profession <> ""
                AND ( em.StatusForlap = "1" OR em.StatusForlap = "2" )')->result_array();

                $r_sub = array('Level' => 'Profesi', 'Details' => array_merge($dataL, $dataSubs2));
                $data[$i]['dataLecturersSub'][2] = $r_sub;


                // get dengan sub prodi
            }
        }

        return print_r(json_encode($data));
    }

    public function getJabatanAkademikDosenTetap()
    {

        $data = $this->db->get_where('db_employees.level_education', array(
            'ID >' => 7
        ))->result_array();

        $dataPosition = $this->db->get('db_employees.lecturer_academic_position')->result_array();

        if (count($data) > 0) {

            for ($i = 0; $i < count($data); $i++) {

                for ($p = 0; $p < count($dataPosition); $p++) {
                    $dataEmp = $this->db->query('SELECT em.NIP, em.NUP, em.NIDN, em.NIDK, em.Name FROM db_employees.employees em
                                                                    WHERE em.LevelEducationID = "' . $data[$i]['ID'] . '"
                                                                    AND em.LecturerAcademicPositionID = "' . $dataPosition[$p]['ID'] . '"
                                                                     AND (em.StatusForlap = "1" || em.StatusForlap = "2") ')->result_array();

                    $r = array(
                        'Position' => $dataPosition[$p]['Position'],
                        'dataEmployees' => $dataEmp
                    );

                    $data[$i]['details'][$p] = $r;
                }


                $dataEmp = $this->db->query('SELECT em.NIP, em.NUP, em.NIDN, em.NIDK, em.Name FROM db_employees.employees em
                                                                    WHERE em.LevelEducationID = "' . $data[$i]['ID'] . '"
                                                                    AND em.LecturerAcademicPositionID NOT IN (SELECT ID FROM db_employees.lecturer_academic_position)
                                                                     AND (em.StatusForlap = "1" || em.StatusForlap = "2") ')->result_array();

                $r = array(
                    'Position' => 'Tenaga Pengajar',
                    'dataEmployees' => $dataEmp
                );

                $data[$i]['details'][4] = $r;
            }
        }

        return print_r(json_encode($data));
    }

    public function getJabatanAkademikDosenTidakTetap()
    {

        $data = $this->db->get_where('db_employees.level_education', array(
            'ID >' => 7
        ))->result_array();

        $dataPosition = $this->db->get('db_employees.lecturer_academic_position')->result_array();

        if (count($data) > 0) {

            for ($i = 0; $i < count($data); $i++) {

                for ($p = 0; $p < count($dataPosition); $p++) {
                    $dataEmp = $this->db->query('SELECT em.NIP, em.NUP, em.NIDN, em.NIDK, em.Name FROM db_employees.employees em
                                                                    WHERE em.LevelEducationID = "' . $data[$i]['ID'] . '"
                                                                    AND em.LecturerAcademicPositionID = "' . $dataPosition[$p]['ID'] . '"
                                                                     AND em.StatusForlap = "0" ')->result_array();

                    $r = array(
                        'Position' => $dataPosition[$p]['Position'],
                        'dataEmployees' => $dataEmp
                    );

                    $data[$i]['details'][$p] = $r;
                }

                $dataEmp = $this->db->query('SELECT em.NIP, em.NUP, em.NIDN, em.NIDK, em.Name FROM db_employees.employees em
                                                                    WHERE em.LevelEducationID = "' . $data[$i]['ID'] . '"
                                                                    AND em.LecturerAcademicPositionID NOT IN (SELECT ID FROM db_employees.lecturer_academic_position)
                                                                     AND em.StatusForlap = "0" ')->result_array();

                $r = array(
                    'Position' => 'Tenaga Pengajar',
                    'dataEmployees' => $dataEmp
                );

                $data[$i]['details'][4] = $r;
            }
        }

        return print_r(json_encode($data));
    }

    public function getLecturerCertificate()
    {

        $data = $this->db->select('ID, Code, Name')->get_where('db_academic.program_study', array(
            'Status' => 1
        ))->result_array();

        if (count($data) > 0) {
            $arrCertificateType = ['Profesional', 'Profesi', 'Industri', 'Kompetensi'];
            for ($i = 0; $i < count($data); $i++) {

                // Total Employees
                $dataEmp = $this->db->query('SELECT em.NIP, em.NUP, em.NIDN, em.NIDK, em.Name FROM db_employees.employees em
                                          WHERE em.ProdiID = "' . $data[$i]['ID'] . '"
                                          AND (em.StatusForlap = "1" || em.StatusForlap = "2")  ')->result_array();

                $data[$i]['TotalLecturer'] = $dataEmp;

                $dataEmpCerti = $this->db->query('SELECT em.NIP, em.NUP, em.NIDN, em.NIDK, em.Name FROM db_employees.employees em
                                          WHERE em.ProdiID = "' . $data[$i]['ID'] . '" AND em.Serdos="1"
                                          AND (em.StatusForlap = "1" || em.StatusForlap = "2")  ')->result_array();

                // untuk certified tanpa serdos
                $dataEmpCerti2 = $dataEmp;

                $data[$i]['TotalLecturerCertifies'] = $dataEmpCerti;

                for ($y = 0; $y < count($arrCertificateType); $y++) {
                    $data[$i]['Certificate_' . $arrCertificateType[$y]] = [];
                }

                for ($x = 0; $x < count($dataEmpCerti2); $x++) {
                    for ($z = 0; $z < count($arrCertificateType); $z++) {
                        $sql = 'select a.*,"' . $dataEmpCerti2[$x]['Name'] . '" as NameDosen, "' . $dataEmpCerti2[$x]['NIDN'] . '" as NIDN from db_employees.employees_certificate as a where a.NIP = "' . $dataEmpCerti2[$x]['NIP'] . '"
                            and a.StatusEdit = 1 and a.Certificate = "' . $arrCertificateType[$z] . '"

                           ';
                        $dt = $this->db->query(
                            $sql
                        )->result_array();


                        if (count($dt) > 0) {

                            // $data[$i]['Certificate_'.$arrCertificateType[$z]] = $data[$i]['Certificate_'.$arrCertificateType[$z]] + $dt;
                            $data[$i]['Certificate_' . $arrCertificateType[$z]] = array_merge($data[$i]['Certificate_' . $arrCertificateType[$z]], $dt);
                        }
                    }
                }
            }
        }

        return print_r(json_encode($data));
    }


    public function getRasioDosenMahasiswa()
    {

        $SemesterID = $this->input->get('smt');
        $Year = $this->input->get('y');
        $Status = $this->input->get('s');

        $data = $this->db->select('ID, Code, Name')->get_where('db_academic.program_study', array(
            'Status' => 1
        ))->result_array();

        if (count($data) > 0) {


            for ($i = 0; $i < count($data); $i++) {



                // Total Mahasiswa
                $dataMhs = $this->db->query('SELECT ats.NPM, ats.Name, ss.Description FROM db_academic.auth_students ats
                                          LEFT JOIN db_academic.status_student ss ON (ss.ID = ats.StatusStudentID)
                                          WHERE ats.StatusStudentID = "3" AND ats.ProdiID = "' . $data[$i]['ID'] . '"
                                          AND ats.Year <= "' . $Year . '"
                                          ORDER BY ats.Year, ats.NPM ASC ')->result_array();

                $data[$i]['dataMahasiwa'] = $dataMhs;


                $dataTA = $this->db->query('SELECT ats.NPM, ats.Name, ss.Description FROM db_academic.std_study_planning ssp
                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssp.NPM)
                                                    LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssp.MKID)
                                                    LEFT JOIN db_academic.status_student ss ON (ss.ID = ats.StatusStudentID)
                                                    WHERE ssp.SemesterID = "' . $SemesterID . '"
                                                    AND ats.ProdiID = "' . $data[$i]['ID'] . '"
                                                     AND ats.Year <= "' . $Year . '"
                                                     AND mk.Yudisium = "1"')->result_array();

                $data[$i]['dataMahasiwaTA'] = $dataTA;


                //
                if ($SemesterID >= 13) {

                    $StatusFolap = ''; // value status all(0)
                    if ($Status == '1' || $Status == 1 || $Status == '99' || $Status == 99) {
                        $StatusFolap = ' AND (em.StatusForlap = "1" OR em.StatusForlap = "2")';
                    } else if ($Status == '2' || $Status == 2) {
                        $StatusFolap = ' AND em.StatusForlap = "0"';
                    }

                    // condition dosen tetap tidak mengajar
                    if ($Status == '99' || $Status == 99) {
                        $dataSchedule = $this->db->query(' select * from (
                                                        SELECT sc.Coordinator AS NIP, em.NUP, em.NIDN, em.NIDK, em.Name, em.StatusForlap FROM db_academic.schedule_details_course sdc
                                                              LEFT JOIN db_academic.schedule sc ON (sc.ID = sdc.ScheduleID)
                                                              LEFT JOIN db_employees.employees em ON (em.NIP = sc.Coordinator)
                                                               WHERE sc.SemesterID = "' . $SemesterID . '"
                                                               AND sdc.ProdiID = "' . $data[$i]['ID'] . '"
                                                               AND em.ProdiID = "' . $data[$i]['ID'] . '"
                                                               ' . $StatusFolap . '
                                                        UNION
                                                            Select em.NIP,em.NUP, em.NIDN, em.NIDK, em.Name,em.StatusForlap from
                                                            db_employees.employees as em
                                                                where em.NIP not in (
                                                                    select sc.Coordinator from db_academic.schedule_details_course as sdc
                                                                    join db_academic.schedule as sc ON (sc.ID = sdc.ScheduleID)
                                                                    join db_employees.employees as em on sc.Coordinator = em.NIP
                                                                    where
                                                                    sc.SemesterID = "' . $SemesterID . '" AND
                                                                    em.ProdiID = "' . $data[$i]['ID'] . '"
                                                                    ' . $StatusFolap . '
                                                                )
                                                                AND em.ProdiID = "' . $data[$i]['ID'] . '"
                                                                   ' . $StatusFolap . '

                                                        )xx
                                                                GROUP BY NIP ')->result_array();
                    } else { // Tidak All
                        $dataSchedule = $this->db->query('SELECT sc.Coordinator AS NIP, em.NUP, em.NIDN, em.NIDK, em.Name, em.StatusForlap FROM db_academic.schedule_details_course sdc
                                                              LEFT JOIN db_academic.schedule sc ON (sc.ID = sdc.ScheduleID)
                                                              LEFT JOIN db_employees.employees em ON (em.NIP = sc.Coordinator)
                                                               WHERE sc.SemesterID = "' . $SemesterID . '"
                                                               AND sdc.ProdiID = "' . $data[$i]['ID'] . '"
                                                               AND em.ProdiID = "' . $data[$i]['ID'] . '"
                                                               ' . $StatusFolap . '
                                                                GROUP BY sc.Coordinator ')->result_array();
                    }



                    $data[$i]['Lecturer_Sch_Co'] = $dataSchedule;

                    $listCoord = [];
                    if (count($dataSchedule) > 0) {
                        foreach ($dataSchedule as $item) {
                            array_push($listCoord, $item['NIP']);
                        }
                    }

                    $data[$i]['Lecturer_Sch_Co_arr'] = $listCoord;

                    $dataScheduleTeam = $this->db->query('SELECT stt.NIP, em.NUP, em.NIDN, em.NIDK, em.Name, em.StatusForlap  FROM db_academic.schedule_team_teaching stt
                                                                LEFT JOIN db_academic.schedule sc ON (sc.ID = stt.ScheduleID)
                                                                LEFT JOIN db_academic.schedule_details_course sdc ON (sc.ID = sdc.ScheduleID)
                                                                LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                                WHERE sc.SemesterID = "' . $SemesterID . '" AND sdc.ProdiID = "' . $data[$i]['ID'] . '"
                                                                AND em.ProdiID = "' . $data[$i]['ID'] . '" ' . $StatusFolap . '
                                                                GROUP BY stt.NIP
                                                                 ')->result_array();

                    $data[$i]['Lecturer_Sch_Team'] = $dataScheduleTeam;

                    if (count($dataScheduleTeam) > 0) {
                        foreach ($dataScheduleTeam as $item) {
                            if (!in_array($item['NIP'], $listCoord)) {
                                array_push($dataSchedule, $item);
                            }
                        }
                    }

                    $data[$i]['Lecturer_Sch_Fix'] = $dataSchedule;
                } else {
                    // Schedule Lama
                    $data[$i]['Lecturer_Sch_Fix'] = [];
                }
            }
        }

        return print_r(json_encode($data));
    }


    public function getLuaran_lainnya()
    {  // 5.h.4. Buku ber-ISBN, Book Chapter

        $statusx = $this->input->get('s');

        if ($statusx == "0") {
            $status = "";
        } else {
            $status = $statusx;
        }

        $requestData = $_REQUEST;
        $whereStatus = ($status != '') ? ' AND YEAR(Tgl_terbit) = "' . $status . '" ' : '';

        $totalData = $this->db->query('SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 4 ' . $whereStatus . ' ')->result_array();

        if (!empty($requestData['search']['value'])) {

            $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 4 AND YEAR(Tgl_terbit) = "' . $status . '" AND ( ';

            $sql .= 'NamaJudul LIKE "' . $requestData['search']['value'] . '%" )';
            $sql .= 'ORDER BY NamaJudul DESC';
        } else {

            if ($status == "") {
                $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 4 ';
                $sql .= 'ORDER BY NamaJudul DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            } else {
                $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 4 AND YEAR(Tgl_terbit) = "' . $status . '" ';
                $sql .= 'ORDER BY NamaJudul DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            }
        }

        $query = $this->db->query($sql)->result_array();
        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            if ($row['Tahun'] == null) {
                $year = $row['Tahun_Laks'];
            } else {
                $year = $row['Tahun'];
            }

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = $row["NamaJudul"];
            $nestedData[] = '<div style="text-align:center;">' . date('Y', strtotime($year)) . '</div>';
            $nestedData[] = $row["Keterangan"];

            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getLuaranTekno_produk()
    {  //Teknologi Tepat Guna, Produk, Karya Seni, Rekayasa

        $statusx = $this->input->get('s');

        if ($statusx == "0") {
            $status = "";
        } else {
            $status = $statusx;
        }

        $requestData = $_REQUEST;
        $whereStatus = ($status != '') ? ' AND YEAR(Tgl_terbit) = "' . $status . '" ' : '';

        $totalData = $this->db->query('SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 1 ' . $whereStatus . ' ')->result_array();

        if (!empty($requestData['search']['value'])) {

            $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 1 AND YEAR(Tgl_terbit) = "' . $status . '" AND ( ';

            $sql .= 'NamaJudul LIKE "' . $requestData['search']['value'] . '%" )';
            $sql .= 'ORDER BY NamaJudul DESC';
        } else {

            if ($status == "") {
                $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 1 ';
                $sql .= 'ORDER BY NamaJudul DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            } else {
                $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 1 AND YEAR(Tgl_terbit) = "' . $status . '" ';
                $sql .= 'ORDER BY NamaJudul DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            }
        }

        $query = $this->db->query($sql)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = $row["NamaJudul"];
            $nestedData[] = '<div style="text-align:center;">' . date('Y', strtotime($row['Tahun'])) . '</div>';
            $nestedData[] = $row["Keterangan"];

            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getLuaranHkiproduk()
    {   // 5.h.2. HKI (Hak Cipta, Desain Produk Industri, dll.)

        $statusx = $this->input->get('s');

        if ($statusx == "0") {
            $status = "";
        } else {
            $status = $statusx;
        }
        $requestData = $_REQUEST;

        $whereStatus = ($status != '') ? ' AND YEAR(Tgl_terbit) = "' . $status . '" ' : '';

        $totalData = $this->db->query('SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 7 ' . $whereStatus . ' ')->result_array();

        if (!empty($requestData['search']['value'])) {

            $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 7 AND YEAR(Tgl_terbit) = "' . $whereStatus . '" AND ( ';

            $sql .= ' NamaJudul LIKE "' . $requestData['search']['value'] . '%" )';
            $sql .= 'ORDER BY NamaJudul DESC';
        } else {

            if ($status == "") {
                $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 7 ';
                $sql .= 'ORDER BY NamaJudul DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            } else {
                $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 7 AND YEAR(Tgl_terbit) = "' . $status . '" ';
                $sql .= 'ORDER BY NamaJudul DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            }
        }

        $query = $this->db->query($sql)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            if ($row['Tahun'] == null) {
                $year = $row['Tahun_Laks'];
            } else {
                $year = $row['Tahun'];
            }

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = $row["NamaJudul"];
            $nestedData[] = '<div style="text-align:center;">' . date('Y', strtotime($year)) . '</div>';
            $nestedData[] = $row["Keterangan"];

            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);


        //$Status = $this->input->get('s');
        //$data = $this->db->query('SELECT Nama_judul, Tahun_perolehan, Keterangan FROM db_agregator.hki_desain_produk ORDER BY ID DESC')->result_array();
        //return print_r(json_encode($data));
    }


    public function getLuaranHkipaten()
    {   // 5.h.1. HKI (Paten, Paten Sederhana)

        $statusx = $this->input->get('s');

        if ($statusx == "0") {
            $status = "";
        } else {
            $status = $statusx;
        }

        $requestData = $_REQUEST;
        $whereStatus = ($status != '') ? ' AND YEAR(Tgl_terbit) = "' . $status . '" ' : '';

        $squery = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 3 ' . $whereStatus . ' ';
        $totalData = $this->db->query($squery, array())->result_array();

        $totalDaddta = $this->db->query('SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 3 ' . $whereStatus . ' ')->result_array();
        //print($sql); exit();

        if (!empty($requestData['search']['value'])) {

            $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 3 AND YEAR(Tgl_terbit) = "' . $status . '" AND ( ';

            $sql .= 'NamaJudul LIKE "' . $requestData['search']['value'] . '%" )';
            $sql .= 'ORDER BY NamaJudul DESC';
        } else {

            if ($status == "") {
                $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 3 ';
                $sql .= 'ORDER BY NamaJudul DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            } else {

                $sql = 'SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan, Waktu_pelaksanaan AS Tahun_Laks
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 3 AND YEAR(Tgl_terbit) = "' . $status . '" ';
                $sql .= 'ORDER BY NamaJudul DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            }
        }

        $query = $this->db->query($sql)->result_array();
        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            if ($row['Tahun'] == null) {
                $year = $row['Tahun_Laks'];
            } else {
                $year = $row['Tahun'];
            }

            $nestedData[] = '<div  style="text-align:center;">' . $no . '</div>';
            $nestedData[] = $row["NamaJudul"];
            $nestedData[] = '<div style="text-align:center;">' . date('Y', strtotime($year)) . '</div>';
            $nestedData[] = $row["Keterangan"];
            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getLuaranHkipatenOLD()
    {

        $data_arr = $this->getInputToken2();
        $hki_year = $data_arr['hkiyear'];

        if (count($data_arr > 0)) {

            if ($data_arr['action'] == 'readHKI_paten') {

                if ($hki_year == "0") {

                    $Yearx = date('Y');

                    $data = $this->db->query('SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 3
                    UNION
                    SELECT Judul_PKM AS NamaJudul, ID_thn_kegiatan AS Tahun, Ket AS Keterangan
                    FROM db_research.pengabdian_masyarakat
                    WHERE ID_kat_capaian = 3 ')->result_array();
                } else {

                    $data = $this->db->query('SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan
                        FROM db_research.publikasi
                        WHERE ID_kat_capaian = 3 AND YEAR(Tgl_terbit) = "' . $hki_year . '"
                        UNION
                        SELECT Judul_PKM AS NamaJudul, ID_thn_kegiatan AS Tahun, Ket AS Keterangan
                        FROM db_research.pengabdian_masyarakat
                        WHERE ID_kat_capaian = 3 AND ID_thn_laks = "' . $hki_year . '" ')->result_array();
                }
                return print_r(json_encode($data));
            }

            if ($data_arr['action'] == 'readHKI_produk') {

                if ($hki_year == "0") {

                    $Yearx = date('Y');

                    $data = $this->db->query('SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan
                    FROM db_research.publikasi
                    WHERE ID_kat_capaian = 7
                    UNION
                    SELECT Judul_PKM AS NamaJudul, ID_thn_kegiatan AS Tahun, Ket AS Keterangan
                    FROM db_research.pengabdian_masyarakat
                    WHERE ID_kat_capaian = 7 ')->result_array();
                } else {

                    $data = $this->db->query('SELECT Judul AS NamaJudul, Tgl_terbit AS Tahun, Ket AS Keterangan
                        FROM db_research.publikasi
                        WHERE ID_kat_capaian = 7 AND YEAR(Tgl_terbit) = "' . $hki_year . '"
                        UNION
                        SELECT Judul_PKM AS NamaJudul, ID_thn_kegiatan AS Tahun, Ket AS Keterangan
                        FROM db_research.pengabdian_masyarakat
                        WHERE ID_kat_capaian = 7 AND ID_thn_laks = "' . $hki_year . '" ')->result_array();
                }
                //print_r($data);
                return print_r(json_encode($data));
            }
        }
    }

    public function getsitasikarya()
    {

        $statusx = $this->input->get('s');

        if ($statusx == "0") {
            $status = "";
        } else {
            $status = $statusx;
        }
        $requestData = $_REQUEST;

        $whereStatus = ($status != '') ? ' WHERE a.Year = "' . $status . '" ORDER BY a.ID DESC' : '';

        $totalData = $this->db->query('SELECT a.Title, a.Citation, a.Year, a.User_create, b.Name, a.Server
                    FROM db_agregator.sitasi_karya AS a
                    LEFT JOIN db_employees.employees AS b ON (a.User_create = b.NIP) ' . $whereStatus . ' ')->result_array();

        if (!empty($requestData['search']['value'])) {

            $sql = 'SELECT a.Title, a.Citation, a.Year, a.User_create, b.Name, a.Server
                    FROM db_agregator.sitasi_karya AS a
                    LEFT JOIN db_employees.employees AS b ON (a.User_create = b.NIP)
                    WHERE (';
            $sql .= 'a.Title LIKE "' . $requestData['search']['value'] . '%" ';
            $sql .= 'OR b.Name LIKE "' . $requestData['search']['value'] . '%")';
            $sql .= ' ORDER BY a.ID DESC';
        } else {

            if ($status == "") {

                if ($requestData['length'] == "-1") {
                    $sql = 'SELECT a.Title, a.Citation, a.Year, a.User_create, b.Name, a.Server
                    FROM db_agregator.sitasi_karya AS a
                    LEFT JOIN db_employees.employees AS b ON (a.User_create = b.NIP)';
                    $sql .= ' ORDER BY a.ID DESC';
                } else {
                    $sql = 'SELECT a.Title, a.Citation, a.Year, a.User_create, b.Name, a.Server
                    FROM db_agregator.sitasi_karya AS a
                    LEFT JOIN db_employees.employees AS b ON (a.User_create = b.NIP)';
                    $sql .= ' ORDER BY a.ID DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
                }
            } else {
                $sql = 'SELECT a.Title, a.Citation, a.Year, a.User_create, b.Name, a.Server
                    FROM db_agregator.sitasi_karya AS a
                    LEFT JOIN db_employees.employees AS b ON (a.User_create = b.NIP)
                    WHERE a.Year = "' . $status . '" ';
                $sql .= 'ORDER BY a.ID DESC LIMIT ' . $requestData['start'] . ' ,' . $requestData['length'] . ' ';
            }
        }

        $query = $this->db->query($sql)->result_array();
        //print_r($sql); die();

        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {
            $nestedData = array();
            $row = $query[$i];

            if ($row["Server"] == "1") {
                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row["Name"] . '</div>';
                $nestedData[] = '<div style="text-align:left;"  class="text-primary">' . $row["Title"] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row["Citation"] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row["Year"] . '</div>';
            } else {
                $nestedData[] = '<div style="text-align:center;">' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row["Name"] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row["Title"] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row["Citation"] . '</div>';
                $nestedData[] = '<div style="text-align:center;">' . $row["Year"] . '</div>';
            }

            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval(count($totalData)),
            "recordsFiltered" => intval(count($totalData)),
            "data"            => $data
        );
        echo json_encode($json_data);
    }

    public function getAkreditasiProdi()
    {
        // get TypeHeader
        $rs = array();
        $header = array();
        $fill = array();
        $sql = 'select Type from db_academic.education_level Group by Type';
        $query = $this->db->query($sql, array())->result_array();
        for ($i = 0; $i < count($query); $i++) {
            $sql2 = 'select * from db_academic.education_level where Type = ? order by Name asc ';
            $query2 = $this->db->query($sql2, array($query[$i]['Type']))->result_array();
            $query[$i]['Detail'] = $query2;
        }

        $header = $query;

        // fill count
        $G_accreditation = $this->m_master->showData_array('db_academic.accreditation');
        for ($i = 0; $i < count($G_accreditation); $i++) {
            $AccreditationID = $G_accreditation[$i]['ID'];
            $AccreditationName = $G_accreditation[$i]['Label'];
            $temp2 = array(
                'AccreditationID' => $AccreditationID,
                'AccreditationName' => $AccreditationName,
                'TypeProgramStudy' => array(),
            );
            $temp3 = array();
            for ($j = 0; $j < count($query); $j++) {
                $TypeProgramStudy = $query[$j]['Type'];
                $temp3 = array(
                    'Name' => $TypeProgramStudy,
                    'Data' => array(),
                );

                $Detail = $query[$j]['Detail'];
                for ($k = 0; $k < count($Detail); $k++) {
                    $EducationLevelID = $Detail[$k]['ID'];
                    $EducationLevelName = $Detail[$k]['Name'];
                    $EducationLevelDesc = $Detail[$k]['Description'];
                    $EducationLevelDescEng = $Detail[$k]['DescriptionEng'];
                    // find sql
                    // $sql3 = 'select count(*) as Total from db_academic.program_study where EducationLevelID = ? and AccreditationID = ? ';
                    // $query3=$this->db->query($sql3, array($EducationLevelID,$AccreditationID))->result_array();

                    $sql3 = 'select * from db_academic.program_study where EducationLevelID = ? and AccreditationID = ? ';
                    $query3 = $this->db->query($sql3, array($EducationLevelID, $AccreditationID))->result_array();
                    $Tot = count($query3);
                    $token = $this->jwt->encode($query3, "UAP)(*");
                    $temp3['Data'][] = array(
                        'EducationLevelID' => $EducationLevelID,
                        'EducationLevelName' => $EducationLevelName,
                        'EducationLevelDesc' => $EducationLevelDesc,
                        'EducationLevelDescEng' => $EducationLevelDescEng,
                        'Count' => $Tot,
                        'data' => $token,
                    );
                }

                $temp2['TypeProgramStudy'][] = $temp3;
            }

            $fill[] = $temp2;
        }

        $rs['fill'] = $fill;
        $rs['header'] = $header;

        return print_r(json_encode($rs));
    }


    public function crudAgregator()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'updateTable') {

            $ID = ($data_arr['ID'] != '') ? $data_arr['ID'] : '';
            $table = $data_arr['table'];
            $dataForm = (array) $data_arr['dataForm'];
            $OldFile = '';

            if ($ID != '') {
                // Update
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('' . $table, $dataForm);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('' . $table, $dataForm);
                $ID = $this->db->insert_id();
            }


            return print_r(json_encode(array(
                'ID' => $ID,
                'File' => $OldFile
            )));
        } else if ($data_arr['action'] == 'readAgregatorAdmin') {
            $data = $this->db->query('SELECT aa.*, em.Name FROM db_agregator.agregator_admin aa
                                              LEFT JOIN db_employees.employees em ON (aa.NIP = em.NIP)')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removeAgregatorAdmin') {
            $ID = $data_arr['ID'];
            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.agregator_admin');

            return print_r(1);
        } else if ($data_arr['action'] == 'addAgregatorAdmin') {
            $NIP = $data_arr['NIP'];

            // Cek nip
            $data = $this->db->get_where('db_agregator.agregator_admin', array(
                'NIP' => $NIP
            ))->result_array();

            if (count($data) > 0) {
                $result = 0;
            } else {

                $dataIns = array(
                    'NIP' => $NIP
                );
                $this->db->insert('db_agregator.agregator_admin', $dataIns);

                $result = 1;
            }
            return print_r($result);
        } else if ($data_arr['action'] == 'readAgregatorHeaderMenu') {
            $data = $this->db->query('SELECT * FROM db_agregator.agregator_menu_header ORDER BY Type, Name ASC')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removeAgregatorHeaderMenu') {
            $ID = $data_arr['ID'];
            $this->db->where('ID', ID);
            $this->db->delete('db_agregator.agregator_menu_header');
            $this->db->reset_query();

            $this->db->where('MHID', $ID);
            $this->db->delete('db_agregator.agregator_menu');

            return print_r(1);
        } else if ($data_arr['action'] == 'updateAgregatorHeaderMenu') {
            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            if ($ID != '') {
                // Update
                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.agregator_menu_header', $dataForm);
            } else {
                $this->db->insert('db_agregator.agregator_menu_header', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'readAgregatorMenu') {
            $data = $this->db->query('SELECT am.*, amh.Name AS H_Name, amh.Type AS H_Type FROM db_agregator.agregator_menu am
                                                  LEFT JOIN db_agregator.agregator_menu_header amh ON (amh.ID = am.MHID)
                                                  ORDER BY am.MHID, am.ID ASC ')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'removedAgregatorMenu') {
            $ID = $data_arr['ID'];
            $this->db->where('ID', $ID);
            $this->db->delete('db_agregator.agregator_menu');

            return print_r(1);
        } else if ($data_arr['action'] == 'updateAgregatorMenu') {
            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            if ($ID != '') {
                // Update
                $this->db->where('ID', $ID);
                $this->db->update('db_agregator.agregator_menu', $dataForm);
            } else {
                $this->db->insert('db_agregator.agregator_menu', $dataForm);
            }

            return print_r(1);
        }
    }

    public function crudGroupStd()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'view_GS') {

            $ProdiID = $data_arr['ProdiID'];

            $data = $this->db->order_by('ID', 'ASC')->get_where('db_academic.prodi_group', array(
                'ProdiID' => $ProdiID
            ))->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'update_GS') {
            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            if ($ID != '') {
                // Update
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.prodi_group', $dataForm);
            } else {
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $this->db->insert('db_academic.prodi_group', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'viewStudent_GS') {
            $data = $this->db->select('ID,NPM, Name, ProdiGroupID')->get_where('db_academic.auth_students', array(
                'ProdiGroupID' => $data_arr['ProdiGroupID']
            ))->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'viewStudentNew_GS') {

            $data = $this->db->query('SELECT ID, NPM, Name, ProdiGroupID FROM db_academic.auth_students
                                          WHERE Year = "' . $data_arr['Year'] . '"
                                           AND ProdiID = "' . $data_arr['ProdiID'] . '"
                                            AND (ProdiGroupID IS NULL OR ProdiGroupID ="")')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateStudent_GS') {

            $arrID = (array) $data_arr['arrID'];

            for ($i = 0; $i < count($arrID); $i++) {

                // Update
                $this->db->where('ID', $arrID[$i]);
                $this->db->update('db_academic.auth_students', array(
                    'ProdiGroupID' => $data_arr['ProdiGroupID']
                ));
                $this->db->reset_query();

                // get nip
                $dataN = $this->db->select('NPM')->get_where('db_academic.auth_students', array(
                    'ID' => $arrID[$i]
                ))->result_array();

                $this->db->insert('db_academic.prodi_group_log', array(
                    'NPM' => $dataN[0]['NPM'],
                    'ProdiGroupID' => $data_arr['ProdiGroupID'],
                    'Status' => 'in',
                    'UpdatedBy' => $this->session->userdata('NIP')
                ));
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'removeFMGrStudent_GS') {

            $this->db->where('ID', $data_arr['ID']);
            $this->db->update('db_academic.auth_students', array(
                'ProdiGroupID' => ''
            ));

            $this->db->reset_query();

            $this->db->insert('db_academic.prodi_group_log', array(
                'NPM' => $data_arr['NPM'],
                'ProdiGroupID' => $data_arr['ProdiGroupID'],
                'Status' => 'out',
                'UpdatedBy' => $this->session->userdata('NIP')
            ));


            return print_r(1);
        }
    }


    public function crudCheckDataKRS()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'checkDataKRS') {

            $Year = $data_arr['Year'];
            $SemesterID = $data_arr['SemesterID'];

            $db = 'ta_' . $Year;

            $dataStd = $this->db->query('SELECT s.NPM, s.Name FROM  ' . $db . '.students s
                                                ORDER BY s.NPM ASC ')->result_array();

            $result = [];
            if (count($dataStd) > 0) {

                for ($i = 0; $i < count($dataStd); $i++) {

                    // KRS Approve
                    $dataSP = $this->db->query('SELECT sp.ID, sp.ScheduleID, sch.ClassGroup FROM ' . $db . '.study_planning sp
                                                LEFT JOIN db_academic.schedule sch ON (sch.ID = sp.ScheduleID)
                                                WHERE sp.SemesterID = ' . $SemesterID . '
                                                AND sp.NPM = "' . $dataStd[$i]['NPM'] . '"
                                                ORDER BY sp.ScheduleID ASC ')->result_array();

                    // KRS Online
                    $dataKO = $this->db->query('SELECT sk.ID, sk.ScheduleID, sch.ClassGroup FROM db_academic.std_krs sk
                                                LEFT JOIN db_academic.schedule sch ON (sch.ID = sk.ScheduleID)
                                                WHERE sk.SemesterID = ' . $SemesterID . '
                                                AND sk.NPM = "' . $dataStd[$i]['NPM'] . '"
                                                AND sk.Status = "3"
                                                ORDER BY sk.ScheduleID ASC ')->result_array();



                    if (count($dataSP) != count($dataKO)) {

                        $dataStd[$i]['A'] = $dataSP;
                        $dataStd[$i]['B'] = $dataKO;
                        array_push($result, $dataStd[$i]);
                    }
                }
            }

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'removeRedundancy') {

            $Year = $data_arr['Year'];
            $NPM = $data_arr['NPM'];
            $SemesterID = $data_arr['SemesterID'];
            $ScheduleID = $data_arr['ScheduleID'];

            $db = 'ta_' . $Year . '.study_planning';

            // Cek apakah double
            $data = $this->db->query('SELECT sp.ID FROM ' . $db . ' sp WHERE sp.SemesterID = "' . $SemesterID . '"
                                                AND sp.NPM = "' . $NPM . '"
                                                 AND sp.ScheduleID = "' . $ScheduleID . '" ')->result_array();

            $result = array(
                'Status' => '0'
            );

            if (count($data) > 1) {


                // Get ID Attendance
                $dataAttd = $this->db->select('ID')->get_where('db_academic.attendance', array(
                    'SemesterID' => $SemesterID,
                    'ScheduleID' => $ScheduleID
                ))->result_array();

                if (count($dataAttd) > 0) {
                    for ($i = 0; $i < count($dataAttd); $i++) {
                        $this->db->where(array(
                            'ID_Attd' => $dataAttd[$i]['ID'],
                            'NPM' => $NPM
                        ));
                        $this->db->delete('db_academic.attendance_students');
                    }
                }

                // Remove di SP
                $this->db->where('ID', $data_arr['SPID']);
                $this->db->delete($db);

                $result = array(
                    'Status' => '1'
                );
            }

            return print_r(json_encode($result));
        }
    }

    public function crudYudisium()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'viewYudisiumList') {

            $requestData = $_REQUEST;

            $SemesterID = $data_arr['SemesterID'];
            $AS = (isset($data_arr['AS'])) ? $data_arr['AS'] : '';

            $ProdiID = (isset($data_arr['ProdiID']) && $data_arr['ProdiID'] != '') ? $data_arr['ProdiID'] : '';
            $WhereProdi = ($ProdiID != '') ? ' AND ats.ProdiID = "' . $ProdiID . '" ' : '';

            $WhereStatusTA = '';
            if (isset($data_arr['StatusTA'])) {
                $stbSts = substr($data_arr['StatusTA'], 0, 1);
                $stbStsVal = substr($data_arr['StatusTA'], 2, 1);

                if ($stbSts == 'l') {
                    $WhereStatusTA = ($stbStsVal == '0')
                        ? ' AND ( fpc.Cl_Library = "' . $stbStsVal . '" OR fpc.Cl_Library  IS NULL OR fpc.Cl_Library  = "")'
                        : ' AND fpc.Cl_Library = "' . $stbStsVal . '" ';
                } else if ($stbSts == 'f') {
                    $WhereStatusTA = ($stbStsVal == '0')
                        ? ' AND ( fpc.Cl_Finance = "' . $stbStsVal . '" OR fpc.Cl_Finance  IS NULL OR fpc.Cl_Finance  = "")'
                        : ' AND fpc.Cl_Finance = "' . $stbStsVal . '" ';
                } else if ($stbSts == 's') {
                    $WhereStatusTA = ($stbStsVal == '0')
                        ? ' AND ( fpc.Cl_StdLife = "' . $stbStsVal . '" OR fpc.Cl_StdLife  IS NULL OR fpc.Cl_StdLife  = "")'
                        : ' AND fpc.Cl_StdLife = "' . $stbStsVal . '" ';
                } else if ($stbSts == 'k') {
                    $WhereStatusTA = ($stbStsVal == '0')
                        ? ' AND ( fpc.Cl_Kaprodi = "' . $stbStsVal . '" OR fpc.Cl_Kaprodi  IS NULL OR fpc.Cl_Kaprodi  = "")'
                        : ' AND fpc.Cl_Kaprodi = "' . $stbStsVal . '" ';
                } else if ($stbSts == 'a') {
                    $WhereStatusTA = ($stbStsVal == '0')
                        ? ' AND ( fpc.Cl_Academic = "' . $stbStsVal . '" OR fpc.Cl_Academic IS NULL OR fpc.Cl_Academic = "")'
                        : ' AND fpc.Cl_Academic = "' . $stbStsVal . '" ';
                } else if ($stbSts == 'i') {

                    $WhereStatusTA = ($stbStsVal == '0')
                        ? ' AND ( dm.Attachment IS NULL OR dm.Attachment = "") '
                        : ' AND ( dm.Attachment IS NOT NULL OR dm.Attachment != "") ';
                }
            }




            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' AND (  ats.Name LIKE "%' . $search . '%"
                                OR ats.NPM LIKE "%' . $search . '%" )';
            }

            $queryDefault = 'SELECT ssp.*, ats.Name AS StudentName, mk.MKCode,
                                        mk.NameEng AS CourseEng, sc.ClassGroup,
                                        fpc.Cl_Library, fpc.Cl_Library_By, fpc.Cl_Library_At, em1.Name AS Cl_Library_Name,
                                        fpc.Cl_Finance, fpc.Cl_Finance_By, fpc.Cl_Finance_At, em2.Name AS Cl_Finance_Name,
                                        fpc.Cl_StdLife, fpc.Cl_StdLife_By, fpc.Cl_StdLife_At, em7.Name AS Cl_StdLife_Name,
                                        fpc.Cl_Kaprodi, fpc.Cl_Kaprodi_By, fpc.Cl_Kaprodi_At, em3.Name AS Cl_Kaprodi_Name,
                                        fpc.Cl_Academic, fpc.Cl_Academic_By, fpc.Cl_Academic_At, em6.Name AS Cl_Academic_Name,
                                        ats.MentorFP1, em4.Name AS MentorFP1Name, ats.MentorFP2, em5.Name AS MentorFP2Name,
                                        ats.ID AS AUTHID, dm.Attachment AS IjazahSMA,
                                        fpn.finance AS Note_Finance,fpn.UploadFile as NoteUploadFile
                                        FROM db_academic.std_study_planning ssp
                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssp.MKID)
                                        LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssp.NPM)
                                        LEFT JOIN db_academic.schedule sc ON (sc.ID = ssp.ScheduleID)

                                        LEFT JOIN db_employees.employees em4 ON (ats.MentorFP1 = em4.NIP)
                                        LEFT JOIN db_employees.employees em5 ON (ats.MentorFP2 = em5.NIP)

                                        LEFT JOIN db_academic.final_project_clearance fpc ON (fpc.NPM = ats.NPM)
                                        LEFT JOIN db_academic.final_project_note fpn ON (fpn.NPM = ats.NPM)

                                        LEFT JOIN db_employees.employees em1 ON (fpc.Cl_Library_By = em1.NIP)
                                        LEFT JOIN db_employees.employees em2 ON (fpc.Cl_Finance_By = em2.NIP)
                                        LEFT JOIN db_employees.employees em3 ON (fpc.Cl_Kaprodi_By = em3.NIP)
                                        LEFT JOIN db_employees.employees em6 ON (fpc.Cl_Academic_By = em6.NIP)
                                        LEFT JOIN db_employees.employees em7 ON (fpc.Cl_StdLife_By = em7.NIP)
                                        LEFT JOIN db_admission.doc_mhs dm ON (dm.NPM = ats.NPM AND dm.ID_reg_doc_checklist = 3)

                                        WHERE mk.Yudisium = "1" AND ssp.SemesterID = "' . $SemesterID . '" ' . $WhereProdi . $WhereStatusTA . $dataSearch . ' GROUP BY ats.NPM';

            $queryDefaultTotal = 'SELECT COUNT(*) Total FROM (SELECT ats.NPM FROM db_academic.std_study_planning ssp
                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssp.MKID)
                                        LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssp.NPM)
                                        WHERE mk.Yudisium = "1" AND ssp.SemesterID = "' . $SemesterID . '" ' . $WhereProdi . $WhereStatusTA . $dataSearch . ' GROUP BY ats.NPM) xx';


            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $StudentName = $row['StudentName'];

                // ========== GET TRANSCRIPT =========

                $dataTranscript = $this->m_rest->getTranscript($row['ClassOf'], $row['NPM'], 'ASC');
                $dataIPK = $dataTranscript['dataIPK'];

                $TotalSKS = ($dataIPK['TotalSKS'] > 0) ? '<a href="javascript:void(0);" class="btnFinalProject_ViewDetailMK" data-title="' . $row['NPM'] . ' ' . $StudentName . ' - Total Credit" data-token="' . $this->jwt->encode($dataTranscript['dataCourse'], "UAP)(*") . '">' . $dataIPK['TotalSKS'] . '</a>' : '0';
                $data_mkD = (count($dataIPK['MK_D']) > 0) ? '<a href="javascript:void(0)" class="btnFinalProject_ViewDetailMK" data-title="' . $row['NPM'] . ' ' . $StudentName . ' - Course D" data-token="' . $this->jwt->encode($dataIPK['MK_D'], "UAP)(*") . '" >' . count($dataIPK['MK_D']) . '</a>' : '0';
                $arr_mkWajib_SKS = ($dataIPK['MK_Wajib_SKS'] > 0) ? '<a href="javascript:void(0)" class="btnFinalProject_ViewDetailMK" data-title="' . $row['NPM'] . ' ' . $StudentName . ' - Credit Course Required" data-token="' . $this->jwt->encode($dataIPK['MK_Wajib'], "UAP)(*") . '" >' . $dataIPK['MK_Wajib_SKS'] . '</a>' : '0';

                // ==================================

                $DeptID = $this->session->userdata('IDdepartementNavigation');

                // Ijazah
                $ijazahBtnD = ($row['IjazahSMA'] != null && $row['IjazahSMA'] != '')
                    ? '<a href="' . base_url('uploads/document/' . $row['NPM'] . '/' . $row['IjazahSMA']) . '" target="_blank"><i class="fa fa-download"></i> Download</a>'
                    : 'Waiting Upload';
                if ($AS != 'Prodi' && ($DeptID == '6' || $DeptID == 6)) {
                    $fileIjazahOld = ($row['IjazahSMA'] != null && $row['IjazahSMA'] != '') ? $row['IjazahSMA'] : '';

                    $ijazah = '<form id="formupload_files_' . $row['AUTHID'] . '" enctype="multipart/form-data" accept-charset="utf-8" method="post" action="">
                                <div class="form-group"><label class="btn btn-sm btn-default btn-upload">
                                        Upload Ijazah
                                        <input type="file" name="userfile" class="uploadIjazahStudentFile" data-old="' . $fileIjazahOld . '" data-npm="' . $row['NPM'] . '" data-id="' . $row['AUTHID'] . '" id="upload_files_' . $row['AUTHID'] . '" accept="application/pdf" style="display: none;">
                                    </label>
                                </div>
                        </form><hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $ijazahBtnD;
                } else {
                    $ijazah = $ijazahBtnD;
                }

                // Edit Mentor Final Project
                $m1 = ($row['MentorFP1'] != null && $row['MentorFP1'] != '') ? $row['MentorFP1'] : '';
                $m2 = ($row['MentorFP2'] != null && $row['MentorFP2'] != '') ? $row['MentorFP2'] : '';

                $btnCrudPembimbing = ($AS == 'Prodi' || ($DeptID == '6' || $DeptID == 6))
                    ? '<div style="margin-bottom: 10px;">
                    <button class="btn btn-sm btn-default btnAddMentor" id="btnAddMentor_' . $row['NPM'] . '" data-npm="' . $row['NPM'] . '"
                data-std="' . $row['NPM'] . ' - ' . $row['StudentName'] . '" data-id="' . $row['AUTHID'] . '"
                data-m1="' . $m1 . '" data-m1-name="' . $row['MentorFP1'] . ' - ' . $row['MentorFP1Name'] . '" data-m2="' . $m2 . '" data-m2-name="' . $row['MentorFP2'] . ' - ' . $row['MentorFP2Name'] . '">Edit Mentor Final Project</button></div>' : '';


                // Academic
                $dateTm = ($row['Cl_Academic_At'] != '' && $row['Cl_Academic_At'] != null) ? ' <div style="color: #9e9e9e;">' . date('d M Y H:i', strtotime($row['Cl_Academic_At'])) . '</div>' : '';
                if ($AS != 'Prodi' && ($DeptID == '6' || $DeptID == 6)) {

                    $c_Academic = ($row['Cl_Academic'] != null && $row['Cl_Academic'] != '' && $row['Cl_Academic'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                        <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_Academic_Name'] . '' . $dateTm
                        : '<button class="btn btn-sm btn-success btnClearnt" data-npm="' . $row['NPM'] . '" data-c="Cl_Academic">Clearance</button>';

                    $c_Academic = ($row['IjazahSMA'] != null && $row['IjazahSMA'] != '')
                        ? $c_Academic
                        : '<span style="color:#ff9800; ">Waiting Upload Ijazah</span>';
                } else {
                    $c_Academic = ($row['Cl_Academic'] != null && $row['Cl_Academic'] != '' && $row['Cl_Academic'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                        <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_Academic_Name'] . '' . $dateTm
                        : 'Waiting Academic Clearance';
                }


                // Library
                $dateTm = ($row['Cl_Library_At'] != '' && $row['Cl_Library_At'] != null) ? ' <div style="color: #9e9e9e;">' . date('d M Y H:i', strtotime($row['Cl_Library_At'])) . '</div>' : '';
                if ($AS != 'Prodi' && ($DeptID == '11' || $DeptID == 11)) {
                    $c_Library = ($row['Cl_Library'] != null && $row['Cl_Library'] != '' && $row['Cl_Library'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                        <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_Library_Name'] . '' . $dateTm
                        : '<button class="btn btn-sm btn-success btnClearnt" data-npm="' . $row['NPM'] . '" data-c="Cl_Library">Clearance</button>';

                    $CheckDataFile = $this->db->get_where('db_academic.final_project_files', array('NPM' => $row['NPM']))->result_array();
                    if (count($CheckDataFile) > 0) {
                        if ($CheckDataFile[0]['Status'] == '0') {
                            $c_Library = '<span style="color: #ff9800;">Not yet sending the final project document</span>';
                        } else if ($CheckDataFile[0]['Status'] == '1') {
                            $c_Library = '<span style="color: blue;">Awaiting action by library staff</span>';
                        } else if ($CheckDataFile[0]['Status'] == '-2') {
                            $c_Library = '<span style="color: darkred;">The final project document rejected</span>';
                        }
                    } else {
                        $c_Library = '<span style="color: #ff9800;">Not yet sending the final project document</span>';
                    }
                } else {
                    $c_Library = ($row['Cl_Library'] != null && $row['Cl_Library'] != '' && $row['Cl_Library'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                        <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_Library_Name'] . '' . $dateTm
                        : 'Waiting Library Clearance';
                }



                // Finance
                $dateTm = ($row['Cl_Finance_At'] != '' && $row['Cl_Finance_At'] != null) ? ' <div style="color: #9e9e9e;">' . date('d M Y H:i', strtotime($row['Cl_Finance_At'])) . '</div>' : '';

                if ($AS != 'Prodi' && ($DeptID == '9' || $DeptID == 9)) {

                    $NoteUploadFile =  (!empty($row['NoteUploadFile'])) ? $row['NoteUploadFile'] : ''; // for field UploadFile in final_project_note

                    $btnShowFileFinance = (!empty($row['NoteUploadFile'])) ? '<a target="_blank" href="' . base_url('uploads/document/' . $row['NPM'] . '/' . $NoteUploadFile) . '">Show File</a>' : '';

                    $v_note_finance = ($row['Note_Finance'] != '' && $row['Note_Finance'] != null) ? '<textarea class="form-control" style="color: #333;" id="finance_viewValueNote_' . $row['NPM'] . '" readonly>' . $row['Note_Finance'] . '</textarea><hr style="margin-bottom: 5px;margin-top: 5px;"/>' : '';

                    $c_Finance = ($row['Cl_Finance'] != null && $row['Cl_Finance'] != '' && $row['Cl_Finance'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                        <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_Finance_Name'] . '' . $dateTm . $v_note_finance . $btnShowFileFinance

                        : '<button class="btn btn-sm btn-default btnClearnt" data-npm="' . $row['NPM'] . '" data-c="Cl_Finance">Clearance</button><hr style="margin-top: 10px;margin-bottom: 7px;" /><div style="text-align: left;" id="finance_viewNote_' . $row['NPM'] . '">' . $v_note_finance . '</div><a href="javascript:void(0);" class="btnNote" data-dept="finance" data-npm="' . $row['NPM'] . '" thefile = "' . $NoteUploadFile . '"><i class="fa fa-edit"></i> Note</a>';
                } else {
                    $c_Finance = ($row['Cl_Finance'] != null && $row['Cl_Finance'] != '' && $row['Cl_Finance'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                        <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_Finance_Name'] . '' . $dateTm
                        : 'Waiting Finance Clearance';
                }



                // Student Life
                $dateTm = ($row['Cl_StdLife_At'] != '' && $row['Cl_StdLife_At'] != null) ? ' <div style="color: #9e9e9e;">' . date('d M Y H:i', strtotime($row['Cl_StdLife_At'])) . '</div>' : '';
                if ($AS != 'Prodi' && ($DeptID == '16' || $DeptID == 16)) {
                    $c_StdLife = ($row['Cl_StdLife'] != null && $row['Cl_StdLife'] != '' && $row['Cl_StdLife'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                        <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_StdLife_Name'] . '' . $dateTm
                        : '<button class="btn btn-sm btn-default btnClearnt" data-npm="' . $row['NPM'] . '" data-c="Cl_StdLife">Clearance</button>';
                } else {
                    $c_StdLife = ($row['Cl_StdLife'] != null && $row['Cl_StdLife'] != '' && $row['Cl_StdLife'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                        <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_StdLife_Name'] . '' . $dateTm
                        : 'Waiting Student Life Clearance';
                }


                // kaprodi
                $dateTm = ($row['Cl_Kaprodi_At'] != '' && $row['Cl_Kaprodi_At'] != null)
                    ? ' <div style="color: #9e9e9e;">' . date('d M Y H:i', strtotime($row['Cl_Kaprodi_At'])) . '</div>'
                    : '';

                if ($ProdiID != '') {
                    $c_Kaprodi = ($row['Cl_Kaprodi'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                    <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_Kaprodi_Name'] . '' . $dateTm
                        : '<div style="margin-bottom: 10px;">Register to be a judiciums participant</div><button class="btn btn-sm btn-success btnClearnt" data-npm="' . $row['NPM'] . '" data-c="Cl_Kaprodi">Register now</button>';
                } else {
                    $c_Kaprodi = ($row['Cl_Kaprodi'] != '0') ? '<i class="fa fa-check-circle" style="color: darkgreen;"></i>
                    <hr style="margin-top: 7px;margin-bottom: 3px;"/>' . $row['Cl_Kaprodi_Name'] . '' . $dateTm
                        : 'Waiting Approval Kaprodi';
                }



                $c_Kaprodi = ($row['Cl_Academic'] != '0' && $row['Cl_Academic'] != null && $row['Cl_Academic'] != '' &&
                    $row['Cl_StdLife'] != '0' && $row['Cl_StdLife'] != null && $row['Cl_StdLife'] != '' &&
                    $row['Cl_Finance'] != '0' && $row['Cl_Finance'] != null && $row['Cl_Finance'] != '' &&
                    $row['Cl_Library'] != '0' && $row['Cl_Library'] != null && $row['Cl_Library'] != '' &&
                    $row['IjazahSMA'] != null && $row['IjazahSMA'] != '')
                    ? $c_Kaprodi : '<span style="font-size: 11px;">Waiting Ijazah Uploaded and Clearance (Academic, Library, Finance & Student Life)</span>';




                $m1Name = ($row['MentorFP1'] != null && $row['MentorFP1'] != '') ? '<div style="color: royalblue;">' . $row['MentorFP1'] . ' - ' . $row['MentorFP1Name'] . '</div>' : '';
                $m2Name = ($row['MentorFP2'] != null && $row['MentorFP2'] != '') ? '<div style="color: royalblue;">' . $row['MentorFP2'] . ' - ' . $row['MentorFP2Name'] . '</div>' : '';

                $dataInformation = '<div style="text-align: left;font-size: 12px;border-top: 1px solid #cccccc;margin-top: 10px;padding-top: 10px;">Total Credit : ' . $TotalSKS .
                    '<br/>Course "D" : ' . $data_mkD .
                    '<br/>Credit Course Req. : ' . $arr_mkWajib_SKS . '</div>';

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;"><b>' . $StudentName . '</b><br/>' . $row['NPM'] . '</div>' . $dataInformation;
                $nestedData[] = '<div style="text-align:left;font-size: 12px;border-bottom: 1px solid #cccccc;padding-bottom: 10px;margin-bottom: 10px;">' . $row['CourseEng'] . '<br/>' . $row['MKCode'] . ' | Group : ' . $row['ClassGroup'] . '</div>
                                        ' . $btnCrudPembimbing . '<div style="text-align:left;" id="viewMentor_' . $row['AUTHID'] . '">' . $m1Name . '' . $m2Name . '</div>';
                $nestedData[] = '<div>' . $ijazah . '</div>';
                $nestedData[] = '<div>' . $c_Academic . '</div>';
                $nestedData[] = '<div>' . $c_Library . '</div>';
                $nestedData[] = '<div>' . $c_Finance . '</div>';
                $nestedData[] = '<div>' . $c_StdLife . '</div>';
                $nestedData[] = '<div>' . $c_Kaprodi . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'viewYudisiumSchedule') {

            $requestData = $_REQUEST;


            $ProdiID = (isset($data_arr['ProdiID']) && $data_arr['ProdiID'] != '') ? $data_arr['ProdiID'] : '';
            $WhereProdi = ($ProdiID != '') ? ' AND ats.ProdiID = "' . $ProdiID . '" ' : '';

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' AND (  ats.Name LIKE "%' . $search . '%"
                                OR ats.NPM LIKE "%' . $search . '%" )';
            }

            $AsKaprodi = $data_arr['AsKaprodi'];
            $NIP = $data_arr['NIP'];
            $whereNonKaprodi = ($AsKaprodi == '0') ? ' AND (ats.MentorFP1 = "' . $NIP . '" OR ats.MentorFP2 = "' . $NIP . '") ' : '';
            $whereSemesterID = ($AsKaprodi == '1') ? ' AND ssp.SemesterID = ' . $data_arr['SemesterID'] . ' ' : '';


            $queryDefault = 'SELECT ssp.*, ats.Name AS StudentName, mk.MKCode,
                                        mk.NameEng AS CourseEng, sc.ClassGroup,
                                        ats.MentorFP1, em4.Name AS MentorFP1Name, ats.MentorFP2, em5.Name AS MentorFP2Name,
                                        ats.ID AS AUTHID
                                        FROM db_academic.std_study_planning ssp
                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = ssp.MKID)
                                        LEFT JOIN db_academic.auth_students ats ON (ats.NPM = ssp.NPM)
                                        LEFT JOIN db_academic.schedule sc ON (sc.ID = ssp.ScheduleID)
                                        LEFT JOIN db_employees.employees em4 ON (ats.MentorFP1 = em4.NIP)
                                        LEFT JOIN db_employees.employees em5 ON (ats.MentorFP2 = em5.NIP)
                                        WHERE mk.Yudisium = "1" ' . $whereSemesterID . $whereNonKaprodi . $WhereProdi . $dataSearch . ' GROUP BY ssp.NPM';

            //            print_r($queryDefault);exit;

            $queryDefaultTotal = 'SELECT COUNT(*) Total FROM (' . $queryDefault . ') xx';

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];


                $m1Name = ($row['MentorFP1'] != null && $row['MentorFP1'] != '') ? '<div style="color: royalblue;">' . $row['MentorFP1'] . ' - ' . $row['MentorFP1Name'] . '</div>' : '';
                $m2Name = ($row['MentorFP2'] != null && $row['MentorFP2'] != '') ? '<div style="color: royalblue;">' . $row['MentorFP2'] . ' - ' . $row['MentorFP2Name'] . '</div>' : '';

                $dataSchedule = $this->db->query('SELECT fps.ID, fps.Date, fps.Start, fps.End, cl.Room FROM db_academic.final_project_schedule_student fpss
                                                                 LEFT JOIN db_academic.final_project_schedule fps ON (fps.ID = fpss.FPSID)
                                                                 LEFT JOIN db_academic.classroom cl ON (cl.ID = ClassroomID)
                                                                 WHERE fpss.NPM = "' . $row['NPM'] . '"')->result_array();

                $rowSchedule = '';
                if (count($dataSchedule) > 0) {
                    for ($s = 0; $s < count($dataSchedule); $s++) {
                        $d = $dataSchedule[$s];

                        // Get Examiner
                        $dataEx = $this->db->query('SELECT em.Name FROM db_academic.final_project_schedule_lecturer fpsl
                                                                            LEFT JOIN db_employees.employees em ON (em.NIP = fpsl.NIP)
                                                                            WHERE fpsl.FPSID = ' . $d['ID'] . '
                                                                            ORDER BY fpsl.Type DESC, fpsl.NIP ASC ')->result_array();

                        $ListEx = '';
                        if (count($dataEx) > 0) {
                            foreach ($dataEx as $key => $item) {
                                $koma = ($key != 0) ? ', ' : '';
                                $ListEx = $ListEx . '' . $koma . $item['Name'];
                            }
                        } else {
                            $ListEx = '<span style="color: darkred;">Not set</span>';
                        }

                        // Get Participant
                        $dataPar = $this->db->query('SELECT ats.Name, ats.NPM FROM db_academic.final_project_schedule_student fpss
                                                                   LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fpss.NPM)
                                                                   WHERE fpss.FPSID = ' . $d['ID'] . ' AND fpss.NPM != "' . $row['NPM'] . '"
                                                                    ORDER BY fpss.NPM ASC ')->result_array();

                        $ListPar = '';
                        if (count($dataPar) > 0) {
                            foreach ($dataPar as $key => $item) {
                                $koma = ($key != 0) ? ', ' : '';
                                $ListPar = $ListPar . '' . $koma . $item['NPM'] . ' - ' . $item['Name'];
                            }
                        } else {
                            $ListPar = '-';
                        }

                        $date = date('l, d F Y', strtotime($d['Date']));
                        $time = substr($d['Start'], 0, 5) . ' - ' . substr($d['End'], 0, 5);
                        $tbSch = '<table class="table table-left table-sch">
                                <tr>
                                    <td style="width: 20%;">Date</td>
                                    <td style="width: 5%;">:</td>
                                    <td>' . $date . ' ' . $time . '</td>
                                </tr>
                                <tr>
                                    <td>Room</td>
                                    <td>:</td>
                                    <td>' . $d['Room'] . '</td>
                                </tr>
                                <tr>
                                    <td>Examiner</td>
                                    <td>:</td>
                                    <td>' . $ListEx . '</td>
                                </tr>
                                <tr>
                                    <td>Participants</td>
                                    <td>:</td>
                                    <td>' . $ListPar . '</td>
                                </tr>
                            </table>';
                        $rowSchedule = $rowSchedule . '<div class="div-sch">' . $tbSch . '</div>';
                    }
                }

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;"><b>' . $row['StudentName'] . '</b><br/>' . $row['NPM'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $m1Name . '' . $m2Name . '</div>';
                $nestedData[] = '<div style="text-align: left;">' . $rowSchedule . '</div>';


                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'viewYudisiumLecturer') {

            $requestData = $_REQUEST;

            $WhereProdiID = ($data_arr['ProdiID'] != '') ? ' AND em.ProdiID = "' . $data_arr['ProdiID'] . '" ' : '';
            $LecturerStatus = $data_arr['LecturerStatus'];

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' AND (  em.Name LIKE "%' . $search . '%"
                                OR em.NIP LIKE "%' . $search . '%" )';
            }

            $queryDefault = 'SELECT em.NIP, em.Name, ps.Name AS Prodi, emps.Description AS StatusLecturer FROM db_employees.employees em
                                                  LEFT JOIN db_academic.program_study ps ON (ps.ID = em.ProdiID)
                                                  LEFT JOIN db_employees.employees_status emps ON (emps.IDStatus = em.StatusLecturerID)
                                                  WHERE  em.StatusLecturerID = "' . $LecturerStatus . '" ' . $WhereProdiID . ' ' . $dataSearch;

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];


                // Get student
                $dataStdFP1 = $this->db->query('SELECT ats.NPM, ats.Name FROM db_academic.auth_students ats
                                                            WHERE ats.MentorFP1 = "' . $row['NIP'] . '" ORDER BY ats.NPM ASC')->result_array();

                $stdFP1 = '';
                if (count($dataStdFP1) > 0) {
                    foreach ($dataStdFP1 as $itm1) {
                        $stdFP1 = $stdFP1 . '<span class="std">' . $itm1['NPM'] . ' - ' . ucwords(strtolower($itm1['Name'])) . '</span>';
                    }
                }


                $dataStdFP2 = $this->db->query('SELECT ats.NPM, ats.Name FROM db_academic.auth_students ats
                                                            WHERE ats.MentorFP2 = "' . $row['NIP'] . '" ORDER BY ats.NPM ASC')->result_array();

                $stdFP2 = '';
                if (count($dataStdFP2) > 0) {
                    foreach ($dataStdFP2 as $itm2) {
                        $stdFP2 = $stdFP2 . '<span class="std2">' . $itm2['NPM'] . ' - ' . ucwords(strtolower($itm2['Name'])) . '</span>';
                    }
                }

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b>
                                                        <p class="help-block"><span style="color: cornflowerblue">' . $row['Prodi'] . '</span><br/>' . $row['StatusLecturer'] . '</p></div>';
                $nestedData[] = '<div style="text-align: left;">' . $stdFP1 . '<hr style="margin-top: 9px;margin-bottom: 9px;" />' . $stdFP2 . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval(count($queryDefaultRow)),
                "recordsFiltered" => intval(count($queryDefaultRow)),
                "data"            => $data
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'getJudiciumsYear') {

            $data = $this->db->query('SELECT j.* FROM db_academic.judiciums j ORDER BY j.ID DESC')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateClearent') {

            $NPM = $data_arr['NPM'];
            $C = $data_arr['C'];
            $NIP = (isset($data_arr['NIP'])) ? $data_arr['NIP'] : '';



            $arr = array(
                'NPM' => $NPM,
                $C => '1',
                $C . '_By' => ($NIP != '') ? $NIP : $this->session->userdata('NIP'),
                $C . '_At' => $this->m_rest->getDateTimeNow()
            );


            $lanjutInsert = false;
            $result = array(
                'Status' => 0
            );

            if ($C == 'Cl_Kaprodi') {
                $SemesterActive = $this->m_rest->_getSemesterActive();
                $SemesterID = $SemesterActive['SemesterID'];
                $arr['SemesterID'] = $SemesterID;

                // Cek apakah ada Judicium active
                $checkJudiciums = $this->db->limit(1)->order_by('ID', 'DESC')->get_where('db_academic.judiciums', array(
                    'Publish' => '1'
                ))->result_array();

                if (count($checkJudiciums) > 0) {

                    $d = $checkJudiciums[0];
                    $result = array(
                        'Status' => 1
                    );
                    $lanjutInsert = true;

                    // Get nomor judiciums
                    $dataNoSKPI = $this->db->select('NoSKPI')->order_by('ID', 'DESC')->limit(1)->get_where('db_academic.judiciums_list', array('JID' => $d['ID']))->result_array();

                    $NoSKPI = 1;
                    if (count($dataNoSKPI) > 0) {
                        $NoSKPI = $dataNoSKPI[0]['NoSKPI'] + 1;
                    }

                    $this->db->insert('db_academic.judiciums_list', array(
                        'JID' => $d['ID'],
                        'NPM' => $NPM,
                        'NoSKPI' => $NoSKPI
                    ));
                } else {
                    $result = array(
                        'Status' => 0,
                        'Message' => 'Active Judicium does not exist, please contact academic service'
                    );
                }
            } else {
                $lanjutInsert = true;
                $result = array(
                    'Status' => 1
                );
            }


            if ($lanjutInsert == true) {
                $dataCk = $this->db->get_where(
                    'db_academic.final_project_clearance',
                    array(
                        'NPM' => $NPM
                    )
                )->result_array();

                if (count($dataCk) > 0) {
                    $this->db->where('ID', $dataCk[0]['ID']);
                    $this->db->update('db_academic.final_project_clearance', $arr);
                } else {
                    $this->db->insert('db_academic.final_project_clearance', $arr);
                }
            }

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'updateNotetoClearent') {
            $rs = ['status' => 1, 'msg' => '', 'callback' => []];
            $dataForm = [];
            if (array_key_exists('userfile', $_FILES)) {
                $selectorFile = $_FILES['userfile']['name'][0];
                $ext = pathinfo($selectorFile, PATHINFO_EXTENSION);
                $filename = $data_arr['Dept'] . '_viewnote' . $data_arr['NPM'] . '.' . $ext;
                if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                    $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;

                    $path = 'document/' . $data_arr['NPM'];
                    $TheFile = 'userfile';
                    $upload = $this->m_master->UploadManyFilesToNas($headerOrigin, $filename, $TheFile, $path, 'array');
                    $dataForm['UploadFile'] = $upload[0];
                } else {
                    $pathStudent = './uploads/document/' . $data_arr['NPM'];
                    if (!file_exists($pathStudent)) {
                        mkdir($pathStudent, 0777, true);
                    }
                    $upload = $this->m_master->uploadDokumenSetFileName($filename, 'userfile', $pathStudent);
                    $dataForm['UploadFile'] = $upload[0];
                }

                $rs['callback']['filename'] =  $dataForm['UploadFile'];
            }

            // Cek apakah sudah ada atau blm
            $Dept = $data_arr['Dept'];
            $NPM = $data_arr['NPM'];
            $dataCk = $this->db->select('ID')->get_where('db_academic.final_project_note', array(
                'NPM' => $NPM
            ))->result_array();

            $dataForm = $dataForm +  array(
                'NPM' => $NPM,
                $Dept => $data_arr['Note'],
                $Dept . 'At' => $data_arr['DateTime'],
                $Dept . 'By' => $data_arr['User']
            );

            if (count($dataCk) > 0) {
                // Update
                $this->db->where('ID', $dataCk[0]['ID']);
                $this->db->update('db_academic.final_project_note', $dataForm);
            } else {
                // Insert
                $this->db->insert('db_academic.final_project_note', $dataForm);
            }

            return print_r(json_encode($rs));
        } elseif ($data_arr['action'] == 'delete_fileNotetoClearent') {
            $rs = ['status' => 1, 'msg' => '', 'callback' => []];
            $NPM = $data_arr['NPM'];
            $data = $this->db->where('NPM', $NPM)->get('db_academic.final_project_note');
            if ($data) {
                $datas = $data->row();
                if (!empty($datas->UploadFile)) {
                    if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                        $headerOrigin = serverRoot;
                        $p = $this->m_master->DeleteFileToNas($headerOrigin, 'pcam/document/' . $NPM . '/' . $datas->UploadFile);
                        if (array_key_exists('Status', $p) && $p['Status'] != '1') {
                            $rs['status'] = 0;
                            $rs['msg'] = 'delete failed';
                        } else {
                            $this->db->update('db_academic.final_project_note', ['UploadFile' => NULL]);
                        }
                    } else {
                        if (file_exists('./uploads/document/' . $NPM . '/' . $datas->UploadFile)) {
                            unlink('./uploads/document/' . $NPM . '/' . $datas->UploadFile);
                        }
                        $this->db->update('db_academic.final_project_note', ['UploadFile' => NULL]);
                    }
                } else {
                    $rs['status'] = 0;
                    $rs['msg'] = 'No file found';
                }
            }
            return print_r(json_encode($rs));
        } else if ($data_arr['action'] == 'loadJudiciumsData') {
            $data = $this->db->get_where('db_academic.judiciums', array('Publish' => '1'))->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateMentorFP') {

            $dataForm = (array) $data_arr['dataForm'];

            $this->db->where('ID', $data_arr['ID']);
            $this->db->update('db_academic.auth_students', $dataForm);

            return print_r(1);
        } else if ($data_arr['action'] == 'updateDataJudiciums') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            $Year = $dataForm['Year'];

            if ($ID != '' && $ID != null) {
                $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['updatedAt'] = $this->m_rest->getDateTimeNow();
                // Update
                $this->db->where('ID', $ID);
                $this->db->update('db_academic.judiciums', $dataForm);
            } else {
                // Insert
                $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $dataForm['EntredAt'] = $this->m_rest->getDateTimeNow();
                $this->db->insert('db_academic.judiciums', $dataForm);
            }

            $result = array(
                'Status' => 1
            );


            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'readDataJudiciums') {
            $data = $this->db->query('SELECT j.* FROM db_academic.judiciums j ORDER BY j.ID DESC')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateStatusDataJudiciums') {
            $ID = $data_arr['ID'];
            $Publish = $data_arr['Publish'];

            if ($Publish == '1') {
                $this->db->query('UPDATE db_academic.judiciums s SET s.Publish=IF(s.ID="' . $ID . '","1","0")');
            } else {
                $this->db->query('UPDATE db_academic.judiciums s SET s.Publish="' . $Publish . '"');
            }
            return print_r(1);
        } else if ($data_arr['action'] == 'editJudisiumDate') {

            $NPM = $data_arr['NPM'];
            $JID = $data_arr['JID'];

            $this->db->where('NPM', $NPM);
            $this->db->update(
                'db_academic.judiciums_list',
                array('JID' => $JID)
            );

            return print_r(1);
        } else if ($data_arr['action'] == 'loadDataParticipantOfJudiciums') {

            $requestData = $_REQUEST;

            $ProdiID = $data_arr['ProdiID'];
            $NPM = (isset($data_arr['NPM']) && $data_arr['NPM'] != '' && $data_arr['NPM'] != null) ? $data_arr['NPM'] : '';

            $WhereProdi = ($ProdiID != '') ? ' AND ats.ProdiID = "' . $ProdiID . '" ' : '';

            $JID = $data_arr['JID'];

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' AND (  ats.Name LIKE "%' . $search . '%"
                                OR ats.NPM LIKE "%' . $search . '%"
                                OR ps.NameEng LIKE "%' . $search . '%"
                                OR ps.Name LIKE "%' . $search . '%"
                                )';
            }

            $queryDefault = 'SELECT ats.Name, ats.NPM, ps.NameEng AS ProdiEng, fp.TitleInd, fp.TitleEng FROM db_academic.judiciums_list jl
                                                        LEFT JOIN db_academic.judiciums j ON (j.ID = jl.JID)
                                                        LEFT JOIN db_academic.auth_students ats ON (ats.NPM = jl.NPM)
                                                        LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                        LEFT JOIN db_academic.final_project fp ON (fp.NPM = ats.NPM)
                                                        WHERE j.ID = "' . $JID . '" ' . $WhereProdi . ' ' . $dataSearch;

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $img = $this->m_api->getPhotoStudent($row['NPM']);
                $urlImg = $img['URLImage'];

                $btnInvitation = '';
                if ($NPM != '' && $row['NPM'] == $NPM) {
                    $btnInvitation = '<a href="' . base_url('images/icon/invitation.png') . '" target="_blank" class="btn btn-sm btn-primary">Download</a>';
                } else if ($NPM == '') {
                    $btnInvitation = '<a href="' . base_url('images/icon/invitation.png') . '" target="_blank" class="btn btn-sm btn-primary">Download</a>';
                }

                // Show buttun change yudisium
                $buttonShowChangeY = (isset($data_arr['Source']) && $data_arr['Source'] == 'puis')
                    ? '<br/><a href="javascript:void(0);" class="btnChangePeriode" data-name="' . $row['Name'] . '" data-npm="' . $row['NPM'] . '" data-jid="' . $JID . '" style="color: #fd9700;">Change Periode</a>'
                    : '';


                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div><img src="' . $urlImg . '" class="img-rounded" style="width: 100%;max-width: 150px;"></div>';
                $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b>
                                    <br/>' . $row['NPM'] . '<br/>' . $row['ProdiEng'] . $buttonShowChangeY . '</div>';
                $nestedData[] = '<div style="text-align: left;"><b>' . $row['TitleInd'] . '</b><br/><i>' . $row['TitleEng'] . '</i></div>';
                $nestedData[] = '<div>' . $btnInvitation . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data
            );
            echo json_encode($json_data);
        }
    }


    private function getDepartmentByNav()
    {
        // read by session
        $IDDivision;
        $inArrDiv = [15, 34]; // prodi dan faculty
        $IDDivision;
        if (!in_array($this->session->userdata('IDdepartementNavigation'), $inArrDiv)) {
            $IDDivision = 'NA.' . $this->session->userdata('IDdepartementNavigation');
        } else {
            if ($this->session->userdata('IDdepartementNavigation') == 15) {
                $IDDivision = 'AC.' . $this->session->userdata('prodi_active_id');
            } else {
                $IDDivision = 'FT.' . $this->session->userdata('faculty_active_id');
            }
        }
        return $IDDivision;
    }



    public function crudkb()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'updateNewKB') {

            $dataForm = (array) $data_arr['dataForm'];

            $ID = ($data_arr['ID'] != '') ? $data_arr['ID'] : '';

            $dataForm['EntredBy'] = $this->session->userdata('NIP');
            // add bukti upload,buktiname dan tingkat
            $BuktiUpload = json_encode('');
            if (array_key_exists('upload_kb', $_FILES)) {
                $Upload = $this->m_master->uploadDokumenMultiple(uniqid(), 'upload_kb', $path = './uploads/kb/');
                $Upload = json_encode($Upload);
                $BuktiUpload = $Upload;
            }

            $dataForm['File'] = $BuktiUpload;
            $this->db->insert('db_employees.knowledge_base', $dataForm);
            $ID = $this->db->insert_Id();

            return print_r(json_encode(array('ID' => $ID)));

            // print_r(updatenewKB);
        } else if ($data_arr['action'] == 'viewListKB_2') {
            // $IDDivision = $this->session->userdata('PositionMain')['IDDivision'];

            // added by adhi
            $IDDivision = $this->getDepartmentByNav();
            /*echo $IDDivision;*/

            $data = $this->db->get_where('db_employees.kb_type', array(
                'IDDivision' => $IDDivision
            ))->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateListKB_2') {
            $ID = $data_arr['ID'];
            //$IDDivision = $this->session->userdata('PositionMain')['IDDivision'];
            // addded by adhi
            $IDDivision = $this->getDepartmentByNav();
            $dataForm = array(
                'Type' => $data_arr['Type'],
                'IDDivision' => $IDDivision
            );

            if ($ID != '') {
                // Update

                $this->db->where('ID', $ID);
                $this->db->update('db_employees.kb_type', $dataForm);
            } else {
                //insert

                $this->db->insert('db_employees.kb_type', $dataForm);
                $ID = $this->db->insert_id();
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'removeDataKB') {
            $ID = $data_arr['ID'];

            // remove file is exist
            $G_data = $this->m_master->caribasedprimary('db_employees.knowledge_base', 'ID', $ID);
            // print_r($G_data);die();
            if ($G_data[0]['File'] != '' && $G_data[0]['File'] != null) {
                $arr_file = (array) json_decode($G_data[0]['File'], true);
                if (count($G_data) > 0) {
                    $old = $G_data[0]['File'];
                    if ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') {
                        $headerOrigin = serverRoot;
                        $rs = $this->m_master->DeleteFileToNas($headerOrigin, 'pcam/kb/' . $old);
                        if ($rs['Status'] != '1') {
                            return print_r('Error delete file');
                            die();
                        }
                    } else {
                        if ($old != ''  && is_file('./uploads/kb/' . $old)) {
                            unlink('./uploads/kb/' . $old);
                        }
                    }
                }
            }

            $get_data =  $this->db->where('ID', $ID)->get('db_employees.knowledge_base')->row();

            // save log
            $dataSaveLog = [
                'ID_knowledge_base' => $ID,
                'Action' => 'Delete', // just insert not update
                'IDType' => $get_data->IDType,
                'Desc' => $get_data->Desc,
                'File' => $get_data->File,
                'Status' => $get_data->Status,
                'ActionBy' => $this->session->userdata('NIP'),
                'ActionAt' => date('Y-m-d H:i:s'),
            ];

            $this->m_master->kb_action_log($dataSaveLog);

            $this->db->where('ID', $ID);
            $this->db->delete('db_employees.knowledge_base');
            return print_r(1);
        } else {
            echo '{"status":"999","message":"Not Authorize"}';
        }
    }


    public function crudAllProgramStudy()
    {
        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'viewAllProdi') {
            $data = $this->db->get_where('db_academic.program_study', array(
                'Status' => 1
            ))->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateCreditAllProdi') {
            $dataForm = (array) $data_arr['dataForm'];

            if (count($dataForm) > 0) {
                for ($i = 0; $i < count($dataForm); $i++) {
                    $d = (array) $dataForm[$i];
                    $this->db->set('DefaultCredit', $d['Credit']);
                    $this->db->where('ID', $d['ID']);
                    $this->db->update('db_academic.program_study');
                }
            }

            return print_r(1);
        }
    }

    private function check_field_ip_server_to($table = "db_employees.log_employees")
    {
        $sql =  $this->db->query('SHOW COLUMNS FROM ' . $table . ' LIKE "ip_server_conn" ')->result_array();
        if (count($sql) == 0) {
            // alter field table
            $this->db->query(
                '
                        ALTER TABLE ' . $table . '
                        ADD `ip_server_conn`  varchar(255) NOT NULL DEFAULT "" ;
                    '
            );
        }
    }

    public function crudLogging()
    {
        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'insertLog') {

            $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $dataForm = (array) $data_arr['dataForm'];
            $dataForm['IPLocal2'] = $this->input->ip_address();
            $dataForm['IPLocal'] = $hostname;
            $dataForm['AccessedOn'] = $this->m_rest->getDateTimeNow();

            $myIP = $_SERVER['SERVER_ADDR'];
            $this->check_field_ip_server_to();
            $dataForm['ip_server_conn'] = $myIP;

            $this->db->insert('db_employees.log_employees', $dataForm);
            return print_r(1);
        } else if ($data_arr['action'] == 'insertLogStudent') {

            $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $dataForm = (array) $data_arr['dataForm'];
            $dataForm['IPLocal2'] = $this->input->ip_address();
            $dataForm['IPLocal'] = $hostname;
            $dataForm['AccessedOn'] = $this->m_rest->getDateTimeNow();

            $myIP = $_SERVER['SERVER_ADDR'];
            $this->check_field_ip_server_to('db_academic.log_student');
            $dataForm['ip_server_conn'] = $myIP;

            $this->db->insert('db_academic.log_student', $dataForm);
            return print_r(1);
        } else if ($data_arr['action'] == 'insertLogLecturer') {

            $hostname = gethostbyaddr($_SERVER['REMOTE_ADDR']);
            $dataForm = (array) $data_arr['dataForm'];
            $dataForm['IPLocal2'] = $this->input->ip_address();
            $dataForm['IPLocal'] = $hostname;
            $dataForm['AccessedOn'] = $this->m_rest->getDateTimeNow();

            $myIP = $_SERVER['SERVER_ADDR'];
            $this->check_field_ip_server_to('db_employees.log_lecturers');
            $dataForm['ip_server_conn'] = $myIP;

            $this->db->insert('db_employees.log_lecturers', $dataForm);
            return print_r(1);
        } else if ($data_arr['action'] == 'removeLogging') {

            $table = $data_arr['table'];

            $this->db->query('DELETE FROM ' . $table . ' WHERE AccessedOn
            BETWEEN "' . $data_arr['Start'] . ' 00:00:00" AND "' . $data_arr['End'] . ' 23:59:00"');

            return print_r(1);
        } else if ($data_arr['action'] == 'updateFCMToken') {

            $dataForm = (array) $data_arr['dataForm'];
            $Username = $dataForm['Username'];
            $TypeUser = $dataForm['TypeUser'];
            $FCMToken = $dataForm['FCMToken'];

            $arrWhere = array('Username' => $Username, 'TypeUser' => $TypeUser);
            // cek apakah username sudah ada atau blm
            $cekUser = $this->db->from('db_it.fcm_token')->where($arrWhere)->count_all_results();

            if ($cekUser > 0) {
                unset($dataForm['Username']);
                unset($dataForm['TypeUser']);
                $this->db->where($arrWhere);
                $this->db->update('db_it.fcm_token', $dataForm);
            } else {
                $this->db->insert('db_it.fcm_token', $dataForm);
            }

            // hanya untuk live
            if ($_SERVER['SERVER_NAME'] != 'localhost') {
                $this->subscribeTopic($Username, $TypeUser, $FCMToken);
            }


            return print_r(1);
        }
    }

    function testNotif()
    {
        $url = 'https://firebase.podomorouniversity.ac.id/pushnotification.php';
        $dataPush = array(
            'type' => 'topic', // topic / token
            //                    'topic' => 'pu_course_'.$dataTopic['ScheduleID'],
            'topic' => 'pu_emp_12',
            'title' => 'nandang',
            'body' => 'ClassGroup NameEng'
                . ' - Create new discussion : Topic'
                . ' - For details, please login to the portal'
        );
        $this->m_master->http_request_post_data($url, $dataPush);
    }

    private function subscribeTopic($Username, $TypeUser, $FCMToken)
    {

        // SUBSCRIBE TOPIK
        /*
        ========================================
        untuk semua mhs - dosen - staff >>> pu_civitas_akademika
        untuk semua mhs - dosen >>> pu_std_lec
        untuk semua dosen - staff >>> pu_lec_emp
        untuk semua dosen >>> pu_lec
        untuk semua staff (exclude dosen) >>> pu_emp
        untuk semua staff per divisi (exclude dosen) >>> pu_emp_divisiid
        untuk semua mhs >>> pu_mhs
        untuk semua mhs (angkatan n) >>> pu_mhs_n (n adalah tahun masuk)
        untuk semua mhs (angkatan n) & prodi x >>> pu_mhs_n_x (n adalah tahun masuk , x adalah prodi id)
        ========================================
        Topik mata kuliah >>> pu_course_ScheduleID (di subscribe dosen dan mahasiswa)
        ========================================
        subscribe_username sendiri (NIP / NPM)
        */

        $array_topic = [];

        if ($TypeUser == 'emp') {

            $array_topic = ['pu_civitas_akademika', 'pu_lec_emp', 'pu_emp', 'pu_user_' . $Username];

            // get id divisi
            $dataEmp = $this->db->select('PositionMain,PositionOther1,PositionOther2,PositionOther3')
                ->get_where('db_employees.employees', array('NIP' => $Username))->result_array()[0];

            if ($dataEmp['PositionMain'] != null && $dataEmp['PositionMain'] != '') {
                $DivisiID = explode('.', $dataEmp['PositionMain'])[0];
                array_push($array_topic, 'pu_emp_' . $DivisiID);
            }

            if ($dataEmp['PositionOther1'] != null && $dataEmp['PositionOther1'] != '') {
                $DivisiID = explode('.', $dataEmp['PositionOther1'])[0];
                array_push($array_topic, 'pu_emp_' . $DivisiID);
            }

            if ($dataEmp['PositionOther2'] != null && $dataEmp['PositionOther2'] != '') {
                $DivisiID = explode('.', $dataEmp['PositionOther2'])[0];
                array_push($array_topic, 'pu_emp_' . $DivisiID);
            }

            if ($dataEmp['PositionOther3'] != null && $dataEmp['PositionOther3'] != '') {
                $DivisiID = explode('.', $dataEmp['PositionOther3'])[0];
                array_push($array_topic, 'pu_emp_' . $DivisiID);
            }
        } else if ($TypeUser == 'lec') {

            $array_topic = ['pu_civitas_akademika', 'pu_lec_emp', 'pu_lec', 'pu_user_' . $Username];

            // get jadwal di semester aktif
            $dataSch = $this->db->query('SELECT s.ID AS ScheduleID FROM db_academic.schedule s
                                                LEFT JOIN db_academic.semester smt ON (smt.ID = s.SemesterID)
                                                WHERE smt.Status = 1 AND s.Coordinator = "' . $Username . '"
                                                UNION ALL
                                                SELECT sst.ScheduleID FROM db_academic.schedule_team_teaching sst
                                                LEFT JOIN db_academic.schedule s2 ON (s2.ID = sst.ScheduleID)
                                                LEFT JOIN db_academic.semester smt2 ON (smt2.ID = s2.SemesterID)
                                                WHERE smt2.Status = 1 AND sst.NIP = "' . $Username . '" ')->result_array();

            if (count($dataSch) > 0) {
                foreach ($dataSch as $item) {
                    array_push($array_topic, 'pu_course_' . $item['ScheduleID']);
                }
            }
        } else if ($TypeUser == 'std') {
            // mahasiswa
            // get data mhs
            $dataMhs = $this->db->select('Year, ProdiID')
                ->get_where('db_academic.auth_students', array('NPM' => $Username))->result_array()[0];

            $array_topic = [
                'pu_civitas_akademika', 'pu_mhs',
                'pu_mhs_' . $dataMhs['Year'],
                'pu_mhs_' . $dataMhs['Year'] . '_' . $dataMhs['ProdiID'], 'pu_user_' . $Username
            ];

            $dbstd = 'ta_' . $dataMhs['Year'];
            // get jadwal aktif
            $dataSch = $this->db->query('SELECT sp.ScheduleID  FROM ' . $dbstd . '.study_planning sp
                                                    LEFT JOIN db_academic.semester s ON (s.ID = sp.SemesterID)
                                                     WHERE s.Status = 1
                                                     AND sp.NPM = "' . $Username . '" ')->result_array();

            if (count($dataSch) > 0) {
                foreach ($dataSch as $item) {
                    array_push($array_topic, 'pu_course_' . $item['ScheduleID']);
                }
            }
        }


        if (count($array_topic) > 0) {
            // push to subscribe
            for ($i = 0; $i < count($array_topic); $i++) {

                $array_check = array(
                    'Topic' => $array_topic[$i],
                    'Username' => $Username,
                    'TypeUser' => $TypeUser,
                    'FCMToken' => $FCMToken
                );

                $dataCkSubs = $this->db->from('db_it.fcm_subscribe_topic')
                    ->where($array_check)->count_all_results();

                if ($dataCkSubs <= 0) {
                    // insert dan subscribe
                    $this->db->insert('db_it.fcm_subscribe_topic', $array_check);
                    $dataPostFCM = array('topic' => $array_topic[$i], 'token' => $FCMToken);
                    $url = 'https://firebase.podomorouniversity.ac.id/subscribe.php';
                    $this->m_master->http_request_post_data($url, $dataPostFCM);
                }
            }
        }
    }

    public function crudLibraryBooking()
    {
        $data_arr = $this->getInputToken2();
        // print_r($data_arr);
        // exit();
        if ($data_arr['action'] == 'viewListBooking') {

            $requestData = $_REQUEST;

            $WhereStatus = ($data_arr['Status'] != '') ? 'WHERE b.status = "' . $data_arr['Status'] . '" ' : '';

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $w = ($data_arr['Status'] != '') ? ' AND ' : 'WHERE ';
                $dataSearch = $w . ' (b.Username LIKE "%' . $search . '%" )';
            }


            $queryDefault = 'SELECT b.*, em.Name, ats.Year FROM db_library.booking b 
                                LEFT JOIN db_employees.employees em ON (em.NIP = b.Username) 
                                LEFT JOIN db_academic.auth_students ats ON (ats.NPM = b.Username)
                                ' . $WhereStatus . $dataSearch;



            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (' . $queryDefault . ') xx';

            $sql = $queryDefault . ' ORDER BY b.ID DESC LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $dataTable = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $query = $dataTable;

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {
                $nestedData = array();
                $row = $query[$i];

                $Name = $row['Name'];

                if ($row['Name'] == '') {
                    $db_ta = 'ta_' . $row['Year'];
                    $getDataStd = $this->db->select('Name')
                        ->get_where($db_ta . '.students', array('NPM' => $row['Username']))->result_array()[0];

                    $Name = $getDataStd['Name'];
                }

                $btnAct = ($row['status'] == 1) ? '<div class="btn-group" role="group" aria-label="...">
                <button type="button" data-status="2" data-id="' . $row['ID'] . '" data-biblio="' . $row['biblio_id'] . '" class="btn btn-sm btn-success actionBooking">Approve</button>
                <button type="button" data-status="-1" data-id="' . $row['ID'] . '" data-biblio="' . $row['biblio_id'] . '" class="btn btn-sm btn-danger actionBooking">Reject</button>
              </div>' : '';

                $viewStatus = '<span class="label label-warning">Waiting Action</span>';

                // 1=request,2=approve,-1=reject, -2 = cancel

                if ($row['status'] == 2) {
                    $viewStatus = '<span class="label label-success">Approved</span>';
                } else if ($row['status'] == -1) {
                    $viewStatus = '<span class="label label-danger">Rejected</span>';
                } else if ($row['status'] == -2) {
                    $viewStatus = '<span class="label label-default">Canceled</span>';
                }

                $dbLib = $this->load->database('server22', TRUE);

                $dataBook = $dbLib->query('SELECT b.title FROM biblio b WHERE b.biblio_id = "' . $row['biblio_id'] . '"')->result_array()[0];

                $notes = ($row['notes'] != '')
                    ? '<div class="well">' . $row['notes'] . '</div>' : '';

                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;"><b>' . $Name . '</b><br/>' . $row['Username'] . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $dataBook['title'] . $notes . '</div>';
                $nestedData[] = '<div style="text-align:left;">' . $row['notes_by_admin'] . '</div>';
                $nestedData[] = '<div>' . $viewStatus . '</div>';
                $nestedData[] = '<div>' . $btnAct . '</div>';

                $no++;

                $data[] = $nestedData;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data
            );
            echo json_encode($json_data);
        }
    }

    public function crudFileFinalProject()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'viewFileFinalProject') {
            $requestData = $_REQUEST;

            $WhereStatus = ($data_arr['Status'] != '') ? 'WHERE fpf.Status = "' . $data_arr['Status'] . '" ' : '';

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {

                $search = $requestData['search']['value'];
                $w = ($data_arr['Status'] != '') ? ' AND ' : 'WHERE ';
                $dataSearch = $w . ' (fpf.NPM LIKE "%' . $search . '%" OR ats.Name LIKE "%' . $search . '%"
                                OR ps.Name LIKE "%' . $search . '%" OR fpf.JudulInd LIKE "%' . $search . '%"
                                 OR fpf.JudulEng LIKE "%' . $search . '%" )';
            }

            $queryDefault = 'SELECT fpf.*, ats.Name, ps.Name AS ProdiName, em.Name AS EmUpdateByName FROM db_academic.final_project_files fpf
                                          LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fpf.NPM)
                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                          LEFT JOIN db_employees.employees em ON (em.NIP = fpf.EmUpdateBy)
                                           ' . $WhereStatus . $dataSearch . ' ORDER BY fpf.UpdatedAt ASC';

            $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (SELECT fpf.*, ats.Name, ps.Name AS ProdiName, em.Name AS EmUpdateByName FROM db_academic.final_project_files fpf
                                          LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fpf.NPM)
                                          LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                          LEFT JOIN db_employees.employees em ON (em.NIP = fpf.EmUpdateBy) ' . $WhereStatus . $dataSearch . ' ) xx';

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $dataTable = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

            $query = $dataTable;

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {
                $nestedData = array();
                $row = $query[$i];

                $Updated = ($row['EmUpdateBy'] != '' && $row['EmUpdateBy'] != null)
                    ? '<div style="font-size: 10px;">' . $row['EmUpdateByName'] . '<br/>' . date('d M Y H:i', strtotime($row['EmUpdateAt'])) . '</div>' : '';

                // 0 = Plan, 1 = Send, 2 = Approve, -2 Rejected
                $Status = '<span style="color:#b3b2b2;font-size: 11px;">Waiting for sending documents</span>';
                if ($row['Status'] == 1 || $row['Status'] == '1') {
                    $Status = '<span style="color:blue;">Need action</span>';
                } else if ($row['Status'] == 2 || $row['Status'] == '2') {
                    $Status = '<span style="color: green;"><i class="fa fa-check-circle"></i> Approved</span>' . $Updated;
                } else if ($row['Status'] == -2 || $row['Status'] == '-2') {
                    $Status = '<span style="color: red;"><i class="fa fa-times-circle"></i> Rejected</span>' . $Updated;
                }

                $Noted = ($row['Noted'] != '' && $row['Noted'] != null) ? $row['Noted'] : '';




                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;"><a href="' . base_url('library/yudisium/final-project/details/' . $row['NPM']) . '" target="_blank"><b>' . $row['Name'] . '</b></a><br/>' . $row['NPM'] . '<br/>' . $row['ProdiName'] . '</div>';
                $nestedData[] = '<div style="text-align:left;"><b>' . $row['JudulInd'] . '</b><br/><i>' . $row['JudulEng'] . '</i></div>';
                $nestedData[] = '<div>' . $Noted . '</div>';
                $nestedData[] = '<div>' . $Status . '</div>';

                $no++;

                $data[] = $nestedData;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval($queryDefaultRow),
                "recordsFiltered" => intval($queryDefaultRow),
                "data"            => $data
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'viewDetailsFileFinalProject') {

            $NPM = $data_arr['NPM'];

            $data = $this->db->query('SELECT fpf.*, ats.Name FROM db_academic.final_project_files fpf
                                                LEFT JOIN db_academic.auth_students ats ON (ats.NPM = fpf.NPM)
                                                WHERE fpf.NPM = "' . $NPM . '" ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateFileFinalProject') {

            $NPM = $data_arr['NPM'];
            $dataForm = (array) $data_arr['dataform'];

            $dataForm['EmUpdateBy'] = $this->session->userdata('NIP');
            $dataForm['EmUpdateAt'] = $this->m_rest->getDateTimeNow();

            $this->db->where('NPM', $NPM);
            $this->db->update('db_academic.final_project_files', $dataForm);
            $this->db->reset_query();

            if ($dataForm['Status'] == 2 || $dataForm['Status'] == '2') {
                // Update juga di final project

                $dataFP = $this->db->get_where('db_academic.final_project_files', array('NPM' => $NPM))->result_array();

                $dataCk = $this->db->get_where('db_academic.final_project', array('NPM' => $NPM))->result_array();

                $dataFP = array(
                    'NPM' => $NPM,
                    'TitleInd' => $dataFP[0]['JudulInd'],
                    'TitleEng' => $dataFP[0]['JudulEng'],
                    'Status' => '2',
                    'UpdatedBy' => $this->session->userdata('NIP')
                );

                if (count($dataCk) > 0) {
                    // Update yang data
                    $this->db->where('ID', $dataCk[0]['ID']);
                    $this->db->update('db_academic.final_project', $dataFP);
                } else {
                    // Insert yang baru
                    $this->db->insert('db_academic.final_project', $dataFP);
                }
            }

            return print_r(1);
        }
    }

    public function crudProgrameStudy()
    {

        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'viewAllDataProdi') {
            $data = $this->db->query('SELECT ps.*,el.Description, a.Label AS Akreditation FROM db_academic.program_study ps
                                           LEFT JOIN db_academic.education_level el ON (el.ID = ps.EducationLevelID)
                                           LEFT JOIN db_academic.accreditation a ON (a.ID = ps.AccreditationID)
                                           WHERE ps.Status = "1"')->result_array();

            // Get jml mhs
            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $DataMhs  = $this->db->query('SELECT COUNT(*) AS Total FROM db_academic.auth_students ats
                                                            WHERE ats.ProdiID = "' . $data[$i]['ID'] . '" AND ats.StatusStudentID = "3" ')->result_array();
                    $data[$i]['TotalMhs'] = $DataMhs[0]['Total'];
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateProgrammeStudy') {
            $ID = $data_arr['ID'];
            $dataForm = $data_arr['dataForm'];

            $this->db->where('ID', $ID);
            $this->db->update('db_academic.program_study', $dataForm);

            return print_r(1);
        }
    }

    public function getAccreditation()
    {
        $data = $this->db->get('db_academic.accreditation')->result_array();

        return print_r(json_encode($data));
    }

    public function getDataLogEmployees()
    {

        $this->check_field_ip_server_to('db_employees.log_lecturers');

        $requestData = $_REQUEST;

        $u = $this->input->get('u');

        $dataWhere = ($u != '' && $u != null && isset($u)) ? 'WHERE lem.NIP = "' . $u . '" ' : '';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];

            $fillSrc = 'lem.URL LIKE "%' . $search . '%" OR
                                 em.Name LIKE "%' . $search . '%" OR
                                 em.NIP LIKE "%' . $search . '%" OR
                                 em2.Name LIKE "%' . $search . '%" OR
                                 em2.NIP LIKE "%' . $search . '%" OR
                                 ats.Name LIKE "%' . $search . '%" OR
                                 ats.NPM LIKE "%' . $search . '%"';

            $dataSearch = ($u != '' && $u != null && isset($u))
                ?  ' AND ( ' . $fillSrc . ' )'
                : ' WHERE ' . $fillSrc;
        }

        // query total
        $sqlTotal = 'select count(*) as total from (
                SELECT lem.ID
                                            FROM db_employees.log_employees lem
                                            LEFT JOIN db_employees.employees em ON (em.NIP = lem.NIP)
                                            LEFT JOIN db_employees.employees em2 ON (em2.NIP = lem.UserID)
                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM =  lem.UserID)
                                            ' . $dataWhere . ' ' . $dataSearch . '
            ) cc';
        $queryTotal = $this->db->query($sqlTotal, array())->result_array()[0]['total'];

        $queryDefault = 'SELECT lem.ID, em.Name, lem.AccessedOn,
                            (CASE WHEN lem.NIP = lem.UserID THEN 0 ELSE lem.UserID END ) AS LoginAs,
                            (CASE WHEN em2.Name = em.Name THEN NULL ELSE em2.Name END) AS LoginAsLec,
                            ats.Name AS LoginAsStd,lem.URL,
                            lem.IPPublic, lem.IPLocal, lem.IPLocal2, lem.ip_server_conn
                            FROM db_employees.log_employees lem
                            LEFT JOIN db_employees.employees em ON (em.NIP = lem.NIP)
                            LEFT JOIN db_employees.employees em2 ON (em2.NIP = lem.UserID)
                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM =  lem.UserID)
                            ' . $dataWhere . ' ' . $dataSearch . ' ORDER BY lem.ID DESC';


        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        // $queryDefaultRow = $this->db->query($queryDefault)->result_array();

        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {

            $nestedData = array();
            $row = $query[$i];

            $LoginAsLecturer = ($row['LoginAsLec'] != '' && $row['LoginAsLec'] != null)
                ? '<span class="label label-danger">Remote Lecturer</span><div style="color: #3f51b5;margin-top: 10px;">' . $row['LoginAsLec'] . '</div>' : '';
            $LoginAsStudent = ($row['LoginAsStd'] != '' && $row['LoginAsStd'] != null)
                ? '<span class="label label-primary">Remote Student</span><div style="color: #3f51b5;margin-top: 10px;">' . $row['LoginAsStd'] . '</div>' : '';

            $urlExp = explode('/', $row['URL']);

            $viewLink = '';
            $im = 2;
            $tokenText = '';
            if (count($urlExp) > $im) {
                for ($i2 = 0; $i2 < count($urlExp); $i2++) {
                    if ($i2 > $im) {
                        $lg = strlen($urlExp[$i2]);
                        $vl = ($lg <= 55) ? $urlExp[$i2] : '';

                        $de = ($i2 != $im && $i2 != count($urlExp) && $vl != '') ? '<i class="fa fa-angle-right"></i>' : '';
                        $viewLink = $viewLink . ' ' . $de . ' <b>' . $vl . '</b>';


                        if ($lg > 55) {
                            $tokenText = '<div style="margin-top: 15px;">Token : <textarea class="form-control" rows="3" style="color: #333333;" readonly>' . $urlExp[$i2] . '</textarea></div>';
                        }
                    }
                }
            } else {
                $viewLink = $urlExp[$im];
            }

            $nestedData[] = '<div>' . $no . '</div>';

            if (!isset($u)) {
                $IPPublic = ($row['IPPublic'] != '' && $row['IPPublic'] != null) ? 'Public : ' . $row['IPPublic'] . '<br/>' : '';
                $IPLocal = ($row['IPLocal'] != '' && $row['IPLocal'] != null) ? 'Local 1 : ' . $row['IPLocal'] . '<br/>' : '';
                $IPLocal2 = ($row['IPLocal2'] != '' && $row['IPLocal2'] != null) ? 'Local 2 : ' . $row['IPLocal2'] : '';
                $dataIP = '<div>' . $IPPublic . '' . $IPLocal . '' . $IPLocal2 . '</div>';
                $dataServerConn = 'Server-connection : ' . $row['ip_server_conn'];

                $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b>' . $dataIP . $dataServerConn . '</div>';
            }

            // Cek apakah today
            $viewDate = explode(' ', $row['AccessedOn'])[0];
            $labelToday =
                ($this->m_rest->getDateNow() == $viewDate)
                ? '<span style="margin-left: 10px;" class="label label-success"><i class="fa fa-check-circle" style="margin-right: 3px;"></i> Today</span>'
                : '';

            $nestedData[] = '<div style="text-align: left;">' . $LoginAsLecturer . $LoginAsStudent . '<div style="color: #FF5722;">' . date('d M Y H:i:s', strtotime($row['AccessedOn'])) . $labelToday . '</div></div>';
            $nestedData[] = '<div style="text-align: left;">' . $viewLink . $tokenText . '</div>';

            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($queryTotal),
            "recordsFiltered" => intval($queryTotal),
            "data"            => $data,
            "dataQuery"            => $query
        );
        echo json_encode($json_data);
    }

    public function getDataLogLecturer()
    {

        $this->check_field_ip_server_to('db_employees.log_lecturers');

        $requestData = $_REQUEST;

        $u = $this->input->get('u');

        $dataWhere = ($u != '' && $u != null && isset($u)) ? 'WHERE lem.NIP = "' . $u . '" ' : '';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];

            $fillSrc = 'lem.URL LIKE "%' . $search . '%" OR
                                 em.Name LIKE "%' . $search . '%" OR
                                 em.NIP LIKE "%' . $search . '%"';

            $dataSearch = ($u != '' && $u != null && isset($u))
                ?  ' AND ( ' . $fillSrc . ' )'
                : ' WHERE ' . $fillSrc;
        }

        $queryDefault = 'SELECT lem.ID, em.Name, lem.AccessedOn,
                            lem.IPPublic, lem.IPLocal, lem.IPLocal2, lem.URL,lem.ip_server_conn
                            FROM db_employees.log_lecturers lem
                            LEFT JOIN db_employees.employees em ON (em.NIP = lem.NIP)
                            ' . $dataWhere . ' ' . $dataSearch . ' ORDER BY lem.ID DESC';

        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (SELECT lem.ID FROM db_employees.log_lecturers lem
                            LEFT JOIN db_employees.employees em ON (em.NIP = lem.NIP)
                            ' . $dataWhere . ' ' . $dataSearch . ' ORDER BY lem.ID DESC) xx';


        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {

            $nestedData = array();
            $row = $query[$i];

            $urlExp = explode('/', $row['URL']);

            $viewLink = '';
            $im = 2;
            $tokenText = '';
            if (count($urlExp) > $im) {
                for ($i2 = 0; $i2 < count($urlExp); $i2++) {
                    if ($i2 > $im) {
                        $lg = strlen($urlExp[$i2]);
                        $vl = ($lg <= 55) ? $urlExp[$i2] : '';

                        $de = ($i2 != $im && $i2 != count($urlExp) && $vl != '') ? '<i class="fa fa-angle-right"></i>' : '';
                        $viewLink = $viewLink . ' ' . $de . ' <b>' . $vl . '</b>';


                        if ($lg > 55 && !isset($u)) {
                            $tokenText = '<div style="margin-top: 15px;">Token : <textarea class="form-control" rows="3" style="color: #333333;" readonly>' . $urlExp[$i2] . '</textarea></div>';
                        }
                    }
                }
            } else {
                $viewLink = $urlExp[$im];
            }

            $nestedData[] = '<div>' . $no . '</div>';


            if (!isset($u)) {
                $IPPublic = ($row['IPPublic'] != '' && $row['IPPublic'] != null) ? 'Public : ' . $row['IPPublic'] . '<br/>' : '';
                $IPLocal = ($row['IPLocal'] != '' && $row['IPLocal'] != null) ? 'Local 1 : ' . $row['IPLocal'] . '<br/>' : '';
                $IPLocal2 = ($row['IPLocal2'] != '' && $row['IPLocal2'] != null) ? 'Local 2 : ' . $row['IPLocal2'] : '';
                $dataIP = '<div>' . $IPPublic . '' . $IPLocal . '' . $IPLocal2 . '</div>';

                $dataServerConn = 'Server-connection : ' . $row['ip_server_conn'];

                $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b>' . $dataIP . $dataServerConn . '</div>';
            }

            $nestedData[] = '<div style="text-align: left;">' . '<div style="color: #FF5722;">' . date('d M Y H:i:s', strtotime($row['AccessedOn'])) . '</div></div>';
            $nestedData[] = '<div style="text-align: left;">' . $viewLink . $tokenText . '</div>';

            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($queryDefaultRow),
            "recordsFiltered" => intval($queryDefaultRow),
            "data"            => $data,
            "dataQuery"            => $query
        );
        echo json_encode($json_data);
    }

    public function getDataLogStudent()
    {

        $this->check_field_ip_server_to('db_academic.log_student');

        $requestData = $_REQUEST;

        $u = $this->input->get('u');

        $dataWhere = ($u != '' && $u != null && isset($u)) ? 'WHERE lem.NPM = "' . $u . '" ' : '';

        $dataSearch = '';
        if (!empty($requestData['search']['value'])) {
            $search = $requestData['search']['value'];

            $fillSrc = 'lem.URL LIKE "%' . $search . '%" OR
                                 em.Name LIKE "%' . $search . '%" OR
                                 em.NPM LIKE "%' . $search . '%"';

            $dataSearch = ($u != '' && $u != null && isset($u))
                ?  ' AND ( ' . $fillSrc . ' )'
                : ' WHERE ' . $fillSrc;
        }

        $queryDefault = 'SELECT lem.ID, em.Name, lem.AccessedOn,
                            lem.IPPublic, lem.IPLocal, lem.IPLocal2, lem.URL,lem.ip_server_conn
                            FROM db_academic.log_student lem
                            LEFT JOIN db_academic.auth_students em ON (em.NPM = lem.NPM)
                            ' . $dataWhere . ' ' . $dataSearch . ' ORDER BY lem.ID DESC';

        $queryDefaultTotal = 'SELECT COUNT(*) AS Total FROM (SELECT lem.ID FROM db_academic.log_student lem
                            LEFT JOIN db_academic.auth_students em ON (em.NPM = lem.NPM)
                            ' . $dataWhere . ' ' . $dataSearch . ') xx ';


        $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

        $query = $this->db->query($sql)->result_array();
        $queryDefaultRow = $this->db->query($queryDefaultTotal)->result_array()[0]['Total'];

        $no = $requestData['start'] + 1;
        $data = array();

        for ($i = 0; $i < count($query); $i++) {

            $nestedData = array();
            $row = $query[$i];

            $urlExp = explode('/', $row['URL']);

            $viewLink = '';
            $im = 2;
            $tokenText = '';
            if (count($urlExp) > $im) {
                for ($i2 = 0; $i2 < count($urlExp); $i2++) {
                    if ($i2 > $im) {
                        $lg = strlen($urlExp[$i2]);
                        $vl = ($lg <= 55) ? $urlExp[$i2] : '';

                        $de = ($i2 != $im && $i2 != count($urlExp) && $vl != '') ? '<i class="fa fa-angle-right"></i>' : '';
                        $viewLink = $viewLink . ' ' . $de . ' <b>' . $vl . '</b>';


                        if ($lg > 55 && !isset($u)) {
                            $tokenText = '<div style="margin-top: 15px;">Token : <textarea class="form-control" rows="3" style="color: #333333;" readonly>' . $urlExp[$i2] . '</textarea></div>';
                        }
                    }
                }
            } else {
                $viewLink = $urlExp[$im];
            }

            $nestedData[] = '<div>' . $no . '</div>';


            if (!isset($u)) {
                $IPPublic = ($row['IPPublic'] != '' && $row['IPPublic'] != null) ? 'Public : ' . $row['IPPublic'] . '<br/>' : '';
                $IPLocal = ($row['IPLocal'] != '' && $row['IPLocal'] != null) ? 'Local 1 : ' . $row['IPLocal'] . '<br/>' : '';
                $IPLocal2 = ($row['IPLocal2'] != '' && $row['IPLocal2'] != null) ? 'Local 2 : ' . $row['IPLocal2'] : '';
                $dataIP = '<div>' . $IPPublic . '' . $IPLocal . '' . $IPLocal2 . '</div>';

                $dataServerConn = 'Server-connection : ' . $row['ip_server_conn'];

                $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b>' . $dataIP . $dataServerConn . '</div>';
            }

            $nestedData[] = '<div style="text-align: left;">' . '<div style="color: #FF5722;">' . date('d M Y H:i:s', strtotime($row['AccessedOn'])) . '</div></div>';
            $nestedData[] = '<div style="text-align: left;">' . $viewLink . $tokenText . '</div>';

            $data[] = $nestedData;
            $no++;
        }

        $json_data = array(
            "draw"            => intval($requestData['draw']),
            "recordsTotal"    => intval($queryDefaultRow),
            "recordsFiltered" => intval($queryDefaultRow),
            "data"            => $data,
            "dataQuery"       => $query
        );
        echo json_encode($json_data);
    }

    public function crudTracerAlumni()
    {
        $data_arr = $this->getInputToken2();

        if ($data_arr['action'] == 'viewAlumni') {

            $Year = $data_arr['Year'];
            $WhereY = ($Year != '') ? ' AND ats.GraduationYear = "' . $Year . '" ' : '';

            $requestData = $_REQUEST;

            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];
                $dataSearch = ' AND ( ats.Name LIKE "%' . $search . '%"
                                    OR ats.NPM LIKE "%' . $search . '%" )';
            }

            $queryDefault = 'SELECT ats.* FROM db_academic.auth_students ats WHERE ats.StatusStudentID = "1" ' . $WhereY . '  ' . $dataSearch;

            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $ShowName = $row['NPM'] . ' - ' . ucwords(strtolower($row['Name']));


                $nestedData[] = '<div>' . $no . '</div>';
                $nestedData[] = '<div style="text-align:left;"><b><a href="javascript:void(0);" class="showDetailAlumni" data-npm="' . $row['NPM'] . '" data-name="' . $ShowName . '">' . $ShowName . '</a></b></div>';
                $nestedData[] = $row['GraduationYear'];

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval(count($queryDefaultRow)),
                "recordsFiltered" => intval(count($queryDefaultRow)),
                "data"            => $data
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'showExperience') {
            $NPM = $data_arr['NPM'];

            $data = $this->db->query('SELECT ae.*, pl.Description AS PositionLevel, c.Name AS Company, c.Industry, c.Phone, c.Address
                                              FROM db_studentlife.alumni_experience ae
                                              LEFT JOIN db_studentlife.position_level pl ON (pl.ID = ae.PositionLevelID)
                                              LEFT JOIN db_studentlife.master_company c ON (c.ID = ae.CompanyID)
                                              WHERE ae.NPM = "' . $NPM . '" ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'loadPositionLevel') {
            $data = $this->db->order_by('ID', 'DESC')->get('db_studentlife.position_level')->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'updateDataExperience') {

            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];
            if ($ID != '') {
                // Update
                if (!array_key_exists('UpdatedBy', $dataForm)) {
                    $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                }
                $dataForm['Logs'] = null;
                //$dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('db_studentlife.alumni_experience', $dataForm);
            } else {
                if (!array_key_exists('EntredBy', $dataForm)) {
                    $dataForm['EntredBy'] = $this->session->userdata('NIP');
                }
                $dataForm['EntredAt'] = $this->m_rest->getDateTimeNow();
                $this->db->insert('db_studentlife.alumni_experience', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'saveMasterCompany') {
            $ID = $data_arr['ID'];
            $dataForm = (array) $data_arr['dataForm'];

            if ($ID != '') {
                if (!array_key_exists('UpdatedBy', $dataForm)) {
                    $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                }
                // $dataForm['UpdatedBy'] = $this->session->userdata('NIP');
                $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                $this->db->where('ID', $ID);
                $this->db->update('db_studentlife.master_company', $dataForm);
            } else {
                if (!array_key_exists('EntredBy', $dataForm)) {
                    $dataForm['EntredBy'] = $this->session->userdata('NIP');
                }
                // $dataForm['EntredBy'] = $this->session->userdata('NIP');
                $dataForm['EntredAt'] = $this->m_rest->getDateTimeNow();
                $this->db->insert('db_studentlife.master_company', $dataForm);
            }

            return print_r(1);
        } else if ($data_arr['action'] == 'loadMasterCompany') {
            //$data = $this->db->order_by('ID','DESC')->get('db_studentlife.master_company')->result_array();
            /*$this->db->select("a.*,b.name as IndustryName");
            $this->db->from('db_studentlife.master_company a');
            $this->db->join('db_employees.master_industry_type b','b.ID = a.IndustryTypeID','left');
            $this->db->order_by('a.ID','DESC');
            $query = $this->db->get();
            return print_r(json_encode($query->result_array()));*/

            /*UPDATED BY FEBRI @ FEB 2020*/
            $this->load->model('student-life/m_studentlife');
            $QUERY = $this->m_studentlife->fetchCompany()->result_array();
            return print_r(json_encode($QUERY));
            /*END UPDATED BY FEBRI @ FEB 2020*/
        } else if ($data_arr['action'] == 'removeMasterCompany') {
            $ID = $data_arr['ID'];

            // cek apakah ID digunakan atau tidak
            $data = $this->db->get_where('db_studentlife.alumni_experience', array(
                'CompanyID' => $ID
            ))->result_array();

            if (count($data) > 0) {
                $result = array('Status' => 0, 'Msg' => 'Data can not removed');
            } else {
                $this->db->where('ID', $ID);
                $this->db->delete('db_studentlife.master_company');
                $result = array('Status' => 1, 'Msg' => 'Data removed');
            }

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'searchMasterCompany') {
            $Key = $data_arr['Key'];
            $data = $this->db->query('SELECT c.* FROM db_studentlife.master_company c
                                                WHERE c.Name LIKE "%' . $Key . '%" ORDER BY c.Name ASC LIMIT 5 ')->result_array();

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getJobLevel') {
            $JobType = $data_arr['JobType'];
            $data = $this->db->get_where('db_studentlife.job_level', array(
                'JobType' => $JobType
            ))->result_array();
            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'getGraduationYear') {
            $data = $this->db->query('SELECT ac.GraduationYear FROM db_academic.auth_students ac WHERE ac.GraduationYear
                                                GROUP BY ac.GraduationYear
                                                 ORDER BY ac.GraduationYear DESC')->result_array();
            return print_r(json_encode($data));
        }
    }


    function getLanguagelabels()
    {

        $lang = $this->input->get('lang');


        $dataLang = $this->db->get_where('db_prodi.language', array(
            'Code' => $lang
        ))->result_array();

        if (count($dataLang) > 0) {

            $d = $dataLang[0];

            $data = $this->db->query('SELECT li.IndexName, ll.Label FROM db_prodi.language_labels ll
                                                          LEFT JOIN db_prodi.language_index li ON (ll.LangIndexID = li.ID)
                                                          WHERE ll.LangID = "' . $d['ID'] . '" ')->result_array();

            $res = array();
            foreach ($data as $item) {
                $res[$item['IndexName']] = $item['Label'];
            }

            $result = array(
                $d['Code'] => $res
            );

            return print_r(json_encode($result));
        } else {
            return print_r(json_encode(array(
                $lang => array()
            )));
        }
    }

    public function getAllTA_MHS()
    {
        $rs = [];
        $sql = "show databases like '" . 'ta_' . "%'";
        $query = $this->db->query($sql, array())->result_array();
        for ($i = 0; $i < count($query); $i++) {
            $variable = $query[$i];
            foreach ($variable as $key => $value) {
                $ex = explode('_', $value);
                $ta = $ex[1];
                $rs[] = $ta;
            }
        }
        echo json_encode($rs);
    }

    public function crudAlumni()
    {
        $data_arr = $this->getInputToken2();

        if (count($data_arr > 0)) {

            if ($data_arr['action'] == 'searchAlumni') {

                $key = $data_arr['key'];

                $data = $this->db->query('SELECT NPM, Name FROM db_academic.auth_students
                                              WHERE StatusStudentID = "1" AND (NPM LIKE "%' . $key . '%"
                                              OR Name LIKE "%' . $key . '%") ORDER BY NPM ASC LIMIT 5')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'jobLoadAlumni') {

                $NPM = $data_arr['NPM'];

                $data = $this->db->order_by('ID', 'DESC')->get_where('db_studentlife.alumni_experience', array(
                    'NPM' => $NPM
                ))->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'insert2AlumniForm') {
                $dataForm = (array) $data_arr['dataForm'];
                $this->db->insert('db_studentlife.alumni_form', $dataForm);
                $insert_id = $this->db->insert_id();

                $dataAspek = (array) $data_arr['dataAspek'];
                for ($i = 0; $i < count($dataAspek); $i++) {
                    $d = (array) $dataAspek[$i];
                    $d['FormID'] = $insert_id;
                    $this->db->insert('db_studentlife.alumni_form_details', $d);
                }

                return print_r(1);
            } else if ($data_arr['action'] == 'ListYearAlumniForm') {

                $data = $this->db->query('SELECT  af.Year FROM db_studentlife.alumni_form af
                                                  GROUP BY af.Year Order BY af.Year DESC')->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'loadAspekPenilaian') {

                $data = $this->db->query('SELECT * FROM db_studentlife.aspek_penilaian_kepuasan')->result_array();

                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'ListDataAlumniForm') {

                $Year = $data_arr['Year'];
                $data = $this->db->query('SELECT af.*, ats.Name, ats.GraduationYear, ae.Title, c.Name AS Company, ae.StartMonth, ae.StartYear, pl.Description AS Position

                                                    FROM db_studentlife.alumni_form af
                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = af.NPM)
                                                    LEFT JOIN db_studentlife.alumni_experience ae ON (ae.ID = af.IDAE)
                                                    LEFT JOIN db_studentlife.position_level pl ON (pl.ID = ae.PositionLevelID)
                                                    LEFT JOIN db_studentlife.master_company c ON (c.ID = ae.CompanyID)
                                                    WHERE af.Year = "' . $Year . '"
                                                    ORDER BY ats.GraduationYear, ats.Name ASC ')->result_array();

                if (count($data) > 0) {
                    for ($i = 0; $i < count($data); $i++) {
                        $data[$i]['DetailForm'] = $this->db->query('SELECT afd.*, apk.Description FROM db_studentlife.alumni_form_details afd
                                                                              LEFT JOIN db_studentlife.aspek_penilaian_kepuasan apk
                                                                              ON (apk.ID = afd.APKID)
                                                                              WHERE afd.FormID = "' . $data[$i]['ID'] . '"')->result_array();
                    }
                }
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'removeAlumniForm') {
                $ID = $data_arr['ID'];
                $this->db->where('ID', $ID);
                $this->db->delete('db_studentlife.alumni_form');
                return print_r(1);
            } else if ($data_arr['action'] == 'updateAlumniFormRate') {

                $dataForm = (array) $data_arr['dataForm'];

                for ($i = 0; $i < count($dataForm); $i++) {

                    $d = (array) $dataForm[$i];

                    $this->db->where('ID', $d['ID']);
                    $this->db->update('db_studentlife.alumni_form_details', array(
                        'Rate' => '' . $d['Rate']
                    ));
                    $this->db->reset_query();
                }

                return print_r(1);
            }
        }
    }

    public function crudMembersLibrary()
    {


        $data_arr = $this->getInputToken();

        if ($data_arr['action'] == 'readLoans') {

            $dbLib = $this->load->database('server22', TRUE);

            $now = date("Y-m-d");

            $member_id = $data_arr['member_id'];
            $data = $dbLib->query('SELECT l.loan_id, l.member_id, l.loan_date, l.due_date, l.is_lent, l.is_return, b.title, b.image, l.renewed
                                                    FROM library.loan l
                                                    LEFT JOIN library.item i ON (i.item_code = l.item_code)
                                                    LEFT JOIN library.biblio b ON (b.biblio_id = i.biblio_id)
                                                    WHERE l.member_id = "' . $member_id . '" ORDER BY  l.is_return ASC ,l.loan_date DESC,
                                                    l.due_date DESC, b.title ASC')->result_array();

            $dataHoliday = $dbLib->query('SELECT holiday_date FROM library.holiday ORDER BY holiday_id DESC')->result_array();

            $dataHolidayArr = [];
            if (count($dataHoliday) > 0) {
                for ($h = 0; $h < count($dataHoliday); $h++) {
                    array_push($dataHolidayArr, $dataHoliday[$h]['holiday_date']);
                }
            }

            //            print_r($dataHoliday);
            //            exit;

            // Cek dendan

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {
                    $sumDueDate = 0;
                    $d = $data[$i];
                    if ($d['is_return'] == '0' || $d['is_return'] == 0) {

                        // Cek apakah sudah lewat hari ini atau tidak
                        if ($d['due_date'] < $now) {
                            $ckDate = date('Y-m-d', strtotime($d['due_date'] . ' +1 day'));
                            $rageDate = $this->dateRange($ckDate, $now);
                            $sumDueDate = count($rageDate);
                            if (count($rageDate) > 0) {
                                for ($h = 0; $h < count($rageDate); $h++) {

                                    $dt =  date('N', strtotime($rageDate[$h]));
                                    if (in_array($rageDate[$h], $dataHolidayArr) || $dt == '6' || $dt == '7') {
                                        $sumDueDate = $sumDueDate - 1;
                                    }
                                }
                            }
                        }
                    }


                    $data[$i]['SumOfLate'] = $sumDueDate;
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'PerpanjangMandiri') {

            $id = $data_arr['member_id'];
            $loan_id = $data_arr['loan_id'];

            $dbLib = $this->load->database('server22', TRUE);
            $vardate = date("Y-m-d");

            $q = $dbLib->get_where('library.member', array('member_id' => $id))->result_array()[0];
            $q2 = $dbLib->get_where('library.mst_member_type', array('member_type_id' => $q['member_type_id']))->result_array()[0];

            $tot = $q2['loan_periode'];

            $newdate2 = strtotime('+' . $tot . ' day', strtotime($vardate));
            $newdate = date('Y-m-j', $newdate2);

            $a1 = $dbLib->query("SELECT COUNT(*) AS jum FROM library.holiday WHERE holiday_date BETWEEN '$vardate' AND '$newdate' ")->result_array();
            //        $q1=mysql_fetch_array($a1);

            //            print_r($a1);exit;
            $libur = $a1[0]['jum'];

            $lm_pinjam1 = strtotime('+' . $libur . ' day', strtotime($newdate));
            $lm_pinjam = date('Y-m-j', $lm_pinjam1);

            //            print_r($lm_pinjam);

            // mysql_query("UPDATE `loan` SET `due_date` = '$day',`renewed` = '1', return_date = '$now' WHERE `loan_id` = '$loan_id';");

            $now = date("Y-m-d");

            $dbLib->where('loan_id', $loan_id);
            $dbLib->update('library.loan', array(
                'due_date' => $lm_pinjam,
                'renewed' => '1',
                'return_date' => $now
            ));

            return print_r(1);
        }
    }


    function dateRange($first, $last, $step = '+1 day', $format = 'Y-m-d')
    {

        $dates = array();
        $current = strtotime($first);
        $last = strtotime($last);

        while ($current <= $last) {

            $dates[] = date($format, $current);
            $current = strtotime($step, $current);
        }

        return $dates;
    }

    public function crudMedicalRecord()
    {

        $data_arr = $this->getInputToken2();

        if (count($data_arr) > 0) {

            if ($data_arr['action'] == 'setDataMedicalRecord') {

                $dataForm = (array) $data_arr['dataForm'];

                $Updated = $data_arr['Updated'];

                if ($data_arr['ID'] != '') {
                    $dataForm['Updated'] = $Updated;
                    $dataForm['UpdatedBy'] = $data_arr['UserID'];
                    $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->where('ID', $data_arr['ID']);
                    $this->db->update('db_studentlife.medical_record', $dataForm);
                } else {
                    $dataForm['CreatedBy'] = $data_arr['UserID'];
                    $dataForm['CreatedAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->insert('db_studentlife.medical_record', $dataForm);
                }
                return print_r(1);
            } else if ($data_arr['action'] == 'getDataMedicalRecord') {

                $requestData = $_REQUEST;

                $WhereProdiID = ($data_arr['ProdiID'] != '') ? ' WHERE ats.ProdiID = "' . $data_arr['ProdiID'] . '" ' : '';

                $dataSearch = '';
                if (!empty($requestData['search']['value'])) {
                    $search = $requestData['search']['value'];
                    $dataSearch = ($WhereProdiID != '')
                        ? ' AND ( mr.DiseaseName LIKE "%' . $search . '%" OR
                                mr.TreatedAt LIKE "%' . $search . '%" OR mr.PersonalDoctorName LIKE "%' . $search . '%" OR
                                mr.Allergy LIKE "%' . $search . '%" OR mr.PersonalDoctorName LIKE "%' . $search . '%" OR
                                ats.Name LIKE "%' . $search . '%" OR ats.NPM LIKE "%' . $search . '%" )'
                        : 'WHERE  mr.DiseaseName LIKE "%' . $search . '%" OR
                                mr.TreatedAt LIKE "%' . $search . '%" OR mr.PersonalDoctorName LIKE "%' . $search . '%" OR
                                mr.Allergy LIKE "%' . $search . '%" OR mr.PersonalDoctorName LIKE "%' . $search . '%" OR
                                ats.Name LIKE "%' . $search . '%" OR ats.NPM LIKE "%' . $search . '%" ';
                }

                $queryDefault = 'SELECT ats.Name, ats.NPM, mr.*, ps.Name AS ProdiInd FROM db_studentlife.medical_record mr
                                                LEFT JOIN db_academic.auth_students ats ON (ats.NPM = mr.NPM)
                                                LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                LEFT JOIN db_academic.status_student ss ON (ss.ID = ats.StatusStudentID)
                                                 ' . $WhereProdiID . $dataSearch . ' ORDER BY ats.NPM ';


                $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

                $query = $this->db->query($sql)->result_array();
                $queryDefaultRow = $this->db->query($queryDefault)->result_array();

                $no = $requestData['start'] + 1;
                $data = array();

                for ($i = 0; $i < count($query); $i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $SickDateStart = ($row['SickDateStart'] != '' && $row['SickDateStart'] != null)
                        ? date('d M Y', strtotime($row['SickDateStart'])) : '';

                    $SickDateEnd = ($row['SickDateEnd'] != '' && $row['SickDateEnd'] != null)
                        ? date('d M Y', strtotime($row['SickDateEnd'])) : '';

                    $duration = '';
                    if ($SickDateStart != '' && $SickDateEnd != '') {
                        $duration = 'Duration : ' . $SickDateStart . ' - ' . $SickDateEnd;
                    } else if ($SickDateStart == '' && $SickDateEnd != '') {
                        $duration = ' End : ' . $SickDateEnd;
                    } else if ($SickDateStart != '' && $SickDateEnd == '') {
                        $duration = 'Start : ' . $SickDateStart;
                    }

                    $DiseaseName = ($row['DiseaseName'] != '' && $row['DiseaseName'] != null) ? $row['DiseaseName'] : '';
                    $Treated = ($row['TreatedAt'] != '' && $row['TreatedAt'] != null) ? $row['TreatedAt'] : '';
                    $PersonalDoctorName = ($row['PersonalDoctorName'] != '' && $row['PersonalDoctorName'] != null) ? '<div>' . $row['PersonalDoctorName'] . '</div>' : '';
                    $Allergy = ($row['Allergy'] != '' && $row['Allergy'] != null) ? '<div>' . $row['Allergy'] . '</div>' : '';

                    $btnAction = '<div class="dropdown">
                                  <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="fa fa-edit"></i>
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a href="javascript:void(0);" data-id="' . $row['ID'] . '" class="btnEditMedicalRegord">Edit</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0);" data-id="' . $row['ID'] . '" class="btnRemoveMedicalRegord">Remove</a></li>
                                  </ul>
                                </div>';

                    $nestedData[] = '<div>' . $no . '</div>';
                    $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b><br/>' . $row['NPM'] . '<br/>' . $row['ProdiInd'] . '</div>';
                    $nestedData[] = '<div style="text-align: left;">' . $DiseaseName . '</div>';
                    $nestedData[] = '<div style="text-align: left;">' . $Treated . '<div>' . $duration . '</div>' . $PersonalDoctorName . '</div>';
                    $nestedData[] = '<div style="text-align: left;">' . $Allergy . '</div>';
                    $nestedData[] = '<div style="text-align: left;">' . $PersonalDoctorName . '</div>';
                    $nestedData[] = '<div>' . $btnAction . '<textarea id="txt_' . $row['ID'] . '" class="hide">' . json_encode($row) . '</textarea></div>';

                    $data[] = $nestedData;
                    $no++;
                }

                $json_data = array(
                    "draw"            => intval($requestData['draw']),
                    "recordsTotal"    => intval(count($queryDefaultRow)),
                    "recordsFiltered" => intval(count($queryDefaultRow)),
                    "data"            => $data
                );
                echo json_encode($json_data);
            } else if ($data_arr['action'] == 'getMyDataMedicalRecord') {
                $UserID = $data_arr['UserID'];
                $data = $this->db->get_where('db_studentlife.medical_record', array('NPM' => $UserID))->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'getMyDataMedicalHistory') {
                $UserID = $data_arr['UserID'];
                $data = $this->db->get_where('db_studentlife.medical_history', array('NPM' => $UserID))->result_array();
                return print_r(json_encode($data));
            } else if ($data_arr['action'] == 'removeDataMedicalRecord') {
                $MedicalRecordID = $data_arr['ID'];
                $this->db->where('ID', $MedicalRecordID);
                $this->db->delete('db_studentlife.medical_record');
                return print_r(1);
            } else if ($data_arr['action'] == 'removeDataMedicalHistory') {
                $MedicalRecordID = $data_arr['ID'];
                $this->db->where('ID', $MedicalRecordID);
                $this->db->delete('db_studentlife.medical_history');
                return print_r(1);
            } else if ($data_arr['action'] == 'setDataMedicalHistory') {

                $dataForm = (array) $data_arr['dataForm'];

                if ($data_arr['ID'] != '') {
                    $dataForm['UpdatedBy'] = $data_arr['UserID'];
                    $dataForm['UpdatedAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->where('ID', $data_arr['ID']);
                    $this->db->update('db_studentlife.medical_history', $dataForm);
                } else {
                    $dataForm['EntredBy'] = $data_arr['UserID'];
                    $dataForm['EntredAt'] = $this->m_rest->getDateTimeNow();
                    $this->db->insert('db_studentlife.medical_history', $dataForm);
                }
                return print_r(1);
            } else if ($data_arr['action'] == 'getDataMedicalHistory') {

                $requestData = $_REQUEST;

                $WhereProdiID = ($data_arr['ProdiID'] != '') ? ' WHERE ats.ProdiID = "' . $data_arr['ProdiID'] . '" ' : '';

                $dataSearch = '';
                if (!empty($requestData['search']['value'])) {
                    $search = $requestData['search']['value'];
                    $dataSearch = ($WhereProdiID != '')
                        ? ' AND ( mh.Description LIKE "%' . $search . '%" OR
                                mh.Executor LIKE "%' . $search . '%" OR
                                ats.Name LIKE "%' . $search . '%" OR ats.NPM LIKE "%' . $search . '%" )'
                        : 'WHERE  mh.Description LIKE "%' . $search . '%" OR
                                mh.Executor LIKE "%' . $search . '%" OR
                                ats.Name LIKE "%' . $search . '%" OR ats.NPM LIKE "%' . $search . '%" ';
                }

                $queryDefault = 'SELECT ats.Name, ats.NPM, mh.*, ps.Name AS ProdiInd FROM db_studentlife.medical_history mh
                                                LEFT JOIN db_academic.auth_students ats ON (ats.NPM = mh.NPM)
                                                LEFT JOIN db_academic.program_study ps ON (ps.ID = ats.ProdiID)
                                                LEFT JOIN db_academic.status_student ss ON (ss.ID = ats.StatusStudentID)
                                                 ' . $WhereProdiID . $dataSearch . ' ORDER BY ats.NPM ';


                $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

                $query = $this->db->query($sql)->result_array();
                $queryDefaultRow = $this->db->query($queryDefault)->result_array();

                $no = $requestData['start'] + 1;
                $data = array();

                for ($i = 0; $i < count($query); $i++) {

                    $nestedData = array();
                    $row = $query[$i];

                    $IncidentDate = ($row['IncidentDate'] != '' && $row['IncidentDate'] != null)
                        ? date('d M Y', strtotime($row['IncidentDate'])) : '';

                    $btnAction = '<div class="dropdown">
                                  <button class="btn btn-sm btn-default dropdown-toggle" type="button" id="dropdownMenu1" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
                                    <i class="fa fa-edit"></i>
                                    <span class="caret"></span>
                                  </button>
                                  <ul class="dropdown-menu" aria-labelledby="dropdownMenu1">
                                    <li><a href="javascript:void(0);" data-id="' . $row['ID'] . '" class="btnEditMedicalHistory">Edit</a></li>
                                    <li role="separator" class="divider"></li>
                                    <li><a href="javascript:void(0);" data-id="' . $row['ID'] . '" class="btnRemoveMedicalHistory">Remove</a></li>
                                  </ul>
                                </div>';

                    $nestedData[] = '<div>' . $no . '</div>';
                    $nestedData[] = '<div style="text-align: left;"><b>' . $row['Name'] . '</b><br/>' . $row['NPM'] . '<br/>' . $row['ProdiInd'] . '</div>';
                    $nestedData[] = '<div>' . $IncidentDate . '</div>';
                    $nestedData[] = '<div style="text-align: left;">' . $row['Description'] . '</div>';
                    $nestedData[] = '<div style="text-align: left;">' . $row['Executor'] . '</div>';
                    $nestedData[] = '<div>' . $btnAction . '<textarea id="txt_' . $row['ID'] . '" class="hide">' . json_encode($row) . '</textarea></div>';

                    $data[] = $nestedData;
                    $no++;
                }

                $json_data = array(
                    "draw"            => intval($requestData['draw']),
                    "recordsTotal"    => intval(count($queryDefaultRow)),
                    "recordsFiltered" => intval(count($queryDefaultRow)),
                    "data"            => $data
                );
                echo json_encode($json_data);
            }
        }
    }

    public function crudStudentReport()
    {
        $data_arr = $this->getInputToken2();
        $data_arr =  json_decode(json_encode($data_arr), true);
        $this->load->model('ticketing/m_ticketing');
        if ($data_arr['action'] == 'inputStudentReport') {
            $data_get = $data_arr['data'];
            $arrInsert = array(
                'NPM' => $data_get['NPM'],
                'Title' => $data_get['Title'],
                'Description' => $data_get['Description'],
                'Status' => '0',
                'EntredAt' => $this->m_rest->getDateTimeNow()
            );

            // upload files to nas
            if (array_key_exists('Files', $_FILES)) {
                $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                $path = 'ticketing';
                $filename = 'Student_' . $data_get['NPM'];
                $uploadNas = $this->m_master->UploadManyFilesToNas($headerOrigin, $filename, 'Files', $path, 'array');
                $uploadNas = $uploadNas[0];
                $arrInsert['Files'] = $uploadNas;
            }

            $this->db->insert('db_ticketing.ss_report', $arrInsert);
            $ID = $this->db->insert_id();

            $rand = $this->m_api->chekCodeReport();

            $this->db->set('ReportNumber', $rand);
            $this->db->where('ID', $ID);
            $this->db->update('db_ticketing.ss_report');

            return print_r(1);
        } else if ($data_arr['action'] == 'getStudentReportService') {

            // 0 = Open, 1 = On Process, 2 = Close

            $dataOpen = $this->db->query('SELECT COUNT(*) AS Total FROM db_ticketing.ss_report WHERE Status = "0" ')->result_array();
            $dataProgress = $this->db->query('SELECT COUNT(*) AS Total FROM db_ticketing.ss_report WHERE Status = "1" ')->result_array();

            $result = array(
                'Open' => $dataOpen[0]['Total'],
                'Progress' => $dataProgress[0]['Total']
            );

            return print_r(json_encode($result));
        } else if ($data_arr['action'] == 'readStudentReport') {
            $NPM = $data_arr['NPM'];


            $q = ' SELECT d.*, c.UpdatedAt
                            FROM db_ticketing.ss_report_last_update a
                            LEFT JOIN ( SELECT b.ID, b.IDReport, MAX(b.UpdatedAt) AS UpdatedAt FROM db_ticketing.ss_report_last_update b GROUP BY b.IDReport) c ON c.IDReport = a.IDReport
                            LEFT JOIN db_ticketing.ss_report d ON a.IDReport = d.ID
                            WHERE d.NPM = "' . $NPM . '"
                            GROUP BY d.ID
                            ORDER BY c.UpdatedAt DESC';

            $data = $this->db->query($q)->result_array();

            if (count($data) > 0) {
                for ($i = 0; $i < count($data); $i++) {

                    // Response
                    $data[$i]['Response'] = $this->db->query('SELECT srr.*, em.Name AS UpdatedAdmin, ats.Name AS UpdatedUser, em.Photo FROM db_ticketing.ss_report_response srr
                                                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM = srr.EnrtedBy)
                                                                            LEFT JOIN db_employees.employees em ON (em.NIP = srr.EnrtedBy)
                                                                            WHERE srr.IDReport = "' . $data[$i]['ID'] . '" ')->result_array();

                    // Last Update
                    $data[$i]['LastUpdate'] = $this->db->query('SELECT srlu.UpdatedAt, em.Name AS UpdatedAdmin, ats.Name AS UpdatedUser FROM db_ticketing.ss_report_last_update srlu
                                                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM = srlu.UpdatedBy)
                                                                            LEFT JOIN db_employees.employees em ON (em.NIP = srlu.UpdatedBy)
                                                                            WHERE srlu.IDReport = "' . $data[$i]['ID'] . '"
                                                                            ORDER BY srlu.ID DESC LIMIT 1')->result_array();
                }
            }

            return print_r(json_encode($data));
        } else if ($data_arr['action'] == 'studentReportClose') {

            $this->db->set('Status', '2');
            $this->db->where('ID', $data_arr['IDReport']);
            $this->db->update('db_ticketing.ss_report');

            return print_r(1);
        } else if ($data_arr['action'] == 'studentReportInsertRespinse') {
            $dataForm = (array) $data_arr['dataForm'];
            // upload files to nas
            if (array_key_exists('Files', $_FILES)) {
                $headerOrigin = ($_SERVER['SERVER_NAME'] == 'localhost') ? "http://localhost" : serverRoot;
                $path = 'ticketing';
                $filename = 'Academic_' . $dataForm['EnrtedBy'];
                $uploadNas = $this->m_master->UploadManyFilesToNas($headerOrigin, $filename, 'Files', $path, 'array');
                $uploadNas = $uploadNas[0];
                $dataForm['Files'] = $uploadNas;
            }

            $this->db->insert('db_ticketing.ss_report_response', $dataForm);

            if ($dataForm['EntredType'] == '1') {
                $this->db->set('Status', '1');
                $this->db->where('ID', $dataForm['IDReport']);
                $this->db->update('db_ticketing.ss_report');
            }

            return print_r($dataForm['IDReport']);
        } else if ($data_arr['action'] == 'admin_getListReport') {

            $requestData = $_REQUEST;

            $Status = $data_arr['Status'];
            $WhereSts = ($Status != '') ? 'WHERE sr.Status = "' . $Status . '"' : '';
            $dataSearch = '';
            if (!empty($requestData['search']['value'])) {
                $search = $requestData['search']['value'];

                $dataSearch = ($WhereSts != '')
                    ? $WhereSts . ' sr.NPM LIKE "%' . $search . '%" OR
                                sr.ReportNumber LIKE "%' . $search . '%" OR
                                sr.Title LIKE "%' . $search . '%" OR
                                ats.Name LIKE "%' . $search . '%" '
                    : 'WHERE sr.NPM LIKE "%' . $search . '%" OR
                                sr.ReportNumber LIKE "%' . $search . '%" OR
                                sr.Title LIKE "%' . $search . '%" OR
                                ats.Name LIKE "%' . $search . '%" ';
            }

            $queryDefault = 'SELECT sr.*, ats.Name FROM db_ticketing.ss_report sr
                                        LEFT JOIN db_academic.auth_students ats ON (ats.NPM = sr.NPM)
                                        ' . $WhereSts . $dataSearch . ' ORDER BY sr.ID DESC';


            $sql = $queryDefault . ' LIMIT ' . $requestData['start'] . ',' . $requestData['length'] . ' ';

            $query = $this->db->query($sql)->result_array();
            $queryDefaultRow = $this->db->query($queryDefault)->result_array();

            $no = $requestData['start'] + 1;
            $data = array();

            for ($i = 0; $i < count($query); $i++) {

                $nestedData = array();
                $row = $query[$i];

                $Sts = 'Open';
                if ($row['Status'] == '1') {
                    $Sts = 'On Process';
                } else if ($row['Status'] == '2') {
                    $Sts = 'Close';
                }

                $Created = date('d M Y H:i', strtotime($row['EntredAt']));


                // Response
                $Response = $this->db->query('SELECT COUNT(*) AS TotalResponse FROM db_ticketing.ss_report_response srr  WHERE srr.IDReport = "' . $row['ID'] . '" ')->result_array();

                // Last Update
                $LastUpdate = $this->db->query('SELECT srlu.UpdatedAt, em.Name AS UpdatedAdmin, ats.Name AS UpdatedUser FROM db_ticketing.ss_report_last_update srlu
                                                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM = srlu.UpdatedBy)
                                                                            LEFT JOIN db_employees.employees em ON (em.NIP = srlu.UpdatedBy)
                                                                            WHERE srlu.IDReport = "' . $row['ID'] . '"
                                                                            ORDER BY srlu.ID DESC LIMIT 1')->result_array();

                $LastUpdateAt = '';
                $LastUpdateBy = '';
                if (count($LastUpdate) > 0) {
                    $LastUpdateAt = date('d M Y H:i', strtotime($LastUpdate[0]['UpdatedAt']));
                    $LastUpdateBy = ($LastUpdate[0]['UpdatedAdmin'] != '' && $LastUpdate[0]['UpdatedAdmin'] != null) ? $LastUpdate[0]['UpdatedAdmin'] : $LastUpdate[0]['UpdatedUser'];
                }

                $nestedData[] = '<div style="text-align: center;">' . $no . '</div>';
                $nestedData[] = '<div><b>' . $row['Name'] . '</b><br/>' . $row['NPM'] . '<br/><i class="fa fa-hashtag"></i> ' . $row['ReportNumber'] . '</div>';

                // get files
                $htmlFiles = '';
                if ($row['Files'] != NULL && !empty($row['Files'])) {
                    $pathfolder = ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') ? "pcam/ticketing/" : "localhost/ticketing/";
                    $FilePath = $pathfolder . $row['Files'];
                    $tokenFiles = $this->jwt->encode($FilePath, "UAP)(*");
                    $urlFiles = url_files . "fileGetAnyToken/" . $tokenFiles;

                    $htmlFiles = '<div style="margin-bottom: 5px;">
                                    <a href= "' . $urlFiles . '" target="_blank">Files Upload<a>
                                </div>';
                }


                // btn create ticket
                $BtnCreateTicket = '';
                if ($row['Status'] != '2') {
                    if ($row['TicketRelation'] == NULL) {
                        $BtnCreateTicket = '| <a class = "btn btn-default btn-sm CreateTicketFromStd" data-id="' . $row['ID'] . '"> Create Ticket </a>';
                    } else {
                        $BtnCreateTicket = '| <span style ="color:green;" >Ticket : ' . $row['TicketRelation'] . '</span>';
                    }
                }

                if ($row['TicketRelation'] != NULL && $row['TicketRelation'] != '') {
                    $BtnCreateTicket = '| <span style ="color:green;" >Ticket : ' . $row['TicketRelation'] . '</span>';
                }

                $nestedData[] = '<div><h4 style="margin-bottom: 3px;margin-top: 0px;">' . $row['Title'] . '</h4><div style="margin-bottom: 5px;">Created : ' . $Created . '</div><div class="well" style="max-height: 150px;overflow: auto;"><p>' . $row['Description'] . '</p></div>
                    ' . $htmlFiles . '
                                        <a class="btn btn-primary btn-sm showDataResponse" data-id="' . $row['ID'] . '"><i class="fa fa-comment margin-right"></i> ' . $Response[0]['TotalResponse'] . ' Response</a> ' . $BtnCreateTicket . ' | <span style="font-size: 11px;color: #848484;">Last Updated : ' . $LastUpdateAt . ' By ' . $LastUpdateBy . '</span></div>';
                $nestedData[] = '<div style="text-align: center;">' . $Sts . '</div>';

                $data[] = $nestedData;
                $no++;
            }

            $json_data = array(
                "draw"            => intval($requestData['draw']),
                "recordsTotal"    => intval(count($queryDefaultRow)),
                "recordsFiltered" => intval(count($queryDefaultRow)),
                "data"            => $data
            );
            echo json_encode($json_data);
        } else if ($data_arr['action'] == 'getStudentReportResponse') {
            $ID = $data_arr['ID'];

            $data = $this->db->query('SELECT sr.*, ats.Name FROM db_ticketing.ss_report sr
                                                    LEFT JOIN db_academic.auth_students ats ON (ats.NPM = sr.NPM)
                                                    WHERE sr.ID = "' . $ID . '" ')->result_array();

            if (count($data) > 0) {
                $data[0]['Response'] = $this->db->query('SELECT srr.*, em.Name AS UpdatedAdmin, ats.Name AS UpdatedUser FROM db_ticketing.ss_report_response srr
                                                                            LEFT JOIN db_academic.auth_students ats ON (ats.NPM = srr.EnrtedBy)
                                                                            LEFT JOIN db_employees.employees em ON (em.NIP = srr.EnrtedBy)
                                                                            WHERE srr.IDReport = "' . $ID . '" ')->result_array();
            }



            return print_r(json_encode($data));
        } elseif ($data_arr['action'] = 'studentReportCreateTicket') {
            $data_post =  [];
            $data_get = $data_arr['dataForm'];
            $IDReport = $data_get['IDReport'];
            $CategoryID = $data_get['CategoryID'];
            $RequestedBy = $data_get['RequestedBy'];
            $DepartmentTicketID = $data_get['DepartmentTicketID'];
            $DepartmentAbbr = $data_get['DepartmentAbbr'];
            $Apikey = $data_get['Apikey'];
            $Hjwtkey = $data_get['Hjwtkey'];

            $G_dt_report = $this->m_master->caribasedprimary('db_ticketing.ss_report', 'ID', $IDReport);
            $Title = $G_dt_report[0]['Title'];
            $Message = $G_dt_report[0]['Description'];
            $data_post = [
                'action' => "create",
                'data' => [
                    'CategoryID' => $CategoryID,
                    'Title' => $Title,
                    'Message' => $Message,
                    'RequestedBy' => $RequestedBy,
                    'DepartmentTicketID' => $DepartmentTicketID,
                ],
                'auth' => 's3Cr3T-G4N',
                'DepartmentAbbr' => $DepartmentAbbr,
            ];
            $fileattach = [];
            $urlPost = base_url() . 'rest_ticketing/__event_ticketing';
            $customPost = [
                'get' => $Apikey,
                'header' => [
                    'Hjwtkey' => $Hjwtkey,
                ],

            ];
            // download file if existing
            $DownloadFiles = [];
            if ($G_dt_report[0]['Files'] != NULL && $G_dt_report[0]['Files'] != ''  && !empty($G_dt_report[0]['Files'])) {
                $pathfolder = ($_SERVER['SERVER_NAME'] == 'pcam.podomorouniversity.ac.id') ? "pcam/ticketing/" : "localhost/ticketing/";
                $FilePath = 'uploads/' . $pathfolder . $G_dt_report[0]['Files'];
                $urlFiles = url_files . $FilePath;
                $DownloadFiles = $this->m_master->downloadByURL($urlFiles);
                $fileattach = [
                    'file_name_with_full_path' => $DownloadFiles['path'],
                    'MimeType' => $this->m_master->MimeType($DownloadFiles['path']),
                    'filename' => $DownloadFiles['filename'],
                    'varfiles' => 'Files',
                ];
            }

            $postTicket = $this->m_master->PostSubmitAPIWithFile($urlPost, $data_post, $fileattach, $customPost);
            if (array_key_exists('callback', $postTicket) && array_key_exists('NoTicket', $postTicket['callback'])) {
                // update relation
                $dataSave = [
                    'TicketRelation' => $postTicket['callback']['NoTicket'],
                ];

                $this->db->where('ID', $IDReport);
                $this->db->update('db_ticketing.ss_report', $dataSave);

                // delete files temp
                if (array_key_exists('filename', $DownloadFiles)) {
                    $pathTemp =  './uploads/temp/' . $DownloadFiles['filename'];
                    if (file_exists($pathTemp)) {
                        unlink($pathTemp);
                    }
                }
            }


            echo json_encode($postTicket);
        }
    }

    public function crudBKD()
    {

        $data_arr = $this->getInputToken2();

        if (count($data_arr > 0)) {

            if ($data_arr['action'] == 'bkdShowingCredit') {

                $SemesterID = $data_arr['SemesterID'];
                $StatusEmployeeID = $data_arr['StatusEmployeeID'];
                $StatusLecturerID = $data_arr['StatusLecturerID'];
                $ProdiID = $data_arr['ProdiID'];

                $whereProdi = ($ProdiID != '') ? ' AND em.ProdiID = "' . $ProdiID . '" ' : '';

                $dataLecturer = $this->db->query('SELECT NIP, Name FROM db_employees.employees em
                                                  WHERE em.StatusEmployeeID = "' . $StatusEmployeeID . '" AND em.StatusLecturerID = "' . $StatusLecturerID . '"
                                                  ' . $whereProdi . '
                                                  ORDER BY em.NIP ASC ')->result_array();


                if (count($dataLecturer) > 0) {
                    for ($i = 0; $i < count($dataLecturer); $i++) {
                        $d = $dataLecturer[$i];

                        $dataAllCourse = [];

                        $dataSch = $this->db->query('SELECT s.ID AS ScheduleID, s.ClassGroup, mk.MKCode, mk.Name, mk.NameEng, cd.TotalSKS AS CreditMK FROM db_academic.schedule s
                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                        LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        WHERE s.SemesterID = "' . $SemesterID . '"
                                                        AND s.Coordinator = "' . $d['NIP'] . '"
                                                        GROUP BY s.ID')->result_array();

                        if (count($dataSch) > 0) {
                            for ($a = 0; $a < count($dataSch); $a++) {
                                $dataTeam = $this->db->query('SELECT em.NIP, em.Name, "0" AS IsCoordinator FROM db_academic.schedule_team_teaching stt
                                                                LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                                WHERE stt.ScheduleID = "' . $dataSch[$a]['ScheduleID'] . '" ')->result_array();

                                $Single = 1;
                                if (count($dataTeam) > 0) {
                                    $Single = 0;
                                }
                                $dataSch[$a]['Single'] = $Single;
                                $dataSch[$a]['DetailTeam'] = $dataTeam;

                                array_push($dataAllCourse, $dataSch[$a]);
                            }
                        }

                        $dataSchTeam = $this->db->query('SELECT s.ID AS ScheduleID, s.ClassGroup, mk.MKCode, mk.Name, mk.NameEng, cd.TotalSKS AS CreditMK,
                                                        em.NIP AS CoordinatorNIP, em.Name AS CoordinatorName, 0 AS Single
                                                        FROM db_academic.schedule s
                                                        LEFT JOIN db_academic.schedule_details_course sdc ON (sdc.ScheduleID = s.ID)
                                                        LEFT JOIN db_academic.schedule_team_teaching stt ON (stt.ScheduleID = s.ID)
                                                        LEFT JOIN db_academic.curriculum_details cd ON (cd.ID = sdc.CDID)
                                                        LEFT JOIN db_academic.mata_kuliah mk ON (mk.ID = sdc.MKID)
                                                        LEFT JOIN db_employees.employees em ON (em.NIP = s.Coordinator)
                                                        WHERE s.SemesterID = "' . $SemesterID . '"
                                                        AND stt.NIP = "' . $d['NIP'] . '"
                                                        GROUP BY s.ID')->result_array();


                        if (count($dataSchTeam) > 0) {
                            for ($a = 0; $a < count($dataSchTeam); $a++) {
                                $dataTeamTeaching = $this->db->query('SELECT stt.NIP, em.Name, "0" AS IsCoordinator FROM db_academic.schedule_team_teaching stt
                                                                        LEFT JOIN db_employees.employees em ON (em.NIP = stt.NIP)
                                                                        WHERE stt.ScheduleID = "' . $dataSchTeam[$a]['ScheduleID'] . '"
                                                                        AND stt.NIP != "' . $d['NIP'] . '" ')->result_array();

                                array_push($dataTeamTeaching, array(
                                    'NIP' => $dataSchTeam[$a]['CoordinatorNIP'],
                                    'Name' => $dataSchTeam[$a]['CoordinatorName'],
                                    'IsCoordinator' => '1',
                                ));


                                $arrPush = array(
                                    'ScheduleID' => $dataSchTeam[$a]['ScheduleID'],
                                    'ClassGroup' => $dataSchTeam[$a]['ClassGroup'],
                                    'MKCode' => $dataSchTeam[$a]['MKCode'],
                                    'Name' => $dataSchTeam[$a]['Name'],
                                    'NameEng' => $dataSchTeam[$a]['NameEng'],
                                    'CreditMK' => $dataSchTeam[$a]['CreditMK'],
                                    'Single' => '0',
                                    'DetailTeam' => $dataTeamTeaching
                                );
                                array_push($dataAllCourse, $arrPush);
                            }
                        }




                        // ==============================
                        // Mengambil data jadwal

                        if (count($dataAllCourse) > 0) {
                            for ($a = 0; $a < count($dataAllCourse); $a++) {

                                $TotalTeam = count($dataAllCourse[$a]['DetailTeam']) + 1;
                                $CreditBKD = ($dataAllCourse[$a]['Single'] == '0') ? (int) $dataAllCourse[$a]['CreditMK'] / $TotalTeam : $dataAllCourse[$a]['CreditMK'];


                                $dataAllCourse[$a]['CreditBKD'] = (is_int($CreditBKD)) ? $CreditBKD : round($CreditBKD, 2);

                                $dataAllCourse[$a]['Schedule'] = $this->db->query('SELECT sd.Credit, sd.DayID, cl.Room, sd.StartSessions, sd.EndSessions, d.NameEng AS DayNameEng
                                                                                    FROM db_academic.schedule_details sd
                                                                                    LEFT JOIN db_academic.classroom cl ON (cl.ID = sd.ClassroomID)
                                                                                    LEFT JOIN db_academic.days d ON (d.ID = sd.DayID)
                                                                                    WHERE sd.ScheduleID = "' . $dataAllCourse[$a]['ScheduleID'] . '" ')->result_array();
                            }
                        }


                        // ==============================

                        $dataLecturer[$i]['Course'] = $dataAllCourse;
                    }
                }

                //        print_r($dataLecturer);
                //
                //        exit;
                $data['dataLecturer'] = $dataLecturer;

                $data['SemesterID'] = $SemesterID;
                $data['ProdiID'] = $ProdiID;
                $data['StatusEmployeeID'] = $StatusEmployeeID;
                $data['StatusLecturerID'] = $StatusLecturerID;


                return print_r(json_encode($data));
            }
        }
    }
}
