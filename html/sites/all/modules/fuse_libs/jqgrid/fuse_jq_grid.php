<?php
// make sure the exception class is loaded
module_load_include('php', 'fuse_libs', 'classes/fuse_exception');

/**
 *
 * @author vparfaniuc
 *        
 *        
 */

class FuseJQGrid  {
	private $url;
	private $grid_id='grid_id';
	private $grid_title = 'Untitled Grid'; 
	private $fields = array(); 
	private $width = 800;
	private $height = FALSE; 
	private $default_order_field='id';
	private $default_order_direction='asc';
	private $toolbar = FALSE; 
	
	
	/**
	 * Set mandatory values for grid initialization
	 * 
	 * @param string $url
	 * @throws FuseException
	 */
	public function __construct($url)
	{
		// validate url
		if(strlen($url)<2){
			throw new FuseException('Unsuported URL in jQfrid assigned');
		}
		
		// assign url value
		$this->url = $url;
	}
	
	
	/**
	 * Function used to set grid title 
	 * 
	 * @param string $grid_title
	 * @return boolean
	 */
	public function setGrid_title($grid_title)
	{
		if(strlen($grid_title)<2){
			return FALSE;
		}
		
		$this->grid_title = $grid_title;
		return $this;
	}

	
	/**
	 * Setting grid width value size
	 * @param int $width
	 */
	public function setWidth(int $width)
	{
		if($width){
			$this->width = $width;
		}
		return $this;
	}
	
	
	/**
	 * Setting grid height value
	 * @param int $height
	 */
	public function setHeight(int $height)
	{
		if($height){
			$this->height = $height;
		}
		return $this;
	}
	
	
	/**
	 * Set html grid object id 
	 * @param unknown_type $grid_id
	 * @return FuseJQGrid
	 */
	public function setGridID($grid_id)
	{
		if(strlen($grid_id)<2){
			$this->grid_id = htmlspecialchars( $grid_id );		
		}
		return $this;
	}
	
	
	/**
	 * Add new field data to array of grid objects
	 * 
	 * @param GridFieldInterface $fieldObj
	 * @return FuseJQGrid
	 */
	public function addField(GridFieldInterface $fieldObj){
		return $this->fields[] = $fieldObj;
	}
	
	
	/**
	 * Specify what field should be ordered by default
	 * @param string $field_name
	 * @return FuseJQGrid
	 */
	public function defaultOrderField($field_name)
	{
		$this->default_order_field = $field_name;
		return $this;
	}
	
	
	/**
	 * Set default order sort for grid results
	 * 
	 * @param string $order_direction
	 * avail params are: desc or asc, others will be ignored 
	 * @return FuseJQGrid
	 */
	public function defaultOrderDirection($order_direction='DESC')
	{
		if(strtolower($order_direction) == 'asc' || strtolower($order_direction) == 'desc'){
			$this->defaultOrderDirection($order_direction);
		}
		
		return $this;
	}
	
	
	/**
	 * Enable the bottom toolbar on the grid
	 * 
	 */
	public function enableToolbar(){
		$this->toolbar = TRUE;
	}
	
	
	/**
	 * Generates HTML output of the grid sourcecode 
	 *  
	 * @return string
	 */
	public function render()
	{
		// load all header files
		$this->loadHeaders();
		
		$out = '';
		$out .= "<script type='text/javascript'>
				jQuery(function(){ 
				  jQuery('#{$this->grid_id}').jqGrid({
				    url:'{$this->url}',
				    datatype: 'json',
				    colNames:{$this->buildColNames()},
				    colModel :{$this->generateColmodelRows()},
// 						{name:'id',index:'id', width:55,editable:false,editoptions:{readonly:true,size:10}},
// 						{name:'invdate',index:'invdate', width:80,editable:true,editoptions:{size:10, dataInit:datepicker}},
					//	{name:'name',index:'name', width:90,editable:true,editoptions:{size:25}},
// 						{name:'amount',index:'amount', width:60, align:'right',editable:true,editoptions:{size:10}},
					//	{name:'tax',index:'tax', width:60, align:'right',editable:true,editoptions:{size:10}},
// 						{name:'tax', editable:true, edittype:'select', editoptions:{value:{1:'One',2:'Two'}} },
// 						{name:'total',index:'total', width:60,align:'right',editable:true,edittype:'textarea',editoptions:{size:10}, editrules: { required: true}},
					//	{name:'closed',index:'closed',width:55,align:'center',editable:true,edittype:'checkbox',editoptions:{value:'Yes:No'}},
					//	{name:'ship_via',index:'ship_via',width:70, editable: true,edittype:'select',editoptions:{value:'FE:FedEx;TN:TNT'}},
// 						{name:'note',index:'note', width:100, sortable:false,editable: true, edittype:'textarea', editoptions:{rows:'2',cols:'20'}}    
// 					],
				    pager: '#pager_{$this->grid_id}',
				    rowNum:10,
				    rowList:[10,20,30],
				    sortname: '{$this->default_order_field}',
				    sortorder: '{$this->default_order_direction}',
				    viewrecords: true,
				    gridview: true,
				    caption: '{$this->grid_title}',
				    editurl:'{$this->url}',
					height:'{$this->height}',
					width:{$this->width}
				  });"; 
		
		// enable toolbar 
		if($this->toolbar)
		{
			$out .= "/* Allow advanced search using the toolbar button */
					jQuery('#{$this->grid_id}').navGrid('#pager_{$this->grid_id}', { search: true, edit: true, add: true, del: true, view:true }, {}, {}, {}, { closeOnEscape: true, multipleSearch: true, closeAfterSearch: true });
					
					/* Add a separator between buttons */
					jQuery('#{$this->grid_id}').navSeparatorAdd('#pager_{$this->grid_id}', { sepclass: 'ui-separator', sepcontent: '' });
					jQuery('#{$this->grid_id}').jqGrid('navGrid','#{$this->grid_id}',{del:false,add:false,edit:false},{},{},{},{multipleSearch:true});
					
					}); 
					
					function datepicker (elem) {
					  jQuery(elem).datepicker(
					      { dateFormat: 'dd-mm-yy' }
					  );
					 };
					
					jQuery('#{$this->grid_id}').jqGrid('navGrid','#pager_{$this->grid_id}',
						{}, //options
						{height:280,reloadAfterSubmit:false}, // edit options
						{height:280,reloadAfterSubmit:false}, // add options
						{reloadAfterSubmit:false}, // del options
						{} // search options
					);";
		}else{
			$out .= '});';
		}
				
		$out .= '</script>';
		$out .= '<table id="'.$this->grid_id.'"><tr><td/></tr></table> 
				<div id="pager_'.$this->grid_id.'"></div>';
		return $out;
	}
	
	
	
