<? /*
<form
	id='frmEditEntry%%UID%%'
	name='editCalendar'
	method='POST'
	action='%%serverPath%%calendar/save/'
	onSubmit="khta.updateAllAreas();"
>

<input type='hidden' name='action' value='saveCalendar' />
<input type='hidden' name='UID' value='%%UID%%' />

<table noborder>
  <tr>
    <td><b>Title:</b></td>
    <td><input type='text' name='title' value='%%title%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Venue:</b></td>
    <td><input type='text' name='venue' value='%%venue%%' size='50' /></td>
  </tr>
  <tr>
    <td><b>Times:</b></td>
    <td>
    <input type='text' name='eventStart' value='%%eventStart%%' size='5' /> 
    Start (hh:mm)
    <input type='text' name='eventEnd' value='%%eventStart%%' size='5' /> 
    End (hh:mm)
    </td>
  </tr>
  <tr>
    <td><b>Category:</b></td>
    <td>
      <select name='category'/>
        <option value='%%category%%'>%%category%%</option>
        <option value='Government'>Government</option>
        <option value='Entertainment'>Entertainment</option>
        <option value='Education'>Education</option>
        <option value='Astronomy'>Astronomy</option>
        <option value='Astronomy'>Sports</option>
        <option value='Astronomy'>Festival</option>
        <option value='Astronomy'>Wildlife</option>
        <option value='Astronomy'>Outdoors</option>
        <option value='Astronomy'>Official Visit</option>
      </select>
    </td>
  </tr>
  <tr>
    <td><b>Date:</b></td>
    <td>
       <input type='text' name='year' value='%%year%%' size='4' /> (yyyy) 
       <input type='text' name='month' value='%%month%%' size='4' /> (mm) 
       <input type='text' name='day' value='%%day%%' size='4' /> (dd) 
    </td>
  </tr>
</table>
<br/>
<b>Description of event:</b><br/>
<div
	class='HyperTextArea64'
	title='content'
	width='-1'
	height='400'
	style='visibility: hidden; display: none'
	refModule='calendar'
	refModel='calendar_entry'
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
		onClick="$('#frmEditEntry%%UID%%').submit();" />
   </td>
   <td>
   <form name='cDelete' method='GET' action='%%delUrl%%'>
   <input type='submit' value='Delete' />
   </form>
   </td>
 </tr>
</table>

*/ ?>
