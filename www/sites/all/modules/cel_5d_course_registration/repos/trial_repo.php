<?php

module_load_include('php', 'cel_5d_course_registration', 'entity_builders/trial_builder');
module_load_include('php', 'cel_5d_course_registration', 'repos/repository');

/**
 * Trial Repository class
 * defining an interface to load, save and update the data on DB 
 * or other sources
 *
 * @author vparfaniuc
 *        
 *        
 */
class TrialRepo extends Repository {
	private $entityBuilder;
	private $table 	= 'cel_5d_trials'; 
	private $id		= 'tid';
	
	/**
	 * Setting up default operations execution at the point of object initialization
	 */
	function __construct(){
		$this->entityBuilder = new TrialBuilder();
	}

	
	/**
	 * Get Trial Object by paymen ID
	 * 
	 * @param int $trial_id
	 * @return Trial
	 */
	public function getTrialByID($trial_id)
	{
		// build sql query
		$sql = db_query("	SELECT * 
							FROM \{{$this->table}\}
							WHERE $this->id = :trial_id
							LIMIT 1", 
						array('trial_id' => $trial_id));
		
		// get purchaser data form DB and buld a purchaser object
		return $this->buildTrialObjFillData( db_fetch_array($sql) );
	} 
	
	
	/**
	 * get set of Trial objects from the db
	 * @return boolean|multitype:Ambigous <Trial, Ambigous, boolean> 
	 */
	public function getAllTrials()
	{
		$sql = 'SELECT * FROM {'.$this->table.'}';
		$all_trials = $this->dbGetRecordsArr($sql);

		if(empty($all_trials)){
			return FALSE;
		}
		
		$trialsObj_arr = array();
		foreach($all_trials as $trial)
		{
			$trialsObj_arr[] = $this->entityBuilder->buildEntity($trial);
		}
		
		return $trialsObj_arr;
	}
	
	
	/**
	 * Pulling records form DB with extended query specificly for jQGrid data JSON
	 * 
	 * @param string $sort_field
	 * @param string $sort_direction
	 * @return array 
	 */
	public function getRosterTrialsForGridJson($sort_field='id', $sort_direction='ASC')
	{
		$sql = 'SELECT 
          u.*,
          t.start_date,
          t.expire_date
      FROM {'.$this->table.'} as t
      JOIN {users} as u ON (t.uid=u.uid)
      ORDER BY '.$sort_field.' ' . $sort_direction;

		$all_trials = $this->dbGetRecordsArr($sql);
		
		if(empty($all_trials)){
			return FALSE;
		}
		
		$users = array();
		$trialsObj_arr = array();
		foreach($all_trials as $trial)
		{
			// Get user values
			$uid = $trial['uid'];
			if(!isset($users[$uid])) {
    		$users[$uid] = user_load($uid);
    	}

    	$user = $users[$uid];
    	$trial['field_first_name'] = "";
    	$trial['field_last_name'] = "";
    	$trial['field_organization'] = "";

    	$first_names = array();
    	$first_name_items = field_get_items('user', $user, 'field_first_name');
    	if(!empty($first_name_items)) {
    		foreach($first_name_items as $value) {
	        $first_name_item = field_view_value('user', $user, 'field_first_name', $value);
	        $first_names[] = render($first_name_item);
	      }
    	}

    	$trial['field_first_name'] = implode(" ", $first_names);


    	$last_names = array();
    	$last_name_items = field_get_items('user', $user, 'field_last_name');
    	if(!empty($last_name_items)) {
    		foreach($last_name_items as $value) {
	        $last_name_item = field_view_value('user', $user, 'field_last_name', $value);
	        $last_names[] = render($last_name_item);
	      }
    	}

    	$trial['field_last_name'] = implode(" ", $last_names);

    	$organizations = array();
    	$organization_items = field_get_items('user', $user, 'field_organization');
    	if(!empty($organization_items)) {
    		foreach($organization_items as $value) {
	        $organization_item = field_view_value('user', $user, 'field_organization', $value);
	        $organizations[] = render($organization_item);
	      }
    	}

    	$trial['field_organization'] = implode(" ", $organizations);

			$trialsObj_arr[] = $this->entityBuilder->buildEntity($trial);
		}
		return $trialsObj_arr;		
	}


