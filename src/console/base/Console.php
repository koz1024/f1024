<?php
/**
 * @package f1024
 * @license https://www.apache.org/licenses/LICENSE-2.0 Apache-2.0
 * @copyright (c) 2017, Konstantin Zavalny (koz1024)
 * @author Konstantin Zavalny (koz1024@gmail.com)
 */

namespace f1024\console\base;

/**
 * Class for working with console
 */
class Console{
    
    const BG_BLACK  = 40;
    const BG_RED    = 41;
    const BG_GREEN  = 42;
    const BG_YELLOW = 43;
    const BG_BLUE   = 44;
    const BG_PURPLE = 45;
    const BG_CYAN   = 46;
    const BG_GREY   = 47;
    const FG_BLACK  = 30;
    const FG_RED    = 31;
    const FG_GREEN  = 32;
    const FG_YELLOW = 33;
    const FG_BLUE   = 34;
    const FG_PURPLE = 35;
    const FG_CYAN   = 36;
    const FG_GREY   = 37;
    const NORMAL      = 0;
    const BOLD        = 1;
    const ITALIC      = 3;
    const UNDERLINE   = 4;
    const BLINK       = 5;
    const NEGATIVE    = 7;
    const CONCEALED   = 8;
    const CROSSED_OUT = 9;
    
    /**
     * Formats string
     * @param string $string String that should be formatted
     * @param array $format Array of formats
     * @return string Formatted string
     */
    public static function format($string, $format = []){
        if (empty($format)){
            $format = [self::NORMAL];
        }
        return "\033[0m\033[" . implode(';', $format) . "m" . $string . "\033[0m";
    }
    
    /**
     * Formats and outputs string
     * @param string $string String that should be formatted
     * @param array $format Array of formats
     */
    public static function e($string, $format = []){
        echo self::format($string, $format) . "\r\n";
    }
    
    /**
     * Moves cursor up
     */
    public static function moveUp(){
        echo "\033[1A";
    }
    
    /**
     * Moves cursor down
     */
    public static function moveDown(){
        echo "\033[1B";
    }
    
    /**
     * Moves cursor forward
     */
    public static function moveFw(){
        echo "\033[1C";
    }
    
    /**
     * Moves cursor backward
     */
    public static function moveBw(){
        echo "\033[1D";
    }
    
    /**
     * Scrolls page up
     */
    public static function scrollUp(){
        echo "\033[1S";
    }
    
    /**
     * Scrolls page down
     */
    public static function scrollDown(){
        echo "\033[1T";
    }
    
    /**
     * Clears the screen
     */
    public static function cls(){
        echo "\033[2J";
    }
    
    /**
     * Clears the screen before cursor
     */
    public static function clsBefore(){
        echo "\033[1J";
    }
    
    /**
     * Clears the screen after cursor
     */
    public static function clsAfter(){
        echo "\033[0J";
    }
}
