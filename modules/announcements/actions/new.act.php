<?

	require_once($kapenta->installPath . 'modules/announcements/models/announcement.mod.php');

//--------------------------------------------------------------------------------------------------
//*	add a new announcement
//--------------------------------------------------------------------------------------------------
	
	if (false == $user->authHas('announcements', 'Announcements_Announcement', 'new'))
		{ $page->do403('You are not aothorized to make new announcements.'); }
	//TODO: add inheritable permission for groups

	if (false == array_key_exists('refmodule', $req->args)) { $page->do404(); }
	if (false == array_key_exists('refuid', $req->args)) { $page->do404(); }

	//----------------------------------------------------------------------------------------------
	//	check user is authorised to make an announcement for this item
	//----------------------------------------------------------------------------------------------
	$isauth = false;
	$model = new Announcements_Announcement($req->ref);
	$cb = "[[:". $req->args['refmodule'] ."::haseditauth::raUID=".  $req->args['refuid'] .":]]";
	$result = $theme->expandBlocks($cb, '');
	if ('yes' == $result) { $isauth = true; }

	//echo "result: $result <br/>\n";

	if ('admin' == $user->role) { $isauth = true; }
	if ('teacher' == $user->role) { $isauth = true; }
	if (false == $isauth) { $page->do403(); }

	//----------------------------------------------------------------------------------------------
	//	OK then, create it
	//----------------------------------------------------------------------------------------------

	$model = new Announcements_Announcement();
	$model->notifications = 'init';
	$model->title = 'Announcement';
	$model->refModule = $req->args['refmodule'];
	$model->refUID = $req->args['refuid'];
	$model->save();

	$page->do302('announcements/edit/' . $model->UID);

?>
