<?php
/**
 * Copyright (c) 2016-2016} Andreas Heigl<andreas@heigl.org>
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @author    Andreas Heigl<andreas@heigl.org>
 * @copyright 2016-2016 Andreas Heigl
 * @license   http://www.opensource.org/licenses/mit-license.php MIT-License
 * @version   0.0
 * @since     08.01.2016
 * @link      http://github.com/heiglandreas/php.ug
 */

namespace UgHealth;

use Phpug\Entity\Groupcontact;
use Phpug\Entity\Usergroup;
use ZendService\Twitter\Response;
use ZendService\Twitter\Twitter;

class TwitterStatus implements UsergroupHealthPluginInterface
{

    public function __construct(Twitter $twitter)
    {
        $this->twitter = $twitter;
        $this->diff = [
            'active' => new \DateInterval('P3M'),
            'rest'   => new \DateInterval('P6M'),
            'stale'  => new \DateInterval('P1Y'),
        ];
    }

    public function check(Usergroup $usergroup)
    {
        $now = new \DateTime();


        $response = $this->twitter->account->verifyCredentials();
        if (! $response->isSuccess()) {
            throw new UnexpectedValueException('Something\'s wrong with the twitter-credentials');
        }

        $endResponse = self::UNKNOWN;
        /** @var Groupcontact $contact */
        foreach ($usergroup->getContacts() as $contact) {

            if ($contact->getServiceName() !== 'Twitter') {
                continue;
            }
            try {
                $contactResponse = $this->getTwitterActivity($contact, $now);
            } catch (\Exception $e) {
                continue;
            }
            if ($contactResponse <= $endResponse) {
                continue;
            }

            $endResponse = $contactResponse;
        }

        return $endResponse;
    }

    protected function getTwitterActivity($contact, $now)
    {


        $response = $this->twitter->users->show($contact->getName());
        if (! $response instanceof Response) {
            throw new \UnexpectedValueException(sprintf(
                'Problems retrieving informations for "%s" from twitter. They had this to say: %s',
                $contact->getName(),
                print_r($response, true)
            ));
        }

        if ($response->isError()) {
            throw new \UnexpectedValueException(sprintf(
                "Problem retrieving informations for \"%s\" from twitter. They had this to say: \n%s\n",
                $contact->getName(),
                implode("\n", array_map (function($item) {
                        return $item->message;
                    }, $response->getErrors()
                )))
            );
        }

        $lastTweet = new \DateTime($response->status->created_at);
        $diff = $lastTweet->diff($now);
        if ($diff > $this->diff['stale']) {
            return self::STALE;
        } elseif ($diff > $this->diff['rest']) {
            return self::RESTING;
        } elseif ($diff > $this->diff['active'] ) {
            return self::ACTIVE;
        }

        return self::BUSY;
    }

    public function getName()
    {
        return 'twitterStatus';
    }
}