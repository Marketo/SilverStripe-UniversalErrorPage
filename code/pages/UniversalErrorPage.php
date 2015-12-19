<?php

class UniversalErrorPage extends ErrorPage {

    private static $description = 'Use a shared HTML or PHP file on the server to display the error';

    public function requireDefaultRecords() {
        if (Config::inst()->get('UniversalErrorPage', 'ConvertOnDevBuild')) {
            $ErrorPages = ErrorPage::get()->filter('ClassName','ErrorPage');
            foreach ($ErrorPages as $ErrorPage) {
                $ErrorPage->ClassName = 'UniversalErrorPage';
                $ErrorPage->write();
                $ErrorPage->doPublish();
                DB::alteration_message("#$ErrorPage->ID $ErrorPage->Title changed to UniversalErrorPage.", "changed");
            }
        }
    }

    /**
     * Stop the static html file being created in assets
     * TODO: This does not prevent /dev/build from creating them...
     */
    public function writeStaticPage() {
        return true;
    }
}

/**
 * Controller for UniversalErrorPage.
 *
 * @package cms
 */
class UniversalErrorPage_Controller extends ErrorPage_Controller {

    private $defaultErrorPagePath = '/var/www/error_pages/';

    public function handleRequest(SS_HTTPRequest $request, DataModel $model = NULL) {
        $body = null;
        $lang = i18n::get_locale();
        $path = Config::inst()->get('UniversalErrorPage', 'DefaultPath');
        if (!$path) $path = $this->defaultErrorPagePath;
        $errorPages = array(
            Config::inst()->get('UniversalErrorPage', $this->ErrorCode),
            $path . "error-{$this->ErrorCode}-$lang.html",
            $path . "error-{$this->ErrorCode}-$lang.php",
            $path . 'error.html',
            $path . 'error.php'
        );
        $this->extend('updateHandleRequest', $errorPages);
        // now check if any of the pages exist
        foreach ($errorPages as $errorPage) {
            if (!$body && file_exists($errorPage)) {
                $ext = pathinfo($errorPage, PATHINFO_EXTENSION);
                if ($ext == 'php') {
                    ob_start();
                    include $errorPage;
                    $body = ob_get_clean();
                } else {
                    $body = file_get_contents($errorPage);
                }
                break;
            }
        }

        if ($body) {
            $response = new SS_HTTPResponse();    
            $response->setStatusCode($this->ErrorCode);
            $response->setBody($body);
            return $response;
        }

        return parent::handleRequest($request, $model);
    }
}

