<?php

/**
 *
 * @author vparfaniuc
 *        
 *        
 */

class UserDrupal {
	private $uid;
	private $name;
	private $pass;
	private $mail;
	private $status;
	private $roles;						// an array of rele_id => role_title vals
	private $field_first_name;
	private $field_last_name;
	private $field_current_position;
	private $field_organization;		// this is a school name in our case

	/**
	 * @return the $uid
	 */
	public function getUid() {
		return $this->uid;
	}

	/**
	 * @param field_type $uid
	 */
	public function setUid($uid) {
		$this->uid = $uid;
	}

	/**
	 * @return the $name
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param field_type $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return the $pass
	 */
	public function getPass() {
		return $this->pass;
	}

	/**
	 * @param field_type $pass
	 */
	public function setPass($pass) {
		$this->pass = $pass;
	}

	/**
	 * @return the $mail
	 */
	public function getMail() {
		return $this->mail;
	}

	/**
	 * @param field_type $mail
	 */
	public function setMail($mail) {
		$this->mail = $mail;
	}

	/**
	 * @return the $status
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * @param field_type $status
	 */
	public function setStatus($status) {
		$this->status = $status;
	}

	/**
	 * @return the $roles
	 */
	public function getRoles() {
		return $this->roles;
	}

	/**
	 * @param field_type $roles
	 */
	public function setRoles($roles) {
		$this->roles = $roles;
	}

	/**
	 * @return the $profile_first_name
	 */
	public function getProfile_first_name() {
		return $this->field_first_name;
	}

	/**
	 * @param field_type $profile_first_name
	 */
	public function setProfile_first_name($field_first_name) {
		$this->field_first_name = $field_first_name;
	}

	/**
	 * @return the $profile_last_name
	 */
	public function getProfile_last_name() {
		return $this->field_last_name;
	}

	/**
	 * @param field_type $profile_last_name
	 */
	public function setProfile_last_name($field_last_name) {
		$this->field_last_name = $field_last_name;
	}

	/**
	 * @return the $profile_current_position
	 */
	public function getProfile_current_position() {
		return $this->field_current_position;
	}

	/**
	 * @param field_type $profile_current_position
	 */
	public function setProfile_current_position($field_current_position) {
		$this->field_current_position = $field_current_position;
	}

	/**
	 * @return the $profile_organization
	 */
	public function getProfile_organization() {
		return $this->field_organization;
	}

	/**
	 * @param field_type $profile_organization
	 */
	public function setProfile_organization($field_organization) {
		$this->field_organization = $field_organization;
	}
	
}

?>