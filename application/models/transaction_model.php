<?php

class Transaction_model extends Ion_auth_model {

    public $message_codes = array();

    public function __construct() {
        parent::__construct();
        $this->load->config('ion_auth', TRUE);
        $this->lang->load('ion_auth');
        $this->message_codes = $this->config->item('message_codes', 'ion_auth');
    }

    /**
     * This Method return user Current balance 
     * @param  $user_id user id
     * return $current_balance current Balance of a user
     * @author Rashida Sultana on 31 jan 2016
     * 
     */
    public function get_user_current_balance($user_id) {
        $current_balance = 0;
        $this->db->where('user_id', $user_id);
        $user_balance_array = $this->db->select('user_id, sum(balance_in) - sum(balance_out) as current_balance')
                        ->from($this->tables['user_payments'])
                        ->get()->result_array();
        if (!empty($user_balance_array)) {
            $current_balance = $user_balance_array[0]['current_balance'];
        }
        return $current_balance;
    }

    public function add_transaction($api_key, $transaction_data, $users_profit_data) {
        $amount = $transaction_data['amount'];
        $cell_no = $transaction_data['cell_no'];
        $description = $transaction_data['description'];
        $user_id = $transaction_data['user_id'];

        if ($amount > $this->get_user_current_balance($user_id)) {
            $this->ion_auth->set_message('error_insaficient_balance');
            return FALSE;
        }
        $this->curl->create(WEBSERVICE_URL_CREATE_TRANSACTION);
        $this->curl->post(array("APIKey" => $api_key, "amount" => $amount, "cell_no" => $cell_no, "description" => $description));
        $result_event = json_decode($this->curl->execute());
        if (!empty($result_event)) {
            $response_code = '';
            if (property_exists($result_event, "responseCode") != FALSE) {
                $response_code = $result_event->responseCode;
            }
            if ($response_code == RESPONSE_CODE_SUCCESS) {
                if (property_exists($result_event, "result") != FALSE) {
                    $transaction_info = $result_event->result;
                    $transaction_id = $transaction_info->transactionId;
                    if (empty($transaction_id) || $transaction_id == "") {
                        $this->ion_auth->set_message('error_while_processing_the_transaction');
                        //Handle a message if there is no transaction id
                        return FALSE;
                    } else {
                        $this->db->trans_begin();
                        $current_time = now();
                        $transaction_data['created_on'] = $current_time;
                        $transaction_data['modified_on'] = $current_time;
                        $transaction_data['transaction_id'] = $transaction_id;
                        $additional_data = $this->_filter_data($this->tables['user_transactions'], $transaction_data);
                        $this->db->insert($this->tables['user_transactions'], $additional_data);
                        $insert_id = $this->db->insert_id();
                        if (isset($insert_id)) {
                            $data = array(
                                'user_id' => $user_id,
                                'reference_id' => $user_id,
                                'transaction_id' => $transaction_id,
                                'balance_in' => 0,
                                'balance_out' => $transaction_data['amount'],
                                'type_id' => PAYMENT_TYPE_ID_USE_SERVICE,
                                'created_on' => $current_time,
                                'modified_on' => $current_time
                            );
                            $payment_data = $this->_filter_data($this->tables['user_payments'], $data);
                            $this->db->insert($this->tables['user_payments'], $payment_data);
                            $insert_id = $this->db->insert_id();
                            if (isset($insert_id)) {
                                $this->db->insert_batch($this->tables['user_profits'], $users_profit_data);
                                $this->db->trans_commit();
                                return TRUE;
                            }
                        }
                        $this->db->trans_rollback();
                        $this->ion_auth->set_message('error_while_processing_the_transaction');
                        return FALSE;
                    }
                }
            }
        }
        $this->ion_auth->set_message('error_while_processing_the_transaction');
        return FALSE;
    }

    public function get_user_transaction_list($service_id_list = array(), $limit = 0, $offset = 0, $from_date = 0, $to_date = 0) {
        //run each where that was passed
        if (isset($this->_ion_where) && !empty($this->_ion_where)) {
            foreach ($this->_ion_where as $where) {
                $this->db->where($where);
            }

            $this->_ion_where = array();
        }
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        if ($offset > 0) {
            $this->db->offset($offset);
        }
        if ($from_date != 0 && $to_date != 0) {
            $this->db->where($this->tables['user_transactions'] . '.created_on >=', $from_date);
            $this->db->where($this->tables['user_transactions'] . '.created_on <=', $to_date);
        }
        if (!empty($service_id_list)) {
            $this->db->where_in($this->tables['user_transactions'] . '.service_id', $service_id_list);
        }
        $this->db->order_by('id', 'desc');
        return $this->db->select($this->tables['user_transactions'] . '.*,' . $this->tables['user_transaction_statuses'] . '.title as status')
                        ->from($this->tables['user_transactions'])
                        ->join($this->tables['user_transaction_statuses'], $this->tables['user_transaction_statuses'] . '.id=' . $this->tables['user_transactions'] . '.status_id')
                        ->get();
    }

    public function get_users_current_balance($user_id_list = array()) {
        $this->db->where_in($this->tables['user_transactions'] . '.user_id', $user_id_list);
        $this->db->group_by('user_id');
        return $this->db->select('user_id, sum(balance_in) - sum(balance_out) as current_balance')
                        ->from($this->tables['user_transactions'])
                        ->get();
    }

    /**
     * this method return payment or receive history of a user
     * @$user_id
     * @$payment_type_ids
     * return payment history
     * @author Rashida on 17 feb 2016
     */
    public function get_payment_history($user_id = 0, $payment_type_ids = array(), $limit = 0, $offset = 0, $start_date = 0, $end_date = 0) {
        $this->db->where($this->tables['user_payments'] . '.user_id', $user_id);
        $this->db->where_in($this->tables['user_payments'] . '.type_id', $payment_type_ids);
        if ($start_date != 0 && $end_date != 0) {
            $this->db->where($this->tables['user_payments'] . '.created_on >=', $start_date);
            $this->db->where($this->tables['user_payments'] . '.created_on <=', $end_date);
        }
        if ($limit > 0) {
            $this->db->limit($limit);
        }
        if ($offset > 0) {
            $this->db->offset($offset);
        }
        return $this->db->select($this->tables['user_payments'] . '.*,' . $this->tables['users'] . '.username')
                        ->from($this->tables['users'])
                        ->join($this->tables['user_payments'], $this->tables['users'] . '.id=' . $this->tables['user_payments'] . '.reference_id')
                        ->get();
    }

    /**
     * this method will return user profit
     * @$user_id
     * @$service_ids
     * return profit history
     * @author Rashida on 17 feb 2016
     */
    public function get_user_profit($user_id, $service_ids) {
        $this->db->where($this->tables['user_profits'] . '.user_id', $user_id);
        $this->db->where_in($this->tables['user_profits'] . '.service_id', $service_ids);
        $this->db->group_by('service_id');
        return $this->db->select($this->tables['user_profits'] . '.service_id, sum(rate) as total_used_amount, sum(amount) as total_profit,' . $this->tables['services'] . '.title')
                        ->from($this->tables['user_profits'])
                        ->join($this->tables['services'], $this->tables['user_profits'] . '.service_id=' . $this->tables['services'] . '.id')
                        ->get();
    }

  
}
