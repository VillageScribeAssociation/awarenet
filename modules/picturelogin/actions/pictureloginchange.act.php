<?

//--------------------------------------------------------------------------------------------------
//*	show icons, drag & drop into iconpassword fields, generate password field, password fields (user copies text) and change password 
//* button 
//--------------------------------------------------------------------------------------------------

	if (false == array_key_exists('action', $_POST)) { $page->do404('Action nto specified.'); }
	if ('Pictures' != $_POST['action']) { $page->do404('Action not supported.'); }
	if (false == array_key_exists('UID', $_POST)) { $page->do404('UID not given.'); }

	// users may only change their own password
	if (('admin' != $user->role) AND ($user->UID != $_POST['UID'])) { $page->do403(); }

	$style = '@charset "UTF-8";
 
		@font-face {
		  font-family: "untitled-font-1";
		  src:url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.eot");
		  src:url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.eot?#iefix") format("embedded-opentype"),
			url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.ttf") format("truetype"),
			url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.svg#untitled-font-1") format("svg"),
			url("/modules/picturelogin/assets/untitled-font-1/fonts/untitled-font-1.woff") format("woff");
		  font-weight: normal;
		  font-style: normal;
		}
 
		[data-icon]:before {
		  font-family: "untitled-font-1" !important;
		  content: attr(data-icon);
		  font-style: normal !important;
		  font-weight: normal !important;
		  font-variant: normal !important;
		  font-size: 230%;
		  text-transform: none !important;
		  speak: none;
		  line-height: 1;
		  -webkit-font-smoothing: antialiased;
		  -moz-osx-font-smoothing: grayscale;
		}
 
		[class^="icon-"]:before,
		[class*=" icon-"]:before {
		  font-family: "untitled-font-1" !important;
		  font-style: normal !important;
		  font-weight: normal !important;
		  font-variant: normal !important;
		  text-transform: none !important;
		  speak: none;
		  line-height: 1;
		  -webkit-font-smoothing: antialiased;
		  -moz-osx-font-smoothing: grayscale;
		} 

	#source
	{float:left; width:100%; height:126px; margin:1px;padding:1px;border:1px solid #aaaaaa;}

	#drop1
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop2
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop3
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop4
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop5
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop6
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop7
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop8
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop9
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop10
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop11
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop12
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop13
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop14
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop15
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop16
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop17
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop18
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop19
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
	#drop20
	{float:left; width:4%; height:32px; margin:1px;padding:1px;border:1px solid #aaaaaa;}
';

	$script = '<script>
		function allowDrop(ev)
		{
			var id = ev.target.id;
			if (-1 < id.search("drop") && 0 == ev.target.children.length) {
				ev.preventDefault();
 			}
		}

		function allowDropSource(ev)
		{
			ev.preventDefault();
		}

		function drag(ev)
		{
			ev.dataTransfer.setData("Text",ev.target.getAttribute("id"));
		}

		function drop(ev)
		{
			ev.preventDefault();
			var data=ev.dataTransfer.getData("Text");
			ev.target.appendChild(document.getElementById(data));
 		}

		function dropSource(ev)
		{
			ev.preventDefault();
			var data=ev.dataTransfer.getData("Text");
			document.getElementById("source").appendChild(document.getElementById(data));
 		}
	</script>	
	';
	
	//----------------------------------------------------------------------------------------------
	//	render the page
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/picturelogin/actions/pictureloginchange.page.php');
	$kapenta->page->blockArgs['head'] = '<style>' . $style . '</style>' . $script;
	$kapenta->page->blockArgs['username'] = $user->username;
	$kapenta->page->blockArgs['UID'] = $_POST['UID'];
	$kapenta->page->render();

?>
