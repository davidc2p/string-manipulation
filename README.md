# string-manipulation

This projects aims to create new string methods 

List of Methods

isPhoneNumber
    
    Validate a phone number according to the following defined pattern
    or
    Check if phone exists invoking a service http://apilayer.net/api/validate
    
    
    @country                    (string)    Country code for validation purpose 2 letters ISO
    @phoneNumber                (string)    The phone number to be validated
    @pattern                    (array)     Array of properties 
       
        Pattern array expected structure
        @charSeparators         (array)     Allowed sepatores within the phone number string. They are stripped in order for the string to be validated.
        @numCountry             (int)       The expected country number. If informed validates if the string has the correct country code.
        @acceptedMask           (array)     Regexp masks accepted for the country
        @minLength              (int)       Minimum length for phone number after country number has been stripped
        @maxLength              (int)       Maximum length for phone number after country number has been stripped
    
    @checkPhoneNumber           (bool)      false: validate only; true: check if phone is valid invoking a service
    
      About the service we use when @checkPhoneNumber is true:
           NumVerify offers a full-featured yet simple RESTful JSON API for national and international phone number validation and information lookup for a total of 232 countries around the world.
           Requested numbers are processed in real-time, cross-checked with the latest international numbering plan databases and returned in handy JSON format enriched with useful carrier, geographical location and line type data.
           Integrating the numverify API into your application will enable you to verify the validity of phone numbers at the point of entry, protecting you from fraud and increasing good leads.  
    
    @return                     (array())
        @valid	                (bool)      Returns true if the specified phone number is valid.
        @number	                (string)    Returns the phone number you specified in a clean format. (stripped of any special characters)
        @localFormat            (string)	Returns the local (national) format of the specified phone number.
        @internationalFormat    (string)	Returns the international format of the specified phone number.
        @countryPrefix          (string)	Returns the international country dial prefix for the specified phone number.
        @countryCode            (string)	Returns the 2-letter country code assigned to the specified phone number.
        @countryName            (string)	Returns the full country name assigned to the specified phone number.
        @location               (string)	If available, returns the location (city, state, or county) assigned to the specified phone number.
        @carrier                (string)	Returns the name of the carrier which the specified phone number is registered with.
        @linetype               (string)	Returns the line type of the specified phone number (See: Line Type Detection)

isEmail

    Validate an email
    and 
    Check if email exists invoking a service https://quickemailverification.p.rapidapi.com/v1/verify (register to create your account there)
    
    
    @email                      (string)    Email to be validated
    @checkEmail                 (bool)      Against the service is positioned to true or regexp if not 
       
    About the service we use when @checkEmail is true:
        QuickEmailVerification is an online web-based email list cleaning service which allows you to verify email addresses 
        in bulk or real-time using REST API. Our online email validation system processes thousands of email addresses every 
        minute to detect invalid and non-working emails and provides you with complete detailed report. Our unique email 
        verification system is composed of multiple different validations starting from syntax checking to the end users' mailbox 
        existence checking.        

    @return                     (array())
        @valid	                (bool)      Returns true if the specified phone number is valid.
        @reason                 (string)    Reason definitions are as below:
            - invalid_email - Specified email has invalid email address syntax
            - invalid_domain - Domain name does not exist
            - rejected_email - SMTP server rejected email. Email does not exist
            - accepted_email - SMTP server accepted email address
            - no_connect - SMTP server connection failure
            - timeout - Session time out occurred at SMTP server
            - unavailable_smtp - SMTP server is not available to process request
            - unexpected_error - An unexpected error has occurred
            - no_mx_record - Could not get MX records for the domain
            - temporarily_blocked - Email is temporarily greylisted
            - exceeded_storage - SMTP server rejected email. Exceeded storage allocation
        @disposable             (bool)      true if the email address uses a disposable domain
        @accept_all             (bool)      true if the domain appears to accept all emails delivered to that domain
        @role                   (bool)      true if the email address is a role address (manager@example.com, ceo@example.com, etc)
        @free                   (bool)      true if the email address is from free email provider like Gmail, Yahoo!, Hotmail etc.
        @email                  (string)    Returns a normalized version. (Niki@example.com -> niki@example.com)
        @user                   (string)    The local part of an email address. (niki@example.com -> niki)
        @domain                 (string)    The domain of the provided email address. (niki@example.com -> example.com)
        @mx_record              (string)    The preferred MX record of the email domain. This field contains an empty string when MX                                        record is not available.
        @mx_domain              (string)    The domain name of the MX host. This field contains an empty string when MX record is not                                       available.
        @safe_to_send           (bool)      True if the email address is safe for deliverability
        @did_you_mean           (string)    Returns email suggestions if specific typo errors found in email
        @success                (bool)      true if the API request was successful
        @message                (string)    Describes API call failure reason



