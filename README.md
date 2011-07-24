Deploying Wordpress on dotcloud
===============================

The fastest way ever to deploy a Wordpress blog.

    curl http://wordpress.org/latest.tar.gz | tar xz
    cd wordpress
    git clone http://github.com/qpleple/dotcloud-wordpress-deployment
    dotcloud create MY_BLOG
    dotcloud push MY_BLOG