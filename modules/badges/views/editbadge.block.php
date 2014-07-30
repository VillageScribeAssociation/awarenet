<? /*

<form name='editBadge' method='POST' action='%%serverPath%%badges/save/'>
    <input type='hidden' name='action' value='saveBadge' />
    <input type='hidden' name='UID' value='%%UID%%' />
    <table noborder='noboder'>
    <tr>
        <td><b>name</b></td>
        <td><input type='text' name='name' value='%%name%%' /></td>
    </tr>
    </table>
<b>description:</b><br/>
<div class='HyperTextArea64' title='description' width='100%' height='400'>
%%description64%%
</div>
<script language='Javascript'> khta.convertDivs(); </script>

<table noborder>
  <tr>
    <td><input type='submit' value='Save' />
</form></td>
    <td>
      <form name='cancelBadge%%UIDJsClean%%' method='GET' action='%%serverPath%%badges/show/%%UID%%'>
        <input type='hidden' name='action' value='deleteBadge' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Cancel' />
      </form>
    </td>
    <td>
      <form name='cancelBadge%%UIDJsClean%%' method='POST' action='%%serverPath%%badges/confirmdelete/'>
        <input type='hidden' name='action' value='deleteBadge' />
        <input type='hidden' name='UID' value='%%UID%%' />
        <input type='submit' value='Delete' />
      </form>
    </td>
  </tr>
</table>

<h2>Images</h2>
[[:images::uploadmultiple::refModule=badges::refModel=badges_badge::refUID=%%UID%%:]]

*/ ?>
