<? /*

<module>
    <modulename>users</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>For managing users, login, etc.</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>yes</enabled>
    <search>no</search>
    <dependencies>
        <module>aliases</module>
        <module>images</module>
    </dependencies>
    <models>
      <model>
        <name>Users_User</name>
		<description></description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
          <export>removeasfriend</export>
          <export>addasfriend</export>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
		  <relationship>friend</relationship>
		  <relationship>classmate</relationship>
		  <relationship>schoolmate</relationship>
        </relationships>
      </model>
      <model>
        <name>Users_Role</name>
		<description>Container for permissions.</description>
        <permissions>
          <permission>new</permission>
          <permission>show</permission>
          <permission>edit</permission>
          <permission>delete</permission>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
		  <relationship>member</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		student:p|users|users_user|viewprofile
		student:p|users|users_user|comments-add
		student:c|users|users_user|images-add|(if)|self
		student:p|users|users_user|comments-add
		student:p|users|users_user|comments-show
		student:p|users|users_user|comments-list
		student:c|users|users_user|comments-retract|(if)|creator
		student:p|users|users_user|show
		student:c|users|users_user|edit|(if)|creator
		student:c|users|users_user|edit|(if)|self
		student:c|users|users_user|editprofile|(if)|self
		student:p|users|users_user|viewprofile

		teacher:p|users|users_user|comments-add
		teacher:c|users|users_user|images-add|(if)|self
		teacher:p|users|users_user|comment-add
		teacher:c|users|users_user|comment-retract|(if)|creator
		teacher:p|users|users_user|show
		teacher:c|users|users_user|edit|(if)|creator
		teacher:c|users|users_user|edit|(if)|self
		teacher:c|users|users_user|editprofile|(if)|self
		teacher:p|users|users_user|viewprofile
    </defaultpermissions>
    <blocks>
    </blocks>
</module>

*/ ?>
