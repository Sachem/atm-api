<?php

use Src\ATM;
use Src\CardReader;
use Src\PaymentCard\MastercardCard;
use Src\PaymentCard\VisaCard;
use Src\Output\ScreenDisplay;
use Src\Input\Keyboard;
use Src\BankAccount;
use Src\Money\MoneyDispenser;

class AtmTest extends PHPUnit_Framework_TestCase {
 
  function setUp() 
  {
    parent::setUp();  
  }
      
  function testCanBeInstantiated() 
  { 
    $atm = new ATM(
                new CardReader(['Mastercard', 'Maestro']), 
                new ScreenDisplay(),
                new Keyboard(),
                new MoneyDispenser()
              );
    
    $this->assertInstanceOf('Src\ATM', $atm);
  }
  
  private function mockKeyboard() 
  {
    $mockKeyboard = \Mockery::mock('Src\Input\Keyboard');
        
    $mockKeyboard
            ->shouldReceive('takeCommand')
            ->once()
            ->with('enter pin')
            ->andReturn('1234');  
    
    return $mockKeyboard;
  }
  
  private function createAtmWithMockedKeyboard() // keyboard returns 1234 as PIN
  {
    return new ATM(
                new CardReader(['Mastercard', 'Maestro']), 
                new ScreenDisplay(),
                $this->mockKeyboard(),
                new MoneyDispenser()
              );
  }

  function testAuthorizeWithCorrectPin()
  {
    $atm = $this->createAtmWithMockedKeyboard();
    
    $atm->insertCard(new MastercardCard(new BankAccount(200), '1234'));
      
    $this->assertTrue($atm->verifyPin());
  }

  function testDoesntAuthorizeWithPinIncorrect()
  {
    $atm = $this->createAtmWithMockedKeyboard();
    
    $atm->insertCard(new MastercardCard(new BankAccount(200), '7654'));
      
    $this->assertFalse($atm->verifyPin());
  }

  /**
    * @expectedException Exception
    */
  function testVerifyPinWithoutCardInserted()
  {
    $atm = $this->createAtmWithMockedKeyboard();
     
    $atm->verifyPin();
  }


  function testCheckAccountBalance()
  {
    $atm = $this->createAtmWithMockedKeyboard();
    
    $atm->insertCard(new MastercardCard(new BankAccount(500), '1234'));
    $atm->verifyPin();
    
    $this->assertEquals(500, $atm->checkBalance());
  }

  /**
    * @expectedException Exception
    */
  function testCheckAccountBalanceWithoutCardInserted()
  {
    $atm = $this->createAtmWithMockedKeyboard();

    $atm->checkBalance();
  }
  
  /**
    * @expectedException Exception
    */
  function testCantWithdrawWithoutVerifyingPin() 
  {
    $atm = $this->createAtmWithMockedKeyboard();
    
    $atm->insertCard(new MastercardCard(new BankAccount(500), '1234'));
    
    $atm->withdrawMoney(10);
  }
  
  function testCantWithdrawWrongAmount() 
  {
    $atm = $this->createAtmWithMockedKeyboard();
    
    $atm->insertCard(new MastercardCard(new BankAccount(500), '1234'));
    
    $this->assertEquals(['error' => 'Wrong amount. Please choose a number being a multiplication of 10'], $atm->withdrawMoney(3.33));
  }
  
  function testInsufficientFunds() 
  {
    $atm = $this->createAtmWithMockedKeyboard();
    
    $atm->insertCard(new MastercardCard(new BankAccount(20), '1234'));
    $atm->verifyPin();
    
    $this->assertEquals(['error' => 'Insufficient funds'], $atm->withdrawMoney(30));
  }
  
  function testInsufficientFundsInAtm() 
  {
    $mockMoneyDispenser = \Mockery::mock('Src\Money\MoneyDispenser');
        
    $mockMoneyDispenser
            ->shouldReceive('findNotes')
            ->once()
            ->with(300)
            ->andReturn(false);  
    
    $atm = new ATM(
                new CardReader(['Mastercard', 'Maestro']), 
                new ScreenDisplay(),
                $this->mockKeyboard(),
                $mockMoneyDispenser
              );

    
    $atm->insertCard(new MastercardCard(new BankAccount(2000), '1234'));
    $atm->verifyPin();
    
    $this->assertEquals(['error' => 'Insufficient funds in ATM'], $atm->withdrawMoney(300));
  }
  
  function testCanWithdraw()
  {
    $mockMoneyDispenser = \Mockery::mock('Src\Money\MoneyDispenser');
        
    $mockMoneyDispenser
            ->shouldReceive('findNotes')
            ->once()
            ->with(300)
            ->andReturn(['50' => 6]);  
    
    $atm = new ATM(
                new CardReader(['Mastercard', 'Maestro']), 
                new ScreenDisplay(),
                $this->mockKeyboard(),
                $mockMoneyDispenser
              );
    
    $atm->insertCard(new MastercardCard(new BankAccount(2000), '1234'));
    $atm->verifyPin();
    
    $this->assertEquals(['50' => 6], $atm->withdrawMoney(300));
  }
  
//  function testCanReplaceMoneyContainers() 
//  {
//    $atm = $this->createAtmWithMockedKeyboard();
//    $atm->replaceMoneyContainers(['10' => 50, '50' => 20]);
//    
//    $this->assertEquals([10,50], $atm->moneyDispencer->get)
//  }
  
}