<?php
/**
 * Braintree Utility methods
 * PHP version 5
 *
 * @copyright  2010 Braintree Payment Solutions
 */

/**
 * Braintree Utility methods
 *
 *
 * @copyright  2010 Braintree Payment Solutions
 */
class Braintree_Util
{
    /**
     * extracts an attribute and returns an array of objects
     *
     * extracts the requested element from an array, and converts the contents
     * of its child arrays to objects of type Braintree_$attributeName, or returns
     * an array with a single element containing the value of that array element
     *
     * @param array $attribArray attributes from a search response
     * @param string $attributeName indicates which element of the passed array to extract
     *
     * @return array array of Braintree_$attributeName objects, or a single element array
     */
    public static function extractAttributeAsArray(& $attribArray, $attributeName)
    {
        if(!isset($attribArray[$attributeName])):
            return array();
        endif;

        // get what should be an array from the passed array
        $data = $attribArray[$attributeName];
        // set up the class that will be used to convert each array element
        $classFactory = self::buildClassName($attributeName) . '::factory';
        if(is_array($data)):
            // create an object from the data in each element
            $objectArray = array_map($classFactory, $data);
        else:
            return array($data);
        endif;

        unset($attribArray[$attributeName]);
        return $objectArray;
    }
    /**
     * throws an exception based on the type of error
     * @param string $statusCode HTTP status code to throw exception from
     * @throws Braintree_Exception multiple types depending on the error
     *
     */
    public static function throwStatusCodeException($statusCode, $message=null)
    {
        switch($statusCode) {
         case 401:
            throw new Braintree_Exception_Authentication();
            break;
         case 403:
             throw new Braintree_Exception_Authorization($message);
            break;
         case 404:
             throw new Braintree_Exception_NotFound();
            break;
         case 426:
             throw new Braintree_Exception_UpgradeRequired();
            break;
         case 500:
             throw new Braintree_Exception_ServerError();
            break;
         case 503:
             throw new Braintree_Exception_DownForMaintenance();
            break;
         default:
            throw new Braintree_Exception_Unexpected('Unexpected HTTP_RESPONSE #'.$statusCode);
            break;
        }
    }

    /**
     * removes the Braintree_ header from a classname
     *
     * @param string $name Braintree_ClassName
     * @return camelCased classname minus Braintree_ header
     */
    public static function cleanClassName($name)
    {
        $name = str_replace('Braintree_', '', $name);
        // lcfirst only exists >= 5.3
        if ( false === function_exists('lcfirst') ):
            function lcfirst( $str )
            { return (string)(strtolower(substr($str,0,1)).substr($str,1));}
        endif;

        return lcfirst($name);
    }

    /**
     *
     * @param string $name className
     * @return string Braintree_ClassName
     */
    public static function buildClassName($name)
    {
        return 'Braintree_' . ucfirst($name);
    }

    /**
     * convert alpha-beta-gamma to alphaBetaGamma
     *
     * @access public
     * @param string $string
     * @return string modified string
     */
    public static function delimiterToCamelCase($string, $delimiter = '[\-\_]')
    {
        return preg_replace('/' . $delimiter . '(\w)/e', 'strtoupper("$1")',$string);
    }

    /**
     * convert alpha-beta-gamma to alpha_beta_gamma
     *
     * @access public
     * @param string $string
     * @return string modified string
     */
    public static function delimiterToUnderscore($string)
    {
        return preg_replace('/-/', '_', $string);
    }


    /**
     * find capitals and convert to delimiter + lowercase
     *
     * @access public
     * @param var $string
     * @return var modified string
     */
    public static function camelCaseToDelimiter($string, $delimiter = '-')
    {
        return preg_replace('/([A-Z])/e', '"' . $delimiter . '" . strtolower("$1")', $string);
    }

    /**
     *
     * @param array $array associative array to implode
     * @param string $separator (optional, defaults to =)
     * @param string $glue (optional, defaults to ', ')
     */
    public static function implodeAssociativeArray($array, $separator = '=', $glue = ', ')
    {
        // build a new array with joined keys and values
        $tmpArray = null;
        foreach ($array AS $key => $value) {
                $tmpArray[] = $key . $separator . $value;

        }
        // implode and return the new array
        return (is_array($tmpArray)) ? implode($glue, $tmpArray) : false;
    }

    /**
     * verify user request structure
     *
     * compares the expected signature of a gateway request
     * against the actual structure sent by the user
     *
     * @param array $signature
     * @param array $attributes
     */
    public static function verifyKeys($signature, $attributes)
    {
        $validKeys = self::_flattenArray($signature);
        $userKeys = self::_flattenUserKeys($attributes);
        $invalidKeys = array_diff($userKeys, $validKeys);
        $invalidKeys = self::_removeWildcardKeys($validKeys, $invalidKeys);

        if(!empty($invalidKeys)) {
            asort($invalidKeys);
            $sortedList = join(', ', $invalidKeys);
            throw new InvalidArgumentException('invalid keys: '. $sortedList);
        }
    }
    /**
     * flattens a numerically indexed nested array to a single level
     * @param array $keys
     * @param string $namespace
     * @return array
     */
    private static function _flattenArray($keys, $namespace = null)
    {
        $flattenedArray = array();
        foreach($keys AS $key) {
            if(is_array($key)) {
                $theKeys = array_keys($key);
                $theValues = array_values($key);
                $scope = $theKeys[0];
                $fullKey = empty($namespace) ? $scope : $namespace . '[' . $scope . ']';
                $flattenedArray = array_merge($flattenedArray, self::_flattenArray($theValues[0], $fullKey));
            } else {
                $fullKey = empty($namespace) ? $key : $namespace . '[' . $key . ']';
                $flattenedArray[] = $fullKey;
            }
        }
        sort($flattenedArray);
        return $flattenedArray;
    }

    private static function _flattenUserKeys($keys, $namespace = null)
    {
       $flattenedArray = array();
       foreach($keys AS $key => $value) {
           $fullKey = empty($namespace) ? $key : $namespace . '[' . $key . ']';
           if(is_array($value)) {
               $more = self::_flattenUserKeys($value, $fullKey);
               $flattenedArray = array_merge($flattenedArray, $more);
           } else {
               $flattenedArray[] = $fullKey;
           }
       }
       sort($flattenedArray);
       return $flattenedArray;
    }

    /**
     * removes wildcard entries from the invalid keys array
     * @param array $validKeys
     * @param <array $invalidKeys
     * @return array
     */
    private static function _removeWildcardKeys($validKeys, $invalidKeys)
    {
        foreach($validKeys AS $key) {
            if (stristr($key, '[_anyKey_]')) {
                $wildcardKey = str_replace('[_anyKey_]', '', $key);
                foreach ($invalidKeys AS $index => $invalidKey) {
                    if (stristr($invalidKey, $wildcardKey)) {
                        unset($invalidKeys[$index]);
                    }
                }
            }
        }
        return $invalidKeys;
    }
}
?>
