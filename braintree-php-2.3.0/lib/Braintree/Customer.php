<?php
/**
 * Braintree Customer module
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2010 Braintree Payment Solutions
 */

/**
 * Creates and manages Customers
 *
 * <b>== More information ==</b>
 *
 * For more detailed information on Customers, see {@link http://www.braintreepaymentsolutions.com/gateway/customer-api http://www.braintreepaymentsolutions.com/gateway/customer-api}
 *
 * @package    Braintree
 * @category   Resources
 * @copyright  2010 Braintree Payment Solutions
 * 
 * @property-read array  $addresses
 * @property-read string $company
 * @property-read string $createdAt
 * @property-read array  $creditCards
 * @property-read array  $customFields custom fields passed with the request
 * @property-read string $email
 * @property-read string $fax
 * @property-read string $firstName
 * @property-read string $id
 * @property-read string $lastName
 * @property-read string $phone
 * @property-read string $updatedAt
 * @property-read string $website
 */
class Braintree_Customer extends Braintree
{
    public static function all()
    {
        $response = braintree_http::post('/customers/advanced_search_ids');
        $pager = array(
            'className' => __CLASS__,
            'classMethod' => 'fetch',
            'methodArgs' => array()
            );

        return new Braintree_ResourceCollection($response, $pager);
    }

    public static function fetch($ids)
    {
        $response = braintree_http::post('/customers/advanced_search', array('search' => array('ids' => $ids)));

        return braintree_util::extractattributeasarray(
            $response['customers'],
            'customer'
        );
    }

    /**
     * Creates a customer using the given +attributes+. If <tt>:id</tt> is not passed,
     * the gateway will generate it.
     *
     * <code>
     *   $result = Braintree_Customer::create(array(
     *     'first_name' => 'John',
     *     'last_name' => 'Smith',
     *     'company' => 'Smith Co.',
     *     'email' => 'john@smith.com',
     *     'website' => 'www.smithco.com',
     *     'fax' => '419-555-1234',
     *     'phone' => '614-555-1234'
     *   ));
     *   if($result->success) {
     *     echo 'Created customer ' . $result->customer->id;
     *   } else {
     *     echo 'Could not create customer, see result->errors';
     *   }
     * </code>
     *
     * @access public
     * @param array $attribs
     * @return object Result, either Successful or Error
     */
    public static function create($attribs = array())
    {
        Braintree_Util::verifyKeys(self::createSignature(), $attribs);
        return self::_doCreate('/customers', array('customer' => $attribs));
    }

    /**
     * attempts the create operation assuming all data will validate
     * returns a Braintree_Customer object instead of a Result
     *
     * @access public
     * @param array $attribs
     * @return object
     * @throws Braintree_Exception_ValidationError
     */
    public static function createNoValidate($attribs = array())
    {
        $result = self::create($attribs);
        return self::returnObjectOrThrowException(__CLASS__, $result);
    }
    /**
     * create a customer from a TransparentRedirect operation
     *
     * @access public
     * @param array $attribs
     * @return object
     */
    public static function createFromTransparentRedirect($queryString)
    {
        trigger_error("DEPRECATED: Please use Braintree_TransparentRedirectRequest::confirm", E_USER_NOTICE);
        $params = Braintree_TransparentRedirect::parseAndValidateQueryString(
                $queryString
                );
        return self::_doCreate(
                '/customers/all/confirm_transparent_redirect_request',
                array('id' => $params['id'])
        );
    }

    /**
     *
     * @access public
     * @param none
     * @return string
     */
    public static function createCustomerUrl()
    {
        trigger_error("DEPRECATED: Please use Braintree_TransparentRedirectRequest::url", E_USER_NOTICE);
        return Braintree_Configuration::merchantUrl() .
                '/customers/all/create_via_transparent_redirect_request';
    }


    /**
     * creates a full array signature of a valid create request
     * @return array gateway create request format
     */
    public static function createSignature()
    {

        $creditCardSignature = Braintree_CreditCard::createSignature();
        unset($creditCardSignature['customerId']);
        $signature = array(
            'id', 'company', 'email', 'fax', 'firstName',
            'lastName', 'phone', 'website',
            array('creditCard' => $creditCardSignature),
            array('customFields' => array('_anyKey_')),
            );
        return $signature;
    }

    /**
     * creates a full array signature of a valid update request
     * @return array update request format
     */
    public static function updateSignature()
    {
        $creditCardSignature = Braintree_CreditCard::updateSignature();

        foreach($creditCardSignature AS $key => $value) {
            if(is_array($value) and array_key_exists('options', $value)) {
                array_push($creditCardSignature[$key]['options'], 'updateExistingToken');
            }
        }

        $signature = array(
            'id', 'company', 'email', 'fax', 'firstName',
            'lastName', 'phone', 'website',
            array('creditCard' => $creditCardSignature),
            array('customFields' => array('_anyKey_')),
            );
        return $signature;
    }


