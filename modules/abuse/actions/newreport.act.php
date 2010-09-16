<?
	require_once($kapenta->installPath . 'modules/abuse/models/report.mod.php');

//--------------------------------------------------------------------------------------------------
//*	create a new Report object
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//*	check permissions and any POST variables
	//----------------------------------------------------------------------------------------------
	if ('public' == $user->role) { $page->do403(); }

	if (false == array_key_exists('refModule', $_POST))
		{ $page->do404('reference module not specified'); }
	if (false == array_key_exists('refModel', $_POST))
		{ $page->do404('reference model not specified'); }
	if (false == array_key_exists('refUID', $_POST))
		{ $page->do404('reference object UID not specified'); }
	if (false == $kapenta->moduleExists($_POST['module']))
		{ $page->do404('specified module does not exist'); }
	if (false == $db->objectExists($_POST['model'], $_POST['UID']))
		{ $page->do404('specified owner does not exist in database'); }

	//----------------------------------------------------------------------------------------------
	//*	create the object
	//----------------------------------------------------------------------------------------------
	$model = new Abuse_Report();
	foreach($_POST as $field => $value) {
		switch(strtolower($field)) {
			case 'refmodule':	$model->refModule = $value; break;
			case 'refmodel':	$model->refModel = $value; break;
			case 'refuid':		$model->refUID = $value; break;
			case 'comment':		$model->comment = $value; break;
			case 'title':		$model->title = $value; break;
			case 'fromurl':		$model->fromurl = str_replace($serverPath, '', $value); break;
			//case 'notes':		$model->notes = $utils->cleanString($value); break;
			//case 'status':	$model->status = $utils->cleanString($value); break;
		}
	}

	$model->status = 'open';
	$report = $model->save();

	//----------------------------------------------------------------------------------------------
	//*	check that object was created and redirect
	//----------------------------------------------------------------------------------------------
	if ('' == $report) {
		$msg = "[[:theme::navtitlebox::label=Thank You:]]<p>Report Submitted.</p><br/>\n";
		$session->msg($msg, 'ok')
		$page->do302($model->fromurl);
	} else {
		$session->msg('Could not create new Report:<br/>' . $report, 'bad');
		$page->do302($model->fromurl);
	}

?>
