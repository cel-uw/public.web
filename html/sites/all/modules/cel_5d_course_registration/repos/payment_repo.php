<?php

module_load_include('php', 'cel_5d_course_registration', 'entity_builders/payment_builder');
module_load_include('php', 'cel_5d_course_registration', 'repos/price_calculation_brain');

/**
 * Payment Repository class
 * defining an interface to load, save and update the data on DB 
 * or other sources
 *
 * @author vparfaniuc
 *        
 *        
 */

class PaymentRepo {
	private $entityBuilder;
	private $table 	= 'cel_5d_payments'; 
	private $id		= 'pid';
	
	function __construct(){
		$this->entityBuilder = new PaymentBuilder();
	}

	
	/**
	 * Build a Payment Entity Object and filled it with data
	 * 
	 * @param unknown_type $payment_info_data
	 * @param bool $aggregation
	 * @return Payment
	 */
	public function buildPaymentObjFillData($payment_info_data, $aggregation){
		return $this->entityBuilder->buildEntity($payment_info_data, $aggregation);
	}

	
	/**
	 * Get Payment Object by paymen ID
	 * 
	 * @param int $payment_id
	 * @return Payment
	 */
	public function getPaymentByID($payment_id)
	{
		// build sql query
		$sql = db_query("SELECT p.*, ps.title as status_title
							FROM {$this->table} as p 
							JOIN {cel_5d_payment_statuses} as ps ON (p.status_id = ps.sid)
							WHERE {$this->id} = :id
							LIMIT 1", 
						array(':id' => $payment_id));
		
		// get purchaser data form DB and buld a purchaser object
		return $this->buildPaymentObjFillData( db_fetch_array($sql) );
	} 
	
	
	/**
	 * Validate Payment Object and returns it's object or FALSE
	 * @param Payment $paymentObj
	 * @return Payment
	 */
	public function getPaymentValidateByPaymentObj(Payment $paymentObj)
	{
		// provide object validation here
		
		return $paymentObj;
	}
	
	
	/**
	 * get all Entity setters
	 * @return array 
	 */
	public function getPaymentSetters($vars_only = FALSE)
	{
		$payment_methods = get_class_methods(Payment);
		
		// walk through the list of Entity methods		
		foreach($payment_methods as $key=>$method)
		{
			// remobe 'set' prefix in the method
			if(stristr($method, 'set')){
				if($vars_only){
					$method = strtolower( str_replace('set', '', $method) );
				}
				$methods[] = $method;
			}
		}
		
		return $methods;
	}

	
	/**
	 * Create new Payment and save it to the DB
	 * @param Payment $paymentObj
	 * @param int $module_id
	 */
	public function createNewPayment(Payment $paymentObj, $module_id)
	{	
		// sql query to insert new records into DB table
		$success = db_query(
				"INSERT INTO {$this->table} (uid, payment_type, amount, status_id, payment_data)
				VALUES ( %d, '%s', %d, %d, '%s')", 
				$paymentObj->getUid(), 
				$paymentObj->getPayment_type(),
				$paymentObj->getAmount(), 
				$this->generatePaymentStatusID($paymentObj),
				$paymentObj->getPayment_data()
		);

		// if successfull insert get last id
		if ($success) {
			// get payment Object just inserted
			return $this->getPaymentByID(db_last_insert_id($this->table, $this->id));
		}
		return FALSE;		
	}
	
	
	/**
	 * Here comes the logic for statuses workflow
	 * 
	 * @param Payment $paymentObj
	 * @return number
	 */
	private function generatePaymentStatusID(Payment $paymentObj)
	{
		// if payment type is PO then first status is 3 - waiting for invoice	
		if($paymentObj->getPayment_type() == 'po'){
			return 3;
		}
		
	}
	
	
	/**
	 * Updates the information about Payment
	 * into 2 places and 2 ways
	 * 1. using drupal standard functionailty for user profile data
	 * 2. save into cel_5d_payment table additional data which is not related to global user profile 
	 * 
	 * @param Payment $paymentObj
	 * @return boolean
	 */
	public function updatePayment(Payment $paymentObj)
	{

		// sql query to update a payment record in the DB
		
		return TRUE;
	}


	/**
	 * Build Payment Object based on incomming data array
	 * 
	 * @param array $submitted_data
	 * @return Payment
	 */
	public function buildPaymentObjFromSubmittedData(array $submitted_data)
	{
		global $user;
		$priceCalculationBrain = new PriceCalculationBrain(	array_get($submitted_data, 'licences_qnt'), 
															node_load(array_get($submitted_data, 'module_nid')) );

		// defining entity data for the new entity Object
		$entity_data = array(
				'payment_type' => array_get($submitted_data, 'payment_type'),
				'amount'	=> $priceCalculationBrain->getTotalPrice(),
				'uid'		=> $user->uid,
				'payment_data'	=> array_get($submitted_data, 'payment_data')
				);

		// build and return entityObj based on entity data defined above
		return $this->buildPaymentObjFillData($entity_data);
	}


}

?>