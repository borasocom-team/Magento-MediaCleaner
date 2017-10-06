# Magento-MediaCleaner
This Magento1 module deletes media files left on disk but unreferenced in DB.

The module defaults to "active and scheduled" (works out-of-the-box) but in "dry run mode":

1. Run it as `n98-magerun.phar sys:cron:run boraso_mediacleaner`
1. Check the dryrun log in `magento/var/log/boraso_mediacleaner.log`
1. Disable dryrun in `Admin -> System -> Configuration -> Media Cleaner`

