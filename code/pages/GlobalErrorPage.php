<?php

class GlobalErrorPage extends ErrorPage {

    private static $description = 'Default to a sinlge error page';

    /**
     * Stop the static html file being created in assets
     */
    public function doPublish() {
        return true;
    }
}

/**
 * Controller for ErrorPages.
 *
 * @package cms
 */
class GlobalErrorPage_Controller extends ErrorPage_Controller {

    private $defaultErrorPagePath = '/var/www/error_pages/';

    public function handleRequest(SS_HTTPRequest $request, DataModel $model = NULL) {
        $body = null;
        $lang = i18n::get_locale();
        $path = Config::inst()->get('GlobalErrorPage', 'GlobalErrorPagePath');
        if (!$path) $path = $this->defaultErrorPagePath;
        $errorPages = array(
            Config::inst()->get('GlobalErrorPage', $this->ErrorCode),
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

        $body = parent::handleRequest($request, $model);
        $this->response->setStatusCode($this->ErrorCode);

        return $this->response;
    }
}

