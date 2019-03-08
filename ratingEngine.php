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
        $('#affgrp').change(function () {
            update_partner();
        });    
    })
    
    function update_partner(){
    
    $.post('ratingEngine_partner_products.php', 
        {affgrp: rating_engine.affgrp.value}, 
            function(output){                                        
	        $('#partner_product').html(output).show();                		    
                //
                //console.log(rating_engine.affgrp.value);
                //console.log($("select#affgrp option:selected"). text());
                
                $('td#surcharges_partner select').html('');
                $('td#surcharges_partner select').append("<option value=''>Select</option>");
                $('td#surcharges_partner select').append("<option value='"+rating_engine.affgrp.value+"'>"+$("select#affgrp option:selected"). text()+"</option>");
            
                $('td#surcharges_products').html(output).show();  
                
                $('td#surcharges_products select').attr('name', 'surcharges[product][]');
                $('td#surcharges_products select').attr('style', 'width: 150px; margin-right: 3px;');
                $("td#surcharges_products select option:first").attr('selected','selected');
                $('td#surcharges_products select').attr('disabled', 'disabled');
                
                $('td#deductible_partner select').html('');
                $('td#deductible_partner select').append("<option value=''>Select</option>");
                $('td#deductible_partner select').append("<option value='"+rating_engine.affgrp.value+"'>"+$("select#affgrp option:selected"). text()+"</option>");
                
                $('td#deductible_products').html(output).show();                		    
                $('td#deductible_products select').attr('name', 'deductible[product][]');
                $('td#deductible_products select').attr('style', 'width: 150px; margin-right: 3px;');
                $("td#deductible_products select option:first").attr('selected','selected');
                $('td#deductible_products select').attr('disabled', 'disabled');
        }); 
    }
    
    function getProductBundle(obj){
        
        var prefix = obj.value;
        console.log(prefix);
        if(prefix !== ''){
            $.post('ratingEngine_product_bundle.php', 
            {
                product: prefix
            },function(output){
                $(obj).closest('tr').find('td:nth-last-child(2) ').html(output).show();
                
                var name = $(obj).closest('tr').find('td:nth-last-child(1) input').val();
                 $(obj).closest('tr').find('td:nth-last-child(2) select').attr('name', name);
            })
        }else{
            $(obj).closest('tr').find('td:nth-last-child(2) select').prop('disabled', true);
        }
        
    }
    
</script>

<?php
// ini_set('display_errors', 1);
  
require("../../config/pdr.conf.php");
Util::validate('admin,root,acct,dentguard,ao'); 
    
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
    <form class=\"form-horizontal\"  id=\"rating_engine\" name=\"rating_engine\" method=\"post\" action=\"ratingEngine_insertMasterRate.php\"  enctype= \"multipart/form-data\">
<fieldset>

<!-- Form Name -->
<legend>Upload Insurer Rate</legend>

<!-- Select Affgrp -->
<div class=\"form-group\">
  <label class=\" col-md-2 control-label\" for=\"affgrp\">Partner</label> 
  <div class=\"col-md-2\">
    <select id=\"affgrp\" name=\"affgrp\" class=\"form-control\" >
      <option value=\"\">Select</option>
      "; 

foreach($partnerArr as $arr){
    $html .= "<option value=\"{$arr['affgrp']}\">{$arr['generic_partner_name']}</option>";
}
$html .= " 
    </select>
  </div>
</div>
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"insurance_company\">Product</label>
  <div class=\"col-md-2\" id='partner_product'>
    <select id=\"products\" name=\"products\" class=\"form-control\">
      <option value=\"\">Select</option>
    </select>
  </div>
</div>
";

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
 
<!-- Multiple Radios (inline) -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"rate_matrix_identifier\">Rate Matrix Identifier</label>
  <div class=\"col-md-3\"> 
    <label class=\"radio-inline\" for=\"rate_matrix_identifier-0\">
      <input type=\"radio\" name=\"rate_matrix_identifier\" id=\"rate_matrix_identifier-0\" value=\"fd\" checked=\"checked\">
      First Dollar
    </label> 
    <label class=\"radio-inline\" for=\"rate_matrix_identifier-1\">
      <input type=\"radio\" name=\"rate_matrix_identifier\" id=\"rate_matrix_identifier-1\" value=\"xol\">
      XOL
    </label>
    <label class=\"radio-inline\" for=\"rate_matrix_identifier-2\">
      <input type=\"radio\" name=\"rate_matrix_identifier\" id=\"rate_matrix_identifier-2\" value=\"std\">
      Standard
    </label>      
  </div>   
