<?php

class EditorController
{
    private $view;

    public function __construct($view)
    {
        $this->view = $view;
    }

    public function show()
    {
        $this->view->render("panelEditor", [
            'title' => 'Panel Editor'
        ]);
    }
}