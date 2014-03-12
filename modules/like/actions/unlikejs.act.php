<?

	require_once($kapenta->installPath . 'modules/like/models/something.mod.php');

//--------------------------------------------------------------------------------------------------
//*	action called by Javascript to note that a user 'unlikes' something
//--------------------------------------------------------------------------------------------------
//postarg: refModule - name of a kapenta module [string]
//postarg: refModule - type of object being unliked [string]
//postarg: refUID - UID of object being unliked [string]

	//----------------------------------------------------------------------------------------------
	//	check arguments and user role
	//----------------------------------------------------------------------------------------------
	if (('public' == $kapenta->user->role) || ('banned' == $kapenta->user->role)) { $kapenta->page->doXmlError('403'); }

	if (false == array_key_exists('refModule', $_POST)) { $kapenta->page->doXmlError('no refModule given'); }
	if (false == array_key_exists('refModel', $_POST)) { $kapenta->page->doXmlError('no refModel given'); }
	if (false == array_key_exists('refUID', $_POST)) { $kapenta->page->doXmlError('no refUID given'); }

	$refModule = $_POST['refModule'];
	$refModel = $_POST['refModel'];
	$refUID = $_POST['refUID'];

	if (false == $kapenta->moduleExists($refModule)) { $kapenta->page->doXmlError('No such module.'); }
	if (false == $kapenta->db->objectExists($refModel, $refUID)) { $kapenta->page->doXmlError('No such object.'); }

	//----------------------------------------------------------------------------------------------
	//	check that the user already likes this item
	//----------------------------------------------------------------------------------------------
	$block = '[[:like::byuser'
	 . '::userUID=' . $kapenta->user->UID
	 . '::refModule=' . $refModule
	 . '::refModel=' . $refModel
	 . '::refUID=' . $refUID
	 . ':]]';

	$likeUID = $theme->expandBlocks($block);
	if ('' == $likeUID) { $kapenta->page->doXmlError('No existing like.'); }

	//----------------------------------------------------------------------------------------------
	//	cancel (but do not delete) the 'like' object
	//----------------------------------------------------------------------------------------------
	
	$model = new Like_Something($likeUID);
	if (false == $model->loaded) { $kapenta->page->doXmlError("Could not load 'like', DB_ERROR."); }
	$model->cancelled = 'yes';
	$report = $model->save();
	
	if ('' != $report) { $kapenta->page->doXmlError('Could not save like, DB_ERROR.'); }
	echo "<ok/>";

?>
