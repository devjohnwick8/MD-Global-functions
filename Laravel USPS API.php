https://github.com/johnpaulmedina/laravel-usps



Laravel-USPS
Laravel-USPS is a composer package that allows you to integrate the USPS Address / Shipping API / Rates Calculator. This package is ported from @author Vincent Gabriel https://github.com/VinceG/USPS-php-api

Requires a valid USPS API Username
Tested on Laravel 9
Installation
Begin by installing this package through Composer. Run this command from the Terminal:

composer require johnpaulmedina/laravel-usps

Laravel integration
For Laravel 5.5 and later, this package will be auto discovered and registered.

To wire this up in your Laravel 5.4 project you need to add the service provider. Open config/app.php, and add a new item to the providers array.

Johnpaulmedina\Usps\UspsServiceProvider::class,

Then you must also specify the alias in config/app.php. Add a new item to the Aliases array.

'Usps' => Johnpaulmedina\Usps\Facades\Usps::class,

This will allow integration by adding the Facade Use Usps;

Laravel Config
Add your USPS username config in config/services.php.

'usps' => [
        'username' => "XXXXXXXXXXXX",
        'testmode' => false,
    ],

////////////////////////////////////////////////////////////////////////////////////////////////////////////////
use Johnpaulmedina\Usps\Facades\Usps;



</php

function convert_weight($weight, $weight_unit) {
        switch ($weight_unit) {
            case 'kg':
                // Convert kilograms to pounds and ounces
                $weight_lb = $weight * 2.20462;
                $pounds = floor($weight_lb);
                $ounces = round(($weight_lb - $pounds) * 16);
                break;
            case 'g':
                // Convert grams to pounds and ounces
                $weight_lb = $weight * 0.00220462;
                $pounds = floor($weight_lb);
                $ounces = round(($weight_lb - $pounds) * 16);
                break;
            case 'oz':
                // Convert ounces to pounds and ounces
                $pounds = floor($weight / 16);
                $ounces = round($weight % 16);
                break;
            case 'lb':
                // Get weight in pounds and ounces
                $pounds = floor($weight);
                $ounces = round(($weight - $pounds) * 16);
                break;
            default:
                // Invalid weight unit
                return 'Invalid weight unit.';
        }

        // Return weight in pounds and ounces
        return ['pounds' => $pounds, 'ounces' => $ounces];
    }
    function convertToInches($value, $unit) {
        switch ($unit) {
            case 'cm':
                $value = $value / 2.54;
                break;
            case 'feet':
                $value = $value * 12;
                break;
            default:
                // If the unit is already inches or not recognized, do nothing
                break;
        }
        return $value;
    }

    public function uspsRates(Request $request)
    {

        //getting cart data starts
            $carts = Cart::content();
            $cart_qty = Cart::count();
            $cart_total = Cart::total();
        //getting cart data ends

        //calculating weight starts
            $ids = collect($carts)->pluck('id');
            $getProducts = Product::whereIn('id', $ids)->get();

            $pounds = 0;
            $ounces = 0;
            foreach($getProducts as $weight)
            {
                $wightCalculate = $this->convert_weight($weight->weight,$weight->weight_units);
                $pounds += $wightCalculate['pounds'];
                $ounces += $wightCalculate['ounces'];
            }
        //calculating weight ends

        //calculating dimensions starts
            // Initialize variables for weight and dimensions
            $totalLength = 0;
            $totalWidth = 0;
            $totalHeight = 0;

            // Loop through each package in the cart
            foreach ($getProducts as $package) {
                // Calculate the weight and dimensions of the package
                $length = $this->convertToInches(intval($package->length),$package->length_units);
                $width  = $this->convertToInches(intval($package->width),$package->length_units);
                $height = $this->convertToInches(intval($package->height),$package->length_units);

                // Add the weight and dimensions of the package to the total
                $totalLength += $length;
                $totalWidth += $width;
                $totalHeight += $height;
            }
        //calculating dimensions starts


        //using upsp package
        // Define the list of services to retrieve rates for
        $services = [
            'Priority Mail',
            'Express Mail',
            'Parcel Post',
            // 'PRIORITY COMMERCIAL',
            // 'PRIORITY MAIL EXPRESS',
            // 'EXPRESS MAIL INTERNATIONAL',
            // 'FIRST CLASS',
            // 'Priority Mail International',
            // 'Global Express Guaranteed',
            // 'Express Mail International (EMS)',
            // 'PARCEL_SELECT',
            // 'ONLINE',
            // 'ALL',
        ];
        $results = [];
        $id = 1;
        // Loop through the services and retrieve the rates for each service
        foreach ($services as $service) {
            $response = Usps::rate([
                'Service' => $service,
                'FirstClassMailType' => $request->input('FirstClassMailType', 'FLAT'),
                'ZipOrigination' => '90017',
                'ZipDestination' => $request->input('ZipDestination'),
                'Pounds' => $pounds,
                'Ounces' => $ounces,
                'Container' => 'VARIABLE',
                'Machinable' => 'True',
                'Length' => $totalLength,
                'Width' => $totalWidth,
                'Height' => $totalHeight,
            ]);
            $text = html_entity_decode(Arr::get($response, 'rate.RateV4Response.Package.Postage.MailService')); // convert HTML entities to characters
            $text = strip_tags($text);
            if ($response && !isset($response['error']) && $result = Arr::get($response, 'rate.RateV4Response.Package.Postage.Rate')) {
                // Only add results that do not contain errors or null values
                $results[] = [
                    'id' => $id,
                    'service' => $text,
                    'price' => $result,
                ];
                $id++;
            }
            $id++;
        }
        return response()->json(['services'=>$results, 'status'=>1]);
    }