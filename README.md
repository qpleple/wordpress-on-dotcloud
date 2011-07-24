Deploying Wordpress on Dotcloud
===============================

The fastest way ever to deploy a Wordpress blog from localhost.

    curl -L https://github.com/qpleple/dotcloud-wordpress-deployment/tarball/master | tar xz
    cd qpleple-dotcloud-wordpress-deployment-*
    curl http://wordpress.org/latest.tar.gz | tar xz
    mv postinstall wordpress
    dotcloud create MY_BLOG
    dotcloud push MY_BLOG
    
After any modifications on localhost, update remote version with :

    dotcloud push MY_BLOG
    
Note that remote ``wp-content/`` will not be overwrite so uploaded static files will be kept and local plugins will not be installed remotely.