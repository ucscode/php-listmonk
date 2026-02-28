<?php

namespace Junisan\ListmonkApi\UseCases\Subscribers;

use Junisan\ListmonkApi\Builders\SubscriberBuilder;
use Junisan\ListmonkApi\API\ListmonkApi;
use Junisan\ListmonkApi\Exceptions\RequiredPropertyException;
use Junisan\ListmonkApi\Models\ListSubscriptionModel;
use Junisan\ListmonkApi\Models\SubscriberModel;

class UpdateSubscriber
{
    private ListmonkApi $api;
    private SubscriberBuilder $builder;

    public function __construct(ListmonkApi $api, SubscriberBuilder $builder)
    {
        $this->api = $api;
        $this->builder = $builder;
    }

    public function __invoke(SubscriberModel $subscriber, bool $preconfirmedSubscriptions = false): SubscriberModel
    {
        $id = $subscriber->getId();

        if (!$id) {
            throw new RequiredPropertyException('Update failed: SubscriptionModel ID is missing.');
        }

        $lists = array_map(function(int|ListSubscriptionModel $list) {
            return $list instanceof ListSubscriptionModel ? $list->getId() : $list;
        }, $subscriber->getLists());

        $data = [
            'email' => $subscriber->getEmail(),
            'name' => $subscriber->getName(),
            'status' => $subscriber->getStatus(),
            'lists' => $lists,
            'preconfirm_subscriptions' => $preconfirmedSubscriptions,
        ];

        if ($subscriber->getAttributes()->count()) {
            $data['attribs'] = $subscriber->getAttributes()->getAll();
        }

        $dataResponse = $this->api->put("/subscribers/{$id}", $data);
        return $this->builder->__invoke($dataResponse);
    }
}
