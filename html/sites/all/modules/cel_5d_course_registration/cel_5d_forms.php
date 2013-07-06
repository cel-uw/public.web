<?php 
module_load_include('php', 'cel_5d_course_registration', 'repos/purchaser_repo');
	


/**
 * Build form which will build the licences form to be implemented
 *
 * @param unknown_type $form_state
 * @param unknown_type $nid_course
 * @return multitype:multitype:string
 */
function cel_5d_course_registration_licences_form(&$form_state, $module_nid)
{
	module_load_include('php', 'cel_5d_course_registration', 'repos/subscriber_repo');
	drupal_add_js(drupal_get_path('module', 'cel_5d_course_registration') .'/cel_5d_course_registration.js');
	module_load_include('php', 'cel_5d_course_registration', 'repos/trial_repo');

	
	// initialize vars
	global $user;
	
	if(!$user->uid){
		$node = node_load($module_nid);
// 		drupal_set_message('Please login First', 'warning');
		drupal_goto($node->path);
	}
	
	$trial_ver = FALSE;
	$purchaserRepo 	= new PurchaserRepo();	
	$trialRepo		= new TrialRepo();
	
	// specify that the current version is a trial version of the form
	if(arg(2) == 'trial'){
		$trial_ver = TRUE;
	}
	
	
	if(arg(1) =='trial' && arg(2)=='start'){
		// redirect to the same page just different url
		drupal_goto('5d-course/473/trial');
		$trial_ver = TRUE;
	}
	
	
	// add message ont he tpo of the form
	if($trial_ver){
		$form['msg'] = array ( '#value' 	=> 'Please complete the fields below to begin a trial subscription.' );
	}else{
		$form['msg'] = array ( '#value' 	=> 'Please complete the fields below to make a purchase.' );
	}
	
	// check if there is any income quantity info
	$licences_qnt = cel_5d_get_licences_qnt_chosed_by_registrar();
	
	
	// set up hidden fields
	$form['licences_qnt'] 	= array( '#type'=> 'hidden', '#value'	=> $licences_qnt);
	$form['module_nid'] 	= array('#type'	=> 'hidden', '#value'	=> $module_nid);
	
	
	// set payer information fieldset
	if($trial_ver == FALSE){
		$form ['purchaser'] = array ( '#type' 	=> 'fieldset', '#title' 	=> 'Purchaser Info', );
	}else{
		$form ['purchaser'] = array ( '#type' 	=> 'fieldset', '#title' 	=> 'User Info', );
	}
	
	// get purchaser info from DB
	$purchaserByID = $purchaserRepo->getPurchaserByUserID($user->uid);

	// set purchaser info fields
	$form['purchaser']['profile_first_name'] = 	array(
			'#type'	=>'textfield',
			'#title'=> 'First Name',
			'#size'	=> 20,
			'#default_value'=> $purchaserByID->getProfile_first_name(),
			'#required' 	=> TRUE,
			);
	$form['purchaser']['profile_last_name'] = 	array(
			'#type'	=>'textfield',
			'#title'=> 'Last Name',
			'#size'	=> 20,
			'#default_value'=> $purchaserByID->getProfile_last_name(),
			'#required' 	=> TRUE,
			);	
	$form['purchaser']['preferred_name'] = 	array(
			'#type'	=>'textfield',
			'#title'=> 'Preferred Name',
			'#size'	=> 20,
			'#default_value'=> $purchaserByID->getPreferred_name(),
			);	
	$form['purchaser']['profile_current_position'] = 	array(
			'#type'	=>'textfield',
			'#title'=> 'Current Position/Title',
			'#size'	=> 20,
			'#default_value'=> $purchaserByID->getProfile_current_position(),
			'#required' 	=> TRUE,
			);
	$form['purchaser']['daytime_phone'] = 	array(
			'#type'	=>'textfield',
			'#title'=> 'Daytime phone',
			'#size'	=> 20,
			'#default_value'=> $purchaserByID->getDaytime_phone(),
			);
	$form['purchaser']['profile_organization'] = 	array(
			'#type'	=>'textfield',
			'#title'=> 'School/District/Organization',
			'#size'	=> 40,
			'#default_value'=> $purchaserByID->getProfile_organization(),
			'#required' 	=> TRUE,
			);
	
	
	// add form buttons
	$form['ok'] =  array (
			'#weight'	=> '99',
			'#type' 	=> 'submit',
			'#value' 	=> 'Continue' );
	
	
	// TRIAL 
	if($trial_ver)
	{
		// check if user is already subscribed for trial
		if($trialRepo->getTrialByUserID($user->uid)){
			drupal_set_message(t('You have been already subscribed for a trial period'));
			drupal_goto(node_load($module_nid)->path);
		}
		
		// add hidden field for trial usecase
		$form['trial'] 	= array('#type'	=> 'hidden', '#value'	=> $module_nid);

		return $form;   			// return the form an this point, don't need any other data
	}	
	
	
	// if user is asking for one licence, 
	if($licences_qnt == 1)
	{
		// display checkbox only for user with 1 licence
		$form['purchaser']['purchaser_licence'] = 	array(
				'#type'	=>'checkbox',
				'#title'=> 'Access to 5D E-Learning is for myself',
		);	
	}
	
	$form['purchaser']['msg2'] = array ( '#value' 	=> 'Please enter the required information to create subscriptions and access the 5D E-Learning Series - Purposeful Instruction Course of Study.' );
	
	// setting up subscribers fieldset
	$form ['subscribers'] = array ( '#type' => 'fieldset', );
	
	// build set of fields for each licence
	for($i=1; $i <= $licences_qnt; $i++)
	{
		$sufix = '_subscriber' . $i;
		
		// building a fieldset for each subscriber data fields
		$form ['subscribers'][$sufix] = array ( '#type' => 'fieldset', '#title' => 'Subscriber '.$i, '#attributes' => array('id'=>'subscriber_info'.$i),);
		
		// add subscribers infor fields
		$form['subscribers'][$sufix]['mail'.$sufix] = array(
				'#type'		=>'textfield',
				'#title'	=> 'Email',
				'#size'		=> 20,
				'#required' => ($licences_qnt > 1) ? TRUE : FALSE,				
				);
		$form['subscribers'][$sufix]['profile_organization'.$sufix] = array(
				'#type'		=>'textfield',
				'#title'	=> 'School Name',
				'#size'		=> 20,
				'#required' => ($licences_qnt > 1) ? TRUE : FALSE,				
			);
		$form['subscribers'][$sufix]['profile_first_name'.$sufix] = array(
				'#type'	=>'textfield',
				'#title'=> 'First Name',
				'#size'	=> 20,
				'#required' => ($licences_qnt > 1) ? TRUE : FALSE,
			);
		$form['subscribers'][$sufix]['profile_last_name'.$sufix] = array(
				'#type'	=>'textfield',
				'#title'=> 'Last Name',
				'#size'	=> 20,
				'#required' => ($licences_qnt > 1) ? TRUE : FALSE,
			);	
		$form['subscribers'][$sufix]['profile_current_position'.$sufix] = array(
				'#type'	=>'textfield',
				'#title'=> 'Current position/title',
				'#size'	=> 20,
				'#required' => ($licences_qnt > 1) ? TRUE : FALSE,
		);			
		
		// add start date field
// 		$form['subscribers'][$sufix]['start_date'.$sufix] = array( '#type' 	=> 'date', '#title' 	=> 'Choose start date', );		
	}
	
	
	// set payment info options redios
	$form['subscribers']['payment_type'] = array(
			'#type'		=> 'radios',
			'#title'	=> t('Payment Method'),
			'#options'	=> array(
// 					'credit_card' => t('Credit Card'),
					'po'	=> 'PO',
// 					'contract'	=> t('Contract'),
					),
			'#default_value'	=> 'po',
			'#required'	=> TRUE,
			'#attributes' => array('class'=>'payment_info'),
			);
	
	// set default value for payment option
	if(isset($_SESSION['subscription_info']['payment_type'])){
		$form['subscribers']['payment_type']['#default_value'] = $_SESSION['subscription_info']['payment_type'];
	}
	
	
	$form['subscribers']['po_number'] = array(
			'#type'	=>'textfield',
			'#title'=> 'PO number',
			'#size'	=> 20,
	);
	


	return $form;
}


