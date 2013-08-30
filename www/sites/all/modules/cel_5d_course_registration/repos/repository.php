<?php

/**
 * Main Repository object 
 * 
 * @author vparfaniuc
 */
class Repository {
	
	/**
	 * Get DB Record Objects
	 *  
	 * @param string $sql
	 * @param array $patterns
	 * @return array
	 */
	function dbGetRecordsObj($sql, $patterns=array())
	{
		$sql = db_query($sql, $patterns);
		
		$records = array();
		$results = db_query($sql);
		foreach($results as $result) {
			$records[] = $result->fetchObject();
		}
		
		return $records;
	} 
	
	
	function dbGetRecordsArr($sql, $patterns=array())
	{
		$sql = db_query($sql, $patterns);
	
		$records = array();
		$results = db_query($sql);
		foreach($results as $result) {
			$records[] = $result->fetchAssoc();
		}
	
		return $records;
	}	
	
	
	function dbGetRecordsFieldArr($field_name, $sql, $patterns=array())
	{
		$records_arr = $this->dbGetRecordsArr($sql, $patterns);

		if(!$records_arr || empty($records_arr)){
			return FALSE;
		}
		
		$out_arr = array();
		foreach ($records_arr as $record){
			$out_arr[] = array_get($record, $field_name);
		}
		
		return $out_arr;
	}
	
	/**
	 * Get one record from the DB query
	 * 
	 * @param unknown_type $col_name
	 * @param unknown_type $sql
	 * @param unknown_type $patterns
	 * @return boolean
	 */
	function dbGetVar($col_name, $sql, $patterns=array())
	{
		$result = db_query($sql, $patterns);
		$record_row = $result->fetchAssoc();
		return array_get($record_row, $col_name);
	}
	
	
	/**
	 * This function makes sense to be called after a select with SQL_CALC_FOUND_ROWS prefix
	 * @return boolean
	 */
	public function dbGetTotalRows()
	{
		$sql = "SELECT FOUND_ROWS() as total_rows";
		return $this->dbGetVar('total_rows', $sql);
	}	
}


?>