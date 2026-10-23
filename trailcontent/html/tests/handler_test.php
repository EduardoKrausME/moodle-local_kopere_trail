<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

/**
 * HTML content security regression test.
 *
 * @package   trailcontent_html
 * @copyright 2026 Eduardo Kraus {@link https://eduardokraus.com}
 * @license   http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

namespace trailcontent_html;

/**
 * Verifies that stored HTML is sanitized when rendered.
 *
 * @covers \trailcontent_html\handler
 */
final class handler_test extends \advanced_testcase {
    /**
     * Untrusted HTML must not be rendered as executable markup.
     *
     * @return void
     */
    public function test_export_view_data_sanitizes_html(): void {
        $this->resetAfterTest();

        $step = (object)[
            "contentconfig" => json_encode([
                "html" => "<p>Safe text</p><script>alert(1)</script>",
                "format" => FORMAT_HTML,
            ]),
        ];

        $view = (new handler())->export_view_data($step, 0);

        $this->assertArrayHasKey("html", $view);
        $this->assertStringContainsString("Safe text", $view["html"]);
        $this->assertStringNotContainsString("<script", $view["html"]);
    }
}
