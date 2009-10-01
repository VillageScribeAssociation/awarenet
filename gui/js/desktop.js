
//-------------------------------------------------------------------------------------------------
//	globals
//-------------------------------------------------------------------------------------------------

var desktopWidth = 100;
var desktopHeight = 100;

var _startX = 0; 			// mouse starting positions 
var _startY = 0;		
var _offsetX = 0; 			// current element offset 
var _offsetY = 0; 
var _dragElement; 			// needs to be passed from OnMouseDown to OnMouseMove 
var _oldZIndex = 0; 		// we temporarily increase the z-index during drag 

var _dragmode = 0;			// are we moving something or resizing it
var _startwidth = 0;
var _startheight = 0;
var _ifstartwidth = 0;
var _ifstartheight = 0;
var _ifResize;
var _resizeminwidth = 0;
var _resizeminheight = 0;

//-------------------------------------------------------------------------------------------------
//	initialise desktop
//-------------------------------------------------------------------------------------------------

function desktopInit() {
	//---------------------------------------------------------------------------------------------
	//	set event handlers
	//---------------------------------------------------------------------------------------------
	document.onmousedown = desktopOnMouseDown; 
	document.onmouseup = desktopOnMouseUp; 

	//---------------------------------------------------------------------------------------------
	//	create the desktop
	//---------------------------------------------------------------------------------------------
	theDiv = document.getElementById('desktop');
	theDiv.innerHTML = "<img id='imgWallpaper' class='wallpaper' src='images/wallpaper.jpg'>\n"
					 + "<div class='taskbar' id='taskbar'>\n"
					 + "<table noborder class='taskbar' width='100%'>\n"
					 + "  <tr>\n"
					 + "    <td class='taskbar' width='50'>\n"
					 + "    <img src='images/start.jpg' id='307button' onClick='desktopShow307Menu();'>\n"
					 + "    </td>\n"
					 + "    <td class='taskbar'>\n"
					 + "    <div class='tasks' id='tasks'></div>\n"
					 + "    </td>\n"
					 + "    <td class='taskbar' width='75'>\n"
					 + "    <div id='systray'><b>10:00</b></div>\n"
					 + "    </td>\n"
					 + "  </tr>\n"
					 + "</table>\n"
					 + "</div>\n";
	desktopResize();
	desktopUpdateClock();
}

//--------------------------------------------------------------------------------------------------
//	tick the clock
//--------------------------------------------------------------------------------------------------

function desktopUpdateClock() {
	var timeNow = new Date()
	var strHours = timeNow.getHours();		if (strHours < 10) { strHours = '0' + strHours; }
	var strMins = timeNow.getMinutes(); 	if (strMins < 10) { strMins = '0' + strMins; }
	var strSecs = timeNow.getSeconds();		if (strSecs < 10) { strSecs = '0' + strSecs; }

	timeStr = strHours + ':' + strMins + ':' + strSecs;
	desktopSetTray(timeStr);
	setTimeout('desktopUpdateClock();', 1000);
}

//--------------------------------------------------------------------------------------------------
//	resize desktop
//--------------------------------------------------------------------------------------------------

function desktopResize() {
	//-----------------------------------------------------------------------------------------
	// measure the taskbar
	//-----------------------------------------------------------------------------------------
	divTaskbar = document.getElementById('taskbar');
	divTaskbar.style.width = window.innerWidth;

	//-----------------------------------------------------------------------------------------
	// set the wallpaper size
	//-----------------------------------------------------------------------------------------
	theImg = document.getElementById('imgWallpaper');
	desktopWidth = window.innerWidth;
	desktopHeight = (window.innerHeight - divTaskbar.clientHeight);
	theImg.width = desktopWidth;
	theImg.height = desktopHeight;
}


//-------------------------------------------------------------------------------------------
//	onMouseMove - only fires when dragging something - hopefully
//-------------------------------------------------------------------------------------------

