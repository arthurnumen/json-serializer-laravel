<?php 

namespace Arthurnumen;

use League\Fractal\Pagination\CursorInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Resource\ResourceInterface;

class ApiSerializer extends \League\Fractal\Serializer\JsonApiSerializer
{
    /**
     * Serialize a collection.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function collection($resourceKey, array $data)
    {
        return array($resourceKey ?: 'data' => $data);
    }
    /**
     * Serialize an item.
     *
     * @param string $resourceKey
     * @param array  $data
     *
     * @return array
     */
    public function item($resourceKey, array $data)
    {
        return array($resourceKey ?: 'data' => $data);
    }

    public function paginator(PaginatorInterface $paginator)
    {
        $currentPage = (int) $paginator->getCurrentPage();
        $lastPage = (int) $paginator->getLastPage();
        $pagination = array(
            'total' => (int) $paginator->getTotal(),
            'count' => (int) $paginator->getCount(),
            'per_page' => (int) $paginator->getPerPage(),
            'current_page' => $currentPage,
            'total_pages' => $lastPage,
        );
        $pagination['links'] = array();
        if ($currentPage > 1) {
        $pagination['links']['previous'] = $paginator->getUrl($currentPage - 1);
        }
        if ($currentPage < $lastPage) {
        $pagination['links']['next'] = $paginator->getUrl($currentPage + 1);
        }
        return array('paging' => $pagination);
    }

    public function cursor(CursorInterface $cursor)
    {
        $cursors = array(
            'previous' => $cursor->getPrev(),
            'next' => $cursor->getNext()
        );
        return array('paging' => array('cursors' => $cursors, 
            'previousLink' => '?limit=' . $cursor->getCount() . "&previous=" . $cursor->getPrev(),
            'nextLink' => '?limit=' . $cursor->getCount() . "&next=" . $cursor->getNext()
            )
        );
    }

    public function includedData(ResourceInterface $resourceKey, array $data)
    {
        $serializedData = array();

        foreach ($data as $value) {
            foreach ($value as $includeKey => $includeValue) {
                $serializedData = array_merge_recursive($serializedData, $includeValue);
            }
        }

        return empty($serializedData) ? array() : array('linked' => $serializedData);
    }

    /**
     * Indicates if includes should be side-loaded.
     *
     * @return bool
     */
    public function sideloadIncludes()
    {
        return false;
    }
}