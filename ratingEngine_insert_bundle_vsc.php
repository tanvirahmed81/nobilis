   

<?php
//ini_set('display_errors', 1);

require("../../config/pdr.conf.php");
Util::validate('admin,root,acct,dentguard,ao');
//ini_set('post_max_size', '500M');   
$rateObj = new ratingEngine_Rate($conn);
//echo "<br /> <br /> <pre>"; print_r($_POST); echo '</pre> <br /> <br />';
 
//echo "<br /> <br /> <pre>".print_r($_FILES,1).'</pre> <br /> <br />';
session_start();
unset($_SESSION['bundle_termmo']);

//echo "<pre>".print_r($_POST,1)."</pre> <br />";  

$upload_code = time(); 
$bundle_contract_option = $_POST['bundle'];
$master_rate_id =  $_POST['master_rate_id'];
$product        =   $_POST['product'];
$affgrp         =   $_POST['affgrp'];
      
      
     // Taking shortcut - Tanvir
    $query = "SELECT id FROM products WHERE prefix='$product' LIMIT 1";
    $res = $conn->Execute($query);
    $row = $res->FetchRow();
    $product_id = $row['id'];
    // --- end shortcut
       

    $path = FILES_DIR.'rate_files'; 
    $uploader = new Uploader($path); 	   
      
    $uploaded_file = $uploader->upload('upload');
    $full_file_name = $path."/$uploaded_file";
      
    $inputFile = $full_file_name;
    $fh = fopen($inputFile,'r');
      
    $delimiter = "\t";  
    $txtFileObj = new ProcessTextFile($inputFile,$delimiter,0); 
    $dataArr = $txtFileObj->readFile();
       
    //echo "<br /> <br /> <pre>".print_r($dataArr,1)."</pre> <br /> <br />";
    //	exit; 

    $query = "DELETE FROM bundle_ratecard_upload ";
    $conn->Execute($query);
                                
    $row = 0;    
    $insertData = array();
    $firstColData = $dataArr[0][0];
    $row_count = 0;
    do {         
            $row_count++;
            $insertData = $dataArr[$row];	  
			
            foreach($insertData as $k=>$v)
            {
                if($_SESSION['login_id'] == 15640)
            {
                if($insertData[$k] == '"I,p"')
                {     
                  //  echo "<br /> <br /> Quote found in {$insertData[$k]} <br /> <br />";
                }
            } 
                $insertData[$k] = str_replace('"','', $v);   
            }
				 
            $insertData['row'] = $row;		  
            $insertData['upload_code'] = $upload_code;
            $firstColData = $dataArr[$row][0];
            
            if($_SESSION['login_id'] == 15640)
            {
              //  echo "<pre>".print_r($insertData,1)."</pre> <br /> <br />";
            } 
            
            dzProduct_DBOperations::insert('bundle_ratecard_upload',$insertData,$conn);
            $row++;  
                                         
                            //   echo "<pre> ".print_r($insertData,1)."</pre> <br /> <br /> <br />";
                                    
            $insertData = array();
                                    
            if($row_count > 50000)
            {                 
                echo "<br /> <br /> FATAL ERROR: INVALID FORMAT. END OF FILE NOT FOUND. PLEASE REVIEW FILE FORMAT AND RE-UPLOAD.";
                exit; 
            }   
                                      
	}
        while (strtolower($firstColData) != 'end_ratecard');
        
       
        
      
        $rateCardObj = new rateStructure_loadBundleRateCard($upload_code,$conn); 
	$rateCardObj->markSections();
        $rateCardObj->getSectionData();
        $bundle_extra_buckets_data = $rateCardObj->getVSCBundleData();
//        echo '<pre> VSC Bundle <br />'; print_r($bundle_extra_buckets_data); echo '</pre>';
//        exit();
    
           
        if($_SESSION['login_id'] == 15640)
        {
         //   echo "<br /> Extra Buckets Arr: <pre>".print_r($bundle_extra_buckets_data,1)."</pre> <br />";
        } 
        
    
if($bundle_contract_option == 'contract_options')
{   
    $is_bundle = 0;
    $is_coverage_group = 1;
    $bundles_raw = trim($_POST['contract_options']);
}   
else
{   
    $is_bundle = 1;
    $is_coverage_group = 0;
    $bundles_raw = trim($_POST['contract_options_bundle']);
}   
       
