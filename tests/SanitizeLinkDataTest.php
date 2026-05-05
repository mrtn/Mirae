<?php
/**
 * Smoke tests for Mirae_Admin::sanitize_link_data().
 *
 * The sanitizer is the main validation gate for every link the user submits,
 * so we want to lock down: platform whitelist, URL scheme check, button-text
 * fallback, and the resequencing behaviour.
 *
 * @package Mirae
 */

use PHPUnit\Framework\TestCase;

final class SanitizeLinkDataTest extends TestCase {

	/** @var Mirae_Admin */
	private $admin;

	protected function setUp(): void {
		$this->admin = new Mirae_Admin( 'mirae', '0-test' );
	}

	public function test_invalid_json_returns_empty_array_json(): void {
		$out = $this->admin->sanitize_link_data( 'not json at all' );
		$this->assertSame( '[]', $out );
	}

	public function test_unknown_platform_is_dropped(): void {
		$in  = json_encode( array(
			array( 'platform' => 'definitely-not-a-platform', 'link' => 'https://example.com', 'buttonText' => 'X' ),
		) );
		$out = json_decode( $this->admin->sanitize_link_data( $in ), true );
		$this->assertSame( array(), $out );
	}

	public function test_javascript_url_is_dropped(): void {
		$in  = json_encode( array(
			array( 'platform' => 'github', 'link' => 'javascript:alert(1)', 'buttonText' => 'X' ),
		) );
		$out = json_decode( $this->admin->sanitize_link_data( $in ), true );
		$this->assertSame( array(), $out, 'javascript: scheme must be rejected' );
	}

	public function test_relative_url_is_dropped(): void {
		$in  = json_encode( array(
			array( 'platform' => 'github', 'link' => '/relative/path', 'buttonText' => 'X' ),
		) );
		$out = json_decode( $this->admin->sanitize_link_data( $in ), true );
		$this->assertSame( array(), $out );
	}

	public function test_valid_row_passes_through_with_resequence(): void {
		$in  = json_encode( array(
			array( 'platform' => 'github',  'link' => 'https://github.com/mrtn',     'buttonText' => 'My GitHub' ),
			array( 'platform' => 'x', 'link' => 'https://twitter.com/mrtn',    'buttonText' => '' ),
		) );
		$out = json_decode( $this->admin->sanitize_link_data( $in ), true );

		$this->assertCount( 2, $out );
		$this->assertSame( 1, $out[0]['sequence'] );
		$this->assertSame( 'github', $out[0]['platform'] );
		$this->assertSame( 'https://github.com/mrtn', $out[0]['link'] );
		$this->assertSame( 'My GitHub', $out[0]['buttonText'] );

		$this->assertSame( 2, $out[1]['sequence'] );
		$this->assertSame( 'x', $out[1]['platform'] );
		$this->assertSame( 'Default', $out[1]['buttonText'], 'empty buttonText must fall back to Default' );
	}

	public function test_mixed_valid_and_invalid_rows_resequence_continuously(): void {
		$in  = json_encode( array(
			array( 'platform' => 'github',     'link' => 'https://github.com/mrtn', 'buttonText' => 'A' ),
			array( 'platform' => 'bogus',      'link' => 'https://example.com',     'buttonText' => 'B' ),
			array( 'platform' => 'x',    'link' => 'ftp://nope',              'buttonText' => 'C' ),
			array( 'platform' => 'instagram',  'link' => 'https://instagram.com/x', 'buttonText' => 'D' ),
		) );
		$out = json_decode( $this->admin->sanitize_link_data( $in ), true );

		$this->assertCount( 2, $out );
		$this->assertSame( array( 1, 2 ), array_column( $out, 'sequence' ) );
		$this->assertSame( array( 'github', 'instagram' ), array_column( $out, 'platform' ) );
	}

	public function test_non_array_row_is_skipped(): void {
		$in  = json_encode( array(
			'this is a string row, not an array',
			array( 'platform' => 'github', 'link' => 'https://github.com/mrtn', 'buttonText' => 'X' ),
		) );
		$out = json_decode( $this->admin->sanitize_link_data( $in ), true );

		$this->assertCount( 1, $out );
		$this->assertSame( 'github', $out[0]['platform'] );
	}

	public function test_color_sanitizer_accepts_hex_only(): void {
		$this->assertSame( '#abc',    $this->admin->sanitize_color( '#abc' ) );
		$this->assertSame( '#aabbcc', $this->admin->sanitize_color( '#aabbcc' ) );
		$this->assertSame( '',        $this->admin->sanitize_color( 'red' ) );
		$this->assertSame( '',        $this->admin->sanitize_color( 'rgba(0,0,0,1)' ) );
		$this->assertSame( '',        $this->admin->sanitize_color( '#xyz' ) );
	}

	public function test_alpha_sanitizer_clamps_and_rounds(): void {
		$this->assertSame( '0',    $this->admin->sanitize_alpha( '-5' ) );
		$this->assertSame( '1',    $this->admin->sanitize_alpha( '99' ) );
		$this->assertSame( '0.5',  $this->admin->sanitize_alpha( '0.5' ) );
		$this->assertSame( '0.33', $this->admin->sanitize_alpha( '0.333' ) );
	}

	public function test_hex_to_rgba_roundtrip(): void {
		$this->assertSame( 'rgba(255, 255, 255, 0.5)', Mirae_Util::hex_to_rgba( '#fff', 0.5 ) );
		$this->assertSame( 'rgba(170, 187, 204, 1)',   Mirae_Util::hex_to_rgba( '#aabbcc', 1 ) );
		$this->assertStringStartsWith( 'rgba(0,0,0,', Mirae_Util::hex_to_rgba( 'not-a-hex', 0.7 ) );
	}
}
