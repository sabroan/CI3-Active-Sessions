<?php defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Session extends CI_Session {

    private $CI;

    public function __construct (array $params = array())
    {
        parent::__construct($params);
        $this->CI = & get_instance();
        $result = $this->CI->load->config('session', true);
        $this->CI->load->model('session_model', null, true);
    }

    /*
    ----------------------------------------------------------------------------
        This function extends the CI default 'sess_regenerate' method.
        On each session regeneration, old session id will be passed to method,
        which will update currently active session. 
    ----------------------------------------------------------------------------
    */
    public function sess_regenerate($destroy = false)
    {
        $sid = session_id();
        parent::sess_regenerate($destroy);
        $uid = $this->get_user_id();
        if ($sid && $uid) {
            $this->CI->session_model->set_active_session($uid, $sid);
        }
    }

    /*
    ----------------------------------------------------------------------------
        Set user ID to session.

        @param (int) $uid - user id.
        @return (void)
    ----------------------------------------------------------------------------
    */
    public function set_user_id ($uid)
    {
        $key = $this->CI->config->item('session_uid_key', 'session');
        $_SESSION[$key] = (int) $uid;
        $this->CI->session_model->set_active_session($uid);
    }

    /*
    ----------------------------------------------------------------------------
        Try to retrieve user ID from session.

        @return (int|string|null)
    ----------------------------------------------------------------------------
    */
    public function get_user_id ()
    {
        $key = $this->CI->config->item('session_uid_key', 'session');
        return (isset($_SESSION[$key]))
            ? (int) $_SESSION[$key]
            : null;
    }
  
    /*
    ----------------------------------------------------------------------------
        Remove expired sessions.

        @return (int) number of affected rows.
    ----------------------------------------------------------------------------
    */
    public function gc () {
        return $this->CI->session_model->delete_expired_sessions();
    }

    /*
    ----------------------------------------------------------------------------
        Remove active session from database when session should be destroyed.
    
        @return (void)
    ----------------------------------------------------------------------------
    */
    public function sess_destroy ()
    {
        $sid = session_id();
        $uid = $this->get_user_id();
        if ($sid && $uid)
        {
            /*
            TODO:
                gc() should be automated trought cron or called with a random chance,
                for minimizing database operations.
            */
            $this->gc();
            return $this->CI->session_model->delete_active_session($uid, $sid);
        }
        parent::sess_destroy();
    }
}