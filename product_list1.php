<?php 
    session_start();
    $_SESSION['cus_id']= 1;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body onload= "load_doc()">
    <center>
        <div id="out"></div>
        <br>
        <div id="out2"></div>
        <br>
        <div id="out3"></div>
        <br>
        <div id="out4"></div>
        <br>
    </center>
    <script>
    let arr;
    let cus_id = <?= $_SESSION['cus_id'] ?>;
    label = ['item id','product code','product name','brand','หน่วยนับ','ราคาขาย','จำนวนสินค้าที่ต้องการ'];
    function load_doc(){
        out = document.getElementById("out");
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){
            if(this.readyState==4 && this.status==200){
                arr= JSON.parse(this.responseText);
                text = "<table border='1'>";
                for(i=0;i<label.length-1;i++){
                    text += "<th>"+label[i]+"</th>";
                }
                text = "<tr>"+text+"</tr>";
                for(i=0;i<arr.length;i++){
                    for(j=0;j<arr[i].length-1;j++){
                        text += "<td>"+arr[i][j]+"</td>";
                    }
                    text += "<td>"+"<button onclick='sel_product("+i+")'>< ShopShock ></button>"+"<td>";
                    text = "<tr>"+text+"</tr>";
                }

                text += "</table>";
                out.innerHTML = text;
            }
        }
        xhttp.open("GET","product_rest.php",true);
        xhttp.send();
    }

    function sel_product(idx){
        out = document.getElementById("out2");
        text = "";
        text = "<table border='1'>";
        for(i=0;i<label.length-1;i++){
            text += "<tr><td>"+label[i]+"</td>";
            text += "<td>"+arr[idx][i]+"</td></tr>";
        }
        text += "<tr><td>"+label[6]+"</td>";
        text += "<td><input type='number' id='n"+idx+"' min='1' max='"+arr[idx][6]+"'></td></tr>";
        text += "<tr><td colspan='2'><button onclick='open_po("+idx+","+cus_id+")'>add to cart</button><input type='reset'></td></tr>"
        text += "</table>";
        out.innerHTML = text;
    }

    function open_po(idx,cus_id){
        out = document.getElementById("out3");
        out = document.getElementById("out4");
        qty = document.getElementById("n"+idx);
        //alert("product_code="+arr[idx][1]+"="+qty.value);
        p_price = arr[idx][5];
        let xhttp = new XMLHttpRequest();
        xhttp.onreadystatechange = function(){ 
            if(this.readyState==4 && this.status==200){
                alert(this.responseText);
                arr2 = JSON.parse(this.responseText);
                head = arr2 ["bill_head"][0];
                text="<table border='1'>";
                text += "<tr><th> Bill_id</th> <th>Cus_ID</th> <th>Emp_ID</th> <th>Bill_Date</th> <th>Bill_Status</th> <th>Paid?</th></tr>";
                
                for(i=0;i<head.length;i++){
                    text += "<td>" + head[i] + "</td>";
                }
                text = "<tr>" + text + "<td><a href='product_rest.php?paid=1'>paid</a></td></tr>";
                text += "</table>";
                out3.innerHTML = text;

                
                detail = arr2["bill_detail"];
                text2 ="<table border='1'>";
                text2 += "<tr><th>Product_ID</th> <th>Product_Code</th> <th>Price</th> <th>Product_Qty</th> <th>Total</th></tr>";
                for(x=0;x<detail.length;x++){
                    for(y=0;y<detail[x].length;y++){
                        text2 += "<td>" + detail[x][y] + "</td>"; 
                    }
                    text2 = "<tr>" + text2 + "</tr>";
                    //text2 += "</table>";
                }
                out4.innerHTML = text2;
                
            }
            
        }
        xhttp.open("POST","product_rest.php",true);
        xhttp.setRequestHeader("Content-type","application/x-www-form-urlencoded");
        xhttp.send("p_id="+arr[idx][0]+"&p_qty="+qty.value+"&p_price="+p_price);
    }
    </script>
</body>
</html>