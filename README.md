RtlMtgCardBbCode
================

xenForo addon for M:TG-related custom BB Codes


Installation
-------------
- download the repository (or a tag) as a .zip (or tarball where available)
- unpack locally and place the following things in your xenForo installation's library (via SCP or ftp, etc):
  - the contents of the upload directory
- Login to xenForo's admin panel
- Select "BB Code Manager" from the far left menu
- Click "Import New BB Code"
- Upload the XML files one at a time
  - the XML files have the appropriate information, including help text and configuration
  - These also setup buttons if you choose to use them
- If you want to setup buttons:
  - click "Buttons Manager"
  - Using the Buttons Visual Configuration Management, you can drag in the various buttons to where you want
- hit "Install Add-on" and xenForo will take it from there


Features
--------------
- [ci] tag for card images from gatherer.com
  - [ci]your-card-name[/ci] is replaced with "http://gatherer.wizards.com/Handlers/Image.ashx?type=card&name=your-card-name" and is appropriately escaped
- [cubedeck] tag for a graphical layout of a deck using images
- [deck] tag for a textual layout of a deck with mouseover
- [c] mouseover card images



Changelog
--------------
- 0.2.0
-- refactor to instead provide button parsers for "BB Code Manager 1.3.4"
-- added cubedeck, deck, c tags
- 0.1.0
-- initial release, ci tag added as a completely standalone BB Code

