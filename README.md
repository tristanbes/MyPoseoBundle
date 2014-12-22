MyPoseoBundle
=========================

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/tristanbes/MyPoseoBundle/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/tristanbes/MyPoseoBundle/?branch=master)

Description:
--------------

This bundle provides a way to communicate with [MyPoseo](http://fr.myposeo.com/) webservices.

For now, only the _[Search API](http://fr.myposeo.com/nos-api/api-search/)_  has been wired. If you need more, PR are welcome.

Installation:
--------------

Add ElaoFormTranslationBundle to your composer.json:
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

            For example, if you just need simple keys you could do with the following configuration:

            ``` yml
            elao_form_translation:
            blocks:
            root:      false
            children:  false
            separator: "_"
            ```
            Which would generate that kind of keys:

            # (parent_field_name)[separator](field_name)[separator][key]
            register_name_label

            #### Default configuration:

            ``` yml
            elao_form_translation:

            # Can be disabled
            enabled: true

            # Generate translation keys for all missing labels
            auto_generate: false

            # Customize available keys
            keys:
            form:
            label:  "label"
            help:   "help"
            # Add yours ...
            collection:
            label_add:      "label_add"
            label_delete:   "label_delete"
            # Add yours ...

            # Customize the ways keys are built
            blocks:

            # Prefix for prototype nodes
            prototype:  "prototype"

            # Prefix for children nodes
            children:   "children"

            # Prefix at the root of the key
            root:       "form"

            # Separator te be used between nodes
            separator:  "."
            ```
