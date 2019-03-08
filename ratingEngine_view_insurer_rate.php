<link rel="stylesheet" type="text/css" href="../../css/aldin/bootstrap/css/bootstrap-responsive.min.css">
<link rel="stylesheet" type="text/css" href="../../css/aldin/style.css">
<link rel="stylesheet" type="text/css" href="../../css/aldin/product-setup.css">
<link rel="stylesheet" type="text/css" href="../../css/aldin/ratingEngine.css">
<link rel="stylesheet" type="text/css"  href="../../css/aldin/bootstrap.min.css">
<link rel="stylesheet" type="text/css"  href="../../css/aldin/bootstrap.css">
<link rel="stylesheet" type="text/css"  href="../../css/aldin/datepicker.css">
<link rel="stylesheet" href="//code.jquery.com/ui/1.11.2/themes/smoothness/jquery-ui.css">
<script src="https://code.jquery.com/jquery-1.11.1.min.js" type="text/javascript"></script>
<script src="https://code.jquery.com/ui/1.12.0/jquery-ui.js"></script>
<script src="../../js/aldin/ratingEngine.js" type="text/javascript"></script>
<script>
    $(document).ready(function(){
        $("form#rating_engine input, form#rating_engine select").prop("disabled", true);
    })    
</script>
<?php
// ini_set('display_errors', 1);
  
require("../../config/pdr.conf.php");
Util::validate('admin,root,acct,dentguard,ao'); 

$master_rate_id= $_GET['master_rate_id'];
$master_rate = dzProduct_DBOperations::select('master_rate', array('id'=>$master_rate_id), $conn);
//print_r($master_rate);
$state_list = productSetup_Product::getStates();

$rateObj = new ratingEngine_Rate($conn);
$makeArr = $rateObj->getMakeCodes();
$companyArr = ratingEngine_Rate::getCompany($conn);

$productObj = new productSetup_Product($conn); 
$partnerArr = $productObj->getAllPartners();

$menuname = $_GET['menuname'];
$menu = rateStructure_RateStructure::getMenu($menuname, 'Upload Insurer Rate');

$html = " 
    $menu  
    <div class=\"container productSetup product ratingEngine\" id=\"productSetup\">
    <form class=\"form-horizontal\"  id=\"rating_engine\" name=\"rating_engine\" >
<fieldset>

<!-- Form Name -->
<legend>View Insurer Rate</legend>

<!-- Select Affgrp -->
<div class=\"form-group\">
  <label class=\" col-md-2 control-label\" for=\"affgrp\">Partner</label> 
  <div class=\"col-md-2\">
    <select id=\"affgrp\" name=\"affgrp\" class=\"form-control\" >
      <option value=\"\">Select</option>
      "; 

foreach($partnerArr as $arr){
    $selected = '';
    if($arr['affgrp'] == $master_rate[0]['affgrp']){ $selected='selected';}
    $html .= "<option value=\"{$arr['affgrp']}\" {$selected}>{$arr['generic_partner_name']}</option>";
}
$html .= " 
    </select>
  </div>
</div>

<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"insurance_company\">Product</label>
  <div class=\"col-md-2\" id='partner_product'>
    <select id=\"products\" name=\"products\" class=\"form-control\">
      <option value=\"\">{$master_rate[0]['products']}</option>
    </select>
  </div>
</div>
";

$html .= ratingEngine_Rate::insurance_company_dropdown($conn, $master_rate[0]['insurance_company']);

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
 ";

$fd = $master_rate[0]['rate_matrix_identifier'] == 'fd' ? 'checked' : '';
$xol = $master_rate[0]['rate_matrix_identifier'] == 'xol' ? 'checked' : '';
$std = $master_rate[0]['rate_matrix_identifier'] == 'std' ? 'checked' : '';

$html .= "
<!-- Multiple Radios (inline) -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"rate_matrix_identifier\">Rate Matrix Identifier</label>
  <div class=\"col-md-3\"> 
    <label class=\"radio-inline\" for=\"rate_matrix_identifier-0\">
      <input type=\"radio\" name=\"rate_matrix_identifier\" id=\"rate_matrix_identifier-0\" value=\"fd\" {$fd}>
      First Dollar
    </label> 
    <label class=\"radio-inline\" for=\"rate_matrix_identifier-1\">
      <input type=\"radio\" name=\"rate_matrix_identifier\" id=\"rate_matrix_identifier-1\" value=\"xol\" {$xol}>
      XOL
    </label>
    <label class=\"radio-inline\" for=\"rate_matrix_identifier-2\">
      <input type=\"radio\" name=\"rate_matrix_identifier\" id=\"rate_matrix_identifier-2\" value=\"std\" {$std}>
      Standard
    </label>      
  </div>   
