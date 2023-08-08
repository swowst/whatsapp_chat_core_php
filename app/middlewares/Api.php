<?php
namespace Middlewares;

use Fogito\App;
use Fogito\Events\Event;
use Fogito\Exception;
use Fogito\Lib\Auth;
use Fogito\Lib\Cache;
use Fogito\Lib\Company;
use Fogito\Lib\Lang;
use Fogito\Http\Request;
use Fogito\Http\Response;
use Fogito\Models\CoreTimezones;
use Lib\Permission;
use Lib\TimeZones;
use Models\LogsAccess;

class Api
{
    /**
     * beforeExecuteRoute
     *
     * @param  Event $event
     * @param  App $app
     * @return void
     */
    public function beforeExecuteRoute(Event $event, App $app)
    {
        try {
            $module     = $app->router->getModuleName();
            $controller = $app->router->getControllerName();
            $action     = $app->router->getActionName();

            Auth::init();
            if (!\in_array($module, ["crons", "s2s"]))
            {
                if(Auth::getError())
                    Response::error(Auth::getError()["description"], Auth::getError()["code"]);


                if (App::$di->config->databases->default->dbname === "CURRENT_DB_NAME") {
                    //THIS PERMISSION CHECH APPLIES ALL APIS.
//                    if (!Permission::check("board_view")) {
//                        Response::error(Lang::get("PageNotAllowed"), 1023);
//                    }
                }

                if (!in_array($controller, ['logscount'])) {
                    $cacheKey = $module . '_' . $controller . '_' . $action . '-' . Request::getServer("REMOTE_ADDR");
                    if (Cache::is_brute_force((string)$cacheKey, ["minute" => 60, "hour" => 3600, "day" => 10000])) {
                        Response::error(Lang::get("AttemptReached", "You attempted many times. Please wait a while and try again"));
                    }
                }
            }

            $timezoneId = CoreTimezones::detectTz()['id'];
            define("USER_TIMEZONE", $timezoneId);

            if (!in_array($module, ["crons", "s2s"])) {
                $i = new LogsAccess();
                if (Auth::isAuth())
                    $i->user_id = Auth::getId();
                $i->query = \array_slice(Request::get(), 0, 100, true);
                $i->set($i);
            }

        } catch (\Exception $e) {
            Response::error($e->getMessage(), $e->getCode());
        }

    }

    /**
     * beforeException
     *
     * @param  Event $event
     * @param  App $app
     * @param  Exception $exception
     * @return void
     */
    public function beforeException(Event $event, App $app, Exception $exception)
    {
        switch ($exception->getCode()) {
            case Exception::ERROR_NOT_FOUND_ACTION:
                Response::error(Lang::get('ActionNotFound', 'Action not found'), $exception->getCode());

            case Exception::ERROR_NOT_FOUND_CONTROLLER:
                Response::error(Lang::get('ControllerNotFound', 'Controller not found'), $exception->getCode());

            case Exception::ERROR_NOT_FOUND_MODULE:
                Response::error(Lang::get('ModuleNotFound', 'Module not found'), $exception->getCode());
        }
    }
}
