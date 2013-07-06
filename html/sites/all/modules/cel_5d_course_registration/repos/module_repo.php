<?php
module_load_include('php', 'cel_5d_course_registration', 'repos/repository');

/**
 *
 * @author vparfaniuc
 *        
 *        
 */
class ModuleRepo extends Repository {
	
	/**
	 * Get list of current module IDs by module ID
	 *  
	 * @param int $module_id
	 * @return array
	 */
	function getSessionsIDsByModuleID($module_id)
	{
		// get current module session
		$sql = "SELECT
					nid
				FROM
					{content_type_5d_session}
				WHERE
					field_module_rel_nid = %d";
		$sessions = $this->dbGetRecordsFieldArr('nid', $sql, array($module_id));
		
		return $sessions;
	}	
	
	
	function getMaterialsIDsBySessionID($session_id)
	{
		// get current session materials
		$sql = "SELECT
					cd.nid
				FROM
					{content_type_5d_documents} as cd
				JOIN {node} as n ON (cd.nid = n.nid)
				WHERE
					cd.field_docs_session_rel_nid = %d
				ORDER BY n.changed";
		$materials_ids = $this->dbGetRecordsFieldArr('nid', $sql, array($session_id));
		
		return $materials_ids;		
	}
	
	
	function getVideoIDsBySessionID($session_id)
	{
		// get current session materials
		$sql = "SELECT
			nid
		FROM
			{content_type_5d_video}
		WHERE
			field_session_rel_nid = %d";
		$video_ids = $this->dbGetRecordsFieldArr('nid', $sql, array($session_id));
	
		return $video_ids;
	}
	
	
	/**
	 * Get the parent module ID based on session ID
	 * 
	 * @param int $session_id
	 * @return string | boolean
	 */
	function getModuleIDBySessionID($session_id)
	{
		$sql = "SELECT field_module_rel_nid FROM {content_type_5d_session} WHERE nid = %d LIMIT 1";
		return $this->dbGetVar('field_module_rel_nid', $sql, array($session_id));
	}
	
	
	function getModuleIDByVideoID($video_id)
	{
		$sql = "SELECT field_module_rel_nid
				FROM content_type_5d_session as s
				JOIN content_type_5d_video as v ON (v.field_session_rel_nid = s.nid)
				WHERE v.nid = %d LIMIT 1";
		return $this->dbGetVar('field_module_rel_nid', $sql, array($video_id));
	}
	
	
	function getMpduleIDByDocumentID($document_id)
	{
		$sql = "SELECT field_module_rel_nid
				FROM content_type_5d_session as s
				JOIN content_type_5d_documents as v ON (v.field_docs_session_rel_nid = s.nid)
				WHERE v.nid = %d LIMIT 1";
		return $this->dbGetVar('field_module_rel_nid', $sql, array($document_id));		
	}
}

?>