<?php

use Symfony\Component\DependencyInjection\Container;

require_once dirname(__DIR__).'/vendor/autoload.php';

$bundle = new \Mink67\MultiPartDeserialize\MultiPartDeserializeBundle;

$cont = new Container;

//var_dump($cont);