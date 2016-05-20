<?php

/**
 * Copyright (c) 2013 Will Hattingh (https://github.com/Nitecon
 *
 * For the full copyright and license information, please view
 * the file LICENSE.txt that was distributed with this source code.
 * 
 * @author Will Hattingh <w.hattingh@nitecon.com>
 *
 * 
 */
return array(
    'zf2-db-session'=>array(
        'sessionConfig' => array(
//            'cache_expire' => 86400,
//            'cookie_domain' => 'localhost',
//            'name' => 'localhost',
//            'cookie_lifetime' => 180,
            'gc_maxlifetime' => 1800,
            'cookie_path' => '/',
            'cookie_secure' => TRUE,
            'remember_me_seconds' => 3600,
            'use_cookies' => true,
        ),
        
        'serviceConfig'=>array(
            'base64Encode'=>false
        )
    )
);
