<?php namespace Src\Money;

use Src\Money\MoneyContainer;

class MoneyDispenser {
  
  private $standardMoneyContainersQuantities = [
              '10' => 100,
              '20' => 100,
              '50' => 100,
          ];
  
  private $moneyContainers = [];
  private $nominals = [10,20,50];
  private $nominalsReversed = [50,20,10];
  
  private $suggestedNotes = [];
  
  public function __construct($moneyContainersQuantities = null)
  {
    if ($moneyContainersQuantities === null)
    {
      $moneyContainersQuantities = $this->standardMoneyContainersQuantities;
    }
      
    $this->replaceMoneyContainers($moneyContainersQuantities);
  }
  
  public function replaceMoneyContainers($moneyContainersQuantities)
  {
    $this->moneyContainers = [];
    $this->nominals = [];
    
    foreach ($moneyContainersQuantities as $nominal => $quantity)
    {
      $this->moneyContainers[$nominal] = new MoneyContainer($nominal, $quantity);
      $this->nominals[] = (int)$nominal;
    }
    
    $this->nominalsReversed = $this->nominals;
    rsort($this->nominalsReversed);
  }
  
  public function getNominals() 
  {
    return $this->nominals;
  }
  
  public function getMoneyContainers() 
  {
    return $this->moneyContainers;
  }
  
  public function findNotes($amount) 
  {
    $this->suggestedNotes = [];
    
    do
    {
      $note = $this->findLargestNoteFittingRemainingAmount($amount);
    
      if ($note === false)
      {
        break;
      }
      
      $this->addToSuggestedNotes($note);
      
      $amount -= $note;
    }
    while ($amount > 0);
    
    if ($amount > 0)
    {
      return ['error' => 'Sorry, there are no notes of £'.$amount.'. Please specify another amount.'];
    }
    
    return $this->suggestedNotes;
  }
  
  private function addToSuggestedNotes($note)
  {
    if (! array_key_exists($note, $this->suggestedNotes))
    {
      $this->suggestedNotes[$note] = 0;
    }
    
    $this->suggestedNotes[$note]++;
  }
  
  private function findLargestNoteFittingRemainingAmount($amount)
  {
    foreach ($this->nominalsReversed as $nominal)
    {
      if ($amount - $nominal >= 0 && $this->noteAvailable($nominal))
      {
        return $nominal;
      }
    }
    
    return false;
  }
  
  private function noteAvailable($nominal) 
  {
    return in_array($nominal, $this->nominals) && $this->moneyContainers[$nominal]->getQuantity() > 0;
  }
}
