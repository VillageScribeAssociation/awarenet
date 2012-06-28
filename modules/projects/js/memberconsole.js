//--------------------------------------------------------------------------------------------------
//*	object for managing project members
//--------------------------------------------------------------------------------------------------
//+	it expects to be called 'memberConsole'

//arg: divId - ID of div to render the console into [string]

function Projects_MemberConsole(projectUID) {

	//----------------------------------------------------------------------------------------------
	//	properties
	//----------------------------------------------------------------------------------------------

	this.UID = projectUID;		//_	ref:Projects_Project [string]

	//----------------------------------------------------------------------------------------------
	//.	show form for adding member to a group
	//----------------------------------------------------------------------------------------------

	this.addMemberForm = function(memberUID) {
		var statusDiv = document.getElementById('divSRStatus' + memberUID);
		statusDiv.innerHTML = statusDiv.innerHTML + "<span class='ajaxmsg'>Loading...</span>";

		var block = '[[' + ':projects::addmemberformjs'
		 + '::projectUID=' + this.UID
		 + '::userUID=' + memberUID
		 + '::un=' + kutils.createUID() + ':]]';

		klive.bindDivToBlock('divSRStatus' + memberUID, block);
	}

	//----------------------------------------------------------------------------------------------
	//.	add a member to a project (make AJAX POST to projects module)
	//----------------------------------------------------------------------------------------------
	//arg: memberUID - UID of a Users_User object to associate with project [string]
	//asg: role - users may be regular members, or project admins (member|admin) [string]

	this.addMember = function(memberUID, role) {
		if (false == kapentaLoaded) { alert('Page loading... please wait'); }

		//------------------------------------------------------------------------------------------
		//	show status in search result
		//------------------------------------------------------------------------------------------
		var statusDivId = 'divSRStatus' + memberUID;
		var statusDiv = document.getElementById(statusDivId);
		statusDiv.innerHTML = "<span class='ajaxmsg'>Adding to project...</span>";	

		//------------------------------------------------------------------------------------------
		//	call the action on the server
		//------------------------------------------------------------------------------------------
		var url = jsServerPath + 'projects/addmemberjs/';

		var params = 'action=addMember'
		 + '&userUID=' + memberUID
		 + '&projectUID=' + this.UID
		 + '&role=' + role;

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
	//.	add a member to a project (make AJAX POST to projects module)
	//----------------------------------------------------------------------------------------------
	//arg: memberUID - UID of a Users_User object to remove from project [string]

	this.removeMember = function (memberUID) {
		if (false == kapentaLoaded) { alert('loading... please wait'); }
	
		var answer = confirm ("Remove from this project?")
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
		var url = jsServerPath + 'projects/removememberjs/';

		var params = 'action=removeMember'
		 + '&userUID=' + memberUID
		 + '&projectUID=' + this.UID;

		var cbFn = function(responseText, status) { 
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
		var blockTag = '[[:projects::listmembersnav::projectUID=' + this.UID + '::editmode=js:]]';

		klive.removeBlock(blockTag, false);
		klive.bindDivToBlock(divId, blockTag, false);
	}

}