/**
 * Validate subscribers email address
 * 
 * @param array $form_state
 * @param array $form_values
 */
function cel_5d_course_registration_licences_form_validate(&$form_state, $form_values)
{
	// check if checkbox is checked
	if(isset($form_values['values']['purchaser_licence']) && $form_values['values']['purchaser_licence'] == '1'){
		return TRUE;
	}
	
	// walking through the list of submitted values
	foreach($form_values['values'] as $key => $value)
	{
		// check if current field is a subscriber email
		if(strstr($key, 'mail_subscriber'))
		{
			// validate email address
			if(!valid_email_address($value)){	
				form_set_error($key, t('The email address appears to be invalid.'));
			}
		}
	}
}


/**
 * Registration Licences form submit function
 * 
 * @param array $form_state
 * @param array $form_values
 */
function cel_5d_course_registration_licences_form_submit(&$form_state, $form_values)
{
	// initialize vars and objects
	global $user;
	$subscriptionRepo = new SubscriptionRepo();

	$submitted_data = $form_values['values'];

	// build payment data value
	if( array_get($submitted_data, 'payment_type') == 'po' && array_get($submitted_data, 'po_number') ){
		$payment_data_arr = array('po_number'=> array_get($submitted_data, 'po_number') );
		$submitted_data['payment_data'] = serialize($payment_data_arr);
	}
	
	// assign all the data submited on the first step to the session var
	$_SESSION['subscription_info'] = $submitted_data;

	$subscriptionObj = $subscriptionRepo->buildSubscriptionObjFromSubmittedData($submitted_data);
	
	drupal_goto('5d-course/'.$subscriptionObj->getModule_id().'/agreement');
}


