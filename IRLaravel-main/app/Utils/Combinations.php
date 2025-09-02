<?php

namespace App\Utils;

class Combinations {
    private $results;
    private $arrs;        

    public function __construct() {
        $this->results = array();
        $this->arrs = array();
    }
    
    public function init($inputArrs) { 
        $this->arrs = $inputArrs;
        $this->generate($this->arrs);
        
        return $this->results;
    }
    
    public function generate($root, $input = [], $index = 0) {
        if (!$index) {
            foreach($root[$index] as $item) {
                $input[] = array($item);
            }
        }

        $output = array();
        $indexNext = $index + 1;
        
        if($index >= 0 && $indexNext < count($root)) {
            foreach($input as $key => $item1) {
                foreach($root[$indexNext] as $item2) {  
                    $temp = $item1;
                    $temp[] = $item2;
                    $output[] = $temp;
                }   
            }
            
            $this->generate($root, $output, $indexNext);
        } else {
            $this->results = $input;
        }
    }
}
