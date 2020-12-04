# DownloadOperation

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![The Whole Fruit Manifesto](https://img.shields.io/badge/writing%20standard-the%20whole%20fruit-brightgreen)](https://github.com/the-whole-fruit/manifesto)

This package provides a way to add "Download" buttons to your Backpack CRUDs, which will download a PDF/image of a Blade view you want. 

This pacakage uses [Backpack for Laravel](https://backpackforlaravel.com/) (of course) but also [spatie/browsershot](https://github.com/spatie/browsershot/), which itself uses a headless Chrome Browser to generate the PDFs. Because of that:
- PRO: you don't need to code special views for them to look good in PDF form; if it looks good in the browser, it'll look good in the PDF; 
- CON: you need to install a bunch of stuff on your server (puppeteer and Chrome); so you probably can't use this on shared hosting;

## Screenshots

![Backpack Download and BulkDownload buttons](https://user-images.githubusercontent.com/1032474/101194862-3f82cc00-3667-11eb-856c-25c21f0181a5.gif)

## Installation

Step 0. While this project is closed-source, please add the repo to your `composer.json` file:

```json
    "repositories": {
        "backpack/download-operation": {
            "type": "vcs",
            "url": "https://github.com/laravel-backpack/download-operation"
        }
    }
```

Step 1. Install via Composer

``` bash
composer require backpack/download-operation
```

Step 2. To add a "Delete" button next to each entry inside your CRUD and a bulk button at the bottom of the table, use these operations on your EntityCrudController:

```diff
class InvoiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
+    use \Backpack\DownloadOperation\DownloadOperation;
+    use \Backpack\DownloadOperation\BulkDownloadOperation;
```

Please note that BulkDelete is not a "real" operation, it's just a button that points to the normal "Delete" operation. So you cannot use `BulkDeleteOperation` without `DeleteOperation`.

The following configuration options are available, which can be used either inside the EntityCrudController using a `setupDownloadOperation()` method, or inside the `config/backpack/crud.php` configuration file:

```php
    /**
     * Configure what the Download button actually downloads.
     */
    public function setupDownloadOperation()
    {
        CRUD::set('download.view', 'user.invoice.wrapper'); // default is 'crud::show'
        CRUD::set('download.format', 'A4'); // default is 'A4'
    }
```

## Usage

Click to download. That's it.

## Overwriting

If you need to change anything else about the generated file (like changing from PDF to something else) we recommend you create a `download()` method in your EntityCrudController, that will give you complete freedom to change anything you want. Take a look at what the operation method does and change it to your liking.

## Change log

Changes are documented here on Github. Please see the [Releases tab](https://github.com/lc:vendor/downloadoperation/releases).

## Testing

``` bash
composer test
```

## Contributing

Please see [contributing.md](contributing.md) for a todolist and howtos.

## Security

If you discover any security related issues, please email cristian.tabacitu@backpackforlaravel.com instead of using the issue tracker.

## Credits

- [Cristian Tabacitu][link-author]
- [All Contributors][link-contributors]

## License

This project was released under MIT, so you can install it on top of any Backpack & Laravel project. Please see the [license file](license.md) for more information. 

However, please note that you do need Backpack installed, so you need to also abide by its [YUMMY License](https://github.com/Laravel-Backpack/CRUD/blob/master/LICENSE.md). That means in production you'll need a Backpack license code. You can get a free one for non-commercial use (or a paid one for commercial use) on [backpackforlaravel.com](https://backpackforlaravel.com).


[ico-version]: https://img.shields.io/packagist/v/backpack/downloadoperation.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/backpack/downloadoperation.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/backpack/downloadoperation
[link-downloads]: https://packagist.org/packages/backpack/downloadoperation
[link-author]: https://github.com/laravel-backpack
[link-contributors]: ../../contributors
