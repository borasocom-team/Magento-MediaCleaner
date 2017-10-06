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
	if( Mage::getStoreConfig('mediacleaner/settings/modenabled') ) {

		$this->helper->cleanProductMedia();
        }
    }
}
