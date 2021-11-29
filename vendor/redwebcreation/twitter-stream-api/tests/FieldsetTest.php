<?php

use RWC\TwitterStream\Fieldset;

it('can render itself', function () {
    $fieldset = new Fieldset('key', 'some', 'values');

    expect((string)$fieldset)->toBe('key=some,values');
});