<? /*
<module>
    <modulename>notifications</modulename>
    <version>1.0</version>
    <revision>0</revision>
    <description>Notifications are small messages to allow user to keep track of events on the system.</description>
    <core>yes</core>
    <installed>no</installed>
    <enabled>no</enabled>
    <search>no</search>
    <models>
      <model>
        <name>Notifications_Notification</name>
		<description></description>
        <permissions>
          <permission>show</permission>
          <permission>hide</permission>
          <export>notifications-show</export>
          <export>notifications-list</export>
        </permissions>
        <relationships>
		  <relationship>creator</relationship>
		  <relationship>member</relationship>
        </relationships>
      </model>
    </models>
    <defaultpermissions>
		student:c|notifications|notifications_notification|show|(if)|member
		teacher:c|notifications|notifications_notification|show|(if)|member
    </defaultpermissions>
</module>
*/ ?>
