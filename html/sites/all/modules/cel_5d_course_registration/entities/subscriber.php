<?php

module_load_include('php', 'cel_5d_course_registration', 'entities/user_drupal');

/**
 * Subscriber Entity
 * Partial data is stored into drupal user profile fields 
 * and some other data into 5D module subscribers_info table 
 *
 * @author vparfaniuc
 *        
 *        
 */

class Subscriber extends UserDrupal {
// 	private $school_name;
	
	/**
	 * @return the $school_name
	 */
// 	public function getSchool_name() {
// 		return $this->school_name;
// 	}

	/**
	 * @param field_type $school_name
	 */
// 	public function setSchool_name($school_name) {
// 		$this->school_name = $school_name;
// 	}
}

?>