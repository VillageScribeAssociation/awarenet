<? /*

<module>
    <modulename>users</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>For managing users, login, etc.</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <dbschema>yes</dbschema>
    <search>no</search>
    <dependency>
        <depend>geocodes|1.0</depend>
        <depend>avatar|1.0</depend>
        <depend>files|1.0</depend>
        <depend>images|1.0</depend>
    </dependency>
    <permissions>
	<perm>view|%%user.ofGroup%%=student</perm>
	<perm>view|%%user.ofGroup%%=teacher</perm>
	<perm>list|%%user.ofGroup%%=user</perm>
	<perm>list|%%user.ofGroup%%=student</perm>
	<perm>list|%%user.ofGroup%%=teacher</perm>
	<perm>summarylist|%%user.ofGroup%%=student</perm>
	<perm>summarylist|%%user.ofGroup%%=teacher</perm>
	<perm>summary|%%user.ofGroup%%=student</perm>
	<perm>summary|%%user.ofGroup%%=teacher</perm>
	<perm>delete|%%user.ofGroup%%=admin</perm>
	<perm>new|%%user.ofGroup%%=admin</perm>
	<perm>edit|%%user.ofGroup%%=admin</perm>
	<perm>viewprofile|%%user.ofGroup%%=user</perm>
	<perm>viewprofile|%%user.ofGroup%%=student</perm>
	<perm>viewprofile|%%user.ofGroup%%=teacher</perm>
	<perm>comment|%%user.ofGroup%%=user</perm>
	<perm>comment|%%user.ofGroup%%=student</perm>
	<perm>comment|%%user.ofGroup%%=teacher</perm>
	<perm>images|%%user.ofGroup%%=user</perm>
	<perm>images|%%user.ofGroup%%=student</perm>
	<perm>images|%%user.ofGroup%%=teacher</perm>
	<perm>imageupload|%%user.ofGroup%%=user</perm>
	<perm>imageupload|%%user.ofGroup%%=student</perm>
	<perm>imageupload|%%user.ofGroup%%=teacher</perm>
    </permissions>
    <blocks>
    </blocks>
</module>

*/ ?>
