<?php
    class database{
        private $db;
        function connect(){
            $this->db = new mysqli("localhost","root","","shopshock");
            $this->db->set_charset("utf8");
            if($this->db->connect_errno) echo "Error something";
        } 
        function query($sql, $option=MYSQLI_NUM){
            $result = $this->db->query($sql);
            return $result->fetch_all($option);
        }
        function exec($sql){ return $this->db->query($sql);}
        function close(){ $this->db->close();}
        // function stmit_exec($sql,$par){
        //     $stmt = $this->db->pre
        // }
    }
    //return $result;
?>