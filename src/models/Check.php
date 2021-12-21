<?php

namespace jordanbeattie\CraftCmsHealth\models;

class Check extends \craft\base\Model
{
    
    private $passed, $title, $text, $applicable;
    
    public function __construct( string $title, bool $passed, string $text = null )
    {
        $this->title = $title;
        $this->passed = $passed;
        $this->text = $text;
        $this->applicable = true;
    }
    
    public function passed()
    {
        return $this->passed;
    }
    
    public function failed()
    {
        return !$this->passed;
    }
    
    public function getIconPath()
    {
        return 'health/icons/' . ( $this->passed() ? 'check' : 'times');
    }
    
    public function getText()
    {
        return $this->text;
    }
    
    public function getTitle()
    {
        return $this->title;
    }
    
    public function setNotApplicable()
    {
        $this->applicable = false;
        $this->text = "n/a";
    }
    
    public function isApplicable()
    {
        return $this->applicable;
    }
    
}
