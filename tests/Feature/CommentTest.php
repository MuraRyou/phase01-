<?php

it('has comment page', function () {
    $response = $this->get('/comment');

    $response->assertStatus(200);
});
