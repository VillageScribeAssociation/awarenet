<? /*
<form
	id='frmEditSchool%%UID%%'
	name='editschool'
	method='POST'
	onSubmit="khta.updateAllAreas();"
	action='%%serverPath%%schools/save/'
>
<input type='hidden' name='action' value='saveRecord' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Name:</b></td>
    <td><input type='text' name='name' value='%%name%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Country:</b></td>
    <td><input type='text' name='country' value='%%country%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Region:</b></td>
    <td><input type='text' name='region' value='%%region%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Type:</b></td>
    <td>[[:schools::selecttype::default=%%type%%::varname=type:]]</td>
  </tr>
  <tr>
    <td><b>Hidden:</b></td>
    <td>
		<select name='hidden'>
			<option value='%%hidden%%'>%%hidden%%</option>
			<option value='yes'>yes</option>
			<option value='no'>no</option>
		</select>
	</td>
  </tr>
  <tr>
    <td><b>Notify:</b></td>
    <td>
		<select name='notifyAll'>
			<option value='%%notifyAll%%'>%%notifyAll%%</option>
			<option value='members'>Announcements sent to members only (members)</option>
			<option value='global'>Announcements sent to all users (global)</option>
		</select>
	</td>
  </tr>
</table>
<br/>
<b>Description of this school:</b><br/>

<div
	class='HyperTextArea64'
	title='description'
	width='-1'
	height='400'
	style='visibility: hidden; display: none'
	refModule='schools'
	refModel='schools_school'
	refUID='%%UID%%'
>%%description64%%</div>
<script language='Javascript'> khta.convertDivs(); </script>
</form>

<table noborder>
  <tr>
   <td valign='top'>
    <input
		type='button'
		value='Save'
		onClick="$('#frmEditSchool%%UID%%').submit();" />
   </td>
   <td valign='top'>
     [[:tags::editbutton::refModule=schools::refModel=schools_school::refUID=%%UID%%:]]
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