/**
 * Building form which will hangle agreement checkbox functionality
 * @param array $form_state
 * @param int $nid_course
 */
function cel_5d_course_registration_agreement_form(&$form_state, $module_nid)
{	
	// check if this is the right module
	if(!isset($_SESSION['subscription_info']['module_nid']) || $_SESSION['subscription_info']['module_nid'] !== $module_nid ){
		drupal_goto('node/'.$module_nid);
	}
	
// 	echo '<pre>';
// 	print_r($_SESSION);
	
	$form['module_nid'] = array(
			'#type'	=> 'hidden',
			'#value'	=> $module_nid
	);	
	
	// add checkbox field to a form
	$form ['agreement_confirm'] = array (
			'#type' 	=> 'checkbox', 
			'#title' 	=> 'I agree to the terms and conditions of this Agreement and I am the Trial User duly authorized to enter into this Agreement.<br>If you do not agree, exit this program.', 
			'#return_value'	=> 'agree',
			'#required' => TRUE, 
			);
	
	// add form submit button
	$form['ok'] =  array (
			'#type' 	=> 'submit',
			'#value' 	=> 'Continue' );
	
	return $form;
}


/**
 * License agreement form submit handler
 * @param array $form_state
 * @param array $form_values
 */
function cel_5d_course_registration_agreement_form_submit(&$form_state, $form_values)
{	
	global $user; 
	
	// init subscription repo obj
	$subscriptionRepo 	= new SubscriptionRepo();
	$subscriptionObj 	= $subscriptionRepo->buildSubscriptionObjFromSubmittedData( $_SESSION['subscription_info'] );

	// handle trial functionality
	if(array_get($_SESSION['subscription_info'], 'trial'))
	{
		module_load_include('php', 'cel_5d_course_registration', 'repos/trial_repo');
		module_load_include('php', 'cel_5d_course_registration', 'classes/email_notification_class');
		
		// init repos
		$trialRepo 	= new TrialRepo();
		$purchaserRepo 	= new PurchaserRepo();
// 		$subscriptionObj= new Subscription();
		
		
		// assign user role to current user
		$purchaserObj = $purchaserRepo->getPurchaserObjOnLoggedInUser();
		
		// update user info and return new Purchaser object filled with data
		$purchaserObjSubmitted = $purchaserRepo->buildPurchaserObjFillData( array( $user, array_get($_SESSION,'subscription_info') ), TRUE );
		
		// apply purchaser changes into DB
		$purchaserRepo->updatePurchaserInfoDB($purchaserObjSubmitted, new Subscription());		

		
		// build trial Obj
		$trialObj = $trialRepo->buildTrialObjFillData( array('uid'=>$purchaserObjSubmitted->getUid()) );
		
		// create new trial subscription and send notification
		if($newTrialObj = $trialRepo->createNewTrial($trialObj) )
		{
			// create email notification class object
			$emailNotifObj = new EmailNotificationClass($subscriptionObj->getModule_id());
			$emailNotifObj->sendTrialConfirmationEmailToPurchaser($newTrialObj, $purchaserObj);
			$emailNotifObj->sendNewTrialNotificationToAdmin($purchaserObj);
			
		}
		
		// destroy all the temp session data
		unset($_SESSION['subscription_info']);
		
		drupal_goto('5d-course/trial/success');
	}
	
	// redirect to order confirmation page
	drupal_goto('5d-course/'.$subscriptionObj->getModule_id().'/payment/confirm');
}


function cel_5d_credit_card_form($module_id){
	$out = 'Credit card Info';
	return $out;
}


/**
 * Build submit button form for confirmation page
 * 
 * @param array $form_state
 * @param nid $module_nid
 * @return array 
 */
