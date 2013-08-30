<?php
module_load_include('php', 'fuse_libs', '/classes/fuse_exception');
module_load_include('php', 'fuse_libs', '/jqgrid/fields/jqgrid_field');
module_load_include('php', 'fuse_libs', '/jqgrid/fields/grid_field_interface');

/**
 * Basic jQgrid field class
 *
 * @author vparfaniuc
 *        
 */
class BasicField extends jQgridField implements GridFieldInterface {
	
	/**
	 * Set mandatory field values at the step of class initialization 
	 * 
	 * @param unknown_type $name
	 * @throws FuseException
	 */
	function __construct($name)
	{
		if(strlen($name)<2){
			throw new FuseException('Unsupported field name assigned');
		}
		parent::__construct($name);
	}

	
	/**
	 *
	 * @see GridFieldInterface::getTitle()
	 *
	 */
	public function getTitle() {
		return $this->title;
	}
	
	
	/**
	 *
	 * @see GridFieldInterface::getTitle()
	 *
	 */
	public function getName() {
		return $this->name;
	}	
	
	/**
	 * get field Index
	 * 
	 */
	public function getIndex(){
		
		return $this->index;
	}
	
	
	/**
	 * Generate an array of json data for each field
	 * 
	 * @return string
	 */
	private function buildFieldDaraParamsArr()
	{
		$fieldParams[] = "name:'{$this->getName()}'";
		$fieldParams[] = "index:'{$this->getIndex()}'";
		
		
		if ($this->getWidth()){
			$fieldParams[] = "width:{$this->getWidth()}";
		}
		
		if($this->readonly === TRUE){
			$fieldParams[] = "editable:false";
			$fieldParams[] = "editoptions:{readonly:true,size:10}";
		}else{
			$fieldParams[] = "editable:true";
		}
		
		
		if($this->getOptions()){
			$fieldParams[] = $this->getOptions();
		}
		
		if(!$this->sortable){
			$fieldParams[] = "sortable:false";
		}
		
		return $fieldParams;
	}
	
	
	/**
	 * Render field data as a JSON obj data
	 * @return string
	 */
	public function render(){
		// {name:'id',index:'id', width:55,editable:false,editoptions:{readonly:true,size:10}},
		
		return '{'.implode(',', $this->buildFieldDaraParamsArr()) . '}';
	}
}

?>