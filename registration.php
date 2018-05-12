<?php
/**
 * An implementation of the OpenCensus tooling for Magento 2
 *
 * @category  Sitewards
 * @package   Sitewards_ClientHints
 * @copyright Copyright (c) Sitewards GmbH (http://www.sitewards.com)
 * @contact   mail@sitewards.com
 */
\Magento\Framework\Component\ComponentRegistrar::register(
    \Magento\Framework\Component\ComponentRegistrar::MODULE,
    'Sitewards_OpenCensus',
    __DIR__
);
