<?php	//	classes

class person {	//	class (blueprint for objects [called "instances"])
	//	PROPERTIES
	var $name;	//	public (by default)
	public $height;	//	anyone can access the property
	protected $social_insurance;	//	only the same class and classes derived from that class can access the property
	private $pinn_number;	//	only the same class can access the property.

	
	//	CONSTRUCTOR (to be run at instantiation time)
	function __construct($persons_name = null) {	//	optionally set properties at time of instantiation (default null)
		$this->name = $persons_name;
	}
	
	//	DESTRUCTOR	(to be run at the time instance is unset)
	
	//	METHODS
	public function set_name($new_name) {
		$this->name = strtoupper($new_name);
	}
	public function get_name() {
		return $this->name;
	}
	
	private function get_pinn_number() {	//	private method (can only be used by other methods in this class; not outside)
		return $this->pinn_number;  
	}       
}

class employee extends person {	//	"extends" = has the properties of the "base class" or "parent class" (i.e. "employee" is a TYPE of "person")
	function __construct($employee_name = null) {
		$this->set_name($employee_name);
	}
		
	public function set_name($new_name) {	//	redeclaring same method in child class OVERRIDES the method in parent
		if ($new_name == "Stefan Sucks") {
			$this->name = $new_name;
		} elseif ($new_name == "Johnny Fingers") {
			person::set_name($new_name);	//	revert back to the parent's method instead of the child's method
			//	parent::set_name($new_name);	//	could also use "parent::"
		}
	}
}
	
/*  NOTES:
	You restrict access to class properties using something called 'access modifiers'. There are 3 access modifiers:

	public
	private
	protected

	'Public' is the default modifier. (e.g. if declared with var)
*/
?>
