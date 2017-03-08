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
            'load' => ['maximal' => [1.5, 1, 1]],
            'mem' => ['mem_minimal' => '10', 'swap_minimal' => '95', 'need_swap' => false],
            'disk' => [mounts=> ['/'], params => ['minimal' => 10]],
            'diskInode' => [mounts=> ['/'], params => ['minimal' => 10]],
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
            'load' => ['maximal' => [1, 1, 1]],
            'mem' => ['mem_minimal' => '10', 'swap_minimal' => '95', 'need_swap' => false],
            'disk' => [mounts=> ['/'], params => ['minimal' => 10]],
            'diskInode' => [mounts=> ['/'], params => ['minimal' => 10]],
        ],
    ],
];