  public function deactivateExpiredTrials(){
      $exp_users = $this->getExpiredTrialUsers();

      if(!$exp_users){
          return FALSE;
      }

      foreach ($exp_users as $usr){
          // remove role
          $this->removeTrialRoleByUserID($usr->uid);
      }

      return TRUE;
  }


  public function removeTrialRoleByUserID($uid){
      // remove user role
      	// load specific user
$user = user_load($uid);

          if(!$user){
              return FALSE;
          }

module_load_include('php', 'cel_5d_course_registration', 'repos/roles_repo');
$rolesRepo = new RolesRepo();

          $transaction = db_query("UPDATE {cel_5d_trials} SET STATUS = 0 WHERE uid = ".$uid);

          if(!$transaction){
              return FALSE;
          }

          $user_info['roles'] = $user->roles;

// unasign 5D Trial role
// unset 5d Cources Trial Temp role
$trial_role = array_search('5D Trial', $user_info['roles'], true);
if($trial_role !== false){
	unset($user_info['roles'][$trial_role]);
} 

return user_save($user, $user_info);
  }


  public function getExpiredTrialUsers(){
      // get users from DB table accoriding to expire field
      $sql = "SELECT t.*
              FROM {cel_5d_trials} as t
              WHERE t.expire_date < NOW()
              AND t.status = 1";

      $records = $this->dbGetRecordsObj($sql);

      return $records;
  }



	
	/**
	 * Get an array of Trial User IDs
	 * @return array | bool
	 */
	public function getAllTrialsUserIDs()
	{
		$all_trials = $this->getAllTrials();
		
		if(empty($all_trials)){
			return FALSE;
		}
		
		$trial_users = array();
		
		/* @var $trialObj Trial */
		foreach ($all_trials as $trialObj)
		{
			$trial_users[] = $trialObj->getUid();
		}
		
		return $trial_users;
	}
	
	
	/**
	 * Get a Trial obj by Subscriber User ID
	 * @param int $user_id
	 * @return Trial
	 */
	public function getTrialByUserID($user_id)
	{
		// build sql query
		$sql = db_query("	SELECT *
				FROM {$this->table}
				WHERE uid = %d
				LIMIT 1",
				$user_id);
		
				// get purchaser data form DB and buld a purchaser object
		return $this->buildTrialObjFillData( db_fetch_array($sql) );		
	}
	
	
	/**
	 * Validate Trial Object and returns it's object or FALSE
	 * @param Trial $trialObj
	 * @return Trial
	 */
	public function getTrialValidateByTrialObj(Trial $trialObj)
	{
		// provide object validation here
		
		return $trialObj;
	}
	
	
	/**
	 * get all Entity setters
	 * @return array 
	 */
	public function getTrialSetters($vars_only = FALSE)
	{
		$trial_methods = get_class_methods(Trial);
		
		// walk through the list of Entity methods		
		foreach($trial_methods as $key=>$method)
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
	 * Create new Trial and save it to the DB
	 * @param Trial $trialObj
	 * @param int $module_id
	 * 
	 * @return Trial or bool
	 */
	public function createNewTrial(Trial $trialObj)
	{	
		// sql query to insert new records into DB table
		$success = db_query(
				"INSERT INTO {$this->table} (uid, status, start_date, expire_date)
				VALUES ( %d, 1, NOW(), NOW() + INTERVAL 30 DAY)", 
				$trialObj->getUid() 
		);

		// if successfull insert get last id
		if ($success) {
			// get trial Object just inserted
			return $this->getTrialByID(db_last_insert_id($this->table, $this->id));
		}
		return FALSE;		
	}
	
	
	/**
	 * Updates the information about Trial
	 * into 2 places and 2 ways
	 * 1. using drupal standard functionailty for user profile data
	 * 2. save into cel_5d_trial table additional data which is not related to global user profile 
	 * 
	 * @param Trial $trialObj
	 * @return boolean
	 */
	public function updateTrial(Trial $trialObj)
	{

		// sql query to update a trial record in the DB
		
		return TRUE;
	}
	
	
	/**
	 * Build a Trial Entity Object and filled it with data
	 *
	 * @param unknown_type $trial_info_data
	 * @param bool $aggregation
	 * @return Trial
	 */
	public function buildTrialObjFillData($trial_info_data, $aggregation=FALSE){
		return $this->entityBuilder->buildEntity($trial_info_data, $aggregation);
	}


	/**
	 * Building a fields map for a trials roster page
	 * This map to be used on json data generation, csv export and jqgrid fields titeling
	 * 
	 * @return multitype:string 
	 */
	public function buildRosterFieldsTitlesMethodsArrMap()
	{
		// build fields map method => Field Name
		$fields_map = array(
				'subscriberObj:uid'				=> 'User ID',
				'subscriberObj:profile_first_name'			=> 'First Name',
				'subscriberObj:profile_last_name'			=> 'Last Name',
				'subscriberObj:profile_current_position'	=> 'Current Position',
				'subscriberObj:profile_organization'		=> 'Organization',
				'subscriberObj:mail'			=> 'Email',
				'trialObj:start_date'			=> 'Start Date',
				'trialObj:expire_date'			=> 'Expire Date',
				);
		
		return $fields_map;
	}


	/**
	 * Get users registerd for tirial a specific number of days ago
	 * 
	 * @param int $days
	 * @return array
	 */
	public function getTrialsRegisteredDaysAgoByDays($days){
		
		$days = (int) $days;

		if(empty($days)){
			$days = 0;
		}
		
		$sql = "SELECT t.*
				FROM {cel_5d_trials} as t
				WHERE date(t.start_date) = CURDATE() - INTERVAL {$days} DAY";
		$records = $this->dbGetRecordsObj($sql);
		
		// validate records data
		if(!$records || empty($records)){
			return FALSE;
		}
		
		$trialsObj_arr = array();
		foreach ($records as $record){
			$trialsObj_arr[] = $this->buildTrialObjFillData($record);
		}
		
		return $trialsObj_arr;
		
	}
	
	
	public function getTrialUsersIDExpiredDaysAgoByDays($days)
	{
		if(empty($days)){
			return false;
		}

		$days = (int) $days;
		
		$sql = "SELECT t.*
				FROM {cel_5d_trials} as t
				WHERE date(t.expire_date) = CURDATE() - INTERVAL {$days} DAY";
		$records = $this->dbGetRecordsObj($sql);

		// validate records data
		if(!$records || empty($records)){
			return FALSE;
		}
		
		$trialsObj_arr = array();
		foreach ($records as $record){
			$trialsObj_arr[] = $this->buildTrialObjFillData($record);
		}
		
		return $trialsObj_arr;		
	}
	
	
	
	public function getTrialUserIDsExpireBeforeDaysByDays($days)
	{
		$days = (int) $days;
		if(empty($days)){
			$days = 0;
		}
		
		$sql = "SELECT t.*
				FROM {cel_5d_trials} as t
				WHERE date(t.expire_date) = CURDATE() + INTERVAL {$days} DAY";
		$records = $this->dbGetRecordsObj( $sql);
		
		// validate records data
		if(!$records || empty($records)){
			return FALSE;
		}
		
		$trialsObj_arr = array();
		foreach ($records as $record){
			$trialsObj_arr[] = $this->buildTrialObjFillData($record);
		}
		
		return $trialsObj_arr;	
	}
	
	
	
	public function emailsHandlersSpecifications(Purchaser $purchaserObj, Trial $trialObj)
	{
		// specify patters
		return array(
			'{trial:first_name}'		=> $purchaserObj->getProfile_first_name(),
			'{trial:expire_date}'		=> date("m/d/Y", strtotime( $trialObj->getExpire_date() )),
		);		
	}

}

?>