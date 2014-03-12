<?php

//--------------------------------------------------------------------------------------------------
//|	action used by AJAX to set a subset of user registry keys
//--------------------------------------------------------------------------------------------------
//;	These are used for introduction boxes and other informative content which users can customize.
//;	Display of notices is recorded in the user registry with key values 'show' or 'hide', with
//;	missing values assumed to be 'show'.
//;
//;	For example, the key 'info.comments.policy' controls whether the comment policty is shown on
//;	comment forms.

	//----------------------------------------------------------------------------------------------
	//	check POST values and user role
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { $kapenta->page->doXmlError('You must log in to customize this.'); }

	//----------------------------------------------------------------------------------------------
	//	dev / admin option to reset the user registry
	//----------------------------------------------------------------------------------------------

	if ('reset' == $kapenta->request->ref) {
		$kapenta->user->set('info.comments.policy', 'show');
		$kapenta->user->set('info.sketchpad.intro', 'show');
		$kapenta->session->msg('Reset user registry.');
		$kapenta->page->do302('users/profile/');
	}

	//----------------------------------------------------------------------------------------------
	//	set a user registry key
	//----------------------------------------------------------------------------------------------

	if (false == array_key_exists('key', $_POST)) { $kapenta->page->doXmlError('Key not specified'); }
	if (false == array_key_exists('value', $_POST)) { $kapenta->page->doXmlError('Value not specified'); }

	$key = $_POST['key'];
	$value = $_POST['value'];

	$allow = false;

	if ('info' == substr($key, 0, 4)) { $allow = true; }

	if (false == $allow) { $kapenta->page->doXmlError('This key cannot be set by AJAX.'); }

	//----------------------------------------------------------------------------------------------
	//	change the registry setting
	//----------------------------------------------------------------------------------------------
	$check = $kapenta->user->set($key, $value);
	if (false == $check) { $kapenta->page->doXmlError('Could not set key.'); }
	echo "<ok/>";

?>
