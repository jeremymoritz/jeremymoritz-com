<?php

define('WAR','Coded by Kyle Keith (kkeith29@cox.net)');

function config_get( $idx,$retval='' ) {
	global $__CONF;
	$idx = ( is_array( $idx ) ? $idx[1] : $idx );
	if ( isset( $__CONF[$idx] ) ) {
		return ( is_string( $__CONF[$idx] ) ? preg_replace_callback( '/:([A-Za-z_]+):/','config_get',$__CONF[$idx] ) : $__CONF[$idx] );
	}
	return $retval;
}

function config_is_set( $idx ) {
	global $__CONF;
	return isset( $__CONF[$idx] );
}

function config_set( $idx,$value ) {
	global $__CONF;
	$__CONF[$idx] = $value;
	return true;
}

function config_remove( $idx ) {
	global $__CONF;
	if ( isset( $__CONF[$idx] ) ) {
		unset( $__CONF[$idx] );
		return true;
	}
	return false;
}

$__CONF = array();

$__CONF['PRODUCTION'] = true;
$__CONF['TIMEZONE'] = 'America/Chicago';

$__CONF['ROOT_DIR'] = str_replace( "\\",'/',dirname( dirname(__FILE__) ) );
$__CONF['PUBLIC_DIR'] = ':ROOT_DIR:';
$__CONF['WAR_DIR'] = ':ROOT_DIR:/war';
$__CONF['WAR_CLASS_DIR'] = ':WAR_DIR:/classes';
$__CONF['WAR_LOG_DIR'] = ':WAR_DIR:/logs';
$__CONF['WAR_TEMPLATE_DIR'] = ':WAR_DIR:/templates';

$__CONF['LOG_PHP'] = 'php.log';

$__CONF['URL'] = 'http://dev.lifeboatcreative.com/war';

$__CONF['SESSION_SAVE_PATH'] = ':WAR_DIR:/sessions';

$__CONF['TEMPLATE_VARS'] = array(
	'base_url' => rtrim( config_get('URL'),'/' ) . '/'
);

date_default_timezone_set(config_get('TIMEZONE'));

include config_get('WAR_CLASS_DIR') . '/class.autoloader.php';

spl_autoload_register('autoloader::load');

error::init();

?>