function desktopOnMouseMove(e) { 
	if (e == null) { var e = window.event; } 

	//---------------------------------------------------------------------------------------
	// this is the actual 'drag code'
	// ps: note use of style properties for absolute positioning, 'px' is important
	//---------------------------------------------------------------------------------------

	if (_dragElement == undefined) {
		// nothing to do
	} else {
	  if (_dragElement != null) {

		//-----------------------------------------------------------------------------------------
		//	move something
		//-----------------------------------------------------------------------------------------

		if ('move' == _dragmode) {

			leftPos = (_offsetX + e.clientX - _startX);
			topPos = (_offsetY + e.clientY - _startY);

			if (leftPos < 0) { leftPos = 0; }
			if (topPos < 0) { topPos = 0; }
			if ((leftPos + _dragElement.clientWidth) > desktopWidth) 
				{ leftPos = (desktopWidth - _dragElement.clientWidth); }
			if ((topPos + _dragElement.clientHeight) > desktopHeight) 
				{ topPos = (desktopHeight - _dragElement.clientHeight); }


			_dragElement.style.left = leftPos + 'px';
			_dragElement.style.top = topPos + 'px'; 
		}

		//-----------------------------------------------------------------------------------------
		//	resize a window
		//-----------------------------------------------------------------------------------------

		if ('resizewindow' == _dragmode) {
			_dragElement.style.width = (_startwidth + (e.clientX - _startX)) + 'px';
			_dragElement.style.height = (_startheight + (e.clientY - _startY)) + 'px';

			var ifWidth = (_ifstartwidth + (e.clientX - _startX));
			var ifHeight = (_ifstartheight + (e.clientY - _startY));

			if (ifWidth >= _resizeminwidth) { _ifResize.style.width = ifWidth + 'px'; }
			if (ifHeight >= _resizeminheight) { _ifResize.style.height = ifHeight + 'px'; }
		}

		//desktopSetTray( '(' + _dragElement.style.left + ', ' + _dragElement.style.top + ')' );
	  }
	}
		
}
	
//--------------------------------------------------------------------------------------------------
//	handle mousedown
//--------------------------------------------------------------------------------------------------

function desktopOnMouseDown(e) {

	// IE is retarded and doesn't pass the event object 
	if (e == null) { e = window.event; }
		
	// IE uses srcElement, others use target  (note to self: neat)
	var target = e.target != null ? e.target : e.srcElement;

	//----------------------------------------------------------------------------------------------
	//	clear open menus if anything else was clicked on
	//----------------------------------------------------------------------------------------------
	//alert(e.target.className);
	if ('menu' != e.target.className) {	desktopRemoveAllMenus(); }
	else { 
		windowProcMenuClick(e.target.innerHTML); 
	}
			
	//----------------------------------------------------------------------------------------------
	//	Determine which button was clicked
	//	for IE, left click == 1  for Firefox, left click == 0 
	//----------------------------------------------------------------------------------------------
			
	// en: if event.button = left and DOM object has class 'drag'
			
	if ( (e.button == 1 && window.event != null || e.button == 0) 
		  && (target.className == 'titlebar' || target.className == 'statusbarsize' )) { 

		// get the window this handle belongs to and set drag mode
		windowUid = target.id + '';

		_dragmode = 'move';
		if (windowUid.substring(0, 7) == 'sresize') {
			windowUid = windowUid.replace('sresize', '');
			_dragmode = 'resizewindow';
		}

		windowUid = windowUid.replace('handle', '');
		windowUid = windowUid.replace('txtTitle', '');
		divWindow = document.getElementById(windowUid);

		// get the mouse position 
		_startX = e.clientX; 
		_startY = e.clientY; 
				
		// get the clicked element's position 
		_offsetX = extractNumber(divWindow.style.left); 
		_offsetY = extractNumber(divWindow.style.top); 

		if ('resizewindow' == _dragmode) {
			// get the clicked window's height and width
			_startwidth = extractNumber(divWindow.clientWidth); 
			_startheight = extractNumber(divWindow.clientHeight); 

			// get the window iframe's size and width
			_ifResize = document.getElementById('c' + windowUid);
			_ifstartwidth = extractNumber(_ifResize.clientWidth);
			_ifstartheight = extractNumber(_ifResize.clientHeight);  

			// get window min width/height
			var idxWindow = windowGetIndex(windowUid);
			_resizeminwidth = windows[idxWindow][4];
			_resizeminheight = windows[idxWindow][5];

		}
				
		// bring the clicked element to the front while it is being dragged 
		// (reconsider this, like maybe [bring to front] button)
		_oldZIndex = divWindow.style.zIndex; 
		divWindow.style.zIndex = 10000; 
				
		// we need to access the element in OnMouseMove
		_dragElement = divWindow; 
				
		// tell our code to start moving the element with the mouse 
		document.onmousemove = desktopOnMouseMove; 
		
		// cancel out any text selections document.body.focus(); 
		// prevent text selection in IE 
		document.onselectstart = function () { return false; }; 
				
		// prevent text selection (except IE) 
		return false; 
				
	} // end if
} // end desktopOnMouseDown

