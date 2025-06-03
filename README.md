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


```# Specific location for email-api-service
* * * * * php /path/to/MailQueueProcessor.php --worker-id=1 >> log1.log 2>&1
* * * * * php /path/to/MailQueueProcessor.php --worker-id=2 >> log2.log 2>&1
```