<?php
/**
 * Copyright (c)2014-2014 heiglandreas
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
 * @copyright ©2014-2014 Andreas Heigl
 * @license   http://www.opesource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     12.05.14
 * @link      https://github.com/heiglandreas/
 */

namespace PhpugTest\Parser;

use Phpug\Parser\Mentoringapp as MentoringParser;

class MentoringAppTest extends \PHPUnit_Framework_TestCase
{
    public function testParsingJsonEndpoint()
    {
        $parser = new MentoringParser(array(''));
        $entries = $parser->parse(__DIR__ . '/__files/mentors.json');
        $expected = array(
            'name' => 'Tristan Bailey',
            'github' => '',
            'lat' => 0,
            'lon' => 0,
            'description' => '<p>I have been developing websites for 13+ years and worked though companies to Senior Dev. Middle/Backend developer and full stack team lead (PHP, MySQL, [Laravel, Slim, Joomla, EE, WP, Magento], DevOps, Agile). Moved to Freelance Developer about 4 years ago, and work with agencies and clients, on medium to large travel and eCommerce sites. When writing fresh projects I use Laravel or SlimPHP but often work on existing systems and integrations.'. "\n" . 'Happy to mentor others about php, full stack development, working with teams, coding, freelance work, DevOps, analytics, strategy etc</p>',
            'type' => 'mentor',
            'tags' => array (
                'mentor' => ['PHP', 'full-stack', 'freelancing', 'behat', 'mysql', 'legacy', 'analytics'],
                'apprentice' => [''],
            ),
            'thumbnail' => 'https://avatars0.githubusercontent.com/u/200137',
            'id' => 6,
            'githubUid' => '200137',
        );
        $this->assertEquals($expected, $entries[0]);
    }
}
 