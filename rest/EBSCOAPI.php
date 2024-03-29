<?php


/**
 * EBSCO API class
 *
 * PHP version 5
 *
 */

require_once 'EBSCOConnector.php';
require_once 'EBSCOResponse.php';
require_once 'EBSCOAuthenticateIP.php';


/**
 * EBSCO API class
 */
class EBSCOAPI
{
    /**
     * The authentication token used for API transactions
     * @global string
     */
    private $authenticationToken;


    /**
     * The session token for API transactions
     * @global string
     */
    private $sessionToken;

    /**
     * The EBSCOConnector object used for API transactions
     * @global object EBSCOConnector
     */
    private $connector;

    /**
     * Create a new EBSCOConnector object or reuse an existing one
     *
     * @param none
     *
     * @return EBSCOConnector object
     * @access public
     */
    public function connector()
    {
		//error_log( "connector function",0);
        if (empty($this->connector)) {
            $this->connector = new EBSCOConnector();
        }

        return $this->connector;
    }


    /**
     * Create a new EBSCOResponse object
     *
     * @param object $response
     *
     * @return EBSCOResponse object
     * @access public
     */
    public function response($response)
    {
        $responseObj = new EBSCOResponse($response);
        return $responseObj;
    }


    /**
     * Request authentication and session tokens, then send the API request.
     * Retry the request if authentication errors occur
     *
     * @param string  $action     The EBSCOConnector method name
     * @param array   $params     The parameters for the HTTP request
     * @param integer $attempts   The number of retries. The default number is 3 but can be increased.
     * 3 retries can handle a situation when both autentication and session tokens need to be refreshed + the current API call
     *
     * @return array              An associative array with results.
     * @access protected
     */
    protected function request($action, $params = null, $attempts = 3)
    {
		//error_log( "Test request",0);
        try {

            $authenticationToken = $this->getAuthToken();
            $sessionToken = $this ->getSessionToken($authenticationToken, 'n');

            
            if(empty($authenticationToken)){
               $authenticationToken = $this -> getAuthToken();
            }
           
            if(empty($sessionToken)){
                $sessionToken = $this -> getSessionToken($authenticationToken,'y');
            }

            $headers = array(
                'x-authenticationToken: ' . $authenticationToken,
                'x-sessionToken: ' . $sessionToken
            );

            $response = call_user_func_array(array($this->connector(), "request{$action}"), array($params, $headers));
			
            $result = $this->response($response)->result();
            $results = $result;             
            return $results;
        } catch(EBSCOException $e) {
            try {
                // Retry the request if there were authentication errors
                $code = $e->getCode();
                switch ($code) {
                    case EBSCOConnector::EDS_AUTH_TOKEN_INVALID:
                        $authenticationToken = $this->getAuthToken();                
                        $sessionToken = $this ->getSessionToken($authenticationToken, 'n');
                        $headers = array(
                'x-authenticationToken: ' . $authenticationToken,
                'x-sessionToken: ' . $sessionToken
            );
                        if ($attempts > 0) {
                            return $this->request($action, $params, $headers, --$attempts);
                        }
                        break;
                    case EBSCOConnector::EDS_SESSION_TOKEN_INVALID:
                        $sessionToken = $this ->getSessionToken($authenticationToken,'y');
                        $headers = array(
                'x-authenticationToken: ' . $authenticationToken,
                'x-sessionToken: ' . $sessionToken
            );
                        if ($attempts > 0) {
                            return $this->request($action, $params, $headers, --$attempts);
                        }
                        break;
                    default:
                        $result = array(
                            'error' => $e->getMessage()
                        );
                        return $result;
                        break;
                }
            }  catch(Exception $e) {
                $result = array(
                    'error' => $e->getMessage()
                );
                return $result;
            }
        } catch(Exception $e) {
            $result = array(
                'error' => $e->getMessage()
            );
            return $result;
        }
    }
    
