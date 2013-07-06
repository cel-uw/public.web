<?php

module_load_include('php', 'cel_5d_course_registration', 'entity_builders/subscriber_builder');
module_load_include('php', 'cel_5d_course_registration', 'repos/repository');

/**
 * Subscriber Repository class
 * defining an interface to load, save and update the data on DB 
 * or other sources
 *
 * @author vparfaniuc
 *        
 *        
 */

class SubscriberRepo extends Repository {
	private $entityBuilder;
	
	function __construct(){
		$this->entityBuilder = new SubscriberBuilder();
	}
	
	
	/**
	 * Create new Subscriber and save this User into the DB
	 * @param Subscriber $subscriberObj
	 * @param int $module_id
	 */
	public function createNewSubscriber(Subscriber $subscriberObj, Subscription $subscriptionObj)
	{
		// get user role details form the DB
		$module_role = cel_5d_helper_get_userrole_by_role_title('5D Subscriber '.$subscriptionObj->getModule_id());
	
		// build new user data array
		$new_user = array(
				'name' 	=> $subscriberObj->getMail(),
				'pass' 	=> '111pass',
				'mail' 	=> $subscriberObj->getMail(),
				'status' 	=> 1,
				'init'		=> $subscriberObj->getMail(),
				'roles'		=> array(12=>'5D Subscriber 473'),
				'profile_first_name' 	=> $subscriberObj->getProfile_first_name(),
				'profile_last_name'	 	=> $subscriberObj->getProfile_last_name(),
				'profile_current_position'	=> $subscriberObj->getProfile_current_position(),
				'profile_organization'		=> $subscriberObj->getProfile_organization(),
		);
	
		$new_user = user_save(null, $new_user);
		if($new_user){
			_user_mail_notify('status_activated', $new_user);
		}
		return $this->buildSubscriberObjFillData($new_user);
	}	
	
	
	/**
	 * Validate if there is existing user with such email in the DB
	 * if so, return subscriptionObj
	 * 
	 * @param Subscriber $subscriberObj
	 * @return Ambigous <Subscriber, boolean>
	 */
	public function checkSubscriberUserExists(Subscriber $subscriberObj)
	{
		// build sql query
		$sql = db_query("SELECT uid
						FROM {users}
						WHERE mail = '%s'
						LIMIT 1",
				$subscriberObj->getMail());

		$user_info = db_fetch_object($sql);
		
		
		if($user_info){
			return $this->getSubscriberByUserID($user_info->uid);
		}

		return FALSE;
	}
	
	
	public function checkSubscriberByUserID($uid){
		$sql = "SELECT sm.subscriber_uid FROM cel_5d_subscriptions_subscribers_map as sm
				WHERE sm.subscriber_uid = %d
				LIMIT 1";
		return $this->dbGetVar('subscriber_uid', $sql, array($uid));
	}
	
	
	/**
	 * Assign subscriber role to an existing user
	 * 
	 * @param Subscriber $subscriberObj
	 * @param Subscription $subscriptionObj
	 */
	private function assignSubscriberRoleToExistingUser(Subscriber $subscriberObj, Subscription $subscriptionObj)
	{
		// load specific user
		$user = user_load($subscriberObj->getUid());
		
		module_load_include('php', 'cel_5d_course_registration', 'repos/roles_repo');
		$rolesRepo = new RolesRepo();
		
		
		$user_info['roles'] = $rolesRepo->getSubscriberRolesArr($subscriberObj, $subscriptionObj);
		
		// unasign 5D Trial role
		// unset 5d Cources Trial Temp role
		if (isset($user_info['roles'][14])){
			unset($user_info['roles'][14]);
		} 

		return user_save($user, $user_info);
	}
	
	
	/**
	 * Take array of subscriberObjects and create users for each subscriberObj
	 * 
	 * @param array $subscribers_array
	 * @param Subscription $subscriptionObj
	 * @return array
	 */
	public function createSubcribersFromSubscribersArray($subscribers_array, Subscription $subscriptionObj)
	{
		$new_users_obj_array = array();
		if (is_array($subscribers_array) && !empty($subscribers_array) ) 
		{
			/* @var $subscriberObj Subscriber */
			foreach ($subscribers_array as $subscriberObj)
			{
				$check_user = $this->checkSubscriberUserExists($subscriberObj);
				if($check_user){
					$this->assignSubscriberRoleToExistingUser($check_user, $subscriptionObj);
					$new_users_obj_array[ $check_user->getUid() ] = $check_user;
				}else{
					// create new user and returns filled subscriber object
					$newSubscriberObj = $this->createNewSubscriber($subscriberObj, $subscriptionObj);
					$new_users_obj_array[ $newSubscriberObj->getUid() ] = $newSubscriberObj;
				}
			}	
		}

		// return an array of Subscriber Objects
		return $new_users_obj_array;
	}
	
	
	/**
	 * Build a Subscriber Entity Object and filled it with data
	 * 
	 * @param unknown_type $subscriber_info_data
	 * @param bool $aggregation
	 * @return Subscriber
	 */
	public function buildSubscriberObjFillData($subscriber_info_data, $aggregation=FALSE){
		return $this->entityBuilder->buildEntity($subscriber_info_data, $aggregation);
	}
	
	
	
