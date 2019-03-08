<?php
require("../../config/pdr.conf.php");     
//Util::validate('admin,root,acct,dentguard,ao');

$rate_id = $_GET['rate_id'];
$master_rate_id = $_GET['master_rate_id'];
         
//print_r($_POST);
$termmo = array();
foreach($_POST as $k=>$v){
    $str = substr($k, 0, 6);
    if($str == 'termmo'){
        $termmo[] = $v;
    }
}

$num = 1;
foreach($_POST as $k=>$v){
               
        if(is_int($k) ){
            
            $whereArr = array('id'=>$k, 'amount'=>$v);

            $is_same_data = dzProduct_DBOperations::select('rate_bundle_template', $whereArr, $conn);    

            if(!is_array($is_same_data)){
                
                $dataArr = array( 'amount'=> $v);

                dzProduct_DBOperations::updateTable('rate_bundle_template', $dataArr, array('id'=>$k), $conn);                
                $updated_id[] = $k;
             }
    }
    
    if(!is_int($k) && $k != 'save' && $k != 'terms' && $k != 'rate_name' && $k != 'cancel_fee' ){
            
                 $field = explode('_', $k);
                 
//                 echo '<pre>'; print_r($field); echo '</pre>';
                 
                 if(trim($field[0]) == 'gl'){
                    
                    $whereArr = array('id'=>$field[1], 'account'=>$v);
                    $is_same_gldata = dzProduct_DBOperations::select('rate_bundle_template', $whereArr, $conn);
                    
                    //if(!is_array($is_same_gldata)){
                         $account = str_replace('-','',$v);
                        $dataArr = array('account'=>$account);
                        dzProduct_DBOperations::updateTable('rate_bundle_template', $dataArr, array('id'=>$field[1]), $conn);  
//                        $dataMessage['gl'][] = $k.' - '.$v.' - successfully changed.';
                    // }
                 }
                 
                if(trim($field[0]) == 'vendor'){
                    $whereArr = array('id'=>$field[1], 'account'=>$v);
                    $is_same_vendordata = dzProduct_DBOperations::select('rate_bundle_template', $whereArr, $conn);
                    
                    if(!is_array($is_same_vendordata)){
                        $dataArr = array('vendorid'=>$v);
                        dzProduct_DBOperations::updateTable('rate_bundle_template', $dataArr, array('id'=>$field[1]), $conn);   
                     
                     }
                 }
                 
                 
        }
}

//echo "<br /> 2: <br />"; 
//
//echo 'data message: <pre> '; print_r($dataMessage); echo '</pre>';

$isRateNameExist = dzProduct_DBOperations::select('rate_template_name', array('rate_id' => $rate_id), $conn);

if(count($isRateNameExist) > 0){
    dzProduct_DBOperations::deleteRows('rate_template_name', array('rate_id'=>$rate_id), $conn);
}

dzProduct_DBOperations::insert('rate_template_name', array('rate_name'=>$_POST['rate_name'], 'gl_cancel_fee' => $_POST['cancel_fee'], 'rate_id'=>$rate_id, 'master_rate_id'=>$master_rate_id), $conn);

//check rate_template data
$isRateExist = dzProduct_DBOperations::select('rate_template', array('rate_id' => $rate_id), $conn);

if(count($isRateExist) > 0){
    dzProduct_DBOperations::deleteRows('rate_template', array('rate_id'=>$rate_id), $conn);
}

$rate_bundle_template = dzProduct_DBOperations::select('rate_bundle_template', array('rate_id' => $rate_id), $conn);

foreach($rate_bundle_template as $k=>$data){
    
    if(in_array($data['term'], $termmo)){
        
        $dataArr = array(
            'rate_id' => $rate_id,
            'product' => $data['product'],
            'is_bundle' => $data['is_bundle'],
            'is_coverage_group' => $data['is_coverage_group'],
            'add_to_dealer_cost'=> $data['add_to_dealer_cost'],
            'product_bundle_name'=> $data['product_bundle_name'],
            'bundle_name'=> $data['bundle_name'],
            'coverage'=> $data['coverage'],
            'rate_bucket_id'=> $data['rate_bucket_id'],
            'bucket'=> $data['bucket'],
            'min_odometer'=> $data['min_odometer'],
            'max_odometer'=> $data['max_odometer'],
            'type'=> $data['type'],
            'account'=> $data['account'],
            'vendorid'=> $data['vendorid'],
            'class'=> $data['class'],
            'term'=> $data['term'],
            'mile'=> $data['mile'],
            'amount'=> $data['amount']
        );
        
//        echo '<pre>'; print_r($dataArr); echo '</pre>';
        
        dzProduct_DBOperations::insert('rate_template', $dataArr, $conn);
    }
}

//echo '<pre> '; print_r($dataArr); echo '</pre>';
//exit();


$updated_id_list = implode(',',$updated_id);
$terms = implode(',',$termmo);
$rate_name = $_POST['rate_name'];
$gl_cancel_fee = $_POST['cancel_fee'];

//if(!is_array($updated_id)){
//    
//    
//    header("Location: ratingEngine_view_VSC_bundle_edit.php?rate_id=$rate_id&updated=no");
//    
//}else{

    echo 'Successfully Updated';
    
    header("Location: ratingEngine_view_VSC_bundle_edit.php?rate_id=$rate_id&master_rate_id=$master_rate_id&terms=$terms&rate_name=$rate_name&gl_cancel_fee=$gl_cancel_fee&success=1");
//}

