<?php
module_load_include('php', 'cel_5d_course_registration', 'entities/user_drupal');

/**
 * Entity definition for Purchaser conent type
 * Some enity data are storred into profile page 
 * and some user infor storred into a module table
 * 
 * @author vparfaniuc
 *        
 *        
 */

class Purchaser extends UserDrupal {
	private $daytime_phone;
	private $preferred_name;
	private $updated;
	private $purchaser_licence;
	
	/**
	 * @return the $daytime_phone
	 */
	public function getDaytime_phone() {
		return $this->daytime_phone;
	}

	/**
	 * @param field_type $daytime_phone
	 */
	public function setDaytime_phone($daytime_phone) {
		$this->daytime_phone = $daytime_phone;
	}

	/**
	 * @return the $preferred_name
	 */
	public function getPreferred_name() {
		return $this->preferred_name;
	}

	/**
	 * @param field_type $preferred_name
	 */
	public function setPreferred_name($preferred_name) {
		$this->preferred_name = $preferred_name;
	}

	/**
	 * @return the $updated
	 */
	public function getUpdated() {
		return $this->updated;
	}

	/**
	 * @param field_type $updated
	 */
	public function setUpdated($updated) {
		$this->updated = $updated;
	}

	/**
	 * @return the $purchaser_licence
	 */
	public function getPurchaser_licence() {
		return $this->purchaser_licence;
	}

	/**
	 * @param field_type $purchaser_licence
	 */
	public function setPurchaser_licence($purchaser_licence) {
		$this->purchaser_licence = $purchaser_licence;
	}

	
	
}

