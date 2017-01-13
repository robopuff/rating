<?php

namespace Robopuff\Rating;

interface RatingInterface
{
    const RESULT_WON_A = -1;
    const RESULT_WON_B = 1;
    const RESULT_DRAW = 0;

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
    public function ratePair($ratingA, $ratingB, $result) : array;

    /**
     * Get new ratings based on ratings array.
     *
     * @param array $ratings
     *
     * @return array
     */
    public function rateArray(array $ratings) : array;
}
