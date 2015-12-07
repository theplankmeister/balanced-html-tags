<?php

/*
 * This file is part of the BalancedHtmlTagsTest package.
 *
 * (c) The Plankmeister <plankmeister@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

use BalancedHtmlTags\BalancedHtmlTags;

class BalancedHtmlTagsTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests broken tag nesting.
     */
    public function testBrokenTags()
    {
        $brokenHtml = '<div>one<p></div>two</p>';
        $this->assertFalse(BalancedHtmlTags::tagsBalanced($brokenHtml));
    }

    /**
     * Tests unbalanced tags
     */
    public function testUnbalancedTags()
    {
        $brokenHtml = '<div>one<p></div>two';
        $this->assertFalse(BalancedHtmlTags::tagsBalanced($brokenHtml));
    }

    /**
     * Tests balanced tags.
     */
    public function testBalancedTags()
    {
        $validHtml = '<div><p><i>blah</i><input name="fred" type="whatever" /></p></div>';
        $this->assertTrue(BalancedHtmlTags::tagsBalanced($validHtml));
    }

    /**
     * Tests balancing broken tag nesting.
     */
    public function testBalancingBrokenTags()
    {
        $brokenHtml = '<div>one<p></div>two</p>';
        $this->assertEquals('<div>one<p></p></div>two', BalancedHtmlTags::balanceTags($brokenHtml));
    }

    /**
     * Tests balancing unbalanced tags
     */
    public function testBalancingUnbalancedTags()
    {
        $brokenHtml = '<div>one<p></div>two';
        $this->assertEquals('<div>one<p></p></div>two', BalancedHtmlTags::balanceTags($brokenHtml));
    }

    /**
     * Tests balancing balanced tags.
     */
    public function testBalancingBalancedTags()
    {
        $validHtml = '<div><p><i>blah</i><input name="fred" type="whatever" /></p></div>';
        $this->assertEquals($validHtml, BalancedHtmlTags::balanceTags($validHtml));
    }
}
