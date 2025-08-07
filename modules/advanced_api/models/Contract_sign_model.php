<?php

defined('BASEPATH') || exit('No direct script access allowed');

/**
 * Advanced_api_model Class.
 *
 * This model class represents the API functionality for  advanced.
 */
class Contract_sign_model extends App_Model
{
    /**
     * Advanced_api_model constructor.
     *
     * Initialize the Advanced_api_model class and load the required model dependencies.
     */
    public function __construct()
    {
        parent::__construct();
        register_language_files('advanced_api');
        load_client_language();
    }



    public function add_sign_event($data)
    {
        if (!isset($data['event']['data'])) {
            return [
                "success" => false,
                "message" => "no data"
            ];
        }

        $eventData = $data['event']['data'];
        $events = $eventData['events'];

        $type = 'sent'; // valor padrão

        if (!empty($events)) {

            foreach ($events as $event) {
                $emailAdm = get_option('autentique_email');
                $userEmail = $event['user']['email'] ?? ''; 
                if ($event['type'] === 'signed' && ($userEmail != $emailAdm && !empty($userEmail))) {
                    $type = 'signed';
                    break; // já achou o mais importante, pode sair do loop
                } elseif ($event['type'] === 'viewed') {
                    $type = 'viewed'; // guarda, mas pode ser sobrescrito por 'signed'
                }
            }
        }

        $user      = $eventData['user'] ?? [];

        // if (empty($eventData['viewed'])) {
        //     return [
        //         "success" => false,
        //         "message" => "no viewed"
        //     ];
        // }

        $document = $eventData['document'] ?? '';

        $exists = $this->db->where('document', $document)->get(db_prefix() . 'contract_sign_events')->row();

        $insert = [
            'document'    => $document,
            'name'        => $user['name'] ?? '',
            'event_type'  => $type,
            'viewed' => !empty($eventData['viewed'])
                ? date('Y-m-d H:i:s', strtotime($eventData['viewed']))
                : null,
            'signed'      => !empty($eventData['signed'])
                ? date('Y-m-d H:i:s', strtotime($eventData['signed']))
                : null,
            'rejected'    => !empty($eventData['rejected'])
                ? date('Y-m-d H:i:s', strtotime($eventData['rejected']))
                : null,
            'status'      => $eventData['action'] ?? '',
            'updated_at'  => date('Y-m-d H:i:s'),
        ];

        $registerID = null;

        if ($exists) {
            // Atualiza o registro
            $this->db->where('document', $document)->update(db_prefix() . 'contract_sign_events', $insert);

            $registerID = $exists->id;
            // return $exists->id;
        } else {
            // Adiciona campos extras só no insert
            // $insert['created_at'] = date('Y-m-d H:i:s');
            $this->db->insert(db_prefix() . 'contract_sign_events', $insert);
            $registerID = $this->db->insert_id();
            // return $this->db->insert_id();
        }


        if ($this->db->affected_rows() > 0) {
            $proposalUpdate = null;
            if ($type == "signed") {
                $proposalUpdate = $this->onProposalUpdate($document);

                if (!$proposalUpdate['success']) {
                    return
                        [
                            'success' => false,
                            'message' => _l('Erro ao atualizar a proposta do Contrato'),
                            'code' => 400
                        ];
                }
            }


            return
                [
                    'success' => true,
                    'message' => _l('Contracts sign events created or updated'),
                    'entry_id' => $registerID,
                    'event_type' => $type,
                    'code' => 200
                ];

            // return $this->db->insert_id();
        }

        return
            [
                'success' => false,
                'message' => _l('Erro ao atualizar o Contracts sign events'),
                'code' => 400
            ];
    }



    public function onProposalUpdate($document)
    {

        $this->load->model('proposals_model');

        $proposal = $this->proposals_model->get('', ['document' => $document])[0];

        $updateData = [
            'rel_id'                    => $proposal['rel_id'],
            'rel_type'                  => $proposal['rel_type'],
            'assigned'                  => $proposal['assigned'],
            'custom_fields' => [
                'proposal'  => [
                    64 => 'Em análise formalização',
                ]
            ]
        ];

        $update = $this->proposals_model->update($updateData, $proposal['id']);

        if ($update) {

            return
                [
                    'success' => true,
                    'message' => _l('Contracts sign events created or updated'),
                    'proposal' => $proposal['id'],
                    'code' => 200
                ];
        }
    }



    /**
     * Generate a not found response.
     *
     * @return array the not found response array
     */
    private function notFoundResponse()
    {
        return [
            'response' => [
                'message' => _l('data_not_found'),
            ],
            'response_code' => 404,
        ];
    }

    /**
     * Generate a forbidden response.
     *
     * @return array the forbidden response array
     */
    private function forbiddenResponse()
    {
        return [
            'response' => [
                'message' => _l('not_permission_to_perform_this_action'),
            ],
            'response_code' => 403,
        ];
    }

    /**
     * Generate an error response.
     *
     * @return array the error response array
     */
    private function errorResponse()
    {
        return [
            'response' => [
                'message' => _l('something_went_wrong'),
            ],
            'response_code' => 500,
        ];
    }
}
