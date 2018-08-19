<?php

class Pages extends Controller
{

    public function __construct()
    {

    }

    public function index()
    {
        if (isLoggedIn()) {
            redirect('posts');
        }
        $data = ['title' => 'Shares', 'description' => 'Simple Social network built on the RascaMVC PHP framework'];
        $this->view('pages/index', $data);
    }


    public function about()
    {
        $data = ['title' => 'About us','description' => 'Love Mada every day'];
        $this->view('pages/about', $data);
    }
}