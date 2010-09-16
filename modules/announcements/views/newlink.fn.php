<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	create a link to add a new announcement to something
//--------------------------------------------------------------------------------------------------
//arg: refModule - the module that will own the new announcement [string]
//arg: refModel - the module that will own the new announcement [string]
//arg: refUID - the record that will own the new announcment [string]

function announcements_newlink($args) {
	global $user;

	if (false == array_key_exists('refModule', $args)) { return ''; }
	if (false == array_key_exists('refModel', $args)) { return ''; }
	if (false == array_key_exists('refUID', $args)) { return ''; }

	$refModule = $args['refModule'];
	$refModel = $args['refModel'];
	$refUID = $args['refUID'];

	if (false == $user->authHas($refModule, $refModel, 'addannouncement', $refUID)) { return ''; }

	$html = "<a href='/announcements/new/"
			 . "refModule_" . $refModule . "/"
			 . "refModel_" . $refModel . "/"
			 . "refUID_" . $refUID . "/'>[add a new announcement]</a>";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

