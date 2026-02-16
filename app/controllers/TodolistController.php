<?php

namespace app\controllers;

class TodolistController extends BaseController
{
    public function index(): void
    {
        $this->render('todolist', [], 'Todolist - BNGRC');
    }
}
