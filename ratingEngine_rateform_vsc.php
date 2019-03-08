<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../../css/aldin/style.css">
<link rel="stylesheet" type="text/css" href="../../css/aldin/rate-structure.css">
<link rel="stylesheet" type="text/css"  href="../../css/aldin/bootstrap.min.css">
<link rel="stylesheet" type="text/css"  href="../../css/aldin/bootstrap.css">
<link rel="stylesheet" type="text/css"  href="../../css/aldin/datepicker.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="../../js/aldin/bootstrap.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script src="../../js/aldin/modal.js" type="text/javascript"></script>
<script src="../../js/aldin/validate.min.js" type="text/javascript"></script>
<script src="../../js/aldin/jquery.lookupbox.js"></script>
<script src="../../js/aldin/modal.js"></script>
<!--<script src="../../js/modernizr-2.0.6.min.js"></script>-->     
<script src="../../js/aldin/jquery.mask.min.js"></script>
  <script>

    $('.amount').mask('0,000.00', {reverse: true});
    
    $('.gl_account').mask("0000-000-000000-000", {placeholder: "____-___-______-___"});
    $('.vendor').mask("000000", {placeholder: "______"});
  </script>
  
    <script type="text/javascript">
     
        $(window).load(function(){
        (function($) {
            var element = $('.follow-scroll'),
                originalY = element.offset().top;

            // Space between element and top of screen (when scrolling)
            var topMargin =0;

            // Should probably be set in CSS; but here just for emphasis
            element.css('position', 'relative');

            $(window).on('scroll', function(event) {
                var scrollTop = $(window).scrollTop();

                element.stop(false, false).animate({
                    top: scrollTop < originalY
                            ? 0
                            : scrollTop - originalY + topMargin
                }, 0);
            });
        })(jQuery);
        });	 
		
function toggleBundleRates(elem) { 
			var id = elem.id;
			var bundle_name = id.replace('bundle_chkbox_',''); 
			var bundle_rate_table_div = "bundle_rate_"+bundle_name;
		
			alert(elem.id); 	
			if(elem.checked)
			{
				//alert(bundle_rate_table_div); 
				//document.getElementById(bundle_rate_table_div).style.display = "block";
				//document.getElementById(bundle_rate_table_div).innerHTML = "block";
				$("#"+bundle_rate_table_div).show();
			} else {
			//document.getElementById(bundle_rate_table_div).innerHTML = "none";
 				//document.getElementById(bundle_rate_table_div).style.display = "none";
				$("#"+bundle_rate_table_div).hide();
				}                         
		}	    		 
			
        function showDealerCost(elem){

            if(elem.checked){
                $('tr.row_data').hide();
                $('h4.row_data').closest('tr').hide();
            }else{
                $('tr.row_data').show();
                $('h4.row_data').closest('tr').show();
            }

        }		 
			   	
</script>	  
  
  <link rel="stylesheet" type="text/css" href="../../css/aldin/lookupbox.css" />
  <link rel="stylesheet" type="text/css" href="../../css/aldin/custom.css" /> 
  <style>
      table.vsc td {padding: 5px !important;}
      table.vsc th {padding: 5px !important; text-align: center;}
      table.miles th:hover,
      table.miles td:hover,
      table.miles td table.submiles:hover
      {background: #f4f4f4;}
/*      table.miles th:nth-child(odd)  {background: #f4f4f4}
      table.submiles th, table.submiles td {background: #f4f4f4}*/

      
      #container {
            overflow:hidden;
            position:relative;
        }
        
     #hide {
        -webkit-animation: cssAnimation 8s forwards; 
        animation: cssAnimation 8s forwards;
        float: left;
        clear: both;
        margin-left: 20px;
        margin-bottom: 0;
        width: 100%;
        padding-top: 12px;
    }
    
    @keyframes cssAnimation {
        0%   {opacity: 1;}
        90%  {opacity: 1;}
        100% {opacity: 0;}
    }
    @-webkit-keyframes cssAnimation {
        0%   {opacity: 1;}
        90%  {opacity: 1;}
        100% {opacity: 0;}
    }
  </style>
  
<?php
 ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); 
require("../../config/pdr.conf.php");

Util::validate('admin,root,acct,dentguard,ao');

//$menuname = $_GET['menuname'];
//$menu = rateStructure_RateStructure::getMenu($menuname,'Upload Rate');
?>
<script src="https://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="../../js/rate_structure_upload.js"></script>
 
<?php  

$rateform = $_GET['rateform'];
$rate_id = $_GET['rate_id'];
$master_rate_id = $_GET['master_rate_id'];

$success = $_GET['success'] == 1 ? "<div class='alert alert-success' id='hide' style='clear: both;'><strong>Successfully updated.</strong></div>" : '';
$html  = "
<div class=\"container rate-structure-search\" id=\"rate-structure-search\"> 
";

$ratecard_num = $_GET['ratecard_num']; 
$bucketArr = array();

$term_query = "SELECT term FROM rate_bundle_template WHERE rate_id='$rate_id' GROUP BY term";

