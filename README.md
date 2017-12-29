Fxp Form Extensions Bundle
==========================

[![Latest Version](https://img.shields.io/packagist/v/fxp/form-extensions-bundle.svg)](https://packagist.org/packages/fxp/form-extensions-bundle)
[![Build Status](https://img.shields.io/travis/fxpio/fxp-form-extensions-bundle/master.svg)](https://travis-ci.org/fxpio/fxp-form-extensions-bundle)
[![Coverage Status](https://img.shields.io/coveralls/fxpio/fxp-form-extensions-bundle/master.svg)](https://coveralls.io/r/fxpio/fxp-form-extensions-bundle?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/fxpio/fxp-form-extensions-bundle/master.svg)](https://scrutinizer-ci.com/g/fxpio/fxp-form-extensions-bundle?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/f353d527-edf0-42a5-aa13-8b045668d853.svg)](https://insight.sensiolabs.com/projects/f353d527-edf0-42a5-aa13-8b045668d853)

The Fxp FormExtensionsBundle add form types.

Features include:

- All features of [Fxp Form Extensions](https://github.com/fxpio/fxp-form-extensions)
- Select2 AJAX request with specific route optimized, already existing for:
  * country form type
  * language form type
  * locale form type
  * timezone form type
  * currency form type
- Select2 AJAX request fallback to the same current URL if the route optimized is
  not defined (overrides the response for include the data of ajax request)

Documentation
-------------

The bulk of the documentation is stored in the `Resources/doc/index.md`
file in this bundle:

[Read the Documentation](Resources/doc/index.md)

Installation
------------

All the installation instructions are located in [documentation](Resources/doc/index.md).

License
-------

This bundle is under the MIT license. See the complete license in the bundle:

[LICENSE](LICENSE)

About
-----

Fxp FormExtensionsBundle is a [François Pluchino](https://github.com/francoispluchino) initiative.
See also the list of [contributors](https://github.com/fxpio/fxp-form-extensions-bundle/graphs/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/fxpio/fxp-form-extensions-bundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
