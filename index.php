<meta name="viewport" content="width=device-width, initial-scale=1">
<link rel="stylesheet" type="text/css" href="../../../css/aldin/style.css">
<link rel="stylesheet" type="text/css" href="../../../css/aldin/rate-structure.css">
<link rel="stylesheet" type="text/css"  href="../../../css/aldin/bootstrap.min.css">
<link rel="stylesheet" type="text/css"  href="../../../css/aldin/bootstrap.css">
<link rel="stylesheet" type="text/css"  href="../../../css/aldin/datepicker.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="../../../js/aldin/bootstrap.min.js" type="text/javascript"></script>
<script src="//code.jquery.com/ui/1.11.2/jquery-ui.js"></script>
<script src="../../../js/aldin/modal.js" type="text/javascript"></script>
<script src="../../../js/aldin/validate.min.js" type="text/javascript"></script>
<script src="../../../js/aldin/jquery.lookupbox.js"></script>
<script src="../../../js/aldin/modal.js"></script>
<!--<script src="../../js/modernizr-2.0.6.min.js"></script>-->     
<script src="../../../js/aldin/jquery.mask.min.js"></script>
  <script>

//    $('.amount').mask('0,000.00', {reverse: true});
    
    //$('.gl_account').mask("0000-000-000000-000", {placeholder: "____-___-______-___"});
    $('.gl_account').mask("000-00-0000-0000-000000", {placeholder: "___-__-____-____-______"});
    $('.vendor').mask("000000", {placeholder: "______"});
  </script>
  
    <script type="text/javascript">

        function showVSC()
        {
            $('#show_vsc').html('');    
            $('#preloader').show(); 
            
            var show_dealer_cost = $("input[name=dealer_cost]:checked").map(function() {return this.value;}).get().join(",");
            
            var view;
            
            if(show_dealer_cost == 'yes'){
                view = 'view_dealer_cost.php';
            }else{
                view = 'view.php'
            }
            
            
            $.post(view, 
                {
                    rate_id: <?php echo $_GET['rate_id']; ?>,
                    master_rate_id: <?php echo $_GET['master_rate_id']; ?>,
                    options: $("input[name=options]:checked").map(function() {return this.value;}).get().join(","),
                    show_dealer_cost: $("input[name=dealer_cost]:checked").map(function() {return this.value;}).get().join(","),
                    termmo: $("input[name=termmo]:checked").map(function() {return this.value;}).get().join(","),
                    classes: $("input[name=classes]:checked").map(function() {return this.value;}).get().join(",")

                }, 

                function(output){ 

                        $('#show_vsc').html(output).show();  
                        $('#preloader').hide(); 

                });  
        }
        
        function update_amount(id, amount){
        
        $.post('update_amount.php', 
                {
                    id: id,
                    amount: amount
                }, 
                function(output){ 
                    console.log('amount was successfully updated.');
                });  
        }
        
       function update_gl(rate_id, product_bundle_name, coverage, bucket, gl){
        
        $.post('update_gl.php', 
                {
                    rate_id: rate_id,
                    product_bundle_name: product_bundle_name,
                    coverage: coverage,
                    bucket: bucket,
                    gl: gl
                }, 
                function(output){ 
                    console.log('GL was successfully updated.');
                    //$('#gl_update').html(output).show(); 
                });  
        }
        
        function update_vendor(rate_id, product_bundle_name, coverage, bucket, vendor){
        
        $.post('update_vendor.php', 
                {
                    rate_id: rate_id,
                    product_bundle_name: product_bundle_name,
                    coverage: coverage,
                    bucket: bucket,
                    vendor: vendor
                }, 
                function(output){ 
                    console.log('Vendor was successfully updated.');
                    //$('#gl_update').html(output).show(); 
                });  
        }
    		 
			   	
    </script>	  
  
  <link rel="stylesheet" type="text/css" href="../../../css/aldin/lookupbox.css" />
  <link rel="stylesheet" type="text/css" href="../../../css/aldin/custom.css" /> 
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
    .checkbox-inline {
    width: auto !important; 
    margin-top: 10px !important;
    margin-bottom: 10px !important;
}
  </style>
  
