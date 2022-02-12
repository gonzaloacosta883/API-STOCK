# API-STOCK

Api rest para el manejo de stocks de productos en los diferentes depositos, se penso para que una aplicación de ventas que se usaria en el local consumiera la misma información que la tienda online, evitando la duplicidad de información y de stock.

###### Tecnologias empleadas
- Symfony 5.4
- Php 7.4
- NelmioApiDocBundle
- Composer

## Instalación
```bash
git clone https://github.com/gonzaloacosta883/API-STOCK.git
git checkout 0.1.0
composer install
yarn install
yarn encore dev
php bin/console doctrine:database:create
php bin/console doctrine:schema:create
symfony server:start
localhost:8000/api/doc
```