	/**
	 * Get array of Subscribers objects based on Subscription ID 
	 * 
	 * @param int $subscription_id
	 * @return array 
	 */
	public function getSubscribersBySubscriptionID($subscription_id)
	{
		// ge list of subscriber IDs
		$sql = 'SELECT subscriber_uid FROM cel_5d_subscriptions_subscribers_map WHERE subscription_id = %d';
// 		$subscriber_ids = $this->dbGetRecordsObj($sql, array($subscription_id));
		$subscriber_ids = $this->dbGetRecordsFieldArr('subscriber_uid', $sql, array($subscription_id));
		
		$subscribersObj_arr = array();
		if($subscriber_ids && !empty($subscriber_ids))
		{
			foreach ($subscriber_ids as $uid) 
			{
				$newSubscriberObj = $this->getSubscriberByUserID($uid);
				
				if($newSubscriberObj){
					$subscribersObj_arr[] = $newSubscriberObj;
				} 
			}	
		}
		
		return $subscribersObj_arr;
	}
	
	
	
	/**
	 * Get list of Subscribers objects based on array of user IDs
	 * @param array $user_ids
	 */
	public function getSubscribersByUserIDs(array $user_ids)
	{
		if(empty($user_ids)){
			return FALSE;
		}
		
		$subscribers_arr = array();
		
		foreach ($user_ids as $uid)
		{
			$subscribers_arr[] = $this->getSubscriberByUserID($uid);
		}
		
		return $subscribers_arr;
	}
	
	
	/**
	 * Get a single Purchased object info based on ID passed 
	 * @param int $uid
	 * @return Subscriber
	 */
	public function getSubscriberByUserID($uid){
		return $this->buildSubscriberObjFillData( user_load($uid) );
	} 
	
	
	/**
	 * Validate Subscriber Object and returns it's object or FALSE
	 * @param Subscriber $subscriberObj
	 * @return Subscriber
	 */
	public function getSubscriberValidateBySubscriberObj(Subscriber $subscriberObj)
	{
		// provide object validation here
		
		return $subscriberObj;
	}
	
	
	/**
	 * Build Subscribers Objects based on submited form data
	 * @param array $submitted_data
	 * @return <Subscriber> 
	 */
	public function getSubscribersFromSubmitedData(array $submitted_data)
	{
		$licences_qty = isset($submitted_data['licences_qnt']) ? intval($submitted_data['licences_qnt']) : 1;

		$subscribersObj_arr = array();
		
		for($i=1; $i <= $licences_qty; $i++)
		{
			// build sufix
			$sufix = '_subscriber' . $i;

			// check if current subscriber has email field setup
			if(!isset($submitted_data['mail'.$sufix]) || strlen($submitted_data['mail'.$sufix]) < 1 ){
				continue;
			}
			
			// get obj setters
			$subscriber_setters = $this->getSubscriberSetters();
			
			// walk through list of setters
			foreach ($subscriber_setters as $method)
			{
				// remove set method prefix and lower the method name
				$method_without_set = str_replace('set', '', strtolower($method));
				
				if(isset($submitted_data[$method_without_set.$sufix]) && strlen(isset($submitted_data[$method_without_set.$sufix])) > 0 ){
					$data_arr[$i][$method_without_set] = $submitted_data[$method_without_set.$sufix];					
				}
			}
			
			// create new Subscriber Object
			$subscriberBuilder = new SubscriberBuilder();

			// build new Subscriber Obj based on submited data
			$subscribersObj_arr[$i] = $subscriberBuilder->buildEntity($data_arr[$i]);
			
			
		}
		return ($subscribersObj_arr);
	}
	
	
	/**
	 * get all Entity setters
	 * @return array 
	 */
	public function getSubscriberSetters()
	{
		$subscriber_methods = get_class_methods(Subscriber);
		
		// walk through the list of Entity methods		
		foreach($subscriber_methods as $key=>$method)
		{
			// remobe 'set' prefix in the method
			if(stristr($method, 'set')){
				$methods[] = $method;
			}
		}
		
		return $methods;
	}
	
	
	/**
	 * Transform Subscriber obj into an array
	 * @param Subscriber $subscriberObj
	 * @return StdClass
	 */
	public function mapSubscriberToUserObj(Subscriber $subscriberObj){
		$subscriber_array = $this->mapSubscriberToUserArray($subscriberObj);
		return (object) $subscriber_array;
	}
	
	
	/**
	 * Transform Sbuscriber Obj into an array of data
	 * @param Subscriber $subscriberObj
	 * @return array
	 */
	public function mapSubscriberToUserArray(Subscriber $subscriberObj){
		$new_user = array(
				'name' => $subscriberObj->getName(),
				'pass' => $subscriberObj->getPass(),
				'mail' => $subscriberObj->getMail(),
				'status' => $subscriberObj->getStatus(),
				'init'	=> $subscriberObj->getMail(),
				'roles'	=> $subscriberObj->getRoles(),
// 				'roles'	=> array(12=>'5D Subscriber 473')
		);
		return $new_user;
	}
	
	
	/**
	 * Updates the information about Subscriber
	 * into 2 places and 2 ways
	 * 1. using drupal standard functionailty for user profile data
	 * 2. save into cel_5d_subscriber table additional data which is not related to global user profile 
	 * 
	 * @param Subscriber $subscriberObj
	 * @return boolean
	 */
	public function updateSubscriber(Subscriber $subscriberObj, array $user_info)
	{
		$user = user_load($subscriberObj->getUid());
		
		// apply user changes and save user
		user_save($user, $user_info);
		
		
		return TRUE;
	}

        public function disableExpiredSubscriptionSubscribers(){
            $subscribers = $this->getExpiredSubscriptionSubscribers();

            if(!$subscribers){
                return FALSE;
            }

            foreach ($subscribers as $subscriber){
                $user = user_load($subscriber->subscriber_uid);

               $user_info['roles'] = $user->roles;

		// unasign 5D Subscriber role
		if (isset($user_info['roles'][12])){
			unset($user_info['roles'][12]);
		}

		user_save($user, $user_info);
            }

            return TRUE;
        }


        public function getExpiredSubscriptionSubscribers(){
          $sql = "SELECT
                            sm.*
                    FROM
                            cel_5d_subscriptions AS s
                    RIGHT JOIN cel_5d_subscriptions_subscribers_map as sm ON sm.subscription_id = s.sid
                    WHERE
                            s.expire_date < NOW()";

          return $this->dbGetRecordsObj($sql);
        }
}

?>