<?php
/**
 * Copyright (c)2015-2015 heiglandreas
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIBILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @category
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright ©2015-2015 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     25.03.15
 * @link      https://github.com/heiglandreas/
 */

namespace Phpug\ORM\Query\AST\Functions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;

/**
 * DistanceFromFunction ::= "DISTANCEFROM" "(" ArithmeticPrimary "," ArithmeticPrimary ")"
 *
 * This distance is calculated acording to http://www.movable-type.co.uk/scripts/gis-faq-5.1.html
 *
 * @see http://www.movable-type.co.uk/scripts/gis-faq-5.1.html
 */
class DistanceFrom extends FunctionNode
{
    protected static $latitudeField = 'latitude';
    protected static $longitudeField = 'longitude';

    /**
     * This is the radius of the sphere.
     *
     * For the earth use 6367 for distance results in kilometers or
     * ?? for results in miles.
     *
     * @var float
     */
    protected static $radius = 6367;

    protected $latitude = null;

    protected $longitude = null;


    /**
     * @param \Doctrine\ORM\Query\SqlWalker $sqlWalker
     *
     * @return string
     */
    public function getSql(\Doctrine\ORM\Query\SqlWalker $sqlWalker)
    {
        return sprintf(
            '(asin(sqrt(pow(sin((%2$s*0.017453293-%4$f*0.017453293)/2),2) ' .
            '+ cos(%2$s*0.017453293) * cos(%4$f*0.017453293) * pow(sin((%3$s*' .
            '0.017453293-%5$f*0.017453293)/2),2))) * %1$f)',
            self::getRadius(),
            self::getLatitudeField(),
            self::getLongitudeField(),
            $this->latitude->dispatch($sqlWalker),
            $this->longitude->dispatch($sqlWalker)
        );
    }

    /**
     * @param \Doctrine\ORM\Query\Parser $parser
     *
     * @return void
     */
    public function parse(\Doctrine\ORM\Query\Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->latitude = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_COMMA);
        $this->longitude = $parser->ArithmeticPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);
    }

    public static function getRadius()
    {
        return self::$radius;
    }

    public static function getLatitudeField()
    {
        return self::$latitudeField;
    }

    public static function getLongitudeField()
    {
        return self::$longitudeField;
    }

    public static function setLongitudeField($longitude)
    {
        self::$longitudeField = (string) $longitude;
    }

    public static function setLatitudeField($latitude)
    {
        self::$latitudeField = (string) $latitude;
    }

    public static function setRadius($radius)
    {
        self::$radius = (float) $radius;
    }
}
