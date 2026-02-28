# PHP Listmonk API

Implementation of the Listmonk API in PHP. It requires PHP >= 7.4.

A clean, object-oriented PHP client for the Listmonk API.

This package provides a structured and model-driven way to interact with a Listmonk instance, including:

* Subscriber management
* List management
* Campaign management
* Pagination handling
* Strongly typed models

It wraps the Listmonk REST API into dedicated services and models for predictable and maintainable usage.

---

## Installation

Install via Composer:

```bash
composer require ucscode/php-listmonk
```

---

## Configuration

Initialize the API client with your Listmonk API URL and credentials:

```php
use Junisan\ListmonkApi\API\ListmonkPHP;

$api = new ListmonkPHP('https://listmonk-domain.com/api', [
    'username' => 'api-username',
    'password' => 'api-token',
]);
```

---

## Available Services

The client exposes three API services:

```php
$subscriberApi = $api->getSubscribersApi();
$listApi       = $api->getListsApi();
$campaignApi   = $api->getCampaignsApi();
```

Each service contains methods specific to its domain.

---

# Subscriber API

## Create Subscriber

```php
use Junisan\ListmonkApi\Models\SubscriberModel;
use Junisan\ListmonkApi\Models\SubscriberAttributesModel;

$model = (new SubscriberModel())
    ->setName('Example')
    ->setEmail('email@example.com')
    ->setStatus('enabled') // enabled, disabled, etc.
    ->setLists([]) // optional: list IDs
    ->setAttributes(new SubscriberAttributesModel()); // optional

$subscriber = $subscriberApi->createSubscriber($model);
```

Returns a `SubscriberModel` with populated `id` and `uuid` if successful.

You may optionally pass:

```php
$subscriberApi->createSubscriber($model, true); // preconfirmed subscriptions
```

---

## Get Subscriber

By Email:

```php
$subscriber = $subscriberApi->getSubscriberByEmail('email@example.com');
```

By ID:

```php
$subscriber = $subscriberApi->getSubscriberById(1);
```

---

## Get All Subscribers (Paginated)

```php
$paginator = $subscriberApi->getAllSubscriber(1, 100);

$subscribers = $paginator->getResults();
$total       = $paginator->getTotal();
```

Returns a `PaginatorModel`.

---

# List API

## Create List

```php
use Junisan\ListmonkApi\Models\ListModel;

$list = (new ListModel())
    ->setName('Newsletter')
    ->setDescription('Main newsletter list')
    ->setIsPublic(true)
    ->setOptinSingle(true)
    ->setIsActive(true)
    ->setTags(['marketing', 'weekly']);

$createdList = $listApi->createList($list);
```

Returns a `ListModel` with `id` and `uuid`.

---

## Get Lists

Get all lists:

```php
$paginator = $listApi->getAllLists(1, 100);
```

Get list by ID:

```php
$list = $listApi->getListById(1);
```

---

# Campaign API

## Create Campaign

```php
use Junisan\ListmonkApi\Models\CampaignModel;

$campaign = (new CampaignModel())
    ->setName('January Campaign')
    ->setSubject('Welcome to our newsletter')
    ->setBody('<h1>Hello World</h1>')
    ->setType('regular')
    ->setContentType('html')
    ->setStatus('draft')
    ->setListIds([1, 2])
    ->setTags(['monthly']);

$createdCampaign = $campaignApi->createCampaign($campaign);
```

---

## Get Campaigns

```php
$paginator = $campaignApi->getAllCampaigns(1, 100);
```

Get single campaign:

```php
$campaign = $campaignApi->getCampaignById(1);
```

---

## Change Campaign Status

```php
$updated = $campaignApi->changeCampaignStatus(1, 'running');
```

Common statuses:

* draft
* scheduled
* running
* paused
* cancelled

---

## Preview Campaign

```php
$htmlPreview = $campaignApi->previewCampaign(1);
```

Returns rendered HTML preview or null.

---

# Models Overview

## CampaignModel

Represents a Listmonk campaign.

Includes:

* Basic data (status, name, subject, body)
* Target lists (`listIds`)
* Tags
* Scheduling (`sendAt`)
* Advanced fields (`templateId`, `fromEmail`, etc.)
* Statistics (`views`, `clicks`, `bounces`)

---

## ListModel

Represents a subscriber list.

Key properties:

* name
* description
* isPublic
* optinSingle
* isActive
* tags

---

## SubscriberModel

Represents a subscriber.

Key properties:

* name
* email
* status
* attributes
* lists

Lists may be either:

* Array of list IDs
* Array of `ListSubscriptionModel`

---

# Pagination

Methods returning multiple results provide a `PaginatorModel`, which typically includes:

* Current page
* Per page
* Total records
* Data collection

---

# Error Handling

Some methods may throw exceptions (e.g., `getSubscriberByEmail`).

It is recommended to wrap calls in try/catch blocks:

```php
try {
    $subscriber = $subscriberApi->getSubscriberByEmail('email@example.com');
} catch (\Exception $e) {
    // handle error
}
```

---

# Requirements

* PHP 8+
* PSR-18 HTTP client (optional but supported)
* Listmonk API access enabled


