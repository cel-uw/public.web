<?php

/**
 *
 * @author vparfaniuc
 *        
 *        
 */

class Payment {
	private $pid;	// PK
	private $uid;	// payer ID, FK from users table
	private $payment_type; 	// credit_card or po
	private $amount;		// payment ammount
	private $status_id;		// FK from payment_statuses table
	private $date;			// payment timestamp
	private $payment_data;	// serialized payment info array
	private $status_title;	// joined data from statuses table 

	/**
	 * @return the $pid
	 */
	public function getPid() {
		return $this->pid;
	}

	/**
	 * @param field_type $pid
	 */
	public function setPid($pid) {
		$this->pid = $pid;
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
	 * @return the $payment_type
	 */
	public function getPayment_type($formatted=FALSE) 
	{
		if($formatted)
		{
			switch ($this->payment_type) {
				case 'po':
					return 'Purchase Order';
				break;
				case 'credit_card':
					return 'Credit Card';
				break;
				default:
					$this->payment_type;
				break;
			}
		}
		return $this->payment_type;
	}

	/**
	 * @param field_type $payment_type
	 */
	public function setPayment_type($payment_type) {
		$this->payment_type = $payment_type;
	}

	/**
	 * @return the $amount
	 */
	public function getAmount() {
		return $this->amount;
	}

	/**
	 * @param field_type $amount
	 */
	public function setAmount($amount) {
		$this->amount = $amount;
	}

	/**
	 * @return the $status_id
	 */
	public function getStatus_id() {
		return $this->status_id;
	}

	/**
	 * @param field_type $status_id
	 */
	public function setStatus_id($status_id) {
		$this->status_id = $status_id;
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
	 * @return the $payment_data
	 */
	public function getPayment_data() {
		return $this->payment_data;
	}

	/**
	 * @param field_type $payment_data
	 */
	public function setPayment_data($payment_data) {
		$this->payment_data = $payment_data;
	}
	/**
	 * @return the $status_title
	 */
	public function getStatus_title() {
		return $this->status_title;
	}

	/**
	 * @param field_type $status_title
	 */
	public function setStatus_title($status_title) {
		$this->status_title = $status_title;
	}


	
}

?>