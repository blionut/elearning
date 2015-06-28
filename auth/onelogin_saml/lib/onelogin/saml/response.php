<?php
  require 'xmlsec.php';

  class SamlResponse {
    private $nameid;
    private $xml;
    private $xpath;

    public $user_settings;

    function __construct($val) {
      $this->xml = new DOMDocument();
      $this->xml->loadXML(base64_decode($val));
    }

    function is_valid() {
      $xmlsec = new XmlSec($this->xml);
      $xmlsec->x509certificate = $this->user_settings->x509certificate;
      return $xmlsec->is_valid();
    }

    function get_nameid() {
		$xpath = new DOMXPath($this->xml);
		$xpath->registerNamespace("samlp","urn:oasis:names:tc:SAML:2.0:protocol");
		$xpath->registerNamespace("saml","urn:oasis:names:tc:SAML:2.0:assertion");
		$query = "/samlp:Response/saml:Assertion/saml:Subject/saml:NameID";
		$entries = $xpath->query($query);
		return $entries->item(0)->nodeValue;
    }
	
	function get_saml_attributes() {
	
		$attributes["username"][0] = $this->get_nameid();
		$attributes["email"][0] = $this->get_nameid();
		$attributes["emailAddress"][0] = $this->get_nameid();
		$attributes["firstname"][0] = $this->get_nameid();
	
		return $attributes;
		
		// OneLogin does not currently support user attribute mapping. This is for later
		$xpath = new DOMXPath($this->xml);
		$nodes = $xpath->query('attribute::*');//[namespace-uri(.) != ""]
		$i_count = 0;
		foreach ($nodes as $node) {
			$attributes[$i_count] = $node->nodeName;
			$i_count++;
		}
		return $attributes;
	}
	
  }
?>