    /**
     * find a customer by id
     *
     * @access public
     * @param string id customer Id
     * @return object Braintree_Customer
     * @throws Braintree_Exception_NotFound
     */
    public static function find($id)
    {
        self::_validateId($id);
        try {
            $response = Braintree_Http::get('/customers/'.$id);
            return self::factory($response['customer']);
        } catch (Braintree_Exception_NotFound $e) {
            throw new Braintree_Exception_NotFound(
            'customer with id ' . $id . ' not found'
            );
        }

    }

    /**
     * credit a customer for the passed transaction
     *
     * @access public
     * @param array $attribs
     * @return object Braintree_Result_Successful or Braintree_Result_Error
     */
    public static function credit($customerId, $transactionAttribs)
    {
        self::_validateId($customerId);
        return Braintree_Transaction::credit(
                array_merge($transactionAttribs,
                        array('customerId' => $customerId)
                        )
                );
    }

    /**
     * credit a customer, assuming validations will pass
     *
     * returns a Braintree_Transaction object on success
     *
     * @access public
     * @param array $attribs
     * @return object Braintree_Transaction
     * @throws Braintree_Exception_ValidationError
     */
    public static function creditNoValidate($customerId, $transactionAttribs)
    {
        $result = self::credit($customerId, $transactionAttribs);
        return self::returnObjectOrThrowException('Braintree_Transaction', $result);
    }

    /**
     * delete a customer by id
     *
     * @param string $customerId
     */
    public static function delete($customerId)
    {
        self::_validateId($customerId);
        Braintree_Http::delete('/customers/' . $customerId);
        return new Braintree_Result_Successful();
    }

    /**
     * create a new sale for a customer
     *
     * @param string $customerId
     * @param array $transactionAttribs
     * @return object Braintree_Result_Successful or Braintree_Result_Error
     * @see Braintree_Transaction::sale()
     */
    public static function sale($customerId, $transactionAttribs)
    {
        self::_validateId($customerId);
        return Braintree_Transaction::sale(
                array_merge($transactionAttribs,
                        array('customerId' => $customerId)
                        )
                );
    }

    /**
     * create a new sale for a customer, assuming validations will pass
     *
     * returns a Braintree_Transaction object on success
     * @access public
     * @param string $customerId
     * @param array $transactionAttribs
     * @return object Braintree_Transaction
     * @throws Braintree_Exception_ValidationsFailed
     * @see Braintree_Transaction::sale()
     */
    public static function saleNoValidate($customerId, $transactionAttribs)
    {
        $result = self::sale($customerId, $transactionAttribs);
        return self::returnObjectOrThrowException('Braintree_Transaction', $result);
    }

    /**
     * updates the customer record
     *
     * if calling this method in static context, customerId
     * is the 2nd attribute. customerId is not sent in object context.
     *
     * @access public
     * @param array $attributes
     * @param string $customerId (optional)
     * @return object Braintree_Result_Successful or Braintree_Result_Error
     */
    public static function update($customerId, $attributes)
    {
        Braintree_Util::verifyKeys(self::updateSignature(), $attributes);
        self::_validateId($customerId);
        return self::_doUpdate(
            'put',
            '/customers/' . $customerId,
            array('customer' => $attributes)
        );
    }

    /**
     * update a customer record, assuming validations will pass
     *
     * if calling this method in static context, customerId
     * is the 2nd attribute. customerId is not sent in object context.
     * returns a Braintree_Customer object on success
     *
     * @access public
     * @param array $attributes
     * @param string $customerId
     * @return object Braintree_Customer
     * @throws Braintree_Exception_ValidationsFailed
     */
    public static function updateNoValidate($customerId, $attributes)
    {
        $result = self::update($customerId, $attributes);
        return self::returnObjectOrThrowException(__CLASS__, $result);
    }
    /**
     *
     * @access public
     * @param none
     * @return string
     */
    public static function updateCustomerUrl()
    {
        trigger_error("DEPRECATED: Please use Braintree_TransparentRedirectRequest::url", E_USER_NOTICE);
        return Braintree_Configuration::merchantUrl() .
                '/customers/all/update_via_transparent_redirect_request';
    }

    /**
     * update a customer from a TransparentRedirect operation
     *
     * @access public
     * @param array $attribs
     * @return object
     */
    public static function updateFromTransparentRedirect($queryString)
    {
        trigger_error("DEPRECATED: Please use Braintree_TransparentRedirectRequest::confirm", E_USER_NOTICE);
        $params = Braintree_TransparentRedirect::parseAndValidateQueryString(
                $queryString
        );
        return self::_doUpdate(
                'post',
                '/customers/all/confirm_transparent_redirect_request',
                array('id' => $params['id'])
        );
    }

