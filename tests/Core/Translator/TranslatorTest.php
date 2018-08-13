<?php

namespace UniMan\Tests\Core\Translator;

use PHPUnit\Framework\TestCase;
use UniMan\Core\Translator\LanguageResolver\StaticLanguageResolver;
use UniMan\Core\Translator\Loader\NeonFileLoader;
use UniMan\Core\Translator\Storage\MemoryStorage;
use UniMan\Core\Translator\Translator;

class TranslatorTest extends TestCase
{
    public function testTranslateEn()
    {
        $localizationDirectory = __DIR__ . '/../../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);
        $storage = new MemoryStorage($loader);
        $languageResolver = new StaticLanguageResolver('en');
        $translator = new Translator($storage, $languageResolver);

        self::assertEquals('Yes', $translator->translate('core.yes'));
        self::assertEquals('Tables and views for database %database%', $translator->translate('mysql.table_types_for_database'));
        self::assertEquals('Tables and views for database Database name', $translator->translate('mysql.table_types_for_database', ['database' => 'Database name']));
    }

    public function testTranslateSk()
    {
        $localizationDirectory = __DIR__ . '/../../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);
        $storage = new MemoryStorage($loader);
        $languageResolver = new StaticLanguageResolver('sk');
        $translator = new Translator($storage, $languageResolver);

        self::assertEquals('Áno', $translator->translate('core.yes'));
        self::assertEquals('Tabuľky a viewy pre databázu %database%', $translator->translate('mysql.table_types_for_database'));
        self::assertEquals('Tabuľky a viewy pre databázu Názov databázy', $translator->translate('mysql.table_types_for_database', ['database' => 'Názov databázy']));
    }

    public function testTranslateCz()
    {
        $localizationDirectory = __DIR__ . '/../../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);
        $storage = new MemoryStorage($loader);
        $languageResolver = new StaticLanguageResolver('cz');
        $translator = new Translator($storage, $languageResolver);

        self::assertEquals('', $translator->translate('core.yes'));
        self::assertEquals('', $translator->translate('mysql.table_types_for_database'));
        self::assertEquals('', $translator->translate('mysql.table_types_for_database', ['database' => 'Názov databázy']));
    }

    public function testTranslateCzWithSkDefault()
    {
        $localizationDirectory = __DIR__ . '/../../../app/lang/';
        $loader = new NeonFileLoader($localizationDirectory);
        $storage = new MemoryStorage($loader);
        $languageResolver = new StaticLanguageResolver('cz');
        $translator = new Translator($storage, $languageResolver);
        $translator->setDefaultLanguage('sk');

        self::assertEquals('Áno', $translator->translate('core.yes'));
        self::assertEquals('Tabuľky a viewy pre databázu %database%', $translator->translate('mysql.table_types_for_database'));
        self::assertEquals('Tabuľky a viewy pre databázu Názov databázy', $translator->translate('mysql.table_types_for_database', ['database' => 'Názov databázy']));
    }
}
