<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	form to add a post to ones own blog, formatted for nav (300px wide)
//--------------------------------------------------------------------------------------------------

function moblog_newpostformnav($args) {
	global $theme;
 return $theme->loadBlock('modules/moblog/views/newformnav.block.php'); }

//--------------------------------------------------------------------------------------------------

?>