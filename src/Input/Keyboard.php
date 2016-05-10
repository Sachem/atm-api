<?php namespace Src\Input;

use Src\Input\Input;

class Keyboard implements Input{
  
  private $command;
  
  public function takeCommand($command) {
    return 'some input entered by the user'; // TODO: should be real input!
  }

}
