<?php

namespace MegaWubs\Avid;

use Illuminate\Support\Collection;

class Avid
{
    /**
     * @var Collection|Collection[]
     */
    private $models = [];

    public function __construct()
    {
        $this->models = new Collection();
    }

    public function add($items)
    {
        if (is_scalar($items) && !is_array($items)) {
            throw new \InvalidArgumentException('The given parameter must be an object');
        }

        if ($items instanceof Collection) {
            $items->each(function ($item) {
                $this->add($item);
            });

            return;
        }

        if (is_array($items)) {
            $this->add(collect($items));

            return;
        }

        if (is_object($items)) {
            $this->push($items);

            return;
        }
    }

    public function script()
    {
        return $this->models->map(function (Collection $modelInstances, $name) {
            return 'avidItems["' . strtolower($name) . '"]=' . $modelInstances->toJson();
        })->implode("\n")
            ;
    }

    private function push($item)
    {
        $name = $this->getModelName($item);
        if ($this->models->has($name)) {
            $this->models->get($name)->push($item);

            return $this;
        }
        $this->models->put($name, collect([$item]));

        return $this;
    }

    private function getModelName($item)
    {
        return (new \ReflectionClass($item))->getShortName();
    }
}
