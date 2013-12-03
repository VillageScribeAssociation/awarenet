<? /*
<form
	id='frmEditGroup%%UID%%'
	name='editschool'
	method='POST'
	action='%%serverPath%%groups/save/'
	onClick='khta.updateAllAreas();'
>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder width='100%'>
  <tr>
    <td width='60px'><b>Name:</b></td>
    <td><input type='text' name='name' value='%%name%%' style='width: 100%;' /></td>
  </tr>
  <tr>
    <td><b>Type:</b></td>
    <td>
      <select name='type'>
        <option value='%%type%%'>%%type%%</option>
        <option value='Club'>Club</option>
        <option value='Team'>Team</option>
        <option value='Society'>Society</option>
        <option value='Association'>Association</option>
        <option value='Production'>Production</option>
        <option value='Group'>Group</option>
      </select>
    </td>
  </tr>
</table>
<br/>
<b>Description of this group:</b><br/>
<div
	class='HyperTextArea64'
	title='description'
	width='-1'
	height='400'
	refModule='groups'
	refModel='groups_group'
	refUID='%%UID%%'
>%%description64%%</div>
<script language='Javascript'> khta.convertDivs(); </script>
</form>

<table noborder>
	<tr>
		<td valign='top'>
			<input type='button' value='Save' onClick="$('#frmEditGroup%%UID%%').submit();" />
		</td>
		<td valign='top'>
			<form name='cDelete' method='GET' action='%%delUrl%%'>
			<input type='submit' value='Delete' />
			</form>
		</td>
	</tr>
</table>
<br/>

*/ ?>
