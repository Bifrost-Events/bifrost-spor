<?php

declare(strict_types=1);

namespace Tests;

use App\Support\PrintLayout;
use PHPUnit\Framework\TestCase;

final class PrintLayoutTest extends TestCase
{
    public function testParsePerPageAcceptsAllowedValues(): void
    {
        $this->assertSame(1, PrintLayout::parsePerPage('1'));
        $this->assertSame(2, PrintLayout::parsePerPage(2));
        $this->assertSame(4, PrintLayout::parsePerPage('4'));
    }

    public function testParsePerPageFallsBackToDefault(): void
    {
        $this->assertSame(PrintLayout::DEFAULT_PER_PAGE, PrintLayout::parsePerPage(null));
        $this->assertSame(PrintLayout::DEFAULT_PER_PAGE, PrintLayout::parsePerPage('3'));
        $this->assertSame(PrintLayout::DEFAULT_PER_PAGE, PrintLayout::parsePerPage('abc'));
    }

    public function testPaginateGroupsSigns(): void
    {
        $signs = ['a', 'b', 'c', 'd', 'e'];

        $this->assertSame([], PrintLayout::paginate([], 2));
        $this->assertSame([['a', 'b'], ['c', 'd'], ['e']], PrintLayout::paginate($signs, 2));
        $this->assertSame([['a', 'b', 'c', 'd'], ['e']], PrintLayout::paginate($signs, 4));
    }

    public function testPageCount(): void
    {
        $this->assertSame(0, PrintLayout::pageCount(0, 2));
        $this->assertSame(1, PrintLayout::pageCount(1, 4));
        $this->assertSame(2, PrintLayout::pageCount(5, 4));
        $this->assertSame(3, PrintLayout::pageCount(5, 2));
    }
}
