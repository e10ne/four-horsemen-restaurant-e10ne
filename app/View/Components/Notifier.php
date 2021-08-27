<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

final class Notifier extends Component
{
    public function __construct(string $id, string $type = "info")
    {
        $this->notifyId = $id;
        switch ($type) {
            case "info":
                $this->titleText = "Info";
                $this->colorName = "save";
                break;
            case "alert":
                $this->titleText = "Alert";
                $this->colorName = "warning-low";
                break;
            case "high-alert":
                $this->titleText = "Alert";
                $this->colorName = "warning-high";
                break;
        }
    }

    public function render(): View
    {
        return view("components.notifier");
    }

    public string $notifyId;
    public string $titleText;
    public string $colorName;
}
