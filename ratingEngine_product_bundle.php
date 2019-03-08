<?php

require("../../config/pdr.conf.php");
Util::validate('admin,root,acct,dentguard,ao');

$prefix = $_POST['product'];
$whereClause = "WHERE prefix='$prefix' GROUP BY prefix";
$product =  dzProduct_DBOperations::select('products','',$conn, array('add_on_options'), $whereClause);
$bundle_id = $product[0]['add_on_options'];
$bundleList = dzProduct_DBOperations::select('product_coverage_bundle', array('bundle_id' => $bundle_id), $conn);

$html = "   <select id=\"bundle\" name=\"surcharges[bundle][]\" class=\"form-control\" style=\"width: 150px;\">
      <option value=\"0\" selected>Select</option>";

if(!empty($bundleList))
{
    foreach($bundleList as $k=>$data){
        $html .= "<option value=\"{$data['id']}\">{$data['bundle_name']}</option>";
    }
} 

$html .= "</select>";

echo $html;
?>
