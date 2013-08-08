<?php

/**
 * Notifications class responsible for sending all kind of notifications emails
 *
 * @author vparfaniuc
 *        
 *        
 */

class EmailNotificationClass {
	private $module_nid;		// module node ID
	private $module_node_obj;	// module node stdObj
	private $replace_patterns;	// arr for dynamic heanlers which can be used by use in the text 
	
	
	/**
	 * Setting up default values needed for new object initialization
	 * @param int $module_nid
	 */
	public function __construct($module_nid)
	{
		// validate node id
		$module_nid = (int) $module_nid;
		if(empty($module_nid)) {
			return FALSE;
		}
		
		// load node object into class private var
		$this->module_node_obj = node_load($module_nid);
	}

	
	/**
	 * Send Subscription email confirmation to subscriber 
	 * 
	 * @param Purchaser $purchaserObj
	 * @return boolean
	 */
	public function sendConfirmationEmailToPurchaser(Purchaser $purchaserObj, Subscription $subscriptionObj)
	{
		$patterns = array(
			'{purchaser:first_name}'		=> $purchaserObj->getProfile_first_name(),
			'{subscription:licences_qty}'	=> $subscriptionObj->getLicences_qty(),
			'{subscription:expire_date}'	=> date("m/d/Y", strtotime( $subscriptionObj->getExpire_date() ) ),
		);
		
		
		// define vars values
		$body 	= $this->getProcessedEmailBody('field_email_confirm_purchaser', $patterns);
		
		$subject= nodeObj_get_var($this->module_node_obj, 'field_email_conf_purchaser_sbj');
		$to 	= $purchaserObj->getMail();
		
		
		// send email to the user in HTML format
		return $this->mailSend($to, $subject, $body, TRUE);
	}
	
	
	/**
	 * Sending CEL admin notificaiton email about new subscription submittion
	 * 
	 * @param Purchaser $purchaserObj
	 * @param Subscription $subscriptinObj
	 * @return Ambigous <Returns, boolean>
	 */
	public function sendNewSubscriptionNotificationToAdmin(Purchaser $purchaserObj, Subscription $subscriptinObj)
	{
		global $domain;
		
		// destination email
		//$to = 'melissa@fuseiq.com';
                $to = variable_get('site_mail', '');
		
		$subject = 'New 5D E-Learning Series Subscription Created';
		$body = $purchaserObj->getProfile_first_name() . ' ' . $purchaserObj->getProfile_last_name() . ' (' . $purchaserObj->getMail() . ') ' . ' has registered ' . $subscriptinObj->getLicences_qty() . ' Subscribers for 5D elearning course<br>';
		$body .= 'Registration Record '.l('Link','5d-courses-admin/subscription/'.$subscriptinObj->getSid().'/details', array(absolute => TRUE));
		// send email to the user in HTML format
		return $this->mailSend($to, $subject, $body, TRUE);
	}
	
	
	/**
	 * Send email notification to CEL admin about new Trial subscription
	 * @param Purchaser $purchaserObj
	 * @return Ambigous <Returns, boolean>
	 */
	public function sendNewTrialNotificationToAdmin(Purchaser $purchaserObj)
	{
		global $domain;
		
		// destination email
		//$to = 'melissa@fuseiq.com';
                $to = variable_get('site_mail', '');
		
		$subject = 'New 5D E-Learning Series Trial Submitted';
		$body = $purchaserObj->getProfile_first_name() . ' ' . $purchaserObj->getProfile_last_name() . ' (' . $purchaserObj->getMail() . ') ' . ' has registered for 5D E-Learning Series Trial<br>';

		// send email to the user in HTML format
		return $this->mailSend($to, $subject, $body, TRUE);		
	}
	
	
	/**
	 * Sending email notification to CEL admin about new Trial Susbscription submitted
	 * 
	 * @param Subscriber $subscriberObj
	 * @return Ambigous <Returns, boolean>
	 */
	public function sendConfirmationEmailToSubscriber(Subscription $subscriptionObj, Subscriber $subscriberObj)
	{
		$patterns = array(
				'{subscriber:first_name}'		=> $subscriberObj->getProfile_first_name(),
				'{subscription:licences_qty}'	=> $subscriptionObj->getLicences_qty(),
				'{subscription:expire_date}'	=> date("m/d/Y", strtotime( $subscriptionObj->getExpire_date() ) ),
		);
		
		
		// define vars values
		$body 	= $this->getProcessedEmailBody('field_email_confirm_subscriber', $patterns);
		$subject= nodeObj_get_var($this->module_node_obj, 'field_email_conf_subscriber_sbj');
		$to 	= $subscriberObj->getMail();
		
		
		// send email to the user in HTML format
		return $this->mailSend($to, $subject, $body, TRUE);		
	}
	
	
	// TRIAL USER NOTIFICATIONS
	public function sendTrialConfirmationEmailToPurchaser(Trial $trialObj,Purchaser $purchaserObj)
	{
		$patterns = array(
				'{trial:first_name}'	=> $purchaserObj->getProfile_first_name(),
				'{trial:expire_date}'	=> date("m/d/Y", strtotime( $trialObj->getExpire_date() ) ),
		);
		
		// define vars values
		$body 	= $this->getProcessedEmailBody('field_email_trial_confirm', $patterns);
		$subject= nodeObj_get_var($this->module_node_obj, 'field_email_trial_confirm_sbj');
		$to 	= $purchaserObj->getMail();
		
		
		// send email to the user in HTML format
		return $this->mailSend($to, $subject, $body, TRUE);		
	}
	
	
	/**
	 * Sending notifications to users registered 2 days ago
	 * 
	 * @return boolean
	 */
	public function sendTrialdNotificationEmail_2_DaysAfterRegistration()
	{
		// load needed repos
		module_load_include('php', 'cel_5d_course_registration', 'repos/trial_repo');
		module_load_include('php', 'cel_5d_course_registration', 'repos/purchaser_repo');
		$trialRepo 		= new TrialRepo();
		$purchaserRepo 	= new PurchaserRepo();

		

		$subject= nodeObj_get_var($this->module_node_obj, 'field_email_trial_usage_tips_sbj');
		
		
		// send notifications to trial users registered 2 days ago
		$trials = $trialRepo->getTrialsRegisteredDaysAgoByDays(2);

		// validate list of users
		if($trials && !empty($trials))
		{
			/* @var $trialObj Trial */
			foreach ($trials as $trialObj)
			{
				// build purchaser object
				$purchaserObj = $purchaserRepo->getPurchaserByUserID($trialObj->getUid());
				// specify patters
				$patterns = $trialRepo->emailsHandlersSpecifications($purchaserObj, $trialObj);
				
				// define vars values
				$body 	= $this->getProcessedEmailBody('field_email_trial_usage_tips', $patterns);				
				
				// sent email to user
				$this->mailSend($purchaserObj->getMail(), $subject, $body, TRUE);
			}
		}		
		
		// send email to the user in HTML format
		return TRUE;
	}
	
	
	/**
	 * Send trial email notifications before the trial expires
	 * 
	 * @param int $days
	 * @return boolean
	 */
	public function sendTrialdNotificationEmailDaysBeforRegistrationExpires($days=7)
	{
		// load needed repos
		module_load_include('php', 'cel_5d_course_registration', 'repos/trial_repo');
		module_load_include('php', 'cel_5d_course_registration', 'repos/purchaser_repo');
		$trialRepo 		= new TrialRepo();
		$purchaserRepo 	= new PurchaserRepo();
	
	
	
		$subject= nodeObj_get_var($this->module_node_obj, 'field_email_trial_place_ord_sbj');
	
	
		// send notifications to trial users registered 2 days ago
		$trials = $trialRepo->getTrialUserIDsExpireBeforeDaysByDays($days);
	
		// validate list of users
		if($trials && !empty($trials))
		{
			foreach ($trials as $trialObj)
			{
				// build purchaser object
				$purchaserObj = $purchaserRepo->getPurchaserByUserID($trialObj->getUid());
	
				// specify patters
				$patterns = $trialRepo->emailsHandlersSpecifications($purchaserObj, $trialObj);
	
				// define vars values
				$body 	= $this->getProcessedEmailBody('field_email_trial_place_order', $patterns);
	
				// sent email to user
				$this->mailSend($purchaserObj->getMail(), $subject, $body, TRUE);
			}
		}
	
		// send email to the user in HTML format
		return TRUE;
	}	
	
	
	
