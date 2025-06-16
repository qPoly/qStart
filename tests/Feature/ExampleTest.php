<?php

test('returns a successful redirect', function () {
    $response = $this->get('/');

    $response->assertStatus(302);
});
