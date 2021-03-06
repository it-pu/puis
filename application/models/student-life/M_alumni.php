<?php
defined('BASEPATH') OR exit('No direct script access allowed');
 
class M_alumni extends CI_Model {
 
    var $table = 'db_alumni.alumni_content';
    var $column_order = array('TitleContent','Description','Status',null); //set column field database for datatable orderable
    var $column_search = array('TitleContent','Description','Status'); //set column field database for datatable searchable just firstname , lastname , address are searchable
    var $order = array('ID_Content' => 'desc'); // default order 
    
    public function __construct()
    {
        parent::__construct();
        // $this->load->database();
    }
 
    private function _get_datatables_query()
    {
        if($this->input->post('type')){
            $sql = 'select ci.* from db_alumni.alumni_content_index AS ci
                            WHERE ci.SegmentMenu="'.$this->input->post('type').'" ';
            $query=$this->db->query($sql, array())->result_array();
            $getvaID= $query[0]['ID'];  
            $this->db->where('IDindex', $getvaID);
        }
        
        $this->db->from('db_alumni.alumni_content'); 
        $i = 0;
        
        // if(!isset($_POST['category']))
        // {
        //     $this->db->join('db_alumni.category', 'db_alumni.content.IDCat = db_alumni.category.ID');

        // }

        foreach ($this->column_search as $item) // loop column 
        {
            if($_POST['search']['value']) // if datatable send POST for search
            {
                 
                if($i===0) // first loop
                {
                    $this->db->group_start(); // open bracket. query Where with OR clause better with bracket. because maybe can combine with other WHERE with AND.

                    $this->db->like($item, $_POST['search']['value']);
                }
                else
                {
                    $this->db->or_like($item, $_POST['search']['value']);
                }
 
                if(count($this->column_search) - 1 == $i) //last loop
                    $this->db->group_end(); //close bracket
            }
            $i++;
        }
         
        if(isset($_POST['order'])) // here order processing
        {
            $this->db->order_by($this->column_order[$_POST['order']['0']['column']], $_POST['order']['0']['dir']);
        } 
        else if(isset($this->order))
        {
            $order = $this->order;
            $this->db->order_by(key($order), $order[key($order)]);
        }

    }
 
    function get_datatables()
    {
        $this->_get_datatables_query();
        if($_POST['length'] != -1)
        $this->db->limit($_POST['length'], $_POST['start']);
        $query = $this->db->get();
        return $query->result();
    }
 
    function count_filtered()
    {
        $this->_get_datatables_query();
        $query = $this->db->get();
        return $query->num_rows();
    }
 
    public function count_all()
    {
        $this->db->from($this->table);
        return $this->db->count_all_results();
    }    
    
    public function get_by_id($id)
    {
        $this->db->from('db_alumni.alumni_content as co');
        // $this->db->join('db_alumni.sub_category as sb','sb.IDSub=co.IDSubCat');          
        // $this->db->join('db_alumni.category as cat','sb.IDSub=cat.ID');  
        $this->db->where('ID_Content',$id);     
        $query = $this->db->get(); 
        return $query->row();
    }
    
    public function get_by_idCat($id)
    {
        $this->db->from('db_alumni.category');
        $this->db->where('ID',$id);
        $query = $this->db->get(); 
        return $query->row();
    }

    public function save($data,$type)
    {   
        $sql = 'select ci.* from db_alumni.alumni_content_index AS ci
                            WHERE ci.SegmentMenu="'.$type.'" ';
        $query=$this->db->query($sql, array())->result_array();
        $data['IDindex'] = $query[0]['ID'];
         // print_r($data);die();
        $this->db->insert($this->table, $data);
        return $this->db->insert_id();
    }

 
    public function update($where, $data)
    {
        $this->db->update($this->table, $data, $where);
        return $this->db->affected_rows();
    }    
    

    public function delete_by_id($id)
    {
        $this->db->where('ID_Content', $id);
        $this->db->delete($this->table);
    }

    
 
 
}
