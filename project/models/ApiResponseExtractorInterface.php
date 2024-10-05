<?php

namespace app\models;

interface ApiResponseExtractorInterface
{
    public function extract(callable $comparator);
}