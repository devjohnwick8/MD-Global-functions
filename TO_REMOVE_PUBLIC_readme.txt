goto: vendor\laravel\framework\src\Illuminate\Foundation\helpers.php




change this:
function asset($path, $secure = null)
    {
         return app('url')->asset("public/".$path, $secure);

    }


to this:
function asset($path, $secure = null)
    {
        return app('url')->asset("".$path, $secure);

    }
