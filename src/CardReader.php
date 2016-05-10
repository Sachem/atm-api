<?php namespace Src;

use Src\PaymentCard\PaymentCard;

class CardReader {
  
  private $acceptedCardTypes;
  private $insertedCard;
  
  public function __construct($accepted_card_types)
  {
    $this->acceptedCardTypes = $accepted_card_types;
  }
  
  public function insertCard(PaymentCard $card) 
  {
    $this->insertedCard = $card;
  }
  
  public function cardAccepted() 
  {
    return in_array($this->insertedCard->getType(), $this->acceptedCardTypes);
  }
}
