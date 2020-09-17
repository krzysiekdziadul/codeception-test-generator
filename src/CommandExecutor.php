<?php


namespace CodeceptionTestsGenerator;

use CodeceptionTestsGenerator\helper\Helper;
use CodeceptionTestsGenerator\TestsCreator;

class CommandExecutor extends FrameworkPrepare
{
    use Helper;

    public function execute()
    {
        $dir   = scandir('./tests/_data/collection');
        $files = array_diff($dir, array('.', '..', '.DS_Store'));
        if (!empty($files)) {

            $values[] = array_values($files);

            echo PHP_EOL . "\e[4;31mFile AcceptanceTester.php will be overwritten.\e[0m\nSelect file or exit \n";

            foreach ($values as $value) {
                $filter = array_filter(array_merge(array(0), $value));
                for ($i = 1; $i <= count($filter); $i++) {
                    if (pathinfo($filter[$i])["extension"] === 'json') {
                        echo '[ ' . $i . ' ] ' . $filter[$i] . PHP_EOL;
                    } else {
                        unset($filter[$i]);
                    }
                }
            }
            echo '[ ' . array_push($filter, 'exit') . ' ] ' . $filter[$i] . PHP_EOL;

            $newFilter = array_filter(array_merge(array(0), $filter));

            trim(fscanf(STDIN, "%d\n", $input));

            if (array_key_last($newFilter) === $input) {
                echo PHP_EOL . "\e[1;30;41m Abandoned ! \e[0m\n";
                exit;
            } else {
                if (array_key_exists($input, $newFilter)) {
                    $execute = new TestsCreator($newFilter[$input]);
                    $execute->collection;
                    $this->copyAcceptanceTester();

                    echo PHP_EOL . "\e[1;30;42m Success, total '{$this->scanDir()}' tests in acceptance folder \e[0m\n";

                } else {
                    echo PHP_EOL . "\e[1;30;41m Selected number '{$input}' not exist's \e[0m\n";
                }
            }
        } else {
            echo PHP_EOL . "\e[1;30;41m No json file's in Postman folder founded \e[0m\n";
        }
    }
}