<?php 

namespace Arthurnumen;

use Illuminate\Http\Response;

use League\Fractal\Manager;
use League\Fractal\TransformerAbstract;
use League\Fractal\Resource\Item;
use League\Fractal\Resource\Collection;
use League\Fractal\Resource\ResourceInterface;
use League\Fractal\Pagination\PaginatorInterface;
use League\Fractal\Pagination\IlluminatePaginatorAdapter;
use League\Fractal\Serializer\JsonApiSerializer;
use Illuminate\Pagination\Paginator as IlluminatePaginator;

class JsonSerializer
{
    private $manager;

    public function __construct(Manager $manager)
    {
        $this->manager = $manager;
        $this->manager->setSerializer(new ApiSerializer);
    }

    public function getManager()
    {
        return $this->manager;
    }

    public function item($rootname, $item, TransformerAbstract $transformer, $options = array())
    {
        $resource = new Item($item, $transformer, $rootname);

        if(isset($options['meta'])) {
            foreach($options['meta'] as $metaKey => $metaItem) {
                $resource->setMetaValue($metaKey, $metaItem);
            }
        }

        return $this->buildResponse($resource);
    }

    public function collection($rootname, $items, TransformerAbstract $transformer, $options = array())
    {
        $resources = new Collection($items, $transformer, $rootname);

        if(isset($options['meta'])) {
            foreach($options['meta'] as $metaKey => $metaItem) {
                $resources->setMetaValue($metaKey, $metaItem);
            }
        }

        if (array_key_exists('cursor', $options)) {
            $resources->setCursor($options['cursor']);
        }

        if (array_key_exists('callback', $options)) {
            call_user_func($options['callback'], $resources);
        }

        if ($items instanceof IlluminatePaginator) {
            $paginatorInterface = array_key_exists('paginatorInterface', $options) ? $options['paginatorInterface'] : null;
            $this->paginateCollection($resources, $items, $paginatorInterface);
        }
        
        return isset($options['raw']) ? $resources : $this->buildResponse($resources);
    }

    public function itemData($item, TransformerAbstract $transformer, $options = array()) {
        $resource = new Item($item, $transformer);

        if(isset($options['meta'])) {
            foreach($options['meta'] as $metaKey => $metaItem) {
                $resource->setMetaValue($metaKey, $metaItem);
            }
        }

        return $resource->getData();
    }

    private function paginateCollection(Collection $collection, IlluminatePaginator $paginator, PaginatorInterface $adapter = null)
    {
        if (is_null($adapter)) {
            $adapter = new IlluminatePaginatorAdapter($paginator);
        }
        
        $collection->setPaginator($adapter);
    }

    private function buildResponse(ResourceInterface $resource)
    {
        $data = $this->manager->createData($resource);
        
        return response()->json($data->toArray());
    }
}
