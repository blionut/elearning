<?php
	global $CFG;

	$string['auth_onelogin_samltitle']         = 'OneLogin SAML'; //SSO Authentication
	
	
	
	$string['auth_onelogin_samldescription']   = '
		<p style="text-align:center;" align="center"><strong><span style=" text-decoration:underline;">
			Single Sign-On (SSO) SAML-based authentication by the one and only...</span> <br /><br /><a href="http://support.onelogin.com/" style="text-decoration:none;font-size:24px;">OneLogin</a></strong>
		</p>
		<br />
		<p>
			Security Assertion Markup Language (SAML) is a standard for logging users into applications based 
			on their session in another context. This has significant advantages over logging in using a 
			username/password: no need to type in credentials, no need to remember and renew password, no weak
			passwords etc. Most companies already know the identity of users because they are logged into their Active Directory
			domain or intranet. It is natural to use this information to log users into other applications as well 
			such as web-based application, and one of the more elegant ways of doing this by using SAML. SAML is very powerful and flexible, 
			but the specification can be quite a handful. Now OneLogin is releasing this SAML toolkit for your Moodle application to enable you 
			to integrate SAML in seconds instead of months. We\'ve filtered the signal from the noise and come up with a simple setup that will work for most 
			applications out there.
		</p>
		<br />
		<p style="font-weight:bold; text-decoration:underline; text-align:center;">Module Setup Notes</p>
		<div align="center">
			<p style="text-align:center">
				<div align="center" style="width:700px; text-align:center;">
					<div align="left">
						<p align="left">
							For the greatest convenience and security, be sure to perform the following steps...
							<br />
							<ul>
								<li>
									Go to your <a target="_blank" title="New Window" href="'.$CFG->wwwroot.'/admin/settings.php?section=manageauths">Manage Authentication</a> page and...
									<ul>
										<li>
											Enable the OneLogin SAML authentication module by clicking on the eyeball so that the eye is open.
										</li>
										<li>
											Click the UP arrow to prioritize the SAML authentication above all of the others.
										</li>
										<li>
											Disable "Self-registration" (optional but recommended)
										</li>
										<li>
											In the "Alternative login URL" textbox   <strong>&larr;   '.$CFG->wwwroot.'/auth/onelogin_saml</strong>
										</li>
									</ul>
								</li>
								<li>
									Configure the options below from your company\'s OneLogin Moodle connector.
								</li>
							</ul>
							<br />
						</p>
					</div>
				</div>
			</p>
			<p style="text-align:center; padding-left:175px;">
				<div align="center" style="background-color:#006600; width:620px; padding:1px; text-align:center;">
					<p align="center" style="text-weight:bold; background-color:#FFFFFF; width:98%; padding-top:5px; padding-bottom:5px;">
						<span style="font-weight:bold;"><i>SECRET  FOR  ADMINS</i></span>
						<br /><br /> Skip the SAML process and see the regular login box by adding <span style="font-size:24px;">&ldquo;</span>?normal<span style="font-size:24px;">&rdquo;</span> to your normal login URL.
						<br /> <a href="'.$CFG->wwwroot.'/login/index.php?normal" title="Normal login mode">'.$CFG->wwwroot.'/login/index.php?normal</a>
						<br />
						<br /><strong>To enable this feature, you must find the 1 line of code in "/login/index.php" that looks like...</strong>
						<br /><span style="font-size:24px;">&ldquo;</span>if (!empty($CFG->alternateloginurl)) {<span style="font-size:24px;">&rdquo;</span>
						<br /><strong>...and change it to...</strong>
						<br /><span style="font-size:24px;">&ldquo;</span>if (!empty($CFG->alternateloginurl) && !isset($_GET[\'normal\'])) {<span style="font-size:24px;">&rdquo;</span>
					</p>
				</div>
			</p>
		</div>'; // <br/> Do not forget to edit the configuration file: '.$CFG->dirroot.'/auth/onelogin_saml/config.php
	$string['auth_onelogin_saml_duallogin'] = 'Enable Dual login for users';
	$string['auth_onelogin_saml_duallogin_description'] = 'Enable to allow your users to login WITHOUT identity verification. It is recommended that this remains disabled/unchecked.';
	$string['auth_onelogin_saml_notshowusername'] = 'Do not show username';
	$string['auth_onelogin_saml_notshowusername_description'] = 'Check to have Moodle not show the username for users logging in by Identity Provider.';
	$string['auth_onelogin_saml_auto_create_users'] = 'Automatically create users?';
	$string['auth_onelogin_saml_auto_create_users_description'] = '<strong>Check to <span style="text-decoration:underline;">automatically create local user accounts</span> which do not already exist if the visitor is OneLogin-verified.</strong>
	<br />If enabled, the end-user\'s OneLogin-verified e-mail address will be applied to the newly created Moodle accounts as the Moodle user account\'s e-mail address, first name, and username.
	<br />By default, the accounts are created without a password, and the user must login via SAML identity verification.';
	$string['auth_onelogin_saml_x509certificate'] = 'OneLogin Certificate Key';
	$string['auth_onelogin_saml_x509certificate_description'] = 'Paste in the secret digital security encryption certificate key generated by OneLogin';
	$string['auth_onelogin_saml_idp_sso_target_url'] = 'SSO Identity Provider URL';
	$string['auth_onelogin_saml_idp_sso_target_url_description'] = 'The OneLogin-supplied single sign-on identity provider URL for your server';
	$string['auth_onelogin_saml_idp_sso_issuer_url'] = 'SSO SAML Issuer URL';
	$string['auth_onelogin_saml_idp_sso_issuer_url_description'] = 'The OneLogin-supplied SAML Issuer URL for your company';
	$string['auth_onelogin_saml_username'] = 'OneLogin SAML Username Mapping';
	$string['auth_onelogin_saml_username_description'] = 'The Moodle user attribute being supplied by OneLogin as the primary unique ID representing the user. (defaults to emailAddress)';
	$string['retriesexceeded'] = 'Maximum number of SAML connection retries exceeded  - there must be a problem with the Identity Service.<br />Please try again in a few minutes.';
	$string['pluginauthfailed'] = 'The OneLogin SAML authentication plugin failed - user $a disallowed (no user auto-creation?) or dual login disabled.';
	$string['pluginauthfailedusername'] = 'The OneLogin SAML authentication plugin failed - user $a disallowed due to invalid username format.';
	$string['auth_onelogin_saml_username_error'] = 'The identity provider returned a set of data that does not contain the SAML username mapping field. This field is required to login. <br />Please check your Username Attribute Mapping configuration.';
	$string['pluginname'] = 'OneLogin SAML SSO Authentication';
?>