$bundles_arr = explode(",", $bundles_raw);
//echo "<br /> is bundle: $is_bundle   coverage group: $is_coverage_group <br /> <br />";
//error_log("is bundle: $is_bundle   coverage options: $is_coverage_group  bundles: ".trim($_POST['contract_options_bundle']),1,'tahmed@dentzone.com');


if(intval($is_bundle))
{     
    foreach($bundles_arr as $bundle_name_raw)
    {   
        $bundle_name_arr_raw = explode('~',$bundle_name_raw);
        $bundle_name = $bundle_name_arr_raw[1]; 
        $bundle_benefits = ratingEngine_Rate::getBundleBenefits($product, $bundle_name, $conn);
        $temp_bundles_arr[$bundle_name] = $bundle_benefits;//$bundle_name_arr_raw[2]; 
        $bundle_name_arr[$bundle_name_arr_raw[2]] = $bundle_name_arr_raw[1];      
    }   
        unset($bundles_arr); 
        $bundles_arr = $temp_bundles_arr;         
}

//echo "<br /> bundles arr: <pre>".print_r($bundles_arr)."</pre> <br /> <br />"; 
//echo $bundle_benefits;

 
//$class_arr = array();

$class_arr = explode(',', $_POST['vehicle_class']); 
 
$masterRateArr = array();
$rate_template_arr = array();        

//echo "bundles arr: <br /> <pre>".print_r($bundles_arr,1)."</pre> <br /> <br />";  
//
//echo "<br /> bundles arr: <pre>".print_r($bundles_arr)."</pre> <br /> <br />";
//echo "<br /> bundle name arr: <pre>".print_r($bundle_name_arr)."</pre> <br /> <br />";
//echo "<br /> bundles arr: <pre>".print_r($bundles_arr)."</pre> <br /> <br />"; 
//echo $bundle_benefits;
//exit();



//echo "<br /> Bundles: <pre>".print_r($bundles_arr,1)."</pre> <br /> <br />";

