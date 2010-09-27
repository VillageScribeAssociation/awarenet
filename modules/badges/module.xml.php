<? /*
<?xml version="1.0" encoding="UTF-8" ?>
<module>
    <modulename>badges</modulename>
    <version>0</version>
    <revision>0</revision>
    <description>User trophies for achieving some task or status.</description>
    <core>no</core>
    <dependencies>
        <module>aliases</module>
        <module>images</module>
    </dependencies>
    <models>
        <model>
            <name>Badge</name>
            <description>A placeholder object for the badge itself.</description>
            <permissions>
				<permission>show</permission>
				<permission>edit</permission>
				<permission>delete</permission>
				<permission>new</permission>
				<permission>award</permission>
				<permission>revoke</permission>
            </permissions>
            <relationships>
				<relationship>creator</relationship>
				<relationship>recipient</relationship>
            </relationships>
        </model>
        <model>
            <name>UserIndex</name>
            <description>Associates users with their badges.</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
            </relationships>
        </model>
    </models>
    <defaultpermissions>
		student:p|badges|Badges_Badge|show

		teacher:p|badges|Badges_Badge|new
		teacher:p|badges|Badges_Badge|edit
		teacher:p|badges|Badges_Badge|show
		teacher:p|badges|Badges_Badge|award
		teacher:p|badges|Badges_Badge|revoke
    </defaultpermissions>
    <blocks>
    </blocks>
</module>
*/ ?>
