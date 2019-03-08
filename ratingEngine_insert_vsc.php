<?php
require("../../config/pdr.conf.php");
Util::validate('admin,root,acct,dentguard,ao');

$rateObj = new ratingEngine_Rate($conn);

$rateObj->set_values($_POST);

$master_rate_id_arr = $rateObj->insertMasterRate();

//print_r($_FILES);

if (count($_POST))	
{	
        $path = FILES_DIR.'rate_files'; 
        $uploader = new Uploader($path); 	
  	$uploaded_file = $uploader->upload('upload_rateform');
          
 	if ($_FILES['upload_rateform']['type'] != 'text/plain')
 	{
  		$html.= "<div class='maroonbold13'>File format is invalid. File must be a tab delimited file with .txt extension.<br /> <br />";
  		$html.= "File Format: {$_FILES['upload_rateform']['type']} </div>";	 
 	}
 	else 
 	{
 		if(strlen(trim($uploaded_file)))
  		{	 
//                    echo 'THIS IS TRUE!';
                    
   			$affgrp   			= trim($_POST['affgrp']);
   			$product  			= trim($_POST['product']);
   			$dealer_group	    = trim($_POST['ratename_inp']);
   			$insurance_company 	= trim($_POST['insurance_company']);
   			$reinsured 			= trim($_POST['reinsured']);
   			$dzc   	  			= trim($_POST['dzc']);
   			$dzaf  	  			= trim($_POST['dzaf']);
   			$abg  	  			= trim($_POST['abg']);
   			$fd_xol             = trim($_POST['fd_xol']);
   			          
   			if(trim(strlen($dzc)))
   			{
   			    $company.= "dzc,";
   			}
   			if(trim(strlen($dzaf)))
   			{
   			    $company.= "dzaf,";
   			}
   			if(trim(strlen($abg)))
   			{
   			    $company.= "abg,";
   			}
   			
			$company = substr($company,0,-1);  

			
			$summary_arr = array(
			    'affgrp'             => $affgrp,
			    'company'            => $company,
			    'dealer_group'       => $dealer_group,
			    'insurance_company'  => $insurance_company,
			    'reinsured'          => $reinsured,
			    'created_by'		 =>	$_SESSION['login_id'],
			    'date_created'		 => date("Y-m-d H:i:s"),
			    'ratecard_num'		 => $upload_code,
			    'fd_xol'             => $fd_xol						         
			);          
			
			dzProduct_DBOperations::insert('ratecard_summary', $summary_arr, $conn);
			                       
                        $full_file_name = $path."/$uploaded_file";
  		        $upload_code = time(); 
   			$inputFile = $full_file_name;
			$fh = fopen($inputFile,'r');
  			          
                	$delimiter = "\t";  
			$txtFileObj = new ProcessTextFile($inputFile,$delimiter,0); 
			$dataArr = $txtFileObj->readFile();
			   
//                        echo "<pre>"; 
//                        print_r($dataArr);
//                        echo "</pre> <br /> <br /> "; 
                        
//                        exit();
                        
			$query = "DELETE FROM ratecard_upload";
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
				 $insertData[$k] = str_replace('"','', $v);   
				}
				
				
				$insertData['row'] = $row;		  
				$insertData['upload_code'] = $upload_code;
				$firstColData = $dataArr[$row][0];
				dzProduct_DBOperations::insert('ratecard_upload',$insertData,$conn);
				$row++;  
                                
//                                echo "<pre> ";  print_r($insertData);  echo "</pre> <br /> <br /> <br />";
                                 
				$insertData = array();
                                
                                if($row_count > 50000)
                                {
                                    echo "<br /> <br /> FATAL ERROR: INVALID FORMAT. END OF FILE NOT FOUND. PLEASE REVIEW FILE FORMAT AND RE-UPLOAD.";
                                    exit; 
                                } 
                                     
			} 
                        
                    
                        
			while (strtolower($firstColData) != 'end_ratecard');
                        $whereClause = " WHERE 1 ORDER BY id DESC LIMIT 1";
                        $master_rate_arr = dzProduct_DBOperations::select('master_rate','',$conn,array('id'),$whereClause);
                        //$master_rate_id = 999;
                        $master_rate_id = $master_rate_arr[0]['id'];
                        $rateCardObj = new rateStructure_LoadRateCard($upload_code,$conn,$master_rate_id); 
			$rateCardObj->markSections();         
			$rateCardObj->loadVSCSectionData(); 
			$errors = $rateCardObj->getErrors();
			
			if(is_array($errors) AND count($errors))
			{
			    $html.= "<br /> <br />FATAL ERROR: Following errors encountered while uploading file: <br />";
							    
			    foreach($errors as $error)
			    {	
			        $html.= "<strong>$error</strong> <br />";
			    }  	
			}
			else 
			{    
				$html.= "<span style='font-weight:bold; font-size:15px;'><a href='view_ratecard_vsc_details_v2.php?master_rate_id=$master_rate_id'>View Rate Card</a> </strong> <br /> <br />";
			}   
//			$rateCardObj->getSectionEndingMarkers();   
						
  		}	
 	}
        
        echo $html;
}

$rateObj->insertMasterRateDetails();

$surchargesArr = $_POST['surcharges'];
$deductibleArr = $_POST['deductible'];

if(!empty($surchargesArr)){
    
    foreach($surchargesArr as $fieldArr){
        $dataRow[] = count($fieldArr);
    }

    $surchargesRow = max($dataRow);

    for($x=0; $x<$surchargesRow; $x++){

        foreach($master_rate_id_arr as $k=>$id){
            $surchargesDataRow = array(
                'master_rate_id' => $id,
                'surcharge_type_id' => $surchargesArr['type'][$x],
                'surcharge_amount' => $surchargesArr['amount'][$x],
                'gl' => $surchargesArr['gl'][$x],
                'partner' => $surchargesArr['partner'][$x],
                'product' => $surchargesArr['product'][$x],
                'bundle' => $surchargesArr['bundle'][$x]
            );    

            dzProduct_DBOperations::insert('master_rate_surcharges', $surchargesDataRow, $conn);

        }

    }
}

if(!empty($deductibleArr)){
    
    foreach($deductibleArr as $fieldArr){
        $dataRow1[] = count($fieldArr);
    }

    $deductibleRow = max($dataRow1);

    for($x=0; $x < $deductibleRow; $x++){

        foreach($master_rate_id_arr as $k=>$id){

            $deductible_amount = $deductibleArr['disappearing'][$x] == 1 ? 0 : $deductibleArr['deductible_amount'][$x];
            $deductibleDataRow = array(
                'master_rate_id' => $id,
                'is_disappearing' => $deductibleArr['disappearing'][$x],
                'deductible_amount' => $deductible_amount,
                'surcharge_amount' => $deductibleArr['surcharge_amount'][$x],
                'gl' => $deductibleArr['gl'][$x],
                'partner' => $deductibleArr['partner'][$x],
                'product' => $deductibleArr['product'][$x],
                'bundle' => $deductibleArr['bundle'][$x]
            );    

            dzProduct_DBOperations::insert('master_rate_deductibles', $deductibleDataRow, $conn);

        }

    }
}
    

session_start();
unset($_SESSION['class']);
unset($_SESSION['class_sl']);

$param = "";
$x=0;
foreach($master_rate_id_arr as $k=>$v){
    if($x != 0 ){
        $and = "&";
    }
    $param .= "{$and}master_rate_id{$k}={$v}";
    
    $x++;
}

header("Location: ratingEngine_search.php?vsc=1&$param");

//echo '<pre>'; print_r($_POST); echo '</pre>';
 
?>
