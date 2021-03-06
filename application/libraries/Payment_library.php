<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

/**
 *
 */
class Payment_library {

    public function __construct() {
        $this->load->model('payment_model');
    }

    /**
     * __call
     *
     * Acts as a simple way to call model methods without loads of stupid alias'
     *
     * */
    public function __call($method, $arguments) {
        if (!method_exists($this->payment_model, $method)) {
            throw new Exception('Undefined method Payment_library::' . $method . '() called');
        }

        return call_user_func_array(array($this->payment_model, $method), $arguments);
    }

    /**
     * __get
     *
     * Enables the use of CI super-global without having to define an extra variable.
     *
     * I can't remember where I first saw this, so thank you if you are the original author. -Militis
     *
     * @access	public
     * @param	$var
     * @return	mixed
     */
    public function __get($var) {
        return get_instance()->$var;
    }

    /*
     * This method will return payment history
     * @author nazmul hasan on 3rd March 2016
     */
    public function get_payment_history($type_id_list = array(), $status_id_list = array(), $start_date = 0, $end_date = 0, $limit = 0, $offset = 0, $order = 'desc', $where = array()) {
        $this->load->library('Date_utils');
        $payment_list = array();
        $payment_information = array();
        $start_time = 0;
        $end_time = 0;
        if ($start_date != 0 && $end_date != 0) {
            $start_time = $this->date_utils->server_start_unix_time_of_date($start_date);
            $end_time = $this->date_utils->server_end_unix_time_of_date($end_date);
        }
        $total_transactions = 0;
        $total_amount_out = 0;
        $total_amount_in = 0;
        if (!empty($where)) {
            $this->payment_model->where($where);
        }
        $payment_summery_array = $this->payment_model->get_payment_history_summary($type_id_list, $status_id_list, $start_time, $end_time)->result_array();
        if (!empty($payment_summery_array)) {
            $total_transactions = (int) $payment_summery_array[0]['total_transactions'];
            $total_amount_out = (int) $payment_summery_array[0]['total_amount_out'];
            $total_amount_in = (int) $payment_summery_array[0]['total_amount_in'];
        }
        if (!empty($where)) {
            $this->payment_model->where($where);
        }
        $payment_list_array = $this->payment_model->get_payment_history($type_id_list, $status_id_list, $start_time, $end_time, $limit, $offset, $order)->result_array();
        foreach ($payment_list_array as $payment_info) {
            $payment_info['created_on'] = $this->date_utils->get_unix_to_display($payment_info['created_on']);
            $payment_list[] = $payment_info;
        }
        $payment_information['total_transactions'] = $total_transactions;
        $payment_information['total_amount_out'] = $total_amount_out;
        $payment_information['total_amount_in'] = $total_amount_in;
        $payment_information['payment_list'] = $payment_list;
        return $payment_information;
    }
}
