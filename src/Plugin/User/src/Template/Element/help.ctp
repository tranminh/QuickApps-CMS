<h2>About</h2>
<p>
	The User plugin allows users to register, log in, and log out.
	It also allows users with proper permissions to manage user roles (used to classify users) and permissions associated with those roles.
</p>

<h2>Uses</h2>
<dl>
	<dt>User roles and permissions</dt>
	<dd>
		Roles are used to group and classify users; each user can be assigned one or more roles.
		By default there are three roles: anonymous user (users that are not logged in), authenticated user (users that are registered and logged in) and administrator (which has unrestricted access to whole the system).
		After creating roles, you can set permissions for each role on the <?php echo $this->Html->link('Permissions page', ['plugin' => 'user', 'controller' => 'permissions']); ?>.
		Granting a permission allows users who have been assigned a particular role to perform an action on the site, such as editing or creating content, administering settings for a particular module,
		or using a particular function of the site (such as search).
	</dd>
</dl>