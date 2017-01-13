<?php

namespace Robopuff\Rating;

/**
 * Elo Rating
 * created by Arpad Elo, implemented using logistic curve.
 *
 * @see https://en.wikipedia.org/wiki/Elo_rating_system
 */
class Elo implements RatingInterface
{
    const FACTOR_MASTERS = 16;
    const FACTOR_WEAKER = 32;

    /**
     * @var array
     */
    private $p2dp = [
       100 => 800, 99 => 677, 98 => 589, 97 => 538,
        96 => 501, 95 => 470, 94 => 444, 93 => 422,
        92 => 401, 91 => 383, 90 => 366, 89 => 351,
        88 => 336, 87 => 322, 86 => 309, 85 => 296,
        84 => 284, 83 => 273, 82 => 262, 81 => 251,
        80 => 240, 79 => 230, 78 => 220, 77 => 211,
        76 => 202, 75 => 193, 74 => 184, 73 => 175,
        72 => 166, 71 => 158, 70 => 149, 69 => 141,
        68 => 133, 67 => 125, 66 => 117, 65 => 110,
        64 => 102, 63 => 95,  62 => 87,  61 => 80,
        60 => 72,  59 => 65,  58 => 57,  57 => 50,
        56 => 43,  55 => 36,  54 => 29,  53 => 21,
        52 => 14,  51 => 7,   50 => 0,
    ];

    /**
     * @var int
     */
    private $factor;

    /**
     * Elo constructor.
     *
     * @param int $factor the K factor
     */
    public function __construct($factor = self::FACTOR_MASTERS)
    {
        $this->setFactor($factor);
    }

    /**
     * Set K factor.
     *
     * @param int $factor
     */
    public function setFactor($factor)
    {
        $this->factor = $factor;
    }

    /**
     * Get FIDE Rating difference based on points (score divided by number of games played).
     *
     * @param $points
     *
     * @return int|mixed
     *
     * @throws Exception\InvalidArgumentException
     */
    public function getFIDERatingDifference($points)
    {
        if (is_float($points)) {
            $points *= 100;
        }

        if ($points < 0 || $points > 100) {
            throw new Exception\InvalidArgumentException(
                'Points should be an ant between 0 and 100 or a float between 0 and 1'
            );
        }

        if ($points < 50) {
            return -1 * $this->p2dp[100 - $points];
        }

        return $this->p2dp[$points];
    }

    /**
     * Get new ratings based on pair of ratings and result of match.
     *
     * @param int $ratingA
     * @param int $ratingB
     * @param int $result  -1 left won, 1 right won, 0 draw or use
     *                     RatingInterface::RESULT_WON_A, RatingInterface::RESULT_WON_B and RatingInterface::RESULT_DRAW
     *
     * @return array
     */
    public function ratePair($ratingA, $ratingB, $result) : array
    {
        $scoreA = $scoreB = 0;

        if ($result === self::RESULT_WON_B) {
            $scoreA = 0;
            $scoreB = 1;
        } elseif ($result === self::RESULT_WON_A) {
            $scoreA = 1;
            $scoreB = 0;
        }

        $expectedA = 1 / (1 + pow(10, ($ratingB - $ratingA) / 400));
        $expectedB = 1 / (1 + pow(10, ($ratingA - $ratingB) / 400));

        return [
            $ratingA + ($this->factor * ($scoreA - $expectedA)),
            $ratingB + ($this->factor * ($scoreB - $expectedB)),
        ];
    }

    /**
     * Get new ratings based on ratings array.
     *
     * @param array $ratings
     *
     * @return array
     */
    public function rateArray(array $ratings) : array
    {
        $calcR = [];
        foreach ($ratings as $position => $rating) {
            $calcR[$position] = pow(10, $rating / 400);
        }

        $expected = [];
        foreach ($calcR as $position => $rating) {
            $sumCalc = array_sum($calcR);
            $expected[$position] = $rating / $sumCalc;
        }

        $results = [];
        $count = count($ratings);
        foreach ($expected as $position => $rating) {
            $score = ((($count - 1) - $position) / ($count - 1));
            $results[$position] = $ratings[$position] + ($this->factor * ($score - $rating));
        }

        return $results;
    }
}
