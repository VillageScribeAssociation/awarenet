<? /*

[[:theme::navtitlebox::label=Edition:]]
<form name='editNotice%%UID%%' method='POST' action='%%serverPath%%newsletter/savenotice/'>
    <input type='hidden' name='action' value='saveNotice' />
    <input type='hidden' name='UID' value='%%UID%%' />
	<table noborder='noboder' width='100%'>
    <tr>
        <td><b>title</b></td>
        <td><input type='text' name='title' value='%%title%%' style='width: 100%;'/></td>
    </tr>
    <tr>
        <td><b>category</b></td>
        <td>[[:newsletter::selectcategory::default=%%category%%:]]</td>
    </tr>
    </table>

	<div
		class='HyperTextArea64'
		title='content'
		width='570'
		height='300'
		style='visibility: hidden; display: none'
		refModule='newsletter'
		refModel='newsletter_edition'
		refUID='%%edition%%'
	>%%content64%%</div>
	<script language='Javascript'> khta.convertDivs(); </script>
	<br/>
</form>
<table noborder>
  <tr>
    <td><input type='button' value='Save' onClick='area.update(); document.editNotice%%UID%%.submit()'></td>
    <td>
       <input type='button' value='Cancel' onClick='kwnd.closeWindow();' />
    </td>
    <td>
      <form name='cancelNotice%%UID%%' method='POST' action='%%serverPath%%newsletter/deletenotice/'>
        <input type='hidden' name='action' value='deleteNotice' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>


*/ ?>
