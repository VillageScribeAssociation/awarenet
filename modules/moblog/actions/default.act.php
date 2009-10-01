<? 

//--------------------------------------------------------------------------------------------------
//	default action for moblog module
//--------------------------------------------------------------------------------------------------

	if ($request['ref'] == '') { 
		// no post specified, list most recent x posts
		include $installPath . 'modules/moblog/actions/moblog.act.php'; 
	} else { 
		// specific post has been specified, display along ith comments, etc
		include $installPath . 'modules/moblog/actions/show.act.php'; 
	}

?>
