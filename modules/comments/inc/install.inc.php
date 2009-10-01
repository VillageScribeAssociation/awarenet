<?

//--------------------------------------------------------------------------------------------------
//	installer for images module (creates table)
//--------------------------------------------------------------------------------------------------

require_once($installPath . 'modules/comments/models/comments.mod.php');

function install_comments_module() {
	global $installPath;
	global $user;
	if ($user->data['ofGroup'] != 'admin') { return false; }

	$model = new Comment();

	$report = '';
	$report .= $model->install();

	return $report;
}

?>
