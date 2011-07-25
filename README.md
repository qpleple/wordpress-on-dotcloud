Deploying Wordpress on Dotcloud
===============================

The fastest way ever to deploy a Wordpress blog from localhost. Copy all the files of this package in the root of your Wordpress directory and push to Dotcloud:

    cd wordpress
    git clone https://github.com/qpleple/wordpress-on-dotcloud
    mv wordpress-on-dotcloud/* .
    dotcloud create MYBLOG
    dotcloud push MYBLOG

Due to a Dotcloud issue, you may have to push twice the first time : ``dotcloud push MYBLOG``. Push local modifications to remote server with :

    dotcloud push MYBLOG
    
Note that remote ``wp-content/`` will not be overwrited. so uploaded static files will be kept and local plugins and themes will not be pushed remotely.

Under the hood
==============
The package contains:

1. The ``dotcloud.yml`` required to push to Dotcloud declaring 2 services:

        www:
            type: php
        db:
            type: mysql
        
2. The ``nginx.conf`` file telling Nginx to redirect everything to Wordpress front controller as Dotcloud does not support ``.htaccess`` files (included in Wordpress):

    try_files $uri $uri/ /index.php;
    
3. The ``postinstall`` script that is a post-install hook and will be executed by Dotcloud after each push. It is calling the scripts in the ``dotcloud-scripts/`` directory.

5. The ``persist-wp-content.sh`` (executed by the post-install hook) that will persist the ``wp-content/`` directory.