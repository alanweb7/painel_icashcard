<?php defined('BASEPATH') or exit('No direct script access allowed');

class Listar_atendentes extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model'); // Modelo padrão do Perfex para staff
    }

    public function index()
    {

        // Obtém o ID do usuário (staff) logado
        $staff_id = $this->session->userdata('staff_user_id');

        // Consulta simplificada para pegar apenas os staffs gerenciados pelo gerente logado
        $this->db->select(
            'staff.staffid,
            staff.firstname, 
            staff.lastname, 
            staff.email, 
            staff.cpf_cnpj, 
            staff.phonenumber, 
            staff.active, 
            staff.role'
        );
        $this->db->from(db_prefix() . 'staff staff');
        $this->db->where('staff.gerente_id', $staff_id);

        $query = $this->db->get();
        $data['staffs'] =  $query->result_array();


        $this->load->view('atendentes/listar_atendentes', $data);
    }
}
