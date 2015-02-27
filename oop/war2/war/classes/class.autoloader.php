<?php

if ( !defined('WAR') ) {
	die('Direct script access is not allowed');
}

abstract class autoloader {

	private function __construct() {}

	public static function load( $class,$test=false ) {
		$name = $class;
		$path = null;
		if ( strpos( $name,'__' ) !== false ) {
			$path = explode( '__',$name );
			$name = array_pop( $path );
		}
		$oname = $name;
		$path = ( !is_null( $path ) ? implode( '/',$path ) . '/' : '' );
		$type = false;
		if ( ( $pos = strrpos( $name,'_' ) ) !== false ) {
			$type = substr( $name,( $pos + 1 ),strlen( $name ) );
			$name = substr( $name,0,$pos );
		}
		switch( $type ) {
			case 'interface':
				$file = config_get('WAR_INTERFACE_DIR') . "/{$path}{$type}.{$name}.php";
			break;
			default:
				$file = config_get('WAR_CLASS_DIR') . "/{$path}class.{$oname}.php";
			break;
		}
		if ( file_exists( $file ) ) {
			if ( $test == true ) {
				return true;
			}
			include $file;
			return true;
		}
		elseif ( $test == true ) {
			return false;
		}
		trigger_error( "Unable to load class '{$class}'",E_USER_ERROR );
	}

}

?>