//--------------------------------------------------------------------------------------------------
//	handle mouseup
//--------------------------------------------------------------------------------------------------

function desktopOnMouseUp(e) {
	if (_dragElement != null) { 

		//------------------------------------------------------------------------------------------
		//	refresh stored Y position of windows
		//------------------------------------------------------------------------------------------

		var stLeft = Number(_dragElement.style.left.replace("px", ""));
		var stTop = Number(_dragElement.style.top.replace("px", "")) - 47;
		var stFromUID = _dragElement.id.replace('cw', '');
		//alert("offsets: " + stLeft + ',' + (stTop + contentHeight) + "id: " + stFromUID);

		//------------------------------------------------------------------------------------------
		//	if we are presently dragging something, let it go
		//------------------------------------------------------------------------------------------
				
		_dragElement.style.zIndex = _oldZIndex; // drop down to old Zindex
				
		// remove event handlers
		document.onmousemove = null; document.onselectstart = null; 
				
		// this is how we know we're not dragging 
		_dragElement = null; 
				
	} 
}

//--------------------------------------------------------------------------------------------------
//	redraw the taskbar
//--------------------------------------------------------------------------------------------------

function desktopRefreshTaskbar() {
	divTasks = document.getElementById('tasks');
	html = '';
	for(i = 0; i < windows.length; i++) {
		icon = "<img src='" + windows[i][3] + "' width='14' height='14' class='taskbaricon' />";
		strTitle = windows[i][2];
		if (strTitle.length > 20) { strTitle = strTitle.substring(0, 18) + '...'; }
	
		if (windows[i][9] == 'minimized') {
			strOnClick = "windowRestore(\"" + windows[i][0] + "\")";
			html = html + "<li class='taskmin' onClick='" + strOnClick + "'>"
				 + "&nbsp;" + icon + '&nbsp;' + strTitle + "</li>";

		} else {
			strOnClick = "windowMinimize(\"" + windows[i][0] + "\")";
			html = html + "<li class='task' onClick='" + strOnClick + "'>"
				 + "&nbsp;" + icon + '&nbsp;' + strTitle + "</li>";

		}
	}
	divTasks.innerHTML = "<ul class='tasks'>" + html + "</ul>";
}

//--------------------------------------------------------------------------------------------------
//	set the contents of the tray
//--------------------------------------------------------------------------------------------------

function desktopSetTray(html) {
	divTray = document.getElementById('systray');
	divTray.innerHTML = "<span style='color: #fff'><b>" + html + "</b></span>";
}

//--------------------------------------------------------------------------------------------------
//	get the highest current z-index (ie, that of foreground window)
//--------------------------------------------------------------------------------------------------

function desktopGetMaxZIndex() {
	maxZIndex = -1;
	for(i = 0; i < windows.length; i++) {
		if (windows[i][10] > maxZIndex) { maxZIndex = windows[i][10]; }
	}
	return maxZIndex;
}

//-------------------------------------------------------------------------------------------------
//	find the absolute x and y position of an element
//-------------------------------------------------------------------------------------------------
//	credit: http://www.quirksmode.org/js/findpos.html

function desktopFindPos(obj) {
	var currLeft = currTop = 0;
	do {
		currLeft += obj.offsetLeft;
		currTop += obj.offsetTop;
	} while (obj = obj.offsetParent);
	return [currLeft,currTop];
}

//-------------------------------------------------------------------------------------------------
//	make menu html
//-------------------------------------------------------------------------------------------------
//	menuitems should be a list of icons, captions, messages and ids separated by tabs and newlines;
//	/gui/icons/document-new.png		<u>N</u>ew	menu::File::New		new
//	/gui/icons/document-open.png	<u>O</u>pen	menu::File::Open	open
//	/gui/icons/document-save.png	<u>S</u>ave	menu::File::Save	save
//	...etc

