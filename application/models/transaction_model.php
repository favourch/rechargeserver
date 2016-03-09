<?php

class Transaction_model extends Ion_auth_model {

    public $message_codes = array();

    public function __construct() {
        parent::__construct();
        $this->load->config('ion_auth', TRUE);
        $this->lang->load('ion_auth');
        $this->message_codes = $this->config->item('message_codes', 'ion_auth');
    }

    public function add_transactions($transction_list, $user_profit_list) {
        $transaction_list_for_webservice = [];
        $user_transaction_list = [];
        $payment_list = [];
        $user_profits = [];
        $current_time = now();
        foreach ($transction_list as $transaction_info) {
            $service_id = $transaction_info['service_id'];
            $transaction_info_for_webservice = [];
            if ($service_id == SERVICE_TYPE_ID_BKASH_CASHIN) {
                $api_key = API_KEY_BKASH_CASHIN;
            } else if ($service_id == SERVICE_TYPE_ID_DBBL_CASHIN) {
                $api_key = API_KEY_DBBL_CASHIN;
            } else if ($service_id == SERVICE_TYPE_ID_MCASH_CASHIN) {
                $api_key = API_KEY_MKASH_CASHIN;
            } else if ($service_id == SERVICE_TYPE_ID_UCASH_CASHIN) {
                $api_key = API_KEY_UKASH_CASHIN;
            } else if ($service_id == SERVICE_TYPE_ID_TOPUP_GP) {
                $api_key = API_KEY_CASHIN_GP;
            } else if ($service_id == SERVICE_TYPE_ID_TOPUP_ROBI) {
                $api_key = API_KEY_CASHIN_ROBI;
            } else if ($service_id == SERVICE_TYPE_ID_TOPUP_BANGLALINK) {
                $api_key = API_KEY_CASHIN_BANGLALINK;
            } else if ($service_id == SERVICE_TYPE_ID_TOPUP_AIRTEL) {
                $api_key = API_KEY_CASHIN_AIRTEL;
            } else if ($service_id == SERVICE_TYPE_ID_TOPUP_TELETALK) {
                $api_key = API_KEY_CASHIN_TELETALK;
            }
            $transaction_info_for_webservice = array(
                'id' => $transaction_info['mapping_id'],
                'APIKey' => $api_key,
                'amount' => $transaction_info['amount'],
                'cell_no' => $transaction_info['cell_no'],
                'description' => $transaction_info['description']
            );
            $transaction_info['created_on'] = $current_time;
            $transaction_info['modified_on'] = $current_time;
            $transaction_info['status_id'] = TRANSACTION_STATUS_ID_PENDING;
            $payment_info = array(
                'user_id' => $transaction_info['user_id'],
                'reference_id' => $transaction_info['user_id'],
                'status_id' => TRANSACTION_STATUS_ID_PENDING,
                'balance_in' => 0,
                'balance_out' => $transaction_info['amount'],
                'type_id' => PAYMENT_TYPE_ID_USE_SERVICE,
                'created_on' => $current_time,
                'modified_on' => $current_time
            );
            $transaction_list_for_webservice[] = $transaction_info_for_webservice;
            $user_transaction_list[$transaction_info['mapping_id']] = $this->_filter_data($this->tables['user_transactions'], $transaction_info);
            $payment_list[$transaction_info['mapping_id']] = $this->_filter_data($this->tables['user_payments'], $payment_info);
        }
        $this->curl->create(WEBSERVICE_URL_CREATE_MULTIPULE_TRANSACTIONS);
        $this->curl->post(array("transction_list" => json_encode($transaction_list_for_webservice)));
        $result_event = json_decode($this->curl->execute());
        if (!empty($result_event)) {
            $response_code = '';
            if (property_exists($result_event, "responseCode") != FALSE) {
                $response_code = $result_event->responseCode;
            }
            if ($response_code == RESPONSE_CODE_SUCCESS) {
                if (property_exists($result_event, "result") != FALSE) {
                    $mapping_info_list = $result_event->result;
                    if (empty($mapping_info_list)) {
                        $this->set_message('error_no_transaction_id');
                        return FALSE;
                    } else {
                        foreach ($mapping_info_list as $mapping_info) {
                            $mapping_id = $mapping_info->id;
                            $user_transaction_list[$mapping_id]['transaction_id'] = $mapping_info->transactionId;
                            $payment_list[$mapping_id]['transaction_id'] = $mapping_info->transactionId;
                            foreach ($user_profit_list as $user_profit_info) {
                                if ($user_profit_info['mapping_id'] == $mapping_info->id) {
                                    $user_profit_info['transaction_id'] = $mapping_info->transactionId;
                                    $user_profits[] = $this->_filter_data($this->tables['user_profits'], $user_profit_info);
                                }
                            }
                        }

                        $this->db->trans_begin();
                        $this->db->insert_batch($this->tables['user_transactions'], $user_transaction_list);
                        $this->db->insert_batch($this->tables['user_payments'], $payment_list);
                        $this->db->insert_batch($this->tables['user_profits'], $user_profits);
                        $this->db->trans_commit();
                        $this->set_message('transaction_successful');
                        return TRUE;
                    }
                } else {
                    $this->set_error('error_no_result_event');
                    return FALSE;
                }
            } else {
                //set message based on response code
                $this->set_error('error_code_' . $response_code);
                return FALSE;
            }
        } else {
            $this->set_error('error_webservice_unavailable');
        }
        return FALSE;
    }

