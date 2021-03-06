# SMS Sender

## Installation
```bash
git clone `https://github.com/zartosht/sms-sender.git`
cd sms-sender
composer install
cp .env.example .env // make required changes to this file to meet your system configurations
```

## Requirements

* php 7.2.5+
* Mysql 5.7.32
* php7.4-redis
* redis 5.0-alpine
* composer

## Run
```bash
php -t 0.0.0.0:80 -t public/ // PROJECT
php bin/console messenger:consume async -vv // REDIS LISTENER
```

## Routes

### Send sms

[http://localhost:80/sms/send?number=NUMBER&body=BODY](http://localhost:80/sms/send?number=NUMBER&body=BODY)

### View sms status

[http://localhost:80/sms/status/ID](http://localhost:80/sms/status/ID)

### View all sms statuses

#### Parameters:

* from: date string (OPTIONAL)
* to: date string (OPTIONAL)
* status: integer (OPTIONAL)
    * 0 : queued
    * 1 : sending
    * 2 : sent
    * 3 : failed
* number: string (OPTIONAL) search whole number
* body: string (OPTIONAL) search for bodies like the input

[http://localhost:80/sms/status](http://localhost:80/sms/status)

### View sender success and failure ratings

#### Parameters:

* from: date string (OPTIONAL)
* to: date string (OPTIONAL)
* status: integer (OPTIONAL)
    * 0 : queued
    * 1 : sending
    * 2 : sent
    * 3 : failed

[http://localhost:80/sms/rate](http://localhost:80/sms/rate)

### View top numbers that we sent sms to (default limit is 10)

* from: date string (OPTIONAL)
* to: date string (OPTIONAL)
* status: integer (OPTIONAL)
    * 0 : queued
    * 1 : sending
    * 2 : sent
    * 3 : failed
* body: string (OPTIONAL) search for bodies like the input

[http://localhost:80/sms/top/{COUNT?}](http://localhost:80/sms/top/{COUNT?})