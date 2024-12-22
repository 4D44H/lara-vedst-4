<?php
/**
 * Created by IntelliJ IDEA.
 * User: fabian
 * Date: 11.06.17
 * Time: 18:57
 */

namespace Lara\Http\Controllers;

use View;

use Lara\Utilities;

/** controller to handle lara upgrade directly */
class AdminController extends Controller
{
    /** handles the upgrade request */
    function startUpdateProcess(){
        /** if you are no admin, you will be redirected back */
        if(!Utilities::requirePermission('admin')){
            return back($status = 403);
        }
        return View::make('update');
    }
}