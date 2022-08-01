<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Tests\Integration\Features\Context;

use Behat\Behat\Context\Context;
use GuzzleHttp\Client;

class HttpRequestsContext implements Context
{
    private string|false $lastResult;

    private const BASE_URL = 'http://localhost:8008';

    private const ERROR_PAGE_BODY = 'An error occurred when getting help page';

    /**
     * @When /^I get the content of "(.+)"$/
     */
    public function getHttpContent(string $url): void
    {
        $httpClient = new Client();
        $this->lastResult = $httpClient->get(self::BASE_URL . $url)->getBody()->getContents();
    }

    /**
     * @Then /^I should get a success response$/
     */
    public function lastRequestResponseShouldBeSuccess(): void
    {
        if (
            false === $this->lastResult
            || empty($this->lastResult)
            || str_contains($this->lastResult, self::ERROR_PAGE_BODY)
        ) {
            throw new \Exception('An error response was not expected here');
        }
    }

    /**
     * @Then /^I should get an error response$/
     */
    public function lastRequestResponseShouldBeError(): void
    {
        if (
            false === $this->lastResult
            || empty($this->lastResult)
            || !str_contains($this->lastResult, self::ERROR_PAGE_BODY)
        ) {
            throw new \Exception('An error response was expected here');
        }
    }
}
