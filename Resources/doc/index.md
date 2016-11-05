Getting Started
===============

## Prerequisites

This version of the bundle requires Symfony 3.

## Installation

Installation is a quick, 2 step process:

1. Download the bundle using composer
2. Enable the bundle
3. Configure the bundle (optional)

### Step 1: Download the bundle using composer

Add Sonatra FormExtensionsBundle in your composer.json:

```json
{
    "require": {
        "sonatra/form-extensions-bundle": "~1.0"
    }
}
```

Or tell composer to download the bundle by running the command:

```bash
$ php composer.phar require sonatra/form-extensions-bundle:"~1.0"
```

Composer will install the bundle to your project's `vendor/sonatra` directory. 

### Step 2: Enable the bundle

Enable the bundle in the kernel:

```php
// app/AppKernel.php

public function registerBundles()
{
    $bundles = array(
        // ...
        new Fxp\Bundle\RequireAssetBundle\FxpRequireAssetBundle(),
        new Fxp\Bundle\AjaxBundle\AjaxBundle(),
        new Sonatra\Bundle\FormExtensionsBundle\SonatraFormExtensionsBundle(),
    );
}
```

### Step 3: Configure the bundle (optional)

You can override the default configuration adding `sonatra_form_extensions` tree in `app/config/config.yml`.
For see the reference of Sonatra Form Extensions Configuration, execute command:

```bash
$ php app/console config:dump-reference SonatraFormExtensionsBundle 
```

### Next Steps

Now that you have completed the basic installation and configuration of the
Sonatra FormExtensionsBundle, you are ready to learn about usages of the bundle.

The following documents are available:

- [Enjoy!](usage.md)
