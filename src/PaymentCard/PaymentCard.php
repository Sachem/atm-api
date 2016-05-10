<?php namespace Src\PaymentCard;

use Src\BankAccount;

abstract class PaymentCard {
  
  protected $type;
  private $relatedBankAccount;
  private $pin;
  
  public function __construct(BankAccount $account, $pin) 
  {
    $this->relatedBankAccount = $account;
    $this->pin = $pin;
  }
  
  public function getType() {
    return $this->type;
  }
  
  public function getPin() {
    return $this->pin;
  }
  
  public function getRelatedBankAccount() {
    return $this->relatedBankAccount;
  }
  
}
