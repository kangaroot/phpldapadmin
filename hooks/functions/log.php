<?php
/**
 * Log stuff.
 *
 * @author Dieter Plaetinck, Kangaroot. dieter@kangaroot.net
 * @package phpLDAPadmin
 */

// opens the logfile at the beginning of the pageload
// note that we don't explicitly close the file again. we rely on php to do that for us
// (if there would be a shutdown/exit/stop hook, we could use that)
function init_logfile () {
	global $changes_log;
	$file = $_SESSION[APPCONFIG]->getValue('log','file');
	$changes_log = fopen($file, 'a') or die("can't open file $file");
	return true;
}
add_hook('post_session_init','init_logfile');


function log_change ($str) {
	global $changes_log;
	if (!fwrite ($changes_log, date ('Y-m-d H:i:s') . ': ' .  $str . "\n")) {
		return false;
	}
	return true;
}

function log_login() {
	$args = func_get_args();
	return log_change ("User Login: Name: " . ($args[0] ? $args[0] : '<anonymous>') . ', ' . ( $args[1] ? 'success' : 'failed'));
}

function log_logout() {
	$args = func_get_args();
	return log_change ("User Logout: Name: " . ($args[0] ? $args[0] : '<anonymous>') . ', ' . ( $args[1] ? 'success' : 'failed'));
}

function log_entry_created() {
	$args = func_get_args();
	return log_change ("Entry created: Server ID: " . $args[0] . ", Method: " . $args[1] . ", DN: " . $args[2] . ", Attributes: " . print_r ($args[3],true));
}

function log_entry_deleted() {
	$args = func_get_args();
	return log_change ("Entry deleted: Server ID: " . $args[0] . ", Method: " . $args[1] . ", DN: " . $args[2]);
}

function log_entry_renamed() {
	$args = func_get_args();
	return log_change ("Entry renamed: Server ID: " . $args[0] . ", Method: " . $args[1] . ", DN old -> DN new: " . $args[2] . ' -> ' . $args[3] . ", Container: " . $args[4]);
}

function log_entry_modified() {
	$args = func_get_args();
	return log_change ("Entry modified: Server ID: " . $args[0] . ", Method: " . $args[1] . ", DN: " . $args[2] . ", Attributes: " . print_r ($args[3],true));
}

function log_attr_added() {
	$args = func_get_args();
	return log_change ("Attributes added: Server ID: " . $args[0] . ", Method: " . $args[1] . ", DN: " . $args[2] . ", Attribute: " . $args[3] . ", Values: " . print_r($args[4],true));
}

function log_attr_modified() {
	$args = func_get_args();
	return log_change ("Attributes modified: Server ID: " . $args[0] . ", Method: " . $args[1] . ", DN: " . $args[2] . ", Attribute: " . $args[3] . ", Values: " . print_r($args[4],true));
}

function log_attr_deleted() {
	$args = func_get_args();
	return log_change ("Attributes deleted: Server ID: " . $args[0] . ", Method: " . $args[1] . ", DN: " . $args[2] . ", Attribute: " . $args[3] . ", Values: " . print_r($args[4],true));
}

add_hook('post_login',        'log_login');
add_hook('post_logout',       'log_logout');
add_hook('post_entry_create', 'log_entry_created');
add_hook('post_entry_delete', 'log_entry_deleted');
add_hook('post_entry_rename', 'log_entry_renamed');
add_hook('post_entry_modify', 'log_entry_modified');
add_hook('post_attr_add',     'log_attr_added');
add_hook('post_attr_modify',  'log_attr_modified');
add_hook('post_attr_delete',  'log_attr_deleted');
?>
