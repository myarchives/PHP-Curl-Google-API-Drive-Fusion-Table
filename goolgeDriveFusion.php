<?php 


function fusionImport($fusionTableId,$rowsForImport,$accessToken){
		

		$endPoint = 'https://www.googleapis.com/upload/fusiontables/v2/tables/'.$fusionTableId.'/import'; 
		
		$header = array(
			'Authorization: Bearer '.$accessToken,
            'Content-Type: application/octet-stream'
        );

		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endPoint);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $rowsForImport);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 55);

    	$errorCount = 0; 
	    $http_response_success = false; 
	    do {
	        if ($errorCount == 1){
	            sleep(1);
	        } elseif ($errorCount > 1){
	            sleep(5);    
	        }
	        try{
	            $data = curl_exec($ch);
	            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 401) {
                    $accessToken = getAccessToken();
                } elseif (!curl_errno($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
	                $http_response_success = false;
	                
	                ++$errorCount;  
	            } elseif (curl_errno($ch) > 0) {
	                $http_response_success = false;
	                ++$errorCount;
	             	
	                   
	            } else {
	                $http_response_success = true;
	            }
	        }
	        catch (Exception $e) {
	            $http_response_success = false;
	            ++$errorCount;  
	        }
	    } while($errorCount < 5 && !$http_response_success);
	    
	    if($http_response_success) {
	        return true;
	    } else {
	        unset($data);
	        return false;
	    }
}
	

function createFusionTable($county,$year,$accessToken){
	$header = array(
			'Authorization: Bearer '.$accessToken,
            'Content-Type: application/json'
        );

	
	$endPoint = 'https://www.googleapis.com/fusiontables/v2/tables';
	$tableStructure = fustionTableStructure($county,$year);
	
	
	$ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $endPoint);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $tableStructure);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER,$header);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);

	$errorCount = 0; 
    $http_response_success = false; 
    do {
        if ($errorCount == 1){
            sleep(1);
        } elseif ($errorCount > 1){
            sleep(5);    
        }
        try{
            $data = curl_exec($ch);
            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 401) {
                    $accessToken = getAccessToken();
            } elseif (!curl_errno($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
                $http_response_success = false;
                
                ++$errorCount;  
            } elseif (curl_errno($ch) > 0) {
                
                $http_response_success = false;
                ++$errorCount;
                
            } else {
                $http_response_success = true;
            }
        }
        catch (Exception $e) {
            
            $http_response_success = false;
            ++$errorCount;  
        }
    } while($errorCount < 5 && !$http_response_success);
    
    if($http_response_success) {
        $data = json_decode($data,true);
        if (!empty($data)) {
            $fusionTableId = $data['tableId'];
            curl_close($ch);
            return $fusionTableId;
        } else {
            unset($data);
            return false;
        }
    } else {
        unset($data);
        return false;
    }

	
} 


function getAccessToken(){
	$clientID = '643204875555-sftk10tgp29hf6ts462bkoa7go8qhs1f.apps.googleusercontent.com';
	$clientSecret = 'wxIi2QML7jCwWshr5oNspesU';
	$refreshToken = '1/RFFX-Tdvi6V-wZNsvX8bHh338VvQUJqu54Ewtd09EJLZ0WEQIKGruXlKNIOtGdku';
	$tokenEndpoint = 'https://www.googleapis.com/oauth2/v3/token';

	$params = array(
    "grant_type" => "refresh_token",
    "client_id" => $clientID,
    "client_secret" => $clientSecret,
    "refresh_token" => $refreshToken,
    "response_type" => "code",
    "redirect_uri" => "https://developers.google.com/oauthplayground",
    "scope" => "https://www.googleapis.com/auth/drive https://www.googleapis.com/auth/fusiontables https://www.googleapis.com/auth/drive.scripts https://www.googleapis.com/auth/drive.metadata https://www.googleapis.com/auth/drive.file https://www.googleapis.com/auth/drive.appdata"
    
    );
    $params = http_build_query($params);
	$paramsLength = strlen($params);
	$header = array(
			        'Content-Type: application/x-www-form-urlencoded',
			        'Content-length: '.$paramsLength,
			        'user-agent: google-oauth-playground',
			    	);


	$ch = curl_init();
	curl_setopt($ch, CURLOPT_URL, $tokenEndpoint);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
	curl_setopt($ch, CURLOPT_POST, true);
	curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
	curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);

	$errorCount = 0; 
    $http_response_success = false; 
    
    do {
        if ($errorCount == 1){
            sleep(1);
        } elseif ($errorCount > 1){
            sleep(5);    
        }
        try{
            $data = curl_exec($ch);
            if (!curl_errno($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
                $http_response_success = false;
                ++$errorCount;  
            } elseif (curl_errno($ch) > 0) {
                $http_response_success = false;
                ++$errorCount;
            } else {
                $http_response_success = true;
            }
        }
        catch (Exception $e) {
            $http_response_success = false;
            ++$errorCount;  
        }
    } while($errorCount < 5 && !$http_response_success); 
    
    if($http_response_success) {
        $data = json_decode($data,true);
        if (!empty($data)) {
            $accesstoken = $data['access_token'];
            curl_close($ch);
            return $accesstoken;
        } else {
            unset($data);
            return false;
        }
    } else {
        unset($data);
        return false;
    }
} 



