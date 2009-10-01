//-------------------------------------------------------------------------------------------------
//	WINDOW MANAGER
//-------------------------------------------------------------------------------------------------
//
//	Windows are stored in an array, like so:
//	window[0] = '01234567890'					- UID/handle
//	window[1] = '/gui/app/editor/editor.html'	- start url
//	window[2] = 'Editor'						- title
//	window[3] = '/gui/app/editor/icon.png'		- icon
//	window[4] = 200								- min width
//	window[5] = 100								- min height
//	window[6] = 200								- width
//	window[7] = 300								- height
//	window[8] = 'fixed'							- type; fixed/resize/
//	window[9] = 'show'							- state; show/minimized/maximized
//	window[10] = 2								- z-index
//

//-------------------------------------------------------------------------------------------------
//	global vars
//-------------------------------------------------------------------------------------------------

var windows = Array();
var focusWindow = Array();

//-------------------------------------------------------------------------------------------------
//	create a new window
//-------------------------------------------------------------------------------------------------

function windowCreate(url, title, icon, minWidth, minHeight, width, height, type) {
	//---------------------------------------------------------------------------------------------
	//	create new window object and add to array
	//---------------------------------------------------------------------------------------------

	windowUid = createUID();
	intZIndex = desktopGetMaxZIndex() + 1;
	if (intZIndex < 1) { intZIndex = 1; }

	var newWindow = Array();
	newWindow[0] = windowUid;
	newWindow[1] = url;
	newWindow[2] = title;
	newWindow[3] = icon;
	newWindow[4] = minWidth;
	newWindow[5] = minHeight;
	newWindow[6] = width;
	newWindow[7] = height;
	newWindow[8] = type;
	newWindow[9] = 'show';
	newWindow[10] = intZIndex;

	windows[windows.length] = newWindow;

	//---------------------------------------------------------------------------------------------
	//	create and add html
	//---------------------------------------------------------------------------------------------
	
	var menuHtml = '';
	if ('fixedmenu' == type) {
		menuHtml = "<div class='menubar' id='menubar" + windowUid + "'>"
				 + "<ul class='menu'><!-- menuinsert --></ul></div>";
	}

	newHtml = "<div class='window' id='" + windowUid + "' style='z-index: -1;'>\n"
			+ "<table noborder width='100%' cellpadding='0px' cellspacing='0px' class='window'>\n"
			+ "<tr height='24px'>\n" 
			+ "<td width='7px' background='/gui/images/titlebar-left.png'></td>\n"
			+ "<td class='titlebar' id='handle" + windowUid + "' "
			 + "background='/gui/images/titlebar-tile.png'>"
			 + "<img src='" + icon + "' width='14px' height='14px' class='titlebaricon' />"
			 + "&nbsp;<b><span id='txtTitle" + windowUid + "' class='titlebar'>"
			 + title + "</span></b></td>\n"  
			+ "<td width='24px' background='/gui/images/titlebar-minimize.png' "
			 + "onClick='windowMinimize(" + "\"" + windowUid + "\"" + ");'></td>\n"  
			+ "<td width='24px' background='/gui/images/titlebar-close.png' onClick='windowClose(\"" + windowUid + "\")'></td>\n"  
			+ "<td width='7px' background='/gui/images/titlebar-right.png'></td>\n"  
			+ "</tr>\n"
			+ "</table>\n"
			+ menuHtml
			+ "<iframe name='ifc" + windowUid + "' id='c" + windowUid + "' style='border: 1px solid'" 
			+ " src='" + url + "' height='" + (height - 24) + "' width='" + width + "'></iframe>"
			+ "<div id='status" + windowUid + "' class='statusbar'>"
			+ "<img id='sresize" + windowUid + "' src='/gui/images/status-resize.png' class='statusbarsize' />"
			+ "</div>"
			+ "</div>\n";

	divWindows = document.getElementById('windowlist');
	divWindows.innerHTML = divWindows.innerHTML + newHtml;

	//---------------------------------------------------------------------------------------------
	//	choose a random position on the screen and bring to front
	//---------------------------------------------------------------------------------------------

	divWindow = document.getElementById(windowUid);
	divWindow.style.left = (Math.random() * (desktopWidth - width)) + 'px';
	divWindow.style.top = (Math.random() * (desktopHeight - height)) + 'px';
	divWindow.style.zIndex = intZIndex;

	//---------------------------------------------------------------------------------------------
	//	redraw the taskbar
	//---------------------------------------------------------------------------------------------
	desktopRefreshTaskbar()
	
	return windowUid;
}

