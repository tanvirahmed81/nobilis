<?php
 require("../../../config/pdr.conf.php");
 
$rate_id = $_POST['rate_id'];
$master_rate_id = $_POST['master_rate_id'];

$success = $_GET['success'] == 1 ? "<div class='alert alert-success' id='hide' style='clear: both;'><strong>Successfully updated.</strong></div>" : '';
$html  = "
<div class=\"container rate-structure-search\" id=\"rate-structure-search\"> 
";

$ratecard_num = $_GET['ratecard_num']; 
$bucketArr = array();

//$term_query = "SELECT term FROM rate_bundle_template WHERE rate_id='$rate_id' GROUP BY term";
//
//$rs = $conn->Execute($term_query);
//
//while ($rows = $rs->FetchRow())
//{ 
//    $termmo[] = $rows['term'];
//
//}

$WHERE = '';
if( $_POST['options']){
    $optionArr = explode(',',$_POST['options']);
    $bundle_name = "'".implode("','", $optionArr)."'";
    $WHERE .= " AND product_bundle_name IN ($bundle_name) ";
}

if( $_POST['termmo']){
    $terms = $_POST['termmo'];
    $WHERE .= " AND term IN ($terms) ";
}

if( $_POST['classes']){
    $classesArr = explode(',',$_POST['classes']);
    $classes = "'".implode("','", $classesArr)."'";
//    echo $classes; exit;
    $WHERE .= " AND class IN ($classes) ";
}
    
$query = "SELECT rate_bundle_template.*, CONCAT(min_odometer,'-',max_odometer) as 'Current Odometer' 
FROM rate_bundle_template WHERE rate_id='$rate_id' AND class <> '' $WHERE
ORDER BY rate_bucket_id, product_bundle_name, bucket, class, mile, term, min_odometer ";

$res = $conn->Execute($query);

while ($row = $res->FetchRow())
{          
    $dataArr[$row['product_bundle_name']][$row['rate_bucket_id']][$row['bucket']][$row['class']][$row['mile']][$row['term']][] = $row;	
    $arrCoverage[$row['product_bundle_name']][$row['rate_bucket_id']][$row['bucket']]['coverage'] = $row['coverage'];
    $arrCoverage[$row['product_bundle_name']][$row['rate_bucket_id']][$row['bucket']]['type'] = $row['type'];
    $arrCoverage[$row['product_bundle_name']][$row['rate_bucket_id']][$row['bucket']]['account'] = $row['account'];
    $arrCoverage[$row['product_bundle_name']][$row['rate_bucket_id']][$row['bucket']]['vendor'] = $row['vendorid'];
}       

//echo '<pre>'; print_r($arrCoverage); echo '</pre>';

$countCol = "SELECT * FROM rate_bundle_template WHERE rate_id='$rate_id' GROUP BY mile  ";
$rs = $conn->Execute($countCol);


while ($rows = $rs->FetchRow()){
    $cols[] = $rows;
}

$colspan = count($cols) + 1;

$dealer_cost_qry = "select product_bundle_name, bundle_name,class, mile, term,concat(CAST(min_odometer AS CHAR),'-',CAST(max_odometer AS CHAR))  as mband,
		concat(CAST(term as CHAR),'-',CAST(mile as CHAR)) as termmile, sum(amount) as dealer_cost "
                . "from rate_bundle_template "
                . "where rate_id='$rate_id' and add_to_dealer_cost='1' and class <> '' $WHERE  group by bundle_name,class,concat(min_odometer,'-',max_odometer),concat (term,'-',mile) "
                . "order by bundle_name asc, class asc, min_odometer asc, max_odometer asc, term asc";
                
            $rs1 = $conn->Execute($dealer_cost_qry);

            $dealerCostArr = array();
            while ($datarows = $rs1->FetchRow())
            { 
                $dealerCostArr[$datarows['product_bundle_name']][$datarows['class']][$datarows['mile']][$datarows['term']][] = $datarows;

            }


foreach($dataArr as $bundle=>$bundleArr){
		$bi++;
        
		$html .= "<div id='bundle_rate_$bi'> <table border='1' width='auto' class='vsc' style='display:block;'> <tr>";
                $html .= "<th colspan='{$colspan}' style='text-align: left; background-color:#7891ab; font-size: 24px; font-weight: bold; color: #fff;'>{$bundle}</th>";
                $html .= "</tr>";    
                  
    $html .= "<tr><th colspan='{$colspan}' style='text-align: left; background: #f5f5f5; '>"
            . "<h5 style='font-weight: bold; font-size: 16px;'>DEALER COST</h5>"
               . "</th>";
            $html .= "</tr>"; 
//            print_r($dealerCostArr[$bundle]);
         foreach($dealerCostArr[$bundle] as $d_class=>$d_classArr){

               $html .= "<tr><th style='padding: 0 !important;  background: #d4dbe2;' colspan='{$colspan}'> <h4 style='margin: 15px; font-weight: bold; font-size: 16px; text-align: left; '>Class {$d_class}</h4></th></tr>";
                $html .= "<tr>";
                    

                foreach($d_classArr as $d_miles=>$d_milesArr){

                    $html .= "<th style='text-align:center; background: #d4dbe2; padding: 0 !important;'><!-- <h4>{$d_miles}K Miles &nbsp;</h4> -->";     
                        $html .= "<table border='1'  style='background:#fff;     margin-top: -2px; margin-left: -1px; margin-bottom: -2px; ' class='miles'><tr>";
                            foreach($d_milesArr as $d_term=>$d_termArr){
                                $html .= "<th style='text-align:center; padding: 0 !important;'><h5><strong>{$d_miles}K Miles</strong> <br /> {$d_term} months </h5>";
                                    $html .= "<table border='1' style='width: 175px; background:#fff;  margin-right: -1px; margin-left: -1px;' class='submiles'>";
                                    $html .= "<tr class=''><th>Current <br /> Odometer</th><th>Amount</th></tr>";
                                            
                                        foreach($d_termArr as $key=>$d_data){       
                                                    $html .= "<tr class=''><th style='pading: 5px;'>{$d_data['mband']}</th>";
                                                    $html .= "<td style='border-top: solid 1px; text-align: center; font-weight: bold;'>"
                                                            . "<input type='text' class='amount' disabled name='dealer_cost_{$d_data['id']}' id='dealer_cost_{$data['id']}' value='{$d_data['dealer_cost']}' style='padding-left: 5px; width: 60px; margin: 0;' />"
                                                            . "</td></tr>";               
                                                 
                                        } 
                                        
                                    $html .= "</table>";
                                $html .= "</th>";
                            }
                        $html .= "</tr></table>";   
                    $html .= "</th>";   
                }
                
                $html .= "</tr>";
         }
            
	$html .= "</table> </div>";
	
}
//$html .= "</table>"
$html .= "</form>";
$html .= "</div>"; 

echo $html;