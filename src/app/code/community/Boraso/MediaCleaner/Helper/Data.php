<?php

class Boraso_MediaCleaner_Helper_Data extends Mage_Core_Helper_Abstract
{
    protected $db;

    public function cleanProductMedia()
    {
        $this->log("##### SECTION: cleanProductMedia() #####", "=");

        //
        $mediaPath              = realpath( Mage::getBaseDir('media') ) . "/";  // Eg: /var/www/linkspa/shop/media/
        $mediaPathProduct       = $mediaPath . "catalog/product/";              // Eg: /var/www/linkspa/shop/media/catalog/product/
        $mediaPathProductCache  = $mediaPathProduct . "cache/";                 // Eg: /var/www/linkspa/shop/media/catalog/product/cache/

        //
        $sqlSelectImages    = "SELECT DISTINCT `value` FROM catalog_product_entity_media_gallery WHERE `value` IS NOT NULL OR `value` != ''";
        $rsImages           = $this->dbSelect($sqlSelectImages);

        //
        $arrReport         = array( 'deleted' => array(), 'kept' => array(), 'skipped' => array(), "err" => array() );

        foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($mediaPathProduct)) as $filename)
        {
            // /cache/ directory shouldn't be processed
            // dot and dotdot files  shouldn't be processed
            if( strpos($filename, $mediaPathProductCache) === 0 || substr($filename,-2) == "/."  || substr($filename,-3) == "/..") {

                //$this->log("Skipped (cache or dot file)");
                $arrReport['skipped'][] = $filename;
                continue;
            }

            //
            $this->log("### Processing a file", "-");
            $this->log("#" . $filename . "#");

            //
            $relativeFilename = "/" . str_ireplace($mediaPathProduct, '', $filename);
            $this->log("Checking usage of #" . $relativeFilename . "#");


            if( in_array($relativeFilename, $rsImages) ) {

                $this->log("File in use, not deleting");
                $arrReport['kept'][] = $filename;

            } else {

                $this->log("File NOT in use! Must be deleted");

                $esito = $this->deleteFile($filename);

                if(empty($esito)) {

                    $this->log("Success: file deleted");
                    $arrReport['deleted'][] = $filename;

                } else {

                    $this->log("Warning: file NOT deleted");
                    $this->log($esito);
                    $arrReport['err'][] = $filename;
                }
            }
        }

        $this->log("### Report", "*");

        foreach($arrReport as $key => $val) {

            $this->log( ucfirst($key) . ": " . count($val));
        }

        return $arrReport;
    }


    protected function getDb()
    {
        if(empty($this->db)) {

            $this->db = Mage::getSingleton('core/resource')->getConnection('core_read');
        }

        return $this->db;
    }


    protected function dbSelect( $sqlSelect, $args = array(), $fetchAll = true, $fetchStyle = PDO::FETCH_COLUMN)
    {
        $stmt   = $this->getDb()->query($sqlSelect);
        $rs     = $fetchAll ? $stmt->fetchAll($fetchStyle) : $stmt->fetch($fetchStyle);

        return $rs;
    }


    protected function deleteFile($file)
    {
        if( Mage::getStoreConfig('mediacleaner/settings/dryrun') ) {

            return "Dry run is active";
        }

        //
        $esito = @unlink($file);

        if($esito) {

            return null;

        } else {

            $message    = error_get_last()["message"];
            $message    = "!!! CRITICAL ERROR - Unable to delete: " . $message;
            return $message;
        }
    }


    protected function log($message, $titleSeparator = false)
    {
        if($titleSeparator) {

            Mage::log("", null, 'boraso_mediacleaner.log');
        }


        Mage::log($message, null, 'boraso_mediacleaner.log');

        if($titleSeparator) {

            Mage::log( str_repeat($titleSeparator, mb_strlen($message)), null, 'boraso_mediacleaner.log' );
        }
    }
}
