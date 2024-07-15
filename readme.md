# DownloadOperation

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Total Downloads][ico-downloads]][link-downloads]
[![The Whole Fruit Manifesto](https://img.shields.io/badge/writing%20standard-the%20whole%20fruit-brightgreen)](https://github.com/the-whole-fruit/manifesto)

This package provides a way to add "Download" buttons to your Backpack CRUDs. By default:
- the file format will be a PDF;
- the file will show a list of CRUD fields, similar to the [Show Operation](https://backpackforlaravel.com/docs/5.x/crud-operation-show);

## Demo

![Backpack Download and BulkDownload buttons](https://user-images.githubusercontent.com/1032474/101194862-3f82cc00-3667-11eb-856c-25c21f0181a5.gif)

## Requirements

This package uses [Backpack for Laravel](https://backpackforlaravel.com/) (of course) but also [spatie/browsershot](https://github.com/spatie/browsershot/), which itself uses Puppeteer, which itself uses a headless Chrome browser to generate the PDFs. Because of that:
- PRO: you don't need to code special views for them to look good in PDF form; if it looks good in the browser, it'll probably look good in the PDF; 
- CON: you need to install a bunch of stuff on your server (Puppeteer and Chrome); so you probably can't use this on shared hosting;

## Installation

**Step 0.** Install [Puppeteer](https://spatie.be/docs/browsershot/v4/requirements) and [spatie/browsershot](https://github.com/spatie/browsershot/), as instructed by Browsershot documentation. Then test your installation by runnning a tinker session (`php artisan tinker`) with the following code: `\Spatie\Browsershot\Browsershot::url('https://google.com')->ignoreHttpsErrors()->save('example.pdf');`. If that simple code triggers errors, please fix your Browsershot / Puppeteer installation before going any further. We've provided a few [troubleshooting tips & tricks](https://github.com/Laravel-Backpack/download-operation/edit/main/readme.md#troubleshooting) at the bottom of this page.

**Step 1.** Install this package via Composer

``` bash
composer require backpack/download-operation
```

**Step 2.** To add a "Download" button next to each entry inside your EntityCrudController and a bulk button at the bottom of the table, use these operations on your EntityCrudController:

```diff
class InvoiceCrudController extends CrudController
{
    use \Backpack\CRUD\app\Http\Controllers\Operations\ListOperation;
    use \Backpack\CRUD\app\Http\Controllers\Operations\DeleteOperation;
+    use \Backpack\DownloadOperation\DownloadOperation;
+    use \Backpack\DownloadOperation\BulkDownloadOperation;
```

Please note that `BulkDownloadOperation` is not a "real" operation. It's just a button that points to the normal "Download" operation. So you cannot use `BulkDownloadOperation` without `DownloadOperation`. The inverse is possible, only using `DownloadOperation` without `BulkDownloadOperation`.

**Step 3.** Configure your download operation by defining your fields and settings in `setupDownloadOperation()`

```php
    /**
     * Configure what the Download button actually downloads.
     */
    public function setupDownloadOperation()
    {
        // [OPTION 1] - you can manually add your columns:
        CRUD::column('title');
        
        // [OPTION 2] - since you've probably already defined columns in your List or Show operation, you could do:
        $this->setupListOperation(); // or $this->setupShowOperation();
        
        // in addition, in case you want to change settings:
        CRUD::set('download.view', 'user.invoice.download'); // default is: crud::show
        CRUD::set('download.format', 'A3'); // default is: A4
        CRUD::set('download.headers', ['Content-Type' => 'application/pdf']); // default is: ['Content-Type' => 'application/pdf']

        // in case you want to configure browsershot instance. more info on Overriding section.
        CRUD::set('download.browsershot', \App\DownloadInvoice::class); // default is: null
    }
```

## Configuration

### Publish the config file

You can optionally publish the config file to overwrite the default settings:

```bash
php artisan vendor:publish --provider="Backpack\DownloadOperation\AddonServiceProvider" --tag=config
```

### Configure the Browsershot instance

If you need to change the way the PDF is generated, you can create a new `__invokable()` class. The class should return the `Browsershow` result we will stream to the browser. Here is an example:

```php
    // in your EntityCrudController.php
    protected function setupDownloadOperation() {
        // ...
        CRUD::set('download.browsershot', \App\DownloadInvoice::class);
    }

    // create the following file in App\DownloadInvoice.php
    namespace App;

    use Spatie\Browsershot\Browsershot;

    class DownloadInvoice
    {
        public function __invoke($data)
        {
            // check spatie docs for more options
            return Browsershot::html(view($data['view'], $data))
                ->noSandbox()
                ->setChromePath('/usr/bin/google-chrome-stable')
                ->format($data['format'])
                ->pdf();
        }
    }
```

### Configure the download method

If you need to change the way the file is downloaded, you can create a new `downloadFile()` method in your EntityCrudController. Here is an example:

```php
    protected function downloadFile($data)
    {
        return response()->streamDownload(function () use ($data) {
            echo Browsershot::html(view($data['view'], $data))
                ->format($data['format'])
                ->pdf();
        }, $data['filename'], $data['headers']);
    }
```

## Changelog

Changes are documented here on Github. Please see the [Releases tab](https://github.com/laravel-backpack/download-operation/releases).

## Contributing

Please see [contributing.md](contributing.md) for a todolist and howtos.

## Security

If you discover any security related issues, please email cristian.tabacitu@backpackforlaravel.com instead of using the issue tracker.

## Credits

- [Cristian Tabacitu][link-author]
- [All Contributors][link-contributors]

## License

This project was released under MIT, so you can install it on top of any Backpack & Laravel project. Please see the [license file](license.md) for more information. 

## Troubleshooting

This package uses [spatie/browsershot](https://github.com/spatie/browsershot/), which uses Puppetteer, which itself uses a headless Chrome. So... a lot can go wrong during the installation phase. It can either go silky-smooth or be a nightmare. To help you out, here are a few issues we've encountered, and their solutions.

#### How to test if the Puppeteer installation is working

We recommend you run a tinker (`php artisan tinker`) and try the following:

```php
use Spatie\Browsershot\Browsershot;

Browsershot::url('https://google.com')->ignoreHttpsErrors()->save('example.pdf');
```

If that triggers errors... the problem is with your Puppeteer installation. Take a hard look at the error message, it might provide steps to fix or clues.

#### Could not find Chromium (rev. 1095492)

We've gotten this error, along the years, for multiple reasons:
- we had to upgrade the node version
- we were not using that node version
- the chache path was indeed incorrect (see below)

#### Cache path is wrong (eg. on MacOS when using Laravel Valet)

In your error message, see what cache path is being used. Most likely it's trying to use `.cache/puppeteer` inside the PROJECT DIRECTORY. Which is wrong, because it definitely won't find Chrome there. You want it to use the GLOBAL DIRECTORY. To define a cache path for a particular project, you can define a new `.ENV` variable:

```
PUPPETEER_CACHE_DIR="/Users/tabacitu/.cache/puppeteer"
```

You might also need to create that directory and restart your server. After that, it should work.

#### Does not work on M1 Mac (aka. chromium arm64 bug)

If you're running an M1 or M2 Mac, you might need to follow [this tutorial](https://linguinecode.com/post/how-to-fix-m1-mac-puppeteer-chromium-arm64-bug).

#### Other problems

We heavily recommend you check Puppeteer's troubleshooting page: https://pptr.dev/troubleshooting


[ico-version]: https://img.shields.io/packagist/v/backpack/download-operation.svg?style=flat-square
[ico-downloads]: https://img.shields.io/packagist/dt/backpack/download-operation.svg?style=flat-square

[link-packagist]: https://packagist.org/packages/backpack/download-operation
[link-downloads]: https://packagist.org/packages/backpack/download-operation
[link-author]: https://github.com/laravel-backpack
[link-contributors]: ../../contributors