    /* instance methods */

    /**
     * sets instance properties from an array of values
     *
     * @ignore
     * @access protected
     * @param array $customerAttribs array of customer data
     * @return none
     */
    protected function _initialize($customerAttribs)
    {
        // set the attributes
        $this->_attributes = $customerAttribs;

        // map each address into its own object
        $addressArray = array();
        if (isset($customerAttribs['addresses'])) {

            foreach ($customerAttribs['addresses'] AS $address) {
                $addressArray[] = Braintree_Address::factory($address);
            }
        }
        $this->_set('addresses', $addressArray);

        // map each creditcard into its own object
        $ccArray = array();
        if (isset($customerAttribs['creditCards'])) {
            foreach ($customerAttribs['creditCards'] AS $creditCard) {
                $ccArray[] = Braintree_CreditCard::factory($creditCard);
            }
        }
        $this->_set('creditCards', $ccArray);

    }

    /**
     * returns a string representation of the customer
     * @return string
     */
    public function  __toString()
    {
        foreach ($this->_attributes AS $key => $value) {
            if (is_array($value)) {
                foreach ($value AS $obj) {
                    $pAttrib .= sprintf('%s', $obj);
                }
            } else {
                $pAttrib = $value;
            }
            $printableAttribs[$key] = sprintf('%s', $pAttrib);
        }
        return __CLASS__ . '[' .
                Braintree_Util::implodeAssociativeArray($printableAttribs) .']';
    }

    /**
     * returns false if comparing object is not a Braintree_Customer,
     * or is a Braintree_Customer with a different id
     *
     * @param object $otherCust customer to compare against
     * @return boolean
     */
    public function isEqual($otherCust)
    {
        return !($otherCust instanceof Braintree_Customer) ? false : $this->id === $otherCust->id;
    }

    /* private class properties  */

    /**
     * @access protected
     * @var array registry of customer data
     */
    protected $_attributes = array(
        'addresses'   => '',
        'company'     => '',
        'creditCards' => '',
        'email'       => '',
        'fax'         => '',
        'firstName'   => '',
        'id'          => '',
        'lastName'    => '',
        'phone'       => '',
        'createdAt'   => '',
        'updatedAt'   => '',
        'website'     => '',
        );

    /**
     * sends the create request to the gateway
     *
     * @ignore
     * @param string $url
     * @param array $params
     * @return mixed
     */
    public static function _doCreate($url, $params)
    {
        $response = Braintree_Http::post($url, $params);

        return self::_verifyGatewayResponse($response);
    }

    /**
     * sets private properties
     * this function is private so values are read only
     * @ignore
     * @access protected
     * @param string $key
     * @param mixed $value
     */
    private function _set($key, $value)
    {
        $this->_attributes[$key] = $value;
    }

    /**
     * verifies that a valid customer id is being used
     * @ignore
     * @param string customer id
     * @throws InvalidArgumentException
     */
    private static function _validateId($id = null) {
        if (empty($id)) {
           throw new InvalidArgumentException(
                   'expected customer id to be set'
                   );
        }
        if (!preg_match('/^[0-9A-Za-z_-]+$/', $id)) {
            throw new InvalidArgumentException(
                    $id . ' is an invalid customer id.'
                    );
        }
    }


    /* private class methods */

    /**
     * sends the update request to the gateway
     *
     * @ignore
     * @param string $url
     * @param array $params
     * @return mixed
     */
    private static function _doUpdate($httpVerb, $url, $params)
    {
        $response = Braintree_Http::$httpVerb($url, $params);

        return self::_verifyGatewayResponse($response);
    }

    /**
     * generic method for validating incoming gateway responses
     *
     * creates a new Braintree_Customer object and encapsulates
     * it inside a Braintree_Result_Successful object, or
     * encapsulates a Braintree_Errors object inside a Result_Error
     * alternatively, throws an Unexpected exception if the response is invalid.
     *
     * @ignore
     * @param array $response gateway response values
     * @return object Result_Successful or Result_Error
     * @throws Braintree_Exception_Unexpected
     */
    private static function _verifyGatewayResponse($response)
    {
        if (isset($response['customer'])) {
            // return a populated instance of Braintree_Customer
            return new Braintree_Result_Successful(
                    self::factory($response['customer'])
            );
        } else if (isset($response['apiErrorResponse'])) {
            return new Braintree_Result_Error($response['apiErrorResponse']);
        } else {
            throw new Braintree_Exception_Unexpected(
            "Expected customer or apiErrorResponse"
            );
        }
    }

    /**
     *  factory method: returns an instance of Braintree_Customer
     *  to the requesting method, with populated properties
     *
     * @ignore
     * @return object instance of Braintree_Customer
     */
    public static function factory($attributes)
    {
        $instance = new self();
        $instance->_initialize($attributes);
        return $instance;
    }

}
