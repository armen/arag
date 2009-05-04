<?php
/**
 * Session library.
 *
 * $Id: Session.php 3495 2008-09-26 04:18:37Z Shadowhand $
 *
 * @package    Core
 * @author     Kohana Team
 * @copyright  (c) 2007-2008 Kohana Team
 * @license    http://kohanaphp.com/license.html
 */
class Session extends Session_Core 
{
	/**
	 * Create a new session.
	 *
	 * @param   array  variables to set after creation
	 * @return  void
	 */
	public function create($vars = NULL)
	{
		// Destroy any current sessions
		$this->destroy();

		if (self::$config['driver'] !== 'native')
		{
			// Set driver name
			$driver = 'Session_'.ucfirst(self::$config['driver']).'_Driver';

			// Load the driver
			if ( ! Kohana::auto_load($driver))
				throw new Kohana_Exception('core.driver_not_found', self::$config['driver'], get_class($this));

			// Initialize the driver
			self::$driver = new $driver();

			// Validate the driver
			if ( ! (self::$driver instanceof Session_Driver))
				throw new Kohana_Exception('core.driver_implements', self::$config['driver'], get_class($this), 'Session_Driver');

			// Register non-native driver as the session handler
			session_set_save_handler
			(
				array(self::$driver, 'open'),
				array(self::$driver, 'close'),
				array(self::$driver, 'read'),
				array(self::$driver, 'write'),
				array(self::$driver, 'destroy'),
				array(self::$driver, 'gc')
			);
		}

		// Validate the session name
		if ( ! preg_match('~^(?=.*[a-z])[a-z0-9_]++$~iD', self::$config['name']))
			throw new Kohana_Exception('session.invalid_session_name', self::$config['name']);

		// Name the session, this will also be the name of the cookie
		session_name(self::$config['name']);

		// Set the session cookie parameters
		session_set_cookie_params
		(
			self::$config['expiration'],
			Kohana::config('cookie.path'),
			Kohana::config('cookie.domain'),
			Kohana::config('cookie.secure'),
			Kohana::config('cookie.httponly')
		);

		// Start the session!
		session_start();

		// Put session_id in the session variable
		$_SESSION['session_id'] = session_id();

        if ($_SESSION['session_id'] == Null) {
    		$this->regenerate();
        }

		// Set defaults
		if ( ! isset($_SESSION['_kf_flash_']))
		{
			$_SESSION['total_hits'] = 0;
			$_SESSION['_kf_flash_'] = array();

			$_SESSION['user_agent'] = Kohana::$user_agent;
			$_SESSION['ip_address'] = $this->input->ip_address();
		}

		// Set up flash variables
		self::$flash =& $_SESSION['_kf_flash_'];

		// Increase total hits
		$_SESSION['total_hits'] += 1;

		// Validate data only on hits after one
		if ($_SESSION['total_hits'] > 1)
		{
			// Validate the session
			foreach (self::$config['validate'] as $valid)
			{
				switch ($valid)
				{
					// Check user agent for consistency
					case 'user_agent':
						if ($_SESSION[$valid] !== Kohana::$user_agent)
							return $this->create();
					break;

					// Check ip address for consistency
					case 'ip_address':
						if ($_SESSION[$valid] !== $this->input->$valid())
							return $this->create();
					break;

					// Check expiration time to prevent users from manually modifying it
					case 'expiration':
						if (time() - $_SESSION['last_activity'] > ini_get('session.gc_maxlifetime'))
							return $this->create();
					break;
				}
			}
		}

		// Expire flash keys
		$this->expire_flash();

		// Update last activity
		$_SESSION['last_activity'] = time();

		// Set the new data
		self::set($vars);
	}
}
