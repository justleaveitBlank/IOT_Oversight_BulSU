<?php
class Appliance{
 
    // database connection and table name
    private $conn;
    private $table_name = "t_appliance";
 
    // object properties
    public $uid;
    public $appl_name;
    public $has_power;
    public $has_power_limit;
    public $has_time_limit;
    public $current_date_time;
    public $time_limit_value;
	public $power_limit_value;
	public $current_power_usage;
	public $avg_watthr;
	public $estimated_cost;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }
	
	// search products
	function search($keywords){
	 
		// select all query
    $query = "SELECT
				*
            FROM
                " . $this->table_name . "
            WHERE
                uid LIKE ?
			LIMIT
				0,1";;
				
	 
		// prepare query statement
		$stmt = $this->conn->prepare($query);
	 
		// sanitize
		$keywords=htmlspecialchars(strip_tags($keywords));
		$keywords = "%{$keywords}%";
	 
		// bind
		$stmt->bindParam(1, $keywords);
		//$stmt->bindParam(2, $keywords);
		//$stmt->bindParam(3, $keywords);
	
	 
		// execute query
		$stmt->execute();
	 
		return $stmt;
	}
	
}

