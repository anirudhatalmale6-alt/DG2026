<?php

use app\services\utilities\Arr;

defined('BASEPATH') or exit('No direct script access allowed');

class Employee_return_model extends App_Model
{
    public function __construct()
    {
        parent::__construct();
    }

    private function clean_input_data($data)
    {
        // Fetch table columns
        $fields = $this->db->list_fields('tbl_monthly_employees_return');
        // Remove keys from $data that are not in $fields
        return array_filter($data, function ($key) use ($fields) {
            return in_array($key, $fields);
        }, ARRAY_FILTER_USE_KEY);
    }

    public function add($data)
    {
    
        try {
            $data = $this->clean_input_data($data); // Clean the data
    
            if ($this->db->insert('tbl_monthly_employees_return', $data)) {
                return $this->db->insert_id(); // Return the inserted ID if successful
            } else {
                // Log an error if insertion fails
                log_message('error', 'Failed to add employee return: ' . json_encode($data));
                log_message('error', 'DB Error: ' . $this->db->error()['message']);
                return false; // Return false indicating failure
            }
        } catch (\Throwable $th) {
            log_message('error', 'Exception occurred: ' . $th->getMessage());
            return false; // Return false indicating failure due to exception
        }
    }
    

    public function update($data, $id)
    {
        $data = $this->clean_input_data($data); // Clean the data
        $this->db->where('id', $id);
        if ($this->db->update('tbl_monthly_employees_return', $data)) {
            return $this->db->affected_rows();
        } else {
            log_message('error', 'Failed to update employee: ' . json_encode($data));
            log_message('error', 'DB Error: ' . $this->db->error()['message']);
            return false;
        }
    }

    public function get($id)
    {
        $this->db->where('id', $id);
        return $this->db->get('tbl_monthly_employees_return')->row();
    }

    public function get_all()
    {
        $this->db->order_by('id', 'desc');  
        return $this->db->get('tbl_monthly_employees_return')->result();
    }
    public function change_person_status($id, $status)
    {
        // Update the 'tbl_pmpro_person_main' table
        $this->db->where('id', $id); // Assuming 'userid' is the identifier
        $this->db->update('tbl_pmpro_person_main', [
            'person_status' => $status,
        ]);

        // Check if the 'tbl_pmpro_person_main' table was updated
        if ($this->db->affected_rows() > 0) {
            hooks()->do_action('person_status_changed', [
                'id'     => $id,
                'status' => $status,
            ]);

            log_activity('Person Status Changed [ID: ' . $id . ' Status(Active/Inactive): ' . $status . ']');

            return true;
        }

        return false;
    }

    public function get_clients(){
        
        $this->db->where('is_active', 1);
        return $this->db->get('tbl_crm_accounts')->result();
    }
    public function get_document_category(){
        
        $this->db->where('sys_document_category_is_active', 1);
        return $this->db->get('tbl_sys_document_category')->result();
    }
    public function get_document_type(){
        
        $this->db->where('sys_doc_type_is_active', 1);
        return $this->db->get('tbl_sys_document_type')->result();
    }
    public function get_staff_users(){
        
        return $this->db->get('tblstaff')->result();
    }
    public function get_periods(){
        // $this->db->where('active', 1);
        // $this->db->order_by('display_order', 'asc');
        // $result = $this->db->get('tbl_sys_periods')->result();
       
        // return $result;
        return [];
    }

    //
    public function get_request_by(){
        //$this->db->where('sys_link_me_to_is_active', 1); 
       // $result = $this->db->get('tbl_sys_link_me_to')->result();
       
       // return $result;
       return [];
    }

    public function get_company_name(){
        $this->db->where('name', 'companyname'); 
        $result = $this->db->get('tbloptions')->result();
       
        return $result;
    }


    

     

    public function delete($id)
    {
        try {
            $this->db->where('id', $id);
            if ($this->db->delete('tbl_monthly_employees_return')) {
                return true; // Deletion successful
            } else {
                log_message('error', 'Failed to delete EMP 201 with ID: ' . $id);
                return false; // Deletion failed
            }
        } catch (\Throwable $th) {
            log_message('error', 'Exception occurred while deleting EMP 201: ' . $th->getMessage());
            return false; // Exception occurred
        }
    }


   

    
}

