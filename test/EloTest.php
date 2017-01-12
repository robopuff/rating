<?php

namespace RatingTest;

use PHPUnit\Framework\TestCase;
use Robopuff\Rating\Elo;
use Robopuff\Rating\Exception\InvalidArgumentException;

class EloTest extends TestCase
{
    /**
     * @var Elo
     */
    private $elo;

    public function setUp()
    {
        $this->elo = new Elo(Elo::FACTOR_MASTERS);
    }

    public function testPairRatingFactorMasters()
    {
        $wonA = $this->elo->ratePair(1500, 1500, Elo::RESULT_WON_A);
        $wonB = $this->elo->ratePair(1500, 1500, Elo::RESULT_WON_B);
        $draw = $this->elo->ratePair(1500, 1500, Elo::RESULT_DRAW);

        $this->assertEquals([1508, 1492], $wonA);
        $this->assertEquals([1492, 1508], $wonB);
        $this->assertEquals([1492, 1492], $draw);
    }

    public function testPairRatingFactorWeaker()
    {
        $this->elo->setFactor(Elo::FACTOR_WEAKER);

        $wonA = $this->elo->ratePair(1500, 1500, Elo::RESULT_WON_A);
        $wonB = $this->elo->ratePair(1500, 1500, Elo::RESULT_WON_B);
        $draw = $this->elo->ratePair(1500, 1500, Elo::RESULT_DRAW);

        $this->assertEquals([1516, 1484], $wonA);
        $this->assertEquals([1484, 1516], $wonB);
        $this->assertEquals([1484, 1484], $draw);

        $this->elo->setFactor(Elo::FACTOR_MASTERS);
    }

    public function testArrayRating()
    {
        $ratings = range(0, 11);

        foreach ($ratings as $i => $v) {
            $ratings[$i] = 1500;
        }

        $this->assertNotEmpty($this->elo->rateArray($ratings));
    }

    public function testArrayAgainstPair()
    {
        $this->assertEquals(
            $this->elo->ratePair(1000, 2000, Elo::RESULT_WON_A),
            $this->elo->rateArray([1000, 2000])
        );

        $this->assertEquals(
            $this->elo->ratePair(2000, 1000, Elo::RESULT_WON_A),
            $this->elo->rateArray([2000, 1000])
        );
    }

    public function testFIDERatingDifference()
    {
        $this->assertEquals(800, $this->elo->getFIDERatingDifference(1.0));
        $this->assertEquals(800, $this->elo->getFIDERatingDifference(100));

        $this->assertEquals(-800, $this->elo->getFIDERatingDifference(0));

        $this->assertEquals(230, $this->elo->getFIDERatingDifference(79));
        $this->assertEquals(-230, $this->elo->getFIDERatingDifference(21));

        $this->assertEquals(-7, $this->elo->getFIDERatingDifference(49));
        $this->assertEquals(-7, $this->elo->getFIDERatingDifference(0.49));
    }

    public function testFIDERatingDifferenceOutOfRangeFloat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->elo->getFIDERatingDifference(1.9);
    }

    public function testFIDERatingDifferenceOutOfRangeInt()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->elo->getFIDERatingDifference(190);
    }

    public function testFIDERatingDifferenceBelowRangeFloat()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->elo->getFIDERatingDifference(-0.1);
    }

    public function testFIDERatingDifferenceBelowRangeInt()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->elo->getFIDERatingDifference(-1);
    }
}
