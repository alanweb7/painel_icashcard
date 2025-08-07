<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gerenciar_propostas extends AdminController
{
    public function __construct()
    {
        parent::__construct();
        // $this->load->model('icash_tools_model');
        // $this->load->library('form_validation'); // Carrega a biblioteca de validação
        $this->load->model('proposals_model');
    }

    public function index()
    {
        // Carregar a view passando os dados corretamente

    }



    public function update_status()
    {

        $this->load->model('proposals_model');

        $proposal_id = $this->input->post('proposal_id');
        $proposal = $this->proposals_model->get($proposal_id);

        $new_status = $this->input->post('status');

        if (!$proposal_id || !$new_status) {
            echo json_encode(['success' => false, 'message' => 'Dados inválidos']);
            return;
        }

        $pipeline = [
            "proposalid" => $proposal_id,
            "status" => $new_status
        ];

        $data['rel_id']   = $proposal->rel_id; //ID do Cliente
        $data['rel_type'] = $proposal->rel_type;

        $etapas = [
            1 => "PEN - Envio Documento",
            20 => "PEN - Doc. Ilegível",
            21 => "Em análise documental",
            22 => "Reprova documental",
            23 => "Link Pag. Enviado",
            24 => "Link Pag. Aprovado",
            25 => "Link Pag. Reprovado",
            26 => "Aguardando formalização",
            27 => "Em análise formalização",
            28 => "Liberar Crédito",
            29 => "Crédito Enviado",
            30 => "Descartado",
            2 => "Cancelada",
            3 => "Operação Finalizada"
        ];

        $data["custom_fields"] = [
            "proposal" => [
                64 => $etapas[$new_status]
            ]
        ];

        // $updated = $this->proposals_model->update_pipeline($pipeline);
        $updated = $this->proposals_model->update($data, $proposal_id);
        // $updated = $this->proposals_model->update_status($proposal_id, $new_status);

        if ($updated) {
            echo json_encode(['success' => true, 'message' => 'Status atualizado com sucesso']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Erro ao atualizar o status']);
        }
    }



    public function get_custom_field_value_db($proposal_id, $custom_field_id)
    {
        $sql = "SELECT cfv.value
                FROM tblcustomfieldsvalues as cfv
                JOIN tblcustomfields as cf ON cfv.fieldid = cf.id
                WHERE cfv.relid = ?
                AND cf.id = ?
                AND cfv.fieldto = 'proposal'";

        $query = $this->db->query($sql, array($proposal_id, $custom_field_id));

        // return json_encode($query);
        if ($query->num_rows() > 0) {
            $row = $query->row();
            return $row->value;
        } else {
            return null;
        }
    }


    public function onUpdateProposal()
    {

        $this->load->model('proposals_model');

        if (!staff_can('edit', 'corban_proposals')) {
            set_alert('danger', 'Permissão Negada.');
            redirect(admin_url('icash_tools/listar_propostas#'));
            return;
        }


        $this->load->model('proposals_model');

        $proposal_id = $this->input->post('proposal_id', TRUE);
        $proposal = $this->proposals_model->get($proposal_id);


        if (!$proposal_id) {
            set_alert('danger', 'Dados inválidos.');
            redirect(admin_url('icash_tools/listar_propostas#'));
            return;
        }


        $proposal_fields = $this->input->post('proposal_fields');
        $custom_fields = [];

        foreach ($proposal_fields as $key => $value) {
            $custom_fields[$key] = $value;
        }

        $items = $proposal->items;

        $parcela = floatval(str_replace(',', '.', str_replace('.', '', $custom_fields[14])));
        $TotalLiquido = floatval(str_replace(',', '.', str_replace('.', '', $custom_fields[16])));
        $parcelaLiq = $TotalLiquido /

        $qty = $custom_fields[13];
        $parcelaLiq = $TotalLiquido / $qty;

        $description = $custom_fields[67];
        $items[0]['itemid'] = $items[0]['id'];
        $items[0]['qty'] = $custom_fields[13];
        $items[0]['rate'] = $parcelaLiq;
        $items[0]['description'] = $description;

        /** total deve ser o valor liquido / parcelas
         * 
         **/

        $total = $qty * $parcela;


        $updateData = [
            'rel_id'        => $proposal->rel_id,
            'rel_type'      => $proposal->rel_type,
            'assigned'      => $proposal->assigned,
            'status'        => $proposal->status,
            "total"         => $TotalLiquido,
            "subtotal"      => $TotalLiquido,
            "items"         => $items,
            'custom_fields' => [
                'proposal'  => $custom_fields
            ]
        ];

        $updated = $this->proposals_model->update($updateData, $proposal_id);


        if ($updated) {
            set_alert('success', 'Dados atualizados com sucesso.');
        } else {
            set_alert('success', 'Nada foi alterado.');
        }

        redirect(admin_url('icash_tools/listar_propostas#'));
    }


    // buscar propostar que mudaram recentemente

public function get_status_atualizados()
{

    header('Content-Type: application/json');

    $staff_id = get_staff_user_id();

    $proposals = $this->db->select('id, status')
    ->from('tblproposals')
    ->where('status !=', 3)
    ->where("update_at >= NOW() - INTERVAL 600 SECOND", null, false)
    ->get()
    ->result_array();


        $response = [
            'success' => false,
            'message' => 'Propostas encontradas',
            "proposals" =>  $proposals,
            "staff_id" => $staff_id
        ];

        echo json_encode($response);
        exit;

}

    
    public function onNotificationToWebhook($data)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://webhook.site/ce642bde-82f6-4b01-a6f8-16e0091d20fb',
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($data)
        ));

        $response = curl_exec($curl);

        curl_close($curl);
    }
}
