<?php

/**
 *
 * @author vparfaniuc
 *        
 *        
 */

class Trial {
	private $tid;			// PK
	private $uid;			// FK for users table
	private $start_date;	// trial start date
	private $expire_date;	// trial expiration date

	/**
	 * @return the $tid
	 */
	public function getTid() {
		return $this->tid;
	}

	/**
	 * @param field_type $tid
	 */
	public function setTid($tid) {
		$this->tid = $tid;
	}

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
	 * @return the $start_date
	 */
	public function getStart_date() {
		return $this->start_date;
	}

	/**
	 * @param field_type $start_date
	 */
	public function setStart_date($start_date) {
		$this->start_date = $start_date;
	}

	/**
	 * @return the $expire_date
	 */
	public function getExpire_date() {
		return $this->expire_date;
	}

	/**
	 * @param field_type $expire_date
	 */
	public function setExpire_date($expire_date) {
		$this->expire_date = $expire_date;
	}

	
}

?>