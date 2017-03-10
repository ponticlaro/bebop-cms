<?php
namespace Presets\Shortcodes;

use \UnitTester;
use phpmock\mockery\PHPMockery;
use Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image;

class ImageCest
{
  public function _before(UnitTester $I)
  {

  }

  public function _after(UnitTester $I)
  {
    \Mockery::close();
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image::__construct
   * 
   * @param UnitTester $I Tester Module
   */
  public function create(UnitTester $I)
  {
    // Create test instance
    $shortcode = new Image;

    // Get $id property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'id');
    $prop_refl->setAccessible(true);

    // Verify that $id matches expected value
    $I->assertEquals('image', $prop_refl->getValue($shortcode));

    // Get $template_path property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'template_path');
    $prop_refl->setAccessible(true);

    // Set expected template path
    $template_path = str_replace('/tests/unit', '/src', __DIR__) .'/templates/image.php';

    // Verify that $template_path matches expected value
    $I->assertEquals($template_path, $prop_refl->getValue($shortcode));

    // Get $default_attrs property and make it accessible
    $prop_refl = new \ReflectionProperty('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'default_attrs');
    $prop_refl->setAccessible(true);

    // Verify that $default_attrs matches expected value
    $I->assertEquals([
      'id'      => null,
      'size'    => 'large',
      'url'     => null,
      'caption' => null,
      'alt'     => null
    ], $prop_refl->getValue($shortcode));
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image::render
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderUsingUrl(UnitTester $I)
  {
    // Set test variables
    $image_size    = 'large';
    $image_url     = 'http://unit-test';
    $image_alt     = '';
    $image_caption = '';

    // Create shortcode mock
    $coll = \Mockery::mock('Ponticlaro\Bebop\Common\Patterns\CollectionInterface');

    // Set expectations for $collection
    $coll->shouldReceive('get')
         ->with('id')
         ->andReturn(null)
         ->once()
         ->mock();

    $coll->shouldReceive('get')
         ->with('url')
         ->andReturn($image_url)
         ->once()
         ->mock();

    $coll->shouldReceive('getAll')
         ->andReturn([
           'url'     => $image_url,
           'alt'     => $image_alt,
           'caption' => $image_caption,
         ])
         ->once()
         ->mock();

    // Create test instance
    $image = new Image;

    // Get ::render reflection
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'render');
  
    // Test ::render
    ob_start();
    $method_refl->invokeArgs($image, [
      $coll,
      null,
      'image'
    ]);
    $html = ob_get_clean();

    // Clean $html
    $html = preg_replace('/\s+/S', ' ', $html);
    $html = trim($html);

    // Define expected HTML
    $expected_html  = '<figure class="media-wrap is-image">';
    $expected_html .= ' <img alt="" src="'. $image_url .'">';
    $expected_html .= ' </figure>';

    // Verify output matches
    $I->assertEquals($expected_html, $html);
  }

 /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image::render
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderUsingUrlWithAltText(UnitTester $I)
  {
    // Set test variables
    $image_size    = 'large';
    $image_url     = 'http://unit-test';
    $image_alt     = 'Alt Text';
    $image_caption = '';

    // Create shortcode mock
    $coll = \Mockery::mock('Ponticlaro\Bebop\Common\Patterns\CollectionInterface');

    // Set expectations for $collection
    $coll->shouldReceive('get')
         ->with('id')
         ->andReturn(null)
         ->once()
         ->mock();

    $coll->shouldReceive('get')
         ->with('url')
         ->andReturn($image_url)
         ->once()
         ->mock();

    $coll->shouldReceive('getAll')
         ->andReturn([
           'url'     => $image_url,
           'alt'     => $image_alt,
           'caption' => $image_caption,
         ])
         ->once()
         ->mock();

    // Create test instance
    $image = new Image;

    // Get ::render reflection
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'render');
  
    // Test ::render
    ob_start();
    $method_refl->invokeArgs($image, [
      $coll,
      null,
      'image'
    ]);
    $html = ob_get_clean();

    // Clean $html
    $html = preg_replace('/\s+/S', ' ', $html);
    $html = trim($html);

    // Define expected HTML
    $expected_html  = '<figure class="media-wrap is-image">';
    $expected_html .= ' <img alt="'. $image_alt .'" src="'. $image_url .'">';
    $expected_html .= ' </figure>';

    // Verify output matches
    $I->assertEquals($expected_html, $html);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image::render
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderUsingUrlWithCaption(UnitTester $I)
  {
    // Set test variables
    $image_size    = 'large';
    $image_url     = 'http://unit-test';
    $image_alt     = '';
    $image_caption = 'Caption';

    // Create shortcode mock
    $coll = \Mockery::mock('Ponticlaro\Bebop\Common\Patterns\CollectionInterface');

    // Set expectations for $collection
    $coll->shouldReceive('get')
         ->with('id')
         ->andReturn(null)
         ->once()
         ->mock();

    $coll->shouldReceive('get')
         ->with('url')
         ->andReturn($image_url)
         ->once()
         ->mock();

    $coll->shouldReceive('getAll')
         ->andReturn([
           'url'     => $image_url,
           'alt'     => $image_alt,
           'caption' => $image_caption,
         ])
         ->once()
         ->mock();

    // Create test instance
    $image = new Image;

    // Get ::render reflection
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'render');
  
    // Test ::render
    ob_start();
    $method_refl->invokeArgs($image, [
      $coll,
      null,
      'image'
    ]);
    $html = ob_get_clean();

    // Clean $html
    $html = preg_replace('/\s+/S', ' ', $html);
    $html = trim($html);

    // Define expected HTML
    $expected_html  = '<figure class="media-wrap is-image">';
    $expected_html .= ' <img alt="" src="'. $image_url .'">';
    $expected_html .= ' <figcaption class="caption">'. $image_caption .'</figcaption>';
    $expected_html .= ' </figure>';

    // Verify output matches
    $I->assertEquals($expected_html, $html);
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image::render
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderUsingUrlWithAltTextAndCaption(UnitTester $I)
  {
    // Set test variables
    $image_size    = 'large';
    $image_url     = 'http://unit-test';
    $image_alt     = 'Alt Text';
    $image_caption = 'Caption';

    // Create shortcode mock
    $coll = \Mockery::mock('Ponticlaro\Bebop\Common\Patterns\CollectionInterface');

    // Set expectations for $collection
    $coll->shouldReceive('get')
         ->with('id')
         ->andReturn(null)
         ->once()
         ->mock();

    $coll->shouldReceive('get')
         ->with('url')
         ->andReturn($image_url)
         ->once()
         ->mock();

    $coll->shouldReceive('getAll')
         ->andReturn([
           'url'     => $image_url,
           'alt'     => $image_alt,
           'caption' => $image_caption,
         ])
         ->once()
         ->mock();

    // Create test instance
    $image = new Image;

    // Get ::render reflection
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'render');
  
    // Test ::render
    ob_start();
    $method_refl->invokeArgs($image, [
      $coll,
      null,
      'image'
    ]);
    $html = ob_get_clean();

    // Clean $html
    $html = preg_replace('/\s+/S', ' ', $html);
    $html = trim($html);

    // Define expected HTML
    $expected_html  = '<figure class="media-wrap is-image">';
    $expected_html .= ' <img alt="'. $image_alt .'" src="'. $image_url .'">';
    $expected_html .= ' <figcaption class="caption">'. $image_caption .'</figcaption>';
    $expected_html .= ' </figure>';

    // Verify output matches
    $I->assertEquals($expected_html, $html);  
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image::render
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderUsingId(UnitTester $I)
  {
    // Set test variables
    $image_id      = 111;
    $image_size    = 'large';
    $image_url     = 'http://unit-test';
    $image_alt     = '';
    $image_caption = '';
 
    // Mock wp_get_attachment_image_src
    PHPMockery::mock('Ponticlaro\Bebop\Cms\Presets\Shortcodes', 'wp_get_attachment_image_src')
              ->withArgs([
                $image_id,
                $image_size,
              ])
              ->andReturn([
                $image_url
              ])
              ->once();

    // Create CollectionInterface mock
    $coll = \Mockery::mock('Ponticlaro\Bebop\Common\Patterns\CollectionInterface');

    // Set expectations for CollectionInterface
    $coll->shouldReceive('get')
         ->with('id')
         ->andReturn($image_id)
         ->twice()
         ->mock();

    $coll->shouldReceive('get')
         ->with('size')
         ->andReturn($image_size)
         ->twice()
         ->mock();

    $coll->shouldReceive('set')
         ->withArgs(['url', null])
         ->once()
         ->mock();

    $coll->shouldReceive('set')
         ->withArgs(['url', $image_url])
         ->once()
         ->mock();

    $coll->shouldReceive('get')
         ->with('url')
         ->andReturn($image_url)
         ->once()
         ->mock();

    $coll->shouldReceive('getAll')
         ->andReturn([
           'url'     => $image_url,
           'alt'     => $image_alt,
           'caption' => $image_caption,
         ])
         ->once()
         ->mock();

    // Create test instance
    $image = new Image;

    // Get ::render reflection
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'render');
  
    // Test ::render
    ob_start();
    $method_refl->invokeArgs($image, [
      $coll,
      null,
      'image'
    ]);
    $html = ob_get_clean();

    // Clean $html
    $html = preg_replace('/\s+/S', ' ', $html);
    $html = trim($html);

    // Define expected HTML
    $expected_html  = '<figure class="media-wrap is-image">';
    $expected_html .= ' <img alt="" src="'. $image_url .'">';
    $expected_html .= ' </figure>';

    // Verify output matches
    $I->assertEquals($expected_html, $html);  
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image::render
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderUsingIdWithAltText(UnitTester $I)
  {
    // Set test variables
    $image_id      = 111;
    $image_size    = 'large';
    $image_url     = 'http://unit-test';
    $image_alt     = 'Alt Text';
    $image_caption = '';
 
    // Mock wp_get_attachment_image_src
    PHPMockery::mock('Ponticlaro\Bebop\Cms\Presets\Shortcodes', 'wp_get_attachment_image_src')
              ->withArgs([
                $image_id,
                $image_size,
              ])
              ->andReturn([
                $image_url
              ])
              ->once();

    // Create CollectionInterface mock
    $coll = \Mockery::mock('Ponticlaro\Bebop\Common\Patterns\CollectionInterface');

    // Set expectations for CollectionInterface
    $coll->shouldReceive('get')
         ->with('id')
         ->andReturn($image_id)
         ->twice()
         ->mock();

    $coll->shouldReceive('get')
         ->with('size')
         ->andReturn($image_size)
         ->twice()
         ->mock();

    $coll->shouldReceive('set')
         ->withArgs(['url', null])
         ->once()
         ->mock();

    $coll->shouldReceive('set')
         ->withArgs(['url', $image_url])
         ->once()
         ->mock();

    $coll->shouldReceive('get')
         ->with('url')
         ->andReturn($image_url)
         ->once()
         ->mock();

    $coll->shouldReceive('getAll')
         ->andReturn([
           'url'     => $image_url,
           'alt'     => $image_alt,
           'caption' => $image_caption,
         ])
         ->once()
         ->mock();

    // Create test instance
    $image = new Image;

    // Get ::render reflection
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'render');
  
    // Test ::render
    ob_start();
    $method_refl->invokeArgs($image, [
      $coll,
      null,
      'image'
    ]);
    $html = ob_get_clean();

    // Clean $html
    $html = preg_replace('/\s+/S', ' ', $html);
    $html = trim($html);

    // Define expected HTML
    $expected_html  = '<figure class="media-wrap is-image">';
    $expected_html .= ' <img alt="'. $image_alt .'" src="'. $image_url .'">';
    $expected_html .= ' </figure>';

    // Verify output matches
    $I->assertEquals($expected_html, $html);  
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image::render
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderUsingIdWithCaption(UnitTester $I)
  {
    // Set test variables
    $image_id      = 111;
    $image_size    = 'large';
    $image_url     = 'http://unit-test';
    $image_alt     = '';
    $image_caption = 'Caption';
 
    // Mock wp_get_attachment_image_src
    PHPMockery::mock('Ponticlaro\Bebop\Cms\Presets\Shortcodes', 'wp_get_attachment_image_src')
              ->withArgs([
                $image_id,
                $image_size,
              ])
              ->andReturn([
                $image_url
              ])
              ->once();

    // Create CollectionInterface mock
    $coll = \Mockery::mock('Ponticlaro\Bebop\Common\Patterns\CollectionInterface');

    // Set expectations for CollectionInterface
    $coll->shouldReceive('get')
         ->with('id')
         ->andReturn($image_id)
         ->twice()
         ->mock();

    $coll->shouldReceive('get')
         ->with('size')
         ->andReturn($image_size)
         ->twice()
         ->mock();

    $coll->shouldReceive('set')
         ->withArgs(['url', null])
         ->once()
         ->mock();

    $coll->shouldReceive('set')
         ->withArgs(['url', $image_url])
         ->once()
         ->mock();

    $coll->shouldReceive('get')
         ->with('url')
         ->andReturn($image_url)
         ->once()
         ->mock();

    $coll->shouldReceive('getAll')
         ->andReturn([
           'url'     => $image_url,
           'alt'     => $image_alt,
           'caption' => $image_caption,
         ])
         ->once()
         ->mock();

    // Create test instance
    $image = new Image;

    // Get ::render reflection
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'render');
  
    // Test ::render
    ob_start();
    $method_refl->invokeArgs($image, [
      $coll,
      null,
      'image'
    ]);
    $html = ob_get_clean();

    // Clean $html
    $html = preg_replace('/\s+/S', ' ', $html);
    $html = trim($html);

    // Define expected HTML
    $expected_html  = '<figure class="media-wrap is-image">';
    $expected_html .= ' <img alt="" src="'. $image_url .'">';
    $expected_html .= ' <figcaption class="caption">'. $image_caption .'</figcaption>';
    $expected_html .= ' </figure>';

    // Verify output matches
    $I->assertEquals($expected_html, $html);  
  }

  /**
   * @author cristianobaptista
   * @covers Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image::render
   * 
   * @param UnitTester $I Tester Module
   */
  public function renderUsingIdWithAltTextAndCaption(UnitTester $I)
  {
    // Set test variables
    $image_id      = 111;
    $image_size    = 'large';
    $image_url     = 'http://unit-test';
    $image_alt     = 'Alt Text';
    $image_caption = 'Caption';
 
    // Mock wp_get_attachment_image_src
    PHPMockery::mock('Ponticlaro\Bebop\Cms\Presets\Shortcodes', 'wp_get_attachment_image_src')
              ->withArgs([
                $image_id,
                $image_size,
              ])
              ->andReturn([
                $image_url
              ])
              ->once();

    // Create CollectionInterface mock
    $coll = \Mockery::mock('Ponticlaro\Bebop\Common\Patterns\CollectionInterface');

    // Set expectations for CollectionInterface
    $coll->shouldReceive('get')
         ->with('id')
         ->andReturn($image_id)
         ->twice()
         ->mock();

    $coll->shouldReceive('get')
         ->with('size')
         ->andReturn($image_size)
         ->twice()
         ->mock();

    $coll->shouldReceive('set')
         ->withArgs(['url', null])
         ->once()
         ->mock();

    $coll->shouldReceive('set')
         ->withArgs(['url', $image_url])
         ->once()
         ->mock();

    $coll->shouldReceive('get')
         ->with('url')
         ->andReturn($image_url)
         ->once()
         ->mock();

    $coll->shouldReceive('getAll')
         ->andReturn([
           'url'     => $image_url,
           'alt'     => $image_alt,
           'caption' => $image_caption,
         ])
         ->once()
         ->mock();

    // Create test instance
    $image = new Image;

    // Get ::render reflection
    $method_refl = new \ReflectionMethod('Ponticlaro\Bebop\Cms\Presets\Shortcodes\Image', 'render');
  
    // Test ::render
    ob_start();
    $method_refl->invokeArgs($image, [
      $coll,
      null,
      'image'
    ]);
    $html = ob_get_clean();

    // Clean $html
    $html = preg_replace('/\s+/S', ' ', $html);
    $html = trim($html);

    // Define expected HTML
    $expected_html  = '<figure class="media-wrap is-image">';
    $expected_html .= ' <img alt="'. $image_alt .'" src="'. $image_url .'">';
    $expected_html .= ' <figcaption class="caption">'. $image_caption .'</figcaption>';
    $expected_html .= ' </figure>';

    // Verify output matches
    $I->assertEquals($expected_html, $html);  
  }
}