<? /*
<module>
    <modulename>projects</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>Projects module, allow multiple users to collaborate on a document..</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>no</enabled>
    <search>no</search>
    <dependencies>
		<module>aliases</module>
		<module>images</module>
		<module>comments</module>
    </dependencies>
    <models>
        <model>
            <name>Projects_Membership</name>
            <description>Relationship object associating a user with a project</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
            </relationships>
        </model>
        <model>
            <name>Projects_Project</name>
            <description>object for representing user projects</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
                <permission>editmembers</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
                <relationship>projectmember</relationship>
                <relationship>projectadmin</relationship>
            </relationships>
        </model>
        <model>
            <name>Projects_Section</name>
            <description>represents and individual section of the project (not abstract)</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
                <relationship>projectmember</relationship>
                <relationship>projectadmin</relationship>
            </relationships>
        </model>
        <model>
            <name>Projects_Revision</name>
            <description>stores a previous version of a project</description>
            <permissions>
                <permission>show</permission>
                <permission>edit</permission>
                <permission>delete</permission>
                <permission>new</permission>
            </permissions>
            <relationships>
                <relationship>creator</relationship>
                <relationship>projectmember</relationship>
                <relationship>projectadmin</relationship>
            </relationships>
        </model>

    </models>
    <defaultpermissions>
		student:p|projects|projects_project|show

		student:p|projects|projects_project|new
		student:p|projects|projects_project|show
		student:p|projects|projects_project|comments-add
		student:p|projects|projects_project|comments-show
		student:p|projects|projects_project|images-show
		student:c|projects|projects_project|images-remove|(if)|projectadmin
		student:c|projects|projects_project|administer|(if)|projectadmin
		student:c|projects|projects_project|edit|(if)|projectmember
		student:c|projects|projects_project|edit|(if)|projectadmin
		student:c|projects|projects_project|tags-manage|(if)|projectmember
		student:c|projects|projects_project|images-add|(if)|projectmember
		student:c|projects|projects_project|images-add|(if)|projectadmin
		student:c|projects|projects_project|edit|(if)|projectadmin
		student:p|projects|projects_revision|show

		student:p|projects|projects_section|show
		student:c|projects|projects_section|new|(if)|projectmember
		student:c|projects|projects_section|edit|(if)|projectadmin
		student:c|projects|projects_section|edit|(if)|projectmember
		student:c|projects|projects_section|edit|(if)|projectadmin
		student:c|projects|projects_section|delete|(if)|projectmember
		student:c|projects|projects_section|delete|(if)|projectadmin

		teacher:c|projects|projects_project|editmembers|(if)|projectadmin

		teacher:p|projects|projects_project|new
		teacher:p|projects|projects_project|show
		teacher:p|projects|projects_project|comments-add
		teacher:p|projects|projects_project|comments-show
		teacher:p|projects|projects_project|images-show
		teacher:c|projects|projects_project|images-remove|(if)|projectadmin
		teacher:c|projects|projects_project|administer|(if)|projectadmin
		teacher:c|projects|projects_project|edit|(if)|projectmember
		teacher:c|projects|projects_project|edit|(if)|projectadmin
		teacher:c|projects|projects_project|tags-manage|(if)|projectmember
		teacher:c|projects|projects_project|images-add|(if)|projectmember
		teacher:c|projects|projects_project|images-add|(if)|projectadmin
		teacher:c|projects|projects_project|edit|(if)|projectadmin
		teacher:p|projects|projects_revision|show

		teacher:p|projects|projects_section|show
		teacher:c|projects|projects_section|new|(if)|projectmember
		teacher:c|projects|projects_section|edit|(if)|projectadmin
		teacher:c|projects|projects_section|edit|(if)|projectmember
		teacher:c|projects|projects_section|edit|(if)|projectadmin
		teacher:c|projects|projects_section|delete|(if)|projectmember
		teacher:c|projects|projects_section|delete|(if)|projectadmin

		teacher:p|projects|projects_project|editmembers

    </defaultpermissions>
</module>
*/ ?>
