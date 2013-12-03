<? /*
<b>Confirm: do you wish to delete this page?</b><br/>
<p>Note that this will also delete any assets attached to this page (images, tags, etc)</p>
<table noborder>
  <tr>
    <td valign='top'>
      <form name='confirmDelete' method='POST' action='%%serverPath%%static/delete/'>
      <input type='hidden' name='action' value='deleteStaticPage' />
      <input type='hidden' name='UID' value='%%UID%%'>
      <input type='submit' value='Confirm'>
      </form>
    </td>
    <td>
      <form name='cancelDelete' method='POST' action='%%serverPath%%static/%%alias%%'>
      <input type='submit' value='Cancel'>
      </form>
    </td>
  </tr>
</table>
*/ ?>
