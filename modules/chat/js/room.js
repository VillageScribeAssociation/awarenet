
//--------------------------------------------------------------------------------------------------
//*	Javascript char room client
//--------------------------------------------------------------------------------------------------
//+	This polls the server for new messages for the current user in the specified room.  Items
//+	are marked as delivered to users then they have been polled out to this.

function Chat_RoomClient(roomUID) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	this.roomUID = roomUID;			//_ UID of a chat room [string]
	this.pollMax = 25;				//_	seconds, for logarithmic back off [int]
	this.pollLimit = 2;				//_	seconds, for logarithmic back off [int]
	this.pollStatus = 0;			//_	number of ticks through poll time [int]
	this.busy = false;				//_	set to true when AJAX request in progress [bool]
	this.joined = false;			//_	set to true when room membership created / confirmed [bool]
	this.connected = false;			//_	set to true if this peer is connected to chat server [bool]
	
	this.userUID = '';				//_	set by init script [string]
	this.userName = '';				//_	set by init script [string]
	this.rm = '';					//_ room membership hash [string]

	//----------------------------------------------------------------------------------------------
	//	render
	//----------------------------------------------------------------------------------------------

	this.render = function() {
		var html = ''
		 + "</div>"
		 + "<div id='divCR" + this.roomUID + "'></div>"
		 + "<div id='sendForm'>"
		 + "<textarea id='txtMessage" + this.roomUID + "' rows='5' style='width: 100%;'></textarea>"
		 + "<table noborder width='100%'>"
		 + "  <tr>"
		 + "    <td width='60px'>"
		 + "      <input type='button' id='btnSubmit" + this.roomUID + "' value='Say it!' />"
		 + "    </td>"
		 + "    <td><div id='divStatus" + this.roomUID + "'></div></td>"
		 + "  </tr>"
		 + "</table>"
		 + "</div>"
		 + "<hr/>"
		 + "<div id='divMembers'><small>loading members...&nbsp;</small></div>";
		document.write(html);
		
	}

	//----------------------------------------------------------------------------------------------
	//.	display a message in the chat window
	//----------------------------------------------------------------------------------------------
	//arg: divcolor - color of div border (red|green|black) [string]

	this.display = function(uid, userUID, userName, message, divcolor) {
		//------------------------------------------------------------------------------------------
		//	make HTML fragment
		//------------------------------------------------------------------------------------------
		var msgAvatar = "images/first/module_users/model_users_user/uid_" + userUID + "/s_thumbsm/";
		var msgHtml = ''
		 + "<table noborder width='100%'>\n"
		 + "  <tr>\n"
		 + "	<td width='55px' valign='top'>\n"
		 + "		<img src='" + kutils.serverPath + msgAvatar + "' border='0' />\n"
		 + "	</td>\n"
		 + "	<td valign='top'>\n"
		 + "		<small><b>" + userName + "</b></small><br/>\n"
		 + "		" + message + "\n"
		 + "	</td>\n"
		 + "  </tr>\n"
		 + "</table>\n";

		//------------------------------------------------------------------------------------------
		//	check if this message is already in the display
		//------------------------------------------------------------------------------------------
		var containerDiv = document.getElementById('divM' + uid);
		if (!containerDiv) {
			var newMsg = ''
			 + "<div id='divM" + uid + "' "
			 + "class='chatmessage" + divcolor + "' "
			 + "style='padding: 0px 0px 0px 0px;'>"
			 + msgHtml
			 + "</div>";

			$(this.msgDiv).append(newMsg);

		} else {
			containerDiv.innerHTML = msgHtml;
			containerDiv.setAttribute('class', 'chatmessage' + divcolor);
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	discover if the current user is a known member of this chat room
	//----------------------------------------------------------------------------------------------
	//returns: true if request made, false if not [bool]	

	this.checkIfMember = function() {
		if (true == this.busy) { return false; }
		this.busy = true;
		this.setStatus("Checking membership...", 'black');

		var that = this;
		var url = jsServerPath + 'chat/ismember/' + this.roomUID;

		var cbFn = function(responseText, status) {
			that.busy = false;
			if ('<yes/>' == responseText) {
				that.msgDiv.innerHTML = ''
				 + that.msgDiv.innerHTML
				 + "<div class='chatmessagegreen'>You are a member of this chat room.</div>\n";

				that.setStatus('Membership confirmed...', 'green');
				that.joined = true;
			} else {
				that.setStatus('Joining room...', 'black');
				that.join();
			}
		}

		kutils.httpPost(url, 'x=y', cbFn);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	join this chat room
	//----------------------------------------------------------------------------------------------
	//returns: true is join request sent, false if not [bool]

	this.join = function() {
		if (true == this.busy) { return false; }
		this.busy = true;

		var that = this;
		var url = jsServerPath + 'chat/join/' + this.roomUID;

		that.msgDiv.innerHTML = ''
		 + that.msgDiv.innerHTML
		 + "<div class='chatmessageblack'>Joining chat room...</div>\n";

		var cbFn = function(responseText, status) {
			//alert("/join/ response:\n" + responseText);
			that.busy = false;
			if (-1 !== responseText.indexOf('<ok/>')) {
				that.msgDiv.innerHTML = ''
				 + that.msgDiv.innerHTML
				 + "<div class='chatmessagegreen'>You are now a member of this chat room.</div>\n";
				that.setStatus('Membership confirmed...', 'green');
				that.joined = true;

			} else {
				alert(responseText);

				that.msgDiv.innerHTML = ''
				 + that.msgDiv.innerHTML
				 + "<div class='chatmessagered'>Retrying...</div>\n";

				that.setStatus('Retrying...', 'black');
				that.join();
			}
		}

		kutils.httpPost(url, 'x=y', cbFn);
		return true;
	}

	//----------------------------------------------------------------------------------------------
	//.	leave this chat room
	//----------------------------------------------------------------------------------------------

	this.leave = function() {
		this.setStatus('Leaving room...', 'black');
	}

	//----------------------------------------------------------------------------------------------
	//.	poll the server
	//----------------------------------------------------------------------------------------------

	this.poll = function() {
		var url = jsServerPath + 'chat/poll/';
		var params = 'UID=' + this.roomUID;	
		var that = this;

		var cbFn = function(responseText, status) {
			if (200 == status) {
				that.setStatus('Polling...', 'green');
				if ('' != responseText) {
					//alert(responseText);
					that.processMessages(responseText);
				}
			} else {
				that.setStatus('Not connected.', 'red');
			}
		}

		kutils.httpPost(url, params, cbFn);
	}

	//----------------------------------------------------------------------------------------------
	//.	reload list of memberships
	//----------------------------------------------------------------------------------------------

	this.getMembers = function() {
		var url = jsServerPath + 'live/getblock/';
		var block = '[[:chat::listmembers::roomUID=' + this.roomUID + ':]]';
		var params = 'b=' + kutils.base64_encode(block);
		var that = this;

		var cbFn = function(responseText, status) { 
			that.divMembers.innerHTML = ''
			 + "<small><b>Members:</b> " + responseText + "</small>";
		}

		kutils.httpPost(url, params, cbFn);
	}

	//----------------------------------------------------------------------------------------------
	//.	tick, from an external clock
	//----------------------------------------------------------------------------------------------

	this.tick = function() {
		if (false == this.joined) { this.checkIfMember(); }

		if (this.pollStatus >= this.pollLimit) {
			this.pollStatus = 0;
			this.pollLimit = this.pollLimit + 1;
			if (this.pollLimit > this.pollMax) { this.pollLimit = this.pollMax; }			
			this.poll();

		} else {
			var label = '';
			for (var i = 0; i <= this.pollLimit; i++) {
				if (i == this.pollStatus) { label = label + '|'; }
				else { label = label + '.'; }
			}
			this.setStatus('Polling... [' + label + ']', 'green');
			this.pollStatus = this.pollStatus + 1;
		}

	}

	//----------------------------------------------------------------------------------------------
	//.	set status (TODO: migrate to window status setting)
	//----------------------------------------------------------------------------------------------

	this.setStatus = function(msg, color) {
		this.statusDiv.innerHTML = ''
		 + "<div class='chatmessage" + color + "'><small>" + msg + "</small></div>";
	}

	//----------------------------------------------------------------------------------------------
	//.	handler for submit button
	//----------------------------------------------------------------------------------------------

	this.submitClicked = function() {
		var that = this;
		var txtMessage = document.getElementById('txtMessage' + this.roomUID);
		var url = jsServerPath + 'chat/send/';


		if ('' == kutils.trim(txtMessage.value)) { 
			this.setStatus('Nothing to Send', 'red');
			txtMessage.value = '';
			return false;
		}

		var outgoingMessage = txtMessage.value;
		var outgoingUID = kutils.createUID();
		this.display(outgoingUID, this.userUID, this.userName, outgoingMessage, 'red');

		var params = ''
		 + 'UID=' + this.roomUID
		 + '&reqUID=' + outgoingUID
		 + "&message=" + kutils.base64_encode(txtMessage.value);	

		var cbFn = function(responseText, status) {
			if (-1 == responseText.indexOf("</sent>")) {
				alert(responseText);
				that.setStatus('Not sent.', 'red');
			} else {
				that.setStatus('Message sent.', 'red');
				if ('' != responseText) {
					// TODO: error checking here
					//alert(responseText);
					var startPos = responseText.indexOf("<sent>");					
					var endPos = responseText.indexOf("</sent>");
					var uid = responseText.substring(startPos + 6, endPos);					

					//alert('uid:' + uid);
					that.display(uid, that.userUID, that.userName, outgoingMessage, 'black');
				}
			}
		}

		this.pollLimit = 2;
		this.setStatus('Sending...', 'green');
		//alert('sending: ' + txtMessage.value);
		kutils.httpPost(url, params, cbFn);
		txtMessage.value = '';
	}

	//----------------------------------------------------------------------------------------------
	//	handle key presses in the textarea
	//----------------------------------------------------------------------------------------------
	this.taKeyUp = function(e) {
		var e = e || window.event;
		var keyID = (window.event) ? event.keyCode : e.keyCode;
		
		if (
			(rm.txtMessage.value.indexOf("\n") != -1) || 
			(rm.txtMessage.value.indexOf("\r") != -1)
		) {
			rm.submitClicked();
		}
	}

	//----------------------------------------------------------------------------------------------
	//	process new messages
	//----------------------------------------------------------------------------------------------

	this.processMessages = function(raw) {
		//alert("processing:\n" + raw);
		var lines = raw.split("\n");
		for (var i = 0; i < lines.length; i++) {		// split into lines
			var parts = lines[i].split("|");
			//alert(parts.length + " parts " + "line: " + lines[i]);

			//--------------------------------------------------------------------------------------
			//	handle messages
			//--------------------------------------------------------------------------------------
			if ('msg' == parts[0]) {					// split into fields
				var msgUID = parts[1];
				var fromUID = parts[2];
				var fromName = kutils.base64_decode(parts[3]);
				var message = kutils.base64_decode(parts[4]);
				//alert('dispalying: ' + msgUID + ' => ' + message);
				this.display(msgUID, fromUID, fromName, message, 'green')

				this.pollLimit = 2;						// shorten poll if messages recieved
			}


			//--------------------------------------------------------------------------------------
			//	check hash
			//--------------------------------------------------------------------------------------
			if ('rmh' == parts[0]) {
				if (parts[1] != this.rm) {
					var throbber = "themes/clockface/images/throbber-inline.gif";
					this.divMembers.innerHTML = this.divMembers.innerHTML
					 + "<img src='" + kutils.serverPath + throbber + "' />";
					this.getMembers();
					this.rm = parts[1];
					this.pollLimit = 2;					// shorten poll if someone joined
				}
			}
		}
	}

	//----------------------------------------------------------------------------------------------
	//	initialize
	//----------------------------------------------------------------------------------------------
	var that = this;
	this.render();

	this.statusDiv = document.getElementById('divStatus' + this.roomUID);
	this.msgDiv = document.getElementById('divCR' + this.roomUID);
	that.divMembers = document.getElementById('divMembers');
	this.submitButton = document.getElementById('btnSubmit' + this.roomUID);
	this.txtMessage = document.getElementById('txtMessage' + this.roomUID);
	this.txtMessage.onkeyup = this.taKeyUp;

	this.submitButton.onclick = function() { that.submitClicked(); }
	this.setStatus('Initializing...', 'green');

}
