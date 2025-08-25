<?php

namespace Kafka\Tests\Fakes;

class HandlingJob
{
    public function handle(): void
    {
        HandleHelper::$handled = true;
    }
}