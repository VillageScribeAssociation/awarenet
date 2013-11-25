<?

//--------------------------------------------------------------------------------------------------
//|	menu for forums, no arguments
//--------------------------------------------------------------------------------------------------

function newsletter_menu($args) {
	global $theme;
	global $user;

	$labels = array();			//%	block variables [string]
	$html = '';					//%	return value [string]

	//----------------------------------------------------------------------------------------------
	//	check permission of current user to perform possible menu actions
	//----------------------------------------------------------------------------------------------
	$labels['subscribers'] = ''
	 . '[[:theme::submenu'
	 . '::label=Subscribers' 
	 . '::link=/newsletter/listsubscriptions/'
	 . ':]]';

	$labels['categories'] = ''
	 . '[[:theme::submenu'
	 . '::label=Categories' 
	 . '::link=/newsletter/listcategories/'
	 . ':]]';

	if ('admin' != $user->role) {
		$labels['subscribers'] = '';
		$labels['categories'] = '';
	}
	

	//----------------------------------------------------------------------------------------------
	//	make the block
	//----------------------------------------------------------------------------------------------
	$block = $theme->loadBlock('modules/newsletter/views/menu.block.php');
	$html = $theme->replaceLabels($labels, $block);
	return $html;	
}

//--------------------------------------------------------------------------------------------------

?>
