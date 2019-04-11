<?php
class Badge{
 
    // database connection and table name
    private $conn;
    private $table_name = "badges";
 
    // object properties
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
    
    public function get_daily_badge_by_type($date, $badgeDesc) {
        $date_query = date($date);
        $query="SELECT * FROM ".$this->table_name . " WHERE date=\"".$date_query. "\" AND badgeDesc=\"".$badgeDesc."\"";
        echo $query;
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    public function get_badges_by_type($badgeDesc) {
        $query="SELECT * FROM ".$this->table_name . " WHERE badgeDesc=\"".$badgeDesc."\"";
        
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    public function remove_badge() {
        $date_query = date($this->date);
        $query="DELETE FROM ".$this->table_name . " WHERE badgeDesc=\"".$this->badgeDesc. "\" AND date=\"".$date_query."\"";
        echo $query;
        // prepare query statement
        $stmt = $this->conn->prepare($query);
    
        // execute query
        $stmt->execute();
    
        return $stmt;
    }

    public function add_badge() {
        // query to insert record
        $query = "INSERT INTO " . $this->table_name . " SET
        badgeDesc=:badgeDesc, userId=:userId, date=:date";

        echo $query;
        // prepare query
        $stmt = $this->conn->prepare($query);

        // sanitize
        $this->userId=htmlspecialchars(strip_tags($this->userId));
        $this->badgeDesc=htmlspecialchars(strip_tags($this->badgeDesc));
        $this->date=htmlspecialchars(strip_tags($this->date));

        // bind values
        $stmt->bindParam(":userId", $this->userId);
        $stmt->bindParam(":badgeDesc", $this->badgeDesc);
        $stmt->bindParam(":date", date($this->date));

        // execute query
        if($stmt->execute()){
            return true;
        }
        return false;
    }
}
?>