    /*
     * Get authentication token from appication scop 
     * Check authToen's expiration 
     * if expired get a new authToken and re-new the time stamp
     * 
     * @param none
     * 
     * @access public
     */
    public function getAuthToken(){
		$timestamp=time();
		$timeout=0;
		if (isset($_SESSION["authenticationToken"])){
            $this->authToken = $_SESSION["authenticationToken"];
            $timeout = $_SESSION["authenticationTimeout"]-600;
            $timestamp = $_SESSION["authenticationTimeStamp"];
		}
		else
		{
			$result = $this->apiAuthenticationToken();
            $_SESSION["authenticationToken"]= $result['authenticationToken'];
            $_SESSION["authenticationTimeout"]= $result['authenticationTimeout'];
            $_SESSION["authenticationTimeStamp"]= $result['authenticationTimeStamp'];
            $_SESSION['autocompleteToken'] = $result['autocompleteToken'];
            $_SESSION['autocompleteUrl'] = $result['autocompleteUrl'];
            $_SESSION['autocompleteCustId'] = $result['autocompleteCustId'];
		}

        if(time()-$timestamp >= $timeout){
            $result = $this->apiAuthenticationToken();
            $_SESSION["authenticationToken"]= $result['authenticationToken'];
            $_SESSION["authenticationTimeout"]= $result['authenticationTimeout'];
            $_SESSION["authenticationTimeStamp"]= $result['authenticationTimeStamp'];
            $_SESSION['autocompleteToken'] = $result['autocompleteToken'];
            $_SESSION['autocompleteUrl'] = $result['autocompleteUrl'];
            $_SESSION['autocompleteCustId'] = $result['autocompleteCustId'];

            return $result['authenticationToken'];
		}
		else{
            return $this->authToken;
        }
      
	}
    
    /**
     * Wrapper for authentication API call
     *
     * @param none
     *
     * @access public
     */
    public function apiAuthenticationToken()
    {
        $response = $this->connector()->requestAuthenticationToken();
        $result = $this->response($response)->result();
        return $result;
    }

    /**
     * Get session token for a profile 
     * If session token is not available 
     * a new session token will be generated
     * 
     * @param Authentication token, Profile 
     * @access public
     */
    public function getSessionToken($authenToken, $guest='n'){
        $token = ''; 
        $configFile="Config.xml";
        
        if(isset($_SESSION['sessionToken']) && !empty($_SESSION['sessionToken']) && isset($_SESSION['sessionTimeout']) && isset($_SESSION['sessionTimeoutValue']) && ((int)$_SESSION['sessionTimeoutValue']  > (int)time()) && isset($_SESSION['guest']) && ($guest == $_SESSION['guest'])){
            // if a sessionToken exists
            // AND the sessionTimeout value is greater than current time()
            // AND guest status has not change
            // return the token that is part of the current SESSION and write forward the sessionTimeoutValue
        
            $_SESSION['sessionTimeoutValue'] = time()+($_SESSION['sessionTimeout']*0.9);
            $token = $_SESSION['sessionToken'];
        }
        else{
        // Check user's login status
            if(isset($_SESSION['login']) or (validAuthIP($configFile)==true)){    
                if (($guest=='n') or (validAuthIP($configFile)==true)){
                    $sessionToken = $this->apiSessionToken($authenToken, 'n');
                    
                    //ensure your sessionToken, GuestStatus and SessionTimeoutValue is set
                    $_SESSION['sessionToken']=$sessionToken;
                    $_SESSION['guest'] = $guest;
                    $_SESSION['sessionTimeoutValue'] = time()+($_SESSION['sessionTimeout']*0.9);
                }
                $token = $_SESSION['sessionToken'];
            }
            else{
                $sessionToken = $this->apiSessionToken($authenToken, 'y');   
                $_SESSION['sessionToken']=$sessionToken;
                $token = $_SESSION['sessionToken'];   
                //ensure your sessionToken, GuestStatus and SessionTimeoutValue is set
                
                $_SESSION['guest'] = $guest;
				if (isset($_SESSION['sessionTimeout'])) { $mySec=$_SESSION['sessionTimeout']; } else { $mySec=0;}
                $_SESSION['sessionTimeoutValue'] = time()+($mySec*0.9);
                // TODO: check IP validation
            }
        }

        return $token;
    }

    /**
     * Wrapper for session API call
     *
     * @param Authentication token
     *
     * @access public
     */
    public function apiSessionToken($authenToken, $guest="y")
    {
        // Add authentication tokens to headers
        $headers = array(
            'x-authenticationToken: ' . $authenToken
        );

        $response = $this->connector()->requestSessionToken($headers, $guest);

        $result = $this->response($response)->result();

         return $result;
    }
   
