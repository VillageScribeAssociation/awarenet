//==================================================================================================
//*	DESKTOP AND WINDOW MANAGER
//==================================================================================================
//+	note that init script in the page template should create the following objects,expected by
//+	klive.
//+
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

	this.pageWidth = $(document).width();		//_ px, can constrain windows, eg for taskbar [int]
	this.pageHeight = $(document).height();		//_ px, can constrain windows, eg for taskbar [int]

	//----------------------------------------------------------------------------------------------
	//	create a new window and add it to the array
	//----------------------------------------------------------------------------------------------
	//arg: title - window titlebar contents [string]
	//arg: frameUrl - location of window contents, relative to jsServerPath [string]
	//arg: width - initial width of the window (contents) in pixels [string]
	//arg: height - initial height of the window (contents) in pixels [string]
	//arg: modal - create modal [bool]
	//returns: window id on success, -1 on failure [int]

	this.createWindow = function (title, frameUrl, width, height, icon, modal) {
		//------------------------------------------------------------------------------------------
		//	create a new Live_Window object
		//------------------------------------------------------------------------------------------
		var icon = jsServerPath + 'modules/live/icons/document-new.png';	// default [string]
		wnd = new Live_Window(title, frameUrl, icon);
		wnd.hWnd = this.windows.length;

		if (isMobile) { width = $(window).width(); }

		if (width) { wnd.width = width; }
		if (height) { wnd.height = height; }
		if (icon) { wnd.icon = icon; }
		if (modal) { wnd.modal = modal; }

		//------------------------------------------------------------------------------------------
		//	render into the page
		//------------------------------------------------------------------------------------------
		var theMsgDiv = document.getElementById('msgDiv');
		if (!theMsgDiv) { alert("Window container div not found."); return -1; }

		if (true == wnd.modal) {
			var height = $("#jqBody").height();
			$("#divModal").height(height); 
			$("#divModal").css('zIndex', wnd.zIndex + 2); 
			$("#divModal").show();
		}

		$('#msgDiv').append(wnd.toHtml());
		
		//------------------------------------------------------------------------------------------
		//	choose a random position on the screen and bring to front
		//------------------------------------------------------------------------------------------
		divWindow = document.getElementById(wnd.UID);
		if (!divWindow) { alert("Could not access window div."); return -1; }

		if (true == wnd.modal) {
			//	center modal windows (TODO: add scrollheight)
			wnd.left = Math.floor(($(window).width() - wnd.width)  / 2);
			wnd.top = Math.floor(($(window).height() - wnd.height)  / 2);

			if (0 > wnd.top) { wnd.top = 10; }
			wnd.top = wnd.top + $(window).scrollTop();

			if (isMobile) { wnd.left = 0; }

			divWindow.style.left = wnd.left + 'px';
			divWindow.style.top = wnd.top + 'px';

		} else {
			//	scatter non-modal windows so they don't hide each other
			wnd.left = Math.floor(Math.random() * ($(window).width() - wnd.width));
			wnd.top = Math.floor(Math.random() * ($(window).height() - wnd.height));
			wnd.top = wnd.top + $(document).scrollTop();

			if (isMobile) { wnd.left = 0; }

			divWindow.style.left = wnd.left + 'px';
			divWindow.style.top = wnd.top + 'px';
		}

		divWindow.style.zIndex = wnd.zIndex + 3;

		this.windows[wnd.hWnd] = wnd;
		this.setFocus(wnd.UID);
		return wnd.hWnd;
	}

	//----------------------------------------------------------------------------------------------
	//	close a window given its UID
	//----------------------------------------------------------------------------------------------
	//arg: wUID - UID of a window [string]
	//returns: true on success, false on failure [bool]

	this.closeWindow = function(wUID) {
		var hWnd = this.getIndex(wUID);
		if (-1 == hWnd) { return false; }
		var check = this.windows[hWnd].close();
		if (true == check) { this.windows[hWnd].UID = ''; }		// clear the UID
		$("#divModal").hide();
		return check;
	}

	//----------------------------------------------------------------------------------------------
	//.	set focus to a window given its UID
	//----------------------------------------------------------------------------------------------
	//arg: wUID - UID of a window [string]
	//returns: true on success, false on failure [bool]

	this.setFocus = function(wUID) {
		var hWnd = this.getIndex(wUID);
		if (-1 == hWnd) { return false; }

		//	increment to highest Z index
		this.windows[hWnd].setZIndex(this.getMaxZIndex() + 5);

		//	cover all other windows
		for (var i = 0; i < this.windows.length; i++) { this.windows[i].setCover(true); }
		this.windows[hWnd].setCover(false);

		//alert('set focus to: ' + hWnd)

		//	remove gaps in list of indexes
		//	TODO

		return true;
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

	//----------------------------------------------------------------------------------------------
	//.	set the onClose event handler for a window
	//----------------------------------------------------------------------------------------------

	this.setOnClose = function(hWnd, cbFn) {
		this.windows[hWnd].onClose = cbFn;
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
	this.modal = false;				//_	lock background page and windows? [bool]

	this.title = title;				//_	window title [string]
	this.frameUrl = frameUrl;		//_	URL of content document [string]
	this.icon = icon;				//_	window title [string]
	this.hWnd = 0;					//_	index in livedesktop.windows array [int]
	this.zIndex = 0;				//_ window focus / layer order [int]
	
	this.state = 'hidden';			//_	may be 'show', 'max', 'min', 'hide' [string]

	//TODOs
	this.hasStatusBar = false;		//_	not yet supported [bool]
	this.hasMenu = false;			//_ not yet supported [bool]
	this.menuType = 'banner';		//_	transitional

	//----------------------------------------------------------------------------------------------
	//	adjust for mobile devices
	//----------------------------------------------------------------------------------------------

	if (isMobile) {
		this.width = 320;
		this.height = 400;
	}

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
		if ('fixedmenu' == this.menuType) {
			menuHtml = ''
			 + "<div class='menubar' id='menubar" + this.UID + "'>"
			 + "<ul class='menu'><!-- menuinsert --></ul>"
			 + "</div>";

		} else {
			menuHtml = "<div id='divMenuBanner" + this.UID + "' c" + "lass='windowbanner'></div>";
		}

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
			 + "<img"
			 + " id='wClose" + this.UID + "'"
			 + " src='" + resUrl + "titlebar-close.png'"
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
			+ "<img"
			 + " id='sresize" + this.UID + "'" 
			 + " src='" + resUrl + "status-resize.png'"
			 + " class='statusbarsize' />"
			+ "</div>"
			+ "</div>"
			+ ""
			+ "<!-- cover div is separate from window -->"
			+ "<div"
			 + " id='divCover" + this.UID + "'"
			 + " class='windowoverlay'"
			 + " style='position: absolute; display: none;'"
			 + " onClick=\"kwindowmanager.setFocus('" + this.UID + "');\">"
			+ "</div>"
			+ "\n";
	
		return newHtml;
	}

	//----------------------------------------------------------------------------------------------
	//.	event triggered before a window is closed
	//----------------------------------------------------------------------------------------------
	//;	if this is repaced with something which returns false the close() call will be cancelled.

	this.onClose = function() { return true; }

	//----------------------------------------------------------------------------------------------
	//.	close/destroy this window
	//----------------------------------------------------------------------------------------------
	//returns: true on success, false on failure [bool]

	this.close = function() {
		//------------------------------------------------------------------------------------------
		//	allow event handler to interrupt
		//------------------------------------------------------------------------------------------
		if (false == this.onClose()) { return false; }

		//------------------------------------------------------------------------------------------
		//	remove the window div 
		//------------------------------------------------------------------------------------------
		var cwDiv = document.getElementById(this.UID);
		var cvDiv = document.getElementById('divCover' + this.UID);
		var divMsg = document.getElementById('msgDiv');
		cwDiv.innerHTML = '';
		divMsg.removeChild(cwDiv);
		divMsg.removeChild(cvDiv);
		return true;
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
	//	set z-index
	//----------------------------------------------------------------------------------------------
	//arg: newIndex - new Z Index [int]

	this.setZIndex = function(newIndex) {
		var mainDiv = document.getElementById(this.UID);
		mainDiv.style.zIndex = newIndex;
		this.zIndex = newIndex;
		$('#divCover' + this.UID).css('z-index', this.zIndex + 1);
		//this.setStatus("New Zindex: " + newIndex);
	}

	//----------------------------------------------------------------------------------------------
	//	disable resize
	//----------------------------------------------------------------------------------------------

	this.disableResize = function() {
		var statusDiv = document.getElementById("status" + this.UID);
		statusDiv.innerHTML = "<span id='statusTxt" + this.UID + "' style='float: left;'></span>";
	}

	//----------------------------------------------------------------------------------------------
	//.	toggle the cover div on and off
	//----------------------------------------------------------------------------------------------
	//;	This is used to prevent mouse events falling in to the iframe.
	//arg: onoff - toggle the cover on or off, true is on [bool]

	this.setCover = function(onoff) {
		if (true == onoff) {
			var ifOffset = $('#c' + this.UID).offset();
			if (ifOffset) {
				$('#divCover' + this.UID).css('top', ifOffset.top);
				$('#divCover' + this.UID).css('left', ifOffset.left);
				$('#divCover' + this.UID).width($('#c' + this.UID).width());
				$('#divCover' + this.UID).height($('#c' + this.UID).height());
				$('#divCover' + this.UID).show();
			}
		}
		else {
			$('#divCover' + this.UID).hide();
		}
	}

	//----------------------------------------------------------------------------------------------
	//	set the menu banner
	//----------------------------------------------------------------------------------------------
	//;	Set tio empty string to hide
	//arg: newTxt - new txt / html content of the banner bar [string]

	this.setBanner = function(newTxt) {
		if ('' == newTxt) {
			$('#divMenuBanner' + this.UID).hide();
		} else {
			newTxt = ''
			 + '<b>&nbsp;' + newTxt + '</b>'
			 + "<img"
			 + " id='bthrob" + this.UID + "'"
			 + " src='" + jsServerPath + "themes/clockface/images/throbber-window.gif'"
			 + " style='float: right; margin-right: 12px; display: none;'>";

			$('#divMenuBanner' + this.UID).html(newTxt);
			$('#divMenuBanner' + this.UID).show();
		}
	}

	//----------------------------------------------------------------------------------------------
	//	show throbber in window banner
	//----------------------------------------------------------------------------------------------
	//arg: onoff - turn it on or off [bool]

	this.setThrobber = function(onoff) {
		if (true == onoff) {
			//alert('throbber on: ' + $('#bthrob' + this.UID).attr('src'));
			$('#bthrob' + this.UID).show();
		} else {
			//alert('throbber off');
			$('#bthrob' + this.UID).hide();
		}
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

		//	touch events on phones and tablets work differently
		if (e.touches) {
			e.clientX = e.touches[0].clientX;
			e.clientY = e.touches[0].clientY;
			e.srcElement = e.touches[0].target;
		}
		
		// IE uses srcElement, others use target  (note to self: neat)
		var target = e.target != null ? e.target : e.srcElement;

		//TODO: close any open menus here
		
		//------------------------------------------------------------------------------------------
		//	Determine which button was clicked
		//------------------------------------------------------------------------------------------
		// for IE, left click == 1  for Firefox, left click == 0 
		// en: if event.button = left and DOM object has class 'drag'
		
		//if ( (e.button == 1 && window.event != null || e.button == 0 || e.touches) 
		//	  && (target.className == 'titlebar' || target.className == 'statusbarsize' )) { 

		if (!(target.className)) { return; }

		if ((target.className == 'titlebar') || (target.className == 'statusbarsize' )) { 

			//alert(e.clientX + ', ' + e.clientY + ', ' + target.className);

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

			//	set focus to the window
			kwindowmanager.setFocus(windowUid);

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
			//divWindow.style.zIndex = kwindowmanager.window; 
				
			// we need to access the element in OnMouseMove
			kmouse.dragElement = divWindow; 
					
			// tell our code to start moving the element with the mouse 
			document.onmousemove = kmouse.onMouseMove;
			document.ontouchmove = kmouse.onMouseMove;
			
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
				
			//kmouse.dragElement.style.zIndex = kmouse.oldZIndex; // drop down to old Zindex
				
			// remove event handlers
			document.onmousemove = null;
			document.ontouchmovemove = null;
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

		if (e.touches) {
			e.clientX = e.touches[0].clientX;
			e.clientY = e.touches[0].clientY;
		}

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
	document.ontouchstart = this.onMouseDown;
	document.onmouseup = this.onMouseUp;
	document.ontouchend = this.onMouseUp;

}	

//--------------------------------------------------------------------------------------------------
//	object to represent point in 2d
//--------------------------------------------------------------------------------------------------

function Live_Point(startX, startY) {
	this.x = startX;			//_	x position, pixels [int]
	this.y = startY;			//_	y position, pixels [int]
	this.toString = function() { return '(' + this.x + ', ' + this.y + ')'; }
}
