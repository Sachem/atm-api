<?php

use Src\Money\MoneyDispenser;
use Src\Money\MoneyContainer;

class MoneyDispenserTest extends PHPUnit_Framework_TestCase {
  
  function testCreateWithStandardMoneyContainers() 
  {
    $dispenser = new MoneyDispenser();
    
    $expectedMoneyContainers = [
              '10' => new MoneyContainer(10, 100),
              '20' => new MoneyContainer(20, 100),
              '50' => new MoneyContainer(50, 100),
          ];
    
    $this->assertEquals([10,20,50], $dispenser->getNominals());
    $this->assertEquals($expectedMoneyContainers, $dispenser->getMoneyContainers());
  }
  
  function testCreateWithCustomMoneyContainers() 
  {
    $dispenser = new MoneyDispenser([
                    '10' => 100,
                    '20' => 100,
                  ]);
    
    $expectedMoneyContainers = [
                  '10' => new MoneyContainer(10, 100),
                  '20' => new MoneyContainer(20, 100),
              ];
    
    $this->assertEquals([10,20], $dispenser->getNominals());
    $this->assertEquals($expectedMoneyContainers, $dispenser->getMoneyContainers());
  }
    
  function testFindNotes() 
  {
    $dispenser = new MoneyDispenser([
                    '10' => 0,
                    '20' => 100,
                  ]);
    
    $this->assertEquals(['20' => 5], $dispenser->findNotes(100));
  }
  
  function testCantFindTennerWhenMinimalNoteIsTwenty() 
  {
    $dispenser = new MoneyDispenser([
                    '10' => 0,
                    '20' => 100,
                  ]);
    
    $this->assertEquals(['error' => 'Sorry, there are no notes of £10. Please specify another amount.'], $dispenser->findNotes(10));
  }

}
