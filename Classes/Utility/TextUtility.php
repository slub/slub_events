<?php
namespace Slub\SlubEvents\Utility;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 3
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */


class TextUtility
{

    /**
     * Function foldline folds the line after 73 signs
     * rfc2445.txt: lines SHOULD NOT be longer than 75 octets
     *
     * @param string $content : Anystring
     *
     * @return string Manipulated string
     */
    public static function foldline($content)
    {
        $text = trim(strip_tags(html_entity_decode($content), '<br>,<p>,<li>'));
        $text = preg_replace('/<p[\ \w\=\"]{0,}>/', '', $text);
        $text = preg_replace('/<li[\ \w\=\"]{0,}>/', '- ', $text);
        // make newline formated (yes, really write \n into the text!
        $text = str_replace('</p>', '\n', $text);
        $text = str_replace('</li>', '\n', $text);
        // remove tabs
        $text = str_replace("\t", ' ', $text);
        // remove multiple spaces
        $text = preg_replace('/[\ ]{2,}/', '', $text);
        $text = str_replace('<br />', '\n', $text);
        // remove more than one empty line
        $text = preg_replace('/[\n]{3,}/', '\n\n', $text);
        // remove windows linkebreak
        $text = preg_replace('/[\r]/', '', $text);
        // newlines are not allowed
        $text = str_replace("\n", '\n', $text);
        // semicolumns are not allowed
        $text = str_replace(';', '\;', $text);

        $firstline = substr($text, 0, (75 - 12));
        $restofline = implode("\n ", str_split(trim(substr($text, (75 - 12), strlen($text))), 73));

        if (strlen($restofline) > 0) {
            $foldedline = $firstline . "\n " . $restofline;
        } else {
            $foldedline = $firstline;
        }

        return $foldedline;
    }
}
