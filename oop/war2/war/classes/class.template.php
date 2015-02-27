<?php

//very basic template class

class template {

	private $file = null;
	private $path = null;
	private $vars = array();

	public function __construct( $file ) {
		$this->file = $file;
		$this->path = config_get('WAR_TEMPLATE_DIR') . "/{$this->file}.tpl";
		if ( !file_exists( $this->path ) ) {
			error::trigger("Template '{$this->file}' does not exist");
		}
		if ( config_is_set('TEMPLATE_VARS') ) {
			$this->vars = config_get('TEMPLATE_VARS');
		}
	}

	public function set_var( $key,$value ) {
		$this->data[$key] = $value;
	}

	public function set_vars( $vars,$clear=false ) {
		if ( !is_array( $vars ) ) {
			error::trigger('Parameter is not an array');
		}
		if ( $clear == true ) {
			$this->vars = $vars;
		}
		$this->vars = array_merge( $this->vars,$vars );
	}

	public function get() {
		foreach( $this->vars as $_var => $_data ) {
			${$_var} = $_data;
		}
		ob_start();
		include $this->path;
		$data = ob_get_contents();
		ob_end_clean();
		return $data;
	}

	public static function fetch( $file,$vars=array() ) {
		$template = new template( $file );
		$template->set_vars( $vars );
		return $template->get();
	}

	public static function output( $file,$vars=array() ) {
		echo template::fetch( $file,$vars );
	}

}

?>