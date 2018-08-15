<?php

namespace UniMan\Core\Forms\FilterForm;

use Nette\Application\UI\Control;
use Nette\Application\UI\Form;
use Nette\Forms\Container;
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

    private $onPage;

    public function __construct(
        ITranslator $translator,
        array $columns,
        array $filter,
        array $sorting,
        int $onPage = self::DEFAULT_ON_PAGE
    ) {
        parent::__construct();
        $this->translator = $translator;
        $this->columns = $columns;
        $this->sorting = $sorting;
        $this->filter = $filter;
        $this->onPage = $onPage;
    }

    public function render(): void
    {
        $this->template->filter = $this->filter;
        $this->template->sorting = $this->sorting;
        $this->template->limit = $this->onPage !== self::DEFAULT_ON_PAGE;
        $this->template->setFile(__DIR__ . '/filter_form.latte');
        $this->template->render();
    }

    protected function createComponentForm(): Form
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

    public function filter(Form $form, ArrayHash $values): void
    {
        if ($form['reset']->isSubmittedBy()) {
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

        foreach ($this->doRedirect as $callback) {
            $callback($onPage, $filter, $sorting);
        }
    }

    private function addSortingFields(Form $form): Container
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

    private function addFilterFields(Form $form): Container
    {
        $filterContainer = $form->addContainer('filter');
        $filterItems = [];
        $operators = [];
        foreach ($this->columns as $column) {
            if (!$column->isFilterable()) {
                continue;
            }
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
