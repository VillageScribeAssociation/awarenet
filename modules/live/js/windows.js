//==================================================================================================
//*	DESKTOP AND WINDOW MANAGER
//==================================================================================================
//+	note that init script in the page template should create the following objects,expected by
//+	klive.

//+		kwindowmanager - a Live_WindowManager
//+		kmouse - a Live_Mouse

//--------------------------------------------------------------------------------------------------
//	Window
//--------------------------------------------------------------------------------------------------
//+	expects to be initalized as global object kwindowmanager

function Live_WindowManager() {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	this.windows = new Array();

	this.pageWidth = -1;					//_ px, can constrain windows, eg for taskbar [int]
	this.pageHeight = -1;					//_ px, can constrain windows, eg for taskbar [int]

	if (window.innerWidth) {							
		this.pageWidth = window.innerWidth;							// available on Firefox, Chrome
		this.pageHeight = window.innerHeight;						// and Safari
	}

	if ((document.documentElement) && (document.documentElement.clientWidth)) {
		this.pageWidth = document.documentElement.clientWidth;		// available on IE and Opera
		this.pageHeight = document.documentElement.clientHeight;
	}

	if (-1 == this.pageWidth) { alert("Could not get viewport."); return; }

	//----------------------------------------------------------------------------------------------
	//	create a new window and add it to the array
	//----------------------------------------------------------------------------------------------
	//returns: window id on success, -1 on failure [int]

	this.createWindow = function (title, frameUrl, width, height, icon) {
		var icon = jsServerPath + 'modules/live/icons/document-new.png';	// default [string]
		wnd = new Live_Window(title, frameUrl, icon);
		wnd.hWnd = this.windows.length;

		if (width) { wnd.width = width; }
		if (height) { wnd.width = width; }
		if (icon) { wnd.icon = icon; }

		var theMsgDiv = document.getElementById('msgDiv');
		if (!theMsgDiv) { alert("Window container div not found."); return -1; }
		theMsgDiv.innerHTML = theMsgDiv.innerHTML + wnd.toHtml();
		
		//------------------------------------------------------------------------------------------
		//	choose a random position on the screen and bring to front
		//------------------------------------------------------------------------------------------
		divWindow = document.getElementById(wnd.UID);
		if (!divWindow) { alert("Could not access window div."); return -1; }
		divWindow.style.left = Math.floor(Math.random() * (this.pageWidth - wnd.width)) + 'px';
		divWindow.style.top = Math.floor(Math.random() * (this.pageHeight - wnd.height)) + 'px';
		divWindow.style.zIndex = wnd.zIndex + 3;

		hWnd = this.windows.length;
		this.windows[hWnd] = wnd;

		return hWnd;
	}

	//----------------------------------------------------------------------------------------------
	//	close a window given its UID
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	this.closeWindow = function(wUID) {
		var hWnd = this.getIndex(wUID);
		if (-1 == hWnd) { return false; }
		var check = this.windows[hWnd].close();
		if (true == check) { this.windows[hWnd].UID = ''; }		// clear the UID
		return check;
	}

	//----------------------------------------------------------------------------------------------
	//.	get the index of a window in the array given its UID
	//----------------------------------------------------------------------------------------------
	//arg: wUID - UID of a window [string]
	//returns: index of window, or -1 if not found [int]

	this.getIndex = function(wUID) {
		for (var idx in this.windows) { if (wUID == this.windows[idx].UID) { return idx; } }
		//logDebug('kwm.getIndex() - index not found for window UID ' + wUID);
	}

	//----------------------------------------------------------------------------------------------
	//.	find maximum z-index of all windows (ie, the one on 'top')
	//----------------------------------------------------------------------------------------------

	this.getMaxZIndex = function() {
		var max = 0;
		for (var i in this.windows) {
			if (this.windows[i].zIndex > max) { max = this.windows[i].zIndex; }
		}
		return max;
	}


}

//--------------------------------------------------------------------------------------------------
//	object to represent individual windows
//--------------------------------------------------------------------------------------------------

