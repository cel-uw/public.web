<?php
module_load_include('php', 'fuse_libs', '/jqgrid/interfaces/grid_field_interface.php');

/**
 * Main Field object calss to be extended by other field types classes
 *
 * @author vparfaniuc
 */
class jQgridField {
	
	protected $title;
	protected $name;
	protected $index;
	protected $readonly=FALSE;
	protected $width;
	protected $options;
	protected $sortable = TRUE;
	
	/**
	 * Set mandatory field data
	 * @param string $name
	 * @throws FuseException
	 */
	function __construct($name)
	{
		if(strlen($name)<2){
			throw new FuseException('Unsupported field name assigned');
		}
		
		// set the name and index to be the same
		$this->name = $name;
		$this->index = $name;
	}
	
	
	/**
	 * Make current field not Editable
	 *
	 * @see GridFieldInterface::makeReadonly()
	 *
	 */
	function makeReadonly() {
		$this->readonly = TRUE;
		return $this;
	}
	
	
	/* Set title used on the grid 
	 * 
	 * @see web\sites\all\modules\fuse_libs\jqgrid.GridFieldInterface::title()
	 */
	function setTitle($title) 
	{
		if(strlen($title)<2){
			throw new FuseException('Unsupported field title assigned');
		}
		
		$this->title = $title;
		return $this;
	}

	
	/**
	 *
	 * @see GridFieldInterface::setName()
	 *
	 */
	function setName($name) 
	{
		if(strlen($name)<2){
			throw new FuseException('Unsupported field name assigned');
		}

		$this->name = $name;
		return $this;
	}
	
	
	/**
	 * Setting ptional field index value, in case if this is different than the field name
	 *  
	 * @see GridFieldInterface::setIndex()
	 *
	 */
	function setIndex($index) 
	{
		if(strlen($index)<2){
			throw new FuseException('Unsupported field index assigned');
		}

		$this->index = $index;
		
		return $this;
	}


	/**
	 * Make field not sortable
	 * @return jQgridField
	 */
	function makeUnsortable()
	{
		$this->sortable = FALSE;
		return $this;
	}
	
	
	/**
	 * @param field_type $width
	 */
	function setWidth($width) 
	{
		if(strlen($width)<1){
			throw new FuseException('Unsupported field width assigned');
		}
		
		$this->width = intval($width);
		return $this;
	}
	
	
	/**
	 * @return the $width
	 */
	public function getWidth() {
		return $this->width;
	}
	
	
	/**
	 * Get diel additional options
	 * @return the $options
	 */
	public function getOptions() {
		return $this->options;
	}

	/**
	 * Set additional options for field
	 * @param field_type $options
	 */
	public function setOptions($options) {
		$this->options = $options;
	}


}

?>