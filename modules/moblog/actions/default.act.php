<? 

//--------------------------------------------------------------------------------------------------
//*	default action for moblog module is to show all posts if no reference given
//--------------------------------------------------------------------------------------------------

	if ('' == $req->ref) { 
		//------------------------------------------------------------------------------------------
		// no post specified, list most recent x posts
		//------------------------------------------------------------------------------------------
		include $kapenta->installPath . 'modules/moblog/actions/moblog.act.php'; 

	} else { 
		//------------------------------------------------------------------------------------------
		// specific post has been specified, display along ith comments, etc
		//------------------------------------------------------------------------------------------
		include $kapenta->installPath . 'modules/moblog/actions/show.act.php'; 

	}

?>