	public function sendTrialFeedbackRequestDaysAfterSubscriptionExpires($days)
	{
		if(!$days){
			return FALSE;
		}
		
		// load needed repos
		module_load_include('php', 'cel_5d_course_registration', 'repos/trial_repo');
		module_load_include('php', 'cel_5d_course_registration', 'repos/purchaser_repo');
		module_load_include('php', 'cel_5d_course_registration', 'repos/subscriber_repo');
		$trialRepo 		= new TrialRepo();
		$purchaserRepo 	= new PurchaserRepo();
		$subscriberRepo = new SubscriberRepo();
		
		
		
		$subject= nodeObj_get_var($this->module_node_obj, 'field_email_trial_feedback_sbj');
		
		
		// send notifications to trial users registered 2 days ago
		$trials = $trialRepo->getTrialUsersIDExpiredDaysAgoByDays($days);
	
		// validate list of users
		if($trials && !empty($trials))
		{
			foreach ($trials as $trialObj)
			{
				// build purchaser object
				$purchaserObj = $purchaserRepo->getPurchaserByUserID($trialObj->getUid());
		
				// TODO: Add validation to check if user purhcased a licence then do not send this email
				
				// specify patters
				$patterns = $trialRepo->emailsHandlersSpecifications($purchaserObj, $trialObj);
		
				// define vars values
				$body 	= $this->getProcessedEmailBody('field_email_trial_feedback', $patterns);
		
				// sent email to user
				$this->mailSend($purchaserObj->getMail(), $subject, $body, TRUE);
			}
		}
		
		// send email to the user in HTML format
		return TRUE;		
	}
	
	
	
