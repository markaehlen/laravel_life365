#!/bin/sh
php artisan cache:clear
php artisan view:clear
rm storage/framework/views/*.php
rm storage/framework/sessions/*
php artisan config:clear
php artisan config:cache
chown -R apache.life365 *
chmod -R ug+r *
