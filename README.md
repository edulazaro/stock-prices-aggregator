## Introduction

This app uses the [Alpha Vantage API Quote endpoint](https://www.alphavantage.co/documentation/#latestprice) to fetch the latest price for a fixed number of stocks. These stocks are defined on the `config/services.php` file, under the `alphavantage.stock_symbols` key.

The `stocks:update` artisan command is executed every minute, as defined on the `routes/console.php` file. The stock data is fetched and cached both per stock and also the full list.

The stock prices can be retrieved via the `/api/stock-prices` route, and also separately via the `/api/stock-prices/{SYMBOL}` route. For example, the `/api/stock-prices/IBM` route will retrieve the latest price for the IBM stock. The `percentage_change` field is the % of variation of the most recent price compared to the close price from yesterday.

There is also an interface displayed when logged in as a user, showing the most recent prices, the percentage of variation with green or red arrows and other stock data. This table is a livewire component which uses polling to fetch the data regularly.

## Running without Docker

To install the app:

1. Clone the repository.
2. Execute `composer install` to install the PHP dependencies.
3. Execute `npm install` to install JavaScript dependencies.
4. Copy the `.env.example` file, rename it as `.env` and add the database configuration values, as well as the `ALPHA_VANTAGE_API_KEY`.
5. Run the migrations with the command `php artisan migrate`.
6. To start with some data, execute the command `php artisan db:seed`

To run the app:

1. Execute the command `php artisan serve` to start the development. The app will start on the port **8000** by default.
2. Execute `npm run dev` to start Vite or `npm run build` in production. 

## Running with Docker

The `docker-compose` file starts three containers. One for nginx, another one for MySQL and anther one for the app, which uses the image created using the Dockerfile. If using more containers, another one could be added for memcached or Redis.

There are the steps to build the image and configure the project with Docker:

1. Clone the repository.
2. First build the `app` image with the command `docker-compose build app`.
3. Copy the `.env.docker.example` file, rename it as `.env.docker` and add the `ALPHA_VANTAGE_API_KEY`.

To run the app:

1. Start all the services defined with Docker Compose witht he command `docker-compose up -d`.
2. The app will be running on `http://localhost:7000/`. 

To run the migrations, execute these commands:

1. Run the migrations with the command `docker-compose exec app php artisan migrate`.
2. Run the seeders with the commad `docker-compose exec app php artisan db:seed`.


## Testing

To run the tests, run the command `php artisan test`.

## App Structure

### Database

The `stock_prices` table is used to store the stock prices, as the `GLOBAL_QUOTE` endpoint already includes the previous close price.

The StockPrice model includes the `getPercentageChangeAttribute` method, adding the `percentage_change` virtual attribute to the JSON responses and array conversions. This is tested via a unit test.

### Services:

* **AlphaVantageService**: This service includes a `request` method to perform requests to to the Alpha Vantage API. This API does not  return the correct error codes for the rate limit or for other errors, providing always a `200` code, so the errors need to be managed checking the JSON inside. The `getStockPrice` method uses the `request` method to fetch the data of the `GLOBAL_QUOTE` endpoint for the specified symbol. Because the API limits of the free version, the tests for this service mock the response.

### Commands:

* **UpdateStocksCommand**: This command uses the `AlphaVantageService` to fetch the latest price for the stocks defined in the configuration, printing the possible errors. The results are stored on the `stock_prices` table, creating the stock if it does not exist. To add a new stock, add it to the configured stocks.

### Repositories:

* **StockPriceRepository**: This repository handles the updates and the fetching of the data, handling also the caching, so it's managed in a consistent way for both the API and the interface. The data is cached for 60 seconds as specified on the requirements. However, this data could be cashed forever, as it's going to be refreshed each 60 seconds, when there is a new price.

### Observers:

* **StockPriceObserver**: This observer was added to cache each individual stock price with each insert or update.

## Frontend

The frontend of the app just uses a livewire component which is inserted into the default dashboard view.

This component shows the full list of stocks with the % of price variation compared to the last close price, with a green or red arrow.

This data is polled and refreshed each 30 seconds. As a possible improvement, a websocket server like Laravel Reverb or Pusher can be used in combination with Echo to trigger the data refresh from the backend, when a new updated price is available.

## Endpoints

* **/api/stock-prices**: Uses the `stockPriceRepository` to return all cached prices, retrieving from the database them if not cached.
* **/api/stock-prices/{SYMBOL}**: Uses the `stockPriceRepository` to return the cached price for a single symbol, getting it from the database if not cached.
