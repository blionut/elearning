<?php 
/* * * * * * * * * *  The OneLogin SAML 2.0 authentication module for Moodle  * * * * * * * * *
 * 
 * index.php - landing page for auth/onelogin_saml
 * 
 * @originalauthor OneLogin, Inc
 * @author Harrison Horowitz
 * @version 1.0
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package auth/onelogin_saml
 * @requires XMLSecLibs v1.2.2
 * @copyright 2011 OneLogin.com
 * 
 * @description 
 * Connects to Moodle, builds the configuration, discovers SAML status, and handles the login process accordingly.
 * 
 * Security Assertion Markup Language (SAML) is a standard for logging users into applications based 
 * on their session in another context. This has significant advantages over logging in using a 
 * username/password: no need to type in credentials, no need to remember and renew password, no weak 
 * passwords etc.
 * 
 * Most companies already know the identity of users because they are logged into their Active Directory 
 * domain or intranet. It is natural to use this information to log users into other applications as well 
 * such as web-based application, and one of the more elegant ways of doing this by using SAML.
 * 
 * SAML is very powerful and flexible, but the specification can be quite a handful. Now OneLogin is 
 * releasing this SAML toolkit for your Moodle application to enable you to integrate SAML in seconds 
 * instead of months. We’ve filtered the signal from the noise and come up with a simple setup that will 
 * work for most applications out there.
 * 
 */
	global $CFG, $USER, $SESSION, $POST, $_POST, $_GET, $_SERVER, $DB, $SITE;

	define('SAML_INTERNAL', 1);
	define('SAML_RETRIES', 10);
	$SAML_RETRIES = 10;

	//$CFG->libdir = str_replace("/", "\\", $CFG->libdir);
	
	//require_once($CFG->libdir.'\dml\moodle_database.php');
	//require_once("auth.php");
	
	// either way we don't want to be SAML controlled at this stage
	unset($SESSION->isSAMLSessionControlled);
	
	// check what kind of request this is
	if(isset($_GET["logout"])) {
		if (isset($SESSION->retry)) {
			$SESSION->retry = 0;
		}
		//if ($valid_saml_session) {
			session_write_close();
			header('Location: /login/logout.php?logout=1&sesskey='.$USER->sesskey); // added
			//logoutpage_hook();
		//} else {
		//	@session_write_close();
		//	@header($_SERVER['SERVER_PROTOCOL'] . ' 303 See Other');
		//	@header('Location: '.$ONELOGIN_SAML_LOGOUT_LINK);
		//	die;
		//}
		unset($SESSION->isSAMLSessionControlled);
		//set_moodle_cookie('nobody');
		//require_logout();
		//redirect($GLOBALS['CFG']->wwwroot.'/auth/onelogin_saml/index.php?logout=1', 0);
		exit(0);
	}

	// do the normal Moodle bootstraping so we have access to all config and the DB // moved
	require_once('../../config.php');

	/**
	 * check that the saml session is OK - if not, send to OneLogin for authentication
	 * if good, then do the Moodle login, and send to the home page, or landing page
	 * if otherwise specified
	 */
	$retry = isset($SESSION->retry) ? $SESSION->retry : 0;
	if ($retry == $SAML_RETRIES) {
		// too many tries at logging in
		session_write_close();
		require_once('../../config.php');
		print_error('retriesexceeded', 'auth_onelogin_saml', '', $retry);
	}
	$SESSION->retry = $retry + 1;

	// save the jump target - this is checked later that it starts with $CFG->wwwroot, and cleaned
	if (isset($_GET['wantsurl'])) {
		$wantsurl = $SESSION->wantsurl = $_GET['wantsurl'];
	}
	
	// check for a wantsurl in the existing Moodle session 
	if (empty($wantsurl) && isset($SESSION->wantsurl)) {
		$wantsurl = $SESSION->wantsurl;
	}

	// get the plugin config for saml
	$pluginconfig = get_config('auth/onelogin_saml');

	
	if (!isset($_POST['SAMLResponse']) && !((isset($_GET['normal']) && $_GET['normal']) || (isset($SESSION->normal) && $SESSION->normal)) && !(isset($_GET['logout']) && $_GET['logout'])) {
		## myDebugger("SAML REQUEST");
		$onelogin_saml_issuer = "onelogin_saml";
		$onelogin_saml_name_identifier_format = "urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress";
		require_once('lib/onelogin/saml.php');
		$authrequest = new AuthRequest();
		$authrequest->user_settings = get_user_settings();
		$onelogin_saml_url = $authrequest->create();
		redirect($onelogin_saml_url, 0);
		
	} elseif (isset($_POST['SAMLResponse']) && $_POST['SAMLResponse'] && !(isset($_GET['normal']) && $_GET['normal']) || (isset($SESSION->normal) && $SESSION->normal) && !(isset($_GET['logout']) && $_GET['logout'])) {
		## myDebugger("SAML RESPONSE");
		require_once 'lib/onelogin/saml.php';		
		$samlresponse = new SamlResponse($_POST['SAMLResponse']);
		$samlresponse->user_settings = get_user_settings();
		if (!$samlresponse->is_valid()) print_error("An invalid SAML response was received from the Identity Provider. Contact the admin.");//onelogin_saml_auth($samlresponse);
		$username = $onelogin_saml_nameId = $samlresponse->get_nameid();
		
		// make variables accessible to saml->get_userinfo; Information will be requested from authenticate_user_login -> create_user_record / update_user_record
		$GLOBALS['onelogin_saml_login_attributes'] = $saml_attributes = $samlresponse->get_saml_attributes();
		## myDebugger("<pre>SAML ATTRIBUTES...<br />".print_r($saml_attributes, true)."SAML...<br />".htmlentities(base64_decode($_POST['SAMLResponse']))."</pre>");
		$wantsurl = isset($SESSION->wantsurl) ? $SESSION->wantsurl : FALSE;
	} else {
		// You shouldn't be able to reach here.
		print_error("Module Setup Error: Review the OneLogin setup instructions for the SAML authentication module, and be sure to change the following one line of code in Moodle's core in 'login/index.php'.<br /><br /><div style=\"text-align:center;\">CHANGE THE FOLLOWING LINE OF CODE (in 'login/index.php')...</div><br /><font style=\"font-size:18px;\"><strong>if (!empty(\$CFG->alternateloginurl)) {</strong></font><br /><br /><div style=\"text-align:center;\">...to...</div><br /><strong><font style=\"font-size:18px;\">if (!empty(\$CFG->alternateloginurl) && !isset(\$_GET['normal'])) { </font></strong> \r\n");
	}

	///$wantsurl = isset($SESSION->wantsurl) ? $SESSION->wantsurl : FALSE;
	///unset($SESSION->retry);
	///unset($SESSION->wantsurl);
	///session_write_close();

	// Valid session. Register or update user in Moodle, log him on, and redirect to Moodle front
	// we require the plugin to know that we are now doing a saml login in hook puser_login
	$GLOBALS['onelogin_saml_login'] = TRUE;


	// check user name attribute actually passed
	if(!isset($onelogin_saml_nameId)) {
		error_log('auth_onelogin_saml: auth failed due to missing username saml attribute: '.$pluginconfig->username);
		session_write_close();
		$USER = new object();
		$USER->id = 0;
		require_once('../../config.php');
		print_error('auth_onelogin_saml: auth failed due to missing username saml attribute: '.$pluginconfig->username."<br />".get_string("auth_onelogin_saml_username_error", "auth_onelogin_saml")."\r\n\r\nonelogin_saml_nameId=".$onelogin_saml_nameId);
	}


	# myDebugger('auth_onelogin_saml: authenticating username: '.$username);
	if ($_POST['SAMLResponse']) { // isset($pluginconfig->duallogin) && $pluginconfig->duallogin && 
		$USER = auth_onelogin_saml_authenticate_user_login($username, time());
		// added the following
		if ($USER == false && ($pluginconfig->duallogin == "on" || $pluginconfig->duallogin == "checked" || $pluginconfig->duallogin === true)) {
			print_error("Attempted SAML authentication process and failed; deferring to regular methods...");
			$USER = authenticate_user_login($username, time());
		}
	} else {    
		print_error("Info received. Finishing authentication process through regular method hook because no SAML response detected.");
		display_object($_POST);
		$USER = authenticate_user_login($username, time());
	}

	// check that the signin worked
	if ($USER == false) {
		print_error("You could not be identified or created. <br />Login result: FAILURE<br />I have...<br />".htmlspecialchars(print_r($USER, true)));
		session_write_close();
		$USER = new object();
		$USER->id = 0;
		require_once('../../config.php');
		print_error('pluginauthfailed', 'auth_onelogin_saml', '', $onelogin_saml_nameId); //$saml_attributes[$pluginconfig->username][0]
	}
	
	// complete the user login sequence
	$USER->loggedin = true;
	$USER->site     = $CFG->wwwroot;
	$USER = get_complete_user_data('id', $USER->id);
	complete_user_login($USER);
	## myDebugger("You have been identified via SAML. <br />I will now complete the login process for you.<br />All about you...<br />".htmlspecialchars(print_r($USER, true)));

	
	if (isset($wantsurl)) {// and (strpos($wantsurl, $CFG->wwwroot) === 0)
		$urltogo = clean_param($wantsurl, PARAM_URL);
	} else {
		$urltogo = $CFG->wwwroot.'/';
	}
	if (!$urltogo || $urltogo == "") $urltogo = $CFG->wwwroot.'/';
	//print_error("I am going to send you to... <br /><pre>".htmlspecialchars(print_r($CFG, true))."</pre>");

	///auth_saml_err($urltogo);

	// flag this as a SAML based login
	$SESSION->isSAMLSessionControlled = true;
	unset($SESSION->wantsurl);
	redirect($urltogo, 0);

	function get_user_settings() {
		#
		# #
		# # #	this function returns the SAML settings for the current user
		# #
		#
		global $pluginconfig;
		$settings                           = new Settings();
		$settings->idp_sso_target_url       = $pluginconfig->idp_sso_target_url;
		$settings->idp_sso_issuer_url       = $pluginconfig->idp_sso_issuer_url;
		$settings->x509certificate          = $pluginconfig->x509certificate;
		return $settings;
	}

	/**
	 * Copied from moodlelib:authenticate_user_login()
	 * 
	 * WHY? because I need to hard code the plugins to auth_saml, and this user
	 * may be set to any number of other types of login method
	 * 
	 * First of all - make sure that they aren't nologin - we don't mess with that!
	 * 
	 * 
	 * Given a username and password, this function looks them
	 * up using the currently selected authentication mechanism,
	 * and if the authentication is successful, it returns a
	 * valid $user object from the 'user' table.
	 *
	 * Uses auth_ functions from the currently active auth module
	 *
	 * After authenticate_user_login() returns success, you will need to
	 * log that the user has logged in, and call complete_user_login() to set
	 * the session up.
	 *
	 * @uses $CFG
	 * @param string $username  User's username (with system magic quotes)
	 * @param string $password  User's password (with system magic quotes)
	 * @return user|flase A {@link $USER} object or false if error
	 */
	function auth_onelogin_saml_authenticate_user_login($username, $password) {

		global $CFG, $DB;

		// ensure that only saml auth module is chosen
		$authsenabled = get_enabled_auth_plugins();    

		if ($user = get_complete_user_data('username', $username)) {
			# print_error(htmlspecialchars(print_r($user, true)));
			$auth = empty($user->auth) ? 'manual' : $user->auth;  // use manual if auth not set
			if ($auth=='nologin' or !is_enabled_auth($auth)) {
				add_to_log(0, 'login', 'error', 'index.php', $username);
				print_error('[client '.getremoteaddr()."]  $CFG->wwwroot  ---&gt;  DISABLED LOGIN:  $username  ".$_SERVER['HTTP_USER_AGENT']);
				return false;
			}
		} else {
			// check if there's a deleted record (cheaply)
			$query_conditions['username'] = $username;
			$query_conditions['deleted'] = 1;
			if ($DB->get_field('user', 'id', $query_conditions)) {
				print_error('[client '.$_SERVER['REMOTE_ADDR']."]  $CFG->wwwroot  ---&gt;  DELETED LOGIN:  $username  ".$_SERVER['HTTP_USER_AGENT']);
				return false;
			}

			$auths = $authsenabled;
			$user = new object();
			$user->id = 0;     // User does not exist
		}

		// hard code SAML
		$auths = array('onelogin_saml');
		foreach ($auths as $auth) {
			$authplugin = get_auth_plugin($auth);

			// on auth fail fall through to the next plugin
			if (!$authplugin->user_login($username, $password)) {
				continue;
			}

			// successful authentication
			if (!$user->id) {
				// if user not found, create him
				$user = create_user_record($username, $password, $auth);
				## myDebugger("Attempted new user creation because user did not exist. Result is...<br /><br />".htmlspecialchars(print_r($user, true)));	
			}
			if ($user->id) {
                          	// User already exists in database
				if (empty($user->auth)) {             // For some reason auth isn't set yet
					$query_conditions['username'] = $username;
					$DB->set_field('user', 'auth', $auth, $query_conditions);
					$user->auth = $auth;
				}
				if (empty($user->firstaccess)) { //prevent firstaccess from remaining 0 for manual account that never required confirmation
					$query_conditions['id'] = $user->id;
					$DB->set_field('user', 'firstaccess', $user->timemodified, $query_conditions);
					$user->firstaccess = $user->timemodified;
				}
				if (empty($user->email) && stristr($username, "@")) {
					$query_conditions['id'] = $user->id;
					$DB->set_field('user', 'email', $username, $query_conditions);
					$user->email = $username;
				}
				if (empty($user->firstname)) {
					$query_conditions['id'] = $user->id;
					$DB->set_field('user', 'firstname', $username, $query_conditions);
					$user->firstname = $username;
				}

				// we don't want to upset the existing authentication schema for the user
				// update_internal_user_password($user, $password); // just in case salt or encoding were changed (magic quotes too one day)

				// update user record from external DB
				if (!$authplugin->is_internal()) { 
					# myDebugger("Authplugin not internal: updating from external source...");
					$user = update_user_record($username, get_auth_plugin($user->auth));
				}
			
			}




			$authplugin->sync_roles($user);

			foreach ($authsenabled as $hau) {
				$hauth = get_auth_plugin($hau);
				$hauth->user_authenticated_hook($user, $username, $password);
			}
			if ($user->id===0) {
				print_error("Failed to create user with the following details... <br /> <pre>".htmlspecialchars(print_r($user, true))."</pre>");
				return false;
			}
			return $user;
		}

		// failed if all the plugins have failed
		add_to_log(0, 'login', 'error', 'index.php', $username);
		//if (myDebugger('', DEBUG_ALL)) {
			print_error('[client '.getremoteaddr()."]  $CFG->wwwroot  ---&gt;  FAILED LOGIN: $username  ".$_SERVER['HTTP_USER_AGENT']);
		//}
		return false;
	}
	
	
	function logoutpage_hook() {
		global $SESSION;
		unset($SESSION->isSAMLSessionControlled);
		//if($this->config->dologout) {
			//set_moodle_cookie('nobody');
			//require_logout();
			redirect($GLOBALS['CFG']->wwwroot.'/auth/onelogin_saml/index.php?logout=1', 0);
		//}
	}
	
	/**
	 * Add slashes for single quotes and backslashes
	 * so they can be included in single quoted string
	 * (for config.php)
	 */
	function auth_onelogin_saml_addsingleslashes($input){
		return preg_replace("/(['\\\])/", "\\\\$1", $input);
	}
	/**
	 * Like {@link me()} but returns a full URL
	 * @see me()
	 * @return string
	 */
	function auth_onelogin_saml_qualified_me() {

		global $CFG;

		if (!empty($CFG->wwwroot)) {
			$url = parse_url($CFG->wwwroot);
		}

		if (!empty($url['host'])) {
			$hostname = $url['host'];
		} else if (!empty($_SERVER['SERVER_NAME'])) {
			$hostname = $_SERVER['SERVER_NAME'];
		} else if (!empty($_ENV['SERVER_NAME'])) {
			$hostname = $_ENV['SERVER_NAME'];
		} else if (!empty($_SERVER['HTTP_HOST'])) {
			$hostname = $_SERVER['HTTP_HOST'];
		} else if (!empty($_ENV['HTTP_HOST'])) {
			$hostname = $_ENV['HTTP_HOST'];
		} else {
			notify('Warning: could not find the name of this server!');
			return false;
		}

		if (!empty($url['port'])) {
			$hostname .= ':'.$url['port'];
		} else if (!empty($_SERVER['SERVER_PORT'])) {
			if ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
				$hostname .= ':'.$_SERVER['SERVER_PORT'];
			}
		}

		// TODO, this does not work in the situation described in MDL-11061, but
		// I don't know how to fix it. Possibly believe $CFG->wwwroot ahead of what
		// the server reports.
		if (isset($_SERVER['HTTPS'])) {
			$protocol = ($_SERVER['HTTPS'] == 'on') ? 'https://' : 'http://';
		} else if (isset($_SERVER['SERVER_PORT'])) { # Apache2 does not export $_SERVER['HTTPS']
			$protocol = ($_SERVER['SERVER_PORT'] == '443') ? 'https://' : 'http://';
		} else {
			$protocol = 'http://';
		}

		$url_prefix = $protocol.$hostname;
		return $url_prefix;
	}
	/**
	 * Returns the name of the current script, WITH the querystring portion.
	 * this function is necessary because PHP_SELF and REQUEST_URI and SCRIPT_NAME
	 * return different things depending on a lot of things like your OS, Web
	 * server, and the way PHP is compiled (ie. as a CGI, module, ISAPI, etc.)
	 * <b>NOTE:</b> This function returns false if the global variables needed are not set.
	 *
	 * @return string
	 */
	function auth_onelogin_saml_me() {

		if (!empty($_SERVER['REQUEST_URI'])) {
			return $_SERVER['REQUEST_URI'];

		} else if (!empty($_SERVER['PHP_SELF'])) {
			if (!empty($_SERVER['QUERY_STRING'])) {
				return $_SERVER['PHP_SELF'] .'?'. $_SERVER['QUERY_STRING'];
			}
			return $_SERVER['PHP_SELF'];

		} else if (!empty($_SERVER['SCRIPT_NAME'])) {
			if (!empty($_SERVER['QUERY_STRING'])) {
				return $_SERVER['SCRIPT_NAME'] .'?'. $_SERVER['QUERY_STRING'];
			}
			return $_SERVER['SCRIPT_NAME'];

		} else if (!empty($_SERVER['URL'])) {     // May help IIS (not well tested)
			if (!empty($_SERVER['QUERY_STRING'])) {
				return $_SERVER['URL'] .'?'. $_SERVER['QUERY_STRING'];
			}
			return $_SERVER['URL'];

		} else {
			notify('Warning: Could not find any of these web server variables: $REQUEST_URI, $PHP_SELF, $SCRIPT_NAME or $URL');
			return false;
		}
	}
	function auth_onelogin_saml_err($msg) {
		$stderr = fopen('php://stderr', 'w');
		fwrite($stderr,"auth_plugin_onelogin_saml: ". $msg . "\n");
		fclose($stderr);
	}
	
	//global $all_debug_msgs;
	//$all_debug_msgs = "";
	function myDebugger($msg) {
		//$all_debug_msgs .= "<br /><br />" . $msg;
		if ($msg != "SAML REQUEST") {
			print_error($msg);
		}
	}
	//if ($all_debug_msgs) print_error($all_debug_msgs);

?>