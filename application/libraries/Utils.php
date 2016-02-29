<?php
if (!defined('BASEPATH'))
    exit('No direct script access allowed');
/**
 * Name:  Date_utils
 * Added in Class Diagram
 * Requirements: PHP5 or above
 */
class Utils {
    /**
     * __construct
     *
     * @return void
     * @author Ben
     * */
    public function __construct() {
        $this->load->config('ion_auth', TRUE);
        // Load the session, CI2 as a library, CI3 uses it as a driver
        if (substr(CI_VERSION, 0, 1) == '2') {
            $this->load->library('session');
        } else {
            $this->load->driver('session');
        }

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
     * this method will validate cell number
     * @param $cell_no phone number
     * @return boolean, true if the cell number is valid otherwise false
     * @author nazmul hasan on 28th february 2016
     */
    public function cell_number_validation($cell_no) {
        if (preg_match("/^((^\+880|0)[1-9][1|5|6|7|8|9])[0-9]{8}$/", $cell_no) === 0) {
            RETURN FALSE;
        } else {
            RETURN True;
        }
    }
    /**
     * this method return a unique random 32 bit string
     * 
     */
    public function get_transaction_id(){
        return random_string('unique', 32);
    }
   
}