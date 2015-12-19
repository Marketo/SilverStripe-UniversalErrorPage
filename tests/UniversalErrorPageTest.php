<?php

/**
 * @author Kirk Mayo <kirk.mayo@solnet.co.nz>
 *
 * Some global error page controller testing
 * Copies a lot from the ErrorPageTest in cms
 */

class  UniversalErrorPageTest extends FunctionalTest {

    protected static $fixture_file = 'UniversalErrorPageTest.yml';
    
    protected $orig = array();
    
    protected $tmpAssetsPath = '';
    
    public function setUp() {
        parent::setUp();

        i18n::set_locale('en_EN');
        $errorPath =  dirname(__FILE__);
        
        Config::inst()->update('Director', 'environment_type', 'live');
        Config::inst()->update('UniversalErrorPage', 'DefaultPath', "$errorPath/");
    }

    public function test404ErrorPage() {
        $page = $this->objFromFixture('UniversalErrorPage', '404');
        // ensure that the errorpage exists as a physical file
        $page->publish('Stage', 'Live');
        
        $response = $this->get('nonexistent-page');
        
        /* We have body text from the error page */
        $this->assertEquals($response->getBody(), 'Test Error Page');

        /* Status code of the SS_HTTPResponse for error page is "404" */
        $this->assertEquals($response->getStatusCode(), '404', 'Status code of the SS_HTTPResponse for error page is "404"');
        
        /* Status message of the SS_HTTPResponse for error page is "Not Found" */
        $this->assertEquals($response->getStatusDescription(), 'Not Found', 'Status message of the HTTResponse for error page is "Not found"');
    }
    
    public function testBehaviourOf403() {
        $page = $this->objFromFixture('UniversalErrorPage', '403');
        $page->publish('Stage', 'Live');
        
        $response = $this->get($page->RelativeLink());

        /* We have body text from the error page */
        $this->assertEquals($response->getBody(), '403 error page');
        
        $this->assertEquals($response->getStatusCode(), '403');
    }
    
    public function testSecurityError() {
        // Generate 404 page
        $page = $this->objFromFixture('UniversalErrorPage', '404');
        $page->publish('Stage', 'Live');
        
        // Test invalid action
        $response = $this->get('Security/nosuchaction');
        $this->assertEquals($response->getStatusCode(), '404');
        $this->assertNotNull($response->getBody());
        $this->assertContains('text/html', $response->getHeader('Content-Type'));
    }
}