function copyFusionTable($fusionTableId,$targetFolderId,$accessToken){
		
		$endPoint = 'https://www.googleapis.com/drive/v2/files/'.$fusionTableId.'/parents'; 

		$params =array('kind' => 'drive#parentReference',
  						'id' => $targetFolderId);
  
		$params = json_encode($params);
		
		$header = array(
			'Authorization: Bearer '.$accessToken,
			'Content-length: '.strlen($params),
            'Content-Type: application/json; charset=utf-8'
        );

		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endPoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_POST, true);
		
		curl_setopt($ch, CURLOPT_POSTFIELDS, $params);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 55);

    	$errorCount = 0; 
	    $http_response_success = false; 
	    do {
	        if ($errorCount == 1){
	            sleep(1);
	        } elseif ($errorCount > 1){
	            sleep(5);    
	        }
	        try{
	            $data = curl_exec($ch);
	            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 401) {
                    
                    $accessToken = getAccessToken();
                } elseif (!curl_errno($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
	                $http_response_success = false;
	                
	                ++$errorCount;  
	            } elseif (curl_errno($ch) > 0) {
	                $http_response_success = false;
	                ++$errorCount;
	             	
	                   
	            } else {
	                $http_response_success = true;
	                
	                
	                
	            }
	        }
	        catch (Exception $e) {
	            $http_response_success = false;
	            ++$errorCount;  
	             
	        }
	    } while($errorCount < 5 && !$http_response_success);
	    
	    if($http_response_success) {
	    	
	        return true;
	    } else {
	    	
	        unset($data);
	        return false;
	    }
}



function removeFusionTable($fusionTableId,$currentFolderId,$accessToken){
		
		$endPoint = 'https://www.googleapis.com/drive/v2/files/'.$fusionTableId.'/parents/'.$currentFolderId; 

		$header = array(
			'Authorization: Bearer '.$accessToken
			
		);

		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endPoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 55);

    	$errorCount = 0; 
	    $http_response_success = false; 
	    do {
	        if ($errorCount == 1){
	            sleep(1);
	        } elseif ($errorCount > 1){
	            sleep(5);    
	        }
	        try{
	            $data = curl_exec($ch);
	            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 401) {
                    
                    $accessToken = getAccessToken();
                } elseif (!curl_errno($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) != 204) {
	                $http_response_success = false;
	                
	                ++$errorCount;  
	            } elseif (curl_errno($ch) > 0) {
	                $http_response_success = false;
	                ++$errorCount;
	             	
	                   
	            } else {
	                $http_response_success = true;
	                
	                
	                
	            }
	        }
	        catch (Exception $e) {
	            $http_response_success = false;
	            ++$errorCount;  
	            
	        }
	    } while($errorCount < 5 && !$http_response_success);
	    
	    if($http_response_success) {
	    	
	        return true;
	    } else {
	    	
	        unset($data);
	        return false;
	    }
}


function deleteFusionTable($fusionTableId,$accessToken){
		
		$endPoint = 'https://www.googleapis.com/drive/v2/files/'.$fusionTableId; 

		$header = array(
			'Authorization: Bearer '.$accessToken
			
		);

		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endPoint);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 55);

    	$errorCount = 0; 
	    $http_response_success = false; 
	    do {
	        if ($errorCount == 1){
	            sleep(1);
	        } elseif ($errorCount > 1){
	            sleep(5);    
	        }
	        try{
	            $data = curl_exec($ch);
	            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 401) {
                    
                    $accessToken = getAccessToken();
                } elseif (!curl_errno($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) != 204) {
	                $http_response_success = false;
	                
	                ++$errorCount;  
	            } elseif (curl_errno($ch) > 0) {
	                $http_response_success = false;
	                ++$errorCount;
	             	
	                   
	            } else {
	                $http_response_success = true;
	                
	                
	                
	            }
	        }
	        catch (Exception $e) {
	            $http_response_success = false;
	            ++$errorCount;  
	            
	        }
	    } while($errorCount < 5 && !$http_response_success);
	    
	    if($http_response_success) {
	    	
	        return true;
	    } else {
	    	
	        unset($data);
	        return false;
	    }
}


function truncateFusionTable($fusionTableId,$accessToken){
		

		$endPoint = 'https://www.googleapis.com/fusiontables/v2/query'; 

		$deleteStatement = 'sql=DELETE FROM '.$fusionTableId;

		
		$header = array(
			'Authorization: Bearer '.$accessToken
            
        );


		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $endPoint);
		curl_setopt($ch, CURLOPT_POST, true);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $deleteStatement);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
    	curl_setopt($ch, CURLOPT_TIMEOUT, 55);

    	$errorCount = 0; 
	    $http_response_success = false; 
	    do {
	        if ($errorCount == 1){
	            sleep(1);
	        } elseif ($errorCount > 1){
	            sleep(5);    
	        }
	        try{
	            $data = curl_exec($ch);
	            if (curl_getinfo($ch, CURLINFO_HTTP_CODE) == 401) {
                    $accessToken = getAccessToken();
                } elseif (!curl_errno($ch) && curl_getinfo($ch, CURLINFO_HTTP_CODE) != 200) {
	                $http_response_success = false;
	                
	                ++$errorCount;  
	            } elseif (curl_errno($ch) > 0) {
	                $http_response_success = false;
	                ++$errorCount;
	             	
	                   
	            } else {
	                $http_response_success = true;
	            }
	        }
	        catch (Exception $e) {
	            $http_response_success = false;
	            ++$errorCount;  
	        }
	    } while($errorCount < 5 && !$http_response_success);
	    
	    if($http_response_success) {
	        return true;
	    } else {
	        unset($data);
	        return false;
	    }
}

?>