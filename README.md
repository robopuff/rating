# Rating

## Elo

[![Build Status](https://travis-ci.org/robopuff/rating.svg?branch=master)](https://travis-ci.org/robopuff/rating)
[![Coverage Status](https://coveralls.io/repos/github/robopuff/rating/badge.svg?branch=master)](https://coveralls.io/github/robopuff/rating?branch=master)

Based on Elo rating system, adapted to use over arrays

```php
$elo = new Elo();
$results = $elo->ratePair(1500, 1500, Elo::RESULT_WON_A);
// $results = [1508, 1492];
```

```php
$elo = new Elo();
$results = $elo->rateArray([
    // Place on the grid => Current rating
    0 => 1500, //1st place
    1 => 1500, //2nd place
    2 => 1500  //3rd place
    // ... Nth place
]);
// $results = [
//     0 => 1510,
//     1 => 1502,
//     2 => 1494,
//     n => ...
// ];
```
