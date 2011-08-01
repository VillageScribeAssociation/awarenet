<? /*
<b>Confirm: you wish to delete this video gallery?</b><br/>
<p>Note that all media it contains, along with comments, tags, etc will also be deleted.</p>
<table noborder>
  <tr>
    <td valign='top'>
    <form name='confirmDelete' method='POST' action='%%serverPath%%videos/deletegallery/'>
    <input type='hidden' name='action' value='deleteRecord' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <input type='submit' value='Yes: Delete it' />
    </form>
    </td>
    <td valign='top'>
    <form name='cancelDelete' method='POST' action='%%serverPath%%videos/showgallery/%%raUID%%/'>
    <input type='submit' value='No: Cancel' />
    </form>
    </td>
  </tr>
</table>
*/ ?>
