<?

//--------------------------------------------------------------------------------------------------
//*	object to maintain session data
//--------------------------------------------------------------------------------------------------
//+	Message icons may be:
//+		info - informational
//+		warn - warning
//+		success - smiley or somesuch

class KSession {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	var $UID;			//_ UID of current session [string]
	var $user;			//_ UID of current user [string]
	var $message;		//_ message to user, displayed on next page render [string]
	var $captcha;		//_ array of captcha solutions (UID => value) [array]
	var $msgBlock = '';	//_ block template of session messages [string]
	var $debug = false;	//_	set to true to enable live debug (call any page with /debug_on/)

	//----------------------------------------------------------------------------------------------
	//.	constructor
	//----------------------------------------------------------------------------------------------

	function KSession() {
		global $kapenta;

		session_start();

		$this->message = '';
		$this->user = 'public';
		$this->captcha = array();

		// sUID - a unique identifier for this session, used for debugging
		if (array_key_exists('sUID', $_SESSION)) { $this->UID = $_SESSION['sUID']; }
		else { $this->UID = $kapenta->createUID(); }

		// sUser - the user UID for the current session (UID=public if not logged in)
		if (array_key_exists('sUser', $_SESSION)) { $this->user = $_SESSION['sUser']; }

		// sMessage - this is used for passing information for the user between pages
		if (array_key_exists('sMessage', $_SESSION)) { $this->message = $_SESSION['sMessage']; }

		// sCaptcha - for storing the correct answers to captchas
		if (array_key_exists('sCaptcha', $_SESSION)) { $this->captcha = $_SESSION['sCaptcha']; }

		// sDebug - toggle debug mode
		if (array_key_exists('sDebug', $_SESSION)) { $this->debug = $_SESSION['sDebug']; }

	}

	//----------------------------------------------------------------------------------------------
	//.	save current state to $_SESSION superglobal
	//----------------------------------------------------------------------------------------------

	function store() {
		$_SESSION['sUID'] = $this->UID;
		$_SESSION['sUser'] = $this->user;
		$_SESSION['sMessage'] = $this->message;
		$_SESSION['sCaptcha'] = $this->captcha;
		$_SESSION['sDebug'] = $this->debug;
	}

	//----------------------------------------------------------------------------------------------
	//.	add a message to be displayed to user on next page view
	//----------------------------------------------------------------------------------------------
	//arg: message - message to user [string]
	//opt: icon - message icon [string]

	function msg($message, $icon = 'info') {
		global $theme;
		if ('' == $this->msgBlock) 
			{ $this->msgBlock = $theme->loadBlock('modules/home/views/sessionmsg.block.php'); }

		$labels = array('msg' => $message, 'icon' => $icon);
		$this->message .= $theme->replaceLabels($labels, $this->msgBlock);
	}

	//----------------------------------------------------------------------------------------------
	//.	add a message to be displayed to an admin on next page view
	//----------------------------------------------------------------------------------------------
	//arg: message - message to user [string]
	//opt: icon - message icon [string]

	function msgAdmin($message, $icon = 'info') {
		global $user;
		if ((true == isset($user)) && ('admin' != $user->role)) { return false; }
		$this->msg($message, $icon);
	}

}

?>
