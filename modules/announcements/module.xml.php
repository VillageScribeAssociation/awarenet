<? /*
<module>
    <modulename>announcements</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>Announcements module, dependant on other modules (schools, groups, etc make announcements).</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>no</enabled>
    <dbschema></dbschema>
    <search>no</search>
    <dependancy>
    </dependancy>
    <permissions>
        <perm>show|%%user.ofGroup%%=student</perm>
        <perm>show|%%user.ofGroup%%=teacher</perm>
        <perm>list|%%user.ofGroup%%=student</perm>
        <perm>list|%%user.ofGroup%%=teacher</perm>
        <perm>summarylist|%%user.ofGroup%%=student</perm>
        <perm>summarylist|%%user.ofGroup%%=teacher</perm>
        <perm>summary|%%user.ofGroup%%=student</perm>
        <perm>summary|%%user.ofGroup%%=teacher</perm>
        <perm>comment|%%user.ofGroup%%=student</perm>
        <perm>comment|%%user.ofGroup%%=teacher</perm>
        <perm>edit|%%user.ofGroup%%=admin</perm>
        <perm>edit|%%user.ofGroup%%=teacher</perm>
        <perm>edit|%%user.ofGroup%%=student</perm>
        <perm>images|%%user.ofGroup%%=student</perm>
        <perm>images|%%user.ofGroup%%=teacher</perm>
        <perm>imageupload|%%user.ofGroup%%=student</perm>
        <perm>imageupload|%%user.ofGroup%%=teacher</perm>
    </permissions>
    <blocks>
    </blocks>
</module>
*/ ?>
