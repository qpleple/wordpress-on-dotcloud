Deploying Wordpress on Dotcloud
===============================

First, get a copy of all the files of this package in the root of your Wordpress directory:

    cd wordpress
    git clone https://github.com/qpleple/wordpress-on-dotcloud
    mv wordpress-on-dotcloud/* .
    chmod +x postinstall
    
Then push to Dotcloud:

    dotcloud create myblog
    dotcloud push myblog
    dotcloud push myblog # dotcloud issue: database not ready at first push
    
Note that after the first push, remote ``wp-content/`` will not be overwritten by a new push:

- uploaded static files will be kept
- new local plugins and themes will be pushed remotely
- modifications to existing plugins and themes will be ignored remotely

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
    
3. The ``postinstall`` script that is a post-install hook and will be executed by Dotcloud after each push. It is calling the scripts in the ``dotcloud-scripts/`` directory:

    #!/bin/bash
    
    # Reads environment.json and config MySQL values in wp-config.php
    ./dotcloud-scripts/feed-wp-config.php
    
    # Move wp-content to make it persistent
    ./dotcloud-scripts/persist-wp-content.sh


4. The ``feed-wp-config.php`` script (executed by the post-install hook) that gets the parameters of the just created MySQL Dotcloud service, write them into the ``wp-config.php`` file and create the database if it does not exist. If ``wp-config.php`` does not exist, it will create it from ``wp-config-sample.php``.

    #!/bin/bash
    
    if [ -d ~/data/wp-content ]; then
        mv -n ~/current/wp-content/plugins/* ~/data/wp-content/plugins
        mv -n ~/current/wp-content/themes/* ~/data/wp-content/themes
        rm -rf ~/current/wp-content
    else
        mkdir -p ~/data/wp-content
        mv ~/current/wp-content ~/data
    fi

ln -s ~/data/wp-content ~/current/wp-content

5. The ``persist-wp-content.sh`` script (executed by the post-install hook) that persists the ``wp-content/`` directory containing uploads, installed plugins and themes. It moves the directory from ``~/code/wp-content`` it to ``~/data/wp-content`` and makes a symlink to it, because ``~/code`` will be overwritten at each push.

More about themes and plugins updates
=====================================
If it is not the first push, the ``persist-wp-content.sh`` executes:

    mv -n ~/current/wp-content/plugins/* ~/data/wp-content/plugins
    mv -n ~/current/wp-content/themes/* ~/data/wp-content/themes

The ``-n`` option means: do not overwrite an existing file. It means that plugins added in the local Wordpress will be added remotely. But if the remote Wordpress has already a plugin/theme, the local version of this plugin/theme will be ignored during the push (even if it has been modified on the local Wordpress).
