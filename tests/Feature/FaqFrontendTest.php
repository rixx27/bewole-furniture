<?php

use App\Models\Faq;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('faq component filters active faqs and orders by sort order', function () {
    $activeFaq2 = Faq::create([
        'question' => 'Pertanyaan Aktif 2',
        'answer' => 'Jawaban Aktif 2',
        'sort_order' => 2,
        'is_active' => true,
    ]);

    $activeFaq1 = Faq::create([
        'question' => 'Pertanyaan Aktif 1',
        'answer' => 'Jawaban Aktif 1',
        'sort_order' => 1,
        'is_active' => true,
    ]);

    $inactiveFaq = Faq::create([
        'question' => 'Pertanyaan Nonaktif',
        'answer' => 'Jawaban Nonaktif',
        'sort_order' => 0,
        'is_active' => false,
    ]);

    $response = $this->get(route('home'));

    $response->assertStatus(200);
    $response->assertSee('Pertanyaan Aktif 1');
    $response->assertSee('Pertanyaan Aktif 2');
    $response->assertDontSee('Pertanyaan Nonaktif');

    $content = $response->getContent();
    $pos1 = strpos($content, 'Pertanyaan Aktif 1');
    $pos2 = strpos($content, 'Pertanyaan Aktif 2');
    
    expect($pos1)->not->toBeFalse();
    expect($pos2)->not->toBeFalse();
    expect($pos1)->toBeLessThan($pos2);
});
