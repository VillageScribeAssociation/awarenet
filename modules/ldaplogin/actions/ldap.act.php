<?php

//--------------------------------------------------------------------------------------------------
//*	show icons, drag & drop into iconpassword fields (one per letter), generate password field, password field (user copies text) and login button 
//--------------------------------------------------------------------------------------------------
	function ldaplogin_check($username, $password) {
		if (strlen($username) > 0 && strlen($password) > 0) {
			return ldaplogin_bind($username, $password);
		} else {
			return true;
		}
	}
	
	function ldaplogin_bind($username, $password) {
		global $kapenta;
		
		if (strlen($username) > 0 && strlen($password) > 0) {
			return true;
			$ldap = ldap_connect($kapenta->registry->get('ldaplogin.server'), (int)$kapenta->registry->get('ldaplogin.port') );
			if (false !== $ldap && $bind = ldap_bind($ldap, $username, $password)) {
				ldap_unbind($ldap);
	  			return true;
			} else {
		  		return false;
			}
		}
		
		return false;
	}

//	if ('' != $kapenta->request->ref) { $page->do404(); }							// check ref

//	$style = getPictureLoginStyle();
//	$script = getPictureLoginScript();

	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
//	$kapenta->page->load('modules/picturelogin/actions/picturelogin.page.php');
//	$kapenta->page->blockArgs['head'] = '<style>' . $style . '</style>' . $script;
//	$kapenta->page->render();

?>
