<?php
    namespace App\StringLib;

    interface iValidateString
    {
        public function isPhoneNumber(string $phoneNumber, bool $checkPhoneNumber = false, string $country = null, array $pattern = null);
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
        *   @acceptedMask           (array)
        *   @minLength              (int)
        *   @maxLength              (int)
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

            //Check if phone number exists thus valids it.
            if ($checkPhoneNumber == true) {
                // set API Access Key
                $access_key = $this->secrets['numverify_access_key']; 

                // Initialize CURL:
                $ch = curl_init('http://apilayer.net/api/validate?access_key='.$access_key.'&number='.$phoneNumber.'&country_code='.$country.'');  
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

                if ($pattern == null) {
                    //retrieve all defined pattern configuration 
                    $patterns = include('phoneNumbers.config.php');

                    if ($country != null) {
                        $pattern = $patterns[array_search($country, array_column($patterns, 'country'))];
                    } else {
                        //TODO Make global pattern
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

                //Strip phone number from separators
                if (isset($pattern['charSeparators']) && $result['valid']) {

                    //Clean Phone number
                    foreach($pattern['charSeparators'] as $str) {
                        $workingPhoneNumber = str_replace($str, '', $workingPhoneNumber);
                    }
                }

                $result['number'] = $workingPhoneNumber;

                //Check against accepted masks
                if (isset($pattern['acceptedMask']) && $result['valid']) {
                    $result['valid'] = false;                   
                    foreach($pattern['acceptedMask'] as $str) {
                        if (preg_match($str, $workingPhoneNumber)) {
                            $result['valid'] = true; 
                        }  
                    }

                    if (!$result['valid']) {
                        $result['error'] = 'Phone number has an invalid format for country '.$country;
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
    }

    ?>