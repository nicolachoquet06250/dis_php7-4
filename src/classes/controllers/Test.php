<?php

namespace app\classes\controllers;

use classes\mvc\Controller;
use classes\routage\Request;
use classes\routage\Response;
use app\classes\services\Test3;
use traits\DIS;

class Test extends Controller {
    use DIS;

    private ?Test3 $test3 = null;

    /**
     * @param Request $req
     * @param Response $res
     * @return string
     *
     * @http get
     * @route /:lol
     */
    public function toto(Request $req, Response $res): void {
        echo '<pre>';
        var_dump($req, $res, 'param lol => ', $req->param('lol'), 'get[test] => ', $req->get('test'), '$this->test3->toto()');
        $this->test3->toto();
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
        return $res->json([
            '{test}' => '{test}',
            'param lol => ' => $req->param('lol'),
            'get[test] => ' => $req->get('test')
        ]);
    }
}