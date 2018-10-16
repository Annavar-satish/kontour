<?php

namespace Kontenta\Kontour\Contracts;

use Illuminate\Support\Collection;

interface AdminViewManager
{
    /**
     * Blade layout that admin views should extend
     * @return string
     */
    public function layout(): string;

    /**
     * Blade layout that admin tool views could extend
     * @return string
     */
    public function toolLayout(): string;

    /**
     * Name of the main content blade section
     * @return string
     */
    public function mainSection(): string;

    /**
     * Name of the navigation blade section
     * @return string
     */
    public function navSection(): string;

    /**
     * Name of the main widget blade section
     * @return string
     */
    public function widgetSection(): string;

    /**
     * Name of the main header blade section
     * @return string
     */
    public function headerSection(): string;

    /**
     * Name of the main footer blade section
     * @return string
     */
    public function footerSection(): string;
    
    /**
     * Name of the tool header blade section
     * @return string
     */
    public function toolHeaderSection(): string;

    /**
     * Name of the tool main blade section
     * @return string
     */
    public function toolMainSection(): string;

    /**
     * Name of the tool widget blade section
     * @return string
     */
    public function toolWidgetSection(): string;

    /**
     * Name of the tool footer blade section
     * @return string
     */
    public function toolFooterSection(): string;

    /**
     * Add a stylesheet that the layout should pull in
     * @param string[] ...$url
     * @return $this
     */
    public function addStylesheetUrl(string ...$url): AdminViewManager;

    /**
     * Add a javascript that the layout should pull in
     * @param string[] ...$url
     * @return $this
     */
    public function addJavascriptUrl(string ...$url): AdminViewManager;

    /**
     * All registered stylesheets for the layout
     * @return Collection
     */
    public function getStylesheetUrls(): Collection;

    /**
     * All registered javascripts for the layout
     * @return Collection
     */
    public function getJavascriptUrls(): Collection;
}
