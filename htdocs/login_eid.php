<?php
/**
 * Perform the login logic for users with client SSL certificates.  (In our case hosted on Eid/smartcards)
 *
 * @author Dieter Plaetinck, Kangaroot
 * @package phpLDAPadmin
 */

require './common.php';

printf('<h3 class="title">%s %s %s</h3>',_('Authenticate to server'),$app['server']->getName(),_(' using your EID'));
echo '<br />';

$server = $_SESSION[APPCONFIG]->getServer(get_request('server_id','REQUEST'));

function getuser () {
	$user = array ();
	global $server;
	if(!@$_SERVER['SSL_CLIENT_S_DN']) {
		$login = '<unknown>';
	} else {
		$login = preg_replace ('/^.*serialNumber=/', '', $_SERVER['SSL_CLIENT_S_DN']);
		if (!$login) {
			$login = '<unknown>';
		}
	}
	$user['login']    = sprintf($server->getValue ('login', 'cert_userspec'), $login);
	$user['password'] = $server->getValue ('login', 'cert_master_password');

	return $user;
}


$user = getuser();

if (@$_SERVER['SSL_CLIENT_VERIFY'] != "SUCCESS") {
        if (function_exists('run_hook'))
                run_hook('post_login',array('user' => $user['login'], 'success'=> false));
        system_message(array(
                'title'=>_('Failed to Authenticate to server'),
                'body'=>_("Your browser should have prompted you for your pin code, which upon entry should log you in.
		           <br>If it didn't, check that:
			   <ul>
			   <li>You are connecting over https</li>
			   <li>The eID middleware and browser addon are correctly installed</li>
			   <li>Your eID is in the reader and you entered the correct PIN</li>
			   <li>The webserver has been configured correctly</li>
			   </ul>
			   You can try again by going back to the login page"),
                'type'=>'error'),
                sprintf('cmd.php?&server_id=%s',get_request('server_id','REQUEST')));
}
if ($server->login($user['login'],$user['password'],'user')) {
        if (function_exists('run_hook'))
                run_hook('post_login',array('user' => $user['login'], 'success'=> true));
        system_message(array(
                'title'=>_('Authenticate to server'),
                'body'=>_('Successfully logged into server.'),
                'type'=>'info'),
                sprintf('cmd.php?server_id=%s&refresh=SID_%s',$server->getIndex(),$server->getIndex()));
} else {
	if (function_exists('run_hook'))
		run_hook('post_login',array('user' => $user['login'], 'success'=> false));
	system_message(array(
		'title'=>_('Failed to Authenticate to server'),
		'body'=>_('Your eID (certificate) is succesfully unlocked,
		           <br>but we cannot find your username (' . $user['login'] . ')
			   <br>in our users database for the configured password'),
		'type'=>'error'),
		sprintf('cmd.php?&server_id=%s',get_request('server_id','REQUEST')));
}

?>
