<? /*
[[:theme::navtitlebox::label=Ask to Join Project:]]
<div id='askProjectNav'>
If you would like to join you can <a href='#' onClick='askJoinProjectNav();'>ask</a> the people whose project this is.<br/>
</div>
<script>
  function askJoinProjectNav() {
    theAskDiv = document.getElementById('askProjectNav');
    askForm = "<form name='askJoin' method='POST' action='%%serverPath%%projects/addme/'>"
            + "<input type='hidden' name='action' value='askToJoin' />" 
            + "<input type='hidden' name='UID' value='%%projectUID%%' />"
            + "<h2>Message to Project Admins</h2>"
            + "<textarea name='message' rows='4' cols='36'>"
            + "Hi!  I'd like to join your project.  I think it's "
            + "interesting and would like to participate.\n\n%%userName%%"
            + "</textarea><br/>"
            + "<input type='submit' value='Make Request' />"
            + "<input type='button' onClick='hideAskForm()' value='Hide Form'/>"
            + "</form>";

    theAskDiv.innerHTML = askForm;
  }

  function hideAskForm() {
    theAskDiv = document.getElementById('askProjectNav');
    askForm = "If you would like to join you can "
            + "<a href='#' onClick='askJoinProjectNav();'>ask</a> "
            + "the people whose project this is.<br/>";

    theAskDiv.innerHTML = askForm;
  }
</script>
<br/>
*/ ?>