</div>
";
      
$html .= " 
<!-- Multiple Checkboxes 
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"company\">Company</label>
  <div class=\"col-md-4\">
  <div class=\"checkbox\">
    <label for=\"company-0\">
      <input type=\"checkbox\" name=\"company_list\" id=\"company-0\" value=\"dzc\" >
      Dent Zone Companies Inc.(DZC)
    </label>
</div>
  <div class=\"checkbox\">
    <label for=\"company-1\">
      <input type=\"checkbox\" name=\"company_list\" id=\"company-1\" value=\"dzaf\">
      Dent Zone Florida (DZAF)
    </label>
	</div>
  <div class=\"checkbox\">
    <label for=\"company-2\">
      <input type=\"checkbox\" name=\"company_list\" id=\"company-2\" value=\"abg\">
      Autobodyguard (ABG)
    </label>
	</div>
  </div>
  <input type=\"hidden\" name=\"company\" id=\"company\"  value=\"\">
</div>
-->
";

$html .="<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"company\">Company</label>
  <div class=\"col-md-4\">";

$x = 0;
foreach($companyArr as $k=>$v){

    $company = strtolower($k);
    $selected_company = $master_rate[0]['company'] == $company ? 'checked' : '';
    $html .= "<div class=\"checkbox\">
        <label for=\"company-$x\">
          <input type=\"checkbox\" name=\"company_list\" id=\"company-$x\" value=\"$company\" {$selected_company}>
          $v ($k)
        </label>
    </div>";
    
    $x++;
}

$html .= " <input type=\"hidden\" name=\"company\" id=\"company\"  value=\"\">
        </div>
</div>";

$vt_lists = array('auto'=>'Auto', 'rv'=> 'RV', 'powersports'=> 'Power Sports', 'vsc' => 'Vehicle Service Contracts');

$html .= "
<!-- Multiple Checkboxes -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"rvrate\">Product Type</label>
  <div class=\"col-md-2\">
  <select name='vehicle_type' id='vehicle_type' required class='form-control'>
        <option >Select   </option>
";
        foreach($vt_lists as $key=>$val){
            $type_selected = $master_rate[0]['vehicle_type'] == $key ? 'selected' : '';
            $html.= "<option value='$key' $type_selected>{$val}</option>";
        }
$html .= "
    </select> 
    
  <!-- <div class=\"checkbox\">
    <label for=\"rv_rate\"> 
      <input type=\"checkbox\" name=\"rv_rate\" id=\"rv_rate\" value=\"1\">
      RV Rate
    </label>
   </div> -->
  
  </div>
  <input type=\"hidden\" name=\"rvrate\" id=\"rvrate\"  value=\"\">
</div>
 
<!-- Multiple Radios (inline) -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"reinsurance\">Reinsured</label>
  <div class=\"col-md-2\">  
  
<!--
    <label class=\"radio-inline\" for=\"reinsurance-0\">
      <input type=\"radio\" name=\"reinsurance\" id=\"reinsurance-0\" value=\"1\" checked=\"checked\">
      Yes
    </label> 
    <label class=\"radio-inline\" for=\"reinsurance-1\">
      <input type=\"radio\" name=\"reinsurance\" id=\"reinsurance-1\" value=\"0\">
      No
    </label>
    -->
 ";

$reinsured_yes = $master_rate[0]['reinsurance']== 1 ? 'checked' : '';
$reinsured_no = $master_rate[0]['reinsurance']== 0 ? 'checked' : '';
$html .= "
 <div class=\"checkbox\">
    <label for=\"reinsured-1\">
      <input type=\"checkbox\" name=\"reinsured\" id=\"reinsured-1\" value=\"1\" {$reinsured_yes}>
      Yes
    </label>
</div>

  <div class=\"checkbox\">
    <label for=\"reinsured-2\">
      <input type=\"checkbox\" name=\"reinsured\" id=\"reinsured-2\" value=\"0\" {$reinsured_no}>
      No
    </label>
  </div>  
  
<input type=\"hidden\" name=\"reinsurance\" id=\"reinsurance\"  value=\"\">
</div>
</div>
";
      $dealer_group = $master_rate[0]['dealer_group'] == 'sewell' ? 'selected' : '';
      $html .= "
