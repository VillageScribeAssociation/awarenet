<? /*
<br/>
<form
	id='frmEditAnnouncement%%UID%%'
	name='editAnnouncement'
	method='POST'
	action='%%serverPath%%announcements/save/'
	onSubmit="khta.updateAllAreas();"
>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title: </b></td>
    <td><input type='text' name='title' value='%%anTitle%%' size='50' /></td>
  </tr>
</table>
<br/>

<div
	class='HyperTextArea64'
	title='content'
	width='-1'
	height='400'
	style='visibility: hidden; display: none'
	refModule='announcements'
	refModel='announcements_announcement'
	refUID='%%UID%%'
>%%content64%%</div>
<script language='Javascript'> khta.convertDivs(); </script>
</form>
<table noborder>
  <tr>
   <td valign='top'>
   <input
		type='button'
		value='Save'
		onClick="$('#frmEditAnnouncement%%UID%%').submit();" />
   </td>
   <td>
   <form name='cDelete' method='GET' action='%%delUrl%%'>
   <input type='submit' value='Delete' />
   </form>
   </td>
 </tr>
</table>

*/ ?>
