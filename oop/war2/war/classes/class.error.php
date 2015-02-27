<?php

abstract class error {

	private static $display_styles = true;

	public static function init() {
		set_error_handler('error::error_handler');
		error_reporting( E_ALL | E_STRICT );
		$config = array(
			'display_errors' => ( config_get('PRODUCTION') === true ? 'Off' : 'On' ),
			'log_errors' => 'On',
			'error_log' => config_get('WAR_LOG_DIR') . '/' . config_get('LOG_PHP')
		);
		if ( !is_writeable( $config['error_log'] ) ) {
			self::trigger('Error log is not writeable');
		}
		foreach( $config as $name => $value ) {
			ini_set( $name,$value );
		}
	}

	public static function error_handler( $number,$message,$file,$line,$context ) {
		$error_reporting = error_reporting();
		if ( !( $error_reporting & $number ) ) {
			return;
		}
		$full = true;
		$stop = false;
		switch( $number ) {
			case E_ERROR:
			case E_USER_ERROR:
			case E_RECOVERABLE_ERROR:
				$type = 'Fatal Error';
				$stop = true;
			break;
			case E_WARNING:
			case E_USER_WARNING:
				$type = 'Warning';
				$full = false;
			break;
			case E_NOTICE:
			case E_USER_NOTICE:
				$type = 'Notice';
				$full = false;
			break;
			case E_DEPRECATED:
			case E_USER_DEPRECATED:
				$type = 'Deprecated';
				$full = false;
			break;
			default:
				$type = 'Unknown Error';
				$stop = true;
			break;
		}
		self::log("{$type}: {$message} in file {$file} on line: {$line}");
		if ( config_get('PRODUCTION') === true ) {
			if ( $stop == true ) {
				template::output('errors/technical_difficulties');
				exit;
			}
			return;
		}
		template::output('errors/error',array(
			'full'      => $full,
			'display_styles' => self::$display_styles,
			'type'      => $type,
			'message'   => $message,
			'file'      => $file,
			'line'      => $line,
			'backtrace' => array()
		));
		self::$display_styles = false;
		if ( $stop == true ) {
			exit;
		}
	}

	public static function trigger( $message,$type=E_USER_ERROR ) {
		trigger_error( $message,$type );
	}

	public static function log( $message ) {
		error_log( $message );
	}

}

?>