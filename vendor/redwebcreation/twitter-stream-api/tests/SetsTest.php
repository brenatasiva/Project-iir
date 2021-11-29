<?php

use RWC\TwitterStream\Fieldset;
use RWC\TwitterStream\Sets;

it('can render a group of fieldsets', function () {
    $sets = new Sets(
        new Fieldset('fields', 'a', 'b', 'c'),
        new Fieldset('someMore', 'd', 'e', 'f')
    );

    expect((string)$sets)->toBe('fields=a,b,c&someMore=d,e,f');
});