    /*
     * This method will udpate transaction as call back function from the authentication server
     * @param $transaction_id, transaction id
     * @param $status_id, status id
     * @param $sender_cell_number, sender cell number
     * @author nazmul hasan on 24th february 2016
     */

    public function update_transaction_callbackws($transaction_id, $status_id, $sender_cell_number) {
        $transaction_data = array(
            'status_id' => $status_id,
            'sender_cell_no' => $sender_cell_number
        );
        $this->db->where('transaction_id', $transaction_id);
        $this->db->update('user_transactions', $transaction_data);

        $payment_data = array(
            'status_id' => $status_id
        );
        $this->db->where('transaction_id', $transaction_id);
        $this->db->update('user_payments', $payment_data);

        $profit_data = array(
            'status_id' => $status_id
        );
        $this->db->where('transaction_id', $transaction_id);
        $this->db->update('user_profits', $profit_data);
    }

    /*
     * This method will return current available balance of a user 
     * @param  $user_id, user id
     * @return $current_balance, current balance of the user
     * @author nazmul hasan on 24th February 2016
     */
    /* public function get_user_current_balance($user_id) {
      $current_balance = 0;
      $this->db->where('user_id', $user_id);
      $this->db->where_in('status_id', array(TRANSACTION_STATUS_ID_PENDING, TRANSACTION_STATUS_ID_SUCCESSFUL));
      $user_balance_array = $this->db->select('user_id, sum(balance_in) - sum(balance_out) as current_balance')
      ->from($this->tables['user_payments'])
      ->get()->result_array();
      if (!empty($user_balance_array)) {
      $current_balance = $user_balance_array[0]['current_balance'];
      }
      return $current_balance;
      } */
    /*
     * This method will call the webservice and add a new transaction
     * @param $api_key, service API key of the transaction
     * @param $transaction_data, transaction data
     * @param $user_profit_data, user profit data
     * @author nazmul hasan on 24th February 2016
     */

