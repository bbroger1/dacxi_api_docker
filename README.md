## Technical Test for Backend Development Team dacxi

## The Task

Create an application that stores in a database the price history of Bitcoin over time. The
application must provider 2 main endpoints:
- An endpoint that returns the most recent Bitcoin price.
- An endpoint that receives a date and a time and returns the estimated Bitcoin price in that
datetime

## The Application

### Technologies
- PHP 8 or higher
- Composer
- Docker
- DBMS MySQL

### Orientations

- Docker started
- run `docker-compose build app`
- run `docker-compose up -d` to start application in docker.
- open the application bash `docker-compose exec app bash`
- to install all dependencies run `composer install`
- run `cp .env.example .env` (Don't need setting database credentials)
- run `php artisan key:generate` (PS: maybe the application show a Permission Error after create .env file.
- now you should create coin table running `php artisan migrate`

Now you can see application running on `http://localhost` .

### Endpoints

- `/api/coin`
    - Method: POST
    - Params:
        - coin_id (accepted coins - 'bitcoin', 'ethereum', 'dacxi', 'cosmos', 'terra-luna')
        - currency (accepted currency - 'usd', 'eur', 'brl')
    - Note: The api is prepared to handle uppercase and lowercase letters

    - API return example:
    
    ![retorno_coin](https://user-images.githubusercontent.com/62220624/176809139-7a744af1-5fb4-4de0-b813-dc9190b2c1c5.PNG)
    
- `/api/coin/price-estimated-date`
    - Method: POST
    - Params:
        - coin_id (accepted coins - 'bitcoin', 'ethereum', 'dacxi', 'cosmos', 'terra-luna')
        - datetime (format AAAA-mm-dd)
        - currency (accepted currency - 'usd', 'eur', 'brl')
    - Note: The api is prepared to handle uppercase and lowercase letters

    - API return example:
    
    ![retorno_estimated_price](https://user-images.githubusercontent.com/62220624/176809941-39d0971b-ad32-4b4e-9933-9f602e840ac4.PNG)

## Link application: https://dacxi-api-docker.herokuapp.com/
