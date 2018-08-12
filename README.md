# Tweebo

This Project can share Twitter to Weibo

## Requirements

- PHP7+
- Composer

## Deployment Steps:

```bash
    composer install dg/twitter-php
    composer require xiaosier/libweibo:dev-master
    
    cp .env.example .env
    chmod 0777 storage/
    
    php sync.php
```
