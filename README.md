# bbq-sqs
SQS implementation for [BBQ library](https://github.com/eventio/bbq).

## Installation

```json
{
  "require": {
    "wizacha/bbq-sqs": "@dev"
  },
  "repositories": [
    {
      "type": "git",
      "url": "https://github.com/wizacha/bbq-sqs.git"
    }
  ]
}
```

## Tests

`./tests/run.sh`

To actually test the SQS implementation, you have to provide SQS credentials in `tests/config.php` with following scheme:
```php
<?php
return [
    'region' => '****',
    'key'    => '****',
    'secret' => '****',
];
```

## Usage

See [original documentation](https://github.com/eventio/bbq).

## TODO
  * PR to the original repository
  * Semver ?
  * Add to packagist ?
  * ...
