
//--------------------------------------------------------------------------------------------------
//	Javascript to initialize a generic client window
//--------------------------------------------------------------------------------------------------
//+	This object is instantiated inside the window content iFrame and handles communication with the
//+	window manager and related events (onLoad, onResize, onClose, etc).
//+
//+	It is assumed that this is instantiated in the client iframe as and object named 'kwnd'

function Live_GenericWindow() {
	
	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	this.hasParent = false;			//_	set to true if window has parent and window manager [bool]
	this.kwm = null;				//_	pointer to parent kwindowmanager [Live_WindowManager]
	this.UID = '';					//_	UID of this window [string]
	this.hWnd = -1;					//_	handle to this window in kwm (resource) [int]

	//----------------------------------------------------------------------------------------------
	//	initialize
	//----------------------------------------------------------------------------------------------

	this.UID = window.name.replace('ifc', '');							//	has a UID

	if (window.parent) {
		this.hasParent = true;											//	has a parent

		if (window.parent.kwindowmanager) {
			this.kwm = window.parent.kwindowmanager;					//	which has a kwm
			this.hWnd = this.kwm.getIndex(this.UID)

			//	clear any initial message in the status bar
			if (this.hWnd > 0) { this.kwm.windows[this.hWnd].setStatus(''); }
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	handle resizing of client window
	//----------------------------------------------------------------------------------------------
	//;	Note that this is intended to be replaced by the content

	this.onResize = function() {
		
	}

	//----------------------------------------------------------------------------------------------
	//.	handle onLoad event
	//----------------------------------------------------------------------------------------------
	//;	Note that this is intended to be replaced by the content

	this.onLoad = function() {
		this.onResize();
	}

	//----------------------------------------------------------------------------------------------
	//.	called by window manager when the 'close' button is pressed
	//----------------------------------------------------------------------------------------------
	//;	Note that the onClose event is run in the parent frame's context. (CHECKME)
	//arg: cbFn - callback function, return true to allow close event, false to cancel it.
	//returns: true on succes, false on failure [bool]

	this.setOnClose = function(cbFn) {
		if (-1 == this.hWnd) { return false; }		//	no parent window manager
		this.kwm.setOnClose(this.hWnd, cbFn);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	allow client content to close the window	
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	this.closeWindow = function() {
		if (-1 == this.hWnd) { return false; }		//	no parent window manager
		this.kwm.closeWindow(this.UID);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	set the window status
	//----------------------------------------------------------------------------------------------

	this.setStatus = function(newTxt) {
		if (-1 == this.hWnd) { return false; }		//	no parent window manager
		this.kwm.windows[this.hWnd].setStatus(newTxt);
	}

	//----------------------------------------------------------------------------------------------
	//.	set the window banner
	//----------------------------------------------------------------------------------------------
	//arg: newTxt - html / text string for the banner [string]

	this.setBanner = function(newTxt) {
		if (-1 == this.hWnd) { return false; }		//	no parent window manager
		this.kwm.windows[this.hWnd].setBanner(newTxt);
	}

	//----------------------------------------------------------------------------------------------
	//.	set of clear the throbber (only visible if banner is set)
	//----------------------------------------------------------------------------------------------
	//arg: onoff - true for on [bool]

	this.setThrobber = function(onoff) {
		if (-1 == this.hWnd) { return false; }		//	no parent window manager
		this.kwm.windows[this.hWnd].setThrobber(onoff);
	}

}
