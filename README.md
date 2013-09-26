RtlMtgCardBbCode
================

xenForo addon for M:TG-related custom BB Codes


Installation
-------------
- download the repository (or a tag) as a .zip (or tarball where available)
- unpack locally and place the following things in your xenForo installation's library (via SCP or ftp, etc):
  - addon-RtlMtgCardBbCode.xml
  - the RtlMtgCardBbCode directory
- in your xenForo's admin console, click "Install Add-On" and in the "Install from file on server" field, enter:
  - library/addon-RtlMtgCardBbCode.xml
- hit "Install Add-on" and xenForo will take it from there


Features
--------------
- [ci] tag for card images from gatherer.com
  - added in 0.1.0
  - [ci]your-card-name[/ci] is replaced with "http://gatherer.wizards.com/Handlers/Image.ashx?type=card&name=your-card-name" and is appropriately escaped
