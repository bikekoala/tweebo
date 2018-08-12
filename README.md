# Tweebo

This Project can share Twitter to Weibo

## Requirements

- PHP7+
- Composer

## Deployment Steps:

1. Build Project
```bash
    git clone https://github.com/popfeng/tweebo.git
    cd tweebo
    composer install dg/twitter-php
    composer require xiaosier/libweibo:dev-master
    cp .env.example .env
    chmod 0777 storage
```

2. Add the script to crontab
```bash
    0 * * * * /usr/bin/php /home/popfeng/dev/tweebo/sync.php >/tmp/tweebo.log 2>&1
    systemctl restart crontab
```
