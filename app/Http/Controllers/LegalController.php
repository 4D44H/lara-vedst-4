<?php

namespace Lara\Http\Controllers;

use Illuminate\Http\Request;
use View;

class LegalController extends Controller
{
    public function showPrivacyPolicy() {
    	return View::make('privacyPolicy');
    }

    // Generate a page for impressum
    public function showImpressum() {
    	// Will redirect to ILSC Impressum for now, might need to create own page later
    	return redirect("http://www.il-sc.de/impressum");
    }
}
