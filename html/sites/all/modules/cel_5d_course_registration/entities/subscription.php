<?php

/**
 *
 * @author vparfaniuc
 *        
 *        
 */

class Subscription {
	private $sid;	
	private $payment_id;	
	private $date;
	private $start_date;
	private $expire_date;
	private $module_id;	
	private $licences_qty;
	private $registrar_id;
	
	
	
	/**
	 * @return the $sid
	 */
	public function getSid() {
		return $this->sid;
	}

	/**
	 * @param field_type $sid
	 */
	public function setSid($sid) {
		$this->sid = $sid;
	}

	/**
	 * @return the $payment_id
	 */
	public function getPayment_id() {
		return $this->payment_id;
	}

	/**
	 * @param field_type $payment_id
	 */
	public function setPayment_id($payment_id) {
		$this->payment_id = $payment_id;
	}

	/**
	 * @return the $date
	 */
	public function getDate() {
		return $this->date;
	}

	/**
	 * @param field_type $date
	 */
	public function setDate($date) {
		$this->date = $date;
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

	/**
	 * @return the $module_id
	 */
	public function getModule_id() {
		return $this->module_id;
	}

	/**
	 * @param field_type $module_id
	 */
	public function setModule_id($module_id) {
		$this->module_id = $module_id;
	}

	/**
	 * @return the $licences_qty
	 */
	public function getLicences_qty() {
		return $this->licences_qty;
	}

	/**
	 * @param field_type $licences_qty
	 */
	public function setLicences_qty($licences_qty) {
		$this->licences_qty = $licences_qty;
	}
	
	/**
	 * @return the $registrar_id
	 */
	public function getRegistrar_id() {
		return $this->registrar_id;
	}

	/**
	 * @param field_type $registrar_id
	 */
	public function setRegistrar_id($registrar_id) {
		$this->registrar_id = $registrar_id;
	}
}




