<? /*
<b>Confirm: you wish to delete this group?</b><br/>
<p>Note that all content belonging to this group will also be deleted (announcements, images, etc).</p>
<table noborder>
  <tr>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%groups/delete/'>
    <input type='hidden' name='action' value='deleteRecord' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <input type='submit' value='Yes: Delete it' />
    </form>
    </td>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%groups/%%raUID%%/'>
    <input type='submit' value='No: Cancel' />
    </form>
    </td>
  </tr>
</table>
*/ ?>
