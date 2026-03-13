<?php

/**
 * All directives are explained in documentation:
 * https://docs.phpmyadmin.net/en/latest/config.html
 */

// === BASIC SETTINGS ===
$cfg['blowfish_secret'] = 'your_random_32_char_string_here__'; // Change this!

// === SERVERS CONFIGURATION ===
$i = 0;

// Server: db (from docker-compose)
$i++;
$cfg['Servers'][$i]['verbose'] = 'MariaDB';
$cfg['Servers'][$i]['host'] = getenv('PMA_HOST') ?: 'db';
$cfg['Servers'][$i]['port'] = 3306;
$cfg['Servers'][$i]['socket'] = '';
$cfg['Servers'][$i]['auth_type'] = 'config';
$cfg['Servers'][$i]['user'] = getenv('PMA_USER') ?: 'rmm_user';
$cfg['Servers'][$i]['password'] = getenv('PMA_PASSWORD') ?: 'rmm_pass';
$cfg['Servers'][$i]['AllowNoPassword'] = false;
$cfg['Servers'][$i]['AllowRoot'] = false;

$cfg['UploadDir'] = '';
$cfg['SaveDir'] = '';

$cfg['LoginCookieValidity'] = 1440;
$cfg['MaxRows'] = 50;
$cfg['SendErrorReports'] = 'never';
$cfg['TempDir'] = '/var/www/html/tmp';

$cfg['MaxDbList'] = 100;
$cfg['MaxTableList'] = 250;
$cfg['ShowPhpInfo'] = false;
$cfg['ShowChgPassword'] = false;
$cfg['AllowUserDropDatabase'] = false;
