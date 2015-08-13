<?php

/*
 * This file is part of Cachet.
 *
 * (c) Alt Three Services Limited
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CachetHQ\Cachet\Handlers\Events;

use CachetHQ\Cachet\Events\CustomerHasSubscribedEvent;
use Illuminate\Contracts\Mail\MailQueue;
use Illuminate\Mail\Message;

class SendSubscriberVerificationEmailHandler
{
    /**
     * The mailer instance.
     *
     * @var \Illuminate\Contracts\Mail\MailQueue
     */
    protected $mailer;

    /**
     * Create a new send subscriber verification email handler.
     *
     * @param \Illuminate\Contracts\Mail\Mailer $mailer
     */
    public function __construct(MailQueue $mailer)
    {
        $this->mailer = $mailer;
    }

    /**
     * Handle the event.
     *
     * @param \CachetHQ\Cachet\Events\CustomerHasSubscribedEvent $event
     */
    public function handle(CustomerHasSubscribedEvent $event)
    {
        $mail = [
            'email'   => $event->subscriber->email,
            'subject' => 'Confirm your subscription.',
            'link'    => route('subscribe.verify', ['code' => $event->subscriber->verify_code]),
            'appUrl'  => env('APP_URL'),
        ];

        $this->mailer->queue([
            'html' => 'emails.subscribers.verify-html',
            'text' => 'emails.subscribers.verify-text',
        ], $mail, function (Message $message) use ($mail) {
            $message->to($mail['email'])->subject($mail['subject']);
        });
    }
}
