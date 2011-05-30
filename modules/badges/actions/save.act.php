<?

	require_once($kapenta->installPath . 'modules/badges/models/badge.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save changes to an Badge object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check permissions and POST variables
	//----------------------------------------------------------------------------------------------
	if (false == array_key_exists('action', $_POST)) { $page->do404('action not specified'); }
	if ('saveBadge' != $_POST['action']) { $page->do404('action not supported'); } 
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not POSTed'); }

	$UID = $_POST['UID'];

	if (false == $user->authHas('badges', 'badges_badge', 'edit', $UID))
		{ $page->do403('You are not authorized to edit this Badge.'); }

	//----------------------------------------------------------------------------------------------
	//	load and update the object
	//----------------------------------------------------------------------------------------------

	$model = new Badges_Badge($UID);
	if (false == $model->loaded) { $page->do404("could not load Badge $UID");}
	//TODO: sanitize description

	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'name':		$model->name = $utils->cleanString($value); break;
			case 'description':	$model->description = $theme->stripBlocks($value); break;
		}
	}
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//	check that object was saved and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) { $session->msg('Saved changes to Badge', 'ok'); }
	else { $session->msg('Could not save Badge:<br/>' . $report, 'bad'); }

	if (true == array_key_exists('return', $_POST)) { $page->do302($_POST['return']); }
	else { $page->do302('badges/show/' . $model->alias); }

?>
