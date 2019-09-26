<?php
    namespace App\StringLib;
    use http\Client;

    interface iValidateString
    {
        public function isPhoneNumber(string $phoneNumber, bool $checkPhoneNumber = false, string $country = null, array $pattern = null);
        public function isEmail(string $email, bool $checkEmail = false);
    }

    class ValidateString  implements iValidateString
    {
        public $secrets = array();
        
        public function __construct() {
            $this->secrets = include('secrets.php');
        }
        /*
        * isPhoneNumber
        *
        * Validate a phone number according to the following defined pattern
        * or
        * Check if phone exists invoking a service http://apilayer.net/api/validate
        *
        *
        * @country                  (string)    Country code for validation purpose 2 letters ISO
        * @phoneNumber              (string)    The phone number to be validated
        * @pattern                  (array)     Array of properties 
        *   
        *   Pattern array expected structure
        *   @charSeparators         (array)     Allowed sepatores within the phone number string. They are stripped in order for the string to be validated.
        *   @numCountry             (int)       The expected country number. If informed validates if the string has the correct country code.
        *   @acceptedMask           (array)     Regexp masks accepted for the country
        *   @minLength              (int)       Minimum length for phone number after country number has been stripped
        *   @maxLength              (int)       Maximum length for phone number after country number has been stripped
        *
        * @checkPhoneNumber         (bool)      false: validate only; true: check if phone is valid invoking a service
        *
        *   About the service we use when @checkPhoneNumber is true:
        *       NumVerify offers a full-featured yet simple RESTful JSON API for national and international phone number validation and information lookup for a total of 232 countries around the world.
        *       Requested numbers are processed in real-time, cross-checked with the latest international numbering plan databases and returned in handy JSON format enriched with useful carrier, geographical location and line type data.
        *       Integrating the numverify API into your application will enable you to verify the validity of phone numbers at the point of entry, protecting you from fraud and increasing good leads.  
        *
        * @return                   (array())
        *   @valid	                (bool)      Returns true if the specified phone number is valid.
        *   @number	                (string)    Returns the phone number you specified in a clean format. (stripped of any special characters)
        *   @localFormat            (string)	Returns the local (national) format of the specified phone number.
        *   @internationalFormat    (string)	Returns the international format of the specified phone number.
        *   @countryPrefix          (string)	Returns the international country dial prefix for the specified phone number.
        *   @countryCode            (string)	Returns the 2-letter country code assigned to the specified phone number.
        *   @countryName            (string)	Returns the full country name assigned to the specified phone number.
        *   @location               (string)	If available, returns the location (city, state, or county) assigned to the specified phone number.
        *   @carrier                (string)	Returns the name of the carrier which the specified phone number is registered with.
        *   @linetype               (string)	Returns the line type of the specified phone number (See: Line Type Detection)
        */
        public function isPhoneNumber(string $phoneNumber, bool $checkPhoneNumber = false, string $country = null, array $pattern = null) {
            $result = array(
                'valid'                 => true,
                'number'                => '',
                'localFormat'           => '',
                'internationalFormat'   => '',
                'countryPrefix'         => '',
                'countryCode'           => '', 
                'countryName'           => '',
                'location'              => '',
                'carrier'               => '', 
                'lineType'              => '',
                'error'                 => ''
            );

            if ($pattern == null) {
                //retrieve all defined pattern configuration 
                $patterns = include('phoneNumbers.config.php');

                if ($country != null) {
                    $pattern = $patterns[array_search($country, array_column($patterns, 'country'))];
                    $result['countryCode'] = $pattern['country'];
                    $result['countryPrefix'] = $pattern['numCountry'];
                    $result['countryName'] = $pattern['countryName'];
                } else {
                    //Country is not set
                    $pattern = array();
                    //1. Validate Country code
                    $pattern['countrySeparator'] = '()';
                    $pattern['charSeparators'] = array('-', '+', ' ');
                    //2. No country code, $pattern is all defined patterns
                }
            } else {
                //validate pattern from method call
                if (!isset($pattern['acceptedMask']) && !isset($pattern['minLength']) && !isset($pattern['maxLength'])) {
                    $result['valid'] = false;
                    $result['error'] = 'No valid validation pattern has been defined';
                }
            }

            $workingPhoneNumber = $phoneNumber;

            //Country code validation
            if (isset($pattern['countrySeparator']) && $result['valid']) {
                $countryCodeIni = 1;
                $countryCodeEnd = strpos($phoneNumber, substr($pattern['countrySeparator'], 1, 1));
                if ($countryCodeIni !== FALSE && $countryCodeEnd !== FALSE) {
                    $result['countryCode'] = substr($phoneNumber, $countryCodeIni, $countryCodeEnd - $countryCodeIni);

                    //Clean country code
                    if (isset($pattern['charSeparators'])) {
                        foreach($pattern['charSeparators'] as $str) {
                            $result['countryCode'] = str_replace($str, '', $result['countryCode']);
                        }
                    }

                    //Remove country code from working number
                    $workingPhoneNumber = substr($workingPhoneNumber, $countryCodeEnd + 1, strlen($workingPhoneNumber) - $countryCodeEnd - 1);

                    if (isset($pattern['numCountry'])) {
                        if ($pattern['numCountry'] != $result['countryCode']) {
                            $result['valid'] = false;
                            $result['error'] = 'Country code and country mismatched';
                        }
                    }
                }
            }

            //2. No country code, $pattern is all defined patterns
            if ($country == null) {
                if (!isset($pattern['acceptedMask'])) {
                    if ($result['countryCode'] != '') {
                        //Country is not set search the pattern according to CountryCode
                        $pattern = $patterns[array_search($result['countryCode'] , array_column($patterns, 'numCountry'))];
                    } else {
                        $pattern['acceptedMask'] = array();
                        foreach($patterns as $item) {
                            foreach($item['acceptedMask'] as $mask) {
                                $pattern['acceptedMask'][] = $mask;
                            }
                        }
                    }
                }
            }

            //Strip phone number from separators
            if (isset($pattern['charSeparators']) && $result['valid']) {

                //Clean Phone number
                foreach($pattern['charSeparators'] as $str) {
                    $workingPhoneNumber = str_replace($str, '', $workingPhoneNumber);
                }
            }

            $result['number'] = $workingPhoneNumber;
                
            //Check if phone number exists thus valids it.
            if ($checkPhoneNumber == true) {
                // set API Access Key
                $access_key = $this->secrets['numverify_access_key']; 

                // Initialize CURL:
                $url = 'http://apilayer.net/api/validate?access_key='.$access_key.'&number='.$result['number'].'';
                if (isset($country)) {
                    $url .= '&country_code='.$country.'';
                }
                $ch = curl_init($url);  
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

                // Store the data:
                $json = curl_exec($ch);
                curl_close($ch);

                // Decode JSON response:
                $validationResult = json_decode($json, true);

                $result['valid'] = $validationResult['valid'];
                $result['number'] = $validationResult['number'];
                $result['localFormat'] = $validationResult['local_format'];
                $result['internationalFormat'] = $validationResult['international_format'];
                $result['countryPrefix'] = $validationResult['country_prefix'];
                $result['countryCode'] = $validationResult['country_code'];
                $result['countryName'] = $validationResult['country_name'];
                $result['location'] = $validationResult['location'];
                $result['carrier'] = $validationResult['carrier'];
                $result['lineType'] = $validationResult['line_type'];
                $result['error'] = '';

            } else {
                //Check against accepted masks
                if (isset($pattern['acceptedMask']) && $result['valid']) {
                    $result['valid'] = false;
                    $foundpattern = '';                  
                    foreach($pattern['acceptedMask'] as $str) {
                        if (preg_match($str, $workingPhoneNumber)) {
                            $result['valid'] = true;
                            $foundpattern = $str;
                            break;
                        }  
                    }

                    if (!$result['valid'] && $country != null) {
                        $result['error'] = 'Phone number has an invalid format for country '.$country;
                    }                                   
                }

                //In case  $country == null it means all patterns have been searched
                if ($country == null) {
                    if ($foundpattern != '' && $result['valid']) {
                        foreach($patterns as $item) {
                            foreach($item['acceptedMask'] as $mask) {
                                if ($mask == $foundpattern) {
                                    $result['countryCode'] = $item['country'];
                                    $result['countryPrefix'] = $item['numCountry'];
                                    $result['countryName'] = $item['countryName'];
                                }
                            }
                        }
                        
                    } else {
                        $result['error'] = 'No pattern matches the phone number.';
                    }
                }


                //Check length
                if (isset($pattern['minLength']) && isset($pattern['maxLength']) && $result['valid']) {
                    if (strlen($workingPhoneNumber) < $pattern['minLength'] || strlen($workingPhoneNumber) > $pattern['maxLength']) {
                        $result['valid'] = false;
                        $result['error'] = 'Phone number has incorrect length for country '.$country;
                    }
                }
            }
            
            return $result;
        }

        /*
        * isEmail
        *
        * Validate an email
        * and 
        * Check if email exists invoking a service https://quickemailverification.p.rapidapi.com/v1/verify
        *
        *
        * @email                    (string)    Email to be validated
        * @checkEmail               (bool)      Against the service is positioned to true or regexp if not 
        *   
        *   About the service we use when @checkEmail is true:
        *       QuickEmailVerification is an online web-based email list cleaning service which allows you to verify email addresses 
        *       in bulk or real-time using REST API. Our online email validation system processes thousands of email addresses every 
        *       minute to detect invalid and non-working emails and provides you with complete detailed report. Our unique email 
        *       verification system is composed of multiple different validations starting from syntax checking to the end users' mailbox 
        *       existence checking.        
        *
        * @return                   (array())
        *   @valid	                (bool)      Returns true if the specified phone number is valid.
        *   @reason                 (string)    Reason definitions are as below:
        *       - invalid_email - Specified email has invalid email address syntax
        *       - invalid_domain - Domain name does not exist
        *       - rejected_email - SMTP server rejected email. Email does not exist
        *       - accepted_email - SMTP server accepted email address
        *       - no_connect - SMTP server connection failure
        *       - timeout - Session time out occurred at SMTP server
        *       - unavailable_smtp - SMTP server is not available to process request
        *       - unexpected_error - An unexpected error has occurred
        *       - no_mx_record - Could not get MX records for the domain
        *       - temporarily_blocked - Email is temporarily greylisted
        *       - exceeded_storage - SMTP server rejected email. Exceeded storage allocation
        *   @disposable             (bool)      true if the email address uses a disposable domain
        *   @accept_all             (bool)      true if the domain appears to accept all emails delivered to that domain
        *   @role                   (bool)      true if the email address is a role address (manager@example.com, ceo@example.com, etc)
        *   @free                   (bool)      true if the email address is from free email provider like Gmail, Yahoo!, Hotmail etc.
        *   @email                  (string)    Returns a normalized version. (Niki@example.com -> niki@example.com)
        *   @user                   (string)    The local part of an email address. (niki@example.com -> niki)
        *   @domain                 (string)    The domain of the provided email address. (niki@example.com -> example.com)
        *   @mx_record              (string)    The preferred MX record of the email domain. This field contains an empty string when MX record is not available.
        *   @mx_domain              (string)    The domain name of the MX host. This field contains an empty string when MX record is not available.
        *   @safe_to_send           (bool)      True if the email address is safe for deliverability
        *   @did_you_mean           (string)    Returns email suggestions if specific typo errors found in email
        *   @success                (bool)      true if the API request was successful
        *   @message                (string)    Describes API call failure reason
        */
        public function isEmail(string $email, bool $checkEmail = false) 
        {
            $result = array(
                'valid'                 => true,
                'reason'                => '',
                'disposable'            => false,
                'accept_all'            => false,
                'role'                  => false,
                'free'                  => true,
                'email'                 => '',
                'user'                  => '',
                'domain'                => '',
                'mx_record'             => '',
                'mx_domain'             => '',
                'safe_to_send'          => '',
                'did_you_mean'          => '',
                'success'               => true,
                'error'                 => ''
            );
            
            $expression= '/^([a-zA-Z0-9\-\._]+)@(([a-zA-Z0-9\-_]+\.)+)([a-z]{2,3})$/';

            if (preg_match($expression, $email)) {
                $result['valid'] = true;
 
                if ($checkEmail) {

                    $curl = curl_init();

                    curl_setopt_array($curl, array(
                        CURLOPT_URL => "https://quickemailverification.p.rapidapi.com/v1/verify?email=".$email,
                        CURLOPT_RETURNTRANSFER => true,
                        CURLOPT_ENCODING => "",
                        CURLOPT_MAXREDIRS => 10,
                        CURLOPT_TIMEOUT => 30,
                        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                        CURLOPT_CUSTOMREQUEST => "GET",
                        CURLOPT_HTTPHEADER => array(
                            "authorization: d7d4065698e152fb0a3cf9cfb6ab3ca72891d0320209d70be198d92f31e5",
                            "x-rapidapi-host: quickemailverification.p.rapidapi.com",
                            "x-rapidapi-key: 9c16016761msh1d72a0d419bb42ep133d4ejsn4a559c15056c"
                        ),
                    ));

                    $response = curl_exec($curl);
                    $err = curl_error($curl);

                    curl_close($curl);

                    // Decode JSON response:
                    $validationResult = json_decode($response, true);

                    if ($err) {
                        $result['valid'] = false;
                        $result['success'] = false;
                        $result['error'] = "cURL Error #:" . $err;
                    } else {
                        if ($validationResult['result'] == 'valid') {
                            $result['valid']        = true;
                        } else {
                            $result['valid']        = false;
                        }
                        $result['reason']       = $validationResult['reason'];
                        $result['disposable']   = $validationResult['disposable'];
                        $result['accept_all']   = $validationResult['accept_all'];
                        $result['role']         = $validationResult['role'];
                        $result['free']         = $validationResult['free'];
                        $result['email']        = $validationResult['email'];
                        $result['user']         = $validationResult['user'];
                        $result['domain']       = $validationResult['domain'];
                        $result['mx_record']    = $validationResult['mx_record'];
                        $result['mx_domain']    = $validationResult['mx_domain'];
                        $result['safe_to_send'] = $validationResult['safe_to_send'];
                        $result['did_you_mean'] = $validationResult['did_you_mean'];
                        $result['success']      = $validationResult['success'];
                        $result['error']        = $validationResult['message'];
                    }

                } else {
                     $result['valid']        = true;
                     $result['email']        = $email;
                     $result['error']        = 'Email is valid.';
                }

                return $result;
            
            }
        }
    }

    ?>