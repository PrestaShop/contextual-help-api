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

namespace Help\PrestaShop;

use HTMLPurifier;
use HTMLPurifier_Config;
use Twig\Environment;

class HtmlContentBuilderBody implements ContentBuilderBodyInterface
{
    public function __construct(private Environment $twig){}

    public function render(PageInfos $infos, mixed $body): string
    {
        return $this->twig->render('index.html.twig', [
            'body' => $this->build($body),
            'pageInfos' => $infos,
        ]);
    }

    private function build(mixed $body): string
    {
        $body = (string) $body->body->view->value;

        // Clean the html content
        $body = str_replace("\n", '', $body);
        $body = preg_replace('@<ac:macro\s+ac:name=\"(\w+)\">@', '<div class="$1">', $body);
        $body = preg_replace('@</ac:macro>@', '</div>', (string) $body);
        $body = preg_replace_callback('/<!\[CDATA\[(.*)\]\]>/U', function ($matches) {
            return htmlspecialchars($matches[1]);
        }, (string) $body);
        $body = strip_tags((string) $body, '<ul><ol><li><div><p><h1><h2><h3><strong>');
        $config = HTMLPurifier_Config::createDefault();
        $config->set('Attr.EnableID', true);
        $purifier = new HTMLPurifier($config);

        return $purifier->purify($body);
    }
}