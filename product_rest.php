<?php 
    session_start();
?>
<?php
    include_once("class.db.php");
    if($_SERVER["REQUEST_METHOD"]=='GET'){
        echo json_encode(product_list(),JSON_UNESCAPED_UNICODE);
        //echo json_encode(openbill());
    }else if($_SERVER["REQUEST_METHOD"]=='POST'){
        echo json_encode(openbill());

    }
    function product_list(){
        $db = new database();
        $db->connect();
        $sql = "SELECT Product_id,Product_code,Product_Name,
                       brand.Brand_name, unit.Unit_name,
                       product.Cost, product.Stock_Quantity
                FROM  product,brand,unit 
                WHERE product.Brand_ID = brand.Brand_id
                and   product.Unit_ID  = unit.Unit_id";
        $result = $db->query($sql);
        $db->close();
        return $result;
    }
    
    function openbill(){
        $current_bill = 1;
        $_first[0][0] = 0;
        $sql= [ "firstbill"   =>"SELECT count(Bill_id) FROM bill",
                "last_id"     =>"SELECT Bill_id FROM bill order by Bill_id desc limit 1",
                "current_bill"=>"SELECT Bill_id, Cus_id, Emp_id, Bill_date, Bill_Status FROM bill 
                                 WHERE Cus_ID = {$_SESSION['cus_id']} and Bill_id = {$current_bill}",
                "current_detail"=>" SELECT bill_detail.Bill_id, bill_detail.Product_ID, product.Product_Name, bill_detail.Quantity,
                                    bill_detail.Unit_Price,(bill_detail.Quantity*bill_detail.Unit_Price) AS 'Total'
                                    FROM bill_detail, product
                                    WHERE product.Product_ID = bill_detail.Product_ID and Bill_id=($current_bill) ",
                "openbill"    =>"INSERT INTO bill(Bill_id, Cus_ID, Bill_Status) 
                                 VALUES ('{$current_bill}','{$_SESSION['cus_id']}',0)",
                "ins_pro"     =>"INSERT INTO bill_detail(Bill_id, Product_ID, Quantity, Unit_Price) 
                                 VALUES ({$current_bill},{$_POST['p_id']},{$_POST['p_qty']},{$_POST['p_price']})",
                "check_pro"   =>"SELECT count(Product_ID) FROM bill_detail 
                                 WHERE Bill_id={$current_bill} and Product_ID ={$_POST['p_id']}", 
                "update_pro"   =>"UPDATE bill_detail SET Quantity={$_POST['p_qty']}, Unit_Price={$_POST['p_price']} 
                                 WHERE Bill_id = {$current_bill} and Product_ID = {$_POST['p_id']}",
                                
            ];
        $db = new database();
        $db->connect();
        $_first = $db->query($sql["firstbill"]);
        if($_first[0][0]==0){
            $result = $db->exec($sql["openbill"]);
            $result = $db->exec($sql["ins_pro"]);
        }else{
            $_bill = $db->query($sql["current_bill"]);
            if($_bill[0][1]==0){
                $result = $db->exec($sql["ins_pro"]); 
                if($result == 0){ // update
                    $result = $db->exec($sql["update_pro"]);
                }
            }else{
                $_last = $db->query($sql["last_id"]);
                $current_bill = $_last[0][0]+1; 
                $result = $db->exec($sql["openbill"]);
                $result = $db->exec($sql["ins_pro"]); 

            }                                                                                           
                    
        }
        $_result1 = $db->query($sql["current_bill"]);
        $_result2 = $db->query($sql["current_detail"]);
        $db->close();
        return ["bill_head"=>$_result1, "bill_detail"=>$_result2];
    }   
?>