<?php

namespace Adminerng\Core\Forms\FilterForm;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Utils\ArrayHash;

class FilterForm extends Control implements FilterFormInterface
{
    const DEFAULT_ON_PAGE = 50;

    public $doRedirect = [];

    public $doReset = [];

    private $columns = [];

    private $sorting = [];

    private $filter = [];

    private $onPage = [];

    public function __construct(array $columns, array $filter, array $sorting, $onPage = self::DEFAULT_ON_PAGE)
    {
        parent::__construct();
        $this->columns = $columns;
        $this->sorting = $sorting;
        $this->filter = $filter;
        $this->onPage = $onPage;
    }

    public function render()
    {
        $this->template->showForm = !(empty($this->sorting) && empty($this->filter) && $this->onPage == self::DEFAULT_ON_PAGE);
        $this->template->setFile(__DIR__ . '/filter_form.latte');
        $this->template->render();
    }

    protected function createComponentForm()
    {
        $form = new Form();
        $sortingItems = [];
        foreach ($this->columns as $column) {
            if ($column->isSortable()) {
                $sortingItems[$column->getKey()] = $column->getTitle();
            }
        }

        $sortingContainer = $form->addContainer('sorting');
        if (!empty($sortingItems)) {
            for ($i = 0; $i < 10; $i++) {
                $sortingContainer->addSelect("column_$i", null, $sortingItems)->setPrompt('Select column to sort');
                $sortingContainer->addSelect("way_$i", null, ['asc' => 'ASC', 'desc' => 'DESC']);
            }
        }

        foreach ($this->sorting as $index => $sorting) {
            foreach ($sorting as $key => $way) {
                $sortingContainer["column_$index"]->setValue($key);
                $sortingContainer["way_$index"]->setDefaultValue($way);
            }
        }
        $form->addText('onPage', 'On page')
            ->setDefaultValue($this->onPage);
        $form->addSubmit('submit', 'Filter');
        $form->addSubmit('reset', 'Reset');
        $form->onSuccess[] = [$this, 'filter'];
        return $form;
    }

    public function filter(Form $form, ArrayHash $values)
    {
        if ($form->isSubmitted()->getName() == 'reset') {
            foreach ($this->doReset as $callback) {
                $callback();
            }
            return;
        }

        $onPage = intval($values['onPage']);
        $filter = [];
        $sorting = [];
        $i = 0;
        foreach ($values['sorting'] as $key => $value) {
            if (strpos($key, 'column_') === 0 && $value) {
                $index = str_replace('column_', '', $key);
                $sorting[$i++][$value] = $values['sorting']["way_$index"];
            }
        }


        // toto hadze chybu Call to undefined method Adminerng\Core\Forms\FilterForm\FilterForm::doRedirect()
//        $this->doRedirect($onPage, $filter, $sorting);

        // takze to ohackujeme takto
        foreach ($this->doRedirect as $callback) {
            $callback($onPage, $filter, $sorting);
        }

    }
}
