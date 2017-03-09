# PhpMonit

Small Monitoring system write in PHP (Just to monitor some hosts, not a lot)

It can check on Unix host :
  * Uptime
  * Load Usage
  * Memory Usage
  * Swap Usage
  * Disk Usage
  * Disk Inode Usage

## Installation

Copy source into a directory : `cd /home/http/PhpMonit`

Use Composer to install PHP libraries : `composer.phar install`

Prepare configuration : `cp data/server.php.sample data/server.php`

Edit Configuration : `vim data/server.php`

Add `script/check.php` in crontab (start it each 5 mins)

## Configuration 

The configuration is stored in a PHP Array. 
Each item of the main array define a host to check.
```php
$arServers = [
    'server name' => [
        'host' => 'hostname',
        'type' => 'HostUnix', // The only suported host type
        'ssh' => [ // SSH info to connect to the server (ssh key only)
            'login'       => 'monitor',  
            'private_key' => 'data/deploy_rsa',
            'public_key'  => 'data/deploy_rsa.pub',
        ],
        'services' => [ // list of activated services
            'uptime' => ['minimal' => 3600],
            'load' => ['maximal' => [1.5, 1, 1]],
            'mem' => ['mem_minimal' => '10', 'swap_minimal' => '95', 'need_swap' => false],
            'disk' => [mounts=> ['/'], params => ['minimal' => 10]],
            'diskInode' => [mounts=> ['/'], params => ['minimal' => 10]],
        ],
    ]
];

```

## List of services

### uptime
### load
### mem
### disk
### diskInode

## TODO

 * Adding Windows host using protocol winRM
 * Adapting checks for Windows hosts (already looking for that ... ;) )
 * Adding some other checks 
 * Adding an interface to manage servers' list
 
