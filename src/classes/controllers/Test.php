<?php


namespace app\classes\controllers;

use classes\mvc\Controller;
use classes\routage\Request;
use classes\routage\Response;

class Test extends Controller {
    public ?\classes\services\Test $test = null;
    private ?\classes\services\Test $test2 = null;

    public function __construct(\classes\services\Test $test) {
        $this->test2 = $test;
    }

    /**
     * @param Request $req
     * @param Response $res
     * @return string
     *
     * @http get
     * @route /:lol
     */
    public function toto(Request $req, Response $res): string {
        ob_start();
        var_dump($req, $res, 'param lol => ', $req->param('lol'), 'get[test] => ', $req->get('test'));
        $var_dump = ob_get_contents();
        ob_clean();
        return $res->html('<pre>'.$var_dump.'</pre>');
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