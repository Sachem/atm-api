<?php namespace Src\Output;

use Src\Output\Output;

class ScreenDisplay implements Output{
  
  public function printMessage($message) {
    echo $message;
  }

}
