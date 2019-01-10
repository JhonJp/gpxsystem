<?php
session_start();

error_reporting(E_ALL);
ini_set('display_errors', 1);

//Configuration Global
require_once "config/global.php";
require_once "vendor/autoload.php";
require_once "controller/GenericController.php";

//load the controller and execute the action
if (isset($_SESSION['login'])) {

    if (isset($_GET["controller"])) {

        $controllerObj = loadController($_GET["controller"]);
        launchAction($controllerObj);

    } else {
        $controllerObj = loadController(DEFAULT_CONTROLLER);
        launchAction($controllerObj);
    }

} else {
    if (isset($_GET["controller"])) {
        if ($_GET["controller"] == 'api') {
            $controllerObj = loadController("api");
            launchAction($controllerObj);
        } else {
            $controllerObj = loadController(DEFAULT_CONTROLLER);
            launchAction($controllerObj);
        }
    } else {
        $controllerObj = loadController(DEFAULT_CONTROLLER);
        launchAction($controllerObj);
    }
}


function loadController($controllername)
{
    $controller = ucwords($controllername) . 'Controller';
    $strFileController = 'controller/' . $controller . '.php';
    
    if (!file_exists($strFileController)) {
        $strFileController = 'controller/GenericController.php';
        require_once $strFileController;    
        $controllerObj = new GenericController();
    }
    else{
        $strFileController = 'controller/'. ucwords($controllername) .'Controller.php';
        require_once $strFileController;    
        $controllerObj = new $controller();
    }

    return $controllerObj;
}

function launchAction($controllerObj)
{
    if (isset($_SESSION['login'])) {
        if (isset($_GET["action"])) {
            $controllerObj->run($_GET["action"]);
        } else {
            $controllerObj->run(DEFAULT_ACTION);
        }
    } else {
        if (isset($_GET["action"])) {
            if ($_GET["controller"] == "api") {
                $controllerObj->run($_GET["action"]);
            } else {
                $controllerObj->run("login");
            }
        } else {
            $controllerObj->run("login");
        }
    }
}

?>