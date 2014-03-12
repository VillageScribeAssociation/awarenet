<?

	require_once($kapenta->installPath . 'modules/users/models/user.mod.php');

//--------------------------------------------------------------------------------------------------
//*	creates a chat iframe
//--------------------------------------------------------------------------------------------------

	//----------------------------------------------------------------------------------------------
	//	check reference and permissions
	//----------------------------------------------------------------------------------------------
	if ('public' == $kapenta->user->role) { $kapenta->page->do403('Please log in to use the chat.', true); }	

	if ('' == $kapenta->request->ref) { $kapenta->page->do404('User not specified.', true); }
	$model = new Users_User($kapenta->request->ref);
	if (false == $model->loaded) { $kapenta->page->do404('User not found.', true); }

	//----------------------------------------------------------------------------------------------
	//	render the page  
	//----------------------------------------------------------------------------------------------
	$kapenta->page->load('modules/live/actions/chatwindow.page.php');
	$kapenta->page->blockArgs['chatUID'] = 'experimental';
	$kapenta->page->render();


/*

	//----------------------------------------------------------------------------------------------
	//	render the page  //TODO: make a generic window template
	//----------------------------------------------------------------------------------------------

	$raw = "<!DOCTYPE html PUBLIC '-//W3C//DTD XHTML 1.0 Transitional//EN' 'http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd'>
<head>
<title>:: kapenta :: live :: test getblock</title>
<meta http-equiv='Content-Type' content='text/html; charset=utf-8' />
<link href='" . $kapenta->serverPath . "themes/%%defaultTheme%%/css/windows.css' rel='stylesheet' type='text/css' />
<script src='" . $kapenta->serverPath . "core/utils.js'></script>
<script src='" . $kapenta->serverPath . "modules/live/js/chatwindow.js'></script>
<style type='text/css'>

body {
	padding: 0;
	margin: 0;
	background: #eeeeee;
	font-family: Verdana, Arial, Helvetica, sans-serif;
	font-size: smaller;
	color: #303030;
}

a { text-decoration: none; color: #365a10 }

a h1 {
	color: #303030;
}

a h2 {
	color: #303030;
}

.style1 {font-size: 9px}
</style>

<script src='" . $kapenta->serverPath . "core/utils.js'></script>
<script language='javascript'>

	jsServerPath = '" . $kapenta->serverPath . "';
	jsUserUID = '" . $kapenta->user->UID . "';
	jsUserName = \"" . $kapenta->user->getName() . "\";
	jsPartnerUID = '" . $model->UID . "';
	jsPartnerName = \"" . $model->getName() . "\";
	
	windowUID = '';
	windowIdx = 0;

	kchatwindow = 0;

	//----------------------------------------------------------------------------------------------
	//	onLoad
	//----------------------------------------------------------------------------------------------

	function kPageInit() {
		//------------------------------------------------------------------------------------------
		//	create chat window object and register this window with chat server
		//------------------------------------------------------------------------------------------
		windowUID = window.name.replace('ifc', '');
		windowIdx = window.parent.kwindowmanager.getIndex(windowUID)

		kchatwindow = new Live_ChatWindow(
			jsServerPath, 
			'divMessages', 
			jsUserUID, 
			jsUserName, 
			jsPartnerUID, 
			jsPartnerName,
			windowIdx
		);

		window.parent.kchatclient.registerLoaded(windowIdx);

		//------------------------------------------------------------------------------------------
		//	register events (TODO: should ony be registered during drag)
		//------------------------------------------------------------------------------------------
		document.onmousemove = function(e) {
			var wndThis = window.parent.kwindowmanager.windows[windowIdx];
			var ifThis = window.parent.document.getElementsByName(window.name);	//%	this iframe
			var txtBox = document.getElementById('content');					//% textarea

			docX = e.clientX + ifThis[0].offsetLeft + wndThis.left;
			docY = e.clientY + ifThis[0].offsetTop + wndThis.top;

			// only these properties neede by the window manager
			var newEvt = function() { 
				this.clientX = 0;	
				this.clientY = 0; 
			}

			newEvt.clientX = docX;
			newEvt.clientY = docY;

			//txtBox.value = (docX) + ', ' + (docY) + ' -- ' + wndThis.top + ', ' + wndThis.left;
			window.parent.kmouse.onMouseMove(newEvt);
		}

		//------------------------------------------------------------------------------------------
		//	resize controls
		//------------------------------------------------------------------------------------------
		resizeWindow();
	}

	//----------------------------------------------------------------------------------------------
	//	re/scale controls to fit inside iFrame
	//----------------------------------------------------------------------------------------------

	function resizeWindow() {
		//------------------------------------------------------------------------------------------
		//	resize controls
		//------------------------------------------------------------------------------------------
		var divCPS = document.getElementById('divChatPartnerSummary');		//%	div
		var divM = document.getElementById('divMessages');					//%	div
		var txtBox = document.getElementById('content');					//% textarea
		
		txtBox.cHeight = extractNumberCW(txtBox.style.height)
		txtBox.style.top = (window.innerHeight - txtBox.cHeight - 8) + 'px';
		txtBox.style.width = (window.innerWidth - 6) + 'px';

		divM.style.height = (window.innerHeight - divCPS.clientHeight - txtBox.cHeight - 25) + 'px';
		divM.style.width = (window.innerWidth - 6) + 'px';
	}

	//----------------------------------------------------------------------------------------------
	//	remove 'px' from numbers //TODO: add this to /core/utils.js
	//----------------------------------------------------------------------------------------------
	
	function extractNumberCW(value) { 
		var n = parseInt(value); 
		return n == null || isNaN(n) ? 0 : n; 
	} 

	//----------------------------------------------------------------------------------------------
	//	remove 'px' from numbers //TODO: add this to /core/utils.js
	//----------------------------------------------------------------------------------------------

	function windowTxtChange() {
		var txtBox = document.getElementById('content');
		if ((txtBox.value.indexOf(\"\\n\") != -1) || (txtBox.value.indexOf(\"\\r\") != -1)) {
			kchatwindow.sendMessage(txtBox.value);
			txtBox.value = '';
		}
	}

</script>

</head>

<body onLoad='kPageInit();' onResize='resizeWindow();'> 
<div id='divChatPartnerSummary' style='background-color: #ffffff;'>
[[:users::summarynav::userUID=" . $model->UID . ":]]
</div>
<hr/>
<div id='divMessages' 
	style='width: 200px; height: 60px; position: absolute; overflow: auto;' ></div>

<textarea name='content' id='content'
	style='width: 200px; height: 60px; left: 0px; top: 100px; position:absolute;'
	onkeyup=\"windowTxtChange()\"> </textarea><br/>
</body>
</html>";

	$raw = $theme->expandBlocks($raw, '');
	echo $raw;

*/

?>
