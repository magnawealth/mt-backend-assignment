## Tech Stack

* CodeIgniter4
* Mysql
* PHP

## Download Instruction

1. Clone the project.

```
git clone https://github.com/magnawealth/mt-backend-assignment.git projectname
```


2. Install dependencies via composer.

```
composer install 
```

3. Database Configuration.

```
php artisan migrate --seed
```

4. Run php server.

```
php spark serve
```


## Api Usage

### Base URL
```
http://localhost:8080
```

#### List All Record:

```
http://localhost:8080/api/vesseltrack
```

## Filter Conditions

### By MMSI

##### Single MMSI

```phpregexp
http://localhost:8080/api/vesseltrack/filter?mmsi=247039300
```

##### Multiple MMSI

```phpregexp
http://localhost:8080/api/vesseltrack/filter?mmsi=311040700,247039300
```

### By Time Inverter

```phpregexp
http://localhost:8080/api/vesseltrack/filter?startTime={startTime}&endTime={endTime}
http://localhost:8080/api/vesseltrack/filter?startTime=2013-07-01T13:06:00&endTime=2013-07-01T10:06:00
```

### By Longitude And Latitude

```phpregexp
http://localhost:8080/api/vesseltrack/filter?lat={latitude}&lon={longitude}
http://localhost:8080/api/vesseltrack/filter?lat=33.5577600&lon=34.6411200
```

You can also do

```phpregexp
http://localhost:8080/api/vesseltrack/filter?lon={longitude}&lat={latitude}
http://localhost:8080/api/vesseltrack/filter?lon=34.6411200&lat=33.5577600
```

### Post JSON Data (in Request Body)

You can post json data via request body with the accepted content types:
* application/json
* application/vnd.api+json
* application/ld+json
* application/hal+json
* application/xml
* text/csv

```phpregexp
http://localhost:8080/api/vesseltrack/postData
```

### Upload JSON Data (file.json)

it  accepts only json files for upload with formats:
* application/json
* application/vnd.api+json
* application/ld+json
* application/hal+json
* application/xml
* text/csv

```phpregexp
http://localhost:8080/api/vesseltrack/uploadData
```


Enjoy!

