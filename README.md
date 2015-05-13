MyPoseoBundle
=========================

[![SensioLabsInsight](https://insight.sensiolabs.com/projects/0306c6a7-d7c4-4614-ae25-092137c8e4f7/small.png)](https://insight.sensiolabs.com/projects/0306c6a7-d7c4-4614-ae25-092137c8e4f7)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tristanbes/MyPoseoBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tristanbes/MyPoseoBundle/?branch=master)

Description:
--------------

This bundle provides a way to communicate with [MyPoseo](http://fr.myposeo.com/) webservices inside your Symfony2 application.

For now, only the _[Search API](http://fr.myposeo.com/nos-api/api-search/)_  has been wired. If you need more, PR are welcome.

The Search API allows you to get the position of an URL by keyword(s) among other feature.

Installation:
--------------

Add tristanbes/my-poseo-bundle to your composer.json:
``` bash
php composer.phar require "tristanbes/my-poseo-bundle": "1.0.*@dev"
```

Register the bundle in the kernel:

``` php
<?php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Tristanbes\MyPoseoBundle\MyPoseoBundle()
    );
}
```

How to use it:
--------------
You can configure the bundle with:

``` yml
my_poseo:
    api:
        key: "YOUR_API_KEY"
        type:
            main:
                base_url: "http://api.myposeo.com/{version}/m/api"
                version: 1.1
            search:
                base_url: "http://api.myposeo.com/m/{version}"
                version: "apiv2"
```

Your API key can be found on [this page](http://account.myposeo.com/account/configuration/api).

**Be careful, the given API key is already url encoded !** You need to decode it since guzzle re-encode automatically all parameters
