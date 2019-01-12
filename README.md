# Leopard
Leopard is an elasticsearch odm (Object Document Mapper) for laravel eloquent.

[![Software license][ico-license]](LICENSE)
[![Travis][ico-travis]][link-travis]
[![Coveralls](https://coveralls.io/repos/github/triadev/Leopard/badge.svg?branch=master)](https://coveralls.io/github/triadev/Leopard?branch=master)
[![CodeCov](https://codecov.io/gh/triadev/Leopard/branch/master/graph/badge.svg)](https://codecov.io/gh/triadev/Leopard)

[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/triadev/Leopard/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/triadev/Leopard/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/triadev/Leopard/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/triadev/Leopard/?branch=master)
[![Build Status](https://scrutinizer-ci.com/g/triadev/Leopard/badges/build.png?b=master)](https://scrutinizer-ci.com/g/triadev/Leopard/build-status/master)

[![Latest stable][ico-version-stable]][link-packagist]
[![Latest development][ico-version-dev]][link-packagist]
[![Monthly installs][ico-downloads-monthly]][link-downloads]
[![Total Downloads](https://img.shields.io/packagist/dt/triadev/leopard.svg?style=flat-square)](https://packagist.org/packages/triadev/leopard)

## Supported laravel versions
[![Laravel 5.5][icon-l55]][link-laravel]
[![Laravel 5.6][icon-l56]][link-laravel]
[![Laravel 5.7][icon-l57]][link-laravel]

## Supported elasticsearch versions
[![Elasticsearch 6.0][icon-e60]][link-elasticsearch]
[![Elasticsearch 6.1][icon-e61]][link-elasticsearch]
[![Elasticsearch 6.2][icon-e62]][link-elasticsearch]
[![Elasticsearch 6.3][icon-e63]][link-elasticsearch]
[![Elasticsearch 6.4][icon-e64]][link-elasticsearch]

## Main features
- Storing eloquent models in elasticsearch
- DSL for elasticsearch
- Fluent mapping builder
- Update elasticsearch mapping via file

## [Documentation](https://github.com/triadev/Leopard/wiki)
* [Home](https://github.com/triadev/Leopard/wiki/Home/_edit)
* [Installation](https://github.com/triadev/Leopard/wiki/Installation)
* [Configuration](https://github.com/triadev/Leopard/wiki/Configuration)
* [Searchable eloquent models](https://github.com/triadev/Leopard/wiki/Searchable-eloquent-models)
* [DSL](https://github.com/triadev/Leopard/wiki/DSL)
* [Mapping](https://github.com/triadev/Leopard/wiki/Mapping)
* [Sync database with elasticsearch](https://github.com/triadev/Leopard/wiki/Sync-database-with-elasticsearch)

## Reporting Issues
If you do find an issue, please feel free to report it with GitHub's bug tracker for this project.

Alternatively, fork the project and make a pull request. :)

## Testing
1. docker-compose -f docker-compose.yml up
2. composer test

## Contributing
Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

## Credits
- [Christopher Lorke][link-author]
- [All Contributors][link-contributors]

## Other

### Project related links
- [Wiki](https://github.com/triadev/Leopard/wiki)
- [Issue tracker](https://github.com/triadev/Leopard/issues)

### License
The code for Leopard is distributed under the terms of the MIT license (see [LICENSE](LICENSE)).

[ico-license]: https://img.shields.io/github/license/triadev/Leopard.svg?style=flat-square
[ico-version-stable]: https://img.shields.io/packagist/v/triadev/leopard.svg?style=flat-square
[ico-version-dev]: https://img.shields.io/packagist/vpre/triadev/leopard.svg?style=flat-square
[ico-downloads-monthly]: https://img.shields.io/packagist/dm/triadev/leopard.svg?style=flat-square
[ico-travis]: https://travis-ci.org/triadev/Leopard.svg?branch=master

[link-packagist]: https://packagist.org/packages/triadev/leopard
[link-downloads]: https://packagist.org/packages/triadev/leopard/stats
[link-travis]: https://travis-ci.org/triadev/Leopard

[icon-l55]: https://img.shields.io/badge/Laravel-5.5-brightgreen.svg?style=flat-square
[icon-l56]: https://img.shields.io/badge/Laravel-5.6-brightgreen.svg?style=flat-square
[icon-l57]: https://img.shields.io/badge/Laravel-5.7-brightgreen.svg?style=flat-square

[icon-e60]: https://img.shields.io/badge/Elasticsearch-6.0-brightgreen.svg?style=flat-square
[icon-e61]: https://img.shields.io/badge/Elasticsearch-6.1-brightgreen.svg?style=flat-square
[icon-e62]: https://img.shields.io/badge/Elasticsearch-6.2-brightgreen.svg?style=flat-square
[icon-e63]: https://img.shields.io/badge/Elasticsearch-6.3-brightgreen.svg?style=flat-square
[icon-e64]: https://img.shields.io/badge/Elasticsearch-6.4-brightgreen.svg?style=flat-square

[link-laravel]: https://laravel.com
[link-elasticsearch]: https://www.elastic.co/
[link-author]: https://github.com/triadev
[link-contributors]: ../../contributors