function desktopMakeMenuHtml(windowUid, menuId, menuItems) {
	var menuHtml = "<table noborder>";

	menuItems.replace(/\t\t/, "\t");	// makes for tidier code
	menuItems.replace(/\t\t/, "\t");
	menuItems.replace(/\t\t/, "\t");
	items = menuItems.split("\n");

	for (i = 0; i < items.length; i++) {
		parts = items[i].split("\t");
		itemId = 'menu' + windowUid + menuId + parts[3];
		menuHtml = menuHtml + "<tr class='menu'>"
				 + "<td class='menu' id='" + itemId + "'>"
				 + "<img src='" + parts[0] + "' width='16px' height=16px'>"
				 + "&nbsp;" + parts[1] + "&nbsp;&nbsp;"
				 + "<!-- " + windowUid + '||' + parts[2] + " --></td>"
				 + "</tr>"
	}
	menuHtml = menuHtml + "</table>";
	return menuHtml;
}

//-------------------------------------------------------------------------------------------------
//	create and show a menu (relative to parent element)
//-------------------------------------------------------------------------------------------------

function desktopShowMenu(relToObj, menuHtml, style) {
	var menuPos = desktopFindPos(relToObj);

	menuUid = createUID();
	
	menuHtml = "<div class='menu' id='m" + menuUid + "'>" + menuHtml + "</div>";
	var divMenuList = document.getElementById('menulist');
	divMenuList.innerHTML = divMenuList.innerHTML + menuHtml;

	var divMenu = document.getElementById('m' + menuUid);

	if ('below' == style) {
		menuPos[1] = menuPos[1] + relToObj.clientHeight;
		divMenu.style.left = (menuPos[0] - 2) + 'px';
		divMenu.style.top = (menuPos[1] + 2) + 'px';
		divMenu.style.zIndex = 100;
	}

	if ('above' == style) {
		menuPos[1] = menuPos[1] - divMenu.clientHeight;
		divMenu.style.left = (menuPos[0] - 2) + 'px';
		divMenu.style.top = (menuPos[1] - 4) + 'px';
		divMenu.style.zIndex = 100;
	}

	if ('right' == style) {
		menuPos[0] = menuPos[0] + divMenu.clientWidth + 15;
		divMenu.style.left = (menuPos[0] + 2) + 'px';
		divMenu.style.top = (menuPos[1]) + 'px';
		divMenu.style.zIndex = 100;
	}

	return menuUid;	
}

//-------------------------------------------------------------------------------------------------
//	show the 307 menu
//-------------------------------------------------------------------------------------------------

function desktopShow307Menu() {
	var relToObj = document.getElementById('307button');
	var menuItems = "/gui/icons/edit-prefs.png	<u>A</u>pps	307menu::Apps	apps\n"
				  + "/gui/icons/edit-prefs.png	<u>F</u>ilesystem	307menu::FS	fs\n"
				  + "/gui/icons/edit-prefs.png	<u>C</u>onsole	307menu::Console	console\n"
				  + "/gui/icons/edit-prefs.png	<u>S</u>ettings	307menu::Settings	settings\n"
				  + "/gui/icons/edit-prefs.png	A<u>b</u>out	307menu::About	about";
	
	var menuHtml = desktopMakeMenuHtml('desktop', '307menu', menuItems);
	desktopShowMenu(relToObj, menuHtml, 'above');
}

//-------------------------------------------------------------------------------------------------
//	show the apps menu
//-------------------------------------------------------------------------------------------------

function desktopShowAppsMenu() {
	var relToObj = document.getElementById('307apps');
	var menuHtml = "<table noborder>"
				 + "<tr><td class='menu'>Txtedit<!-- desktop||307menu::Apps::Txtedit --></td></tr>"
				 + "<tr><td class='menu'>Imgedit<!-- desktop||307menu::Apps::ImgEdit --></td></tr>"
				 + "</table>";

	desktopShowMenu(relToObj, menuHtml, 'right');	
}

//-------------------------------------------------------------------------------------------------
//	remove all menus
//-------------------------------------------------------------------------------------------------

function desktopRemoveAllMenus() {
	var divMenuList = document.getElementById('menulist');
	divMenuList.innerHTML = '';
}

//-------------------------------------------------------------------------------------------------
//	procMsg
//-------------------------------------------------------------------------------------------------

function procMsg(msg) {
	switch (msg) {
		case '307menu::Apps':
			desktopShowAppsMenu();
			break;

		case '307menu::FS':
			testWindowCreate();
			desktopRemoveAllMenus();
			break;

		default:
			alert('unhandled msg: ' + msg);
	}
}
