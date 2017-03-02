<?php
$arServers = [
    'vps108106.ovh.net' => [
        'host' => 'vps108106.ovh.net',
        'type' => 'HostUnix',
        'ssh' => [
            'login'       => 'monitor',
            'private_key' => 'data/deploy_rsa',
            'public_key'  => 'data/deploy_rsa.pub',
        ],
        'services' => [
            'uptime' => ['minimal' => 3600],
//            'load' => [],
            'mem' => ['mem_minimal' => '10', 'swap_minimal' => '95', 'need_swap' => false],
//            'disk' => [],
//            'disk-inode' => [],
        ],
    ],
    'vps364911.ovh.net' => [
        'host' => 'vps364911.ovh.net',
        'type' => 'HostUnix',
        'ssh' => [
            'login'       => 'monitor',
            'private_key' => 'data/deploy_rsa',
            'public_key'  => 'data/deploy_rsa.pub',
        ],
        'services' => [
            'uptime' => ['minimal' => 3600],
//            'load' => [],
            'mem' => ['mem_minimal' => '10', 'swap_minimal' => '95', 'need_swap' => false],
//            'disk' => [],
//            'disk-inode' => [],
        ],
    ],
];