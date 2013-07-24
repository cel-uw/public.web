<?php

module_load_include('php', 'cel_5d_course_registration', 'entity_builders/purchaser_builder');

/**
 * Purchaser Repository class
 * defining an interface to load, save and update the data on DB 
 * or other sources
 *
 * @author vparfaniuc
 *        
 *        
 */

class PurchaserRepo {
	private $entityBuilder;
	private $table = 'cel_5d_purchaser_info';
	private $id = 'uid';
	
	function __construct(){
		$this->entityBuilder = new PurchaserBuilder();
	}
	
	
	/**
	 * Build a Purchaser Entity Object and filled it with data
	 * 
	 * @param unknown_type $purchaser_info_data
	 * @param bool $aggregation
	 * @return Purchaser
	 */
	public function buildPurchaserObjFillData($purchaser_info_data, $aggregation){
		return $this->entityBuilder->buildEntity($purchaser_info_data, $aggregation);
	}
	
	
	/**
	 * Get a single Purchased object info based on ID passed 
	 * @param int $uid
	 * @return Purchaser
	 */
	public function getPurchaserByUserID($uid)
	{
		$user = user_load($uid);
		
		// get purchaser data form DB 
		$purchaser_db_info = $this->getPurchaserAddlInfoBuUserID($uid);

		// build new Entity Object and fill it with data form user and DB info
		$purchaserObj = $this->entityBuilder->buildEntity( array($user, $purchaser_db_info), TRUE );
		
		return $purchaserObj;
	} 
	
	
	/**
	 * get Additional Info from the DB
	 * 
	 * @param int $uid
	 * @return Ambigous <An, object, boolean, unknown>
	 */
	public function getPurchaserAddlInfoBuUserID(int $uid)
	{
		// build sql query
		$sql = db_query("	SELECT *
							FROM {$this->table}
							WHERE $this->id = %d
							LIMIT 1",		
		 				$uid);
		
		// get purchaser data form DB
		return db_fetch_object($sql);
	}
	
	/**
	 * Build a Purchaser object based on logged in user dara
	 * @return Purchaser
	 */
	public function getPurchaserObjOnLoggedInUser()
	{
		global $user;
		
		// load data based on logged in user ID
		$purchaserObj = $this->getPurchaserByUserID($user->uid);
				
		// return Purchased object to caller
		return $purchaserObj;		
	}
	
	
	/**
	 * Insert addition extra user information into the DB
	 * 
	 * @param Purchaser $purchaserObj
	 * @return Ambigous <Aboolean, resource>
	 */
	public function insertPurchaserAddlData(Purchaser $purchaserObj)
	{
		// check if there are any exisitng data for existing user
		if($this->getPurchaserAddlInfoBuUserID($purchaserObj->getUid())){
			// if there are any data just update existing record
			return $this->updatePurchaserAddlData($purchaserObj);
		}
		
		// sql query to insert new records into DB table
		$transaction = db_query(
				"INSERT INTO {$this->table} (
					uid,
					preferred_name,
					daytime_phone
				)
				VALUES (
					%d,
					'%s',
					'%s'
				)",
				$purchaserObj->getUid(),
				$purchaserObj->getPreferred_name(),
				$purchaserObj->getDaytime_phone()
		);

		return $transaction;		
	}
	
	
	/**
	 * Upudate DB record
	 * @param Purchaser $purchaserObj
	 * @return Ambigous <A, boolean, unknown, resource>
	 */
	public function updatePurchaserAddlData(Purchaser $purchaserObj)
	{
		// sql query to insert new records into DB table
		$transaction = db_query(
				"UPDATE {$this->table} 
				SET 
					preferred_name = '%s',
					daytime_phone = '%s'
				WHERE
					uid = %d
				LIMIT 1",
				$purchaserObj->getPreferred_name(),
				$purchaserObj->getDaytime_phone(),
				$purchaserObj->getUid()
				);
		return $transaction;		
	}
	
	
	/**
	 * Updates the information about Purchaser
	 * into 2 places and 2 ways
	 * 1. using drupal standard functionailty for user profile data
	 * 2. save into cel_5d_purchaser table additional data which is not related to global user profile 
	 * 
	 * @param Purchaser $purchaserObj
	 * @return boolean
	 */
	public function updatePurchaserInfoDB(Purchaser $purchaserObj, Subscription $subscriptionObj)
	{
		module_load_include('php', 'cel_5d_course_registration', 'repos/roles_repo');
		
		global $user;
		$rolesRepo = new RolesRepo();
		
		// 1. update the profile information using dropal user management func
		// set up updated values into a temp array		
		$user_info['field_first_name']	= $purchaserObj->getProfile_first_name();
		$user_info['field_last_name'] 	= $purchaserObj->getProfile_last_name();
		$user_info['field_current_position'] 	= $purchaserObj->getProfile_current_position();
		$user_info['field_organization']		= $purchaserObj->getProfile_organization();
		$user_info['roles']		= $rolesRepo->getPurchaserRolesArr($purchaserObj, $subscriptionObj);
		
		
		$tempRegistrarRole = $rolesRepo->getTemporaryRegistrarRole($subscriptionObj->getModule_id());
		
		// remove temp registrar user
		if (isset($user_info['roles'][$tempRegistrarRole->rid])){
			unset($user_info['roles'][$tempRegistrarRole->rid]);
		}
		
		// insert additional data into the DB
		if($this->insertPurchaserAddlData($purchaserObj)){
			// apply user changes and save user
			return user_save($user, $user_info);
		}
		return FALSE;
	}	
}

?>