function Live_Window(title, frameUrl, icon) {

	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------
	this.UID = kutils.createUID();	//_	Unique ID of this window [string]
	this.top = 0;					//_ distance from top of document window, pixels [int]
	this.left = 0;					//_ distance from left of document window, pixels [int]
	this.width = 300;				//_	current width of window, pixels [int]
	this.height = 500;				//_	current height of window, pixels [int]
	this.minWidth = 200;			//_ pixels [int]
	this.minHeight = 200;			//_	pixels [int]

	this.title = title;				//_	window title [string]
	this.frameUrl = frameUrl;		//_	URL of content document [string]
	this.icon = icon;				//_	window title [string]
	this.hWnd = 0;					//_	index in livedesktop.windows array [int]
	this.zIndex = 0;				//_  [int]
	
	this.state = 'hidden';			//_	may be 'show', 'max', 'min', 'hide' [string]

	//TODOs
	this.hasStatusBar = false;		//_	not yet supported [bool]
	this.hasMenu = false;			//_ not yet supported [bool]

	//----------------------------------------------------------------------------------------------
	//	make HTML div of window frame
	//----------------------------------------------------------------------------------------------

	this.toHtml = function() {

		this.zIndex = kwindowmanager.getMaxZIndex() + 1;
		if (this.zIndex < 1) { this.zIndex = 1; }			// unnecessary?  remove?

		//------------------------------------------------------------------------------------------
		//	create and add html
		//------------------------------------------------------------------------------------------
	
		var menuHtml = '';				// TODO: replace this with a menu object
		//if ('fixedmenu' == type) {
		//	menuHtml = "<div class='menubar' id='menubar" + windowUid + "'>"
		//			 + "<ul class='menu'><!-- menuinsert --></ul></div>";
		//}

		var resUrl = jsServerPath + 'modules/live/images/';

		newHtml = "<div class='window' id='" + this.UID + "' style='z-index: -1; width: auto;'>\n"
			+ "<table noborder id='title" + this.UID + "' width='" + (this.width + 2) + "' "
			 + "cellpadding='0px' cellspacing='0px' class='window'>\n"
			+ "<tr height='24px'>\n" 
			+ "<td width='7px' background='" + resUrl + "titlebar-left.png'></td>\n"
			+ "<td class='titlebar' id='handle" + this.UID + "' "
			 + "background='" + resUrl + "titlebar-tile.png'>"
			 + "<img src='" + icon + "' width='14px' height='14px' class='titlebaricon' />"
			 + "&nbsp;<b><span id='txtTitle" + this.UID + "' class='titlebar'>"
			 + this.title + "</span></b></td>\n"
			+ "<td width='24px'>"
			 + "<img src='/gui/images/titlebar-close.png' id='wClose" + this.UID + "'"
			 + " style='cursor: pointer; cursor: hand;'"
			 + " onClick=\"kwindowmanager.closeWindow('" + this.UID + "');\""
			 + " />"
			 + "</td>\n"  
			+ "<td width='7px' background='" + resUrl + "titlebar-right.png'></td>\n"  
			+ "</tr>\n"
			+ "</table>\n"
			+ menuHtml
			+ "<iframe name='ifc" + this.UID + "' id='c" + this.UID + "' style='border: 1px solid' " 
			 + "src='" + this.frameUrl + "' "
			 + "height='" + (this.height - 24) + "' "
			 + "width='" + this.width + "' scrolling='no'>"
			+ "</iframe>"
			+ "<div id='status" + this.UID + "' class='statusbar' "
			 + "style='width: " + this.width + "px'>"
			+ "<span id='statusTxt" + this.UID + "' style='float: left;'></span>"
			+ "<img id='sresize" + this.UID + "' " 
			 + "src='" + resUrl + "status-resize.png' "
			 + "class='statusbarsize' />"
			+ "</div>"
			+ "</div>\n";
	
		return newHtml;	
	}

	//----------------------------------------------------------------------------------------------
	//	set window status
	//----------------------------------------------------------------------------------------------

	this.setStatus = function(txt) {
		//TODO: check here whether window has a status bar
		var spanStatus = document.getElementById('statusTxt' + this.UID);
		spanStatus.innerHTML = '&nbsp;' + txt;
	}

	//----------------------------------------------------------------------------------------------
	//	close/destroy this window
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	this.close = function() {
		//--------------------------------------------------------------------------------------
		//	remove the window div 
		//--------------------------------------------------------------------------------------
		var cwDiv = document.getElementById(this.UID);
		var divMsg = document.getElementById('msgDiv');
		cwDiv.innerHTML = '';
		divMsg.removeChild(cwDiv);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//	disable resize
	//----------------------------------------------------------------------------------------------

	this.disableResize = function() {
		var statusDiv = document.getElementById("status" + this.UID);
		statusDiv.innerHTML = "<span id='statusTxt" + this.UID + "' style='float: left;'></span>";
	}

}

//--------------------------------------------------------------------------------------------------
//	object to represent the mouse
//--------------------------------------------------------------------------------------------------
//+	initialize as kmouse

function Live_Mouse() {
	
	//----------------------------------------------------------------------------------------------
	//	member variables
	//----------------------------------------------------------------------------------------------

	this.dragMode = '';						//_ 'move', 'resize', 'drag' [string]
	this.dragElement;						//_ HTML entity (div) being dragged [object]
	this.dragIdx;							//_	index of window being dragged [int]

	// For dragging
	this.start = new Live_Point(0, 0);		//_ mouse position at start of drag [object_Point]
	this.offset = new Live_Point(0, 0);		//_	mouse offset relative to drag item [object]

	// For resizing
	this.startSize = new Live_Point(0, 0);		//_ initial size of window [object]
	this.ifStartSize = new Live_Point(0, 0);	//_ initial size of content iFrame [object]
	this.rsMin = new Live_Point(0, 0);			//_ minimum size of current window [object]

	this.titleResize;							//_ title table of a window [obejct]
	this.ifResize;								//_ iframe element of a window [object]
	this.statusResize;							//_ status bar element of a window [object]

	this.oldZIndex = 0;							//_ temporarily increased during drag [int]

	//----------------------------------------------------------------------------------------------
	//. handle mouse down event (attached at end of this object)
	//----------------------------------------------------------------------------------------------

	this.onMouseDown = function(e) {
		// IE is retarded and doesn't pass the event object 
		if (e == null) { e = window.event; }
		
		// IE uses srcElement, others use target  (note to self: neat)
		var target = e.target != null ? e.target : e.srcElement;

		//TODO: close any open menus here
		
		//------------------------------------------------------------------------------------------
		//	Determine which button was clicked
		//------------------------------------------------------------------------------------------
		// for IE, left click == 1  for Firefox, left click == 0 
		// en: if event.button = left and DOM object has class 'drag'
			
		if ( (e.button == 1 && window.event != null || e.button == 0) 
			  && (target.className == 'titlebar' || target.className == 'statusbarsize' )) { 

			//logDebug('onmousedown()::left click on active element');

			// get the window this handle belongs to and set drag mode
			windowUid = target.id + '';

			kmouse.dragMode = 'move';
			if ('statusbarsize' == target.className) { kmouse.dragMode = 'resize'; }

			windowUid = windowUid.replace('handle', '');
			windowUid = windowUid.replace('txtTitle', '');
			windowUid = windowUid.replace('sresize', '');
			divWindow = document.getElementById(windowUid);
			kmouse.dragIdx = kwindowmanager.getIndex(windowUid);
			//logDebug('onmousedown() - windowUid=' + windowUid);

			// get the mouse position 
			kmouse.start.x = e.clientX; 
			kmouse.start.y = e.clientY; 

			// get the clicked element's position 
			kmouse.offset.x = kmouse.extractNumber(divWindow.style.left); 
			kmouse.offset.y = kmouse.extractNumber(divWindow.style.top); 

			if ('resize' == kmouse.dragMode) {
				// get the clicked window's height and width
				kmouse.startSize.x = kmouse.extractNumber(divWindow.clientWidth); 
				kmouse.startSize.y = kmouse.extractNumber(divWindow.clientHeight); 

				// get the window iframe's size and width
				kmouse.titleResize = document.getElementById('title' + windowUid);
				kmouse.ifResize = document.getElementById('c' + windowUid);
				kmouse.statusResize = document.getElementById('status' + windowUid);

				kmouse.ifStartSize.x = kmouse.extractNumber(kmouse.ifResize.clientWidth);
				kmouse.ifStartSize.y = kmouse.extractNumber(kmouse.ifResize.clientHeight);  

				// get window min width/height
				var idxWindow = kwindowmanager.getIndex(windowUid);
				kmouse.rsMin.x = kwindowmanager.windows[idxWindow].minHeight;
				kmouse.rsMin.y = kwindowmanager.windows[idxWindow].minHeight;

			}
				
			// bring the clicked element to the front while it is being dragged 
			// (reconsider this, like maybe [bring to front] button)
			kmouse.oldZIndex = divWindow.style.zIndex; 
			divWindow.style.zIndex = 10000; 
				
			// we need to access the element in OnMouseMove
			kmouse.dragElement = divWindow; 
					
			// tell our code to start moving the element with the mouse 
			document.onmousemove = kmouse.onMouseMove; 
			
			// cancel out any text selections document.body.focus(); 
			// prevent text selection in IE 
			document.onselectstart = function () { return false; }; 
				
			// prevent text selection (except IE) 
			return false;
				
		} // end if
	}

	//--------------------------------------------------------------------------------------------------
	//.	utility method - clumsy
	//--------------------------------------------------------------------------------------------------

	this.extractNumber = function(value) { 
		var n = parseInt(value); 
		return n == null || isNaN(n) ? 0 : n; 
	}

	//----------------------------------------------------------------------------------------------
	//. handle mouse up event (attached at end of this object)
	//----------------------------------------------------------------------------------------------

	this.onMouseUp = function(e) {
		//logDebug('onmouseup()');
		if (kmouse.dragElement != null) { 

			//logDebug('onmouseup() - drag element not null');

			//--------------------------------------------------------------------------------------
			//	refresh stored Y position of windows
			//--------------------------------------------------------------------------------------

			var stLeft = Number(kmouse.dragElement.style.left.replace("px", ""));
			var stTop = Number(kmouse.dragElement.style.top.replace("px", "")) - 47;
			var stFromUID = kmouse.dragElement.id.replace('cw', '');

			//--------------------------------------------------------------------------------------
			//	if we are presently dragging something, let it go
			//--------------------------------------------------------------------------------------
				
			kmouse.dragElement.style.zIndex = kmouse.oldZIndex; // drop down to old Zindex
				
			// remove event handlers
			document.onmousemove = null;
			document.onselectstart = null; 
					
			// this is how we know we're not dragging 
			kmouse.dragElement = null; 
				
		} 
	}

	//----------------------------------------------------------------------------------------------
	//. handle mouse move event (attached at end of this object)
	//----------------------------------------------------------------------------------------------

	this.onMouseMove = function(e) {
		if (e == null) { var e = window.event; } 
		//------------------------------------------------------------------------------------------
		// this is the actual 'drag code'
		//------------------------------------------------------------------------------------------
		// ps: note use of style properties for absolute positioning, 'px' is important

		if (undefined == kmouse.dragElement) {	return true; }	//TODO: research this return value
		if (null == kmouse.dragElement) { return true; }		//TODO: research this return value

		var wnd = kwindowmanager.windows[kmouse.dragIdx];
		//logDebug('kmouse dragindex: ' + kmouse.dragIdx);

		//------------------------------------------------------------------------------------------
		//	move something
		//------------------------------------------------------------------------------------------

		if ('move' == kmouse.dragMode) {
			//logDebug('kmouse.onmousemove() dragMode = move');
			wnd.left = (kmouse.offset.x + e.clientX - kmouse.start.x);
			wnd.top = (kmouse.offset.y + e.clientY - kmouse.start.y);

			if (wnd.left < 0) { this.left = 0; }
			if (wnd.top < 0) { this.top = 0; }
			if ((wnd.left + kmouse.dragElement.clientWidth) > kwindowmanager.pageWidth) 
				{ wnd.left = (kwindowmanager.pageWidth - kmouse.dragElement.clientWidth); }
			if ((wnd.top + kmouse.dragElement.clientHeight) > kwindowmanager.pageHeight) 
				{ wnd.top = (kwindowmanager.pageHeight - kmouse.dragElement.clientHeight); }

			kmouse.dragElement.style.left = wnd.left + 'px';
			kmouse.dragElement.style.top = wnd.top + 'px'; 

			//logDebug('kmouse.onmousemove() km.dargElement (' + leftPos + ', ' + topPos + ')');

		}

		//-----------------------------------------------------------------------------------------
		//	resize a window
		//-----------------------------------------------------------------------------------------

		if ('resize' == kmouse.dragMode) {
			//logDebug('kmouse.onmousemove() dragMode = resize');
			kmouse.dragElement.style.width = (kmouse.startSize.x + (e.clientX - kmouse.start.x)) + 'px';
			kmouse.dragElement.style.height = (kmouse.startSize.y + (e.clientY - kmouse.start.y)) + 'px';

			var ifWidth = (kmouse.ifStartSize.x + (e.clientX - kmouse.start.x));
			var ifHeight = (kmouse.ifStartSize.y + (e.clientY - kmouse.start.y));
	
			if (ifWidth >= kmouse.rsMin.x) {
				kmouse.titleResize.style.width = ifWidth + 'px';
				kmouse.ifResize.style.width = ifWidth + 'px';
				kmouse.statusResize.style.width = ifWidth + 'px';
			}
			if (ifHeight >= kmouse.rsMin.y) { kmouse.ifResize.style.height = ifHeight + 'px'; }
		}

		//desktopSetTray( '(' + _dragElement.style.left + ', ' + _dragElement.style.top + ')' );

	}

	document.onmousedown = this.onMouseDown;
	document.onmouseup = this.onMouseUp;

}

//--------------------------------------------------------------------------------------------------
//	object to represent point in 2d
//--------------------------------------------------------------------------------------------------

function Live_Point(startX, startY) {
	this.x = startX;			//_	x position, pixels [int]
	this.y = startY;			//_	y position, pixels [int]
	this.toString = function() { return '(' + this.x + ', ' + this.y + ')'; }
}
