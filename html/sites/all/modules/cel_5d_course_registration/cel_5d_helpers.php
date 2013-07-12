<?php 

	/**
	 * Build buy now form for the CEL 5D module pages
	 * or login now form if user is not logged in  
	 * 
	 * @param unknown_type $node
	 * @return Ambigous <The, A, NULL, string>
	 */
	function cel_5d_helper_build_module_buy_now_form()
	{
		module_load_include('php', 'cel_5d_course_registration', 'repos/trial_repo');
		module_load_include('php', 'cel_5d_course_registration', 'classes/permissions_class');
		
		global $user;
		$out = '';
		
		// define empty object
		$node = new stdClass();
		
		// load node object if data was passed
		if(arg(1)){
			$node = node_load(arg(1));
		}
		
		// validate loaded node object
		if(!isset($node->type) || $node->type !== '5d_module'){
			return FALSE;
		}

		
		$trialRepo = new TrialRepo();
		$permissionsClass = new PermissionsClass();
		

		
		// if user is already a subscriber of current module do not display the buttons
		if($user->uid && !$permissionsClass->checkCurrentUserHasAccessBuyLicense($node->nid)){
			return '';
		}
		
		
		// hide Trial button for users already in the DB
		if(!$user->uid )
		{
			$out .= '<form method="POST" action="/5d-course/trial/register">';
			$out .= '<input type="submit" name="trial_init" value="Free Trial" />';
			$out .= '<br><br>';
			$out .= '</form>';
		}elseif($user->uid && !$trialRepo->getTrialByUserID($user->uid)){
			$out .= '<form method="POST" action="/5d-course/'.$node->nid.'/trial">';
			$out .= '<input type="submit" name="trial_init" value="Free Trial" />';
			$out .= '<br><br>';
			$out .= '</form>';
		}
		
		// BUY NOW button
		if(!$user->uid){
			$out .= '<form method="POST" action="/5d-course/'.$node->nid.'/course_registration">';
			$out .= '<input type="submit" value="Buy Now" />';
			$out .= '<br><br>';
			$out .= '</form>';
		}elseif($user->uid && $permissionsClass->checkCurrentUserHasAccessBuyLicense($node->nid))
		{
			$out .= '<form method="POST" action="/5d-course/'.$node->nid.'/licences">';
			$out .= '<div>Quantity:&nbsp;<input type="text" size="2" maxlength="2" value="1" name="licences_qnt" /></div>';
	// 		$out .= '<br><br>';
			$out .= '<input type="submit" value="Buy Now" />';
			$out .= '</form>';
		}
		
		return $out;
	}

	
	/**
	 * Get number of licences to operate with
	 * this licences could be passed in multiple ways: POST, SESSION, DB
	 * 
	 * @return number
	 */
	function cel_5d_get_licences_qnt_chosed_by_registrar()
	{
		// check fi there is any data passed through POST
		if(isset($_POST['licences_qnt']) && strlen($_POST['licences_qnt']) > 0){
			return intval($_POST['licences_qnt']);
		} 		
		
		// default value = 1 licence
		return 1;
	}
	
	
	
	/**
	 * Get userrole details by Role title
	 * 
	 * @param string $role_title
	 * @return stdClass
	 */
	function cel_5d_helper_get_userrole_by_role_title($role_title)
	{
		// build sql query
		$sql = db_query("SELECT *
				FROM {role}
				WHERE name = '%s'
				LIMIT 1", $role_title);

		// get role data form DB
		return db_fetch_object($sql);
	}
	
	
	function cel_5d_helper_build_credit_card_info_form(){
		$out = 'Credit card Info';
		
		$out .= '<form method="POST" action="/5d-course/473/payment/confirm" >';
		$out .= '<input type="submit" value="Confirm">';
		$out .= '</form>';
		
		return $out;
	}
	
	
//==================== FUNCTIONS TO BE MOVED INTO SEPARATE FuseIQ Libs module =======================\\
	
	/**
	 * returns array item or false if it's not exist
	 * @param array $array
	 * @param unknown_type $key
	 */
	function array_get(array $array, $key)
	{
		if(isset($array[$key])){
			return $array[$key];
		}	
		
		return FALSE;
	}
	
	
	/**
	 * Get the node standard Object field data
	 * NOTE: the field data should be accessible as
	 * $nodeObj->field_name[0][value] 
	 * 
	 * @param stdClass $nodeObj
	 * @param string $var_title
	 * @return Ambigious <bool, string>
	 */
	function nodeObj_get_var(stdClass $nodeObj, $var_title)
	{
		return fuse_get_nodeObj_var($nodeObj, $var_title, 'value');
	}
	
	
	/**
	 * Get the node standard Object field data
	 * NOTE: the field data should be accessible as
	 * $nodeObj->field_name[0][value]
	 *
	 * @param stdClass $nodeObj
	 * @param string $var_titl
	 * @param otional value index used in array structure, comes in [value], [nid]....
	 * @return Ambigious <bool, string>
	 */
	function fuse_get_nodeObj_var($nodeObj, $var_title, $var_ind = 'value')
	{
		// check variable obj param presence
		$var_items = field_get_items('node', $nodeObj, $var_title);
		if(empty($var_items)) {
			return false;
		}

		// We only get the first value, cause this function sucks
		$var_item = reset($var_items);
		if(!isset($var_item[$var_ind])){
			return false;
		}
	
		// return variable data
		return $var_item[$var_ind];
	}	
	
	
	
	/**
	 * FuseIQ debugging function 
	 * 
	 * @param unknown_type $var
	 * @param string $title
	 */
	function fuse_debug($var, $title=FALSE)
	{
		echo '<pre style=\"border: 1px solid red; padding:15px; overflow: auto;>';
		
		if($title){
			echo '<h3>'.$title.'</h3>';
		}
		
		print_r($var);
		echo '<pre>';
	} 
	
	
	/**
	 * FuseIQ debugging function with die after debugging info displayed 
	 * 
	 * @param unknown_type $var
	 * @param string $title
	 */
	function fuse_debug2($var, $title=FALSE){
		echo '<pre style=\"border: 1px solid red; padding:15px; overflow: auto;>';
		
		if($title){
			echo '<h3>'.$title.'</h3>';
		}
		
		print_r($var);
		echo '<pre>';
		die;
	}
	
	
	function fuse_dump($var, $title=FALSE){
		echo "<pre style=\"border: 1px solid red; padding:15px; overflow: auto; \">";
		echo '<h3>'.$title.'<h3>';
		var_dump($var);
		echo "</pre>\n";		
	}
	
	
	/**
	 * Pricing and buttons block visibility rules
	 * @return boolean
	 */
	function cel_5d_helper_pricing_block_visibility($module_id)
	{
		// validate module id url param
		if(!arg(1)){
			return FALSE;
		}

		// load user
		global $user;		

		
		// load permissions class
		module_load_include('php', 'cel_5d_course_registration', 'classes/permissions_class');
		$permissionsClass = new PermissionsClass();
		
		
		// load current node object
		$node = node_load(arg(1));
		
		// split current node path into sections
		list($arg1, $arg2) = explode('/', $node->path);

		// display only on the 5d-cources/* pages
		if (arg(0) !== 'node' || ($arg1 && $arg1 !== '5d-courses') || $arg2 != 'e-learning' ){
			return FALSE;
		}
		
		
		// hide block from subscribers
		if( $permissionsClass->checkLoggedInUserIsModuleSubscriber($module_id) && (!$permissionsClass->checkLoggedInUserDistrictRole() && !$permissionsClass->checkUserIsTrialSubscriberByUserID($user->uid) ) ){
			return FALSE;
		}
		
		// display block for district or trial users
		if($permissionsClass->checkLoggedInUserDistrictRole() || $permissionsClass->checkUserIsTrialSubscriberByUserID($user->uid)){
			return TRUE;
		}
		
		return TRUE;
	}
	
	
	/**
	 * Visibility rules for the left menu site menu 
	 * 
	 * @param int $module_id
	 * @return boolean
	 */
	function cel_5d_helper_application_menu_block_visibility($module_id)
	{
// 		drupal_set_message('Message Text', 'warning');
		// load permissions class
		module_load_include('php', 'cel_5d_course_registration', 'classes/permissions_class');
		$permissionsClass = new PermissionsClass();
		
		global $user;
		
		// load current node object
		$node = node_load(arg(1));
		
		// split current node path into sections
		list($arg1, $arg2) = explode('/', $node->path);

		// display only on the 5d-cources/* pages
		if (arg(0) !== 'node' || ($arg1 && $arg1 !== '5d-courses') || $arg2 != 'e-learning' ){
			return FALSE;
		}		
		
		
		// display for anon users
		if(!$user->uid){
			return TRUE;
		}
		
		if($user->uid && !$permissionsClass->checkLoggedInUserIsModuleSubscriber($module_id) && !$permissionsClass->checkUserIsTrialSubscriberByUserID($user->uid)){
			return TRUE;
		}
		
		return FALSE;
	}
	
	
	/**
	 * Defining visibility rules for the 
	 * login link on the main navigation block
	 * 
	 * @return boolean
	 */
	function cel_5d_helper_check_nav_login_visibility()
	{
		// registration form
		if(arg(0) && arg(0) == '5d-course' ){
			return FALSE;
		}
				
		return TRUE;
	}
	