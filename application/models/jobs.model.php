<?php
 /*
  +-------------------------------------------------------------------------+
  | Copyright 2010-2013, Davide Franco			                            |
  |                                                                         |
  | This program is free software; you can redistribute it and/or           |
  | modify it under the terms of the GNU General Public License             |
  | as published by the Free Software Foundation; either version 2          |
  | of the License, or (at your option) any later version.                  |
  |                                                                         |
  | This program is distributed in the hope that it will be useful,         |
  | but WITHOUT ANY WARRANTY; without even the implied warranty of          |
  | MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the           |
  | GNU General Public License for more details.                            |
  +-------------------------------------------------------------------------+
 */

 class Jobs_Model extends CModel {
	
	// ==================================================================================
	// Function: 	count_Jobs()
	// Parameters:	$pdo_connection
	//				$period_timestamps (optional)
	//				$job_status (optional)
	//				$job_level (optional)
	// Return:		Jobs count (optional)
	// ==================================================================================
	
	public static function count_Jobs( $pdo_connection, $period_timestamps = null, $job_status = null, $job_level = null) {
		$statment	= null;
		$where		= null;
		$tablename	= 'Job';
		$fields		= array('COUNT(*) as job_count');
		$intervals	= null;		
		
		// Check PDO object
		if( !is_a( $pdo_connection, 'PDO') and is_null($pdo_connection)  ) 
			throw new Exception('Unvalid PDO object provided in count_Jobs() function');

		// PDO object singleton
		if( is_null(CModel::$pdo_connection) )
			CModel::$pdo_connection = $pdo_connection;
		
		
		// Check timestamp interval
		if( !is_null( $period_timestamps ) ) {
			if( !is_array($period_timestamps) ) 
				throw new Exception('Wrong period of missing array provided in count_Jobs() function');
			else
				$intervals  = CDBQuery::get_Timestamp_Interval( $pdo_connection, $period_timestamps );
		}
		
		// Defining interval depending on job status
		if( !is_null($job_status) && !is_null( $period_timestamps ) ) {
			switch( $job_status ) {
				case 'running':
					$where[] = '(starttime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
				break;
				case 'waiting':
					// don't use interval for waiting jobs
				break;
				default:
					$where[] = '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
				break;
			}
		}
		
		// Job status
		if( !is_null( $job_status )) {
			switch( $job_status ){
				case 'running':
					$where[] = "JobStatus = 'R'" ;
				break;
				case 'completed':
					$where[] = "JobStatus = 'T' ";
				break;
				case 'failed':
					$where[] = "JobStatus IN ('f','E') ";
				break;
				case 'canceled':
					$where[] = "JobStatus = 'A' ";
				break;
				case 'waiting':
					$where[] = "JobStatus IN ('F','S','M','m','s','j','c','d','t','p','C') ";
				break;
			} // end switch
		}		
		
		// Job level
		if( !is_null($job_level) )
			$where[] = "Level = '$job_level' ";
		
		// Building SQL statment
		$statment = array( 'table' => $tablename, 'fields' => $fields, 'where' => $where);
		$statment = CDBQuery::get_Select( $statment );

		// Execute SQL statment
		$result = CDBUtils::runQuery($statment, $pdo_connection);
		$result = $result->fetch();
		return $result['job_count'];
	}
	
	// ==================================================================================
	// Function: 	getStoredFiles()
	// Parameters: 	$period	 		start and end date (unix timestamp)
	//				$job_name		optional job name
	//				$client			optional client name
	// Return:		Total of stored files within the specific period
	// ==================================================================================	
	public function getStoredFiles( $pdo_connection, $period_timestamps = array(), $job_name = 'ALL', $client = 'ALL' )
	{
		$statment 	= '';
		$where  	= array();
		$fields 	= array( 'SUM(JobFiles) AS stored_files' );
		$tablename	= 'Job';
		
		// Defined period
		$intervals 	= CDBQuery::get_Timestamp_Interval( $pdo_connection, $period_timestamps );
		$where[] 	= '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
		
		if( $job_name != 'ALL' ) 
			$where[] = "name = '$job_name'";
		
		if( $client != 'ALL' )
			$where[] = "clientid = '$client'";
		
		// Building SQL statment
		$statment = array( 'table' => CModel::get_Table($tablename), 'fields' => $fields, 'where' => $where);
		$statment = CDBQuery::get_Select( $statment );

		// Execute query
		$result = CDBUtils::runQuery( $statment, $pdo_connection );
		$result = $result->fetch();
		
		return $result['stored_files'];
	}	

	// ==================================================================================
	// Function: 	getStoredBytes()
	// Parameters: 	$period	 	start and end date (unix timestamp)
	//				$job_name		optional job name
	//				$client			optional client name
	// Return:		Total of stored bytes within the specific period
	// ==================================================================================	
	public function getStoredBytes( $pdo_connection, $period_timestamps = array(), $job_name = 'ALL', $client = 'ALL' )
	{
		$statment 	= '';
		$where  	= array();
		$fields 	= array( 'SUM(JobBytes) AS stored_bytes' );
		$tablename	= 'Job';
		
		// Defined period
		$intervals 	= CDBQuery::get_Timestamp_Interval( $pdo_connection, $period_timestamps );
		$where[] 	= '(endtime BETWEEN ' . $intervals['starttime'] . ' AND ' . $intervals['endtime'] . ') ';
		
		if( $job_name != 'ALL' ) 
			$where[] = "name = '$job_name'";
		
		if( $client != 'ALL' )
			$where[] = "clientid = '$client'";
		
		// Building SQL statment
		$statment = array( 'table' => CModel::get_Table($tablename), 'fields' => $fields, 'where' => $where);
		$statment = CDBQuery::get_Select( $statment );

		// Execute query
		$result = CDBUtils::runQuery( $statment, $pdo_connection );
		$result = $result->fetch();
		
		return $result['stored_bytes'];
	}	

    // ==================================================================================
	// Function: 	count_Job_Names()
	// Parameters:	$pdo - valid PDO object
	// Return:		total of defined jobs name
	// ==================================================================================	

	public static function count_Job_Names( $pdo ) {
		$fields		= array( 'COUNT(DISTINCT Name) AS job_name_count' );

		// Prepare and execute query
		$statment 	= CDBQuery::get_Select( array( 'table' => CModel::get_Table('Job'), 'fields' => $fields ) );
		$result 	= CDBUtils::runQuery( $statment, $pdo );

		$result		= $result->fetch();
		return $result['job_name_count'];
	}

    // ==================================================================================
	// Function: 	get_Jobs_List()
	// Parameters:	$pdo - valid PDO object
	// Return:		total of defined jobs name
	// ==================================================================================	

	public static function get_Jobs_List( $pdo, $client_id = null ) {
		$jobs		= array();
		$fields		= array( 'Name' );

		// Prepare and execute query
		$statment 	= CDBQuery::get_Select( array( 'table' => Jobs_Model::get_Table('Job'), 'fields' => $fields, 'groupby' => 'Name', 'orderby' => 'Name' ) );
		$result 	= CDBUtils::runQuery( $statment, $pdo );

		foreach( $result->fetchAll() as $job ) {
			$jobs[] = $job['name'];
		}
		
		return $jobs;
	}
 }
 
?>
