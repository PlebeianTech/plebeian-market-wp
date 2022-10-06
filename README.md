# Plebeian Market WordPress plugin

This is the plugin that you can use to display your auctions from [Plebeian Market](https://plebeian.market/) or your own instance of the market in your WordPress blog.

## Install instructions

You can search for the Plebeian Market plugin in your WordPress site (`Plugins / Add New`) to use the stable version. If you want to use the version in this repository (could be less stable), just compress the `plugin` directory into a `.zip` file and upload it to your WordPress installation using `Plugins / Add New`.

## Development environment

The development environment for the Plebeian Market WordPress plugin is based on `docker-compose`, so it's the only requirement to run it locally:

### Install the environment

* Download the files in this repository, or clone it:
```
git clone https://github.com/PlebeianTech/plebeian-market-wp-plugin
```

* Build the docker containers:
```
docker-compose up -d
```

* Install WordPress and enable the plugin:
```
docker-compose exec pm_wordpress_cli wp core install --url=http://localhost:8000 --title="Plebeian Market WordPress plugin test site" --admin_user=admin --admin_password=pass4admin --admin_email=test@example.com --skip-email

docker-compose exec pm_wordpress_cli wp plugin activate plebeian_market

docker-compose exec pm_wordpress_cli wp post update /var/www/html/assets/post1-demo.txt 1 --post_title="Testing Plebeian Market shortcodes"
```

## URLs:

* WordPress blog: http://localhost:8000/
* WordPress Plebeian Market demo page: http://localhost:8000/?p=1
* WordPress admin: http://localhost:8000/wp-admin
* PHPMyAdmin: http://localhost:8080/

## Usage of the development environment

### Start development environment

`docker-compose up -d`

### Stop development environment

`docker-compose stop`

### Destroy development environment

`docker-compose down`

### Destroy development environment and delete WordPress database

`docker-compose down -v`

## phpMyAdmin

You can visit http://localhost:8080 to access phpMyAdmin and manage your MariaDB database. Use `root` / `pass4root` to login.