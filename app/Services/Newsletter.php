<?php

namespace App\Services;

use MailchimpMarketing\ApiClient;

class Newsletter
{
    /**
     * @return mixed
     */
    public function subscribe(string $email, ?string $list = null)
    {
        $list ??= config('services.mailchimp.lists.subscribers');

        // @phpstan-ignore-next-line
        return $this->client()->lists->addListMember($list, [
            'email_address' => $email,
            'status' => 'subscribed',
        ]);
    }

    /**
     * @return ApiClient
     */
    protected function client()
    {
        return (new ApiClient)->setConfig([
            'apiKey' => config('services.mailchimp.key'),
            'server' => 'us5',
        ]);
    }
}
