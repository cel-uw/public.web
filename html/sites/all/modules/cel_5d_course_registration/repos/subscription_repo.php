<?php

module_load_include('php', 'cel_5d_course_registration', 'entity_builders/subscription_builder');
module_load_include('php', 'cel_5d_course_registration', 'repos/repository');

/**
 * Subscription Repository class
 * defining an interface to load, save and update the data on DB 
 * or other sources
 *
 * @author vparfaniuc
 *        
 *        
 */

class SubscriptionRepo extends Repository {
  private $entityBuilder;
  private $table   = 'cel_5d_subscriptions';
  private $id   = 'sid'; 
  
  function __construct(){
    $this->entityBuilder = new SubscriptionBuilder();
  }
  
  
  /**
   * Get Subscription OBJ form DB based on it's ID
   * 
   * @param int $subscription_id
   * @return Subscription
   */
  public function getSubscriptionByID( int $subscription_id )
  {
    // build sql query
    $sql = db_query("SELECT *
              FROM {$this->table}
              WHERE $this->id = %d
              LIMIT 1",
              $subscription_id);
    
    $result_array = db_fetch_array($sql);
    
    if(!$result_array){
      throw new Exception('Non Existing Subscription');
    }
    
    // get purchaser data form DB and buld a purchaser object
    return $this->buildSubscriptionObjFillData( $result_array );
  }
  
  
  public function getRosterSubscriptionsForGridJson($sort_field='subscriber_uid', $sort_direction='ASC', $rows_per_page = 10, $cur_page = 1)
  {
    switch($sort_direction) {
      case 'ASC':
      case 'DESC':
        break;

      default:
        $sort_direction = 'ASC';
    }
    // define offset
    $rows_per_page = (int) $rows_per_page;
    $offset = (int) ($cur_page-1)*$rows_per_page;
    $sql = "SELECT   SQL_CALC_FOUND_ROWS
            sm.subscription_id,
            sm.subscriber_uid,
            p.uid AS registrar_id,
            p.payment_type,
            p.amount,
            s.start_date,
            s.registrar_id,
            u.mail AS subscriber_mail,
        FROM {cel_5d_subscriptions_subscribers_map} AS sm
        JOIN {cel_5d_subscriptions} AS s ON (s.sid = sm.subscription_id)
        JOIN {cel_5d_payments} AS p ON (p.pid = s.payment_id)
        JOIN {users} AS u ON (u.uid = sm.subscriber_uid)
        GROUP BY sm.subscriber_uid
        ORDER BY `{$sort_field}` {$sort_direction}
        LIMIT {$rows_per_page} OFFSET {$offset}";
    $subscriptionsRect = $this->dbGetRecordsObj($sql);

    // Now get additional field values for each user
    $registrars = array();
    $users = array();
    foreach($subscriptionsRect as $key=>$subscriber) {
    	$uid = $subscriber->subscriber_uid;
    	$registrar_uid = $subscriber->registrar_id;
    	if(!isset($users[$uid])) {
    		$users[$uid] = user_load($uid);
    	}

    	if(!isset($registrars[$registrar_uid])) {
    		$registrars[$registrar_uid] = user_load($registrar_uid);
    	}
    	
    	$user = $users[$uid];
    	$registrar = $registrars[$registrar_uid];

    	$subscriptionsRect[$key]->field_first_name = "";
    	$subscriptionsRect[$key]->field_last_name = "";
    	$subscriptionsRect[$key]->field_organization = "";
    	$subscriptionsRect[$key]->registrar_name = "";

    	// From what I can tell, this isn't used
    	$subscriptionsRect[$key]->field_current_position = "";

    	$first_names = array();
    	$first_name_items = field_get_items('user', $user, 'field_first_name');
    	if(!empty($first_name_items)) {
    		foreach($first_name_items as $value) {
	        $first_name_item = field_view_value('user', $user, 'field_first_name', $value);
	        $first_names[] = render($first_name_item);
	      }
    	}

    	$subscriptionsRect[$key]->field_first_name = implode(" ", $first_names);


    	$last_names = array();
    	$last_name_items = field_get_items('user', $user, 'field_last_name');
    	if(!empty($last_name_items)) {
    		foreach($last_name_items as $value) {
	        $last_name_item = field_view_value('user', $user, 'field_last_name', $value);
	        $last_names[] = render($last_name_item);
	      }
    	}

    	$subscriptionsRect[$key]->field_last_name = implode(" ", $last_names);

    	$organizations = array();
    	$organization_items = field_get_items('user', $user, 'field_organization');
    	if(!empty($organization_items)) {
    		foreach($organization_items as $value) {
	        $organization_item = field_view_value('user', $user, 'field_organization', $value);
	        $organizations[] = render($organization_item);
	      }
    	}

    	$subscriptionsRect[$key]->field_organization = implode(" ", $organizations);

    	// Get the registrar's name
    	$registrar_names = array();
    	$reg_first_name_items = field_get_items('user', $registrar, 'field_first_name');
    	if(!empty($reg_first_name_items)) {
    		foreach($reg_first_name_items as $value) {
	        $reg_first_name_item = field_view_value('user', $registrar, 'field_first_name', $value);
	        $registrar_names[] = render($reg_first_name_item);
	      }
    	}

    	$reg_last_name_items  = field_get_items('user', $registrar, 'field_last_name');
    	if(!empty($reg_last_name_items)) {
    		foreach($reg_last_name_items as $value) {
	        $reg_last_name_item = field_view_value('user', $registrar, 'field_last_name', $value);
	        $registrar_names[] = render($reg_last_name_item);
	      }
    	}

    	$subscriptionsRect[$key]->registrar_name = implode(" ", $registrar_names);
    }

    return $subscriptionsRect; 
  }
  
  
  /**
   * Define list of fields for grid, ajax and CSV design 
   * @return multitype:string 
   */
  public function buildFieldsRelationalMap(){
    $fields = array(
        'subscriber_uid'      => 'id',
        'field_last_name'     => 'Last Name',
        'field_first_name'    => 'First Name',
        'field_current_position'  => 'Position',
        'field_organization'    => 'School',
        'registrar_organization'    => 'Organization/District',
        'subscriber_mail'  => 'Email',
        'registrar_name'  => 'Purchaser name',
        'payment_type'    => 'Payment type',
        'amount'      => 'Total',
        'start_date'    => 'Date',
        );
    return $fields;
  }  
  
  
  public function getSubscriptionsExpireInDays($days){
    $days = (int) $days;
    $sql = "SELECT *
         FROM {cel_5d_subscriptions}
        WHERE date(expire_date) = CURDATE() + INTERVAL {$days} DAY";
    $db_data = $this->dbGetRecordsArr($sql);
    
    if(!$db_data || empty($db_data)){
      return FALSE;
    }
    
    $subscriptionsObj_arr = array();
    foreach($db_data as $record){
      $subscriptionsObj_arr[] = $this->buildSubscriptionObjFillData($record);
    }
    
    return $subscriptionsObj_arr;
  }
  
  
  public function getSubscriptionsStartedDaysAgo($days)
  {
    $days = (int) $days;
    $sql = "SELECT *
        FROM {cel_5d_subscriptions}
        WHERE date(start_date) = CURDATE() - INTERVAL {$days} DAY";
    $db_data = $this->dbGetRecordsArr($sql);
    
    if(!$db_data || empty($db_data)){
      return FALSE;
    }
    
    $subscriptionsObj_arr = array();
    foreach($db_data as $record){
      $subscriptionsObj_arr[] = $this->buildSubscriptionObjFillData($record);
    }
    
    return $subscriptionsObj_arr;    
  }
  
  
  /**
   * Get Subscription objects from the DB based on registrar ID
   * 
   * @param int $user_id
   * @return Subscription 
   */
  public function getSubscriptionsByUserID($user_id)
  {
    $sql = 'select * from {cel_5d_subscriptions} where registrar_id = :user_id';

    $subscriptions_records = $this->dbGetRecordsObj($sql, array(':user_id' => $user_id));
    
    if (empty($subscriptions_records)){
      return FALSE;
    }
    
    $Subscriptions_arr = array();
    
    foreach ($subscriptions_records as $record){
      $Subscriptions_arr[] = $this->buildSubscriptionObjFillData($record);  
    }
    
    return $Subscriptions_arr;
  }
  
  
  /**
   * Create new Subscription at the DB level 
   * 
   * @param Subscription $subscriptionObj
   * @param Payment $paymentObj
   * @return Subscription
   */
  public function createNewSubscription(Subscription $subscriptionObj, Payment $paymentObj)
  {
    global $user;

    // sql query to insert new records into DB table
    $transaction = db_query(
        "INSERT INTO {$this->table} (
                                    payment_id,
                                    module_id,
                                    licences_qty,
                                    registrar_id,
                                    date,
                                    start_date,
                                    expire_date
                                    )
                                    VALUES (
                                        %d,
                                        %d,
                                        %d,
                                        %d,
                                        NOW(),
                                        NOW(),
                                        NOW() + INTERVAL 1 YEAR
                                    )",
        $paymentObj->getPid(),
        $subscriptionObj->getModule_id(),
        $subscriptionObj->getLicences_qty(),
        $user->uid
        );
  
  
    if($transaction){
        // insert transaction successfully run then return last inserted Subcription Obj
      return $this->getSubscriptionByID( db_last_insert_id($this->table, $this->id) );
    }
  }  
  
    
  /**
   * Updates the information about Subscription
   * into 2 places and 2 ways
   * 1. using drupal standard functionailty for user profile data
   * 2. save into cel_5d_subscription table additional data which is not related to global user profile
   *
   * @param Subscription $subscriptionObj
   * @return boolean
   */
  public function updateSubscription(Subscription $subscriptionObj)
  {
  
    // sql query to update a subscription record in the DB
  
    return TRUE;
  }

  
  public function mapSubscribersArrToSubscription(Subscription $subscriptionObj, array $subscibers)
  {
    if (empty($subscibers)){
      return FALSE;
    }
    
    $emailNotificationClass = new EmailNotificationClass($subscriptionObj->getModule_id());
    
    /* @var $subscriberObj Subscriber */
    foreach ($subscibers as $subscriber_id => $subscriberObj)
    {
      if($this->mapSubscriberToSubscription($subscriptionObj, $subscriberObj)){
        $emailNotificationClass->sendConfirmationEmailToSubscriber($subscriptionObj, $subscriberObj);
      }
    }
  }
  
  
  /**
   * Create Subscriber -> Subscription relationship 
   * at the DB level
   * 
   * @param Subscription $subscriptionObj
   * @param Subscriber $subscriberObj
   * @return boolean
   */
  public function mapSubscriberToSubscription(Subscription $subscriptionObj, Subscriber $subscriberObj)
  {
    // sql query to insert new records into DB table
    $transaction = db_query(
        "INSERT INTO {cel_5d_subscriptions_subscribers_map} (
                                    subscription_id,
                                    subscriber_uid
        )
        VALUES (
                                    %d,
                                    %d
        )",
        $subscriptionObj->getSid(),
        $subscriberObj->getUid()
        );

    return $transaction;
  }
  
  
    
  /**
   * Build a Subscription Entity Object and filled it with data
   * 
   * @param unknown_type $subscription_info_data
   * @param bool $aggregation
   * @return Subscription
   */
  public function buildSubscriptionObjFillData($subscription_info_data, $aggregation=FALSE){
    return $this->entityBuilder->buildEntity($subscription_info_data, $aggregation);
  }

  
  /**
   * Validate Subscription Object and returns it's object or FALSE
   * @param Subscription $subscriptionObj
   * @return Subscription
   */
  public function getSubscriptionValidateBySubscriptionObj(Subscription $subscriptionObj)
  {
    // provide object validation here
    
    return $subscriptionObj;
  }
  
  
  /**
   * get all Entity setters
   * @return array 
   */
  public function getSubscriptionSetters()
  {
    $subscription_methods = get_class_methods(Subscription);
    
    // walk through the list of Entity methods    
    foreach($subscription_methods as $key=>$method)
    {
      // remobe 'set' prefix in the method
      if(stristr($method, 'set')){
        $methods[] = $method;
      }
    }
    
    return $methods;
  }
  
  
  /**
   * Build Subscription Object based on incomming data array
   * 
   * @param array $submitted_data
   * @return Subscription
   */
  public function buildSubscriptionObjFromSubmittedData(array $submitted_data)
  {
    global $user;
    $entity_data = array(
        'start_date'   => mktime(0, 0, 0, date("m"),   date("d"),   date("Y")),
        'expire_date'   => mktime(0, 0, 0, date("m"),   date("d"),   date("Y")+1),
        'module_id'    => $submitted_data['module_nid'],
        'licences_qty'  => $submitted_data['licences_qnt'],
        'purchaser_id'  => $user->uid, 
        );
    
    return $this->buildSubscriptionObjFillData($entity_data);
  }

}

?>