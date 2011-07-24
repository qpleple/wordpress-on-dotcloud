Deploying Wordpress on dotcloud
===============================

    curl http://wordpress.org/latest.zip | unzip
    cd wordpress
    git clone http://github.com/qpleple/dotcloud-wordpress-deployment
    dotcloud create MY_BLOG
    dotcloud push MY_BLOG