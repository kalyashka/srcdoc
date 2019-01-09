<?php

namespace Kalyashka\Srcdoc\Command;

use Kalyashka\Srcdoc\FileCollector;
use Kalyashka\Srcdoc\HtmlDocumentor;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\Table;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class SourceCommand extends Command
{
    const STYLES_DIR = 'vendor/scrivo/highlight.php/styles';

    /**
     * SourceCommand constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function configure()
    {
        $this
            ->setName('srcdoc')
            ->setHelp('Paste source code from files to html with syntax highlighting (using <comment>scrivo/highlight.php</comment>)')
            ->setDescription('Utility to paste source code to html')
            ->addArgument('directory', InputArgument::OPTIONAL, 'Directory where source files located (defaults to current directory)')
            ->addOption('extensions', 'x', InputOption::VALUE_REQUIRED, 'File extensions to collect', 'php,js,css,scss')
            ->addOption('exclude', 'e', InputOption::VALUE_REQUIRED, 'Exclude files/directories from processing')
            ->addOption('output', 'o', InputOption::VALUE_REQUIRED, 'Output file (defaults to stdout)')
            ->addOption('list', 'l', InputOption::VALUE_REQUIRED, 'File containing files list')
            ->addOption('no-syntax', 's', InputOption::VALUE_NONE, 'Disable syntax highlighting')
            ->addOption('theme', 't', InputOption::VALUE_REQUIRED, 'Theme css file name', 'idea')
            ->addOption('theme-list', null, InputOption::VALUE_NONE, 'List available highlight.php css themes')
            ->addOption('heading', null, InputOption::VALUE_REQUIRED, 'Heading tag', 'h3');
    }

    public function execute(InputInterface $input, OutputInterface $output)
    {
        $errOutput = $output instanceof ConsoleOutputInterface ? $output->getErrorOutput() : $output;
        $ext       = $this->getMultiValue($input->getOption('extensions'));
        $exclude   = $this->getMultiValue($input->getOption('exclude'));
        $directory = $input->getArgument('directory') ?: getcwd();
        $theme     = $input->getOption('theme');
        $list      = $input->getOption('list');
        $outFile   = $input->getOption('output');
        $heading   = $input->getOption('heading');
        $themeList = $input->getOption('theme-list');
        $noSyntax  = $input->getOption('no-syntax');
        if ($themeList) {
            return $this->themeList($input, $output);
        }
        $themeFile = $this->getStylesDir() . '/' . $theme . '.css';
        if (!file_exists($themeFile)) {
            $errOutput->writeln('<error>Style file not found</error>');

            return 1;
        }
        $fileCollector = new FileCollector();
        if ($list) {
            if (!is_readable($list)) {
                $errOutput->writeln('<error>Listing file not found</error>');

                return 1;
            }
            $directory = dirname($list);
            $fileCollector->setFileList(array_filter(file($list)), $directory);
        } else {
            $errOutput->writeln('Search for files...', OutputInterface::VERBOSITY_VERBOSE);
            $fileCollector->collect($directory, $ext, $exclude);

        }
        $errOutput->writeln('Found ' . $fileCollector->getFiles()->count() . ' source files', OutputInterface::VERBOSITY_VERBOSE);
        if ($fileCollector->getFiles()->count() == 0) {
            $errOutput->writeln('<error>No source files found</error>');

            return 0;
        }

        $documentor = new HtmlDocumentor($heading);
        if (!$noSyntax) {
            $documentor->setStyleFile($themeFile);
        }
        $documentor->setFiles($fileCollector);

        if (!$outFile) {
            $outFile = STDOUT;
        }
        $errOutput->writeln('Writing...', OutputInterface::VERBOSITY_VERBOSE);
        $documentor->output($outFile);

        $errOutput->writeln('Done.', OutputInterface::VERBOSITY_VERBOSE);

        return 0;
    }

    protected function themeList(InputInterface $input, OutputInterface $output)
    {
        $styles = array_map(function ($f) {
            return pathinfo($f, PATHINFO_FILENAME);
        }, glob($this->getStylesDir() . '/*.css'));
        $output->writeln('<info>Available styles:</info>');

        $columns = 4;
        $w       = intval((getenv('COLUMNS') - $columns) / $columns);
        $table   = new Table($output);
        $table->setStyle('compact');
        $table->setColumnWidths(array_fill(0, $columns, $w));

        $rows = ceil(count($styles) / $columns);
        $ch   = array_chunk($styles, $rows);
        for ($i = 0; $i < $rows; $i++) {
            $table->addRow(array_column($ch, $i));
        }
        $table->render();
    }

    protected function getMultiValue($value)
    {
        return is_string($value) ? preg_split('/[,; ]+/', $value) : (array)$value;
    }

    protected function getStylesDir()
    {
        if (is_dir(__DIR__ . '/../../' . self::STYLES_DIR)) {
            return __DIR__ . '/../../' . self::STYLES_DIR;
        } else {
            return __DIR__ . '/../../../../../' . self::STYLES_DIR;
        }
    }
}