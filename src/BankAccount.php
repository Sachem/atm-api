<?php namespace Src;

class BankAccount {
  
  private $balance;
  
  public function __construct($balance) 
  {
    $this->balance = $balance;
  }
  
  public function getBalance() 
  {
    return $this->balance;
  }
  
  public function withdrawMoney($amount) 
  {
    $this->balance -= $amount;
  }
}