    /**
     * Wrapper for end session API call
     *
     * @param Authentication token
     *
     * @access public
     */
    public function apiEndSessionToken($authenToken, $sessionToken){
        
        // Add authentication tokens to headers
        $headers = array(
            'x-authenticationToken: '.$authenToken
        );
        
        $this -> connector()->requestEndSessionToken($headers, $sessionToken);
    }

    /**
     * Wrapper for search API call
     *
     * @param 
     *
     * @throws object             PEAR Error
     * @return array              An array of query results
     * @access public
     */
    public function apiSearch($params) {
        $results = $this->request('Search', $params);
        return $results;
    }


    /**
     * Wrapper for retrieve API call
     *
     * @param array  $an          The accession number
     * @param string $start       The short database name
     *
     * @throws object             PEAR Error
     * @return array              An associative array of data
     * @access public
     */
    public function apiRetrieve($an, $db, $term)
    {
        // Add the HTTP query params
        $params = array(
            'an'        => $an,
            'dbid'      => $db,
            'highlightterms' => $term // Get currect param name
        );
        $params = http_build_query($params);
        $result = $this->request('Retrieve', $params);
        return $result;
    }

    /**
     * Wrapper for RIS Export
     *
     * @param string $an        The accession number
     * @param string $db        The short database name
     *
     * @throws object             PEAR Error
     * @return array              An associative array of data
     * @access public
     */
    public function apiExport($an, $db)
    {
        // Add the HTTP query params
        $params = array(
            'an'        => $an,
            'dbid'      => $db,
            'format'    => 'ris'
        );
        $params = http_build_query($params);
        $result = $this->request('Export', $params);
        
        return $result;
    }

    /**
     * Wrapper for info API call
     *
     * @return array              An associative array of data
     * @access public
     */
    public function getInfo()
    {
		//error_log('EBSCOAPI - GET INFO',0);
        if(isset($_SESSION['info'])){
			//error_log('EBSCOAPI - getInfo, session vars',0);
			//error_log(print_r($_SESSION,true),0); 
			
            $InfoArray = $_SESSION['info'];
            $timestamp = $InfoArray['timestamp'];
            if(time()-$timestamp>=3600){        
                // Get new Info for the profile
                $InfoArray = $this->apiInfo();
                $_SESSION['info'] = $InfoArray;
                $info = $InfoArray['Info'];           
            }else{
                $info = $InfoArray['Info'];
            }
        }else{              
            // Get new Info for the profile
            $InfoArray = $this->apiInfo();
            $_SESSION['info'] = $InfoArray;
            $info = $InfoArray['Info'];          
        }
		//error_log(print_r($info,true),0);
        return $info;
    }
    
    public function apiInfo(){
        // error_log("EBSCOAPI - apiInfo",0);
        $response = $this->request('Info');
        $Info = array(
            'Info' => $response,
            'timestamp'=>time()
        ); 
        return $Info;
    }

    public function getRelatedContentOptions($Info){
        $return = '';
        $returnArray = array();
        if(isset($Info['relatedcontent'])){
            foreach($Info['relatedcontent'] as $rc){
                if($rc['DefaultOn'] == 'y'){
                    $returnArray[] = $rc['Type'];
                }
            }
        }
        $return = implode(',', $returnArray);
        return $return;
    }

    public function getAutoSuggestState($Info){
        $return = 'n';
        if(isset($Info['AvailableDidYouMeanOptions'])){
            foreach($Info['AvailableDidYouMeanOptions'] as $dym){
                if($dym['Id'] == 'AutoSuggest' && $dym['DefaultOn'] == 'y'){
                    $return = 'y';
                }
            }
        }
        return $return;
    }

    public function getAutoCorrectState($Info){
        $return = 'n';
        if(isset($Info['AvailableDidYouMeanOptions'])){
            foreach($Info['AvailableDidYouMeanOptions'] as $dym){
                if($dym['Id'] == 'AutoCorrect' && $dym['DefaultOn'] == 'y'){
                    $return = 'y';
                }
            }
        }
        return $return;
    }

    public function getImageQuickViewState($Info){
        $return = 'n';
        if(isset($Info['IncludeImageQuickView']) && $Info['IncludeImageQuickView'][0]['DefaultOn'] == 'y'){
            $return = 'y';
        }
        return $return;
    }
}
?>
