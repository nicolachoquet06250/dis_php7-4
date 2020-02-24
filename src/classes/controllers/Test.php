<?php


namespace app\classes\controllers;

use classes\mvc\Controller;
use classes\routage\Request;
use classes\routage\Response;

class Test extends Controller {
    public ?\classes\services\Test $test = null;

    public function __construct(\classes\services\Test $test) {
        $this->test = $test;
    }

    /**
     * @param Request $req
     * @param Response $res
     *
     * @http get
     * @route /:lol
     */
    public function toto(Request $req, Response $res) {
        echo '<pre>';
        var_dump($req, $res, $req->get('test'));
        echo '</pre>';
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return string
     *
     * @route /test/titi/:lol
     */
    public function tata(Request $req, Response $res): string {
        $res->assign('test', 'name');
        return $res->json(['{test}' => '{test}']);
    }
}