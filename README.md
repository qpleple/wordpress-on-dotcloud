Deploying Wordpress on Dotcloud
===============================

Copy all the files of this package in the root of your Wordpress directory and push to Dotcloud:

    cd wordpress
    git clone https://github.com/qpleple/wordpress-on-dotcloud
    mv wordpress-on-dotcloud/* .
    dotcloud create myblog
    dotcloud push myblog
    dotcloud push myblog # dotcloud issue: database not ready at first push

Then, push local modifications to remote server with :

    dotcloud push myblog
    
Note that remote ``wp-content/`` will not be overwritten. so uploaded static files will be kept and local plugins and themes will not be pushed remotely.

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

4. The ``feed-wp-config.php`` script (executed by the post-install hook) that gets the parameters of the just created MySQL Dotcloud service, write them into the ``wp-config.php`` file and create the database if it does not exist. If ``wp-config.php`` does not exist, it will create it from ``wp-config-sample.php``.

5. The ``persist-wp-content.sh`` script (executed by the post-install hook) that persists the ``wp-content/`` directory containing uploads, installed plugins and themes. It moves the directory from ``~/code/wp-content`` it to ``~/data/wp-content`` and makes a symlink to it, because ``~/code`` will be overwritten at each push.