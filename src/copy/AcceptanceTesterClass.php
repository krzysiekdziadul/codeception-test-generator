<?php


/**
 * Inherited Methods
 * @method void wantToTest($text)
 * @method void wantTo($text)
 * @method void execute($callable)
 * @method void expectTo($prediction)
 * @method void expect($prediction)
 * @method void amGoingTo($argumentation)
 * @method void am($role)
 * @method void lookForwardTo($achieveValue)
 * @method void comment($description)
 * @method void pause()
 *
 * @SuppressWarnings(PHPMD)
 */
class AcceptanceTester extends \Codeception\Actor
{
    use _generated\AcceptanceTesterActions;

    public $result;
    public $header;

    public function chunkingTheParameters($arg)
    {
        $chunks = array_chunk(preg_split('/[:]/', str_replace(' ', '', str_replace('|', ':', $arg))), 2);
        $result = array_combine(array_column($chunks, 0), array_column($chunks, 1));

        return $result;
    }

    public function urlGenerator()
    {
        $pathArgQuantiny = count(preg_grep('/(arg)+[0-9]+/', array_keys($this->result)));
        $grouping        = array_chunk($this->result, $pathArgQuantiny);
        $queryParameters = array_slice($this->result, $pathArgQuantiny);

        $path  = preg_replace('/[0-9]=/', '', http_build_query($grouping[0], null, '/'));
        $query = http_build_query($queryParameters, null, '&');

        if (array_key_exists(1, $grouping)) {
            $url = $path . '?' . $query;
        } else {
            $url = $path;
        }

        return $url;
    }

    /**
     * @Given /^the parameters "([^"]*)"$/
     */
    public function theParameters($parameters)
    {
        $this->result = $this->chunkingTheParameters($parameters);
    }

    /**
     * @Given /^the header "([^"]*)"$/
     */
    public function theHeader($header)
    {
        $this->header = $this->chunkingTheParameters($header);
    }

    /**
     * @When /^I request ([^"]*) by "([^"]*)" method$$/
     */
    public function iRequestUrlByMethod($method)
    {

        if (!empty($this->header)) {
            foreach ($this->header as $key => $value) {
                $this->haveHttpHeader("{$key}", "{$value}");
            }
        }
        $method = 'send'.$method;
        $this->$method($_ENV['HOST'].$this->urlGenerator());
    }

    /**
     * @When /^I request secured ([^"]*) by "([^"]*)" method$/
     */
    public function iRequestSecuredUrlByMethod($method)
    {

        if (!empty($this->header)) {
            foreach ($this->header as $key => $value) {
                $this->haveHttpHeader("{$key}", "{$value}");
            }
        }
        $method = 'send'.$method;
        $this->$method($_ENV['HOST'] . $this->urlGenerator() . "&API_KEY=" . $_ENV['API_KEY']);
    }

    /**
     * @Then /^I see response status code is "([^"]*)"$/
     */
    public function iSeeResponseStatusCodeIs($code)
    {
        $this->seeResponseCodeIs($code);
    }

    /**
     * @Then /^the response matches "([^"]*)" json schema$/
     */
    public function theResponseMatchesJsonSchema($schemaName)
    {
        $schema = file_get_contents("./tests/_data/schema/{$schemaName}/{$schemaName}.json");
        $this->seeResponseIsValidOnJsonSchemaString($schema);
    }


}
