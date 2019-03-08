<style>
    
    .tooltipclass {background: #000; color: #fff; opacity: 9;}
    
</style>
<?php 

error_reporting(0);


require("../../config/pdr.conf.php");
Util::validate('admin,root,acct,dentguard,ao');

$rateObj = new ratingEngine_Rate($conn);

$masterRateArr = $rateObj->searchMasterRate($_POST);

$productObj = new productSetup_Product($conn); 
$partnerArr = $productObj->getAllPartners();
$partner = array();
foreach($partnerArr as $arr){
    $partner[$arr['affgrp']] = $arr['generic_partner_name'];
}

//echo '<pre>'; print_r($masterRateArr); echo '</pre>';

if(is_array($masterRateArr)){
    
    $html ="
        <div class=\"panel panel-success\"> 
  
        <div class=\"panel-heading\">Search Result</div>
        <div class=\"panel-body\">
    ";
    $html .= "<table class=\" table table-hover \">
            <thead>
              <th>Insurance <br />Company</th>
              <th>Rate Matrix <br />Identifier</th>
              <th>Company </th>
              <th>Reinsured </th>
              <th>Dealer Group</th>
              <th>Partner</th>
              <th>Product</th>
              <th>Product <br />type</th>
              <th>Effective <br />Date</th>
              <th>Expiration <br />Date</th>
              <th style=\"width: 120px;\">&nbsp;</th>
            </thead>";

    foreach($masterRateArr as $dataArr=>$data){
        
        $states = str_replace(',', ', ', $rateObj->getStates($data['id']));
        $classes = $rateObj->getVehicleClasses($data['id']);
        $id = $data['id'];
        //echo '<pre>'; print_r($classes); echo '</pre>';
        
        $style = " 
               
                position: absolute;
                border: 2px solid rgb(242, 242, 242);
                padding: 10px;
                border-radius: 5px;
                box-shadow: grey 10px 10px 5px;
                margin-left: -360px;
                margin-top: -40px;
                width: 350px;
                overflow: hidden;
                background: none 0% 0% repeat scroll rgb(255, 255, 255);
            ";
        
        $checked1 = $data['active'] == 1 ? "checked=\"checked\"" : '';
        $checked2 = $data['active'] == 0 ? "checked=\"checked\"" : ''; 
        
       $save_form = " <div class=\"ms_{$id}\" style=\"display: none; $style\" ><form class=\"form-horizontal\"   id=\"form_master_rate_{$id}\"  name=\"form_master_rate_{$id}\" >
        <fieldset>
        <input type=\"hidden\" name=\"master_rate_id\" id=\"master_rate_id\" value=\"$id\" >
        <!-- Form Name -->
        <legend>Update Master Rate ({$id})</legend>

        <!-- Multiple Radios (inline) -->
        
        <div class=\"form-group\">
          <label class=\"col-md-4 control-label\" for=\"active\">Active</label>
          <div class=\"col-md-7\"> 
            <label class=\"radio-inline\" for=\"active-0\">
              <input type=\"radio\" name=\"active\" id=\"active-0\" value=\"1\" $checked1>
              Yes
            </label> 
            <label class=\"radio-inline\" for=\"active-1\">
              <input type=\"radio\" name=\"active\" id=\"active-1\" value=\"0\" $checked2>
              No
            </label>
          </div>
        </div>

        <!-- Text input-->
        <div class=\"form-group\">
          <label class=\"col-md-4 control-label\" for=\"effective_date\">Effective Date</label>  
          <div class=\"col-md-7\">
          <input id=\"effective_date1\" name=\"effective_date1\" value=\"{$data['effective_date']}\" type=\"text\" placeholder=\"\" class=\"form-control input-md\">

          </div>
        </div>

        <!-- Text input-->
        <div class=\"form-group\">
          <label class=\"col-md-4 control-label\" for=\"expiration_date\">Expiration Date</label>  
          <div class=\"col-md-7\">
          <input id=\"expiration_date\" name=\"expiration_date\"  value=\"{$data['expiration_date']}\" type=\"text\" placeholder=\"\" class=\"form-control input-md\">

          </div>
        </div>

        <!-- Button -->
        <div class=\"form-group\">
          <label class=\"col-md-4 control-label\" for=\"save_master_rate\"></label>
          <div class=\"col-md-7\">
            <button id=\"save_master_rate_{$id}\" name=\"save_master_rate_{$id}\" class=\"btn btn-inverse\">Save
            <div id='loader2' style=\"display: none; float: left; margin-right: 5px;\"><img src='../../images/icn_loading.gif' />  </div>         
                  
            </button>
            <button id=\"close_master_rate_{$id}\" name=\"close_master_rate_{$id}\" class=\"btn btn-inverse\">Close</button>

            </div>
        </div>
        
        <div class=\"form-group\">
          <label class=\"col-md-4 control-label\" for=\"save_master_rate\"></label>
          <div class=\"col-md-7\">
                <div id=\"viewSaveMsg{$id}\"></div>
            </div>
        </div>
        
       

        </fieldset>
        </form> 
        
        <script>
        
        $( \"#effective_date1\" ).datepicker({
              dateFormat: \"yy-mm-dd\"
        });

        $( \"#expiration_date\" ).datepicker({
              dateFormat: \"yy-mm-dd\" 
        });
        
           $(\"#close_master_rate_{$id}\").click(function(){
                $(\".ms_{$id}\").hide(500);
                return false; 
           });
        
         $(\"#save_master_rate_{$id}\").click(function(){
             
                saveRate_{$id}();
                //$(\".ms_{$id}\").hide(500);
                return false; 
           });
            function saveRate_{$id}(){
                
            $('#loader2').show();
                
            $.post('ratingEngine_update_masterRate.php',  
             {
                master_rate_id: form_master_rate_{$id}.master_rate_id.value,
                active: form_master_rate_{$id}.active.value,
                effective_date: form_master_rate_{$id}.effective_date1.value,
                expiration_date: form_master_rate_{$id}.expiration_date.value
 		},                         
 		function(output){
 			
 			$('#viewSaveMsg{$id}').html(output).fadeIn();
                        $('td#efdate_{$id}').text($( \"#effective_date1\" ).val());
                        $('td#exdate_{$id}').text($( \"#expiration_date\" ).val());      
                        $('#loader2').hide();     
 			 
 		});      
                
            }
            
        </script>
        </div>";

        $yesno = $data['reinsurance'] > 0 ? 'Yes' : 'No';
        $affgrp = $partner[$data['affgrp']];
        $html .="
                <tr id=\"id_{$data['id']}\">
                  <td>{$data['insurance_company']}</td>
                  <td>{$data['rate_matrix_identifier']}</td>
                  <td>{$data['company']}</td>
                  <td>{$yesno}</td>
                  <td>{$data['dealer_group']}</td>
                   <td>{$affgrp}</td>
                  <td>{$data['products']}</td>";
                  
//                  foreach($classes as $k=>$v){
//                      
//                      $vehclass = str_replace(',',', ',$v);
//                      $html .= $k." - $vehclass <br />";
//                  }
//                  
//                  if($data['vehicle_type'] == 'vsc'){
//                      $link = "<a href=\"../rateStructure/view_ratecard_vsc_details_v2.php?master_rate_id={$data['id']}\" target=\"_blank\" title=\"View VSC Ratecard\" class=\"btn btn-primary\" style=\"font-size: 11px; padding: 1px 3px 5px 5px;\"><span class=\"glyphicon glyphicon-new-window\"></span> Ratecard</a>"; 
//                      $link .= "<a href=\"ratingEngine_vehicle_classing_vsc.php?master_rate_id={$data['id']}\" target=\"_blank\" title=\"View Vehicle Classing\" class=\"btn btn-primary\" style=\"font-size: 11px; padding: 1px 3px 5px 5px;\"><span class=\"glyphicon glyphicon-new-window\"></span> Vehicle Classing</a>"; 
//                  }else{
//                      $link = "<a href=\"../rateStructure/view_ratecard_details.php?master_rate_id={$data['id']}\" target=\"_blank\" title=\"View Ratecard\" class=\"btn btn-primary\" style=\"font-size: 11px; padding: 1px 3px 5px 5px;\"><span class=\"glyphicon glyphicon-new-window\"></span> Ratecard</a>"; 
//                  }
                  
                  if($data['vehicle_type'] == 'vsc'){
                      $link = "<li><a href=\"../rateStructure/view_ratecard_vsc_details_v2.php?master_rate_id={$data['id']}&menuname=createratestructure\" target=\"_blank\" title=\"View VSC Ratecard\"  ><span class=\"glyphicon glyphicon-usd\"></span> Ratecard</a></li>"; 
                      $link .= "<li><a href=\"ratingEngine_vehicle_classing_vsc.php?master_rate_id={$data['id']}\" target=\"_blank\" title=\"View Vehicle Classing\"  ><span class=\"glyphicon glyphicon-align-justify\"></span> Vehicle Classing</a></li>"; 
                  }else{
                      $link = "<li><a href=\"../rateStructure/view_ratecard_details.php?master_rate_id={$data['id']}\" target=\"_blank\"  ><span class=\"glyphicon glyphicon-new-window\"></span> Ratecard</a></li>"; 
                  }
                  
                  
         $html .="
                  <td>{$data['vehicle_type']}</td>
                  <td id=\"efdate_{$id}\">{$data['effective_date']}</td>
                  <td id=\"exdate_{$id}\">{$data['expiration_date']}</td>
                  <td>
                  <div class='col'>
                    <div class=\"dropdown\">
                        <button class=\"btn btn-primary dropdown-toggle\" type=\"button\" data-toggle=\"dropdown\" style=\"margin-top: -5px !important; margin-left: 5px !important;\">Select Action
                        <span class=\"caret\"></span></button>
                        <ul class=\"dropdown-menu\" style=\"margin-top: 0px !important; margin-left: 0px !important;\">
                         <li><a href=\"ratingEngine_view_insurer_rate.php?master_rate_id={$data['id']}&menuname=searchratestructure\" target=\"_blank\"><span class=\"glyphicon glyphicon-eye-open\"></span> View Setup</a> 
                         <li><a href=\"ratingEngine_bundles_contract_options.php?master_rate_id={$data['id']}&affgrp={$data['affgrp']}&rvrate={$data['rvrate']}&type={$data['vehicle_type']}\" target=\"_blank\"><span class=\"glyphicon glyphicon-upload\"></span> Apply Rates</a></li>
                          <li><a href=\"#\" id=\"btn_{$id}\"><span class=\"glyphicon glyphicon-edit\"></span> Update</a></li>
                          $link
                        </ul>
                      </div>
                   </div>   
                   <!-- <a href=\"ratingEngine_bundles_contract_options.php?master_rate_id={$data['id']}&affgrp={$data['affgrp']}&rvrate={$data['rvrate']}&type={$data['vehicle_type']}\" target=\"_blank\" title=\"Apply Rate\" class=\"btn btn-primary\" style=\"font-size: 11px; padding: 5px;\"><span class=\"glyphicon glyphicon-new-window\"></span></a>
                      <button class=\"btn btn-primary\" id=\"btn_{$id}\" title=\"Update Rate\" style=\" padding: 1px 3px 5px 5px; font-size: 11px;\"><span class=\"glyphicon glyphicon-edit\"></span> Update</button>
                       $link     -->
                      $save_form 
                      <script>
                                $(\"#btn_{$id}\").click(function(){
                                    $(\".ms_{$id}\").show(500);
                                    return false; 
                              });
                     
                       $( function() {
                        $( document ).tooltip({effect: \"blind\", duration: 1000, tooltipClass: 'tooltipclass'});
                      } );
                     </script>
                        
                    </td>
                </tr>";
    }
    $html .="
          </table>
          </div></div>
    ";
    
    
}else{
    
    $html = "
        <div class=\"alert alert-danger\">
            <strong>Message: </strong> No available master rates.
          </div>
            ";
    
}

echo $html;


?>