//-------------------------------------------------------------------------------------------------
//	get window array index given UID
//-------------------------------------------------------------------------------------------------

function windowGetIndex(windowUid) {
	for (i = 0; i < windows.length; i++) {
		if (windows[i][0] == windowUid) { return i; }
	}
	return -1;
}

//-------------------------------------------------------------------------------------------------
//	send a message to a window
//-------------------------------------------------------------------------------------------------

function windowSendMsg(windowUid, msg) {
	if ('desktop' == windowUid) {							// desktop has a magic windowUid
		return procMsg(msg);
	} else {
		var ifc = document.getElementById('c' + windowUid);
		if (null == ifc) { return false; }
		return ifc.contentWindow.procMsg(msg);				// send to the window
	}
}

//-------------------------------------------------------------------------------------------------
//	set foreground window / bring a window to front
//-------------------------------------------------------------------------------------------------

function windowBringToFront(windowUid) {
	var winIdx = windowGetIndex(windowUid);	
	var maxZIndex = desktopGetMaxZIndex();

	//	get element and bump zIndex
	var divWindow = document.getElementById(windowUid);
	divWindow.style.zIndex = (maxZIndex + 1);

	//	set zIndex in array
	windows[winIdx][10] = (maxZIndex + 1);

	// tidy zIndex of all Windows
	windowTidyZIndex();	
}

//-------------------------------------------------------------------------------------------------
//	tidy the set of window z-indices
//-------------------------------------------------------------------------------------------------

function windowTidyZIndex() {
	var currZIndex = 1;
	
	while (currZIndex != false) {
		winIdx = windowGetNextZIndex(currZIndex);
		if (false == winIdx) { return; }	// no more to sort
		currZIndex++;

		windows[winIdx][10] = currZIndex;
		divCurr = document.getElementById(windows[winIdx][0]);
		divCurr.style.zIndex = currZIndex;
	}
}

//-------------------------------------------------------------------------------------------------
//	find the index of the window with the next greatest zIndex
//-------------------------------------------------------------------------------------------------

function windowGetNextZIndex(currZIndex) {
	var nextZIndex = 1024;
	var winIdx = false;
	for (i = 0; i < windows.length; i++) {
		if ((windows[i][10] > currZIndex) && (windows[1][10] < nextZIndex)) {
			nextZIndex = windows[i][10];
			winIdx = i;
		}
	}
	return winIdx;
}

//-------------------------------------------------------------------------------------------------
//	restore a window
//-------------------------------------------------------------------------------------------------

function windowRestore(windowUid) {
	intZIndex = desktopGetMaxZIndex() + 1;
	if (intZIndex < 1) { intZIndex = 1; }
	intWinIdx = windowGetIndex(windowUid);
	if (-1 == intWinIdx) { return false; }
	windows[intWinIdx][9] = 'show';
	windows[intWinIdx][10] = intZIndex;
	divWindow = document.getElementById(windowUid);
	divWindow.style.zIndex = intZIndex;
	windowBringToFront(windowUid);
	desktopRefreshTaskbar();
}

//-------------------------------------------------------------------------------------------------
//	minimize a window
//-------------------------------------------------------------------------------------------------

function windowMinimize(windowUid) {
	//---------------------------------------------------------------------------------------------
	//	find this window in array
	//---------------------------------------------------------------------------------------------
	aryWindow = false;
	idxWindow = 0;
	for (i = 0; i < windows.length; i++) { 
		if (windows[i][0] == windowUid) { 
			aryWindow = windows[i]; 	// window details
			idxWindow = i;				// position in array
		}	
	}

	if (false == aryWindow) { return false; }

	//---------------------------------------------------------------------------------------------
	//	set z-index to -1 (below desktop)
	//---------------------------------------------------------------------------------------------
	divWindow = document.getElementById(windowUid);
	divWindow.style.zIndex = -1;
	windows[idxWindow][10] = -1;			// record z-index in window data
	windows[idxWindow][9] = 'minimized'		// set state in window data

	//---------------------------------------------------------------------------------------------
	//	decrement the z-index of all windows above this one (if any)
	//---------------------------------------------------------------------------------------------
	intZIndex = aryWindow[10];
	for (i = 0; i < windows.length; i++) {
		if (windows[i][10] > intZIndex) { windows[i][10] = windows[i][10] - 1; }
	}

	//---------------------------------------------------------------------------------------------
	//	update the taskbar
	//---------------------------------------------------------------------------------------------
	desktopRefreshTaskbar();
}