</div>
 
<!-- Multiple Checkboxes 
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"company\">Company</label>
  <div class=\"col-md-4\">
  <div class=\"checkbox\">
    <label for=\"company-0\">
      <input type=\"checkbox\" name=\"company_list\" id=\"company-0\" value=\"dzc\">
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
    $html .= "<div class=\"checkbox\">
        <label for=\"company-$x\">
          <input type=\"checkbox\" name=\"company_list\" id=\"company-$x\" value=\"$company\">
          $v ($k)
        </label>
    </div>";
    
    $x++;
}

$html .= " <input type=\"hidden\" name=\"company\" id=\"company\"  value=\"\">
        </div>
</div>";

$html .= "
<!-- Multiple Checkboxes -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"rvrate\">Product Type</label>
  <div class=\"col-md-2\">
  <select name='vehicle_type' id='vehicle_type' required class='form-control'>
        <option >Select   </option>
        <option value='auto'>Auto</option>
        <option value='rv'>RV</option>
        <option value='powersports'>Power Sports</option>
        <option value='vsc'>Vehicle Service Contracts</option>
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
     
 <div class=\"checkbox\">
    <label for=\"reinsured-1\">
      <input type=\"checkbox\" name=\"reinsured\" id=\"reinsured-1\" value=\"1\">
      Yes
    </label>
</div>

  <div class=\"checkbox\">
    <label for=\"reinsured-2\">
      <input type=\"checkbox\" name=\"reinsured\" id=\"reinsured-2\" value=\"0\">
      No
    </label>
  </div>  
  
<input type=\"hidden\" name=\"reinsurance\" id=\"reinsurance\"  value=\"\">
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

<!-- States -->
<div class=\"form-group state_restriction\"> 
  <label class=\"col-md-2 control-label\" for=\"state\">States</label>
  <div class=\"col-md-9 states\">
  
  <div class=\"states_list\">
  ";


 $html .= "
        <label class=\"checkbox-inline\" for=\"select-all\">
          <input type=\"checkbox\" name=\"select-all\" id=\"select-all\" >
          Select All 
        </label>
        "; 

