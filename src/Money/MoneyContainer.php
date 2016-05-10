<?php namespace Src\Money;

class MoneyContainer {
  
  private $nominal;
  private $quantity;
  
  public function __construct($nominal, $quantity)
  {
    $this->nominal = $nominal;
    $this->quantity = $quantity;
  }
  
  public function getQuantity() 
  {
    return $this->quantity;
  }
  
}
