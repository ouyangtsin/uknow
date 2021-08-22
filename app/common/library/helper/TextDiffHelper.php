<?php
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------

namespace app\common\library\helper;

class TextDiffHelper
{

    public const UNMODIFIED = 0; //行或字符同时出现在字符串或文件中
    public const DELETED = 1;//行或字符仅出现在第一个字符串或文件中
    public const INSERTED = 2;//行或字符仅出现在第二个字符串或文件中

    public static function compare($string1, $string2, $compareCharacters = false): array
    {
        $start = 0;
        if ($compareCharacters) {
            $sequence1 = $string1;
            $sequence2 = $string2;
            $end1 = strlen($string1) - 1;
            $end2 = strlen($string2) - 1;
        } else {
            $sequence1 = preg_split('/\R/', $string1);
            $sequence2 = preg_split('/\R/', $string2);
            $end1 = count($sequence1) - 1;
            $end2 = count($sequence2) - 1;
        }

        // skip any common prefix
        while ($start <= $end1 && $start <= $end2 && $sequence1[$start] == $sequence2[$start]) {
            $start++;
        }

        // skip any common suffix
        while ($end1 >= $start && $end2 >= $start && $sequence1[$end1] == $sequence2[$end2]) {
            $end1--;
            $end2--;
        }

        // compute the table of longest common subsequence lengths
        $table = self::computeTable($sequence1, $sequence2, $start, $end1, $end2);

        // generate the partial diff
        $partialDiff = self::generatePartialDiff($table, $sequence1, $sequence2, $start);

        // generate the full diff
        $diff = array();
        for ($index = 0; $index < $start; $index++) {
            $diff[] = array($sequence1[$index], self::UNMODIFIED);
        }
        while (count($partialDiff) > 0) {
            $diff[] = array_pop($partialDiff);
        }
        for ($index = $end1 + 1; $index < ($compareCharacters ? strlen($sequence1) : count($sequence1)); $index++) {
            $diff[] = array($sequence1[$index], self::UNMODIFIED);
        }
        return $diff;

    }

    /**
     * 文件差异对比
     * @param $file1
     * @param $file2
     * @param false $compareCharacters
     * @return array
     */
    public static function compareFiles($file1, $file2, bool $compareCharacters = false): array
    {
        return self::compare(file_get_contents($file1), file_get_contents($file2), $compareCharacters);
    }

    /**
     * 表格差异对比
     * @param $sequence1
     * @param $sequence2
     * @param $start
     * @param $end1
     * @param $end2
     * @return array
     */
    private static function computeTable($sequence1, $sequence2, $start, $end1, $end2)
    {

        $length1 = $end1 - $start + 1;
        $length2 = $end2 - $start + 1;

        // initialise the table
        $table = array(array_fill(0, $length2 + 1, 0));

        // loop over the rows
        for ($index1 = 1; $index1 <= $length1; $index1++) {
            // create the new row
            $table[$index1] = array(0);

            // loop over the columns
            for ($index2 = 1; $index2 <= $length2; $index2++) {

                // store the longest common subsequence length
                if ($sequence1[$index1 + $start - 1] == $sequence2[$index2 + $start - 1]) {
                    $table[$index1][$index2] = $table[$index1 - 1][$index2 - 1] + 1;
                } else {
                    $table[$index1][$index2] = max($table[$index1 - 1][$index2], $table[$index1][$index2 - 1]);
                }

            }
        }
        return $table;
    }

    /* Returns the partial diff for the specificed sequences, in reverse order.
     * The parameters are:
     *
     * $table     - the table returned by the computeTable function
     * $sequence1 - the first sequence
     * $sequence2 - the second sequence
     * $start     - the starting index
     */
    private static function generatePartialDiff($table, $sequence1, $sequence2, $start): array
    {
        $diff = array();
        $index1 = count($table) - 1;
        $index2 = count($table[0]) - 1;

        // loop until there are no items remaining in either sequence
        while ($index1 > 0 || $index2 > 0) {
            // check what has happened to the items at these indices
            if ($index1 > 0 && $index2 > 0 && $sequence1[$index1 + $start - 1] == $sequence2[$index2 + $start - 1]) {
                // update the diff and the indices
                $diff[] = array($sequence1[$index1 + $start - 1], self::UNMODIFIED);
                $index1--;
                $index2--;
            } elseif ($index2 > 0 && $table[$index1][$index2] == $table[$index1][$index2 - 1]) {
                // update the diff and the indices
                $diff[] = array($sequence2[$index2 + $start - 1], self::INSERTED);
                $index2--;
            } else {
                // update the diff and the indices
                $diff[] = array($sequence1[$index1 + $start - 1], self::DELETED);
                $index1--;
            }
        }
        return $diff;

    }

