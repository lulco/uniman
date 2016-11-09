<?php

namespace Adminerng\Core\Forms\FilterForm;

use Adminerng\Core\Column;
use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Localization\ITranslator;
use Nette\Utils\ArrayHash;

class FilterForm extends Control implements FilterFormInterface
{
    const DEFAULT_ON_PAGE = 50;

    public $doRedirect = [];

    public $doReset = [];

    private $translator;

    private $columns = [];

    private $sorting = [];

    private $filter = [];

    private $onPage = [];

    /**
     * @param Column[] $columns
     * @param array $filter
     * @param array $sorting
     * @param integer $onPage
     */
    public function __construct(ITranslator $translator, array $columns, array $filter, array $sorting, $onPage = self::DEFAULT_ON_PAGE)
    {
        parent::__construct();
        $this->translator = $translator;
        $this->columns = $columns;
        $this->sorting = $sorting;
        $this->filter = $filter;
        $this->onPage = $onPage;
    }

    public function render()
    {
        $this->template->filter = $this->filter;
        $this->template->sorting = $this->sorting;
        $this->template->limit = $this->onPage != self::DEFAULT_ON_PAGE;
        $this->template->setFile(__DIR__ . '/filter_form.latte');
        $this->template->render();
    }

    protected function createComponentForm()
    {
        $form = new Form();
        $form->setTranslator($this->translator);
        $this->addSortingFields($form);
        $this->addFilterFields($form);

        $form->addText('onPage', 'On page')
            ->setAttribute('placeholder', 'On page')
            ->setValue($this->onPage);

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

        $sorting = [];
        $i = 0;
        foreach ($values['sorting'] as $key => $value) {
            if (strpos($key, 'column_') === 0 && $value) {
                $index = str_replace('column_', '', $key);
                $sorting[$i++][$value] = $values['sorting']["way_$index"];
            }
        }

        $filter = [];
        $i = 0;
        foreach ($values['filter'] as $key => $value) {
            if (strpos($key, 'column_') === 0 && $value) {
                $index = str_replace('column_', '', $key);
                $filter[$i++][$value][$values['filter']["operator_$index"]] = $values['filter']["value_$index"];
            }
        }

        // toto hadze chybu Call to undefined method Adminerng\Core\Forms\FilterForm\FilterForm::doRedirect()
//        $this->doRedirect($onPage, $filter, $sorting);

        // takze to ohackujeme takto
        foreach ($this->doRedirect as $callback) {
            $callback($onPage, $filter, $sorting);
        }
    }

    private function addSortingFields($form)
    {
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
                $sortingContainer["way_$index"]->setValue($way);
            }
        }
        return $sortingContainer;
    }

    private function addFilterFields($form)
    {
        $filterContainer = $form->addContainer('filter');
        $filterItems = [];
        $operators = [];
        foreach ($this->columns as $column) {
            if (!$column->isFilterable()) {
                continue;
            }
            // TODO idealne by bolo davat do dropdownu s operatormi pre kazdy field len tie operatory, ktore on moze pouzit
            $filterItems[$column->getKey()] = $column->getTitle();
            foreach ($column->getFilters() as $filter) {
                $operators[$filter] = 'core.filter.' . $filter;
            }
        }


        if (!empty($filterItems)) {
            for ($i = 0; $i < 10; $i++) {
                $filterContainer->addSelect("column_$i", null, $filterItems)->setPrompt('Select column to filter');
                $filterContainer->addSelect("operator_$i", null, $operators);
                $filterContainer->addText("value_$i")->setAttribute('placeholder', 'value');
            }
        }

        foreach ($this->filter as $index => $filter) {
            foreach ($filter as $column => $settings) {
                foreach ($settings as $operator => $value) {
                    $filterContainer["column_$index"]->setValue($column);
                    $filterContainer["operator_$index"]->setValue($operator);
                    $filterContainer["value_$index"]->setValue($value);
                }
            }
        }
        return $filterContainer;
    }
}
