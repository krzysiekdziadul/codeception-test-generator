<?php


namespace CodeceptionTestsGenerator;


use CodeceptionTestsGenerator\helper\Helper;

class FrameworkPrepare
{
    use Helper;

    public function isDir($path, $folder)
    {
        if (is_dir("./tests/{$path}/{$folder}") !== true) {
            return mkdir("./tests/{$path}/{$folder}", 0777);
        }
    }

    public function colectionFolder()
    {
        $folder = 'collection';
        $path   = '_data';

        $this->isDir($path, $folder);
    }

    public function commandFolder()
    {
        $folder = 'Command';
        $path   = '_support';

        $this->isDir($path, $folder);
    }

    public function schemaFolder()
    {
        $folder = 'schema';
        $path   = '_data';

        $this->isDir($path, $folder);
    }

    public function copyCusomCommand()
    {
        return copy('./vendor/dziadul/codeception-test-generator/src/copy/TestCreateCommand.php', './tests/_support/Command/TestCreateCommand.php');
    }

    public function copyAcceptanceTester()
    {
        return copy('./vendor/dziadul/codeception-test-generator/src/copy/AcceptanceTesterClass.php', './tests/_support/AcceptanceTester.php');
    }

    public function removeAcceptanceTester()
    {
        unlink('./vendor/dziadul/codeception-test-generator/src/copy/AcceptanceTesterClass.php');
    }

    public function bootstrap()
    {
        $file     = '_bootstrap.php';
        $contents = "<?php" . PHP_EOL . "include_once __DIR__.'/_support/Command/TestCreateCommand.php';";       // Some simple example content.

        if (!is_file("./tests/{$file}")) {

            $myfile = fopen("./tests/{$file}", "w+");
            $this->save($myfile, $contents);

        } else {
            $myfile  = fopen("./tests/{$file}", "a");
            $content = PHP_EOL . "include_once __DIR__.'/_support/Command/TestCreateCommand.php';";       // Some simple example content.

            $this->save($myfile, $content);
        }
    }
}