<?php

namespace Kirby\Cms;

use Exception;
use Kirby\Toolkit\F;
use Kirby\Exception\InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class PageActionsTest extends TestCase
{

    protected $app;
    protected $fixtures;

    public function app()
    {
        return new App([
            'blueprints' => [
                'pages/default' => [
                    'title'  => 'Default',
                    'name'   => 'default',
                    'fields' => [
                        'headline' => [
                            'type' => 'text'
                        ]
                    ]
                ],
                'pages/article' => [
                    'title'  => 'Article',
                    'name'   => 'article',
                    'num'    => 'date',
                    'status' => ['draft' => 'Draft', 'listed' => 'Published'],
                    'fields' => [
                        'date' => [
                            'type' => 'date'
                        ]
                    ]
                ]
            ],
            'roots' => [
               'index' => $this->fixtures = __DIR__ . '/fixtures/PageActionsTest'
            ],
            'site' => [
                'children' => [
                    [
                        'slug'  => 'test',
                    ],
                    [
                        'slug'     => 'article',
                        'num'      => 20121212,
                        'template' => 'article'
                    ]
                ],
            ],
            'users' => [
                [
                    'email' => 'admin@domain.com',
                    'role'  => 'admin'
                ]
            ],
            'user' => 'admin@domain.com'
        ]);
    }

    public function setUp()
    {
        $this->app = $this->app();
        Dir::make($this->fixtures);
    }

    public function tearDown()
    {
        Dir::remove($this->fixtures);
    }

    public function site()
    {
        return $this->app->site();
    }

    public function testChangeSlug()
    {
        $page = $this->site()->find('test')->save();

        $this->assertTrue($page->exists());
        $this->assertEquals('test', $page->slug());

        $modified = $page->changeSlug('modified-test');

        $this->assertTrue($modified->exists());
        $this->assertEquals('modified-test', $modified->slug());
    }

    public function testChangeTemplate()
    {

    }

    public function testChangeTitle()
    {
        $page = $this->site()->find('test');
        $this->assertEquals('test', $page->title());

        $modified = $page->changeTitle($title = 'Modified Title');
        $this->assertEquals($title, $modified->title());
    }

    public function testDelete()
    {
        $page = $this->site()->find('test')->save();
        $this->assertTrue($page->exists());

        $page->delete();
        $this->assertFalse($page->exists());
        $this->assertFalse($page->parentModel()->children()->has($page->id()));
    }

    public function testSave()
    {
        $page = $this->site()->find('test');

        $this->assertFalse($page->exists());
        $page->save();
        $this->assertTrue($page->exists());
    }

    public function testUpdate()
    {
        $page = $this->site()->find('test')->save();
        $this->assertEquals(null, $page->headline()->value());

        $oldStatus = $page->status();

        $modified = $page->update(['headline' => 'Test']);
        $this->assertEquals('Test', $modified->headline()->value());

        // assert that the page status didn't change with the update
        $this->assertEquals($oldStatus, $modified->status());
    }

}
