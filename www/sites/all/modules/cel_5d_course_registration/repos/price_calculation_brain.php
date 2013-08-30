<?php
/**
 * Class will contain all the price calculation logic needed on the application 
 * 
 * @author vparfaniuc
 *        
 */

class PriceCalculationBrain {
	private $regular_price;
	private $volume_price;
	private $volume_price_qty_devidier;
	private $licences_qty;
	
	public function __construct(int $licences_qty, stdClass $module_obj)
	{
		$this->licences_qty = intval($licences_qty);
		$this->regular_price 	= intval($module_obj->field_cel_5d_module_unit_price[0]['value']);
		$this->volume_price	= intval($module_obj->field_cel_5d_module_volume_price[0]['value']);
		$this->volume_price_qty_devidier	= intval($module_obj->field_cel_5d_module_price_divide[0]['value']);
	}

	
	/**
	 * Defining price based on price calculaiton logic and 
	 * data filled by the admin in the module node definition page
	 * 
	 * @return number
	 */
	private function defineUnitPrice()
	{
		// check if volume price devider is set and if qnt is more thatn that devider, get the volume price
		if($this->volume_price_qty_devidier && $this->licences_qty > $this->volume_price_qty_devidier){
			return $this->volume_price;
		}
		
		return $this->regular_price;
	}
	
	
	/**
	 * Price Calculation Logic goes here 
	 * @return number
	 */
	private function calculateTotalPrice()
	{
		return $this->licences_qty * $this->defineUnitPrice();
	}
	
	
	/**
	 * building money format for a number
	 * @param int $number
	 * @return string
	 */
	public function moneyFormatting(int $number){
		return '$ ' . number_format($number, 2, '.', ',');
	}
	
	
	/**
	 * Public function used to be called in order to get total order price
	 * @return number
	 */
	public function getTotalPrice($formatted = FALSE)
	{
		$total_price = $this->calculateTotalPrice();
		if (!$formatted){
			return $total_price;
		}
		
		return $this->moneyFormatting($total_price);
	}
	
	
	/**
	 * getting licence price
	 * 
	 * @param unknown_type $formatted
	 * @return Ambigous <number, number>|string
	 */
	public function getUnitPrice($formatted = FALSE)
	{
		$unit_price = $this->defineUnitPrice();
		if (!$formatted){
			return $unit_price;
		}
			
		return $this->moneyFormatting($unit_price);
	}
}

?>