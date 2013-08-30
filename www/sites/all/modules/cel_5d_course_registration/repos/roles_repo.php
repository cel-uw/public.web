<?php

/**
 *
 * @author vparfaniuc
 *        
 *        
 */

class RolesRepo {

	
	/**
	 * Get userrole details by Role title
	 *
	 * @param string $role_title
	 * @return stdClass
	 */
	public function getRoleByTitle($role_title)
	{
		// build sql query
		$sql = db_query("SELECT *
				FROM {role}
				WHERE name = '%s'
				LIMIT 1", $role_title);
	
		// get role data form DB
		return db_fetch_object($sql);
	}
	
	
	
	/**
	 * Get Temporary Registrar role info
	 * 
	 * @param int $module_nid
	 * @return stdClass
	 */
	public function getTemporaryRegistrarRole($module_nid)
	{
		$role_title = '5D Registrar Temp : '.$module_nid;
		
		return $this->getRoleByTitle($role_title);
	}
	
	
	public function getTemporaryRegistrarRoleByUserObj($userObj)
	{
		// get assigned user roles
		$user_roles = $userObj->roles;
		
		
		foreach ($user_roles as $role_id => $role_title) 
		{
			if(stristr($role_title, '5D Registrar Temp'))
			{
				$roleObj = new stdClass();
				$roleObj->rid = $role_id;
				$roleObj->name = $role_title;
				return $roleObj;	
			}
		}
		
		return FALSE;
	}
	
	
	/**
	 * get array of roles access array for purchaser
	 * 
	 * @param Purchaser $purchaserObj
	 * @param Subscription $subscriptionObj
	 * @return array
	 */
	public function getPurchaserRolesArr(Purchaser $purchaserObj, Subscription $subscriptionObj)
	{
		// trial role arr building
		if(array_get($_SESSION['subscription_info'], 'trial')){
			return $this->getTrialUserRolesArr($purchaserObj);
		}
		
		// load purchaser drupal user object to get it's current roles
		$purchaser_user = user_load($purchaserObj->getUid());
		
		// initialize roles array with predefined user roles
		$roles_arr = $purchaser_user->roles;
		
		
		// if there are only 1 licence and user chosed the licesnce for own use then then set the content access role
		if($subscriptionObj->getLicences_qty() == 1 && $purchaserObj->getPurchaser_licence() == 1)
		{
			$role_obj = $this->getRoleByTitle( $this->buildContentAccessRoleTitleByModuleID($subscriptionObj->getModule_id()) );
			
			if($role_obj){
				$roles_arr[$role_obj->rid] = $role_obj->name;
			}
		} 
		
		
		if(!$purchaserObj->getPurchaser_licence()){
			$role_obj = $this->getRoleByTitle( '5D District' );
				
			if($role_obj){
				$roles_arr[$role_obj->rid] = $role_obj->name;
			}
		}

		return $roles_arr;
	}
	
	
	/**
	 * Get array of role access for subscriber
	 * 
	 * @param Subscriber $subscriberObj
	 * @param Subscription $subscriptionObj
	 * @return array
	 */
	public function getSubscriberRolesArr(Subscriber $subscriberObj, Subscription $subscriptionObj)
	{
		// load purchaser drupal user object to get it's current roles
		$subscriber_user = user_load($subscriberObj->getUid());
		
		// initialize roles array with predefined user roles
		$roles_arr = $subscriber_user->roles;
				
		$role_obj = $this->getRoleByTitle( $this->buildContentAccessRoleTitleByModuleID($subscriptionObj->getModule_id()) );
		
		if($role_obj){
			$roles_arr[$role_obj->rid] = $role_obj->name;
		}
		
		return  $roles_arr;
	}
	
	
	/**
	 * Method to build user roles arr for trial users
	 * 
	 * @param Purchaser $purchaserObj
	 * @return array
	 */
	public function getTrialUserRolesArr(Purchaser $purchaserObj)
	{
		// load purchaser drupal user object to get it's current roles
		$purchaser_user = user_load($purchaserObj->getUid());
		// initialize roles array with predefined user roles
		$roles_arr = $purchaser_user->roles;
		
		// unset 5d Cources Trial Temp role
		if (isset($roles_arr[15])){
			unset($roles_arr[15]);
		}
		
		$role_obj = $this->getRoleByTitle( '5D Trial' );
		
		if($role_obj){
			$roles_arr[$role_obj->rid] = $role_obj->name;
		}

		return $roles_arr;
	}
	
	
	/**
	 * Building role title string for module access
	 * 
	 * @param Subscription $subscriptionObj
	 * @return string
	 */
	public function buildContentAccessRoleTitleByModuleID($module_id)
	{
		return '5D Subscriber '.$module_id;
	}
}

?>