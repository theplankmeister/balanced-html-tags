<?php

/*
 * This file is part of the BalancedHtmlTagsTest package.
 *
 * (c) The Plankmeister <plankmeister@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE file that was distributed with this source
 * code.
 */

namespace BalancedHtmlTags;

/**
 * Makes sure that the passed HTML contains balanced tags, so we don't break the frontend.
 *
 * @package App\Model\Rule
 */
class BalancedHtmlTags
{
    /**
     * Balances the tags on the passed HTML.
     *
     * @param $html
     * @return string
     */
    public static function balanceTags($html)
    {
        return self::_balanceTags($html);
    }

    /**
     * Determines if the passed HTML contains balanced tags.
     *
     * @param $html
     * @return bool
     */
    public static function tagsBalanced($html)
    {
        return $html === self::_balanceTags($html);
    }

    /**
     * Balances tags of string using a modified stack.
     *
     * @since 2.0.4
     *
     * @author Leonard Lin <leonard@acm.org>
     * @license GPL v2.0
     * @copyright November 4, 2001
     * @version 1.1
     * @todo Make better - change loop condition to $text in 1.2
     * @internal Modified by Scott Reilly (coffee2code) 02 Aug 2004
     *		1.1  Fixed handling of append/stack pop order of end text
     *			 Added Cleaning Hooks
     *		1.0  First Version
     *
     * @param string $text Text to be balanced.
     * @return string Balanced text.
     */
    protected static function _balanceTags( $text ) {
        $tagstack = array(); $stacksize = 0; $tagqueue = ''; $newtext = '';
        $single_tags = array('area', 'base', 'br', 'col', 'embed', 'hr', 'img', 'input', 'keygen', 'link', 'meta',
            'param', 'source', 'track', 'wbr'); //Known single-entity/self-closing tags
        $nestable_tags = array('blockquote', 'div', 'span'); //Tags that can be immediately nested within themselves

        # WP bug fix for comments - in case you REALLY meant to type '< !--'
        $text = str_replace('< !--', '<    !--', $text);
        # WP bug fix for LOVE <3 (and other situations with '<' before a number)
        $text = preg_replace('#<([0-9]{1})#', '&lt;$1', $text);

        while (preg_match("/<(\/?\w*)\s*([^>]*)>/",$text,$regex)) {
            $newtext .= $tagqueue;

            $i = strpos($text,$regex[0]);
            $l = strlen($regex[0]);

            // clear the shifter
            $tagqueue = '';
            // Pop or Push
            if ( isset($regex[1][0]) && '/' == $regex[1][0] ) { // End Tag
                $tag = strtolower(substr($regex[1],1));
                // if too many closing tags
                if($stacksize <= 0) {
                    $tag = '';
                    //or close to be safe $tag = '/' . $tag;
                }
                // if stacktop value = tag close value then pop
                else if ($tagstack[$stacksize - 1] == $tag) { // found closing tag
                    $tag = '</' . $tag . '>'; // Close Tag
                    // Pop
                    array_pop ($tagstack);
                    $stacksize--;
                } else { // closing tag not at top, search for it
                    for ($j=$stacksize-1;$j>=0;$j--) {
                        if ($tagstack[$j] == $tag) {
                            // add tag to tagqueue
                            for ($k=$stacksize-1;$k>=$j;$k--){
                                $tagqueue .= '</' . array_pop ($tagstack) . '>';
                                $stacksize--;
                            }
                            break;
                        }
                    }
                    $tag = '';
                }
            } else { // Begin Tag
                $tag = strtolower($regex[1]);

                // Tag Cleaning

                // If self-closing or '', don't do anything.
                if((substr($regex[2],-1) == '/') || ($tag == '')) {
                }
                // ElseIf it's a known single-entity tag but it doesn't close itself, do so
                elseif ( in_array($tag, $single_tags) ) {
                    $regex[2] .= '/';
                } else {	// Push the tag onto the stack
                    // If the top of the stack is the same as the tag we want to push, close previous tag
                    if (($stacksize > 0) && !in_array($tag, $nestable_tags) && ($tagstack[$stacksize - 1] == $tag)) {
                        $tagqueue = '</' . array_pop ($tagstack) . '>';
                        $stacksize--;
                    }
                    $stacksize = array_push ($tagstack, $tag);
                }

                // Attributes
                $attributes = $regex[2];
                if($attributes) {
                    $attributes = ' '.$attributes;
                }
                $tag = '<'.$tag.$attributes.'>';
                //If already queuing a close tag, then put this tag on, too
                if ($tagqueue) {
                    $tagqueue .= $tag;
                    $tag = '';
                }
            }
            $newtext .= substr($text,0,$i) . $tag;
            $text = substr($text,$i+$l);
        }

        // Clear Tag Queue
        $newtext .= $tagqueue;

        // Add Remaining text
        $newtext .= $text;

        // Empty Stack
        while($x = array_pop($tagstack)) {
            $newtext .= '</' . $x . '>'; // Add remaining tags to close
        }

        // WP fix for the bug with HTML comments
        $newtext = str_replace("< !--","<!--",$newtext);
        $newtext = str_replace("<    !--","< !--",$newtext);

        return $newtext;
    }
}
