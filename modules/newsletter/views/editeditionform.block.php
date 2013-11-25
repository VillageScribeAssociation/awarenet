<? /*

<form name='editEdition%%UID%%' method='POST' action='%%serverPath%%newsletter/saveedition/'>
    <input type='hidden' name='action' value='saveEdition' />
    <input type='hidden' name='UID' value='%%UID%%' />
	<table noborder='noboder' width='100%'>
    <tr>
        <td><b>subject</b></td>
        <td><input type='text' name='subject' value='%%subject%%' style='width: 100%;'/></td>
    </tr>
    </table>
<div class='HyperTextArea64' title='abstract' width='570' height='400' style='visibility: hidden; display: none'>%%abstract64%%</div>
<script language='Javascript'> khta.convertDivs(); </script>
<br/>
</form>
<table noborder>
  <tr>
    <td><input type='button' value='Save' onClick='area.update(); document.editEdition%%UID%%.submit()'></td>
    <td>
      <form name='cancelEdition%%UID%%' method='GET' action='%%serverPath%%newsletter/showedition/%%UID%%'>
        <input type='hidden' name='action' value='cancelEditEdition' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Cancel' />
      </form>
    </td>
    <td>
      <form name='deleteEdition%%UID%%' method='POST' action='%%serverPath%%newsletter/confirmdeleteedition/'>
        <input type='hidden' name='action' value='deleteEdition' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
	<td><b>%%status%%</b></td>
  </tr>
</table>


*/ ?>
