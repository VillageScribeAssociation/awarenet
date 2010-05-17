<?

	require_once($installPath . 'modules/moblog/models/moblog.mod.php');
	require_once($installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a post to ones own blog, formatted for nav (300px wide)
//--------------------------------------------------------------------------------------------------

function moblog_newpostformnav($args) { return loadBlock('modules/moblog/views/newformnav.block.php'); }

//--------------------------------------------------------------------------------------------------

?>
