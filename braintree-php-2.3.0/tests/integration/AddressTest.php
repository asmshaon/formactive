<?php
require_once realpath(dirname(__FILE__)) . '/../TestHelper.php';

class Braintree_AddressTest extends PHPUnit_Framework_TestCase
{
    function testCreate()
    {
        $customer = Braintree_Customer::createNoValidate();
        $result = Braintree_Address::create(array(
            'customerId' => $customer->id,
            'firstName' => 'Dan',
            'lastName' => 'Smith',
            'company' => 'Braintree',
            'streetAddress' => '1 E Main St',
            'extendedAddress' => 'Apt 1F',
            'locality' => 'Chicago',
            'region' => 'IL',
            'postalCode' => '60622',
            'countryName' => 'United States of America'
        ));
        $this->assertTrue($result->success);
        $address = $result->address;
        $this->assertEquals('Dan', $address->firstName);
        $this->assertEquals('Smith', $address->lastName);
        $this->assertEquals('Braintree', $address->company);
        $this->assertEquals('1 E Main St', $address->streetAddress);
        $this->assertEquals('Apt 1F', $address->extendedAddress);
        $this->assertEquals('Chicago', $address->locality);
        $this->assertEquals('IL', $address->region);
        $this->assertEquals('60622', $address->postalCode);
        $this->assertEquals('United States of America', $address->countryName);
    }

    function testCreate_withValidationErrors()
    {
        $customer = Braintree_Customer::createNoValidate();
        $result = Braintree_Address::create(array(
            'customerId' => $customer->id,
            'countryName' => 'Invalid States of America'
        ));
        $this->assertFalse($result->success);
        $countryErrors = $result->errors->forKey('address')->onAttribute('countryName');
        $this->assertEquals(Braintree_Error_Codes::ADDRESS_COUNTRY_NAME_IS_NOT_ACCEPTED, $countryErrors[0]->code);
    }

    function testCreateNoValidate()
    {
        $customer = Braintree_Customer::createNoValidate();
        $address = Braintree_Address::createNoValidate(array(
            'customerId' => $customer->id,
            'firstName' => 'Dan',
            'lastName' => 'Smith',
            'company' => 'Braintree',
            'streetAddress' => '1 E Main St',
            'extendedAddress' => 'Apt 1F',
            'locality' => 'Chicago',
            'region' => 'IL',
            'postalCode' => '60622',
            'countryName' => 'United States of America'
        ));
        $this->assertEquals('Dan', $address->firstName);
        $this->assertEquals('Smith', $address->lastName);
        $this->assertEquals('Braintree', $address->company);
        $this->assertEquals('1 E Main St', $address->streetAddress);
        $this->assertEquals('Apt 1F', $address->extendedAddress);
        $this->assertEquals('Chicago', $address->locality);
        $this->assertEquals('IL', $address->region);
        $this->assertEquals('60622', $address->postalCode);
        $this->assertEquals('United States of America', $address->countryName);
    }

    function testCreateNoValidate_withValidationErrors()
    {
        $customer = Braintree_Customer::createNoValidate();
        $this->setExpectedException('Braintree_Exception_ValidationsFailed');
        Braintree_Address::createNoValidate(array(
            'customerId' => $customer->id,
            'countryName' => 'Invalid States of America'
        ));
    }

    function testDelete()
    {
        $customer = Braintree_Customer::createNoValidate();
        $address = Braintree_Address::createNoValidate(array(
            'customerId' => $customer->id,
            'streetAddress' => '1 E Main St'
        ));
        Braintree_Address::find($customer->id, $address->id);
        Braintree_Address::delete($customer->id, $address->id);
        $this->setExpectedException('Braintree_Exception_NotFound');
        Braintree_Address::find($customer->id, $address->id);
    }

    function testFind()
    {
        $customer = Braintree_Customer::createNoValidate();
        $result = Braintree_Address::create(array(
            'customerId' => $customer->id,
            'firstName' => 'Dan',
            'lastName' => 'Smith',
            'company' => 'Braintree',
            'streetAddress' => '1 E Main St',
            'extendedAddress' => 'Apt 1F',
            'locality' => 'Chicago',
            'region' => 'IL',
            'postalCode' => '60622',
            'countryName' => 'United States of America'
        ));
        $this->assertTrue($result->success);
        $address = Braintree_Address::find($customer->id, $result->address->id);
        $this->assertEquals('Dan', $address->firstName);
        $this->assertEquals('Smith', $address->lastName);
        $this->assertEquals('Braintree', $address->company);
        $this->assertEquals('1 E Main St', $address->streetAddress);
        $this->assertEquals('Apt 1F', $address->extendedAddress);
        $this->assertEquals('Chicago', $address->locality);
        $this->assertEquals('IL', $address->region);
        $this->assertEquals('60622', $address->postalCode);
        $this->assertEquals('United States of America', $address->countryName);
    }

