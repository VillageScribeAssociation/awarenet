<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	moblog submenu
//--------------------------------------------------------------------------------------------------

function moblog_menu($args) { 
	global $theme;
	global $kapenta;

	$html = '';							//%	return value [string]
	$atMySchool = '';					//% posts from user's school if not public [string]
	$myBlog = '';						//% user's blog if not public [string]

	//----------------------------------------------------------------------------------------------
	//	logged in users have a blog and a school
	//----------------------------------------------------------------------------------------------
	
	if ('public' != $kapenta->user->role) {
		$myBlog = ''
		 . "[[:theme::submenu::label=My Blog"
		 . "::link=/moblog/blog/" . $kapenta->user->alias
		 . "::alt=My Blog:]]";

		if ('' != $kapenta->user->school) {
			$atMySchool = ''
			 . "[[:theme::submenu"
			 . "::label=At MySchool::link=/moblog/school/" . $kapenta->user->school
			 . "::alt=Blog posts from my school:]]";
		}
	}

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/moblog/views/menu.block.php');

	$labels = array(
		'submenu_atMySchool' => $atMySchool,
		'submenu_myBlog' => $myBlog
	);

	$html = $theme->replaceLabels($labels, $block);
	return $html;
}

//--------------------------------------------------------------------------------------------------

?>
