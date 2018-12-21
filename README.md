# CodeIgniter 3 Active Sessions
Extend default session library.

## Install
Import SQL file from *sql* folder to you database.

Copy **session** folder to */application/third_party/* or whatever folder where you store your [packages](https://www.codeigniter.com/user_guide/libraries/loader.html#application-packages), load package and library trought CodeIgniter [Loader](https://www.codeigniter.com/user_guide/libraries/loader.html) class.
```php
$module_path = APPPATH . 'third_party' . DIRECTORY_SEPARATOR . 'session';
$this->load->add_package_path($module_path, false);
$this->load->library('session');
$this->load->remove_package_path($module_path);
```
*Note:* In case if you `subclass_prefix` was changed in */application/config/config.php*, prefix **MY_** should be changed accordingly in */session/libraries/Session/MY_Session.php*, class and filename should be renamed.

## Functions
### Set user ID.
You must use this method on every login, if you want track user active sessions.
```php
$this->session->set_user_id(USER_ID);
```
### Get user ID.
For retriving user id, you can use this function.
```php
$this->session->get_user_id();
```
### Delete old sessions
Remove expired active sessions.
```php
$this->session->gc();
```
*Note:* This function should be called trought cron or when you want retrieve actual list of active sessions.

## TODO
- [ ] Retrieve list of currently active sessions.
- [ ] Add random chance for GC when it called on session destroy.
- [ ] Allow multiple authentications for one session.
