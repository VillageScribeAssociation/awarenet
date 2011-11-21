<?

	require_once($kapenta->installPath . 'modules/moblog/models/post.mod.php');
	require_once($kapenta->installPath . 'modules/moblog/models/precache.mod.php');

//--------------------------------------------------------------------------------------------------
//|	moblog submenu
//--------------------------------------------------------------------------------------------------

function moblog_menu($args) { 
	global $theme;
	global $user;

	$html = '';							//%	return value [string]
	$atMySchool = '';					//% posts from user's school if not public [string]
	$myBlog = '';						//% user's blog if not public [string]

	//----------------------------------------------------------------------------------------------
	//	logged in users have a blog and a school
	//----------------------------------------------------------------------------------------------
	
	if ('public' != $user->role) {
		$myBlog = ''
		 . "[[:theme::submenu::label=My Blog"
		 . "::link=/moblog/blog/" . $user->alias
		 . "::alt=My Blog:]]";

		if ('' != $user->school) {
			$atMySchool = ''
			 . "[[:theme::submenu"
			 . "::label=At MySchool::link=/moblog/school/" . $user->school
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
