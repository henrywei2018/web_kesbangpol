<?php

namespace App\View\Components\Layouts;

use Illuminate\View\Component;
use App\Settings\GeneralSettings;

class PublicPanel extends Component
{
    public $pageTitle;
    public $pageSubtitle;
    public $settings;
    public $user;

    public function __construct($pageTitle = null, $pageSubtitle = null)
    {
        $this->pageTitle = $pageTitle;
        $this->pageSubtitle = $pageSubtitle;
        $this->settings = app(GeneralSettings::class);
        $this->user = auth()->user();
    }

    public function render()
    {
        return view('layouts.public-panel');
    }
}