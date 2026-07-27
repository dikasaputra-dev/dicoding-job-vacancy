<?php

namespace Tests\Unit\Support;

use App\Support\VacancyDescriptionSanitizer;
use PHPUnit\Framework\TestCase;

class VacancyDescriptionSanitizerTest extends TestCase
{
    public function test_it_keeps_allowed_formatting_and_removes_unsafe_html(): void
    {
        $sanitizer = new VacancyDescriptionSanitizer;

        $result = $sanitizer->sanitize(
            <<<'HTML'
<script>alert('xss')</script>
<h2 onclick="alert('xss')">Job Description</h2>
<p style="color:red">
    Build <strong class="highlight">safe</strong> products.
</p>
<a href="javascript:alert('xss')">Read more</a>
HTML,
        );

        $this->assertStringContainsString(
            '<h2>Job Description</h2>',
            $result,
        );

        $this->assertStringContainsString(
            '<strong>safe</strong>',
            $result,
        );

        $this->assertStringContainsString(
            'Read more',
            $result,
        );

        $this->assertStringNotContainsString(
            '<script',
            $result,
        );

        $this->assertStringNotContainsString(
            'alert(',
            $result,
        );

        $this->assertStringNotContainsString(
            'onclick',
            $result,
        );

        $this->assertStringNotContainsString(
            'style=',
            $result,
        );

        $this->assertStringNotContainsString(
            'class=',
            $result,
        );

        $this->assertStringNotContainsString(
            'href=',
            $result,
        );

        $this->assertStringNotContainsString(
            '<a',
            $result,
        );
    }

    public function test_it_removes_elements_that_can_embed_executable_content(): void
    {
        $sanitizer = new VacancyDescriptionSanitizer;

        $result = $sanitizer->sanitize(
            <<<'HTML'
<p>Safe content</p>
<iframe src="https://example.com"></iframe>
<object data="dangerous-file"></object>
<svg><script>alert('xss')</script></svg>
HTML,
        );

        $this->assertSame(
            '<p>Safe content</p>',
            $result,
        );
    }

    public function test_it_preserves_plain_text_inside_unsupported_elements(): void
    {
        $sanitizer = new VacancyDescriptionSanitizer;

        $result = $sanitizer->sanitize(
            '<div><span>Backend Developer</span></div>',
        );

        $this->assertSame(
            'Backend Developer',
            $result,
        );
    }
}
