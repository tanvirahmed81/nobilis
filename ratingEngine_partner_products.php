<?php

require("../../config/pdr.conf.php");
Util::validate('admin,root,acct,dentguard,ao');

$affgrp = $_POST['affgrp'];
$rateObj = new ratingEngine_Rate($conn);
$productList = $rateObj->getProductPerPartner($affgrp);

$html = "   <select id=\"products\" name=\"products\" class=\"form-control\">
      <option value=\"\">Select</option>";

if(is_array($productList) AND count($productList))
{
foreach($productList as $product){
    $html .= "<option value=\"$product\">$product</option>";
}
} 

$html .= "</select>";

echo $html;
?>

<script>
$(document).ready(function() 
{   

    $('div#partner_product select#products').change(function () {
        search_bundle();
    }); 
        
});
</script>