$rs = $conn->Execute($term_query);

while ($rows = $rs->FetchRow())
{ 
    $termmo[] = $rows['term'];

}


    //1519980957
//$query = "SELECT rate_bundle_template.*, CONCAT(min_odometer,'-',max_odometer) as 'Current Odometer' 
//FROM rate_bundle_template WHERE rate_id='$rate_id' AND class <> ''
//ORDER BY rate_bucket_id, product_bundle_name, bucket, class, mile, term, min_odometer 
//
$query ="SELECT t2.*, concat(t3.nat_acct,'-',dept_acct,'-',cc_acct,'-',co_acct) as account, t3.gl_account,  CONCAT(CAST(t2.min_odometer AS CHAR),'-',CAST(t2.max_odometer AS CHAR)) as 'Current Odometer' FROM dzrateform t1 
        INNER JOIN dzrates t2 ON t1.rateform=t2.rateform 
        LEFT JOIN bucket_types t3 on (t2.bucket_id = t3.id)
        WHERE t1.rateform='$rateform' AND t1.rate_id = '$rate_id' AND t1.master_rate_id='$master_rate_id' 
        AND t2.amount > 0.00 
        ORDER BY t2.rate_bucket_id, t2.product_bundle_name, t2.bucket_type, t2.vehicle_class, t2.mile, t2.term, t2.min_odometer "; 

$res = $conn->Execute($query);

while ($row = $res->FetchRow())
{          
    $dataArr[$row['product_bundle_name']][$row['rate_bucket_id']][$row['type']][$row['bucket_type']][$row['vehicle_class']][$row['mile']][$row['term']][] = $row;	
    $arrCoverage[$row['product_bundle_name']][$row['rate_bucket_id']][$row['type']][$row['bucket_type']]['coverage'] = $row['benefit'];
    $arrCoverage[$row['product_bundle_name']][$row['rate_bucket_id']][$row['type']][$row['bucket_type']]['type'] = $row['type'];
    $account = $row['gl_account'];
    $glArr = array(substr($account, 0, 3), substr($account, 3, 2), substr($account, 5, 4), substr($account, 9, 4),substr($account, 13, 6));  
    $arrCoverage[$row['product_bundle_name']][$row['rate_bucket_id']][$row['type']][$row['bucket_type']]['account'] = $row['gl_account'] ? implode('-',$glArr) : $row['account'];
    $arrCoverage[$row['product_bundle_name']][$row['rate_bucket_id']][$row['type']][$row['bucket_type']]['vendor'] = $row['party'];
}       

//echo '<pre>'; print_r($arrCoverage); echo '</pre>';

$countCol = "SELECT * FROM rate_bundle_template WHERE rate_id='$rate_id' GROUP BY mile  ";
$rs = $conn->Execute($countCol);


while ($rows = $rs->FetchRow()){
    $cols[] = $rows;
}

$colspan = count($cols) + 1;
//echo '<pre>'; print_r($dataArr); echo '</pre>';

            
//        $qry ="select bundle_name,product_bundle_name,  class, term, mile, min_odometer ,max_odometer, sum(amount) as dealer_cost from rate_template 
//           where rate_id='$rate_id' 
//           AND add_to_dealer_cost = 1 AND class != ''
//           group by bundle_name, class,term,mile";    
$dealerCostArr = ratingEngine_Rate::getVSCDealerCost($rate_id, $conn);
        
//        echo '<pre>'; print_r($dealerCostArr); echo '</pre>';

$html .= "<form name='vsc' action='ratingEngine_view_VSC_bundle_save.php?rate_id={$rate_id}&master_rate_id={$master_rate_id}' method='post' >"
. "<div class='follow-scroll-do-not-scroll' style='    overflow: hidden;   background: #fff;'> ";
		foreach($dataArr as $bundle=>$bundleArr)
		{
			$bii++;
			$html.= "<input type='checkbox' id='bundle_chkbox_$bii' checked=true onclick='toggleBundleRates(this);'/>$bundle &nbsp;&nbsp;&nbsp;";
			
		}
		$bii = 0; 
		$html.= "<input type='checkbox' id='show_dealer_cost'  onchange='showDealerCost(this);'/>Show Dealer Cost Only &nbsp;&nbsp;&nbsp;";
		$html.= "<br /></div>";
		    
