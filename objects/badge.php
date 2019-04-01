<?php
class Badge{
 
    // database connection and table name
    private $conn;
    private $table_name = "badges";
 
    // object properties
    public $badgeId;
    public $badgeDesc;
    public $userId;
    public $date;
 
    // constructor with $db as database connection
    public function __construct($db){
        $this->conn = $db;
    }

    public function get_user_badges($id=0) {
        $query="SELECT * FROM ".$this->table_name;
        if($id != 0)
        {
            $query.=" WHERE userId=".$id;
        }
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    
    public function get_daily_scores($date) {
        $date_query = date($date);
        $query="SELECT * FROM ".$this->table_name . " WHERE date=".$date_query;
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }
    // need delete all badges of desc of that day
}
?>