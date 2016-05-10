<?php

use Src\ATM;
use Src\CardReader;
use Src\PaymentCard\MastercardCard;
use Src\PaymentCard\VisaCard;
use Src\BankAccount;

class CardReaderTest  extends PHPUnit_Framework_TestCase {

  function setUp() {
    parent::setUp();
    
    $this->cardReader = new CardReader(['Mastercard', 'Maestro']);
  }
  
  function testAcceptsAllowedCardType() 
  {   
    $this->cardReader->insertCard(new MastercardCard(new BankAccount(0), '9999'));
    
    $this->assertTrue($this->cardReader->cardAccepted());
  }

  function testDoesntAcceptDisallowedCardType() 
  {   
    $this->cardReader->insertCard(new VisaCard(new BankAccount(0), '9999'));
    
    $this->assertFalse($this->cardReader->cardAccepted());
  }
}