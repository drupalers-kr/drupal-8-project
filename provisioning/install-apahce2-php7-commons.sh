# Add PPA for php7.0
sudo LC_ALL=C.UTF-8 add-apt-repository -y ppa:ondrej/php && sudo apt -qq update

# Install git, mariadb client, openssl
sudo apt -y -qq install git mariadb-client openssl

# Install PHP, yaml, apcu, uploadprogress
# php-uploadprogress 7.0만 지원
sudo apt -y -qq install php7.0 php7.0-curl php7.0-xml php7.0-gd php7.0-mbstring php7.0-mysql php7.0-zip php-yaml php-apcu php-uploadprogress
# module install on UI => php-ssh2
# redis => php-redis
# drush qd => sqlite3 php7.0-sqlite

# Set php configurations
sudo sed -i "s/^;date.timezone =$/date.timezone = \"Asia\/Seoul\"/" /etc/php/7.0/{cli,apache2}/php.ini
# grep -nw /etc/php/7.0/{cli,apache2}/php.ini -e 'date.timezone'
phpmemory_limit=512M
sudo sed -i 's/memory_limit = .*/memory_limit = '${phpmemory_limit}'/' /etc/php/7.0/{cli,apache2}/php.ini

# install drush
php -r "readfile('https://s3.amazonaws.com/files.drush.org/drush.phar');" > drush.phar && chmod a+x drush.phar && sudo mv drush.phar /usr/local/bin/ && sudo ln -s /usr/local/bin/drush.phar /usr/local/bin/drush

# install composer
curl -sS https://getcomposer.org/installer | sudo php -- --install-dir=/usr/local/bin --filename=composer

# install drupal console
# curl https://drupalconsole.com/installer -L -o drupal.phar && sudo mv drupal.phar /usr/local/bin/drupal && sudo chmod +x /usr/local/bin/drupal && drupal init --override && drupal check

# get ready
cd /vagrant && composer install --no-dev && cp .env.example .env
