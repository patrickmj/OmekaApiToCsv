OmekaApiToCsv
=============

Simple conversion tool for data from Omeka API to CSV file.

Based on [ApiImport](https://github.com/omeka/plugin-ApiImport)

Requirements
------------

PHP 5.4

Usage
-----

Slap this up on your server someplace.

Define an Omeka API endpoint in `OmekaApiToCsv.php`, then run the script. CSV export file will appear in the `files` folder.

Currently no authentication, so only public items will be exported.

Watch for timeouts -- your server settings might stop the script before it completes if you have a lot of items. If you can run it from the command line, you might have better luck.
