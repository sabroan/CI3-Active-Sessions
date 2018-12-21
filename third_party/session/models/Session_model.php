<?php defined('BASEPATH') OR exit('No direct script access allowed');

/*
--------------------------------------------------------------------------------
    All functions shouldn't be accessable directly in this model since
    they will be called by __call method, this must be done for checking
    if table was set in config.
--------------------------------------------------------------------------------
*/

class Session_model extends CI_Model {
    
    private $_table = NULL;
    
    public function __construct()
    {
        parent::__construct();
        $this->load->database();
        $config = config_item('session');
        $this->_table = $config['database_table'];
    }
    
    public function __call ($method, $arguments)
    {
        if ($this->_table && method_exists($this, $method)) {
            return call_user_func_array(array($this,$method), $arguments);
        }
    }

    /*
    ----------------------------------------------------------------------------
        Delete user active session.

        @param (int) $uid - user id.
        @param (string) $sid - session id which was regenerated.
        @return (int|null) number of affected rows
    ----------------------------------------------------------------------------
    */
    private function delete_active_session ($uid, $sid)
    {
        $where = array(
            'user_id'       => (int) $uid,
            'session_id'    => $sid,
        );
        $this->db->reset_query();
        $this->db->delete($this->_table, $where);
        return $this->db->affected_rows();
    }
    
    /*
    ----------------------------------------------------------------------------
        Insert or Update user active session.

        Replace method is used for cases, when 'session->set_user_id' method
        is called and user is stil have active session (not logged out).
        For preventing sql 'dublicate key' error, 'replace' will overwrite data.
        Possible solution - check if user id is already set
        and prevent furher execution, but this approach can be useful
        if we decide to extend session library and allow user multiple authentications.

        @param (int) $uid - user id.
        @param (string|null) $sid - session id which was regenerated.
        @return (int|null) number of affected rows
    ----------------------------------------------------------------------------
    */
    private function set_active_session ($uid, $sid = null)
    {
        $this->load->library('user_agent');
        $query = array(
            'session_id'    => session_id(),
            'timestamp'     => time(),
            'ip_address'    => $this->input->ip_address(),
            'browser'       => $this->agent->browser().' '.$this->agent->version(),
            'platform'      => $this->agent->platform(),
        );
        $this->db->reset_query();
        if ($sid) {
            $this->db->update($this->_table, $query, array('session_id' => $sid));
        } else {
            $query['user_id'] = (int) $uid;
            $this->db->replace($this->_table, $query);
        }
        return $this->db->affected_rows();
    }
    
    /*
    ----------------------------------------------------------------------------
        Remove expired sessions.

        @return (int) number of affected rows.
    ----------------------------------------------------------------------------
    */
    private function delete_expired_sessions ()
    {
        $this->db->reset_query();
        $this->db->delete($this->_table, array(
            'timestamp <' => (time() - (int) config_item('sess_expiration'))
        ));
        return $this->db->affected_rows();
    }
}