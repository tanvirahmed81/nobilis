<?php

//echo 'successful!';
//print_r($_POST);
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
			   
                        //echo "<br /> <br /> <pre>".print_r($dataArr,1)."</pre> <br /> <br /> "; 
                        
                        
			$query = "DELETE FROM ratecard_upload ";
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
                                
                               // echo "<pre> ".print_r($insertData,1)."</pre> <br /> <br /> <br />";
                                 
				$insertData = array();
                                
                                if($row_count > 50000)
                                {
                                    echo "<br /> <br /> FATAL ERROR: INVALID FORMAT. END OF FILE NOT FOUND. PLEASE REVIEW FILE FORMAT AND RE-UPLOAD.";
                                    exit; 
                                } 
                                     
			}     
			while (strtolower($firstColData) != 'end_ratecard');
                        $whereClause = " WHERE 1 ORDER BY id DESC LIMIT 1";
                        $master_rate_arr = dzProduct_DBOperations::select($table,'',$conn,array('id'),$whereClause);
                        //$master_rate_id = 999;
                        $master_rate_id = $master_rate_arr[0]['id'];
                        $rateCardObj = new rateStructure_LoadRateCard($upload_code,$conn,$master_rate_id); 
			$rateCardObj->markSections();         
			$rateCardObj->loadData(); 
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
				$html.= "<span style='font-weight:bold; font-size:15px;'><a href='view_ratecard_details.php?ratecard_num=$upload_code'>View Rate Card</a> </strong> <br /> <br />";
			}   
			//$rateCardObj->getSectionEndingMarkers();   
			
			
  		}	
 	}
        
        echo $html;
}

?>
