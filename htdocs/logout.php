<?php
/**
 * Log the user out of the application.
 *
 * @package phpLDAPadmin
 * @subpackage Page
 */

/**
 */

require './common.php';

$user = $app['server']->getLogin('user');
if ($app['server']->logout()) {
	if (function_exists('run_hook'))
		run_hook('post_logout',array('user' => $user, 'success'=> true));
	system_message(array(
		'title'=>_('Authenticate to server'),
		'body'=>_('Successfully logged out of server.'),
		'type'=>'info'),
		sprintf('index.php?server_id=%s',$app['server']->getIndex()));
} else {
	if (function_exists('run_hook'))
		run_hook('post_logout',array('user' => $user, 'success'=> false));
	system_message(array(
		'title'=>_('Failed to Logout of server'),
		'body'=>_('Please report this error to the admins.'),
		'type'=>'error'),
		sprintf('index.php?server_id=%s',$app['server']->getIndex()));
}
?>