function cel_5d_course_registration_confirmation_form(&$form_state, $module_nid)
{
	// add form submit button
	$form['ok'] =  array (
			'#type' 	=> 'submit',
			'#value' 	=> 'Confirm' );
	return $form;
}


/**
 * This is the last step of process
 * at this poins we are adding into the DB all the data colected
 * 
 * @param array $form_state
 * @param array $form_values
 */
function cel_5d_course_registration_confirmation_form_submit(&$form_state, $form_values)
{
	global $user;
	// load payment repository
	module_load_include('php', 'cel_5d_course_registration', 'repos/payment_repo');
	module_load_include('php', 'cel_5d_course_registration', 'repos/subscriber_repo');
	module_load_include('php', 'cel_5d_course_registration', 'classes/email_notification_class');
	
	// get submitted Data 
	$submitted_data = $_SESSION['subscription_info'];
	// initialize all the rpositories objects
	$paymentRepo 		= new PaymentRepo();
	$subscriptionRepo 	= new SubscriptionRepo();
	$subscriberRepo 	= new SubscriberRepo();
	$purchaserRepo 		= new PurchaserRepo();
	
	// building needed objects
	$paymentObj 		= $paymentRepo->buildPaymentObjFromSubmittedData($submitted_data);
	$subscriptionObj 	= $subscriptionRepo->buildSubscriptionObjFromSubmittedData($submitted_data);
	$subscribersObjArr	= $subscriberRepo->getSubscribersFromSubmitedData($submitted_data);	
	
	// if payment type is credit card, then redirect to card info page
	if($paymentObj->getPayment_type() == 'credit_card'){
		drupal_goto('/5d-course/'.$subscriptionObj->getModule_id().'/payment/card_info');
	}
	
	
	// update user info and return new Purchaser object filled with data
	$purchaserObjSubmitted = $purchaserRepo->buildPurchaserObjFillData(array($user, $submitted_data), TRUE);
	
	// apply purchaser changes into DB
	$purchaserRepo->updatePurchaserInfoDB($purchaserObjSubmitted, $subscriptionObj);

	
	// SAVE THE PAYMENT INFO
	$newPaymenObj = $paymentRepo->createNewPayment($paymentObj, $subscriptionObj->getModule_id());
	
	// SAVE SUBSCRIPTION INFO
	$newSubscriptionObj = $subscriptionRepo->createNewSubscription($subscriptionObj, $newPaymenObj);

	// CREATE NEW SUBSCRIBERS
	$new_subscribers_obj_arr = $subscriberRepo->createSubcribersFromSubscribersArray($subscribersObjArr, $newSubscriptionObj);
	
	
	//===== NOTIFICATIONS
	// create email notification class object	
	$emailNotifObj = new EmailNotificationClass($newSubscriptionObj->getModule_id());
	
	$newPurchaserObj = $purchaserRepo->getPurchaserObjOnLoggedInUser();
	
	// sent notification to purchaser
	$emailNotifObj->sendConfirmationEmailToPurchaser($newPurchaserObj, $newSubscriptionObj);
	$emailNotifObj->sendNewSubscriptionNotificationToAdmin($newPurchaserObj, $newSubscriptionObj);
	
	// check if user is buyng licence for itself
	if(array_get($submitted_data, 'purchaser_licence'))
	{
		// build subscriber Object based on my info
		$mySubscriberObj = $subscriberRepo->getSubscriberByUserID($user->uid);
		
		$subscriptionRepo->mapSubscriberToSubscription($newSubscriptionObj, $mySubscriberObj);
	}
	

	// MAP SUBSCRIBERS TO SUBSCRIPTION and SENDING EMAIL NOTIFICATIONS
	$subscriptionRepo->mapSubscribersArrToSubscription($newSubscriptionObj, $new_subscribers_obj_arr);
	
	
	
		
	
	
	// destroy all the temp session data
	unset($_SESSION['subscription_info']);
	
	// redirect to success page
	drupal_goto('5d-course/success');
	
}


/**
 * Altering reigstration form, adding new submit heandler
 *
 * @param array $form
 * @param array $form_state
 * @param string $form_id
 * @return array
 */