	public function sendSubscriberurhcaserEmailNotification($days, $rule = 'beforeSubscriptionExpires')
	{
		module_load_include('php', 'cel_5d_course_registration', 'repos/purchaser_repo');
		module_load_include('php', 'cel_5d_course_registration', 'repos/subscriber_repo');
		module_load_include('php', 'cel_5d_course_registration', 'repos/subscription_repo');

		$purchaserRepo 	= new PurchaserRepo();
		$subscriberRepo = new SubscriberRepo();		
		$subscriptionRepo = new SubscriptionRepo();
		
		
		$purchaser_email_subject_field 	= '';
		$purchaser_email_body_field 	= '';
		$subscriber_email_subject_field = '';
		$subscriber_email_body_field	= '';
		
		// setting rules
		if($rule == 'beforeSubscriptionExpires')
		{
			switch ($days) {
				case 15:
					$purchaser_email_subject_field 	= 'field_email_renew_15_sbj';
					$purchaser_email_body_field 	= 'field_email_renew_15';
					$subscriber_email_subject_field = 'field_email_sbscrb_renew_15_sbj';
					$subscriber_email_body_field	= 'field_email_sbscrb_renew_15';
				break;	
				case 30:
					$purchaser_email_subject_field 	= 'field_email_renew_30_sbj';
					$purchaser_email_body_field 	= 'field_email_renew_30';
					$subscriber_email_subject_field = 'field_email_sbscrb_renew_30_sbj';
					$subscriber_email_body_field	= 'field_email_sbscrb_renew_30';
				break;
				case 60:
					$purchaser_email_subject_field 	= 'field_email_renew_60_sbj';
					$purchaser_email_body_field 	= 'field_email_renew_60';
					$subscriber_email_subject_field = 'field_email_sbscrb_renew_60_sbj';
					$subscriber_email_body_field	= 'field_email_sbscrb_renew_60';					
				break;
			}
			
			// get array of subscriptins which are specific days before expire
			$subscriptionsObj_arr = $subscriptionRepo->getSubscriptionsExpireInDays($days);
		}elseif('afterSubscriptionStarts'){
			switch ($days) {
				case 7:
					$subscriber_email_subject_field = 'field_email_usage_tips_sbj';
					$subscriber_email_body_field	= 'field_email_usage_tips';
					break;
				case 60:
					$subscriber_email_subject_field = 'field_email_check_in_sbj';
					$subscriber_email_body_field	= 'field_email_check_in';
					break;
			}			
			
			// get array of subscriptions started days ago
			$subscriptionsObj_arr = $subscriptionRepo->getSubscriptionsStartedDaysAgo($days);
		}
		
		
		if(!$subscriptionsObj_arr || empty($subscriptionsObj_arr)){
			return FALSE;
		}

		/* @var  $subscriptionObj Subscription */
		foreach($subscriptionsObj_arr as $subscriptionObj)
		{
			if($rule == 'beforeSubscriptionExpires')
			{
				// PURCHASER email
				$purchaserObj = $purchaserRepo->getPurchaserByUserID($subscriptionObj->getRegistrar_id());
				
				$patterns = array(
						'{purchaser:first_name}'		=> $purchaserObj->getProfile_first_name(),
						'{subscription:licences_qty}'	=> $subscriptionObj->getLicences_qty(),
						'{subscription:expire_date}'	=> date("m/d/Y", strtotime( $subscriptionObj->getExpire_date() ) ),
				);
				
				
				// define vars values
				$body 	= $this->getProcessedEmailBody($purchaser_email_body_field, $patterns);
				
				$subject= nodeObj_get_var($this->module_node_obj, $purchaser_email_subject_field);
				$to 	= $purchaserObj->getMail();
				
				// send email to the purchaser in HTML format
				$this->mailSend($to, $subject, $body, TRUE);			
			}
			
			// SUBSCRIBERS email
			// get list of subscriber objects
			$subscribersObj_arr = $subscriberRepo->getSubscribersBySubscriptionID($subscriptionObj->getSid());
			
			// if empty arr then just continue foreach
			if(!$subscribersObj_arr || empty($subscribersObj_arr)){
				continue;
			}
			
			/* @var  $subscriberObj Subscriber */
			foreach($subscribersObj_arr as $subscriberObj)
			{
				$patterns = array(
						'{subscriber:first_name}'		=> $subscriberObj->getProfile_first_name(),
						'{subscription:expire_date}'	=> date("m/d/Y", strtotime( $subscriptionObj->getExpire_date() ) ),
				);
				
				
				
				// define vars values
				$body 	= $this->getProcessedEmailBody($subscriber_email_body_field, $patterns);
				$subject= nodeObj_get_var($this->module_node_obj, $subscriber_email_subject_field);
				$to 	= $subscriberObj->getMail();
				
				
				// send email to the user in HTML format
				 $this->mailSend($to, $subject, $body, TRUE);								
			}
		}
		
	}
	

	
	/**
	 * Funciton will process the body string replacing all the dinamic data
	 * and performing all cleaning operations
	 * 
	 * @return string
	 */
	private function getProcessedEmailBody($body_field_title, $patterns=array())
	{
		$body 	= nodeObj_get_var($this->module_node_obj, $body_field_title);

		$patterns = $this->buildPatternsArrsByArrAssoc($patterns);
		$body	= str_ireplace($patterns['keys'], $patterns['replacements'], $body);
		return nl2br($body);
	}
	
	
	private function buildPatternsArrsByArrAssoc($patternsAssocArr)
	{
		$search_arr = array();
		$replace_arr= array();
		
		foreach ($patternsAssocArr as $search_key=>$replace_val)
		{
			$search_arr[]	= $search_key;
			$replace_arr[]	= $replace_val;
		}
		
		return array('keys'=>$search_arr, 'replacements'=>$replace_arr);
	}
	
	
	
	/**
	 * Execute internal drupal send mail function
	 * 
	 * @param string $subject
	 * @param string $body
	 * @param bool $html
	 * 
	 *  @return
 	 *   Returns TRUE if the mail was successfully accepted for delivery,
 	 *   FALSE otherwise.
	 */
	private function mailSend($to, $subject, $body, $html=FALSE)
	{
		$message = array(
				'to' 	=> $to,
				'from' 	=> 'edlead@u.washington.edu',
				'subject' 	=> t($subject),
				'body' 		=> t($body),
				'headers' 	=> $this->buildHeaders($html),
		);
		
		return drupal_mail_send($message);
	}
	
	
	/**
	 * Build email headers array data
	 * 
	 * @param bool $html
	 * @return array
	 */
	private function buildHeaders($html=FALSE)
	{
		$headers = array();
		if ($html)
		{
			$headers = array(
					'From' => 'edlead@u.washington.edu',
					'MIME-Version' => '1.0',
					'Content-Type' => 'text/html;charset=utf-8',);
		}

		return $headers;
	}
}

?>