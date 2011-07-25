Deploying Wordpress on Dotcloud
===============================

The fastest way ever to deploy a Wordpress blog from localhost.

Copy all the files of this package in the root of your Wordpress directory and just push to Dotcloud.

    cd wordpress
    git clone https://github.com/qpleple/wordpress-on-dotcloud
    mv wordpress-on-dotcloud/* .
    dotcloud create MYBLOG
    dotcloud push MYBLOG

Due to a Dotcloud issue, you may have to push twice the first time : ``dotcloud push MYBLOG``.
    
Push local modifications to remote server with :

    dotcloud push MYBLOG
    
Note that remote ``wp-content/`` will not be overwrited. so uploaded static files will be kept and local plugins and themes will not be pushed remotely.

Under