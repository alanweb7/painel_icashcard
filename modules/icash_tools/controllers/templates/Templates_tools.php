<?php defined('BASEPATH') or exit('No direct script access allowed');

class Templates_tools extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('staff_model'); // Modelo padrão do Perfex para staff
    }

    public function index()
    {

        $data['staffs'] =  "staff";

        $this->load->view('templates/template-client-details', $data);
    }

    public function proposal_details()
    {

        $data['staffs'] =  "staff";

        $this->load->view('templates/template-client-details', $data);
    }

    public function my_link()
    {

        $data['staffs'] =  "staff";
        $this->load->view('templates/template_link_corban', $data);
    }

    public function proposal_edit()
    {
        $proposal_id = $this->input->post('proposal_id');



        // otions de tabelas
        $this->load->model('invoice_items_model');
        $items = $this->invoice_items_model->get();

        $options = [];
        foreach ($items as $item) {
            $options[] = [
                'itemid' => $item['itemid'],
                'slug' => $item['description'],          // slug = description
                'title' => $item['long_description'],     // title = long_description
                'unit'  => $item['unit'],
                'rate'  => $item['rate'],
            ];
        }


        $data['options'] = $options;

        if ($proposal_id) {
            // Obtenha os dados da proposta do banco de dados
            $proposal = $this->proposals_model->get($proposal_id);

            if ($proposal) {

                // $rg = get_custom_fields('customers', ['id' => 68], $proposal->rel_id);
                $rg = $this->get_custom_field_value_db($proposal->rel_id, 68, 'customers');
                $data_nasc = $this->get_custom_field_value_db($proposal->rel_id, 70, 'customers');
                $timestamp = strtotime($data_nasc);

                $customer = [

                    "rg" => $rg,
                    "data_nasc" => $data_nasc,

                ];


                $custom_fields = get_custom_fields('proposal', ['show_on_table' => 1], $proposal_id);
                $custom_data = [];

                foreach ($custom_fields as $field) {
                    $valor_campo = $this->get_custom_field_value_db($proposal_id, $field['id']);
                    $custom_data[$field['name']] = $valor_campo;
                }
                // Passar dados para o template
                $proposal->custom_fields = $custom_data;
                $proposal->customer = $customer;
                $data['proposal'] = $proposal;
                // $data['proposal']['custom_fields'] = 123;
                $this->load->view('templates/template-edit-proposal', $data);
            } else {
                echo 'Proposta não encontrada.';
            }
        } else {
            show_error('ID da proposta não fornecido.', 400);
        }
    }

    public function get_custom_field_value_db($proposal_id, $custom_field_id, $fieldto = 'proposal')
    {
        $sql = "SELECT cfv.value
                FROM tblcustomfieldsvalues as cfv
                JOIN tblcustomfields as cf ON cfv.fieldid = cf.id
                WHERE cfv.relid = ?
                AND cf.id = ?
                AND cfv.fieldto = '{$fieldto}'";

        $query = $this->db->query($sql, array($proposal_id, $custom_field_id));

        // return json_encode($query);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->value;
        } else {
            return null;
        }
    }
}
