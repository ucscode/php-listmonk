<?php

namespace Junisan\ListmonkApi\UseCases\Lists;

use Junisan\ListmonkApi\Builders\ListBuilder;
use Junisan\ListmonkApi\API\ListmonkApi;
use Junisan\ListmonkApi\Models\ListModel;

class CreateList
{
    private ListmonkApi $api;
    private ListBuilder $listBuilder;

    public function __construct(ListmonkApi $api, ListBuilder $listBuilder)
    {
        $this->api = $api;
        $this->listBuilder = $listBuilder;
    }

    public function __invoke(ListModel $list): ListModel
    {
        $data = [
            'name' => $list->getName(),
            'type' => $list->getIsPublic() ? 'public' : 'private',
            'optin' => $list->getOptinSingle() ? 'single' : 'double',
            'status' => $list->getIsActive() ? 'active' : 'archived',
            'tags' => $list->getTags(),
            'description' => $list->getDescription(),
        ];

        $dataResponse = $this->api->post('/lists', $data);
        return $this->listBuilder->__invoke($dataResponse);
    }
}
