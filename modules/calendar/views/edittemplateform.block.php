<? /*

<form name='editTemplate%%UID%%' id='formET%%UID%%' method='POST' action='%%serverPath%%calendar/savetemplate/'>
    <input type='hidden' name='UID' value='%%UID%%' />
    <table noborder='noboder'>
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
<b>content:</b><br/>
<div class='HyperTextArea64' title='content' width='100%' height='400'>
%%content64%%
</div>
<script language='Javascript'> khta.convertDivs(); </script>
</form>
<table noborder>
  <tr>
		<td><input 
			type='button'
			value='Save Template' 
			onClick="
				var theForm = document.getElementById('formET%%UID%%');
				theForm.action = '%%serverPath%%calendar/savetemplate/';
				khta.updateAllAreas();
				theForm.submit();
			">
	</td>
    <td>
      <form name='cancelTemplate%%UIDJsClean%%' method='POST' action='%%serverPath%%calendar/confirmdeletetemplate/UID_%%UID%%'>
        <input type='hidden' name='action' value='deleteTemplate' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete Template' />
      </form>
    </td>
    <td><input 
			type='button'
			value='Apply Template &gt;&gt;' 
			onClick="
				var theForm = document.getElementById('formET%%UID%%');
				theForm.action = '%%serverPath%%calendar/applytemplate/';
				area.update();
				theForm.submit();
			"
		></td>
  </tr>
</table>


*/ ?>
