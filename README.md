Sonatra Form Extensions Bundle
==============================

[![Latest Version](https://img.shields.io/packagist/v/sonatra/form-extensions-bundle.svg)](https://packagist.org/packages/sonatra/form-extensions-bundle)
[![Build Status](https://img.shields.io/travis/sonatra/SonatraFormExtensionsBundle/master.svg)](https://travis-ci.org/sonatra/SonatraFormExtensionsBundle)
[![Coverage Status](https://img.shields.io/coveralls/sonatra/SonatraFormExtensionsBundle/master.svg)](https://coveralls.io/r/sonatra/SonatraFormExtensionsBundle?branch=master)
[![Scrutinizer Code Quality](https://img.shields.io/scrutinizer/g/sonatra/SonatraFormExtensionsBundle/master.svg)](https://scrutinizer-ci.com/g/sonatra/SonatraFormExtensionsBundle?branch=master)
[![SensioLabsInsight](https://img.shields.io/sensiolabs/i/f353d527-edf0-42a5-aa13-8b045668d853.svg)](https://insight.sensiolabs.com/projects/f353d527-edf0-42a5-aa13-8b045668d853)

The Sonatra FormExtensionsBundle add form types.

Features include:

- All features of [Sonatra Form Extensions](https://github.com/sonatra/sonatra-form-extensions)
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

[Resources/meta/LICENSE](Resources/meta/LICENSE)

About
-----

Sonatra FormExtensionsBundle is a [sonatra](https://github.com/sonatra) initiative.
See also the list of [contributors](https://github.com/sonatra/SonatraFormExtensionsBundle/graphs/contributors).

Reporting an issue or a feature request
---------------------------------------

Issues and feature requests are tracked in the [Github issue tracker](https://github.com/sonatra/SonatraFormExtensionsBundle/issues).

When reporting a bug, it may be a good idea to reproduce it in a basic project
built using the [Symfony Standard Edition](https://github.com/symfony/symfony-standard)
to allow developers of the bundle to reproduce the issue by simply cloning it
and following some steps.
