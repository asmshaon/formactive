<?php
/**
 * Braintree ResourceCollection
 *
 * @package    Braintree
 * @subpackage Utility
 * @copyright  2010 Braintree Payment Solutions
 */

/**
 * ResourceCollection is a container object for result data
 *
 * stores and retrieves search results and aggregate data
 *
 * example:
 * <code>
 * $result = Braintree_Customer::all();
 *
 * foreach($result as $transaction) {
 *   print_r($transaction->id);
 * }
 * </code>
 *
 * @package    Braintree
 * @subpackage Utility
 * @copyright  2010 Braintree Payment Solutions
 */
class Braintree_ResourceCollection implements Iterator
{
    private $_index;
    private $_batchIndex;
    private $_items;
    private $_pageSize;
    private $_pager;

    /**
     * set up the resource collection
     *
     * expects an array of attributes with literal keys
     *
     * @param array $attributes
     * @param array $pagerAttribs
     */
    public function  __construct($response, $pager)
    {
        $this->_pageSize = $response["searchResults"]["pageSize"];
        $this->_ids = $response["searchResults"]["ids"];
        $this->_pager = $pager;
    }

    /**
     * returns the current item when iterating with foreach
     */
    public function current()
    {
        return $this->_items[$this->_index];
    }

    /**
     * returns the first item in the collection
     *
     * @return mixed
     */
    public function firstItem()
    {
        $ids = $this->_ids;
        $page = $this->_getPage(array($ids[0]));
        return $page[0];
    }

    public function key()
    {
        return null;
    }

    /**
     * advances to the next item in the collection when iterating with foreach
     */
    public function next()
    {
        ++$this->_index;
    }

    /**
     * rewinds thtestIterateOverResultse collection to the first item when iterating with foreach
     */
    public function rewind()
    {
        $this->_batchIndex = 0;
        $this->_getNextPage();
    }

    /**
     * returns whether the current item is valid when iterating with foreach
     */
    public function valid()
    {
        if ($this->_index == count($this->_items) && $this->_batchIndex < count($this->_ids)) {
            $this->_getNextPage();
        }

        if ($this->_index < count($this->_items)) {
            return true;
        } else {
            return false;
        }
    }

    public function maximumCount()
    {
        return count($this->_ids);
    }

    private function _getNextPage()
    {
        $this->_items = $this->_getPage(array_slice($this->_ids, $this->_batchIndex, $this->_pageSize));
        $this->_batchIndex += $this->_pageSize;
        $this->_index = 0;
    }

    /**
     * requests the next page of results for the collection
     *
     * @return none
     */
    private function _getPage($ids)
    {
        $className = $this->_pager['className'];
        $classMethod = $this->_pager['classMethod'];
        $methodArgs = array();
        foreach ($this->_pager['methodArgs'] as $arg) {
            array_push($methodArgs, $arg);
        }
        array_push($methodArgs, $ids);

        return call_user_func_array(
            array($className, $classMethod),
            $methodArgs
        );
    }
}
?>
