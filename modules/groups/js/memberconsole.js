//--------------------------------------------------------------------------------------------------
//*	object for managing group members
//--------------------------------------------------------------------------------------------------
//+	it expects to be called 'memberConsole'

//arg: divId - ID of div to render the console into [string]

function Groups_MemberConsole(groupUID) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	this.UID = groupUID;		//_	ref:Groups_Group [string]

	//----------------------------------------------------------------------------------------------
	//.	show form for adding member to a group
	//----------------------------------------------------------------------------------------------

	this.addMemberForm = function(memberUID) {
		var statusDiv = document.getElementById('divSRStatus' + memberUID);
		statusDiv.innerHTML = statusDiv.innerHTML + "<span class='ajaxmsg'>Loading...</span>";

		var block = '[[' + ':groups::addmemberformjs'
		 + '::groupUID=' + this.UID
		 + '::userUID=' + memberUID
		 + '::un=' + kutils.createUID() + ':]]';

		klive.bindDivToBlock('divSRStatus' + memberUID, block);
	}

	//----------------------------------------------------------------------------------------------
	//.	add a member to a group (make AJAX POST to groups module)
	//----------------------------------------------------------------------------------------------
	//arg: memberUID - UID of a Users_User object to associate with group [string]
	//asg: role - users may be regular members, or group admins (member|admin) [string]

	this.addMember = function(memberUID, position, isAdmin) {
		if (false == kapentaLoaded) { alert('Page loading... please wait'); }

		//------------------------------------------------------------------------------------------
		//	show status in search result
		//------------------------------------------------------------------------------------------
		var statusDivId = 'divSRStatus' + memberUID;
		var statusDiv = document.getElementById(statusDivId);
		statusDiv.innerHTML = "<span class='ajaxmsg'>Adding to group...</span>";	

		//------------------------------------------------------------------------------------------
		//	call the action on the server
		//------------------------------------------------------------------------------------------
		var url = jsServerPath + 'groups/addmemberjs/';

		var params = 'action=addMember'
		 + '&userUID=' + memberUID
		 + '&groupUID=' + this.UID
		 + '&position=' + position
		 + '&admin=' + position;

		cbFn = function(responseText, status) { 
			if (200 == status) {
				var sDiv = document.getElementById(statusDivId);
				sDiv.innerHTML = responseText;
				memberConsole.refresh();
			} else {
				alert('WARNING: ' + status + "\n" + responseText);
			}
		}

		kutils.httpPost(url, params, cbFn);

		//statusDiv.innerHTML = '';
	}

	//----------------------------------------------------------------------------------------------
	//.	add a member to a group (make AJAX POST to groups module)
	//----------------------------------------------------------------------------------------------
	//arg: memberUID - UID of a Users_User object to remove from group [string]

	this.removeMember = function (memberUID) {
		if (false == kapentaLoaded) { alert('loading... please wait'); }
	
		var answer = confirm ("Remove from this group?")
		if (answer) {
			// continue...
		} else {
			return;	// done...
		}

		var statusDivId = 'divMemberStatus' + memberUID;
		var statusDiv = document.getElementById(statusDivId);
		statusDiv.innerHTML = statusDiv.innerHTML + "<span class='ajaxwarn'>Removing...</span>";

		//------------------------------------------------------------------------------------------
		//	call the action on the server
		//------------------------------------------------------------------------------------------
		var url = jsServerPath + 'groups/removememberjs/';

		var params = 'action=removeMember'
		 + '&userUID=' + memberUID
		 + '&groupUID=' + this.UID;

		cbFn = function(responseText, status) { 
			if (200 == status) {
				var sDiv = document.getElementById(statusDivId);
				sDiv.innerHTML = responseText;
				memberConsole.refresh();
			} else {
				alert('WARNING: ' + status + "\n" + responseText);
				memberConsole.refresh();
			}
		}

		kutils.httpPost(url, params, cbFn);
	}

	//----------------------------------------------------------------------------------------------
	//.	reload the list of members and those who have asked to join
	//----------------------------------------------------------------------------------------------

	this.refresh = function() {
		var divId = 'divMembers' + this.UID;
		var blockTag = '[[:groups::listmembersnav::groupUID=' + this.UID + '::editmode=js:]]';

		klive.removeBlock(blockTag, false);
		klive.bindDivToBlock(divId, blockTag, false);
	}

}