<?php
//ini_set('error_reporting', E_ALL & ~E_NOTICE & ~E_STRICT & ~E_DEPRECATED); 
require("../../../config/pdr.conf.php");

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

$query = "SELECT rate_bundle_template.*, CONCAT(min_odometer,'-',max_odometer) as 'Current Odometer' 
FROM rate_bundle_template WHERE rate_id='$rate_id' AND class <> ''
ORDER BY rate_bucket_id, product_bundle_name, bucket, class, mile, term, min_odometer ";

$res = $conn->Execute($query);

while ($row = $res->FetchRow())
{          
    $dataArr[$row['product_bundle_name']][$row['rate_bucket_id']][$row['bucket']][$row['class']][$row['mile']][$row['term']][] = $row;	
}  

$html = '';
$html .= "<form name='vsc' action='save.php?rate_id={$rate_id}&master_rate_id={$master_rate_id}' method='post' >"
. "<div > "
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
              $html .= "<label class=\"checkbox-inline\"><input type=\"checkbox\" $checked value='$term' name='termmo$term' /> $term </label>";
                      //. "<input type='checkbox' value='$term' name='termmo$term' $checked /> <span style='font-size: 17px; ' >{$term}</span> &nbsp;";
        }
}else{
        foreach($termmo as $term){
              $html .= "<input type='checkbox' value='$term' name='termmo$term' checked /> <span style='font-size: 17px; ' >{$term}</span> &nbsp;";
        }
}
        
$html .= "<br /><input style=\"float: left; width: 350px; margin-right: 10px; font-size: 16px;\" type=\"text\" name=\"rate_name\" id=\"rate_name\" required=\"required\" class=\"form-control\" placeholder=\"Rate Name\" value='{$_GET['rate_name']}'>";        
$html .= "<br /><br /><br /><strong>Cancel Fee GL:</strong><br /><input  type='text' name='cancel_fee' style=\" width: 350px; margin-top: 10px; font-size: 16px;\" required=\"required\" value='{$_GET['gl_cancel_fee']}'  class=' gl_account form-control' style='padding: 3px;' placeholder=\"Cancel Fee GL\" />";        
        
$html   .= " </div>
		<br /> 
                <h4 class='title'>Please select one or more options  to show VSC.</h4>
		<div id='rate_bundle_chkboxes' class='alert alert-info'> ";

        $html.= " <strong>Contract Options:</strong><br />";
		foreach($dataArr as $bundle=>$bundleArr)
		{
			$bii++;
			$html.= "<label class=\"checkbox-inline\"><input type=\"checkbox\" name='options' checked id='bundle_chkbox_$bii' value=\"$bundle\">$bundle</label>  ";
			
		}
		$bii = 0; 
		
		$html.= " <label class=\"checkbox-inline\"><input type=\"checkbox\"  name='dealer_cost'  value=\"yes\">Show Dealer Cost Only</label> "
                        . "<br /><strong>Term Months:</strong><br />" ;
                 foreach($termmo as $term){
                        $html .= "<label class=\"checkbox-inline\"><input type=\"checkbox\" checked value='$term' name='termmo' /> $term </label>";
                 }
                    
                    $html.= " <br /><strong>Classes:</strong><br />" ;
                    $classes = array(1,2,3,4,5,6,7,8,9,10);
                    foreach($classes as $class){
                        $html .= "<label class=\"checkbox-inline\"><input type=\"checkbox\" checked value='$class' name='classes' /> Class $class </label>";
                 }
                        $html .= "<br />"
                                . "<a href='#showdata' onclick='showVSC()' class='btn btn-warning'>Show Rates</a>"
                                . "</div>";

            
	$html .= " </div>";
        
        $html .= " <a name='showdata' ></a>"
                . "<div id='preloader' style=\"display: none;  text-align: center; margin-right: 5px; position: relative; \"><img src='preloader.gif' />  </div>  "
                . "<div id='gl_update'> </div>"
                . "<div class='row alert'> "
                . " <div class='col'  id='show_vsc'> </div>"
                . "</div>";
	
//$html .= "</table>"
$html .= "</form>";
$html .= "</div>"; 
$bi = 0;
$insert = $html;   

include(TEMPLATE);  
              