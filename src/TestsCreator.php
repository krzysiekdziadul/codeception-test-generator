<?php


namespace CodeceptionTestsGenerator;

use CodeceptionTestsGenerator\FeatureGenerator;

class TestsCreator
{
    public $collection;

    public function __construct($collection)
    {
        $this->collection = $collection;
        $this->run();
    }
    private function run()
    {
        return new FeatureGenerator($this->collection);
    }
}