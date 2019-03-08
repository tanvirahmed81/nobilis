
<link rel="stylesheet" type="text/css" href="../../css/aldin/bootstrap/css/bootstrap-responsive.min.css">
<link rel="stylesheet" type="text/css" href="../../css/aldin/style.css">
<link rel="stylesheet" type="text/css" href="../../css/aldin/product-setup.css">
<link rel="stylesheet" type="text/css" href="../../css/aldin/ratingEngine.css">
<!--<link rel="stylesheet" type="text/css"  href="../../css/aldin/bootstrap.min.css">-->
<link rel="stylesheet" type="text/css"  href="../../css/aldin/bootstrap.css">
<link rel="stylesheet" type="text/css"  href="../../css/aldin/datepicker.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script>

<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">

<!-- jQuery library -->
<!--<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>-->

<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js"></script>

<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script src="../../js/aldin/ratingEngine_search.js"></script>


<?php
require("../../config/pdr.conf.php");
Util::validate('admin,root,acct,dentguard,ao');

$productObj = new productSetup_Product($conn); 
$partnerArr = $productObj->getAllPartners();

$menuname = $_GET['menuname'];
$menu = rateStructure_RateStructure::getMenu($menuname, 'View Insurer Rate');

$html = "
    $menu 
    <div class=\"container productSetup product ratingEngine\" id=\"productSetup\">
    <form class=\"form-horizontal\" name=\"search\">
<fieldset>

<!-- Form Name -->
<legend>Search</legend>
";

//echo '<pre>'; print_r($_GET); echo '</pre>';
$x=0;
foreach($_GET as $param=>$v){
       
    if($param === 'ratecard_num'){
        $x=0;
    }
    
    //$master_rate_param = "master_rate_id".$x;
    if($param == "master_rate_id0" || $param == "master_rate_id1" || $param == "master_rate_id2"){
     //echo $x.' - '.$param.'>'.$v.'<br />';   
        if($_GET['vsc']==1){
            $ratecardLink = "<a href=\"../rateStructure/view_ratecard_vsc_details_v2.php?master_rate_id={$v}\" target=\"_blank\" class=\"btn btn-primary\">View VSC Rate Card</a>";
        }else{
            $ratecardLink = "<a href=\"../rateStructure/view_ratecard_details.php?ratecard_num={$_GET['ratecard_num']}&master_rate_id={$v}\" target=\"_blank\" class=\"btn btn-primary\">View Rate Card</a>";
        }
        $html .= "
        <div class=\"form-group\">
        <label class=\" col-md-2 control-label\" for=\"\"></label>
        <div class=\"col-md-6\">
            <div class=\"alert alert-success\">
            <strong style=\"font-size: 13px; margin: 10px;\">Master Rate ({$v}) have been successfully added! </strong> 
            $ratecardLink
            </div> 
         </div>
        </div>
        ";
        
    }
        
 $x++;
}

if(isset($_GET['ratecard_num']) && isset($_GET['master_rate_id'])){
    
}

$html .="
<!-- Select Affgrp -->
<div class=\"form-group\">
  <label class=\" col-md-2 control-label\" for=\"affgrp\">Partner</label> 
  <div class=\"col-md-2\">
    <select id=\"affgrp\" name=\"affgrp\" class=\"form-control\" required>
      <option value=\"\">Select</option>
      "; 

foreach($partnerArr as $arr){
    $html .= "<option value=\"{$arr['affgrp']}\">{$arr['generic_partner_name']}</option>";
}
$html .= " 
    </select>
  </div>
</div>";

$html .= ratingEngine_Rate::insurance_company_dropdown($conn);

$html .= "
<!-- Select Basic
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"insurance_company\">Insurance Company</label>
  <div class=\"col-md-2\">
    <select id=\"insurance_company\" name=\"insurance_company\" class=\"form-control\">
      <option value=\"\">Select</option>
      <option value=\"125238\">Arch</option>
      <option value=\"128470\">Amtrust</option>
    </select>
  </div>
</div> -->

<!-- Select Basic -->
<div class=\"form-group\">
    <label class=\"col-md-2 control-label\" for=\"rate_matrix_identifier\">Rate Matrix Identifier</label>
  <div class=\"col-md-2\">
    <select id=\"rate_matrix_identifier\" name=\"rate_matrix_identifier\" class=\"form-control\">
      <option value=\"\">Select</option>
      <option value=\"fd\">First Dollar</option>
      <option value=\"xol\">XOL</option>
      <option value=\"std\">Standard</option>    
    </select>
  </div>
</div>

<!-- Select Basic -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"company\">Company</label>
  <div class=\"col-md-2\">
    <select id=\"company\" name=\"company\" class=\"form-control\">
      <option value=\"\">Select</option>";

      $companyArr = ratingEngine_Rate::getCompany($conn);
      foreach($companyArr as $k=>$v){
          $company = strtolower($k);
            $html .= "<option value=\"$company\">$v ($k)</option>";    
      }
      
$html .= "
    </select>
  </div>
</div>

<!-- Select Basic -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"reinsurance\">Reinsured</label>
  <div class=\"col-md-2\">
    <select id=\"reinsurance\" name=\"reinsurance\" class=\"form-control\">
      <option value=\"\">Select</option>
      <option value=\"1\">Yes</option>
      <option value=\"0\">No</option>
    </select>
  </div>
</div>

<!-- Select Basic -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"dealer_group\">Dealer Group</label>
  <div class=\"col-md-2\">
    <select id=\"dealer_group\" name=\"dealer_group\" class=\"form-control\">
      <option value=\"\">Select</option>
      <option value=\"sewell\">Sewell</option>
    </select>
  </div>
</div>

<!-- Select Basic -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"product_type\">Product Type</label>
  <div class=\"col-md-2\">
    <select name='vehicle_type' id='vehicle_type' required class='form-control'>
        <option value=''>Select</option>
        <option value='auto'>Auto</option>
        <option value='rv'>RV</option>
        <option value='powersports'>Power Sports</option>
        <option value='vsc'>Vehicle Service Contracts</option>
    </select> 
  </div>
</div>


<!-- Select Basic -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"\"></label>
  <div class=\"col-md-2\">
    <div id='loader1' style=\"display: none; float: left; margin-right: 5px;\"><img src='../../images/icn_loading.gif' />  </div>         
  </div>
</div>

<!-- Select Basic -->
<div class=\"form-group\">
  <label class=\"col-md-1 control-label\" for=\"dealer_group\"></label>
  <div class=\"col-md-12\">
    <div id=\"search_result\"></div>
</div>
</div>

              

</fieldset>
</form>
</div>
    
    ";


$insert = $html;


include (TEMPLATE);

?>