//        . "<table border='1' width='auto' class='vsc'>";
foreach($dataArr as $bundle=>$bundleArr){
		$bi++;
        $html .= "<br /> =========================================================== <br />";
		$html .= "<div id='bundle_rate_$bi'> <table border='1' width='auto' class='vsc' style='display:block;'> <tr>";
        $html .= "<th colspan='{$colspan}' style='text-align: left; background-color:#7891ab; font-size: 24px; font-weight: bold; color: #fff;'>{$bundle}</th>";
        $html .= "</tr>";    
                  
    foreach($bundleArr as $rate_bucket_id=>$typeArr){
            
        foreach($typeArr as $type=>$benefitsArr){
            
            foreach($benefitsArr as $buckets=>$bucketsArr){
                $coverage = $arrCoverage[$bundle][$rate_bucket_id][$type][$buckets]['coverage'] ? $arrCoverage[$bundle][$rate_bucket_id][$type][$buckets]['coverage'] : '--'; 
                $rate_type = $arrCoverage[$bundle][$rate_bucket_id][$type][$buckets]['type'];
                $gl_value = $arrCoverage[$bundle][$rate_bucket_id][$type][$buckets]['account'];
                $gl = "{$gl_value}"; 
                $vendor_value = $arrCoverage[$bundle][$rate_bucket_id][$type][$buckets]['vendor'];
                $vendor = "{$vendor_value}"; 

                $desc = dzProduct_DBOperations::select('buckets', array('code'=>$buckets), $conn, array('description'));

                $script = "<script>
                                    $(document).ready(function(){

                                        $(\".main_gl_account_{$rate_bucket_id}_{$buckets}\").keyup(function(){
                                            $(\".gl_account_{$rate_bucket_id}_{$buckets}\").val($(\".main_gl_account_{$rate_bucket_id}_{$buckets}\").val());
                                        });

                                        $(\".main_vendor_{$rate_bucket_id}_{$buckets}\").keyup(function(){
                                            $(\".vendor_data_{$rate_bucket_id}_{$buckets}\").val($(\".main_vendor_{$rate_bucket_id}_{$buckets}\").val());
                                        });

                                    });
                                    </script>";

                $html .= "<tr class='row_data'> $script";
                $html .= "<th  colspan='{$colspan}' style='text-align: left; background: #f5f5f5; padding-left: 0px; padding-right:0px; padding-bottom:0px; '>"
                . "<h5 style='font-weight: bold; font-size: 16px;'>"
                        . "{$coverage} /"
                        . " {$buckets} "
        //                . "({$desc[0]['description']})"
                        . "/ "
                        . " {$rate_type} /"
                        . " {$gl} / "
                        . " {$vendor} "                
                        . "</h5>"
                   . "</th></tr>";   

                foreach($bucketsArr as $class=>$classArr){

                   $html .= " <th style='padding: 0 !important;  ' colspan='{$colspan}'> <h4 class='row_data' style='margin: 0; background: #d4dbe2; padding: 15px; font-weight: bold; font-size: 16px; text-align: left; '>Class {$class}</h4></th>"
                   . "</tr>";
                   $html .= "<tr class='row_data'>";

                    foreach($classArr as $miles=>$milesArr){

                        $html .= "<th style='text-align:center; background: #d4dbe2; padding: 0 !important;'><!-- <h4>{$miles}K Miles &nbsp;</h4> -->";     
                            $html .= "<table border='1'  style='background:#fff;     margin-top: -2px; margin-left: -1px; margin-bottom: -2px; ' class='miles'><tr>";
                                foreach($milesArr as $term=>$termArr){
                                    $html .= "<th style='text-align:center; padding: 0 !important;'><h5><strong>{$miles}K Miles</strong> <br /> {$term} months </h5>";
                                        $html .= "<table border='1' style='width: 170px; background:#fff;  margin-right: -1px; margin-left: -1px;' class='submiles'>"
                                                . "<tr class=''><th>Current <br /> Odometer</th><th>Amount</th></tr>";
                                            //$dealer_cost = 0;            
                                            foreach($termArr as $k=>$data){
        //                                            $html .= "<th style='padding: 0 !important;'><table>";
                                                        $html .= "<tr class='row_data'><th style='pading: 5px;'>{$data['Current Odometer']}</th>";
                                                        $html .= "<td style='border-top: solid 1px; text-align: center;'>"
                                                                . "{$data['amount']}"
                                                                .  "</td></tr>";
        //                                            $html .= "</table></th>";
    //                                                 if($data['add_to_dealer_cost'] == 1){
    //                                                     $dealer_cost = number_format($dealer_cost +  $data['amount'], 2);          
    //                                                 }           

                                            } 
    //                                        $html .="<tr class='row_dealer_cost'>"
    //                                                . "<th style='pading: 5px;'>Dealer Cost</th>"
    //                                                . "<td style='  text-align: center; font-weight: bold;'><input type='text' class='amount' name='dealer_cost_{$data['id']}' disabled id='dealer_cost_{$data['id']}' value='{$dealer_cost}' style='padding-left: 5px; width: 60px; margin: 0;' /></td>"
    //                                                . "</tr>";
                                        $html .= "</table>";
                                    $html .= "</th>";
                                }
                            $html .= "</tr></table>";   
                        $html .= "</th>";   
                    }

                    $html .= "</tr>";   
                 }

            } 
        }
        
    }
    
    $html .= "<th colspan='{$colspan}' style='text-align: left; background: #f5f5f5; '>"
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
                                    $html .= "<table border='1' style='width: 170px; background:#fff;  margin-right: -1px; margin-left: -1px;' class='submiles'>";
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
$bi = 0;
$insert = $html;   

include(TEMPLATE);  
                    
exit;               