    function testFind_whenNotFound()
    {
        $customer = Braintree_Customer::createNoValidate();
        $this->setExpectedException('Braintree_Exception_NotFound');
        Braintree_Address::find($customer->id, 'does-not-exist');
    }

    function testUpdate()
    {
        $customer = Braintree_Customer::createNoValidate();
        $address = Braintree_Address::createNoValidate(array(
            'customerId' => $customer->id,
            'firstName' => 'Old First',
            'lastName' => 'Old Last',
            'company' => 'Old Company',
            'streetAddress' => '1 E Old St',
            'extendedAddress' => 'Apt Old',
            'locality' => 'Old Chicago',
            'region' => 'Old Region',
            'postalCode' => 'Old Postal',
            'countryName' => 'United States of America'
        ));
        $result = Braintree_Address::update($customer->id, $address->id, array(
            'firstName' => 'New First',
            'lastName' => 'New Last',
            'company' => 'New Company',
            'streetAddress' => '1 E New St',
            'extendedAddress' => 'Apt New',
            'locality' => 'New Chicago',
            'region' => 'New Region',
            'postalCode' => 'New Postal',
            'countryName' => 'Mexico'
        ));
        $this->assertTrue($result->success);
        $address = $result->address;
        $this->assertEquals('New First', $address->firstName);
        $this->assertEquals('New Last', $address->lastName);
        $this->assertEquals('New Company', $address->company);
        $this->assertEquals('1 E New St', $address->streetAddress);
        $this->assertEquals('Apt New', $address->extendedAddress);
        $this->assertEquals('New Chicago', $address->locality);
        $this->assertEquals('New Region', $address->region);
        $this->assertEquals('New Postal', $address->postalCode);
        $this->assertEquals('Mexico', $address->countryName);
    }

    function testUpdate_withValidationErrors()
    {
        $customer = Braintree_Customer::createNoValidate();
        $address = Braintree_Address::createNoValidate(array(
            'customerId' => $customer->id,
            'streetAddress' => '1 E Main St'
        ));
        $result = Braintree_Address::update(
            $customer->id,
            $address->id,
            array(
                'countryName' => 'Invalid States of America'
            )
        );
        $this->assertFalse($result->success);
        $countryErrors = $result->errors->forKey('address')->onAttribute('countryName');
        $this->assertEquals(Braintree_Error_Codes::ADDRESS_COUNTRY_NAME_IS_NOT_ACCEPTED, $countryErrors[0]->code);
    }

    function testUpdateNoValidate()
    {
        $customer = Braintree_Customer::createNoValidate();
        $createdAddress = Braintree_Address::createNoValidate(array(
            'customerId' => $customer->id,
            'firstName' => 'Old First',
            'lastName' => 'Old Last',
            'company' => 'Old Company',
            'streetAddress' => '1 E Old St',
            'extendedAddress' => 'Apt Old',
            'locality' => 'Old Chicago',
            'region' => 'Old Region',
            'postalCode' => 'Old Postal',
            'countryName' => 'United States of America'
        ));
        $address = Braintree_Address::updateNoValidate($customer->id, $createdAddress->id, array(
            'firstName' => 'New First',
            'lastName' => 'New Last',
            'company' => 'New Company',
            'streetAddress' => '1 E New St',
            'extendedAddress' => 'Apt New',
            'locality' => 'New Chicago',
            'region' => 'New Region',
            'postalCode' => 'New Postal',
            'countryName' => 'Mexico'
        ));
        $this->assertEquals('New First', $address->firstName);
        $this->assertEquals('New Last', $address->lastName);
        $this->assertEquals('New Company', $address->company);
        $this->assertEquals('1 E New St', $address->streetAddress);
        $this->assertEquals('Apt New', $address->extendedAddress);
        $this->assertEquals('New Chicago', $address->locality);
        $this->assertEquals('New Region', $address->region);
        $this->assertEquals('New Postal', $address->postalCode);
        $this->assertEquals('Mexico', $address->countryName);
    }
}
?>