<!-- Select Basic -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"dealer_group\">Dealer Group</label>
  <div class=\"col-md-2\">
    <select id=\"dealer_group\" name=\"dealer_group\" class=\"form-control\">
      <option value=\"\">Select</option>
      <option value=\"sewell\" {$dealer_group}>Sewell</option>
    </select>
  </div>
</div>

<!-- States -->
<div class=\"form-group state_restriction\"> 
  <label class=\"col-md-2 control-label\" for=\"state\">States</label>
  <div class=\"col-md-9 states\">
  
  <div class=\"states_list\">
  ";

$master_rate_states = dzProduct_DBOperations::select('master_rate_states', array('master_rate_id'=>$master_rate_id), $conn);

if(!empty($master_rate_states)){
    $states = array();
    foreach($master_rate_states as  $k=>$data){
        $states[] = $data['state'];
    }
    //echo '<pre>'; print_r($states); echo '</pre>';
    foreach($state_list as $k=>$v){
        if(in_array($k, $states)){$checked_state = 'checked';}else{$checked_state = ''; }

        if($checked_state){
            $html .= "
                <label class=\"checkbox-inline\" for=\"state-$k\">
                  <input type=\"checkbox\" name=\"states_list\" id=\"state-$k\" value=\"$k\" {$checked_state} disabled>
                  $v
                </label>
                "; 
        }
    }
}

$html .= " <input type=\"hidden\" name=\"states\" id=\"states\"  value=\"\">
        <hr />
    </div>
  </div>
</div>";

$temp_id = time();

$html .= "
 <input type=\"hidden\" value=\"$temp_id\" name=\"temp_id\" id=\"temp_id\" />
<legend>Vehicle Class</legend>     
<!-- Vehicle Class-->
<div class=\"form-group state_restriction\"> 
  <label class=\"col-md-1 control-label\" for=\"state\"></label>
  <div class=\"col-md-9 make\">
  
  <div class=\"make_list\">
  ";

$master_rate_classes = dzProduct_DBOperations::select('master_rate_classes', array('master_rate_id'=>$master_rate_id), $conn);

if(!empty($master_rate_classes)){
    $classes = array();
    foreach($master_rate_classes as  $k=>$data){
        $classes[$data['class']][] = $data['make_code'];
    }

    ksort($classes);
    //echo '<pre>'; print_r($classes);echo '</pre>';
    $html .= "<div class='vehclass row'>
    <div class=\"span1\" style=\"margin-left: 5px; width: 100%;\">
    ";
                    foreach($classes as $class=>$dataArr){
                                $html.="<ul class=\"plan\" style=\"float: left; margin-right: 10px;\"><li class=\"plan-name\"> Class {$class}</li>";
                                foreach($dataArr as $k=>$data){
                                    $html.="<li>{$data}</li>";
                                }
                                $html.="</ul>";

                    }            
    $html .= "</div></div>
    ";
}
$html .= "
             
    </div>
  </div>
</div>
<!-- Class -->

<legend>Standard / Luxury</legend> 
<!-- Luxury Class-->
<div class=\"form-group state_restriction \"> 
  <label class=\"col-md-1 control-label\" for=\"state\"></label>
  <div class=\"col-md-9 make_sl\">
  
  <div class=\"make_sl_list\">
  ";

$master_rate_std_lux = dzProduct_DBOperations::select('master_rate_std_lux', array('master_rate_id'=>$master_rate_id), $conn);
if(!empty($master_rate_std_lux)){
    $std_lux = array();
    foreach($master_rate_std_lux as  $k=>$data){
        $std_lux[$data['type']][] = $data['make_code'];
    }

    $html .= "<div class='vehclass row'>
    <div class=\"span1\" style=\"margin-left: 5px; width: 100%;\">
    ";
                    foreach($std_lux as $type=>$dataArr){
                                $html.="<ul class=\"plan\" style=\"float: left; margin-right: 10px;\"><li class=\"plan-name\">{$type}</li>";
                                foreach($dataArr as $k=>$data){
                                    $html.="<li>{$data}</li>";
                                }
                                $html.="</ul>";

                    }            
    $html .= "</div></div>";
}    
$html .= "
    </div>
  </div>
</div>


<!-- RV Class -->

<legend>RV Class</legend> 
<!-- RV Class-->

<!-- RV Class --> 
<div class=\"form-group\">
  <label class=\"col-md-1 control-label\" for=\"rv_class\"></label>
  <div class=\" col-md-8 \">
   ";