	// PRIVATE LOCAL METHODS
	
	/**
	 * Method to load all services files needed for full functionality
	 *
	 * @return boolean
	 */
	private function loadHeaders()
	{
		// load css files
		drupal_add_css(drupal_get_path('module', 'fuse_libs') .'/jqgrid/css/ui-lightness/jquery-ui-1.8.18.custom.css', 'module');
		drupal_add_css(drupal_get_path('module', 'fuse_libs') .'/jqgrid/css/ui.jqgrid.css', 'module');
	
		// load js files
		drupal_add_js(drupal_get_path('module', 'fuse_libs').'/jqgrid/js/jquery-1.5.2.min.js');
		drupal_add_js(drupal_get_path('module', 'fuse_libs').'/jqgrid/js/i18n/grid.locale-en.js');
		drupal_add_js(drupal_get_path('module', 'fuse_libs').'/jqgrid/js/jquery.jqGrid.min.js');
		drupal_add_js(drupal_get_path('module', 'fuse_libs').'/jqgrid/js/jquery.ui.core.js');
		drupal_add_js(drupal_get_path('module', 'fuse_libs').'/jqgrid/js/jquery.ui.datepicker.js');
	
		return true;
	}
	
	
	/**
	 * Internal local method to generate somodel value of a grid
	 * @return boolean|string
	 */
	private function generateColmodelRows()
	{
		if(empty($this->fields)){
			return FALSE;
		}
	
		$fields_arr = array();
	
		/* @var $fieldObj GridFieldInterface */
		foreach ($this->fields as $fieldObj)
		{
			$fields_arr[] = $fieldObj->render();
		}
	
		return  '['.implode(',', $fields_arr).']';
	}
	
	
	/**
	 * Internal local method to generate colName fields value of a grid
	 * @return boolean|string
	 */
	private function buildColNames()
	{
		if(empty($this->fields)){
			return FALSE;
		}
	
		$fields_arr = array();
	
		/* @var $fieldObj GridFieldInterface */
		foreach ($this->fields as $fieldObj)
		{
			$fields_arr[] = "'" . $fieldObj->getTitle() . "'";
		}
	
		return  '['.implode(',', $fields_arr).']';
	}	
}

