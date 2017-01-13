# Drupal 8 composer 12 factors project

drupal composer project
https://github.com/drupal-composer/drupal-project

twelve-factor-drupal
https://github.com/shomeya/twelve-factor-drupal

## Requirements for local development

* php7
* composer
* sqlite3

## Development

```
composer install
cp .env.example .env
# add credential to .env file
source .env
# go to webroot and install drupal site
cd web && drush si
drush rs /
```