<?php

class AppTest extends \PHPUnit\Framework\TestCase
{
    /** @var  \Symfony\Component\Console\Tester\ApplicationTester */
    private $tester;

    public function setUp()
    {
        $command = new \Kalyashka\Srcdoc\Command\SourceCommand();
        $app     = new \Symfony\Component\Console\Application('srcdoc');
        $app->setAutoExit(false);
        $app->add($command);
        $app->setDefaultCommand($command->getName(), true);
        $this->tester = new \Symfony\Component\Console\Tester\ApplicationTester($app);
    }

    public function testExecute()
    {
        $file = tempnam(sys_get_temp_dir(), '');
        $code = $this->tester->run([
            '--heading'    => 'h2',
            '--extensions' => 'php,js',
            '--exclude'    => 'vendor',
            '--output'     => $file,
            'directory'    => 'tests/data',

        ], ['interactive' => false]);
        $this->assertEquals(0, $code);
        $contents = file_get_contents($file);
        $this->assertNotEmpty($contents);
        unlink($file);
    }

    public function testThemeList()
    {
        $code   = $this->tester->run([
            '--theme-list' => true,
        ]);
        $output = $this->tester->getDisplay();
        $this->assertEquals(0, $code);
        $this->assertContains('idea', $output);
    }

    public function testNoHighlight()
    {
        $file = tempnam(sys_get_temp_dir(), '');
        $code = $this->tester->run([
            '--heading'    => 'h2',
            '--extensions' => 'php,js',
            '--exclude'    => 'vendor',
            '--output'     => $file,
            '--no-syntax'  => true,
            'directory'    => 'tests/data',
        ], ['interactive' => false]);
        $this->assertEquals(0, $code);
        $this->assertNotContains('<style type="text/css">', file_get_contents($file));
        unlink($file);
    }

    public function testNoFiles()
    {
        $file = tempnam(sys_get_temp_dir(), '');
        $this->tester->run([
            '--heading'    => 'h2',
            '--extensions' => 'nonexistent',
            '--exclude'    => 'vendor',
            '--output'     => $file,
            '--no-syntax'  => true,
            'directory'    => 'tests/data',
        ], ['interactive' => false]);
        $this->assertContains('No source files found', $this->tester->getDisplay());
    }

    public function testNonExistentTheme()
    {
        $code = $this->tester->run([
            '--theme'     => 'nonexistent',
            '--no-syntax' => true,
            'directory'   => 'tests/data',
        ], ['interactive' => false]);
        $this->assertNotEquals(0, $code);
    }

    public function testList()
    {
        $code = $this->tester->run([
            '--list'   => 'tests/data/files.list',
            '--output' => '/dev/null',
        ], ['interactive' => false]);
        $this->assertEquals(0, $code);
    }

    public function testNonExistentList()
    {
        $code = $this->tester->run([
            '--list'      => 'tests/data/nonexistent.list',
            '--no-syntax' => true,
            //'directory'   => 'tests/data',
        ], ['interactive' => false]);
        $this->assertNotEquals(0, $code);
    }
}