$master_rate_rv = dzProduct_DBOperations::select('master_rate_rv', array('master_rate_id'=>$master_rate_id), $conn);
if(!empty($master_rate_rv)){
    $rv = array();
    foreach($master_rate_rv as  $k=>$data){
        $rv[$data['type']][] = $data['ft_from'].' ft - '.$data['ft_to'].' ft';
    }

    $html .= "<div class='vehclass row'>
    <div class=\"span1\" style=\"margin-left: 5px; width: 100%;\">
    ";
                    foreach($rv as $type=>$dataArr){
                                $html.="<ul class=\"plan\" style=\"float: left; margin-right: 10px;\"><li class=\"plan-name\">Class {$type}</li>";
                                foreach($dataArr as $k=>$data){
                                    $html.="<li>{$data}</li>";
                                }
                                $html.="</ul>";

                    }            
    $html .= "</div></div>";
}

$html .="
  </div>
</div>

<!-- -------------------------- -->

<legend>Surcharges</legend> 

<div class=\"form-group\">
  <label class=\"col-md-1 control-label\" for=\"\"></label> 
  <div class=\" col-md-8 surcharges\">
  ";
        $surcharges = dzProduct_DBOperations::select('surcharge_types', array(), $conn);
        $surcharge=array();
        foreach($surcharges as $k=>$data){
            $surcharge[$data['id']]= $data['surcharge_type'];
        }
         
$master_rate_surcharges = dzProduct_DBOperations::select('master_rate_surcharges', array('master_rate_id'=>$master_rate_id), $conn);
//print_r($master_rate_surcharges);
if(!empty($master_rate_surcharges)){
    $html.="<table class='table-striped table'>"
            . "<tr><th>Type</th><th>Amount</th><th>GL</th><th>Partner</th><th>Product</th><th>Bundle</th></tr>";
        foreach($master_rate_surcharges as $key=>$data){ 
            if($data['bundle']){
                 $bundle = dzProduct_DBOperations::select('product_coverage_bundle', array('id'=>$data['bundle']), $conn);
                 $bundle_name = $bundle[0]['bundle_name'];
            }else{
                $bundle_name = '--';
            }
                   
            $surcharge_desc = $surcharge[$data['surcharge_type_id']];
            $html.= "<tr><td>{$surcharge_desc}</td><td>{$data['surcharge_amount']}</td><td>{$data['gl']}</td><td>{$data['partner']}</td><td>{$data['product']}</td><td>{$bundle_name}</td></tr>";
        }
            $html.= "</table>";
}
$html .= " 
 
</div>
</div>


<legend>Deductibles</legend> 

<div class=\"form-group\">
  <label class=\"col-md-1 control-label\" for=\"\"></label> 
  <div class=\" col-md-8 deductibles\">
  ";

         
$master_rate_deductibles = dzProduct_DBOperations::select('master_rate_deductibles', array('master_rate_id'=>$master_rate_id), $conn);
//print_r($master_rate_deductibles);
if(!empty($master_rate_deductibles)){
    $html.="<table class='table-striped table'>"
            . "<tr><th>Disappearing Deductible</th><th>Deductible Amount</th><th>Surcharge Amount</th><th>GL</th><th>Partner</th><th>Product</th><th>Bundle</th></tr>";
    
        foreach($master_rate_deductibles as $key=>$data){   
            if($data['bundle']){
                 $bundle = dzProduct_DBOperations::select('product_coverage_bundle', array('id'=>$data['bundle']), $conn);
                 $bundle_name = $bundle[0]['bundle_name'];
            }else{
                $bundle_name = '--';
            }
            $desc = $data['is_disappearing'] == 0 ? 'No' : 'Yes';
            $html.= "<tr><td>{$desc}</td><td>{$data['deductible_amount']}</td><td>{$data['surcharge_amount']}</td><td>{$data['gl']}</td><td>{$data['partner']}</td><td>{$data['product']}</td><td>{$bundle_name}</td></tr>";
        }
            $html.= "</table>";
}
$html .= " 
 
</div>
</div>
    
    <!-- Effective Date --> 
    <legend></legend>
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"effective_date\">Effective Date</label> 
  <div class=\" col-md-2 \">
  
    <input id=\"effective_date\" name=\"effective_date\" type=\"text\" class=\"form-control input-md\" value=\"{$master_rate[0]['effective_date']}\" >
  
  </div>
</div>


<!-- Expiration Date --> 
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"expiration_date\">Expiration Date</label>
  <div class=\" col-md-2 \">
  <input id=\"expiration_date\" name=\"expiration_date\" type=\"text\" class=\"form-control input-md\" value=\"{$master_rate[0]['expiration_date']}\" >
  </div>
</div>

</fieldset>
</form>
      </div>
    
    ";

$insert = $html;


include (TEMPLATE);

?>