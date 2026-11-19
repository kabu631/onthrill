<?php

namespace Tests\Unit;

use App\Support\ContentCleaner;
use PHPUnit\Framework\TestCase;

class ContentCleanerTest extends TestCase
{
    public function test_removes_leftover_elementor_css(): void
    {
        $html = '<p>Before.</p><p> /*! elementor – v3.23.0 – 05-08-2024 */<br/> .elementor-widget-image{text-align:center}.elementor-widget-image a{display:inline-block}.elementor-widget-image img{vertical-align:middle;display:inline-block} <img src="/storage/poojas/x.webp" alt=""></p><p>After.</p>';

        $clean = ContentCleaner::clean($html);

        $this->assertStringNotContainsString('elementor', $clean);
        $this->assertStringNotContainsString('{', $clean);
        $this->assertStringContainsString('<img src="/storage/poojas/x.webp"', $clean);
        $this->assertStringContainsString('<p>Before.</p>', $clean);
        $this->assertStringContainsString('<p>After.</p>', $clean);
    }

    public function test_imported_text_loses_pictographs_and_long_dashes(): void
    {
        $html = '<p>👉 Book through <strong>Vishesh Puja</strong>.</p><h3>⭐ <strong>Gaya</strong></h3>'
            .'<p>It is not just a ritual — it is a duty. —<br>📞 Call us: May–June</p>';

        $clean = ContentCleaner::clean($html, stripAttributes: true);

        $this->assertSame('<p>Book through <strong>Vishesh Puja</strong>.</p><h3><strong>Gaya</strong></h3>'
            .'<p>It is not just a ritual, it is a duty.<br>Call us: May–June</p>', $clean);

        // Text typed into the admin editor is left as the editor wrote it.
        $this->assertSame('<p>Open 6 AM — 9 PM ⭐</p>', ContentCleaner::clean('<p>Open 6 AM — 9 PM ⭐</p>'));
    }

    public function test_keeps_ordinary_text_with_braces_and_colons(): void
    {
        $html = '<p>Timings {morning: 6 AM, evening: 7 PM} at the ghat.</p>';

        $this->assertSame($html, ContentCleaner::clean($html));
    }

    public function test_removes_old_builder_buttons_and_fixes_phone_links(): void
    {
        $html = '<p><a href="http://+919458914488"><br/> Whatsapp<br/> </a><br/> <a href="http://+919458914488"><br/> Call Us<br/> </a></p>'
            .'<p>Call <a href="http://+919458914488">Pandit Ji</a> or <a href="/pooja">Book Now</a>.</p>';

        $clean = ContentCleaner::clean($html);

        $this->assertStringNotContainsString('Whatsapp', $clean);
        $this->assertStringNotContainsString('Call Us', $clean);
        $this->assertStringContainsString('<a href="tel:+919458914488">Pandit Ji</a>', $clean);
        $this->assertStringContainsString('<a href="/pooja">Book Now</a>', $clean); // deliberate link kept
    }

    public function test_cleans_shortcodes_markdown_tracking_and_empty_tags(): void
    {
        $html = '<p>[forminator_form id="240"]</p><h4></h4><p>Visit [our site](https://www.onthrill.com/about-us) or **call**.</p>'
            .'<p><a class="decorated-link" data-start="1" href="https://example.org/page?utm_source=newsletter">Source</a></p>';

        $clean = ContentCleaner::clean($html, stripAttributes: true);

        $this->assertStringNotContainsString('forminator', $clean);
        $this->assertStringNotContainsString('<h4>', $clean);
        $this->assertStringContainsString('<a href="/about-us">our site</a>', $clean);
        $this->assertStringNotContainsString('**', $clean);
        $this->assertStringContainsString('<a href="https://example.org/page">Source</a>', $clean);
        $this->assertStringNotContainsString('data-start', $clean);
    }
}
