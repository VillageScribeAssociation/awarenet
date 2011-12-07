<? /*

<script src='%%serverPath%%modules/groups/js/memberconsole.js' language='Javascript'></script>
<script language='Javascript'>

	var memberConsole = new Groups_MemberConsole('%%groupUID%%');

	//----------------------------------------------------------------------------------------------
	//	show form for adding member to a group
	//----------------------------------------------------------------------------------------------

	function groups_addMemberForm(memberUID) {
		alert('deprecated: groups_addMemberForm(memberUID)');
		var statusDiv = document.getElementById('divSRStatus' + memberUID);
		statusDiv.innerHTML = statusDiv.innerHTML + "<span class='ajaxmsg'>Loading...</span>";

		var block = '[[' + ':groups::addmemberformjs'
		 + '::groupUID=%%groupUID%%'
		 + '::userUID=' + memberUID
		 + '::un=' + kutils.createUID() + ':]]';

		klive.bindDivToBlock('divSRStatus' + memberUID, block);
	}

	//----------------------------------------------------------------------------------------------
	//	add a member to a group (make AJAX POST to groups module)
	//----------------------------------------------------------------------------------------------
	//arg: memberUID - UID of a Users_User object to associate with group [string]

	function groups_addMember(memberUID, groupUID, position, isGroupAdmin) {
		alert('deprecated: groups_addMember(memberUID, groupUID, position, isGroupAdmin)');
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
		var url = '%%serverPath%%groups/addmemberjs/';

		var params = 'action=addMember'
		 + '&userUID=' + memberUID
		 + '&groupUID=' + groupUID
		 + '&position=' + position
		 + '&admin=' + isGroupAdmin;

		cbFn = function(responseText) { 
			if (200 == status) {
				var sDiv = document.getElementById(statusDivId);
				sDiv.innerHTML = responseText;
			} else {
				alert('' + status + "\n" + responseText);
			}
		}

		kutils.httpPost(url, params, cbFn);

		//statusDiv.innerHTML = '';
	}

	//----------------------------------------------------------------------------------------------
	//	add a member to a group (make AJAX POST to groups module)
	//----------------------------------------------------------------------------------------------
	//arg: memberUID - UID of a Users_User object to remove from group [string]

	function groups_removeMember(memberUID) {
		alert('deprecated: groups_removeMember(memberUID)');
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
		var url = '%%serverPath%%groups/removememberjs/';

		var params = 'action=removeMember'
		 + '&userUID=' + memberUID
		 + '&groupUID=%%groupUID%%';

		cbFn = function(responseText, status) { 
			if (200 == status) {
				var sDiv = document.getElementById(statusDivId);
				sDiv.innerHTML = responseText;
			} else {
				alert('' + status + "\n" + responseText);
			}
		}

		kutils.httpPost(url, params, cbFn);

	}

</script>

[[:theme::navtitlebox::label=Add Members::toggle=divAddmembers%%groupUID%%:]]
<div id='divAddmembers%%groupUID%%'>
[[:users::usersearchbox::cbjs=memberConsole.addMemberForm::cblabel=add to group::cbicon=arrow_down_green.png:]]
</div>
<br/>

[[:theme::navtitlebox::label=All Members::toggle=divMembers%%groupUID%%:]]
<div id='divMembers%%groupUID%%'>
[[:groups::listmembersnav::groupUID=%%groupUID%%::editmode=js:]]
</div>
<br/>

*/ ?>
