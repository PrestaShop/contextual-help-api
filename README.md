# contextual-help-api

## About

This webapp is in charge of providing the documentation that is displayed in PrestaShop's back-office.
That's the application behind https://help.prestashop-project.org/

### How it works?

When a request is made to this app, the returned documentation is taken from the user documentation's Github repository,
parsed, and converted to either plain HTML or json-encoded HTML.

Which content from which repository to be taken is related to the request path and/or parameters that indicates PrestaShop's version,
language and back-office page the documentation is needed for.

There's basically 2 ways you can make request to this app:

- http​s://this.app/api/?request=getHelp%3D**page_controller**%26version%3D**prestashop_version**%26language%3D**lang_iso_code**%26callback%3D**jsonp_callback**
- http​s://this.app/**lang_iso_code**/doc/**page_controller**/?version=**prestashop_version**

In the first case, the part after `?request=` is actually urlencoded. By using this way, a jsonp response containing the HTML documentation will be returned.

In the second case, the documentation is returned as plain HTML.

### Configurations

The repositories where the documentation is taken from are defined in `config/mapping_urls.yml`.
The markdown files to be used are defined in `config/mapping_v{prestashop_version}`.

### Markown + Liquid tags

As the markdown available in the user documentation repositories is made through Gitbook, there's some parts that are not classic markdown.
Those specials features are more or less [liquid tags](https://jekyllrb.com/docs/liquid/tags/) and a special Markdown parsing/rendering extension has been
made to handle them (`src/Markdown/Node/Liquid.php`, `src/Markdown/Parser/Block/LiquidBlock*` and `src/Markdown/Renderer/*`).

## Requirement

PHP >= 8.0.7

## How to run it

### Locally

```shell
$ composer install
$ php -S localhost:8080 -t public/
```

## Deployment

See the documentation in the `.cloud` folder
