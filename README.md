# Email-API-Service

### NGinx configuration changes

```# Specific location for email-api-service
location /email-api-service/ {
    # Route all requests to index.php
    try_files $uri $uri/ /email-api-service/index.php?$query_string;
    error_log /opt/homebrew/var/log/nginx/email-api-service-error.log debug;
}
```

### Cron setup

```
* * * * * php /path/to/MailQueueProcessor.php --worker-id=1 >> log1.log 2>&1
* * * * * php /path/to/MailQueueProcessor.php --worker-id=2 >> log2.log 2>&1
```

### cURL Snippet

#### With Parameters:

```
curl --location 'localhost/email-api-service/api/email' \
--header 'Content-Type: application/json' \
--data-raw '{
    "subject": "Test subject",
    "to": "jayy.shah16@gmail.com",
    "from": "jayy.shah16@gmail.com",
    "body": "Hi, {{first_name}} {{last_name}}, How are you doing today?",
    "parameters": {
        "first_name": "Jay",
        "last_name": "Shah"
    }
}'
```

#### Without Parameters:

```
curl --location 'localhost/email-api-service/api/email' \
--header 'Content-Type: application/json' \
--data-raw '{
    "subject": "Test subject",
    "to": "jayy.shah16@gmail.com",
    "from": "jayy.shah16@gmail.com",
    "body": "Hi Test, How are you doing today?"
}'
```

### Run PHP Unit Tests


```
./vendor/bin/phpunit tests                        
```