//-------------------------------------------------------------------------------------------------
//	close a window
//-------------------------------------------------------------------------------------------------

function windowClose(windowUid) {
	//---------------------------------------------------------------------------------------------
	//	remove from windows array
	//---------------------------------------------------------------------------------------------
	var newWindows = Array();
	var found = false;
	for (i = 0; i < windows.length; i++) {
		if (windows[i][0] == windowUid) {
			found = true;
		} else {
			if (true == found) {
				newWindows[i - 1] = windows[i];
			} else {
				newWindows[i] = windows[i];
			}
		}
	}

	if (false == found) { return false; }	// no such window UID
	windows = newWindows;

	//---------------------------------------------------------------------------------------------
	//	remove window div element from DOM
	//---------------------------------------------------------------------------------------------
	var child = document.getElementById(windowUid);
	var parent = document.getElementById('windowlist');
	parent.removeChild(child);

	//---------------------------------------------------------------------------------------------
	//	refresh the taskbar
	//---------------------------------------------------------------------------------------------
	desktopRefreshTaskbar();
}

//-------------------------------------------------------------------------------------------------
//	clear a window's menu
//-------------------------------------------------------------------------------------------------

function windowClearMenu(windowUid) {
	var menuDiv = document.getElementById('menubar' + windowUid);
	if (null == menuDiv) { return false; }
	menuDiv.innerHTML = "<ul class='menu'><!-- menuinsert --></ul>";
	return true;
}

//-------------------------------------------------------------------------------------------------
//	add a menu to a window
//-------------------------------------------------------------------------------------------------

function windowAddMenuItem(windowUid, menuCaption, menuProcMsg, menuId) {
	//---------------------------------------------------------------------------------------------
	//	get the menubar div
	//---------------------------------------------------------------------------------------------
	var divMenubar = document.getElementById('menubar' + windowUid);
	if (null == divMenubar) { alert('no such menu bar'); return false; }

	var menuId = 'menuitem' + windowUid + menuId;
	var onClick = "windowSendMsg('" + windowUid + "', '" + menuProcMsg + "')";
	var menuItem = "<li class='menuitem' id='" + menuId + "' onClick=\"" + onClick + "\">"
			 + menuCaption + "&nbsp;</li><!-- menuinsert -->";

	divMenubar.innerHTML = divMenubar.innerHTML.replace(/<!-- menuinsert -->/, menuItem)
	divMenubar.innerHTML;
}

//-------------------------------------------------------------------------------------------------
//	create and show a menu
//-------------------------------------------------------------------------------------------------

function windowShowMenu(windowUid, menuId, menuItems) {
	var liMenuItem = document.getElementById('menuitem' + windowUid + menuId);
	if (null == liMenuItem) { return false; }

	//---------------------------------------------------------------------------------------------
	//	make menu html
	//---------------------------------------------------------------------------------------------
	menuHtml = desktopMakeMenuHtml(windowUid, menuId, menuItems);

	//---------------------------------------------------------------------------------------------
	//	create menu div
	//---------------------------------------------------------------------------------------------

	var menuUid = desktopShowMenu(liMenuItem, menuHtml, 'below');
	return menuUid;
}

//-------------------------------------------------------------------------------------------------
//	a menu item has been clicked, get 
//-------------------------------------------------------------------------------------------------

function windowProcMenuClick(html) {
	html = html.substring((html.indexOf('<!-- ') + 5), html.indexOf(' -->'));
	var pipedelim = html.indexOf('||');
	windowSendMsg(html.substring(0, pipedelim), html.substring(pipedelim + 2, html.length));
}
