<?php


namespace CodeceptionTestsGenerator\helper;

use FilesystemIterator;

trait Helper
{
    private static $log               = 'collection-log.json';
    private static $urlRegex          = '/^((http)[s]{0,1}:\/\/{{[\w]*}}\/)([\w\d\/\-*]+[?]{0,})[&]{0,}([\w\d\-]*[=]{1}[\w\d\-%*]*[&]{1,})*([\w]*={{[\w]*}}){0,}[&]{0,}([\w\d\-]*[=]{1}[\w\d\-]*[&]{0,})*$/';
    private static $jaonPath          = './tests/_data/collection/';
    private static $featureName       = '/^[\sA-Za-z0-9_-]*$/';
    private static $testDirectoryPath = './tests/acceptance/';

    private function saveFeature($testName, $tableValues)
    {
        $myfile = fopen(self::$testDirectoryPath . $testName . ".feature", "w+");
        $this->save($myfile, $tableValues);
    }

    private function saveSchema($testName, $schema)
    {
        $shcemaPath = "./tests/_data/schema/{$testName}";
        if (is_dir($shcemaPath) !== true) {
            mkdir($shcemaPath, 0777);
        }
        $schemaFile = fopen($shcemaPath . '/' . $testName . ".json", "w+");
        $this->save($schemaFile, $schema);
    }

    private function logging($store)
    {
        $data = [
            'validation' => $store,
        ];
        $json = json_encode($data, JSON_PRETTY_PRINT);
        file_put_contents("./tests/_output/" . self::$log, $json);
    }

    private function urlValidator($url)
    {
        return preg_match(self::$urlRegex, $url);
    }

    private function featureNameValidator($name)
    {
        return preg_match(self::$featureName, $name);
    }

    private function collectionValidator($collection, $schema)
    {
        if (file_exists(self::$jaonPath . $collection)) {
            $info = pathinfo(self::$jaonPath . $collection);
            if ($info['extension'] == 'json' && $schema != null) {
                if (in_array('v2.1.0', explode('/', $schema['info']['schema']))) {
                    $this->creator();
                } else {
                    echo "\e[1;30;41m Bad postman collection version. \e[0m\n";
                    exit;
                }
            } else {
                echo "\e[1;30;41m No file or file content \e[0m\n";
                exit;
            }
        } else {
            echo "\e[1;30;41m No file exist \e[0m\n";
            exit;
        }
    }

    private function postmanCollectionValidate($collection)
    {
        $this->collectionValidator($collection, $this->jsonFile());
    }

    private function jsonFile()
    {
        return json_decode(file_get_contents('./tests/_data/collection/' . $this->collection), true);
    }

    private function columnBy($items, $param)
    {
        return array_column(array_values($items['request']['header']), $param);
    }

    private function implodeData($glue, $param)
    {
        return implode($glue, $param);
    }

    private function scanDir()
    {
        $files = new FilesystemIterator(self::$testDirectoryPath, FilesystemIterator::SKIP_DOTS);

        return iterator_count($files);
    }

    private function save($file, $value)
    {
        fwrite($file, $value);
        fclose($file);
    }

    private function feature($featureName, $description, $testName, $params, $header, $method, $code)
    {
        return [
            "Feature:"                                         => "{$featureName}" . '.',
            ""                                                 => "{$description}",
            "As a consumer "                                   => "of the API, I want an API that provides with data about {$featureName}" . '.' . PHP_EOL .
                "So that I can use this for my application.",
            "\n  Scenario Outline:"                            => "{$featureName}.",
            "\tGiven the parameters"                           => "\"$params\"",
            "\tAnd"                                            => "the header \"{$header}\"",
            "\tWhen I request url created from params"         => "by \"{$method}\" method",
            "\tWhen I request secured url created from params" => "by \"{$method}\" method",
            "\tThen I see response status code is"             => "\"{$code}\"",
            "\tAnd the response matches"                       => "\"{$testName}\" json schema",
            "\n\tExamples:"                                    => ''
        ];
    }

    private function responseValidationExamples($validateBody, $validationCode, $headerValue, $template, $featureName, $string, $queryParameters, $headerTable)
    {
        for ($i = 0; $i <= count($validateBody) - 1; $i++) {

            foreach ($validateBody[$i]['url'] as $value) {
                $validation = '';
                $explode    = explode(' ',
                    rtrim(str_replace(['/', '?', '&'], ' ', preg_replace([self::$https, self::$apiKey], '', $value)), ' '));
                $pathParameters = str_replace(':', "\t\t",
                    preg_replace(self::$httpsUrlXpath, '', $this->implodeData('|', $explode)));
                $collection[]   = $pathParameters;
            }

            foreach ($collection as $tableData) {
                $tableExamples = "\t| " . $tableData . " |" . $this->implodeData('|',
                        $headerValue) . " |" . PHP_EOL;
                $validation    .= $tableExamples;
                unset($collection);
            }
            unset($template["Feature:"]);
            unset($template[""]);
            unset($template["\tAnd the response matches"]);
            $template["\n  Scenario Outline:"]                = "{$featureName} - {$validationCode[$i]} validation.";
            $template["\tThen I see response status code is"] = "\"{$validationCode[$i]}\"";

            foreach ($template as $key => $val) {
                $string .= "$key $val\n";
            }

            $tableExamples = "\t| " . $queryParameters . " |" . $headerTable . " |" . PHP_EOL;
            $string        .= $tableExamples . $validation;
        }

        return $string;
    }

    private function templateLoop($template, $tableExamples)
    {
        $string = '';

        foreach ($template as $key => $val) {
            $string .= "$key $val\n";
        }
        $string .= $tableExamples . PHP_EOL;

        return $string;
    }
}