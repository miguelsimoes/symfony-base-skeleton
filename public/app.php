<?php

/*
 * This file is part of Miguel Simões generic packages.
 *
 * (c) Miguel Simões <msimoes@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

use Symfony\Component\Dotenv\Dotenv;
use Symfony\Component\HttpFoundation\Request;
#
# Retrieve the autoloader to be used with the current execution
$loader = require __DIR__ .'/../vendor/autoload.php';
#
# Initialize the command line execution by retrieving the information from the
# provided arguments and/or environment configuration
if (false === getenv('APPLICATION_ENVIRONMENT') || empty(getenv('APPLICATION_ENVIRONMENT'))) {
    /* We do not have the environment available, we will need to retrieve it from the configuration */
    (new Dotenv())->load(__DIR__.'/../.env');
}

$environment = getenv('APPLICATION_ENVIRONMENT') ?: 'prod';
$debug       = "0" !== getenv('APPLICATION_DEBUG') && 'prod' !== $environment;
#
# When under the production environment, we want to improve the application
# by using all the possible caches available but only when using versions under
# 7.0.0 of PHP
if (PHP_VERSION_ID < 70000 && 'prod' === $environment) {
    /* Since we have a version higher then 7.0.0 we will not use the bootstrap */
    require __DIR__.'/../var/bootstrap.php.cache';
}
#
# We will bootstrap the kernel right away since we may need to wrap it on the
# HttpCache layer of symfony
$kernel = new ApplicationKernel($environment, true);
#
# We will also force the kernel to load all the caches available
# when using versions under 7.0.0 of PHP
if (PHP_VERSION_ID < 70000 && 'prod' === $environment) {
    /* Since we have a version higher then 7.0.0 we will not use the bootstrap */
    $kernel->loadClassCache();
}
#
# We will now initialize the HTTP cache component to improve the performance of
# the application when on production environment
if ('prod' === $environment) {
    /* We are under the production environment, so we will use the application cache */
    $kernel = new ApplicationCache($kernel);
}
#
# We need to ensure that we have the correct handling of the requests based on
# the provided rules updated on the request representation
Request::enableHttpMethodParameterOverride();
Request::setTrustedHeaderName(Request::HEADER_FORWARDED, null);
#
# Bootstrap and handle the request with the generated kernel
$request = Request::createFromGlobals();

$response = $kernel->handle($request);
$response->send();

$kernel->terminate($request, $response);