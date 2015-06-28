<?php
  class authrequest {
    public $user_settings;

    public function create() {
      $id                = $this->generateUniqueID(20);
      $issue_instant     = $this->getTimestamp();

	  global $_SERVER;
      global $const_assertion_consumer_service_url;
      global $const_issuer;
      global $const_name_identifier_format;
	  global $onelogin_saml_name_identifier_format;
	  global $pluginconfig;
	  
	  $const_assertion_consumer_service_url = $pluginconfig->idp_sso_target_url;
	  $const_issuer = $pluginconfig->idp_sso_issuer_url;
	  $const_name_identifier_format = "urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress";
	  $const_name_identifier_format = $onelogin_saml_name_identifier_format; // ???
	  $add_port = "";
	  
	    if (!empty($url['port'])) {
			$add_port = ':'.$url['port'];
		} else if (!empty($_SERVER['SERVER_PORT'])) {
			if ($_SERVER['SERVER_PORT'] != 80 && $_SERVER['SERVER_PORT'] != 443) {
				$add_port = ':'.$_SERVER['SERVER_PORT'];
			}
		}
	  if (!empty($_SERVER['HTTPS'])) {
		$const_assertion_consumer_service_url = "https://".$_SERVER['SERVER_NAME'].$add_port.$_SERVER['REQUEST_URI'];
	  } else {
		$const_assertion_consumer_service_url = "http://".$_SERVER['SERVER_NAME'].$add_port.$_SERVER['REQUEST_URI'];
	  }
      $request =
        "<samlp:AuthnRequest xmlns:samlp=\"urn:oasis:names:tc:SAML:2.0:protocol\" ID=\"$id\" Version=\"2.0\" IssueInstant=\"$issue_instant\" ProtocolBinding=\"urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST\" AssertionConsumerServiceURL=\"".$const_assertion_consumer_service_url."\">".
        "<saml:Issuer xmlns:saml=\"urn:oasis:names:tc:SAML:2.0:assertion\">".$const_issuer."</saml:Issuer>\n".
        "<samlp:NameIDPolicy xmlns:samlp=\"urn:oasis:names:tc:SAML:2.0:protocol\" Format=\"".$const_name_identifier_format."\" AllowCreate=\"true\"></samlp:NameIDPolicy>\n".
        "<samlp:RequestedAuthnContext xmlns:samlp=\"urn:oasis:names:tc:SAML:2.0:protocol\" Comparison=\"exact\">".
        "<saml:AuthnContextClassRef xmlns:saml=\"urn:oasis:names:tc:SAML:2.0:assertion\">urn:oasis:names:tc:SAML:2.0:ac:classes:PasswordProtectedTransport</saml:AuthnContextClassRef></samlp:RequestedAuthnContext>\n".
        "</samlp:AuthnRequest>";
		
		//print_error("<pre>".htmlspecialchars($request)."</pre>");
	
      $deflated_request  = gzdeflate($request);
      $base64_request    = base64_encode($deflated_request);
      $encoded_request   = urlencode($base64_request);
	  if (!$this->user_settings->idp_sso_target_url) $this->user_settings->idp_sso_target_url = $pluginconfig->idp_sso_target_url;
      return $this->user_settings->idp_sso_target_url."?SAMLRequest=".$encoded_request;
    }

    private function generateUniqueID($length) {
      $chars = "abcdef0123456789";
      $chars_len = strlen($chars);
      $uniqueID = "";
      for ($i = 0; $i < $length; $i++)
        $uniqueID .= substr($chars,rand(0,15),1);
      return "_".$uniqueID;
    }

    private function getTimestamp() {
      date_default_timezone_set('UTC');
      return strftime("%Y-%m-%dT%H:%M:%SZ");
    }
  };
?>