//--------------------------------------------------------------------------------------------------
//	awareNet chat client
//--------------------------------------------------------------------------------------------------

//	chatWindows: 2d array of windows [0] => [fromUID],[x],[y],[messageCount],[temp]
//	chatMsg: 2d array of messages [0] => [msgUID],[fromUID],[timestr],[timestamp],[content],[mine]
//
//	chat has now been moved onto the page notifications system: new messages arrive on channel:
//
//		chat-user-1212151345 (where 1212151345 is the user's UID)
//
//	
//	
//
//==================================================================================================
//	globals
//==================================================================================================

var chatWindows = Array();
var chatMsg = Array();			

var lastUpdate = 0;				// last time a cookie was changed
var lastMsg = 0;				// timestamp of the last message this client received
var contentHeight = 800;		// height of window

var _startX = 0; // mouse starting positions 
var _startY = 0;		
var _offsetX = 0; // current element offset 
var _offsetY = 0; 
var _dragElement; // needs to be passed from OnMouseDown to OnMouseMove 
var _oldZIndex = 0; // we temporarily increase the z-index during drag 

var timerInterval = 1000;	// timer ticks ~ every 1 second
var timerStep = 8;			// check for new messages every 8 seconds
var timerStepCount = 0;
var msgQueueSize = 200;		// max no. of messages we track, limited by browser's max cookie size

//==================================================================================================
//	set everything up
//==================================================================================================

//=== *BEGIN* ======================================================================================

function chatInit() {
	if (awareNetChat == false) return false;
	setContentHeight();		// get the height of current window
	initDragDrop(); 		// set event handlers

	msgSubscribe('chat-user-' + jsUserUID, msgh_chat);
}

//--------------------------------------------------------------------------------------------------
//	open a new chat window
//--------------------------------------------------------------------------------------------------

function chatStart(userUID) {
	if (windowExists(userUID) == false) { 
		var xPos = Math.round(Math.random() * 700);
		var yPos = Math.round(Math.random() * 200);
		windowCreate(userUID, xPos, yPos);
	} 
}

//==================================================================================================
//	message handling
//==================================================================================================

function msgh_chat(channel, event, args) {
	var dataStr = "channel: " + channel + "\n"
				+ "event: " + event + "\n"
				+ "args: " + args + "\n";

	//alert(dataStr);

	var parts = args.split('|');
	var msgUID = parts[0];
	var fromUID = parts[1];
	var time = parts[2];
	var timestamp = parts[3];
	var content = base64_decode(parts[4]);
	var isMine = Math.floor(parts[5]);

	if (windowExists(fromUID) == false) { 
		var xPos = Math.round(Math.random() * 700);
		var yPos = Math.round(Math.random() * 200);
		windowCreate(fromUID, xPos,  yPos );
	} 

	messageAdd(msgUID, fromUID, time, timestamp, content, isMine);
}

//==================================================================================================
//	drag-drop code
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	set the event handlers
//--------------------------------------------------------------------------------------------------
		
function initDragDrop() { 
	document.onmousedown = OnMouseDown; 
	document.onmouseup = OnMouseUp; 
}

//--------------------------------------------------------------------------------------------------
//	find height of window content before any chat windows are drawn
//--------------------------------------------------------------------------------------------------
		
function setContentHeight() {
	//alert("height: " + document.body.offsetHeight);
	contentHeight = document.body.offsetHeight
}

//--------------------------------------------------------------------------------------------------
//	onMouseDown
//--------------------------------------------------------------------------------------------------

function OnMouseDown(e) { 
	// IE is retarded and doesn't pass the event object 
	if (e == null) { e = window.event; }
		
	// IE uses srcElement, others use target  (note to self: neat)
	var target = e.target != null ? e.target : e.srcElement;

	//tempVar = target.className == 'drag' ? 'draggable element clicked' : 'NON-draggable element clicked'; 			
	//debugNotify(tempVar);
			
	//----------------------------------------------------------------------------------------------
	//	Determine which button was clicked
	//	for IE, left click == 1  for Firefox, left click == 0 
	//----------------------------------------------------------------------------------------------
			
	// en: if event.button = left and DOM object has class 'drag'
			
	if ( (e.button == 1 && window.event != null || e.button == 0) 
		  && (target.className == 'drag') && (target.id != 'msgDiv') ) { 
				  
		// get the mouse position 
		_startX = e.clientX; 
		_startY = e.clientY; 
				
		// get the clicked element's position 
		_offsetX = ExtractNumber(target.style.left); 
		_offsetY = ExtractNumber(target.style.top); 
				
		// bring the clicked element to the front while it is being dragged 
		// (reconsider this, like maybe [bring to front] button)
		_oldZIndex = target.style.zIndex; 
		target.style.zIndex = 10000; 
				
		// we need to access the element in OnMouseMove 
		_dragElement = target; 
				
		// tell our code to start moving the element with the mouse 
		document.onmousemove = OnMouseMove; 
				
		// cancel out any text selections document.body.focus(); 
		// prevent text selection in IE 
		document.onselectstart = function () { return false; }; 
				
		// prevent text selection (except IE) 
			
		return false; 
				
	} // end if
			
} // end OnMouseDown
		
