Magento HipChat
=============

Magento HipChat allows you to send notifications to a HipChat room. 

Facts
-----
- version: 0.3.0
- extension key: magento-hipchat
- [extension on GitHub](https://github.com/mhauri/magento-hipchat)
- [direct download link](https://github.com/mhauri/magento-hipchat/archive/master.zip)

Description
-----------
This extension currently uses Version 1 of the HipChat API.

For more information check the [API Documentation](https://www.hipchat.com/docs/api)
It is recommended to use the message queue. A [configured CronJob](http://www.magentocommerce.com/wiki/1_-_installation_and_configuration/how_to_setup_a_cron_job) is a precondition for the message queue.

**Available Notifications**

 - Admin User Login Failed
 - New Customer Account Created
 - New Orders

Requirements
------------
- PHP >= 5.3.0

Compatibility
-------------
- Magento >= 1.8

Installation Instructions
-------------------------
1. Install the extension via Composer or copy all the files into your document root.
2. Clear the cache, logout from the admin panel and then login again.

Uninstallation
--------------
1. Remove all extension files from your Magento installation.

Support
-------
If you have any issues with this extension, open an issue on [GitHub](https://github.com/mhauri/magento-hipchat/issues).

Contribution
------------
Any contribution is highly appreciated. The best way to contribute code is to open a [pull request on GitHub](https://help.github.com/articles/using-pull-requests).

Developer
---------
Marcel Hauri and all other [contributors](https://github.com/mhauri/magento-hipchat/contributors)

License
-------
Magento HipChat is licensed under the MIT License - see the LICENSE file for details

Copyright
---------
(c) 2015, Marcel Hauri