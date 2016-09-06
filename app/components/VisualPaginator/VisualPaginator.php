<?php

namespace App\Component;

use Nette\Application\UI\Control;
use Nette\Utils\Paginator;

/**
 * Nette Framework Extras
 *
 * This source file is subject to the New BSD License.
 *
 * For more information please see http://extras.nettephp.com
 *
 * @copyright  Copyright (c) 2009 David Grudl
 * @license     New BSD License
 * @link       http://extras.nettephp.com
 * @package     Nette Extras
 * @version     $Id: VisualPaginator.php 4 2009-07-14 15:22:02Z david@grudl.com $
 */

/**
 * Visual paginator control.
 *
 * @author   David Grudl
 * @copyright  Copyright (c) 2009 David Grudl
 * @package     Nette Extras
 */
class VisualPaginator extends Control
{
    /** @var string     Defined template file */
    private $paginatorTemplate = 'default';

    /** @var Paginator */
    private $paginator;

    /** @persistent */
    public $page = 1;
    
    /**
     * @return Paginator
     */
    public function getPaginator()
    {
        if (!$this->paginator) {
            $this->paginator = new Paginator();
        }
        return $this->paginator;
    }

    /**
     * Renders paginator.
     * @return void
     */
    public function render()
    {
        $paginator = $this->getPaginator();
        $page = $paginator->page;
        if ($paginator->pageCount < 2) {
            $steps = array($page);
        } else {
            $arr = range(max($paginator->firstPage, $page - 3), min($paginator->lastPage, $page + 3));
            $arr[] = $paginator->firstPage;
            $arr[] = $paginator->lastPage;

            sort($arr);
            $steps = array_values(array_unique($arr));
        }

        $this->template->steps = $steps;
        $this->template->paginator = $paginator;
        $this->template->setFile(dirname(__FILE__) . '/' . $this->paginatorTemplate . '.latte');
        $this->template->render();
    }
}
