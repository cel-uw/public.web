<?php
module_load_include('php', 'cel_5d_course_registration', 'repos/roles_repo');

/**
 *
 * @author vparfaniuc
 *        
 *        
 */

class PermissionsClass {
  private $rolesRepo;    // roles repository
  private $user;      // logged in user
  
  public function __construct()
  {
    global $user;
    $this->user = $user;
    
    $this->rolesRepo = new RolesRepo();
    
  }
  
  
  /**
   * Checks if current user is a subscriber of module
   *
   * @param int $module_id
   * @return boolean
   */
  public function checkLoggedInUserIsModuleSubscriber($module_id)
  {
    global $user;
  
    $user_roles = $user->roles;
  
    return in_array($this->rolesRepo->buildContentAccessRoleTitleByModuleID($module_id), $user_roles);
  }
  
  
  /**
   * Check if a user is a trial user
   * 
   * @param int $uid
   * @return boolean
   */
  public function checkUserIsTrialSubscriberByUserID($uid)
  {
    // load a user by uid
    $user = user_load($uid);
    
    if(!$user->uid){
      FALSE;
    }
    
    $roles = $user->roles;
    
    // walk through the list of user roles
    return in_array("5D Trial", $roles);
  }
  
  
  /**
   * Check if current user has district role assigned
   *
   * @return boolean
   */
  public function checkLoggedInUserDistrictRole()
  {
    global $user;
  
    $user_roles = $user->roles;
    return in_array('5D District', $user_roles);
  }
  
  
  /**
   * Check if user has access to buy licences
   * 
   * @param int $module_id
   * @return boolean
   */
  public function checkCurrentUserHasAccessBuyLicense($module_id)
  {
    global $user;
  
    // check if user is logged in
    if(!$user->uid){
      return FALSE;
    }
  
    // if user is Subscriber but not district
    if($this->checkLoggedInUserIsModuleSubscriber($module_id) && !$this->checkLoggedInUserDistrictRole()){
      return FALSE;
    }
  
    return TRUE;
  }
  
  
  /**
   * This permission layer will check if user has access to see 
   * Module Body Text available only to Subscribers and Trial users
   * 
   * RULES:
   * 1. User should be logged in
   * 2. User may be Subscriber or
   * 3. User may be Trial 
   * 
   * @param int $module_id
   */
  public function checkCurrUsrAccessModuleAuthUserBodyTxt($module_id)
  {
    // 1 Rule implement
    if(!$this->user || !$this->user->uid){
      return FALSE;
    }

    // get user roles arr
    $user_roles_arr = $this->user->roles;
    
    if (in_array("5D Trial", $user_roles_arr) || in_array("5D Subscriber 473", $user_roles_arr) || user_access('administer nodes')) {
      return TRUE;
    }
    
    // return false for any other use case
    return FALSE;
  }
}

?>