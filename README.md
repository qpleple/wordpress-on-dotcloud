Deploying Wordpress on dotcloud
===============================

The fastest way ever to deploy a Wordpress blog.

    curl -L https://github.com/qpleple/dotcloud-wordpress-deployment/tarball/master | tar xz
    cd qpleple-dotcloud-wordpress-deployment-*
    curl http://wordpress.org/latest.tar.gz | tar xz
    dotcloud create MY_BLOG
    dotcloud push MY_BLOG