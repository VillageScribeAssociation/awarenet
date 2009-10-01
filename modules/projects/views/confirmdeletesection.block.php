<? /*
[[:theme::navtitlebox::width=570::label=Confirm:]]
<div class='inlinequote'>
Confirm: you wish to delete this section?<br/><br/>
<table noborder>
  <tr>
    <td valign='top'>
      <form name='confirmDelete' method='POST' action='/projects/deletesection/'>
      <input type='hidden' name='action' value='deleteSection' />
      <input type='hidden' name='UID' value='%%UID%%' />
      <input type='hidden' name='section' value='%%section%%' />
      <input type='submit' value='Yes: Delete it' />
    </form>
    </td>
    <td valign='top'>
    <form name='confirmDelete' method='POST' 
	  action='/projects/editsection/section_%%section%%/%%raUID%%/'>
	  <input type='submit' value='No: Cancel' />
    </form>
    </td>
  </tr>
</table>
</div>
*/ ?>
