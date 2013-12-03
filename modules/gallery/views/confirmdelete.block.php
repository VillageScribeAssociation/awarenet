<? /*
<b>Confirm: you wish to delete this image gallery?</b><br/>
<p>Note that all pictures it contains, along with comments, tags, etc will also be deleted.</p>
<table noborder>
  <tr>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%gallery/delete/'>
    <input type='hidden' name='action' value='deleteRecord' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <input type='submit' value='Yes: Delete it' />
    </form>
    </td>
    <td valign='top'>
    <form name='cancelDelete' method='POST' action='%%serverPath%%gallery/%%raUID%%/'>
    <input type='submit' value='No: Cancel' />
    </form>
    </td>
  </tr>
</table>
*/ ?>
