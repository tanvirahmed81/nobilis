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
    
    //$('.gl_account').mask("0000-000-000000-000", {placeholder: "____-___-______-___"});
    $('.gl_account').mask("000-00-0000-0000-000000", {placeholder: "___-__-____-____-______"});
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
                $('h4.row_data').hide();
            }else{
                $('tr.row_data').show();
                $('h4.row_data').show();
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
//ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); 
require("../../config/pdr.conf.php");

Util::validate('admin,root,acct,dentguard,ao');

//$menuname = $_GET['menuname'];
//$menu = rateStructure_RateStructure::getMenu($menuname,'Upload Rate');
?>
<script src="https://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script> 
<script type="text/javascript" src="../../js/rate_structure_upload.js"></script>
 
<?php  
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
$query = "SELECT rate_bundle_template.*, CONCAT(min_odometer,'-',max_odometer) as 'Current Odometer' 
FROM rate_bundle_template WHERE rate_id='$rate_id' AND class <> ''
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
//echo '<pre>'; print_r($dataArr); echo '</pre>';

            
//        $qry ="select bundle_name,product_bundle_name,  class, term, mile, min_odometer ,max_odometer, sum(amount) as dealer_cost from rate_template 
//           where rate_id='$rate_id' 
//           AND add_to_dealer_cost = 1 AND class != ''
//           group by bundle_name, class,term,mile";    
$dealerCostArr = ratingEngine_Rate::getVSCDealerCost($rate_id, $conn);
        
//        echo '<pre>'; print_r($dealerCostArr); echo '</pre>';

$html .= "<form name='vsc' action='ratingEngine_view_VSC_bundle_save.php?rate_id={$rate_id}&master_rate_id={$master_rate_id}' method='post' >"
. "<div class='follow-scroll-do-not-scroll' style='    overflow: hidden;   background: #fff;'> "
        . "<input type='submit' name='save' class='btn btn-primary' style='clear: both;' value='Save VSC Bundle Rate' />$success<br />"
        . ""
        . "<br /><strong>Term Months: </strong>";

if($_GET['terms']){
     $terms = explode(',', $_GET['terms']);
        foreach($termmo as $term){
            if(in_array($term, $terms)){
                $checked='checked';
            }else{
                $checked='';
            }
              $html .= "<input type='checkbox' value='$term' name='termmo$term' $checked /> <span style='font-size: 17px; ' >{$term}</span> &nbsp;";
        }
}else{
        foreach($termmo as $term){
              $html .= "<input type='checkbox' value='$term' name='termmo$term' checked /> <span style='font-size: 17px; ' >{$term}</span> &nbsp;";
        }
}
        
$html .= "<br /><input style=\"float: left; width: 350px; margin-right: 10px; font-size: 16px;\" type=\"text\" name=\"rate_name\" id=\"rate_name\" required=\"required\" class=\"form-control\" placeholder=\"Rate Name\" value='{$_GET['rate_name']}'>";        
$html .= "<br /><br /><br /><strong>Cancel Fee GL:</strong><br /><input  type='text' name='cancel_fee' style=\" width: 350px; margin-top: 10px; font-size: 16px;\" required=\"required\" value='{$_GET['gl_cancel_fee']}'  class=' gl_account form-control' style='padding: 3px;' placeholder=\"Cancel Fee GL\" />";        
        
$html   .= "<hr /></div>
		<br /> <br />
		<div id='rate_bundle_chkboxes'>";
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
                  
    foreach($bundleArr as $rate_bucket_id=>$benefitsArr){
            

        foreach($benefitsArr as $buckets=>$bucketsArr){
            $coverage = $arrCoverage[$bundle][$rate_bucket_id][$buckets]['coverage'] ? $arrCoverage[$bundle][$rate_bucket_id][$buckets]['coverage'] : '--'; 
            $type = $arrCoverage[$bundle][$rate_bucket_id][$buckets]['type'];
            $gl_value = $arrCoverage[$bundle][$rate_bucket_id][$buckets]['account'];
            $gl = "<input type='text' value='{$gl_value}' class='main_gl_account_{$rate_bucket_id}_{$buckets} gl_account' style='padding: 3px;' />"; 
            $vendor_value = $arrCoverage[$bundle][$rate_bucket_id][$buckets]['vendor'];
            $vendor = "<input type='text' value='{$vendor_value}' class='main_vendor_{$rate_bucket_id}_{$buckets} vendor' style='padding: 3px; width: 90px; /> "; 

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
                    . " {$type} /"
                    . " {$gl} / "
                    . " {$vendor} "                
                    . "</h5>"
               . "</th>";   

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
                                                            . "<input type='text' class='amount' name='{$data['id']}' id='{$data['id']}' value='{$data['amount']}' style='padding-left: 5px; width: 60px; margin: 0;' />"
                                                            . "<input type='hidden' class='gl_account_{$rate_bucket_id}_{$buckets}' name='gl_{$data['id']}' id='gl_{$data['id']}' value='{$data['account']}' />"
                                                            . "<input type='hidden' class='vendor_data_{$rate_bucket_id}_{$buckets}' name='vendor_{$data['id']}' id='vendor_{$data['id']}' value='{$data['vendorid']}' /> "
                                                            . "</td></tr>";
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