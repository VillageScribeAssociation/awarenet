<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	summary of author in the nav
//--------------------------------------------------------------------------------------------------
//arg: raUID - recordAlias or UID of post [string]
//opt: postUID - overrides raUID [string]

function moblog_showauthornav($args) {
	if (array_key_exists('postUID', $args) == true) { $args['raUID'] = $args['postUID']; }
	if (array_key_exists('raUID', $args) == false) { return false; }
	$model = new Moblog($args['raUID']);
	if ($model->data['UID'] == '') { return false; }

	$userUID = $model->data['createdBy'];
	$userRa = raGetDefault('users', $userUID);

	$html = "<a href='/moblog/blog/" . $userRa . "'>";
	$html .= "[[:users::avatar::userUID=" . $userUID . "::size=width300::link=no:]]</a>\n";
	$html .= "[[:users::summarynav::userUID=" . $userUID . ":]]\n";

	return $html;
}

//--------------------------------------------------------------------------------------------------

?>

