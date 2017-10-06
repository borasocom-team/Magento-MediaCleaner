<?php

class Boraso_MediaCleaner_Model_Observer
{
    protected $helper;

    public function __construct()
    {
        $this->helper = Mage::helper("boraso_mediacleaner");
    }


    public function run()
    {
        $this->helper->cleanProductMedia();
    }


}
