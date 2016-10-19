<?php

namespace Adminerng\Presenters;

use Adminerng\Core\LoginForm;
use Nette\Application\Responses\TextResponse;

class DefaultPresenter extends AbstractBasePresenter
{
    public function actionDefault($driver = null)
    {
        $actualDriver = $driver ?: current(array_keys($this->driverStorage->getDrivers()));
        $this->driver = $this->driverStorage->getDriver($actualDriver);
        $this->template->driver = $actualDriver;
    }

    public function actionLogout($driver = null)
    {
        $section = $this->getSession('adminerng');
        if ($driver) {
            unset($section->{$driver});
        } else {
            $section->remove();
        }
        $this->redirect('Default:default', $driver);
    }

    public function actionFile($file)
    {
        $httpResponse = $this->getHttpResponse();
        $httpResponse->setHeader('Pragma', null);
        $httpResponse->setExpiration('+10 minutes');
        $contentTypes = [
            'css' => 'text/css',
            'js' => 'application/javascript',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'png' => 'image/png',
            'ico' => 'image/x-icon',
            'eot' => 'application/vnd.ms-fontobject',
            'woff' => 'application/font-woff',
            'woff2' => 'application/font-woff2',
            'ttf' => 'application/x-font-truetype',
            'svg' => 'image/svg+xml',
            'otf' => 'application/x-font-opentype',
        ];
        $path = __DIR__ . '/../../www/' . $file;
        $extension = pathinfo($path, PATHINFO_EXTENSION);
        $contentType = isset($contentTypes[$extension]) ? $contentTypes[$extension] : 'text/plain';
        $httpResponse->setContentType($contentType);
        $this->sendResponse(new TextResponse(file_get_contents($path)));
    }

    public function renderCheck()
    {
        $drivers = $this->driverStorage->getDrivers();
        $actualDriver = current(array_keys($drivers));
        $this->driver = $this->driverStorage->getDriver($actualDriver);
        $this->template->driver = $actualDriver;
        $this->template->drivers = $drivers;
    }

    protected function createComponentLoginForm()
    {
        return new LoginForm($this->translator, $this->driverStorage, $this->credentialsStorage, $this->driver->type(), $this->locale);
    }
}