function cel_5d_course_registration_form_alter(&$form, $form_state, $form_id)
{

	// altering user registration form
	if ($form_id == 'user_register')
	{
		
		// if this is a trial registration page then add new submit hamdler
		if(arg(1) && arg(1) == 'trial')
		{
			$login_msg = '<h3>If you already have an account please '.l('login', '5d-course/trial_login').' using your credentials.</h3><h3>If you don\'t already have an account with CEL, register below.</h3><br>';
			$form['msg'] = array ( 
						'#value' 	=> $login_msg,
						'#weight'	=> -20, 
					);
			$form['cel_5d_course_registration_target'] 	= array('#type'	=> 'hidden', '#value'	=> 'trial_registration');
			$form['#submit'][] = 'cel_5d_course_registration_user_register_submit';
		}
		elseif (arg(2) && arg(2) == 'course_registration')
		{
			if(!arg(1)){
				drupal_goto('');
			}
			
			$module_nid = intval(arg(1));
			
			$login_msg = '<h3>If you already have an account please '.l('login', '5d-course/'.$module_nid.'/course_login').' using your credentials.</h3><h3>If you don\'t already have an account with CEL, register below.</h3><br><br>';
			// display explanation msg
			$form['msg'] = array (
					'#value' 	=> $login_msg,
					'#weight'	=> -20,
			);
			
			$form['module_nid'] 	= array('#type'	=> 'hidden', '#value'	=> $module_nid);
			$form['cel_5d_course_registration_target'] 	= array('#type'	=> 'hidden', '#value'	=> 'course_registration');
			
			$form['#submit'][] = 'cel_5d_course_registration_user_register_submit';
		}
	}
	elseif($form_id == 'user_login')
	{
		// if this is a trial registration page then add new submit hamdler
		if(arg(1) && arg(1) == 'trial_login')
		{
			// add message on the top of the form
			$form['msg'] = array (
					'#value' 	=> '<h3>If you don\'t have an account please '.l('register', '5d-course/trial/register').' first.</h3><br><br>',
					'#weight'	=> -20,
			); 
						
			$form['cel_5d_course_registration_target'] 	= array('#type'	=> 'hidden', '#value'	=> 'trial_login');
			$form['#submit'][] = 'cel_5d_course_registration_user_register_submit';
		}
		elseif (arg(2) && arg(2) == 'course_login')
		{
			if(!arg(1)){
				drupal_goto('');
			}
				
			$module_nid = intval(arg(1));
			
			// add message on the top of the form
			$form['msg'] = array (
					'#value' 	=> '<h3>If you don\'t have an account please '.l('register', '5d-course/'.$module_nid.'/course_registration').' first.</h3><br><br>',
					'#weight'	=> -20,
			);
						
			$form['module_nid'] 	= array('#type'	=> 'hidden', '#value'	=> $module_nid);
			$form['cel_5d_course_registration_target'] 	= array('#type'	=> 'hidden', '#value'	=> 'course_login');
				
			$form['#submit'][] = 'cel_5d_course_registration_user_register_submit';
		}		
	}
	
	
	return $form;
}


/**
 * User Registration additional submit function
 * This functoin is used to add needed temporary roles to user just created
 * 
 * @param array $form
 * @param array $form_state
 * @return boolean
 */
function cel_5d_course_registration_user_register_submit($form, &$form_state)
{
	global $user;
	
	module_load_include('php', 'cel_5d_course_registration', 'repos/roles_repo');
	
	$rolesRepo = new RolesRepo();
	
	
	// get submitted values
	$values = $form_state['values'];

	if(!$user->uid){
		// get new user object data
		$user = $form_state['user'];
	}
	
	// get action target value from the submitted form data
	$form_target_action = fuse_array_get($values, 'cel_5d_course_registration_target');

	// getting current user roles
	$user_roles = $user->roles;
	$user_info = array();
			
	switch ($form_target_action) {
		case 'trial_registration':
			$user_roles[15]	= '5D Trial Temp';
			$user_info['roles'] = $user_roles;
			
			user_save($user, $user_info);
			session_destroy();
		break;
		case 'trial_login':
			$user_roles[15]	= '5D Trial Temp';
			$user_info['roles'] = $user_roles;
			user_save($user, $user_info);
		break;		
		
		case 'course_registration':
			// getting module node ID 
			$module_nid = fuse_array_get($values, 'module_nid');
			
			$temp_role = $rolesRepo->getTemporaryRegistrarRole($module_nid);
			
			// setting new temp user role info
			$user_roles[$temp_role->rid]	= $temp_role->name;
		
			$user_info['roles'] = $user_roles;
			
			user_save($user, $user_info);
			session_destroy();
		break;
		
		case 'course_login':
			// getting module node ID
			$module_nid = fuse_array_get($values, 'module_nid');
				
			$temp_role = $rolesRepo->getTemporaryRegistrarRole($module_nid);
				
			// setting new temp user role info
			$user_roles[$temp_role->rid]	= $temp_role->name;
		
			$user_info['roles'] = $user_roles;
				
			user_save($user, $user_info);
		break;
	}
	

	return TRUE;
}