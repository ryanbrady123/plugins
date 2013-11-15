<?php

class Mass_Unfollow extends Common{

    private $id;
	private $status;
    private $tableName;


    function __construct() {
        $this->status = 0;
        $this->tableName = "unfollow_scheduler";
    }
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($val){
		$this->id = $val;
	} 

    public function getStatus() {
        return $this->status;
    }

    public function setStatus($status) {
        $this->status = $status;
    }
	

    public function getTableName() {
        return $this->tableName;
    }

    public function setTableName($tableName) {
        $this->tableName = $tableName;
    }

    public function getTableHeaders() {
        return $header = array('ID', ' Login', 'Password', 'Role Type');
    }
	public function getRecordsId($limit = "") {
       $sql = "SELECT id FROM ".$this->tableName." WHERE next_run < NOW() AND status = 0 ORDER BY next_run ASC ";
	   if($limit !=""){
	   		$sql .= " LIMIT ".$limit; 
	   }
       return $sql;
    }
	public function getRecordsForIds($limit = "") {
       $sql = "SELECT id,type,params FROM ".$this->tableName." WHERE type='list' AND id_complete = 0 ORDER BY next_run ASC ";
	   if($limit !=""){
	   		$sql .= " LIMIT ".$limit; 
	   }
       return $sql;
    }
	public function getRecordsById($ids) {
       $sql = "SELECT id,count,all_unfollow,days_in_past,notes,period,next_run FROM ".$this->tableName." WHERE id IN (".$ids.") ORDER BY next_run ASC ";
       return $sql;
    }
	public function getRecords($limit = "") {
       $sql = "SELECT * FROM ".$this->tableName." WHERE next_run < NOW() ORDER BY next_run ASC ";
	   if($limit !=""){
	   		$sql .= " LIMIT ".$limit; 
	   }
       return $sql;
    }
	
	  public function updateStatusByIds(){
		$sql = "UPDATE ".$this->tableName." SET status = " .$this->getStatus(). " WHERE  id IN(".$this->getId().")";
		return $sql;
	}
	
	  public function updateListByIds($id,$params){
		$sql = "UPDATE ".$this->tableName." SET params = " .$params. " , id_complete = 1 WHERE  id ".$id;
		return $sql;
	}
	
	public function create_table($thread_id){
		$sql = "";
		if(!empty($thread_id) && !is_null($thread_id)){
			$sql = str_replace("%thread_id%",$thread_id,TABLE_STRUCTURE_UN_FOLLOW);
		}
		return $sql;
	}
	
	public function get_content($thread_id,$publish_date,$end_date){		
		$sql = "";
		if(!empty($thread_id) && !empty($publish_date)){
			echo $sql = "SELECT * FROM un_follow_thread_".$thread_id." WHERE publish_date < NOW() AND publish_date > DATE_SUB( NOW() , INTERVAL ".$end_date." MINUTE )";
		}
		return $sql;
	}
	
	public function get_users_to_unfollow($table_name,$status = 1,$date,$username,$limit){
		echo "\n\n";
		echo $sql = "SELECT id,follow_name,status,date FROM follow_table_".$table_name." WHERE date <= '".$date."' AND status = ".$status." AND username = '".$username."' ORDER BY date DESC LIMIT ".$limit;
		echo "\n\n";
		return $sql;
		
	}
	
	public function delete_content($thread_id,$publish_date){		
		$sql = "";
		if(!empty($thread_id) && !empty($publish_date)){
			echo $sql = "DELETE FROM un_follow_thread_".$thread_id." WHERE publish_date < NOW()";
			echo '<br>';
		}
		return $sql;
	}
	
	
}

?>