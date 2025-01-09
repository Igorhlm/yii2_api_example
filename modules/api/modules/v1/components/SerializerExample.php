<?php

declare(strict_types=1);

namespace app\modules\api\modules\v1\components;

use app\modules\api\modules\v1\components\PaginationExample;
use yii\data\DataProviderInterface;

/**
 * Класс сериализации
 */
class SerializerExample extends \yii\rest\Serializer
{
    /**
     * @var string
     */
    public $collectionEnvelope = 'items';

    /**
     * @var string
     */
    public $totalCountHeader = 'X-Pagination-Total';

    /**
     * @var string
     */

    public $offsetHeader = 'X-Pagination-Offset';
    /**
     * @var string
     */

    public $limitHeader = 'X-Pagination-Limit';

    /**
     * Serializes a data provider.
     * @param DataProviderInterface $dataProvider
     * @return array the array representation of the data provider.
     */
    protected function serializeDataProvider($dataProvider)
    {
        if ($this->preserveKeys) {
            $models = $dataProvider->getModels();
        } else {
            $models = array_values($dataProvider->getModels());
        }
        $models = $this->serializeModels($models);

        if (($pagination = $dataProvider->getPagination()) !== false) {
            $this->addPaginationHeaders($pagination);
        }

        $result = [
            $this->collectionEnvelope => $models,
        ];
        $pagination = $dataProvider->getPagination();
        if ($pagination !== false) {
            return array_merge($result, $this->serializePagination($pagination));
        }

        return $result;
    }

    /**
     * Serializes a pagination into an array.
     * @param PaginationExample $pagination
     * @return array the array representation of the pagination
     */
    protected function serializePagination($pagination)
    {
        return [
            'meta' => [
                'total' => $pagination->totalCount,
                'offset' => $pagination->getOffset(),
                'limit' => $pagination->getLimit(),
            ],
        ];
    }

    /**
     * Adds HTTP headers about the pagination to the response.
     * @param PaginationExample $pagination
     */
    protected function addPaginationHeaders($pagination)
    {
        $this->response->getHeaders()
            ->set($this->totalCountHeader, $pagination->totalCount)
            ->set($this->offsetHeader, $pagination->getOffset())
            ->set($this->limitHeader, $pagination->getLimit());
    }
}
