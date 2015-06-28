<?php
/* * * * * * * * * *  The OneLogin SAML Authentication module for Moodle  * * * * * * * * *
 * 
 * auth.php - extends the Moodle core to embrace SAML
 * 
 * @originalauthor OneLogin, Inc
 * @author Harrison Horowitz
 * @version 1.0
 * @license http://www.gnu.org/copyleft/gpl.html GNU Public License
 * @package auth/onelogin_saml
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
	global $CFG;
	
	if (stristr(strtolower(PHP_OS), "win") === FALSE) {
		require_once($GLOBALS['CFG']->libdir.'/authlib.php');
	} else {
		require_once($GLOBALS['CFG']->libdir.'\authlib.php');
	}
	
	//if (!defined('MOODLE_INTERNAL')) {
	//	die('Direct access to this script is forbidden.');    ///  It must be included from a Moodle page
	//}

	/**
	 * OneLogin SAML for Moodle - base definition
	**/
	class auth_plugin_onelogin_saml extends auth_plugin_base {
		
		/**
		* Constructor.
		*/
		function auth_plugin_onelogin_saml() {
			$this->authtype = 'onelogin_saml';
			$this->config = get_config('auth/onelogin_saml');
		}
		
		/**
		* Returns true if the username and password work and false if they are
		* wrong or don't exist.
		*
		* @param string $username The username (with system magic quotes)
		* @param string $password The password (with system magic quotes)
		* @return bool Authentication success or failure.
		*/
		function user_login($username, $password) {
			// if true, user_login was initiated by onelogin_saml/index.php
			if($GLOBALS['onelogin_saml_login']) {
				unset($GLOBALS['onelogin_saml_login']);
				return TRUE;
			}
			return FALSE;
		}
		
		
		/**
		* Returns the user information for 'external' users. In this case the
		* attributes provided by Identity Provider
		*
		* @return array $result Associative array of user data
		*/
		function get_userinfo($username) {
			if ($login_attributes = $GLOBALS['onelogin_saml_login_attributes']) {
				$attributemap = $this->get_attributes();
				$attributemap['memberof'] = $this->config->username;
				$result = array();
		
				foreach ($attributemap as $key => $value) {
					// added this IF loop to fix firstname field for OneLogin logins
					if ($key == "firstname" && strpos($value, "@") > 0) {
						$value = substr($value, 0, strpos($value, "@"));
					}
					if(isset($login_attributes[$value]) && $attribute = $login_attributes[$value][0]) {
						$result[$key] = clean_param($attribute, PARAM_TEXT);
					} else {
						$result[$key] = clean_param($value, PARAM_TEXT); // allows user to set a hardcode default
					}
				}
				return $result;
			}
			
			return FALSE;
		}
		
		/*
		* Returns array containg attribute mappings between Moodle and Identity Provider.
		*/
		function get_attributes() {
			$configarray = (array) $this->config;
			
			$fields = array("email", "username", "emailAddress", "firstname");
			
			$moodleattributes = array();
			foreach ($fields as $field) {
				if (isset($configarray["field_map_$field"])) {
					$moodleattributes[$field] = $configarray["field_map_$field"];
				}
			}
			return $moodleattributes;
		}
		
		/**
		* Returns true if this authentication plugin is 'internal'.
		*
		* @return bool
		*/
		function is_internal() {
			return false; ######
		}
		
		/**
		* Returns true if this authentication plugin can change the user's
		* password.
		*
		* @return bool
		*/
		function can_change_password() {
			return false;
		}
		
		function loginpage_hook() {
			// Prevent username from being shown on login page after logout
			$GLOBALS['CFG']->nolastloggedin = true;
			
			return;
		}

		function logoutpage_hook() {
			global $SESSION;
			unset($SESSION->isSAMLSessionControlled);
			//if($this->config->dologout) {
				set_moodle_cookie('nobody');
				require_logout();
				redirect($GLOBALS['CFG']->wwwroot.'/auth/onelogin_saml/index.php?logout=1');
			//}
		}
		
		/**
		* Prints a form for configuring this authentication plugin.
		*
		* This function is called from admin/auth.php, and outputs a full page with
		* a form for configuring this plugin.
		*
		* @param array $page An object containing all the data for this page.
		*/

		function config_form($config, $err, $user_fields) {
			include "config.html";
		}

		/**
		 * A chance to validate form data, and last chance to
		 * do stuff before it is inserted in config_plugin
		 */
		 function validate_form(&$form, &$err) {
		 
		 }
		
		/**
		* Processes and stores configuration data for this authentication plugin.
		*
		*
		* @param object $config Configuration object
		*/
		function process_config($config) {
			// set to defaults if undefined
			if (!isset ($config->username)) {
				$config->username = 'emailAddress';
			}
			//if (!isset ($config->notshowusername)) {
			//	$config->notshowusername = '';
			//}
			//if (!isset ($config->duallogin)) {
			//	$config->duallogin = '';
			//}
			if (!isset($config->x509certificate)) {
				$config->x509certificate = '';
			}
			if (!isset($config->idp_sso_target_url)) {
				$config->idp_sso_target_url = '';
			}
			if (!isset($config->idp_sso_issuer_url)) {
				$config->idp_sso_issuer_url = '';
			}
			if (!isset($config->auto_create_users)) {
				$config->auto_create_users = '';
			}
			
			// save settings
			set_config('username',        $config->username,        'auth/onelogin_saml');
			//set_config('notshowusername', $config->notshowusername, 'auth/onelogin_saml');
			set_config('x509certificate', $config->x509certificate, 'auth/onelogin_saml');
			set_config('idp_sso_target_url', $config->idp_sso_target_url, 'auth/onelogin_saml');
			set_config('idp_sso_issuer_url', $config->idp_sso_issuer_url, 'auth/onelogin_saml');
			//set_config('duallogin',       $config->duallogin,       'auth/onelogin_saml');
			set_config('auto_create_users',  $config->auto_create_users, 'auth/onelogin_saml');
			
			return true;
		}
		
		/**
		* Cleans and returns first of potential many values (multi-valued attributes)
		*
		* @param string $string Possibly multi-valued attribute from Identity Provider
		*/
		function get_first_string($string) {
			$list = split(';', $string);
			$clean_string = trim($list[0]);
			
			return $clean_string;
		}
	}

?>