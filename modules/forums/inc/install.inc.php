<?

//--------------------------------------------------------------------------------------------------
//	installer for gallery module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/forums/models/forum.mod.php');
require_once($installPath . 'modules/forums/models/forumthread.mod.php');
require_once($installPath . 'modules/forums/models/forumreply.mod.php');

function install_forums_module() {
	global $installPath;
	global $user;

	if ($user->data['ofGroup'] != 'admin') { return false; }

	//----------------------------------------------------------------------------------------------
	//	main forums table
	//----------------------------------------------------------------------------------------------
	$model = new Forum();
	$report = $model->install();

	//----------------------------------------------------------------------------------------------
	//	threads table
	//----------------------------------------------------------------------------------------------
	$model = new ForumThread();
	$report .= $model->install();

	//----------------------------------------------------------------------------------------------
	//	replies table
	//----------------------------------------------------------------------------------------------
	$model = new ForumReply();
	$report .= $model->install();

	return $report;
}

?>
