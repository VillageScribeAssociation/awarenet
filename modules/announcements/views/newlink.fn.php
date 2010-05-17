<?

	require_once($installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//|	create a link to add a new announcement to something
//--------------------------------------------------------------------------------------------------
//arg: refModule - the module that will own the new announcement [string]
//arg: refUID - the record that will own the new announcment [string]

function announcements_newlink($args) {
	if (authHas('announcements', 'edit', $args) == false) { return false; }
	if (array_key_exists('refModule', $args) == false) { return false; }
	if (array_key_exists('refUID', $args) == false) { return false; }

	$cb = "[[:" . $args['refModule'] . "::haseditauth::refUID=" . $args['refUID'] . ":]]";
	$result = expandBlocks($cb, '');

	if ('yes' != $result) { return false; }

	$html = "<a href='/announcements/new/refModule_" . $args['refModule']
			 . "/refUID_" . $args['refUID'] . "/'>[add a new announcement]</a>";
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

