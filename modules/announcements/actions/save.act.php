<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	save an Announcements_Announcement object
//--------------------------------------------------------------------------------------------------
//TODO: update this with more generic (GENERATED) code

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action not specified.'); }
	if ('saveRecord' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('Announcement not specified.'); }
	
	$model = new Announcements_Announcement($_POST['UID']);
	if (false == $model->loaded) { $page->do404('Announcement not found.'); }

	$authorised = false;

	//	other auth methods (admins can make any announcement they please)
	if (true == $user->authHas($model->refModule, $model->refModel, 'announcements-add', $model->refUID))
		{ $authorised = true; }

	if (false == $authorised) { $page->do403('You are not authorized to make announcements.'); }

	//----------------------------------------------------------------------------------------------
	//	save the object
	//----------------------------------------------------------------------------------------------
	$model->title = $utils->cleanTitle($_POST['title']);
	$model->content = $utils->cleanHtml($_POST['content']);
	$ext = $model->extArray();
	$report = $model->save();

	if ('' != $report) {
		$session->msg('The announement could not be saved: ' . $report, 'bad');
		$page->do302('announcements/' . $model->alias);
	}

	//----------------------------------------------------------------------------------------------
	//	raise event
	//----------------------------------------------------------------------------------------------
	$args = array(
		'refModule' => $model->refModule,
		'refModel' => $model->refModel,
		'refUID' => $model->refUID,
		'UID' => $model->UID,
		'title' => $model->title,
		'content' => $model->content
	);	

	$kapenta->raiseEvent('*', 'announcement_updated', $args);

	$page->do302('announcements/' . $model->alias); 

?>
