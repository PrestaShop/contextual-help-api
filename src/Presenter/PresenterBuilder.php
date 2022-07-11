<?php
/**
 * This file is authored by PrestaShop SA and Contributors <contact@prestashop.com>
 *
 * It is distributed under MIT license, since 2021.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
declare(strict_types=1);

namespace Help\PrestaShop\Presenter;

use Help\PrestaShop\RequestInfo;

class PresenterBuilder
{
    private array $presenters;
    public function __construct(PresenterInterface ...$presenters)
    {
        $this->presenters = $presenters;
    }

    public function getPresenter(RequestInfo $requestInfo): PresenterInterface
    {
        foreach ($this->presenters as $presenter) {
            if ($presenter->canPresentWithRequestInfo($requestInfo)) {
                return $presenter;
            }
        }

        return reset($this->presenters);
    }
}