foreach($bundles_arr as $bundle_name => $bundle)
{     
	// echo "--bundle name: $bundle_name <br /> <br />"; 
   // echo "<br />Bunddle is  $bundle <br /> <br />"; 
    
    $barr = explode("~", $bundle);
    
	
	//error_log("bArr: ".print_r($barr,1),1,'tahmed@dentzone');
	
		  
    if(intval($is_bundle))
    {              
        $product_bundle_name = strtoupper($bundle_name); //strtoupper($bundle_name_arr[$bundle]);
    }                  
    else    
    {       
        $product_bundle_name = ''; 
    }       
        
    $rate_template_arr = array(); 
    
   //echo "<br /> 1. Product bundle name: $product_bundle_name <br />";
      


    foreach($barr as $b)
    {          
        if(!is_array($masterRateArr[$b]))
        {      
            echo "Benefit: $b   master rate id: $master_rate_id  product: $product <br />"; 
            $masterRateArr[$b] = ratingEngine_Rate::getMasterRateDetailsForBenefit($master_rate_id, $b, $conn,$product);
        }   
        
        $reserve_bucket_code = 'reserve'; //ratingEngine_Rate::getBucketCode($b, $master_rate_id, 'reserve', $conn);
         
       // echo "Benefit: $b   master rate id: $master_rate_id  product: $product reserve bucket code: $reserve_bucket_code <br /> <pre>".print_r($masterRateArr,1).'</pre> <br /> <br />';
		
		//exit; 
        // echo "<br /> master arr: <PRE>".print_r($masterRateArr[$b],1).'</pre> <br /> <br />'; 
         
        if(is_array($masterRateArr[$b]['reserve']))
        {   
            foreach($class_arr as $class)  
            {
                if(is_array($masterRateArr[$b]['reserve'][$class]))
                {    
                  $rate_template_arr[$b][$reserve_bucket_code][$class] = $masterRateArr[$b]['reserve'][$class];   
                }
                else
                { 
                  $rate_template_arr[$b][$reserve_bucket_code][$class] = $masterRateArr[$b]['reserve']['all'];   
                }    
            }
        }
          
        
        if(is_array($masterRateArr[$b]['prem_fee']))
        {    
            foreach($class_arr as $class)
            {
                if(is_array($masterRateArr[$b]['prem_fee'][$class]))
                {    
                  $rate_template_arr[$b]['prem_fee'][$class] = $masterRateArr[$b]['prem_fee'][$class];   
                }
                else
                {
                  $rate_template_arr[$b]['prem_fee'][$class] = $masterRateArr[$b]['prem_fee']['all'];   
                }    
            }
        }    
             
                
                 
        if(is_array($masterRateArr[$b]['prem_tax']))
        {        
           // $allbuckets[] = $reserve_bucket_code;
                 
            foreach($class_arr as $class)
            {       
                if(is_array($masterRateArr[$b]['prem_tax'][$class]))
                {        
                    $rate_template_arr[$b]['prem_tax'][$class] = $masterRateArr[$b]['prem_tax'][$class];   
                }   
                else
                {     
                    $rate_template_arr[$b]['prem_tax'][$class] = $masterRateArr[$b]['prem_tax']['all'];   
                }    
            }    
        }           
                 
       //   echo "<br /> <br /> rate template Arr: <pre>".print_r($rate_template_arr,1)."</pre> <br /> <br />"; 
   
                
            
            /*
            
             */
     
    }
    
    // echo "<br /> 2. Product bundle name: $product_bundle_name <br />";
    
//    echo "Rate Template: <pre>".print_r($rate_template_arr)."</pre> <br /> <br />";
    
            
            $rate_bucket_id = 0;   
			
			//echo "<br /> <br /> rate template arr: ".print_r($rate_template_arr,1)."</pre> <br /> ";
			//exit;
		    	   
			foreach($rate_template_arr as $a_benefit=>$a_arr)
            {
                $rate_bucket_id++;
                foreach($a_arr as $b_bucket => $b_bdArr)
                {	
					//echo "<br /> bucket: $b_bucket <br /> <pre>".print_r($b_bdArr,1)."</pre> <br />";
						
					/*
                    $b_vendorId = $b_bdArr['vendorId'];
                    $b_account  = $b_bdArr['account'];
                    $b_type     = trim($b_bdArr['type']);
//                  $coverage  = dzProduct_DBOperations::select('buckets', array('code'=>$b_bucket), $conn, array('coverage'));
                    
//                  $whereArr = array('category'=>'reserve', 'code'=>$b_bucket, 'fd_xol'=>$fd_xol);
                    $whereArr = array('code'=>$b_bucket);
                    $coverage  = dzProduct_DBOperations::select('buckets', $whereArr, $conn, array('coverage'));
                    
                    if(strtoupper($b_type) != 'I')
                    {
                        $b_type = "I,P";       
                    }
                    */
					if(is_array($b_bdArr['data']) AND count($b_bdArr['data']))
					{
						foreach($b_bdArr['data'] as $b_class=>$b_cArr)
						{
							foreach($b_cArr as $b_miles => $b_terms)
							{
								foreach($b_terms as $b_term => $b_oArr){
									
									foreach($b_oArr as $k_odo => $b_amount){
										
										$odometerArr = explode('-',$k_odo);
										$min = $odometerArr[0];
										$max = $odometerArr[1];
										
										if($a_bucket == 'PREM_FEE' OR $a_bucket == 'PREM_TAX')
										{
											$type = 'I,P';
											$add_to_dealer_cost = 1;
										}
										else
										{
											$type = 'I';
											$add_to_dealer_cost = 1;        
										}    


										$insertArr = array(
											'rate_id'             => $upload_code,
											'product'             => $product,
											'is_bundle'           => $is_bundle,
											'is_coverage_group'   => $is_coverage_group,
											'bundle_name'         => $bundle,
											'product_bundle_name' => $product_bundle_name,
											'coverage'            => $coverage[0]['coverage'],
											'bucket'              => $b_bucket,
											'rate_bucket_id'      => $rate_bucket_id,
											'type'                => $b_type,
											'add_to_dealer_cost'  => $add_to_dealer_cost,
											'account'             => $b_account,  
											'vendorid'            => $b_vendorId,
											'class'               => $b_class,
											'mile'                => $b_miles,
											'term'                => $b_term,
											'min_odometer'        => $min,
											'max_odometer'        => $max,
											'amount'              => $b_amount
										 );     


									   dzProduct_DBOperations::insert('rate_bundle_template', $insertArr, $conn); 
	//                                    echo "<pre>InsertData: "; print_r($insertArr); echo"</pre>";
									}
										
								}
							}  
						}
					}
					
                }
                
            }
				
			
			
			/*
            foreach($rate_template_arr as $a_benefit=>$a_arr)
            {
                foreach($a_arr as $a_bucket => $b_arr)
                {
                    $rate_bucket_id++; 
                    $a_bucket = strtoupper($a_bucket); 
                    
                    foreach($b_arr as $a_class => $c_arr)
                    {
                        foreach($c_arr as $a_miles => $a_terms)
                        {
                            foreach($a_terms as $a_term => $a_oArr){
                                
                                foreach($a_oArr as $k_odo => $a_amount){
                                    
                                    $odometerArr = explode('-',$k_odo);
                                    $min = $odometerArr[0];
                                    $max = $odometerArr[1];
                                    
                                    if($a_bucket == 'PREM_FEE' OR $a_bucket == 'PREM_TAX')
                                    {
                                        $type = 'I,P';
                                        $add_to_dealer_cost = 1;
                                    }
                                    else
                                    {
                                        $type = 'I';
                                        $add_to_dealer_cost = 1;        
                                    }    


                                    $insertArr = array(
                                        'rate_id'             => $upload_code,
                                        'product'             => $product,
                                        'is_bundle'           => $is_bundle,
                                        'is_coverage_group'   => $is_coverage_group,
                                        'bundle_name'         => $bundle,
                                        'product_bundle_name' => $product_bundle_name,
                                        'coverage'            => $a_benefit,
                                        'bucket'              => $a_bucket,
                                        'rate_bucket_id'      => $rate_bucket_id,
                                        'type'                => $type,
                                        'add_to_dealer_cost'  => $add_to_dealer_cost,
                                        'account'             => '',  
                                        'vendorid'            => 0,
                                        'class'               => $a_class,
                                        'mile'                => $a_miles,
                                        'term'                => $a_term,
                                        'min_odometer'        => $min,
                                        'max_odometer'        => $max,
                                        'amount'              => $a_amount
                                     );     


                                 dzProduct_DBOperations::insert('rate_bundle_template', $insertArr, $conn); 
                                }
                                    
                            }
                        }         
                    }
                }
            }
                
			*/	
				
				
				
            
            // echo "Extra Buckets Data: <pre>".print_r($bundle_extra_buckets_data,1)."</pre> <br /> <br />";
            // exit();
             
            
            if(intval($is_bundle))
            {   
               $extra_buckets_data = $bundle_extra_buckets_data[$product_bundle_name];  
               //echo "Bundle: $product_bundle_name Extra Buckets Data: <pre>".print_r($extra_buckets_data,1)."</pre> <br /> <br />";
               //exit;
            }   
            else 
            {        
                
               $extra_buckets_data = $bundle_extra_buckets_data[$bundle]; 
               //echo "Bundle: $product_bundle_name Extra Buckets Data: <pre>".print_r($extra_buckets_data,1)."</pre> <br /> <br />";
               //exit;
            }   
                
           // echo "Bundle: $bundle --<><><> <br /> <pre>".print_r($extra_buckets_data,1)."</pre> <br /> <br />"; 
            
           
//           echo "Extra Buckets Data: <pre>".print_r($bundle_extra_buckets_data,1)."</pre> <br /> <br />";
           
            if(is_array($extra_buckets_data) AND count($extra_buckets_data)) {
                
                $rate_matrix_identifier = dzProduct_DBOperations::select('master_rate', array('id'=>$master_rate_id), $conn, array('rate_matrix_identifier'));
                $fd_xol = $rate_matrix_identifier[0]['rate_matrix_identifier'];
                
//                echo '<pre>'; print_r($rate_matrix_identifier); echo '</pre>';
//                echo 'fd_xol - '.$fd_xol;
//                exit();
                
            foreach( $extra_buckets_data as $id => $idArr)
            {
                $rate_bucket_id++;
                foreach($idArr as $b_bucket => $b_bdArr)
                {
                    $b_vendorId = $b_bdArr['vendorId'];
                    $b_account  = $b_bdArr['account'];
                    $b_type     = trim($b_bdArr['type']);
//                    $coverage  = dzProduct_DBOperations::select('buckets', array('code'=>$b_bucket), $conn, array('coverage'));
                    
//                    $whereArr = array('category'=>'reserve', 'code'=>$b_bucket, 'fd_xol'=>$fd_xol);
                    $whereArr = array('code'=>$b_bucket);
                    $coverage  = dzProduct_DBOperations::select('buckets', $whereArr, $conn, array('coverage'));
                    
                    if(strtoupper($b_type) != 'I')
                    {
                        $b_type = "I,P";       
                    }
                    
                    foreach($b_bdArr['data'] as $b_class=>$b_cArr)
                    {
                        foreach($b_cArr as $b_miles => $b_terms)
                        {
                            foreach($b_terms as $b_term => $b_oArr){
                                
                                foreach($b_oArr as $k_odo => $b_amount){
                                    
                                    $odometerArr = explode('-',$k_odo);
                                    $min = $odometerArr[0];
                                    $max = $odometerArr[1];
                                    
                                    if($a_bucket == 'PREM_FEE' OR $a_bucket == 'PREM_TAX')
                                    {
                                        $type = 'I,P';
                                        $add_to_dealer_cost = 1;
                                    }
                                    else
                                    {
                                        $type = 'I';
                                        $add_to_dealer_cost = 1;        
                                    }    


                                    $insertArr = array(
                                        'rate_id'             => $upload_code,
                                        'product'             => $product,
                                        'is_bundle'           => $is_bundle,
                                        'is_coverage_group'   => $is_coverage_group,
                                        'bundle_name'         => $bundle,
                                        'product_bundle_name' => $product_bundle_name,
                                        'coverage'            => $coverage[0]['coverage'],
                                        'bucket'              => $b_bucket,
                                        'rate_bucket_id'      => $rate_bucket_id,
                                        'type'                => $b_type,
                                        'add_to_dealer_cost'  => $add_to_dealer_cost,
                                        'account'             => $b_account,  
                                        'vendorid'            => $b_vendorId,
                                        'class'               => $b_class,
                                        'mile'                => $b_miles,
                                        'term'                => $b_term,
                                        'min_odometer'        => $min,
                                        'max_odometer'        => $max,
                                        'amount'              => $b_amount
                                     );     


                                   dzProduct_DBOperations::insert('rate_bundle_template', $insertArr, $conn); 
//                                    echo "<pre>InsertData: "; print_r($insertArr); echo"</pre>";
                                }
                                    
                            }
                        }  
                    }
                }
                
                
            }
		}
            
              
            
     }       
     
//echo '<pre>'; print_r($_POST); echo '</pre>';
//echo '<pre>'; print_r($_POST['vehicle_class']); echo '</pre>';

//$html = $rateObj->getMasterRateDetails($_POST['master_rate_id'], $_POST);
// echo "Upload Code at the top (time()): {$upload_code} <br />";

if($bundle_contract_option == 'contract_options'){       
      
    // $rate_bundle_id = dzProduct_DBOperations::select('rate_bundle_template', '', $conn, '', " WHERE 1 ORDER BY id DESC LIMIT 1");
    
    // $upload_code = $rate_bundle_id[0]['rate_id'];
    
    $upload_code = $rateObj->get_rate_bundle_template_lastinsertid();
    
  //  echo "Upload Code from Last insert ID: {$rateObj->get_rate_bundle_template_lastinsertid()} <br /><br />";
} 
 
//$html = $rateObj->getMasterRateDetails($_POST['master_rate_id'], $upload_code, $_POST);

//echo $detailsArr;
//echo '<pre>'; print_r($detailsArr); echo '</pre>';


unset($_SESSION['contract_options']);

header('Location: ratingEngine_view_VSC_bundle_edit.php?rate_id='.$upload_code.'&master_rate_id='.$master_rate_id);

?>

  

 
