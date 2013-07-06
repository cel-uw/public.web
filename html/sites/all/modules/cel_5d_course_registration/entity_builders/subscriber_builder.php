<?php

module_load_include('php', 'cel_5d_course_registration', 'entities/subscriber');

/**
 * Entity builder for Subscriber object
 * 
 * @author vparfaniuc
 *        
 *        
 */

class SubscriberBuilder  {
	
	private $entity_title = 'Subscriber';
	private $entity_methods;
	
	/**
	 * Build Entity Object and fill with data based on passed object, array or set of array and objects
	 * 
	 * @param unknown_type $income_vals
	 * @param bool $aggregation
	 * @return Subscriber
	 */
	function buildEntity($income_vals, $aggregation=FALSE)
	{
		// get all the entity methods
		$this->entity_methods = get_class_methods($this->entity_title);
		
		// initialize entity Object
		$entityObj = new $this->entity_title;

		// check if aggregation flag is set
		if ($aggregation){
			foreach ($income_vals as $data_item){
				$entityObj = $this->run_needed_internal_method($data_item, $entityObj);
			}
			
			return $entityObj;
		}
		
		// run needed internal method
		return $this->run_needed_internal_method($income_vals, $entityObj);
	}
	
	
	
	/**
	 * Decide which internal building method should be run
	 * 
	 * @param unknown_type $income_vals
	 * @param unknown_type $entityObj
	 * @return Ambigous <Subscriber, boolean>
	 */
	private function run_needed_internal_method($income_vals, $entityObj)
	{
		// if income vals are an object
		if(is_object($income_vals)){
			return $this->build_entity_object_based_on_incoming_object_data($income_vals, $entityObj);
		}elseif (is_array($income_vals)){
			return $this->build_entity_object_based_on_incoming_array_data($income_vals, $entityObj);
		}		
	}
	
	
	
	/**
	 * Filling Entity object with data based on income generic object data
	 * each object var name should corespond to a entity var name 
	 * 
	 * @param stdObj incomeGenericObj
	 * @param Subscriber incomeGenericObjObj
	 * @return Subscriber
	 */
	private function build_entity_object_based_on_incoming_object_data($incomeGenericObj, Subscriber $entityObj)
	{
		// check if there are object methods
		if(!$this->entity_methods || !is_array($this->entity_methods)){
			return FALSE;
		}
		
		// get income object vars 
		$income_obj_vars = get_object_vars($incomeGenericObj);
		
		// walk through list of income object vars
		foreach ($income_obj_vars as $var=>$val)
		{
			// check if there is a setMethod on the entity methods list
			if(($method = array_search(strtolower('set'.$var), array_map('strtolower', $this->entity_methods))) !== FALSE)
			{
				// run Entity setMethod to set val
				$entit_method_title = $this->entity_methods[$method];
				$entityObj->$entit_method_title($val);
			}
		}
		
		return $entityObj;
	}
	
	
	/**
	 * Filling Entity object with data based on income array data structure
	 * each array key should corespond to entity paramentrs
	 * 
	 * @param array entityObjy
	 * @param Subscriber entityObj
	 * @return Subscriber
	 */
	private function build_entity_object_based_on_incoming_array_data(array $income_array, Subscriber $entityObj)
	{
		// validate entity methods and income array
		if(!$this->entity_methods || !is_array($this->entity_methods) || empty($income_array)){
			return FALSE;
		}		
		
		// walk through incomming array items
		foreach ($income_array as $var=>$val)
		{
			// check if there is a setMethod on the entity methods list
			if(($method = array_search(strtolower('set'.$var), array_map('strtolower', $this->entity_methods))) !== FALSE)
			{
				// run Entity setMethod to set val
				$entit_method_title = $this->entity_methods[$method];
				$entityObj->$entit_method_title($val);
			}
		}
		
		return $entityObj;
	}
}

?>