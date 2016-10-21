<?php

namespace Adminerng\Presenters;

use Nette\Application\BadRequestException;

class ErrorPresenter extends AbstractBasePresenter
{
    public function renderDefault($exception)
    {
        if ($exception instanceof BadRequestException) {
            $this->setView('4xx');
        } else {
            $this->setView('500');
        }

        if ($this->isAjax()) { // AJAX request? Note this error in payload.
            $this->payload->error = true;
            $this->terminate();
        }
    }
}