//-------------------------------------------------------------------------------------------
//	onMouseMove - only fires when dragging something - hopefully
//-------------------------------------------------------------------------------------------

function OnMouseMove(e) { 
	if (e == null) { var e = window.event; } 
			
	//---------------------------------------------------------------------------------------
	// this is the actual 'drag code'
	// ps: note use of style properties for absolute positioning, 'px' is important
	//---------------------------------------------------------------------------------------

	if (_dragElement == undefined) {
		// nothing to do
	} else {
	  if (_dragElement != null) {
		_dragElement.style.left = (_offsetX + e.clientX - _startX) + 'px';
		_dragElement.style.top = (_offsetY + e.clientY - _startY) + 'px'; 

		//debugNotify ( '(' + _dragElement.style.left + ', ' + _dragElement.style.top + ')' );
	  }
	}
		
}
		
//-------------------------------------------------------------------------------------------
//	onMouseUp
//	TODO: save position of dropped item for drag/drop/block insertion, etc
//-------------------------------------------------------------------------------------------

function OnMouseUp(e) { 
	if (_dragElement != null) { 

		//------------------------------------------------------------------------------------------
		//	refresh stored Y position of windows
		//------------------------------------------------------------------------------------------

		var stLeft = Number(_dragElement.style.left.replace("px", ""));
		var stTop = Number(_dragElement.style.top.replace("px", "")) - 47;
		var stFromUID = _dragElement.id.replace('cw', '');
		//alert("offsets: " + stLeft + ',' + (stTop + contentHeight) + "id: " + stFromUID);
		//cookieMoveWindow(stFromUID, stLeft, (stTop + contentHeight));
		//lastUpdate = cookieSetChatUpdate();

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
//	utility functions
//--------------------------------------------------------------------------------------------------
		
function ExtractNumber(value) { 
	var n = parseInt(value); 
	return n == null || isNaN(n) ? 0 : n; 
} 
		
// this is simply a shortcut for the eyes and fingers 
function $(id) { return document.getElementById(id); }

//==================================================================================================
//	window functions
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	duh
//--------------------------------------------------------------------------------------------------

function windowSetPosition(fromUID, xPos, yPos) {
	var cwDiv = document.getElementById('cw' + fromUID);
	var currLeft = Number(cwDiv.style.left.replace('px', ''));
	var currTop = Number(cwDiv.style.top.replace('px', ''));
	var newTop = ((contentHeight - yPos - 47) * -1)

	//alert('currLeft: ' + currLeft + ' currTop: ' + currTop);
	//alert('xPos: ' + xPos + ' yPos: ' + yPos + ' newTop: ' + newTop);
	cwDiv.style.left = xPos + 'px';
	cwDiv.style.top = newTop + 'px';
}

//--------------------------------------------------------------------------------------------------
//	create a chat window given a user UID, if one does not already exist
//--------------------------------------------------------------------------------------------------

function windowCreate(fromUser, xPos, yPos) {
	//alert('windowCreate(' + fromUser + ',' + xPos + ',' + yPos + ')');
	if (windowExists(fromUser) == true) { return false; }

	// get position for window relative to mainContentFrame
	cwDivLeft = xPos + 'px';
	cwDivTop = (yPos - contentHeight + 47) + 'px';

	// add to local array
	var newChatWindow = new Array();
	newChatWindow[0] = fromUser;	// fromUID
	newChatWindow[1] = cwDivLeft	// x		// not used - consider removing
	newChatWindow[2] = cwDivTop;	// y
	newChatWindow[3] = 0;			// number of messages
	newChatWindow[4] = 0;			// temp, tally of messages
	chatWindows.push(newChatWindow);

	divMsg = document.getElementById('msgDiv');
	divMsg.innerHTML = divMsg.innerHTML
					 + "<div id='cw" + fromUser + "' class='drag'"
					 + " style='top: " + cwDivTop + "; left: " + cwDivLeft + "; float: left;'>"
					 + "<img src='" + jsServerPath + "themes/clockface/images/close.png' "
					 + " style='float: right;' " + " onClick=\"windowClose('" + fromUser  + "')\" />"
					 + "<iframe src='" + jsServerPath + "users/tinybox/" + fromUser + "/' "
					 + " width='200' height='80' frameborder='0' "
					 + " name='userBox" + fromUser + "' ></iframe>"
					 + "<div id='cwt" + fromUser + "' class='cwtext'></div>" 
					 + "<form name='reply" + fromUser + "' id='rpy" + fromUser + "' method='POST'"
					 + " action='" + jsServerPath + "chat/sendmsg/' target='ifs" + fromUser + "'>"
					 + "<input type='hidden' name='toUser' value='" + fromUser + "' />"
					 + "<textarea name='content' id='content" + fromUser + "' rows='3' cols='22'"
					 + " onkeyup=\"windowTxtChange('" + fromUser + "')\">"
					 + "</textarea><br/>"
					 + "<iframe name='ifs" + fromUser + "' width='5' height='5'"
					 + " frameborder='0'></iframe>"
					 + "</form>"
					 + "</div>";

	// set record correct Y position of window (relative to top of screen)
	//refreshWindowY();
	return true;
}

//--------------------------------------------------------------------------------------------------
//	close chat window, given user UID
//--------------------------------------------------------------------------------------------------

function windowClose(fromUser) {
	//----------------------------------------------------------------------------------------------
	//	remove from local window list
	//----------------------------------------------------------------------------------------------
	var newList = Array();	// user UIDs
	var j = 0;

	for (i = 0; i < chatWindows.length ; i++) {
		if (chatWindows[i][0] == fromUser) { 
			// leave it out
		} else {
			newList[j] = chatWindows[i];
			j++;
		}
	}	
	chatWindows = newList;

	//----------------------------------------------------------------------------------------------
	//	bump all windows before it 50px to the right
	//----------------------------------------------------------------------------------------------
	//var foundMatch = false;
	//var windowArray = cookieGetWindowArray()
	//for (hwnd = 0; hwnd < windowArray.length; hwnd++) {
	//	var thisWindow = windowArray[hwnd];
	//	if (fromUser == thisWindow[0]) { foundMatch = true; }
	//	if (foundMatch == false) {
	///		cookieMoveWindow(thisWindow[0], (Number(thisWindow[1]) + 50), thisWindow[2]);
	//}
	//}

	//----------------------------------------------------------------------------------------------
	//	replace with tiny iFrame, move to bottom of window
	//----------------------------------------------------------------------------------------------
	cwDiv = document.getElementById('cw' + fromUser);
	closeWindowLink = jsServerPath + 'chat/endchat/fromuid_' + fromUser;

	cwDiv.innerHTML = "<iframe src='" + jsServerPath + 'chat/endchat/fromuid_' + fromUser + "' " 
					+ " width='20' height='20' style='align=right;'></iframe>";

	cwDiv.style.top = contentHeight;	// move to bottom of screen
}

//--------------------------------------------------------------------------------------------------
//	remove a chatWindow completely (after it's closed)
//--------------------------------------------------------------------------------------------------

function windowRemove(fromUser) {
	//--------------------------------------------------------------------------------------
	//	remove from cookie:chatwindows
	//--------------------------------------------------------------------------------------
	//alert("windowClose is removing " + fromUser + " cookieRemoveWindow");
	//cookieRemoveWindow(fromUser);
	//lastUpdate = cookieSetChatUpdate(); // let other windows know to check

	//--------------------------------------------------------------------------------------
	//	kill the div
	//--------------------------------------------------------------------------------------
	cwDiv = document.getElementById('cw' + fromUser);
	divMsg = document.getElementById('msgDiv');
	cwDiv.innerHTML = '';
	divMsg.removeChild(cwDiv);
}

//--------------------------------------------------------------------------------------------------
//	check if a chat window exists for a given user UID
//--------------------------------------------------------------------------------------------------

function windowExists(fromUser) {
	for (i = 0; i < chatWindows.length ; i++) {
		if (chatWindows[i][0] == fromUser) { return true; }
	}
	return false;
}

//--------------------------------------------------------------------------------------------------
//	look for newlines in text box, submit if found
//--------------------------------------------------------------------------------------------------

function windowTxtChange(fromUser) {
	txtBox = document.getElementById('content' + fromUser);
	if (txtBox.value.indexOf("\n") != -1) { sendReply(fromUser); }
	if (txtBox.value.indexOf("\r") != -1) { sendReply(fromUser); }
}

//--------------------------------------------------------------------------------------------------
//	count messages for each window - update display if there are new massages
//--------------------------------------------------------------------------------------------------

function windowCountMessages() {
	for (windowIdx = 0; windowIdx < chatWindows.length; windowIdx++) {
		chatWindows[windowIdx][4] = 0;
		for (msgIdx = 0; msgIdx < chatMsg.length; msgIdx++) {
			if (chatMsg[msgIdx][1] == chatWindows[windowIdx][0]) {
				chatWindows[windowIdx][4] += 1;
			} 
		}
		if (chatWindows[windowIdx][4] > chatWindows[windowIdx][3]) {
			windowReloadTxt(chatWindows[windowIdx][0]);
		}
	}
}

//--------------------------------------------------------------------------------------------------
//	reload a chat window
//--------------------------------------------------------------------------------------------------

function windowReloadTxt(fromUser) {
	txtDiv = document.getElementById('cwt' + fromUser);
	var newMsgTxt = '';
	for (msgIdx = 0; msgIdx < chatMsg.length; msgIdx++) {
		var thisMsg = chatMsg[msgIdx];
		if (thisMsg[1] == fromUser) {
			if (thisMsg[5] == 'yes') {
				newMsgTxt = newMsgTxt + "<font color='green'>" + thisMsg[4]
						  + '<br/><small>' + thisMsg[2] + '</small></font><br/>';
			} else {
				newMsgTxt = newMsgTxt + thisMsg[4]
						  + '<br/><small>' + thisMsg[2] + '</small><br/>';
			}
		}
	}
	txtDiv.innerHTML = newMsgTxt;
	txtDiv.scrollTop = txtDiv.scrollHeight;
}

//==================================================================================================
//	message functions
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	send a reply (TODO: replace with xmlHttpRequest)
//--------------------------------------------------------------------------------------------------

function sendReply(fromUser) {
	theForm = document.forms['reply' + fromUser];
	theForm.submit();

	dt = new Date();
	dtStr = dt.getFullYear() + '-' + dt.getMonth() + '-' + dt.getDate() + ' ' 
		  + dt.getHours() + ':' + dt.getMinutes() + ':' + dt.getSeconds();

	theTxtBox = document.getElementById('content' + fromUser);
	theCwtDiv = document.getElementById('cwt' + fromUser);
	theCwtDiv.innerHTML = theCwtDiv.innerHTML + "<font color='#888888'>" + theTxtBox.value
							 + '<br/><small>' + dtStr + '</small></font><br/>';
	theCwtDiv.scrollTop = theCwtDiv.scrollHeight;
	theTxtBox.value = '';
}

//--------------------------------------------------------------------------------------------------
//	add a message
//--------------------------------------------------------------------------------------------------

function messageAdd(msgUID, fromUser, time, timestamp, content, isMine) {
	//alert('received message from ' + fromUser);
	//----------------------------------------------------------------------------------------------
	//	check if message is known to other windows, if not add it
	//----------------------------------------------------------------------------------------------
	//if (false == cookieMessageExists(msgUID)) {
		// check if this is the start of a new conversation
		//if (false == cookieWindowExists(fromUser)) { 
		//	cookieAddWindow(fromUser, 100, 100); 
		//	cookieSetChatUpdate();
		//}
		//cookieAddMessage(msgUID);
	//}

	//----------------------------------------------------------------------------------------------
	//	check if this is already in this window's queue
	//----------------------------------------------------------------------------------------------
	
	if (false == messageExists(msgUID)) {
		newItem = new Array();
		newItem[0] = msgUID;
		newItem[1] = fromUser;
		newItem[2] = time;
		newItem[3] = timestamp;
		newItem[4] = content;
		newItem[5] = isMine;
		chatMsg[chatMsg.length] = newItem;
	}

	//----------------------------------------------------------------------------------------------
	//	update time of last message
	//----------------------------------------------------------------------------------------------
	if (Number(timestamp) > lastMsg) { lastMsg = Number(timestamp); }

	//----------------------------------------------------------------------------------------------
	//	refresh all windows
	//----------------------------------------------------------------------------------------------
	windowCountMessages();
}

//--------------------------------------------------------------------------------------------------
//	check if a message exists in this windows queue
//--------------------------------------------------------------------------------------------------

function messageExists(msgUID) {
	for (mIdx = 0; mIdx < chatMsg.length; mIdx++) 
		{ if (chatMsg[mIdx][0] == msgUID) { return true; }	}
	return false;
}

//==================================================================================================
//	cookie functions
//==================================================================================================

//--------------------------------------------------------------------------------------------------
//	create/write a cookie
//--------------------------------------------------------------------------------------------------

//function cookieCreate(cookieName,value,days) {
//	if (days) {
//		var date = new Date();
//		date.setTime(date.getTime()+(days*24*60*60*1000));
//		var expires = "; expires="+date.toGMTString();
//	}
//	else var expires = "";
//	document.cookie = cookieName+"="+value+expires+"; path=/";
//}

//--------------------------------------------------------------------------------------------------
//	read a cookie (returns null if does not exist)
//--------------------------------------------------------------------------------------------------

//function cookieRead(cookieName) {
//	var nameEQ = cookieName + "=";
//	var ca = document.cookie.split(';');
//	for(var i=0;i < ca.length;i++) {
//		var c = ca[i];
//		while (c.charAt(0)==' ') c = c.substring(1,c.length);
//		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
//	}
//	return null;
//}

//--------------------------------------------------------------------------------------------------
//	delete a cookie (may use this on window close)
//--------------------------------------------------------------------------------------------------

//function cookieErase(cookieName) {
//	cookieCreate(cookieName,"",-1);
//}

//--------------------------------------------------------------------------------------------------
//	add a window to chatwindows cookie
//--------------------------------------------------------------------------------------------------
//	javascript:cookieAddWindow('public', 100, 100); //124289610010992581 //102266576519543775

//function cookieAddWindow(fromUID, xPos, yPos) {
//	if (true == cookieWindowExists(fromUID)) { cookieRemoveWindow(fromUID); }	// prevent duplicates
//	var cWindowList = cookieRead('chatwindows');
//	if (null == cWindowList) { cWindowList = ''; }
//	cWindowList = cWindowList + "||" + fromUID + "|" + xPos + "|" + yPos;
//	//alert('cookieAddWindow saving: ' + cWindowList);
//	cookieCreate('chatwindows', cWindowList, 7);
//}

//--------------------------------------------------------------------------------------------------
//	check whether a window is in cookie:chatwindows (returns true if it is)
//--------------------------------------------------------------------------------------------------

//function cookieWindowExists(fromUID) {
//	var windowAry = cookieGetWindowArray();
//	for (idx = 0; idx < windowAry.length; idx++) {
//		var thisWin = windowAry[idx];
//		if (fromUID == thisWin[0]) { return true; }
//	}
//	return false;
//}

//--------------------------------------------------------------------------------------------------
//	move a window without reordering the chatwindows string
//--------------------------------------------------------------------------------------------------

//function cookieMoveWindow(fromUID, xPos, yPos) {
//	var wndArray = cookieGetWindowArray()
//	var newStr = '';
//	if (xPos > 900) { xPos = 900; }
//	for (hw = 0; hw < wndArray.length; hw++) {
//		var thisHw = wndArray[hw];
//		if (thisHw[0] == fromUID) {
//			newStr = newStr + '||' + fromUID + '|' + xPos + '|' + yPos;
//		} else {
//			newStr = newStr + '||' + thisHw[0] + '|' + thisHw[1] + '|' + thisHw[2];
//		}
//	}
//	//alert("cookieMoveWindow newStr: " + newStr);
//	cookieCreate('chatwindows', newStr, 7);
//}

//--------------------------------------------------------------------------------------------------
//	remove a window from chatwindows cookie
//--------------------------------------------------------------------------------------------------
//	javascript:cookieRemoveWindow('public');

//function cookieRemoveWindow(fromUID) {
//	var winAry = cookieGetWindowArray();
//	//alert("winAry length = " + winAry.length);
//	var newCookie = '';
//	for (hi = 0; hi < winAry.length; hi++) {
//		var rcols = winAry[hi];
//		if (rcols[0] == fromUID) {
//			// dont add it
//		} else {
//			newCookie = newCookie + "||" + rcols[0] + "|" + rcols[1] + "|" + rcols[2];
//		}
//	}
//	cookieCreate('chatwindows', newCookie, 7);
//}

//--------------------------------------------------------------------------------------------------
//	read cookie:chatwindows into a 2d array
//--------------------------------------------------------------------------------------------------

//function cookieGetWindowArray() {
//	var windowArray = new Array();
//
//	// get the cookie string
//	var cWindowList = cookieRead('chatwindows');
//	if (null == cWindowList) { cWindowList = ''; }
//	
//	// split into rows and columns
//	var rows = cWindowList.split("||");
//	for (i = 1; i < rows.length; i++) {
//		var colsString = rows[i];
//		if (colsString.length > 0) {
//			var cols = colsString.split("|");
//			if (cols[1] > 900) { cols[1] = 900; }	// stop windows moving off right of screen
//			windowArray[(i - 1)] = cols;
//		}
//	}
//	// done
//	return windowArray;
//}

//--------------------------------------------------------------------------------------------------
//	get the cookie:chatupdate timestamp
//--------------------------------------------------------------------------------------------------

//function cookieGetChatUpdate() {
//	var tsUpdate = cookieRead('chatupdate');
//	if (null == tsUpdate) {
//		timestamp = Number(new Date());
//		cookieCreate('chatupdate', timestamp, 7);
//		return timestamp;
//	} else { return tsUpdate; }		
//}

//--------------------------------------------------------------------------------------------------
//	set the cookie:chatupdate timestamp
//--------------------------------------------------------------------------------------------------

//function cookieSetChatUpdate() {
//	timestamp = Number(new Date());
//	cookieCreate('chatupdate', timestamp, 7);
//	return timestamp;
//}

//--------------------------------------------------------------------------------------------------
//	read cookie:chatmessages, return array of message UIDs
//--------------------------------------------------------------------------------------------------

//function cookieGetMessageArray() {
//	var msgArray = new Array();
//	var msgString = cookieRead('chatmessages');
//	if (null == msgString) { 
//		msgString = cookieMakeMessageString();
//		cookieCreate('chatmessages', msgString, 7); 
//	}
//
//	var thisPart = '';
//	var msgStringParts = msgString.split('|');
//	for (i = 0; i < msgStringParts.length; i++) {
//		thisPart = msgStringParts[i];
//		if (thisPart.length > 0) { msgArray.push(thisPart); }
//	}
//	return msgArray;
//}

//--------------------------------------------------------------------------------------------------
//	make string of message UIDS, like |JKLJK|GHJUH|KJJKH|... (to maximum size of msgQueueSize)
//--------------------------------------------------------------------------------------------------

//function cookieMakeMessageString() {
//	var msgString = '';  var maxMsg = msgQueueSize;		// maximum number
//	for (i = 0; i < chatMsg.length; i++) {
//		var msgCols = chatMsg[i];
//		if (maxMsg > 0) {
//			msgString = msgCols[0] + '|' + msgString;
//			maxMsg--;
//		}
//	}
//	return msgString;
//}

//--------------------------------------------------------------------------------------------------
//	check if a message is in the list
//--------------------------------------------------------------------------------------------------

//function cookieMessageExists(msgUID) {
//	var cMsgAry = cookieGetMessageArray();
//	for (cM = 0; cM < cMsgAry.length; cM++) {
//		if (msgUID == cMsgAry[cM]) { return true; }
//	}
//	return false;
//}

//--------------------------------------------------------------------------------------------------
//	add a message to the list
//--------------------------------------------------------------------------------------------------

//function cookieAddMessage(msgUID) {
//	var cMsgAry = cookieGetMessageArray();
//	var msgQCount = msgQueueSize;
//	newStr = msgUID + '|';
//	for(cMs = 0; cMs < cMsgAry; cMs++) {
//		if (msgQCount > 0) { newStr = newStr + cMsgAry[cMs] + '|';	}
//		msgQCount--;
//	}
//	cookieCreate('chatmessages', newStr, 7);
//}

//==================================================================================================
//	testing functions
//==================================================================================================
//	javascript:testAlertWindowArray()

function testAlertWindowArray() {
	var alertStr = "cookie:chatwindows\n";
	var windowArray = cookieGetWindowArray();
	for (i = 0; i < windowArray.length; i++) {
		alertStr = alertStr + '[' + i + ']' + "\n";
		var cols = windowArray[i];
		for (j = 0; j < cols.length; j++) {
			alertStr = alertStr + '[' + i + '][' + j + '] = ' + cols[j] + "\n";
		}
	}
	alert(alertStr);
}

