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
		student:p|users|Users_User|viewprofile
		student:p|users|Users_User|comments-add
		student:c|users|Users_User|images-add|(if)|self
		student:p|users|Users_User|comments-add
		student:p|users|Users_User|comments-show
		student:p|users|Users_User|comments-list
		student:c|users|Users_User|comments-retract|(if)|creator
		student:p|users|Users_User|show
		student:c|users|Users_User|edit|(if)|creator
		student:c|users|Users_User|edit|(if)|self
		student:c|users|Users_User|editprofile|(if)|self
		student:p|users|Users_User|viewprofile

		teacher:p|users|Users_User|comments-add
		teacher:c|users|Users_User|images-add|(if)|self
		teacher:p|users|Users_User|comment-add
		teacher:c|users|Users_User|comment-retract|(if)|creator
		teacher:p|users|Users_User|show
		teacher:c|users|Users_User|edit|(if)|creator
		teacher:c|users|Users_User|edit|(if)|self
		teacher:c|users|Users_User|editprofile|(if)|self
		student:p|users|Users_User|viewprofile
    </defaultpermissions>
    <blocks>
    </blocks>
</module>

*/ ?>