    /* Returns a diff as a string, where unmodified lines are prefixed by '  ',
     * deletions are prefixed by '- ', and insertions are prefixed by '+ '. The
     * parameters are:
     *
     * $diff      - the diff array
     * $separator - the separator between lines; this optional parameter defaults
     *              to "\n"
     */
    public static function toString($diff, $separator = "\n"): string
    {
        // initialise the string
        $string = '';
        // loop over the lines in the diff
        foreach ($diff as $line) {

            // extend the string with the line
            switch ($line[1]) {
                case self::UNMODIFIED :
                    $string .= '  ' . $line[0];
                    break;
                case self::DELETED    :
                    $string .= '- ' . $line[0];
                    break;
                case self::INSERTED   :
                    $string .= '+ ' . $line[0];
                    break;
            }

            $string .= $separator;
        }

        return $string;
    }

    /* Returns a diff as an HTML string, where unmodified lines are contained
     * within 'span' elements, deletions are contained within 'del' elements, and
     * insertions are contained within 'ins' elements. The parameters are:
     *
     * $diff      - the diff array
     * $separator - the separator between lines; this optional parameter defaults
     *              to '<br>'
     */
    public static function toHTML($diff, $separator = '<br>'): string
    {
        $html = '';

        foreach ($diff as $line) {
            // extend the HTML with the line
            switch ($line[1]) {
                case self::UNMODIFIED :
                    $element = 'span';
                    break;
                case self::DELETED    :
                    $element = 'del';
                    break;
                case self::INSERTED   :
                    $element = 'ins';
                    break;
            }
            $html .=
                '<' . $element . '>'
                . htmlspecialchars($line[0])
                . '</' . $element . '>';

            $html .= $separator;
        }

        return $html;

    }

    /* Returns a diff as an HTML table. The parameters are:
     *
     * $diff        - the diff array
     * $indentation - indentation to add to every line of the generated HTML; this
     *                optional parameter defaults to ''
     * $separator   - the separator between lines; this optional parameter
     *                defaults to '<br>'
     */
    public static function toTable($diff, $indentation = '', $separator = '<br>'): string
    {

        // initialise the HTML
        $html = $indentation . "<table class=\"diff\">\n";

        // loop over the lines in the diff
        $index = 0;
        while ($index < count($diff)) {

            // determine the line type
            switch ($diff[$index][1]) {

                // display the content on the left and right
                case self::UNMODIFIED:
                    $leftCell =
                        self::getCellContent(
                            $diff, $indentation, $separator, $index, self::UNMODIFIED);
                    $rightCell = $leftCell;
                    break;

                // display the deleted on the left and inserted content on the right
                case self::DELETED:
                    $leftCell =
                        self::getCellContent(
                            $diff, $indentation, $separator, $index, self::DELETED);
                    $rightCell =
                        self::getCellContent(
                            $diff, $indentation, $separator, $index, self::INSERTED);
                    break;

                // display the inserted content on the right
                case self::INSERTED:
                    $leftCell = '';
                    $rightCell =
                        self::getCellContent(
                            $diff, $indentation, $separator, $index, self::INSERTED);
                    break;

            }

            // extend the HTML with the new row
            $html .=
                $indentation
                . "  <tr>\n"
                . $indentation
                . '    <td class="diff'
                . ($leftCell == $rightCell
                    ? 'Unmodified'
                    : ($leftCell == '' ? 'Blank' : 'Deleted'))
                . '">'
                . $leftCell
                . "</td>\n"
                . $indentation
                . '    <td class="diff'
                . ($leftCell == $rightCell
                    ? 'Unmodified'
                    : ($rightCell == '' ? 'Blank' : 'Inserted'))
                . '">'
                . $rightCell
                . "</td>\n"
                . $indentation
                . "  </tr>\n";

        }

        // return the HTML
        return $html . $indentation . "</table>\n";

    }

    /* Returns the content of the cell, for use in the toTable function. The
     * parameters are:
     *
     * $diff        - the diff array
     * $indentation - indentation to add to every line of the generated HTML
     * $separator   - the separator between lines
     * $index       - the current index, passes by reference
     * $type        - the type of line
     */
    private static function getCellContent($diff, $indentation, $separator, &$index, $type): string
    {

        // initialise the HTML
        $html = '';

        // loop over the matching lines, adding them to the HTML
        while ($index < count($diff) && $diff[$index][1] == $type) {
            $html .=
                '<span>'
                . htmlspecialchars($diff[$index][0])
                . '</span>'
                . $separator;
            $index++;
        }

        // return the HTML
        return $html;

    }
}

?>