    public function add_transaction($api_key, $transaction_data, $users_profit_data) {
        $amount = $transaction_data['amount'];
        $cell_no = $transaction_data['cell_no'];
        $description = $transaction_data['description'];
        $user_id = $transaction_data['user_id'];

        //checking whether user has enough balance before the transaction
        $this->load->model('payment_model');
        $user_current_balance_array = $this->payment_model->get_users_current_balance(array($user_id))->result_array();
        if (!empty($user_current_balance_array)) {
            $user_current_balance = $user_current_balance_array[0]['current_balance'];
            if ($amount > $user_current_balance) {
                $this->set_error('error_insufficient_balance');
                return FALSE;
            }
        } else {
            $this->set_error('error_insufficient_balance');
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
                        $this->set_message('error_no_transaction_id');
                        return FALSE;
                    } else {
                        $this->db->trans_begin();
                        $current_time = now();
                        $transaction_data['created_on'] = $current_time;
                        $transaction_data['modified_on'] = $current_time;
                        $transaction_data['transaction_id'] = $transaction_id;
                        $transaction_data['status_id'] = TRANSACTION_STATUS_ID_PENDING;
                        $additional_data = $this->_filter_data($this->tables['user_transactions'], $transaction_data);
                        $this->db->insert($this->tables['user_transactions'], $additional_data);
                        $insert_id = $this->db->insert_id();
                        if (isset($insert_id)) {
                            $data = array(
                                'user_id' => $user_id,
                                'reference_id' => $user_id,
                                'transaction_id' => $transaction_id,
                                'status_id' => TRANSACTION_STATUS_ID_PENDING,
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
                                $user_profit_list = array();
                                foreach ($users_profit_data as $user_profit_info) {
                                    $user_profit_info['transaction_id'] = $transaction_id;
                                    $user_profit_list[] = $user_profit_info;
                                }
                                $this->db->insert_batch($this->tables['user_profits'], $user_profit_list);
                                $this->db->trans_commit();
                                $this->set_message('transaction_successful');
                                return TRUE;
                            }
                        }
                        $this->db->trans_rollback();
                        $this->set_error('transaction_unsuccessful');
                        return FALSE;
                    }
                } else {
                    $this->set_error('error_no_result_event');
                    return FALSE;
                }
            } else {
                //set message based on response code
                $this->set_error('error_code_' . $response_code);
                return FALSE;
            }
        } else {
            $this->set_error('error_webservice_unavailable');
        }
        return FALSE;
    }

    /*
     * This method will return user transaction list
     * @param $service_id_list, service id list of transactions
     * @param $from_date, start date in unix format
     * @param $to_date, end date in unix format
     * @param $limit, limit
     * @param $offset, offset
     * @author nazmul hasan on 24th February 2016
     */

    public function get_user_transaction_list($service_id_list = array(), $status_id_list = array(), $from_date = 0, $to_date = 0, $limit = 0, $offset = 0) {
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
        if (!empty($status_id_list)) {
            $this->db->where_in($this->tables['user_transactions'] . '.status_id', $status_id_list);
        }
        $this->db->order_by($this->tables['user_transactions'] . '.id', 'desc');
        return $this->db->select($this->tables['user_transactions'] . '.*,' . $this->tables['user_transaction_statuses'] . '.title as status,' . $this->tables['services'] . '.title as service_title')
                        ->from($this->tables['user_transactions'])
                        ->join($this->tables['user_transaction_statuses'], $this->tables['user_transaction_statuses'] . '.id=' . $this->tables['user_transactions'] . '.status_id')
                        ->join($this->tables['services'], $this->tables['services'] . '.id=' . $this->tables['user_transactions'] . '.service_id')
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
        $this->db->where_in($this->tables['user_profits'] . '.status_id', array(TRANSACTION_STATUS_ID_PENDING, TRANSACTION_STATUS_ID_SUCCESSFUL));
        $this->db->group_by('service_id');
        return $this->db->select($this->tables['user_profits'] . '.service_id, sum(rate) as total_used_amount, sum(amount) as total_profit,' . $this->tables['services'] . '.title')
                        ->from($this->tables['user_profits'])
                        ->join($this->tables['services'], $this->tables['user_profits'] . '.service_id=' . $this->tables['services'] . '.id')
                        ->get();
    }

}
