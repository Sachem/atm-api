<?php namespace Src;

use Src\Output\Output;
use Src\Input\Input;
use Src\PaymentCard\PaymentCard;
use Src\Money\MoneyDispenser;

class ATM {
  
  private $cardReader;
  private $output;
  private $input;
  private $moneyDispenser;
  private $bankAccount = null;
  private $insertedCard = null;
  
  public function __construct(CardReader $cardReader, Output $output, Input $input, MoneyDispenser $moneyDispenser) 
  {
    $this->cardReader = $cardReader;
    $this->output = $output;
    $this->input = $input;
    $this->moneyDispenser = $moneyDispenser;
  }
  
  public function insertCard(PaymentCard $card) 
  {
    $this->cardReader->insertCard($card);

    if ($this->cardReader->cardAccepted())
    {
      $this->insertedCard = $card;
    }
  }
  
  public function verifyPin()
  {
    if ($this->insertedCard === null)
    {
      throw new \Exception('No card inserted');
    }
    
    $enteredPin = $this->input->takeCommand('enter pin');

    if ($enteredPin == $this->insertedCard->getPin())
    {
      $this->bindBankAccountToSession();
      return true;
    }
    
    return false;
  }
  
  private function bindBankAccountToSession()
  {
    $this->bankAccount = $this->insertedCard->getRelatedBankAccount();
  }
  
  public function checkBalance() 
  {
    if ($this->bankAccount === null)
    {
      throw new \Exception('No bank account bond');
    }
    
    return $this->bankAccount->getBalance();
  }
  
  public function withdrawMoney($amount) 
  {
    if (
      $amount != (int)$amount
      OR $amount < 10      
      OR ($amount / 10) != (int)($amount / 10)
    )
    {
      return ['error' => 'Wrong amount. Please choose a number being a multiplication of 10']; // amount has to be positive multiplication of 10
    }
    
    if ($this->bankAccount === null)
    {
      throw new \Exception('No bank account bond');
    }
    
    if ($this->bankAccount->getBalance() < $amount)
    {
      return ['error' => 'Insufficient funds'];
    }
    
    $notes = $this->moneyDispenser->findNotes($amount);
    
    if ($notes === false)
    {
      return ['error' => 'Insufficient funds in ATM'];
    }
    
    $this->bankAccount->withdrawMoney($amount);
    
    return $notes;
  }
  
//  public function replaceMoneyContainers($moneyContainers) 
//  {
//    $this->moneyDispenser->replaceMoneyContainers($moneyContainers);
//  }
}