foreach($state_list as $k=>$v){
    $html .= "
        <label class=\"checkbox-inline\" for=\"state-$k\">
          <input type=\"checkbox\" name=\"states_list\" id=\"state-$k\" value=\"$k\">
          $v
        </label>
        "; 
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
  <label class=\"col-md-2 control-label\" for=\"state\">Make</label>
  <div class=\"col-md-9 make\">
  
  <div class=\"make_list\">
  ";


 $html .= "
        <label class=\"checkbox-inline\" for=\"select-all-make\">
          <input type=\"checkbox\" name=\"select-all-make\" id=\"select-all-make\" >
          Select All 
        </label>
        "; 

foreach($makeArr as $k=>$v){
    $html .= "
        <label class=\"checkbox-inline label-make-$k\" for=\"make-$k\">
          <input type=\"checkbox\" name=\"make\" id=\"make-$k\" value=\"$k\">
          $v <span class=\"class-$k\"></span>
        </label>
        "; 
} 

$html .= "
               <hr />
    </div>
  </div>
</div>
<!-- Class -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"vehclass\"></label>
  <div class=\"col-md-5\">
  
    <select id=\"vehclass\" name=\"vehclass\" class=\"form-control\" 
              style=\"width: 160px;float: left; margin-right: 3px;\">
      <option value=\"\">Select Class</option>
      <option value=\"1\">Class 1</option>
      <option value=\"2\">Class 2</option>
      <option value=\"3\">Class 3</option>
      <option value=\"4\">Class 4</option>
      <option value=\"5\">Class 5</option>
      <option value=\"6\">Class 6</option>
      <option value=\"7\">Class 7</option>        
    </select>
    <button id=\"apply\" name=\"apply\" class=\"btn btn-primary\">Apply
     <div id='loader' style=\"display: none; \"><img src='../../images/icn_loading.gif' /> Loading...  </div>         
     </button>
     <button id=\"clear\" name=\"clear\" class=\"btn btn-primary\">Clear
     <div id='loader1' style=\"display: none; float: left; margin-right: 5px;\"><img src='../../images/icn_loading.gif' />  </div>         
     </button>           
  </div>
</div>
              
    <!-- Vehicle Class --> 
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"vehicle_class\">Vehicle Class</label>
  <div class=\" col-md-8 \">
   <div id=\"vehicleClass\" class=\"vehclass\"></div>
  </div>
</div>
<legend>Standard / Luxury</legend> 
<!-- Luxury Class-->
<div class=\"form-group state_restriction \"> 
  <label class=\"col-md-2 control-label\" for=\"state\">Make</label>
  <div class=\"col-md-9 make_sl\">
  
  <div class=\"make_sl_list\">
  ";


 $html .= "
        <label class=\"checkbox-inline\" for=\"select-all-make-sl\">
          <input type=\"checkbox\" name=\"select-all-make-sl\" id=\"select-all-make-sl\" >
          Select All 
        </label>
        "; 

foreach($makeArr as $k=>$v){
    $html .= "
        <label class=\"checkbox-inline label-make_sl-$k\" for=\"make_sl-$k\">
          <input type=\"checkbox\" name=\"make_sl\" id=\"make_sl-$k\" value=\"$k\">
          $v <span class=\"class_sl-$k\"></span>
        </label>
        "; 
}


$html .= "
               <hr />
    </div>
  </div>
</div>

<!-- Class -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"slclass\"></label>
  <div class=\"col-md-5\">
    <select id=\"slclass\" name=\"slclass\" class=\"form-control\" 
              style=\"width: 160px;float: left; margin-right: 3px;\">
      <option value=\"\">Select Class</option>
      <option value=\"1\">Standard</option>
      <option value=\"2\">Luxury</option>
      
    </select>
    <button id=\"apply_sl\" name=\"apply_sl\" class=\"btn btn-primary\">Apply
     <div id='loader2b' style=\"display: none; \"><img src='../../images/icn_loading.gif' /> Loading...  </div>         
     </button>
     <button id=\"clear_sl\" name=\"clear\" class=\"btn btn-primary\">Clear
     <div id='loader2a' style=\"display: none; float: left; margin-right: 5px;\"><img src='../../images/icn_loading.gif' />  </div>         
     </button>           
  </div>
</div>

<!-- Standard/Luxury Class --> 
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"vehicle_class\">Standard/Luxury Class</label>
  <div class=\" col-md-8 \">
   <div id=\"slClass\" class=\"slclass\"></div>
  </div>
</div>

<!-- RV Class -->

<legend>RV Class</legend> 
<!-- RV Class-->

<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"option_num\">Select RV Total Class</label>
  <div class=\" col-md-6 \">
   <select id=\"rv_total_class\" name=\"rvtotalclass\" class=\"form-control\" 
              style=\"width: 125px;float: left; margin-right: 3px;\">
      <option value=\"\" selected>Select RV Total Class</option>
      ";
      for($x=1; $x<=25; $x++){
          
          if($x==7){$selected = " selected=\"selected\" ";}else{ $selected = '';}
         
          $html .= "<option value=\"$x\" $selected>$x</option>";
      }
      
$html .= "
    </select>
  </div>
</div>

<!-- RV Class -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"rvclass\"></label>
  <div class=\"col-md-6\">
  <input type=\"text\" style=\"width:100px; float: left;\" class=\"form-control\" id=\"ft_from\" name=\"ft_from\" value=\"\" placeholder=\"Feet From\" /> 
  <div style=\" display: block; width:20px; float: left;\">&nbsp;to &nbsp;</div> <input type=\"text\" style=\"width:100px; float: left;\" class=\"form-control\" id=\"ft_to\" name=\"ft_to\" value=\"\" placeholder=\"Feet To\" />
<br style=\"clear: both;\" /><br />
    <div id=\"rvClass_select\" style=\"float: left;\" >
    <select id=\"rvclass\" name=\"rvclass\" class=\"form-control\" 
              style=\"width: 225px;float: left; margin-right: 3px;\">
      <option value=\"\">Select RV Class</option>
      <option value=\"1\">Class 1 </option>
      <option value=\"2\">Class 2 </option>
      <option value=\"3\">Class 3 </option>
      <option value=\"4\">Class 4 </option>
      <option value=\"5\">Class 5 </option>
      <option value=\"6\">Class 6 </option>
      <option value=\"7\">Class 7 </option>
    </select>
    </div>
    <button id=\"apply_rv\" name=\"apply_rv\" class=\"btn btn-primary\">Apply
     <div id='loader2d' style=\"display: none; \"><img src='../../images/icn_loading.gif' /> Loading...  </div>         
     </button>
     <button id=\"clear_rv\" name=\"clear\" class=\"btn btn-primary\">Clear
     <div id='loader2c' style=\"display: none; float: left; margin-right: 5px;\"><img src='../../images/icn_loading.gif' />  </div>         
     </button>           
  </div>
</div>

<!-- RV Class --> 
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"rv_class\">RV Class</label>
  <div class=\" col-md-8 \">
   <div id=\"rvClass\" class=\"rvclass\"></div>
  </div>
</div>

<!-- -------------------------- -->

<legend>Surcharges</legend> 

<div class=\"form-group\">
  <label class=\"col-md-1 control-label\" for=\"\"></label> 
  <div class=\" col-md-9 surcharges\">
  <table class='surcharges_tbl mb-1' style='margin-bottom: 10px;'>
  <thead style='margin-bottom: 8px'><tr><th>Surcharge Type</th><th>Surcharge Amount</th><th>Surcharge GL</th>
        <th>Partner</th><th>Product</th><th>Bundle</th>
        <th></th>
        </tr></thead>
        <tbody>
        <tr id='surcharge_clone'><td>
         <select id=\"surcharges_type\" name=\"surcharges[type][]\" class=\"form-control\" 
              style=\"width: 160px;float: left; margin-right: 3px;\">
      <option value=\"\">Select Surcharges Type</option>
    ";
        $surcharges = dzProduct_DBOperations::select('surcharge_types', array(), $conn);
        
        foreach($surcharges as $k=>$data){
            $html .= "<option value=\"{$data['id']}\">{$data['surcharge_type']}</option>";
        }

$html .= "
    </select></td>
    <td>
    <input type='number' name='surcharges[amount][]' value='' class=\"form-control\" placeholder='Surcharge Amount'   style=\"width: 160px;float: left; margin-right: 3px;\">
    </td>
    <td>
    <input type='text' name='surcharges[gl][]' value='' class=\"form-control\" placeholder='GL Account'  maxlength=\"19\"    style=\"width: 180px;float: left; margin-right: 3px;\">
    </td>
    <td  id='surcharges_partner'><select class='form-control' name='surcharges[partner][]'
        
        onchange='if(this.value != \"\"){ $(this).closest(\"tr\").find(\"td#surcharges_products select\").removeAttr(\"disabled\" );
                    $(this).closest(\"tr\").find(\"td#surcharges_products select\").attr(\"onchange\", \"getProductBundle(this)\" )
            }
            else{ 
                $(this).closest(\"tr\").find(\"td#surcharges_products select\").prop(\"disabled\", \"true\") 
            }'
            
        style='width: 150px; margin-right: 3px;'>
            <option value=''>Select</option>
        </select></td>
    <td id='surcharges_products'><select class='form-control' name='surcharges[product][]' 
          disabled style='width: 110px;  margin-right: 3px;'>
            <option value=''>Select Product</option>
        </select></td>
    <td id='surcharges_bundle'><select class='form-control' name='surcharges[bundle][]' disabled  style='width: 150px;  margin-right: 3px;'>
            <option value=''>Select</option>
        </select></td>
    <td><input type='hidden' id='hidden' value='surcharges[bundle][]' />
        <button type='button' class=\"btn btn-danger invisible\" style='font-size: 10px;' onclick='$(this).closest(\"tr\").remove();'>Remove</button>
     </td>
     </tr></tbody>
     </table>
     <button type='button' id=\"add_surcharge\" name=\"add_surcharge\" class=\"btn btn-primary\"  style='font-size: 10px;'
  onclick=\" $( 'table.surcharges_tbl tbody tr:first' ).clone().appendTo('table.surcharges_tbl');
             $( 'table.surcharges_tbl tr:last td input' ).val(''); $( 'table.surcharges_tbl tr:last td button' ).removeClass('invisible');
            $('table.surcharges_tbl tbody tr:last td input#hidden').val('surcharges[bundle][]')
    \"><span class='glyphicon glyphicon-plus'></span> Add </button>
</div>
</div>


<legend>Deductibles</legend> 

<div class=\"form-group\">
  <label class=\"col-md-1 control-label\" for=\"\"></label> 
  <div class=\" col-md-9 deductibles\">
  
  <table class='deductible_tbl mb-1' style='margin-bottom: 10px;'>
        <thead style='margin-bottom: 8px'><tr><th style='width:100px;'>Disappearing <br />Deductible</th><th>Deductible <br />Amount</th><th>Surcharge <br />Amount</th><th>Surcharge GL</th>
        <th>Partner</th><th>Product</th><th>Bundle</th>
        <th></th>
        </tr></thead>
        <tbody>
        <tr>
            <td>
               <select id=\"is_disappearing\" name=\"deductible[disappearing][]\" class=\"form-control\" 
              style=\"width: 100px;float: left; margin-right: 3px;\">
                    <option value=\"0\">No</option>
                    <option value=\"1\">Yes</option>
                </select>
               <!-- <input type='checkbox' name='deductible[disappearing][]' id='disappearing_deductible' onclick='$(\"input#is_disappearing\").closest().val(1)' />
                <input type='hidden' name='deductible[is_disappearing][]' id='is_disappearing' value='' /> -->
            
            </td>
            <td><input type='number' name='deductible[deductible_amount][]' value='' class=\"form-control\" placeholder=' Amount' style=\"width: 120px;float: left; margin-right: 3px;\"></td>
            <td><input type='number' name='deductible[surcharge_amount][]' value='' class=\"form-control\" placeholder='Amount'   style=\"width: 120px;float: left; margin-right: 3px;\"></td>
            <td><input type='text' name='deductible[gl][]' value='' class=\"form-control\" placeholder='GL Account' maxlength=\"19\"    style=\"width: 180px;float: left; margin-right: 3px;\"></td>
            <td id='deductible_partner'><select class='form-control' name='deductible[partner][]' 
                    onchange='if(this.value != \"\"){ 
                    $(this).closest(\"tr\").find(\"td#deductible_products select\").removeAttr(\"disabled\" ) 
                     $(this).closest(\"tr\").find(\"td#deductible_products select\").attr(\"onchange\", \"getProductBundle(this)\" )
                    }else{ $(this).closest(\"tr\").find(\"td#deductible_products select\").prop(\"disabled\", \"true\")}'
                    style='width: 150px;  margin-right: 3px;' >
                    <option value='' >Select Partner</option>
                </select></td>
            <td id='deductible_products'><select class='form-control' name='deductible[product][]' disabled style='width: 110px;  margin-right: 3px;' >
                    <option value=''>Select Product</option>
                </select></td>
            <td id='deductible_bundle'><select class='form-control' name='deductible[bundle][]' disabled style='width: 150px;  margin-right: 3px;'>
                    <option value=''>Select</option>
                </select></td>
            <td><input type='hidden' id='hidden'  value='deductible[bundle][]' />
            <button type='button' class=\"btn btn-danger invisible\" style='font-size: 10px;' onclick='$(this).closest(\"tr\").remove();'>Remove</button></td>
        </tr>
        </tbody>
    </table>
        <button type='button' id=\"add_deductibles\" name=\"add_deductible\" class=\"btn btn-primary\" style='font-size: 10px;'
        onclick=\" $( 'table.deductible_tbl tbody tr:first' ).clone().appendTo('table.deductible_tbl');
                   
                   $( 'table.deductible_tbl tbody tr:last td input' ).val(''); $( 'table.deductible_tbl tbody tr:last td button' ).removeClass('invisible');
                   $('table.deductible_tbl tbody tr:last td input#hidden').val('deductible[bundle][]');
      \"><span class='glyphicon glyphicon-plus'></span> Add </button>
  </div>
</div>

    
    <!-- Effective Date --> 
    <legend></legend>
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"effective_date\">Effective Date</label> 
  <div class=\" col-md-2 \">
  
    <input id=\"effective_date\" name=\"effective_date\" type=\"text\" class=\"form-control input-md\" >
  
  </div>
</div>


<!-- Expiration Date --> 
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"expiration_date\">Expiration Date</label>
  <div class=\" col-md-2 \">
  <input id=\"expiration_date\" name=\"expiration_date\" type=\"text\" value=\"2099-12-31\" class=\"form-control input-md\" >
  </div>
</div>

<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"upload_rateform\">Browse Rate File:</label>
  <div class=\" col-md-3 \">
  <input id=\"upload_rateform\" name=\"upload_rateform\" type=\"file\" class=\"form-control input-md\" style=\"font-size: 11px; padding: 0px;\">
  </div>
</div> 


<!-- Button -->
<div class=\"form-group\">
  <label class=\"col-md-2 control-label\" for=\"submit\"></label>
  <div class=\"col-md-8\">
    <button id=\"submit\" name=\"submit\" class=\"btn btn-primary\">Submit
              <div id='loader2' style=\"display: none; float: left; margin-right: 5px;\"><img src='../../images/icn_loading.gif' />  </div>         
              
              </button>
              
     <div id=\"success\"></div>
     <div id=\"rateform\"></div>
  </div>
</div>

</fieldset>
</form>
      </div>
    
    ";

$insert = $html;


include (TEMPLATE);

?>